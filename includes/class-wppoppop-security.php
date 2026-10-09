<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Security {

    /**
     * Resolve the client IP address considering proxy and CDN headers.
     */
    public static function get_client_ip() {
        $ip = '';
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $ip = sanitize_text_field(wp_unslash($_SERVER['HTTP_CF_CONNECTING_IP']));
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $forwarded = sanitize_text_field(wp_unslash($_SERVER['HTTP_X_FORWARDED_FOR']));
            $parts = explode(',', $forwarded);
            $ip = trim($parts[0]);
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            $ip = sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR']));
        }

        return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '127.0.0.1';
    }

    /**
     * Check if client IP has exceeded the hourly submission rate limit.
     */
    public static function is_rate_limited($ip, $max_per_hour = 10) {
        if (empty($ip) || $max_per_hour <= 0) {
            return false;
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_submissions';
        if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") !== $table_name) {
            return false;
        }

        $one_hour_ago = gmdate('Y-m-d H:i:s', time() - HOUR_IN_SECONDS);
        $count = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$table_name} WHERE ip_address = %s AND created_at >= %s",
            $ip,
            $one_hour_ago
        ));

        return ($count >= $max_per_hour);
    }

    /**
     * Verify Google reCAPTCHA v2 / v3 token via Google verification API.
     */
    public static function verify_recaptcha($token, $remote_ip = '') {
        $secret_key = wppoppop_get_setting('recaptcha_secret_key', '');
        if (empty($secret_key)) {
            return true; // Bypass if secret key is unconfigured
        }
        if (empty($token)) {
            return false;
        }

        $response = wp_remote_post('https://www.google.com/recaptcha/api/siteverify', [
            'timeout' => 5,
            'body'    => [
                'secret'   => $secret_key,
                'response' => $token,
                'remoteip' => $remote_ip
            ]
        ]);

        if (is_wp_error($response)) {
            return true; // Fail open on network failure to avoid locking out genuine users
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (is_array($data) && !empty($data['success'])) {
            // For v3, evaluate minimum score threshold
            if (isset($data['score']) && (float) $data['score'] < 0.4) {
                return false;
            }
            return true;
        }

        return false;
    }

    /**
     * Verify if email belongs to a disposable temporary email service.
     */
    public static function is_disposable_email($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        }

        $domain = strtolower(substr(strrchr($email, "@"), 1));
        if (empty($domain)) {
            return true;
        }

        $disposable_domains = [
            'mailinator.com', '10minutemail.com', '10minutemail.net', 'tempmail.com',
            'temp-mail.org', 'guerrillamail.com', 'guerrillamail.net', 'guerrillamail.org',
            'throwawaymail.com', 'yopmail.com', 'trashmail.com', 'trashmail.net',
            'getairmail.com', 'sharklasers.com', 'dispostable.com', 'fakemailgenerator.com',
            'burnermail.io', 'dropmail.me', 'mohmal.com', 'mytrashmail.com', 'mailnesia.com',
            'inboxkitten.com', 'crazymailing.com', 'maildrop.cc', 'getnada.com', 'tempail.com',
            'tempinbox.com', 'fakeinbox.com', 'emailondeck.com', 'mintemail.com', 'nada.ltd'
        ];

        if (in_array($domain, $disposable_domains, true)) {
            return true;
        }

        // Substring pattern matching
        if (strpos($domain, 'throwaway') !== false || strpos($domain, 'tempmail') !== false || strpos($domain, 'trashmail') !== false) {
            return true;
        }

        return false;
    }

    /**
     * Resolve visitor two-letter ISO country code based on configured service with transient caching.
     */
    public static function detect_visitor_country($ip = '') {
        $geo_enabled = (bool) wppoppop_get_setting('ip_geotargeting', false);
        if (!$geo_enabled) {
            return '';
        }

        if (empty($ip)) {
            $ip = self::get_client_ip();
        }

        // 1. Direct Edge Headers (Cloudflare, MaxMind Server Module, LiteSpeed)
        if (!empty($_SERVER['HTTP_CF_IPCOUNTRY']) && strlen($_SERVER['HTTP_CF_IPCOUNTRY']) === 2) {
            return strtoupper(sanitize_text_field(wp_unslash($_SERVER['HTTP_CF_IPCOUNTRY'])));
        }
        if (!empty($_SERVER['GEOIP_COUNTRY_CODE']) && strlen($_SERVER['GEOIP_COUNTRY_CODE']) === 2) {
            return strtoupper(sanitize_text_field(wp_unslash($_SERVER['GEOIP_COUNTRY_CODE'])));
        }
        if (!empty($_SERVER['MM_COUNTRY_CODE']) && strlen($_SERVER['MM_COUNTRY_CODE']) === 2) {
            return strtoupper(sanitize_text_field(wp_unslash($_SERVER['MM_COUNTRY_CODE'])));
        }

        $service = wppoppop_get_setting('geoip_service', 'none');
        if ($service === 'none') {
            return '';
        }

        // Localhost fallback
        if (in_array($ip, ['127.0.0.1', '::1', '0.0.0.0'], true) || strpos($ip, '192.168.') === 0 || strpos($ip, '10.') === 0) {
            return 'US';
        }

        $transient_key = 'wppoppop_geo_' . md5($service . '_' . $ip);
        $cached_country = get_transient($transient_key);
        if ($cached_country !== false && is_string($cached_country)) {
            return $cached_country;
        }

        $country = '';

        // Provider: ipapi.co
        if ($service === 'ipapi') {
            $res = wp_remote_get("https://ipapi.co/{$ip}/country/", ['timeout' => 3]);
            if (!is_wp_error($res)) {
                $code = trim(wp_remote_retrieve_body($res));
                if (strlen($code) === 2 && ctype_alpha($code)) {
                    $country = strtoupper($code);
                }
            }
        }
        // Provider: ip-api.com
        elseif ($service === 'ip-api') {
            $res = wp_remote_get("http://ip-api.com/json/{$ip}?fields=countryCode", ['timeout' => 3]);
            if (!is_wp_error($res)) {
                $json = json_decode(wp_remote_retrieve_body($res), true);
                if (!empty($json['countryCode']) && strlen($json['countryCode']) === 2) {
                    $country = strtoupper($json['countryCode']);
                }
            }
        }

        if (!empty($country)) {
            set_transient($transient_key, $country, DAY_IN_SECONDS);
        }

        return $country;
    }
}
