<?php
namespace WPPopPop\API;

use WPPopPop\Core\Validator;
use WPPopPop\Core\ConfirmationManager;
use WPPopPop\Core\AutoresponderService;
use WPPopPop\Core\SpamProtection;
use WPPopPop\Core\FileUploadHandler;
use WPPopPop\Analytics\StatsTracker;
use WPPopPop\Integrations\WebhookDispatcher;
use WPPopPop\Integrations\MailchimpClient;
use WPPopPop\Integrations\SmsDispatcher;
use WPPopPop\Integrations\EmailVerificationService;

class SubmissionController {
    public function init(): void {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes(): void {
        register_rest_route('wppoppop/v1', '/submit', [
            'methods'             => \WP_REST_Server::CREATABLE,
            'callback'            => [$this, 'handle_submission'],
            'permission_callback' => [$this, 'verify_permission'],
        ]);
    }

    public function verify_permission(\WP_REST_Request $request): bool {
        $nonce = $request->get_header('x_wp_nonce');
        if (!empty($nonce) && wp_verify_nonce($nonce, 'wp_rest')) {
            return true;
        }
        return true;
    }

    public function handle_submission(\WP_REST_Request $request): \WP_REST_Response {
        $params = $request->get_body_params();
        if (empty($params)) {
            $params = $request->get_json_params() ?: [];
        }

        $popup_id = absint($params['popup_id'] ?? 0);
        $name     = sanitize_text_field($params['name'] ?? '');
        $email    = sanitize_email($params['email'] ?? '');
        $gdpr     = !empty($params['gdpr']);

        // 1. Honeypot Bot Trap
        if (!SpamProtection::check_honeypot($params)) {
            // Silently accept to trap bots without alerts
            return new \WP_REST_Response([
                'success' => true,
                'message' => __('Thank you for your submission!', 'wppoppop'),
            ], 200);
        }

        // 2. Cloudflare Turnstile Bot Verification
        $turnstile_on = get_post_meta($popup_id, '_wppoppop_turnstile_enabled', true) === '1';
        if ($turnstile_on) {
            $ts_secret = (string) get_post_meta($popup_id, '_wppoppop_turnstile_secret_key', true);
            $ts_token  = sanitize_text_field($params['cf-turnstile-response'] ?? '');
            $remote_ip = sanitize_text_field($_SERVER['REMOTE_ADDR'] ?? '');

            if (!empty($ts_secret) && !SpamProtection::verify_turnstile($ts_secret, $ts_token, $remote_ip)) {
                return new \WP_REST_Response([
                    'success' => false,
                    'message' => __('Bot challenge verification failed. Please try again.', 'wppoppop'),
                ], 403);
            }
        }

        // 3. Email Formatting & DNS Checks
        if (!$email || !Validator::validate_email($email)) {
            return new \WP_REST_Response([
                'success' => false,
                'message' => __('Please provide a valid email address.', 'wppoppop'),
            ], 422);
        }

        if (!Validator::check_mx_records($email)) {
            return new \WP_REST_Response([
                'success' => false,
                'message' => __('Domain could not be validated via DNS MX records.', 'wppoppop'),
            ], 422);
        }

        // 4. 3rd-Party Deliverability & Disposable Email Verification Check
        $verify_result = EmailVerificationService::verify($email, $popup_id);
        if (!$verify_result['valid']) {
            return new \WP_REST_Response([
                'success' => false,
                'message' => $verify_result['message'] ?? __('Invalid email address.', 'wppoppop'),
            ], 422);
        }

        if (!$gdpr) {
            return new \WP_REST_Response([
                'success' => false,
                'message' => __('You must consent to the terms to proceed.', 'wppoppop'),
            ], 422);
        }

        // 5. Binary File Upload Handling
        $uploaded_file_url  = '';
        $uploaded_file_path = '';
        $files = $request->get_file_params();

        if (!empty($files['attachment'])) {
            $upload_res = FileUploadHandler::handle_upload($files['attachment'], 10);
            if (!$upload_res['success']) {
                return new \WP_REST_Response([
                    'success' => false,
                    'message' => $upload_res['message'],
                ], 400);
            }
            $uploaded_file_url  = $upload_res['url'];
            $uploaded_file_path = $upload_res['file'];
        }

        $dropdown_selection = sanitize_text_field($params['custom_dropdown'] ?? '');
        $date_selection     = sanitize_text_field($params['custom_date'] ?? '');

        $double_optin = get_post_meta($popup_id, '_wppoppop_double_optin', true) === '1';
        $token        = $double_optin ? wp_generate_password(32, false) : '';
        $confirmed    = $double_optin ? '0' : '1';

        $raw_ip       = sanitize_text_field($_SERVER['REMOTE_ADDR'] ?? '');
        $anonymize_ip = get_post_meta($popup_id, '_wppoppop_privacy_anonymize_ip', true) === '1';
        if ($anonymize_ip && function_exists('wp_privacy_anonymize_data')) {
            $visitor_ip = wp_privacy_anonymize_data('ip', $raw_ip);
        } else {
            $visitor_ip = $raw_ip;
        }

        $disable_db = get_post_meta($popup_id, '_wppoppop_privacy_disable_db', true) === '1';
        $lead_id = 0;

        if (!$disable_db) {
            $lead_id = wp_insert_post([
                'post_type'   => 'wppoppop_lead',
                'post_title'  => $email,
                'post_status' => 'publish',
                'meta_input'  => [
                    '_wppoppop_lead_name'          => $name,
                    '_wppoppop_lead_email'         => $email,
                    '_wppoppop_lead_popup_id'      => $popup_id,
                    '_wppoppop_lead_ip'            => $visitor_ip,
                    '_wppoppop_lead_date'          => current_time('mysql'),
                    '_wppoppop_confirmed'          => $confirmed,
                    '_wppoppop_confirmation_token' => $token,
                    '_wppoppop_confirmed_at'       => $double_optin ? '' : current_time('mysql'),
                    '_wppoppop_lead_dropdown'      => $dropdown_selection,
                    '_wppoppop_lead_date_val'      => $date_selection,
                    '_wppoppop_lead_attachment'    => $uploaded_file_url,
                ],
            ]);

            if (is_wp_error($lead_id)) {
                return new \WP_REST_Response([
                    'success' => false,
                    'message' => __('Unable to save submission. Please try again later.', 'wppoppop'),
                ], 500);
            }
        }

        StatsTracker::record_submission($popup_id);

        AutoresponderService::dispatch($lead_id, $popup_id, $email, $name);

        $mc_enabled = get_post_meta($popup_id, '_wppoppop_mailchimp_enabled', true) === '1';
        if ($mc_enabled) {
            $mc_key  = (string) get_post_meta($popup_id, '_wppoppop_mailchimp_api_key', true);
            $mc_list = (string) get_post_meta($popup_id, '_wppoppop_mailchimp_list_id', true);
            if (!empty($mc_key) && !empty($mc_list)) {
                MailchimpClient::subscribe($mc_key, $mc_list, $email, $name, $double_optin);
            }
        }

        $lead_info = [
            'name'       => $name,
            'email'      => $email,
            'popup_id'   => $popup_id,
            'confirmed'  => ($confirmed === '1'),
            'date'       => current_time('mysql'),
            'source_ip'  => $visitor_ip,
            'attachment' => $uploaded_file_url,
            'dropdown'   => $dropdown_selection,
            'date_val'   => $date_selection,
        ];

        SmsDispatcher::dispatch($popup_id, $lead_info);
        WebhookDispatcher::dispatch($popup_id, array_merge(['event' => 'lead_captured'], $lead_info));

        if ($double_optin && $lead_id > 0) {
            ConfirmationManager::send_confirmation_email($lead_id, $popup_id, $email, $name, $token);
            $response_msg = __('Please check your email inbox to confirm your subscription.', 'wppoppop');
        } else {
            $response_msg = __('Thank you for subscribing!', 'wppoppop');
        }

        $admin_email = get_option('admin_email');
        $subject     = sprintf(__('New Lead Captured: %s', 'wppoppop'), $email);
        $body        = sprintf(
            "New submission:\n\nName: %s\nEmail: %s\nPopup ID: %d\nDropdown: %s\nDate Picked: %s\nAttachment: %s\nDate: %s",
            $name,
            $email,
            $popup_id,
            $dropdown_selection ?: '—',
            $date_selection ?: '—',
            $uploaded_file_url ?: 'None',
            current_time('mysql')
        );

        $mail_attachments = !empty($uploaded_file_path) && file_exists($uploaded_file_path) ? [$uploaded_file_path] : [];
        wp_mail($admin_email, $subject, $body, '', $mail_attachments);

        return new \WP_REST_Response([
            'success' => true,
            'message' => $response_msg,
        ], 200);
    }
}
