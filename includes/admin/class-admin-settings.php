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
            'max_submissions_per_ip' => 10, 'ip_geotargeting' => 0, 'geoip_service' => 'none',
            'custom_css' => '', 'custom_js' => '', 'custom_header_html' => '', 'custom_footer_html' => '',
            'clean_on_uninstall' => 0,
        ];
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
        $o['max_submissions_per_ip'] = isset($in['max_submissions_per_ip']) ? min(100, max(1, (int) $in['max_submissions_per_ip'])) : 10;
        $o['ip_geotargeting']        = $b('ip_geotargeting');
        $geo = !empty($in['geoip_service']) ? $in['geoip_service'] : (!empty($in['geoip_api_service']) ? $in['geoip_api_service'] : 'none');
        $o['geoip_service']     = in_array($geo, ['none', 'ipapi', 'ip-api', 'maxmind'], true) ? $geo : 'none';
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

        // 6. System state
        $o['clean_on_uninstall'] = $b('clean_on_uninstall');
        $o['cookie_epoch'] = isset($in['cookie_epoch']) ? (int) $in['cookie_epoch'] : (int) get_option('wppoppop_cookie_epoch', 1);

        return $o;
    }
}
