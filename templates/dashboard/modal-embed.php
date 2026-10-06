<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-modal-backdrop" id="wppoppop-dash-embed-modal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,0.7);backdrop-filter:blur(3px);z-index:99999;align-items:center;justify-content:center;">
    <div style="background:#ffffff;border-radius:8px;width:520px;max-width:90vw;box-shadow:0 20px 25px -5px rgba(0,0,0,0.25);overflow:hidden;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 20px;background:#0f172a;color:#ffffff;">
            <h3 style="margin:0;font-size:14px;font-weight:700;">Embed Snippets</h3>
            <button type="button" id="wppoppop-dash-embed-close" style="background:transparent;border:none;color:#94a3b8;font-size:22px;cursor:pointer;line-height:1;">&times;</button>
        </div>
        <div style="padding:20px;">
            <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">Standard Shortcode</label>
            <input type="text" class="widefat" id="wppoppop-dash-sc-field" readonly style="font-family:monospace;">
            
            <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin:12px 0 4px 0;">Content Locker Shortcode</label>
            <input type="text" class="widefat" id="wppoppop-dash-locker-field" readonly style="font-family:monospace;">
            
            <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin:12px 0 4px 0;">Button Click Trigger (HTML Link)</label>
            <input type="text" class="widefat" id="wppoppop-dash-btn-field" readonly style="font-family:monospace;">
        </div>
    </div>
</div>
