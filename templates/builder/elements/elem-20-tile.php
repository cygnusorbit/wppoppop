<?php
if (!defined('ABSPATH')) exit;
?>
<!-- DECOMPOSED MODULE 20: TILE SELECTOR (tile) -->
<div class="element-panel wppoppop-panel-specific" id="panel-elem-tile" data-elem-family="form" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;">20. Tile Selector Settings (Form Family — 5 Tabs)</div>

    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:6px;margin-bottom:10px;">
        <div>
            <label for="prop-tile-fieldname" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">TOKEN</label>
            <input type="text" id="prop-tile-fieldname" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="fieldName" value="plan_tile" style="font-size:12px;">
        </div>
        <div>
            <label for="prop-tile-cols" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">COLUMNS</label>
            <select id="prop-tile-cols" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="columns" style="font-size:12px;">
                <option value="1">1 Col</option>
                <option value="2">2 Cols</option>
                <option value="3" selected>3 Cols</option>
                <option value="4">4 Cols</option>
            </select>
        </div>
        <div>
            <label for="prop-tile-color" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">ACTIVE BG</label>
            <input type="color" id="prop-tile-color" class="widefat wppoppop-gp-bind" data-prop="activeColor" value="#0284c7" style="height:34px;padding:2px;background:#090d16;border:1px solid #1e293b;border-radius:6px;cursor:pointer;">
        </div>
    </div>

    <label for="prop-tile-options" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">TILE ITEMS (COMMA OR NEWLINE SEPARATED)</label>
    <textarea id="prop-tile-options" rows="3" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="options" style="margin-bottom:10px;font-size:12px;">$10 / mo, $25 / mo, $50 / mo</textarea>

    <div style="display:flex;gap:14px;">
        <label style="display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:600;color:#cbd5e1;cursor:pointer;">
            <input type="checkbox" id="prop-tile-multi" class="wppoppop-gp-bind" data-prop="multiSelect" value="1"> Multi-Select
        </label>
        <label style="display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:600;color:#cbd5e1;cursor:pointer;">
            <input type="checkbox" id="prop-tile-required" class="wppoppop-gp-bind" data-prop="required" value="1"> Required
        </label>
    </div>
</div>
