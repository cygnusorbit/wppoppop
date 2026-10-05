<?php
namespace WPPopPop\Targeting;

class ScheduleManager {
    public static function init(): void {
        // Lifecycle coordinator registration
    }

    public static function is_popup_scheduled(int $popup_id): bool {
        $enabled = get_post_meta($popup_id, '_wppoppop_schedule_enabled', true) === '1';
        if (!$enabled) {
            return true;
        }

        $now = current_datetime(); // Uses WordPress site timezone

        // 1. Start and End Date-Time Flighting
        $start_date = get_post_meta($popup_id, '_wppoppop_schedule_start', true);
        $end_date   = get_post_meta($popup_id, '_wppoppop_schedule_end', true);

        if (!empty($start_date)) {
            $start_dt = \DateTimeImmutable::createFromFormat('Y-m-d\TH:i', $start_date, wp_timezone());
            if ($start_dt && $now < $start_dt) {
                return false;
            }
        }

        if (!empty($end_date)) {
            $end_dt = \DateTimeImmutable::createFromFormat('Y-m-d\TH:i', $end_date, wp_timezone());
            if ($end_dt && $now > $end_dt) {
                return false;
            }
        }

        // 2. Day-of-Week Recurrence Filter (1 = Monday, 7 = Sunday)
        $allowed_days = get_post_meta($popup_id, '_wppoppop_schedule_days', true);
        if (is_array($allowed_days) && !empty($allowed_days)) {
            $current_day = (int) $now->format('N');
            if (!in_array($current_day, array_map('intval', $allowed_days), true)) {
                return false;
            }
        }

        // 3. Time-of-Day Window (HH:MM format)
        $time_start = get_post_meta($popup_id, '_wppoppop_schedule_time_start', true);
        $time_end   = get_post_meta($popup_id, '_wppoppop_schedule_time_end', true);

        if (!empty($time_start) && !empty($time_end)) {
            $current_time = $now->format('H:i');
            if ($time_start <= $time_end) {
                if ($current_time < $time_start || $current_time > $time_end) {
                    return false;
                }
            } else {
                // Overnight window (e.g., 22:00 to 04:00)
                if ($current_time < $time_start && $current_time > $time_end) {
                    return false;
                }
            }
        }

        return true;
    }

    public static function is_device_allowed(int $popup_id): bool {
        $device_target = get_post_meta($popup_id, '_wppoppop_device_target', true) ?: 'all';
        if ($device_target === 'all') {
            return true;
        }

        $is_mobile = wp_is_mobile();

        if ($device_target === 'desktop_only' && $is_mobile) {
            return false;
        }

        if ($device_target === 'mobile_only' && !$is_mobile) {
            return false;
        }

        return true;
    }
}
