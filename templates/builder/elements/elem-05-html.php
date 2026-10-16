<?php
if (!defined('ABSPATH')) exit;
?>
<!-- DECOMPOSED MODULE 05: RAW HTML CONTENT (html) -->
<div class="element-panel wppoppop-panel-specific" id="panel-elem-html" data-elem-family="media" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;">5. Custom HTML / SVG Code</div>
    <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">RAW HTML / SVG / EMBED MARKUP</label>
    <textarea id="prop-html-code" rows="5" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="content" placeholder="&lt;div class=&quot;custom-box&quot;&gt;Custom HTML...&lt;/div&gt;" style="font-family:monospace;font-size:11px;margin-bottom:8px;"></textarea>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">WRAPPER TAG</label>
            <select id="prop-html-tag" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="tag" style="font-size:12px;">
                <option value="div">&lt;div&gt;</option>
                <option value="span">&lt;span&gt;</option>
            </select>
        </div>
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">CONTAINER OVERFLOW</label>
            <select id="prop-html-overflow" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="overflow" style="font-size:12px;">
                <option value="hidden">Hidden (Clip to Box)</option>
                <option value="auto">Scrollable (Auto)</option>
                <option value="visible">Visible</option>
            </select>
        </div>
    </div>
</div>
