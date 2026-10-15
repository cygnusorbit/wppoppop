<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!-- DECOMPOSED TAB MODULE: CONDITIONAL LOGIC RULE BUILDER (All 26 Elements) -->
<div class="wppoppop-insp-card" id="insp-section-gp-conditional-logic" style="background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div class="wppoppop-prop-row wppoppop-gp-switch-row">
        <label class="wppoppop-prop-label" for="prop-logic-enabled" style="font-size:11px;font-weight:700;color:#e2e8f0;text-transform:uppercase;">ENABLE CONDITIONAL LOGIC</label>
        <label class="wppoppop-gp-switch">
            <input type="checkbox" id="prop-logic-enabled">
            <span class="wppoppop-gp-slider"></span>
        </label>
    </div>
    <div id="wppoppop-gp-logic-rules-wrap" style="display:none;margin-top:12px;padding-top:12px;border-top:1px solid #1e293b;">
        <div class="wppoppop-gp-2col-grid" style="margin-bottom:10px;">
            <select id="prop-logic-action" class="widefat wppoppop-select wppoppop-input" style="font-size:12px;">
                <option value="show">Show this element</option>
                <option value="hide">Hide this element</option>
            </select>
            <select id="prop-logic-match" class="widefat wppoppop-select wppoppop-input" style="font-size:12px;">
                <option value="all">if all rules match</option>
                <option value="any">if any rule matches</option>
            </select>
        </div>
        <div id="wppoppop-gp-logic-rules-list" class="wppoppop-gp-repeater-list"></div>
        <button type="button" class="wppoppop-gp-btn-outline" id="wppoppop-btn-add-logic-rule">+ Add rule</button>
    </div>
</div>
