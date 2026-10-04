<?php
namespace WPPopPop\Analytics;

class StatsTracker {
    public function init(): void {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes(): void {
        register_rest_route('wppoppop/v1', '/impression', [
            'methods'             => \WP_REST_Server::CREATABLE,
            'callback'            => [$this, 'handle_impression'],
            'permission_callback' => '__return_true',
        ]);
    }

    public function handle_impression(\WP_REST_Request $request): \WP_REST_Response {
        $params   = $request->get_json_params() ?: $request->get_body_params();
        $popup_id = absint($params['popup_id'] ?? 0);

        if ($popup_id > 0) {
            $impressions = (int) get_post_meta($popup_id, '_wppoppop_impressions', true);
            update_post_meta($popup_id, '_wppoppop_impressions', $impressions + 1);
        }

        return new \WP_REST_Response(['success' => true], 200);
    }

    public static function record_submission(int $popup_id): void {
        if ($popup_id > 0) {
            $submissions = (int) get_post_meta($popup_id, '_wppoppop_submissions', true);
            update_post_meta($popup_id, '_wppoppop_submissions', $submissions + 1);
        }
    }
}
