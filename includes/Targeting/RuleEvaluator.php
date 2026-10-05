<?php
namespace WPPopPop\Targeting;

class RuleEvaluator {
    public function get_active_popup(): ?\WP_Post {
        $popups = get_posts([
            'post_type'      => PopupPostType::POST_TYPE,
            'post_status'    => 'publish',
            'posts_per_page' => 20,
            'orderby'        => 'date',
            'order'          => 'DESC'
        ]);

        foreach ($popups as $p) {
            // Check User Authentication Status
            $user_status = get_post_meta($p->ID, '_wppoppop_user_status', true) ?: 'all';
            if ($user_status === 'logged_in' && !is_user_logged_in()) {
                continue;
            }
            if ($user_status === 'logged_out' && is_user_logged_in()) {
                continue;
            }

            // Check Display Location Rule
            $location_rule = get_post_meta($p->ID, '_wppoppop_target_rule', true) ?: 'all';
            if ($location_rule === 'frontpage' && !is_front_page()) {
                continue;
            }
            if ($location_rule === 'posts' && !is_single()) {
                continue;
            }
            if ($location_rule === 'pages' && !is_page()) {
                continue;
            }

            // Check GeoIP Country Filter Gate
            if (class_exists(GeoIPManager::class) && !GeoIPManager::is_country_allowed($p->ID)) {
                continue;
            }

            // Check Date-Time Flighting & Recurrence
            if (class_exists(ScheduleManager::class) && !ScheduleManager::is_popup_scheduled($p->ID)) {
                continue;
            }

            // Check Device Target (Desktop vs Mobile)
            if (class_exists(ScheduleManager::class) && !ScheduleManager::is_device_allowed($p->ID)) {
                continue;
            }

            return $p;
        }

        return !empty($popups) ? $popups[0] : null;
    }
}
