<?php
if (!defined('ABSPATH')) exit;
/**
 * Decomposed Module: Media & Action Family Contextual Style Controls
 * Scoped per element via WpPopPopBuilderInspector (Elements 1-9 & 26)
 */
?>
<div id="wppoppop-decomposed-style-media-Extras" class="wppoppop-decomposed-module" style="display:none; margin-top:10px;">
    <!-- Contextual Hover State Card (Strictly Elements 9 Button & 26 Submit) -->
    <div class="wppoppop-insp-card wppoppop-media-style-scope" data-style-elements="button,link,link_btn,step,step_btn,submit,submit_btn" style="display:none; background:#1e293b; border:1px solid #334155; border-radius:6px; padding:12px; margin-bottom:12px;">
        <div style="font-size:11px; font-weight:700; text-transform:uppercase; color:#38bdf8; margin-bottom:10px; letter-spacing:0.5px;">
            <span class="dashicons dashicons-admin-customizer" style="font-size:14px; width:14px; height:14px; margin-right:4px;"></span>
            Button Hover State
        </div>
        <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:8px;">
            <div>
                <label style="display:block; font-size:11px; color:#94a3b8; margin-bottom:4px;">Hover Bg</label>
                <input type="color" id="prop-elem-hover-bg" class="wppoppop-gp-bind" data-prop="hoverBgColor" data-elem-prop="hoverBgColor" value="#0284c7" style="width:100%; height:30px; border:1px solid #475569; border-radius:4px; background:#0f172a; cursor:pointer;">
            </div>
            <div>
                <label style="display:block; font-size:11px; color:#94a3b8; margin-bottom:4px;">Hover Text</label>
                <input type="color" id="prop-elem-hover-color" class="wppoppop-gp-bind" data-prop="hoverTextColor" data-elem-prop="hoverTextColor" value="#ffffff" style="width:100%; height:30px; border:1px solid #475569; border-radius:4px; background:#0f172a; cursor:pointer;">
            </div>
            <div>
                <label style="display:block; font-size:11px; color:#94a3b8; margin-bottom:4px;">Hover Border</label>
                <input type="color" id="prop-elem-hover-border" class="wppoppop-gp-bind" data-prop="hoverBorderColor" data-elem-prop="hoverBorderColor" value="#0284c7" style="width:100%; height:30px; border:1px solid #475569; border-radius:4px; background:#0f172a; cursor:pointer;">
            </div>
        </div>
    </div>

    <!-- Contextual Close / Icon Hover Color (Strictly Elements 6 Close & 7 Icon) -->
    <div class="wppoppop-insp-card wppoppop-media-style-scope" data-style-elements="close,close_icon,icon,fontawesome" style="display:none; background:#1e293b; border:1px solid #334155; border-radius:6px; padding:12px; margin-bottom:12px;">
        <div style="font-size:11px; font-weight:700; text-transform:uppercase; color:#38bdf8; margin-bottom:8px;">Glyph Hover &amp; Badge Style</div>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
            <div>
                <label style="display:block; font-size:11px; color:#94a3b8; margin-bottom:4px;">Hover Glyph Color</label>
                <input type="color" id="prop-glyph-hover-color" class="wppoppop-gp-bind" data-prop="hoverColor" data-elem-prop="hoverColor" value="#ef4444" style="width:100%; height:30px; border:1px solid #475569; border-radius:4px; background:#0f172a; cursor:pointer;">
            </div>
            <div>
                <label style="display:block; font-size:11px; color:#94a3b8; margin-bottom:4px;">Rotation (0°–360°)</label>
                <input type="number" id="prop-glyph-rotate" class="wppoppop-gp-bind" data-prop="rotate" data-elem-prop="shapeRotate" min="0" max="360" placeholder="0" style="width:100%; background:#0f172a; border:1px solid #475569; color:#f8fafc; border-radius:4px; padding:5px 8px; font-size:12px;">
            </div>
        </div>
    </div>
</div>
