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
        add_action('wp_ajax_wppoppop_toggle_status', [$this, 'toggle_status']);
        add_action('wp_ajax_wppoppop_reset_stats', [$this, 'reset_stats']);
        add_action('admin_post_wppoppop_export_definition', [$this, 'export_definition']);
        add_action('admin_post_wppoppop_export_csv', [$this, 'export_csv']);
        add_action('wp_ajax_wppoppop_save_settings', [$this, 'save_settings']);
    }

    private function verify_admin_security($nonce_action = 'wppoppop_builder_nonce') {
        $nonce = '';
        if (!empty($_REQUEST['nonce'])) {
            $nonce = sanitize_text_field($_REQUEST['nonce']);
        } elseif (!empty($_REQUEST['_wpnonce'])) {
            $nonce = sanitize_text_field($_REQUEST['_wpnonce']);
        } elseif (!empty($_REQUEST['wppoppop_builder_nonce_field'])) {
            $nonce = sanitize_text_field($_REQUEST['wppoppop_builder_nonce_field']);
        } elseif (!empty($_REQUEST['security'])) {
            $nonce = sanitize_text_field($_REQUEST['security']);
        }

        $valid = wp_verify_nonce($nonce, $nonce_action)
            || wp_verify_nonce($nonce, 'wppoppop_builder_nonce')
            || wp_verify_nonce($nonce, 'wppoppop_dash_nonce')
            || wp_verify_nonce($nonce, 'wppoppop_settings_nonce');

        if (!$valid) {
            wp_send_json_error(['message' => 'Security token expired or invalid. Please refresh the page.']);
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized administrator permissions required.']);
        }
    }

    public function save_popup() {
        $this->verify_admin_security('wppoppop_builder_nonce');
        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';

        $title  = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : 'Untitled Popup';
        $uid    = (!empty($_POST['uid'])) ? sanitize_key($_POST['uid']) : ('pop_' . substr(md5(uniqid(wp_rand(), true)), 0, 10));
        $status = (!empty($_POST['status']) && in_array($_POST['status'], ['publish', 'draft'], true)) ? $_POST['status'] : 'publish';

        $raw_data = isset($_POST['data']) ? wp_unslash($_POST['data']) : '{}';
        if (is_array($raw_data)) {
            $data = wp_json_encode($raw_data);
        } else {
            $decoded = json_decode($raw_data, true);
            if ($decoded === null && !empty($raw_data) && $raw_data !== '{}') {
                wp_send_json_error(['message' => 'Malformed JSON: ' . json_last_error_msg()]);
            }
            $data = $raw_data;
        }

        $existing = $wpdb->get_row($wpdb->prepare("SELECT id FROM {$table_name} WHERE uid = %s", $uid));

        if ($existing) {
            $result = $wpdb->update(
                $table_name,
                [
                    'title'      => $title,
                    'data'       => $data,
                    'status'     => $status,
                    'updated_at' => current_time('mysql')
                ],
                ['uid' => $uid],
                ['%s', '%s', '%s', '%s'],
                ['%s']
            );
        } else {
            $result = $wpdb->insert(
                $table_name,
                [
                    'uid'           => $uid,
                    'title'         => $title,
                    'data'          => $data,
                    'status'        => $status,
                    'impressions'   => 0,
                    'submissions'   => 0,
                    'confirmations' => 0,
                    'created_at'    => current_time('mysql'),
                    'updated_at'    => current_time('mysql')
                ],
                ['%s', '%s', '%s', '%s', '%d', '%d', '%d', '%s', '%s']
            );
        }

        if ($result === false) {
            wp_send_json_error(['message' => 'Database error: ' . ($wpdb->last_error ?: 'Could not save record.')]);
        }

        wp_send_json_success([
            'uid'     => $uid,
            'status'  => $status,
            'message' => 'Popup configuration saved successfully!'
        ]);
    }

    public function load_popup() {
        $this->verify_admin_security('wppoppop_builder_nonce');
        global $wpdb;
        $uid = isset($_REQUEST['uid']) ? sanitize_key($_REQUEST['uid']) : '';
        if (empty($uid)) {
            wp_send_json_error(['message' => 'Missing popup UID parameter.']);
        }

        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}wppoppop_items WHERE uid = %s", $uid), ARRAY_A);
        if (!$row) {
            wp_send_json_error(['message' => 'Popup configuration not found.']);
        }

        wp_send_json_success($row);
    }

    public function duplicate_popup() {
        $this->verify_admin_security('wppoppop_dash_nonce');
        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';
        $uid = isset($_POST['uid']) ? sanitize_key($_POST['uid']) : '';

        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_name} WHERE uid = %s", $uid));
        if (!$row) wp_send_json_error(['message' => 'Source popup not found.']);

        $new_uid = 'pop_' . substr(md5(uniqid(wp_rand(), true)), 0, 10);
        $wpdb->insert(
            $table_name,
            [
                'uid'           => $new_uid,
                'title'         => $row->title . ' (Copy)',
                'data'          => $row->data,
                'status'        => 'draft',
                'impressions'   => 0,
                'submissions'   => 0,
                'confirmations' => 0
            ],
            ['%s', '%s', '%s', '%s', '%d', '%d', '%d']
        );

        wp_send_json_success(['uid' => $new_uid, 'message' => 'Popup duplicated successfully!']);
    }

    public function delete_popup() {
        $this->verify_admin_security('wppoppop_dash_nonce');
        global $wpdb;
        $uid = isset($_POST['uid']) ? sanitize_key($_POST['uid']) : '';
        $wpdb->delete($wpdb->prefix . 'wppoppop_items', ['uid' => $uid], ['%s']);
        wp_send_json_success(['message' => 'Popup permanently deleted.']);
    }

    public function toggle_status() {
        $this->verify_admin_security('wppoppop_dash_nonce');
        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';
        $uid = isset($_POST['uid']) ? sanitize_key($_POST['uid']) : '';

        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_name} WHERE uid = %s", $uid));
        if (!$row) wp_send_json_error(['message' => 'Popup not found.']);

        $new_status = ($row->status === 'publish') ? 'draft' : 'publish';
        $wpdb->update($table_name, ['status' => $new_status], ['uid' => $uid], ['%s'], ['%s']);

        $is_active = ($new_status === 'publish');
        wp_send_json_success([
            'new_status'   => $new_status,
            'is_active'    => $is_active,
            'status_label' => $is_active ? 'Active' : 'Inactive',
            'action_text'  => $is_active ? 'Deactivate' : 'Activate'
        ]);
    }

    public function reset_stats() {
        $this->verify_admin_security('wppoppop_dash_nonce');
        global $wpdb;
        $uid = isset($_POST['uid']) ? sanitize_key($_POST['uid']) : '';
        $wpdb->update($wpdb->prefix . 'wppoppop_items', ['impressions' => 0, 'submissions' => 0, 'confirmations' => 0], ['uid' => $uid], ['%d', '%d', '%d'], ['%s']);
        wp_send_json_success(['message' => 'Campaign statistics reset to zero.']);
    }

    public function export_definition() {
        $uid = isset($_GET['uid']) ? sanitize_key($_GET['uid']) : '';
        $nonce = isset($_GET['nonce']) ? sanitize_text_field($_GET['nonce']) : '';

        if (!wp_verify_nonce($nonce, 'wppoppop_export_' . $uid) || !current_user_can('manage_options')) {
            wp_die('Unauthorized export request.');
        }

        global $wpdb;
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}wppoppop_items WHERE uid = %s", $uid));
        if (!$row) wp_die('Popup definition not found.');

        $payload = [
            'wppoppop_export' => true,
            'version'         => WPPOPPOP_VERSION,
            'uid'             => $row->uid,
            'title'           => $row->title,
            'data'            => json_decode($row->data, true),
            'exported_at'     => current_time('mysql')
        ];

        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename=wppoppop-definition-' . $uid . '.json');
        echo json_encode($payload, JSON_PRETTY_PRINT);
        exit;
    }

    public function export_csv() {
        $uid = isset($_GET['uid']) ? sanitize_key($_GET['uid']) : '';
        $nonce = isset($_GET['nonce']) ? sanitize_text_field($_GET['nonce']) : '';

        if (!wp_verify_nonce($nonce, 'wppoppop_csv_' . $uid) || !current_user_can('manage_options')) {
            wp_die('Unauthorized CSV export request.');
        }

        global $wpdb;
        $rows = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}wppoppop_submissions WHERE popup_uid = %s ORDER BY id DESC", $uid));

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=wppoppop-leads-' . $uid . '-' . date('Y-m-d') . '.csv');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['Lead ID', 'Popup UID', 'Submission Date', 'Lead Payload / Fields', 'User IP', 'Status']);

        if (!empty($rows)) {
            foreach ($rows as $r) {
                fputcsv($out, [$r->id, $r->popup_uid, $r->created_at, $r->fields_data, $r->user_ip, $r->status]);
            }
        }
        fclose($out);
        exit;
    }

    public function save_settings() {
        $this->verify_admin_security('wppoppop_settings_nonce');
        $raw_data = isset($_POST['data']) ? wp_unslash($_POST['data']) : '';
        $parsed = [];
        if (is_array($raw_data)) {
            $parsed = $raw_data;
        } elseif (is_string($raw_data) && !empty($raw_data)) {
            parse_str($raw_data, $parsed);
        } else {
            $parsed = $_POST;
        }

        $settings = [
            'sender_name'       => isset($parsed['sender_name']) ? sanitize_text_field($parsed['sender_name']) : 'wppoppop',
            'sender_email'      => isset($parsed['sender_email']) ? sanitize_email($parsed['sender_email']) : 'noreply@localhost',
            'preload_popups'    => !empty($parsed['preload_popups']) ? 1 : 0,
            'preload_events'    => !empty($parsed['preload_events']) ? 1 : 0,
            'ga_tracking'       => !empty($parsed['ga_tracking']) ? 1 : 0,
            'google_fonts'      => !empty($parsed['google_fonts']) ? 1 : 0,
            'font_awesome'      => !empty($parsed['font_awesome']) ? 1 : 0,
            'air_datepicker'    => !empty($parsed['air_datepicker']) ? 1 : 0,
            'no_air_datepicker' => !empty($parsed['no_air_datepicker']) ? 1 : 0,
            'jquery_mask'       => !empty($parsed['jquery_mask']) ? 1 : 0,
            'js_parser'         => !empty($parsed['js_parser']) ? 1 : 0,
            'signature_pad'     => !empty($parsed['signature_pad']) ? 1 : 0,
            'range_slider'      => !empty($parsed['range_slider']) ? 1 : 0,
            'adblock_detector'  => !empty($parsed['adblock_detector']) ? 1 : 0,
            'csv_separator'     => isset($parsed['csv_separator']) ? sanitize_text_field($parsed['csv_separator']) : ',',
            'custom_fonts'      => isset($parsed['custom_fonts']) ? sanitize_textarea_field($parsed['custom_fonts']) : '',
            'email_validation'  => isset($parsed['email_validation']) ? sanitize_text_field($parsed['email_validation']) : 'basic',
            'geoip_service'     => isset($parsed['geoip_service']) ? sanitize_text_field($parsed['geoip_service']) : 'none',
            'user_uploads'      => isset($parsed['user_uploads']) ? sanitize_text_field($parsed['user_uploads']) : 'keep',
            'custom_css'        => isset($parsed['custom_css']) ? sanitize_textarea_field($parsed['custom_css']) : '',
            'custom_js'         => isset($parsed['custom_js']) ? sanitize_textarea_field($parsed['custom_js']) : ''
        ];

        update_option('wppoppop_settings', $settings);
        wp_send_json_success(['message' => 'Settings saved successfully!']);
    }
}
