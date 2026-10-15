<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!-- DECOMPOSED MODULE 20: TILE SELECTOR (tile) -->
<div class="wppoppop-elem-panel element-panel wppoppop-panel-specific" id="panel-elem-tile" data-elem-family="form" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;"><?php esc_html_e( '20. Tile Selector Settings', 'wppoppop' ); ?></div>
    <div style="margin-bottom:10px;">
        <label for="prop-tile-options" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'TILE LABELS (COMMA OR NEWLINE SEPARATED)', 'wppoppop' ); ?></label>
        <textarea id="prop-tile-options" rows="2" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="options" style="font-size:12px;">$10, $25, $50, $100</textarea>
    </div>
    <div class="wppoppop-gp-2col-grid" style="margin-bottom:8px;">
        <div class="wppoppop-gp-col">
            <label for="prop-tile-cols" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'GRID COLUMNS', 'wppoppop' ); ?></label>
            <select id="prop-tile-cols" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="columns" style="font-size:12px;">
                <option value="2">2 Columns</option>
                <option value="3">3 Columns</option>
                <option value="4" selected>4 Columns</option>
            </select>
        </div>
        <div class="wppoppop-gp-col">
            <label for="prop-tile-color" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'ACTIVE ACCENT', 'wppoppop' ); ?></label>
            <input type="color" id="prop-tile-color" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="activeColor" value="#c2185b" style="height:30px;padding:2px;cursor:pointer;">
        </div>
    </div>
    <label style="display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:600;color:#cbd5e1;cursor:pointer;">
        <input type="checkbox" id="prop-tile-multi" class="wppoppop-gp-bind" data-prop="multiSelect" value="1"> <?php esc_html_e( 'Allow Multiple Tile Selection', 'wppoppop' ); ?>
    </label>
</div>
