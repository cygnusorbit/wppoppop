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

    /** Shared module-manager UI (Module tab + Extend page): stylesheet, API script and its localized data. */
    protected function enqueue_modules_ui($ver, array $css_deps = [], array $extra = []) {
        wp_enqueue_style('dashicons');
        wp_enqueue_style('wppoppop-modules-css', WPPOPPOP_URL . 'admin/css/modules.css', $css_deps, $ver);
        wp_enqueue_style('wppoppop-module-categories-css', WPPOPPOP_URL . 'admin/css/module-categories.css', ['wppoppop-modules-css'], $ver);
        wp_enqueue_script('wppoppop-modules-api-js', WPPOPPOP_URL . 'admin/js/modules/modules-api.js', ['jquery'], $ver, true);
        wp_localize_script('wppoppop-modules-api-js', 'wppoppop_modules_vars', WpPopPop_Modules_Installer::js_vars($extra));
    }

    public function enqueue_assets($hook) {
        $wppoppop_page = isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : '';
        if (strpos($hook, 'wppoppop') === false && strpos($wppoppop_page, 'wppoppop') === false) {
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
        WpPopPop_Promo::enqueue();

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
            $wppoppop_st_file = WPPOPPOP_PATH . 'admin/css/builder/save-template.css';
            wp_enqueue_style('wppoppop-builder-save-template-css', WPPOPPOP_URL . 'admin/css/builder/save-template.css', ['wppoppop-builder-css'], file_exists($wppoppop_st_file) ? (string) filemtime($wppoppop_st_file) : self::ver());
            $wppoppop_ts_bld = WPPOPPOP_PATH . 'admin/css/library/theme-sync-modals.css';
            wp_enqueue_style('wppoppop-builder-theme-modals-css', WPPOPPOP_URL . 'admin/css/library/theme-sync-modals.css', ['wppoppop-builder-save-template-css'], file_exists($wppoppop_ts_bld) ? (string) filemtime($wppoppop_ts_bld) : self::ver());
            wp_enqueue_script('jquery-ui-draggable');
            wp_enqueue_script('jquery-ui-resizable');
            wp_enqueue_script('jquery-ui-sortable');

            // Enqueue active typography & Font Awesome for builder stage
            WpPopPop_Libraries::enqueue_builder_libraries();
            wp_enqueue_style('wppoppop-animations-core-css', WPPOPPOP_URL . 'public/css/animations-core.css', [], (defined('WPPOPPOP_VERSION') ? WPPOPPOP_VERSION : '1.0'));
            wp_enqueue_script('wppoppop-builder-animation-js', WPPOPPOP_URL . 'admin/js/builder/builder-animation.js', ['jquery', 'wppoppop-builder-core-js'], self::ver(), true);

            if (function_exists('wp_enqueue_media')) { wp_enqueue_media(); }
        wp_enqueue_script('wppoppop-builder-element-ext-js', WPPOPPOP_URL . 'admin/js/builder/builder-element-ext.js', [], self::ver(), true);
        wp_enqueue_script('wppoppop-builder-core-js', WPPOPPOP_URL . 'admin/js/builder/builder-core.js', ['jquery', 'wppoppop-builder-element-ext-js'], self::ver(), true);
            wp_enqueue_script('wppoppop-builder-spawn-place-js', WPPOPPOP_URL . 'admin/js/builder/builder-spawn-place.js', [], self::ver(), true);
            wp_enqueue_script('wppoppop-builder-canvas-js', WPPOPPOP_URL . 'admin/js/builder/builder-canvas.js', ['jquery', 'jquery-ui-draggable', 'jquery-ui-resizable', 'wppoppop-builder-core-js', 'jquery-ui-droppable', 'wppoppop-builder-spawn-place-js'], self::ver(), true);
            wp_enqueue_script('wppoppop-builder-layers-js', WPPOPPOP_URL . 'admin/js/builder/builder-layers.js', ['jquery', 'jquery-ui-sortable', 'wppoppop-builder-core-js'], self::ver(), true);
            wp_enqueue_script('wppoppop-style-toggles-js', WPPOPPOP_URL . 'admin/js/builder/registry/registry-style-toggles.js', ['jquery', 'wppoppop-builder-core-js', 'wppoppop-builder-canvas-js'], self::ver(), true);
            wp_enqueue_script('wppoppop-style-stable-js', WPPOPPOP_URL . 'admin/js/builder/registry/registry-style-stable.js', ['jquery', 'wppoppop-builder-core-js', 'wppoppop-builder-canvas-js'], self::ver(), true);
            wp_enqueue_script('wppoppop-builder-hotkeys-js', WPPOPPOP_URL . 'admin/js/builder/builder-hotkeys.js', ['jquery', 'wppoppop-builder-core-js', 'wppoppop-builder-canvas-js', 'wppoppop-builder-layers-js'], self::ver(), true);
            wp_enqueue_script('wppoppop-builder-inspector-js', WPPOPPOP_URL . 'admin/js/builder/builder-inspector.js', ['jquery', 'wppoppop-builder-core-js'], self::ver(), true);
            wp_enqueue_script('wppoppop-builder-data-tab-sync-js', WPPOPPOP_URL . 'admin/js/builder/builder-data-tab-sync.js', ['jquery', 'wppoppop-builder-inspector-js'], self::ver(), true);
        $wppoppop_reg_mods = array('registry-core', 'registry-style', 'registry-media', 'registry-label', 'registry-form', 'registry-lifecycle', 'registry-token', 'registry-compat', 'registry-inspector', 'registry-icons');
        $wppoppop_reg_prev = array('jquery');
        foreach ($wppoppop_reg_mods as $wppoppop_reg_i => $wppoppop_reg_m) {
            $wppoppop_reg_h = ($wppoppop_reg_i === count($wppoppop_reg_mods) - 1) ? 'wppoppop-builder-elements-registry' : 'wppoppop-' . $wppoppop_reg_m;
            wp_enqueue_script($wppoppop_reg_h, WPPOPPOP_URL . 'admin/js/builder/registry/' . $wppoppop_reg_m . '.js', $wppoppop_reg_prev, self::ver(), true);
            $wppoppop_reg_prev = array($wppoppop_reg_h);
        }
        wp_enqueue_script('wppoppop-builder-video-inspector-js', WPPOPPOP_URL . 'admin/js/builder/elements/video-inspector.js', ['jquery', 'wppoppop-builder-core-js', 'wppoppop-builder-elements-registry'], self::ver(), true);
        wp_enqueue_script('wppoppop-builder-image-inspector-js', WPPOPPOP_URL . 'admin/js/builder/elements/image-inspector.js', ['jquery', 'wppoppop-builder-core-js', 'wppoppop-builder-elements-registry'], self::ver(), true);
        wp_enqueue_script('wppoppop-builder-shape-inspector-js', WPPOPPOP_URL . 'admin/js/builder/elements/shape-inspector.js', ['jquery', 'wppoppop-builder-core-js', 'wppoppop-builder-elements-registry'], self::ver(), true);
            wp_enqueue_script('wppoppop-builder-settings-js', WPPOPPOP_URL . 'admin/js/builder/builder-settings.js', ['jquery', 'wppoppop-builder-core-js'], self::ver(), true);
            if (class_exists('WpPopPop_Modules_Engine')) { WpPopPop_Modules_Engine::enqueue_builder(); }
            wp_enqueue_script('wppoppop-builder-modals-js', WPPOPPOP_URL . 'admin/js/builder/builder-modals.js', ['jquery', 'wppoppop-builder-core-js'], self::ver(), true);
            wp_enqueue_script('wppoppop-builder-io-js', WPPOPPOP_URL . 'admin/js/builder/builder-io.js', ['jquery', 'wppoppop-builder-core-js'], self::ver(), true);

            wp_enqueue_script('wppoppop-builder-js', WPPOPPOP_URL . 'admin/js/builder.js', [
                'jquery',
                'wppoppop-builder-core-js',
                'wppoppop-builder-canvas-js',
                'wppoppop-builder-layers-js',
                'wppoppop-builder-inspector-js',
                'wppoppop-builder-settings-js',
                'wppoppop-builder-ext-js',
                'wppoppop-builder-modals-js',
                'wppoppop-builder-io-js', 'wppoppop-builder-elements-registry', 'wppoppop-builder-animation-js'
            ], self::ver(), true);

            wp_enqueue_script('wppoppop-builder-preview-js', WPPOPPOP_URL . 'admin/js/builder/builder-preview.js', ['jquery', 'wppoppop-builder-core-js', 'wppoppop-builder-canvas-js', 'wppoppop-builder-io-js'], self::ver(), true);
            wp_enqueue_media();
            wp_enqueue_script('wppoppop-builder-template-js', WPPOPPOP_URL . 'admin/js/builder/builder-template.js', ['jquery', 'wppoppop-builder-core-js', 'wppoppop-builder-io-js', 'wppoppop-builder-settings-js'], self::ver(), true);
            wp_enqueue_script('wppoppop-builder-save-state-js', WPPOPPOP_URL . 'admin/js/builder/builder-save-state.js', ['jquery', 'wppoppop-builder-core-js', 'wppoppop-builder-io-js', 'wppoppop-builder-settings-js'], self::ver(), true);

            wp_enqueue_script('wppoppop-builder-button-canvas-select-js', WPPOPPOP_URL . 'admin/js/builder/builder-button-canvas-select.js', ['jquery', 'wppoppop-builder-core-js', 'wppoppop-builder-inspector-js', 'wppoppop-builder-elements-registry'], self::ver(), true);
            wp_enqueue_script('wppoppop-builder-ribbon-toggle-js', WPPOPPOP_URL . 'admin/js/builder/builder-ribbon-toggle.js', ['jquery', 'wppoppop-builder-core-js'], self::ver(), true);
            wp_enqueue_script('wppoppop-builder-align-dock-js', WPPOPPOP_URL . 'admin/js/builder/builder-align-dock.js', ['jquery', 'wppoppop-builder-core-js'], self::ver(), true);
            wp_enqueue_script('wppoppop-builder-history-fix-js', WPPOPPOP_URL . 'admin/js/builder/builder-history-fix.js', ['jquery', 'wppoppop-builder-core-js', 'wppoppop-builder-settings-js', 'wppoppop-builder-io-js'], self::ver(), true); /* WPPOPPOP_HISTORY_FIX */
            wp_enqueue_script('wppoppop-builder-nudge-js', WPPOPPOP_URL . 'admin/js/builder/builder-nudge.js', ['jquery', 'wppoppop-builder-core-js'], self::ver(), true);
            wp_enqueue_script('wppoppop-builder-click-open-js', WPPOPPOP_URL . 'admin/js/builder/builder-click-open.js', ['jquery', 'wppoppop-builder-core-js'], self::ver(), true);
            wp_enqueue_script('wppoppop-builder-panel-memory-js', WPPOPPOP_URL . 'admin/js/builder/builder-panel-memory.js', ['jquery', 'wppoppop-builder-core-js', 'wppoppop-builder-inspector-js', 'wppoppop-builder-settings-js', 'wppoppop-builder-io-js'], self::ver(), true); /* WPPOPPOP_PANEL_MEMORY */
            wp_enqueue_script('wppoppop-builder-ribbon-tooltip-js', WPPOPPOP_URL . 'admin/js/builder/builder-ribbon-tooltip.js', [], self::ver(), true);

            $builder_payload = array_merge($shared_payload, [
                'nonce'        => wp_create_nonce('wppoppop_builder_nonce'),
                'save_template' => (function () {
                    $s = get_option('wppoppop_settings', []);
                    return (is_array($s) && !empty($s['builder_save_template'])) ? 1 : 0;
                })(),
                'alignment_tool' => (function () {
                    $s = get_option('wppoppop_settings', []);
                    return (is_array($s) && !empty($s['builder_alignment_tool'])) ? 1 : 0;
                })(),
                'click_canvas_open_settings' => (function () {
                    $s = get_option('wppoppop_settings', []);
                    return (is_array($s) && !empty($s['builder_click_canvas_settings'])) ? 1 : 0;
                })(),
                'click_element_open_settings' => (function () {
                    $s = get_option('wppoppop_settings', []);
                    return (is_array($s) && !empty($s['builder_click_element_settings'])) ? 1 : 0;
                })(),
                'custom_fonts' => WpPopPop_Libraries::get_custom_font_families(),
                'fa_url' => WpPopPop_Libraries::fa_url(),
                'layer_settings' => WpPopPop_Layer_Settings::builder_payload(),
                'new_popup_defaults' => class_exists('WpPopPop_Saved_Defaults') ? WpPopPop_Saved_Defaults::get('popup') : null
            ]);
            $builder_payload = apply_filters('wppoppop_builder_payload', $builder_payload);
            wp_localize_script('wppoppop-builder-core-js', 'wppoppop_vars', $builder_payload);
            wp_enqueue_script('wppoppop-builder-layer-settings-js', WPPOPPOP_URL . 'admin/js/builder/builder-layer-settings.js', ['jquery', 'wppoppop-builder-core-js', 'wppoppop-builder-elements-registry', 'wppoppop-style-stable-js'], self::ver(), true);
            wp_localize_script('wppoppop-builder-inspector-js', 'wppoppop_custom_fonts', WpPopPop_Libraries::get_custom_font_families());
            wp_localize_script('wppoppop-builder-io-js', 'wppoppop_vars', $builder_payload);
            wp_localize_script('wppoppop-builder-js', 'wppoppop_vars', $builder_payload);
            wp_localize_script('wppoppop-builder-preview-js', 'wppoppop_vars', $builder_payload);
            wp_enqueue_script('wppoppop-builder-saved-defaults-js', WPPOPPOP_URL . 'admin/js/builder/builder-saved-defaults.js', ['jquery', 'wppoppop-builder-core-js', 'wppoppop-builder-settings-js', 'wppoppop-builder-io-js'], self::ver(), true);
            WpPopPop_Saved_Defaults::enqueue();
            do_action('wppoppop_admin_builder_assets', $builder_payload);
        }

        // 3. Settings Assets
        if ($page === 'wppoppop-settings') {
            $set_ver = self::ver();
            wp_enqueue_style('dashicons');
            wp_enqueue_style('wppoppop-dashboard-css', WPPOPPOP_URL . 'admin/css/dashboard.css', [], $set_ver);
            wp_enqueue_style('wppoppop-overview-css', WPPOPPOP_URL . 'admin/css/overview.css', [], $set_ver);
            wp_enqueue_script('wppoppop-overview-js', WPPOPPOP_URL . 'admin/js/overview.js', ['jquery'], $set_ver, true);
            wp_enqueue_style('wppoppop-settings-css', WPPOPPOP_URL . 'admin/css/settings.css', ['wppoppop-dashboard-css', 'wppoppop-overview-css', 'wppoppop-theme-global-css'], $set_ver);
            wp_enqueue_style('wppoppop-settings-search-css', WPPOPPOP_URL . 'admin/css/settings/search.css', ['wppoppop-settings-css'], $set_ver);
            wp_enqueue_style('wppoppop-settings-reset-css', WPPOPPOP_URL . 'admin/css/settings/reset.css', ['wppoppop-settings-css'], $set_ver);

            wp_enqueue_style('wppoppop-settings-typography-css', WPPOPPOP_URL . 'admin/css/settings/typography.css', ['wppoppop-settings-css'], $set_ver); /* WPPOPPOP_DEFAULT_TYPO */
            wp_enqueue_script('wppoppop-settings-typography-js', WPPOPPOP_URL . 'admin/js/settings/settings-typography.js', ['jquery'], self::ver(), true);
            wp_enqueue_script('wppoppop-settings-tabs-js', WPPOPPOP_URL . 'admin/js/settings/settings-tabs.js', ['jquery'], self::ver(), true);
            wp_enqueue_script('wppoppop-settings-save-js', WPPOPPOP_URL . 'admin/js/settings/settings-save.js', ['jquery', 'wppoppop-settings-tabs-js'], self::ver(), true);
            wp_enqueue_script('wppoppop-settings-tools-js', WPPOPPOP_URL . 'admin/js/settings/settings-tools.js', ['jquery'], self::ver(), true);
            wp_enqueue_script('wppoppop-settings-pages-js', WPPOPPOP_URL . 'admin/js/settings/settings-pages.js', ['jquery', 'jquery-ui-sortable', 'wppoppop-settings-tabs-js'], self::ver(), true);
            $this->enqueue_modules_ui($set_ver, ['wppoppop-settings-css']);
            wp_enqueue_script('wppoppop-settings-modules-js', WPPOPPOP_URL . 'admin/js/settings/settings-modules.js', ['jquery', 'jquery-ui-sortable', 'wppoppop-settings-tabs-js', 'wppoppop-modules-api-js'], self::ver(), true);
            wp_enqueue_script('wppoppop-settings-search-js', WPPOPPOP_URL . 'admin/js/settings/settings-search.js', ['jquery', 'wppoppop-settings-tabs-js'], self::ver(), true);
            wp_enqueue_script('wppoppop-settings-reset-js', WPPOPPOP_URL . 'admin/js/settings/settings-reset.js', ['jquery', 'wppoppop-settings-tabs-js'], self::ver(), true);

            wp_enqueue_style('wppoppop-settings-layer-settings-css', WPPOPPOP_URL . 'admin/css/settings/builder-layer-settings.css', ['wppoppop-settings-css'], $set_ver);
            wp_enqueue_script('wppoppop-settings-layer-settings-js', WPPOPPOP_URL . 'admin/js/settings/settings-layer-settings.js', ['jquery', 'wppoppop-settings-tabs-js'], self::ver(), true);
            wp_enqueue_script('wppoppop-settings-js', WPPOPPOP_URL . 'admin/js/settings.js', [
                'jquery',
                'wppoppop-settings-tabs-js',
                'wppoppop-settings-save-js',
                'wppoppop-settings-tools-js',
                'wppoppop-settings-search-js'
            ], self::ver(), true);

            $settings_payload = [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce'    => wp_create_nonce('wppoppop_settings_nonce')
            ];

            wp_localize_script('wppoppop-settings-js', 'wppoppop_settings_vars', $settings_payload);
            wp_localize_script('wppoppop-settings-js', 'wppoppop_vars', $shared_payload);
            WpPopPop_Saved_Defaults::enqueue();
        }

        // 4. Library Assets
        if ($page === 'wppoppop-library') {
            $lib_ver = self::ver();
            wp_enqueue_style('dashicons');
            wp_enqueue_style('wppoppop-dashboard-css', WPPOPPOP_URL . 'admin/css/dashboard.css', [], $lib_ver);
            wp_enqueue_style('wppoppop-overview-css', WPPOPPOP_URL . 'admin/css/overview.css', [], $lib_ver);
            wp_enqueue_script('wppoppop-overview-js', WPPOPPOP_URL . 'admin/js/overview.js', ['jquery'], $lib_ver, true);
            wp_enqueue_style('wppoppop-library-css', WPPOPPOP_URL . 'admin/css/library.css', ['wppoppop-dashboard-css', 'wppoppop-overview-css', 'wppoppop-theme-global-css'], $lib_ver);
            wp_enqueue_script('wppoppop-library-js', WPPOPPOP_URL . 'admin/js/library.js', ['jquery'], $lib_ver, true);
            wp_localize_script('wppoppop-library-js', 'wppoppop_vars', $shared_payload);
            wp_enqueue_script('wppoppop-library-live-preview-js', WPPOPPOP_URL . 'admin/js/library/library-live-preview.js', ['jquery', 'wppoppop-library-js'], $lib_ver, true);
            $wppoppop_lib_payload = ['ajax_url' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('wppoppop_admin_nonce')];
            wp_localize_script('wppoppop-library-live-preview-js', 'wppoppop_lib_vars', $wppoppop_lib_payload);
            wp_enqueue_script('wppoppop-library-categories-js', WPPOPPOP_URL . 'admin/js/library/library-categories.js', ['jquery', 'jquery-ui-sortable', 'wppoppop-library-js'], $lib_ver, true);
            wp_localize_script('wppoppop-library-categories-js', 'wppoppop_libcat_vars', ['ajax_url' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('wppoppop_admin_nonce')]);
            wp_enqueue_style('wppoppop-fa-library', WpPopPop_Libraries::fa_url(), [], '6.5.1');
            wp_enqueue_media();
            $wppoppop_la_css = WPPOPPOP_PATH . 'admin/css/library/actions.css';
            wp_enqueue_style('wppoppop-library-actions-css', WPPOPPOP_URL . 'admin/css/library/actions.css', ['wppoppop-library-css', 'wppoppop-fa-library'], file_exists($wppoppop_la_css) ? (string) filemtime($wppoppop_la_css) : $lib_ver);
            wp_enqueue_script('wppoppop-library-actions-js', WPPOPPOP_URL . 'admin/js/library/library-actions.js', ['jquery', 'wppoppop-library-js'], $lib_ver, true);
            wp_localize_script('wppoppop-library-actions-js', 'wppoppop_libact_vars', ['ajax_url' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('wppoppop_admin_nonce')]);
            $wppoppop_ts_lib = WPPOPPOP_PATH . 'admin/css/library/theme-sync-modals.css';
            wp_enqueue_style('wppoppop-library-theme-modals-css', WPPOPPOP_URL . 'admin/css/library/theme-sync-modals.css', ['wppoppop-library-css'], file_exists($wppoppop_ts_lib) ? (string) filemtime($wppoppop_ts_lib) : $lib_ver);
            $wppoppop_ub_css = WPPOPPOP_PATH . 'admin/css/library/uniform-buttons.css';
            wp_enqueue_style('wppoppop-library-uniform-css', WPPOPPOP_URL . 'admin/css/library/uniform-buttons.css', ['wppoppop-library-css', 'wppoppop-library-actions-css', 'wppoppop-library-theme-modals-css'], file_exists($wppoppop_ub_css) ? (string) filemtime($wppoppop_ub_css) : $lib_ver);
            $wppoppop_sb_css = WPPOPPOP_PATH . 'admin/css/library/select-button.css';
            wp_enqueue_style('wppoppop-library-select-css', WPPOPPOP_URL . 'admin/css/library/select-button.css', ['wppoppop-library-uniform-css'], file_exists($wppoppop_sb_css) ? (string) filemtime($wppoppop_sb_css) : $lib_ver);
            wp_enqueue_script('wppoppop-library-download-js', WPPOPPOP_URL . 'admin/js/library/library-download.js', ['jquery', 'wppoppop-library-actions-js'], $lib_ver, true);
            wp_enqueue_script('wppoppop-library-user-js', WPPOPPOP_URL . 'admin/js/library/library-user.js', ['jquery', 'wppoppop-library-js'], $lib_ver, true);
            wp_enqueue_script('wppoppop-library-tooltip-js', WPPOPPOP_URL . 'admin/js/library/library-tooltip.js', ['jquery', 'wppoppop-library-actions-js', 'wppoppop-library-download-js', 'wppoppop-library-user-js'], $lib_ver, true);
            wp_enqueue_style('wppoppop-library-pager-css', WPPOPPOP_URL . 'admin/css/library/pager.css', ['wppoppop-library-css'], $lib_ver);
            wp_enqueue_script('wppoppop-library-pager-js', WPPOPPOP_URL . 'admin/js/library/library-pager.js', ['jquery', 'wppoppop-library-js'], $lib_ver, true);
            wp_localize_script('wppoppop-library-pager-js', 'wppoppop_libpg_vars', ['i18n' => [
                'showing' => /* translators: 1: first item number, 2: last item number, 3: total number of items. */ __('Showing %1$s-%2$s of %3$s', 'wppoppop'),
                'prev'    => __('Previous page', 'wppoppop'),
                'next'    => __('Next page', 'wppoppop'),
            ]]);
            wp_enqueue_style('wppoppop-library-import-css', WPPOPPOP_URL . 'admin/css/library/import.css', ['wppoppop-library-css'], $lib_ver);
            wp_enqueue_style('wppoppop-library-import-preview-css', WPPOPPOP_URL . 'admin/css/library/import-preview.css', ['wppoppop-library-css'], $lib_ver);
            wp_enqueue_script('wppoppop-library-import-preview-js', WPPOPPOP_URL . 'admin/js/library/library-import-preview.js', ['jquery', 'wppoppop-library-js'], $lib_ver, true);
            wp_localize_script('wppoppop-library-import-preview-js', 'wppoppop_libprev_vars', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce'    => wp_create_nonce('wppoppop_admin_nonce'),
                'i18n'     => [
                    'title'    => __('Preview template', 'wppoppop'),
                    'note'     => __('Check the template below, then click Import to add it to My Templates.', 'wppoppop'),
                    'loading'  => __('Reading template...', 'wppoppop'),
                    'failed'   => __('This file could not be read.', 'wppoppop'),
                    'elements' => __('elements', 'wppoppop'),
                    'images'   => __('images', 'wppoppop'),
                    'remove'   => __('Remove', 'wppoppop'),
                    'cancel'   => __('Cancel', 'wppoppop'),
                    'import'   => __('Import', 'wppoppop'),
                    /* translators: %d: number of templates */
                    'import_n' => __('Import %d templates', 'wppoppop'),
                ],
            ]);
            wp_enqueue_script('wppoppop-library-bundle-js', WPPOPPOP_URL . 'admin/js/library/library-bundle.js', ['jquery', 'wppoppop-library-js'], $lib_ver, true);
            wp_localize_script('wppoppop-library-bundle-js', 'wppoppop_libzip_vars', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce'    => wp_create_nonce('wppoppop_admin_nonce'),
                'zip'      => class_exists('WpPopPop_Template_Bundle') ? WpPopPop_Template_Bundle::zip_available() : false,
                'max_mb'   => 60,
                'i18n'     => [
                    'missing'       => __('Some images could not be found and were left as links.', 'wppoppop'),
                    'export_failed' => __('Download failed.', 'wppoppop'),
                    'bad_type'      => __('Only .zip or .json files can be imported.', 'wppoppop'),
                    'too_big'       => __('File is too large.', 'wppoppop'),
                    'uploading'     => __('Importing...', 'wppoppop'),
                    'done'          => __('Imported', 'wppoppop'),
                    'failed'        => __('Import failed.', 'wppoppop'),
                ],
            ]);
        }

        // Dashboard overview page (separate from the Popups list assets)
        if (in_array($page, ['wppoppop-dashboard', 'wppoppop'], true)) {
            $ov_ver = self::ver();
            wp_enqueue_style('dashicons');
            wp_enqueue_style('wppoppop-overview-css', WPPOPPOP_URL . 'admin/css/overview.css', [], $ov_ver);
            wp_enqueue_script('wppoppop-overview-js', WPPOPPOP_URL . 'admin/js/overview.js', ['jquery'], $ov_ver, true);
            if ($page === 'wppoppop-dashboard') {
                $wppoppop_sync_deps = ['wppoppop-overview-css'];
                if (wp_style_is('wppoppop-theme-global-css', 'enqueued')) { $wppoppop_sync_deps[] = 'wppoppop-theme-global-css'; }
                wp_enqueue_style('wppoppop-overview-sync-css', WPPOPPOP_URL . 'admin/css/overview-sync.css', $wppoppop_sync_deps, $ov_ver);
                wp_enqueue_script('wppoppop-dash-layout-js', WPPOPPOP_URL . 'admin/js/dash-layout.js', [], $ov_ver, true);
                wp_localize_script('wppoppop-dash-layout-js', 'wppoppop_dash_layout', [
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'nonce'    => wp_create_nonce('wppoppop_admin_nonce'),
                ]);
            }
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
            $tl_ver = self::ver();
            wp_enqueue_style('dashicons');
            wp_enqueue_style('wppoppop-dashboard-css', WPPOPPOP_URL . 'admin/css/dashboard.css', [], $tl_ver);
            wp_enqueue_style('wppoppop-overview-css', WPPOPPOP_URL . 'admin/css/overview.css', [], $tl_ver);
            wp_enqueue_style('wppoppop-bar-actions-css', WPPOPPOP_URL . 'admin/css/bar-actions.css', ['wppoppop-dashboard-css', 'wppoppop-overview-css'], $tl_ver);
            wp_enqueue_style('wppoppop-tools-css', WPPOPPOP_URL . 'admin/css/tools.css', ['wppoppop-bar-actions-css', 'wppoppop-theme-global-css'], $tl_ver);
            wp_enqueue_script('wppoppop-overview-js', WPPOPPOP_URL . 'admin/js/overview.js', ['jquery'], $tl_ver, true);
            wp_enqueue_script('wppoppop-tools-bar-js', WPPOPPOP_URL . 'admin/js/tools/tools-bar.js', ['jquery'], $tl_ver, true);
            wp_enqueue_script('wppoppop-tools-maintenance-js', WPPOPPOP_URL . 'admin/js/tools/tools-maintenance.js', ['jquery'], $tl_ver, true);
            wp_enqueue_script('wppoppop-tools-system-js', WPPOPPOP_URL . 'admin/js/tools/tools-system.js', ['jquery'], self::ver(), true);
            wp_enqueue_script('wppoppop-tools-database-js', WPPOPPOP_URL . 'admin/js/tools/tools-database.js', ['jquery'], self::ver(), true);
            wp_enqueue_script('wppoppop-tools-portability-js', WPPOPPOP_URL . 'admin/js/tools/tools-portability.js', ['jquery'], self::ver(), true);
            wp_enqueue_script('wppoppop-tools-js', WPPOPPOP_URL . 'admin/js/tools.js', ['jquery', 'wppoppop-tools-system-js', 'wppoppop-tools-database-js', 'wppoppop-tools-portability-js'], self::ver(), true);
            wp_localize_script('wppoppop-tools-js', 'wppoppop_vars', $shared_payload);
            WpPopPop_Saved_Defaults::enqueue();
        }

        // 6. Extend (module manager) page
        if ($page === 'wppoppop-extend') {
            $ex_ver = self::ver();
            wp_enqueue_style('dashicons');
            wp_enqueue_style('wppoppop-dashboard-css', WPPOPPOP_URL . 'admin/css/dashboard.css', [], $ex_ver);
            wp_enqueue_style('wppoppop-overview-css', WPPOPPOP_URL . 'admin/css/overview.css', [], $ex_ver);
            wp_enqueue_style('wppoppop-bar-actions-css', WPPOPPOP_URL . 'admin/css/bar-actions.css', ['wppoppop-dashboard-css', 'wppoppop-overview-css'], $ex_ver);
            wp_enqueue_script('wppoppop-overview-js', WPPOPPOP_URL . 'admin/js/overview.js', ['jquery'], $ex_ver, true);
            $this->enqueue_modules_ui($ex_ver, ['wppoppop-bar-actions-css', 'wppoppop-theme-global-css'], ['modules' => WpPopPop_Modules_Installer::extend_items()]);
            wp_enqueue_style('wppoppop-extend-css', WPPOPPOP_URL . 'admin/css/extend.css', ['wppoppop-modules-css'], $ex_ver);
            wp_enqueue_script('wppoppop-extend-js', WPPOPPOP_URL . 'admin/js/modules/extend.js', ['jquery', 'wppoppop-modules-api-js'], $ex_ver, true);
            wp_enqueue_script('wppoppop-module-categories-js', WPPOPPOP_URL . 'admin/js/modules/module-categories.js', ['jquery', 'jquery-ui-sortable', 'wppoppop-modules-api-js'], $ex_ver, true); /* WPPOPPOP_CATS_ON_EXTEND */
        }
    }
}
