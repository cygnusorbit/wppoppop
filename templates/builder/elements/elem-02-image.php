<?php
if (!defined('ABSPATH')) exit;
?>
<!-- DECOMPOSED MODULE 02: IMAGE (image) -->
<div class="element-panel wppoppop-panel-specific" id="panel-elem-image" data-elem-family="media" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;">2. Image Source &amp; Fitting</div>
    <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">IMAGE SOURCE URL</label>
    <div style="display:flex;gap:6px;margin-bottom:10px;">
        <input type="text" id="prop-image-url" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="src" placeholder="https://..." style="flex:1;font-size:12px;">
        <button type="button" id="wppoppop-btn-media-upload" class="button button-secondary wppoppop-media-lib-trigger" style="height:36px;padding:0 10px;font-size:11px;font-weight:700;white-space:nowrap;">WP Media</button>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px;">
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">OBJECT FIT</label>
            <select id="prop-image-fit" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="objectFit" style="font-size:12px;">
                <option value="cover">Cover (Crop)</option>
                <option value="contain">Contain (Full)</option>
                <option value="fill">Stretch Fill</option>
                <option value="scale-down">Scale Down</option>
            </select>
        </div>
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">ALT TEXT</label>
            <input type="text" id="prop-image-alt" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="alt" placeholder="Describe image..." style="font-size:12px;">
        </div>
    </div>
    <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">OPTIONAL CLICK REDIRECT URL</label>
    <input type="text" id="prop-image-link" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="linkUrl" placeholder="https://example.com" style="font-size:12px;">
</div>
