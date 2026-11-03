<?php
if (!defined('ABSPATH')) exit;
?>
<!-- DECOMPOSED MODULE 01: RECTANGLE / SQUARE / VECTOR SHAPE (shape) -->
<div class="element-panel wppoppop-panel-specific wppoppop-shape-panel" id="panel-elem-shape" data-elem-family="media" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div class="wppoppop-shape-head">
        <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;letter-spacing:0.5px;">1. Shape / Vector Geometry</div>
        <button type="button" id="prop-shape-reset" class="button button-small wppoppop-shape-reset" title="Reset stroke width and rotation to 0">&#8634; Reset</button>
    </div>
    <div class="wppoppop-shape-field">
        <label class="wppoppop-shape-label" for="prop-shape-type">SVG SHAPE PRESET</label>
        <select id="prop-shape-type" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="shapePreset" style="font-size:12px;">
            <option value="square" selected>Square</option>
            <option value="rectangle">Rectangle</option>
            <option value="rounded">Rounded Box</option>
            <option value="circle">Circle / Oval</option>
            <option value="pill">Pill / Stadium</option>
            <option value="triangle">Triangle</option>
            <option value="diamond">Diamond / Rhombus</option>
            <option value="pentagon">Pentagon</option>
            <option value="hexagon">Hexagon</option>
            <option value="star">5-Point Star</option>
            <option value="heart">Heart</option>
            <option value="badge">Seal / Burst Badge</option>
        </select>
    </div>
    <div class="wppoppop-shape-row">
        <div>
            <label class="wppoppop-shape-label" for="prop-size-width">WIDTH (PX)</label>
            <input type="number" id="prop-size-width" class="widefat wppoppop-input" min="16" step="1" value="120" style="font-size:12px;">
        </div>
        <div>
            <label class="wppoppop-shape-label" for="prop-size-height">HEIGHT (PX)</label>
            <input type="number" id="prop-size-height" class="widefat wppoppop-input" min="16" step="1" value="120" style="font-size:12px;">
        </div>
    </div>
    <div class="wppoppop-shape-field">
        <label class="wppoppop-shape-label" for="prop-shape-fill">FILL COLOR</label>
        <div class="wppoppop-shape-color" data-wpp-clear-for="prop-shape-transparent">
            <input type="color" id="prop-shape-fill" class="wppoppop-gp-bind" data-prop="shapeFill" value="#3b82f6">
            <label class="wppoppop-shape-clear" title="Transparent (no fill)">
                <input type="checkbox" id="prop-shape-transparent" class="wppoppop-gp-bind" data-prop="shapeNoFill" value="1" aria-label="Transparent fill">
                <span class="wppoppop-shape-clear-sw" aria-hidden="true"></span>
                <span class="wppoppop-shape-clear-tx">Transparent</span>
            </label>
        </div>
    </div>
    <div class="wppoppop-shape-row">
        <div>
            <label class="wppoppop-shape-label" for="prop-shape-stroke-color">STROKE COLOR</label>
            <div class="wppoppop-shape-color" data-wpp-clear-for="prop-shape-stroke-transparent">
                <input type="color" id="prop-shape-stroke-color" data-wpp-prop="strokeColor" value="#0284c7">
                <label class="wppoppop-shape-clear wppoppop-shape-clear-icon" title="Transparent stroke">
                    <input type="checkbox" id="prop-shape-stroke-transparent" aria-label="Transparent stroke">
                    <span class="wppoppop-shape-clear-sw" aria-hidden="true"></span>
                </label>
            </div>
        </div>
        <div>
            <label class="wppoppop-shape-label" for="prop-shape-stroke-width">STROKE WIDTH (PX)</label>
            <input type="number" id="prop-shape-stroke-width" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="strokeWidth" min="0" max="40" value="0" style="font-size:12px;" placeholder="0">
        </div>
    </div>
    <div class="wppoppop-shape-field" style="margin-bottom:0;">
        <div class="wppoppop-shape-rot-head">
            <label class="wppoppop-shape-label" for="prop-shape-rotate" style="margin:0;">ROTATION (0&deg;&ndash;360&deg;)</label>
            <span id="prop-shape-rotate-val" class="wppoppop-shape-rot-val">0&deg;</span>
        </div>
        <input type="range" id="prop-shape-rotate" class="wppoppop-gp-bind wppoppop-shape-range" data-prop="rotate" min="0" max="360" step="1" value="0">
    </div>
</div>
