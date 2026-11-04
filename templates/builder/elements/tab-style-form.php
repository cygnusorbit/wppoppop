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
        <strong style="color:#38bdf8; display:block; margin-bottom:2px;"><?php esc_html_e('GLOBAL INPUT STYLING', 'wppoppop'); ?></strong>
        <?php esc_html_e('Configure base style of input fields through', 'wppoppop'); ?> <strong><?php esc_html_e('Popup Settings » Style Tab » Inputs', 'wppoppop'); ?></strong><?php esc_html_e('. Use the controls below for per-field alignment, icons, and inline CSS overrides.', 'wppoppop'); ?>
    </div>

    <!-- 2. Input Text Alignment (Left | Center | Right) -->
    <div class="wppoppop-insp-card" style="background:#0f172a; border:1px solid #1e293b; border-radius:8px; padding:12px; margin-bottom:12px;">
        <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:6px; text-transform:uppercase;"><?php esc_html_e('INPUT TEXT ALIGNMENT', 'wppoppop'); ?></label>
        <div id="wppoppop-form-align-group" style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:6px;">
            <button type="button" class="button wppoppop-form-align-btn" data-align="left" style="font-size:11px; font-weight:700;"><?php esc_html_e('Left', 'wppoppop'); ?></button>
            <button type="button" class="button wppoppop-form-align-btn" data-align="center" style="font-size:11px; font-weight:700;"><?php esc_html_e('Center', 'wppoppop'); ?></button>
            <button type="button" class="button wppoppop-form-align-btn" data-align="right" style="font-size:11px; font-weight:700;"><?php esc_html_e('Right', 'wppoppop'); ?></button>
        </div>
    </div>

    <!-- 3. Input Icons (Left / Right + Pixel Offsets) -->
    <div id="wppoppop-form-icons-card" class="wppoppop-insp-card" style="background:#0f172a; border:1px solid #1e293b; border-radius:8px; padding:12px; margin-bottom:12px;">
        <div style="font-size:10px; font-weight:800; color:#38bdf8; text-transform:uppercase; margin-bottom:8px;"><?php esc_html_e('INPUT ICONS & OFFSETS', 'wppoppop'); ?></div>
        <div style="display:grid; grid-template-columns:2fr 1fr; gap:8px; margin-bottom:8px;">
            <div>
                <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;"><?php esc_html_e('LEFT ICON (GLYPH / CLASS)', 'wppoppop'); ?></label>
                <input type="text" id="prop-form-icon-left" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="iconLeft" placeholder="<?php esc_attr_e('e.g. ✉ or dashicons-email', 'wppoppop'); ?>" style="font-size:12px;">
            </div>
            <div>
                <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;"><?php esc_html_e('OFFSET (PX)', 'wppoppop'); ?></label>
                <input type="number" id="prop-form-icon-left-offset" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="iconLeftOffset" min="0" max="40" value="10" style="font-size:12px;">
            </div>
        </div>
        <div style="display:flex; gap:4px; flex-wrap:wrap; margin-bottom:10px;">
            <button type="button" class="button button-small wppoppop-form-icon-preset" data-target="#prop-form-icon-left" data-glyph="✉"><?php esc_html_e('✉ Email', 'wppoppop'); ?></button>
            <button type="button" class="button button-small wppoppop-form-icon-preset" data-target="#prop-form-icon-left" data-glyph="👤"><?php esc_html_e('👤 User', 'wppoppop'); ?></button>
            <button type="button" class="button button-small wppoppop-form-icon-preset" data-target="#prop-form-icon-left" data-glyph="📞"><?php esc_html_e('📞 Phone', 'wppoppop'); ?></button>
            <button type="button" class="button button-small wppoppop-form-icon-preset" data-target="#prop-form-icon-left" data-glyph="🔒"><?php esc_html_e('🔒 Lock', 'wppoppop'); ?></button>
            <button type="button" class="button button-small wppoppop-form-icon-preset" data-target="#prop-form-icon-left" data-glyph=""><?php esc_html_e('✕ Clear', 'wppoppop'); ?></button>
        </div>
        <div style="display:grid; grid-template-columns:2fr 1fr; gap:8px;">
            <div>
                <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;"><?php esc_html_e('RIGHT ICON (GLYPH / CLASS)', 'wppoppop'); ?></label>
                <input type="text" id="prop-form-icon-right" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="iconRight" placeholder="<?php esc_attr_e('e.g. ✔ or dashicons-yes', 'wppoppop'); ?>" style="font-size:12px;">
            </div>
            <div>
                <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;"><?php esc_html_e('OFFSET (PX)', 'wppoppop'); ?></label>
                <input type="number" id="prop-form-icon-right-offset" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="iconRightOffset" min="0" max="40" value="10" style="font-size:12px;">
            </div>
        </div>
    </div>

    <!-- 4. Custom CSS Class & Inline CSS Styles Repeater (+ Add a style) -->
    <div class="wppoppop-insp-card" style="background:#0f172a; border:1px solid #1e293b; border-radius:8px; padding:12px;">
        <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px; text-transform:uppercase;"><?php esc_html_e('CUSTOM CSS CLASS', 'wppoppop'); ?></label>
        <input type="text" id="prop-form-custom-class" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="customClass" placeholder="<?php esc_attr_e('e.g. my-custom-input', 'wppoppop'); ?>" style="margin-bottom:10px; font-size:12px;">

        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:8px;">
            <span style="font-size:10px; font-weight:700; color:#94a3b8; text-transform:uppercase;"><?php esc_html_e('INLINE CSS STYLES', 'wppoppop'); ?></span>
            <button type="button" id="wppoppop-btn-add-inline-style" class="button button-small" style="font-size:11px; font-weight:700;"><?php esc_html_e('+ Add a style', 'wppoppop'); ?></button>
        </div>
        <div id="wppoppop-gp-inline-styles-list" style="display:flex; flex-direction:column; gap:6px;"></div>
    </div>
</div>
