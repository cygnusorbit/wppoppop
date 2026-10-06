<?php
if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$tables = [
    'wp_wppoppop_items'        => 'Popup Campaigns & Definitions',
    'wp_wppoppop_submissions'  => 'Leads & Form Submissions',
    'wp_wppoppop_campaigns'    => 'A/B Split-Testing Experiments',
    'wp_wppoppop_logs'         => 'System & Delivery Event Audit',
    'wp_wppoppop_transactions' => 'Payment & Checkout Orders'
];
?>
<div class="wppoppop-card-box" style="background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;padding:20px;box-shadow:0 1px 3px rgba(0,0,0,0.05);margin-bottom:24px;">
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;border-bottom:1px solid #f1f5f9;padding-bottom:10px;">
        <span class="dashicons dashicons-database" style="font-size:20px;width:20px;height:20px;color:#10b981;"></span>
        <h3 style="margin:0;font-size:16px;font-weight:700;color:#1e293b;">Database Schema Status & Maintenance</h3>
    </div>

    <table class="wp-list-table widefat fixed striped" style="border:none;margin-bottom:18px;">
        <thead>
            <tr>
                <th style="font-weight:600;">Table Name</th>
                <th style="font-weight:600;">Description</th>
                <th style="width:130px;text-align:right;font-weight:600;">Record Count</th>
                <th style="width:110px;text-align:center;font-weight:600;">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tables as $tbl_suffix => $desc) : 
                $actual_name = $wpdb->prefix . str_replace('wp_', '', $tbl_suffix);
                $exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $actual_name)) === $actual_name;
                $count  = $exists ? $wpdb->get_var("SELECT COUNT(*) FROM {$actual_name}") : 0;
                ?>
                <tr>
                    <td><code><?php echo esc_html($actual_name); ?></code></td>
                    <td style="color:#64748b;"><?php echo esc_html($desc); ?></td>
                    <td style="text-align:right;font-weight:600;color:#1e293b;"><?php echo $exists ? number_format($count) : '—'; ?></td>
                    <td style="text-align:center;">
                        <?php if ($exists) : ?>
                            <span style="background:#dcfce7;color:#166534;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:700;">OK</span>
                        <?php else : ?>
                            <span style="background:#fee2e2;color:#991b1b;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:700;">Missing</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Maintenance Action Buttons -->
    <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:center;">
        <form method="post" style="margin:0;">
            <?php wp_nonce_field('wppoppop_tools_action', 'wppoppop_tools_nonce'); ?>
            <button type="submit" name="wppoppop_repair_tables" class="button button-secondary" style="display:inline-flex;align-items:center;gap:4px;">
                <span class="dashicons dashicons-admin-tools" style="font-size:16px;width:16px;height:16px;"></span> Verify & Repair Schema Tables
            </button>
        </form>

        <form method="post" onsubmit="return confirm('Reset all campaign impression, lead, and confirmation counters to 0?');" style="margin:0;">
            <?php wp_nonce_field('wppoppop_tools_action', 'wppoppop_tools_nonce'); ?>
            <button type="submit" name="wppoppop_reset_counters" class="button" style="color:#f59e0b;border-color:#fcd34d;display:inline-flex;align-items:center;gap:4px;">
                <span class="dashicons dashicons-update" style="font-size:16px;width:16px;height:16px;"></span> Reset All Campaign Counters
            </button>
        </form>
    </div>
</div>
