<?php
if (!defined('ABSPATH')) {
    exit;
}

/** AJAX endpoints for WpPopPop_Saved_Defaults (admin only, nonce protected). */
class WpPopPop_Ajax_Saved_Defaults {
    public function __construct() {
        add_action('wp_ajax_wppoppop_defaults_save', [$this, 'save']);
        add_action('wp_ajax_wppoppop_defaults_clear', [$this, 'clear']);
    }

    private function verify() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Unauthorized action.', 'wppoppop')], 403);
        }
        $nonce = isset($_REQUEST['nonce']) ? sanitize_text_field(wp_unslash($_REQUEST['nonce'])) : '';
        if (!wp_verify_nonce($nonce, WpPopPop_Saved_Defaults::NONCE)) {
            wp_send_json_error(['message' => __('Security check failed. Please refresh the page.', 'wppoppop')], 403);
        }
    }

    private function scope() {
        return isset($_POST['scope']) ? sanitize_key(wp_unslash($_POST['scope'])) : '';
    }

    private function respond($res, $message) {
        if (is_wp_error($res)) {
            wp_send_json_error(['message' => $res->get_error_message()]);
        }
        if (empty($res['file'])) {
            $message .= ' ' . __('(Stored on this site only: the plugin "defaults" folder is not writable.)', 'wppoppop');
        }
        wp_send_json_success(['message' => $message, 'status' => WpPopPop_Saved_Defaults::status()]);
    }

    public function save() {
        $this->verify();
        $scope = $this->scope();
        $data  = null;
        if ($scope === 'settings') {
            $data = WpPopPop_Saved_Defaults::capture_settings();
        } elseif ($scope === 'theme') {
            $data = WpPopPop_Saved_Defaults::capture_theme();
        } elseif ($scope === 'popup') {
            $raw = isset($_POST['data']) ? json_decode(wp_unslash($_POST['data']), true) : null; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- whitelisted by sanitize_popup()
            if (is_array($raw)) {
                $data = WpPopPop_Saved_Defaults::sanitize_popup($raw);
            }
        }
        if (!is_array($data)) {
            wp_send_json_error(['message' => __('Nothing to save.', 'wppoppop')]);
        }
        $this->respond(WpPopPop_Saved_Defaults::save($scope, $data), __('Defaults saved.', 'wppoppop'));
    }

    public function clear() {
        $this->verify();
        $this->respond(WpPopPop_Saved_Defaults::clear($this->scope()), __('Saved defaults removed.', 'wppoppop'));
    }
}
