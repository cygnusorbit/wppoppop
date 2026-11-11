<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Dashboard box layout: per-user columns, each holding a stack of boxes (WordPress dashboard style).
 * Whitelist: cols[0..2][] = known box keys only (unique, sanitize_key).
 */
class WpPopPop_Ajax_Dash_Layout {
    const META      = 'wppoppop_dash_layout';  // older flat order, still read once
    const COLS_META = 'wppoppop_dash_columns';
    const COLUMNS   = 3;

    public function __construct() {
        add_action('wp_ajax_wppoppop_save_dash_layout', [$this, 'save']);
    }

    public static function keys() {
        return ['popups', 'recent', 'promo']; // default order = default columns: Popups | Recent Submissions | Promo
    }

    private static function clean_key($k) {
        return is_scalar($k) ? sanitize_key((string) $k) : '';
    }

    /**
     * Boxes per column. $present limits the result to boxes that are rendered.
     * Boxes without a saved place go to the shortest column.
     */
    public static function get_columns($present = null, $user_id = 0) {
        $uid  = $user_id ? (int) $user_id : get_current_user_id();
        $keys = self::keys();
        if (is_array($present)) {
            $keys = array_values(array_intersect($keys, $present));
        }
        $cols   = array_fill(0, self::COLUMNS, []);
        $placed = [];
        $saved  = get_user_meta($uid, self::COLS_META, true);
        if (is_array($saved)) {
            for ($i = 0; $i < self::COLUMNS; $i++) {
                if (!isset($saved[$i]) || !is_array($saved[$i])) { continue; }
                foreach ($saved[$i] as $k) {
                    $k = self::clean_key($k);
                    if ($k !== '' && in_array($k, $keys, true) && !in_array($k, $placed, true)) {
                        $cols[$i][] = $k;
                        $placed[]   = $k;
                    }
                }
            }
        }
        $queue = [];
        if (!is_array($saved)) {
            $legacy = get_user_meta($uid, self::META, true);
            if (is_array($legacy)) {
                foreach ($legacy as $k) { $queue[] = self::clean_key($k); }
            }
        }
        foreach ($keys as $k) { $queue[] = $k; }
        foreach ($queue as $k) {
            if (!in_array($k, $keys, true) || in_array($k, $placed, true)) { continue; }
            $target = 0;
            for ($i = 1; $i < self::COLUMNS; $i++) {
                if (count($cols[$i]) < count($cols[$target])) { $target = $i; }
            }
            $cols[$target][] = $k;
            $placed[]        = $k;
        }
        return $cols;
    }

    /** Flat order (column by column). */
    public static function get_order($user_id = 0) {
        $out = [];
        foreach (self::get_columns(null, $user_id) as $col) {
            foreach ($col as $k) { $out[] = $k; }
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
        /* phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- each key is sanitize_key()d and checked against an allow-list */ $raw = isset($_POST['cols']) ? (array) wp_unslash($_POST['cols']) : [];
        $clean = [];
        $seen  = [];
        for ($i = 0; $i < self::COLUMNS; $i++) {
            $clean[$i] = [];
            $list      = (isset($raw[$i]) && is_array($raw[$i])) ? $raw[$i] : [];
            foreach (array_slice($list, 0, 20) as $k) {
                $k = self::clean_key($k);
                if ($k !== '' && in_array($k, self::keys(), true) && !in_array($k, $seen, true)) {
                    $clean[$i][] = $k;
                    $seen[]      = $k;
                }
            }
        }
        // Boxes that are not on the page right now (for example the promo box while Pro is active) keep their place.
        $old = get_user_meta(get_current_user_id(), self::COLS_META, true);
        if (is_array($old)) {
            for ($i = 0; $i < self::COLUMNS; $i++) {
                if (!isset($old[$i]) || !is_array($old[$i])) { continue; }
                foreach ($old[$i] as $k) {
                    $k = self::clean_key($k);
                    if ($k !== '' && in_array($k, self::keys(), true) && !in_array($k, $seen, true)) {
                        $clean[$i][] = $k;
                        $seen[]      = $k;
                    }
                }
            }
        }
        update_user_meta(get_current_user_id(), self::COLS_META, $clean);
        wp_send_json_success(['cols' => $clean]);
    }
}
