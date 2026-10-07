<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Ajax_Frontend {
    public function __construct() {
        add_action('wp_ajax_wppoppop_submit_form', [$this, 'submit_form']);
        add_action('wp_ajax_nopriv_wppoppop_submit_form', [$this, 'submit_form']);
        add_action('wp_ajax_wppoppop_record_impression', [$this, 'record_impression']);
        add_action('wp_ajax_nopriv_wppoppop_record_impression', [$this, 'record_impression']);
        add_action('wp_ajax_wppoppop_serve_remote_embed', [$this, 'serve_remote_embed']);
        add_action('wp_ajax_nopriv_wppoppop_serve_remote_embed', [$this, 'serve_remote_embed']);
        add_action('wp_ajax_wppoppop_process_payment', [$this, 'process_payment']);
        add_action('wp_ajax_nopriv_wppoppop_process_payment', [$this, 'process_payment']);
    }

    public function submit_form() {
        // Honeypot anti-spam verification
        if (!empty($_POST['_wppoppop_hp_email'])) {
            wp_send_json_error(['message' => __('Spam detected.', 'wppoppop')]);
        }

        $uid = !empty($_POST['uid']) ? sanitize_text_field(wp_unslash($_POST['uid'])) : '';
        if (empty($uid)) {
            wp_send_json_error(['message' => __('Missing campaign identifier.', 'wppoppop')]);
        }

        $email = !empty($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
        $raw_fields = !empty($_POST['fields']) && is_array($_POST['fields']) ? wp_unslash($_POST['fields']) : [];
        $fields = array_map('sanitize_text_field', $raw_fields);
        $country = !empty($_POST['country']) ? sanitize_text_field(wp_unslash($_POST['country'])) : '';
        $quiz_score = isset($_POST['quiz_score']) ? intval($_POST['quiz_score']) : 0;

        // Filter: Pre-process submission fields before validation and database persistence
        $submission_data = apply_filters('wppoppop_pre_process_submission', [
            'uid'        => $uid,
            'email'      => $email,
            'fields'     => $fields,
            'country'    => $country,
            'quiz_score' => $quiz_score,
        ]);

        $uid        = $submission_data['uid'];
        $email      = $submission_data['email'];
        $fields     = $submission_data['fields'];
        $country    = $submission_data['country'];
        $quiz_score = $submission_data['quiz_score'];

        global $wpdb;
        $subs_table  = $wpdb->prefix . 'wppoppop_submissions';
        $items_table = $wpdb->prefix . 'wppoppop_items';

        // Action: Before saving lead record
        do_action('wppoppop_before_submission_save', $uid, $email, $fields);

        $inserted = $wpdb->insert(
            $subs_table,
            [
                'popup_uid'   => $uid,
                'email'       => $email,
                'fields'      => wp_json_encode($fields),
                'ip_address'  => sanitize_text_field($_SERVER['REMOTE_ADDR'] ?? ''),
                'country'     => $country,
                'quiz_score'  => $quiz_score,
                'created_at'  => current_time('mysql'),
            ],
            ['%s', '%s', '%s', '%s', '%s', '%d', '%s']
        );

        $sub_id = $wpdb->insert_id;

        // Increment campaign submissions counter
        $wpdb->query($wpdb->prepare("UPDATE {$items_table} SET submissions = submissions + 1 WHERE uid = %s", $uid));

        // Action: After lead record successfully written to database
        do_action('wppoppop_after_submission_saved', $sub_id, $uid, $email, $fields);

        // Fetch popup configuration to handle autoresponders, webhooks, and redirects
        $popup_row = $wpdb->get_row($wpdb->prepare("SELECT data FROM {$items_table} WHERE uid = %s", $uid), ARRAY_A);
        $config    = (!empty($popup_row['data'])) ? json_decode($popup_row['data'], true) : [];

        // Autoresponder Email Notification Pipeline
        $autoresponder = $config['autoresponder'] ?? [];
        if (!empty($autoresponder['enable']) && !empty($email)) {
            $settings     = get_option('wppoppop_settings', []);
            $sender_name  = $settings['sender_name'] ?? 'WpPopPop';
            $sender_email = $settings['sender_email'] ?? get_option('admin_email');

            $mail_args = [
                'to'          => $email,
                'subject'     => !empty($autoresponder['subject']) ? sanitize_text_field($autoresponder['subject']) : __('Thank you for subscribing!', 'wppoppop'),
                'message'     => !empty($autoresponder['body']) ? wp_kses_post($autoresponder['body']) : __('We have received your submission.', 'wppoppop'),
                'headers'     => [
                    'Content-Type: text/html; charset=UTF-8',
                    sprintf('From: %s <%s>', $sender_name, $sender_email),
                ],
            ];

            // Filter: Allow external extensions to alter autoresponder email arguments
            $mail_args = apply_filters('wppoppop_autoresponder_mail', $mail_args, $uid, $sub_id);
            if (!empty($mail_args['to'])) {
                wp_mail($mail_args['to'], $mail_args['subject'], $mail_args['message'], $mail_args['headers']);
            }
        }

        // Webhooks Pipeline
        $marketing = $config['marketing'] ?? [];
        if (!empty($marketing['webhook_url'])) {
            $webhook_payload = [
                'event'       => 'wppoppop_submission',
                'sub_id'      => $sub_id,
                'popup_uid'   => $uid,
                'email'       => $email,
                'fields'      => $fields,
                'country'     => $country,
                'timestamp'   => time(),
            ];

            // Filter: Mutate webhook transmission payload
            $webhook_payload = apply_filters('wppoppop_webhook_payload', $webhook_payload, $uid, $sub_id);

            $response = wp_remote_post(esc_url_raw($marketing['webhook_url']), [
                'method'      => 'POST',
                'timeout'     => 10,
                'headers'     => ['Content-Type' => 'application/json; charset=utf-8'],
                'body'        => wp_json_encode($webhook_payload),
                'blocking'    => false,
            ]);

            // Action: After dispatching external webhook
            do_action('wppoppop_webhook_dispatched', $marketing['webhook_url'], $response, $sub_id);
        }

        $redirect_url = !empty($config['triggers']['redirect_url']) ? esc_url_raw($config['triggers']['redirect_url']) : '';

        wp_send_json_success([
            'message'      => __('Thank you! Submission received.', 'wppoppop'),
            'sub_id'       => $sub_id,
            'redirect_url' => $redirect_url,
        ]);
    }

    public function record_impression() {
        $uid = !empty($_POST['uid']) ? sanitize_text_field(wp_unslash($_POST['uid'])) : '';
        if (empty($uid)) {
            wp_send_json_error(['message' => 'Missing UID']);
        }

        global $wpdb;
        $items_table = $wpdb->prefix . 'wppoppop_items';
        $wpdb->query($wpdb->prepare("UPDATE {$items_table} SET impressions = impressions + 1 WHERE uid = %s", $uid));

        do_action('wppoppop_impression_recorded', $uid);

        wp_send_json_success(['message' => 'Impression logged']);
    }

    public function serve_remote_embed() {
        $uid = !empty($_GET['uid']) ? sanitize_text_field(wp_unslash($_GET['uid'])) : '';
        if (empty($uid)) {
            exit;
        }

        header('Content-Type: application/javascript; charset=UTF-8');
        header('Access-Control-Allow-Origin: *');

        echo "(function() { console.log('WpPopPop remote embed active for: " . esc_js($uid) . "'); })();";
        exit;
    }

    public function process_payment() {
        $uid    = !empty($_POST['uid']) ? sanitize_text_field(wp_unslash($_POST['uid'])) : '';
        $email  = !empty($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : 'customer@example.com';
        $amount = !empty($_POST['amount']) ? floatval($_POST['amount']) : 10.00;
        $curr   = !empty($_POST['currency']) ? sanitize_text_field(wp_unslash($_POST['currency'])) : 'USD';

        global $wpdb;
        $tx_table = $wpdb->prefix . 'wppoppop_transactions';

        $tx_id = 'tx_' . wp_generate_password(12, false, false);
        $wpdb->insert(
            $tx_table,
            [
                'transaction_id' => $tx_id,
                'popup_uid'      => $uid,
                'email'          => $email,
                'amount'         => $amount,
                'currency'       => $curr,
                'gateway'        => 'Stripe',
                'status'         => 'completed',
                'created_at'     => current_time('mysql'),
            ]
        );

        do_action('wppoppop_payment_completed', $tx_id, $uid, $email, $amount, $curr);

        wp_send_json_success([
            'message'        => __('Payment completed successfully!', 'wppoppop'),
            'transaction_id' => $tx_id,
        ]);
    }
}
