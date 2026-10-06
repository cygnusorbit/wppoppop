<?php
if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$items_table = $wpdb->prefix . 'wppoppop_items';
$all_popups = $wpdb->get_results("SELECT uid, title FROM {$items_table}");
$popups_by_uid = [];
foreach ($all_popups as $p) {
    $popups_by_uid[$p->uid] = $p->title;
}
?>
<div class="wppoppop-card-table" style="background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;box-shadow:0 1px 3px rgba(0,0,0,0.05);overflow:hidden;">
    <table class="wp-list-table widefat fixed striped table-view-list" id="wppoppop-payments-table" style="border:none;">
        <thead>
            <tr>
                <th style="width:50px;text-align:center;">#</th>
                <th style="width:180px;font-weight:600;">Transaction ID</th>
                <th style="width:220px;font-weight:600;">Customer Email</th>
                <th style="font-weight:600;">Popup Campaign</th>
                <th style="width:120px;text-align:right;font-weight:600;">Amount</th>
                <th style="width:110px;text-align:center;font-weight:600;">Gateway</th>
                <th style="width:110px;text-align:center;font-weight:600;">Status</th>
                <th style="width:140px;font-weight:600;">Date</th>
                <th style="width:120px;text-align:right;font-weight:600;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($transactions)) : ?>
                <tr>
                    <td colspan="9" style="text-align:center;padding:36px 10px;color:#64748b;">
                        <span class="dashicons dashicons-cart" style="font-size:32px;width:32px;height:32px;margin:0 auto 8px auto;display:block;color:#94a3b8;"></span>
                        No sales transactions recorded yet. Popups configured with payment buttons will log checkout records here.
                    </td>
                </tr>
            <?php else : ?>
                <?php foreach ($transactions as $idx => $t) : 
                    $pop_title = $popups_by_uid[$t->popup_uid] ?? 'Untitled Popup';
                    $is_completed = ($t->status === 'completed');
                    ?>
                    <tr class="wppoppop-tx-row" data-id="<?php echo esc_attr($t->id); ?>" data-email="<?php echo esc_attr(strtolower($t->email)); ?>" data-txid="<?php echo esc_attr(strtolower($t->transaction_id)); ?>">
                        <td style="text-align:center;color:#94a3b8;"><?php echo esc_html($t->id); ?></td>
                        <td>
                            <strong style="color:#1e293b;"><code><?php echo esc_html($t->transaction_id); ?></code></strong>
                        </td>
                        <td>
                            <strong><?php echo esc_html($t->email); ?></strong>
                        </td>
                        <td>
                            <strong><?php echo esc_html($pop_title); ?></strong>
                            <div style="font-size:11px;color:#94a3b8;margin-top:2px;"><code><?php echo esc_html($t->popup_uid); ?></code></div>
                        </td>
                        <td style="text-align:right;font-weight:700;color:#10b981;font-size:14px;">
                            $<?php echo number_format(floatval($t->amount), 2); ?> <span style="font-size:11px;color:#64748b;"><?php echo esc_html($t->currency); ?></span>
                        </td>
                        <td style="text-align:center;">
                            <span class="badge" style="display:inline-block;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600;<?php echo strtolower($t->gateway) === 'stripe' ? 'background:#e0e7ff;color:#3730a3;' : 'background:#e0f2fe;color:#0369a1;'; ?>">
                                <?php echo esc_html($t->gateway); ?>
                            </span>
                        </td>
                        <td style="text-align:center;">
                            <span class="badge" style="display:inline-block;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600;<?php echo $is_completed ? 'background:#dcfce7;color:#166534;' : 'background:#fef3c7;color:#92400e;'; ?>">
                                <?php echo esc_html(ucfirst($t->status)); ?>
                            </span>
                        </td>
                        <td style="font-size:12px;color:#64748b;"><?php echo esc_html($t->created_at); ?></td>
                        <td style="text-align:right;">
                            <button type="button" class="button button-small wppoppop-view-receipt-btn"
                                    data-id="<?php echo esc_attr($t->id); ?>"
                                    data-txid="<?php echo esc_attr($t->transaction_id); ?>"
                                    data-email="<?php echo esc_attr($t->email); ?>"
                                    data-amount="<?php echo esc_attr(number_format(floatval($t->amount), 2)); ?>"
                                    data-currency="<?php echo esc_attr($t->currency); ?>"
                                    data-gateway="<?php echo esc_attr($t->gateway); ?>"
                                    data-status="<?php echo esc_attr(ucfirst($t->status)); ?>"
                                    data-popup="<?php echo esc_attr($pop_title); ?>"
                                    data-date="<?php echo esc_attr($t->created_at); ?>">
                                Receipt
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
