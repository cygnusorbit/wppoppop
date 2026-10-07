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
        add_action('wp_ajax_wppoppop_quick_edit', [$this, 'quick_edit']);
        add_action('wp_ajax_wppoppop_bulk_delete', [$this, 'bulk_delete']);
        add_action('wp_ajax_wppoppop_bulk_status', [$this, 'bulk_status']);
        add_action('wp_ajax_wppoppop_bulk_duplicate', [$this, 'bulk_duplicate']);
        add_action('wp_ajax_wppoppop_bulk_export_selected', [$this, 'bulk_export_selected']);
        add_action('wp_ajax_wppoppop_export_popup', [$this, 'export_popup']);
        add_action('wp_ajax_wppoppop_import_popup', [$this, 'import_popup']);
        add_action('wp_ajax_wppoppop_save_campaign', [$this, 'save_campaign']);
        add_action('wp_ajax_wppoppop_delete_campaign', [$this, 'delete_campaign']);
        add_action('wp_ajax_wppoppop_bulk_export', [$this, 'bulk_export']);
        add_action('wp_ajax_wppoppop_bulk_import', [$this, 'bulk_import']);
        add_action('wp_ajax_wppoppop_repair_tables', [$this, 'repair_tables']);
        add_action('wp_ajax_wppoppop_reset_counters', [$this, 'reset_counters']);
    }

    private function verify_security() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized action.']);
        }
        $nonce = '';
        if (!empty($_REQUEST['nonce'])) {
            $nonce = sanitize_text_field(wp_unslash($_REQUEST['nonce']));
        } elseif (!empty($_REQUEST['_ajax_nonce'])) {
            $nonce = sanitize_text_field(wp_unslash($_REQUEST['_ajax_nonce']));
        } elseif (!empty($_REQUEST['_wpnonce'])) {
            $nonce = sanitize_text_field(wp_unslash($_REQUEST['_wpnonce']));
        }

        if (!wp_verify_nonce($nonce, 'wppoppop_builder_nonce') && !wp_verify_nonce($nonce, 'wppoppop_admin_nonce')) {
            wp_send_json_error(['message' => 'Security check failed. Please refresh the page.']);
        }
    }

    public function save_popup() {
        $this->verify_security();

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
        $this->verify_security();
        global $wpdb;
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}wppoppop_items WHERE uid = %s", sanitize_key($_GET['uid'])), ARRAY_A);
        if (!$row) {
            wp_send_json_error(['message' => 'Popup not found.']);
        }
        wp_send_json_success($row);
    }

    public function duplicate_popup() {
        $this->verify_security();
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

        wp_send_json_success([
            'uid'     => $new_uid,
            'title'   => $row['title'] . ' (Copy)',
            'message' => 'Popup duplicated successfully!'
        ]);
    }

    public function delete_popup() {
        $this->verify_security();
        global $wpdb;
        $uid = isset($_POST['uid']) ? sanitize_text_field(wp_unslash($_POST['uid'])) : '';
        if (empty($uid) && isset($_GET['uid'])) {
            $uid = sanitize_text_field(wp_unslash($_GET['uid']));
        }

        if (empty($uid)) {
            wp_send_json_error(['message' => 'Missing campaign identifier.']);
        }

        $table_name = $wpdb->prefix . 'wppoppop_items';
        $deleted = $wpdb->delete($table_name, ['uid' => $uid]);

        if ($deleted === false) {
            wp_send_json_error(['message' => 'Database error while removing popup: ' . $wpdb->last_error]);
        }

        wp_send_json_success(['message' => 'Popup campaign deleted successfully.', 'uid' => $uid]);
    }

    public function quick_edit() {
        $this->verify_security();
        global $wpdb;
        $uid    = isset($_POST['uid']) ? sanitize_text_field(wp_unslash($_POST['uid'])) : '';
        $title  = isset($_POST['title']) ? sanitize_text_field(wp_unslash($_POST['title'])) : '';
        $status = isset($_POST['status']) && in_array($_POST['status'], ['publish', 'draft'], true) ? sanitize_key($_POST['status']) : 'publish';

        if (empty($uid)) {
            wp_send_json_error(['message' => 'Missing campaign UID.']);
        }

        if (empty($title)) {
            wp_send_json_error(['message' => 'Title cannot be blank.']);
        }

        $table_name = $wpdb->prefix . 'wppoppop_items';
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_name} WHERE uid = %s", $uid), ARRAY_A);
        if (!$row) {
            wp_send_json_error(['message' => 'Campaign not found.']);
        }

        // Synchronize title inside JSON configuration data if valid
        $data = $row['data'];
        $decoded = json_decode($data, true);
        if (is_array($decoded)) {
            if (!isset($decoded['meta'])) {
                $decoded['meta'] = [];
            }
            $decoded['meta']['title'] = $title;
            $data = wp_json_encode($decoded);
        }

        $updated = $wpdb->update(
            $table_name,
            [
                'title'  => $title,
                'status' => $status,
                'data'   => $data,
            ],
            ['uid' => $uid],
            ['%s', '%s', '%s'],
            ['%s']
        );

        if ($updated === false) {
            wp_send_json_error(['message' => 'Failed to update campaign in database.']);
        }

        wp_send_json_success([
            'message' => 'Campaign updated successfully.',
            'uid'     => $uid,
            'title'   => $title,
            'status'  => $status,
        ]);
    }

    public function bulk_delete() {
        $this->verify_security();
        $uids = isset($_POST['uids']) ? (array)$_POST['uids'] : [];
        if (empty($uids)) {
            wp_send_json_error(['message' => 'No popups were selected for deletion.']);
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';
        $sanitized_uids = array_map('sanitize_text_field', array_map('wp_unslash', $uids));
        $placeholders = implode(',', array_fill(0, count($sanitized_uids), '%s'));

        $deleted = $wpdb->query($wpdb->prepare("DELETE FROM {$table_name} WHERE uid IN ($placeholders)", $sanitized_uids));

        wp_send_json_success([
            'message'       => sprintf('Successfully removed %d popup campaign(s).', count($sanitized_uids)),
            'deleted_count' => $deleted
        ]);
    }

    public function bulk_status() {
        $this->verify_security();
        $uids = isset($_POST['uids']) ? (array)$_POST['uids'] : [];
        $status = isset($_POST['status']) && in_array($_POST['status'], ['publish', 'draft'], true) ? sanitize_key($_POST['status']) : '';

        if (empty($uids) || empty($status)) {
            wp_send_json_error(['message' => 'Invalid parameters for status update.']);
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';
        $sanitized_uids = array_map('sanitize_text_field', array_map('wp_unslash', $uids));
        $placeholders = implode(',', array_fill(0, count($sanitized_uids), '%s'));

        $query = $wpdb->prepare("UPDATE {$table_name} SET status = %s WHERE uid IN ($placeholders)", array_merge([$status], $sanitized_uids));
        $wpdb->query($query);

        wp_send_json_success([
            'message' => sprintf('Status updated to "%s" for %d campaign(s).', ucfirst($status), count($sanitized_uids)),
            'status'  => $status,
            'uids'    => $sanitized_uids
        ]);
    }

    public function bulk_duplicate() {
        $this->verify_security();
        $uids = isset($_POST['uids']) ? (array)$_POST['uids'] : [];
        if (empty($uids)) {
            wp_send_json_error(['message' => 'No campaigns selected for duplication.']);
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';
        $sanitized_uids = array_map('sanitize_text_field', array_map('wp_unslash', $uids));
        $placeholders = implode(',', array_fill(0, count($sanitized_uids), '%s'));

        $rows = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table_name} WHERE uid IN ($placeholders)", $sanitized_uids), ARRAY_A);
        if (empty($rows)) {
            wp_send_json_error(['message' => 'Selected campaigns were not found.']);
        }

        $duplicated_count = 0;
        foreach ($rows as $row) {
            $new_uid = wp_generate_uuid4();
            $wpdb->insert(
                $table_name,
                [
                    'uid'           => $new_uid,
                    'title'         => $row['title'] . ' (Copy)',
                    'data'          => $row['data'],
                    'status'        => 'draft',
                    'impressions'   => 0,
                    'submissions'   => 0,
                    'confirmations' => 0
                ],
                ['%s', '%s', '%s', '%s', '%d', '%d', '%d']
            );
            $duplicated_count++;
        }

        wp_send_json_success([
            'message'          => sprintf('Successfully duplicated %d campaign(s) as draft.', $duplicated_count),
            'duplicated_count' => $duplicated_count
        ]);
    }

    public function bulk_export_selected() {
        $this->verify_security();
        $uids = isset($_POST['uids']) ? (array)$_POST['uids'] : [];
        if (empty($uids)) {
            wp_send_json_error(['message' => 'No campaigns selected for export.']);
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';
        $sanitized_uids = array_map('sanitize_text_field', array_map('wp_unslash', $uids));
        $placeholders = implode(',', array_fill(0, count($sanitized_uids), '%s'));

        $items = $wpdb->get_results($wpdb->prepare("SELECT uid, title, data, status FROM {$table_name} WHERE uid IN ($placeholders)", $sanitized_uids), ARRAY_A);

        $backup_payload = [
            'generator' => 'WpPopPop ' . (defined('WPPOPPOP_VERSION') ? WPPOPPOP_VERSION : '1.0.0'),
            'exported'  => current_time('mysql'),
            'count'     => count($items),
            'items'     => $items ?: []
        ];

        wp_send_json_success([
            'filename' => 'wppoppop-selected-export-' . gmdate('Y-m-d') . '.json',
            'payload'  => $backup_payload
        ]);
    }

    public function export_popup() {
        $this->verify_security();
        global $wpdb;
        $row = $wpdb->get_row($wpdb->prepare("SELECT title, data FROM {$wpdb->prefix}wppoppop_items WHERE uid = %s", sanitize_key($_GET['uid'])), ARRAY_A);
        if (!$row) {
            wp_send_json_error(['message' => 'Popup not found.']);
        }
        wp_send_json_success(['filename' => sanitize_title($row['title']) . '-wppoppop-export.json', 'payload' => $row['data']]);
    }

    public function import_popup() {
        $this->verify_security();
        $json_raw = isset($_POST['import_data']) ? wp_unslash($_POST['import_data']) : '';
        $data = json_decode($json_raw, true);
        if (!$data || !isset($data['meta'])) {
            wp_send_json_error(['message' => 'Invalid JSON definition.']);
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
        $this->verify_security();
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
        $this->verify_security();
        global $wpdb;
        $wpdb->delete($wpdb->prefix . 'wppoppop_campaigns', ['uid' => sanitize_key($_POST['uid'])]);
        wp_send_json_success(['message' => 'Campaign deleted.']);
    }

    public function bulk_export() {
        $this->verify_security();
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
        $this->verify_security();
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
            wp_send_json_error(['message' => 'Invalid or unrecognized backup format.']);
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
        $this->verify_security();
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
            'message' => 'All 5 database tables successfully verified and schema confirmed healthy!',
            'tables'  => $status_data
        ]);
    }

    public function reset_counters() {
        $this->verify_security();
        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';
        $affected = $wpdb->query("UPDATE `{$table_name}` SET impressions = 0, submissions = 0, confirmations = 0");

        wp_send_json_success([
            'message'  => 'All popup impressions, leads captured, and confirmation counters have been reset to 0.',
            'affected' => $affected !== false ? $affected : 0
        ]);
    }
}
