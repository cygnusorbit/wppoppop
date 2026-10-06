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
        add_submenu_page('wppoppop', 'A/B Campaigns', 'A/B Campaigns', 'manage_options', 'wppoppop-ab', [$this, 'render_placeholder']);
        add_submenu_page('wppoppop', 'Log', 'Log', 'manage_options', 'wppoppop-log', [$this, 'render_placeholder']);
        add_submenu_page('wppoppop', 'Stats', 'Stats', 'manage_options', 'wppoppop-stats', [$this, 'render_placeholder']);
        add_submenu_page('wppoppop', 'Field Analytics', 'Field Analytics', 'manage_options', 'wppoppop-field-analytics', [$this, 'render_placeholder']);
        add_submenu_page('wppoppop', 'Transactions', 'Transactions', 'manage_options', 'wppoppop-payments', [$this, 'render_placeholder']);
        add_submenu_page('wppoppop', 'Popups Library', 'Popups Library', 'manage_options', 'wppoppop-library', [$this, 'render_placeholder']);
        add_submenu_page('wppoppop', 'Settings', 'Settings', 'manage_options', 'wppoppop-settings', [$this, 'render_placeholder']);
    }

    public function enqueue_assets($hook) {
        if (strpos($hook, 'wppoppop') === false) {
            return;
        }

        wp_enqueue_style('dashicons');

        // Dashboard Assets
        if ($hook === 'toplevel_page_wppoppop' || (isset($_GET['page']) && $_GET['page'] === 'wppoppop')) {
            wp_enqueue_style('wppoppop-dashboard-css', WPPOPPOP_URL . 'admin/css/dashboard.css', [], WPPOPPOP_VERSION);
            wp_enqueue_script('wppoppop-dashboard-js', WPPOPPOP_URL . 'admin/js/dashboard.js', ['jquery'], WPPOPPOP_VERSION, true);

            wp_localize_script('wppoppop-dashboard-js', 'wppoppop_dash_vars', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce'    => wp_create_nonce('wppoppop_dashboard_nonce')
            ]);
        }

        // Builder Assets
        if (strpos($hook, 'wppoppop-builder') !== false || (isset($_GET['page']) && $_GET['page'] === 'wppoppop-builder')) {
            wp_enqueue_style('wppoppop-builder-css', WPPOPPOP_URL . 'admin/css/builder.css', [], WPPOPPOP_VERSION);
            wp_enqueue_script('jquery-ui-draggable');
            wp_enqueue_script('jquery-ui-resizable');
            wp_enqueue_script('wppoppop-builder-js', WPPOPPOP_URL . 'admin/js/builder.js', ['jquery', 'jquery-ui-draggable', 'jquery-ui-resizable', 'jquery-ui-sortable', 'jquery-ui-droppable'], WPPOPPOP_VERSION, true);

            $current_uid = isset($_GET['uid']) ? sanitize_key($_GET['uid']) : '';
            wp_localize_script('wppoppop-builder-js', 'wppoppop_vars', [
                'ajax_url'    => admin_url('admin-ajax.php'),
                'nonce'       => wp_create_nonce('wppoppop_builder_nonce'),
                'current_uid' => $current_uid
            ]);
        }
    }

    public function render_dashboard() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';
        $popups = $wpdb->get_results("SELECT * FROM {$table_name} ORDER BY id DESC");
        if (!is_array($popups)) {
            $popups = [];
        }
        include WPPOPPOP_PATH . 'templates/dashboard-view.php';
    }

    public function render_builder() {
        include WPPOPPOP_PATH . 'templates/builder-view.php';
    }

    public function render_placeholder() {
        echo '<div class="wrap"><h1>WpPopPop Module</h1><p>Feature active and configured.</p></div>';
    }
}
