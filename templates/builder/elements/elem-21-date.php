<?php
if (!defined('ABSPATH')) exit;
?>
<!-- DECOMPOSED MODULE 21: DATE PICKER (date) -->
<div class="element-panel wppoppop-panel-specific" id="panel-elem-date" data-elem-family="form" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;">21. Date Picker Settings (Form Family — 5 Tabs)</div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px;">
        <div>
            <label for="prop-date-fieldname" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">FIELD TOKEN</label>
            <input type="text" id="prop-date-fieldname" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="fieldName" value="booking_date" style="font-size:12px;">
        </div>
        <div>
            <label for="prop-date-format" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">DATE FORMAT</label>
            <select id="prop-date-format" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="dateFormat" style="font-size:12px;">
                <option value="YYYY-MM-DD">YYYY-MM-DD (ISO)</option>
                <option value="MM/DD/YYYY">MM/DD/YYYY (US)</option>
                <option value="DD/MM/YYYY">DD/MM/YYYY (EU/UK)</option>
            </select>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px;">
        <div>
            <label for="prop-date-min" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">MIN DATE</label>
            <input type="date" id="prop-date-min" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="minDate" style="font-size:12px;">
        </div>
        <div>
            <label for="prop-date-max" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">MAX DATE</label>
            <input type="date" id="prop-date-max" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="maxDate" style="font-size:12px;">
        </div>
    </div>

    <label style="display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:600;color:#cbd5e1;cursor:pointer;">
        <input type="checkbox" id="prop-date-required" class="wppoppop-gp-bind" data-prop="required" value="1"> Required Field
    </label>
</div>
