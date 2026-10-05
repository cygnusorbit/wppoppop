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

        add_submenu_page(
            'wppoppop',
            'All Popups',
            'All Popups',
            'manage_options',
            'wppoppop',
            [$this, 'render_dashboard']
        );

        add_submenu_page(
            'wppoppop',
            'Create Popup',
            'Create Popup',
            'manage_options',
            'wppoppop-builder',
            [$this, 'render_builder']
        );

        add_submenu_page(
            'wppoppop',
            'Tools & Export',
            'Tools & Export',
            'manage_options',
            'wppoppop-tools',
            [$this, 'render_tools']
        );
    }

    public function enqueue_assets($hook) {
        if (strpos($hook, 'wppoppop') === false) {
            return;
        }

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

    public function render_dashboard() {
        include WPPOPPOP_PATH . 'templates/dashboard-view.php';
    }

    public function render_builder() {
        include WPPOPPOP_PATH . 'templates/builder-view.php';
    }

    public function render_tools() {
        include WPPOPPOP_PATH . 'templates/tools-view.php';
    }
}
