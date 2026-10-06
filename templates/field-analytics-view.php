<?php
if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$selected_uid = isset($_GET['popup_uid']) ? sanitize_key($_GET['popup_uid']) : '';

// 1. Fetch Campaigns for dropdown selector
$items_table = $wpdb->prefix . 'wppoppop_items';
$all_popups  = $wpdb->get_results("SELECT uid, title FROM {$items_table} ORDER BY id DESC");

// 2. Query Submissions Data
$subs_table = $wpdb->prefix . 'wppoppop_submissions';
if (!empty($selected_uid)) {
    $rows = $wpdb->get_results($wpdb->prepare("SELECT fields_data FROM {$subs_table} WHERE popup_uid = %s", $selected_uid), ARRAY_A);
} else {
    $rows = $wpdb->get_results("SELECT fields_data FROM {$subs_table}", ARRAY_A);
}

$total_submissions_count = count($rows);
$fields_summary  = [];
$choice_fields   = [];
$rating_histogram = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
$total_rating_count = 0;
$total_rating_sum   = 0;
$top_choice_label   = '—';
$top_choice_votes   = 0;

// 3. Aggregate JSON Field Payloads
foreach ($rows as $r) {
    $data = json_decode($r['fields_data'], true);
    if (!is_array($data)) continue;

    foreach ($data as $key => $val) {
        if ($key === 'signature' || strpos($key, 'utm_') === 0 || $key === '_wppoppop_hp_email') {
            continue;
        }

        if (!isset($fields_summary[$key])) {
            $fields_summary[$key] = 0;
        }
        $fields_summary[$key]++;

        // Rating Element
        if ($key === 'rating') {
            $star_val = intval($val);
            if ($star_val >= 1 && $star_val <= 5) {
                $rating_histogram[$star_val]++;
                $total_rating_sum += $star_val;
                $total_rating_count++;
            }
            continue;
        }

        // Choice, Dropdown, Radio, Checkbox Elements
        if (is_array($val) || (is_string($val) && strlen($val) < 60 && $key !== 'email' && $key !== 'name')) {
            if (!isset($choice_fields[$key])) {
                $choice_fields[$key] = ['total' => 0, 'options' => []];
            }

            $options_list = is_array($val) ? $val : [$val];
            foreach ($options_list as $opt) {
                $opt_str = trim((string)$opt);
                if ($opt_str === '') continue;

                if (!isset($choice_fields[$key]['options'][$opt_str])) {
                    $choice_fields[$key]['options'][$opt_str] = 0;
                }
                $choice_fields[$key]['options'][$opt_str]++;
                $choice_fields[$key]['total']++;

                if ($choice_fields[$key]['options'][$opt_str] > $top_choice_votes) {
                    $top_choice_votes = $choice_fields[$key]['options'][$opt_str];
                    $top_choice_label = $opt_str;
                }
            }
        }
    }
}

$avg_rating = $total_rating_count > 0 ? round($total_rating_sum / $total_rating_count, 1) : 0;
?>
<div class="wrap wppoppop-analytics-wrap" style="max-width:1200px;">
    <!-- Header & Campaign Selector -->
    <?php include WPPOPPOP_PATH . 'templates/field-analytics/header.php'; ?>

    <!-- KPI Summary Cards -->
    <?php include WPPOPPOP_PATH . 'templates/field-analytics/kpi-summary.php'; ?>

    <!-- 5-Star Customer Rating Histogram -->
    <?php include WPPOPPOP_PATH . 'templates/field-analytics/breakdown-ratings.php'; ?>

    <!-- Choices & Percentage Distribution Bars -->
    <?php include WPPOPPOP_PATH . 'templates/field-analytics/breakdown-choice.php'; ?>
</div>
