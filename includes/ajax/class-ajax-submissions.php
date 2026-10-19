<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Ajax_Submissions {
    public function __construct() {
        add_action('wp_ajax_wppoppop_export_submissions_csv', [$this, 'export_submissions_csv']);
        add_action('wp_ajax_wppoppop_delete_submission', [$this, 'delete_submission']);
        add_action('wp_ajax_wppoppop_anonymize_submission', [$this, 'anonymize_submission']);
        add_action('wp_ajax_wppoppop_save_table_options', [$this, 'save_table_options']);
        add_action('wp_ajax_wppoppop_assign_sub_labels', [$this, 'assign_sub_labels']);
        add_action('wp_ajax_wppoppop_mark_submission_read', [$this, 'mark_submission_read']);
        add_action('wp_ajax_wppoppop_mark_submission_unread', [$this, 'mark_submission_unread']);
    }

    public function save_table_options() {
        $this->verify_nonce();
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized'], 403);
        }

        $uid = isset($_POST['popup_uid']) ? sanitize_text_field(wp_unslash($_POST['popup_uid'])) : '';
        $key = ($uid !== '') ? $uid : '_default';
        $raw = isset($_POST['config']) ? json_decode(wp_unslash($_POST['config']), true) : null;
        if (!is_array($raw)) {
            wp_send_json_error(['message' => 'Invalid payload.']);
        }

        // Whitelist: labels
        $labels = [];
        if (!empty($raw['labels']) && is_array($raw['labels'])) {
            foreach (array_slice($raw['labels'], 0, 30) as $l) {
                $id   = isset($l['id']) ? preg_replace('/[^A-Za-z0-9_\-]/', '', (string) $l['id']) : '';
                $name = isset($l['name']) ? mb_substr(sanitize_text_field((string) $l['name']), 0, 40) : '';
                $col  = isset($l['color']) ? sanitize_hex_color((string) $l['color']) : '';
                if ($id === '' || $name === '') {
                    continue;
                }
                $labels[] = ['id' => $id, 'name' => $name, 'color' => $col ? $col : '#f0c330'];
            }
        }

        // Whitelist: columns
        $builtin = ['date', 'popup', 'email', 'country', 'status'];
        $columns = [];
        if (!empty($raw['columns']) && is_array($raw['columns'])) {
            foreach (array_slice($raw['columns'], 0, 40) as $c) {
                $c = sanitize_text_field((string) $c);
                if (in_array($c, $builtin, true) || preg_match('/^field:[A-Za-z0-9_\-\. ]{1,64}$/', $c)) {
                    if (!in_array($c, $columns, true)) {
                        $columns[] = $c;
                    }
                }
            }
        }
        if (empty($columns)) {
            $columns = ['date', 'email', 'popup'];
        }

        $per_page = isset($raw['per_page']) ? intval($raw['per_page']) : 20;
        $per_page = max(5, min(200, $per_page));

        $all = get_option('wppoppop_sub_table_options', []);
        if (!is_array($all)) {
            $all = [];
        }
        $all[$key] = ['labels' => $labels, 'columns' => $columns, 'per_page' => $per_page];
        update_option('wppoppop_sub_table_options', $all, false);

        wp_send_json_success(['saved' => $key]);
    }

    public function assign_sub_labels() {
        $this->verify_nonce();
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized'], 403);
        }

        $ids   = isset($_POST['ids']) && is_array($_POST['ids']) ? array_map('intval', wp_unslash($_POST['ids'])) : [];
        $label = isset($_POST['label_id']) ? preg_replace('/[^A-Za-z0-9_\-]/', '', (string) wp_unslash($_POST['label_id'])) : '';
        $mode  = (isset($_POST['mode']) && $_POST['mode'] === 'clear') ? 'clear' : 'add';
        $ids   = array_slice(array_filter($ids), 0, 500);
        if (empty($ids) || ($mode === 'add' && $label === '')) {
            wp_send_json_error(['message' => 'Nothing to update.']);
        }

        $map = get_option('wppoppop_sub_label_map', []);
        if (!is_array($map)) {
            $map = [];
        }
        foreach ($ids as $id) {
            if ($mode === 'clear') {
                unset($map[$id]);
            } else {
                $cur = isset($map[$id]) && is_array($map[$id]) ? $map[$id] : [];
                if (!in_array($label, $cur, true)) {
                    $cur[] = $label;
                }
                $map[$id] = $cur;
            }
        }
        update_option('wppoppop_sub_label_map', $map, false);
        wp_send_json_success(['updated' => count($ids)]);
    }

    private function verify_nonce() {
        $n = isset($_REQUEST['nonce']) ? sanitize_text_field(wp_unslash($_REQUEST['nonce'])) : '';
        if (!wp_verify_nonce($n, 'wppoppop_builder_nonce') && !wp_verify_nonce($n, 'wppoppop_admin_nonce')) {
            wp_send_json_error(['message' => 'Security verification failed.'], 403);
        }
    }

    public function export_submissions_csv() {
        $this->verify_nonce();
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }

        global $wpdb;
        $rows = $wpdb->get_results("SELECT s.id, i.title as popup_title, s.email, s.country_code, s.status, s.fields_data, s.created_at 
            FROM {$wpdb->prefix}wppoppop_submissions s LEFT JOIN {$wpdb->prefix}wppoppop_items i ON s.popup_uid = i.uid ORDER BY s.id DESC", ARRAY_A);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=wppoppop-submissions-' . date('Y-m-d') . '.csv');
        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'Popup', 'Email', 'Country', 'Status', 'Form Fields', 'Date']);
        foreach ($rows as $row) {
            fputcsv($output, [$row['id'], $row['popup_title'], $row['email'], $row['country_code'], ucfirst($row['status']), $row['fields_data'], $row['created_at']]);
        }
        fclose($output);
        exit;
    }

    public function mark_submission_read() {
        $n = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
        if (!wp_verify_nonce($n, 'wppoppop_builder_nonce') && !wp_verify_nonce($n, 'wppoppop_admin_nonce')) {
            wp_send_json_error(['message' => 'Security verification failed.'], 403);
        }
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized action.'], 403);
        }
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        if ($id > 0) {
            global $wpdb;
            $wpdb->update($wpdb->prefix . 'wppoppop_submissions', ['is_read' => 1], ['id' => $id], ['%d'], ['%d']);
        }
        wp_send_json_success(['id' => $id]);
    }

    public function mark_submission_unread() {
        $n = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
        if (!wp_verify_nonce($n, 'wppoppop_builder_nonce') && !wp_verify_nonce($n, 'wppoppop_admin_nonce')) {
            wp_send_json_error(['message' => 'Security verification failed.'], 403);
        }
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized action.'], 403);
        }
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        if ($id <= 0) {
            wp_send_json_error(['message' => 'Invalid submission.']);
        }
        global $wpdb;
        $ok = $wpdb->update($wpdb->prefix . 'wppoppop_submissions', ['is_read' => 0], ['id' => $id], ['%d'], ['%d']);
        if ($ok === false) {
            wp_send_json_error(['message' => 'Database error while saving.']);
        }
        wp_send_json_success(['id' => $id]);
    }

    public function delete_submission() {
        $this->verify_nonce();
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized action.']);
        }
        global $wpdb;
        $wpdb->delete($wpdb->prefix . 'wppoppop_submissions', ['id' => intval($_POST['id'])]);
        wp_send_json_success(['message' => 'Record permanently deleted.']);
    }

    public function anonymize_submission() {
        $this->verify_nonce();
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized action.']);
        }
        $id = intval($_POST['id']);
        global $wpdb;
        $wpdb->update($wpdb->prefix . 'wppoppop_submissions', ['email' => 'anonymized_' . $id . '@privacy.local', 'fields_data' => wp_json_encode(['gdpr' => 'anonymized'])], ['id' => $id]);
        wp_send_json_success(['message' => 'PII anonymized.']);
    }
}
