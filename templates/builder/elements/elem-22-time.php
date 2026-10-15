<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!-- DECOMPOSED MODULE 22: TIME PICKER (time) -->
<div class="wppoppop-elem-panel element-panel wppoppop-panel-specific" id="panel-elem-time" data-elem-family="form" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;"><?php esc_html_e( '22. Time Picker Settings', 'wppoppop' ); ?></div>
    <div style="margin-bottom:10px;">
        <label for="prop-time-placeholder" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'PLACEHOLDER', 'wppoppop' ); ?></label>
        <input type="text" id="prop-time-placeholder" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="placeholder" value="Select time..." style="font-size:12px;">
    </div>
    <div class="wppoppop-gp-2col-grid">
        <div class="wppoppop-gp-col">
            <label for="prop-time-format" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'CLOCK FORMAT', 'wppoppop' ); ?></label>
            <select id="prop-time-format" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="timeFormat" style="font-size:12px;">
                <option value="12h">12-Hour (AM / PM)</option>
                <option value="24h">24-Hour (00:00–23:59)</option>
            </select>
        </div>
        <div class="wppoppop-gp-col">
            <label for="prop-time-step" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'MINUTE INTERVAL', 'wppoppop' ); ?></label>
            <select id="prop-time-step" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="timeStep" style="font-size:12px;">
                <option value="5">5 Minutes</option>
                <option value="15" selected>15 Minutes</option>
                <option value="30">30 Minutes</option>
                <option value="60">60 Minutes</option>
            </select>
        </div>
    </div>
</div>
