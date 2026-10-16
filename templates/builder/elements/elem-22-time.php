<?php
if (!defined('ABSPATH')) exit;
?>
<!-- DECOMPOSED MODULE 22: TIME PICKER (time) -->
<div class="element-panel wppoppop-panel-specific" id="panel-elem-time" data-elem-family="form" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;">22. Time Picker Settings</div>
    <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">TIME PLACEHOLDER</label>
    <input type="text" id="prop-time-placeholder" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="placeholder" placeholder="09:00 AM" style="margin-bottom:10px;font-size:12px;">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">CLOCK FORMAT</label>
            <select id="prop-time-format" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="timeFormat" style="font-size:12px;">
                <option value="12h">12-Hour (AM / PM)</option>
                <option value="24h">24-Hour (00:00–23:59)</option>
            </select>
        </div>
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">MINUTE INTERVAL</label>
            <select id="prop-time-step" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="minuteStep" style="font-size:12px;">
                <option value="5">Every 5 Min</option>
                <option value="10">Every 10 Min</option>
                <option value="15">Every 15 Min</option>
                <option value="30">Every 30 Min</option>
                <option value="60">Every 60 Min</option>
            </select>
        </div>
    </div>
</div>
