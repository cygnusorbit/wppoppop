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
            'Create Popup',
            'Create Popup',
            'manage_options',
            'wppoppop-builder',
            [$this, 'render_builder']
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

        wp_localize_script('wppoppop-builder-js', 'wppoppop_vars', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('wppoppop_builder_nonce')
        ]);
    }

    public function render_dashboard() {
        echo '<div class="wrap"><h1>WpPopPop Dashboard</h1><p>Select <a href="' . admin_url('admin.php?page=wppoppop-builder') . '">Create Popup</a> to open the builder.</p></div>';
    }

    public function render_builder() {
        include WPPOPPOP_PATH . 'templates/builder-view.php';
    }
}
