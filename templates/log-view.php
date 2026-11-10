<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- template variables are local to the method that includes this file
if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$table = $wpdb->prefix . 'wppoppop_logs';

// 1. Process Log Purge Action
$notice_msg = '';
if (isset($_POST['wppoppop_clear_logs']) && check_admin_referer('wppoppop_clear_logs_action', 'wppoppop_clear_logs_nonce')) {
    if (current_user_can('manage_options')) {
        /* phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- table name is built from $wpdb->prefix and the query takes no user input */ $wpdb->query("TRUNCATE TABLE {$table}");
        $notice_msg = __('All event logs have been successfully cleared.', 'wppoppop');
    }
}

// 2. Fetch Aggregated Diagnostic Totals
/* phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- table name is built from $wpdb->prefix and the query takes no user input */ $total_logs    = (int)$wpdb->get_var("SELECT COUNT(id) FROM {$table}");
/* phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- table name is built from $wpdb->prefix and the query takes no user input */ $error_logs    = (int)$wpdb->get_var("SELECT COUNT(id) FROM {$table} WHERE event_type LIKE '%error%' OR event_type LIKE '%failed%'");
/* phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- table name is built from $wpdb->prefix and the query takes no user input */ $dispatch_logs = (int)$wpdb->get_var("SELECT COUNT(id) FROM {$table} WHERE event_type LIKE '%sms%' OR event_type LIKE '%webhook%'");
/* phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- table name is built from $wpdb->prefix and the query takes no user input */ $optin_logs    = (int)$wpdb->get_var("SELECT COUNT(id) FROM {$table} WHERE event_type LIKE '%optin%' OR event_type LIKE '%submission%'");

// 3. Process Event Filtering
$selected_type = isset($_GET['type']) ? sanitize_key($_GET['type']) : '';
if (!empty($selected_type)) {
    if ($selected_type === 'errors') {
        /* phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- table name is built from $wpdb->prefix and the query takes no user input */ $logs = $wpdb->get_results("SELECT * FROM {$table} WHERE event_type LIKE '%error%' OR event_type LIKE '%failed%' ORDER BY id DESC LIMIT 150");
    } else {
        /* phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- table name is built from $wpdb->prefix and the query takes no user input */ $logs = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table} WHERE event_type = %s ORDER BY id DESC LIMIT 150", $selected_type));
    }
} else {
    /* phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- table name is built from $wpdb->prefix and the query takes no user input */ $logs = $wpdb->get_results("SELECT * FROM {$table} ORDER BY id DESC LIMIT 150");
}
?>
<div class="wrap wppoppop-log-wrap" style="max-width:1200px;">
    <!-- Top Action & Search Header -->
    <?php include WPPOPPOP_PATH . 'templates/log/header.php'; ?>

    <!-- KPI Totals Summary Cards -->
    <?php include WPPOPPOP_PATH . 'templates/log/kpi-summary.php'; ?>

    <!-- Activity Log List Table -->
    <?php include WPPOPPOP_PATH . 'templates/log/table.php'; ?>

    <!-- JSON Payload Inspection Modal -->
    <?php include WPPOPPOP_PATH . 'templates/log/modal-payload.php'; ?>
</div>
