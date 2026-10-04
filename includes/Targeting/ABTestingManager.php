<?php
namespace WPPopPop\Targeting;

class ABTestingManager {
    public function resolve_variant(\WP_Post $popup): \WP_Post {
        $ab_enabled = get_post_meta($popup->ID, '_wppoppop_ab_enabled', true) === '1';
        $variant_id = absint(get_post_meta($popup->ID, '_wppoppop_ab_variant_id', true));

        if (!$ab_enabled || !$variant_id) {
            return $popup;
        }

        $variant_post = get_post($variant_id);
        if (!$variant_post || $variant_post->post_status !== 'publish' || $variant_post->post_type !== PopupPostType::POST_TYPE) {
            return $popup;
        }

        $cookie_name = 'wppoppop_ab_' . $popup->ID;
        $selected_variant = sanitize_text_field($_COOKIE[$cookie_name] ?? '');

        if ($selected_variant !== 'A' && $selected_variant !== 'B') {
            $split_ratio = (int)(get_post_meta($popup->ID, '_wppoppop_ab_split_ratio', true) ?: 50);
            $random_roll = wp_rand(1, 100);
            $selected_variant = ($random_roll <= $split_ratio) ? 'B' : 'A';

            if (!headers_sent()) {
                setcookie($cookie_name, $selected_variant, time() + (30 * DAY_IN_SECONDS), COOKIEPATH ?: '/', COOKIE_DOMAIN, is_ssl(), false);
            }
        }

        return ($selected_variant === 'B') ? $variant_post : $popup;
    }

    public static function get_variant_stats(int $popup_a_id, int $popup_b_id): array {
        $imp_a = (int) get_post_meta($popup_a_id, '_wppoppop_impressions', true);
        $sub_a = (int) get_post_meta($popup_a_id, '_wppoppop_submissions', true);
        $cr_a  = $imp_a > 0 ? round(($sub_a / $imp_a) * 100, 1) : 0;

        $imp_b = (int) get_post_meta($popup_b_id, '_wppoppop_impressions', true);
        $sub_b = (int) get_post_meta($popup_b_id, '_wppoppop_submissions', true);
        $cr_b  = $imp_b > 0 ? round(($sub_b / $imp_b) * 100, 1) : 0;

        $winner = 'tie';
        if ($cr_a > $cr_b && $sub_a > 0) {
            $winner = 'A';
        } elseif ($cr_b > $cr_a && $sub_b > 0) {
            $winner = 'B';
        }

        return [
            'A' => ['impressions' => $imp_a, 'submissions' => $sub_a, 'conversion_rate' => $cr_a],
            'B' => ['impressions' => $imp_b, 'submissions' => $sub_b, 'conversion_rate' => $cr_b],
            'winner' => $winner,
        ];
    }
}
