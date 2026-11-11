<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Admin_Menu {
    protected $pages;
    protected $dashboard_hook = '';
    protected static $original = [];
    protected static $hidden = [];

    public function __construct($pages = null) {
        if ($pages instanceof WpPopPop_Admin_Pages) {
            $this->pages = $pages;
        } else {
            if (!class_exists('WpPopPop_Admin_Pages') && file_exists(WPPOPPOP_PATH . 'includes/admin/class-admin-pages.php')) {
                require_once WPPOPPOP_PATH . 'includes/admin/class-admin-pages.php';
            }
            $this->pages = new WpPopPop_Admin_Pages();
        }

        add_action('admin_menu', [$this, 'register_menus']);
        add_filter('set-screen-option', [$this, 'set_screen_option'], 10, 3);
        add_action('admin_menu', [$this, 'apply_menu_prefs'], 999);
        add_action('admin_menu', ['WpPopPop_Promo', 'register_menu'], 1000);
        add_action('admin_enqueue_scripts', ['WpPopPop_Promo', 'enqueue_menu']);
        WpPopPop_Promo::init_plugin_link();
        add_action('admin_head', [$this, 'print_hidden_css']);
        add_action('admin_head', [$this, 'print_unread_pulse_css']);
        add_action('admin_footer', [$this, 'print_hidden_js']);
    }

    /* WPPOPPOP_MENU_ICON: SVG data URI (WordPress recolors it); falls back to a dashicon. */
    protected function menu_icon() {
        $file = WPPOPPOP_PATH . 'admin/img/wppoppop-menu-icon.svg';
        if (is_readable($file)) {
            /* phpcs:ignore WordPress.WP.AlternativeFunctions -- reads a local plugin or upload temp file, not a remote URL */ $svg = file_get_contents($file);
            if ($svg !== false && $svg !== '') {
                return 'data:image/svg+xml;base64,' . /* phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- inline SVG data URI, not obfuscation */ base64_encode($svg);
            }
        }
        return 'dashicons-external';
    }

    public function register_menus() {
        /* WPPOPPOP_MENU_V2: Dashboard is the parent page, so WordPress adds no duplicate first item. */
        $parent = 'wppoppop-dashboard';
        $overview_cb = method_exists($this->pages, 'render_overview') ? [$this->pages, 'render_overview'] : [$this->pages, 'render_dashboard'];

        add_menu_page(
            'WpPopPop',
            'WpPopPop',
            'manage_options',
            $parent,
            $overview_cb,
            $this->menu_icon(),
            30
        );

        add_submenu_page($parent, __('Dashboard', 'wppoppop'), __('Dashboard', 'wppoppop'), 'manage_options', $parent, $overview_cb);
        $this->dashboard_hook = add_submenu_page($parent, __('Popups', 'wppoppop'), __('Popups', 'wppoppop'), 'manage_options', 'wppoppop', [$this->pages, 'render_dashboard']);
        add_submenu_page($parent, __('Create Popup', 'wppoppop'), __('Create Popup', 'wppoppop'), 'manage_options', 'wppoppop-builder', [$this->pages, 'render_builder']);
        add_submenu_page($parent, __('Submissions', 'wppoppop'), __('Submissions', 'wppoppop'), 'manage_options', 'wppoppop-submissions', [$this->pages, 'render_submissions']);
        add_submenu_page($parent, __('Activity Log', 'wppoppop'), __('Activity Log', 'wppoppop'), 'manage_options', 'wppoppop-log', [$this->pages, 'render_log']);
        add_submenu_page($parent, __('Field Analytics', 'wppoppop'), __('Field Analytics', 'wppoppop'), 'manage_options', 'wppoppop-field-analytics', [$this->pages, 'render_field_analytics']);
        add_submenu_page($parent, __('Transactions', 'wppoppop'), __('Transactions', 'wppoppop'), 'manage_options', 'wppoppop-payments', [$this->pages, 'render_payments']);
        add_submenu_page($parent, __('Popups Library', 'wppoppop'), __('Popups Library', 'wppoppop'), 'manage_options', 'wppoppop-library', [$this->pages, 'render_library']);
        add_submenu_page($parent, __('Settings', 'wppoppop'), __('Settings', 'wppoppop'), 'manage_options', 'wppoppop-settings', [$this->pages, 'render_settings']);
        add_submenu_page($parent, __('Extend', 'wppoppop'), __('Extend', 'wppoppop'), 'manage_options', 'wppoppop-extend', [$this->pages, 'render_extend']);
        add_submenu_page($parent, __('Theme', 'wppoppop'), __('Theme', 'wppoppop'), 'manage_options', 'wppoppop-theme', [$this->pages, 'render_theme']);
        add_submenu_page($parent, __('Tools & Export', 'wppoppop'), __('Tools & Export', 'wppoppop'), 'manage_options', 'wppoppop-tools', [$this->pages, 'render_tools']);

        do_action('wppoppop_admin_menu', $parent);

        if (!empty($this->dashboard_hook)) {
            add_action("load-{$this->dashboard_hook}", [$this, 'load_dashboard_screen_options']);
        }
    }

    /** Pages that can never be hidden (prevents lock-out). */
    public static function pinned() {
        return ['wppoppop-dashboard', 'wppoppop-settings'];
    }

    /** slug => original title, captured live from the WP submenu (dynamic sync). */
    public static function get_original_pages() {
        return self::$original;
    }

    public static function get_prefs() {
        $s = get_option('wppoppop_settings', []);
        $prefs = (is_array($s) && isset($s['menu_pages']) && is_array($s['menu_pages'])) ? $s['menu_pages'] : [];
        if (!class_exists('WpPopPop_Admin_Settings')) {
            require_once WPPOPPOP_PATH . 'includes/admin/class-admin-settings.php';
        }
        foreach (WpPopPop_Admin_Settings::get_default_menu_pages() as $slug => $defaults) {
            $prefs[$slug] = (isset($prefs[$slug]) && is_array($prefs[$slug]))
                ? array_merge($defaults, $prefs[$slug])
                : $defaults;
        }
        return $prefs;
    }

    /** Merge live pages with saved prefs; returns ordered rows. */
    public static function resolve_pages($orig, $prefs) {
        $rows = [];
        $i = 0;
        foreach ($orig as $slug => $title) {
            $p = (isset($prefs[$slug]) && is_array($prefs[$slug])) ? $prefs[$slug] : [];
            $rows[] = [
                'slug'    => $slug,
                'default' => $title,
                'label'   => (isset($p['label']) && $p['label'] !== '') ? $p['label'] : $title,
                'custom'  => (isset($p['label']) && $p['label'] !== '') ? 1 : 0,
                'hidden'  => (!empty($p['hidden']) && !in_array($slug, self::pinned(), true)) ? 1 : 0,
                'order'   => (isset($p['order']) && (int) $p['order'] > 0) ? (int) $p['order'] : null,
                'idx'     => $i++,
            ];
        }
        usort($rows, function ($a, $b) {
            if ($a['order'] === null && $b['order'] === null) { return $a['idx'] - $b['idx']; }
            if ($a['order'] === null) { return 1; }
            if ($b['order'] === null) { return -1; }
            return ($a['order'] === $b['order']) ? ($a['idx'] - $b['idx']) : ($a['order'] - $b['order']);
        });
        return $rows;
    }

    public function apply_menu_prefs() {
        global $submenu, $menu;
        $parent = 'wppoppop-dashboard';

        /* WPPOPPOP_MENU_UNREAD_PULSE: pulsing notice icon on the top-level menu while unread submissions exist */
        if (is_array($menu) && self::has_unread_submissions()) {
            foreach ($menu as $k => $m) {
                if (isset($m[2]) && $m[2] === $parent) {
                    $menu[$k][0] = $m[0] . '<span class="wppoppop-menu-pulse" title="' . esc_attr__('Unread submissions', 'wppoppop') . '" aria-hidden="true">!</span><span class="screen-reader-text">' . esc_html__('Unread submissions', 'wppoppop') . '</span>';
                    break;
                }
            }
        }
        if (empty($submenu[$parent]) || !is_array($submenu[$parent])) {
            return;
        }

        $items = [];
        self::$original = [];
        foreach ($submenu[$parent] as $it) {
            if (!isset($it[2])) { continue; }
            $items[$it[2]] = $it;
            self::$original[$it[2]] = trim(wp_strip_all_tags($it[0]));
        }

        $rows = self::resolve_pages(self::$original, self::get_prefs());
        $out = [];
        self::$hidden = [];
        foreach ($rows as $r) {
            $it = $items[$r['slug']];
            if (!empty($r['custom'])) {
                $it[0] = esc_html($r['label']);
                if (isset($it[3])) { $it[3] = $r['label']; }
            }
            if (!empty($r['hidden'])) {
                self::$hidden[] = $r['slug'];
            }
            $out[] = $it;
        }
        $submenu[$parent] = $out;
    }

    /** True when at least one submission is still unread (cheap existence check, no full count). */
    public static function has_unread_submissions() {
        global $wpdb;
        if (!current_user_can('manage_options')) { return false; }
        $table = $wpdb->prefix . 'wppoppop_submissions';
        $prev = $wpdb->suppress_errors(true);
        /* phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- table name is built from $wpdb->prefix and the query takes no user input */ $found = $wpdb->get_var("SELECT 1 FROM {$table} WHERE is_read = 0 LIMIT 1");
        $wpdb->suppress_errors($prev);
        return !empty($found);
    }

    /** Pulse icon styling in the plugin primary color (Theme page), printed on every admin screen. */
    public function print_unread_pulse_css() {
        if (!current_user_can('manage_options') || !self::has_unread_submissions()) { return; }
        $color = '#c1272d';
        if (class_exists('WpPopPop_Admin_Theme')) {
            $v = WpPopPop_Admin_Theme::get_values();
            $c = isset($v['c_primary']) ? sanitize_hex_color($v['c_primary']) : '';
            if ($c) { $color = $c; }
        }
        $hex = ltrim($color, '#');
        if (strlen($hex) === 3) { $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2]; }
        $rgb = hexdec(substr($hex, 0, 2)) . ',' . hexdec(substr($hex, 2, 2)) . ',' . hexdec(substr($hex, 4, 2));
        ?>
<style id="wppoppop-unread-pulse-css">
#adminmenu .wppoppop-menu-pulse{display:inline-block;box-sizing:border-box;width:16px;height:16px;margin:0 0 0 7px;border-radius:50%;background:<?php echo esc_html($color); ?>;color:#fff;font:700 11px/16px Arial,Helvetica,sans-serif;text-align:center;vertical-align:text-top;-webkit-animation:wppoppop-menu-pulse 1.6s ease-out infinite;animation:wppoppop-menu-pulse 1.6s ease-out infinite}
@-webkit-keyframes wppoppop-menu-pulse{0%{box-shadow:0 0 0 0 rgba(<?php echo esc_html($rgb); ?>,.65)}70%{box-shadow:0 0 0 8px rgba(<?php echo esc_html($rgb); ?>,0)}100%{box-shadow:0 0 0 0 rgba(<?php echo esc_html($rgb); ?>,0)}}
@keyframes wppoppop-menu-pulse{0%{box-shadow:0 0 0 0 rgba(<?php echo esc_html($rgb); ?>,.65)}70%{box-shadow:0 0 0 8px rgba(<?php echo esc_html($rgb); ?>,0)}100%{box-shadow:0 0 0 0 rgba(<?php echo esc_html($rgb); ?>,0)}}
@media (prefers-reduced-motion:reduce){#adminmenu .wppoppop-menu-pulse{-webkit-animation:none;animation:none}}
</style>
        <?php
    }

    /* Hidden pages stay registered (direct URLs keep working); only the menu entry is hidden. */
    public function print_hidden_css() {
        if (empty(self::$hidden)) { return; }
        echo '<style id="wppoppop-hidden-pages-css">';
        foreach (self::$hidden as $slug) {
            echo '#adminmenu .wp-submenu a[href$="page=' . esc_attr($slug) . '"]{display:none !important;}';
        }
        echo '</style>';
    }

    public function print_hidden_js() {
        if (empty(self::$hidden)) { return; }
        ?>
        <script id="wppoppop-hidden-pages-js">
        (function () {
            var hidden = <?php echo wp_json_encode(array_values(self::$hidden)); ?>;
            var links = document.querySelectorAll('#adminmenu .wp-submenu a');
            for (var i = 0; i < links.length; i++) {
                var m = /[?&]page=([^&#]+)/.exec(links[i].getAttribute('href') || '');
                if (m && hidden.indexOf(decodeURIComponent(m[1])) > -1 && links[i].parentNode && links[i].parentNode.style) {
                    links[i].parentNode.style.display = 'none';
                }
            }
        })();
        </script>
        <?php
    }

    public function load_dashboard_screen_options() {
        // Screen Options intentionally hidden on the Popups page.
        add_filter('screen_options_show_screen', '__return_false');
    }

    public function set_screen_option($status, $option, $value) {
        if ('wppoppop_campaigns_per_page' === $option) {
            return (int) $value;
        }
        return $status;
    }
}
