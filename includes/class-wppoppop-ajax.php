<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Ajax {
    public function __construct() {
        add_action('wp_ajax_wppoppop_save_popup', [$this, 'save_popup']);
        add_action('wp_ajax_wppoppop_load_popup', [$this, 'load_popup']);
        add_action('wp_ajax_wppoppop_duplicate_popup', [$this, 'duplicate_popup']);
        add_action('wp_ajax_wppoppop_delete_popup', [$this, 'delete_popup']);
        add_action('wp_ajax_wppoppop_export_popup', [$this, 'export_popup']);
        add_action('wp_ajax_wppoppop_import_popup', [$this, 'import_popup']);
        add_action('wp_ajax_wppoppop_save_campaign', [$this, 'save_campaign']);
        add_action('wp_ajax_wppoppop_delete_campaign', [$this, 'delete_campaign']);
        add_action('wp_ajax_wppoppop_export_submissions_csv', [$this, 'export_submissions_csv']);

        add_action('wp_ajax_wppoppop_record_impression', [$this, 'record_impression']);
        add_action('wp_ajax_nopriv_wppoppop_record_impression', [$this, 'record_impression']);
        add_action('wp_ajax_wppoppop_submit_form', [$this, 'submit_form']);
        add_action('wp_ajax_nopriv_wppoppop_submit_form', [$this, 'submit_form']);

        // Payment processor AJAX
        add_action('wp_ajax_wppoppop_process_payment', [$this, 'process_payment']);
        add_action('wp_ajax_nopriv_wppoppop_process_payment', [$this, 'process_payment']);
    }

    public function save_popup() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized action.']);
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';

        $title = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : 'Untitled Popup';
        $uid   = isset($_POST['uid']) && !empty($_POST['uid']) ? sanitize_key($_POST['uid']) : wp_generate_uuid4();
        $data  = isset($_POST['data']) ? wp_unslash($_POST['data']) : '{}';

        if (json_decode($data) === null) {
            wp_send_json_error(['message' => 'Malformed JSON definition.']);
        }

        $existing = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$table_name} WHERE uid = %s", $uid));

        if ($existing) {
            $wpdb->update($table_name, ['title' => $title, 'data' => $data], ['uid' => $uid], ['%s', '%s'], ['%s']);
        } else {
            $wpdb->insert($table_name, ['uid' => $uid, 'title' => $title, 'data' => $data, 'status' => 'publish'], ['%s', '%s', '%s', '%s']);
        }

        wp_send_json_success(['uid' => $uid, 'message' => 'Popup configuration saved successfully!']);
    }

    public function load_popup() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        global $wpdb;
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}wppoppop_items WHERE uid = %s", sanitize_key($_GET['uid'])), ARRAY_A);
        if (!$row) wp_send_json_error(['message' => 'Popup not found.']);
        wp_send_json_success($row);
    }

    public function duplicate_popup() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error(['message' => 'Unauthorized']);

        global $wpdb;
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}wppoppop_items WHERE uid = %s", sanitize_key($_POST['uid'])), ARRAY_A);
        if (!$row) wp_send_json_error(['message' => 'Popup not found']);

        $new_uid = wp_generate_uuid4();
        $wpdb->insert($wpdb->prefix . 'wppoppop_items', [
            'uid' => $new_uid, 'title' => $row['title'] . ' (Copy)', 'data' => $row['data'], 'status' => 'publish'
        ]);

        wp_send_json_success(['uid' => $new_uid, 'message' => 'Duplicated successfully!']);
    }

    public function delete_popup() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error(['message' => 'Unauthorized']);
        global $wpdb;
        $wpdb->delete($wpdb->prefix . 'wppoppop_items', ['uid' => sanitize_key($_POST['uid'])]);
        wp_send_json_success(['message' => 'Popup removed.']);
    }

    public function export_popup() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        global $wpdb;
        $row = $wpdb->get_row($wpdb->prepare("SELECT title, data FROM {$wpdb->prefix}wppoppop_items WHERE uid = %s", sanitize_key($_GET['uid'])), ARRAY_A);
        wp_send_json_success(['filename' => sanitize_title($row['title']) . '-wppoppop-export.json', 'payload' => $row['data']]);
    }

    public function import_popup() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error(['message' => 'Unauthorized']);
        $json_raw = isset($_POST['import_data']) ? wp_unslash($_POST['import_data']) : '';
        $data = json_decode($json_raw, true);
        if (!$data || !isset($data['meta'])) wp_send_json_error(['message' => 'Invalid JSON']);

        global $wpdb;
        $wpdb->insert($wpdb->prefix . 'wppoppop_items', [
            'uid' => wp_generate_uuid4(), 'title' => sanitize_text_field($data['meta']['title']) . ' (Imported)', 'data' => $json_raw, 'status' => 'publish'
        ]);
        wp_send_json_success(['message' => 'Imported successfully!']);
    }

    public function save_campaign() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        global $wpdb;
        $wpdb->insert($wpdb->prefix . 'wppoppop_campaigns', [
            'uid' => wp_generate_uuid4(),
            'title' => sanitize_text_field($_POST['title']),
            'popup_uids' => wp_json_encode(array_map('sanitize_key', (array)$_POST['popup_uids'])),
            'status' => 'active'
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
        global $wpdb;
        $rows = $wpdb->get_results("SELECT s.id, i.title as popup_title, s.email, s.fields_data, s.created_at 
            FROM {$wpdb->prefix}wppoppop_submissions s LEFT JOIN {$wpdb->prefix}wppoppop_items i ON s.popup_uid = i.uid ORDER BY s.id DESC", ARRAY_A);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=wppoppop-submissions-' . date('Y-m-d') . '.csv');
        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'Popup', 'Email', 'Form Fields', 'Date']);
        foreach ($rows as $row) {
            fputcsv($output, [$row['id'], $row['popup_title'], $row['email'], $row['fields_data'], $row['created_at']]);
        }
        fclose($output);
        exit;
    }

    public function record_impression() {
        check_ajax_referer('wppoppop_front_nonce', 'nonce');
        $uid = isset($_POST['uid']) ? sanitize_key($_POST['uid']) : '';
        if (!empty($uid)) {
            global $wpdb;
            $wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}wppoppop_items SET impressions = impressions + 1 WHERE uid = %s", $uid));
        }
        wp_send_json_success();
    }

    public function submit_form() {
        check_ajax_referer('wppoppop_front_nonce', 'nonce');
        $uid = sanitize_key($_POST['uid']);
        $email = sanitize_email($_POST['email']);
        $form_data = isset($_POST['fields']) ? (array)$_POST['fields'] : [];

        if (!is_email($email)) {
            wp_send_json_error(['message' => 'Please enter a valid email address.']);
        }

        global $wpdb;
        $wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}wppoppop_items SET submissions = submissions + 1 WHERE uid = %s", $uid));
        $wpdb->insert($wpdb->prefix . 'wppoppop_submissions', [
            'popup_uid'   => $uid,
            'email'       => $email,
            'fields_data' => wp_json_encode($form_data)
        ]);

        $row = $wpdb->get_row($wpdb->prepare("SELECT title, data FROM {$wpdb->prefix}wppoppop_items WHERE uid = %s", $uid), ARRAY_A);
        $config = json_decode($row['data'], true);

        // Secure Downloads Token Generation
        $download_url = '';
        $downloads_cfg = isset($config['downloads']) ? $config['downloads'] : [];
        if (!empty($downloads_cfg['enable']) && !empty($downloads_cfg['file_url'])) {
            $token = wp_generate_password(32, false);
            $expires = date('Y-m-d H:i:s', time() + (intval($downloads_cfg['expiry_hours'] ?? 24) * 3600));

            $wpdb->insert($wpdb->prefix . 'wppoppop_downloads', [
                'popup_uid'   => $uid,
                'token'       => $token,
                'file_url'    => esc_url_raw($downloads_cfg['file_url']),
                'expires_at'  => $expires
            ]);

            $download_url = home_url('/?wppoppop_download=' . $token);
        }

        // Email Notification
        $notif = isset($config['notifications']) ? $config['notifications'] : [];
        if (!empty($notif['enable_email'])) {
            $recipient = !empty($notif['recipient']) ? sanitize_email($notif['recipient']) : get_option('admin_email');
            $body = "New lead: {$email}\nPopup: {$row['title']}\n";
            if ($download_url) $body .= "Secure File Link: {$download_url}\n";
            wp_mail($recipient, 'New Submission: ' . $row['title'], $body);
        }

        // Set unlock cookie for content locker
        setcookie('wppoppop_unlocked_' . $uid, '1', time() + (86400 * 30), '/');

        $actions = isset($config['actions']) ? $config['actions'] : [];
        wp_send_json_success([
            'message'      => !empty($actions['success_message']) ? esc_html($actions['success_message']) : 'Thank you for your submission!',
            'redirect_url' => !empty($actions['redirect_url']) ? esc_url_raw($actions['redirect_url']) : '',
            'download_url' => $download_url
        ]);
    }

    public function process_payment() {
        check_ajax_referer('wppoppop_front_nonce', 'nonce');
        $uid = sanitize_key($_POST['uid']);
        $email = sanitize_email($_POST['email']);
        $amount = floatval($_POST['amount']);
        $currency = sanitize_text_field($_POST['currency'] ?? 'USD');
        $gateway = sanitize_text_field($_POST['gateway'] ?? 'Simulated Gateway');

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

        $wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}wppoppop_items SET submissions = submissions + 1 WHERE uid = %s", $uid));

        wp_send_json_success([
            'message'        => 'Payment verified successfully! Thank you.',
            'transaction_id' => $tx_id
        ]);
    }
}
