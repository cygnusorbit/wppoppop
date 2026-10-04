<?php
namespace WPPopPop\Targeting;

class GeoManager {
    public static function get_visitor_country(): string {
        $headers = [
            'HTTP_CF_IPCOUNTRY',
            'HTTP_X_COUNTRY_CODE',
            'HTTP_GEOIP_COUNTRY_CODE',
            'HTTP_X_FORWARDED_COUNTRY',
        ];

        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                return strtoupper(sanitize_text_field(wp_unslash($_SERVER[$header])));
            }
        }

        // Allow simulation parameter for testing in local environments (?wppoppop_sim_geo=US)
        if (current_user_can('manage_options') && !empty($_GET['wppoppop_sim_geo'])) {
            return strtoupper(sanitize_text_field(wp_unslash($_GET['wppoppop_sim_geo'])));
        }

        return 'UNKNOWN';
    }

    public static function matches_geo(\WP_Post $post): bool {
        $mode = get_post_meta($post->ID, '_wppoppop_geo_mode', true) ?: 'all';
        if ($mode === 'all') {
            return true;
        }

        $raw_countries = get_post_meta($post->ID, '_wppoppop_geo_countries', true) ?: '';
        $target_countries = array_filter(array_map('trim', explode(',', strtoupper($raw_countries))));

        if (empty($target_countries)) {
            return true;
        }

        $visitor_country = self::get_visitor_country();
        if ($visitor_country === 'UNKNOWN') {
            return true; // Gracefully permit display if country cannot be determined
        }

        if ($mode === 'allow') {
            return in_array($visitor_country, $target_countries, true);
        }

        if ($mode === 'block') {
            return !in_array($visitor_country, $target_countries, true);
        }

        return true;
    }
}
