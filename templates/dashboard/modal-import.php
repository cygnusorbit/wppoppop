<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-modal-backdrop" id="wppoppop-import-modal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,0.7);backdrop-filter:blur(3px);z-index:99999;align-items:center;justify-content:center;">
    <div style="background:#ffffff;border-radius:8px;width:500px;max-width:90vw;box-shadow:0 20px 25px -5px rgba(0,0,0,0.25);overflow:hidden;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 20px;background:#0f172a;color:#ffffff;">
            <h3 style="margin:0;font-size:14px;font-weight:700;">Import Popup Template</h3>
            <button type="button" id="wppoppop-import-close" style="background:transparent;border:none;color:#94a3b8;font-size:22px;cursor:pointer;line-height:1;">&times;</button>
        </div>
        <div style="padding:20px;">
            <label style="display:block;font-size:12px;font-weight:600;color:#334155;margin-bottom:6px;">Upload JSON Export File</label>
            <input type="file" id="wppoppop-import-file" accept=".json" style="width:100%;margin-bottom:12px;font-size:12px;">
            
            <label style="display:block;font-size:12px;font-weight:600;color:#334155;margin-bottom:6px;">Or Paste Raw JSON Configuration</label>
            <textarea id="wppoppop-import-raw" rows="6" class="widefat" placeholder="{ &quot;meta&quot;: { ... } }" style="font-family:monospace;font-size:12px;"></textarea>
            
            <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:16px;">
                <button type="button" class="button" id="wppoppop-import-cancel">Cancel</button>
                <button type="button" class="button button-primary" id="wppoppop-import-submit">Import Popup</button>
            </div>
        </div>
    </div>
</div>
