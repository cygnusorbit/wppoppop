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
        add_submenu_page('wppoppop', 'Submissions & Stats', 'Submissions & Stats', 'manage_options', 'wppoppop-submissions', [$this, 'render_submissions']);
        add_submenu_page('wppoppop', 'Payments & Sales', 'Payments & Sales', 'manage_options', 'wppoppop-payments', [$this, 'render_payments']);
        add_submenu_page('wppoppop', 'Tools & Export', 'Tools & Export', 'manage_options', 'wppoppop-tools', [$this, 'render_tools']);
        add_submenu_page('wppoppop', 'Settings', 'Settings', 'manage_options', 'wppoppop-settings', [$this, 'render_settings']);
    }

    public function enqueue_assets($hook) {
        if (strpos($hook, 'wppoppop') === false) {
            return;
        }

        // Settings Page Assets
        if (strpos($hook, 'wppoppop-settings') !== false) {
            wp_enqueue_style('wppoppop-settings-css', WPPOPPOP_URL . 'admin/css/settings.css', [], WPPOPPOP_VERSION);
            wp_enqueue_script('wppoppop-settings-js', WPPOPPOP_URL . 'admin/js/settings.js', ['jquery'], WPPOPPOP_VERSION, true);
            wp_localize_script('wppoppop-settings-js', 'wppoppop_settings_vars', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce'    => wp_create_nonce('wppoppop_settings_nonce')
            ]);
            return;
        }

        // Builder Assets
        wp_enqueue_style('wppoppop-builder-css', WPPOPPOP_URL . 'admin/css/builder.css', [], WPPOPPOP_VERSION);
        wp_enqueue_script('jquery-ui-draggable');
        wp_enqueue_script('jquery-ui-resizable');
        wp_enqueue_script('wppoppop-builder-js', WPPOPPOP_URL . 'admin/js/builder.js', ['jquery', 'jquery-ui-draggable', 'jquery-ui-resizable'], WPPOPPOP_VERSION, true);

        $current_uid = isset($_GET['uid']) ? sanitize_key($_GET['uid']) : '';

        wp_localize_script('wppoppop-builder-js', 'wppoppop_vars', [
            'ajax_url'    => admin_url('admin-ajax.php'),
            'nonce'       => wp_create_nonce('wppoppop_builder_nonce'),
            'current_uid' => $current_uid
        ]);
    }

    public function render_dashboard() { include WPPOPPOP_PATH . 'templates/dashboard-view.php'; }
    public function render_builder() { include WPPOPPOP_PATH . 'templates/builder-view.php'; }
    public function render_ab() { include WPPOPPOP_PATH . 'templates/ab-view.php'; }
    public function render_submissions() { include WPPOPPOP_PATH . 'templates/submissions-view.php'; }
    public function render_payments() { include WPPOPPOP_PATH . 'templates/payments-view.php'; }
    public function render_tools() { include WPPOPPOP_PATH . 'templates/tools-view.php'; }
    public function render_settings() { include WPPOPPOP_PATH . 'templates/settings-view.php'; }
}
