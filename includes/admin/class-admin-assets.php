<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Admin_Assets {
    public function __construct() {
        add_action('admin_enqueue_scripts', array($this, 'enqueue_assets'));
    }

    public function enqueue_assets($hook) {
        if (strpos($hook, 'wppoppop') === false && (!isset($_GET['page']) || strpos($_GET['page'], 'wppoppop') === false)) {
            return;
        }

        $page = isset($_GET['page']) ? sanitize_key($_GET['page']) : '';
        $current_uid = isset($_GET['uid']) ? sanitize_key($_GET['uid']) : '';

        // Shared localization payload
        $shared_payload = array(
            'ajax_url'      => admin_url('admin-ajax.php'),
            'home_url'      => home_url('/'),
            'admin_url'     => admin_url(),
            'site_url'      => site_url('/'),
            'nonce'         => wp_create_nonce('wppoppop_admin_nonce'),
            'builder_nonce' => wp_create_nonce('wppoppop_builder_nonce'),
            'uid'           => $current_uid,
            'rest_url'      => esc_url_raw(rest_url('wppoppop/v1/'))
        );

        // 1. Dashboard Page (admin.php?page=wppoppop)
        if ($page === 'wppoppop' || $hook === 'toplevel_page_wppoppop' || empty($page)) {
            wp_enqueue_style('dashicons');
            wp_enqueue_style('wppoppop-admin-css', WPPOPPOP_URL . 'admin/css/admin.css', array(), WPPOPPOP_VERSION);
            if (file_exists(WPPOPPOP_PATH . 'admin/css/dashboard.css')) {
                wp_enqueue_style('wppoppop-dashboard-css', WPPOPPOP_URL . 'admin/css/dashboard.css', array(), WPPOPPOP_VERSION);
            }

            wp_enqueue_script('jquery');
            wp_enqueue_script('wppoppop-dashboard-actions-js', WPPOPPOP_URL . 'admin/js/dashboard/dashboard-actions.js', array('jquery'), WPPOPPOP_VERSION, true);
            wp_enqueue_script('wppoppop-dashboard-import-js', WPPOPPOP_URL . 'admin/js/dashboard/dashboard-import.js', array('jquery'), WPPOPPOP_VERSION, true);
            wp_enqueue_script('wppoppop-dashboard-embed-js', WPPOPPOP_URL . 'admin/js/dashboard/dashboard-embed.js', array('jquery'), WPPOPPOP_VERSION, true);
            wp_enqueue_script('wppoppop-dashboard-table-js', WPPOPPOP_URL . 'admin/js/dashboard/dashboard-table.js', array('jquery'), WPPOPPOP_VERSION, true);
            wp_enqueue_script('wppoppop-dashboard-search-js', WPPOPPOP_URL . 'admin/js/dashboard/dashboard-search.js', array('jquery', 'wppoppop-dashboard-table-js'), WPPOPPOP_VERSION, true);

            wp_enqueue_script('wppoppop-dashboard-js', WPPOPPOP_URL . 'admin/js/dashboard.js', array(
                'jquery',
                'wppoppop-dashboard-actions-js',
                'wppoppop-dashboard-import-js',
                'wppoppop-dashboard-embed-js',
                'wppoppop-dashboard-table-js',
                'wppoppop-dashboard-search-js'
            ), WPPOPPOP_VERSION, true);

            wp_localize_script('wppoppop-dashboard-js', 'wppoppop_vars', $shared_payload);
        }

        // 2. Visual Builder Page (admin.php?page=wppoppop-builder)
        if ($page === 'wppoppop-builder') {
            wp_enqueue_style('dashicons');
            wp_enqueue_style('wppoppop-admin-css', WPPOPPOP_URL . 'admin/css/admin.css', array(), WPPOPPOP_VERSION);
            wp_enqueue_style('wppoppop-builder-css', WPPOPPOP_URL . 'admin/css/builder.css', array(), WPPOPPOP_VERSION);
            wp_enqueue_style('wppoppop-animate', 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css', array(), '4.1.1');

            wp_enqueue_script('jquery');
            wp_enqueue_script('jquery-ui-draggable');
            wp_enqueue_script('jquery-ui-resizable');
            wp_enqueue_script('jquery-ui-sortable');

            wp_enqueue_script('wppoppop-builder-core-js', WPPOPPOP_URL . 'admin/js/builder/builder-core.js', array('jquery'), WPPOPPOP_VERSION, true);
            wp_enqueue_script('wppoppop-builder-canvas-js', WPPOPPOP_URL . 'admin/js/builder/builder-canvas.js', array('wppoppop-builder-core-js'), WPPOPPOP_VERSION, true);
            wp_enqueue_script('wppoppop-builder-layers-js', WPPOPPOP_URL . 'admin/js/builder/builder-layers.js', array('wppoppop-builder-core-js'), WPPOPPOP_VERSION, true);
            wp_enqueue_script('wppoppop-builder-inspector-js', WPPOPPOP_URL . 'admin/js/builder/builder-inspector.js', array('wppoppop-builder-core-js'), WPPOPPOP_VERSION, true);
            wp_enqueue_script('wppoppop-builder-settings-js', WPPOPPOP_URL . 'admin/js/builder/builder-settings.js', array('wppoppop-builder-core-js'), WPPOPPOP_VERSION, true);
            wp_enqueue_script('wppoppop-builder-modals-js', WPPOPPOP_URL . 'admin/js/builder/builder-modals.js', array('wppoppop-builder-core-js'), WPPOPPOP_VERSION, true);
            wp_enqueue_script('wppoppop-builder-io-js', WPPOPPOP_URL . 'admin/js/builder/builder-io.js', array('wppoppop-builder-core-js'), WPPOPPOP_VERSION, true);
            wp_enqueue_script('wppoppop-builder-main-js', WPPOPPOP_URL . 'admin/js/builder.js', array(
                'wppoppop-builder-core-js',
                'wppoppop-builder-canvas-js',
                'wppoppop-builder-layers-js',
                'wppoppop-builder-inspector-js',
                'wppoppop-builder-settings-js',
                'wppoppop-builder-modals-js',
                'wppoppop-builder-io-js'
            ), WPPOPPOP_VERSION, true);

            wp_localize_script('wppoppop-builder-core-js', 'wppoppop_vars', $shared_payload);
        }

        // 3. Settings Page
        if ($page === 'wppoppop-settings') {
            wp_enqueue_style('wppoppop-settings-css', WPPOPPOP_URL . 'admin/css/settings.css', array(), WPPOPPOP_VERSION);
            wp_enqueue_script('wppoppop-settings-js', WPPOPPOP_URL . 'admin/js/settings.js', array('jquery'), WPPOPPOP_VERSION, true);
            wp_localize_script('wppoppop-settings-js', 'wppoppop_settings_vars', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce'    => wp_create_nonce('wppoppop_settings_nonce')
            ));
        }

        // 4. Tools Page
        if ($page === 'wppoppop-tools') {
            wp_enqueue_script('wppoppop-tools-system-js', WPPOPPOP_URL . 'admin/js/tools/tools-system.js', array('jquery'), WPPOPPOP_VERSION, true);
            wp_enqueue_script('wppoppop-tools-database-js', WPPOPPOP_URL . 'admin/js/tools/tools-database.js', array('jquery'), WPPOPPOP_VERSION, true);
            wp_enqueue_script('wppoppop-tools-portability-js', WPPOPPOP_URL . 'admin/js/tools/tools-portability.js', array('jquery'), WPPOPPOP_VERSION, true);
            wp_enqueue_script('wppoppop-tools-js', WPPOPPOP_URL . 'admin/js/tools.js', array('jquery', 'wppoppop-tools-system-js', 'wppoppop-tools-database-js', 'wppoppop-tools-portability-js'), WPPOPPOP_VERSION, true);
            wp_localize_script('wppoppop-tools-js', 'wppoppop_vars', $shared_payload);
        }

        // 5. Library Page
        if ($page === 'wppoppop-library') {
            wp_enqueue_style('wppoppop-library-css', WPPOPPOP_URL . 'admin/css/library.css', array(), WPPOPPOP_VERSION);
            wp_enqueue_script('wppoppop-library-js', WPPOPPOP_URL . 'admin/js/library.js', array('jquery'), WPPOPPOP_VERSION, true);
            wp_localize_script('wppoppop-library-js', 'wppoppop_vars', $shared_payload);
        }
    }
}
