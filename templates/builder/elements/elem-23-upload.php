<?php
if (!defined('ABSPATH')) exit;
?>
<!-- DECOMPOSED MODULE 23: FILE UPLOAD (upload / file) -->
<div class="element-panel wppoppop-panel-specific" id="panel-elem-upload" data-elem-family="form" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;">23. File Upload Dropzone</div>
    <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">DROPZONE / BUTTON LABEL</label>
    <input type="text" id="prop-upload-label" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="placeholder" placeholder="Choose file or drag here..." style="margin-bottom:10px;font-size:12px;">
    <div style="display:grid;grid-template-columns:2fr 1fr;gap:8px;margin-bottom:8px;">
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">ALLOWED EXTENSIONS</label>
            <input type="text" id="prop-upload-exts" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="allowedExtensions" placeholder="pdf,jpg,png" style="font-size:12px;">
        </div>
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">MAX SIZE (MB)</label>
            <input type="number" id="prop-upload-maxsize" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="maxFileSize" min="1" max="100" style="font-size:12px;">
        </div>
    </div>
    <label style="display:inline-flex;align-items:center;gap:6px;font-size:11px;color:#cbd5e1;cursor:pointer;">
        <input type="checkbox" id="prop-upload-multiple" class="wppoppop-gp-bind" data-prop="multiUpload" value="1"> Allow Multiple Files
    </label>
</div>
