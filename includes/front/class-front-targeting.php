<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Front_Targeting {
    public function detect_visitor_country() {
        if (!empty($_SERVER['HTTP_CF_IPCOUNTRY'])) {
            return strtoupper(sanitize_text_field($_SERVER['HTTP_CF_IPCOUNTRY']));
        }
        if (!empty($_SERVER['HTTP_X_COUNTRY_CODE'])) {
            return strtoupper(sanitize_text_field($_SERVER['HTTP_X_COUNTRY_CODE']));
        }
        return '';
    }

    public function matches_targeting(array $config) {
        $targeting = $config['targeting'] ?? [];
        $scope     = $targeting['scope'] ?? 'everywhere';

        // 1. Device Viewport
        $device    = $targeting['devices'] ?? 'all';
        $is_mobile = wp_is_mobile();
        if ($device === 'desktop' && $is_mobile) {
            return false;
        }
        if ($device === 'mobile' && !$is_mobile) {
            return false;
        }

        // 2. Geolocation Filter
        $geo_mode = $targeting['geo_mode'] ?? 'all';
        if ($geo_mode !== 'all' && !empty($targeting['geo_countries'])) {
            $countries       = array_map('trim', explode(',', strtoupper($targeting['geo_countries'])));
            $visitor_country = $this->detect_visitor_country();
            if ($geo_mode === 'whitelist' && (!in_array($visitor_country, $countries, true))) {
                return false;
            }
            if ($geo_mode === 'blacklist' && in_array($visitor_country, $countries, true)) {
                return false;
            }
        }

        // 3. User Authentication & Roles
        $auth_mode    = $targeting['auth_mode'] ?? 'all';
        $is_logged_in = is_user_logged_in();
        if ($auth_mode === 'guests_only' && $is_logged_in) {
            return false;
        }
        if ($auth_mode === 'logged_in_only') {
            if (!$is_logged_in) {
                return false;
            }
            if (!empty($targeting['target_roles'])) {
                $target_roles = array_map('trim', explode(',', strtolower($targeting['target_roles'])));
                $user         = wp_get_current_user();
                $has_role     = array_intersect($target_roles, (array)$user->roles);
                if (empty($has_role)) {
                    return false;
                }
            }
        }

        // 4. URL Query / UTM Targeting
        if (!empty($targeting['url_param_key'])) {
            $key = sanitize_key($targeting['url_param_key']);
            if (!isset($_GET[$key])) {
                return false;
            }
            if (!empty($targeting['url_param_val']) && sanitize_text_field($_GET[$key]) !== sanitize_text_field($targeting['url_param_val'])) {
                return false;
            }
        }

        // 5. Taxonomy Category Filter
        if (!empty($targeting['category_slugs']) && is_single()) {
            $cats = array_map('trim', explode(',', $targeting['category_slugs']));
            if (!has_category($cats)) {
                return false;
            }
        }

        // 6. Page Scope
        if ($scope === 'everywhere') {
            return true;
        }
        if ($scope === 'posts' && is_single()) {
            return true;
        }
        if ($scope === 'pages' && is_page()) {
            return true;
        }
        if ($scope === 'specific') {
            $allowed_ids = isset($targeting['specific_ids']) ? array_filter(array_map('intval', explode(',', $targeting['specific_ids']))) : [];
            return in_array(get_queried_object_id(), $allowed_ids, true);
        }

        return false;
    }
}
