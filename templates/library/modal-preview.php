<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-modal-backdrop" id="wppoppop-lib-preview-modal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,0.7);backdrop-filter:blur(3px);z-index:99999;align-items:center;justify-content:center;">
    <div style="background:#ffffff;border-radius:10px;width:600px;max-width:92vw;box-shadow:0 20px 25px -5px rgba(0,0,0,0.25);overflow:hidden;display:flex;flex-direction:column;max-height:85vh;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;background:#0f172a;color:#ffffff;">
            <h3 style="margin:0;font-size:15px;font-weight:700;" id="wppoppop-lib-modal-title">Template Details</h3>
            <button type="button" id="wppoppop-lib-modal-close" style="background:transparent;border:none;color:#94a3b8;font-size:22px;cursor:pointer;line-height:1;">&times;</button>
        </div>
        <div style="padding:24px;overflow-y:auto;flex:1;">
            <p id="wppoppop-lib-modal-desc" style="font-size:13px;color:#475569;margin:0 0 16px 0;line-height:1.5;"></p>
            
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:20px;">
                <div style="background:#f8fafc;padding:10px 14px;border-radius:6px;border:1px solid #e2e8f0;">
                    <span style="font-size:11px;color:#64748b;font-weight:600;display:block;text-transform:uppercase;">Dimensions</span>
                    <strong style="font-size:14px;color:#1e293b;" id="wppoppop-lib-modal-dims"></strong>
                </div>
                <div style="background:#f8fafc;padding:10px 14px;border-radius:6px;border:1px solid #e2e8f0;">
                    <span style="font-size:11px;color:#64748b;font-weight:600;display:block;text-transform:uppercase;">Included Elements</span>
                    <strong style="font-size:14px;color:#1e293b;" id="wppoppop-lib-modal-elements"></strong>
                </div>
            </div>

            <div style="background:#f1f5f9;border-radius:8px;padding:16px;border:1px dashed #cbd5e1;text-align:center;">
                <span class="dashicons dashicons-admin-appearance" style="font-size:32px;width:32px;height:32px;color:#64748b;display:block;margin:0 auto 6px auto;"></span>
                <span style="font-size:12px;color:#64748b;">
                    Importing this template will instantiate a new copy in your campaigns database and open it directly in the visual builder canvas.
                </span>
            </div>
        </div>
        <div style="display:flex;justify-content:flex-end;gap:10px;padding:14px 20px;background:#f8fafc;border-top:1px solid #e2e8f0;">
            <button type="button" class="button" id="wppoppop-lib-modal-cancel">Cancel</button>
            <button type="button" class="button button-primary" id="wppoppop-lib-modal-import-action" style="background:#c2185b;border-color:#ad1457;">
                Import & Edit Template
            </button>
        </div>
    </div>
</div>
