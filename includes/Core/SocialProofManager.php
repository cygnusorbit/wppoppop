<?php
namespace WPPopPop\Core;

use WPPopPop\Targeting\PopupPostType;
use WPPopPop\Admin\SettingsManager;

class SocialProofManager {
    public static function init(): void {
        add_action('rest_api_init', [__CLASS__, 'register_routes']);
        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_assets']);
        add_action('wp_footer', [__CLASS__, 'render_toast_container']);
    }

    public static function register_routes(): void {
        register_rest_route('wppoppop/v1', '/social-proof-feed', [
            'methods'             => 'GET',
            'callback'            => [__CLASS__, 'get_social_proof_feed'],
            'permission_callback' => '__return_true',
        ]);
    }

    public static function enqueue_assets(): void {
        if (is_admin() || !SettingsManager::get('social_proof_enabled', 1)) {
            return;
        }

        wp_enqueue_style(
            'wppoppop-social-proof',
            WPPOPPOP_URL . 'assets/css/social-proof.css',
            [],
            WPPOPPOP_VERSION
        );

        wp_enqueue_script(
            'wppoppop-social-proof',
            WPPOPPOP_URL . 'assets/js/social-proof.js',
            [],
            WPPOPPOP_VERSION,
            true
        );

        wp_localize_script('wppoppop-social-proof', 'WPPopPopSocialProofConfig', [
            'feedUrl'  => rest_url('wppoppop/v1/social-proof-feed'),
            'interval' => max(4, absint(SettingsManager::get('social_proof_interval', 10))),
            'duration' => max(2, absint(SettingsManager::get('social_proof_duration', 5))),
            'position' => SettingsManager::get('social_proof_position', 'bottom-left'),
        ]);
    }

    public static function render_toast_container(): void {
        if (is_admin() || !SettingsManager::get('social_proof_enabled', 1)) {
            return;
        }

        $pos = sanitize_html_class(SettingsManager::get('social_proof_position', 'bottom-left'));
        echo '<div id="wppoppop-social-proof-container" class="wppoppop-sp-pos-' . $pos . '"></div>';
    }

    public static function get_social_proof_feed(): \WP_REST_Response {
        $feed = [];

        // 1. Query real leads from database
        $leads = get_posts([
            'post_type'      => PopupPostType::LEAD_POST_TYPE,
            'post_status'    => 'publish',
            'posts_per_page' => 15,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ]);

        foreach ($leads as$l) {
            $raw_name = get_post_meta($l->ID, '_wppoppop_lead_name', true);
            $popup_id = (int) get_post_meta($l->ID, '_wppoppop_lead_popup_id', true);
            $amount   = (float) get_post_meta($l->ID, '_wppoppop_amount', true);

            // Anonymize name: "Firstname L."
            $name_parts = explode(' ', trim($raw_name ?: 'Subscriber'));
            $first_name =$name_parts[0];
            $last_initial = isset($name_parts[1]) ? strtoupper(substr($name_parts[1], 0, 1)) . '.' : '';$display_name = trim($first_name . ' ' .$last_initial);

            $action = ($amount > 0)
                ? sprintf(__('purchased for $\%s', 'wppoppop'), number_format($amount, 2))
                : __('claimed a discount voucher', 'wppoppop');

            $time_diff = human_time_diff(get_the_time('U',$l), current_time('timestamp')) . ' ' . __('ago', 'wppoppop');

            $feed[] = [
                'name'      => esc_html($display_name),
                'action'    => esc_html($action),
                'time_ago'  => esc_html($time_diff),
                'popup_id'  => $popup_id,
                'avatar'    => '👤',
            ];
        }

        // 2. Blend with fallback/synthetic activities if feed count is low
        if (count($feed) < 6) {$fallbacks_raw = SettingsManager::get('social_proof_fallbacks', '');
            if (!empty($fallbacks_raw)) {
                $lines = explode("\n", str_replace("\r", "", trim($fallbacks_raw)));
                foreach ($lines as$line) {
                    $parts = explode('\vert{}', trim($line));
                    $fb_name   = trim($parts[0] ?? '');
                    $fb_action = trim($parts[1] ?? __('joined the newsletter', 'wppoppop'));
                    $fb_pid    = absint($parts[2] ?? 0);

                    if (!empty($fb_name)) {$feed[] = [
                            'name'     => esc_html($fb_name),
                            'action'   => esc_html($fb_action),
                            'time_ago' => esc_html(wp_rand(2, 45) . ' ' . __('minutes ago', 'wppoppop')),
                            'popup_id' => $fb_pid,
                            'avatar'   => '⭐',
                        ];
                    }
                }
            }
        }

        return new \WP_REST_Response([
            'success' => true,
            'items'   => $feed,
        ], 200);
    }
}
