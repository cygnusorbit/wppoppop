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
    <style>
    #panel-elem-upload .wppoppop-up-slider{position:absolute;inset:0;background:#475569;border-radius:20px;transition:background .15s ease;cursor:pointer;}
    #panel-elem-upload .wppoppop-up-slider::before{content:"";position:absolute;left:2px;top:2px;width:16px;height:16px;background:#fff;border-radius:50%;transition:transform .15s ease;}
    #panel-elem-upload #prop-upload-multiple:checked + .wppoppop-up-slider{background:#0284c7;}
    #panel-elem-upload #prop-upload-multiple:checked + .wppoppop-up-slider::before{transform:translateX(16px);}
    #panel-elem-upload #prop-upload-multiple:focus + .wppoppop-up-slider{box-shadow:0 0 0 2px rgba(56,189,248,.5);}
    </style>
    <label style="display:flex;align-items:center;justify-content:space-between;gap:8px;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;cursor:pointer;margin-bottom:8px;">
        <span>Allow Multiple Files</span>
        <span style="position:relative;display:inline-block;width:36px;height:20px;flex-shrink:0;">
            <input type="checkbox" id="prop-upload-multiple" class="wppoppop-gp-bind" data-prop="multiUpload" value="1" style="position:absolute;opacity:0;width:0;height:0;">
            <span class="wppoppop-up-slider"></span>
        </span>
    </label>
    <div id="prop-upload-maxfiles-row" style="display:none;margin-bottom:8px;">
        <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">MAX NUMBER OF FILES</label>
        <input type="number" id="prop-upload-maxfiles" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="maxFiles" min="1" max="20" placeholder="e.g. 3" style="font-size:12px;">
    </div>
    <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">ERROR MESSAGE (FILE TOO LARGE)</label>
    <input type="text" id="prop-upload-errmsg" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="uploadErrorMessage" placeholder="File too large. Maximum size is {max}MB." style="font-size:12px;">
    <div style="font-size:9px;color:#64748b;margin-top:3px;">Tokens: {max} = size limit (MB), {file} = file name</div>
</div>
