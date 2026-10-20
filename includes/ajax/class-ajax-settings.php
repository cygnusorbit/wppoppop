<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Ajax_Settings {
    public function __construct() {
        add_action('wp_ajax_wppoppop_save_settings', [$this, 'save_settings']);
        add_action('wp_ajax_wppoppop_reset_cookies', [$this, 'reset_cookies']);
    }

    private function verify_security() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Unauthorized capability.', 'wppoppop')]);
        }
        $nonce = isset($_REQUEST['nonce']) ? sanitize_text_field(wp_unslash($_REQUEST['nonce'])) : '';
        if (!wp_verify_nonce($nonce, 'wppoppop_settings_nonce') && !wp_verify_nonce($nonce, 'wppoppop_admin_nonce')) {
            wp_send_json_error(['message' => __('Security verification failed. Please reload the page.', 'wppoppop')]);
        }
    }

    public function save_settings() {
        $this->verify_security();

        // Support both nested 'settings' array and flattened POST payload
        $raw = [];
        if (isset($_POST['settings']) && is_array($_POST['settings'])) {
            $raw = wp_unslash($_POST['settings']);
        } elseif (isset($_POST['data'])) {
            parse_str(wp_unslash($_POST['data']), $parsed);
            $raw = isset($parsed['settings']) && is_array($parsed['settings']) ? $parsed['settings'] : $parsed;
        } else {
            $raw = wp_unslash($_POST);
        }

        $existing = get_option('wppoppop_settings', []);
        if (!is_array($existing)) {
            $existing = [];
        }

        if (!class_exists('WpPopPop_Admin_Settings')) {
            require_once WPPOPPOP_PATH . 'includes/admin/class-admin-settings.php';
        }

        // Cookie epoch is server-owned: never trust the client value.
        $raw['cookie_epoch'] = isset($existing['cookie_epoch'])
            ? (int) $existing['cookie_epoch']
            : (int) get_option('wppoppop_cookie_epoch', 1);

        $clean = WpPopPop_Admin_Settings::sanitize_settings($raw);

        update_option('wppoppop_settings', $clean);
        if (function_exists('wppoppop_flush_settings_cache')) {
            wppoppop_flush_settings_cache();
        }
        $clean = get_option('wppoppop_settings', $clean);

        wp_send_json_success([
            'message'  => __('Settings saved successfully!', 'wppoppop'),
            'settings' => $clean
        ]);
    }

    public function reset_cookies() {
        $this->verify_security();

        $settings = get_option('wppoppop_settings', []);
        if (!is_array($settings)) {
            $settings = [];
        }

        $epoch = time();
        $settings['cookie_epoch'] = $epoch;
        update_option('wppoppop_settings', $settings);
        update_option('wppoppop_cookie_epoch', $epoch);

        wp_send_json_success([
            'message' => __('Cookie epoch reset successfully! All visitor popups will re-trigger.', 'wppoppop'),
            'epoch'   => $epoch
        ]);
    }
}
