<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!-- DECOMPOSED MODULE 07: VECTOR ICON (icon) -->
<div class="wppoppop-elem-panel element-panel wppoppop-panel-specific" id="panel-elem-icon" data-elem-family="media" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;"><?php esc_html_e( '7. Vector Icon & Badge Settings', 'wppoppop' ); ?></div>
    <div style="margin-bottom:8px;">
        <label for="prop-icon-class" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'ICON CLASS (FONT AWESOME OR DASHICONS)', 'wppoppop' ); ?></label>
        <input type="text" class="widefat wppoppop-input wppoppop-gp-bind" id="prop-icon-class" data-prop="iconClass" value="dashicons-star-filled" placeholder="fas fa-star or dashicons-star-filled" style="font-size:12px;">
    </div>
    <div style="display:flex;gap:5px;flex-wrap:wrap;margin-bottom:10px;">
        <button type="button" class="button button-small wppoppop-icon-preset" data-icon="dashicons-star-filled">&#9733; Star</button>
        <button type="button" class="button button-small wppoppop-icon-preset" data-icon="dashicons-yes-alt">&#10004; Check</button>
        <button type="button" class="button button-small wppoppop-icon-preset" data-icon="dashicons-heart">&#9829; Heart</button>
        <button type="button" class="button button-small wppoppop-icon-preset" data-icon="dashicons-bell">&#128276; Bell</button>
        <button type="button" class="button button-small wppoppop-icon-preset" data-icon="dashicons-awards">&#127942; Award</button>
        <button type="button" class="button button-small wppoppop-icon-preset" data-icon="dashicons-megaphone">&#128227; Alert</button>
    </div>
    <div class="wppoppop-gp-2col-grid" style="margin-bottom:10px;">
        <div class="wppoppop-gp-col">
            <label for="prop-icon-size" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'ICON SIZE (PX)', 'wppoppop' ); ?></label>
            <input type="number" class="widefat wppoppop-input wppoppop-gp-bind" id="prop-icon-size" data-prop="iconSize" value="36" min="12" max="160" style="font-size:12px;">
        </div>
        <div class="wppoppop-gp-col">
            <label for="prop-icon-rotate" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'ROTATION (0°–360°)', 'wppoppop' ); ?></label>
            <input type="number" class="widefat wppoppop-input wppoppop-gp-bind" id="prop-icon-rotate" data-prop="rotate" value="0" min="0" max="360" style="font-size:12px;">
        </div>
    </div>
    <div>
        <label for="prop-icon-link-url" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'OPTIONAL CLICK URL', 'wppoppop' ); ?></label>
        <input type="text" class="widefat wppoppop-input wppoppop-gp-bind" id="prop-icon-link-url" data-prop="linkUrl" placeholder="https://..." style="font-size:12px;">
    </div>
</div>
