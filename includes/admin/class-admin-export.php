<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Admin_Export {
    public function __construct() {
        add_action('admin_post_wppoppop_export_submissions', [$this, 'export_submissions_csv']);
        add_action('wp_ajax_wppoppop_export_submissions', [$this, 'export_submissions_csv']);
        add_action('wp_ajax_wppoppop_export_columns', [$this, 'list_export_columns']); /* WPPOPPOP_EXPORT_COLUMNS */
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

    /* WPPOPPOP_EXPORT_FIELDMAP: form fields defined on a popup (key => label), same rules as the submissions table */
    private function extract_fields($cfg) {
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
                $t = strtolower(trim((string) $el['type']));
                if (!in_array($t, $types, true)) {
                    continue;
                }
                if (isset($el['saveToDb']) && ($el['saveToDb'] === false || $el['saveToDb'] === 'false' || $el['saveToDb'] === 0 || $el['saveToDb'] === '0')) {
                    continue;
                }
                $k = '';
                foreach (['fieldName', 'token', 'field_name', 'fieldId'] as $pp) {
                    if (!empty($el[$pp])) {
                        $k = sanitize_key((string) $el[$pp]);
                        if ($k !== '') {
                            break;
                        }
                    }
                }
                if ($k === '') {
                    $k = sanitize_key($t);
                }
                if ($k === '' || isset($out[$k])) {
                    continue;
                }
                $l = '';
                foreach (['name', 'label_text', 'label', 'placeholder'] as $pp) {
                    if (!empty($el[$pp]) && is_string($el[$pp])) {
                        $l = trim(wp_strip_all_tags($el[$pp]));
                        if ($l !== '') {
                            break;
                        }
                    }
                }
                if ($l === '') {
                    $l = ucwords(str_replace(['_', '-'], ' ', $k));
                }
                $out[$k] = mb_substr($l, 0, 60);
            }
        }
        return $out;
    }

    /* returns [ defined (canonKey => label), canon (popupUid => [storedKey => canonKey]) ] */
    private function build_field_index($popup_uid) {
        global $wpdb;
        $items = $wpdb->prefix . 'wppoppop_items';
        $maps = [];
        foreach ((array) $wpdb->get_results("SELECT uid, data FROM {$items}") as $ir) {
            if ($popup_uid !== '' && $ir->uid !== $popup_uid) {
                continue;
            }
            $maps[$ir->uid] = $this->extract_fields($ir->data ? json_decode($ir->data, true) : null);
        }
        $defined = [];
        $canon   = [];
        $lbl2key = [];
        foreach ($maps as $u => $map) {
            foreach ($map as $k => $lbl) {
                $lc = strtolower($lbl);
                if ($popup_uid === '' && isset($lbl2key[$lc])) {
                    $ck = $lbl2key[$lc];
                } else {
                    $ck = (string) $k;
                    $lbl2key[$lc] = $ck;
                    $defined[$ck] = $lbl;
                }
                $canon[$u][(string) $k] = $ck;
            }
        }
        return [$defined, $canon];
    }

    private function resolve_field_key($canon, $uid, $stored) {
        $nk = sanitize_key((string) $stored);
        $cands = [$nk];
        if (strpos($nk, 'fields_') === 0) {
            $cands[] = substr($nk, 7);
        } elseif (strpos($nk, 'fields') === 0 && strlen($nk) > 6) {
            $cands[] = substr($nk, 6);
        }
        foreach ($cands as $c) {
            if (isset($canon[$uid][$c])) {
                return $canon[$uid][$c];
            }
        }
        return null;
    }

    /* WPPOPPOP_EXPORT_COLUMNS: columns offered by the Export modal (built-ins + fields really present in the filtered popup's submissions) */
    public function list_export_columns() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized'], 403);
        }
        $nonce = isset($_REQUEST['nonce']) ? sanitize_text_field(wp_unslash($_REQUEST['nonce'])) : '';
        if (!wp_verify_nonce($nonce, 'wppoppop_export_nonce')
            && !wp_verify_nonce($nonce, 'wppoppop_admin_nonce')
            && !wp_verify_nonce($nonce, 'wppoppop_builder_nonce')) {
            wp_send_json_error(['message' => 'Security verification failed.'], 403);
        }
        global $wpdb;
        $table = $wpdb->prefix . 'wppoppop_submissions';
        $uid   = isset($_REQUEST['popup_uid']) ? sanitize_text_field(wp_unslash($_REQUEST['popup_uid'])) : '';

        $cols = [
            ['id' => 'id',        'label' => 'ID',         'group' => 'core'],
            ['id' => 'popup',     'label' => 'Popup',      'group' => 'core'],
            ['id' => 'popup_uid', 'label' => 'Popup UID',  'group' => 'core'],
            ['id' => 'email',     'label' => 'Email',      'group' => 'core'],
            ['id' => 'status',    'label' => 'Status',     'group' => 'core'],
            ['id' => 'country',   'label' => 'Country',    'group' => 'core'],
            ['id' => 'date',      'label' => 'Date',       'group' => 'core'],
            ['id' => 'name',      'label' => 'Name',       'group' => 'core'],
            ['id' => 'phone',     'label' => 'Phone',      'group' => 'core'],
            ['id' => 'ip',        'label' => 'IP Address', 'group' => 'core'],
        ];

        list($defined, $canon) = $this->build_field_index($uid);
        foreach ($defined as $fk => $flbl) {
            $cols[] = ['id' => 'field:' . $fk, 'label' => $flbl, 'group' => 'field'];
        }
        if (empty($defined)) {
            /* no popup-defined fields: fall back to keys found in stored submissions */
            if ($uid !== '') {
                $raws = $wpdb->get_col($wpdb->prepare("SELECT fields_data FROM {$table} WHERE popup_uid = %s ORDER BY id DESC LIMIT 500", $uid));
            } else {
                $raws = $wpdb->get_col("SELECT fields_data FROM {$table} ORDER BY id DESC LIMIT 500");
            }
            $skip = ['signature', 'quiz_score', '_name', '_phone', '_ip'];
            $seen = [];
            foreach ((array) $raws as $raw) {
                $f = json_decode($raw, true);
                if (!is_array($f)) {
                    continue;
                }
                foreach ($f as $k => $v) {
                    $nk = $this->normalise_key($k);
                    if ($nk === '' || substr($nk, -5) === '_path' || in_array($nk, $skip, true) || strpos($nk, 'utm_') === 0 || isset($seen[$nk]) || count($seen) >= 80) {
                        continue;
                    }
                    $seen[$nk] = true;
                    $cols[] = ['id' => 'field:' . $nk, 'label' => ucwords(str_replace(['_', '-'], ' ', $nk)), 'group' => 'field'];
                }
            }
        }
        wp_send_json_success(['columns' => $cols, 'popup_uid' => $uid]);
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

        // Decode once; map stored keys onto the popup-defined columns (labels), extras appended.
        list($defined, $canon) = $this->build_field_index($popup_uid);
        $decoded    = [];
        $extras     = [];
        $skip       = ['signature', 'quiz_score', '_name', '_phone', '_ip'];
        foreach ((array) $rows as $i => $row) {
            $f = json_decode($row->fields_data, true);
            $norm = [];
            if (is_array($f)) {
                foreach ($f as $k => $v) {
                    $nk = $this->normalise_key($k);
                    if (substr($nk, -5) === '_path') {
                        continue;
                    }
                    if (in_array($nk, ['_name', '_phone', '_ip'], true)) {
                        $norm[$nk] = $v;
                        continue;
                    }
                    $ck = $this->resolve_field_key($canon, $row->popup_uid, $k);
                    if ($ck !== null) {
                        if (!isset($norm[$ck])) {
                            $norm[$ck] = $v;
                        }
                        continue;
                    }
                    $norm[$nk] = $v;
                    if (!in_array($nk, $skip, true) && !isset($defined[$nk]) && !in_array($nk, $extras, true) && count($extras) < 80) {
                        $extras[] = $nk;
                    }
                }
            }
            $decoded[$i] = $norm;
        }
        $keys = array_merge(array_map('strval', array_keys($defined)), $extras); /* WPPOPPOP_EXPORT_FIELDMAP */
        $key_labels = $defined;
        foreach ($extras as $ex) {
            $key_labels[$ex] = ucwords(str_replace(['_', '-'], ' ', $ex));
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

        $head = ['ID', 'Popup', 'Popup UID', 'Email', 'Status', 'Country', 'Date', 'Name', 'Phone', 'IP Address'];
        foreach ($keys as $hk) {
            $head[] = isset($key_labels[$hk]) ? $key_labels[$hk] : $hk;
        }
        /* WPPOPPOP_EXPORT_PICK: optional column whitelist from the Export modal (JSON array of column ids) */
        $colids = ['id', 'popup', 'popup_uid', 'email', 'status', 'country', 'date', 'name', 'phone', 'ip'];
        foreach ($keys as $k) {
            $colids[] = 'field:' . $k;
        }
        $pick = null;
        if (isset($_REQUEST['columns'])) {
            $want = json_decode(wp_unslash($_REQUEST['columns']), true);
            if (is_array($want)) {
                $pick = [];
                foreach (array_slice($want, 0, 120) as $w) {
                    $ix = array_search(sanitize_text_field((string) $w), $colids, true);
                    if ($ix !== false && !in_array($ix, $pick, true)) {
                        $pick[] = $ix;
                    }
                }
                if (empty($pick)) {
                    $pick = null;
                }
            }
        }
        if ($pick !== null) {
            $head_p = [];
            foreach ($pick as $ix) {
                $head_p[] = $head[$ix];
            }
            fputcsv($out, $head_p, $delimiter);
        } else {
            fputcsv($out, $head, $delimiter);
        }

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
            if ($pick !== null) {
                $line_p = [];
                foreach ($pick as $ix) {
                    $line_p[] = isset($line[$ix]) ? $line[$ix] : '';
                }
                fputcsv($out, $line_p, $delimiter);
            } else {
                fputcsv($out, $line, $delimiter);
            }
        }

        fclose($out);
        exit;
    }
}
