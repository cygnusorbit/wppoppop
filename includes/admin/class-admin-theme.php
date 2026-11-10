<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Global admin theme: schema -> whitelist sanitizer -> option -> CSS variables.
 */
class WpPopPop_Admin_Theme {
    const OPTION       = 'wppoppop_theme';
    const PAGE         = 'wppoppop-theme';
    const NONCE_ACTION = 'wppoppop_theme_save';

    public function __construct() {
        add_action('admin_init', [$this, 'handle_post']);
        add_filter('admin_body_class', [$this, 'body_class']);
        add_action('admin_menu', [$this, 'reorder_menu'], 100);
        add_action('admin_menu', [$this, 'register_fallback_menu'], 99);
    }

    public function register_fallback_menu() {
        global $submenu;
        if (!empty($submenu['wppoppop-dashboard'])) {
            foreach ($submenu['wppoppop-dashboard'] as $item) {
                if (isset($item[2]) && $item[2] === self::PAGE) { return; }
            }
        }
        add_submenu_page('wppoppop-dashboard', __('Theme', 'wppoppop'), __('Theme', 'wppoppop'), 'manage_options', self::PAGE, [$this, 'render_page']);
    }

    public function render_page() {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'wppoppop'));
        }
        include WPPOPPOP_PATH . 'templates/theme-view.php';
    }

    /* Place "Theme" immediately below "Settings" in the WpPopPop submenu. */
    public function reorder_menu() {
        global $submenu;
        if (empty($submenu['wppoppop-dashboard']) || !is_array($submenu['wppoppop-dashboard'])) { return; }
        $items = array_values($submenu['wppoppop-dashboard']);
        $theme_item = null;
        $rest = [];
        foreach ($items as $item) {
            if (isset($item[2]) && $item[2] === self::PAGE) { $theme_item = $item; } else { $rest[] = $item; }
        }
        if ($theme_item === null) { return; }
        $out = [];
        $placed = false;
        foreach ($rest as $item) {
            $out[] = $item;
            if (isset($item[2]) && $item[2] === 'wppoppop-settings') { $out[] = $theme_item; $placed = true; }
        }
        if (!$placed) { $out[] = $theme_item; }
        $submenu['wppoppop-dashboard'] = $out;
    }

    /* ---------------------------------------------------------- schema */
    public static function schema() {
        $sans = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif';
        $mono = 'ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace';
        $weights = ['300' => '300 ' . __('Light', 'wppoppop'), '400' => '400 ' . __('Regular', 'wppoppop'), '500' => '500 ' . __('Medium', 'wppoppop'), '600' => '600 ' . __('Semi-bold', 'wppoppop'), '700' => '700 ' . __('Bold', 'wppoppop'), '800' => '800 ' . __('Extra-bold', 'wppoppop'), '900' => '900 ' . __('Black', 'wppoppop')];
        $transforms = ['none' => __('None', 'wppoppop'), 'uppercase' => __('UPPERCASE', 'wppoppop'), 'lowercase' => __('lowercase', 'wppoppop'), 'capitalize' => __('Capitalize', 'wppoppop')];

        $f = function ($label, $type, $default, $opts = []) {
            return array_merge(['label' => $label, 'type' => $type, 'default' => $default], $opts);
        };

        return [
            'general' => ['label' => __('General', 'wppoppop'), 'fields' => [
                'enabled'       => $f(__('Enable global theme on all WpPopPop pages', 'wppoppop'), 'bool', 1),
                'apply_builder' => $f(__('Also apply to the Builder page', 'wppoppop'), 'bool', 0),
                'logo_id'       => $f(__('Top bar logo', 'wppoppop'), 'media', 0),
                'c_bg_page'     => $f(__('Page background', 'wppoppop'), 'color', '#f0f0f1'),
                'c_bg_card'     => $f(__('Card / panel background', 'wppoppop'), 'color', '#ffffff'),
                'c_border'      => $f(__('Border color', 'wppoppop'), 'color', '#dcdcde'),
                'c_border_strong' => $f(__('Strong border color', 'wppoppop'), 'color', '#c3c4c7'),
                'bw'            => $f(__('Border width', 'wppoppop'), 'size', '1px'),
            ]],
            'typography' => ['label' => __('Typography', 'wppoppop'), 'fields' => [
                'font_base'    => $f(__('Body font family', 'wppoppop'), 'font', $sans),
                'font_heading' => $f(__('Heading font family', 'wppoppop'), 'font', $sans),
                'font_mono'    => $f(__('Monospace font family', 'wppoppop'), 'font', $mono),
                'fs_base'      => $f(__('Body font size', 'wppoppop'), 'size', '13px'),
                'fs_h1'        => $f(__('H1 size', 'wppoppop'), 'size', '22px'),
                'fs_h2'        => $f(__('H2 size', 'wppoppop'), 'size', '15px'),
                'fs_h3'        => $f(__('H3 size', 'wppoppop'), 'size', '15px'),
                'fs_small'     => $f(__('Small / helper text size', 'wppoppop'), 'size', '12px'),
                'fs_label'     => $f(__('Label size', 'wppoppop'), 'size', '11px'),
                'fs_button'    => $f(__('Button text size', 'wppoppop'), 'size', '13px'),
                'fs_input'     => $f(__('Input text size', 'wppoppop'), 'size', '12px'),
                'fs_table'     => $f(__('Table text size', 'wppoppop'), 'size', '13px'),
                'fs_badge'     => $f(__('Badge text size', 'wppoppop'), 'size', '11px'),
                'fs_kpi'       => $f(__('KPI value size', 'wppoppop'), 'size', '24px'),
                'fs_modal_title' => $f(__('Modal title size', 'wppoppop'), 'size', '14px'),
                'fw_body'      => $f(__('Body weight', 'wppoppop'), 'enum', '400', ['options' => $weights]),
                'fw_heading'   => $f(__('Heading weight', 'wppoppop'), 'enum', '600', ['options' => $weights]),
                'fw_label'     => $f(__('Label weight', 'wppoppop'), 'enum', '700', ['options' => $weights]),
                'fw_button'    => $f(__('Button weight', 'wppoppop'), 'enum', '600', ['options' => $weights]),
                'fw_kpi'       => $f(__('KPI value weight', 'wppoppop'), 'enum', '800', ['options' => $weights]),
                'lh_base'      => $f(__('Line height (unitless)', 'wppoppop'), 'number', '1.5'),
                'ls_label'     => $f(__('Label letter spacing', 'wppoppop'), 'size', '0.5px'),
                'tt_label'     => $f(__('Label text transform', 'wppoppop'), 'enum', 'uppercase', ['options' => $transforms]),
                'tt_badge'     => $f(__('Badge text transform', 'wppoppop'), 'enum', 'uppercase', ['options' => $transforms]),
            ]],
            'text' => ['label' => __('Text & Links', 'wppoppop'), 'fields' => [
                'c_text'       => $f(__('Body text', 'wppoppop'), 'color', '#1d2327'),
                'c_heading'    => $f(__('Headings', 'wppoppop'), 'color', '#1d2327'),
                'c_muted'      => $f(__('Muted / description text', 'wppoppop'), 'color', '#787c82'),
                'c_link'       => $f(__('Link', 'wppoppop'), 'color', '#2237c9'),
                'c_link_hover' => $f(__('Link hover', 'wppoppop'), 'color', '#c1272d'),
            ]],
            'buttons' => ['label' => __('Buttons', 'wppoppop'), 'fields' => [
                'c_primary'        => $f(__('Primary background', 'wppoppop'), 'color', '#c1272d'),
                'c_primary_hover'  => $f(__('Primary hover background', 'wppoppop'), 'color', '#a51f25'),
                'c_primary_text'   => $f(__('Primary text', 'wppoppop'), 'color', '#ffffff'),
                'c_secondary_bg'   => $f(__('Secondary background', 'wppoppop'), 'color', '#ffffff'),
                'c_secondary_hover' => $f(__('Secondary hover background', 'wppoppop'), 'color', '#f1f5f9'),
                'c_secondary_text' => $f(__('Secondary text', 'wppoppop'), 'color', '#1d2327'),
                'c_secondary_border' => $f(__('Secondary border', 'wppoppop'), 'color', '#c3c4c7'),
                'c_danger'         => $f(__('Danger background', 'wppoppop'), 'color', '#b32d2e'),
                'c_danger_text'    => $f(__('Danger text', 'wppoppop'), 'color', '#ffffff'),
                'c_active'         => $f(__('Active background', 'wppoppop'), 'color', '#c1272d'),
                'c_active_text'    => $f(__('Active text', 'wppoppop'), 'color', '#ffffff'),
                'r_button'         => $f(__('Button radius', 'wppoppop'), 'size', '3px'),
                'h_button'         => $f(__('Button height', 'wppoppop'), 'size', '34px'),
            ]],
            'forms' => ['label' => __('Form Fields', 'wppoppop'), 'fields' => [
                'c_input_bg'          => $f(__('Input background', 'wppoppop'), 'color', '#ffffff'),
                'c_input_text'        => $f(__('Input text', 'wppoppop'), 'color', '#1e293b'),
                'c_input_border'      => $f(__('Input border', 'wppoppop'), 'color', '#8c8f94'),
                'c_input_focus'       => $f(__('Input focus border', 'wppoppop'), 'color', '#c1272d'),
                'c_input_placeholder' => $f(__('Placeholder text', 'wppoppop'), 'color', '#94a3b8'),
                'r_input'             => $f(__('Input radius', 'wppoppop'), 'size', '3px'),
                'h_input'             => $f(__('Input height', 'wppoppop'), 'size', '32px'),
            ]],
            'tables' => ['label' => __('Tables', 'wppoppop'), 'fields' => [
                'c_th_bg'      => $f(__('Header background', 'wppoppop'), 'color', '#f6f7f7'),
                'c_th_text'    => $f(__('Header text', 'wppoppop'), 'color', '#1d2327'),
                'c_row_bg'     => $f(__('Row background', 'wppoppop'), 'color', '#ffffff'),
                'c_row_stripe' => $f(__('Striped row background', 'wppoppop'), 'color', '#f6f7f7'),
                'c_row_hover'  => $f(__('Row hover background', 'wppoppop'), 'color', '#f6f7f7'),
                'c_row_border' => $f(__('Row border', 'wppoppop'), 'color', '#f0f0f1'),
            ]],
            'cards' => ['label' => __('Cards & KPI', 'wppoppop'), 'fields' => [
                'c_kpi_label' => $f(__('KPI label color', 'wppoppop'), 'color', '#787c82'),
                'c_kpi_value' => $f(__('KPI value color', 'wppoppop'), 'color', '#1d2327'),
                'r_card'      => $f(__('Card radius', 'wppoppop'), 'size', '3px'),
                'p_card'      => $f(__('Card padding', 'wppoppop'), 'size', '16px'),
                'gap'         => $f(__('Grid gap', 'wppoppop'), 'size', '16px'),
                'shadow_card' => $f(__('Card shadow', 'wppoppop'), 'shadow', '0 1px 1px rgba(0,0,0,0.04)'),
            ]],
            'badges' => ['label' => __('Badges & Chips', 'wppoppop'), 'fields' => [
                'c_badge_pub_bg'    => $f(__('Published badge background', 'wppoppop'), 'color', '#dcfce7'),
                'c_badge_pub_text'  => $f(__('Published badge text', 'wppoppop'), 'color', '#15803d'),
                'c_badge_draft_bg'  => $f(__('Draft badge background', 'wppoppop'), 'color', '#f1f5f9'),
                'c_badge_draft_text' => $f(__('Draft badge text', 'wppoppop'), 'color', '#475569'),
                'c_chip_bg'         => $f(__('Shortcode chip background', 'wppoppop'), 'color', '#f1f5f9'),
                'c_chip_text'       => $f(__('Shortcode chip text', 'wppoppop'), 'color', '#334155'),
                'c_chip_border'     => $f(__('Shortcode chip border', 'wppoppop'), 'color', '#e2e8f0'),
                'r_badge'           => $f(__('Badge radius', 'wppoppop'), 'size', '12px'),
            ]],
            'status' => ['label' => __('Status & Notices', 'wppoppop'), 'fields' => [
                'c_success' => $f(__('Success', 'wppoppop'), 'color', '#16a34a'),
                'c_warning' => $f(__('Warning', 'wppoppop'), 'color', '#d97706'),
                'c_info'    => $f(__('Info', 'wppoppop'), 'color', '#0284c7'),
            ]],
            'modals' => ['label' => __('Modals', 'wppoppop'), 'fields' => [
                'c_modal_bg'        => $f(__('Modal background', 'wppoppop'), 'color', '#ffffff'),
                'c_modal_head_bg'   => $f(__('Modal header background', 'wppoppop'), 'color', '#0f172a'),
                'c_modal_head_text' => $f(__('Modal header text', 'wppoppop'), 'color', '#ffffff'),
                'c_modal_close'     => $f(__('Modal close icon', 'wppoppop'), 'color', '#94a3b8'),
                'c_modal_backdrop'  => $f(__('Backdrop (rgba)', 'wppoppop'), 'color', 'rgba(15,23,42,0.7)'),
                'shadow_modal'      => $f(__('Modal shadow', 'wppoppop'), 'shadow', '0 20px 25px -5px rgba(0,0,0,0.25)'),
            ]],
            'scrollbar' => ['label' => __('Scrollbars', 'wppoppop'), 'fields' => [
                'c_scroll_thumb' => $f(__('Scrollbar thumb', 'wppoppop'), 'color', '#cbd5e1'),
                'c_scroll_track' => $f(__('Scrollbar track', 'wppoppop'), 'color', '#f8fafc'),
            ]],
            'custom' => ['label' => __('Custom CSS', 'wppoppop'), 'fields' => [
                'custom_css' => $f(__('Extra CSS (applied after theme, admin pages only)', 'wppoppop'), 'textarea', ''),
            ]],
        ];
    }

    public static function font_presets() {
        return [
            '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif',
            'Arial, Helvetica, sans-serif',
            'Verdana, Geneva, sans-serif',
            'Tahoma, Geneva, sans-serif',
            '"Trebuchet MS", Helvetica, sans-serif',
            'Georgia, "Times New Roman", serif',
            '"Times New Roman", Times, serif',
            '"Courier New", Courier, monospace',
            'ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace',
            'system-ui, sans-serif',
            'inherit',
        ];
    }

    public static function css_var($key) {
        return '--wpp-' . str_replace('_', '-', $key);
    }

    public static function defaults() {
        $out = [];
        foreach (self::schema() as $group) {
            foreach ($group['fields'] as $key => $field) {
                $out[$key] = $field['default'];
            }
        }
        return class_exists('WpPopPop_Saved_Defaults') ? WpPopPop_Saved_Defaults::apply_theme($out) : $out;
    }

    public static function field_def($key) {
        foreach (self::schema() as $group) {
            if (isset($group['fields'][$key])) {
                return $group['fields'][$key];
            }
        }
        return null;
    }

    /* ---------------------------------------------------- sanitization */
    public static function sanitize_value($field, $value) {
        if (is_array($value) || is_object($value)) {
            return $field['default'];
        }
        $value = trim((string) $value);
        switch ($field['type']) {
            case 'bool':
                return !empty($value) && $value !== '0' ? 1 : 0;
            case 'color':
                if (preg_match('/^#([0-9a-f]{3,4}|[0-9a-f]{6}|[0-9a-f]{8})$/i', $value)) {
                    return strtolower($value);
                }
                if (preg_match('/^rgba?\(\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}\s*(,\s*(0|1|0?\.\d+|1\.0+)\s*)?\)$/i', $value)) {
                    return preg_replace('/\s+/', '', $value);
                }
                if (strtolower($value) === 'transparent') {
                    return 'transparent';
                }
                return $field['default'];
            case 'size':
                return preg_match('/^-?\d{1,4}(\.\d{1,3})?(px|rem|em|%|pt)?$/', $value) ? $value : $field['default'];
            case 'number':
                return preg_match('/^\d{1,3}(\.\d{1,3})?$/', $value) ? $value : $field['default'];
            case 'enum':
                return isset($field['options'][$value]) ? $value : $field['default'];
            case 'font':
                return ($value !== '' && strlen($value) <= 250 && preg_match('/^[A-Za-z0-9 ,"\'\-_.]+$/', $value)) ? $value : $field['default'];
            case 'shadow':
                return ($value !== '' && strlen($value) <= 200 && preg_match('/^(none|[0-9a-zA-Z\s.,#()%\-]+)$/', $value)) ? $value : $field['default'];
            case 'media':
                $wpp_id = absint($value);
                return ($wpp_id && strpos((string) get_post_mime_type($wpp_id), 'image/') === 0) ? $wpp_id : 0;
            case 'textarea':
                $value = wp_strip_all_tags($value);
                return str_ireplace('</style', '', substr($value, 0, 20000));
        }
        return $field['default'];
    }

    public static function sanitize_all($raw) {
        $clean = [];
        $raw = is_array($raw) ? $raw : [];
        foreach (self::schema() as $group) {
            foreach ($group['fields'] as $key => $field) {
                if ($field['type'] === 'bool') {
                    $clean[$key] = !empty($raw[$key]) ? 1 : 0;
                } else {
                    $clean[$key] = isset($raw[$key]) ? self::sanitize_value($field, $raw[$key]) : $field['default'];
                }
            }
        }
        return $clean;
    }

    public static function get_values() {
        $saved = get_option(self::OPTION, []);
        if (!is_array($saved)) {
            $saved = [];
        }
        $values = self::defaults();
        foreach ($saved as $key => $val) {
            $field = self::field_def($key);
            if ($field) {
                $values[$key] = self::sanitize_value($field, $val);
            }
        }
        if (!array_key_exists('c_active', $saved) && isset($values['c_primary'])) {
            $values['c_active'] = $values['c_primary'];
        }
        if (!array_key_exists('c_active_text', $saved) && isset($values['c_primary_text'])) {
            $values['c_active_text'] = $values['c_primary_text'];
        }
        return $values;
    }

    /** Custom top-bar logo URL, or '' to use the bundled default logo. */
    public static function logo_url() {
        $v  = self::get_values();
        $id = isset($v['logo_id']) ? (int) $v['logo_id'] : 0;
        if ($id <= 0) { return ''; }
        $url = wp_get_attachment_image_url($id, 'full');
        if (!$url) { $url = wp_get_attachment_url($id); }
        return $url ? $url : '';
    }

    /* -------------------------------------------------------- output */
    public static function is_wppoppop_admin_page() {
        $page = isset($_GET['page']) ? sanitize_key(wp_unslash($_GET['page'])) : '';
        return $page !== '' && strpos($page, 'wppoppop') === 0;
    }

    public function body_class($classes) {
        if (!self::is_wppoppop_admin_page()) {
            return $classes;
        }
        $v = self::get_values();
        $page = isset($_GET['page']) ? sanitize_key(wp_unslash($_GET['page'])) : '';
        if (empty($v['enabled'])) {
            return $classes;
        }
        if ($page === 'wppoppop-builder' && empty($v['apply_builder'])) {
            return $classes;
        }
        return $classes . ' wppoppop-admin-themed';
    }

    public static function build_css() {
        $v = self::get_values();
        $css = 'body.wppoppop-admin-themed{';
        foreach (self::schema() as $group) {
            foreach ($group['fields'] as $key => $field) {
                if (in_array($field['type'], ['bool', 'textarea', 'media'], true)) {
                    continue;
                }
                $css .= self::css_var($key) . ':' . $v[$key] . ';';
            }
        }
        $css .= '}';
        if (!empty($v['custom_css'])) {
            $css .= "\n" . $v['custom_css'];
        }
        return $css;
    }

    protected static function version() {
        return defined('WPPOPPOP_VERSION') ? WPPOPPOP_VERSION : '1.0';
    }

    public static function enqueue_global() {
        wp_enqueue_style('wppoppop-theme-global-css', WPPOPPOP_URL . 'admin/css/theme-global.css', [], self::version());
        wp_add_inline_style('wppoppop-theme-global-css', self::build_css());
    }

    public static function enqueue_page() {
        $v = self::version();
        wp_enqueue_style('dashicons');
        wp_enqueue_style('wppoppop-dashboard-css', WPPOPPOP_URL . 'admin/css/dashboard.css', [], $v);
        wp_enqueue_style('wppoppop-overview-css', WPPOPPOP_URL . 'admin/css/overview.css', [], $v);
        wp_enqueue_style('wppoppop-theme-css', WPPOPPOP_URL . 'admin/css/theme.css', ['wppoppop-dashboard-css', 'wppoppop-overview-css', 'wppoppop-theme-global-css'], $v);
        wp_enqueue_script('wppoppop-overview-js', WPPOPPOP_URL . 'admin/js/overview.js', ['jquery'], $v, true);
        wp_enqueue_script('wppoppop-theme-js', WPPOPPOP_URL . 'admin/js/theme.js', [], $v, true);
        wp_enqueue_media();
        wp_enqueue_script('wppoppop-theme-logo-js', WPPOPPOP_URL . 'admin/js/theme-logo.js', ['wppoppop-theme-js'], $v, true);
        if (class_exists('WpPopPop_Saved_Defaults')) {
            WpPopPop_Saved_Defaults::enqueue();
        }
    }

    /* ---------------------------------------------------------- save */
    public function handle_post() {
        if (!is_admin() || empty($_POST['wpp_theme_action'])) {
            return;
        }
        if (!isset($_GET['page']) || sanitize_key(wp_unslash($_GET['page'])) !== self::PAGE) {
            return;
        }
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'wppoppop'));
        }
        check_admin_referer(self::NONCE_ACTION, 'wpp_theme_nonce');

        $action = sanitize_key(wp_unslash($_POST['wpp_theme_action']));
        $status = 'saved';

        if ($action === 'reset') {
            delete_option(self::OPTION);
            $status = 'reset';
        } elseif ($action === 'import') {
            $json = isset($_POST['wpp_theme_import']) ? /* phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- JSON decoded and sanitised key by key by the theme importer */ wp_unslash($_POST['wpp_theme_import']) : '';
            $data = json_decode($json, true);
            if (!is_array($data)) {
                $status = 'import_error';
            } else {
                $merged = array_merge(self::get_values(), $data);
                update_option(self::OPTION, self::sanitize_all($merged));
                $status = 'imported';
            }
        } else {
            $raw = isset($_POST['wpp_theme']) && is_array($_POST['wpp_theme']) ? /* phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitised key by key by the theme sanitiser */ wp_unslash($_POST['wpp_theme']) : [];
            update_option(self::OPTION, self::sanitize_all($raw));
        }

        wp_safe_redirect(add_query_arg(['page' => self::PAGE, 'wpp_status' => $status], admin_url('admin.php')));
        exit;
    }
}
