<?php
if (!defined('ABSPATH')) exit;
?>
<!-- DECOMPOSED MODULE 19: IMAGE SELECT (imageselect) -->
<div class="element-panel wppoppop-panel-specific" id="panel-elem-imageselect" data-elem-family="form" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;">19. Image Select Settings (Form Family — 5 Tabs)</div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px;">
        <div>
            <label for="prop-imageselect-fieldname" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">FIELD TOKEN</label>
            <input type="text" id="prop-imageselect-fieldname" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="fieldName" value="image_choice" style="font-size:12px;">
        </div>
        <div>
            <label for="prop-imageselect-cols" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">GRID COLUMNS</label>
            <select id="prop-imageselect-cols" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="columns" style="font-size:12px;">
                <option value="1">1 Column</option>
                <option value="2">2 Columns</option>
                <option value="3" selected>3 Columns</option>
                <option value="4">4 Columns</option>
            </select>
        </div>
    </div>

    <label for="prop-imageselect-items" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">IMAGE CHOICES (LABEL OR LABEL|IMAGE_URL)</label>
    <textarea id="prop-imageselect-items" rows="3" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="options" style="margin-bottom:10px;font-size:12px;">Starter Plan, Pro Plan, Enterprise</textarea>

    <div style="display:flex;gap:14px;">
        <label style="display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:600;color:#cbd5e1;cursor:pointer;">
            <input type="checkbox" id="prop-imageselect-multi" class="wppoppop-gp-bind" data-prop="multiSelect" value="1"> Multi-Select
        </label>
        <label style="display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:600;color:#cbd5e1;cursor:pointer;">
            <input type="checkbox" id="prop-imageselect-required" class="wppoppop-gp-bind" data-prop="required" value="1"> Required
        </label>
    </div>
</div>
