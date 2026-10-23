<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * WpPopPop Tracking, A/B, Privacy, Rate-limit and Reporting controls.
 * Single owner of the "Tracking & A/B" settings group.
 */
class WpPopPop_Tracking {
    const DB_VER = '1';

    public static function boot() {
        add_filter('cron_schedules', [__CLASS__, 'cron_schedules']);
        add_action('init', [__CLASS__, 'maybe_install']);
        add_action('init', [__CLASS__, 'sync_schedules']);
        add_action('wppoppop_tracking_daily', [__CLASS__, 'run_daily']);
        add_action('wppoppop_weekly_report', [__CLASS__, 'send_weekly_report']);
    }

    /* ------------------------------------------------------------ settings */
    public static function defaults() {
        return [
            'track_impressions' => 1, 'track_once_session' => 0, 'track_exclude_admins' => 0,
            'track_exclude_bots' => 0, 'track_retention_days' => 90,
            'ab_default_mode' => 'onload', 'ab_cookie_days' => 30, 'ab_sticky_user' => 0,
            'ab_auto_promote' => 0, 'ab_promote_min_impr' => 90, 'ab_promote_confidence' => 95,
            'cache_nocache_ab' => 0,
            'privacy_anonymize_ip' => 0, 'privacy_respect_dnt' => 0, 'privacy_consent_cookie' => '',
            'freq_max_per_page' => 0, 'freq_min_gap' => 0,
            'rate_limit_impr' => 600, 'rate_limit_submit' => 10, 'cors_origins' => '',
            'debug_ab' => 1, 'report_enabled' => 0, 'report_email' => '',
        ];
    }

    public static function get($key) {
        $d = self::defaults();
        return wppoppop_get_setting($key, isset($d[$key]) ? $d[$key] : '');
    }

    /** Whitelist + clamp. Missing key keeps the stored value (then the default). */
    public static function sanitize_input($in) {
        $prev = get_option('wppoppop_settings', []);
        $prev = is_array($prev) ? $prev : [];
        $d    = self::defaults();
        $pick = function ($k) use ($in, $prev, $d) {
            if (array_key_exists($k, $in))   { return $in[$k]; }
            if (array_key_exists($k, $prev)) { return $prev[$k]; }
            return $d[$k];
        };
        $int = function ($k, $min, $max) use ($pick) {
            return min($max, max($min, (int) $pick($k)));
        };
        $o = [];
        foreach (['track_impressions', 'track_once_session', 'track_exclude_admins', 'track_exclude_bots',
                  'ab_sticky_user', 'ab_auto_promote', 'cache_nocache_ab', 'privacy_anonymize_ip',
                  'privacy_respect_dnt', 'debug_ab', 'report_enabled'] as $k) {
            $o[$k] = !empty($pick($k)) ? 1 : 0;
        }
        $o['track_retention_days']  = $int('track_retention_days', 0, 3650);
        $o['ab_cookie_days']        = $int('ab_cookie_days', 1, 365);
        $o['ab_promote_min_impr']   = $int('ab_promote_min_impr', 50, 100000);
        $o['freq_max_per_page']     = $int('freq_max_per_page', 0, 20);
        $o['freq_min_gap']          = $int('freq_min_gap', 0, 3600);
        $o['rate_limit_impr']       = $int('rate_limit_impr', 1, 600);
        $o['rate_limit_submit']     = $int('rate_limit_submit', 1, 120);
        $mode = (string) $pick('ab_default_mode');
        $o['ab_default_mode']       = in_array($mode, ['inline', 'onload'], true) ? $mode : 'inline';
        $conf = (int) $pick('ab_promote_confidence');
        $o['ab_promote_confidence'] = in_array($conf, [90, 95, 99], true) ? $conf : 95;
        $o['privacy_consent_cookie'] = preg_replace('/[^A-Za-z0-9_\-]/', '', (string) $pick('privacy_consent_cookie'));
        $em = sanitize_email((string) $pick('report_email'));
        $o['report_email'] = is_email($em) ? $em : '';

        $origins = [];
        foreach (preg_split('/[\r\n,]+/', (string) $pick('cors_origins')) as $line) {
            $u = esc_url_raw(trim($line));
            $p = $u ? wp_parse_url($u) : null;
            if ($p && !empty($p['scheme']) && !empty($p['host']) && in_array($p['scheme'], ['http', 'https'], true)) {
                $origins[] = $p['scheme'] . '://' . $p['host'] . (!empty($p['port']) ? ':' . (int) $p['port'] : '');
            }
        }
        $o['cors_origins'] = implode("\n", array_values(array_unique($origins)));
        return $o;
    }

    /* ------------------------------------------------------------ database */
    public static function table() {
        global $wpdb;
        return $wpdb->prefix . 'wppoppop_stats_daily';
    }

    public static function maybe_install() {
        if (get_option('wppoppop_tracking_db') === self::DB_VER) {
            return;
        }
        global $wpdb;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        $cc = $wpdb->get_charset_collate();
        dbDelta("CREATE TABLE " . self::table() . " (
            stat_date date NOT NULL,
            popup_uid varchar(64) NOT NULL,
            impressions bigint(20) unsigned NOT NULL DEFAULT 0,
            PRIMARY KEY  (stat_date,popup_uid)
        ) {$cc};");
        update_option('wppoppop_tracking_db', self::DB_VER);
    }

    /* --------------------------------------------------------- request utils */
    public static function client_ip() {
        if (class_exists('WpPopPop_Security') && is_callable(['WpPopPop_Security', 'get_client_ip'])) {
            $ip = WpPopPop_Security::get_client_ip();
            if ($ip) { return (string) $ip; }
        }
        return isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '';
    }

    public static function anonymize_ip($ip) {
        if (!self::get('privacy_anonymize_ip') || $ip === '') {
            return $ip;
        }
        $bin = @inet_pton($ip);
        if ($bin === false) { return $ip; }
        if (strlen($bin) === 4) {
            $bin[3] = "\0";
        } else {
            for ($i = 6; $i < 16; $i++) { $bin[$i] = "\0"; }
        }
        return inet_ntop($bin);
    }

    public static function is_bot() {
        $ua = isset($_SERVER['HTTP_USER_AGENT']) ? (string) wp_unslash($_SERVER['HTTP_USER_AGENT']) : '';
        if ($ua === '') { return true; }
        return (bool) preg_match('/bot|crawl|spider|slurp|bingpreview|facebookexternalhit|headless|lighthouse|pingdom|monitor|curl|wget|python-requests/i', $ua);
    }

    /** Fixed-limit per-minute throttle per IP and bucket. True = allowed. */
    public static function throttle($bucket, $limit) {
        $limit = max(1, (int) $limit);
        $key   = 'wpp_rl_' . sanitize_key($bucket) . '_' . md5(self::client_ip());
        $n     = (int) get_transient($key);
        if ($n >= $limit) { return false; }
        set_transient($key, $n + 1, MINUTE_IN_SECONDS);
        return true;
    }

    public static function cors($res) {
        $list = array_filter(array_map('trim', explode("\n", (string) self::get('cors_origins'))));
        if (empty($list)) {
            $res->header('Access-Control-Allow-Origin', '*');
            return $res;
        }
        $origin = isset($_SERVER['HTTP_ORIGIN']) ? esc_url_raw(wp_unslash($_SERVER['HTTP_ORIGIN'])) : '';
        $origin = rtrim($origin, '/');
        if ($origin !== '' && in_array($origin, $list, true)) {
            $res->header('Access-Control-Allow-Origin', $origin);
            $res->header('Vary', 'Origin');
        }
        return $res;
    }

    /* ----------------------------------------------------------- impressions */
    public static function rest_impression($request) {
        $params = $request->get_json_params() ?: $request->get_body_params();
        $uid    = isset($params['uid']) ? sanitize_key($params['uid']) : '';
        $out    = ['success' => true, 'tracked' => false];
        $code   = 200;

        if (!self::get('track_impressions') || $uid === '') {
            // tracking off or empty uid: acknowledge only
        } elseif (self::get('track_exclude_bots') && self::is_bot()) {
            // bots are acknowledged but never counted
        } elseif (!self::throttle('imp', (int) self::get('rate_limit_impr'))) {
            $out  = ['success' => false, 'message' => 'rate_limited'];
            $code = 429;
        } else {
            $out['tracked'] = self::record_impression($uid);
        }
        return self::cors(new WP_REST_Response($out, $code));
    }

    public static function record_impression($uid) {
        global $wpdb;
        $n = $wpdb->query($wpdb->prepare(
            "UPDATE {$wpdb->prefix}wppoppop_items SET impressions = impressions + 1 WHERE uid = %s", $uid
        ));
        if (!$n) { return false; }
        $wpdb->query($wpdb->prepare(
            "INSERT INTO " . self::table() . " (stat_date, popup_uid, impressions) VALUES (%s, %s, 1)
             ON DUPLICATE KEY UPDATE impressions = impressions + 1",
            current_time('Y-m-d'), $uid
        ));
        return true;
    }

    /* ------------------------------------------------------------------ A/B */
    public static function ab_default_mode() { return self::get('ab_default_mode') === 'onload' ? 'onload' : 'inline'; }
    public static function ab_cookie_days()  { return max(1, (int) self::get('ab_cookie_days')); }

    public static function ab_no_cache() {
        if (!self::get('cache_nocache_ab')) { return; }
        if (!defined('DONOTCACHEPAGE')) { define('DONOTCACHEPAGE', true); }
        if (!headers_sent()) { nocache_headers(); }
    }

    public static function ab_sticky_get($uid, $chosen, $variants) {
        if (self::get('ab_sticky_user') && is_user_logged_in()) {
            $m = sanitize_key((string) get_user_meta(get_current_user_id(), 'wppoppop_ab_' . $uid, true));
            if ($m !== '' && in_array($m, (array) $variants, true)) { return $m; }
        }
        return $chosen;
    }

    public static function ab_sticky_set($uid, $chosen) {
        if (self::get('ab_sticky_user') && is_user_logged_in()) {
            update_user_meta(get_current_user_id(), 'wppoppop_ab_' . $uid, $chosen);
        }
    }

    /* ---------------------------------------------------------------- front */
    public static function front_vars() {
        return [
            'rest_url' => esc_url_raw(rest_url()),
            'tracking' => [
                'enabled'        => (bool) self::get('track_impressions'),
                'once_session'   => (bool) self::get('track_once_session'),
                'exclude_admins' => (bool) self::get('track_exclude_admins'),
                'is_admin'       => current_user_can('manage_options'),
                'respect_dnt'    => (bool) self::get('privacy_respect_dnt'),
                'consent_cookie' => (string) self::get('privacy_consent_cookie'),
                'max_per_page'   => (int) self::get('freq_max_per_page'),
                'min_gap'        => (int) self::get('freq_min_gap'),
                'debug'          => (bool) self::get('debug_ab'),
            ],
        ];
    }

    /* ------------------------------------------------------------ scheduling */
    public static function cron_schedules($s) {
        if (!isset($s['weekly'])) {
            $s['weekly'] = ['interval' => WEEK_IN_SECONDS, 'display' => __('Once Weekly', 'wppoppop')];
        }
        return $s;
    }

    public static function sync_schedules() {
        if (!wp_next_scheduled('wppoppop_tracking_daily')) {
            wp_schedule_event(time() + HOUR_IN_SECONDS, 'daily', 'wppoppop_tracking_daily');
        }
        $on   = (bool) self::get('report_enabled');
        $next = wp_next_scheduled('wppoppop_weekly_report');
        if ($on && !$next) {
            wp_schedule_event(time() + DAY_IN_SECONDS, 'weekly', 'wppoppop_weekly_report');
        } elseif (!$on && $next) {
            wp_unschedule_event($next, 'wppoppop_weekly_report');
        }
    }

    public static function run_daily() {
        global $wpdb;
        $days = (int) self::get('track_retention_days');
        if ($days > 0) {
            $cut = gmdate('Y-m-d', current_time('timestamp') - $days * DAY_IN_SECONDS);
            $wpdb->query($wpdb->prepare("DELETE FROM " . self::table() . " WHERE stat_date < %s", $cut));
        }
        if (self::get('ab_auto_promote')) {
            self::auto_promote();
        }
    }

    protected static function z_score($s1, $n1, $s2, $n2) {
        if ($n1 < 1 || $n2 < 1) { return 0; }
        $p = ($s1 + $s2) / ($n1 + $n2);
        $se = sqrt($p * (1 - $p) * (1 / $n1 + 1 / $n2));
        return $se > 0 ? (($s1 / $n1) - ($s2 / $n2)) / $se : 0;
    }

    /** Keeps only the winner once every variant has the minimum impressions and the lead is significant. */
    protected static function auto_promote() {
        global $wpdb;
        $min  = (int) self::get('ab_promote_min_impr');
        $zmap = [90 => 1.645, 95 => 1.96, 99 => 2.576];
        $thr  = $zmap[(int) self::get('ab_promote_confidence')];
        $camps = $wpdb->get_results("SELECT id, uid, popup_uids FROM {$wpdb->prefix}wppoppop_campaigns WHERE LOWER(status) = 'active'");
        foreach ((array) $camps as $c) {
            $uids = array_values(array_filter(array_map('sanitize_key', (array) json_decode($c->popup_uids, true))));
            if (count($uids) < 2) { continue; }
            $ph   = implode(',', array_fill(0, count($uids), '%s'));
            $rows = $wpdb->get_results($wpdb->prepare(
                "SELECT uid, impressions, submissions FROM {$wpdb->prefix}wppoppop_items WHERE uid IN ($ph)", $uids
            ));
            if (count($rows) < 2) { continue; }
            $ok = true; $stats = [];
            foreach ($rows as $r) {
                if ((int) $r->impressions < $min) { $ok = false; break; }
                $stats[] = ['uid' => $r->uid, 'n' => (int) $r->impressions, 's' => (int) $r->submissions];
            }
            if (!$ok) { continue; }
            usort($stats, function ($a, $b) { return ($b['s'] / $b['n']) <=> ($a['s'] / $a['n']); });
            if (self::z_score($stats[0]['s'], $stats[0]['n'], $stats[1]['s'], $stats[1]['n']) >= $thr) {
                $wpdb->update("{$wpdb->prefix}wppoppop_campaigns", ['popup_uids' => wp_json_encode([$stats[0]['uid']])], ['id' => (int) $c->id], ['%s'], ['%d']);
                if (function_exists('wppoppop_log_event')) {
                    wppoppop_log_event('ab_auto_promote', 'A/B winner promoted: ' . $stats[0]['uid'], ['campaign' => $c->uid, 'stats' => $stats]);
                }
            }
        }
    }

    public static function send_weekly_report() {
        if (!self::get('report_enabled')) { return; }
        global $wpdb;
        $to = sanitize_email((string) self::get('report_email'));
        if (!is_email($to)) { $to = get_option('admin_email'); }
        $since_d = gmdate('Y-m-d', current_time('timestamp') - 7 * DAY_IN_SECONDS);
        $since_t = $since_d . ' 00:00:00';
        $impr  = $wpdb->get_results($wpdb->prepare("SELECT popup_uid, SUM(impressions) AS n FROM " . self::table() . " WHERE stat_date >= %s GROUP BY popup_uid", $since_d), OBJECT_K);
        $leads = $wpdb->get_results($wpdb->prepare("SELECT popup_uid, COUNT(id) AS n FROM {$wpdb->prefix}wppoppop_submissions WHERE created_at >= %s GROUP BY popup_uid", $since_t), OBJECT_K);
        $items = $wpdb->get_results("SELECT uid, title FROM {$wpdb->prefix}wppoppop_items WHERE status != 'trash'");
        $lines = []; $ti = 0; $tl = 0;
        foreach ((array) $items as $it) {
            $i = isset($impr[$it->uid]) ? (int) $impr[$it->uid]->n : 0;
            $l = isset($leads[$it->uid]) ? (int) $leads[$it->uid]->n : 0;
            if ($i === 0 && $l === 0) { continue; }
            $ti += $i; $tl += $l;
            $lines[] = sprintf('%s: %d impressions, %d leads, %s%% CR', $it->title, $i, $l, $i > 0 ? round($l / $i * 100, 1) : 0);
        }
        $body = sprintf("WpPopPop weekly report (since %s)\n\nTotal impressions: %d\nTotal leads: %d\nConversion rate: %s%%\n\n%s\n",
            $since_d, $ti, $tl, $ti > 0 ? round($tl / $ti * 100, 1) : 0, $lines ? implode("\n", $lines) : 'No activity recorded.');
        wp_mail($to, sprintf('[%s] WpPopPop weekly report', get_bloginfo('name')), $body);
    }
}

WpPopPop_Tracking::boot();
