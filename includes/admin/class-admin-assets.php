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

    /* Cache-friendly asset version: plugin version; adds a cache-buster only when WP_DEBUG is on. */
    protected static function ver() {
        $v = defined('WPPOPPOP_VERSION') ? WPPOPPOP_VERSION : '1.0';
        return (defined('WP_DEBUG') && WP_DEBUG) ? $v . '.' . time() : $v;
    }

    public function enqueue_assets($hook) {
        if (strpos($hook, 'wppoppop') === false && (!isset($_GET['page']) || strpos($_GET['page'], 'wppoppop') === false)) {
            return;
        }

        $page = isset($_GET['page']) ? sanitize_key($_GET['page']) : '';
        $current_uid = isset($_GET['uid']) ? sanitize_text_field(wp_unslash($_GET['uid'])) : '';

        // 0. Global Theme (all WpPopPop admin pages) + Theme page assets
        if (class_exists('WpPopPop_Admin_Theme')) {
            WpPopPop_Admin_Theme::enqueue_global();
            if ($page === WpPopPop_Admin_Theme::PAGE) {
                WpPopPop_Admin_Theme::enqueue_page();
            }
        }

        // Shared general configuration
        $shared_payload = [
            'ajax_url'    => admin_url('admin-ajax.php'),
            'nonce'       => wp_create_nonce('wppoppop_admin_nonce'),
            'current_uid' => $current_uid
        ];

        // 1. Dashboard Assets
        if ($page === 'wppoppop' || $hook === 'toplevel_page_wppoppop') {
            wp_enqueue_style('wppoppop-dashboard-css', WPPOPPOP_URL . 'admin/css/dashboard.css', [], self::ver());
            wp_enqueue_script('wppoppop-dashboard-actions-js', WPPOPPOP_URL . 'admin/js/dashboard/dashboard-actions.js', ['jquery'], self::ver(), true);
            wp_enqueue_script('wppoppop-dashboard-import-js', WPPOPPOP_URL . 'admin/js/dashboard/dashboard-import.js', ['jquery'], self::ver(), true);
            wp_enqueue_script('wppoppop-dashboard-embed-js', WPPOPPOP_URL . 'admin/js/dashboard/dashboard-embed.js', ['jquery'], self::ver(), true);
            wp_enqueue_script('wppoppop-dashboard-table-js', WPPOPPOP_URL . 'admin/js/dashboard/dashboard-table.js', ['jquery'], self::ver(), true);
            wp_enqueue_script('wppoppop-dashboard-search-js', WPPOPPOP_URL . 'admin/js/dashboard/dashboard-search.js', ['jquery', 'wppoppop-dashboard-table-js'], self::ver(), true);
            wp_enqueue_script('wppoppop-dashboard-js', WPPOPPOP_URL . 'admin/js/dashboard.js', [
                'jquery',
                'wppoppop-dashboard-actions-js',
                'wppoppop-dashboard-import-js',
                'wppoppop-dashboard-embed-js',
                'wppoppop-dashboard-table-js',
                'wppoppop-dashboard-search-js'
            ], self::ver(), true);

            wp_localize_script('wppoppop-dashboard-js', 'wppoppop_vars', $shared_payload);
            wp_enqueue_script('wppoppop-dashboard-quickedit-js', WPPOPPOP_URL . 'admin/js/dashboard/dashboard-quickedit.js', ['jquery', 'wppoppop-dashboard-js'], self::ver(), true);
            wp_enqueue_script('wppoppop-dashboard-bulk-js', WPPOPPOP_URL . 'admin/js/dashboard/dashboard-bulk.js', ['jquery', 'wppoppop-dashboard-js'], self::ver(), true);
        }

        // 2. Visual Builder Assets (Equipped with Typography and Custom Fonts)
        if ($page === 'wppoppop-builder') {
            wp_enqueue_style('wppoppop-builder-css', WPPOPPOP_URL . 'admin/css/builder.css', [], self::ver());
            wp_enqueue_script('jquery-ui-draggable');
            wp_enqueue_script('jquery-ui-resizable');
            wp_enqueue_script('jquery-ui-sortable');

            // Enqueue active typography & Font Awesome for builder stage
            WpPopPop_Libraries::enqueue_builder_libraries();
            wp_enqueue_style('wppoppop-animations-core-css', WPPOPPOP_URL . 'public/css/animations-core.css', [], (defined('WPPOPPOP_VERSION') ? WPPOPPOP_VERSION : '1.0'));
            wp_enqueue_script('wppoppop-builder-animation-js', WPPOPPOP_URL . 'admin/js/builder/builder-animation.js', ['jquery', 'wppoppop-builder-core-js'], self::ver(), true);

            if (function_exists('wp_enqueue_media')) { wp_enqueue_media(); }
        wp_enqueue_script('wppoppop-builder-core-js', WPPOPPOP_URL . 'admin/js/builder/builder-core.js', ['jquery'], self::ver(), true);
            wp_enqueue_script('wppoppop-builder-canvas-js', WPPOPPOP_URL . 'admin/js/builder/builder-canvas.js', ['jquery', 'jquery-ui-draggable', 'jquery-ui-resizable', 'wppoppop-builder-core-js', 'jquery-ui-droppable'], self::ver(), true);
            wp_enqueue_script('wppoppop-builder-layers-js', WPPOPPOP_URL . 'admin/js/builder/builder-layers.js', ['jquery', 'jquery-ui-sortable', 'wppoppop-builder-core-js'], self::ver(), true);
            wp_enqueue_script('wppoppop-builder-inspector-js', WPPOPPOP_URL . 'admin/js/builder/builder-inspector.js', ['jquery', 'wppoppop-builder-core-js'], self::ver(), true);
        $wppoppop_reg_mods = array('registry-core', 'registry-style', 'registry-media', 'registry-label', 'registry-form', 'registry-lifecycle', 'registry-token', 'registry-compat', 'registry-inspector', 'registry-icons');
        $wppoppop_reg_prev = array('jquery');
        foreach ($wppoppop_reg_mods as $wppoppop_reg_i => $wppoppop_reg_m) {
            $wppoppop_reg_h = ($wppoppop_reg_i === count($wppoppop_reg_mods) - 1) ? 'wppoppop-builder-elements-registry' : 'wppoppop-' . $wppoppop_reg_m;
            wp_enqueue_script($wppoppop_reg_h, WPPOPPOP_URL . 'admin/js/builder/registry/' . $wppoppop_reg_m . '.js', $wppoppop_reg_prev, self::ver(), true);
            $wppoppop_reg_prev = array($wppoppop_reg_h);
        }
            wp_enqueue_script('wppoppop-builder-settings-js', WPPOPPOP_URL . 'admin/js/builder/builder-settings.js', ['jquery', 'wppoppop-builder-core-js'], self::ver(), true);
            wp_enqueue_script('wppoppop-builder-modals-js', WPPOPPOP_URL . 'admin/js/builder/builder-modals.js', ['jquery', 'wppoppop-builder-core-js'], self::ver(), true);
            wp_enqueue_script('wppoppop-builder-io-js', WPPOPPOP_URL . 'admin/js/builder/builder-io.js', ['jquery', 'wppoppop-builder-core-js'], self::ver(), true);

            wp_enqueue_script('wppoppop-builder-js', WPPOPPOP_URL . 'admin/js/builder.js', [
                'jquery',
                'wppoppop-builder-core-js',
                'wppoppop-builder-canvas-js',
                'wppoppop-builder-layers-js',
                'wppoppop-builder-inspector-js',
                'wppoppop-builder-settings-js',
                'wppoppop-builder-modals-js',
                'wppoppop-builder-io-js', 'wppoppop-builder-elements-registry', 'wppoppop-builder-animation-js'
            ], self::ver(), true);

            wp_enqueue_script('wppoppop-builder-preview-js', WPPOPPOP_URL . 'admin/js/builder/builder-preview.js', ['jquery', 'wppoppop-builder-core-js', 'wppoppop-builder-canvas-js', 'wppoppop-builder-io-js'], self::ver(), true);

            $builder_payload = array_merge($shared_payload, [
                'nonce'        => wp_create_nonce('wppoppop_builder_nonce'),
                'custom_fonts' => WpPopPop_Libraries::get_custom_font_families()
            ]);
            wp_localize_script('wppoppop-builder-core-js', 'wppoppop_vars', $builder_payload);
            wp_localize_script('wppoppop-builder-inspector-js', 'wppoppop_custom_fonts', WpPopPop_Libraries::get_custom_font_families());
            wp_localize_script('wppoppop-builder-io-js', 'wppoppop_vars', $builder_payload);
            wp_localize_script('wppoppop-builder-js', 'wppoppop_vars', $builder_payload);
            wp_localize_script('wppoppop-builder-preview-js', 'wppoppop_vars', $builder_payload);
        }

        // 3. Settings Assets
        if ($page === 'wppoppop-settings') {
            $set_ver = self::ver();
            wp_enqueue_style('dashicons');
            wp_enqueue_style('wppoppop-dashboard-css', WPPOPPOP_URL . 'admin/css/dashboard.css', [], $set_ver);
            wp_enqueue_style('wppoppop-overview-css', WPPOPPOP_URL . 'admin/css/overview.css', [], $set_ver);
            wp_enqueue_script('wppoppop-overview-js', WPPOPPOP_URL . 'admin/js/overview.js', ['jquery'], $set_ver, true);
            wp_enqueue_style('wppoppop-settings-css', WPPOPPOP_URL . 'admin/css/settings.css', ['wppoppop-dashboard-css', 'wppoppop-overview-css', 'wppoppop-theme-global-css'], $set_ver);

            wp_enqueue_script('wppoppop-settings-tabs-js', WPPOPPOP_URL . 'admin/js/settings/settings-tabs.js', ['jquery'], self::ver(), true);
            wp_enqueue_script('wppoppop-settings-save-js', WPPOPPOP_URL . 'admin/js/settings/settings-save.js', ['jquery', 'wppoppop-settings-tabs-js'], self::ver(), true);
            wp_enqueue_script('wppoppop-settings-tools-js', WPPOPPOP_URL . 'admin/js/settings/settings-tools.js', ['jquery'], self::ver(), true);

            wp_enqueue_script('wppoppop-settings-js', WPPOPPOP_URL . 'admin/js/settings.js', [
                'jquery',
                'wppoppop-settings-tabs-js',
                'wppoppop-settings-save-js',
                'wppoppop-settings-tools-js'
            ], self::ver(), true);

            $settings_payload = [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce'    => wp_create_nonce('wppoppop_settings_nonce')
            ];

            wp_localize_script('wppoppop-settings-js', 'wppoppop_settings_vars', $settings_payload);
            wp_localize_script('wppoppop-settings-js', 'wppoppop_vars', $shared_payload);
        }

        // 4. Library Assets
        if ($page === 'wppoppop-library') {
            wp_enqueue_style('wppoppop-library-css', WPPOPPOP_URL . 'admin/css/library.css', [], self::ver());
            wp_enqueue_script('wppoppop-library-js', WPPOPPOP_URL . 'admin/js/library.js', ['jquery'], self::ver(), true);
            wp_localize_script('wppoppop-library-js', 'wppoppop_vars', $shared_payload);
        }

        // Dashboard overview page (separate from the Popups list assets)
        if (in_array($page, ['wppoppop-dashboard', 'wppoppop'], true)) {
            $ov_ver = self::ver();
            wp_enqueue_style('dashicons');
            wp_enqueue_style('wppoppop-overview-css', WPPOPPOP_URL . 'admin/css/overview.css', [], $ov_ver);
            wp_enqueue_script('wppoppop-overview-js', WPPOPPOP_URL . 'admin/js/overview.js', ['jquery'], $ov_ver, true);
        }

        // A/B Campaigns Assets (previously never enqueued -> buttons were dead, no nonce)
        if ($page === 'wppoppop-ab') {
            $ab_ver = self::ver();
            wp_enqueue_style('dashicons');
            wp_enqueue_style('wppoppop-overview-css', WPPOPPOP_URL . 'admin/css/overview.css', [], $ab_ver);
            wp_enqueue_style('wppoppop-ab-css', WPPOPPOP_URL . 'admin/css/ab.css', ['wppoppop-overview-css'], $ab_ver);
            wp_enqueue_script('wppoppop-overview-js', WPPOPPOP_URL . 'admin/js/overview.js', ['jquery'], $ab_ver, true);
            wp_enqueue_script('wppoppop-ab-modal-js', WPPOPPOP_URL . 'admin/js/ab/ab-modal.js', ['jquery'], $ab_ver, true);
            wp_enqueue_script('wppoppop-ab-actions-js', WPPOPPOP_URL . 'admin/js/ab/ab-actions.js', ['jquery'], $ab_ver, true);
            wp_enqueue_script('wppoppop-ab-js', WPPOPPOP_URL . 'admin/js/ab.js', ['jquery', 'wppoppop-ab-modal-js', 'wppoppop-ab-actions-js'], $ab_ver, true);
            wp_localize_script('wppoppop-ab-js', 'wppoppop_vars', $shared_payload);
            wp_enqueue_script('wppoppop-ab-edit-js', WPPOPPOP_URL . 'admin/js/ab/ab-edit.js', ['jquery', 'wppoppop-ab-js'], $ab_ver, true);
        }

        // 6. Submissions Assets
        if ($page === 'wppoppop-submissions') {
            $sub_ver = self::ver();
            wp_enqueue_style('dashicons');
            wp_enqueue_style('wppoppop-dashboard-css', WPPOPPOP_URL . 'admin/css/dashboard.css', [], $sub_ver);
            wp_enqueue_style('wppoppop-overview-css', WPPOPPOP_URL . 'admin/css/overview.css', [], $sub_ver);
            wp_enqueue_style('wppoppop-submissions-css', WPPOPPOP_URL . 'admin/css/submissions.css', ['wppoppop-dashboard-css', 'wppoppop-overview-css'], $sub_ver);
            wp_enqueue_script('wppoppop-overview-js', WPPOPPOP_URL . 'admin/js/overview.js', ['jquery'], $sub_ver, true);
            wp_enqueue_script('wppoppop-submissions-search-js', WPPOPPOP_URL . 'admin/js/submissions/submissions-search.js', ['jquery'], $sub_ver, true);
            wp_enqueue_script('wppoppop-submissions-actions-js', WPPOPPOP_URL . 'admin/js/submissions/submissions-actions.js', ['jquery'], $sub_ver, true);
            wp_enqueue_script('wppoppop-submissions-modal-js', WPPOPPOP_URL . 'admin/js/submissions/submissions-modal.js', ['jquery'], $sub_ver, true);
            wp_enqueue_script('wppoppop-submissions-table-options-js', WPPOPPOP_URL . 'admin/js/submissions/submissions-table-options.js', ['jquery', 'jquery-ui-sortable'], $sub_ver, true);
            wp_enqueue_script('wppoppop-submissions-list-js', WPPOPPOP_URL . 'admin/js/submissions/submissions-list.js', ['jquery'], $sub_ver, true);
            wp_enqueue_script('wppoppop-submissions-labels-js', WPPOPPOP_URL . 'admin/js/submissions/submissions-labels.js', ['jquery'], $sub_ver, true);
            wp_enqueue_script('wppoppop-submissions-js', WPPOPPOP_URL . 'admin/js/submissions.js', [
                'jquery',
                'wppoppop-submissions-search-js',
                'wppoppop-submissions-actions-js',
                'wppoppop-submissions-modal-js',
                'wppoppop-submissions-list-js'
            ], $sub_ver, true);
            wp_localize_script('wppoppop-submissions-js', 'wppoppop_vars', array_merge($shared_payload, [
                'nonce' => wp_create_nonce('wppoppop_builder_nonce')
            ]));
        }

        // 5. Tools & Portability Assets
        if ($page === 'wppoppop-tools') {
            wp_enqueue_script('wppoppop-tools-system-js', WPPOPPOP_URL . 'admin/js/tools/tools-system.js', ['jquery'], self::ver(), true);
            wp_enqueue_script('wppoppop-tools-database-js', WPPOPPOP_URL . 'admin/js/tools/tools-database.js', ['jquery'], self::ver(), true);
            wp_enqueue_script('wppoppop-tools-portability-js', WPPOPPOP_URL . 'admin/js/tools/tools-portability.js', ['jquery'], self::ver(), true);
            wp_enqueue_script('wppoppop-tools-js', WPPOPPOP_URL . 'admin/js/tools.js', ['jquery', 'wppoppop-tools-system-js', 'wppoppop-tools-database-js', 'wppoppop-tools-portability-js'], self::ver(), true);
            wp_localize_script('wppoppop-tools-js', 'wppoppop_vars', $shared_payload);
        }
    }
}
