<?php
namespace WPPopPop\API;

use WPPopPop\Targeting\PopupPostType;

class SubmissionHandler {
    public static function init(): void {
        add_action('rest_api_init', [__CLASS__, 'register_rest_routes']);
        add_action('wp_ajax_wppoppop_delete_lead', [__CLASS__, 'ajax_delete_lead']);
        add_action('wp_ajax_wppoppop_bulk_delete_leads', [__CLASS__, 'ajax_bulk_delete_leads']);
        add_action('wp_ajax_wppoppop_get_lead_details', [__CLASS__, 'ajax_get_lead_details']);
    }

    public static function register_rest_routes(): void {
        register_rest_route('wppoppop/v1', '/submit', [
            'methods'             => 'POST',
            'callback'            => [__CLASS__, 'handle_submission'],
            'permission_callback' => '__return_true',
        ]);

        register_rest_route('wppoppop/v1', '/impression', [
            'methods'             => 'POST',
            'callback'            => [__CLASS__, 'handle_impression'],
            'permission_callback' => '__return_true',
        ]);
    }

    public static function handle_submission(\WP_REST_Request $request): \WP_REST_Response {
        $params   = $request->get_json_params() ?: $request->get_params();
                // 0. Anti-Spam Honeypot Verification
        $honeypot = $params['_wppoppop_hp'] ?? ($_POST['_wppoppop_hp'] ?? '');
        if (!empty($honeypot)) {
            // Silently drop bot submission
            return new \WP_REST_Response([
                'success' => true,
                'message' => __('Subscription verified.', 'wppoppop'),
            ], 200);
        }

        $popup_id = absint($params['popup_id'] ?? 0);
        $email    = sanitize_email($params['email'] ?? '');
        $name     = sanitize_text_field($params['name'] ?? '');
        $amount   = sanitize_text_field($params['calculated_total'] ?? ($params['amount'] ?? '0.00'));
        $fields   = isset($params['fields']) && is_array($params['fields']) ? $params['fields'] : [];

        if (!$popup_id || !is_email($email)) {
            return new \WP_REST_Response([
                'success' => false,
                'message' => __('A valid email address and popup ID are required.', 'wppoppop'),
            ], 400);
        }

        // Create lead entry
        $lead_id = wp_insert_post([
            'post_type'   => PopupPostType::LEAD_POST_TYPE,
            'post_title'  => $email,
            'post_status' => 'publish',
        ]);

        if (is_wp_error($lead_id)) {
            return new \WP_REST_Response([
                'success' => false,
                'message' => __('Failed to record submission.', 'wppoppop'),
            ], 500);
        }

        update_post_meta($lead_id, '_wppoppop_lead_popup_id', $popup_id);
        update_post_meta($lead_id, '_wppoppop_lead_email', $email);
        update_post_meta($lead_id, '_wppoppop_lead_name', $name);
        update_post_meta($lead_id, '_wppoppop_amount', (float)$amount > 0 ? number_format((float)$amount, 2, '.', '') : '0.00');
        $is_paid = ((float)$amount > 0);
        $double_optin_enabled = (bool) get_post_meta($popup_id, '_wppoppop_double_optin', true);

        if (!$is_paid && $double_optin_enabled) {
            \WPPopPop\Core\ConfirmationManager::send_confirmation_request($lead_id, $popup_id, $email, $name);
        } else {
            update_post_meta($lead_id, '_wppoppop_status', $is_paid ? 'Completed' : 'Confirmed');
            update_post_meta($lead_id, '_wppoppop_confirmed', 1);
            \WPPopPop\Core\AutoresponderService::send_welcome($lead_id, $popup_id, $email, $name);
            
            // Count instant confirmed
            $daily_stats[$today]['confirmed']++;
        }
        update_post_meta($lead_id, '_wppoppop_lead_fields', json_encode($fields));

        // Increment popup stats
        $submissions = (int) get_post_meta($popup_id, '_wppoppop_submissions', true);
        update_post_meta($popup_id, '_wppoppop_submissions', $submissions + 1);
        update_post_meta($popup_id, '_wppoppop_submissions_count', $submissions + 1);

        // Daily aggregated stats
        $today = gmdate('Y-m-d');
        $daily_stats = get_option('wppoppop_daily_stats', []);
        if (!isset($daily_stats[$today])) {
            $daily_stats[$today] = ['impressions' => 0, 'submissions' => 0, 'confirmed' => 0, 'payments' => 0];
        }
        $daily_stats[$today]['submissions']++;
        if ((float)$amount > 0) {
            $daily_stats[$today]['payments']++;
        }
        update_option('wppoppop_daily_stats', $daily_stats);

        // Dispatch Webhooks and Notifications
        \WPPopPop\Integrations\CRMManager::sync_lead($lead_id, $popup_id, $email, $name, $fields);
        \WPPopPop\Integrations\WebhookDispatcher::dispatch($lead_id, $popup_id, [
            'email'  => $email,
            'name'   => $name,
            'amount' => $amount,
            'status' => (float)$amount > 0 ? 'Completed' : 'Captured'
        ]);

        return new \WP_REST_Response([
            'success' => true,
            'lead_id' => $lead_id,
            'message' => __('Thank you for subscribing!', 'wppoppop'),
        ], 200);
    }

    public static function handle_impression(\WP_REST_Request $request): \WP_REST_Response {
        $params   = $request->get_json_params() ?: $request->get_params();
        $popup_id = absint($params['popup_id'] ?? 0);

        if ($popup_id > 0) {
            $impressions = (int) get_post_meta($popup_id, '_wppoppop_impressions', true);
            update_post_meta($popup_id, '_wppoppop_impressions', $impressions + 1);

            $today = gmdate('Y-m-d');
            $daily_stats = get_option('wppoppop_daily_stats', []);
            if (!isset($daily_stats[$today])) {
                $daily_stats[$today] = ['impressions' => 0, 'submissions' => 0, 'confirmed' => 0, 'payments' => 0];
            }
            $daily_stats[$today]['impressions']++;
            update_option('wppoppop_daily_stats', $daily_stats);
        }

        return new \WP_REST_Response(['success' => true], 200);
    }

    public static function ajax_get_lead_details(): void {
        check_ajax_referer('wppoppop_admin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied.']);
        }

        $lead_id = absint($_GET['lead_id'] ?? 0);
        $lead = get_post($lead_id);
        if (!$lead || $lead->post_type !== PopupPostType::LEAD_POST_TYPE) {
            wp_send_json_error(['message' => 'Lead not found.']);
        }

        $pid = (int) get_post_meta($lead_id, '_wppoppop_lead_popup_id', true);
        wp_send_json_success([
            'id'          => $lead_id,
            'email'       => esc_html($lead->post_title),
            'name'        => esc_html(get_post_meta($lead_id, '_wppoppop_lead_name', true) ?: ''),
            'popup_title' => esc_html($pid ? get_the_title($pid) : 'Direct Entry'),
            'status'      => esc_html(get_post_meta($lead_id, '_wppoppop_status', true) ?: 'Completed'),
            'amount'      => number_format((float) get_post_meta($lead_id, '_wppoppop_amount', true), 2),
            'created'     => get_the_date('Y-m-d H:i:s', $lead),
        ]);
    }

    public static function ajax_delete_lead(): void {
        check_ajax_referer('wppoppop_admin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied.']);
        }
        $lead_id = absint($_POST['lead_id'] ?? 0);
        if ($lead_id && wp_delete_post($lead_id, true)) {
            wp_send_json_success(['message' => __('Record deleted.', 'wppoppop')]);
        }
        wp_send_json_error(['message' => 'Unable to delete record.']);
    }

    public static function ajax_bulk_delete_leads(): void {
        check_ajax_referer('wppoppop_admin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied.']);
        }
        $ids = isset($_POST['lead_ids']) && is_array($_POST['lead_ids']) ? array_map('absint', $_POST['lead_ids']) : [];
        foreach ($ids as $id) {
            wp_delete_post($id, true);
        }
        wp_send_json_success(['message' => sprintf(__('%d record(s) deleted.', 'wppoppop'), count($ids))]);
    }
}
