<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WpPopPop_Security')) {
    require_once WPPOPPOP_PATH . 'includes/class-wppoppop-security.php';
}

class WpPopPop_Front_Targeting {

    public function detect_visitor_country() {
        return WpPopPop_Security::detect_visitor_country();
    }

    public function is_eligible($popup) {
        if (!$popup || empty($popup->status) || $popup->status !== 'publish') {
            return false;
        }

        $config = !empty($popup->config) ? json_decode($popup->config, true) : [];
        if (!is_array($config)) {
            $config = [];
        }

        // 1. Geolocation Targeting Gate
        if ((bool) wppoppop_get_setting('ip_geotargeting', false)) {
            $visitor_country = $this->detect_visitor_country();
            $target_settings = isset($config['targeting']) && is_array($config['targeting']) ? $config['targeting'] : [];
            $geo_mode        = isset($target_settings['geo_mode']) ? $target_settings['geo_mode'] : 'all';
            $geo_countries   = isset($target_settings['geo_countries']) && is_array($target_settings['geo_countries']) ? array_map('strtoupper', $target_settings['geo_countries']) : [];

            if (!empty($visitor_country) && !empty($geo_countries)) {
                if ($geo_mode === 'whitelist' && !in_array($visitor_country, $geo_countries, true)) {
                    return false;
                }
                if ($geo_mode === 'blacklist' && in_array($visitor_country, $geo_countries, true)) {
                    return false;
                }
            }
        }

        // 2. Device Viewport Targeting
        $target_settings = isset($config['targeting']) && is_array($config['targeting']) ? $config['targeting'] : [];
        $devices = isset($target_settings['devices']) ? $target_settings['devices'] : 'all';
        $is_mobile = wp_is_mobile();

        if ($devices === 'desktop' && $is_mobile) {
            return false;
        }
        if ($devices === 'mobile' && !$is_mobile) {
            return false;
        }

        // 3. User Authentication Targeting
        $users = isset($target_settings['users']) ? $target_settings['users'] : 'all';
        if ($users === 'logged_in' && !is_user_logged_in()) {
            return false;
        }
        if ($users === 'logged_out' && is_user_logged_in()) {
            return false;
        }

        return true;
    }
}
