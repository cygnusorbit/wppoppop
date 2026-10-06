<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<!-- Embed Shortcodes Modal -->
<div class="wppoppop-modal-backdrop" id="wppoppop-builder-embed-modal">
    <div style="background:#ffffff;border-radius:8px;width:520px;max-width:92vw;overflow:hidden;box-shadow:0 20px 25px -5px rgba(0,0,0,0.25);">
        <div style="background:#111827;color:#ffffff;padding:12px 16px;display:flex;align-items:center;justify-content:space-between;">
            <span style="font-weight:700;font-size:13px;">Embed Codes & Shortcodes</span>
            <button type="button" id="wppoppop-builder-embed-close" style="background:transparent;border:none;color:#9ca3af;font-size:20px;cursor:pointer;line-height:1;">&times;</button>
        </div>
        <div style="padding:16px;">
            <label style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;color:#475569;">Standard Popup Shortcode</label>
            <input type="text" id="wppoppop-embed-sc" readonly style="width:100%;padding:6px 10px;font-family:monospace;font-size:12px;margin-bottom:12px;box-sizing:border-box;">

            <label style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;color:#475569;">Content Locker Shortcode</label>
            <input type="text" id="wppoppop-embed-locker" readonly style="width:100%;padding:6px 10px;font-family:monospace;font-size:12px;margin-bottom:12px;box-sizing:border-box;">

            <label style="display:block;font-size:11px;font-weight:700;margin-bottom:4px;color:#475569;">Manual Button Click Trigger HTML</label>
            <input type="text" id="wppoppop-embed-click" readonly style="width:100%;padding:6px 10px;font-family:monospace;font-size:12px;box-sizing:border-box;">
        </div>
    </div>
</div>

<!-- Live Preview Sandbox Modal -->
<div class="wppoppop-modal-backdrop" id="wppoppop-builder-preview-modal">
    <div style="background:#ffffff;border-radius:8px;width:740px;max-width:95vw;max-height:90vh;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 25px 50px -12px rgba(0,0,0,0.5);">
        <div style="background:#111827;color:#ffffff;padding:12px 16px;display:flex;align-items:center;justify-content:space-between;">
            <span style="font-weight:700;font-size:13px;">Interactive Live Sandbox Preview</span>
            <button type="button" id="wppoppop-builder-preview-close" style="background:transparent;border:none;color:#9ca3af;font-size:20px;cursor:pointer;line-height:1;">&times;</button>
        </div>
        <div id="wppoppop-preview-sandbox-root" style="flex:1;overflow:auto;padding:30px;display:flex;align-items:center;justify-content:center;background:#f1f5f9;"></div>
    </div>
</div>
