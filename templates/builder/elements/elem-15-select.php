<?php
if (!defined('ABSPATH')) exit;
?>
<!-- DECOMPOSED MODULE 15: SELECT DROPDOWN (select / dropdown) -->
<div class="element-panel wppoppop-panel-specific" id="panel-elem-select" data-elem-family="form" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;">15. Select Dropdown Settings (Form Family — 5 Tabs)</div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px;">
        <div>
            <label for="prop-select-placeholder" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">FIRST PROMPT</label>
            <input type="text" id="prop-select-placeholder" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="placeholder" value="Select an option..." style="font-size:12px;">
        </div>
        <div>
            <label for="prop-select-fieldname" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">FIELD TOKEN</label>
            <input type="text" id="prop-select-fieldname" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="fieldName" value="dropdown_choice" style="font-size:12px;">
        </div>
    </div>

    <label for="prop-select-options" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">DROPDOWN OPTIONS (COMMA OR NEWLINE SEPARATED)</label>
    <textarea id="prop-select-options" rows="3" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="options" placeholder="Option 1, Option 2, Option 3" style="margin-bottom:10px;font-size:12px;">Option 1, Option 2, Option 3</textarea>

    <label style="display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:600;color:#cbd5e1;cursor:pointer;">
        <input type="checkbox" id="prop-select-required" class="wppoppop-gp-bind" data-prop="required" value="1"> Required Selection
    </label>
</div>
