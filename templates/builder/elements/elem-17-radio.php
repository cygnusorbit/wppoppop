<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!-- DECOMPOSED MODULE 17: RADIO GROUP (radio / radios) -->
<div class="wppoppop-elem-panel element-panel wppoppop-panel-specific" id="panel-elem-radio" data-elem-family="form" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;"><?php esc_html_e( '17. Radio Buttons Settings', 'wppoppop' ); ?></div>
    <div style="margin-bottom:10px;">
        <label for="prop-radio-options" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'RADIO CHOICES (ONE PER LINE OR COMMA SEPARATED)', 'wppoppop' ); ?></label>
        <textarea id="prop-radio-options" rows="3" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="options" style="font-size:12px;">Choice A, Choice B, Choice C</textarea>
    </div>
    <div>
        <label for="prop-radio-layout" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'OPTION LAYOUT ORIENTATION', 'wppoppop' ); ?></label>
        <select id="prop-radio-layout" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="radioLayout" style="font-size:12px;">
            <option value="horizontal"><?php esc_html_e( 'Horizontal (Inline Row)', 'wppoppop' ); ?></option>
            <option value="vertical"><?php esc_html_e( 'Vertical (Stacked Column)', 'wppoppop' ); ?></option>
        </select>
    </div>
</div>
