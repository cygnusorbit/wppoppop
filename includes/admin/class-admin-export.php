<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Admin_Export {
    public function __construct() {
        add_action('admin_post_wppoppop_export_submissions', [$this, 'export_submissions_csv']);
        add_action('wp_ajax_wppoppop_export_submissions', [$this, 'export_submissions_csv']);
    }

    private function csv_safe($v) {
        if (is_array($v)) {
            $v = implode(', ', $v);
        }
        $v = (string) $v;
        if ($v !== '' && strpos("=+-@\t\r", $v[0]) !== false) {
            $v = "'" . $v;
        }
        return $v;
    }

    private function normalise_key($k) {
        $k = (string) $k;
        if (strpos($k, 'fields_') === 0) {
            return substr($k, 7);
        }
        if (strpos($k, 'fields') === 0 && strlen($k) > 6) {
            return substr($k, 6);
        }
        return $k;
    }

    public function export_submissions_csv() {
        if (!current_user_can('manage_options')) {
            wp_die(__('Unauthorized capability.', 'wppoppop'));
        }

        $nonce = isset($_REQUEST['nonce']) ? sanitize_text_field(wp_unslash($_REQUEST['nonce'])) : '';
        if (!wp_verify_nonce($nonce, 'wppoppop_export_nonce')
            && !wp_verify_nonce($nonce, 'wppoppop_admin_nonce')
            && !wp_verify_nonce($nonce, 'wppoppop_builder_nonce')) {
            wp_die(__('Security verification failed.', 'wppoppop'));
        }

        global $wpdb;
        $table = $wpdb->prefix . 'wppoppop_submissions';
        $items = $wpdb->prefix . 'wppoppop_items';
        $popup_uid = isset($_REQUEST['popup_uid']) ? sanitize_text_field(wp_unslash($_REQUEST['popup_uid'])) : '';

        if ($popup_uid !== '') {
            $rows = $wpdb->get_results($wpdb->prepare(
                "SELECT s.*, i.title AS popup_title FROM {$table} s LEFT JOIN {$items} i ON s.popup_uid = i.uid WHERE s.popup_uid = %s ORDER BY s.id DESC LIMIT 5000",
                $popup_uid
            ));
        } else {
            $rows = $wpdb->get_results("SELECT s.*, i.title AS popup_title FROM {$table} s LEFT JOIN {$items} i ON s.popup_uid = i.uid ORDER BY s.id DESC LIMIT 5000");
        }

        $csv_sep   = wppoppop_get_setting('csv_separator', ',');
        $delimiter = ',';
        if ($csv_sep === ';') {
            $delimiter = ';';
        } elseif ($csv_sep === 'tab' || $csv_sep === "\t") {
            $delimiter = "\t";
        }

        // Decode once, collect the union of dynamic field keys.
        $decoded = [];
        $keys    = [];
        $skip    = ['signature', 'quiz_score', '_name', '_phone', '_ip'];
        foreach ((array) $rows as $i => $row) {
            $f = json_decode($row->fields_data, true);
            $norm = [];
            if (is_array($f)) {
                foreach ($f as $k => $v) {
                    $nk = $this->normalise_key($k);
                    if (substr($nk, -5) === '_path') {
                        continue;
                    }
                    $norm[$nk] = $v;
                    if (!in_array($nk, $skip, true) && !in_array($nk, $keys, true) && count($keys) < 80) {
                        $keys[] = $nk;
                    }
                }
            }
            $decoded[$i] = $norm;
        }

        $filename = 'wppoppop-submissions-' . gmdate('Y-m-d-His') . '.csv';
        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $out = fopen('php://output', 'w');
        fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel / Numbers

        $head = array_merge(['ID', 'Popup', 'Popup UID', 'Email', 'Status', 'Country', 'Date', 'Name', 'Phone', 'IP Address'], $keys);
        fputcsv($out, $head, $delimiter);

        foreach ((array) $rows as $i => $row) {
            $f = $decoded[$i];
            $line = [
                $row->id,
                $this->csv_safe($row->popup_title),
                $row->popup_uid,
                $this->csv_safe($row->email),
                $row->status,
                $row->country_code,
                $row->created_at,
                $this->csv_safe(isset($f['_name']) ? $f['_name'] : ''),
                $this->csv_safe(isset($f['_phone']) ? $f['_phone'] : ''),
                $this->csv_safe(isset($f['_ip']) ? $f['_ip'] : ''),
            ];
            foreach ($keys as $k) {
                $line[] = $this->csv_safe(isset($f[$k]) ? $f[$k] : '');
            }
            fputcsv($out, $line, $delimiter);
        }

        fclose($out);
        exit;
    }
}
