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
<div class="wppoppop-card-box" style="background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;padding:20px;box-shadow:0 1px 3px rgba(0,0,0,0.05);margin-bottom:24px;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;border-bottom:1px solid #f1f5f9;padding-bottom:10px;">
        <div style="display:flex;align-items:center;gap:8px;">
            <span class="dashicons dashicons-database" style="font-size:20px;width:20px;height:20px;color:#0284c7;"></span>
            <h3 style="margin:0;font-size:16px;font-weight:700;color:#1e293b;">Database Schema Table Health & Maintenance</h3>
        </div>
        <span id="wppoppop-db-status-badge" style="display:inline-flex;align-items:center;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600;background:#dcfce7;color:#15803d;">
            Schema Monitored
        </span>
    </div>

    <div id="wppoppop-db-status-notice" style="display:none;margin-bottom:16px;padding:10px 14px;border-radius:6px;font-size:13px;line-height:1.4;"></div>

    <!-- Live Database Table Health Grid -->
    <table class="widefat" style="border:1px solid #e2e8f0;box-shadow:none;border-radius:6px;overflow:hidden;margin-bottom:18px;">
        <thead>
            <tr style="background:#f8fafc;">
                <th style="padding:10px 14px;font-weight:600;color:#475569;font-size:12px;">Component Domain</th>
                <th style="padding:10px 14px;font-weight:600;color:#475569;font-size:12px;">MySQL Table Name</th>
                <th style="padding:10px 14px;font-weight:600;color:#475569;font-size:12px;">Status</th>
                <th style="padding:10px 14px;font-weight:600;color:#475569;font-size:12px;text-align:right;">Records</th>
                <th style="padding:10px 14px;font-weight:600;color:#475569;font-size:12px;text-align:right;">Table Size</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($custom_tables as $key => $tbl) :
                $exists = ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $tbl['name'])) === $tbl['name']);
                $rows = $exists ? (int)$wpdb->get_var("SELECT COUNT(*) FROM `{$tbl['name']}`") : 0;
                $size_str = '0 KB';
                if ($exists) {$status_row = $wpdb->get_row($wpdb->prepare("SHOW TABLE STATUS LIKE %s", $tbl['name']), ARRAY_A);
                    if ($status_row && isset($status_row['Data_length'])) {$bytes = (int)$status_row['Data_length'] + (int)$status_row['Index_length'];
                        $size_str = size_format($bytes, 2);
                    }
                }
            ?>
            <tr id="wppoppop-table-row-<?php echo esc_attr($key); ?>" data-table-key="<?php echo esc_attr($key); ?>">
                <td style="padding:10px 14px;font-weight:600;color:#1e293b;font-size:13px;"><?php echo esc_html($tbl['label']); ?></td>
                <td style="padding:10px 14px;font-family:monospace;font-size:12px;color:#64748b;"><?php echo esc_html($tbl['name']); ?></td>
                <td style="padding:10px 14px;">
                    <span class="wppoppop-tbl-status-pill" style="display:inline-flex;align-items:center;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:600;background:<?php echo $exists ? '#dcfce7' : '#fee2e2'; ?>;color:<?php echo $exists ? '#15803d' : '#b91c1c'; ?>;">
                        <?php echo $exists ? 'Optimal' : 'Missing'; ?>
                    </span>
                </td>
                <td class="wppoppop-tbl-rows-count" style="padding:10px 14px;text-align:right;font-size:13px;color:#334155;"><?php echo esc_html(number_format_i18n($rows)); ?></td>
                <td class="wppoppop-tbl-size-val" style="padding:10px 14px;text-align:right;font-size:13px;color:#64748b;"><?php echo esc_html($size_str); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Maintenance Action Triggers -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
        <!-- Repair Database Tables -->
        <div style="background:#f8fafc;padding:16px;border-radius:8px;border:1px solid #e2e8f0;display:flex;flex-direction:column;justify-content:space-between;">
            <div>
                <h4 style="margin:0 0 6px 0;font-size:13px;font-weight:700;color:#1e293b;">Verify & Repair Schema Tables</h4>
                <p style="margin:0 0 12px 0;font-size:12px;color:#64748b;line-height:1.4;">
                    Runs dbDelta routines to verify table structures, apply missing columns, and guarantee index alignment across all 5 custom tables without modifying existing data.
                </p>
            </div>
            <form method="post" id="wppoppop-repair-tables-form" style="margin:0;">
                <?php wp_nonce_field('wppoppop_tools_action', 'wppoppop_tools_nonce'); ?>
                <button type="submit" name="wppoppop_repair_tables" id="wppoppop-repair-tables-btn" class="button button-secondary" style="display:inline-flex;align-items:center;gap:6px;">
                    <span class="dashicons dashicons-admin-tools" style="font-size:16px;width:16px;height:16px;"></span> Verify & Repair Schema
                </button>
            </form>
        </div>

        <!-- Flush Performance Counters -->
        <div style="background:#fff7ed;padding:16px;border-radius:8px;border:1px solid #fed7aa;display:flex;flex-direction:column;justify-content:space-between;">
            <div>
                <h4 style="margin:0 0 6px 0;font-size:13px;font-weight:700;color:#9a3412;">Reset Campaign Metrics Counters</h4>
                <p style="margin:0 0 12px 0;font-size:12px;color:#7c2d12;line-height:1.4;">
                    Wipes all recorded popup impressions, captured lead counters, and double opt-in confirmations back to 0. This does not delete any designs or lead submissions.
                </p>
            </div>
            <form method="post" id="wppoppop-reset-counters-form" style="margin:0;">
                <?php wp_nonce_field('wppoppop_tools_action', 'wppoppop_tools_nonce'); ?>
                <button type="submit" name="wppoppop_reset_counters" id="wppoppop-reset-counters-btn" class="button" style="border-color:#f97316;color:#c2410c;background:#fff;display:inline-flex;align-items:center;gap:6px;">
                    <span class="dashicons dashicons-update" style="font-size:16px;width:16px;height:16px;"></span> Reset All Counters
                </button>
            </form>
        </div>
    </div>
</div>
