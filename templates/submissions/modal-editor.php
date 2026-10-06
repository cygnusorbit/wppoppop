<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-modal-backdrop" id="wppoppop-submission-modal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,0.7);backdrop-filter:blur(3px);z-index:99999;align-items:center;justify-content:center;">
    <div style="background:#ffffff;border-radius:8px;width:640px;max-width:92vw;box-shadow:0 20px 25px -5px rgba(0,0,0,0.25);overflow:hidden;display:flex;flex-direction:column;max-height:90vh;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;background:#0f172a;color:#ffffff;">
            <h3 style="margin:0;font-size:15px;font-weight:700;">Lead Submission Details & Receipt</h3>
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
                        <span style="font-size:11px;color:#94a3b8;display:block;">Record ID</span>
                        <code style="font-size:14px;font-weight:700;" id="wppoppop-modal-id"></code>
                    </div>
                </div>
            </div>

            <div style="margin-bottom:16px;">
                <h5 style="margin:0 0 10px 0;font-size:12px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:0.5px;">Submitted Form Values</h5>
                <div id="wppoppop-modal-fields-list" style="display:grid;grid-template-columns:1fr 1fr;gap:12px;"></div>
            </div>

            <!-- Digital Signature Preview Container -->
            <div id="wppoppop-modal-sig-wrap" style="display:none;margin-top:16px;border-top:1px dashed #cbd5e1;padding-top:16px;">
                <h5 style="margin:0 0 8px 0;font-size:12px;font-weight:700;color:#475569;text-transform:uppercase;">Digital Signature</h5>
                <div style="border:1px solid #e2e8f0;border-radius:6px;background:#f8fafc;padding:10px;display:inline-block;">
                    <img id="wppoppop-modal-sig-img" src="" alt="Digital Signature" style="max-height:80px;display:block;">
                </div>
            </div>

            <!-- Attribution & Campaign Data -->
            <div id="wppoppop-modal-utm-wrap" style="display:none;margin-top:16px;border-top:1px dashed #cbd5e1;padding-top:16px;">
                <h5 style="margin:0 0 8px 0;font-size:12px;font-weight:700;color:#475569;text-transform:uppercase;">Campaign Attribution</h5>
                <div id="wppoppop-modal-utm-list" style="font-size:12px;color:#64748b;display:flex;gap:8px;flex-wrap:wrap;"></div>
            </div>
        </div>
        <div style="display:flex;justify-content:space-between;align-items:center;padding:14px 20px;background:#f8fafc;border-top:1px solid #e2e8f0;">
            <button type="button" class="button" id="wppoppop-sub-modal-print" style="display:inline-flex;align-items:center;gap:4px;">
                <span class="dashicons dashicons-printer" style="font-size:16px;width:16px;height:16px;"></span> Print Receipt
            </button>
            <button type="button" class="button button-primary" id="wppoppop-sub-modal-done">Close</button>
        </div>
    </div>
</div>
