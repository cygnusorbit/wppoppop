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
$tbl_opts = wp_parse_args($tbl_opts, ['labels' => [], 'columns' => [], 'per_page' => 20]);
if (!is_array($tbl_opts['labels']))  { $tbl_opts['labels'] = []; }
if (!is_array($tbl_opts['columns'])) { $tbl_opts['columns'] = []; }
$tbl_opts['per_page'] = max(5, min(200, intval($tbl_opts['per_page'])));

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
$seen_fields = [];
if (!empty($submissions)) {
    foreach ($submissions as $__s) {
        $__f = json_decode($__s->fields_data, true);
        if (!is_array($__f)) {
            continue;
        }
        foreach ($__f as $__k => $__v) {
            if ($__k === 'signature' || $__k === 'quiz_score') {
                continue;
            }
            if (!in_array($__k, $seen_fields, true) && count($seen_fields) < 60) {
                $seen_fields[] = $__k;
            }
        }
    }
}
foreach ($seen_fields as $__k) {
    $avail_cols['field:' . $__k] = ucwords(str_replace(['_', '-'], ' ', $__k));
}
foreach ($tbl_opts['columns'] as $__c) {
    if (!isset($avail_cols[$__c]) && strpos($__c, 'field:') === 0) {
        $avail_cols[$__c] = ucwords(str_replace(['_', '-'], ' ', substr($__c, 6)));
    }
}

$active_cols = array_values(array_filter($tbl_opts['columns'], function ($c) use ($avail_cols) {
    return isset($avail_cols[$c]);
}));
if (empty($active_cols)) {
    $active_cols = ['date'];
    $__n = 0;
    foreach ($seen_fields as $__k) {
        if (strpos($__k, 'utm_') === 0) {
            continue;
        }
        $active_cols[] = 'field:' . $__k;
        if (++$__n >= 7) {
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
];
