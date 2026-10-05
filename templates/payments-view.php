<?php
if (!defined('ABSPATH')) {
    exit;
}
global $wpdb;
$table_tx = $wpdb->prefix . 'wppoppop_transactions';
$table_items = $wpdb->prefix . 'wppoppop_items';

$transactions = $wpdb->get_results("SELECT t.*, i.title as popup_title 
    FROM {$table_tx} t LEFT JOIN {$table_items} i ON t.popup_uid = i.uid ORDER BY t.id DESC LIMIT 100");

$total_revenue = $wpdb->get_var("SELECT SUM(amount) FROM {$table_tx} WHERE status = 'completed'");
$total_orders  = $wpdb->get_var("SELECT COUNT(*) FROM {$table_tx} WHERE status = 'completed'");
?>
<div class="wrap wppoppop-admin-page">
    <h1 class="wp-heading-inline">Payments & Transactions</h1>
    <p>Track payments captured via payment popups.</p>
    <hr class="wp-header-end">

    <div style="display: flex; gap: 20px; margin: 20px 0;">
        <div class="postbox" style="flex: 1; padding: 20px; text-align: center;">
            <div style="font-size: 13px; color: #646970; text-transform: uppercase; font-weight: 600;">Total Revenue</div>
            <div style="font-size: 32px; font-weight: 700; color: #00a32a; margin-top: 5px;">$<?php echo number_format((float)$total_revenue, 2); ?></div>
        </div>
        <div class="postbox" style="flex: 1; padding: 20px; text-align: center;">
            <div style="font-size: 13px; color: #646970; text-transform: uppercase; font-weight: 600;">Successful Payments</div>
            <div style="font-size: 32px; font-weight: 700; color: #2271b1; margin-top: 5px;"><?php echo number_format((int)$total_orders); ?></div>
        </div>
    </div>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th style="width: 80px;">ID</th>
                <th>Transaction ID</th>
                <th>Popup Campaign</th>
                <th>Customer Email</th>
                <th>Amount</th>
                <th>Gateway</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($transactions)) : ?>
                <tr><td colspan="8">No payments logged yet.</td></tr>
            <?php else : ?>
                <?php foreach ($transactions as $tx) : ?>
                    <tr>
                        <td>#<?php echo esc_html($tx->id); ?></td>
                        <td><code><?php echo esc_html($tx->transaction_id); ?></code></td>
                        <td><strong><?php echo esc_html($tx->popup_title ?? 'Deleted'); ?></strong></td>
                        <td><?php echo esc_html($tx->email); ?></td>
                        <td><strong>$<?php echo number_format($tx->amount, 2) . ' ' . esc_html($tx->currency); ?></strong></td>
                        <td><?php echo esc_html($tx->gateway); ?></td>
                        <td><span style="color: #00a32a; font-weight: 600;"><?php echo esc_html(strtoupper($tx->status)); ?></span></td>
                        <td><?php echo esc_html($tx->created_at); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
