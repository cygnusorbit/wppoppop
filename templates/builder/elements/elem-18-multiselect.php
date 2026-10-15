<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!-- DECOMPOSED MODULE 18: MULTISELECT (multiselect) -->
<div class="wppoppop-elem-panel element-panel wppoppop-panel-specific" id="panel-elem-multiselect" data-elem-family="form" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;"><?php esc_html_e( '18. Multiselect List Settings', 'wppoppop' ); ?></div>
    <div style="margin-bottom:10px;">
        <label for="prop-multiselect-options" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'MULTISELECT OPTIONS (ONE PER LINE OR COMMA SEPARATED)', 'wppoppop' ); ?></label>
        <textarea id="prop-multiselect-options" rows="3" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="options" style="font-size:12px;">Marketing, Design, Development, Sales</textarea>
    </div>
    <div>
        <label for="prop-multiselect-max" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'MAX SELECTIONS ALLOWED (0 = UNLIMITED)', 'wppoppop' ); ?></label>
        <input type="number" id="prop-multiselect-max" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="maxSelect" value="0" min="0" max="20" style="font-size:12px;">
    </div>
</div>
