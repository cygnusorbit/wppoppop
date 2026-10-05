<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Admin {
    public function __construct() {
        add_action('admin_menu', [$this, 'register_menus']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    public function register_menus() {
        add_menu_page(
            'WpPopPop',
            'WpPopPop',
            'manage_options',
            'wppoppop',
            [$this, 'render_dashboard'],
            'dashicons-external',
            30
        );

        add_submenu_page('wppoppop', 'Popups', 'Popups', 'manage_options', 'wppoppop', [$this, 'render_dashboard']);
        add_submenu_page('wppoppop', 'Create Popup', 'Create Popup', 'manage_options', 'wppoppop-builder', [$this, 'render_builder']);
        add_submenu_page('wppoppop', 'A/B Campaigns', 'A/B Campaigns', 'manage_options', 'wppoppop-ab', [$this, 'render_ab']);
        add_submenu_page('wppoppop', 'Log', 'Log', 'manage_options', 'wppoppop-log', [$this, 'render_log']);
        add_submenu_page('wppoppop', 'Stats', 'Stats', 'manage_options', 'wppoppop-stats', [$this, 'render_stats']);
        add_submenu_page('wppoppop', 'Field Analytics', 'Field Analytics', 'manage_options', 'wppoppop-field-analytics', [$this, 'render_field_analytics']);
        add_submenu_page('wppoppop', 'Transactions', 'Transactions', 'manage_options', 'wppoppop-payments', [$this, 'render_payments']);
        add_submenu_page('wppoppop', 'Popups Library', 'Popups Library', 'manage_options', 'wppoppop-library', [$this, 'render_library']);
        add_submenu_page('wppoppop', 'Settings', 'Settings', 'manage_options', 'wppoppop-settings', [$this, 'render_settings']);
    }

    public function enqueue_assets($hook) {
        $page = isset($_GET['page']) ? sanitize_text_field($_GET['page']) : '';
        if (strpos($hook, 'wppoppop') === false && strpos($page, 'wppoppop') === false) {
            return;
        }

        // Settings Assets
        if ($page === 'wppoppop-settings' || strpos($hook, 'wppoppop-settings') !== false) {
            wp_enqueue_style('wppoppop-settings-css', WPPOPPOP_URL . 'admin/css/settings.css', [], WPPOPPOP_VERSION);
            wp_enqueue_script('jquery');
            wp_enqueue_script('wppoppop-settings-js', WPPOPPOP_URL . 'admin/js/settings.js', ['jquery'], WPPOPPOP_VERSION, true);
            wp_localize_script('wppoppop-settings-js', 'wppoppop_settings_vars', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce'    => wp_create_nonce('wppoppop_settings_nonce')
            ]);
            return;
        }

        // Library Assets
        if ($page === 'wppoppop-library') {
            wp_enqueue_style('wppoppop-library-css', WPPOPPOP_URL . 'admin/css/library.css', [], WPPOPPOP_VERSION);
            wp_enqueue_script('jquery');
            wp_enqueue_script('wppoppop-library-js', WPPOPPOP_URL . 'admin/js/library.js', ['jquery'], WPPOPPOP_VERSION, true);
            wp_localize_script('wppoppop-library-js', 'wppoppop_lib_vars', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce'    => wp_create_nonce('wppoppop_builder_nonce')
            ]);
            return;
        }

        // Builder Assets
        if ($page === 'wppoppop-builder' || strpos($hook, 'wppoppop-builder') !== false) {
            wp_enqueue_style('wppoppop-builder-css', WPPOPPOP_URL . 'admin/css/builder.css', [], WPPOPPOP_VERSION);
            wp_enqueue_script('jquery');
            wp_enqueue_script('jquery-ui-draggable');
            wp_enqueue_script('jquery-ui-resizable');
            wp_enqueue_script('wppoppop-builder-js', WPPOPPOP_URL . 'admin/js/builder.js', ['jquery', 'jquery-ui-draggable', 'jquery-ui-resizable'], WPPOPPOP_VERSION, true);

            $settings = get_option('wppoppop_settings', []);
            $features = [
                'google_fonts'     => isset($settings['google_fonts']) ? !empty($settings['google_fonts']) : true,
                'font_awesome'     => !empty($settings['font_awesome']),
                'air_datepicker'   => isset($settings['air_datepicker']) ? !empty($settings['air_datepicker']) : true,
                'jquery_mask'      => !empty($settings['jquery_mask']),
                'js_parser'        => !empty($settings['js_parser']),
                'signature_pad'    => !empty($settings['signature_pad']),
                'range_slider'     => !empty($settings['range_slider']),
                'adblock_detector' => !empty($settings['adblock_detector'])
            ];

            wp_localize_script('wppoppop-builder-js', 'wppoppop_vars', [
                'ajax_url'    => admin_url('admin-ajax.php'),
                'nonce'       => wp_create_nonce('wppoppop_builder_nonce'),
                'current_uid' => isset($_GET['uid']) ? sanitize_key($_GET['uid']) : '',
                'features'    => $features
            ]);
        }
    }

    public function render_dashboard() { include WPPOPPOP_PATH . 'templates/dashboard-view.php'; }
    public function render_builder() { include WPPOPPOP_PATH . 'templates/builder-view.php'; }
    public function render_ab() { include WPPOPPOP_PATH . 'templates/ab-view.php'; }
    public function render_log() { include WPPOPPOP_PATH . 'templates/log-view.php'; }
    public function render_stats() { include WPPOPPOP_PATH . 'templates/stats-view.php'; }
    public function render_field_analytics() { include WPPOPPOP_PATH . 'templates/field-analytics-view.php'; }
    public function render_payments() { include WPPOPPOP_PATH . 'templates/payments-view.php'; }
    public function render_library() { include WPPOPPOP_PATH . 'templates/library-view.php'; }
    public function render_settings() { include WPPOPPOP_PATH . 'templates/settings-view.php'; }
}
