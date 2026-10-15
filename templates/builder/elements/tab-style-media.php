<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!-- DECOMPOSED TAB MODULE: MEDIA & ACTION ELEMENT STYLE (Elements 1-9 & 26) -->
<div id="insp-section-media-style-suite">
    <!-- Opacity Card -->
    <div class="wppoppop-insp-card" id="insp-card-media-opacity" style="background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:12px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
            <label for="prop-opacity" style="font-size:10px;font-weight:700;color:#38bdf8;text-transform:uppercase;">OPACITY &amp; VISIBILITY</label>
            <span id="prop-opacity-val" class="wppoppop-opacity-badge" style="font-size:11px;font-weight:700;color:#e2e8f0;background:#1e293b;padding:2px 6px;border-radius:4px;">100%</span>
        </div>
        <input type="range" id="prop-opacity" min="0" max="100" step="1" value="100" style="width:100%;cursor:pointer;">
    </div>

    <!-- Typography Card (Scoped to Label, HTML, Button, Submit, Progress) -->
    <div class="wppoppop-insp-card" id="insp-section-typography" style="background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:12px;">
        <div style="font-size:10px;font-weight:700;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;">TYPOGRAPHY &amp; TEXT STYLING</div>
        <div style="margin-bottom:8px;">
            <label for="prop-font-family" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">FONT FAMILY</label>
            <select id="prop-font-family" class="widefat wppoppop-select wppoppop-input" style="font-size:12px;">
                <option value="inherit">Inherit Default</option>
                <option value="Inter, sans-serif">Inter</option>
                <option value="Roboto, sans-serif">Roboto</option>
                <option value="'Open Sans', sans-serif">Open Sans</option>
                <option value="Montserrat, sans-serif">Montserrat</option>
                <option value="Poppins, sans-serif">Poppins</option>
                <option value="Lato, sans-serif">Lato</option>
                <option value="'Playfair Display', serif">Playfair Display</option>
                <option value="monospace">Monospace</option>
            </select>
        </div>
        <div class="wppoppop-gp-2col-grid" style="margin-bottom:8px;">
            <div class="wppoppop-gp-col">
                <label for="prop-font-size" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">SIZE (PX)</label>
                <input type="number" id="prop-font-size" class="widefat wppoppop-input" value="16" min="8" max="120" style="font-size:12px;">
            </div>
            <div class="wppoppop-gp-col">
                <label for="prop-font-weight" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">WEIGHT</label>
                <select id="prop-font-weight" class="widefat wppoppop-select wppoppop-input" style="font-size:12px;">
                    <option value="300">Light (300)</option>
                    <option value="400" selected>Regular (400)</option>
                    <option value="500">Medium (500)</option>
                    <option value="600">Semi-Bold (600)</option>
                    <option value="700">Bold (700)</option>
                    <option value="800">Extra-Bold (800)</option>
                </select>
            </div>
        </div>
        <div class="wppoppop-gp-2col-grid" style="margin-bottom:8px;">
            <div class="wppoppop-gp-col">
                <label for="prop-line-height" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">LINE HEIGHT</label>
                <input type="number" id="prop-line-height" class="widefat wppoppop-input" value="1.4" step="0.1" min="0.8" max="3" style="font-size:12px;">
            </div>
            <div class="wppoppop-gp-col">
                <label for="prop-letter-spacing" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">LETTER SPACING (PX)</label>
                <input type="number" id="prop-letter-spacing" class="widefat wppoppop-input" value="0" step="0.5" min="-5" max="30" style="font-size:12px;">
            </div>
        </div>
        <div>
            <label for="prop-text-align" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">TEXT ALIGN</label>
            <select id="prop-text-align" class="widefat wppoppop-select wppoppop-input" style="font-size:12px;">
                <option value="left">Left</option>
                <option value="center">Center</option>
                <option value="right">Right</option>
                <option value="justify">Justify</option>
            </select>
        </div>
    </div>

    <!-- Colors & Padding Card -->
    <div class="wppoppop-insp-card" id="insp-card-media-colors-padding" style="background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:12px;">
        <div style="font-size:10px;font-weight:700;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;">COLORS &amp; SPACING</div>
        <div class="wppoppop-gp-2col-grid" style="margin-bottom:10px;">
            <div class="wppoppop-gp-col" id="insp-col-fg-color">
                <label for="prop-color" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">TEXT / ICON COLOR</label>
                <input type="color" id="prop-color" class="widefat wppoppop-input" value="#1e293b" style="height:32px;padding:2px;cursor:pointer;">
            </div>
            <div class="wppoppop-gp-col" id="insp-col-bg-color">
                <label for="prop-bg-color" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">BACKGROUND / FILL</label>
                <input type="color" id="prop-bg-color" class="widefat wppoppop-input" value="#ffffff" style="height:32px;padding:2px;cursor:pointer;">
            </div>
        </div>
        <div id="insp-row-media-padding">
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">PADDING (TOP / RIGHT / BOTTOM / LEFT PX)</label>
            <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:6px;">
                <input type="number" id="prop-pad-top" class="widefat wppoppop-input" value="0" placeholder="T" style="font-size:12px;text-align:center;">
                <input type="number" id="prop-pad-right" class="widefat wppoppop-input" value="0" placeholder="R" style="font-size:12px;text-align:center;">
                <input type="number" id="prop-pad-bottom" class="widefat wppoppop-input" value="0" placeholder="B" style="font-size:12px;text-align:center;">
                <input type="number" id="prop-pad-left" class="widefat wppoppop-input" value="0" placeholder="L" style="font-size:12px;text-align:center;">
            </div>
        </div>
    </div>

    <!-- Border & Box Shadow Card -->
    <div class="wppoppop-insp-card" id="insp-card-media-border-shadow" style="background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:12px;">
        <div style="font-size:10px;font-weight:700;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;">BORDER &amp; BOX SHADOW</div>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:6px;margin-bottom:8px;">
            <div>
                <label for="prop-border-width" style="display:block;font-size:9px;font-weight:700;color:#94a3b8;margin-bottom:3px;text-transform:uppercase;">WIDTH (PX)</label>
                <input type="number" id="prop-border-width" class="widefat wppoppop-input" value="0" min="0" max="30" style="font-size:12px;">
            </div>
            <div>
                <label for="prop-border-style" style="display:block;font-size:9px;font-weight:700;color:#94a3b8;margin-bottom:3px;text-transform:uppercase;">STYLE</label>
                <select id="prop-border-style" class="widefat wppoppop-select wppoppop-input" style="font-size:12px;">
                    <option value="none">None</option>
                    <option value="solid">Solid</option>
                    <option value="dashed">Dashed</option>
                    <option value="dotted">Dotted</option>
                    <option value="double">Double</option>
                </select>
            </div>
            <div>
                <label for="prop-border-radius" style="display:block;font-size:9px;font-weight:700;color:#94a3b8;margin-bottom:3px;text-transform:uppercase;">RADIUS (PX)</label>
                <input type="number" id="prop-border-radius" class="widefat wppoppop-input" value="0" min="0" max="200" style="font-size:12px;">
            </div>
        </div>
        <div style="margin-bottom:10px;">
            <label for="prop-border-color" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">BORDER COLOR</label>
            <input type="color" id="prop-border-color" class="widefat wppoppop-input" value="#cbd5e1" style="height:28px;padding:2px;cursor:pointer;">
        </div>
        <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">BOX SHADOW (X / Y / BLUR / SPREAD PX)</label>
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:6px;margin-bottom:8px;">
            <input type="number" id="prop-shadow-x" class="widefat wppoppop-input" value="0" placeholder="X" style="font-size:12px;text-align:center;">
            <input type="number" id="prop-shadow-y" class="widefat wppoppop-input" value="0" placeholder="Y" style="font-size:12px;text-align:center;">
            <input type="number" id="prop-shadow-blur" class="widefat wppoppop-input" value="0" min="0" placeholder="Blur" style="font-size:12px;text-align:center;">
            <input type="number" id="prop-shadow-spread" class="widefat wppoppop-input" value="0" placeholder="Sprd" style="font-size:12px;text-align:center;">
        </div>
        <div>
            <label for="prop-shadow-color" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">SHADOW COLOR</label>
            <input type="text" id="prop-shadow-color" class="widefat wppoppop-input" value="rgba(0,0,0,0.15)" style="font-size:12px;">
        </div>
    </div>

    <!-- Button Hover State Card (Scoped strictly to Button & Submit) -->
    <div class="wppoppop-insp-card" id="wppoppop-hover-state-card" data-section="prop-btn-hover-section" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:12px;">
        <div style="font-size:10px;font-weight:700;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;">BUTTON HOVER STATE</div>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:6px;">
            <div>
                <label for="prop-hover-bg" style="display:block;font-size:9px;font-weight:700;color:#94a3b8;margin-bottom:3px;text-transform:uppercase;">HOVER BG</label>
                <input type="color" id="prop-hover-bg" class="widefat wppoppop-input" value="#1d4ed8" style="height:30px;padding:2px;cursor:pointer;">
            </div>
            <div>
                <label for="prop-hover-color" style="display:block;font-size:9px;font-weight:700;color:#94a3b8;margin-bottom:3px;text-transform:uppercase;">HOVER TEXT</label>
                <input type="color" id="prop-hover-color" class="widefat wppoppop-input" value="#ffffff" style="height:30px;padding:2px;cursor:pointer;">
            </div>
            <div>
                <label for="prop-hover-border" style="display:block;font-size:9px;font-weight:700;color:#94a3b8;margin-bottom:3px;text-transform:uppercase;">HOVER BORDER</label>
                <input type="color" id="prop-hover-border" class="widefat wppoppop-input" value="#1d4ed8" style="height:30px;padding:2px;cursor:pointer;">
            </div>
        </div>
    </div>
</div>
