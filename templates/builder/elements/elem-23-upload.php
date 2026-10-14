<?php
if (!defined('ABSPATH')) exit;
?>
<!-- DECOMPOSED MODULE 23: FILE UPLOAD (upload / file) -->
<div class="element-panel wppoppop-panel-specific" id="panel-elem-upload" data-elem-family="form" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;">23. File Upload Settings (Form Family — 5 Tabs)</div>

    <label for="prop-upload-label" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">DROPZONE PROMPT LABEL</label>
    <input type="text" id="prop-upload-label" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="placeholder" value="Choose file or drag here..." style="margin-bottom:10px;font-size:12px;">
    <input type="hidden" id="prop-file-label" class="wppoppop-gp-bind" data-prop="placeholder">

    <label for="prop-upload-fieldname" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">FIELD NAME / TOKEN</label>
    <input type="text" id="prop-upload-fieldname" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="fieldName" value="attachment" style="margin-bottom:10px;font-size:12px;">

    <div style="display:grid;grid-template-columns:2fr 1fr;gap:8px;margin-bottom:10px;">
        <div>
            <label for="prop-upload-ext" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">ALLOWED EXTENSIONS</label>
            <input type="text" id="prop-upload-ext" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="allowedExtensions" value=".jpg,.png,.pdf" style="font-size:12px;">
            <input type="hidden" id="prop-file-exts" class="wppoppop-gp-bind" data-prop="allowedExtensions">
        </div>
        <div>
            <label for="prop-upload-maxmb" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">MAX SIZE (MB)</label>
            <input type="number" id="prop-upload-maxmb" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="maxFileSize" value="5" min="1" max="50" style="font-size:12px;">
        </div>
    </div>

    <div style="display:flex;gap:14px;">
        <label style="display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:600;color:#cbd5e1;cursor:pointer;">
            <input type="checkbox" id="prop-upload-multi" class="wppoppop-gp-bind" data-prop="multiUpload" value="1"> Allow Multiple Files
        </label>
        
    </div>
</div>
