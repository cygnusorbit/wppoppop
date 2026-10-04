<?php
namespace WPPopPop\Targeting;

class ScheduleManager {
    public static function matches_schedule(\WP_Post $post): bool {
        $enabled = get_post_meta($post->ID, '_wppoppop_schedule_enabled', true) === '1';
        if (!$enabled) {
            return true;
        }

        $now_timestamp = current_time('timestamp');

        // 1. Start and End Date Check
        $start_date = get_post_meta($post->ID, '_wppoppop_schedule_start', true);
        if (!empty($start_date)) {
            $start_timestamp = strtotime($start_date);
            if ($start_timestamp && $now_timestamp < $start_timestamp) {
                return false;
            }
        }

        $end_date = get_post_meta($post->ID, '_wppoppop_schedule_end', true);
        if (!empty($end_date)) {
            $end_timestamp = strtotime($end_date);
            if ($end_timestamp && $now_timestamp > $end_timestamp) {
                return false;
            }
        }

        // 2. Day of Week Check (1 = Monday, 7 = Sunday)
        $allowed_days = get_post_meta($post->ID, '_wppoppop_schedule_days', true);
        if (is_array($allowed_days) && !empty($allowed_days)) {
            $current_day = (string) current_time('N');
            if (!in_array($current_day, $allowed_days, true)) {
                return false;
            }
        }

        // 3. Time of Day Window Check (HH:MM format)
        $time_from = get_post_meta($post->ID, '_wppoppop_schedule_time_from', true);
        $time_to   = get_post_meta($post->ID, '_wppoppop_schedule_time_to', true);

        if (!empty($time_from) && !empty($time_to)) {
            $current_time_str = current_time('H:i');
            if ($time_from <= $time_to) {
                if ($current_time_str < $time_from || $current_time_str > $time_to) {
                    return false;
                }
            } else {
                // Overnight window (e.g. 22:00 to 04:00)
                if ($current_time_str < $time_from && $current_time_str > $time_to) {
                    return false;
                }
            }
        }

        return true;
    }
}
