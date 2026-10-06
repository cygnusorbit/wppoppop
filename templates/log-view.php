<?php
if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$table = $wpdb->prefix . 'wppoppop_logs';

// 1. Process Log Purge Action
$notice_msg = '';
if (isset($_POST['wppoppop_clear_logs']) && check_admin_referer('wppoppop_clear_logs_action', 'wppoppop_clear_logs_nonce')) {
    if (current_user_can('manage_options')) {
        $wpdb->query("TRUNCATE TABLE {$table}");
        $notice_msg = 'All event logs have been successfully cleared.';
    }
}

// 2. Fetch Aggregated Diagnostic Totals
$total_logs    = (int)$wpdb->get_var("SELECT COUNT(id) FROM {$table}");
$error_logs    = (int)$wpdb->get_var("SELECT COUNT(id) FROM {$table} WHERE event_type LIKE '%error%' OR event_type LIKE '%failed%'");
$dispatch_logs = (int)$wpdb->get_var("SELECT COUNT(id) FROM {$table} WHERE event_type LIKE '%sms%' OR event_type LIKE '%webhook%'");
$optin_logs    = (int)$wpdb->get_var("SELECT COUNT(id) FROM {$table} WHERE event_type LIKE '%optin%' OR event_type LIKE '%submission%'");

// 3. Process Event Filtering
$selected_type = isset($_GET['type']) ? sanitize_key($_GET['type']) : '';
if (!empty($selected_type)) {
    if ($selected_type === 'errors') {
        $logs = $wpdb->get_results("SELECT * FROM {$table} WHERE event_type LIKE '%error%' OR event_type LIKE '%failed%' ORDER BY id DESC LIMIT 150");
    } else {
        $logs = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table} WHERE event_type = %s ORDER BY id DESC LIMIT 150", $selected_type));
    }
} else {
    $logs = $wpdb->get_results("SELECT * FROM {$table} ORDER BY id DESC LIMIT 150");
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

<script>
(function($) {
    'use strict';
    $(document).ready(function() {
        // 1. Live Keyword Search
        $('#wppoppop-log-search').on('input', function() {
            var term = $(this).val().toLowerCase();
            $('.wppoppop-log-row').each(function() {
                var type = $(this).data('type') || '';
                var msg  = $(this).data('msg') || '';
                if (type.indexOf(term) !== -1 || msg.indexOf(term) !== -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // 2. Payload Inspection Modal
        var currentRawPayload = '';
        $('.wppoppop-view-payload-btn').on('click', function() {
            var $btn = $(this);
            var id = $btn.data('id');
            var type = $btn.data('type');
            var msg = $btn.data('msg');
            var date = $btn.data('date');
            var payload = $btn.data('payload');

            $('#wppoppop-modal-log-title').text('#' + id + ' • ' + type + ' (' + date + ')');
            $('#wppoppop-modal-log-desc').text(msg);

            var prettyJson = '';
            try {
                var parsed = (typeof payload === 'string') ? JSON.parse(payload) : payload;
                prettyJson = JSON.stringify(parsed, null, 2);
            } catch(e) {
                prettyJson = String(payload);
            }

            currentRawPayload = prettyJson;
            $('#wppoppop-modal-payload-code').text(prettyJson);
            $('#wppoppop-payload-modal').css('display', 'flex');
        });

        // 3. Modal Dismissal
        $('#wppoppop-payload-close, #wppoppop-payload-done').on('click', function() {
            $('#wppoppop-payload-modal').hide();
        });

        // 4. Copy Payload Action
        $('#wppoppop-payload-copy-btn').on('click', function() {
            if (navigator.clipboard && currentRawPayload) {
                navigator.clipboard.writeText(currentRawPayload);
                var orig = $(this).text();
                $(this).text('Copied!');
                var $self = $(this);
                setTimeout(function() { $self.text(orig); }, 1500);
            }
        });
    });
})(jQuery);
</script>
