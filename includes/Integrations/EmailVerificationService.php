<?php
namespace WPPopPop\Integrations;

class EmailVerificationService {
    public static function verify(string $email, int $popup_id): array {
        $verify_enabled = get_post_meta($popup_id, '_wppoppop_email_verify_enabled', true) === '1';
        if (!$verify_enabled) {
            return ['valid' => true];
        }

        $api_key = trim((string) get_post_meta($popup_id, '_wppoppop_email_verify_api_key', true));
        if (empty($api_key)) {
            return ['valid' => true]; // Bypass if API key not supplied
        }

        // Kickbox Verification API Endpoint
        $endpoint = add_query_arg([
            'email'   => urlencode($email),
            'apikey'  => $api_key,
            'timeout' => 4000,
        ], 'https://api.kickbox.com/v2/verify');

        $response = wp_remote_get($endpoint, ['timeout' => 5]);
        if (is_wp_error($response)) {
            return ['valid' => true]; // Fail open to avoid blocking legitimate visitors on network timeouts
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);
        if (!empty($body['result'])) {
            if ($body['result'] === 'undeliverable') {
                return [
                    'valid'   => false,
                    'message' => __('This email address appears to be invalid or undeliverable.', 'wppoppop'),
                ];
            }
            if (!empty($body['disposable']) && $body['disposable'] === true) {
                return [
                    'valid'   => false,
                    'message' => __('Disposable email addresses are not permitted.', 'wppoppop'),
                ];
            }
        }

        return ['valid' => true];
    }
}
