<?php
if (!defined('ABSPATH')) exit;
?>
<!-- DECOMPOSED MODULE 16: CHECKBOX / CONSENT (checkbox / checkboxes) -->
<div class="element-panel wppoppop-panel-specific" id="panel-elem-checkbox" data-elem-family="form" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;">16. Checkbox Settings (Form Family — 5 Tabs)</div>

    <label for="prop-checkbox-options" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">CHECKBOX OPTIONS / CONSENT TEXT (COMMA SEPARATED)</label>
    <textarea id="prop-checkbox-options" rows="2" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="options" placeholder="I agree to the Terms &amp; Privacy Policy" style="margin-bottom:10px;font-size:12px;">I agree to the terms and conditions</textarea>
    <input type="hidden" id="prop-checkboxes-options" class="wppoppop-gp-bind" data-prop="options">

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px;">
        <div>
            <label for="prop-checkbox-fieldname" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">FIELD TOKEN</label>
            <input type="text" id="prop-checkbox-fieldname" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="fieldName" value="consent" style="font-size:12px;">
        </div>
        <div>
            <label for="prop-checkbox-layout" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">ORIENTATION</label>
            <select id="prop-checkbox-layout" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="layout" style="font-size:12px;">
                <option value="vertical">Stacked (Vertical)</option>
                <option value="inline">Inline (Horizontal)</option>
            </select>
        </div>
    </div>

    <label for="prop-checkbox-link" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">OPTIONAL TERMS / PRIVACY LINK URL</label>
    <input type="text" id="prop-checkbox-link" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="termsUrl" placeholder="https://example.com/privacy" style="margin-bottom:10px;font-size:12px;">

    <div style="display:flex;gap:14px;">
        <label style="display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:600;color:#cbd5e1;cursor:pointer;">
            <input type="checkbox" id="prop-checkbox-checked" class="wppoppop-gp-bind" data-prop="checkedDefault" value="1"> Checked by Default
        </label>
        <label style="display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:600;color:#cbd5e1;cursor:pointer;">
            <input type="checkbox" id="prop-checkbox-required" class="wppoppop-gp-bind" data-prop="required" value="1" checked> Required
        </label>
    </div>
</div>
