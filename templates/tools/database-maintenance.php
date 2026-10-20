<?php
if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;

$custom_tables = [
    'items'        => ['label' => 'Popups & Designs', 'name' => $wpdb->prefix . 'wppoppop_items'],
    'submissions'  => ['label' => 'Captured Leads', 'name' => $wpdb->prefix . 'wppoppop_submissions'],
    'campaigns'    => ['label' => 'A/B Experiments', 'name' => $wpdb->prefix . 'wppoppop_campaigns'],
    'logs'         => ['label' => 'Audit & System Logs', 'name' => $wpdb->prefix . 'wppoppop_logs'],
    'transactions' => ['label' => 'Payment Records', 'name' => $wpdb->prefix . 'wppoppop_transactions'],
];
?>
<?php
$tbl_status = class_exists('WpPopPop_Ajax_Tools') ? WpPopPop_Ajax_Tools::table_status() : [];
?>
<div class="wppoppop-card-box wpp-tools-card">
    <div class="wpp-tools-card-head">
        <span class="dashicons dashicons-database"></span>
        <h3><?php esc_html_e('Database Schema Table Health & Maintenance', 'wppoppop'); ?></h3>
    </div>

    <div id="wppoppop-db-status-notice" class="wpp-tools-notice" hidden></div>

    <table class="widefat striped wpp-tools-table">
        <thead>
            <tr>
                <th><?php esc_html_e('Component Domain', 'wppoppop'); ?></th>
                <th><?php esc_html_e('MySQL Table Name', 'wppoppop'); ?></th>
                <th><?php esc_html_e('Status', 'wppoppop'); ?></th>
                <th class="num"><?php esc_html_e('Records', 'wppoppop'); ?></th>
                <th class="num"><?php esc_html_e('Table Size', 'wppoppop'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($custom_tables as $key => $tbl) :
                $st = isset($tbl_status[$key]) ? $tbl_status[$key] : ['exists' => false, 'rows' => '0', 'size' => '0 KB', 'status' => 'Missing'];
            ?>
            <tr id="wppoppop-table-row-<?php echo esc_attr($key); ?>" data-table-key="<?php echo esc_attr($key); ?>">
                <td class="wpp-strong"><?php echo esc_html($tbl['label']); ?></td>
                <td class="wpp-mono"><?php echo esc_html($tbl['name']); ?></td>
                <td><span class="wppoppop-tbl-status-pill wpp-pill <?php echo !empty($st['exists']) ? 'is-ok' : 'is-bad'; ?>"><?php echo esc_html($st['status']); ?></span></td>
                <td class="num wppoppop-tbl-rows-count"><?php echo esc_html($st['rows']); ?></td>
                <td class="num wppoppop-tbl-size-val"><?php echo esc_html($st['size']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="wpp-tools-grid">
        <div class="wpp-tools-panel">
            <h4><?php esc_html_e('Verify & Repair Schema Tables', 'wppoppop'); ?></h4>
            <p class="wpp-tools-help"><?php esc_html_e('Runs dbDelta routines to verify table structures, apply missing columns, and guarantee index alignment across all 5 custom tables without modifying existing data.', 'wppoppop'); ?></p>
            <form method="post" id="wppoppop-repair-tables-form">
                <input type="hidden" name="wppoppop_tools_nonce" value="<?php echo esc_attr(wp_create_nonce('wppoppop_tools_action')); ?>"><?php wp_referer_field(); ?>
                <button type="submit" name="wppoppop_repair_tables" id="wppoppop-repair-tables-btn" class="button wpp-tools-btn"><span class="dashicons dashicons-admin-tools"></span><?php esc_html_e('Verify & Repair Schema', 'wppoppop'); ?></button>
            </form>
        </div>

        <div class="wpp-tools-panel is-danger">
            <h4><?php esc_html_e('Reset Campaign Metrics Counters', 'wppoppop'); ?></h4>
            <p class="wpp-tools-help"><?php esc_html_e('Wipes all recorded popup impressions, captured lead counters, and double opt-in confirmations back to 0. This does not delete any designs or lead submissions.', 'wppoppop'); ?></p>
            <form method="post" id="wppoppop-reset-counters-form">
                <input type="hidden" name="wppoppop_tools_nonce" value="<?php echo esc_attr(wp_create_nonce('wppoppop_tools_action')); ?>"><?php wp_referer_field(); ?>
                <button type="submit" name="wppoppop_reset_counters" id="wppoppop-reset-counters-btn" class="button wpp-tools-btn wpp-btn-danger"><span class="dashicons dashicons-update"></span><?php esc_html_e('Reset All Counters', 'wppoppop'); ?></button>
            </form>
        </div>
    </div>
</div>
