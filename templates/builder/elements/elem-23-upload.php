<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!-- DECOMPOSED MODULE 23: FILE UPLOAD (upload / file) -->
<div class="wppoppop-elem-panel element-panel wppoppop-panel-specific" id="panel-elem-upload" data-elem-family="form" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;"><?php esc_html_e( '23. File Upload Settings', 'wppoppop' ); ?></div>
    <div style="margin-bottom:10px;">
        <label for="prop-upload-placeholder" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'DROPZONE PROMPT LABEL', 'wppoppop' ); ?></label>
        <input type="text" id="prop-upload-placeholder" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="placeholder" value="Choose file or drag here..." style="font-size:12px;">
    </div>
    <div class="wppoppop-gp-2col-grid" style="margin-bottom:8px;">
        <div class="wppoppop-gp-col">
            <label for="prop-upload-exts" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'ALLOWED EXTENSIONS', 'wppoppop' ); ?></label>
            <input type="text" id="prop-upload-exts" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="allowedExtensions" value=".jpg,.png,.pdf" style="font-size:12px;">
        </div>
        <div class="wppoppop-gp-col">
            <label for="prop-upload-maxsize" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'MAX SIZE (MB)', 'wppoppop' ); ?></label>
            <input type="number" id="prop-upload-maxsize" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="maxFileSize" value="5" min="1" max="100" style="font-size:12px;">
        </div>
    </div>
    <label style="display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:600;color:#cbd5e1;cursor:pointer;">
        <input type="checkbox" id="prop-upload-multiple" class="wppoppop-gp-bind" data-prop="multiple" value="1"> <?php esc_html_e( 'Allow Multiple File Upload', 'wppoppop' ); ?>
    </label>
</div>
