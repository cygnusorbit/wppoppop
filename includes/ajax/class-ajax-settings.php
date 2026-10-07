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
        if (!current_user_can('manage_wppoppop') && !current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Unauthorized action.', 'wppoppop')]);
        }

        parse_str(isset($_POST['data']) ? wp_unslash($_POST['data']) : '', $parsed);
        $raw_input = isset($parsed['wppoppop_settings']) ? $parsed['wppoppop_settings'] : $parsed;

        if (!class_exists('WpPopPop_Admin_Settings')) {
            require_once WPPOPPOP_PATH . 'includes/admin/class-admin-settings.php';
        }

        $sanitized = WpPopPop_Admin_Settings::sanitize_settings($raw_input);
        update_option('wppoppop_settings', $sanitized);

        wp_send_json_success(['message' => __('Settings saved successfully!', 'wppoppop')]);
    }

    public function reset_cookies() {
        check_ajax_referer('wppoppop_settings_nonce', 'nonce');
        if (!current_user_can('manage_wppoppop') && !current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Unauthorized.', 'wppoppop')]);
        }

        $epoch = time();
        update_option('wppoppop_cookie_epoch', $epoch);
        wp_send_json_success(['message' => __('Cookies have been successfully reset across all popups.', 'wppoppop')]);
    }
}
