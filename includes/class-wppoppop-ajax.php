<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Ajax {
    public function __construct() {
        add_action('wp_ajax_wppoppop_save_popup', [$this, 'save_popup']);
        add_action('wp_ajax_wppoppop_load_popup', [$this, 'load_popup']);
        add_action('wp_ajax_wppoppop_toggle_popup_status', [$this, 'toggle_status']);
        add_action('wp_ajax_wppoppop_duplicate_popup', [$this, 'duplicate_popup']);
        add_action('wp_ajax_wppoppop_reset_stats', [$this, 'reset_stats']);
        add_action('wp_ajax_wppoppop_delete_popup', [$this, 'delete_popup']);
    }

    private function verify_security() {
        $nonce = '';
        if (!empty($_POST['nonce'])) $nonce = $_POST['nonce'];
        elseif (!empty($_POST['_wpnonce'])) $nonce = $_POST['_wpnonce'];
        elseif (!empty($_POST['wppoppop_builder_nonce_field'])) $nonce = $_POST['wppoppop_builder_nonce_field'];
        elseif (!empty($_GET['nonce'])) $nonce = $_GET['nonce'];

        if (wp_verify_nonce($nonce, 'wppoppop_builder_nonce') || wp_verify_nonce($nonce, 'wppoppop_admin_nonce')) {
            return true;
        }

        if (current_user_can('manage_options')) {
            return true;
        }

        wp_send_json_error(['message' => 'Security token invalid or expired.']);
    }

    public function save_popup() {
        $this->verify_security();
        global $wpdb;
        $table = $wpdb->prefix . 'wppoppop_items';

        // Self-healing database migration for varchar(64) UID
        $col_info = $wpdb->get_results("SHOW COLUMNS FROM {$table} LIKE 'uid'", ARRAY_A);
        if (!empty($col_info) && strpos(strtolower($col_info[0]['Type']), 'varchar(32)') !== false) {
            $wpdb->query("ALTER TABLE {$table} MODIFY COLUMN uid varchar(64) NOT NULL");
        }

        $title = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : 'yes-no-3';
        $uid   = !empty($_POST['uid']) ? sanitize_key($_POST['uid']) : 'pop_' . wp_generate_uuid4();
        $data  = isset($_POST['data']) ? wp_unslash($_POST['data']) : '{}';

        $exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$table} WHERE uid = %s", $uid));
        if ($exists) {
            $wpdb->update($table, ['title' => $title, 'data' => $data, 'status' => 'publish'], ['uid' => $uid], ['%s', '%s', '%s'], ['%s']);
        } else {
            $wpdb->insert($table, [
                'uid'         => $uid,
                'title'       => $title,
                'data'        => $data,
                'status'      => 'publish',
                'impressions' => 0,
                'submissions' => 0
            ], ['%s', '%s', '%s', '%s', '%d', '%d']);
        }

        wp_send_json_success(['uid' => $uid, 'message' => 'Popup configuration saved successfully!']);
    }

    public function load_popup() {
        $this->verify_security();
        global $wpdb;
        $table = $wpdb->prefix . 'wppoppop_items';
        $uid   = isset($_GET['uid']) ? sanitize_key($_GET['uid']) : '';

        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE uid = %s", $uid), ARRAY_A);
        if (!$row) {
            wp_send_json_error(['message' => 'Popup campaign not found.']);
        }

        wp_send_json_success($row);
    }

    public function toggle_status() {
        $this->verify_security();
        global $wpdb;
        $table = $wpdb->prefix . 'wppoppop_items';
        $uid   = isset($_POST['uid']) ? sanitize_key($_POST['uid']) : '';
        $curr  = $wpdb->get_var($wpdb->prepare("SELECT status FROM {$table} WHERE uid = %s", $uid));
        $new   = ($curr === 'publish') ? 'draft' : 'publish';
        $wpdb->update($table, ['status' => $new], ['uid' => $uid], ['%s'], ['%s']);
        wp_send_json_success(['status' => $new]);
    }

    public function duplicate_popup() {
        $this->verify_security();
        global $wpdb;
        $table = $wpdb->prefix . 'wppoppop_items';
        $uid   = isset($_POST['uid']) ? sanitize_key($_POST['uid']) : '';
        $row   = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE uid = %s", $uid), ARRAY_A);

        if (!$row) wp_send_json_error(['message' => 'Target not found']);

        $new_uid = 'pop_' . wp_generate_uuid4();
        $wpdb->insert($table, [
            'uid'         => $new_uid,
            'title'       => $row['title'] . ' (Copy)',
            'data'        => $row['data'],
            'status'      => 'draft',
            'impressions' => 0,
            'submissions' => 0
        ]);
        wp_send_json_success(['new_uid' => $new_uid]);
    }

    public function reset_stats() {
        $this->verify_security();
        global $wpdb;
        $table = $wpdb->prefix . 'wppoppop_items';
        $uid   = isset($_POST['uid']) ? sanitize_key($_POST['uid']) : '';
        $wpdb->update($table, ['impressions' => 0, 'submissions' => 0], ['uid' => $uid], ['%d', '%d'], ['%s']);
        wp_send_json_success();
    }

    public function delete_popup() {
        $this->verify_security();
        global $wpdb;
        $table = $wpdb->prefix . 'wppoppop_items';
        $uid   = isset($_POST['uid']) ? sanitize_key($_POST['uid']) : '';
        $wpdb->delete($table, ['uid' => $uid], ['%s']);
        wp_send_json_success();
    }
}
new WpPopPop_Ajax();
