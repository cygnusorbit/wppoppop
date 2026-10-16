<?php
if (!defined('ABSPATH')) exit;
?>
<!-- DECOMPOSED MODULE 21: DATE PICKER (date) -->
<div class="element-panel wppoppop-panel-specific" id="panel-elem-date" data-elem-family="form" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;">21. Date Picker Format &amp; Bounds</div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px;">
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">PLACEHOLDER</label>
            <input type="text" id="prop-date-placeholder" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="placeholder" placeholder="Select date..." style="font-size:12px;">
        </div>
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">DATE FORMAT</label>
            <select id="prop-date-format" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="dateFormat" style="font-size:12px;">
                <option value="YYYY-MM-DD">YYYY-MM-DD</option>
                <option value="MM/DD/YYYY">MM/DD/YYYY</option>
                <option value="DD/MM/YYYY">DD/MM/YYYY</option>
            </select>
        </div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">MIN DATE</label>
            <input type="date" id="prop-date-min" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="minDate" style="font-size:12px;">
        </div>
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">MAX DATE</label>
            <input type="date" id="prop-date-max" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="maxDate" style="font-size:12px;">
        </div>
    </div>
</div>
