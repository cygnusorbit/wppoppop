<?php
namespace WPPopPop\Admin;

class SettingsManager {
    public const OPTION_KEY = 'wppoppop_general_settings';

    public static function init(): void {
        add_action('wp_ajax_wppoppop_save_all_settings', [__CLASS__, 'ajax_save_all_settings']);
    }

    public static function get_defaults(): array {
        return [
            'sender_name'         => 'wppoppop',
            'sender_email'        => 'noreply@localhost',
            'preload'             => 0,
            'preload_events'      => 0,
            'ga_tracking'         => 0,
            'google_fonts'        => 1,
            'font_awesome'        => 0,
            'air_datepicker'      => 1,
            'air_datepicker_skip' => 0,
            'mask_plugin'         => 0,
            'expression_parser'   => 0,
            'signature_pad'       => 0,
            'rangeslider'         => 0,
            'adblock_detector'    => 0,
            'csv_separator'       => ',',
            'custom_fonts'        => '',
            'email_validation'    => 'basic',
            'geoip_service'       => 'none',
            'user_uploads'        => 'keep',
            // Advanced Tab Settings
            'custom_css'          => '',
            'custom_js'           => '',
            'webhook_url'         => '',
            'admin_notify_email'  => get_option('admin_email', ''),
            // Payment Gateway Configurations
            'payment_currency'    => 'USD',
            'payment_mode'        => 'sandbox',
            'stripe_enabled'      => 0,
            'stripe_pub_key'      => '',
            'stripe_secret_key'   => '',
            'paypal_enabled'      => 0,
            'paypal_client_id'    => '',
            'paypal_secret'       => '',
            // CRM & Email Marketing Service Integrations
            'crm_provider'             => 'none',
            'mailchimp_api_key'        => '',
            'mailchimp_list_id'        => '',
            'activecampaign_api_url'   => '',
            'activecampaign_api_key'   => '',
            'activecampaign_list_id'   => '',
            'brevo_api_key'            => '',
            'brevo_list_id'            => '',
            // Sound Effects & Confetti Celebration
            'sound_enabled'            => 1,
            'confetti_enabled'         => 1,
            'sound_open_preset'        => 'pop',
            'sound_success_preset'     => 'fanfare',
            'sound_volume'             => 50,
            // Automated Welcome Autoresponder
            'autoresponder_enabled'    => 1,
            'autoresponder_subject'    => 'Welcome to {site_name}! Here is your gift',
            'autoresponder_body'       => '',
            // WooCommerce E-Commerce & Cart Abandonment Recovery
            'woo_abandonment_enabled'  => 1,
            'woo_cart_threshold'       => 0,
            'woo_abandonment_popup'    => 0,
            'woo_auto_redirect'        => 0,
        ];
    }

    public static function get_all(): array {
        $saved = get_option(self::OPTION_KEY, []);
        return wp_parse_args($saved, self::get_defaults());
    }

    public static function get(string $key, $default = null) {
        $all = self::get_all();
        return $all[$key] ?? $default;
    }

    public static function ajax_save_all_settings(): void {
        check_ajax_referer('wppoppop_admin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied.', 'wppoppop')]);
        }

        $incoming = isset($_POST['settings']) && is_array($_POST['settings']) ? wp_unslash($_POST['settings']) : [];
        $defaults = self::get_defaults();
        $sanitized = [];

        foreach ($defaults as $key => $default_val) {
            if (is_int($default_val)) {
                $sanitized[$key] = !empty($incoming[$key]) ? 1 : 0;
            } elseif ($key === 'custom_css' || $key === 'custom_js' || $key === 'custom_fonts') {
                $sanitized[$key] = sanitize_textarea_field($incoming[$key] ?? '');
            } elseif ($key === 'sender_email' || $key === 'admin_notify_email') {
                $sanitized[$key] = sanitize_email($incoming[$key] ?? $default_val);
            } elseif ($key === 'webhook_url') {
                $sanitized[$key] = esc_url_raw($incoming[$key] ?? '');
            } else {
                $sanitized[$key] = sanitize_text_field($incoming[$key] ?? $default_val);
            }
        }

        update_option(self::OPTION_KEY, $sanitized);
        wp_send_json_success(['message' => __('Settings successfully saved!', 'wppoppop')]);
    }
}
