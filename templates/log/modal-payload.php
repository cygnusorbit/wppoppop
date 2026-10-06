<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-modal-backdrop" id="wppoppop-payload-modal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,0.7);backdrop-filter:blur(3px);z-index:99999;align-items:center;justify-content:center;">
    <div style="background:#ffffff;border-radius:8px;width:600px;max-width:92vw;box-shadow:0 20px 25px -5px rgba(0,0,0,0.25);overflow:hidden;display:flex;flex-direction:column;max-height:85vh;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;background:#0f172a;color:#ffffff;">
            <h3 style="margin:0;font-size:15px;font-weight:700;">Event Payload Details</h3>
            <button type="button" id="wppoppop-payload-close" style="background:transparent;border:none;color:#94a3b8;font-size:22px;cursor:pointer;line-height:1;">&times;</button>
        </div>
        <div style="padding:20px;overflow-y:auto;flex:1;">
            <div style="margin-bottom:14px;">
                <span style="font-size:11px;color:#64748b;font-weight:600;display:block;text-transform:uppercase;">Event & Time</span>
                <strong style="font-size:14px;color:#1e293b;" id="wppoppop-modal-log-title"></strong>
                <p style="margin:4px 0 0;font-size:12px;color:#475569;" id="wppoppop-modal-log-desc"></p>
            </div>

            <label style="display:block;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;margin-bottom:6px;">Raw JSON Payload</label>
            <pre id="wppoppop-modal-payload-code" style="background:#0f172a;color:#38bdf8;padding:16px;border-radius:6px;font-size:12px;overflow:auto;max-height:280px;margin:0;line-height:1.4;"></pre>
        </div>
        <div style="display:flex;justify-content:space-between;align-items:center;padding:14px 20px;background:#f8fafc;border-top:1px solid #e2e8f0;">
            <button type="button" class="button" id="wppoppop-payload-copy-btn">Copy Payload</button>
            <button type="button" class="button button-primary" id="wppoppop-payload-done">Close</button>
        </div>
    </div>
</div>
