<?php
namespace WPPopPop\API;

use WPPopPop\Integrations\StripeClient;
use WPPopPop\Targeting\PopupPostType;

class PaymentController {
    public function init(): void {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes(): void {
        register_rest_route('wppoppop/v1', '/create-payment', [
            'methods'             => \WP_REST_Server::CREATABLE,
            'callback'            => [$this, 'handle_payment_intent'],
            'permission_callback' => '__return_true',
        ]);
    }

    public function handle_payment_intent(\WP_REST_Request $request): \WP_REST_Response {
        $params   = $request->get_json_params() ?: $request->get_body_params();
        $popup_id = absint($params['popup_id'] ?? 0);
        $popup    = get_post($popup_id);

        if (!$popup || $popup->post_type !== PopupPostType::POST_TYPE) {
            return new \WP_REST_Response(['success' => false, 'message' => 'Invalid popup target.'], 404);
        }

        $payment_enabled = get_post_meta($popup_id, '_wppoppop_payment_enabled', true) === '1';
        if (!$payment_enabled) {
            return new \WP_REST_Response(['success' => false, 'message' => 'Payments not enabled on this popup.'], 400);
        }

        $secret_key = (string) get_post_meta($popup_id, '_wppoppop_stripe_secret_key', true);
        $currency   = (string)(get_post_meta($popup_id, '_wppoppop_payment_currency', true) ?: 'usd');
        $base_price = (float)(get_post_meta($popup_id, '_wppoppop_payment_amount', true) ?: 19.99);

        // Bind dynamic amount if provided from frontend calculation
        $requested_amount = !empty($params['amount']) ? (float)$params['amount'] : $base_price;
        $email            = sanitize_email($params['email'] ?? '');
        $description      = sprintf('Order via WPPopPop - %s (#%d)', $popup->post_title, $popup_id);

        if (empty($secret_key)) {
            // Mock sandbox payment when no secret key is configured
            self::record_transaction($popup_id, $requested_amount);
            return new \WP_REST_Response([
                'success'       => true,
                'sandbox'       => true,
                'client_secret' => 'mock_secret_' . wp_generate_password(24, false),
                'message'       => 'Test transaction approved (sandbox simulation mode).',
            ], 200);
        }

        $result = StripeClient::create_payment_intent($secret_key, $requested_amount, $currency, $email, $description);
        if ($result['success']) {
            self::record_transaction($popup_id, $requested_amount);
            return new \WP_REST_Response($result, 200);
        }

        return new \WP_REST_Response(['success' => false, 'message' => $result['message']], 400);
    }

    public static function record_transaction(int $popup_id, float $amount): void {
        $count = (int) get_post_meta($popup_id, '_wppoppop_payments_count', true);
        $total = (float) get_post_meta($popup_id, '_wppoppop_revenue_total', true);

        update_post_meta($popup_id, '_wppoppop_payments_count', $count + 1);
        update_post_meta($popup_id, '_wppoppop_revenue_total', $total + $amount);
    }
}
