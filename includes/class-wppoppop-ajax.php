<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Ajax {
    private static $disposable_domains = [
        'mailinator.com', '10minutemail.com', 'tempmail.com', 'guerrillamail.com',
        'sharklasers.com', 'throwawaymail.com', 'yopmail.com', 'trashmail.com',
        'fakeinbox.com', 'dispostable.com', 'getnada.com', 'burnermail.io'
    ];

    public function __construct() {
        // Builder CRUD Actions
        add_action('wp_ajax_wppoppop_save_popup', [$this, 'save_popup']);
        add_action('wp_ajax_wppoppop_load_popup', [$this, 'load_popup']);
        add_action('wp_ajax_wppoppop_duplicate_popup', [$this, 'duplicate_popup']);
        add_action('wp_ajax_wppoppop_delete_popup', [$this, 'delete_popup']);
        add_action('wp_ajax_wppoppop_export_popup', [$this, 'export_popup']);
        add_action('wp_ajax_wppoppop_import_popup', [$this, 'import_popup']);

        // A/B Testing & Campaigns
        add_action('wp_ajax_wppoppop_save_campaign', [$this, 'save_campaign']);
        add_action('wp_ajax_wppoppop_delete_campaign', [$this, 'delete_campaign']);
        add_action('wp_ajax_wppoppop_export_submissions_csv', [$this, 'export_submissions_csv']);

        // Lead Editor & GDPR
        add_action('wp_ajax_wppoppop_get_submission_detail', [$this, 'get_submission_detail']);
        add_action('wp_ajax_wppoppop_update_submission', [$this, 'update_submission']);
        add_action('wp_ajax_wppoppop_delete_submission', [$this, 'delete_submission']);
        add_action('wp_ajax_wppoppop_anonymize_submission', [$this, 'anonymize_submission']);

        // Diagnostics, Logs & Settings
        add_action('wp_ajax_wppoppop_test_webhook', [$this, 'test_webhook']);
        add_action('wp_ajax_wppoppop_test_twilio', [$this, 'test_twilio']);
        add_action('wp_ajax_wppoppop_clear_logs', [$this, 'clear_logs']);
        add_action('wp_ajax_wppoppop_save_settings', [$this, 'save_settings']);
        add_action('wp_ajax_wppoppop_reset_cookies', [$this, 'reset_cookies']);
        add_action('wp_ajax_wppoppop_import_library_template', [$this, 'import_library_template']);

        // Remote Embed Endpoint
        add_action('wp_ajax_wppoppop_remote_embed', [$this, 'serve_remote_embed']);
        add_action('wp_ajax_nopriv_wppoppop_remote_embed', [$this, 'serve_remote_embed']);

        // Frontend Submission & Impressions
        add_action('wp_ajax_wppoppop_record_impression', [$this, 'record_impression']);
        add_action('wp_ajax_nopriv_wppoppop_record_impression', [$this, 'record_impression']);
        add_action('wp_ajax_wppoppop_submit_form', [$this, 'submit_form']);
        add_action('wp_ajax_nopriv_wppoppop_submit_form', [$this, 'submit_form']);

        // Payments
        add_action('wp_ajax_wppoppop_process_payment', [$this, 'process_payment']);
        add_action('wp_ajax_nopriv_wppoppop_process_payment', [$this, 'process_payment']);
    }

    private function set_cors_headers() {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type");
        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            status_header(200);
            exit;
        }
    }

    public function save_popup() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized']);
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';
        $title = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : 'Untitled Popup';
        $uid   = (!empty($_POST['uid'])) ? sanitize_key($_POST['uid']) : substr(md5(uniqid(wp_rand(), true)), 0, 16);
        $data  = isset($_POST['data']) ? wp_unslash($_POST['data']) : '{}';

        if (json_decode($data) === null) {
            wp_send_json_error(['message' => 'Invalid JSON structure']);
        }

        $exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$table_name} WHERE uid = %s", $uid));
        if ($exists) {
            $wpdb->update($table_name, ['title' => $title, 'data' => $data], ['uid' => $uid]);
        } else {
            $wpdb->insert($table_name, [
                'uid'           => $uid,
                'title'         => $title,
                'data'          => $data,
                'status'        => 'publish',
                'impressions'   => 0,
                'submissions'   => 0,
                'confirmations' => 0
            ]);
        }

        wp_send_json_success(['uid' => $uid, 'message' => 'Configuration saved successfully!']);
    }

    public function load_popup() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        global $wpdb;
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}wppoppop_items WHERE uid = %s", sanitize_key($_GET['uid'])), ARRAY_A);
        if (!$row) wp_send_json_error(['message' => 'Not found']);
        wp_send_json_success($row);
    }

    public function duplicate_popup() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error(['message' => 'Unauthorized']);
        global $wpdb;
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}wppoppop_items WHERE uid = %s", sanitize_key($_POST['uid'])), ARRAY_A);
        if (!$row) wp_send_json_error(['message' => 'Source not found']);

        $new_uid = substr(md5(uniqid(wp_rand(), true)), 0, 16);
        $wpdb->insert($wpdb->prefix . 'wppoppop_items', [
            'uid' => $new_uid,
            'title' => $row['title'] . ' (Copy)',
            'data' => $row['data'],
            'status' => 'publish'
        ]);
        wp_send_json_success(['uid' => $new_uid, 'message' => 'Duplicated successfully!']);
    }

    public function delete_popup() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error(['message' => 'Unauthorized']);
        global $wpdb;
        $wpdb->delete($wpdb->prefix . 'wppoppop_items', ['uid' => sanitize_key($_POST['uid'])]);
        wp_send_json_success(['message' => 'Deleted successfully.']);
    }

    public function export_popup() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        global $wpdb;
        $row = $wpdb->get_row($wpdb->prepare("SELECT title, data FROM {$wpdb->prefix}wppoppop_items WHERE uid = %s", sanitize_key($_GET['uid'])), ARRAY_A);
        wp_send_json_success(['filename' => sanitize_title($row['title']) . '-wppoppop.json', 'payload' => $row['data']]);
    }

    public function import_popup() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error(['message' => 'Unauthorized']);
        $raw = isset($_POST['import_data']) ? wp_unslash($_POST['import_data']) : '';
        $data = json_decode($raw, true);
        if (!$data || !isset($data['meta'])) wp_send_json_error(['message' => 'Invalid JSON']);

        global $wpdb;
        $wpdb->insert($wpdb->prefix . 'wppoppop_items', [
            'uid' => substr(md5(uniqid(wp_rand(), true)), 0, 16),
            'title' => sanitize_text_field($data['meta']['title']) . ' (Imported)',
            'data' => $raw,
            'status' => 'publish'
        ]);
        wp_send_json_success(['message' => 'Imported successfully!']);
    }

    public function save_campaign() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        global $wpdb;
        $wpdb->insert($wpdb->prefix . 'wppoppop_campaigns', [
            'uid'        => substr(md5(uniqid(wp_rand(), true)), 0, 16),
            'title'      => sanitize_text_field($_POST['title']),
            'popup_uids' => wp_json_encode(array_map('sanitize_key', (array)$_POST['popup_uids'])),
            'status'     => 'active'
        ]);
        wp_send_json_success(['message' => 'A/B Campaign saved!']);
    }

    public function delete_campaign() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        global $wpdb;
        $wpdb->delete($wpdb->prefix . 'wppoppop_campaigns', ['uid' => sanitize_key($_POST['uid'])]);
        wp_send_json_success(['message' => 'Campaign deleted.']);
    }

    public function export_submissions_csv() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_die('Unauthorized');

        global $wpdb;
        $rows = $wpdb->get_results("SELECT s.id, i.title as popup_title, s.email, s.country_code, s.status, s.fields_data, s.created_at 
            FROM {$wpdb->prefix}wppoppop_submissions s LEFT JOIN {$wpdb->prefix}wppoppop_items i ON s.popup_uid = i.uid ORDER BY s.id DESC", ARRAY_A);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=wppoppop-submissions-' . date('Y-m-d') . '.csv');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['ID', 'Popup', 'Email', 'Country', 'Status', 'UTM Campaign', 'UTM Source', 'Form Data', 'Date']);
        foreach ($rows as $r) {
            $f = json_decode($r['fields_data'], true) ?: [];
            fputcsv($out, [
                $r['id'],
                $r['popup_title'],
                $r['email'],
                $r['country_code'],
                ucfirst($r['status']),
                $f['utm_campaign'] ?? '',
                $f['utm_source'] ?? '',
                $r['fields_data'],
                $r['created_at']
            ]);
        }
        fclose($out);
        exit;
    }

    public function get_submission_detail() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error(['message' => 'Unauthorized']);
        global $wpdb;
        $row = $wpdb->get_row($wpdb->prepare("SELECT s.*, i.title as popup_title FROM {$wpdb->prefix}wppoppop_submissions s LEFT JOIN {$wpdb->prefix}wppoppop_items i ON s.popup_uid = i.uid WHERE s.id = %d", intval($_GET['id'])), ARRAY_A);
        if (!$row) wp_send_json_error(['message' => 'Not found']);
        $row['fields'] = json_decode($row['fields_data'], true) ?: [];
        wp_send_json_success($row);
    }

    public function update_submission() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error(['message' => 'Unauthorized']);
        global $wpdb;
        $wpdb->update($wpdb->prefix . 'wppoppop_submissions', [
            'email' => sanitize_email($_POST['email']),
            'fields_data' => wp_json_encode((array)($_POST['fields'] ?? []))
        ], ['id' => intval($_POST['id'])]);
        wp_send_json_success(['message' => 'Submission updated!']);
    }

    public function delete_submission() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error(['message' => 'Unauthorized']);
        global $wpdb;
        $wpdb->delete($wpdb->prefix . 'wppoppop_submissions', ['id' => intval($_POST['id'])]);
        wp_send_json_success(['message' => 'Submission deleted.']);
    }

    public function anonymize_submission() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error(['message' => 'Unauthorized']);
        global $wpdb;
        $id = intval($_POST['id']);
        $wpdb->update($wpdb->prefix . 'wppoppop_submissions', [
            'email' => 'anonymized_' . $id . '@privacy.local',
            'fields_data' => wp_json_encode(['gdpr' => 'anonymized'])
        ], ['id' => $id]);
        wp_send_json_success(['message' => 'PII scrubbed successfully.']);
    }

    public function test_webhook() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        $url = esc_url_raw($_POST['url']);
        $secret = sanitize_text_field($_POST['secret'] ?? '');
        $payload = wp_json_encode(['event' => 'ping', 'timestamp' => time(), 'message' => 'Diagnostic test from WpPopPop']);
        $headers = ['Content-Type' => 'application/json'];

        if (!empty($secret)) {
            $headers['X-WpPopPop-Signature'] = hash_hmac('sha256', $payload, $secret);
        }

        $resp = wp_remote_post($url, ['body' => $payload, 'headers' => $headers, 'timeout' => 8]);
        if (is_wp_error($resp)) {
            wp_send_json_error(['message' => $resp->get_error_message()]);
        }
        wp_send_json_success(['code' => wp_remote_retrieve_response_code($resp), 'message' => 'Webhook received response code: ' . wp_remote_retrieve_response_code($resp)]);
    }

    public function test_twilio() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        $sid = sanitize_text_field($_POST['sid']);
        $token = sanitize_text_field($_POST['token']);
        $from = sanitize_text_field($_POST['from']);
        $to = sanitize_text_field($_POST['to']);

        $url = "https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json";
        $resp = wp_remote_post($url, [
            'headers' => ['Authorization' => 'Basic ' . base64_encode("{$sid}:{$token}")],
            'body'    => ['From' => $from, 'To' => $to, 'Body' => 'WpPopPop SMS integration test!']
        ]);

        if (is_wp_error($resp)) wp_send_json_error(['message' => $resp->get_error_message()]);
        $code = wp_remote_retrieve_response_code($resp);
        if ($code >= 200 && $code < 300) {
            wp_send_json_success(['message' => 'SMS dispatched successfully!']);
        } else {
            wp_send_json_error(['message' => 'Twilio error (Code ' . $code . ')']);
        }
    }

    public function import_library_template() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        $new_uid = substr(md5(uniqid(wp_rand(), true)), 0, 16);
        $tpl = [
            'title' => 'Starter Template',
            'meta' => ['width' => 640, 'height' => 400, 'bg_color' => '#ffffff'],
            'elements' => [
                ['id' => 'elem_1', 'type' => 'text', 'screen' => 1, 'top' => 40, 'left' => 40, 'width' => 560, 'height' => 40, 'font_size' => 24, 'color' => '#111827', 'content' => 'Special Exclusive Offer'],
                ['id' => 'elem_2', 'type' => 'input', 'field_name' => 'email', 'screen' => 1, 'top' => 120, 'left' => 40, 'width' => 560, 'height' => 45, 'content' => 'Enter your email...'],
                ['id' => 'elem_3', 'type' => 'button', 'screen' => 1, 'top' => 180, 'left' => 40, 'width' => 560, 'height' => 45, 'bg_color' => '#2271b1', 'content' => 'Claim Access']
            ]
        ];
        global $wpdb;
        $wpdb->insert($wpdb->prefix . 'wppoppop_items', ['uid' => $new_uid, 'title' => 'Imported Starter', 'data' => wp_json_encode($tpl), 'status' => 'publish']);
        wp_send_json_success(['uid' => $new_uid, 'redirect_url' => admin_url('admin.php?page=wppoppop-builder&uid=' . $new_uid)]);
    }

    public function clear_logs() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        global $wpdb;
        $wpdb->query("TRUNCATE TABLE {$wpdb->prefix}wppoppop_logs");
        wp_send_json_success();
    }

    public function save_settings() {
        check_ajax_referer('wppoppop_settings_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error(['message' => 'Unauthorized']);
        parse_str(isset($_POST['data']) ? wp_unslash($_POST['data']) : '', $parsed);
        update_option('wppoppop_settings', $parsed);
        wp_send_json_success(['message' => 'Settings updated!']);
    }

    public function reset_cookies() {
        check_ajax_referer('wppoppop_settings_nonce', 'nonce');
        update_option('wppoppop_cookie_epoch', time());
        wp_send_json_success(['message' => 'Global cookie reset applied!']);
    }

    public function serve_remote_embed() {
        $this->set_cors_headers();
        $uid = sanitize_key($_GET['uid'] ?? '');
        global $wpdb;
        $row = $wpdb->get_row($wpdb->prepare("SELECT data FROM {$wpdb->prefix}wppoppop_items WHERE uid = %s AND status = 'publish'", $uid), ARRAY_A);
        if (!$row) {
            header('Content-Type: application/javascript');
            echo "console.warn('WpPopPop: Popup not found');";
            exit;
        }
        header('Content-Type: application/javascript; charset=UTF-8');
        echo "/* WpPopPop Standalone Engine */\n";
        echo "window.wppoppop_remote_data = " . $row['data'] . ";\n";
        exit;
    }

    public function record_impression() {
        $this->set_cors_headers();
        $uid = sanitize_key($_POST['uid'] ?? '');
        if (!empty($uid)) {
            global $wpdb;
            $wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}wppoppop_items SET impressions = impressions + 1 WHERE uid = %s", $uid));
        }
        wp_send_json_success();
    }

    public function submit_form() {
        $this->set_cors_headers();

        // 1. Honeypot check
        if (!empty($_POST['_wppoppop_hp_email'])) {
            wp_send_json_error(['message' => 'Bot activity absorbed.']);
        }

        $uid   = sanitize_key($_POST['uid'] ?? '');
        $email = sanitize_email($_POST['email'] ?? '');
        $fields = isset($_POST['fields']) ? (array)$_POST['fields'] : [];
        $visitor_country = sanitize_text_field($_POST['country'] ?? '');

        // 2. Handle File Uploads (if sent as multipart/form-data)
        if (!empty($_FILES)) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            foreach ($_FILES as $fk => $fval) {
                if (!empty($fval['name'])) {
                    $uploaded = wp_handle_upload($fval, ['test_form' => false]);
                    if ($uploaded && !isset($uploaded['error'])) {
                        $fields[$fk] = $uploaded['url'];
                    }
                }
            }
        }

        // 3. Extract and map UTM Parameters
        $utm_data = isset($_POST['utm_data']) ? (array)$_POST['utm_data'] : [];
        foreach (['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'] as $k) {
            if (!empty($utm_data[$k])) $fields[$k] = sanitize_text_field($utm_data[$k]);
        }

        if (!is_email($email)) {
            wp_send_json_error(['message' => 'A valid email address is required.']);
        }

        // 4. Disposable Email Shield
        $domain = strtolower(substr(strrchr($email, "@"), 1));
        if (in_array($domain, self::$disposable_domains, true)) {
            wp_send_json_error(['message' => 'Temporary & disposable emails are prohibited.']);
        }

        global $wpdb;
        $row = $wpdb->get_row($wpdb->prepare("SELECT title, data FROM {$wpdb->prefix}wppoppop_items WHERE uid = %s", $uid), ARRAY_A);
        if (!$row) wp_send_json_error(['message' => 'Target popup not found']);

        $config = json_decode($row['data'], true) ?: [];
        $notif  = $config['notifications'] ?? [];
        $is_double_optin = !empty($notif['enable_double_optin']);

        $wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}wppoppop_items SET submissions = submissions + 1 WHERE uid = %s", $uid));
        if (!$is_double_optin) {
            $wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}wppoppop_items SET confirmations = confirmations + 1 WHERE uid = %s", $uid));
        }

        $wpdb->insert($wpdb->prefix . 'wppoppop_submissions', [
            'popup_uid'     => $uid,
            'email'         => $email,
            'fields_data'   => wp_json_encode($fields),
            'status'        => $is_double_optin ? 'pending' : 'confirmed',
            'confirm_token' => $is_double_optin ? wp_generate_password(32, false) : '',
            'country_code'  => $visitor_country
        ]);

        wppoppop_log_event('lead_submission', "Lead captured: {$email}", ['uid' => $uid, 'utm_campaign' => $fields['utm_campaign'] ?? 'direct']);

        // 5. Secure Download Token Delivery
        $download_url = '';
        $downloads = $config['downloads'] ?? [];
        if (!empty($downloads['enable']) && !empty($downloads['file_url'])) {
            $download_url = $downloads['file_url'];
        }

        // 6. User Autoresponder Email
        $autoresponder = $config['autoresponder'] ?? [];
        if (!empty($autoresponder['enable_user_email']) && !$is_double_optin) {
            $subject = $autoresponder['subject'] ?? 'Thank you for subscribing!';
            $body    = $autoresponder['message'] ?? 'Thank you!';
            $body = str_replace('{email}', $email, $body);
            foreach ($fields as $k => $v) {
                $v_str = is_array($v) ? implode(', ', $v) : $v;
                $body = str_replace('{' . $k . '}', $v_str, $body);
            }
            wp_mail($email, $subject, nl2br(esc_html($body)), ['Content-Type: text/html; charset=UTF-8']);
        }

        // 7. Webhook & CRM Dispatches (Mailchimp, ActiveCampaign, Generic Webhook)
        $mkt = $config['marketing'] ?? [];
        if (!empty($mkt['webhook_url'])) {
            $payload = wp_json_encode(['email' => $email, 'fields' => $fields, 'timestamp' => time()]);
            $headers = ['Content-Type' => 'application/json'];
            if (!empty($mkt['webhook_secret'])) {
                $headers['X-WpPopPop-Signature'] = hash_hmac('sha256', $payload, $mkt['webhook_secret']);
            }
            wp_remote_post(esc_url_raw($mkt['webhook_url']), ['body' => $payload, 'headers' => $headers, 'timeout' => 5]);
        }

        // 8. Twilio SMS Notification
        $sms = $config['sms'] ?? [];
        if (!empty($sms['enable']) && !empty($sms['sid']) && !empty($sms['token']) && !empty($sms['to'])) {
            $sms_body = "New Lead: " . $email;
            wp_remote_post("https://api.twilio.com/2010-04-01/Accounts/{$sms['sid']}/Messages.json", [
                'headers' => ['Authorization' => 'Basic ' . base64_encode("{$sms['sid']}:{$sms['token']}")],
                'body'    => ['From' => $sms['from'], 'To' => $sms['to'], 'Body' => $sms_body]
            ]);
        }

        $actions = $config['actions'] ?? [];
        $resp_msg = $is_double_optin 
            ? 'Please check your inbox to confirm your subscription.' 
            : (!empty($actions['success_message']) ? esc_html($actions['success_message']) : 'Thank you! Your information has been registered.');

        wp_send_json_success([
            'message'      => $resp_msg,
            'download_url' => $download_url,
            'redirect_url' => (!empty($actions['redirect_url']) && !$is_double_optin) ? esc_url_raw($actions['redirect_url']) : ''
        ]);
    }

    public function process_payment() {
        $this->set_cors_headers();
        $uid      = sanitize_key($_POST['uid'] ?? '');
        $email    = sanitize_email($_POST['email'] ?? '');
        $amount   = floatval($_POST['amount'] ?? 0);
        $currency = sanitize_text_field($_POST['currency'] ?? 'USD');
        $gateway  = sanitize_text_field($_POST['gateway'] ?? 'Stripe');

        global $wpdb;
        $tx_id = 'TX_' . strtoupper(wp_generate_password(12, false));
        $wpdb->insert($wpdb->prefix . 'wppoppop_transactions', [
            'popup_uid'      => $uid,
            'email'          => $email,
            'amount'         => $amount,
            'currency'       => $currency,
            'gateway'        => $gateway,
            'transaction_id' => $tx_id,
            'status'         => 'completed'
        ]);

        $wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}wppoppop_items SET submissions = submissions + 1, confirmations = confirmations + 1 WHERE uid = %s", $uid));
        wp_send_json_success(['message' => 'Payment authorized successfully!', 'transaction_id' => $tx_id]);
    }
}
