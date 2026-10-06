<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!current_user_can('manage_options')) {
    wp_die('Unauthorized');
}

global $wpdb;
$notice_msg = '';
$notice_type = 'success';

// Handle Tools Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && check_admin_referer('wppoppop_tools_action', 'wppoppop_tools_nonce')) {
    // 1. Repair Schema Tables
    if (isset($_POST['wppoppop_repair_tables'])) {
        require_once WPPOPPOP_PATH . 'includes/class-wppoppop-installer.php';
        WpPopPop_Installer::create_tables();
        $notice_msg = 'Database tables and schema indexes successfully verified and updated!';
    }

    // 2. Reset Campaign Metrics Counters
    if (isset($_POST['wppoppop_reset_counters'])) {
        $wpdb->query("UPDATE {$wpdb->prefix}wppoppop_items SET impressions = 0, submissions = 0, confirmations = 0");
        $notice_msg = 'All popup impressions, leads, and confirmation counters have been reset to 0.';
    }

    // 3. Bulk JSON Export
    if (isset($_POST['wppoppop_bulk_export'])) {
        $items = $wpdb->get_results("SELECT uid, title, data, status FROM {$wpdb->prefix}wppoppop_items", ARRAY_A);
        $campaigns = $wpdb->get_results("SELECT uid, title, popup_uids, status FROM {$wpdb->prefix}wppoppop_campaigns", ARRAY_A);
        
        $backup_payload = [
            'generator' => 'WpPopPop ' . WPPOPPOP_VERSION,
            'exported'  => current_time('mysql'),
            'items'     => $items,
            'campaigns' => $campaigns
        ];

        $json_out = wp_json_encode($backup_payload, JSON_PRETTY_PRINT);
        $filename = 'wppoppop-bulk-backup-' . date('Y-m-d') . '.json';

        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($json_out));
        echo $json_out;
        exit;
    }

    // 4. Bulk JSON Import
    if (isset($_POST['wppoppop_bulk_import']) && !empty($_FILES['wppoppop_bulk_import_file']['tmp_name'])) {
        $raw_json = file_get_contents($_FILES['wppoppop_bulk_import_file']['tmp_name']);
        $decoded = json_decode($raw_json, true);

        if (!empty($decoded['items']) && is_array($decoded['items'])) {
            $imported_count = 0;
            foreach ($decoded['items'] as $item) {
                $new_uid = wp_generate_uuid4();
                $wpdb->insert(
                    $wpdb->prefix . 'wppoppop_items',
                    [
                        'uid'    => $new_uid,
                        'title'  => sanitize_text_field($item['title']) . ' (Restored)',
                        'data'   => is_array($item['data']) ? wp_json_encode($item['data']) : $item['data'],
                        'status' => 'publish'
                    ],
                    ['%s', '%s', '%s', '%s']
                );
                $imported_count++;
            }
            $notice_msg = "Successfully restored {$imported_count} popup campaigns from backup archive!";
        } else {
            $notice_msg = 'Invalid backup archive format. Please select a valid WpPopPop bulk JSON export file.';
            $notice_type = 'error';
        }
    }
}
?>
<div class="wrap wppoppop-tools-wrap" style="max-width:1200px;">
    <div style="margin-bottom:20px;padding-top:10px;">
        <h1 class="wp-heading-inline" style="margin:0;font-size:24px;font-weight:700;color:#1e293b;">Tools, Diagnostics & Portability</h1>
        <p style="margin:4px 0 0;color:#64748b;font-size:13px;">Review server diagnostics, maintain database table health, and create bulk backup archives.</p>
    </div>

    <?php if (!empty($notice_msg)) : ?>
        <div class="notice notice-<?php echo esc_attr($notice_type); ?> is-dismissible" style="margin-bottom:20px;">
            <p><?php echo esc_html($notice_msg); ?></p>
        </div>
    <?php endif; ?>

    <!-- System Environment & Health Diagnostics -->
    <?php include WPPOPPOP_PATH . 'templates/tools/system-info.php'; ?>

    <!-- Database Schema Table Status & Repair -->
    <?php include WPPOPPOP_PATH . 'templates/tools/database-maintenance.php'; ?>

    <!-- Bulk JSON Backup Export & Import -->
    <?php include WPPOPPOP_PATH . 'templates/tools/bulk-portability.php'; ?>
</div>
