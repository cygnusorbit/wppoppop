<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-modal-backdrop" id="wppoppop-receipt-modal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,0.7);backdrop-filter:blur(3px);z-index:99999;align-items:center;justify-content:center;">
    <div style="background:#ffffff;border-radius:8px;width:560px;max-width:92vw;box-shadow:0 20px 25px -5px rgba(0,0,0,0.25);overflow:hidden;display:flex;flex-direction:column;max-height:85vh;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;background:#0f172a;color:#ffffff;">
            <h3 style="margin:0;font-size:15px;font-weight:700;"><?php esc_html_e('Customer Payment Receipt', 'wppoppop'); ?></h3>
            <button type="button" id="wppoppop-receipt-close" style="background:transparent;border:none;color:#94a3b8;font-size:22px;cursor:pointer;line-height:1;">&times;</button>
        </div>
        <div id="wppoppop-receipt-area" style="padding:24px;overflow-y:auto;flex:1;">
            <div style="border-bottom:1px solid #e2e8f0;padding-bottom:16px;margin-bottom:16px;display:flex;justify-content:space-between;align-items:flex-start;">
                <div>
                    <h4 style="margin:0 0 4px 0;font-size:18px;color:#1e293b;"><?php esc_html_e('Payment Receipt', 'wppoppop'); ?></h4>
                    <span style="font-size:12px;color:#64748b;" id="wppoppop-rcpt-date"></span>
                </div>
                <div style="text-align:right;">
                    <span style="font-size:11px;color:#94a3b8;display:block;"><?php esc_html_e('Transaction Hash', 'wppoppop'); ?></span>
                    <code style="font-size:13px;font-weight:700;" id="wppoppop-rcpt-txid"></code>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
                <div style="background:#f8fafc;padding:12px;border-radius:6px;border:1px solid #e2e8f0;">
                    <label style="display:block;font-size:11px;color:#64748b;font-weight:600;text-transform:uppercase;"><?php esc_html_e('Customer', 'wppoppop'); ?></label>
                    <div style="font-weight:700;font-size:14px;color:#1e293b;margin-top:2px;" id="wppoppop-rcpt-email"></div>
                </div>
                <div style="background:#f8fafc;padding:12px;border-radius:6px;border:1px solid #e2e8f0;">
                    <label style="display:block;font-size:11px;color:#64748b;font-weight:600;text-transform:uppercase;"><?php esc_html_e('Origin Campaign', 'wppoppop'); ?></label>
                    <div style="font-weight:700;font-size:14px;color:#1e293b;margin-top:2px;" id="wppoppop-rcpt-popup"></div>
                </div>
            </div>

            <table style="width:100%;border-collapse:collapse;margin-bottom:20px;font-size:13px;">
                <thead>
                    <tr style="border-bottom:2px solid #e2e8f0;text-align:left;">
                        <th style="padding:8px 0;color:#64748b;"><?php esc_html_e('Description', 'wppoppop'); ?></th>
                        <th style="padding:8px 0;color:#64748b;"><?php esc_html_e('Gateway', 'wppoppop'); ?></th>
                        <th style="padding:8px 0;color:#64748b;text-align:right;"><?php esc_html_e('Status', 'wppoppop'); ?></th>
                        <th style="padding:8px 0;color:#64748b;text-align:right;"><?php esc_html_e('Amount', 'wppoppop'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="border-bottom:1px solid #f1f5f9;">
                        <td style="padding:12px 0;font-weight:600;color:#1e293b;"><?php esc_html_e('Popup Offer Purchase', 'wppoppop'); ?></td>
                        <td style="padding:12px 0;" id="wppoppop-rcpt-gateway"></td>
                        <td style="padding:12px 0;text-align:right;color:#166534;font-weight:600;" id="wppoppop-rcpt-status"></td>
                        <td style="padding:12px 0;text-align:right;font-weight:700;color:#1e293b;" id="wppoppop-rcpt-amount"></td>
                    </tr>
                </tbody>
            </table>

            <div style="text-align:right;border-top:2px solid #e2e8f0;padding-top:12px;">
                <span style="font-size:12px;color:#64748b;margin-right:12px;"><?php esc_html_e('Total Paid:', 'wppoppop'); ?></span>
                <span style="font-size:22px;font-weight:800;color:#10b981;" id="wppoppop-rcpt-total"></span>
            </div>
        </div>
        <div style="display:flex;justify-content:space-between;align-items:center;padding:14px 20px;background:#f8fafc;border-top:1px solid #e2e8f0;">
            <button type="button" class="button" id="wppoppop-rcpt-print" style="display:inline-flex;align-items:center;gap:4px;">
                <span class="dashicons dashicons-printer" style="font-size:16px;width:16px;height:16px;"></span> <?php esc_html_e('Print Receipt', 'wppoppop'); ?>
            </button>
            <button type="button" class="button button-primary" id="wppoppop-rcpt-done"><?php esc_html_e('Close', 'wppoppop'); ?></button>
        </div>
    </div>
</div>
