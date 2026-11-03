<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * WpPopPop WordPress Core Settings API Registrar
 * Registers option groups, schema sanitization, and settings validation rules.
 */
class WpPopPop_Admin_Settings {

    public function __construct() {
        add_action('admin_init', [$this, 'register_settings']);
    }

    public function register_settings() {
        register_setting(
            'wppoppop_settings_group',
            'wppoppop_settings',
            [
                'type'              => 'array',
                'description'       => __('Global configuration options for WpPopPop.', 'wppoppop'),
                'sanitize_callback' => [__CLASS__, 'sanitize_settings'],
                'show_in_rest'      => false,
                'default'           => self::get_defaults(),
            ]
        );
    }

    /** Popup Settings accordions that are listed in Settings > Module (key = acc-*.php basename). */
    public static function get_modules() {
        return [
            'acc-01-canvas-styling' => __('Canvas Settings', 'wppoppop'),
            'acc-20-animation'    => __('Animation', 'wppoppop'),
            'acc-02-triggers'     => __('Display Triggers', 'wppoppop'),
            'acc-03-logic-math'   => __('Conditional Logic & Math', 'wppoppop'),
            'acc-04-side-tabs'    => __('Side Tabs', 'wppoppop'),
            'acc-05-payments'     => __('Payments', 'wppoppop'),
            'acc-06-downloads'    => __('Downloads', 'wppoppop'),
            'acc-07-video'        => __('Video', 'wppoppop'),
            'acc-08-autoresponder' => __('Autoresponder', 'wppoppop'),
            'acc-09-webhooks'     => __('Webhooks', 'wppoppop'),
            'acc-10-sms'          => __('SMS', 'wppoppop'),
            'acc-11-targeting'    => __('Targeting', 'wppoppop'),
            'acc-12-frequency'    => __('Frequency', 'wppoppop'),
            'acc-13-woocommerce'  => __('WooCommerce', 'wppoppop'),
            'acc-14-custom-code'  => __('Custom Code', 'wppoppop'),
            'acc-15-quiz'         => __('Quiz', 'wppoppop'),
            'acc-16-popup-overlay' => __('Popup Overlay', 'wppoppop'),
            'acc-17-double-optin'  => __('Double Opt-In', 'wppoppop'),
            'acc-18-notifications' => __('Notifications', 'wppoppop'),
            'acc-19-success-celebration' => __('Success Celebration', 'wppoppop'),
        ];
    }

    /** Modules that are always on and cannot be switched off. */
    public static function get_locked_modules() {
        return ['acc-01-canvas-styling', 'acc-02-triggers'];
    }

    public static function get_default_module_order() {
        return array_keys(self::get_modules());
    }

    /** Whitelist + de-duplicate an order; modules missing from it are appended in default order. */
    public static function normalize_module_order($order) {
        $known = self::get_default_module_order();
        $out   = [];
        if (is_array($order)) {
            foreach ($order as $k) {
                if (is_string($k) && in_array($k, $known, true) && !in_array($k, $out, true)) {
                    $out[] = $k;
                }
            }
        }
        foreach ($known as $k) {
            if (!in_array($k, $out, true)) {
                $out[] = $k;
            }
        }
        return $out;
    }

    /** Global Popup Settings module order (Settings > Module). */
    public static function get_module_order() {
        $s = get_option('wppoppop_settings', []);
        return self::normalize_module_order((is_array($s) && isset($s['module_order'])) ? $s['module_order'] : []);
    }

    public static function get_module_defaults() {
        $enabled = array_merge(self::get_locked_modules(), ['acc-20-animation', 'acc-16-popup-overlay', 'acc-17-double-optin', 'acc-18-notifications', 'acc-19-success-celebration']);
        $defaults = [];
        foreach (array_keys(self::get_modules()) as $key) {
            $defaults[$key] = in_array($key, $enabled, true) ? 1 : 0;
        }
        return $defaults;
    }

    public static function get_builder_defaults() {
        return [
            'builder_alignment_tool' => 0,
            'builder_viewport_toggle' => 0,
            'builder_zoom_select' => 0,
            'builder_save_template' => 0,
            'builder_extra_elements' => 0,
        ];
    }

    public static function get_default_menu_pages() {
        return [
            'wppoppop-log'             => ['hidden' => 1],
            'wppoppop-field-analytics' => ['hidden' => 1],
            'wppoppop-payments'       => ['hidden' => 1],
        ];
    }

    /** Use the saved value when present; otherwise use the current module default. */
    public static function module_enabled($key) {
        if (in_array($key, self::get_locked_modules(), true)) {
            return true;
        }
        $s = get_option('wppoppop_settings', []);
        /* WPPOPPOP_MODULE_LEGACY_KEYS: keep toggles saved under the pre-renumber keys */
        $legacy = ['acc-17-double-optin' => 'acc-16-double-optin', 'acc-16-popup-overlay' => 'acc-17-popup-overlay'];
        if (is_array($s) && isset($s['modules']) && is_array($s['modules']) && !array_key_exists($key, $s['modules'])
            && isset($legacy[$key]) && array_key_exists($legacy[$key], $s['modules'])) {
            return !empty($s['modules'][$legacy[$key]]);
        }
        $defaults = self::get_module_defaults();
        if (!is_array($s) || !isset($s['modules']) || !is_array($s['modules']) || !array_key_exists($key, $s['modules'])) {
            return !empty($defaults[$key]);
        }
        return !empty($s['modules'][$key]);
    }

    public static function get_defaults() {
        $admin = get_option('admin_email');
        return [
            'sender_name' => 'WpPopPop', 'sender_email' => $admin, 'admin_email' => $admin,
            'csv_separator' => ',', 'user_uploads' => 'keep', 'powered_by_badge' => 0, 'test_mode' => 0,
            'preload_popups' => 0, 'preload_events' => 0, 'minify_css' => 0, 'cache_busting' => 0,
            'render_delay' => 0, 'ga_tracking' => 0, 'adblock_detector' => 0,
            'default_font' => 'inherit', 'google_fonts' => 0, 'disable_google_fonts' => 0,
            'font_awesome' => 0, 'custom_fonts' => '',
            'load_canvas_confetti' => 0, 'load_canvas_fireworks' => 0,
            'air_datepicker' => 0, 'jquery_mask' => 0, 'signature_pad' => 0,
            'range_slider' => 0, 'js_parser' => 0,
            'email_validation' => 'basic', 'enable_recaptcha' => 0,
            'recaptcha_site_key' => '', 'recaptcha_secret_key' => '',
            'recaptcha_threshold' => 0.4, 'max_submissions_per_ip' => 10, 'ip_geotargeting' => 0, 'geoip_service' => 'none',
            'custom_css' => '', 'custom_js' => '', 'custom_header_html' => '', 'custom_footer_html' => '',
            'modules' => self::get_module_defaults(),
            'module_order' => self::get_default_module_order(),
            'builder_alignment_tool' => 0,
            'builder_viewport_toggle' => 0,
            'builder_zoom_select' => 0,
            'builder_save_template' => 0,
            'builder_extra_elements' => 0,
            'menu_pages' => self::get_default_menu_pages(),
            'clean_on_uninstall' => 0,
        ] + self::tracking_defaults();
    }

    protected static function tracking_defaults() {
        if (!class_exists('WpPopPop_Tracking')) { require_once WPPOPPOP_PATH . 'includes/class-wppoppop-tracking.php'; }
        return WpPopPop_Tracking::defaults();
    }

    /**
     * Idempotent: safe to run on already-clean data (it runs on every update_option).
     * Input is expected to be already unslashed.
     */
    public static function sanitize_settings($input) {
        if (!is_array($input)) {
            return self::get_defaults();
        }
        $in  = $input;
        $b   = function ($k) use ($in) { return !empty($in[$k]) ? 1 : 0; };
        $any = function ($a, $b2) use ($in) { return (!empty($in[$a]) || !empty($in[$b2])) ? 1 : 0; };
        $enum = function ($k, $allowed, $def) use ($in) {
            return (isset($in[$k]) && in_array($in[$k], $allowed, true)) ? $in[$k] : $def;
        };
        $admin = get_option('admin_email');
        $o = [];
        $o['builder_save_template'] = (array_key_exists('builder_save_template', $in) && empty($in['builder_save_template'])) ? 0 : 1;

        // 1. General & Mailing
        $name = !empty($in['sender_name']) ? $in['sender_name'] : (!empty($in['from_name']) ? $in['from_name'] : '');
        $o['sender_name'] = $name !== '' ? sanitize_text_field($name) : 'WpPopPop';
        $o['from_name']   = $o['sender_name'];

        $raw = !empty($in['sender_email']) ? $in['sender_email'] : (!empty($in['from_email']) ? $in['from_email'] : '');
        $raw = sanitize_email($raw);
        if (is_email($raw)) {
            $o['sender_email'] = $raw;
        } else {
            $o['sender_email'] = $admin;
            if (!empty($in['sender_email'])) {
                add_settings_error('wppoppop_settings', 'invalid_sender_email',
                    __('Invalid sender email address provided. Reverted to site administrator email.', 'wppoppop'), 'error');
            }
        }
        $o['from_email']  = $o['sender_email'];
        $adm = !empty($in['admin_email']) ? sanitize_email($in['admin_email']) : '';
        $o['admin_email'] = is_email($adm) ? $adm : $admin;
        $o['csv_separator']    = $enum('csv_separator', [',', ';', 'tab'], ',');
        $o['user_uploads']     = $enum('user_uploads', ['keep', 'delete'], 'keep');
        $o['powered_by_badge'] = $b('powered_by_badge');
        $o['test_mode']        = $b('test_mode');

        // 2. Performance & Preload
        $o['preload_popups']   = $b('preload_popups');
        $o['preload_events']   = $b('preload_events');
        $o['minify_css']       = $b('minify_css');
        $o['cache_busting']    = $b('cache_busting');
        $o['render_delay']     = isset($in['render_delay']) ? min(10000, max(0, (int) $in['render_delay'])) : 0;
        $o['ga_tracking']      = $b('ga_tracking');
        $o['adblock_detector'] = $b('adblock_detector');

        // 3. Typography & Libraries
        $o['default_font']          = !empty($in['default_font']) ? sanitize_text_field($in['default_font']) : 'inherit';
        $o['google_fonts']          = $b('google_fonts');
        $o['disable_google_fonts']  = $b('disable_google_fonts');
        $o['font_awesome']          = $b('font_awesome');
        $o['custom_fonts']          = isset($in['custom_fonts']) ? sanitize_textarea_field($in['custom_fonts']) : '';
        $o['load_canvas_confetti']  = $b('load_canvas_confetti');
        $o['load_canvas_fireworks'] = $b('load_canvas_fireworks');
        $o['air_datepicker']  = $any('air_datepicker', 'load_air_datepicker');  $o['load_air_datepicker'] = $o['air_datepicker'];
        $o['jquery_mask']     = $any('jquery_mask', 'load_jquery_mask');        $o['load_jquery_mask']    = $o['jquery_mask'];
        $o['signature_pad']   = $any('signature_pad', 'load_signature_pad');    $o['load_signature_pad']  = $o['signature_pad'];
        $o['range_slider']    = $any('range_slider', 'load_range_slider');      $o['load_range_slider']   = $o['range_slider'];
        $o['js_parser']       = $any('js_parser', 'load_math_parser');          $o['load_math_parser']    = $o['js_parser'];

        // 4. Security & Geolocation
        $o['email_validation']       = $enum('email_validation', ['basic', 'disposable_filter'], 'basic');
        $o['enable_recaptcha']       = $b('enable_recaptcha');
        $o['recaptcha_site_key']     = !empty($in['recaptcha_site_key']) ? sanitize_text_field($in['recaptcha_site_key']) : '';
        $o['recaptcha_secret_key']   = !empty($in['recaptcha_secret_key']) ? sanitize_text_field($in['recaptcha_secret_key']) : '';
        $thr = (isset($in['recaptcha_threshold']) && is_numeric($in['recaptcha_threshold'])) ? (float) $in['recaptcha_threshold'] : 0.4;
        $o['recaptcha_threshold']    = round(min(0.9, max(0.1, $thr)), 2);
        $o['max_submissions_per_ip'] = isset($in['max_submissions_per_ip']) ? min(100, max(1, (int) $in['max_submissions_per_ip'])) : 10;
        $o['ip_geotargeting']        = $b('ip_geotargeting');
        $geo = !empty($in['geoip_service']) ? $in['geoip_service'] : (!empty($in['geoip_api_service']) ? $in['geoip_api_service'] : 'none');
        $o['geoip_service']     = in_array($geo, ['none', 'ipapi', 'ip-api'], true) ? $geo : 'none';
        $o['geoip_api_service'] = $o['geoip_service'];

        // 5. Custom code (capability-aware, no double unslash)
        $css = isset($in['custom_css']) ? (string) $in['custom_css'] : '';
        $js  = isset($in['custom_js']) ? (string) $in['custom_js'] : '';
        $hh  = isset($in['custom_header_html']) ? (string) $in['custom_header_html'] : '';
        $fh  = isset($in['custom_footer_html']) ? (string) $in['custom_footer_html'] : '';
        if (current_user_can('unfiltered_html')) {
            $o['custom_css'] = $css; $o['custom_js'] = $js;
            $o['custom_header_html'] = $hh; $o['custom_footer_html'] = $fh;
        } else {
            // Restricted users cannot author executable code: keep what is already stored
            // instead of overwriting it with a mangled copy.
            $prev = get_option('wppoppop_settings', []);
            $prev = is_array($prev) ? $prev : [];
            $o['custom_css']         = wp_strip_all_tags($css);
            $o['custom_js']          = isset($prev['custom_js']) ? $prev['custom_js'] : '';
            $o['custom_header_html'] = isset($prev['custom_header_html']) ? $prev['custom_header_html'] : '';
            $o['custom_footer_html'] = isset($prev['custom_footer_html']) ? $prev['custom_footer_html'] : '';
        }

        // 5b. Popup Settings modules (whitelist; missing = current default)
        $module_defaults = self::get_module_defaults();
        $o['modules'] = [];
        foreach (array_keys(self::get_modules()) as $__mk) {
            if (in_array($__mk, self::get_locked_modules(), true)) {
                $o['modules'][$__mk] = 1;
                continue;
            }
            $o['modules'][$__mk] = (isset($in['modules']) && is_array($in['modules']) && array_key_exists($__mk, $in['modules']))
                ? (!empty($in['modules'][$__mk]) ? 1 : 0)
                : $module_defaults[$__mk];
        }

        // 5b2. Module order (whitelist; missing key = keep the stored order)
        $__mo_prev = get_option('wppoppop_settings', []);
        $__mo_src  = isset($in['module_order'])
            ? $in['module_order']
            : ((is_array($__mo_prev) && isset($__mo_prev['module_order'])) ? $__mo_prev['module_order'] : []);
        $o['module_order'] = self::normalize_module_order($__mo_src);

        // 5c. Builder tools (missing = disabled by default)
        $builder_defaults = self::get_builder_defaults();
        foreach ($builder_defaults as $__bk => $__bv) {
            $o[$__bk] = (array_key_exists($__bk, $in) && !empty($in[$__bk])) ? 1 : $__bv;
        }

        // 5d. Admin menu pages (rename / reorder / hide). Whitelist: wppoppop* slugs only.
        // Missing key = keep what is stored, so unrelated saves never reset it.
        $__mp_prev = get_option('wppoppop_settings', []);
        $__mp_defaults = self::get_default_menu_pages();
        $__mp_src  = (isset($in['menu_pages']) && is_array($in['menu_pages']))
            ? $in['menu_pages']
            : ((is_array($__mp_prev) && isset($__mp_prev['menu_pages']) && is_array($__mp_prev['menu_pages'])) ? $__mp_prev['menu_pages'] : $__mp_defaults);
        $__mp_pin  = ['wppoppop-dashboard', 'wppoppop-settings'];
        $o['menu_pages'] = [];
        foreach ($__mp_src as $__mp_slug => $__mp_row) {
            $__mp_slug = sanitize_key($__mp_slug);
            if ($__mp_slug === '' || strpos($__mp_slug, 'wppoppop') !== 0 || !is_array($__mp_row)) {
                continue;
            }
            $__mp_label = isset($__mp_row['label']) ? sanitize_text_field($__mp_row['label']) : '';
            if (function_exists('mb_substr')) {
                $__mp_label = mb_substr($__mp_label, 0, 60);
            } else {
                $__mp_label = substr($__mp_label, 0, 60);
            }
            $o['menu_pages'][$__mp_slug] = [
                'label'  => $__mp_label,
                'hidden' => (!empty($__mp_row['hidden']) && !in_array($__mp_slug, $__mp_pin, true)) ? 1 : 0,
                'order'  => isset($__mp_row['order']) ? max(0, min(999, (int) $__mp_row['order'])) : 0,
            ];
        }

        // 5e. Tracking, A/B, privacy, rate limits, reporting
        if (!class_exists('WpPopPop_Tracking')) { require_once WPPOPPOP_PATH . 'includes/class-wppoppop-tracking.php'; }
        $o = array_merge($o, WpPopPop_Tracking::sanitize_input($in));

        // 6. System state
        $o['clean_on_uninstall'] = $b('clean_on_uninstall');
        $o['cookie_epoch'] = isset($in['cookie_epoch']) ? (int) $in['cookie_epoch'] : (int) get_option('wppoppop_cookie_epoch', 1);

        return $o;
    }
}
