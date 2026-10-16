<?php
if (!defined('ABSPATH')) exit;
?>
<!-- DECOMPOSED MODULE 20: INTERACTIVE TILE SELECTOR (tile) -->
<div class="element-panel wppoppop-panel-specific" id="panel-elem-tile" data-elem-family="form" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;">20. Interactive Tile Selector</div>
    <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">TILE CHOICES (ONE PER LINE)</label>
    <textarea id="prop-tile-options" rows="3" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="optionsList" placeholder="$10 / mo&#10;$25 / mo&#10;$50 / mo" style="margin-bottom:10px;font-size:12px;"></textarea>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:8px;">
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">GRID COLUMNS</label>
            <select id="prop-tile-cols" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="gridCols" style="font-size:12px;">
                <option value="2">2 Columns</option>
                <option value="3">3 Columns</option>
                <option value="4">4 Columns</option>
            </select>
        </div>
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">ACTIVE ACCENT</label>
            <input type="color" id="prop-tile-accent" class="widefat wppoppop-gp-bind" data-prop="activeColor" value="#38bdf8" style="height:34px;padding:2px;background:#090d16;border:1px solid #1e293b;border-radius:6px;cursor:pointer;">
        </div>
    </div>
    <label style="display:inline-flex;align-items:center;gap:6px;font-size:11px;color:#cbd5e1;cursor:pointer;">
        <input type="checkbox" id="prop-tile-multi" class="wppoppop-gp-bind" data-prop="multiSelect" value="1"> Allow Multi-Tile Selection
    </label>
</div>
