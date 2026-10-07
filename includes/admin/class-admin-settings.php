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
        return [
            'sender_name'       => 'WpPopPop',
            'sender_email'      => get_option('admin_email'),
            'csv_separator'     => ',',
            'user_uploads'      => 'keep',
            'preload_popups'    => 0,
            'preload_events'    => 0,
            'ga_tracking'       => 0,
            'adblock_detector'  => 0,
            'google_fonts'      => 0,
            'font_awesome'      => 0,
            'custom_fonts'      => '',
            'air_datepicker'    => 0,
            'jquery_mask'       => 0,
            'signature_pad'     => 0,
            'range_slider'      => 0,
            'js_parser'         => 0,
            'email_validation'  => 'basic',
            'geoip_service'     => 'none',
            'custom_css'        => '',
            'custom_js'         => '',
        ];
    }

    public static function sanitize_settings($input) {
        if (!is_array($input)) {
            return self::get_defaults();
        }

        $sanitized = [];

        // 1. General & Mailing
        $sanitized['sender_name'] = !empty($input['sender_name']) ? sanitize_text_field($input['sender_name']) : 'WpPopPop';

        $raw_email = !empty($input['sender_email']) ? sanitize_email($input['sender_email']) : '';
        if (is_email($raw_email)) {
            $sanitized['sender_email'] = $raw_email;
        } else {
            $sanitized['sender_email'] = get_option('admin_email');
            if (!empty($input['sender_email'])) {
                add_settings_error(
                    'wppoppop_settings',
                    'invalid_sender_email',
                    __('Invalid sender email address provided. Reverted to site administrator email.', 'wppoppop'),
                    'error'
                );
            }
        }

        $allowed_csv = [',', ';', 'tab'];
        $sanitized['csv_separator'] = (isset($input['csv_separator']) && in_array($input['csv_separator'], $allowed_csv, true)) ? $input['csv_separator'] : ',';

        $allowed_uploads = ['keep', 'delete'];
        $sanitized['user_uploads'] = (isset($input['user_uploads']) && in_array($input['user_uploads'], $allowed_uploads, true)) ? $input['user_uploads'] : 'keep';

        // 2. Performance & Preload
        $sanitized['preload_popups']   = !empty($input['preload_popups']) ? 1 : 0;
        $sanitized['preload_events']   = !empty($input['preload_events']) ? 1 : 0;
        $sanitized['ga_tracking']      = !empty($input['ga_tracking']) ? 1 : 0;
        $sanitized['adblock_detector'] = !empty($input['adblock_detector']) ? 1 : 0;

        // 3. Typography & Libraries
        $sanitized['google_fonts']   = !empty($input['google_fonts']) ? 1 : 0;
        $sanitized['font_awesome']   = !empty($input['font_awesome']) ? 1 : 0;
        $sanitized['custom_fonts']   = isset($input['custom_fonts']) ? sanitize_textarea_field($input['custom_fonts']) : '';
        $sanitized['air_datepicker'] = !empty($input['air_datepicker']) ? 1 : 0;
        $sanitized['jquery_mask']    = !empty($input['jquery_mask']) ? 1 : 0;
        $sanitized['signature_pad']  = !empty($input['signature_pad']) ? 1 : 0;
        $sanitized['range_slider']   = !empty($input['range_slider']) ? 1 : 0;
        $sanitized['js_parser']      = !empty($input['js_parser']) ? 1 : 0;

        // 4. Geolocation & Security
        $allowed_email_val = ['basic', 'disposable_filter'];
        $sanitized['email_validation'] = (isset($input['email_validation']) && in_array($input['email_validation'], $allowed_email_val, true)) ? $input['email_validation'] : 'basic';

        $allowed_geoip = ['none', 'ipapi', 'maxmind'];
        $sanitized['geoip_service'] = (isset($input['geoip_service']) && in_array($input['geoip_service'], $allowed_geoip, true)) ? $input['geoip_service'] : 'none';

        // 5. Global Custom CSS & JS
        $sanitized['custom_css'] = isset($input['custom_css']) ? wp_strip_all_tags($input['custom_css']) : '';
        $sanitized['custom_js']  = isset($input['custom_js']) ? wp_unslash($input['custom_js']) : '';

        return $sanitized;
    }
}
