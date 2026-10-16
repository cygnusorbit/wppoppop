<?php
if (!defined('ABSPATH')) exit;
/**
 * Decomposed Module: Authoritative Style Tab Suite (All Elements)
 */
?>
<!-- 1. OPACITY & VISIBILITY -->
<div class="wppoppop-insp-section" id="insp-section-opacity" style="margin-bottom:14px;">
    <div class="wppoppop-insp-section-title">OPACITY &amp; VISIBILITY</div>
    <div class="wppoppop-prop-row" style="display:flex; align-items:center; gap:10px;">
        <input type="range" id="prop-opacity" class="wppoppop-gp-bind" data-prop="opacity" min="0" max="100" value="100" style="flex:1;" />
        <span id="prop-opacity-val" style="font-size:12px; font-weight:700; color:#38bdf8; min-width:38px; text-align:right;">100%</span>
    </div>
</div>

<!-- 2. BACKGROUND & TRANSPARENCY -->
<div class="wppoppop-insp-section" id="insp-section-bg-color" style="margin-bottom:14px;">
    <div class="wppoppop-insp-section-title">BACKGROUND COLOR</div>
    <div class="wppoppop-prop-row" style="display:flex; align-items:center; gap:8px;">
        <input type="color" id="prop-bg-color" class="widefat wppoppop-gp-bind" data-prop="bgColor" value="#3b82f6" style="height:34px; padding:2px; background:#090d16; border:1px solid #1e293b; border-radius:6px; cursor:pointer; flex:1;" />
        <label style="display:inline-flex; align-items:center; gap:5px; font-size:11px; color:#cbd5e1; cursor:pointer; white-space:nowrap;">
            <input type="checkbox" id="prop-bg-transparent" class="wppoppop-gp-bind" data-prop="transparentFill" value="1" /> Transparent
        </label>
    </div>
</div>

<!-- 3. TYPOGRAPHY & TEXT SPACING (Scoped for text-bearing elements) -->
<div class="wppoppop-insp-section" id="insp-section-typography" style="margin-bottom:14px;">
    <div class="wppoppop-insp-section-title">TYPOGRAPHY &amp; SPACING</div>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:8px;">
        <div>
            <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px; text-transform:uppercase;">Font Size (px)</label>
            <input type="number" id="prop-font-size" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="fontSize" min="8" max="120" value="16" style="font-size:12px;" />
        </div>
        <div>
            <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px; text-transform:uppercase;">Font Weight</label>
            <select id="prop-font-weight" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="fontWeight" style="font-size:12px;">
                <option value="400">Regular (400)</option>
                <option value="500">Medium (500)</option>
                <option value="600">Semi-Bold (600)</option>
                <option value="700">Bold (700)</option>
                <option value="800">Extra-Bold (800)</option>
            </select>
        </div>
    </div>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:8px;">
        <div>
            <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px; text-transform:uppercase;">Line Height</label>
            <input type="number" id="prop-line-height" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="lineHeight" step="0.1" min="0.8" max="3" value="1.4" style="font-size:12px;" />
        </div>
        <div>
            <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px; text-transform:uppercase;">Letter Spacing</label>
            <input type="number" id="prop-letter-spacing" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="letterSpacing" step="0.5" min="-5" max="20" value="0" style="font-size:12px;" />
        </div>
    </div>
    <div style="margin-bottom:8px;">
        <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px; text-transform:uppercase;">Text Color</label>
        <input type="color" id="prop-color" class="widefat wppoppop-gp-bind" data-prop="color" value="#ffffff" style="height:34px; padding:2px; background:#090d16; border:1px solid #1e293b; border-radius:6px; cursor:pointer;" />
    </div>
</div>

<!-- 4. DIRECTIONAL PADDING -->
<div class="wppoppop-insp-section" id="insp-section-padding" style="margin-bottom:14px;">
    <div class="wppoppop-insp-section-title">PADDING (PX)</div>
    <div style="display:grid; grid-template-columns:1fr 1fr 1fr 1fr; gap:6px;">
        <div>
            <label style="display:block; font-size:9px; color:#94a3b8; text-align:center;">TOP</label>
            <input type="number" id="prop-pad-top" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="paddingTop" min="0" max="200" value="0" style="font-size:11px; text-align:center;" />
        </div>
        <div>
            <label style="display:block; font-size:9px; color:#94a3b8; text-align:center;">RIGHT</label>
            <input type="number" id="prop-pad-right" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="paddingRight" min="0" max="200" value="0" style="font-size:11px; text-align:center;" />
        </div>
        <div>
            <label style="display:block; font-size:9px; color:#94a3b8; text-align:center;">BOTTOM</label>
            <input type="number" id="prop-pad-bottom" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="paddingBottom" min="0" max="200" value="0" style="font-size:11px; text-align:center;" />
        </div>
        <div>
            <label style="display:block; font-size:9px; color:#94a3b8; text-align:center;">LEFT</label>
            <input type="number" id="prop-pad-left" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="paddingLeft" min="0" max="200" value="0" style="font-size:11px; text-align:center;" />
        </div>
    </div>
</div>

<!-- 5. BORDER & RADIUS -->
<div class="wppoppop-insp-section" id="insp-section-border" style="margin-bottom:14px;">
    <div class="wppoppop-insp-section-title">BORDER &amp; CORNER RADIUS</div>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:8px;">
        <div>
            <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px; text-transform:uppercase;">Border Width</label>
            <input type="number" id="prop-border-width" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="borderWidth" min="0" max="40" value="0" style="font-size:12px;" />
        </div>
        <div>
            <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px; text-transform:uppercase;">Border Style</label>
            <select id="prop-border-style" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="borderStyle" style="font-size:12px;">
                <option value="solid">Solid</option>
                <option value="dashed">Dashed</option>
                <option value="dotted">Dotted</option>
                <option value="double">Double</option>
                <option value="none">None</option>
            </select>
        </div>
    </div>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
        <div>
            <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px; text-transform:uppercase;">Border Color</label>
            <input type="color" id="prop-border-color" class="widefat wppoppop-gp-bind" data-prop="borderColor" value="#334155" style="height:34px; padding:2px; background:#090d16; border:1px solid #1e293b; border-radius:6px; cursor:pointer;" />
        </div>
        <div>
            <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px; text-transform:uppercase;">Radius (px)</label>
            <input type="number" id="prop-border-radius" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="borderRadius" min="0" max="200" value="4" style="font-size:12px;" />
        </div>
    </div>
</div>

<!-- 6. 5-SLIDER BOX SHADOW -->
<div class="wppoppop-insp-section" id="insp-section-shadow" style="margin-bottom:14px;">
    <div class="wppoppop-insp-section-title">BOX SHADOW</div>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:8px;">
        <div>
            <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px; text-transform:uppercase;">Offset X (px)</label>
            <input type="number" id="prop-shadow-x" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="shadowX" min="-50" max="50" value="0" style="font-size:12px;" />
        </div>
        <div>
            <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px; text-transform:uppercase;">Offset Y (px)</label>
            <input type="number" id="prop-shadow-y" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="shadowY" min="-50" max="50" value="4" style="font-size:12px;" />
        </div>
    </div>
    <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:6px;">
        <div>
            <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px; text-transform:uppercase;">Blur</label>
            <input type="number" id="prop-shadow-blur" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="shadowBlur" min="0" max="100" value="12" style="font-size:12px;" />
        </div>
        <div>
            <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px; text-transform:uppercase;">Spread</label>
            <input type="number" id="prop-shadow-spread" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="shadowSpread" min="-50" max="50" value="0" style="font-size:12px;" />
        </div>
        <div>
            <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px; text-transform:uppercase;">Color</label>
            <input type="color" id="prop-shadow-color" class="widefat wppoppop-gp-bind" data-prop="shadowColor" value="#000000" style="height:34px; padding:2px; background:#090d16; border:1px solid #1e293b; border-radius:6px; cursor:pointer;" />
        </div>
    </div>
</div>

<!-- 7. BUTTON HOVER STATE (Strictly Button & Submit) -->
<div class="wppoppop-insp-card wppoppop-media-style-scope" id="prop-btn-hover-section" data-style-elements="button,link,link_btn,step,step_btn,submit,submit_btn" style="display:none; background:#1e293b; border:1px solid #334155; border-radius:6px; padding:12px; margin-bottom:14px;">
    <div style="font-size:11px; font-weight:700; text-transform:uppercase; color:#38bdf8; margin-bottom:10px; letter-spacing:0.5px;">Button Hover State</div>
    <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:8px;">
        <div>
            <label style="display:block; font-size:10px; color:#94a3b8; margin-bottom:4px;">Hover Bg</label>
            <input type="color" id="prop-elem-hover-bg" class="wppoppop-gp-bind" data-prop="hoverBgColor" value="#0284c7" style="width:100%; height:30px; border:1px solid #475569; border-radius:4px; background:#0f172a; cursor:pointer;" />
        </div>
        <div>
            <label style="display:block; font-size:10px; color:#94a3b8; margin-bottom:4px;">Hover Text</label>
            <input type="color" id="prop-elem-hover-color" class="wppoppop-gp-bind" data-prop="hoverTextColor" value="#ffffff" style="width:100%; height:30px; border:1px solid #475569; border-radius:4px; background:#0f172a; cursor:pointer;" />
        </div>
        <div>
            <label style="display:block; font-size:10px; color:#94a3b8; margin-bottom:4px;">Hover Border</label>
            <input type="color" id="prop-elem-hover-border" class="wppoppop-gp-bind" data-prop="hoverBorderColor" value="#0284c7" style="width:100%; height:30px; border:1px solid #475569; border-radius:4px; background:#0f172a; cursor:pointer;" />
        </div>
    </div>
</div>

<!-- 8. FORM INPUT STYLE MODULE (Form Family 10-25) -->
<div id="wppoppop-form-style-module" class="wppoppop-decomposed-module" style="display:none; margin-bottom:14px;">
    <div style="background:rgba(56,189,248,0.1); border:1px solid rgba(56,189,248,0.3); border-radius:6px; padding:10px 12px; margin-bottom:12px; font-size:11px; color:#bae6fd; line-height:1.45;">
        <strong style="color:#38bdf8; display:block; margin-bottom:2px;">GLOBAL INPUT STYLING</strong>
        Per-field text alignment, input icons, and custom inline CSS overrides.
    </div>
    <div class="wppoppop-insp-card" style="background:#0f172a; border:1px solid #1e293b; border-radius:8px; padding:12px; margin-bottom:12px;">
        <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:6px; text-transform:uppercase;">INPUT TEXT ALIGNMENT</label>
        <div id="wppoppop-form-align-group" style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:6px;">
            <button type="button" class="button wppoppop-form-align-btn active" data-align="left" style="font-size:11px; font-weight:700;">Left</button>
            <button type="button" class="button wppoppop-form-align-btn" data-align="center" style="font-size:11px; font-weight:700;">Center</button>
            <button type="button" class="button wppoppop-form-align-btn" data-align="right" style="font-size:11px; font-weight:700;">Right</button>
        </div>
    </div>
    <div id="wppoppop-form-icons-card" class="wppoppop-insp-card" style="background:#0f172a; border:1px solid #1e293b; border-radius:8px; padding:12px; margin-bottom:12px;">
        <div style="font-size:10px; font-weight:800; color:#38bdf8; text-transform:uppercase; margin-bottom:8px;">INPUT ICONS &amp; OFFSETS</div>
        <div style="display:grid; grid-template-columns:2fr 1fr; gap:8px; margin-bottom:8px;">
            <div>
                <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;">LEFT ICON (GLYPH / CLASS)</label>
                <input type="text" id="prop-form-icon-left" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="iconLeft" placeholder="e.g. ✉ or dashicons-email" style="font-size:12px;" />
            </div>
            <div>
                <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;">OFFSET (PX)</label>
                <input type="number" id="prop-form-icon-left-offset" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="iconLeftOffset" min="0" max="40" value="10" style="font-size:12px;" />
            </div>
        </div>
        <div style="display:flex; gap:4px; flex-wrap:wrap; margin-bottom:10px;">
            <button type="button" class="button button-small wppoppop-form-icon-preset" data-target="#prop-form-icon-left" data-glyph="✉">✉ Email</button>
            <button type="button" class="button button-small wppoppop-form-icon-preset" data-target="#prop-form-icon-left" data-glyph="👤">👤 User</button>
            <button type="button" class="button button-small wppoppop-form-icon-preset" data-target="#prop-form-icon-left" data-glyph="📞">📞 Phone</button>
            <button type="button" class="button button-small wppoppop-form-icon-preset" data-target="#prop-form-icon-left" data-glyph="🔒">🔒 Lock</button>
            <button type="button" class="button button-small wppoppop-form-icon-preset" data-target="#prop-form-icon-left" data-glyph="">✕ Clear</button>
        </div>
        <div style="display:grid; grid-template-columns:2fr 1fr; gap:8px;">
            <div>
                <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;">RIGHT ICON (GLYPH / CLASS)</label>
                <input type="text" id="prop-form-icon-right" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="iconRight" placeholder="e.g. ✔ or dashicons-yes" style="font-size:12px;" />
            </div>
            <div>
                <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;">OFFSET (PX)</label>
                <input type="number" id="prop-form-icon-right-offset" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="iconRightOffset" min="0" max="40" value="10" style="font-size:12px;" />
            </div>
        </div>
    </div>
    <div class="wppoppop-insp-card" style="background:#0f172a; border:1px solid #1e293b; border-radius:8px; padding:12px;">
        <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px; text-transform:uppercase;">CUSTOM CSS CLASS</label>
        <input type="text" id="prop-form-custom-class" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="customClass" placeholder="e.g. my-custom-input" style="margin-bottom:10px; font-size:12px;" />
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:8px;">
            <span style="font-size:10px; font-weight:700; color:#94a3b8; text-transform:uppercase;">INLINE CSS STYLES</span>
            <button type="button" id="wppoppop-btn-add-inline-style" class="button button-small" style="font-size:11px; font-weight:700;">+ Add a style</button>
        </div>
        <div id="wppoppop-gp-inline-styles-list" style="display:flex; flex-direction:column; gap:6px;"></div>
    </div>
</div>
