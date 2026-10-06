<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Ajax_Settings {
    public function __construct() {
        add_action('wp_ajax_wppoppop_save_settings', [$this, 'save_settings']);
        add_action('wp_ajax_wppoppop_reset_cookies', [$this, 'reset_cookies']);
    }

    public function save_settings() {
        check_ajax_referer('wppoppop_settings_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized action.']);
        }

        parse_str(isset($_POST['data']) ? wp_unslash($_POST['data']) : '', $parsed);

        $settings = [
            'sender_name'       => isset($parsed['sender_name']) ? sanitize_text_field($parsed['sender_name']) : 'wppoppop',
            'sender_email'      => isset($parsed['sender_email']) ? sanitize_email($parsed['sender_email']) : 'noreply@localhost',
            'preload_popups'    => !empty($parsed['preload_popups']),
            'preload_events'    => !empty($parsed['preload_events']),
            'ga_tracking'       => !empty($parsed['ga_tracking']),
            'google_fonts'      => !empty($parsed['google_fonts']),
            'font_awesome'      => !empty($parsed['font_awesome']),
            'air_datepicker'    => !empty($parsed['air_datepicker']),
            'no_air_datepicker' => !empty($parsed['no_air_datepicker']),
            'jquery_mask'       => !empty($parsed['jquery_mask']),
            'js_parser'         => !empty($parsed['js_parser']),
            'signature_pad'     => !empty($parsed['signature_pad']),
            'range_slider'      => !empty($parsed['range_slider']),
            'adblock_detector'  => !empty($parsed['adblock_detector']),
            'csv_separator'     => isset($parsed['csv_separator']) ? sanitize_text_field($parsed['csv_separator']) : ',',
            'custom_fonts'      => isset($parsed['custom_fonts']) ? sanitize_textarea_field($parsed['custom_fonts']) : '',
            'email_validation'  => isset($parsed['email_validation']) ? sanitize_text_field($parsed['email_validation']) : 'basic',
            'geoip_service'     => isset($parsed['geoip_service']) ? sanitize_text_field($parsed['geoip_service']) : 'none',
            'user_uploads'      => isset($parsed['user_uploads']) ? sanitize_text_field($parsed['user_uploads']) : 'keep',
            'custom_css'        => isset($parsed['custom_css']) ? sanitize_textarea_field($parsed['custom_css']) : '',
            'custom_js'         => isset($parsed['custom_js']) ? sanitize_textarea_field($parsed['custom_js']) : ''
        ];

        update_option('wppoppop_settings', $settings);
        wp_send_json_success(['message' => 'Settings saved successfully!']);
    }

    public function reset_cookies() {
        check_ajax_referer('wppoppop_settings_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized.']);
        }

        $epoch = time();
        update_option('wppoppop_cookie_epoch', $epoch);
        wp_send_json_success(['message' => 'Cookies have been successfully reset across all popups.']);
    }
}
