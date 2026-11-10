<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Embed UID format: <popup-name>-<6 lowercase alphanumerics>, unique across wppoppop_items.
 */
class WpPopPop_Uid {
    public static function base($title) {
        $base = sanitize_title((string) $title);
        $base = preg_replace('/[^a-z0-9-]+/', '-', strtolower($base));
        $base = trim(preg_replace('/-{2,}/', '-', $base), '-');
        $base = rtrim(substr($base, 0, 29), '-');
        return $base === '' ? 'popup' : $base;
    }

    public static function generate($title = '') {
        global $wpdb;
        $table = $wpdb->prefix . 'wppoppop_items';
        $base  = self::base($title);
        $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        for ($try = 0; $try < 30; $try++) {
            $suffix = '';
            for ($i = 0; $i < 6; $i++) {
                $suffix .= $chars[wp_rand(0, strlen($chars) - 1)];
            }
            $candidate = $base . '-' . $suffix;
            /* phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- table name is built from $wpdb->prefix and every value is prepared */ if (!$wpdb->get_var($wpdb->prepare("SELECT COUNT(1) FROM {$table} WHERE uid = %s", $candidate))) {
                return $candidate;
            }
        }
        return substr($base, 0, 20) . '-' . strtolower(wp_generate_password(6, false, false));
    }

    /** Re-base an auto-format uid on a new title. Legacy uids are returned untouched. */
    public static function rename_for_title($uid, $title) {
        global $wpdb;
        if (!preg_match('/^(.+)-([a-z0-9]{6})$/', $uid, $m)) {
            return $uid;
        }
        $base = self::base($title);
        if ($m[1] === $base) {
            return $uid;
        }
        $items = $wpdb->prefix . 'wppoppop_items';
        $new   = $base . '-' . $m[2];
        /* phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- table name is built from $wpdb->prefix and every value is prepared */ if ($wpdb->get_var($wpdb->prepare("SELECT COUNT(1) FROM {$items} WHERE uid = %s", $new))) {
            $new = self::generate($title);
        }
        $wpdb->update($items, ['uid' => $new], ['uid' => $uid], ['%s'], ['%s']);
        $wpdb->update($wpdb->prefix . 'wppoppop_submissions', ['popup_uid' => $new], ['popup_uid' => $uid], ['%s'], ['%s']);
        $wpdb->update($wpdb->prefix . 'wppoppop_transactions', ['popup_uid' => $new], ['popup_uid' => $uid], ['%s'], ['%s']);
        do_action('wppoppop_popup_uid_renamed', $uid, $new);
        return $new;
    }
}
