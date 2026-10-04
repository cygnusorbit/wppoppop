<?php
namespace WPPopPop\Integrations;

use WPPopPop\Admin\SettingsManager;
use WPPopPop\Targeting\PopupPostType;

class PaymentGatewayManager {
    public static function init(): void {
        add_action('rest_api_init', [__CLASS__, 'register_payment_routes']);
    }

    public static function register_payment_routes(): void {
        register_rest_route('wppoppop/v1', '/create-payment', [
            'methods'             => 'POST',
            'callback'            => [__CLASS__, 'handle_create_payment'],
            'permission_callback' => '__return_true',
        ]);

        register_rest_route('wppoppop/v1', '/confirm-payment', [
            'methods'             => 'POST',
            'callback'            => [__CLASS__, 'handle_confirm_payment'],
            'permission_callback' => '__return_true',
        ]);
    }

    public static function handle_create_payment(\WP_REST_Request $request): \WP_REST_Response {
        $params   = $request->get_json_params() ?: $request->get_params();
        $popup_id = absint($params['popup_id'] ?? 0);
        $amount   = (float) ($params['amount'] ?? 0);
        $gateway  = sanitize_key($params['gateway'] ?? 'stripe');
        $email    = sanitize_email($params['email'] ?? '');
        $name     = sanitize_text_field($params['name'] ?? '');

        if ($amount <= 0 || !$popup_id) {
            return new \WP_REST_Response([
                'success' => false,
                'message' => __('Invalid payment amount or popup ID.', 'wppoppop'),
            ], 400);
        }

        $currency = strtoupper(SettingsManager::get('payment_currency', 'USD'));
        $settings = SettingsManager::get_all();

        // 1. Create a pending lead entry in the transactions database
        $lead_id = wp_insert_post([
            'post_type'   => PopupPostType::LEAD_POST_TYPE,
            'post_title'  => $email ?: 'Guest Payer',
            'post_status' => 'publish',
        ]);

        if (is_wp_error($lead_id)) {
            return new \WP_REST_Response([
                'success' => false,
                'message' => __('Failed to initialize payment record.', 'wppoppop'),
            ], 500);
        }

        update_post_meta($lead_id, '_wppoppop_lead_popup_id', $popup_id);
        update_post_meta($lead_id, '_wppoppop_lead_email', $email);
        update_post_meta($lead_id, '_wppoppop_lead_name', $name);
        update_post_meta($lead_id, '_wppoppop_amount', number_format($amount, 2, '.', ''));
        update_post_meta($lead_id, '_wppoppop_status', 'Pending');
        update_post_meta($lead_id, '_wppoppop_gateway', $gateway);

        // Client intent mock for direct front-end settlement
        $transaction_ref = strtoupper($gateway) . '-' . gmdate('YmdHis') . '-' . wp_rand(1000, 9999);
        update_post_meta($lead_id, '_wppoppop_transaction_id', $transaction_ref);

        return new \WP_REST_Response([
            'success'        => true,
            'lead_id'        => $lead_id,
            'transaction_id' => $transaction_ref,
            'amount'         => number_format($amount, 2, '.', ''),
            'currency'       => $currency,
            'gateway'        => $gateway,
            'public_key'     => $gateway === 'stripe' ? ($settings['stripe_pub_key'] ?? '') : ($settings['paypal_client_id'] ?? ''),
        ], 200);
    }

    public static function handle_confirm_payment(\WP_REST_Request $request): \WP_REST_Response {
        $params   = $request->get_json_params() ?: $request->get_params();
        $lead_id  = absint($params['lead_id'] ?? 0);
        $tx_id    = sanitize_text_field($params['transaction_id'] ?? '');

        if (!$lead_id) {
            return new \WP_REST_Response(['success' => false, 'message' => 'Invalid lead ID.'], 400);
        }

        $lead = get_post($lead_id);
        if (!$lead || $lead->post_type !== PopupPostType::LEAD_POST_TYPE) {
            return new \WP_REST_Response(['success' => false, 'message' => 'Record not found.'], 404);
        }

        // Mark Transaction Completed
        update_post_meta($lead_id, '_wppoppop_status', 'Completed');
        if (!empty($tx_id)) {
            update_post_meta($lead_id, '_wppoppop_transaction_id', $tx_id);
        }

        $popup_id = (int) get_post_meta($lead_id, '_wppoppop_lead_popup_id', true);
        $amount   = (float) get_post_meta($lead_id, '_wppoppop_amount', true);

        // Update popup submission counters
        if ($popup_id > 0) {
            $submits = (int) get_post_meta($popup_id, '_wppoppop_submissions', true);
            update_post_meta($popup_id, '_wppoppop_submissions', $submits + 1);
            update_post_meta($popup_id, '_wppoppop_submissions_count', $submits + 1);
        }

        // Increment daily payments on the stats timeline
        $today = gmdate('Y-m-d');
        $daily_stats = get_option('wppoppop_daily_stats', []);
        if (!isset($daily_stats[$today])) {
            $daily_stats[$today] = ['impressions' => 0, 'submits' => 0, 'confirmed' => 0, 'payments' => 0];
        }
        $daily_stats[$today]['submits']++;
        $daily_stats[$today]['payments']++;
        update_option('wppoppop_daily_stats', $daily_stats);

        // Dispatch Webhook notification
        WebhookDispatcher::dispatch($lead_id, $popup_id, [
            'email'  => get_post_meta($lead_id, '_wppoppop_lead_email', true),
            'name'   => get_post_meta($lead_id, '_wppoppop_lead_name', true),
            'amount' => number_format($amount, 2, '.', ''),
            'status' => 'Completed',
        ]);

        return new \WP_REST_Response([
            'success' => true,
            'message' => __('Payment successfully processed!', 'wppoppop'),
        ], 200);
    }
}
