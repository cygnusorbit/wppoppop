<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * WpPopPop Settings: "Reset Defaults" (per tab or all).
 * Never touches popups, campaigns, submissions or the cookie epoch.
 */
class WpPopPop_Settings_Reset {

    public static function boot() {
        add_action('wp_ajax_wppoppop_reset_settings', [__CLASS__, 'handle']);
    }

    /** Stored option keys owned by each settings tab (aliases listed so they reset together). */
    public static function tab_keys() {
        if (!class_exists('WpPopPop_Tracking')) {
            require_once WPPOPPOP_PATH . 'includes/class-wppoppop-tracking.php';
        }
        return [
            'general'     => ['sender_name', 'from_name', 'sender_email', 'from_email', 'admin_email', 'csv_separator', 'user_uploads', 'powered_by_badge', 'test_mode'],
            'performance' => ['preload_popups', 'preload_events', 'minify_css', 'cache_busting', 'render_delay', 'ga_tracking', 'adblock_detector', 'disable_google_fonts'],
            'libraries'   => ['default_font', 'google_fonts', 'font_awesome', 'custom_fonts', 'load_canvas_confetti', 'load_canvas_fireworks',
                              'air_datepicker', 'load_air_datepicker', 'jquery_mask', 'load_jquery_mask', 'signature_pad', 'load_signature_pad',
                              'range_slider', 'load_range_slider', 'js_parser', 'load_math_parser'],
            'security'    => ['email_validation', 'enable_recaptcha', 'recaptcha_site_key', 'recaptcha_secret_key', 'recaptcha_threshold',
                              'max_submissions_per_ip', 'ip_geotargeting', 'geoip_service', 'geoip_api_service'],
            'custom-code' => ['custom_css', 'custom_js', 'custom_header_html', 'custom_footer_html'],
            'module'      => ['modules'],
            'builder'     => ['builder_alignment_tool', 'builder_viewport_toggle', 'builder_zoom_select', 'builder_save_template'],
            'pages'       => ['menu_pages'],
            'tracking'    => array_keys(WpPopPop_Tracking::defaults()),
        ];
    }

    protected static function aliases() {
        return [
            'from_name' => 'sender_name', 'from_email' => 'sender_email',
            'load_air_datepicker' => 'air_datepicker', 'load_jquery_mask' => 'jquery_mask',
            'load_signature_pad' => 'signature_pad', 'load_range_slider' => 'range_slider',
            'load_math_parser' => 'js_parser', 'geoip_api_service' => 'geoip_service',
        ];
    }

    protected static function verify() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Unauthorized capability.', 'wppoppop')]);
        }
        $nonce = isset($_REQUEST['nonce']) ? sanitize_text_field(wp_unslash($_REQUEST['nonce'])) : '';
        if (!wp_verify_nonce($nonce, 'wppoppop_settings_nonce') && !wp_verify_nonce($nonce, 'wppoppop_admin_nonce')) {
            wp_send_json_error(['message' => __('Security verification failed. Please reload the page.', 'wppoppop')]);
        }
    }

    public static function handle() {
        self::verify();

        $tab = isset($_POST['tab']) ? sanitize_key(wp_unslash($_POST['tab'])) : '';
        $map = self::tab_keys();
        if ($tab !== 'all' && !isset($map[$tab])) {
            wp_send_json_error(['message' => __('This tab has no stored settings to reset.', 'wppoppop')]);
        }

        if (!class_exists('WpPopPop_Admin_Settings')) {
            require_once WPPOPPOP_PATH . 'includes/admin/class-admin-settings.php';
        }

        $existing = get_option('wppoppop_settings', []);
        $raw      = is_array($existing) ? $existing : [];
        $defaults = WpPopPop_Admin_Settings::get_defaults();
        $aliases  = self::aliases();
        $tabs     = ($tab === 'all') ? array_keys($map) : [$tab];

        foreach ($tabs as $t) {
            foreach ($map[$t] as $key) {
                if ($key === 'modules') {
                    $mods = [];
                    foreach (array_keys(WpPopPop_Admin_Settings::get_modules()) as $mk) { $mods[$mk] = 1; }
                    $raw['modules'] = $mods;
                } elseif ($key === 'menu_pages') {
                    $raw['menu_pages'] = [];
                } else {
                    $src = isset($aliases[$key]) ? $aliases[$key] : $key;
                    if (array_key_exists($src, $defaults)) {
                        $raw[$key] = $defaults[$src];
                    } else {
                        unset($raw[$key]);
                    }
                }
            }
        }

        /* Cookie epoch is server-owned */
        $raw['cookie_epoch'] = isset($existing['cookie_epoch'])
            ? (int) $existing['cookie_epoch']
            : (int) get_option('wppoppop_cookie_epoch', 1);

        $clean = WpPopPop_Admin_Settings::sanitize_settings($raw);
        update_option('wppoppop_settings', $clean);
        if (function_exists('wppoppop_flush_settings_cache')) {
            wppoppop_flush_settings_cache();
        }
        if (function_exists('wppoppop_log_event')) {
            wppoppop_log_event('settings_reset', 'Settings reset to defaults: ' . $tab, ['user' => get_current_user_id()]);
        }

        wp_send_json_success([
            'message' => ($tab === 'all')
                ? __('All settings were reset to defaults.', 'wppoppop')
                : __('This tab was reset to defaults.', 'wppoppop'),
            'tab' => $tab,
        ]);
    }
}

WpPopPop_Settings_Reset::boot();
