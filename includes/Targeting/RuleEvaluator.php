<?php
namespace WPPopPop\Targeting;

class RuleEvaluator {
    private ABTestingManager $ab_manager;

    public function __construct() {
        $this->ab_manager = new ABTestingManager();
    }

    public function get_active_popup(): ?\WP_Post {
        if (is_admin() || wp_doing_ajax() || wp_is_json_request()) {
            return null;
        }

        $query = new \WP_Query([
            'post_type'      => PopupPostType::POST_TYPE,
            'post_status'    => 'publish',
            'posts_per_page' => 25,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ]);

        if (!$query->have_posts()) {
            return null;
        }

        foreach ($query->posts as $post) {
            if ($this->matches_targeting($post)) {
                return $this->ab_manager->resolve_variant($post);
            }
        }

        return null;
    }

    private function matches_targeting(\WP_Post $post): bool {
        // User Authentication Targeting
        $user_status = get_post_meta($post->ID, '_wppoppop_user_status', true) ?: 'all';
        if ($user_status === 'logged_in' && !is_user_logged_in()) {
            return false;
        }
        if ($user_status === 'logged_out' && is_user_logged_in()) {
            return false;
        }

        // Standard Page Hierarchy Rules
        $target_rule = get_post_meta($post->ID, '_wppoppop_target_rule', true) ?: 'all';
        $location_matches = true;
        switch ($target_rule) {
            case 'frontpage':
                $location_matches = is_front_page();
                break;
            case 'posts':
                $location_matches = is_single() && get_post_type() === 'post';
                break;
            case 'pages':
                $location_matches = is_page();
                break;
            case 'all':
            default:
                $location_matches = true;
                break;
        }

        if (!$location_matches) {
            return false;
        }

        // Geolocation Access Rule
        if (!GeoManager::matches_geo($post)) {
            return false;
        }

        // WooCommerce Store Targeting Rule
        if (!WooCommerceManager::matches_woocommerce($post)) {
            return false;
        }

        // Campaign Scheduling Check
        if (!ScheduleManager::matches_schedule($post)) {
            return false;
        }

        // Query Parameter Targeting
        $param_key = trim((string) get_post_meta($post->ID, '_wppoppop_url_param_key', true));
        $param_val = trim((string) get_post_meta($post->ID, '_wppoppop_url_param_val', true));
        if (!empty($param_key)) {
            if (!isset($_GET[$param_key])) {
                return false;
            }
            if (!empty($param_val) && sanitize_text_field(wp_unslash($_GET[$param_key])) !== $param_val) {
                return false;
            }
        }

        return true;
    }
}
