<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!-- DECOMPOSED MODULE 06: CLOSE ICON (close / close_icon) -->
<div class="wppoppop-elem-panel element-panel wppoppop-panel-specific" id="panel-elem-close" data-elem-family="media" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;"><?php esc_html_e( '6. Close Icon & Dismissal Behavior', 'wppoppop' ); ?></div>
    <div class="wppoppop-gp-2col-grid" style="margin-bottom:10px;">
        <div class="wppoppop-gp-col">
            <label for="prop-close-symbol" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'ICON GLYPH PRESET', 'wppoppop' ); ?></label>
            <select class="widefat wppoppop-input wppoppop-gp-bind" id="prop-close-symbol" data-prop="closeSymbol" style="font-size:12px;">
                <option value="times">&times; Classic Cross</option>
                <option value="dashicons-no-alt">Thin Cross</option>
                <option value="dashicons-dismiss">Circle Dismiss</option>
                <option value="dashicons-arrow-left-alt2">Back Arrow</option>
                <option value="text_close">CLOSE (Text)</option>
            </select>
        </div>
        <div class="wppoppop-gp-col">
            <label for="prop-close-size" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'GLYPH SIZE (PX)', 'wppoppop' ); ?></label>
            <input type="number" class="widefat wppoppop-input wppoppop-gp-bind" id="prop-close-size" data-prop="iconSize" value="20" min="10" max="80" style="font-size:12px;">
        </div>
    </div>
    <div style="margin-bottom:8px;">
        <label for="prop-close-action" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'DISMISS BEHAVIOR', 'wppoppop' ); ?></label>
        <select class="widefat wppoppop-input wppoppop-gp-bind" id="prop-close-action" data-prop="closeAction" style="font-size:12px;">
            <option value="close"><?php esc_html_e( 'Close Popup Immediately', 'wppoppop' ); ?></option>
            <option value="close_period"><?php esc_html_e( 'Dismiss for Current Browser Session', 'wppoppop' ); ?></option>
            <option value="close_forever"><?php esc_html_e( 'Dismiss with Cookie Suppression', 'wppoppop' ); ?></option>
        </select>
    </div>
    <div id="prop-close-cookie-wrap" style="display:none;margin-top:8px;">
        <label for="prop-close-cookie-days" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'COOKIE SUPPRESSION DAYS', 'wppoppop' ); ?></label>
        <input type="number" class="widefat wppoppop-input wppoppop-gp-bind" id="prop-close-cookie-days" data-prop="cookieDays" value="30" min="1" max="365" style="font-size:12px;">
    </div>
</div>
