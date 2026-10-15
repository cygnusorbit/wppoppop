<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!-- DECOMPOSED MODULE 01: RECTANGLE / VECTOR SHAPE (shape) -->
<div class="wppoppop-elem-panel element-panel wppoppop-panel-specific" id="panel-elem-shape" data-elem-family="media" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;"><?php esc_html_e( '1. Vector Shape & Geometry', 'wppoppop' ); ?></div>
    <div style="margin-bottom:10px;">
        <label for="prop-shape-preset" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'SVG SHAPE PRESET (11 VECTOR SHAPES)', 'wppoppop' ); ?></label>
        <select class="widefat wppoppop-input wppoppop-gp-bind" id="prop-shape-preset" data-prop="shapePreset" style="font-size:12px;">
            <option value="rectangle"><?php esc_html_e( 'Rectangle / Square', 'wppoppop' ); ?></option>
            <option value="rounded"><?php esc_html_e( 'Rounded Card', 'wppoppop' ); ?></option>
            <option value="circle"><?php esc_html_e( 'Circle / Oval', 'wppoppop' ); ?></option>
            <option value="pill"><?php esc_html_e( 'Pill / Stadium', 'wppoppop' ); ?></option>
            <option value="triangle"><?php esc_html_e( 'Triangle', 'wppoppop' ); ?></option>
            <option value="diamond"><?php esc_html_e( 'Diamond / Rhombus', 'wppoppop' ); ?></option>
            <option value="star"><?php esc_html_e( '5-Point Star', 'wppoppop' ); ?></option>
            <option value="heart"><?php esc_html_e( 'Heart', 'wppoppop' ); ?></option>
            <option value="pentagon"><?php esc_html_e( 'Pentagon', 'wppoppop' ); ?></option>
            <option value="hexagon"><?php esc_html_e( 'Hexagon', 'wppoppop' ); ?></option>
            <option value="badge"><?php esc_html_e( 'Seal / Badge', 'wppoppop' ); ?></option>
        </select>
    </div>
    <div class="wppoppop-gp-2col-grid" style="margin-bottom:10px;">
        <div class="wppoppop-gp-col">
            <label for="prop-shape-stroke-width" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'STROKE WIDTH (PX)', 'wppoppop' ); ?></label>
            <input type="number" class="widefat wppoppop-input wppoppop-gp-bind" id="prop-shape-stroke-width" data-prop="strokeWidth" value="0" min="0" max="30" style="font-size:12px;">
        </div>
        <div class="wppoppop-gp-col">
            <label for="prop-shape-stroke-color" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'STROKE COLOR', 'wppoppop' ); ?></label>
            <input type="color" class="widefat wppoppop-input wppoppop-gp-bind" id="prop-shape-stroke-color" data-prop="strokeColor" value="#38bdf8" style="height:30px;padding:2px;cursor:pointer;">
        </div>
    </div>
    <div class="wppoppop-gp-2col-grid" style="align-items:end;">
        <div class="wppoppop-gp-col">
            <label for="prop-shape-rotate" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'ROTATION (0°–360°)', 'wppoppop' ); ?></label>
            <input type="number" class="widefat wppoppop-input wppoppop-gp-bind" id="prop-shape-rotate" data-prop="rotate" value="0" min="0" max="360" style="font-size:12px;">
        </div>
        <div class="wppoppop-gp-col" style="padding-bottom:6px;">
            <label style="font-size:11px;font-weight:600;color:#cbd5e1;display:flex;align-items:center;gap:6px;cursor:pointer;">
                <input type="checkbox" class="wppoppop-gp-bind" id="prop-shape-transparent" data-prop="transparentFill" value="1">
                <?php esc_html_e( 'Transparent Fill', 'wppoppop' ); ?>
            </label>
        </div>
    </div>
</div>
