<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!-- DECOMPOSED MODULE 21: DATE PICKER (date) -->
<div class="wppoppop-elem-panel element-panel wppoppop-panel-specific" id="panel-elem-date" data-elem-family="form" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;"><?php esc_html_e( '21. Date Picker Settings', 'wppoppop' ); ?></div>
    <div class="wppoppop-gp-2col-grid" style="margin-bottom:10px;">
        <div class="wppoppop-gp-col">
            <label for="prop-date-placeholder" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'PLACEHOLDER', 'wppoppop' ); ?></label>
            <input type="text" id="prop-date-placeholder" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="placeholder" value="Select date..." style="font-size:12px;">
        </div>
        <div class="wppoppop-gp-col">
            <label for="prop-date-format" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'DATE FORMAT', 'wppoppop' ); ?></label>
            <select id="prop-date-format" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="dateFormat" style="font-size:12px;">
                <option value="YYYY-MM-DD">YYYY-MM-DD</option>
                <option value="MM/DD/YYYY">MM/DD/YYYY</option>
                <option value="DD/MM/YYYY">DD/MM/YYYY</option>
            </select>
        </div>
    </div>
    <div class="wppoppop-gp-2col-grid">
        <div class="wppoppop-gp-col">
            <label for="prop-date-min" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'MIN DATE', 'wppoppop' ); ?></label>
            <input type="date" id="prop-date-min" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="minDate" style="font-size:12px;">
        </div>
        <div class="wppoppop-gp-col">
            <label for="prop-date-max" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'MAX DATE', 'wppoppop' ); ?></label>
            <input type="date" id="prop-date-max" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="maxDate" style="font-size:12px;">
        </div>
    </div>
</div>
