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
        add_submenu_page('wppoppop-dashboard', 'Theme', 'Theme', 'manage_options', self::PAGE, [$this, 'render_page']);
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
        $weights = ['300' => '300 Light', '400' => '400 Regular', '500' => '500 Medium', '600' => '600 Semi-bold', '700' => '700 Bold', '800' => '800 Extra-bold', '900' => '900 Black'];
        $transforms = ['none' => 'None', 'uppercase' => 'UPPERCASE', 'lowercase' => 'lowercase', 'capitalize' => 'Capitalize'];

        $f = function ($label, $type, $default, $opts = []) {
            return array_merge(['label' => $label, 'type' => $type, 'default' => $default], $opts);
        };

        return [
            'general' => ['label' => 'General', 'fields' => [
                'enabled'       => $f('Enable global theme on all WpPopPop pages', 'bool', 1),
                'apply_builder' => $f('Also apply to the Builder page', 'bool', 0),
                'c_bg_page'     => $f('Page background', 'color', '#f0f0f1'),
                'c_bg_card'     => $f('Card / panel background', 'color', '#ffffff'),
                'c_border'      => $f('Border color', 'color', '#e2e8f0'),
                'c_border_strong' => $f('Strong border color', 'color', '#cbd5e1'),
                'bw'            => $f('Border width', 'size', '1px'),
            ]],
            'typography' => ['label' => 'Typography', 'fields' => [
                'font_base'    => $f('Body font family', 'font', $sans),
                'font_heading' => $f('Heading font family', 'font', $sans),
                'font_mono'    => $f('Monospace font family', 'font', $mono),
                'fs_base'      => $f('Body font size', 'size', '13px'),
                'fs_h1'        => $f('H1 size', 'size', '22px'),
                'fs_h2'        => $f('H2 size', 'size', '18px'),
                'fs_h3'        => $f('H3 size', 'size', '15px'),
                'fs_small'     => $f('Small / helper text size', 'size', '12px'),
                'fs_label'     => $f('Label size', 'size', '11px'),
                'fs_button'    => $f('Button text size', 'size', '13px'),
                'fs_input'     => $f('Input text size', 'size', '12px'),
                'fs_table'     => $f('Table text size', 'size', '13px'),
                'fs_badge'     => $f('Badge text size', 'size', '11px'),
                'fs_kpi'       => $f('KPI value size', 'size', '24px'),
                'fs_modal_title' => $f('Modal title size', 'size', '14px'),
                'fw_body'      => $f('Body weight', 'enum', '400', ['options' => $weights]),
                'fw_heading'   => $f('Heading weight', 'enum', '700', ['options' => $weights]),
                'fw_label'     => $f('Label weight', 'enum', '700', ['options' => $weights]),
                'fw_button'    => $f('Button weight', 'enum', '600', ['options' => $weights]),
                'fw_kpi'       => $f('KPI value weight', 'enum', '800', ['options' => $weights]),
                'lh_base'      => $f('Line height (unitless)', 'number', '1.5'),
                'ls_label'     => $f('Label letter spacing', 'size', '0.5px'),
                'tt_label'     => $f('Label text transform', 'enum', 'uppercase', ['options' => $transforms]),
                'tt_badge'     => $f('Badge text transform', 'enum', 'uppercase', ['options' => $transforms]),
            ]],
            'text' => ['label' => 'Text & Links', 'fields' => [
                'c_text'       => $f('Body text', 'color', '#1e293b'),
                'c_heading'    => $f('Headings', 'color', '#0f172a'),
                'c_muted'      => $f('Muted / description text', 'color', '#64748b'),
                'c_link'       => $f('Link', 'color', '#2563eb'),
                'c_link_hover' => $f('Link hover', 'color', '#1d4ed8'),
            ]],
            'buttons' => ['label' => 'Buttons', 'fields' => [
                'c_primary'        => $f('Primary background', 'color', '#2563eb'),
                'c_primary_hover'  => $f('Primary hover background', 'color', '#1d4ed8'),
                'c_primary_text'   => $f('Primary text', 'color', '#ffffff'),
                'c_secondary_bg'   => $f('Secondary background', 'color', '#ffffff'),
                'c_secondary_hover' => $f('Secondary hover background', 'color', '#f1f5f9'),
                'c_secondary_text' => $f('Secondary text', 'color', '#1e293b'),
                'c_secondary_border' => $f('Secondary border', 'color', '#cbd5e1'),
                'c_danger'         => $f('Danger background', 'color', '#dc2626'),
                'c_danger_text'    => $f('Danger text', 'color', '#ffffff'),
                'r_button'         => $f('Button radius', 'size', '6px'),
                'h_button'         => $f('Button height', 'size', '34px'),
            ]],
            'forms' => ['label' => 'Form Fields', 'fields' => [
                'c_input_bg'          => $f('Input background', 'color', '#ffffff'),
                'c_input_text'        => $f('Input text', 'color', '#1e293b'),
                'c_input_border'      => $f('Input border', 'color', '#cbd5e1'),
                'c_input_focus'       => $f('Input focus border', 'color', '#2563eb'),
                'c_input_placeholder' => $f('Placeholder text', 'color', '#94a3b8'),
                'r_input'             => $f('Input radius', 'size', '6px'),
                'h_input'             => $f('Input height', 'size', '32px'),
            ]],
            'tables' => ['label' => 'Tables', 'fields' => [
                'c_th_bg'      => $f('Header background', 'color', '#f8fafc'),
                'c_th_text'    => $f('Header text', 'color', '#475569'),
                'c_row_bg'     => $f('Row background', 'color', '#ffffff'),
                'c_row_stripe' => $f('Striped row background', 'color', '#f8fafc'),
                'c_row_hover'  => $f('Row hover background', 'color', '#f1f5f9'),
                'c_row_border' => $f('Row border', 'color', '#e2e8f0'),
            ]],
            'cards' => ['label' => 'Cards & KPI', 'fields' => [
                'c_kpi_label' => $f('KPI label color', 'color', '#64748b'),
                'c_kpi_value' => $f('KPI value color', 'color', '#0f172a'),
                'r_card'      => $f('Card radius', 'size', '8px'),
                'p_card'      => $f('Card padding', 'size', '16px'),
                'gap'         => $f('Grid gap', 'size', '16px'),
                'shadow_card' => $f('Card shadow', 'shadow', '0 1px 3px rgba(0,0,0,0.05)'),
            ]],
            'badges' => ['label' => 'Badges & Chips', 'fields' => [
                'c_badge_pub_bg'    => $f('Published badge background', 'color', '#dcfce7'),
                'c_badge_pub_text'  => $f('Published badge text', 'color', '#15803d'),
                'c_badge_draft_bg'  => $f('Draft badge background', 'color', '#f1f5f9'),
                'c_badge_draft_text' => $f('Draft badge text', 'color', '#475569'),
                'c_chip_bg'         => $f('Shortcode chip background', 'color', '#f1f5f9'),
                'c_chip_text'       => $f('Shortcode chip text', 'color', '#334155'),
                'c_chip_border'     => $f('Shortcode chip border', 'color', '#e2e8f0'),
                'r_badge'           => $f('Badge radius', 'size', '12px'),
            ]],
            'status' => ['label' => 'Status & Notices', 'fields' => [
                'c_success' => $f('Success', 'color', '#16a34a'),
                'c_warning' => $f('Warning', 'color', '#d97706'),
                'c_info'    => $f('Info', 'color', '#0284c7'),
            ]],
            'modals' => ['label' => 'Modals', 'fields' => [
                'c_modal_bg'        => $f('Modal background', 'color', '#ffffff'),
                'c_modal_head_bg'   => $f('Modal header background', 'color', '#0f172a'),
                'c_modal_head_text' => $f('Modal header text', 'color', '#ffffff'),
                'c_modal_close'     => $f('Modal close icon', 'color', '#94a3b8'),
                'c_modal_backdrop'  => $f('Backdrop (rgba)', 'color', 'rgba(15,23,42,0.7)'),
                'shadow_modal'      => $f('Modal shadow', 'shadow', '0 20px 25px -5px rgba(0,0,0,0.25)'),
            ]],
            'scrollbar' => ['label' => 'Scrollbars', 'fields' => [
                'c_scroll_thumb' => $f('Scrollbar thumb', 'color', '#cbd5e1'),
                'c_scroll_track' => $f('Scrollbar track', 'color', '#f8fafc'),
            ]],
            'custom' => ['label' => 'Custom CSS', 'fields' => [
                'custom_css' => $f('Extra CSS (applied after theme, admin pages only)', 'textarea', ''),
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
        return $out;
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
        return $values;
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
        $page = sanitize_key(wp_unslash($_GET['page']));
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
                if (in_array($field['type'], ['bool', 'textarea'], true)) {
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
        // Shared dashboard base (wrap, header, buttons, KPI cards, badges, chips)
        wp_enqueue_style('wppoppop-dashboard-css', WPPOPPOP_URL . 'admin/css/dashboard.css', [], self::version());
        wp_enqueue_style('wppoppop-theme-css', WPPOPPOP_URL . 'admin/css/theme.css', ['wppoppop-dashboard-css', 'wppoppop-theme-global-css'], self::version());
        wp_enqueue_script('wppoppop-theme-js', WPPOPPOP_URL . 'admin/js/theme.js', [], self::version(), true);
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
            $json = isset($_POST['wpp_theme_import']) ? wp_unslash($_POST['wpp_theme_import']) : '';
            $data = json_decode($json, true);
            if (!is_array($data)) {
                $status = 'import_error';
            } else {
                $merged = array_merge(self::get_values(), $data);
                update_option(self::OPTION, self::sanitize_all($merged));
                $status = 'imported';
            }
        } else {
            $raw = isset($_POST['wpp_theme']) && is_array($_POST['wpp_theme']) ? wp_unslash($_POST['wpp_theme']) : [];
            update_option(self::OPTION, self::sanitize_all($raw));
        }

        wp_safe_redirect(add_query_arg(['page' => self::PAGE, 'wpp_status' => $status], admin_url('admin.php')));
        exit;
    }
}
