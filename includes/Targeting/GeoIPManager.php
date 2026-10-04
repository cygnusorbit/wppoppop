<?php
namespace WPPopPop\Targeting;

use WPPopPop\Admin\SettingsManager;

class GeoIPManager {
    public static function get_client_ip(): string {
        $ip_keys = [
            'HTTP_CF_CONNECTING_IP',
            'HTTP_X_REAL_IP',
            'HTTP_X_FORWARDED_FOR',
            'REMOTE_ADDR'
        ];

        foreach ($ip_keys as $key) {
            if (!empty($_SERVER[$key])) {
                $ips = explode(',', $_SERVER[$key]);
                $clean_ip = trim($ips[0]);
                if (filter_var($clean_ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $clean_ip;
                }
            }
        }

        return sanitize_text_field($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');
    }

    public static function get_visitor_country(): string {
        // 1. Direct Edge Headers (Cloudflare, cPanel/LiteSpeed GeoIP)
        if (!empty($_SERVER['HTTP_CF_IPCOUNTRY'])) {
            return strtoupper(sanitize_text_field($_SERVER['HTTP_CF_IPCOUNTRY']));
        }
        if (!empty($_SERVER['GEOIP_COUNTRY_CODE'])) {
            return strtoupper(sanitize_text_field($_SERVER['GEOIP_COUNTRY_CODE']));
        }

        $ip = self::get_client_ip();
        if ($ip === '127.0.0.1' || $ip === '::1') {
            return 'US'; // Localhost fallback
        }

        // Check transient cache
        $transient_key = 'wppoppop_geoip_' . md5($ip);
        $cached = get_transient($transient_key);
        if ($cached !== false) {
            return (string) $cached;
        }

        $service = SettingsManager::get('geoip_service', 'none');
        $country = 'UNKNOWN';

        if ($service === 'ipstack') {
            $api_key = SettingsManager::get('ipstack_api_key', '');
            if (!empty($api_key)) {
                $response = wp_remote_get("http://api.ipstack.com/{$ip}?access_key={$api_key}&fields=country_code", ['timeout' => 3]);
                if (!is_wp_error($response)) {
                    $data = json_decode(wp_remote_retrieve_body($response), true);
                    $country = strtoupper($data['country_code'] ?? 'UNKNOWN');
                }
            }
        }

        // Fallback to lightweight open service if not configured
        if ($country === 'UNKNOWN') {
            $response = wp_remote_get("https://ipapi.co/{$ip}/country/", ['timeout' => 3]);
            if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
                $code = trim(wp_remote_retrieve_body($response));
                if (strlen($code) === 2) {
                    $country = strtoupper($code);
                }
            }
        }

        set_transient($transient_key, $country, 12 * HOUR_IN_SECONDS);
        return $country;
    }

    public static function is_country_allowed(int $popup_id): bool {
        $mode = get_post_meta($popup_id, '_wppoppop_geoip_mode', true) ?: 'all';
        if ($mode === 'all') {
            return true;
        }

        $target_countries_raw = get_post_meta($popup_id, '_wppoppop_geoip_countries', true) ?: '';
        if (empty($target_countries_raw)) {
            return true;
        }

        $target_countries = array_map('trim', explode(',', strtoupper($target_countries_raw)));
        $visitor_country = self::get_visitor_country();

        if ($mode === 'include') {
            return in_array($visitor_country, $target_countries, true);
        }

        if ($mode === 'exclude') {
            return !in_array($visitor_country, $target_countries, true);
        }

        return true;
    }
}
