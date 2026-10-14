<?php
if (!defined('ABSPATH')) exit;
?>
<!-- DECOMPOSED MODULE 17: RADIO BUTTON GROUP (radio / radios) -->
<div class="element-panel wppoppop-panel-specific" id="panel-elem-radio" data-elem-family="form" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;">17. Radio Button Settings (Form Family — 5 Tabs)</div>

    <label for="prop-radio-options" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">RADIO CHOICES (COMMA OR NEWLINE SEPARATED)</label>
    <textarea id="prop-radio-options" rows="3" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="options" placeholder="Choice A, Choice B, Choice C" style="margin-bottom:10px;font-size:12px;">Choice A, Choice B</textarea>
    <input type="hidden" id="prop-radios-options" class="wppoppop-gp-bind" data-prop="options">

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px;">
        <div>
            <label for="prop-radio-fieldname" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">FIELD TOKEN</label>
            <input type="text" id="prop-radio-fieldname" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="fieldName" value="radio_choice" style="font-size:12px;">
        </div>
        <div>
            <label for="prop-radio-layout" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">ORIENTATION</label>
            <select id="prop-radio-layout" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="layout" style="font-size:12px;">
                <option value="vertical">Stacked (Vertical)</option>
                <option value="inline">Inline (Horizontal)</option>
            </select>
        </div>
    </div>

    <label style="display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:600;color:#cbd5e1;cursor:pointer;">
        <input type="checkbox" id="prop-radio-required" class="wppoppop-gp-bind" data-prop="required" value="1"> Required Selection
    </label>
</div>
