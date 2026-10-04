<?php
namespace WPPopPop\Admin;

use WPPopPop\Targeting\PopupPostType;

class StatsManager {
    public static function init(): void {
        // Lifecycle hook registration for timeline metric queries
        add_action('wp_ajax_wppoppop_get_timeline_stats', [__CLASS__, 'ajax_get_timeline_stats']);
    }

    public static function ajax_get_timeline_stats(): void {
        check_ajax_referer('wppoppop_admin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied.']);
        }

        $popup_id   = absint($_GET['popup_id'] ?? 0);
        $start_date = sanitize_text_field($_GET['start_date'] ?? gmdate('Y-m-01'));
        $end_date   = sanitize_text_field($_GET['end_date'] ?? gmdate('Y-m-t'));

        $timeline = self::get_timeline_data($popup_id, $start_date, $end_date);
        wp_send_json_success(['timeline' => $timeline]);
    }

    public static function get_timeline_data(int $popup_id, string $start_date, string $end_date): array {
        $start_ts = strtotime($start_date ?: gmdate('Y-m-01'));
        $end_ts   = strtotime($end_date ?: gmdate('Y-m-t'));

        if ($end_ts < $start_ts) {
            $end_ts = $start_ts;
        }

        if (($end_ts - $start_ts) > (60 * 86400)) {
            $start_ts = $end_ts - (30 * 86400);
        }

        $days = [];
        for ($cur = $start_ts; $cur <= $end_ts; $cur = strtotime('+1 day', $cur)) {
            $date_str = gmdate('Y-m-d', $cur);
            $days[$date_str] = [
                'date'        => $date_str,
                'impressions' => 0,
                'submits'     => 0,
                'confirmed'   => 0,
                'payments'    => 0,
            ];
        }

        $daily_stats = get_option('wppoppop_daily_stats', []);
        foreach ($days as $date_str => &$day_data) {
            if (isset($daily_stats[$date_str])) {
                $day_data['impressions'] += (int) ($daily_stats[$date_str]['impressions'] ?? 0);
            }
        }

        $args = [
            'post_type'      => PopupPostType::LEAD_POST_TYPE,
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'date_query'     => [
                [
                    'after'     => gmdate('Y-m-d 00:00:00', $start_ts),
                    'before'    => gmdate('Y-m-d 23:59:59', $end_ts),
                    'inclusive' => true,
                ],
            ],
        ];

        if ($popup_id > 0) {
            $args['meta_key']   = '_wppoppop_lead_popup_id';
            $args['meta_value'] = $popup_id;
        }

        $leads = get_posts($args);

        foreach ($leads as $l) {
            $lead_day = get_the_date('Y-m-d', $l);
            if (isset($days[$lead_day])) {
                $days[$lead_day]['submits']++;
                $status = get_post_meta($l->ID, '_wppoppop_status', true);
                if ($status === 'Confirmed' || $status === 'Completed' || $status === 'Paid') {
                    $days[$lead_day]['confirmed']++;
                }
                $amt = (float) get_post_meta($l->ID, '_wppoppop_amount', true);
                if ($amt > 0) {
                    $days[$lead_day]['payments']++;
                }
            }
        }

        return $days;
    }

    public static function build_svg_points(array $timeline, string $metric, int $width = 940, int $height = 200, int $baseline_y = 200): string {
        $count = count($timeline);
        if ($count === 0) {
            return "40,$baseline_y 980,$baseline_y";
        }

        $max_val = 0;
        foreach ($timeline as $day) {
            if ($day[$metric] > $max_val) {
                $max_val = $day[$metric];
            }
        }

        $step_x = $count > 1 ? ($width / ($count - 1)) : $width;
        $points = [];
        $i = 0;

        foreach ($timeline as $day) {
            $x = round(40 + ($i * $step_x));
            if ($max_val > 0) {
                $y = round($baseline_y - (($day[$metric] / $max_val) * ($height - 40)));
            } else {
                $y = $baseline_y;
            }
            $points[] = "{$x},{$y}";
            $i++;
        }

        return implode(' ', $points);
    }
}
