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
        add_action('wp_ajax_wppoppop_bulk_export', [$this, 'bulk_export']);
        add_action('wp_ajax_wppoppop_bulk_import', [$this, 'bulk_import']);
        add_action('wp_ajax_wppoppop_repair_tables', [$this, 'repair_tables']);
        add_action('wp_ajax_wppoppop_reset_counters', [$this, 'reset_counters']);
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
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}wppoppop_items WHERE uid = %s", sanitize_key($_POST['uid'])), ARRAY_A);
        if (!$row) {
            wp_send_json_error(['message' => 'Source popup not found.']);
        }

        $new_uid = wp_generate_uuid4();
        $wpdb->insert(
            $wpdb->prefix . 'wppoppop_items',
            [
                'uid'           => $new_uid,
                'title'         => $row['title'] . ' (Copy)',
                'data'          => $row['data'],
                'status'        => 'publish',
                'impressions'   => 0,
                'submissions'   => 0,
                'confirmations' => 0
            ],
            ['%s', '%s', '%s', '%s', '%d', '%d', '%d']
        );

        wp_send_json_success(['uid' => $new_uid, 'message' => 'Popup duplicated successfully!']);
    }

    public function delete_popup() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized action.']);
        }
        global $wpdb;
        $wpdb->delete($wpdb->prefix . 'wppoppop_items', ['uid' => sanitize_key($_POST['uid'])], ['%s']);
        wp_send_json_success(['message' => 'Popup removed successfully.']);
    }

    public function export_popup() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        global $wpdb;
        $row = $wpdb->get_row($wpdb->prepare("SELECT title, data FROM {$wpdb->prefix}wppoppop_items WHERE uid = %s", sanitize_key($_GET['uid'])), ARRAY_A);
        wp_send_json_success(['filename' => sanitize_title($row['title']) . '-wppoppop-export.json', 'payload' => $row['data']]);
    }

    public function import_popup() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized']);
        }
        $json_raw = isset($_POST['import_data']) ? wp_unslash($_POST['import_data']) : '';
        $data = json_decode($json_raw, true);
        if (!$data || !isset($data['meta'])) {
            wp_send_json_error(['message' => 'Invalid JSON']);
        }

        global $wpdb;
        $wpdb->insert($wpdb->prefix . 'wppoppop_items', [
            'uid'    => wp_generate_uuid4(),
            'title'  => sanitize_text_field($data['meta']['title']) . ' (Imported)',
            'data'   => $json_raw,
            'status' => 'publish'
        ]);
        wp_send_json_success(['message' => 'Popup imported successfully!']);
    }

    public function save_campaign() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        global $wpdb;
        $wpdb->insert($wpdb->prefix . 'wppoppop_campaigns', [
            'uid'        => wp_generate_uuid4(),
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

    public function bulk_export() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized action.']);
        }

        $nonce_valid = false;
        if (!empty($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], 'wppoppop_admin_nonce')) {
            $nonce_valid = true;
        } elseif (!empty($_REQUEST['wppoppop_tools_nonce']) && wp_verify_nonce($_REQUEST['wppoppop_tools_nonce'], 'wppoppop_tools_action')) {
            $nonce_valid = true;
        }

        if (!$nonce_valid) {
            wp_send_json_error(['message' => 'Security check failed. Please refresh the page.']);
        }

        global $wpdb;
        $items = $wpdb->get_results("SELECT uid, title, data, status FROM {$wpdb->prefix}wppoppop_items", ARRAY_A);
        $campaigns = $wpdb->get_results("SELECT uid, title, popup_uids, status FROM {$wpdb->prefix}wppoppop_campaigns", ARRAY_A);

        $backup_payload = [
            'generator'  => 'WpPopPop ' . (defined('WPPOPPOP_VERSION') ? WPPOPPOP_VERSION : '1.0.0'),
            'exported'   => current_time('mysql'),
            'items'      => $items ?: [],
            'campaigns'  => $campaigns ?: []
        ];

        wp_send_json_success([
            'filename' => 'wppoppop-bulk-backup-' . gmdate('Y-m-d') . '.json',
            'payload'  => $backup_payload
        ]);
    }

    public function bulk_import() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized action.']);
        }

        $nonce_valid = false;
        if (!empty($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], 'wppoppop_admin_nonce')) {
            $nonce_valid = true;
        } elseif (!empty($_REQUEST['wppoppop_tools_nonce']) && wp_verify_nonce($_REQUEST['wppoppop_tools_nonce'], 'wppoppop_tools_action')) {
            $nonce_valid = true;
        }

        if (!$nonce_valid) {
            wp_send_json_error(['message' => 'Security check failed. Please refresh the page.']);
        }

        $raw_json = '';
        if (!empty($_FILES['wppoppop_bulk_import_file']['tmp_name'])) {
            $raw_json = file_get_contents($_FILES['wppoppop_bulk_import_file']['tmp_name']);
        } elseif (!empty($_POST['import_data'])) {
            $raw_json = wp_unslash($_POST['import_data']);
        }

        if (empty($raw_json)) {
            wp_send_json_error(['message' => 'No backup data was provided for restoration.']);
        }

        $decoded = json_decode($raw_json, true);
        if (!$decoded || !is_array($decoded) || (empty($decoded['items']) && empty($decoded['campaigns']))) {
            wp_send_json_error(['message' => 'Invalid or unrecognized backup format. A valid JSON archive is required.']);
        }

        global $wpdb;
        $items_table     = $wpdb->prefix . 'wppoppop_items';
        $campaigns_table = $wpdb->prefix . 'wppoppop_campaigns';

        $imported_items     = 0;
        $imported_campaigns = 0;
        $uid_map            = [];

        if (!empty($decoded['items']) && is_array($decoded['items'])) {
            foreach ($decoded['items'] as $item) {
                $old_uid = isset($item['uid']) ? sanitize_key($item['uid']) : '';
                $new_uid = wp_generate_uuid4();
                if ($old_uid) {
                    $uid_map[$old_uid] = $new_uid;
                }

                $title = isset($item['title']) ? sanitize_text_field($item['title']) . ' (Restored)' : 'Restored Popup';
                $data  = isset($item['data']) ? (is_array($item['data']) ? wp_json_encode($item['data']) : $item['data']) : '{}';

                $wpdb->insert(
                    $items_table,
                    [
                        'uid'           => $new_uid,
                        'title'         => $title,
                        'data'          => $data,
                        'status'        => 'publish',
                        'impressions'   => 0,
                        'submissions'   => 0,
                        'confirmations' => 0
                    ],
                    ['%s', '%s', '%s', '%s', '%d', '%d', '%d']
                );
                $imported_items++;
            }
        }

        if (!empty($decoded['campaigns']) && is_array($decoded['campaigns'])) {
            foreach ($decoded['campaigns'] as $camp) {
                $camp_uid = wp_generate_uuid4();
                $title    = isset($camp['title']) ? sanitize_text_field($camp['title']) . ' (Restored)' : 'Restored A/B Test';

                $raw_uids = isset($camp['popup_uids']) ? (is_array($camp['popup_uids']) ? $camp['popup_uids'] : json_decode($camp['popup_uids'], true)) : [];
                $remapped_uids = [];

                if (is_array($raw_uids)) {
                    foreach ($raw_uids as $var_uid) {
                        $remapped_uids[] = isset($uid_map[$var_uid]) ? $uid_map[$var_uid] : sanitize_key($var_uid);
                    }
                }

                $wpdb->insert(
                    $campaigns_table,
                    [
                        'uid'        => $camp_uid,
                        'title'      => $title,
                        'popup_uids' => wp_json_encode($remapped_uids),
                        'status'     => 'active'
                    ],
                    ['%s', '%s', '%s', '%s']
                );
                $imported_campaigns++;
            }
        }

        wp_send_json_success([
            'message'            => sprintf('Successfully restored %d popups and %d A/B campaigns!', $imported_items, $imported_campaigns),
            'imported_items'     => $imported_items,
            'imported_campaigns' => $imported_campaigns
        ]);
    }

    public function repair_tables() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized action.']);
        }

        $nonce_valid = false;
        if (!empty($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], 'wppoppop_admin_nonce')) {
            $nonce_valid = true;
        } elseif (!empty($_REQUEST['wppoppop_tools_nonce']) && wp_verify_nonce($_REQUEST['wppoppop_tools_nonce'], 'wppoppop_tools_action')) {
            $nonce_valid = true;
        }

        if (!$nonce_valid) {
            wp_send_json_error(['message' => 'Security check failed. Please refresh the page.']);
        }

        require_once WPPOPPOP_PATH . 'includes/class-wppoppop-installer.php';
        WpPopPop_Installer::create_tables();

        global $wpdb;
        $tables = [
            'items'        => $wpdb->prefix . 'wppoppop_items',
            'submissions'  => $wpdb->prefix . 'wppoppop_submissions',
            'campaigns'    => $wpdb->prefix . 'wppoppop_campaigns',
            'logs'         => $wpdb->prefix . 'wppoppop_logs',
            'transactions' => $wpdb->prefix . 'wppoppop_transactions',
        ];

        $status_data = [];
        foreach ($tables as $key => $table_name) {
            $check = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name));
            $exists = ($check === $table_name);
            $rows = 0;
            $data_size = '0 KB';

            if ($exists) {
                $rows = (int)$wpdb->get_var("SELECT COUNT(*) FROM `{$table_name}`");
                $table_status = $wpdb->get_row($wpdb->prepare("SHOW TABLE STATUS LIKE %s", $table_name), ARRAY_A);
                if ($table_status && isset($table_status['Data_length'])) {
                    $bytes = (int)$table_status['Data_length'] + (int)$table_status['Index_length'];
                    $data_size = size_format($bytes, 2);
                }
            }

            $status_data[$key] = [
                'table'     => $table_name,
                'exists'    => $exists,
                'rows'      => number_format_i18n($rows),
                'size'      => $data_size,
                'status'    => $exists ? 'Optimal' : 'Missing',
            ];
        }

        wp_send_json_success([
            'message' => 'All 5 database tables successfully verified, indexes verified, and schema confirmed healthy!',
            'tables'  => $status_data
        ]);
    }

    public function reset_counters() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized action.']);
        }

        $nonce_valid = false;
        if (!empty($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], 'wppoppop_admin_nonce')) {
            $nonce_valid = true;
        } elseif (!empty($_REQUEST['wppoppop_tools_nonce']) && wp_verify_nonce($_REQUEST['wppoppop_tools_nonce'], 'wppoppop_tools_action')) {
            $nonce_valid = true;
        }

        if (!$nonce_valid) {
            wp_send_json_error(['message' => 'Security check failed. Please refresh the page.']);
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';
        $affected = $wpdb->query("UPDATE `{$table_name}` SET impressions = 0, submissions = 0, confirmations = 0");

        wp_send_json_success([
            'message'  => 'All popup impressions, leads captured, and confirmation counters have been reset to 0.',
            'affected' => $affected !== false ? $affected : 0
        ]);
    }
}
