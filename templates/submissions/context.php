<?php
if (!defined('ABSPATH')) {
    exit;
}
global $wpdb;

$sel_uid    = isset($_GET['popup_uid']) ? sanitize_text_field(wp_unslash($_GET['popup_uid'])) : '';
$all_popups = $wpdb->get_results("SELECT uid, title FROM {$wpdb->prefix}wppoppop_items ORDER BY title ASC");

$all_opts = get_option('wppoppop_sub_table_options', []);
if (!is_array($all_opts)) {
    $all_opts = [];
}
$opt_key = ($sel_uid !== '') ? $sel_uid : '_default';
if (isset($all_opts[$opt_key]) && is_array($all_opts[$opt_key])) {
    $tbl_opts = $all_opts[$opt_key];
} elseif (isset($all_opts['_default']) && is_array($all_opts['_default'])) {
    $tbl_opts = $all_opts['_default'];
} else {
    $tbl_opts = [];
}
$tbl_opts = wp_parse_args($tbl_opts, ['labels' => [], 'columns' => [], 'per_page' => 10]);
if (!is_array($tbl_opts['labels']))  { $tbl_opts['labels'] = []; }
if (!is_array($tbl_opts['columns'])) { $tbl_opts['columns'] = []; }
$__pp = intval($tbl_opts['per_page']);
$tbl_opts['per_page'] = ($__pp <= 0) ? 10 : ($__pp > 25 ? 30 : ($__pp > 15 ? 20 : 10));

$label_map = get_option('wppoppop_sub_label_map', []);
if (!is_array($label_map)) {
    $label_map = [];
}
$labels_by_id = [];
foreach ($tbl_opts['labels'] as $__l) {
    if (!empty($__l['id'])) {
        $labels_by_id[$__l['id']] = $__l;
    }
}

// Scope entries to the selected popup.
if ($sel_uid !== '' && !empty($submissions)) {
    $submissions = array_values(array_filter($submissions, function ($row) use ($sel_uid) {
        return isset($row->popup_uid) && $row->popup_uid === $sel_uid;
    }));
}

// Available columns (built-ins + every field key seen in the scoped entries + saved custom keys).
$avail_cols = [
    'date'    => 'Date',
    'popup'   => 'Popup',
    'email'   => 'Email address',
    'country' => 'Country',
    'status'  => 'Status',
];
// ---- Live sync: form fields defined on the selected popup -------------------
$popup_fields = [];   // key => label
$popup_alias  = [];   // alternate stored key => canonical key
if ($sel_uid !== '') {
    $__raw = $wpdb->get_var($wpdb->prepare("SELECT data FROM {$wpdb->prefix}wppoppop_items WHERE uid = %s", $sel_uid));
    $__cfg = $__raw ? json_decode($__raw, true) : null;
    if (is_array($__cfg)) {
        $__form_types = ['text', 'textfield', 'email', 'number', 'phone', 'textarea', 'select', 'dropdown', 'checkbox', 'checkboxes',
            'radio', 'radios', 'multiselect', 'imageselect', 'tile', 'date', 'time', 'upload', 'file', 'password', 'rating', 'slider', 'signature'];
        $__canvases = isset($__cfg['canvases']) ? $__cfg['canvases'] : (isset($__cfg['screens']) ? $__cfg['screens'] : []);
        if (empty($__canvases) && !empty($__cfg['elements']) && is_array($__cfg['elements'])) {
            $__canvases = [$__cfg['elements']];
        }
        if (is_array($__canvases)) {
            foreach ($__canvases as $__cv) {
                $__els = (is_array($__cv) && isset($__cv['elements']) && is_array($__cv['elements'])) ? $__cv['elements'] : $__cv;
                if (!is_array($__els)) {
                    continue;
                }
                foreach ($__els as $__el) {
                    if (!is_array($__el) || empty($__el['type'])) {
                        continue;
                    }
                    $__type = strtolower(trim((string) $__el['type']));
                    if (!in_array($__type, $__form_types, true)) {
                        continue;
                    }
                    if (isset($__el['saveToDb']) && ($__el['saveToDb'] === false || $__el['saveToDb'] === 'false' || $__el['saveToDb'] === 0 || $__el['saveToDb'] === '0')) {
                        continue;
                    }
                    $__key = '';
                    foreach (['fieldName', 'token', 'field_name', 'fieldId'] as $__p) {
                        if (!empty($__el[$__p])) {
                            $__key = sanitize_key((string) $__el[$__p]);
                            if ($__key !== '') {
                                break;
                            }
                        }
                    }
                    if ($__key === '') {
                        $__key = sanitize_key($__type);
                    }
                    if ($__key === '' || isset($popup_fields[$__key])) {
                        continue;
                    }
                    $__lbl = '';
                    foreach (['name', 'label_text', 'label', 'placeholder'] as $__p) {
                        if (!empty($__el[$__p]) && is_string($__el[$__p])) {
                            $__lbl = trim(wp_strip_all_tags($__el[$__p]));
                            if ($__lbl !== '') {
                                break;
                            }
                        }
                    }
                    if ($__lbl === '') {
                        $__lbl = ucwords(str_replace(['_', '-'], ' ', $__key));
                    }
                    $popup_fields[$__key] = mb_substr($__lbl, 0, 60);
                    $popup_alias['fields' . $__key]  = $__key;
                    $popup_alias['fields_' . $__key] = $__key;
                }
            }
        }
    }
}

// ---- Per-popup field maps (uid => [key => label]) used to resolve cells across popups ----
$popup_field_maps = [];
$__ftypes = ['text', 'textfield', 'email', 'number', 'phone', 'textarea', 'select', 'dropdown', 'checkbox', 'checkboxes',
    'radio', 'radios', 'multiselect', 'imageselect', 'tile', 'date', 'time', 'upload', 'file', 'password', 'rating', 'slider', 'signature'];
$__extract = function ($cfg) use ($__ftypes) {
    $out = [];
    if (!is_array($cfg)) {
        return $out;
    }
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
            if (!in_array($t, $__ftypes, true)) {
                continue;
            }
            if (isset($el['saveToDb']) && ($el['saveToDb'] === false || $el['saveToDb'] === 'false' || $el['saveToDb'] === 0 || $el['saveToDb'] === '0')) {
                continue;
            }
            $k = '';
            foreach (['fieldName', 'token', 'field_name', 'fieldId'] as $p) {
                if (!empty($el[$p])) {
                    $k = sanitize_key((string) $el[$p]);
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
            foreach (['name', 'label_text', 'label', 'placeholder'] as $p) {
                if (!empty($el[$p]) && is_string($el[$p])) {
                    $l = trim(wp_strip_all_tags($el[$p]));
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
};
$__item_rows = $wpdb->get_results("SELECT uid, data FROM {$wpdb->prefix}wppoppop_items");
foreach ((array) $__item_rows as $__ir) {
    $popup_field_maps[$__ir->uid] = $__extract($__ir->data ? json_decode($__ir->data, true) : null);
}

// "All popups": one column per field LABEL, unioned across every popup.
if ($sel_uid === '') {
    $__lbl_to_key = [];
    foreach ($popup_field_maps as $__map) {
        foreach ($__map as $__k => $__lbl) {
            $__lc = strtolower($__lbl);
            if (isset($__lbl_to_key[$__lc])) {
                $__canon = $__lbl_to_key[$__lc];
            } else {
                $__canon = $__k;
                $__lbl_to_key[$__lc] = $__k;
                $popup_fields[$__k] = $__lbl;
            }
            $popup_alias[$__k] = $__canon;
            $popup_alias['fields' . $__k]  = $__canon;
            $popup_alias['fields_' . $__k] = $__canon;
        }
    }
}

$seen_fields = [];
if (!empty($submissions)) {
    foreach ($submissions as $__s) {
        $__f = json_decode($__s->fields_data, true);
        if (!is_array($__f)) {
            continue;
        }
        foreach ($__f as $__k => $__v) {
            if ($__k === 'signature' || $__k === 'quiz_score' || $__k === '_ip' || substr((string) $__k, -5) === '_path') {
                continue;
            }
            $__nk = sanitize_key((string) $__k);
            if ($__nk !== '') {
                $__k = $__nk;
            }
            if (isset($popup_alias[$__k])) {
                $__k = $popup_alias[$__k];
            }
            if (!in_array($__k, $seen_fields, true) && count($seen_fields) < 60) {
                $seen_fields[] = $__k;
            }
        }
    }
}
foreach ($popup_fields as $__k => $__lbl) {
    $avail_cols['field:' . $__k] = $__lbl;
}
// Fallback only: when no popup defines form fields, derive columns from stored data.
if (empty($popup_fields)) {
    foreach ($seen_fields as $__k) {
        if (strpos($__k, 'utm_') === 0 || $__k === 'fields') {
            continue;
        }
        if (!isset($avail_cols['field:' . $__k])) {
            $avail_cols['field:' . $__k] = ucwords(str_replace(['_', '-'], ' ', $__k));
        }
    }
    foreach ($tbl_opts['columns'] as $__c) {
        if (!isset($avail_cols[$__c]) && strpos($__c, 'field:') === 0) {
            $avail_cols[$__c] = ucwords(str_replace(['_', '-'], ' ', substr($__c, 6)));
        }
    }
}

$active_cols = array_values(array_filter($tbl_opts['columns'], function ($c) use ($avail_cols) {
    return isset($avail_cols[$c]);
}));
if ($sel_uid === '' && !empty($active_cols) && empty(array_intersect($active_cols, ['popup', 'email', 'country', 'status']))) {
    $active_cols = array_merge(['date', 'popup', 'email'], array_diff($active_cols, ['date']));
}
if (empty($active_cols) && $sel_uid === '') {
    // "All popups": fields differ per popup, so default to the columns every row has.
    $active_cols = ['date', 'popup', 'email', 'country', 'status'];
}
if (empty($active_cols)) {
    $active_cols = ['date'];
    $__n = 0;
    foreach (array_unique(empty($popup_fields) ? $seen_fields : array_keys($popup_fields)) as $__k) {
        if (strpos($__k, 'utm_') === 0) {
            continue;
        }
        $active_cols[] = 'field:' . $__k;
        if (++$__n >= 12) {
            break;
        }
    }
    if (count($active_cols) === 1) {
        $active_cols[] = 'email';
        $active_cols[] = 'popup';
    }
}

$tbl_config_payload = [
    'popup_uid' => $sel_uid,
    'labels'    => array_values($tbl_opts['labels']),
    'active'    => $active_cols,
    'available' => $avail_cols,
    'per_page'  => $tbl_opts['per_page'],
    'popup_fields' => array_map(function ($k) { return 'field:' . $k; }, array_keys($popup_fields)),
];
