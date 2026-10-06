<?php
if (!defined('ABSPATH')) {
    exit;
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

        $shared_payload = [
            'ajax_url'    => admin_url('admin-ajax.php'),
            'nonce'       => wp_create_nonce('wppoppop_builder_nonce'),
            'current_uid' => $current_uid
        ];

        // 1. Dashboard Assets (Segregated Sub-Modules)
        if ($page === 'wppoppop' || $hook === 'toplevel_page_wppoppop') {
            wp_enqueue_style('wppoppop-dashboard-css', WPPOPPOP_URL . 'admin/css/dashboard.css', [], WPPOPPOP_VERSION);

            wp_enqueue_script('wppoppop-dashboard-actions-js', WPPOPPOP_URL . 'admin/js/dashboard/dashboard-actions.js', ['jquery'], WPPOPPOP_VERSION, true);
            wp_enqueue_script('wppoppop-dashboard-import-js', WPPOPPOP_URL . 'admin/js/dashboard/dashboard-import.js', ['jquery'], WPPOPPOP_VERSION, true);
            wp_enqueue_script('wppoppop-dashboard-embed-js', WPPOPPOP_URL . 'admin/js/dashboard/dashboard-embed.js', ['jquery'], WPPOPPOP_VERSION, true);
            wp_enqueue_script('wppoppop-dashboard-search-js', WPPOPPOP_URL . 'admin/js/dashboard/dashboard-search.js', ['jquery'], WPPOPPOP_VERSION, true);

            wp_enqueue_script('wppoppop-dashboard-js', WPPOPPOP_URL . 'admin/js/dashboard.js', [
                'jquery',
                'wppoppop-dashboard-actions-js',
                'wppoppop-dashboard-import-js',
                'wppoppop-dashboard-embed-js',
                'wppoppop-dashboard-search-js'
            ], WPPOPPOP_VERSION, true);

            wp_localize_script('wppoppop-dashboard-actions-js', 'wppoppop_vars', $shared_payload);
        }

        // 2. Visual Builder Assets (Segregated Sub-Modules)
        if ($page === 'wppoppop-builder') {
            wp_enqueue_style('wppoppop-builder-css', WPPOPPOP_URL . 'admin/css/builder.css', [], WPPOPPOP_VERSION);
            wp_enqueue_script('jquery-ui-draggable');
            wp_enqueue_script('jquery-ui-resizable');
            wp_enqueue_script('jquery-ui-sortable');

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
                'nonce' => wp_create_nonce('wppoppop_builder_nonce')
            ]);
            wp_localize_script('wppoppop-builder-core-js', 'wppoppop_vars', $builder_payload);
        }

        // 3. Settings Assets (Segregated Sub-Modules)
        if ($page === 'wppoppop-settings') {
            wp_enqueue_style('wppoppop-settings-css', WPPOPPOP_URL . 'admin/css/settings.css', [], WPPOPPOP_VERSION);

            wp_enqueue_script('wppoppop-settings-tabs-js', WPPOPPOP_URL . 'admin/js/settings/settings-tabs.js', ['jquery'], WPPOPPOP_VERSION, true);
            wp_enqueue_script('wppoppop-settings-save-js', WPPOPPOP_URL . 'admin/js/settings/settings-save.js', ['jquery'], WPPOPPOP_VERSION, true);
            wp_enqueue_script('wppoppop-settings-tools-js', WPPOPPOP_URL . 'admin/js/settings/settings-tools.js', ['jquery'], WPPOPPOP_VERSION, true);

            wp_enqueue_script('wppoppop-settings-js', WPPOPPOP_URL . 'admin/js/settings.js', [
                'jquery',
                'wppoppop-settings-tabs-js',
                'wppoppop-settings-save-js',
                'wppoppop-settings-tools-js'
            ], WPPOPPOP_VERSION, true);

            wp_localize_script('wppoppop-settings-tabs-js', 'wppoppop_settings_vars', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce'    => wp_create_nonce('wppoppop_settings_nonce')
            ]);
        }

        // 4. Library Assets (Segregated Sub-Modules)
        if ($page === 'wppoppop-library') {
            wp_enqueue_style('wppoppop-library-css', WPPOPPOP_URL . 'admin/css/library.css', [], WPPOPPOP_VERSION);

            wp_enqueue_script('wppoppop-library-filter-js', WPPOPPOP_URL . 'admin/js/library/library-filter.js', ['jquery'], WPPOPPOP_VERSION, true);
            wp_enqueue_script('wppoppop-library-preview-js', WPPOPPOP_URL . 'admin/js/library/library-preview.js', ['jquery'], WPPOPPOP_VERSION, true);
            wp_enqueue_script('wppoppop-library-import-js', WPPOPPOP_URL . 'admin/js/library/library-import.js', ['jquery'], WPPOPPOP_VERSION, true);

            wp_enqueue_script('wppoppop-library-js', WPPOPPOP_URL . 'admin/js/library.js', [
                'jquery',
                'wppoppop-library-filter-js',
                'wppoppop-library-preview-js',
                'wppoppop-library-import-js'
            ], WPPOPPOP_VERSION, true);

            wp_localize_script('wppoppop-library-import-js', 'wppoppop_vars', $shared_payload);
        }

        // 5. Submissions Assets (Segregated Sub-Modules)
        if ($page === 'wppoppop-submissions') {
            wp_enqueue_script('wppoppop-submissions-search-js', WPPOPPOP_URL . 'admin/js/submissions/submissions-search.js', ['jquery'], WPPOPPOP_VERSION, true);
            wp_enqueue_script('wppoppop-submissions-actions-js', WPPOPPOP_URL . 'admin/js/submissions/submissions-actions.js', ['jquery'], WPPOPPOP_VERSION, true);
            wp_enqueue_script('wppoppop-submissions-modal-js', WPPOPPOP_URL . 'admin/js/submissions/submissions-modal.js', ['jquery'], WPPOPPOP_VERSION, true);

            wp_enqueue_script('wppoppop-submissions-js', WPPOPPOP_URL . 'admin/js/submissions.js', [
                'jquery',
                'wppoppop-submissions-search-js',
                'wppoppop-submissions-actions-js',
                'wppoppop-submissions-modal-js'
            ], WPPOPPOP_VERSION, true);

            wp_localize_script('wppoppop-submissions-actions-js', 'wppoppop_vars', $shared_payload);
        }

        // 6. A/B Testing Assets (Segregated Sub-Modules)
        if ($page === 'wppoppop-ab') {
            wp_enqueue_script('wppoppop-ab-modal-js', WPPOPPOP_URL . 'admin/js/ab/ab-modal.js', ['jquery'], WPPOPPOP_VERSION, true);
            wp_enqueue_script('wppoppop-ab-actions-js', WPPOPPOP_URL . 'admin/js/ab/ab-actions.js', ['jquery'], WPPOPPOP_VERSION, true);

            wp_enqueue_script('wppoppop-ab-js', WPPOPPOP_URL . 'admin/js/ab.js', [
                'jquery',
                'wppoppop-ab-modal-js',
                'wppoppop-ab-actions-js'
            ], WPPOPPOP_VERSION, true);

            wp_localize_script('wppoppop-ab-actions-js', 'wppoppop_vars', $shared_payload);
        }

        // 7. Payments Assets (Segregated Sub-Modules)
        if ($page === 'wppoppop-payments') {
            wp_enqueue_script('wppoppop-payments-search-js', WPPOPPOP_URL . 'admin/js/payments/payments-search.js', ['jquery'], WPPOPPOP_VERSION, true);
            wp_enqueue_script('wppoppop-payments-modal-js', WPPOPPOP_URL . 'admin/js/payments/payments-modal.js', ['jquery'], WPPOPPOP_VERSION, true);
            wp_enqueue_script('wppoppop-payments-export-js', WPPOPPOP_URL . 'admin/js/payments/payments-export.js', ['jquery'], WPPOPPOP_VERSION, true);

            wp_enqueue_script('wppoppop-payments-js', WPPOPPOP_URL . 'admin/js/payments.js', [
                'jquery',
                'wppoppop-payments-search-js',
                'wppoppop-payments-modal-js',
                'wppoppop-payments-export-js'
            ], WPPOPPOP_VERSION, true);
        }

        // 8. Activity Log Assets (Segregated Sub-Modules)
        if ($page === 'wppoppop-log') {
            wp_enqueue_script('wppoppop-log-search-js', WPPOPPOP_URL . 'admin/js/log/log-search.js', ['jquery'], WPPOPPOP_VERSION, true);
            wp_enqueue_script('wppoppop-log-payload-js', WPPOPPOP_URL . 'admin/js/log/log-payload.js', ['jquery'], WPPOPPOP_VERSION, true);

            wp_enqueue_script('wppoppop-log-js', WPPOPPOP_URL . 'admin/js/log.js', [
                'jquery',
                'wppoppop-log-search-js',
                'wppoppop-log-payload-js'
            ], WPPOPPOP_VERSION, true);
        }
    }
}
