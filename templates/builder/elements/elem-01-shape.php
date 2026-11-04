<?php
if (!defined('ABSPATH')) exit;
?>
<!-- DECOMPOSED MODULE 01: RECTANGLE / SQUARE / VECTOR SHAPE (shape) -->
<div class="element-panel wppoppop-panel-specific wppoppop-shape-panel" id="panel-elem-shape" data-elem-family="media" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div class="wppoppop-shape-head">
        <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;letter-spacing:0.5px;"><?php esc_html_e('1. Shape / Vector Geometry', 'wppoppop'); ?></div>
        <button type="button" id="prop-shape-reset" class="button button-small wppoppop-shape-reset" title="<?php esc_attr_e('Reset stroke width and rotation to 0', 'wppoppop'); ?>">&#8634; <?php esc_html_e('Reset', 'wppoppop'); ?></button>
    </div>
    <div class="wppoppop-shape-field">
        <label class="wppoppop-shape-label" for="prop-shape-type"><?php esc_html_e('SVG SHAPE PRESET', 'wppoppop'); ?></label>
        <select id="prop-shape-type" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="shapePreset" style="font-size:12px;">
            <option value="square" selected><?php esc_html_e('Square', 'wppoppop'); ?></option>
            <option value="rectangle"><?php esc_html_e('Rectangle', 'wppoppop'); ?></option>
            <option value="rounded"><?php esc_html_e('Rounded Box', 'wppoppop'); ?></option>
            <option value="circle"><?php esc_html_e('Circle / Oval', 'wppoppop'); ?></option>
            <option value="pill"><?php esc_html_e('Pill / Stadium', 'wppoppop'); ?></option>
            <option value="triangle"><?php esc_html_e('Triangle', 'wppoppop'); ?></option>
            <option value="diamond"><?php esc_html_e('Diamond / Rhombus', 'wppoppop'); ?></option>
            <option value="pentagon"><?php esc_html_e('Pentagon', 'wppoppop'); ?></option>
            <option value="hexagon"><?php esc_html_e('Hexagon', 'wppoppop'); ?></option>
            <option value="star"><?php esc_html_e('5-Point Star', 'wppoppop'); ?></option>
            <option value="heart"><?php esc_html_e('Heart', 'wppoppop'); ?></option>
            <option value="badge"><?php esc_html_e('Seal / Burst Badge', 'wppoppop'); ?></option>
        </select>
    </div>
    <div class="wppoppop-shape-row">
        <div>
            <label class="wppoppop-shape-label" for="prop-size-width"><?php esc_html_e('WIDTH (PX)', 'wppoppop'); ?></label>
            <input type="number" id="prop-size-width" class="widefat wppoppop-input" min="16" step="1" value="120" style="font-size:12px;">
        </div>
        <div>
            <label class="wppoppop-shape-label" for="prop-size-height"><?php esc_html_e('HEIGHT (PX)', 'wppoppop'); ?></label>
            <input type="number" id="prop-size-height" class="widefat wppoppop-input" min="16" step="1" value="120" style="font-size:12px;">
        </div>
    </div>
    <div class="wppoppop-shape-field">
        <label class="wppoppop-shape-label" for="prop-shape-fill"><?php esc_html_e('FILL COLOR', 'wppoppop'); ?></label>
        <div class="wppoppop-shape-color" data-wpp-clear-for="prop-shape-transparent">
            <input type="color" id="prop-shape-fill" class="wppoppop-gp-bind" data-prop="shapeFill" value="#3b82f6">
            <label class="wppoppop-shape-clear" title="<?php esc_attr_e('Transparent (no fill)', 'wppoppop'); ?>">
                <input type="checkbox" id="prop-shape-transparent" class="wppoppop-gp-bind" data-prop="shapeNoFill" value="1" aria-label="<?php esc_attr_e('Transparent fill', 'wppoppop'); ?>">
                <span class="wppoppop-shape-clear-sw" aria-hidden="true"></span>
                <span class="wppoppop-shape-clear-tx"><?php esc_html_e('Transparent', 'wppoppop'); ?></span>
            </label>
        </div>
    </div>
    <div class="wppoppop-shape-row">
        <div>
            <label class="wppoppop-shape-label" for="prop-shape-stroke-color"><?php esc_html_e('STROKE COLOR', 'wppoppop'); ?></label>
            <div class="wppoppop-shape-color" data-wpp-clear-for="prop-shape-stroke-transparent">
                <input type="color" id="prop-shape-stroke-color" data-wpp-prop="strokeColor" value="#0284c7">
                <label class="wppoppop-shape-clear wppoppop-shape-clear-icon" title="<?php esc_attr_e('Transparent stroke', 'wppoppop'); ?>">
                    <input type="checkbox" id="prop-shape-stroke-transparent" aria-label="<?php esc_attr_e('Transparent stroke', 'wppoppop'); ?>">
                    <span class="wppoppop-shape-clear-sw" aria-hidden="true"></span>
                </label>
            </div>
        </div>
        <div>
            <label class="wppoppop-shape-label" for="prop-shape-stroke-width"><?php esc_html_e('STROKE WIDTH (PX)', 'wppoppop'); ?></label>
            <input type="number" id="prop-shape-stroke-width" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="strokeWidth" min="0" max="40" value="0" style="font-size:12px;" placeholder="0">
        </div>
    </div>
    <div class="wppoppop-shape-field" style="margin-bottom:0;">
        <div class="wppoppop-shape-rot-head">
            <label class="wppoppop-shape-label" for="prop-shape-rotate" style="margin:0;"><?php esc_html_e('ROTATION (0°–360°)', 'wppoppop'); ?></label>
            <span id="prop-shape-rotate-val" class="wppoppop-shape-rot-val">0&deg;</span>
        </div>
        <input type="range" id="prop-shape-rotate" class="wppoppop-gp-bind wppoppop-shape-range" data-prop="rotate" min="0" max="360" step="1" value="0">
    </div>
</div>
