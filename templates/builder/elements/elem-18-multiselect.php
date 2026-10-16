<?php
if (!defined('ABSPATH')) exit;
?>
<!-- DECOMPOSED MODULE 18: MULTISELECT (multiselect) -->
<div class="element-panel wppoppop-panel-specific" id="panel-elem-multiselect" data-elem-family="form" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;">18. Multiselect List Settings</div>
    <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">SELECTABLE OPTIONS (ONE PER LINE)</label>
    <textarea id="prop-multiselect-options" rows="4" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="optionsList" placeholder="Web Design&#10;SEO Marketing&#10;Brand Strategy" style="margin-bottom:10px;font-size:12px;"></textarea>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">VISIBLE ROWS (3–10)</label>
            <input type="number" id="prop-multiselect-rows" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="rows" min="3" max="10" value="4" style="font-size:12px;">
        </div>
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">MAX SELECTIONS</label>
            <input type="number" id="prop-multiselect-max" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="maxSelections" min="1" max="20" value="3" style="font-size:12px;">
        </div>
    </div>
</div>
