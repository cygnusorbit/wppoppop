<?php
if (!defined('ABSPATH')) exit;
/**
 * Decomposed Module: Form Family Style Tab Suite (Elements 10-25)
 * Includes: Global Input Style Notice, Text Alignment (L|C|R),
 * Left & Right Input Icons + Pixel Offsets, Custom CSS Class, and CSS Styles Repeater
 */
?>
<div id="wppoppop-form-style-module" class="wppoppop-decomposed-module" style="display:none; margin-bottom:14px;">
    <!-- 1. Global Input Style Notice Banner -->
    <div style="background:rgba(56,189,248,0.1); border:1px solid rgba(56,189,248,0.3); border-radius:6px; padding:10px 12px; margin-bottom:12px; font-size:11px; color:#bae6fd; line-height:1.45;">
        <strong style="color:#38bdf8; display:block; margin-bottom:2px;">GLOBAL INPUT STYLING</strong>
        Configure base style of input fields through <strong>Popup Settings &raquo; Style Tab &raquo; Inputs</strong>. Use the controls below for per-field alignment, icons, and inline CSS overrides.
    </div>

    <!-- 2. Input Text Alignment (Left | Center | Right) -->
    <div class="wppoppop-insp-card" style="background:#0f172a; border:1px solid #1e293b; border-radius:8px; padding:12px; margin-bottom:12px;">
        <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:6px; text-transform:uppercase;">INPUT TEXT ALIGNMENT</label>
        <div id="wppoppop-form-align-group" style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:6px;">
            <button type="button" class="button wppoppop-form-align-btn active" data-align="left" style="font-size:11px; font-weight:700;">Left</button>
            <button type="button" class="button wppoppop-form-align-btn" data-align="center" style="font-size:11px; font-weight:700;">Center</button>
            <button type="button" class="button wppoppop-form-align-btn" data-align="right" style="font-size:11px; font-weight:700;">Right</button>
        </div>
    </div>

    <!-- 3. Input Icons (Left / Right + Pixel Offsets) -->
    <div id="wppoppop-form-icons-card" class="wppoppop-insp-card" style="background:#0f172a; border:1px solid #1e293b; border-radius:8px; padding:12px; margin-bottom:12px;">
        <div style="font-size:10px; font-weight:800; color:#38bdf8; text-transform:uppercase; margin-bottom:8px;">INPUT ICONS &amp; OFFSETS</div>
        <div style="display:grid; grid-template-columns:2fr 1fr; gap:8px; margin-bottom:8px;">
            <div>
                <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;">LEFT ICON (GLYPH / CLASS)</label>
                <input type="text" id="prop-form-icon-left" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="iconLeft" placeholder="e.g. ✉ or dashicons-email" style="font-size:12px;">
            </div>
            <div>
                <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;">OFFSET (PX)</label>
                <input type="number" id="prop-form-icon-left-offset" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="iconLeftOffset" min="0" max="40" value="10" style="font-size:12px;">
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
                <input type="text" id="prop-form-icon-right" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="iconRight" placeholder="e.g. ✔ or dashicons-yes" style="font-size:12px;">
            </div>
            <div>
                <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;">OFFSET (PX)</label>
                <input type="number" id="prop-form-icon-right-offset" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="iconRightOffset" min="0" max="40" value="10" style="font-size:12px;">
            </div>
        </div>
    </div>

    <!-- 4. Custom CSS Class & Inline CSS Styles Repeater (+ Add a style) -->
    <div class="wppoppop-insp-card" style="background:#0f172a; border:1px solid #1e293b; border-radius:8px; padding:12px;">
        <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px; text-transform:uppercase;">CUSTOM CSS CLASS</label>
        <input type="text" id="prop-form-custom-class" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="customClass" placeholder="e.g. my-custom-input" style="margin-bottom:10px; font-size:12px;">

        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:8px;">
            <span style="font-size:10px; font-weight:700; color:#94a3b8; text-transform:uppercase;">INLINE CSS STYLES</span>
            <button type="button" id="wppoppop-btn-add-inline-style" class="button button-small" style="font-size:11px; font-weight:700;">+ Add a style</button>
        </div>
        <div id="wppoppop-gp-inline-styles-list" style="display:flex; flex-direction:column; gap:6px;"></div>
    </div>
</div>
