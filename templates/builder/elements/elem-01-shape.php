<?php
if (!defined('ABSPATH')) exit;
?>
<!-- DECOMPOSED MODULE 01: RECTANGLE / SQUARE / VECTOR SHAPE (shape) -->
<div class="element-panel wppoppop-panel-specific" id="panel-elem-shape" data-elem-family="media" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;">1. Shape / Vector Geometry</div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px;">
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">SVG SHAPE PRESET</label>
            <select id="prop-shape-type" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="shapePreset" style="font-size:12px;">
                <option value="rectangle">Rectangle / Square</option>
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
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">ROTATION (0°–360°)</label>
            <input type="number" id="prop-shape-rotate" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="rotate" min="0" max="360" value="0" style="font-size:12px;">
        </div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px;">
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">FILL COLOR</label>
            <input type="color" id="prop-shape-fill" class="widefat wppoppop-gp-bind" data-prop="shapeFill" value="#3b82f6" style="height:34px;padding:2px;background:#090d16;border:1px solid #1e293b;border-radius:6px;cursor:pointer;">
        </div>
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">STROKE WIDTH (PX)</label>
            <input type="number" id="prop-shape-stroke-width" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="strokeWidth" min="0" max="40" value="0" style="font-size:12px;">
        </div>
    </div>
    <div style="margin-bottom:10px;">
        <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">STROKE COLOR</label>
        <input type="color" id="prop-shape-stroke-color" class="widefat wppoppop-gp-bind" data-prop="strokeColor" value="#0284c7" style="height:34px;padding:2px;background:#090d16;border:1px solid #1e293b;border-radius:6px;cursor:pointer;">
    </div>
    <label style="display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:600;color:#cbd5e1;cursor:pointer;">
        <input type="checkbox" id="prop-shape-transparent" class="wppoppop-gp-bind" data-prop="shapeNoFill" value="1"> Transparent Fill (Outline Mode)
    </label>
</div>
