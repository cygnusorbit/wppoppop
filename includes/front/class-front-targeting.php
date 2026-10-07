<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Front_Targeting {

    public function detect_visitor_country() {
        $country = '';

        if (!empty($_SERVER['HTTP_CF_IPCOUNTRY'])) {
            $country = strtoupper(sanitize_text_field($_SERVER['HTTP_CF_IPCOUNTRY']));
        } elseif (!empty($_SERVER['GEOIP_COUNTRY_CODE'])) {
            $country = strtoupper(sanitize_text_field($_SERVER['GEOIP_COUNTRY_CODE']));
        }

        // Filter: Override or customize visitor country detection (e.g., custom GeoIP DB)
        return apply_filters('wppoppop_visitor_country', $country);
    }

    public function matches_targeting(array $config) {
        $targeting = $config['targeting'] ?? [];

        // 1. Device Viewport Targeting
        if (!empty($targeting['devices']) && $targeting['devices'] !== 'all') {
            $is_mobile = wp_is_mobile();
            if ($targeting['devices'] === 'mobile' && !$is_mobile) {
                return apply_filters('wppoppop_targeting_decision', false, $config, $this);
            }
            if ($targeting['devices'] === 'desktop' && $is_mobile) {
                return apply_filters('wppoppop_targeting_decision', false, $config, $this);
            }
        }

        // 2. User Authentication State
        if (!empty($targeting['user_state'])) {
            if ($targeting['user_state'] === 'logged_in' && !is_user_logged_in()) {
                return apply_filters('wppoppop_targeting_decision', false, $config, $this);
            }
            if ($targeting['user_state'] === 'logged_out' && is_user_logged_in()) {
                return apply_filters('wppoppop_targeting_decision', false, $config, $this);
            }
        }

        // 3. Geolocation Whitelisting / Blacklisting
        $geo_mode      = $targeting['geo_mode'] ?? 'none';
        $target_countries = !empty($targeting['countries']) && is_array($targeting['countries']) ? $targeting['countries'] : [];
        if ($geo_mode !== 'none' && !empty($target_countries)) {
            $visitor_country = $this->detect_visitor_country();
            if ($geo_mode === 'whitelist' && !in_array($visitor_country, $target_countries, true)) {
                return apply_filters('wppoppop_targeting_decision', false, $config, $this);
            }
            if ($geo_mode === 'blacklist' && in_array($visitor_country, $target_countries, true)) {
                return apply_filters('wppoppop_targeting_decision', false, $config, $this);
            }
        }

        // 4. UTM Parameter Targeting
        if (!empty($targeting['utm_source'])) {
            $param = sanitize_text_field($_GET['utm_source'] ?? '');
            if ($param !== $targeting['utm_source']) {
                return apply_filters('wppoppop_targeting_decision', false, $config, $this);
            }
        }

        // Filter: Final programmatic filter for all targeting rules
        return apply_filters('wppoppop_targeting_decision', true, $config, $this);
    }
}
