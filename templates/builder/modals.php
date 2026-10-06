<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<!-- Embed Codes Dialog Modal -->
<div class="wppoppop-modal-backdrop" id="wppoppop-builder-embed-modal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,0.7);backdrop-filter:blur(3px);z-index:999999;align-items:center;justify-content:center;">
    <div style="background:#ffffff;border-radius:8px;width:520px;max-width:92vw;box-shadow:0 20px 25px -5px rgba(0,0,0,0.25);overflow:hidden;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 20px;background:#0f172a;color:#ffffff;">
            <h3 style="margin:0;font-size:14px;font-weight:700;">Embed Codes & Shortcodes</h3>
            <button type="button" id="wppoppop-builder-embed-close" style="background:transparent;border:none;color:#94a3b8;font-size:22px;cursor:pointer;line-height:1;">&times;</button>
        </div>
        <div style="padding:20px;">
            <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">Standard Shortcode</label>
            <input type="text" class="widefat" id="wppoppop-embed-sc" readonly style="font-family:monospace;margin-bottom:12px;">

            <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">Content Locker Shortcode</label>
            <input type="text" class="widefat" id="wppoppop-embed-locker" readonly style="font-family:monospace;margin-bottom:12px;">

            <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">Click Trigger Link</label>
            <input type="text" class="widefat" id="wppoppop-embed-click" readonly style="font-family:monospace;">
        </div>
    </div>
</div>

<!-- Live Sandbox Preview Modal -->
<div class="wppoppop-modal-backdrop" id="wppoppop-builder-preview-modal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,0.8);backdrop-filter:blur(4px);z-index:999999;align-items:center;justify-content:center;">
    <div style="background:#ffffff;border-radius:8px;width:720px;max-width:95vw;max-height:90vh;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 25px 50px -12px rgba(0,0,0,0.5);">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 18px;background:#0f172a;color:#ffffff;">
            <h3 style="margin:0;font-size:14px;font-weight:700;">Interactive Sandbox Preview</h3>
            <button type="button" id="wppoppop-builder-preview-close" style="background:transparent;border:none;color:#94a3b8;font-size:22px;cursor:pointer;line-height:1;">&times;</button>
        </div>
        <div id="wppoppop-preview-sandbox-root" style="flex:1;overflow:auto;padding:30px;display:flex;align-items:center;justify-content:center;background:#f1f5f9;"></div>
    </div>
</div>
