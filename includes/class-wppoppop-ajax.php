<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Ajax {
    public function __construct() {
        // Admin builder handlers
        add_action('wp_ajax_wppoppop_save_popup', [$this, 'save_popup']);
        add_action('wp_ajax_wppoppop_load_popup', [$this, 'load_popup']);

        // Frontend impression and submission handlers
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

        wp_send_json_success(['uid' => $uid, 'message' => 'Popup successfully saved and synced!']);
    }

    public function load_popup() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');

        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';
        $uid = isset($_GET['uid']) ? sanitize_key($_GET['uid']) : '';

        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_name} WHERE uid = %s", $uid), ARRAY_A);

        if (!$row) {
            wp_send_json_error(['message' => 'Popup could not be found.']);
        }

        wp_send_json_success($row);
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

        if (!is_email($email)) {
            wp_send_json_error(['message' => 'Please enter a valid email address.']);
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';
        $wpdb->query($wpdb->prepare("UPDATE {$table_name} SET submissions = submissions + 1 WHERE uid = %s", $uid));

        wp_send_json_success([
            'message' => 'Thank you! Your information has been registered.',
            'redirect_url' => ''
        ]);
    }
}
