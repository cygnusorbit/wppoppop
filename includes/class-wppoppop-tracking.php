<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * WpPopPop Tracking, Privacy and Rate-limit controls.
 * Single owner of the "Tracking" settings group. Statistics and A/B testing are provided by the Pro add-on
 * through the wppoppop_impression_recorded action and the wppoppop_tracking_tab_sections action.
 */
class WpPopPop_Tracking {
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
        $bin = /* phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- invalid addresses must not raise a warning */ @inet_pton($ip);
        if ($bin === false) { return $ip; }
        if (strlen($bin) === 4) {
            $bin[3] = "\0";
        } else {
            for ($i = 6; $i < 16; $i++) { $bin[$i] = "\0"; }
        }
        return inet_ntop($bin);
    }

    public static function is_bot() {
        $ua = ! empty( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';
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
        do_action('wppoppop_impression_recorded', $uid);
        return true;
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

}
