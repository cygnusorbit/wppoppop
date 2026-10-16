<?php
if (!defined('ABSPATH')) exit;
?>
<!-- DECOMPOSED MODULE 19: IMAGE SELECT (imageselect) -->
<div class="element-panel wppoppop-panel-specific" id="panel-elem-imageselect" data-elem-family="form" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;">19. Visual Image Select Grid</div>
    <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">IMAGE CHOICES (LABEL, IMAGE URL PER LINE)</label>
    <textarea id="prop-imageselect-options" rows="3" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="optionsList" placeholder="Style A&#10;Style B&#10;Style C" style="margin-bottom:10px;font-size:12px;"></textarea>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:8px;">
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">GRID COLUMNS</label>
            <select id="prop-imageselect-cols" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="gridCols" style="font-size:12px;">
                <option value="2">2 Columns</option>
                <option value="3">3 Columns</option>
                <option value="4">4 Columns</option>
            </select>
        </div>
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">ACTIVE BORDER</label>
            <input type="color" id="prop-imageselect-accent" class="widefat wppoppop-gp-bind" data-prop="activeColor" value="#38bdf8" style="height:34px;padding:2px;background:#090d16;border:1px solid #1e293b;border-radius:6px;cursor:pointer;">
        </div>
    </div>
    <label style="display:inline-flex;align-items:center;gap:6px;font-size:11px;color:#cbd5e1;cursor:pointer;">
        <input type="checkbox" id="prop-imageselect-multi" class="wppoppop-gp-bind" data-prop="multiSelect" value="1"> Allow Multiple Image Selections
    </label>
</div>
