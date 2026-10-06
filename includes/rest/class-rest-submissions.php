<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Rest_Submissions {
    public function register_routes() {
        register_rest_route('wppoppop/v1', '/submit', [
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => [$this, 'handle_submission'],
            'permission_callback' => '__return_true',
        ]);

        register_rest_route('wppoppop/v1', '/impression', [
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => [$this, 'handle_impression'],
            'permission_callback' => '__return_true',
        ]);
    }

    public function handle_impression(WP_REST_Request $request) {
        $params = $request->get_json_params() ?: $request->get_body_params();
        $uid    = isset($params['uid']) ? sanitize_key($params['uid']) : '';

        if (!empty($uid)) {
            global $wpdb;
            $table = $wpdb->prefix . 'wppoppop_items';
            $wpdb->query($wpdb->prepare("UPDATE {$table} SET impressions = impressions + 1 WHERE uid = %s", $uid));
        }

        $res = new WP_REST_Response(['success' => true], 200);
        $res->header('Access-Control-Allow-Origin', '*');
        return $res;
    }

    public function handle_submission(WP_REST_Request $request) {
        $params = $request->get_json_params() ?: $request->get_body_params();

        // 1. Honeypot Bot Trap Validation
        if (!empty($params['_wppoppop_hp_email'])) {
            return new WP_Error('spam_detected', 'Spam detected by security shield.', ['status' => 400]);
        }

        $uid   = isset($params['uid']) ? sanitize_key($params['uid']) : '';
        $email = isset($params['email']) ? sanitize_email($params['email']) : '';
        $fields = isset($params['fields']) ? (array)$params['fields'] : [];
        $visitor_country = isset($params['country']) ? sanitize_text_field($params['country']) : '';
        $quiz_score = isset($params['quiz_score']) ? intval($params['quiz_score']) : null;

        if (!is_email($email)) {
            return new WP_Error('invalid_email', 'Please enter a valid email address.', ['status' => 422]);
        }

        // 2. Disposable Email Filter
        $disposable_domains = ['mailinator.com', '10minutemail.com', 'guerrillamail.com', 'trashmail.com', 'tempmail.com', 'sharklasers.com'];
        $domain = substr(strrchr($email, '@'), 1);
        if (in_array(strtolower($domain), $disposable_domains, true)) {
            return new WP_Error('disposable_email', 'Disposable and temporary email addresses are not allowed.', ['status' => 422]);
        }

        global $wpdb;
        $items_table = $wpdb->prefix . 'wppoppop_items';
        $subs_table  = $wpdb->prefix . 'wppoppop_submissions';

        $row = $wpdb->get_row($wpdb->prepare("SELECT title, data FROM {$items_table} WHERE uid = %s", $uid), ARRAY_A);
        if (!$row) {
            return new WP_Error('popup_not_found', 'Associated popup configuration was not found.', ['status' => 404]);
        }

        $config = json_decode($row['data'], true) ?: [];
        $notif  = $config['notifications'] ?? [];
        $is_double_optin = !empty($notif['enable_double_optin']);

        $initial_status = $is_double_optin ? 'pending' : 'confirmed';
        $confirm_token  = $is_double_optin ? wp_generate_password(32, false) : '';

        // Increment submissions counter
        $wpdb->query($wpdb->prepare("UPDATE {$items_table} SET submissions = submissions + 1 WHERE uid = %s", $uid));
        if (!$is_double_optin) {
            $wpdb->query($wpdb->prepare("UPDATE {$items_table} SET confirmations = confirmations + 1 WHERE uid = %s", $uid));
        }

        if ($quiz_score !== null) {
            $fields['quiz_score'] = $quiz_score;
        }

        // Insert lead record
        $wpdb->insert(
            $subs_table,
            [
                'popup_uid'     => $uid,
                'email'         => $email,
                'fields_data'   => wp_json_encode($fields),
                'status'        => $initial_status,
                'confirm_token' => $confirm_token,
                'country_code'  => $visitor_country,
            ],
            ['%s', '%s', '%s', '%s', '%s', '%s']
        );
        $submission_id = $wpdb->insert_id;

        // Autoresponder dispatch
        $autoresponder = $config['autoresponder'] ?? [];
        if (!empty($autoresponder['enable_user_email']) && !$is_double_optin) {
            $ar_subject = !empty($autoresponder['subject']) ? sanitize_text_field($autoresponder['subject']) : 'Welcome! Here is your reward';
            $ar_body    = !empty($autoresponder['message']) ? $autoresponder['message'] : "Thank you for subscribing!

Enjoy your offer.";

            $ar_body = str_replace('{email}', $email, $ar_body);
            foreach ($fields as $k => $v) {
                $val_str = is_array($v) ? implode(', ', $v) : $v;
                $ar_body = str_replace('{' . $k . '}', $val_str, $ar_body);
            }
            wp_mail($email, $ar_subject, nl2br(esc_html($ar_body)), ['Content-Type: text/html; charset=UTF-8']);
        }

        // Outgoing Webhook Dispatch
        $marketing = $config['marketing'] ?? [];
        if (!empty($marketing['webhook_url'])) {
            $webhook_payload = [
                'event'         => 'submission',
                'popup_uid'     => $uid,
                'submission_id' => $submission_id,
                'email'         => $email,
                'fields'        => $fields,
                'country'       => $visitor_country,
                'timestamp'     => current_time('mysql'),
            ];
            $body_encoded = wp_json_encode($webhook_payload);
            $headers = ['Content-Type' => 'application/json'];

            if (!empty($marketing['webhook_secret'])) {
                $sig = hash_hmac('sha256', $body_encoded, $marketing['webhook_secret']);
                $headers['X-WpPopPop-Signature'] = $sig;
            }

            wp_remote_post(esc_url_raw($marketing['webhook_url']), [
                'headers'  => $headers,
                'body'     => $body_encoded,
                'timeout'  => 10,
                'blocking' => false,
            ]);
        }

        $actions = $config['actions'] ?? [];
        $success_message = $is_double_optin
            ? 'Thank you! A verification link has been dispatched to your email address.'
            : (!empty($actions['success_message']) ? esc_html($actions['success_message']) : 'Thank you! Your information has been registered.');

        $res = new WP_REST_Response([
            'success'      => true,
            'message'      => $success_message,
            'redirect_url' => (!empty($actions['redirect_url']) && !$is_double_optin) ? esc_url_raw($actions['redirect_url']) : '',
        ], 200);

        $res->header('Access-Control-Allow-Origin', '*');
        return $res;
    }
}
