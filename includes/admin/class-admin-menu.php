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
        add_action('admin_head', [$this, 'print_hidden_css']);
        add_action('admin_footer', [$this, 'print_hidden_js']);
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
            'dashicons-external',
            30
        );

        add_submenu_page($parent, 'Dashboard', 'Dashboard', 'manage_options', $parent, $overview_cb);
        $this->dashboard_hook = add_submenu_page($parent, 'Popups', 'Popups', 'manage_options', 'wppoppop', [$this->pages, 'render_dashboard']);
        add_submenu_page($parent, 'Create Popup', 'Create Popup', 'manage_options', 'wppoppop-builder', [$this->pages, 'render_builder']);
        add_submenu_page($parent, 'A/B Campaigns', 'A/B Campaigns', 'manage_options', 'wppoppop-ab', [$this->pages, 'render_ab']);
        add_submenu_page($parent, 'Submissions', 'Submissions', 'manage_options', 'wppoppop-submissions', [$this->pages, 'render_submissions']);
        add_submenu_page($parent, 'Activity Log', 'Activity Log', 'manage_options', 'wppoppop-log', [$this->pages, 'render_log']);
        add_submenu_page($parent, 'Statistics', 'Statistics', 'manage_options', 'wppoppop-stats', [$this->pages, 'render_stats']);
        add_submenu_page($parent, 'Field Analytics', 'Field Analytics', 'manage_options', 'wppoppop-field-analytics', [$this->pages, 'render_field_analytics']);
        add_submenu_page($parent, 'Transactions', 'Transactions', 'manage_options', 'wppoppop-payments', [$this->pages, 'render_payments']);
        add_submenu_page($parent, 'Popups Library', 'Popups Library', 'manage_options', 'wppoppop-library', [$this->pages, 'render_library']);
        add_submenu_page($parent, 'Settings', 'Settings', 'manage_options', 'wppoppop-settings', [$this->pages, 'render_settings']);
        add_submenu_page($parent, 'Theme', 'Theme', 'manage_options', 'wppoppop-theme', [$this->pages, 'render_theme']);
        add_submenu_page($parent, 'Tools & Export', 'Tools & Export', 'manage_options', 'wppoppop-tools', [$this->pages, 'render_tools']);

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
        return (is_array($s) && isset($s['menu_pages']) && is_array($s['menu_pages'])) ? $s['menu_pages'] : [];
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
        global $submenu;
        $parent = 'wppoppop-dashboard';
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
