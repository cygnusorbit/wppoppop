<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!-- DECOMPOSED MODULE 14: TEXTAREA (textarea) -->
<div class="wppoppop-elem-panel element-panel wppoppop-panel-specific" id="panel-elem-textarea" data-elem-family="form" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;"><?php esc_html_e( '14. Textarea Settings', 'wppoppop' ); ?></div>
    <div style="margin-bottom:10px;">
        <label for="prop-textarea-placeholder" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'PLACEHOLDER', 'wppoppop' ); ?></label>
        <input type="text" id="prop-textarea-placeholder" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="placeholder" value="Write your message..." style="font-size:12px;">
    </div>
    <div class="wppoppop-gp-2col-grid">
        <div class="wppoppop-gp-col">
            <label for="prop-textarea-rows" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'VISIBLE ROWS', 'wppoppop' ); ?></label>
            <input type="number" id="prop-textarea-rows" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="rows" value="4" min="2" max="15" style="font-size:12px;">
        </div>
        <div class="wppoppop-gp-col">
            <label for="prop-textarea-maxlen" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'MAX CHARACTERS', 'wppoppop' ); ?></label>
            <input type="number" id="prop-textarea-maxlen" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="maxLength" value="500" min="10" max="5000" style="font-size:12px;">
        </div>
    </div>
</div>
