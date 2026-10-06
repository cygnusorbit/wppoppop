<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Ajax_Builder {
    public function __construct() {
        add_action('wp_ajax_wppoppop_save_popup', [$this, 'save_popup']);
        add_action('wp_ajax_wppoppop_load_popup', [$this, 'load_popup']);
        add_action('wp_ajax_wppoppop_duplicate_popup', [$this, 'duplicate_popup']);
        add_action('wp_ajax_wppoppop_delete_popup', [$this, 'delete_popup']);
        add_action('wp_ajax_wppoppop_export_popup', [$this, 'export_popup']);
        add_action('wp_ajax_wppoppop_import_popup', [$this, 'import_popup']);
        add_action('wp_ajax_wppoppop_save_campaign', [$this, 'save_campaign']);
        add_action('wp_ajax_wppoppop_delete_campaign', [$this, 'delete_campaign']);
    }

    public function save_popup() {
        if (!check_ajax_referer('wppoppop_builder_nonce', 'nonce', false) && !check_ajax_referer('wppoppop_admin_nonce', 'nonce', false)) {
            wp_send_json_error(['message' => 'Security token verification failed. Please refresh the page.']);
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized user permissions.']);
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';

        $uid   = isset($_POST['uid']) ? sanitize_key($_POST['uid']) : '';
        $title = isset($_POST['title']) ? sanitize_text_field(wp_unslash($_POST['title'])) : 'Untitled Popup Campaign';
        $data  = isset($_POST['data']) ? wp_unslash($_POST['data']) : '{}';

        // Validate JSON integrity
        $decoded = json_decode($data, true);
        if ($decoded === null) {
            wp_send_json_error(['message' => 'Malformed JSON configuration payload.']);
        }

        $now = current_time('mysql');

        if (!empty($uid)) {
            $existing = $wpdb->get_row($wpdb->prepare("SELECT id FROM {$table_name} WHERE uid = %s", $uid));
            if ($existing) {
                $wpdb->update(
                    $table_name,
                    [
                        'title'      => $title,
                        'data'       => $data,
                        'updated_at' => $now
                    ],
                    ['uid' => $uid],
                    ['%s', '%s', '%s'],
                    ['%s']
                );
                wp_send_json_success(['uid' => $uid, 'message' => 'Popup configuration saved successfully.']);
            }
        }

        // New Popup Record
        $new_uid = 'pop_' . wp_generate_password(8, false, false);
        $wpdb->insert(
            $table_name,
            [
                'uid'        => $new_uid,
                'title'      => $title,
                'status'     => 'publish',
                'data'       => $data,
                'created_at' => $now,
                'updated_at' => $now
            ],
            ['%s', '%s', '%s', '%s', '%s', '%s']
        );

        wp_send_json_success(['uid' => $new_uid, 'message' => 'New popup created and saved successfully.']);
    }

    public function load_popup() {
        check_ajax_referer('wppoppop_admin_nonce', 'nonce');
        $uid = isset($_POST['uid']) ? sanitize_key($_POST['uid']) : '';
        global $wpdb;
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}wppoppop_items WHERE uid = %s", $uid), ARRAY_A);
        if ($row) {
            wp_send_json_success($row);
        }
        wp_send_json_error(['message' => 'Popup not found']);
    }

    public function duplicate_popup() {
        check_ajax_referer('wppoppop_admin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized']);
        }
        $uid = isset($_POST['uid']) ? sanitize_key($_POST['uid']) : '';
        global $wpdb;
        $table = $wpdb->prefix . 'wppoppop_items';
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE uid = %s", $uid), ARRAY_A);
        if ($row) {
            $new_uid = 'pop_' . wp_generate_password(8, false, false);
            $wpdb->insert($table, [
                'uid'        => $new_uid,
                'title'      => $row['title'] . ' (Copy)',
                'status'     => 'draft',
                'data'       => $row['data'],
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            ]);
            wp_send_json_success(['uid' => $new_uid]);
        }
        wp_send_json_error(['message' => 'Duplication failed']);
    }

    public function delete_popup() {
        check_ajax_referer('wppoppop_admin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized']);
        }
        $uid = isset($_POST['uid']) ? sanitize_key($_POST['uid']) : '';
        global $wpdb;
        $wpdb->delete($wpdb->prefix . 'wppoppop_items', ['uid' => $uid]);
        wp_send_json_success();
    }

    public function export_popup() {
        check_ajax_referer('wppoppop_admin_nonce', 'nonce');
        $uid = isset($_GET['uid']) ? sanitize_key($_GET['uid']) : '';
        global $wpdb;
        $row = $wpdb->get_row($wpdb->prepare("SELECT title, data FROM {$wpdb->prefix}wppoppop_items WHERE uid = %s", $uid), ARRAY_A);
        if ($row) {
            header('Content-Type: application/json');
            header('Content-Disposition: attachment; filename="wppoppop-' . $uid . '.json"');
            echo wp_json_encode($row);
            exit;
        }
        wp_die('Invalid popup ID');
    }

    public function import_popup() {
        check_ajax_referer('wppoppop_admin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized']);
        }
        $title = isset($_POST['title']) ? sanitize_text_field(wp_unslash($_POST['title'])) : 'Imported Popup';
        $data = isset($_POST['data']) ? wp_unslash($_POST['data']) : '{}';
        global $wpdb;
        $new_uid = 'pop_' . wp_generate_password(8, false, false);
        $wpdb->insert($wpdb->prefix . 'wppoppop_items', [
            'uid'        => $new_uid,
            'title'      => $title,
            'status'     => 'draft',
            'data'       => $data,
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ]);
        wp_send_json_success(['uid' => $new_uid]);
    }

    public function save_campaign() {
        check_ajax_referer('wppoppop_admin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized']);
        }
        $title = sanitize_text_field($_POST['title'] ?? 'New Campaign');
        $uids = isset($_POST['popup_uids']) ? (array)$_POST['popup_uids'] : [];
        global $wpdb;
        $table = $wpdb->prefix . 'wppoppop_campaigns';
        $new_uid = 'camp_' . wp_generate_password(8, false, false);
        $wpdb->insert($table, [
            'uid'        => $new_uid,
            'title'      => $title,
            'popup_uids' => wp_json_encode(array_map('sanitize_key', $uids)),
            'status'     => 'active',
            'created_at' => current_time('mysql')
        ]);
        wp_send_json_success();
    }

    public function delete_campaign() {
        check_ajax_referer('wppoppop_admin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized']);
        }
        $uid = isset($_POST['uid']) ? sanitize_key($_POST['uid']) : '';
        global $wpdb;
        $wpdb->delete($wpdb->prefix . 'wppoppop_campaigns', ['uid' => $uid]);
        wp_send_json_success();
    }
}
