<?php
if (!defined('ABSPATH')) exit;
?>
<!-- WPPOPPOP UNIFIED STYLE TAB SUITE -->
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
        <strong style="color:#38bdf8; display:block; margin-bottom:2px;">INPUT STYLING</strong>
        Base input styling is inherited from <strong>Popup Settings &raquo; Style &raquo; Inputs</strong>. Configure per-layer alignment, left/right icons, and CSS overrides below.
    </div>
    <div class="wppoppop-insp-card" style="background:#0f172a; border:1px solid #1e293b; border-radius:8px; padding:12px; margin-bottom:12px;">
        <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:6px; text-transform:uppercase;">TEXT ALIGNMENT</label>
        <div id="wppoppop-form-align-group" style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:6px;">
            <button type="button" class="button wppoppop-form-align-btn" data-align="left" style="font-size:11px; font-weight:700;">Left</button>
            <button type="button" class="button wppoppop-form-align-btn" data-align="center" style="font-size:11px; font-weight:700;">Center</button>
            <button type="button" class="button wppoppop-form-align-btn" data-align="right" style="font-size:11px; font-weight:700;">Right</button>
        </div>
    </div>
    <div id="wppoppop-form-icons-card" class="wppoppop-insp-card" style="background:#0f172a; border:1px solid #1e293b; border-radius:8px; padding:12px; margin-bottom:12px;">
        <div style="font-size:10px; font-weight:800; color:#38bdf8; text-transform:uppercase; margin-bottom:8px;">INPUT ICONS &amp; OFFSETS</div>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
            <div>
                <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;">LEFT ICON</label>
                <div style="display:flex; gap:4px; align-items:center;">
                    <button type="button" class="button wppoppop-fa-pick-btn" data-target="#prop-form-icon-left" title="Choose icon" aria-label="Choose left icon" style="min-width:36px; height:30px; padding:0; display:inline-flex; align-items:center; justify-content:center;"><i class="fas fa-icons" aria-hidden="true"></i></button>
                    <button type="button" class="button wppoppop-fa-clear-btn" data-target="#prop-form-icon-left" title="Clear icon" aria-label="Clear left icon" style="min-width:30px; height:30px; padding:0;">&times;</button>
                    <input type="text" id="prop-form-icon-left" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="iconLeft" placeholder="fas fa-envelope" style="font-size:11px; min-width:0;" />
                </div>
                <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin:8px 0 4px;">OFFSET (PX)</label>
                <input type="number" id="prop-form-icon-left-offset" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="iconLeftOffset" min="0" max="40" value="10" style="font-size:12px;" />
                <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin:8px 0 4px;">ICON COLOR</label>
                <div style="display:flex; gap:4px; align-items:center;">
                    <input type="color" id="prop-form-icon-left-color" class="wppoppop-gp-bind" data-prop="iconLeftColor" value="#64748b" style="flex:1; height:30px; padding:2px; background:#090d16; border:1px solid #1e293b; border-radius:4px; cursor:pointer; min-width:0;" />
                    <button type="button" class="button wppoppop-icon-color-reset" data-target="#prop-form-icon-left-color" title="Reset color" aria-label="Reset left icon color" style="height:30px; min-width:30px; padding:0; font-size:11px;">&#8635;</button>
                </div>
            </div>
            <div>
                <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;">RIGHT ICON</label>
                <div style="display:flex; gap:4px; align-items:center;">
                    <button type="button" class="button wppoppop-fa-pick-btn" data-target="#prop-form-icon-right" title="Choose icon" aria-label="Choose right icon" style="min-width:36px; height:30px; padding:0; display:inline-flex; align-items:center; justify-content:center;"><i class="fas fa-icons" aria-hidden="true"></i></button>
                    <button type="button" class="button wppoppop-fa-clear-btn" data-target="#prop-form-icon-right" title="Clear icon" aria-label="Clear right icon" style="min-width:30px; height:30px; padding:0;">&times;</button>
                    <input type="text" id="prop-form-icon-right" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="iconRight" placeholder="fas fa-check" style="font-size:11px; min-width:0;" />
                </div>
                <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin:8px 0 4px;">OFFSET (PX)</label>
                <input type="number" id="prop-form-icon-right-offset" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="iconRightOffset" min="0" max="40" value="10" style="font-size:12px;" />
                <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin:8px 0 4px;">ICON COLOR</label>
                <div style="display:flex; gap:4px; align-items:center;">
                    <input type="color" id="prop-form-icon-right-color" class="wppoppop-gp-bind" data-prop="iconRightColor" value="#64748b" style="flex:1; height:30px; padding:2px; background:#090d16; border:1px solid #1e293b; border-radius:4px; cursor:pointer; min-width:0;" />
                    <button type="button" class="button wppoppop-icon-color-reset" data-target="#prop-form-icon-right-color" title="Reset color" aria-label="Reset right icon color" style="height:30px; min-width:30px; padding:0; font-size:11px;">&#8635;</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- B. OPACITY & VISIBILITY -->
<div class="wppoppop-insp-section" id="insp-section-opacity" style="background:#0f172a; border:1px solid #1e293b; border-radius:8px; padding:12px; margin-bottom:12px;">
    <div style="font-size:10px; font-weight:800; color:#38bdf8; text-transform:uppercase; margin-bottom:8px;">OPACITY &amp; VISIBILITY</div>
    <div style="display:flex; align-items:center; gap:10px;">
        <input type="range" id="prop-opacity" class="wppoppop-gp-bind" data-prop="opacity" min="0" max="100" value="100" style="flex:1;" />
        <span id="prop-opacity-val" style="font-size:12px; font-weight:700; color:#38bdf8; min-width:38px; text-align:right;">100%</span>
    </div>
</div>

<!-- C. BACKGROUND COLOR (layer box) -->
<div class="wppoppop-insp-section" id="insp-section-bg-color" style="background:#0f172a; border:1px solid #1e293b; border-radius:8px; padding:12px; margin-bottom:12px;">
    <div style="font-size:10px; font-weight:800; color:#38bdf8; text-transform:uppercase; margin-bottom:8px;">BACKGROUND COLOR</div>
    <div style="display:flex; align-items:center; gap:10px;">
        <input type="color" id="prop-bg-color" class="widefat wppoppop-gp-bind" data-prop="bgColor" value="#3b82f6" style="height:34px; padding:2px; background:#090d16; border:1px solid #1e293b; border-radius:6px; cursor:pointer; flex:1;" />
        <label style="display:inline-flex; align-items:center; gap:5px; font-size:11px; color:#cbd5e1; cursor:pointer; white-space:nowrap;">
            <input type="checkbox" id="prop-bg-transparent" class="wppoppop-gp-bind" data-prop="transparentFill" value="1" /> Transparent
        </label>
    </div>
</div>

<!-- D. TYPOGRAPHY & TEXT SPACING -->
<div class="wppoppop-insp-section" id="insp-section-typography" style="background:#0f172a; border:1px solid #1e293b; border-radius:8px; padding:12px; margin-bottom:12px;">
    <div style="font-size:10px; font-weight:800; color:#38bdf8; text-transform:uppercase; margin-bottom:8px;">TYPOGRAPHY &amp; SPACING</div>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:8px;">
        <div>
            <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;">FONT SIZE (PX)</label>
            <input type="number" id="prop-font-size" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="fontSize" min="8" max="120" value="16" style="font-size:12px;" />
        </div>
        <div>
            <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;">FONT WEIGHT</label>
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
            <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;">LINE HEIGHT</label>
            <input type="number" id="prop-line-height" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="lineHeight" step="0.1" min="0.8" max="3" value="1.4" style="font-size:12px;" />
        </div>
        <div>
            <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;">LETTER SPACING (PX)</label>
            <input type="number" id="prop-letter-spacing" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="letterSpacing" step="0.5" min="-5" max="20" value="0" style="font-size:12px;" />
        </div>
    </div>
    <div>
        <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;">TEXT / FOREGROUND COLOR</label>
        <input type="color" id="prop-color" class="widefat wppoppop-gp-bind" data-prop="color" value="#ffffff" style="height:34px; padding:2px; background:#090d16; border:1px solid #1e293b; border-radius:6px; cursor:pointer;" />
    </div>
</div>

<!-- E. 4-WAY DIRECTIONAL PADDING -->
<div class="wppoppop-insp-section" id="insp-section-padding" style="background:#0f172a; border:1px solid #1e293b; border-radius:8px; padding:12px; margin-bottom:12px;">
    <div style="font-size:10px; font-weight:800; color:#38bdf8; text-transform:uppercase; margin-bottom:8px;">PADDING (TOP / RIGHT / BOTTOM / LEFT PX)</div>
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

<!-- F. BORDER & CORNER RADIUS -->
<div class="wppoppop-insp-section" id="insp-section-border" style="background:#0f172a; border:1px solid #1e293b; border-radius:8px; padding:12px; margin-bottom:12px;">
    <div style="font-size:10px; font-weight:800; color:#38bdf8; text-transform:uppercase; margin-bottom:8px;">BORDER &amp; CORNER RADIUS</div>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:8px;">
        <div>
            <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;">WIDTH (PX)</label>
            <input type="number" id="prop-border-width" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="borderWidth" min="0" max="40" value="0" style="font-size:12px;" />
        </div>
        <div>
            <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;">STYLE</label>
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
            <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;">BORDER COLOR</label>
            <input type="color" id="prop-border-color" class="widefat wppoppop-gp-bind" data-prop="borderColor" value="#334155" style="height:34px; padding:2px; background:#090d16; border:1px solid #1e293b; border-radius:6px; cursor:pointer;" />
        </div>
        <div>
            <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;">RADIUS (PX)</label>
            <input type="number" id="prop-border-radius" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="borderRadius" min="0" max="200" value="4" style="font-size:12px;" />
        </div>
    </div>
</div>

<!-- G. BOX SHADOW -->
<div class="wppoppop-insp-section" id="insp-section-shadow" style="background:#0f172a; border:1px solid #1e293b; border-radius:8px; padding:12px; margin-bottom:12px;">
    <div style="font-size:10px; font-weight:800; color:#38bdf8; text-transform:uppercase; margin-bottom:8px;">BOX SHADOW</div>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:8px;">
        <div>
            <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;">OFFSET X (PX)</label>
            <input type="number" id="prop-shadow-x" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="shadowX" min="-50" max="50" value="0" style="font-size:12px;" />
        </div>
        <div>
            <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;">OFFSET Y (PX)</label>
            <input type="number" id="prop-shadow-y" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="shadowY" min="-50" max="50" value="0" style="font-size:12px;" />
        </div>
    </div>
    <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:6px;">
        <div>
            <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;">BLUR</label>
            <input type="number" id="prop-shadow-blur" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="shadowBlur" min="0" max="100" value="0" style="font-size:12px;" />
        </div>
        <div>
            <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;">SPREAD</label>
            <input type="number" id="prop-shadow-spread" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="shadowSpread" min="-50" max="50" value="0" style="font-size:12px;" />
        </div>
        <div>
            <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;">COLOR</label>
            <input type="color" id="prop-shadow-color" class="widefat wppoppop-gp-bind" data-prop="shadowColor" value="#000000" style="height:34px; padding:2px; background:#090d16; border:1px solid #1e293b; border-radius:6px; cursor:pointer;" />
        </div>
    </div>
</div>

<!-- H. BUTTON HOVER STATE (Buttons & Submit) -->
<div class="wppoppop-insp-section" id="prop-btn-hover-section" style="display:none; background:#0f172a; border:1px solid #1e293b; border-radius:8px; padding:12px; margin-bottom:12px;">
    <div style="font-size:10px; font-weight:800; color:#38bdf8; text-transform:uppercase; margin-bottom:8px;">BUTTON HOVER STATE</div>
    <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:8px;">
        <div>
            <label style="display:block; font-size:10px; color:#94a3b8; margin-bottom:4px;">Hover Bg</label>
            <input type="color" id="prop-elem-hover-bg" class="wppoppop-gp-bind" data-prop="hoverBgColor" value="#0284c7" style="width:100%; height:32px; border:1px solid #334155; border-radius:4px; background:#090d16; cursor:pointer;" />
        </div>
        <div>
            <label style="display:block; font-size:10px; color:#94a3b8; margin-bottom:4px;">Hover Text</label>
            <input type="color" id="prop-elem-hover-color" class="wppoppop-gp-bind" data-prop="hoverTextColor" value="#ffffff" style="width:100%; height:32px; border:1px solid #334155; border-radius:4px; background:#090d16; cursor:pointer;" />
        </div>
        <div>
            <label style="display:block; font-size:10px; color:#94a3b8; margin-bottom:4px;">Hover Border</label>
            <input type="color" id="prop-elem-hover-border" class="wppoppop-gp-bind" data-prop="hoverBorderColor" value="#0284c7" style="width:100%; height:32px; border:1px solid #334155; border-radius:4px; background:#090d16; cursor:pointer;" />
        </div>
    </div>
</div>

<!-- I. CUSTOM CSS CLASS & INLINE CSS STYLES REPEATER (+ Add a style) -->
<div class="wppoppop-insp-section" id="insp-section-custom-css" style="background:#0f172a; border:1px solid #1e293b; border-radius:8px; padding:12px;">
    <div style="font-size:10px; font-weight:800; color:#38bdf8; text-transform:uppercase; margin-bottom:8px;">CUSTOM CSS &amp; INLINE STYLES</div>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:10px;">
        <div>
            <label for="prop-custom-class" style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;">CUSTOM CSS CLASS</label>
            <input type="text" id="prop-custom-class" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="customClass" placeholder="e.g. my-custom-layer" style="font-size:12px;" />
            <input type="hidden" id="prop-form-custom-class" class="wppoppop-gp-bind" data-prop="customClass" />
        </div>
        <div>
            <label for="prop-custom-id" style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;">CUSTOM HTML ID</label>
            <input type="text" id="prop-custom-id" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="customId" placeholder="e.g. custom-layer-id" style="font-size:12px;" />
        </div>
    </div>
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:8px;">
        <span style="font-size:10px; font-weight:700; color:#94a3b8; text-transform:uppercase;">CSS STYLES REPEATER</span>
        <button type="button" id="wppoppop-btn-add-inline-style" class="button button-small" style="font-size:11px; font-weight:700;">+ Add a style</button>
    </div>
    <div id="wppoppop-gp-inline-styles-list" style="display:flex; flex-direction:column; gap:6px;"></div>
</div>

<?php
$wppoppop_fa_picker = __DIR__ . '/tab-style-icon-picker.php';
if ( file_exists( $wppoppop_fa_picker ) ) { include_once $wppoppop_fa_picker; }
?>
