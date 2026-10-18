<?php
if (!defined('ABSPATH')) exit;
?>
<!-- DECOMPOSED MODULE 09: BUTTON / LINK / STEP TRIGGER (button / link / link_btn / step_btn) -->
<div class="element-panel wppoppop-panel-specific" id="panel-elem-button" data-elem-family="media" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;">9. Button Label &amp; Click Action</div>
    <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">BUTTON LABEL TEXT</label>
    <input type="text" id="prop-button-label" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="content" value="Click Here" placeholder="Click Here" style="margin-bottom:10px;font-size:12px;">
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
<style>
.wpp-sw{display:flex;align-items:center;gap:10px;font-size:11px;color:#cbd5e1;cursor:pointer;position:relative}
.wpp-sw input{position:absolute;opacity:0;width:0;height:0}
.wpp-sw-track{position:relative;flex:0 0 34px;width:34px;height:18px;border-radius:18px;background:#334155;transition:background .15s ease}
.wpp-sw-track:after{content:"";position:absolute;top:2px;left:2px;width:14px;height:14px;border-radius:50%;background:#fff;transition:transform .15s ease}
.wpp-sw input:checked+.wpp-sw-track{background:#10b981}
.wpp-sw input:checked+.wpp-sw-track:after{transform:translateX(16px)}
.wpp-sw input:focus-visible+.wpp-sw-track{box-shadow:0 0 0 2px #38bdf8}
</style>

        <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">DESTINATION URL</label>
        <input type="text" id="prop-link-url" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="linkUrl" placeholder="https://example.com" style="margin-bottom:10px;font-size:12px;">
        <div style="display:flex;flex-direction:column;gap:10px;">
            <label class="wpp-sw"><input type="checkbox" id="prop-link-blank" class="wppoppop-gp-bind" data-prop="linkBlank" value="1"><span class="wpp-sw-track"></span><span>Open in new tab (<code>_blank</code>)</span></label>
            <label class="wpp-sw"><input type="checkbox" id="prop-link-nofollow" class="wppoppop-gp-bind" data-prop="nofollow" value="1"><span class="wpp-sw-track"></span><span><code>rel="nofollow"</code></span></label>
        </div>
    </div>
</div>
