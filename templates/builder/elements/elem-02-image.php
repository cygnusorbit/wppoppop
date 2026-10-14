<?php
if (!defined('ABSPATH')) exit;
if (function_exists('wp_enqueue_media')) {
    wp_enqueue_media();
}
?>
<!-- DECOMPOSED MODULE 02: IMAGE (image) -->
<div class="element-panel wppoppop-panel-specific" id="panel-elem-image" data-elem-family="media" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;">2. Image Settings (Media Family)</div>

    <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">IMAGE PREVIEW &amp; SELECTION</label>
    <div id="prop-image-preview-wrap" style="width:100%;height:110px;background:#090d16;border:1px dashed #334155;border-radius:6px;display:flex;align-items:center;justify-content:center;margin-bottom:8px;overflow:hidden;position:relative;">
        <img id="prop-image-preview" src="" alt="Preview" style="max-width:100%;max-height:100%;object-fit:contain;display:none;">
        <span id="prop-image-placeholder" style="font-size:11px;color:#64748b;font-weight:600;">No image selected</span>
    </div>

    <div style="display:flex;gap:6px;margin-bottom:10px;">
        <button type="button" id="prop-image-upload-btn" class="button button-primary wppoppop-media-upload-trigger" style="flex:1;height:34px;font-size:11px;font-weight:700;background:#2563eb;border-color:#3b82f6;color:#ffffff;cursor:pointer;">Select from Media Library</button>
        <button type="button" id="prop-image-remove-btn" class="button wppoppop-media-remove-trigger" style="height:34px;font-size:11px;font-weight:600;color:#ef4444;background:#090d16;border:1px solid #334155;cursor:pointer;">Remove</button>
    </div>

    <label for="prop-image-url" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">IMAGE SOURCE URL</label>
    <input type="text" id="prop-image-url" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="imageUrl" placeholder="https://..." style="margin-bottom:10px;font-size:12px;">

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px;">
        <div>
            <label for="prop-image-fit" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">OBJECT FIT</label>
            <select id="prop-image-fit" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="objectFit" style="font-size:12px;">
                <option value="cover">Cover (Scale &amp; Crop)</option>
                <option value="contain">Contain (Full Image)</option>
                <option value="fill">Fill (Stretch)</option>
                <option value="none">Original Dimensions</option>
            </select>
        </div>
        <div>
            <label for="prop-image-alt" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">ALT TEXT</label>
            <input type="text" id="prop-image-alt" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="imageAlt" placeholder="Image description..." style="font-size:12px;">
        </div>
    </div>

    <label for="prop-image-link" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">OPTIONAL CLICK REDIRECT URL</label>
    <input type="text" id="prop-image-link" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="linkUrl" placeholder="https://example.com" style="font-size:12px;">
</div>
