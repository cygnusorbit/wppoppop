<?php
if (!defined('ABSPATH')) exit;
?>
<!-- DECOMPOSED MODULE 22: TIME PICKER (time) -->
<div class="element-panel wppoppop-panel-specific" id="panel-elem-time" data-elem-family="form" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;"><?php esc_html_e('22. Time Picker Settings', 'wppoppop'); ?></div>
    <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e('TIME PLACEHOLDER', 'wppoppop'); ?></label>
    <input type="text" id="prop-time-placeholder" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="placeholder" placeholder="<?php esc_attr_e('09:00 AM', 'wppoppop'); ?>" style="margin-bottom:10px;font-size:12px;">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e('CLOCK FORMAT', 'wppoppop'); ?></label>
            <select id="prop-time-format" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="timeFormat" style="font-size:12px;">
                <option value="12h"><?php esc_html_e('12-Hour (AM / PM)', 'wppoppop'); ?></option>
                <option value="24h"><?php esc_html_e('24-Hour (00:00–23:59)', 'wppoppop'); ?></option>
            </select>
        </div>
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e('MINUTE INTERVAL', 'wppoppop'); ?></label>
            <select id="prop-time-step" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="timeStep" style="font-size:12px;">
                <option value="5"><?php esc_html_e('Every 5 Min', 'wppoppop'); ?></option>
                <option value="10"><?php esc_html_e('Every 10 Min', 'wppoppop'); ?></option>
                <option value="15"><?php esc_html_e('Every 15 Min', 'wppoppop'); ?></option>
                <option value="30"><?php esc_html_e('Every 30 Min', 'wppoppop'); ?></option>
                <option value="60"><?php esc_html_e('Every 60 Min', 'wppoppop'); ?></option>
            </select>
        </div>
    </div>
</div>
