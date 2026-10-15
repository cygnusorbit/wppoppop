<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!-- DECOMPOSED MODULE 05: CUSTOM HTML / SVG (html) -->
<div class="wppoppop-elem-panel element-panel wppoppop-panel-specific" id="panel-elem-html" data-elem-family="media" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;"><?php esc_html_e( '5. Custom HTML / SVG Code', 'wppoppop' ); ?></div>
    <div style="margin-bottom:10px;">
        <label for="prop-html-code" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'RAW HTML / SVG MARKUP', 'wppoppop' ); ?></label>
        <textarea class="widefat wppoppop-input wppoppop-gp-bind" id="prop-html-code" data-prop="content" rows="5" style="font-family:monospace;font-size:11px;" placeholder="&lt;div&gt;Custom HTML or SVG&lt;/div&gt;"></textarea>
    </div>
    <div class="wppoppop-gp-2col-grid">
        <div class="wppoppop-gp-col">
            <label for="prop-html-tag" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'WRAPPER TAG', 'wppoppop' ); ?></label>
            <select class="widefat wppoppop-input wppoppop-gp-bind" id="prop-html-tag" data-prop="tag" style="font-size:12px;">
                <option value="div">&lt;div&gt;</option>
                <option value="span">&lt;span&gt;</option>
            </select>
        </div>
        <div class="wppoppop-gp-col">
            <label for="prop-html-overflow" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'OVERFLOW MODE', 'wppoppop' ); ?></label>
            <select class="widefat wppoppop-input wppoppop-gp-bind" id="prop-html-overflow" data-prop="overflow" style="font-size:12px;">
                <option value="hidden"><?php esc_html_e( 'Hidden / Clip', 'wppoppop' ); ?></option>
                <option value="auto"><?php esc_html_e( 'Scrollable (Auto)', 'wppoppop' ); ?></option>
                <option value="visible"><?php esc_html_e( 'Visible', 'wppoppop' ); ?></option>
            </select>
        </div>
    </div>
</div>
