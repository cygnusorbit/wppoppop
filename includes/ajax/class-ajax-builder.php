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
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');

        $uid = !empty($_POST['uid']) ? sanitize_text_field(wp_unslash($_POST['uid'])) : '';
        if (!current_user_can('edit_wppoppop_campaign', $uid)) {
            wp_send_json_error(['message' => __('You do not have permission to save this campaign.', 'wppoppop')]);
        }

        global $wpdb;
        $table_items = $wpdb->prefix . 'wppoppop_items';

        $title = !empty($_POST['title']) ? sanitize_text_field(wp_unslash($_POST['title'])) : __('Untitled Popup', 'wppoppop');
        $raw_data = !empty($_POST['data']) ? wp_unslash($_POST['data']) : '{}';
        $decoded_data = is_array($raw_data) ? $raw_data : (json_decode($raw_data, true) ?: []);

        // Filter: Allow external extensions to mutate/sanitize campaign configuration before saving
        $filtered_data = apply_filters('wppoppop_pre_save_popup_data', $decoded_data, $uid);
        $encoded_data  = wp_json_encode($filtered_data);

        $status = !empty($_POST['status']) ? sanitize_key($_POST['status']) : 'publish';
        if (!in_array($status, ['publish', 'draft', 'trash'], true)) {
            $status = 'publish';
        }

        $is_new   = false;
        $existing = null;
        if (!empty($uid)) {
            $existing = $wpdb->get_row($wpdb->prepare("SELECT id, uid FROM {$table_items} WHERE uid = %s", $uid));
        }

        if (!$existing) {
            $is_new = true;
            if (empty($uid)) {
                $uid = 'pop_' . wp_generate_password(8, false, false);
            }
        }

        // Action: Before saving popup
        do_action('wppoppop_before_save_popup', $uid, $filtered_data, $is_new);

        if ($existing) {
            $wpdb->update(
                $table_items,
                [
                    'title'  => $title,
                    'data'   => $encoded_data,
                    'status' => $status,
                ],
                ['uid' => $uid],
                ['%s', '%s', '%s'],
                ['%s']
            );
        } else {
            $wpdb->insert(
                $table_items,
                [
                    'uid'         => $uid,
                    'title'       => $title,
                    'data'        => $encoded_data,
                    'status'      => $status,
                    'impressions' => 0,
                    'submissions' => 0,
                    'created_at'  => current_time('mysql'),
                ],
                ['%s', '%s', '%s', '%s', '%d', '%d', '%s']
            );
        }

        // Action: After saving popup
        do_action('wppoppop_after_save_popup', $uid, $filtered_data, $is_new);

        wp_send_json_success([
            'message' => __('Campaign saved successfully!', 'wppoppop'),
            'uid'     => $uid,
            'is_new'  => $is_new,
        ]);
    }

    public function load_popup() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        $uid = !empty($_POST['uid']) ? sanitize_text_field(wp_unslash($_POST['uid'])) : '';

        if (!current_user_can('edit_wppoppop_campaign', $uid)) {
            wp_send_json_error(['message' => __('Unauthorized.', 'wppoppop')]);
        }

        global $wpdb;
        $table_items = $wpdb->prefix . 'wppoppop_items';
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_items} WHERE uid = %s", $uid), ARRAY_A);

        if (!$row) {
            wp_send_json_error(['message' => __('Campaign not found.', 'wppoppop')]);
        }

        $config = json_decode($row['data'], true) ?: [];
        $config = apply_filters('wppoppop_load_popup_config', $config, $uid);

        wp_send_json_success([
            'uid'    => $row['uid'],
            'title'  => $row['title'],
            'status' => $row['status'],
            'data'   => $config,
        ]);
    }

    public function duplicate_popup() {
        check_ajax_referer('wppoppop_admin_nonce', 'nonce');
        if (!current_user_can('edit_wppoppop_campaigns')) {
            wp_send_json_error(['message' => __('Unauthorized.', 'wppoppop')]);
        }

        $uid = !empty($_POST['uid']) ? sanitize_text_field(wp_unslash($_POST['uid'])) : '';
        global $wpdb;
        $table_items = $wpdb->prefix . 'wppoppop_items';
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_items} WHERE uid = %s", $uid), ARRAY_A);

        if (!$row) {
            wp_send_json_error(['message' => __('Source campaign not found.', 'wppoppop')]);
        }

        $new_uid   = 'pop_' . wp_generate_password(8, false, false);
        $new_title = sprintf(__('%s (Copy)', 'wppoppop'), $row['title']);

        $wpdb->insert(
            $table_items,
            [
                'uid'         => $new_uid,
                'title'       => $new_title,
                'data'        => $row['data'],
                'status'      => 'draft',
                'impressions' => 0,
                'submissions' => 0,
                'created_at'  => current_time('mysql'),
            ]
        );

        // Action: After duplicating popup
        do_action('wppoppop_after_duplicate_popup', $new_uid, $uid);

        wp_send_json_success(['message' => __('Campaign duplicated successfully!', 'wppoppop'), 'uid' => $new_uid]);
    }

    public function delete_popup() {
        check_ajax_referer('wppoppop_admin_nonce', 'nonce');
        $uid = !empty($_POST['uid']) ? sanitize_text_field(wp_unslash($_POST['uid'])) : '';

        if (!current_user_can('delete_wppoppop_campaign', $uid)) {
            wp_send_json_error(['message' => __('You do not have permission to delete this campaign.', 'wppoppop')]);
        }

        global $wpdb;
        $table_items = $wpdb->prefix . 'wppoppop_items';
        $table_subs  = $wpdb->prefix . 'wppoppop_submissions';

        // Action: Before permanent deletion
        do_action('wppoppop_before_delete_popup', $uid);

        $wpdb->delete($table_items, ['uid' => $uid]);
        if ($wpdb->get_var("SHOW TABLES LIKE '{$table_subs}'") === $table_subs) {
            $wpdb->delete($table_subs, ['popup_uid' => $uid]);
        }

        // Action: After permanent deletion
        do_action('wppoppop_after_delete_popup', $uid);

        wp_send_json_success(['message' => __('Campaign deleted successfully.', 'wppoppop')]);
    }

    public function export_popup() {
        check_ajax_referer('wppoppop_admin_nonce', 'nonce');
        if (!current_user_can('edit_wppoppop_campaigns')) {
            wp_send_json_error(['message' => __('Unauthorized.', 'wppoppop')]);
        }

        $uid = !empty($_POST['uid']) ? sanitize_text_field(wp_unslash($_POST['uid'])) : '';
        global $wpdb;
        $table_items = $wpdb->prefix . 'wppoppop_items';
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_items} WHERE uid = %s", $uid), ARRAY_A);

        if (!$row) {
            wp_send_json_error(['message' => __('Campaign not found.', 'wppoppop')]);
        }

        $export_payload = [
            'wppoppop_export' => true,
            'version'         => WPPOPPOP_VERSION,
            'title'           => $row['title'],
            'data'            => json_decode($row['data'], true),
        ];

        // Filter: Modify export payload prior to download
        $export_payload = apply_filters('wppoppop_export_campaign_payload', $export_payload, $uid);

        wp_send_json_success(['export' => $export_payload, 'filename' => sanitize_title($row['title']) . '-export.json']);
    }

    public function import_popup() {
        check_ajax_referer('wppoppop_admin_nonce', 'nonce');
        if (!current_user_can('edit_wppoppop_campaigns')) {
            wp_send_json_error(['message' => __('Unauthorized.', 'wppoppop')]);
        }

        $json = !empty($_POST['import_data']) ? wp_unslash($_POST['import_data']) : '';
        $data = json_decode($json, true);

        if (!$data || empty($data['data'])) {
            wp_send_json_error(['message' => __('Invalid import JSON format.', 'wppoppop')]);
        }

        // Filter: Inspect or mutate imported campaign data
        $data = apply_filters('wppoppop_pre_import_campaign_data', $data);

        global $wpdb;
        $table_items = $wpdb->prefix . 'wppoppop_items';
        $new_uid     = 'pop_' . wp_generate_password(8, false, false);
        $title       = !empty($data['title']) ? sanitize_text_field($data['title']) . ' ' . __('(Imported)', 'wppoppop') : __('Imported Popup', 'wppoppop');

        $wpdb->insert(
            $table_items,
            [
                'uid'         => $new_uid,
                'title'       => $title,
                'data'        => wp_json_encode($data['data']),
                'status'      => 'draft',
                'impressions' => 0,
                'submissions' => 0,
                'created_at'  => current_time('mysql'),
            ]
        );

        // Action: After importing campaign
        do_action('wppoppop_after_import_popup', $new_uid, $data);

        wp_send_json_success(['message' => __('Campaign imported successfully!', 'wppoppop'), 'uid' => $new_uid]);
    }

    public function save_campaign() {
        check_ajax_referer('wppoppop_admin_nonce', 'nonce');
        if (!current_user_can('edit_wppoppop_campaigns')) {
            wp_send_json_error(['message' => __('Unauthorized.', 'wppoppop')]);
        }

        global $wpdb;
        $table_camp = $wpdb->prefix . 'wppoppop_campaigns';
        $title      = !empty($_POST['title']) ? sanitize_text_field(wp_unslash($_POST['title'])) : __('Untitled A/B Experiment', 'wppoppop');
        $popups     = !empty($_POST['popups']) && is_array($_POST['popups']) ? array_map('sanitize_text_field', wp_unslash($_POST['popups'])) : [];
        $uid        = 'ab_' . wp_generate_password(8, false, false);

        $wpdb->insert(
            $table_camp,
            [
                'uid'        => $uid,
                'title'      => $title,
                'popup_uids' => wp_json_encode($popups),
                'status'     => 'active',
                'created_at' => current_time('mysql'),
            ]
        );

        // Action: After saving A/B split-test campaign
        do_action('wppoppop_after_save_ab_campaign', $uid, $title, $popups);

        wp_send_json_success(['message' => __('A/B Campaign created successfully.', 'wppoppop'), 'uid' => $uid]);
    }

    public function delete_campaign() {
        check_ajax_referer('wppoppop_admin_nonce', 'nonce');
        if (!current_user_can('delete_wppoppop_campaigns')) {
            wp_send_json_error(['message' => __('Unauthorized.', 'wppoppop')]);
        }

        $uid = !empty($_POST['uid']) ? sanitize_text_field(wp_unslash($_POST['uid'])) : '';
        global $wpdb;
        $table_camp = $wpdb->prefix . 'wppoppop_campaigns';

        // Action: Before and after A/B campaign deletion
        do_action('wppoppop_before_delete_ab_campaign', $uid);
        $wpdb->delete($table_camp, ['uid' => $uid]);
        do_action('wppoppop_after_delete_ab_campaign', $uid);

        wp_send_json_success(['message' => __('A/B Campaign removed.', 'wppoppop')]);
    }
}
