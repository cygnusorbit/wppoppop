<?php
namespace WPPopPop\Core;

use WPPopPop\Targeting\PopupPostType;

class SocialProofManager {
    public static function get_activity_feed(int $popup_id): array {
        $enabled = get_post_meta($popup_id, '_wppoppop_sp_enabled', true) === '1';
        if (!$enabled) {
            return [];
        }

        $feed = [];
        $use_real_leads = get_post_meta($popup_id, '_wppoppop_sp_use_real', true) !== '0';

        // 1. Fetch real recent submissions
        if ($use_real_leads) {
            $leads = get_posts([
                'post_type'      => PopupPostType::LEAD_POST_TYPE,
                'post_status'    => 'publish',
                'posts_per_page' => 6,
                'orderby'        => 'date',
                'order'          => 'DESC',
                'meta_query'     => [
                    [
                        'key'     => '_wppoppop_lead_popup_id',
                        'value'   => $popup_id,
                        'compare' => '=',
                    ],
                ],
            ]);

            foreach ($leads as $lead) {
                $name  = get_post_meta($lead->ID, '_wppoppop_lead_name', true);
                $email = get_post_meta($lead->ID, '_wppoppop_lead_email', true) ?: $lead->post_title;
                
                $display_name = !empty($name) ? self::mask_name($name) : self::mask_email($email);
                $time_ago     = human_time_diff(get_post_time('U', true, $lead), current_time('timestamp')) . ' ' . __('ago', 'wppoppop');

                $feed[] = [
                    'title' => sprintf(__('%s just subscribed!', 'wppoppop'), $display_name),
                    'time'  => $time_ago,
                    'icon'  => '🎉',
                ];
            }
        }

        // 2. Fetch configured fallback entries
        $fallbacks_raw = get_post_meta($popup_id, '_wppoppop_sp_fallbacks', true);
        if (!empty($fallbacks_raw)) {
            $lines = array_filter(array_map('trim', explode("\n", $fallbacks_raw)));
            foreach ($lines as $line) {
                $parts = explode('|', $line);
                $text  = trim($parts[0] ?? '');
                $time  = trim($parts[1] ?? __('Just now', 'wppoppop'));
                $icon  = trim($parts[2] ?? '⚡');

                if (!empty($text)) {
                    $feed[] = [
                        'title' => $text,
                        'time'  => $time,
                        'icon'  => $icon,
                    ];
                }
            }
        }

        return $feed;
    }

    private static function mask_name(string $name): string {
        $parts = explode(' ', trim($name));
        if (count($parts) > 1) {
            return $parts[0] . ' ' . strtoupper(substr($parts[1], 0, 1)) . '.';
        }
        return $parts[0];
    }

    private static function mask_email(string $email): string {
        $parts = explode('@', $email);
        $user  = $parts[0] ?? 'Someone';
        return ucfirst(substr($user, 0, 4)) . '***';
    }
}
