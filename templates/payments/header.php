<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-payments-header" style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;padding-top:10px;">
    <div>
        <h1 class="wp-heading-inline" style="margin:0;font-size:24px;font-weight:700;color:#1e293b;"><?php esc_html_e('Payments & Sales Transactions', 'wppoppop'); ?></h1>
        <p style="margin:4px 0 0;color:#64748b;font-size:13px;"><?php esc_html_e('Monitor collected payments, review gateway transactions, and print sales receipts.', 'wppoppop'); ?></p>
    </div>
    <div style="display:flex;gap:10px;align-items:center;">
        <input type="text" id="wppoppop-payments-search" placeholder="<?php esc_attr_e('Search by email or transaction ID...', 'wppoppop'); ?>" style="width:260px;padding:6px 12px;font-size:13px;border-radius:4px;border:1px solid #cbd5e1;">
        <button type="button" class="button button-primary" id="wppoppop-btn-export-sales-csv" style="display:inline-flex;align-items:center;gap:6px;">
            <span class="dashicons dashicons-download" style="font-size:16px;width:16px;height:16px;"></span> <?php esc_html_e('Export Sales CSV', 'wppoppop'); ?>
        </button>
    </div>
</div>
