<?php
if (!defined('ABSPATH')) exit;
?>
<!-- DECOMPOSED MODULE 09: BUTTON / LINK / STEP TRIGGER (button / link / link_btn / step_btn) -->
<div class="element-panel wppoppop-panel-specific" id="panel-elem-button" data-elem-family="media" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;">9. Button Label &amp; Click Action</div>
    <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">BUTTON LABEL TEXT</label>
    <input type="text" id="prop-button-label" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="content" placeholder="Click Here" style="margin-bottom:10px;font-size:12px;">
    <input type="hidden" id="prop-link-text" class="wppoppop-gp-bind" data-prop="content">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px;">
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">CLICK ACTION</label>
            <select id="prop-button-action" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="buttonAction" style="font-size:12px;">
                <option value="link">Open URL Link</option>
                <option value="step">Go to Canvas Step</option>
                <option value="close">Close Popup</option>
            </select>
        </div>
        <div id="prop-button-step-wrap">
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">TARGET CANVAS #</label>
            <input type="number" id="prop-goto-canvas" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="goto_canvas" min="1" max="10" value="2" style="font-size:12px;">
        </div>
    </div>
    <div id="prop-button-url-wrap">
        <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">DESTINATION URL</label>
        <input type="text" id="prop-link-url" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="linkUrl" placeholder="https://example.com" style="margin-bottom:10px;font-size:12px;">
        <div style="display:flex;gap:14px;">
            <label style="display:inline-flex;align-items:center;gap:6px;font-size:11px;color:#cbd5e1;cursor:pointer;">
                <input type="checkbox" id="prop-link-blank" class="wppoppop-gp-bind" data-prop="linkBlank" value="1"> Open in new tab (<code>_blank</code>)
            </label>
            <label style="display:inline-flex;align-items:center;gap:6px;font-size:11px;color:#cbd5e1;cursor:pointer;">
                <input type="checkbox" id="prop-link-nofollow" class="wppoppop-gp-bind" data-prop="nofollow" value="1"> <code>rel="nofollow"</code>
            </label>
        </div>
    </div>
</div>
