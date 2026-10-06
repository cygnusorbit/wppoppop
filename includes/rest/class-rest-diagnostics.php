<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Rest_Diagnostics {
    public function register_routes() {
        register_rest_route('wppoppop/v1', '/diagnostic/webhook', [
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => [$this, 'test_webhook_diagnostic'],
            'permission_callback' => [$this, 'check_admin_permissions'],
        ]);

        register_rest_route('wppoppop/v1', '/diagnostic/sms', [
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => [$this, 'test_sms_diagnostic'],
            'permission_callback' => [$this, 'check_admin_permissions'],
        ]);
    }

    public function check_admin_permissions() {
        return current_user_can('manage_options');
    }

    public function test_webhook_diagnostic(WP_REST_Request $request) {
        $params   = $request->get_json_params() ?: $request->get_body_params();
        $url      = isset($params['url']) ? esc_url_raw($params['url']) : '';
        $secret   = isset($params['secret']) ? sanitize_text_field($params['secret']) : '';

        if (empty($url)) {
            return new WP_Error('missing_url', 'Webhook URL is required.', ['status' => 400]);
        }

        $payload = [
            'event'     => 'diagnostic_ping',
            'timestamp' => current_time('mysql'),
            'message'   => 'Ping test dispatched from WpPopPop REST diagnostic tool.',
        ];
        $body = wp_json_encode($payload);
        $headers = ['Content-Type' => 'application/json'];

        if (!empty($secret)) {
            $headers['X-WpPopPop-Signature'] = hash_hmac('sha256', $body, $secret);
        }

        $response = wp_remote_post($url, [
            'headers' => $headers,
            'body'    => $body,
            'timeout' => 8,
        ]);

        if (is_wp_error($response)) {
            return new WP_Error('webhook_error', $response->get_error_message(), ['status' => 502]);
        }

        $code = wp_remote_retrieve_response_code($response);
        return new WP_REST_Response([
            'success'     => ($code >= 200 && $code < 300),
            'status_code' => $code,
            'message'     => 'Webhook responded with HTTP status ' . $code,
        ], 200);
    }

    public function test_sms_diagnostic(WP_REST_Request $request) {
        $params = $request->get_json_params() ?: $request->get_body_params();
        $to     = isset($params['to']) ? sanitize_text_field($params['to']) : '';
        $creds  = [
            'account_sid' => sanitize_text_field($params['account_sid'] ?? ''),
            'auth_token'  => sanitize_text_field($params['auth_token'] ?? ''),
            'from_number' => sanitize_text_field($params['from_number'] ?? ''),
        ];

        if (!class_exists('WpPopPop_Addons')) {
            return new WP_Error('addons_missing', 'Addons module not loaded.', ['status' => 500]);
        }

        $addons = new WpPopPop_Addons();
        $result = $addons->dispatch_twilio_sms($to, 'WpPopPop diagnostic test SMS ping.', $creds);

        if (is_wp_error($result)) {
            return new WP_Error('sms_error', $result->get_error_message(), ['status' => 502]);
        }

        return new WP_REST_Response([
            'success' => true,
            'message' => 'Test SMS dispatched successfully via Twilio.',
        ], 200);
    }
}
