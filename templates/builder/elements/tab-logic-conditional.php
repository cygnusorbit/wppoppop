<?php
if (!defined('ABSPATH')) exit;
/**
 * Decomposed Module: Logic Tab Suite (All Elements)
 * Conditional Logic Rule Builder & Action Routing
 */
?>
<div class="wppoppop-insp-section" id="insp-section-conditional-logic">
    <div class="wppoppop-insp-section-title">CONDITIONAL LOGIC</div>
    <div class="wppoppop-prop-row wppoppop-switch-row" style="display:flex; justify-content:space-between; align-items:center;">
        <label for="prop-logic-enabled" style="font-size:11px; font-weight:700; color:#e2e8f0; margin:0;">Enable Conditional Logic</label>
        <input type="checkbox" id="prop-logic-enabled" class="wppoppop-switch wppoppop-logic-bind" data-logic-prop="logicEnabled" />
    </div>
    <div id="wppoppop-logic-body" style="display:none; margin-top:10px;">
        <div class="wppoppop-prop-row" style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
            <div>
                <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px; text-transform:uppercase;">Action</label>
                <select id="prop-logic-action" class="widefat wppoppop-input wppoppop-logic-bind" data-logic-prop="logicAction" style="font-size:12px;">
                    <option value="show">Show Layer</option>
                    <option value="hide">Hide Layer</option>
                </select>
            </div>
            <div>
                <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px; text-transform:uppercase;">Match Mode</label>
                <select id="prop-logic-match" class="widefat wppoppop-input wppoppop-logic-bind" data-logic-prop="logicMatch" style="font-size:12px;">
                    <option value="all">If ALL rules match</option>
                    <option value="any">If ANY rule matches</option>
                </select>
            </div>
        </div>
        <div style="margin-top:12px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                <span style="font-size:10px; font-weight:700; color:#94a3b8; text-transform:uppercase;">Conditional Rules</span>
                <button type="button" id="btn-add-logic-rule" class="button button-small" style="font-size:11px; font-weight:700;">+ Add rule</button>
            </div>
            <div id="wppoppop-logic-rules-list" style="display:flex; flex-direction:column; gap:8px;"></div>
        </div>
    </div>
</div>

<div class="wppoppop-insp-section" id="insp-section-action-routing" style="display:none; margin-top:14px; border-top:1px solid #334155; padding-top:12px;">
    <div class="wppoppop-insp-section-title">ACTION &amp; CLICK ROUTING</div>
    <div class="wppoppop-prop-row">
        <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px; text-transform:uppercase;">Click Action</label>
        <select id="prop-logic-click-action" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="buttonAction" style="font-size:12px;">
            <option value="link">Open URL Link</option>
            <option value="step">Go to Canvas Step</option>
            <option value="close">Close Popup</option>
        </select>
    </div>
    <div class="wppoppop-prop-row" id="prop-logic-link-url-row" style="margin-top:8px;">
        <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px; text-transform:uppercase;">Destination URL</label>
        <input type="text" id="prop-logic-link-url" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="linkUrl" placeholder="https://example.com" style="font-size:12px;" />
    </div>
    <div class="wppoppop-prop-row" id="prop-logic-goto-canvas-row" style="display:none; margin-top:8px;">
        <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px; text-transform:uppercase;">Target Canvas #</label>
        <input type="number" id="prop-logic-goto-canvas" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="goto_canvas" min="1" max="10" value="2" style="font-size:12px;" />
    </div>
    <div class="wppoppop-prop-row" style="margin-top:8px;">
        <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px; text-transform:uppercase;">Custom JS Callback (OnClick)</label>
        <input type="text" id="prop-logic-custom-js" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="customJs" placeholder="e.g. console.log('Clicked')" style="font-size:12px;" />
    </div>
</div>
