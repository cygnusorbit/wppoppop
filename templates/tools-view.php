<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!current_user_can('manage_options')) {
    wp_die('Unauthorized');
}

global $wpdb;
$notice_msg  = '';
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

    // 3. Bulk JSON Import (Synchronous Postback Fallback)
    if (isset($_POST['wppoppop_bulk_import']) && !empty($_FILES['wppoppop_bulk_import_file']['tmp_name'])) {
        $raw_json = file_get_contents($_FILES['wppoppop_bulk_import_file']['tmp_name']);
        $decoded  = json_decode($raw_json, true);

        if ($decoded && is_array($decoded) && (!empty($decoded['items']) || !empty($decoded['campaigns']))) {
            $items_table     = $wpdb->prefix . 'wppoppop_items';
            $campaigns_table = $wpdb->prefix . 'wppoppop_campaigns';

            $imported_items     = 0;
            $imported_campaigns = 0;
            $uid_map            = [];

            if (!empty($decoded['items']) && is_array($decoded['items'])) {
                foreach ($decoded['items'] as $item) {
                    $old_uid = isset($item['uid']) ? sanitize_key($item['uid']) : '';
                    $new_uid = wp_generate_uuid4();
                    if ($old_uid) {
                        $uid_map[$old_uid] = $new_uid;
                    }

                    $title = isset($item['title']) ? sanitize_text_field($item['title']) . ' (Restored)' : 'Restored Popup';
                    $data  = isset($item['data']) ? (is_array($item['data']) ? wp_json_encode($item['data']) : $item['data']) : '{}';

                    $wpdb->insert(
                        $items_table,
                        [
                            'uid'           => $new_uid,
                            'title'         => $title,
                            'data'          => $data,
                            'status'        => 'publish',
                            'impressions'   => 0,
                            'submissions'   => 0,
                            'confirmations' => 0
                        ],
                        ['%s', '%s', '%s', '%s', '%d', '%d', '%d']
                    );
                    $imported_items++;
                }
            }

            if (!empty($decoded['campaigns']) && is_array($decoded['campaigns'])) {
                foreach ($decoded['campaigns'] as $camp) {
                    $camp_uid = wp_generate_uuid4();
                    $title    = isset($camp['title']) ? sanitize_text_field($camp['title']) . ' (Restored)' : 'Restored A/B Test';

                    $raw_uids = isset($camp['popup_uids']) ? (is_array($camp['popup_uids']) ? $camp['popup_uids'] : json_decode($camp['popup_uids'], true)) : [];
                    $remapped_uids = [];

                    if (is_array($raw_uids)) {
                        foreach ($raw_uids as $var_uid) {
                            $remapped_uids[] = isset($uid_map[$var_uid]) ? $uid_map[$var_uid] : sanitize_key($var_uid);
                        }
                    }

                    $wpdb->insert(
                        $campaigns_table,
                        [
                            'uid'        => $camp_uid,
                            'title'      => $title,
                            'popup_uids' => wp_json_encode($remapped_uids),
                            'status'     => 'active'
                        ],
                        ['%s', '%s', '%s', '%s']
                    );
                    $imported_campaigns++;
                }
            }

            $notice_msg = sprintf('Successfully restored %d popups and %d A/B campaigns from backup archive!', $imported_items, $imported_campaigns);
        } else {
            $notice_msg  = 'Invalid backup archive format. Please select a valid WpPopPop bulk JSON export file.';
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
