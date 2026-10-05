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

        add_action('wp_ajax_wppoppop_record_impression', [$this, 'record_impression']);
        add_action('wp_ajax_nopriv_wppoppop_record_impression', [$this, 'record_impression']);
        add_action('wp_ajax_wppoppop_submit_form', [$this, 'submit_form']);
        add_action('wp_ajax_nopriv_wppoppop_submit_form', [$this, 'submit_form']);
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
            $wpdb->update(
                $table_name,
                ['title' => $title, 'data' => $data],
                ['uid' => $uid],
                ['%s', '%s'],
                ['%s']
            );
        } else {
            $wpdb->insert(
                $table_name,
                ['uid' => $uid, 'title' => $title, 'data' => $data, 'status' => 'publish'],
                ['%s', '%s', '%s', '%s']
            );
        }

        wp_send_json_success(['uid' => $uid, 'message' => 'Popup configuration saved successfully!']);
    }

    public function load_popup() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';
        $uid = isset($_GET['uid']) ? sanitize_key($_GET['uid']) : '';

        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_name} WHERE uid = %s", $uid), ARRAY_A);

        if (!$row) {
            wp_send_json_error(['message' => 'Popup not found.']);
        }

        wp_send_json_success($row);
    }

    public function duplicate_popup() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized action.']);
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';
        $source_uid = isset($_POST['uid']) ? sanitize_key($_POST['uid']) : '';

        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_name} WHERE uid = %s", $source_uid), ARRAY_A);
        if (!$row) {
            wp_send_json_error(['message' => 'Source popup not found.']);
        }

        $new_uid   = wp_generate_uuid4();
        $new_title = $row['title'] . ' (Duplicate)';

        $wpdb->insert(
            $table_name,
            [
                'uid'         => $new_uid,
                'title'       => $new_title,
                'data'        => $row['data'],
                'status'      => 'publish',
                'impressions' => 0,
                'submissions' => 0
            ],
            ['%s', '%s', '%s', '%s', '%d', '%d']
        );

        wp_send_json_success(['uid' => $new_uid, 'message' => 'Popup duplicated successfully!']);
    }

    public function delete_popup() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized action.']);
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';
        $uid = isset($_POST['uid']) ? sanitize_key($_POST['uid']) : '';

        $wpdb->delete($table_name, ['uid' => $uid], ['%s']);
        wp_send_json_success(['message' => 'Popup removed successfully.']);
    }

    public function export_popup() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized action.']);
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';
        $uid = isset($_GET['uid']) ? sanitize_key($_GET['uid']) : '';

        $row = $wpdb->get_row($wpdb->prepare("SELECT title, data FROM {$table_name} WHERE uid = %s", $uid), ARRAY_A);
        if (!$row) {
            wp_send_json_error(['message' => 'Popup not found.']);
        }

        wp_send_json_success([
            'filename' => sanitize_title($row['title']) . '-wppoppop-export.json',
            'payload'  => $row['data']
        ]);
    }

    public function import_popup() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized action.']);
        }

        $json_raw = isset($_POST['import_data']) ? wp_unslash($_POST['import_data']) : '';
        $data = json_decode($json_raw, true);

        if (!$data || !isset($data['meta'])) {
            wp_send_json_error(['message' => 'Invalid WpPopPop JSON format.']);
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';
        $new_uid = wp_generate_uuid4();
        $title = isset($data['meta']['title']) ? sanitize_text_field($data['meta']['title']) . ' (Imported)' : 'Imported Popup';

        $wpdb->insert(
            $table_name,
            [
                'uid'         => $new_uid,
                'title'       => $title,
                'data'        => $json_raw,
                'status'      => 'publish',
                'impressions' => 0,
                'submissions' => 0
            ],
            ['%s', '%s', '%s', '%s', '%d', '%d']
        );

        wp_send_json_success(['uid' => $new_uid, 'message' => 'Popup imported successfully!']);
    }

    public function record_impression() {
        check_ajax_referer('wppoppop_front_nonce', 'nonce');
        $uid = isset($_POST['uid']) ? sanitize_key($_POST['uid']) : '';

        if (!empty($uid)) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'wppoppop_items';
            $wpdb->query($wpdb->prepare("UPDATE {$table_name} SET impressions = impressions + 1 WHERE uid = %s", $uid));
        }
        wp_send_json_success();
    }

    public function submit_form() {
        check_ajax_referer('wppoppop_front_nonce', 'nonce');

        $uid   = isset($_POST['uid']) ? sanitize_key($_POST['uid']) : '';
        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        $form_data = isset($_POST['fields']) ? (array)$_POST['fields'] : [];

        if (!is_email($email)) {
            wp_send_json_error(['message' => 'Please enter a valid email address.']);
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';
        $row = $wpdb->get_row($wpdb->prepare("SELECT title, data FROM {$table_name} WHERE uid = %s", $uid), ARRAY_A);

        if ($row) {
            $wpdb->query($wpdb->prepare("UPDATE {$table_name} SET submissions = submissions + 1 WHERE uid = %s", $uid));
            $config = json_decode($row['data'], true);

            // Dispatch Email Notification
            $notif = isset($config['notifications']) ? $config['notifications'] : [];
            if (!empty($notif['enable_email'])) {
                $recipient = !empty($notif['recipient']) ? sanitize_email($notif['recipient']) : get_option('admin_email');
                $subject   = !empty($notif['subject']) ? sanitize_text_field($notif['subject']) : 'New Lead: ' . $row['title'];
                
                $body = "New form submission received on " . date('Y-m-d H:i:s') . "\n\n";
                $body .= "Popup: " . $row['title'] . "\n";
                $body .= "Email: " . $email . "\n";
                if (!empty($form_data)) {
                    $body .= "\nForm Fields:\n";
                    foreach ($form_data as $key => $val) {
                        $body .= sanitize_key($key) . ": " . sanitize_text_field($val) . "\n";
                    }
                }

                wp_mail($recipient, $subject, $body);
            }

            $actions = isset($config['actions']) ? $config['actions'] : [];
            $success_msg = !empty($actions['success_message']) ? esc_html($actions['success_message']) : 'Thank you! Your submission has been processed.';
            $redirect_url = !empty($actions['redirect_url']) ? esc_url_raw($actions['redirect_url']) : '';

            wp_send_json_success([
                'message'      => $success_msg,
                'redirect_url' => $redirect_url
            ]);
        }

        wp_send_json_error(['message' => 'An error occurred during submission.']);
    }
}
