<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-modal-backdrop" id="wppoppop-submission-modal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,0.7);backdrop-filter:blur(3px);z-index:99999;align-items:center;justify-content:center;">
    <div style="background:#ffffff;border-radius:8px;width:640px;max-width:92vw;box-shadow:0 20px 25px -5px rgba(0,0,0,0.25);overflow:hidden;display:flex;flex-direction:column;max-height:90vh;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;background:#0f172a;color:#ffffff;">
            <h3 style="margin:0;font-size:15px;font-weight:700;"><?php esc_html_e('Lead Submission Details & Receipt', 'wppoppop'); ?></h3>
            <button type="button" id="wppoppop-sub-modal-close" style="background:transparent;border:none;color:#94a3b8;font-size:22px;cursor:pointer;line-height:1;">&times;</button>
        </div>
        <div id="wppoppop-receipt-printable-area" style="padding:24px;overflow-y:auto;flex:1;">
            <div style="border-bottom:1px solid #e2e8f0;padding-bottom:16px;margin-bottom:16px;">
                <div style="display:flex;justify-content:space-between;">
                    <div>
                        <h4 style="margin:0 0 4px 0;font-size:18px;color:#1e293b;" id="wppoppop-modal-email"></h4>
                        <span style="font-size:12px;color:#64748b;" id="wppoppop-modal-meta"></span>
                    </div>
                    <div style="text-align:right;">
                        <span style="font-size:11px;color:#94a3b8;display:block;"><?php esc_html_e('Record ID', 'wppoppop'); ?></span>
                        <code style="font-size:14px;font-weight:700;" id="wppoppop-modal-id"></code>
                    </div>
                </div>
            </div>

            <div style="margin-bottom:16px;">
                <h5 style="margin:0 0 10px 0;font-size:12px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:0.5px;"><?php esc_html_e('Submitted Form Values', 'wppoppop'); ?></h5>
                <div id="wppoppop-modal-fields-list" style="display:grid;grid-template-columns:1fr 1fr;gap:12px;"></div>
            </div>

            <!-- Digital Signature Preview Container -->
            <div id="wppoppop-modal-sig-wrap" style="display:none;margin-top:16px;border-top:1px dashed #cbd5e1;padding-top:16px;">
                <h5 style="margin:0 0 8px 0;font-size:12px;font-weight:700;color:#475569;text-transform:uppercase;"><?php esc_html_e('Digital Signature', 'wppoppop'); ?></h5>
                <div style="border:1px solid #e2e8f0;border-radius:6px;background:#f8fafc;padding:10px;display:inline-block;">
                    <img id="wppoppop-modal-sig-img" src="" alt="<?php esc_attr_e('Digital Signature', 'wppoppop'); ?>" style="max-height:80px;display:block;">
                </div>
            </div>

            <!-- Attribution & Campaign Data -->
            <div id="wppoppop-modal-utm-wrap" style="display:none;margin-top:16px;border-top:1px dashed #cbd5e1;padding-top:16px;">
                <h5 style="margin:0 0 8px 0;font-size:12px;font-weight:700;color:#475569;text-transform:uppercase;"><?php esc_html_e('Campaign Attribution', 'wppoppop'); ?></h5>
                <div id="wppoppop-modal-utm-list" style="font-size:12px;color:#64748b;display:flex;gap:8px;flex-wrap:wrap;"></div>
            </div>
        </div>
        <div style="display:flex;justify-content:space-between;align-items:center;padding:14px 20px;background:#f8fafc;border-top:1px solid #e2e8f0;">
            <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
                <label for="wppoppop-sub-resend-target" class="screen-reader-text"><?php esc_html_e('Resend to', 'wppoppop'); ?></label>
                <select id="wppoppop-sub-resend-target" style="height:32px;min-height:32px;box-sizing:border-box;margin:0;padding:0 24px 0 8px;line-height:1;vertical-align:middle;">
                    <option value="all"><?php esc_html_e('All', 'wppoppop'); ?></option>
                    <option value="admin"><?php esc_html_e('Admin', 'wppoppop'); ?></option>
                    <option value="user"><?php esc_html_e('User', 'wppoppop'); ?></option>
                </select>
                <button type="button" class="button" id="wppoppop-sub-modal-resend" style="display:inline-flex;align-items:center;gap:4px;height:32px;min-height:32px;box-sizing:border-box;margin:0;padding:0 12px;line-height:1;vertical-align:middle;">
                    <span class="dashicons dashicons-email-alt" style="font-size:16px;width:16px;height:16px;"></span> <?php esc_html_e('Resend Notification', 'wppoppop'); ?>
                </button>
                <span id="wppoppop-sub-resend-msg" role="status" aria-live="polite" style="font-size:12px;color:#64748b;"></span>
            </div>
            <button type="button" class="button button-primary" id="wppoppop-sub-modal-done"><?php esc_html_e('Close', 'wppoppop'); ?></button>
        </div>
    </div>
</div>
