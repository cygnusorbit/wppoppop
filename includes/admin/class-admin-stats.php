<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Statistics page service: per-popup label colors, per-user table/chart prefs,
 * device capture for submissions, and the AJAX whitelist.
 */
class WpPopPop_Admin_Stats {
    const OPT_LABELS = 'wppoppop_stats_labels';
    const META_PREFS = 'wppoppop_stats_prefs';
    const DB_VER     = '1';
    const MAX_SERIES = 8;

    protected static $booted = false;

    public static function boot() {
        if (self::$booted) {
            return;
        }
        self::$booted = true;
        add_action('init', [__CLASS__, 'maybe_install']);
        add_action('wp_ajax_wppoppop_stats_label_save', [__CLASS__, 'ajax_label_save']);
        add_action('wp_ajax_wppoppop_stats_label_delete', [__CLASS__, 'ajax_label_delete']);
        add_action('wp_ajax_wppoppop_stats_prefs_save', [__CLASS__, 'ajax_prefs_save']);
    }

    /* ---------------------------------------------------------- schema */
    public static function maybe_install() {
        if (get_option('wppoppop_stats_db') === self::DB_VER) {
            return;
        }
        global $wpdb;
        $t = $wpdb->prefix . 'wppoppop_submissions';
        if ($wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($t))) !== $t) {
            return;
        }
        $has = $wpdb->get_var($wpdb->prepare("SHOW COLUMNS FROM {$t} LIKE %s", 'device'));
        if (!$has) {
            $wpdb->query("ALTER TABLE {$t} ADD COLUMN device varchar(16) NOT NULL DEFAULT ''");
            $has = $wpdb->get_var($wpdb->prepare("SHOW COLUMNS FROM {$t} LIKE %s", 'device'));
        }
        if ($has) {
            update_option('wppoppop_stats_db', self::DB_VER, false);
        }
    }

    public static function detect_device($ua = '') {
        if ($ua === '' && isset($_SERVER['HTTP_USER_AGENT'])) {
            $ua = (string) wp_unslash($_SERVER['HTTP_USER_AGENT']);
        }
        $ua = strtolower($ua);
        if ($ua === '') {
            return '';
        }
        if (preg_match('/ipad|tablet|kindle|silk|playbook/', $ua) || (strpos($ua, 'android') !== false && strpos($ua, 'mobile') === false)) {
            return 'tablet';
        }
        if (preg_match('/mobi|iphone|ipod|android|blackberry|opera mini|windows phone/', $ua)) {
            return 'mobile';
        }
        return 'desktop';
    }

    /** Called right after a submission insert (AJAX + REST paths). */
    public static function record_device($submission_id) {
        $submission_id = (int) $submission_id;
        if ($submission_id <= 0 || get_option('wppoppop_stats_db') !== self::DB_VER) {
            return;
        }
        $device = self::detect_device();
        if ($device === '') {
            return;
        }
        global $wpdb;
        $wpdb->update($wpdb->prefix . 'wppoppop_submissions', ['device' => $device], ['id' => $submission_id], ['%s'], ['%d']);
    }

    /* ----------------------------------------------------- definitions */
    public static function columns() {
        return [
            'rank'        => __('Rank', 'wppoppop'),
            'popup'       => __('Popup Campaign', 'wppoppop'),
            'label'       => __('Label Color', 'wppoppop'),
            'country'     => __('Country', 'wppoppop'),
            'device'      => __('Device', 'wppoppop'),
            'impressions' => __('Impressions', 'wppoppop'),
            'leads'       => __('Captured Leads', 'wppoppop'),
            'conversion'  => __('Conversion Rate', 'wppoppop'),
            'status'      => __('Status', 'wppoppop'),
            'action'      => __('Action', 'wppoppop'),
        ];
    }

    public static function palette() {
        return ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ef4444', '#06b6d4', '#ec4899', '#84cc16'];
    }

    public static function labels() {
        $l = get_option(self::OPT_LABELS, []);
        return is_array($l) ? $l : [];
    }

    public static function prefs($user_id = 0) {
        $user_id = $user_id ? (int) $user_id : get_current_user_id();
        $raw = get_user_meta($user_id, self::META_PREFS, true);
        $raw = is_array($raw) ? $raw : [];
        $all = array_keys(self::columns());
        $cols = (isset($raw['columns']) && is_array($raw['columns'])) ? array_values(array_intersect($all, $raw['columns'])) : $all;
        if (!in_array('popup', $cols, true)) {
            array_unshift($cols, 'popup');
        }
        $chart = (isset($raw['chart_uids']) && is_array($raw['chart_uids'])) ? array_values($raw['chart_uids']) : null;
        return ['columns' => $cols, 'chart_uids' => $chart];
    }

    /* ------------------------------------------------------------ AJAX */
    protected static function verify() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Unauthorized action.', 'wppoppop')]);
        }
        $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
        if (!wp_verify_nonce($nonce, 'wppoppop_admin_nonce')) {
            wp_send_json_error(['message' => __('Security check failed. Please refresh the page.', 'wppoppop')]);
        }
    }

    protected static function popup_exists($uid) {
        global $wpdb;
        if ($uid === '') {
            return false;
        }
        return (bool) $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}wppoppop_items WHERE uid = %s", $uid));
    }

    public static function ajax_label_save() {
        self::verify();
        $uid   = isset($_POST['uid']) ? sanitize_text_field(wp_unslash($_POST['uid'])) : '';
        $color = isset($_POST['color']) ? sanitize_hex_color(wp_unslash($_POST['color'])) : '';
        if (!self::popup_exists($uid) || !$color) {
            wp_send_json_error(['message' => __('Invalid popup or color.', 'wppoppop')]);
        }
        $labels = self::labels();
        $labels[$uid] = $color;
        update_option(self::OPT_LABELS, $labels, false);
        wp_send_json_success(['uid' => $uid, 'color' => $color]);
    }

    public static function ajax_label_delete() {
        self::verify();
        $uid = isset($_POST['uid']) ? sanitize_text_field(wp_unslash($_POST['uid'])) : '';
        $labels = self::labels();
        unset($labels[$uid]);
        update_option(self::OPT_LABELS, $labels, false);
        wp_send_json_success(['uid' => $uid]);
    }

    public static function ajax_prefs_save() {
        self::verify();
        $prefs = get_user_meta(get_current_user_id(), self::META_PREFS, true);
        $prefs = is_array($prefs) ? $prefs : [];

        if (isset($_POST['columns'])) {
            $cols = is_array($_POST['columns']) ? array_map('sanitize_key', wp_unslash($_POST['columns'])) : [];
            $cols = array_values(array_intersect(array_keys(self::columns()), $cols));
            if (!in_array('popup', $cols, true)) {
                array_unshift($cols, 'popup');
            }
            $prefs['columns'] = $cols;
        }
        if (!empty($_POST['chart_set'])) {
            $uids = (isset($_POST['chart_uids']) && is_array($_POST['chart_uids'])) ? array_map('sanitize_text_field', wp_unslash($_POST['chart_uids'])) : [];
            $uids = array_values(array_unique(array_filter($uids)));
            $prefs['chart_uids'] = array_slice($uids, 0, self::MAX_SERIES);
        }
        update_user_meta(get_current_user_id(), self::META_PREFS, $prefs);
        wp_send_json_success(['saved' => true]);
    }
}
