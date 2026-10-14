<?php
if (!defined('ABSPATH')) exit;
?>
<!-- DECOMPOSED MODULE 18: MULTISELECT (multiselect) -->
<div class="element-panel wppoppop-panel-specific" id="panel-elem-multiselect" data-elem-family="form" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;">18. Multiselect Settings (Form Family — 5 Tabs)</div>

    <label for="prop-multiselect-fieldname" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">FIELD NAME / TOKEN</label>
    <input type="text" id="prop-multiselect-fieldname" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="fieldName" value="interests" placeholder="interests" style="margin-bottom:10px;font-size:12px;">

    <label for="prop-multiselect-options" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">MULTISELECT OPTIONS (COMMA OR NEWLINE SEPARATED)</label>
    <textarea id="prop-multiselect-options" rows="3" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="options" style="margin-bottom:10px;font-size:12px;">Marketing, Design, Engineering, Sales</textarea>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px;">
        <div>
            <label for="prop-multiselect-max" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">MAX SELECTIONS</label>
            <input type="number" id="prop-multiselect-max" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="maxSelect" value="3" min="1" max="20" style="font-size:12px;">
        </div>
        <div>
            <label for="prop-multiselect-rows" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">VISIBLE ROWS</label>
            <input type="number" id="prop-multiselect-rows" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="rows" value="4" min="2" max="12" style="font-size:12px;">
        </div>
    </div>

    <label style="display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:600;color:#cbd5e1;cursor:pointer;">
        <input type="checkbox" id="prop-multiselect-required" class="wppoppop-gp-bind" data-prop="required" value="1"> Required Field
    </label>
</div>
