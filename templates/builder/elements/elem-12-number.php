<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!-- DECOMPOSED MODULE 12: NUMBER INPUT (number / spinner) -->
<div class="wppoppop-elem-panel element-panel wppoppop-panel-specific" id="panel-elem-number" data-elem-family="form" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;"><?php esc_html_e( '12. Number Input Settings', 'wppoppop' ); ?></div>
    <div style="margin-bottom:10px;">
        <label for="prop-number-placeholder" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'PLACEHOLDER', 'wppoppop' ); ?></label>
        <input type="text" id="prop-number-placeholder" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="placeholder" placeholder="0" style="font-size:12px;">
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;">
        <div>
            <label for="prop-number-min" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'MIN VALUE', 'wppoppop' ); ?></label>
            <input type="number" id="prop-number-min" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="min" value="0" style="font-size:12px;">
        </div>
        <div>
            <label for="prop-number-max" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'MAX VALUE', 'wppoppop' ); ?></label>
            <input type="number" id="prop-number-max" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="max" value="100" style="font-size:12px;">
        </div>
        <div>
            <label for="prop-number-step" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'STEP', 'wppoppop' ); ?></label>
            <input type="number" id="prop-number-step" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="step" value="1" style="font-size:12px;">
        </div>
    </div>
</div>
