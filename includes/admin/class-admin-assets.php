<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WpPopPop_Libraries')) {
    require_once WPPOPPOP_PATH . 'includes/class-wppoppop-libraries.php';
}

class WpPopPop_Admin_Assets {
    public function __construct() {
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    public function enqueue_assets($hook) {
        if (strpos($hook, 'wppoppop') === false && (!isset($_GET['page']) || strpos($_GET['page'], 'wppoppop') === false)) {
            return;
        }

        $page = isset($_GET['page']) ? sanitize_key($_GET['page']) : '';
        $current_uid = isset($_GET['uid']) ? sanitize_text_field(wp_unslash($_GET['uid'])) : '';

        // Shared general configuration
        $shared_payload = [
            'ajax_url'    => admin_url('admin-ajax.php'),
            'nonce'       => wp_create_nonce('wppoppop_admin_nonce'),
            'current_uid' => $current_uid
        ];

        // 1. Dashboard Assets
        if ($page === 'wppoppop' || $hook === 'toplevel_page_wppoppop') {
            wp_enqueue_style('wppoppop-dashboard-css', WPPOPPOP_URL . 'admin/css/dashboard.css', [], WPPOPPOP_VERSION);
            wp_enqueue_script('wppoppop-dashboard-actions-js', WPPOPPOP_URL . 'admin/js/dashboard/dashboard-actions.js', ['jquery'], WPPOPPOP_VERSION, true);
            wp_enqueue_script('wppoppop-dashboard-import-js', WPPOPPOP_URL . 'admin/js/dashboard/dashboard-import.js', ['jquery'], WPPOPPOP_VERSION, true);
            wp_enqueue_script('wppoppop-dashboard-embed-js', WPPOPPOP_URL . 'admin/js/dashboard/dashboard-embed.js', ['jquery'], WPPOPPOP_VERSION, true);
            wp_enqueue_script('wppoppop-dashboard-table-js', WPPOPPOP_URL . 'admin/js/dashboard/dashboard-table.js', ['jquery'], WPPOPPOP_VERSION, true);
            wp_enqueue_script('wppoppop-dashboard-search-js', WPPOPPOP_URL . 'admin/js/dashboard/dashboard-search.js', ['jquery', 'wppoppop-dashboard-table-js'], WPPOPPOP_VERSION, true);
            wp_enqueue_script('wppoppop-dashboard-js', WPPOPPOP_URL . 'admin/js/dashboard.js', [
                'jquery',
                'wppoppop-dashboard-actions-js',
                'wppoppop-dashboard-import-js',
                'wppoppop-dashboard-embed-js',
                'wppoppop-dashboard-table-js',
                'wppoppop-dashboard-search-js'
            ], WPPOPPOP_VERSION, true);

            wp_localize_script('wppoppop-dashboard-js', 'wppoppop_vars', $shared_payload);
        }

        // 2. Visual Builder Assets (Equipped with Typography and Custom Fonts)
        if ($page === 'wppoppop-builder') {
            wp_enqueue_style('wppoppop-builder-css', WPPOPPOP_URL . 'admin/css/builder.css', [], WPPOPPOP_VERSION);
            wp_enqueue_script('jquery-ui-draggable');
            wp_enqueue_script('jquery-ui-resizable');
            wp_enqueue_script('jquery-ui-sortable');

            // Enqueue active typography & Font Awesome for builder stage
            WpPopPop_Libraries::enqueue_builder_libraries();

            wp_enqueue_script('wppoppop-builder-core-js', WPPOPPOP_URL . 'admin/js/builder/builder-core.js', ['jquery'], WPPOPPOP_VERSION, true);
            wp_enqueue_script('wppoppop-builder-canvas-js', WPPOPPOP_URL . 'admin/js/builder/builder-canvas.js', ['jquery', 'jquery-ui-draggable', 'jquery-ui-resizable', 'wppoppop-builder-core-js'], WPPOPPOP_VERSION, true);
            wp_enqueue_script('wppoppop-builder-layers-js', WPPOPPOP_URL . 'admin/js/builder/builder-layers.js', ['jquery', 'jquery-ui-sortable', 'wppoppop-builder-core-js'], WPPOPPOP_VERSION, true);
            wp_enqueue_script('wppoppop-builder-inspector-js', WPPOPPOP_URL . 'admin/js/builder/builder-inspector.js', ['jquery', 'wppoppop-builder-core-js'], WPPOPPOP_VERSION, true);
            wp_enqueue_script('wppoppop-builder-settings-js', WPPOPPOP_URL . 'admin/js/builder/builder-settings.js', ['jquery', 'wppoppop-builder-core-js'], WPPOPPOP_VERSION, true);
            wp_enqueue_script('wppoppop-builder-modals-js', WPPOPPOP_URL . 'admin/js/builder/builder-modals.js', ['jquery', 'wppoppop-builder-core-js'], WPPOPPOP_VERSION, true);
            wp_enqueue_script('wppoppop-builder-io-js', WPPOPPOP_URL . 'admin/js/builder/builder-io.js', ['jquery', 'wppoppop-builder-core-js'], WPPOPPOP_VERSION, true);

            wp_enqueue_script('wppoppop-builder-js', WPPOPPOP_URL . 'admin/js/builder.js', [
                'jquery',
                'wppoppop-builder-core-js',
                'wppoppop-builder-canvas-js',
                'wppoppop-builder-layers-js',
                'wppoppop-builder-inspector-js',
                'wppoppop-builder-settings-js',
                'wppoppop-builder-modals-js',
                'wppoppop-builder-io-js'
            ], WPPOPPOP_VERSION, true);

            $builder_payload = array_merge($shared_payload, [
                'nonce'        => wp_create_nonce('wppoppop_builder_nonce'),
                'custom_fonts' => WpPopPop_Libraries::get_custom_font_families()
            ]);
            wp_localize_script('wppoppop-builder-core-js', 'wppoppop_vars', $builder_payload);
            wp_localize_script('wppoppop-builder-inspector-js', 'wppoppop_custom_fonts', WpPopPop_Libraries::get_custom_font_families());
            wp_localize_script('wppoppop-builder-io-js', 'wppoppop_vars', $builder_payload);
            wp_localize_script('wppoppop-builder-js', 'wppoppop_vars', $builder_payload);
        }

        // 3. Settings Assets
        if ($page === 'wppoppop-settings') {
            wp_enqueue_style('wppoppop-settings-css', WPPOPPOP_URL . 'admin/css/settings.css', [], WPPOPPOP_VERSION);

            wp_enqueue_script('wppoppop-settings-tabs-js', WPPOPPOP_URL . 'admin/js/settings/settings-tabs.js', ['jquery'], WPPOPPOP_VERSION, true);
            wp_enqueue_script('wppoppop-settings-save-js', WPPOPPOP_URL . 'admin/js/settings/settings-save.js', ['jquery', 'wppoppop-settings-tabs-js'], WPPOPPOP_VERSION, true);
            wp_enqueue_script('wppoppop-settings-tools-js', WPPOPPOP_URL . 'admin/js/settings/settings-tools.js', ['jquery'], WPPOPPOP_VERSION, true);

            wp_enqueue_script('wppoppop-settings-js', WPPOPPOP_URL . 'admin/js/settings.js', [
                'jquery',
                'wppoppop-settings-tabs-js',
                'wppoppop-settings-save-js',
                'wppoppop-settings-tools-js'
            ], WPPOPPOP_VERSION, true);

            $settings_payload = [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce'    => wp_create_nonce('wppoppop_settings_nonce')
            ];

            wp_localize_script('wppoppop-settings-js', 'wppoppop_settings_vars', $settings_payload);
            wp_localize_script('wppoppop-settings-save-js', 'wppoppop_settings_vars', $settings_payload);
            wp_localize_script('wppoppop-settings-tools-js', 'wppoppop_settings_vars', $settings_payload);
            wp_localize_script('wppoppop-settings-js', 'wppoppop_vars', $shared_payload);
        }

        // 4. Library Assets
        if ($page === 'wppoppop-library') {
            wp_enqueue_style('wppoppop-library-css', WPPOPPOP_URL . 'admin/css/library.css', [], WPPOPPOP_VERSION);
            wp_enqueue_script('wppoppop-library-js', WPPOPPOP_URL . 'admin/js/library.js', ['jquery'], WPPOPPOP_VERSION, true);
            wp_localize_script('wppoppop-library-js', 'wppoppop_vars', $shared_payload);
        }

        // 5. Tools & Portability Assets
        if ($page === 'wppoppop-tools') {
            wp_enqueue_script('wppoppop-tools-system-js', WPPOPPOP_URL . 'admin/js/tools/tools-system.js', ['jquery'], WPPOPPOP_VERSION, true);
            wp_enqueue_script('wppoppop-tools-database-js', WPPOPPOP_URL . 'admin/js/tools/tools-database.js', ['jquery'], WPPOPPOP_VERSION, true);
            wp_enqueue_script('wppoppop-tools-portability-js', WPPOPPOP_URL . 'admin/js/tools/tools-portability.js', ['jquery'], WPPOPPOP_VERSION, true);
            wp_enqueue_script('wppoppop-tools-js', WPPOPPOP_URL . 'admin/js/tools.js', ['jquery', 'wppoppop-tools-system-js', 'wppoppop-tools-database-js', 'wppoppop-tools-portability-js'], WPPOPPOP_VERSION, true);
            wp_localize_script('wppoppop-tools-js', 'wppoppop_vars', $shared_payload);
        }
    }
}
