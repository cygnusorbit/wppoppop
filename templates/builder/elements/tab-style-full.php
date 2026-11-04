<?php
if (!defined('ABSPATH')) exit;
?>
<!-- WPPOPPOP UNIFIED STYLE TAB SUITE -->
<style>
.wppoppop-sec-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;}
.wppoppop-sec-head .wppoppop-sec-title{font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;}
.wppoppop-insp-switch{position:relative;display:inline-block;width:34px;height:18px;margin:0;cursor:pointer;}
.wppoppop-insp-switch input{position:absolute;opacity:0;width:100%;height:100%;margin:0;cursor:pointer;z-index:2;}
.wppoppop-insp-switch .wppoppop-insp-slider{position:absolute;top:0;right:0;bottom:0;left:0;background:#475569;border-radius:18px;-webkit-transition:background .2s ease;transition:background .2s ease;}
.wppoppop-insp-switch .wppoppop-insp-slider:before{content:"";position:absolute;left:2px;top:2px;width:14px;height:14px;background:#fff;border-radius:50%;-webkit-transition:-webkit-transform .2s ease;transition:transform .2s ease;}
.wppoppop-insp-switch input:checked + .wppoppop-insp-slider{background:#2563eb;}
.wppoppop-insp-switch input:checked + .wppoppop-insp-slider:before{-webkit-transform:translateX(16px);transform:translateX(16px);}
.wppoppop-insp-switch input:focus + .wppoppop-insp-slider{box-shadow:0 0 0 2px #38bdf8;}
.wppoppop-sec-off .wppoppop-sec-body{opacity:.4;pointer-events:none;}
</style>

<!-- WPPOPPOP_TL_NOBORDER --><!-- WPPOPPOP_TL_STYLE: Tile hides icons/opacity/background/padding/shadow/custom css; shows border + hover state -->
<style>
#insp-tab-style.wppoppop-tl-style #wppoppop-form-icons-card,
#insp-tab-style.wppoppop-tl-style #insp-section-opacity,
#insp-tab-style.wppoppop-tl-style #insp-section-padding,
#insp-tab-style.wppoppop-tl-style #insp-section-border,
#insp-tab-style.wppoppop-tl-style #insp-section-shadow,
#insp-tab-style.wppoppop-tl-style #insp-section-custom-css { display: none !important; }
#insp-tab-style.wppoppop-tl-style #insp-section-typography,
#insp-tab-style.wppoppop-tl-style #insp-section-bg-color, /* WPPOPPOP_TL_BG */
#insp-tab-style.wppoppop-tl-style #prop-btn-hover-section { display: block !important; }
</style>

<!-- WPPOPPOP_SHAPE_STYLE: Shape hides Opacity & Visibility and Background Color (fill is set in Shape / Vector Geometry); CSS-only so selection never flashes hidden cards -->
<style>
#insp-tab-style.wppoppop-shape-style #insp-section-opacity,
#insp-tab-style.wppoppop-shape-style #insp-section-bg-color,
#insp-tab-style.wppoppop-shape-style #insp-section-text-align,
#insp-tab-style.wppoppop-shape-style #insp-section-submit-text-align,
#insp-tab-style.wppoppop-shape-style #insp-section-typography,
#insp-tab-style.wppoppop-shape-style #wppoppop-form-style-module,
#insp-tab-style.wppoppop-shape-style #prop-btn-hover-section { display: none !important; }
</style>

<!-- WPPOPPOP_RD_STYLE: Radio hides everything except Alignment and Typography -->
<style>
#insp-tab-style.wppoppop-rd-style #wppoppop-form-icons-card,
#insp-tab-style.wppoppop-rd-style #insp-section-opacity,
#insp-tab-style.wppoppop-rd-style #insp-section-bg-color,
#insp-tab-style.wppoppop-rd-style #insp-section-padding,
#insp-tab-style.wppoppop-rd-style #insp-section-border,
#insp-tab-style.wppoppop-rd-style #insp-section-shadow,
#insp-tab-style.wppoppop-rd-style #insp-section-custom-css { display: none !important; }
#insp-tab-style.wppoppop-rd-style #insp-section-typography { display: block !important; }
</style>

<!-- WPPOPPOP_CB_STYLE: Checkbox hides all style cards except Typography -->
<style>
#insp-tab-style.wppoppop-cb-style #wppoppop-form-icons-card,
#insp-tab-style.wppoppop-cb-style #insp-section-opacity,
#insp-tab-style.wppoppop-cb-style #insp-section-padding,
#insp-tab-style.wppoppop-cb-style #insp-section-border,
#insp-tab-style.wppoppop-cb-style #insp-section-custom-css { display: none !important; }
#insp-tab-style.wppoppop-cb-style #insp-section-typography { display: block !important; } /* WPPOPPOP_CB_TYPO */
#insp-tab-style.wppoppop-cb-style #insp-section-bg-color { display: block !important; } /* WPPOPPOP_CB_BG */
#insp-tab-style.wppoppop-cb-style #insp-section-shadow { display: block !important; } /* WPPOPPOP_CB_SHADOW */
</style>

<!-- WPPOPPOP_TF_STYLE_LEAN: Text Field hides generic style cards -->
<style>
#insp-tab-style.wppoppop-date-style { display: block; }
</style>

<!-- WPPOPPOP_DATE_STYLE: Date hides Opacity/Visibility, Background Color, Padding, Box Shadow, Custom CSS & Inline Styles -->
<style>
#insp-tab-style.wppoppop-date-style #insp-section-opacity,
#insp-tab-style.wppoppop-date-style #insp-section-bg-color,
#insp-tab-style.wppoppop-date-style #insp-section-padding,
#insp-tab-style.wppoppop-date-style #insp-section-shadow,
#insp-tab-style.wppoppop-date-style #insp-section-custom-css { display: none !important; }
#insp-tab-style.wppoppop-date-style #insp-section-typography,
#insp-tab-style.wppoppop-date-style #insp-section-border,
#insp-tab-style.wppoppop-date-style #wppoppop-form-icons-card { display: block !important; }
</style>
<style>
#insp-tab-style.wppoppop-tf-style-lean #insp-section-opacity,
#insp-tab-style.wppoppop-tf-style-lean #insp-section-bg-color,
#insp-tab-style.wppoppop-tf-style-lean #insp-section-typography,
#insp-tab-style.wppoppop-tf-style-lean #insp-section-padding,
#insp-tab-style.wppoppop-tf-style-lean #insp-section-border,
#insp-tab-style.wppoppop-tf-style-lean #insp-section-shadow,
#insp-tab-style.wppoppop-tf-style-lean #insp-section-custom-css { display: none !important; }
</style>

<!-- A. FORM INPUT STYLE MODULE (Form Family 10-25) -->
<div id="wppoppop-form-style-module" class="wppoppop-insp-section" style="display:none; margin-bottom:14px;">
    <div style="background:rgba(56,189,248,0.12); border:1px solid rgba(56,189,248,0.35); border-radius:6px; padding:10px 12px; margin-bottom:12px; font-size:11px; color:#bae6fd; line-height:1.45;">
        <strong style="color:#38bdf8; display:block; margin-bottom:2px;"><?php esc_html_e('INPUT STYLING', 'wppoppop'); ?></strong>
        <?php esc_html_e('Base input styling is inherited from', 'wppoppop'); ?> <strong><?php esc_html_e('Popup Settings » Style » Inputs', 'wppoppop'); ?></strong><?php esc_html_e('. Configure per-layer alignment, left/right icons, and CSS overrides below.', 'wppoppop'); ?>
    </div>
    <div class="wppoppop-insp-card" style="background:#0f172a; border:1px solid #1e293b; border-radius:8px; padding:12px; margin-bottom:12px;">
        <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:6px; text-transform:uppercase;"><?php esc_html_e('TEXT ALIGNMENT', 'wppoppop'); ?></label>
        <div id="wppoppop-form-align-group" style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:6px;">
            <button type="button" class="button wppoppop-form-align-btn" data-align="left" style="font-size:11px; font-weight:700;"><?php esc_html_e('Left', 'wppoppop'); ?></button>
            <button type="button" class="button wppoppop-form-align-btn" data-align="center" style="font-size:11px; font-weight:700;"><?php esc_html_e('Center', 'wppoppop'); ?></button>
            <button type="button" class="button wppoppop-form-align-btn" data-align="right" style="font-size:11px; font-weight:700;"><?php esc_html_e('Right', 'wppoppop'); ?></button>
        </div>
    </div>
    <div id="wppoppop-form-icons-card" class="wppoppop-insp-card" style="background:#0f172a; border:1px solid #1e293b; border-radius:8px; padding:12px; margin-bottom:12px;">
        <div style="font-size:10px; font-weight:800; color:#38bdf8; text-transform:uppercase; margin-bottom:8px;"><?php esc_html_e('INPUT ICONS & OFFSETS', 'wppoppop'); ?></div>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
            <div>
                <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;"><?php esc_html_e('LEFT ICON', 'wppoppop'); ?></label>
                <div style="display:flex; gap:4px; align-items:center;">
                    <button type="button" class="button wppoppop-fa-pick-btn" data-target="#prop-form-icon-left" title="<?php esc_attr_e('Choose icon', 'wppoppop'); ?><?php esc_html_e('" aria-label="', 'wppoppop'); ?><?php esc_attr_e('Choose left icon', 'wppoppop'); ?>" style="min-width:36px; height:30px; padding:0; display:inline-flex; align-items:center; justify-content:center;"><i class="fas fa-icons" aria-hidden="true"></i></button>
                    <button type="button" class="button wppoppop-fa-clear-btn" data-target="#prop-form-icon-left" title="<?php esc_attr_e('Clear icon', 'wppoppop'); ?><?php esc_html_e('" aria-label="', 'wppoppop'); ?><?php esc_attr_e('Clear left icon', 'wppoppop'); ?>" style="min-width:30px; height:30px; padding:0;">&times;</button>
                    <input type="text" id="prop-form-icon-left" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="iconLeft" placeholder="fas fa-envelope" style="font-size:11px; min-width:0;" />
                </div>
                <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin:8px 0 4px;"><?php esc_html_e('OFFSET (PX)', 'wppoppop'); ?></label>
                <input type="number" id="prop-form-icon-left-offset" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="iconLeftOffset" min="0" max="40" value="10" style="font-size:12px;" />
                <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin:8px 0 4px;"><?php esc_html_e('ICON COLOR', 'wppoppop'); ?></label>
                <div style="display:flex; gap:4px; align-items:center;">
                    <input type="color" id="prop-form-icon-left-color" class="wppoppop-gp-bind" data-prop="iconLeftColor" value="#64748b" style="flex:1; height:30px; padding:2px; background:#090d16; border:1px solid #1e293b; border-radius:4px; cursor:pointer; min-width:0;" />
                    <button type="button" class="button wppoppop-icon-color-reset" data-target="#prop-form-icon-left-color" title="<?php esc_attr_e('Reset color', 'wppoppop'); ?><?php esc_html_e('" aria-label="', 'wppoppop'); ?><?php esc_attr_e('Reset left icon color', 'wppoppop'); ?>" style="height:30px; min-width:30px; padding:0; font-size:11px;">&#8635;</button>
                </div>
            </div>
            <div>
                <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;"><?php esc_html_e('RIGHT ICON', 'wppoppop'); ?></label>
                <div style="display:flex; gap:4px; align-items:center;">
                    <button type="button" class="button wppoppop-fa-pick-btn" data-target="#prop-form-icon-right" title="<?php esc_attr_e('Choose icon', 'wppoppop'); ?><?php esc_html_e('" aria-label="', 'wppoppop'); ?><?php esc_attr_e('Choose right icon', 'wppoppop'); ?>" style="min-width:36px; height:30px; padding:0; display:inline-flex; align-items:center; justify-content:center;"><i class="fas fa-icons" aria-hidden="true"></i></button>
                    <button type="button" class="button wppoppop-fa-clear-btn" data-target="#prop-form-icon-right" title="<?php esc_attr_e('Clear icon', 'wppoppop'); ?><?php esc_html_e('" aria-label="', 'wppoppop'); ?><?php esc_attr_e('Clear right icon', 'wppoppop'); ?>" style="min-width:30px; height:30px; padding:0;">&times;</button>
                    <input type="text" id="prop-form-icon-right" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="iconRight" placeholder="fas fa-check" style="font-size:11px; min-width:0;" />
                </div>
                <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin:8px 0 4px;"><?php esc_html_e('OFFSET (PX)', 'wppoppop'); ?></label>
                <input type="number" id="prop-form-icon-right-offset" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="iconRightOffset" min="0" max="40" value="10" style="font-size:12px;" />
                <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin:8px 0 4px;"><?php esc_html_e('ICON COLOR', 'wppoppop'); ?></label>
                <div style="display:flex; gap:4px; align-items:center;">
                    <input type="color" id="prop-form-icon-right-color" class="wppoppop-gp-bind" data-prop="iconRightColor" value="#64748b" style="flex:1; height:30px; padding:2px; background:#090d16; border:1px solid #1e293b; border-radius:4px; cursor:pointer; min-width:0;" />
                    <button type="button" class="button wppoppop-icon-color-reset" data-target="#prop-form-icon-right-color" title="<?php esc_attr_e('Reset color', 'wppoppop'); ?><?php esc_html_e('" aria-label="', 'wppoppop'); ?><?php esc_attr_e('Reset right icon color', 'wppoppop'); ?>" style="height:30px; min-width:30px; padding:0; font-size:11px;">&#8635;</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- B. OPACITY & VISIBILITY -->
<div class="wppoppop-insp-section" id="insp-section-opacity" style="background:#0f172a; border:1px solid #1e293b; border-radius:8px; padding:12px; margin-bottom:12px;">
    <div style="font-size:10px; font-weight:800; color:#38bdf8; text-transform:uppercase; margin-bottom:8px;"><?php esc_html_e('OPACITY & VISIBILITY', 'wppoppop'); ?></div>
    <div style="display:flex; align-items:center; gap:10px;">
        <input type="range" id="prop-opacity" class="wppoppop-gp-bind" data-prop="opacity" min="0" max="100" value="100" style="flex:1;" />
        <span id="prop-opacity-val" style="font-size:12px; font-weight:700; color:#38bdf8; min-width:38px; text-align:right;">100%</span>
    </div>
</div>

<!-- C. BACKGROUND COLOR (layer box) -->
<div class="wppoppop-insp-section" id="insp-section-bg-color" style="background:#0f172a; border:1px solid #1e293b; border-radius:8px; padding:12px; margin-bottom:12px;">
    <div style="font-size:10px; font-weight:800; color:#38bdf8; text-transform:uppercase; margin-bottom:8px;"><?php esc_html_e('BACKGROUND COLOR', 'wppoppop'); ?></div>
    <div style="display:flex; align-items:center; gap:10px;">
        <input type="color" id="prop-bg-color" class="widefat wppoppop-gp-bind" data-prop="bgColor" value="#3b82f6" style="height:34px; padding:2px; background:#090d16; border:1px solid #1e293b; border-radius:6px; cursor:pointer; flex:1;" />
        <label style="display:inline-flex; align-items:center; gap:5px; font-size:11px; color:#cbd5e1; cursor:pointer; white-space:nowrap;">
            <input type="checkbox" id="prop-bg-transparent" class="wppoppop-gp-bind" data-prop="transparentFill" value="1" /> <?php esc_html_e('Transparent', 'wppoppop'); ?>
        </label>
    </div>
</div>

<!-- D. TYPOGRAPHY & TEXT SPACING -->
<div class="wppoppop-insp-section" id="insp-section-typography" style="background:#0f172a; border:1px solid #1e293b; border-radius:8px; padding:12px; margin-bottom:12px;">
    <div style="font-size:10px; font-weight:800; color:#38bdf8; text-transform:uppercase; margin-bottom:8px;"><?php esc_html_e('TYPOGRAPHY & SPACING', 'wppoppop'); ?></div>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:8px;">
        <div>
            <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;"><?php esc_html_e('FONT SIZE (PX)', 'wppoppop'); ?></label>
            <input type="number" id="prop-font-size" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="fontSize" min="8" max="120" value="16" style="font-size:12px;" />
        </div>
        <div>
            <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;"><?php esc_html_e('FONT WEIGHT', 'wppoppop'); ?></label>
            <select id="prop-font-weight" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="fontWeight" style="font-size:12px;">
                <option value="400"><?php esc_html_e('Regular (400)', 'wppoppop'); ?></option>
                <option value="500"><?php esc_html_e('Medium (500)', 'wppoppop'); ?></option>
                <option value="600"><?php esc_html_e('Semi-Bold (600)', 'wppoppop'); ?></option>
                <option value="700"><?php esc_html_e('Bold (700)', 'wppoppop'); ?></option>
                <option value="800"><?php esc_html_e('Extra-Bold (800)', 'wppoppop'); ?></option>
            </select>
        </div>
    </div>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:8px;">
        <div>
            <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;"><?php esc_html_e('LINE HEIGHT', 'wppoppop'); ?></label>
            <input type="number" id="prop-line-height" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="lineHeight" step="0.1" min="0.8" max="3" value="1.4" style="font-size:12px;" />
        </div>
        <div>
            <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;"><?php esc_html_e('LETTER SPACING (PX)', 'wppoppop'); ?></label>
            <input type="number" id="prop-letter-spacing" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="letterSpacing" step="0.5" min="-5" max="20" value="0" style="font-size:12px;" />
        </div>
    </div>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
        <div>
            <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;"><?php esc_html_e('TEXT COLOR', 'wppoppop'); ?></label>
            <input type="color" id="prop-color" class="widefat wppoppop-gp-bind" data-prop="color" value="#ffffff" style="height:34px; padding:2px; background:#090d16; border:1px solid #1e293b; border-radius:6px; cursor:pointer;" />
        </div>
        <div>
            <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;"><?php esc_html_e('FOREGROUND COLOR', 'wppoppop'); ?></label>
            <div style="display:flex; gap:4px; align-items:center;">
                <input type="color" id="prop-fg-color" class="widefat wppoppop-gp-bind" data-prop="fgColor" value="#64748b" title="<?php esc_attr_e('Icons & accents', 'wppoppop'); ?>" style="flex:1; height:34px; padding:2px; background:#090d16; border:1px solid #1e293b; border-radius:6px; cursor:pointer; min-width:0;" />
                <button type="button" id="prop-fg-color-reset" class="button" title="<?php esc_attr_e('Reset foreground color', 'wppoppop'); ?><?php esc_html_e('" aria-label="', 'wppoppop'); ?><?php esc_attr_e('Reset foreground color', 'wppoppop'); ?>" style="height:34px; min-width:30px; padding:0; font-size:11px;">&#8635;</button>
            </div>
        </div>
    </div>
</div>

<!-- E. 4-WAY DIRECTIONAL PADDING -->
<div class="wppoppop-insp-section" id="insp-section-padding" style="background:#0f172a; border:1px solid #1e293b; border-radius:8px; padding:12px; margin-bottom:12px;">
    <div style="font-size:10px; font-weight:800; color:#38bdf8; text-transform:uppercase; margin-bottom:8px;"><?php esc_html_e('PADDING (TOP / RIGHT / BOTTOM / LEFT PX)', 'wppoppop'); ?></div>
    <div style="display:grid; grid-template-columns:1fr 1fr 1fr 1fr; gap:6px;">
        <div>
            <label style="display:block; font-size:9px; color:#94a3b8; text-align:center;"><?php esc_html_e('TOP', 'wppoppop'); ?></label>
            <input type="number" id="prop-pad-top" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="paddingTop" min="0" max="200" value="0" style="font-size:11px; text-align:center;" />
        </div>
        <div>
            <label style="display:block; font-size:9px; color:#94a3b8; text-align:center;"><?php esc_html_e('RIGHT', 'wppoppop'); ?></label>
            <input type="number" id="prop-pad-right" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="paddingRight" min="0" max="200" value="0" style="font-size:11px; text-align:center;" />
        </div>
        <div>
            <label style="display:block; font-size:9px; color:#94a3b8; text-align:center;"><?php esc_html_e('BOTTOM', 'wppoppop'); ?></label>
            <input type="number" id="prop-pad-bottom" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="paddingBottom" min="0" max="200" value="0" style="font-size:11px; text-align:center;" />
        </div>
        <div>
            <label style="display:block; font-size:9px; color:#94a3b8; text-align:center;"><?php esc_html_e('LEFT', 'wppoppop'); ?></label>
            <input type="number" id="prop-pad-left" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="paddingLeft" min="0" max="200" value="0" style="font-size:11px; text-align:center;" />
        </div>
    </div>
</div>

<!-- F. BORDER & CORNER RADIUS -->
<div class="wppoppop-insp-section" id="insp-section-border" style="background:#0f172a; border:1px solid #1e293b; border-radius:8px; padding:12px; margin-bottom:12px;">
    <div class="wppoppop-sec-head"><span class="wppoppop-sec-title"><?php esc_html_e('BORDER & CORNER RADIUS', 'wppoppop'); ?></span><label class="wppoppop-insp-switch" title="<?php esc_attr_e('Enable / Disable', 'wppoppop'); ?>"><input type="checkbox" id="prop-border-enabled" data-toggle-prop="borderEnabled" checked><span class="wppoppop-insp-slider"></span></label></div><div class="wppoppop-sec-body">
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:8px;">
        <div>
            <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;"><?php esc_html_e('WIDTH (PX)', 'wppoppop'); ?></label>
            <input type="number" id="prop-border-width" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="borderWidth" min="0" max="40" value="0" style="font-size:12px;" />
        </div>
        <div>
            <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;"><?php esc_html_e('STYLE', 'wppoppop'); ?></label>
            <select id="prop-border-style" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="borderStyle" style="font-size:12px;">
                <option value="solid"><?php esc_html_e('Solid', 'wppoppop'); ?></option>
                <option value="dashed"><?php esc_html_e('Dashed', 'wppoppop'); ?></option>
                <option value="dotted"><?php esc_html_e('Dotted', 'wppoppop'); ?></option>
                <option value="double"><?php esc_html_e('Double', 'wppoppop'); ?></option>
                <option value="none"><?php esc_html_e('None', 'wppoppop'); ?></option>
            </select>
        </div>
    </div>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
        <div>
            <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;"><?php esc_html_e('BORDER COLOR', 'wppoppop'); ?></label>
            <input type="color" id="prop-border-color" class="widefat wppoppop-gp-bind" data-prop="borderColor" value="#334155" style="height:34px; padding:2px; background:#090d16; border:1px solid #1e293b; border-radius:6px; cursor:pointer;" />
        </div>
        <div>
            <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;"><?php esc_html_e('RADIUS (PX)', 'wppoppop'); ?></label>
            <input type="number" id="prop-border-radius" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="borderRadius" min="0" max="200" value="0" style="font-size:12px;" />
        </div>
    </div>
</div>
</div>

<!-- G. BOX SHADOW -->
<div class="wppoppop-insp-section" id="insp-section-shadow" style="background:#0f172a; border:1px solid #1e293b; border-radius:8px; padding:12px; margin-bottom:12px;">
    <div class="wppoppop-sec-head"><span class="wppoppop-sec-title"><?php esc_html_e('BOX SHADOW', 'wppoppop'); ?></span><label class="wppoppop-insp-switch" title="<?php esc_attr_e('Enable / Disable', 'wppoppop'); ?>"><input type="checkbox" id="prop-shadow-enabled" data-toggle-prop="shadowEnabled" checked><span class="wppoppop-insp-slider"></span></label></div><div class="wppoppop-sec-body">
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:8px;">
        <div>
            <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;"><?php esc_html_e('OFFSET X (PX)', 'wppoppop'); ?></label>
            <input type="number" id="prop-shadow-x" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="shadowX" min="-50" max="50" value="0" style="font-size:12px;" />
        </div>
        <div>
            <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;"><?php esc_html_e('OFFSET Y (PX)', 'wppoppop'); ?></label>
            <input type="number" id="prop-shadow-y" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="shadowY" min="-50" max="50" value="0" style="font-size:12px;" />
        </div>
    </div>
    <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:6px;">
        <div>
            <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;"><?php esc_html_e('BLUR', 'wppoppop'); ?></label>
            <input type="number" id="prop-shadow-blur" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="shadowBlur" min="0" max="100" value="0" style="font-size:12px;" />
        </div>
        <div>
            <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;"><?php esc_html_e('SPREAD', 'wppoppop'); ?></label>
            <input type="number" id="prop-shadow-spread" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="shadowSpread" min="-50" max="50" value="0" style="font-size:12px;" />
        </div>
        <div>
            <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;"><?php esc_html_e('COLOR', 'wppoppop'); ?></label>
            <input type="color" id="prop-shadow-color" class="widefat wppoppop-gp-bind" data-prop="shadowColor" value="#000000" style="height:34px; padding:2px; background:#090d16; border:1px solid #1e293b; border-radius:6px; cursor:pointer;" />
        </div>
    </div>
</div>
</div>

<!-- H. BUTTON HOVER STATE (Buttons & Submit) -->
<div class="wppoppop-insp-section" id="prop-btn-hover-section" style="display:none; background:#0f172a; border:1px solid #1e293b; border-radius:8px; padding:12px; margin-bottom:12px;">
    <div style="font-size:10px; font-weight:800; color:#38bdf8; text-transform:uppercase; margin-bottom:8px;"><?php esc_html_e('BUTTON HOVER STATE', 'wppoppop'); ?></div>
    <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:8px;">
        <div>
            <label style="display:block; font-size:10px; color:#94a3b8; margin-bottom:4px;"><?php esc_html_e('Hover Bg', 'wppoppop'); ?></label>
            <input type="color" id="prop-elem-hover-bg" class="wppoppop-gp-bind" data-prop="hoverBgColor" value="#0284c7" style="width:100%; height:32px; border:1px solid #334155; border-radius:4px; background:#090d16; cursor:pointer;" />
        </div>
        <div>
            <label style="display:block; font-size:10px; color:#94a3b8; margin-bottom:4px;"><?php esc_html_e('Hover Text', 'wppoppop'); ?></label>
            <input type="color" id="prop-elem-hover-color" class="wppoppop-gp-bind" data-prop="hoverTextColor" value="#ffffff" style="width:100%; height:32px; border:1px solid #334155; border-radius:4px; background:#090d16; cursor:pointer;" />
        </div>
        <div>
            <label style="display:block; font-size:10px; color:#94a3b8; margin-bottom:4px;"><?php esc_html_e('Hover Border', 'wppoppop'); ?></label>
            <input type="color" id="prop-elem-hover-border" class="wppoppop-gp-bind" data-prop="hoverBorderColor" value="#0284c7" style="width:100%; height:32px; border:1px solid #334155; border-radius:4px; background:#090d16; cursor:pointer;" />
        </div>
    </div>
</div>

<!-- I. CUSTOM CSS CLASS & INLINE CSS STYLES REPEATER (+ Add a style) -->
<div class="wppoppop-insp-section" id="insp-section-custom-css" style="background:#0f172a; border:1px solid #1e293b; border-radius:8px; padding:12px;">
    <div style="font-size:10px; font-weight:800; color:#38bdf8; text-transform:uppercase; margin-bottom:8px;"><?php esc_html_e('CUSTOM CSS & INLINE STYLES', 'wppoppop'); ?></div>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:10px;">
        <div>
            <label for="prop-custom-class" style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;"><?php esc_html_e('CUSTOM CSS CLASS', 'wppoppop'); ?></label>
            <input type="text" id="prop-custom-class" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="customClass" placeholder="<?php esc_attr_e('e.g. my-custom-layer', 'wppoppop'); ?>" style="font-size:12px;" />
            <input type="hidden" id="prop-form-custom-class" class="wppoppop-gp-bind" data-prop="customClass" />
        </div>
        <div>
            <label for="prop-custom-id" style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;"><?php esc_html_e('CUSTOM HTML ID', 'wppoppop'); ?></label>
            <input type="text" id="prop-custom-id" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="customId" placeholder="<?php esc_attr_e('e.g. custom-layer-id', 'wppoppop'); ?>" style="font-size:12px;" />
        </div>
    </div>
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:8px;">
        <span style="font-size:10px; font-weight:700; color:#94a3b8; text-transform:uppercase;"><?php esc_html_e('CSS STYLES REPEATER', 'wppoppop'); ?></span>
        <button type="button" id="wppoppop-btn-add-inline-style" class="button button-small" style="font-size:11px; font-weight:700;"><?php esc_html_e('+ Add a style', 'wppoppop'); ?></button>
    </div>
    <div id="wppoppop-gp-inline-styles-list" style="display:flex; flex-direction:column; gap:6px;"></div>
</div>

<?php
$wppoppop_fa_picker = __DIR__ . '/tab-style-icon-picker.php';
if ( file_exists( $wppoppop_fa_picker ) ) { include_once $wppoppop_fa_picker; }
?>
