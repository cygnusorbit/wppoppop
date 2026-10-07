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
        add_action('wp_ajax_wppoppop_bulk_delete', [$this, 'bulk_delete']);
        add_action('wp_ajax_wppoppop_bulk_status', [$this, 'bulk_status']);
        add_action('wp_ajax_wppoppop_bulk_duplicate', [$this, 'bulk_duplicate']);
        add_action('wp_ajax_wppoppop_bulk_export_selected', [$this, 'bulk_export_selected']);
        add_action('wp_ajax_wppoppop_export_popup', [$this, 'export_popup']);
        add_action('wp_ajax_wppoppop_import_popup', [$this, 'import_popup']);
        add_action('wp_ajax_wppoppop_quick_edit', [$this, 'quick_edit']);
        add_action('wp_ajax_wppoppop_save_campaign', [$this, 'save_campaign']);
        add_action('wp_ajax_wppoppop_delete_campaign', [$this, 'delete_campaign']);
        add_action('wp_ajax_wppoppop_bulk_export', [$this, 'bulk_export']);
        add_action('wp_ajax_wppoppop_bulk_import', [$this, 'bulk_import']);
        add_action('wp_ajax_wppoppop_repair_tables', [$this, 'repair_tables']);
        add_action('wp_ajax_wppoppop_reset_counters', [$this, 'reset_counters']);
    }

    private function verify_security() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Unauthorized action. Administrator access required.', 'wppoppop')], 403);
        }
        $nonce = '';
        if (!empty($_POST['nonce'])) {
            $nonce = sanitize_text_field(wp_unslash($_POST['nonce']));
        } elseif (!empty($_REQUEST['nonce'])) {
            $nonce = sanitize_text_field(wp_unslash($_REQUEST['nonce']));
        }

        $valid = wp_verify_nonce($nonce, 'wppoppop_admin_nonce') || wp_verify_nonce($nonce, 'wppoppop_builder_nonce');
        if (!$valid) {
            wp_send_json_error(['message' => __('Security check failed (nonce mismatch). Please refresh the page.', 'wppoppop')], 403);
        }
    }

    public function delete_popup() {
        $this->verify_security();

        $uid = !empty($_POST['uid']) ? sanitize_text_field(wp_unslash($_POST['uid'])) : '';
        if (empty($uid)) {
            wp_send_json_error(['message' => __('Missing campaign identifier.', 'wppoppop')], 400);
        }

        global $wpdb;
        $items_table = $wpdb->prefix . 'wppoppop_items';

        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$items_table} WHERE uid = %s", $uid));
        if (!$row && is_numeric($uid)) {
            $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$items_table} WHERE id = %d", (int) $uid));
            if ($row) {
                $uid = $row->uid;
            }
        }

        if (!$row) {
            wp_send_json_error(['message' => __('Campaign not found or already deleted.', 'wppoppop')], 404);
        }

        $deleted = $wpdb->delete($items_table, ['uid' => $uid]);
        if ($deleted === false) {
            wp_send_json_error(['message' => __('Database error deleting campaign.', 'wppoppop')], 500);
        }

        // Clean up associated submissions
        $subs_table = $wpdb->prefix . 'wppoppop_submissions';
        if ($wpdb->get_var("SHOW TABLES LIKE '{$subs_table}'") === $subs_table) {
            $wpdb->delete($subs_table, ['popup_uid' => $uid]);
        }

        wp_send_json_success([
            'message' => __('Campaign deleted successfully.', 'wppoppop'),
            'uid'     => $uid
        ]);
    }

    public function bulk_delete() {
        $this->verify_security();

        $uids = isset($_POST['uids']) && is_array($_POST['uids']) ? array_map('sanitize_text_field', wp_unslash($_POST['uids'])) : [];
        if (empty($uids)) {
            wp_send_json_error(['message' => __('No campaigns selected.', 'wppoppop')], 400);
        }

        global $wpdb;
        $items_table = $wpdb->prefix . 'wppoppop_items';
        $subs_table  = $wpdb->prefix . 'wppoppop_submissions';

        foreach ($uids as $uid) {
            $wpdb->delete($items_table, ['uid' => $uid]);
            if ($wpdb->get_var("SHOW TABLES LIKE '{$subs_table}'") === $subs_table) {
                $wpdb->delete($subs_table, ['popup_uid' => $uid]);
            }
        }

        wp_send_json_success(['message' => __('Selected campaigns deleted successfully.', 'wppoppop')]);
    }

    public function quick_edit() {
        $this->verify_security();

        $uid    = isset($_POST['uid']) ? sanitize_text_field(wp_unslash($_POST['uid'])) : '';
        $title  = isset($_POST['title']) ? sanitize_text_field(wp_unslash($_POST['title'])) : '';
        $status = isset($_POST['status']) ? sanitize_key($_POST['status']) : 'draft';

        if (empty($uid) || empty($title)) {
            wp_send_json_error(['message' => __('Title and UID are required.', 'wppoppop')], 400);
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_name} WHERE uid = %s", $uid));

        if (!$row) {
            wp_send_json_error(['message' => __('Campaign not found.', 'wppoppop')], 404);
        }

        $payload = !empty($row->data) ? json_decode($row->data, true) : [];
        if (!is_array($payload)) {
            $payload = [];
        }
        if (!isset($payload['meta']) || !is_array($payload['meta'])) {
            $payload['meta'] = [];
        }
        $payload['meta']['title'] = $title;

        $wpdb->update(
            $table_name,
            [
                'title'      => $title,
                'status'     => $status,
                'data'       => wp_json_encode($payload),
                'updated_at' => current_time('mysql'),
            ],
            ['uid' => $uid],
            ['%s', '%s', '%s', '%s'],
            ['%s']
        );

        wp_send_json_success([
            'uid'    => $uid,
            'title'  => $title,
            'status' => $status,
        ]);
    }

    public function duplicate_popup() {
        $this->verify_security();

        $uid = isset($_POST['uid']) ? sanitize_text_field(wp_unslash($_POST['uid'])) : '';
        if (empty($uid)) {
            wp_send_json_error(['message' => __('Missing campaign identifier.', 'wppoppop')], 400);
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';
        $original = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_name} WHERE uid = %s", $uid));

        if (!$original) {
            wp_send_json_error(['message' => __('Campaign not found.', 'wppoppop')], 404);
        }

        $new_uid = 'pop_' . wp_generate_password(8, false);
        $payload = !empty($original->data) ? json_decode($original->data, true) : [];
        if (is_array($payload) && isset($payload['meta'])) {
            $payload['meta']['title'] = $original->title . ' (Copy)';
        }

        $wpdb->insert($table_name, [
            'uid'           => $new_uid,
            'title'         => $original->title . ' (Copy)',
            'status'        => 'draft',
            'data'          => wp_json_encode($payload),
            'impressions'   => 0,
            'submissions'   => 0,
            'confirmations' => 0,
            'created_at'    => current_time('mysql'),
            'updated_at'    => current_time('mysql'),
        ]);

        wp_send_json_success(['new_uid' => $new_uid]);
    }

    public function bulk_status() {
        $this->verify_security();

        $status = isset($_POST['status']) ? sanitize_key($_POST['status']) : 'draft';
        $uids   = isset($_POST['uids']) && is_array($_POST['uids']) ? array_map('sanitize_text_field', wp_unslash($_POST['uids'])) : [];

        if (empty($uids)) {
            wp_send_json_error(['message' => __('No campaigns selected.', 'wppoppop')], 400);
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';
        foreach ($uids as $uid) {
            $wpdb->update($table_name, ['status' => $status, 'updated_at' => current_time('mysql')], ['uid' => $uid]);
        }

        wp_send_json_success(['message' => __('Status updated.', 'wppoppop')]);
    }

    public function bulk_duplicate() {
        $this->verify_security();

        $uids = isset($_POST['uids']) && is_array($_POST['uids']) ? array_map('sanitize_text_field', wp_unslash($_POST['uids'])) : [];
        if (empty($uids)) {
            wp_send_json_error(['message' => __('No campaigns selected.', 'wppoppop')], 400);
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';
        foreach ($uids as $uid) {
            $orig = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_name} WHERE uid = %s", $uid));
            if ($orig) {
                $new_uid = 'pop_' . wp_generate_password(8, false);
                $wpdb->insert($table_name, [
                    'uid'           => $new_uid,
                    'title'         => $orig->title . ' (Copy)',
                    'status'        => 'draft',
                    'data'          => $orig->data,
                    'impressions'   => 0,
                    'submissions'   => 0,
                    'confirmations' => 0,
                    'created_at'    => current_time('mysql'),
                    'updated_at'    => current_time('mysql'),
                ]);
            }
        }

        wp_send_json_success(['message' => __('Duplicated successfully.', 'wppoppop')]);
    }

    public function bulk_export_selected() {
        $this->verify_security();

        $uids = isset($_POST['uids']) && is_array($_POST['uids']) ? array_map('sanitize_text_field', wp_unslash($_POST['uids'])) : [];
        if (empty($uids)) {
            wp_send_json_error(['message' => __('No campaigns selected.', 'wppoppop')], 400);
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';
        $export = [];

        foreach ($uids as $uid) {
            $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_name} WHERE uid = %s", $uid), ARRAY_A);
            if ($row) {
                $row['data'] = json_decode($row['data'], true);
                $export[] = $row;
            }
        }

        wp_send_json_success([
            'payload'  => $export,
            'filename' => 'wppoppop-selected-export-' . gmdate('Y-m-d') . '.json'
        ]);
    }

    public function export_popup() {
        $this->verify_security();

        $uid = isset($_GET['uid']) ? sanitize_text_field(wp_unslash($_GET['uid'])) : '';
        if (empty($uid)) {
            wp_send_json_error(['message' => __('Missing UID.', 'wppoppop')], 400);
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_name} WHERE uid = %s", $uid), ARRAY_A);
        if (!$row) {
            wp_send_json_error(['message' => __('Not found.', 'wppoppop')], 404);
        }

        $row['data'] = json_decode($row['data'], true);
        wp_send_json_success([
            'payload'  => $row,
            'filename' => 'popup-' . $uid . '.json'
        ]);
    }

    public function import_popup() {
        $this->verify_security();

        $raw = isset($_POST['import_data']) ? wp_unslash($_POST['import_data']) : '';
        $data = json_decode($raw, true);
        if (!$data || !is_array($data)) {
            wp_send_json_error(['message' => __('Invalid JSON format.', 'wppoppop')], 400);
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';
        $title = !empty($data['title']) ? sanitize_text_field($data['title']) : 'Imported Popup';
        $new_uid = 'pop_' . wp_generate_password(8, false);

        $payload = isset($data['data']) ? $data['data'] : $data;
        $wpdb->insert($table_name, [
            'uid'           => $new_uid,
            'title'         => $title,
            'status'        => 'draft',
            'data'          => wp_json_encode($payload),
            'impressions'   => 0,
            'submissions'   => 0,
            'confirmations' => 0,
            'created_at'    => current_time('mysql'),
            'updated_at'    => current_time('mysql'),
        ]);

        wp_send_json_success(['uid' => $new_uid]);
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
            'generator' => 'WpPopPop ' . (defined('WPPOPPOP_VERSION') ? WPPOPPOP_VERSION : '1.0.0'),
            'exported'  => current_time('mysql'),
            'items'     => $items ?: [],
            'campaigns' => $campaigns ?: []
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

        wp_send_json_success(['message' => __('Tables verified and repaired.', 'wppoppop')]);
    }

    public function reset_counters() {
        $this->verify_security();
        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';
        $wpdb->query("UPDATE {$table_name} SET impressions = 0, submissions = 0, confirmations = 0");
        wp_send_json_success(['message' => __('Counters reset.', 'wppoppop')]);
    }

    public function save_popup() {
        $this->verify_security();

        $uid   = isset($_POST['uid']) ? sanitize_text_field(wp_unslash($_POST['uid'])) : '';
        $data  = isset($_POST['data']) ? wp_unslash($_POST['data']) : '';
        $title = isset($_POST['title']) ? sanitize_text_field(wp_unslash($_POST['title'])) : 'Untitled Campaign';

        if (empty($uid)) {
            $uid = 'pop_' . wp_generate_password(8, false);
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';
        $existing = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$table_name} WHERE uid = %s", $uid));

        if ($existing) {
            $wpdb->update($table_name, [
                'title'      => $title,
                'data'       => $data,
                'updated_at' => current_time('mysql')
            ], ['uid' => $uid]);
        } else {
            $wpdb->insert($table_name, [
                'uid'           => $uid,
                'title'         => $title,
                'status'        => 'publish',
                'data'          => $data,
                'impressions'   => 0,
                'submissions'   => 0,
                'confirmations' => 0,
                'created_at'    => current_time('mysql'),
                'updated_at'    => current_time('mysql')
            ]);
        }

        wp_send_json_success(['uid' => $uid]);
    }

    public function load_popup() {
        $uid = isset($_GET['uid']) ? sanitize_text_field(wp_unslash($_GET['uid'])) : '';
        if (empty($uid)) {
            wp_send_json_error(['message' => __('Missing UID.', 'wppoppop')], 400);
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_name} WHERE uid = %s", $uid));

        if (!$row) {
            wp_send_json_error(['message' => __('Popup not found.', 'wppoppop')], 404);
        }

        wp_send_json_success([
            'uid'   => $row->uid,
            'title' => $row->title,
            'data'  => $row->data
        ]);
    }
}
