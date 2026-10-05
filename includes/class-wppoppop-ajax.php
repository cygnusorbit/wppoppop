<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Ajax {
    public function __construct() {
        add_action('wp_ajax_wppoppop_save_popup', [$this, 'save_popup']);
        add_action('wp_ajax_wppoppop_load_popup', [$this, 'load_popup']);
        add_action('wp_ajax_wppoppop_toggle_popup_status', [$this, 'toggle_popup_status']);
        add_action('wp_ajax_wppoppop_duplicate_popup', [$this, 'duplicate_popup']);
        add_action('wp_ajax_wppoppop_reset_stats', [$this, 'reset_stats']);
        add_action('wp_ajax_wppoppop_delete_popup', [$this, 'delete_popup']);

        // Downloadable Definition and CSV Endpoints
        add_action('admin_post_wppoppop_export_definition', [$this, 'export_definition']);
        add_action('admin_post_wppoppop_export_csv', [$this, 'export_csv']);

        // Frontend Tracking
        add_action('wp_ajax_wppoppop_record_impression', [$this, 'record_impression']);
        add_action('wp_ajax_nopriv_wppoppop_record_impression', [$this, 'record_impression']);
        add_action('wp_ajax_wppoppop_submit_form', [$this, 'submit_form']);
        add_action('wp_ajax_nopriv_wppoppop_submit_form', [$this, 'submit_form']);
    }

    private function verify_security($action = '') {
        $nonce = isset($_REQUEST['nonce']) ? $_REQUEST['nonce'] : (isset($_REQUEST['_wpnonce']) ? $_REQUEST['_wpnonce'] : '');
        $valid = false;

        if ($action && wp_verify_nonce($nonce, $action)) {
            $valid = true;
        } elseif (wp_verify_nonce($nonce, 'wppoppop_dashboard_nonce')) {
            $valid = true;
        } elseif (wp_verify_nonce($nonce, 'wppoppop_builder_nonce')) {
            $valid = true;
        }

        if (!$valid || !current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized or security token expired.']);
        }
    }

    public function toggle_popup_status() {
        $this->verify_security();
        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';
        $uid = isset($_POST['uid']) ? sanitize_key($_POST['uid']) : '';

        $current = $wpdb->get_var($wpdb->prepare("SELECT status FROM {$table_name} WHERE uid = %s", $uid));
        if ($current === null) {
            wp_send_json_error(['message' => 'Popup not found.']);
        }

        $new_status = ($current === 'publish') ? 'draft' : 'publish';
        $wpdb->update($table_name, ['status' => $new_status], ['uid' => $uid], ['%s'], ['%s']);
        wp_send_json_success(['status' => $new_status]);
    }

    public function duplicate_popup() {
        $this->verify_security();
        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';
        $uid = isset($_POST['uid']) ? sanitize_key($_POST['uid']) : '';

        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_name} WHERE uid = %s", $uid), ARRAY_A);
        if (!$row) {
            wp_send_json_error(['message' => 'Campaign record not found.']);
        }

        $new_uid = 'pop_' . substr(md5(uniqid(wp_generate_uuid4(), true)), 0, 16);
        $wpdb->insert(
            $table_name,
            [
                'uid'         => $new_uid,
                'title'       => $row['title'] . ' (Copy)',
                'data'        => $row['data'],
                'status'      => 'draft',
                'impressions' => 0,
                'submissions' => 0
            ],
            ['%s', '%s', '%s', '%s', '%d', '%d']
        );

        wp_send_json_success(['uid' => $new_uid, 'message' => 'Popup duplicated successfully!']);
    }

    public function reset_stats() {
        $this->verify_security();
        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';
        $uid = isset($_POST['uid']) ? sanitize_key($_POST['uid']) : '';

        $wpdb->update($table_name, ['impressions' => 0, 'submissions' => 0], ['uid' => $uid], ['%d', '%d'], ['%s']);
        wp_send_json_success(['message' => 'Counters reset to 0.']);
    }

    public function delete_popup() {
        $this->verify_security();
        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';
        $uid = isset($_POST['uid']) ? sanitize_key($_POST['uid']) : '';

        $wpdb->delete($table_name, ['uid' => $uid], ['%s']);
        wp_send_json_success(['message' => 'Popup campaign deleted.']);
    }

    public function export_definition() {
        $uid = isset($_GET['uid']) ? sanitize_key($_GET['uid']) : '';
        $nonce = isset($_GET['nonce']) ? $_GET['nonce'] : '';

        if (!wp_verify_nonce($nonce, 'wppoppop_export_' . $uid) && !current_user_can('manage_options')) {
            wp_die('Unauthorized request.');
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';
        $row = $wpdb->get_row($wpdb->prepare("SELECT title, data FROM {$table_name} WHERE uid = %s", $uid), ARRAY_A);

        if (!$row) {
            wp_die('Popup configuration not found.');
        }

        $filename = 'wppoppop-definition-' . sanitize_title($row['title']) . '-' . $uid . '.json';
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo $row['data'];
        exit;
    }

    public function export_csv() {
        $uid = isset($_GET['uid']) ? sanitize_key($_GET['uid']) : '';
        $nonce = isset($_GET['nonce']) ? $_GET['nonce'] : '';

        if (!wp_verify_nonce($nonce, 'wppoppop_csv_' . $uid) && !current_user_can('manage_options')) {
            wp_die('Unauthorized request.');
        }

        global $wpdb;
        $sub_table = $wpdb->prefix . 'wppoppop_submissions';

        if ($wpdb->get_var("SHOW TABLES LIKE '{$sub_table}'") === $sub_table) {
            $rows = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$sub_table} WHERE popup_uid = %s ORDER BY id DESC", $uid), ARRAY_A);
        } else {
            $rows = [];
        }

        $filename = 'wppoppop-submissions-' . $uid . '-' . date('Y-m-d') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['ID', 'Popup UID', 'Email', 'Fields / Payload', 'Created Date']);

        if (!empty($rows)) {
            foreach ($rows as $r) {
                fputcsv($out, [
                    $r['id'] ?? '',
                    $r['popup_uid'] ?? '',
                    $r['email'] ?? '',
                    $r['fields'] ?? ($r['data'] ?? ''),
                    $r['created_at'] ?? ''
                ]);
            }
        }
        fclose($out);
        exit;
    }

    public function save_popup() {
        $this->verify_security();
        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';

        $title = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : 'Untitled Popup';
        $uid   = isset($_POST['uid']) && !empty($_POST['uid']) ? sanitize_key($_POST['uid']) : ('pop_' . substr(md5(uniqid(wp_generate_uuid4(), true)), 0, 16));
        $data  = isset($_POST['data']) ? wp_unslash($_POST['data']) : '{}';

        $existing = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$table_name} WHERE uid = %s", $uid));
        if ($existing) {
            $wpdb->update($table_name, ['title' => $title, 'data' => $data], ['uid' => $uid], ['%s', '%s'], ['%s']);
        } else {
            $wpdb->insert($table_name, ['uid' => $uid, 'title' => $title, 'data' => $data, 'status' => 'publish'], ['%s', '%s', '%s', '%s']);
        }

        wp_send_json_success(['uid' => $uid, 'message' => 'Popup configuration saved successfully!']);
    }

    public function load_popup() {
        $this->verify_security();
        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';
        $uid = isset($_GET['uid']) ? sanitize_key($_GET['uid']) : '';

        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_name} WHERE uid = %s", $uid), ARRAY_A);
        if (!$row) {
            wp_send_json_error(['message' => 'Popup campaign not found.']);
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
        $uid = isset($_POST['uid']) ? sanitize_key($_POST['uid']) : '';
        if (!empty($uid)) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'wppoppop_items';
            $wpdb->query($wpdb->prepare("UPDATE {$table_name} SET submissions = submissions + 1 WHERE uid = %s", $uid));
        }
        wp_send_json_success(['message' => 'Thank you! Submission processed successfully.']);
    }
}
