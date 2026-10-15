<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!-- DECOMPOSED MODULE 10: TEXT INPUT (text / textfield) -->
<div class="wppoppop-elem-panel element-panel wppoppop-panel-specific" id="panel-elem-text" data-elem-family="form" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;"><?php esc_html_e( '10. Text Input Settings', 'wppoppop' ); ?></div>
    <div style="display:grid;grid-template-columns:2fr 1fr;gap:8px;">
        <div>
            <label for="prop-text-placeholder" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'PLACEHOLDER', 'wppoppop' ); ?></label>
            <input type="text" id="prop-text-placeholder" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="placeholder" placeholder="<?php esc_attr_e( 'Enter your name...', 'wppoppop' ); ?>" style="font-size:12px;">
        </div>
        <div>
            <label for="prop-text-maxlen" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'MAX LENGTH', 'wppoppop' ); ?></label>
            <input type="number" id="prop-text-maxlen" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="maxLength" min="0" max="1000" placeholder="120" style="font-size:12px;">
        </div>
    </div>
</div>
