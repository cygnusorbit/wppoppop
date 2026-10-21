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
        add_action('wp_ajax_wppoppop_get_popup_fields', [$this, 'get_popup_fields']);
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

        $per_page = isset($raw['per_page']) ? intval($raw['per_page']) : 10;
        $per_page = ($per_page <= 0) ? 10 : ($per_page > 25 ? 30 : ($per_page > 15 ? 20 : 10)); // whitelist: 10 / 20 / 30

        $all = get_option('wppoppop_sub_table_options', []);
        if (!is_array($all)) {
            $all = [];
        }
        $all[$key] = ['labels' => $labels, 'columns' => $columns, 'per_page' => $per_page];
        update_option('wppoppop_sub_table_options', $all, false);

        wp_send_json_success(['saved' => $key]);
    }


    public function get_popup_fields() {
        $this->verify_nonce();
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized'], 403);
        }
        $uid = isset($_POST['popup_uid']) ? sanitize_text_field(wp_unslash($_POST['popup_uid'])) : '';
        global $wpdb;
        $t = $wpdb->prefix . 'wppoppop_items';
        $out = [];
        if ($uid !== '') {
            $raw = $wpdb->get_var($wpdb->prepare("SELECT data FROM {$t} WHERE uid = %s", $uid));
            $out = $this->extract_form_fields($raw ? json_decode($raw, true) : null);
        } else {
            $seen = [];
            foreach ((array) $wpdb->get_results("SELECT data FROM {$t}") as $r) {
                foreach ($this->extract_form_fields($r->data ? json_decode($r->data, true) : null) as $id => $lbl) {
                    $lc = strtolower($lbl);
                    if (isset($seen[$lc])) {
                        continue;   // same label across popups = one column
                    }
                    $seen[$lc] = true;
                    $out[$id] = $lbl;
                }
            }
        }
        wp_send_json_success(['fields' => empty($out) ? new stdClass() : $out]);
    }

    private function extract_form_fields($cfg) {
        $out = [];
        if (!is_array($cfg)) {
            return $out;
        }
        $types = ['text', 'textfield', 'email', 'number', 'phone', 'textarea', 'select', 'dropdown', 'checkbox', 'checkboxes',
            'radio', 'radios', 'checkboxgroup', 'multiselect', 'imageselect', 'tile', 'date', 'time', 'upload', 'file', 'password', 'rating', 'slider', 'signature'];
        $cvs = isset($cfg['canvases']) ? $cfg['canvases'] : (isset($cfg['screens']) ? $cfg['screens'] : []);
        if (empty($cvs) && !empty($cfg['elements']) && is_array($cfg['elements'])) {
            $cvs = [$cfg['elements']];
        }
        if (!is_array($cvs)) {
            return $out;
        }
        foreach ($cvs as $cv) {
            $els = (is_array($cv) && isset($cv['elements']) && is_array($cv['elements'])) ? $cv['elements'] : $cv;
            if (!is_array($els)) {
                continue;
            }
            foreach ($els as $el) {
                if (!is_array($el) || empty($el['type'])) {
                    continue;
                }
                $type = strtolower(trim((string) $el['type']));
                if (!in_array($type, $types, true)) {
                    continue;
                }
                if (isset($el['saveToDb']) && ($el['saveToDb'] === false || $el['saveToDb'] === 'false' || $el['saveToDb'] === 0 || $el['saveToDb'] === '0')) {
                    continue;
                }
                $key = '';
                foreach (['fieldName', 'token', 'field_name', 'fieldId'] as $p) {
                    if (!empty($el[$p])) {
                        $key = sanitize_key((string) $el[$p]);
                        if ($key !== '') {
                            break;
                        }
                    }
                }
                if ($key === '') {
                    $key = sanitize_key($type);
                }
                if ($key === '' || isset($out['field:' . $key]) || !preg_match('/^[A-Za-z0-9_\\-\\. ]{1,64}$/', $key)) {
                    continue;
                }
                $lbl = '';
                foreach (['name', 'label_text', 'label', 'placeholder'] as $p) {
                    if (!empty($el[$p]) && is_string($el[$p])) {
                        $lbl = trim(wp_strip_all_tags($el[$p]));
                        if ($lbl !== '') {
                            break;
                        }
                    }
                }
                if ($lbl === '') {
                    $lbl = ucwords(str_replace(['_', '-'], ' ', $key));
                }
                $out['field:' . $key] = mb_substr($lbl, 0, 60);
            }
        }
        return $out;
    }

    public function assign_sub_labels() {
        $this->verify_nonce();
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized'], 403);
        }

        $ids   = isset($_POST['ids']) && is_array($_POST['ids']) ? array_map('intval', wp_unslash($_POST['ids'])) : [];
        $label = isset($_POST['label_id']) ? preg_replace('/[^A-Za-z0-9_\-]/', '', (string) wp_unslash($_POST['label_id'])) : '';
        $mode  = isset($_POST['mode']) ? sanitize_key(wp_unslash($_POST['mode'])) : 'add';
        if (!in_array($mode, ['add', 'remove', 'clear'], true)) {
            $mode = 'add';
        }
        $ids   = array_slice(array_filter($ids), 0, 500);
        if (empty($ids) || ($mode !== 'clear' && $label === '')) {
            wp_send_json_error(['message' => 'Nothing to update.']);
        }

        $map = get_option('wppoppop_sub_label_map', []);
        if (!is_array($map)) {
            $map = [];
        }
        foreach ($ids as $id) {
            if ($mode === 'clear') {
                unset($map[$id]);
            } elseif ($mode === 'remove') {
                $cur = isset($map[$id]) && is_array($map[$id]) ? $map[$id] : [];
                $cur = array_values(array_diff($cur, [$label]));
                if (empty($cur)) {
                    unset($map[$id]);
                } else {
                    $map[$id] = $cur;
                }
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
