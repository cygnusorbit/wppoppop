<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Dashboard box layout: per-user order of the overview boxes.
 * Whitelist: order[] = known box keys only (unique, sanitize_key).
 */
class WpPopPop_Ajax_Dash_Layout {
    const META = 'wppoppop_dash_layout';

    public function __construct() {
        add_action('wp_ajax_wppoppop_save_dash_layout', [$this, 'save']);
    }

    public static function keys() {
        return ['popups', 'recent', 'tools'];
    }

    /** Saved order first (valid keys only), then any box not yet placed. */
    public static function get_order($user_id = 0) {
        $keys  = self::keys();
        $saved = get_user_meta($user_id ? (int) $user_id : get_current_user_id(), self::META, true);
        $out   = [];
        if (is_array($saved)) {
            foreach ($saved as $k) {
                if (!is_scalar($k)) { continue; }
                $k = sanitize_key((string) $k);
                if (in_array($k, $keys, true) && !in_array($k, $out, true)) { $out[] = $k; }
            }
        }
        foreach ($keys as $k) {
            if (!in_array($k, $out, true)) { $out[] = $k; }
        }
        return $out;
    }

    public function save() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Unauthorized action.', 'wppoppop')], 403);
        }
        $nonce = isset($_REQUEST['nonce']) ? sanitize_text_field(wp_unslash($_REQUEST['nonce'])) : '';
        if (!wp_verify_nonce($nonce, 'wppoppop_admin_nonce')) {
            wp_send_json_error(['message' => __('Security check failed. Please refresh the page.', 'wppoppop')], 403);
        }
        $raw   = isset($_POST['order']) ? (array) wp_unslash($_POST['order']) : [];
        $clean = [];
        foreach (array_slice($raw, 0, 20) as $k) {
            if (!is_scalar($k)) { continue; }
            $k = sanitize_key((string) $k);
            if (in_array($k, self::keys(), true) && !in_array($k, $clean, true)) { $clean[] = $k; }
        }
        update_user_meta(get_current_user_id(), self::META, $clean);
        wp_send_json_success(['order' => self::get_order()]);
    }
}
