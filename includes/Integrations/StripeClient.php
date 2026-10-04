<?php
namespace WPPopPop\Integrations;

class StripeClient {
    public static function create_payment_intent(string $secret_key, float $amount, string $currency, string $email, string $description = ''): array {
        $secret_key = trim($secret_key);
        if (empty($secret_key) || $amount <= 0) {
            return ['success' => false, 'message' => 'Invalid Stripe credentials or payment amount.'];
        }

        // Convert decimal amount to smallest currency unit (cents)
        $amount_cents = (int) round($amount * 100);
        $endpoint = 'https://api.stripe.com/v1/payment_intents';

        $body = [
            'amount'               => $amount_cents,
            'currency'             => strtolower(trim($currency)),
            'receipt_email'        => sanitize_email($email),
            'description'          => sanitize_text_field($description),
            'payment_method_types' => ['card'],
        ];

        $response = wp_remote_post($endpoint, [
            'headers' => [
                'Authorization' => 'Bearer ' . $secret_key,
                'Content-Type'  => 'application/x-www-form-urlencoded',
            ],
            'body'    => http_build_query($body),
            'timeout' => 12,
        ]);

        if (is_wp_error($response)) {
            return ['success' => false, 'message' => $response->get_error_message()];
        }

        $code = wp_remote_retrieve_response_code($response);
        $data = json_decode(wp_remote_retrieve_body($response), true);

        if ($code >= 200 && $code < 300 && !empty($data['client_secret'])) {
            return [
                'success'       => true,
                'client_secret' => $data['client_secret'],
                'intent_id'     => $data['id'] ?? '',
            ];
        }

        $error_message = $data['error']['message'] ?? 'Stripe payment initialization failed.';
        return ['success' => false, 'message' => $error_message];
    }
}
