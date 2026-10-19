/**
 * WpPopPop Builder Registry Module: registry-inspector
 * Split from builder-elements-registry.js (load order matters; see class-admin-assets.php)
 */
/* === WPPOPPOP_MASTER_LAYER_INSPECTOR_AND_LAYER_ENGINE_START === */
(function(window, $) {
    'use strict';

    var FALLBACK_STYLE_HTML = "<!-- WPPOPPOP UNIFIED STYLE TAB SUITE -->\n<!-- A. FORM INPUT STYLE MODULE (Form Family 10-25) -->\n<div id=\"wppoppop-form-style-module\" class=\"wppoppop-insp-section\" style=\"display:none; margin-bottom:14px;\">\n    <div style=\"background:rgba(56,189,248,0.12); border:1px solid rgba(56,189,248,0.35); border-radius:6px; padding:10px 12px; margin-bottom:12px; font-size:11px; color:#bae6fd; line-height:1.45;\">\n        <strong style=\"color:#38bdf8; display:block; margin-bottom:2px;\">INPUT STYLING</strong>\n        Base input styling is inherited from <strong>Popup Settings &raquo; Style &raquo; Inputs</strong>. Configure per-layer alignment, left/right icons, and CSS overrides below.\n    </div>\n    <div class=\"wppoppop-insp-card\" style=\"background:#0f172a; border:1px solid #1e293b; border-radius:8px; padding:12px; margin-bottom:12px;\">\n        <label style=\"display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:6px; text-transform:uppercase;\">TEXT ALIGNMENT</label>\n        <div id=\"wppoppop-form-align-group\" style=\"display:grid; grid-template-columns:1fr 1fr 1fr; gap:6px;\">\n            <button type=\"button\" class=\"button wppoppop-form-align-btn active\" data-align=\"left\" style=\"font-size:11px; font-weight:700;\">Left</button>\n            <button type=\"button\" class=\"button wppoppop-form-align-btn\" data-align=\"center\" style=\"font-size:11px; font-weight:700;\">Center</button>\n            <button type=\"button\" class=\"button wppoppop-form-align-btn\" data-align=\"right\" style=\"font-size:11px; font-weight:700;\">Right</button>\n        </div>\n    </div>\n    <div id=\"wppoppop-form-icons-card\" class=\"wppoppop-insp-card\" style=\"background:#0f172a; border:1px solid #1e293b; border-radius:8px; padding:12px; margin-bottom:12px;\">\n        <div style=\"font-size:10px; font-weight:800; color:#38bdf8; text-transform:uppercase; margin-bottom:8px;\">INPUT ICONS &amp; OFFSETS</div>\n        <div style=\"display:grid; grid-template-columns:2fr 1fr; gap:8px; margin-bottom:8px;\">\n            <div>\n                <label style=\"display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;\">LEFT ICON</label>\n                <input type=\"text\" id=\"prop-form-icon-left\" class=\"widefat wppoppop-input wppoppop-gp-bind\" data-prop=\"iconLeft\" placeholder=\"\u2709 or dashicons-email\" style=\"font-size:12px;\" />\n            </div>\n            <div>\n                <label style=\"display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;\">OFFSET (PX)</label>\n                <input type=\"number\" id=\"prop-form-icon-left-offset\" class=\"widefat wppoppop-input wppoppop-gp-bind\" data-prop=\"iconLeftOffset\" min=\"0\" max=\"40\" value=\"10\" style=\"font-size:12px;\" />\n            </div>\n        </div>\n        <div style=\"display:flex; gap:4px; flex-wrap:wrap; margin-bottom:10px;\">\n            <button type=\"button\" class=\"button button-small wppoppop-form-icon-preset\" data-target=\"#prop-form-icon-left\" data-glyph=\"\u2709\">\u2709 Email</button>\n            <button type=\"button\" class=\"button button-small wppoppop-form-icon-preset\" data-target=\"#prop-form-icon-left\" data-glyph=\"\ud83d\udc64\">\ud83d\udc64 User</button>\n            <button type=\"button\" class=\"button button-small wppoppop-form-icon-preset\" data-target=\"#prop-form-icon-left\" data-glyph=\"\ud83d\udcde\">\ud83d\udcde Phone</button>\n            <button type=\"button\" class=\"button button-small wppoppop-form-icon-preset\" data-target=\"#prop-form-icon-left\" data-glyph=\"\ud83d\udd12\">\ud83d\udd12 Lock</button>\n            <button type=\"button\" class=\"button button-small wppoppop-form-icon-preset\" data-target=\"#prop-form-icon-left\" data-glyph=\"\">\u2715 Clear</button>\n        </div>\n        <div style=\"display:grid; grid-template-columns:2fr 1fr; gap:8px;\">\n            <div>\n                <label style=\"display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;\">RIGHT ICON</label>\n                <input type=\"text\" id=\"prop-form-icon-right\" class=\"widefat wppoppop-input wppoppop-gp-bind\" data-prop=\"iconRight\" placeholder=\"\u2714 or dashicons-yes\" style=\"font-size:12px;\" />\n            </div>\n            <div>\n                <label style=\"display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;\">OFFSET (PX)</label>\n                <input type=\"number\" id=\"prop-form-icon-right-offset\" class=\"widefat wppoppop-input wppoppop-gp-bind\" data-prop=\"iconRightOffset\" min=\"0\" max=\"40\" value=\"10\" style=\"font-size:12px;\" />\n            </div>\n        </div>\n    </div>\n</div>\n\n<!-- B. OPACITY & VISIBILITY -->\n<div class=\"wppoppop-insp-section\" id=\"insp-section-opacity\" style=\"background:#0f172a; border:1px solid #1e293b; border-radius:8px; padding:12px; margin-bottom:12px;\">\n    <div style=\"font-size:10px; font-weight:800; color:#38bdf8; text-transform:uppercase; margin-bottom:8px;\">OPACITY &amp; VISIBILITY</div>\n    <div style=\"display:flex; align-items:center; gap:10px;\">\n        <input type=\"range\" id=\"prop-opacity\" class=\"wppoppop-gp-bind\" data-prop=\"opacity\" min=\"0\" max=\"100\" value=\"100\" style=\"flex:1;\" />\n        <span id=\"prop-opacity-val\" style=\"font-size:12px; font-weight:700; color:#38bdf8; min-width:38px; text-align:right;\">100%</span>\n    </div>\n</div>\n\n<!-- C. BACKGROUND / FILL COLOR -->\n<div class=\"wppoppop-insp-section\" id=\"insp-section-bg-color\" style=\"background:#0f172a; border:1px solid #1e293b; border-radius:8px; padding:12px; margin-bottom:12px;\">\n    <div style=\"font-size:10px; font-weight:800; color:#38bdf8; text-transform:uppercase; margin-bottom:8px;\">BACKGROUND / FILL COLOR</div>\n    <div style=\"display:flex; align-items:center; gap:10px;\">\n        <input type=\"color\" id=\"prop-bg-color\" class=\"widefat wppoppop-gp-bind\" data-prop=\"bgColor\" value=\"#3b82f6\" style=\"height:34px; padding:2px; background:#090d16; border:1px solid #1e293b; border-radius:6px; cursor:pointer; flex:1;\" />\n        <label style=\"display:inline-flex; align-items:center; gap:5px; font-size:11px; color:#cbd5e1; cursor:pointer; white-space:nowrap;\">\n            <input type=\"checkbox\" id=\"prop-bg-transparent\" class=\"wppoppop-gp-bind\" data-prop=\"transparentFill\" value=\"1\" /> Transparent\n        </label>\n    </div>\n</div>\n\n<!-- D. TYPOGRAPHY & TEXT SPACING -->\n<div class=\"wppoppop-insp-section\" id=\"insp-section-typography\" style=\"background:#0f172a; border:1px solid #1e293b; border-radius:8px; padding:12px; margin-bottom:12px;\">\n    <div style=\"font-size:10px; font-weight:800; color:#38bdf8; text-transform:uppercase; margin-bottom:8px;\">TYPOGRAPHY &amp; SPACING</div>\n    <div style=\"display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:8px;\">\n        <div>\n            <label style=\"display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;\">FONT SIZE (PX)</label>\n            <input type=\"number\" id=\"prop-font-size\" class=\"widefat wppoppop-input wppoppop-gp-bind\" data-prop=\"fontSize\" min=\"8\" max=\"120\" value=\"16\" style=\"font-size:12px;\" />\n        </div>\n        <div>\n            <label style=\"display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;\">FONT WEIGHT</label>\n            <select id=\"prop-font-weight\" class=\"widefat wppoppop-input wppoppop-gp-bind\" data-prop=\"fontWeight\" style=\"font-size:12px;\">\n                <option value=\"400\">Regular (400)</option>\n                <option value=\"500\">Medium (500)</option>\n                <option value=\"600\">Semi-Bold (600)</option>\n                <option value=\"700\">Bold (700)</option>\n                <option value=\"800\">Extra-Bold (800)</option>\n            </select>\n        </div>\n    </div>\n    <div style=\"display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:8px;\">\n        <div>\n            <label style=\"display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;\">LINE HEIGHT</label>\n            <input type=\"number\" id=\"prop-line-height\" class=\"widefat wppoppop-input wppoppop-gp-bind\" data-prop=\"lineHeight\" step=\"0.1\" min=\"0.8\" max=\"3\" value=\"1.4\" style=\"font-size:12px;\" />\n        </div>\n        <div>\n            <label style=\"display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;\">LETTER SPACING (PX)</label>\n            <input type=\"number\" id=\"prop-letter-spacing\" class=\"widefat wppoppop-input wppoppop-gp-bind\" data-prop=\"letterSpacing\" step=\"0.5\" min=\"-5\" max=\"20\" value=\"0\" style=\"font-size:12px;\" />\n        </div>\n    </div>\n    <div>\n        <label style=\"display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;\">TEXT / FOREGROUND COLOR</label>\n        <input type=\"color\" id=\"prop-color\" class=\"widefat wppoppop-gp-bind\" data-prop=\"color\" value=\"#ffffff\" style=\"height:34px; padding:2px; background:#090d16; border:1px solid #1e293b; border-radius:6px; cursor:pointer;\" />\n    </div>\n</div>\n\n<!-- E. 4-WAY DIRECTIONAL PADDING -->\n<div class=\"wppoppop-insp-section\" id=\"insp-section-padding\" style=\"background:#0f172a; border:1px solid #1e293b; border-radius:8px; padding:12px; margin-bottom:12px;\">\n    <div style=\"font-size:10px; font-weight:800; color:#38bdf8; text-transform:uppercase; margin-bottom:8px;\">PADDING (TOP / RIGHT / BOTTOM / LEFT PX)</div>\n    <div style=\"display:grid; grid-template-columns:1fr 1fr 1fr 1fr; gap:6px;\">\n        <div>\n            <label style=\"display:block; font-size:9px; color:#94a3b8; text-align:center;\">TOP</label>\n            <input type=\"number\" id=\"prop-pad-top\" class=\"widefat wppoppop-input wppoppop-gp-bind\" data-prop=\"paddingTop\" min=\"0\" max=\"200\" value=\"0\" style=\"font-size:11px; text-align:center;\" />\n        </div>\n        <div>\n            <label style=\"display:block; font-size:9px; color:#94a3b8; text-align:center;\">RIGHT</label>\n            <input type=\"number\" id=\"prop-pad-right\" class=\"widefat wppoppop-input wppoppop-gp-bind\" data-prop=\"paddingRight\" min=\"0\" max=\"200\" value=\"0\" style=\"font-size:11px; text-align:center;\" />\n        </div>\n        <div>\n            <label style=\"display:block; font-size:9px; color:#94a3b8; text-align:center;\">BOTTOM</label>\n            <input type=\"number\" id=\"prop-pad-bottom\" class=\"widefat wppoppop-input wppoppop-gp-bind\" data-prop=\"paddingBottom\" min=\"0\" max=\"200\" value=\"0\" style=\"font-size:11px; text-align:center;\" />\n        </div>\n        <div>\n            <label style=\"display:block; font-size:9px; color:#94a3b8; text-align:center;\">LEFT</label>\n            <input type=\"number\" id=\"prop-pad-left\" class=\"widefat wppoppop-input wppoppop-gp-bind\" data-prop=\"paddingLeft\" min=\"0\" max=\"200\" value=\"0\" style=\"font-size:11px; text-align:center;\" />\n        </div>\n    </div>\n</div>\n\n<!-- F. BORDER & CORNER RADIUS -->\n<div class=\"wppoppop-insp-section\" id=\"insp-section-border\" style=\"background:#0f172a; border:1px solid #1e293b; border-radius:8px; padding:12px; margin-bottom:12px;\">\n    <div style=\"font-size:10px; font-weight:800; color:#38bdf8; text-transform:uppercase; margin-bottom:8px;\">BORDER &amp; CORNER RADIUS</div>\n    <div style=\"display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:8px;\">\n        <div>\n            <label style=\"display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;\">WIDTH (PX)</label>\n            <input type=\"number\" id=\"prop-border-width\" class=\"widefat wppoppop-input wppoppop-gp-bind\" data-prop=\"borderWidth\" min=\"0\" max=\"40\" value=\"0\" style=\"font-size:12px;\" />\n        </div>\n        <div>\n            <label style=\"display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;\">STYLE</label>\n            <select id=\"prop-border-style\" class=\"widefat wppoppop-input wppoppop-gp-bind\" data-prop=\"borderStyle\" style=\"font-size:12px;\">\n                <option value=\"solid\">Solid</option>\n                <option value=\"dashed\">Dashed</option>\n                <option value=\"dotted\">Dotted</option>\n                <option value=\"double\">Double</option>\n                <option value=\"none\">None</option>\n            </select>\n        </div>\n    </div>\n    <div style=\"display:grid; grid-template-columns:1fr 1fr; gap:8px;\">\n        <div>\n            <label style=\"display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;\">BORDER COLOR</label>\n            <input type=\"color\" id=\"prop-border-color\" class=\"widefat wppoppop-gp-bind\" data-prop=\"borderColor\" value=\"#334155\" style=\"height:34px; padding:2px; background:#090d16; border:1px solid #1e293b; border-radius:6px; cursor:pointer;\" />\n        </div>\n        <div>\n            <label style=\"display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;\">RADIUS (PX)</label>\n            <input type=\"number\" id=\"prop-border-radius\" class=\"widefat wppoppop-input wppoppop-gp-bind\" data-prop=\"borderRadius\" min=\"0\" max=\"200\" value=\"4\" style=\"font-size:12px;\" />\n        </div>\n    </div>\n</div>\n\n<!-- G. BOX SHADOW -->\n<div class=\"wppoppop-insp-section\" id=\"insp-section-shadow\" style=\"background:#0f172a; border:1px solid #1e293b; border-radius:8px; padding:12px; margin-bottom:12px;\">\n    <div style=\"font-size:10px; font-weight:800; color:#38bdf8; text-transform:uppercase; margin-bottom:8px;\">BOX SHADOW</div>\n    <div style=\"display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:8px;\">\n        <div>\n            <label style=\"display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;\">OFFSET X (PX)</label>\n            <input type=\"number\" id=\"prop-shadow-x\" class=\"widefat wppoppop-input wppoppop-gp-bind\" data-prop=\"shadowX\" min=\"-50\" max=\"50\" value=\"0\" style=\"font-size:12px;\" />\n        </div>\n        <div>\n            <label style=\"display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;\">OFFSET Y (PX)</label>\n            <input type=\"number\" id=\"prop-shadow-y\" class=\"widefat wppoppop-input wppoppop-gp-bind\" data-prop=\"shadowY\" min=\"-50\" max=\"50\" value=\"4\" style=\"font-size:12px;\" />\n        </div>\n    </div>\n    <div style=\"display:grid; grid-template-columns:1fr 1fr 1fr; gap:6px;\">\n        <div>\n            <label style=\"display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;\">BLUR</label>\n            <input type=\"number\" id=\"prop-shadow-blur\" class=\"widefat wppoppop-input wppoppop-gp-bind\" data-prop=\"shadowBlur\" min=\"0\" max=\"100\" value=\"12\" style=\"font-size:12px;\" />\n        </div>\n        <div>\n            <label style=\"display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;\">SPREAD</label>\n            <input type=\"number\" id=\"prop-shadow-spread\" class=\"widefat wppoppop-input wppoppop-gp-bind\" data-prop=\"shadowSpread\" min=\"-50\" max=\"50\" value=\"0\" style=\"font-size:12px;\" />\n        </div>\n        <div>\n            <label style=\"display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;\">COLOR</label>\n            <input type=\"color\" id=\"prop-shadow-color\" class=\"widefat wppoppop-gp-bind\" data-prop=\"shadowColor\" value=\"#000000\" style=\"height:34px; padding:2px; background:#090d16; border:1px solid #1e293b; border-radius:6px; cursor:pointer;\" />\n        </div>\n    </div>\n</div>\n\n<!-- H. BUTTON HOVER STATE (Buttons & Submit) -->\n<div class=\"wppoppop-insp-section\" id=\"prop-btn-hover-section\" style=\"display:none; background:#0f172a; border:1px solid #1e293b; border-radius:8px; padding:12px; margin-bottom:12px;\">\n    <div style=\"font-size:10px; font-weight:800; color:#38bdf8; text-transform:uppercase; margin-bottom:8px;\">BUTTON HOVER STATE</div>\n    <div style=\"display:grid; grid-template-columns:1fr 1fr 1fr; gap:8px;\">\n        <div>\n            <label style=\"display:block; font-size:10px; color:#94a3b8; margin-bottom:4px;\">Hover Bg</label>\n            <input type=\"color\" id=\"prop-elem-hover-bg\" class=\"wppoppop-gp-bind\" data-prop=\"hoverBgColor\" value=\"#0284c7\" style=\"width:100%; height:32px; border:1px solid #334155; border-radius:4px; background:#090d16; cursor:pointer;\" />\n        </div>\n        <div>\n            <label style=\"display:block; font-size:10px; color:#94a3b8; margin-bottom:4px;\">Hover Text</label>\n            <input type=\"color\" id=\"prop-elem-hover-color\" class=\"wppoppop-gp-bind\" data-prop=\"hoverTextColor\" value=\"#ffffff\" style=\"width:100%; height:32px; border:1px solid #334155; border-radius:4px; background:#090d16; cursor:pointer;\" />\n        </div>\n        <div>\n            <label style=\"display:block; font-size:10px; color:#94a3b8; margin-bottom:4px;\">Hover Border</label>\n            <input type=\"color\" id=\"prop-elem-hover-border\" class=\"wppoppop-gp-bind\" data-prop=\"hoverBorderColor\" value=\"#0284c7\" style=\"width:100%; height:32px; border:1px solid #334155; border-radius:4px; background:#090d16; cursor:pointer;\" />\n        </div>\n    </div>\n</div>\n\n<!-- I. CUSTOM CSS CLASS & INLINE CSS STYLES REPEATER (+ Add a style) -->\n<div class=\"wppoppop-insp-section\" id=\"insp-section-custom-css\" style=\"background:#0f172a; border:1px solid #1e293b; border-radius:8px; padding:12px;\">\n    <div style=\"font-size:10px; font-weight:800; color:#38bdf8; text-transform:uppercase; margin-bottom:8px;\">CUSTOM CSS &amp; INLINE STYLES</div>\n    <label style=\"display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px;\">CUSTOM CSS CLASS</label>\n    <input type=\"text\" id=\"prop-form-custom-class\" class=\"widefat wppoppop-input wppoppop-gp-bind\" data-prop=\"customClass\" placeholder=\"e.g. my-custom-layer\" style=\"margin-bottom:10px; font-size:12px;\" />\n    <div style=\"display:flex; align-items:center; justify-content:space-between; margin-bottom:8px;\">\n        <span style=\"font-size:10px; font-weight:700; color:#94a3b8; text-transform:uppercase;\">CSS STYLES REPEATER</span>\n        <button type=\"button\" id=\"wppoppop-btn-add-inline-style\" class=\"button button-small\" style=\"font-size:11px; font-weight:700;\">+ Add a style</button>\n    </div>\n    <div id=\"wppoppop-gp-inline-styles-list\" style=\"display:flex; flex-direction:column; gap:6px;\"></div>\n</div>\n";
    var FALLBACK_LOGIC_HTML = "<!-- CONDITIONAL LOGIC & ACTION ROUTING SUITE -->\n<div class=\"wppoppop-insp-section\" id=\"insp-section-conditional-logic\" style=\"background:#0f172a; border:1px solid #1e293b; border-radius:8px; padding:12px; margin-bottom:12px;\">\n    <div style=\"font-size:10px; font-weight:800; color:#38bdf8; text-transform:uppercase; margin-bottom:10px;\">CONDITIONAL LOGIC</div>\n    <div style=\"display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;\">\n        <label for=\"prop-logic-enabled\" style=\"font-size:11px; font-weight:700; color:#e2e8f0; margin:0; cursor:pointer;\">Enable Conditional Logic</label>\n        <input type=\"checkbox\" id=\"prop-logic-enabled\" class=\"wppoppop-switch wppoppop-logic-bind\" data-logic-prop=\"logicEnabled\" />\n    </div>\n    <div id=\"wppoppop-logic-body\" style=\"display:none; margin-top:10px; border-top:1px solid #1e293b; padding-top:10px;\">\n        <div style=\"display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:10px;\">\n            <div>\n                <label style=\"display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px; text-transform:uppercase;\">Action</label>\n                <select id=\"prop-logic-action\" class=\"widefat wppoppop-input wppoppop-logic-bind\" data-logic-prop=\"logicAction\" style=\"font-size:12px;\">\n                    <option value=\"show\">Show Layer</option>\n                    <option value=\"hide\">Hide Layer</option>\n                </select>\n            </div>\n            <div>\n                <label style=\"display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px; text-transform:uppercase;\">Match Mode</label>\n                <select id=\"prop-logic-match\" class=\"widefat wppoppop-input wppoppop-logic-bind\" data-logic-prop=\"logicMatch\" style=\"font-size:12px;\">\n                    <option value=\"all\">If ALL rules match</option>\n                    <option value=\"any\">If ANY rule matches</option>\n                </select>\n            </div>\n        </div>\n        <div>\n            <div style=\"display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;\">\n                <span style=\"font-size:10px; font-weight:700; color:#94a3b8; text-transform:uppercase;\">Rules</span>\n                <button type=\"button\" id=\"btn-add-logic-rule\" class=\"button button-small\" style=\"font-size:11px; font-weight:700;\">+ Add rule</button>\n            </div>\n            <div id=\"wppoppop-logic-rules-list\" style=\"display:flex; flex-direction:column; gap:8px;\"></div>\n        </div>\n    </div>\n</div>\n\n<div class=\"wppoppop-insp-section\" id=\"insp-section-action-routing\" style=\"background:#0f172a; border:1px solid #1e293b; border-radius:8px; padding:12px;\">\n    <div style=\"font-size:10px; font-weight:800; color:#38bdf8; text-transform:uppercase; margin-bottom:10px;\">ACTION &amp; CLICK ROUTING</div>\n    <div style=\"margin-bottom:8px;\">\n        <label style=\"display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px; text-transform:uppercase;\">Click Action</label>\n        <select id=\"prop-logic-click-action\" class=\"widefat wppoppop-input wppoppop-gp-bind\" data-prop=\"buttonAction\" style=\"font-size:12px;\">\n            <option value=\"link\">Open URL Link</option>\n            <option value=\"step\">Go to Canvas Step</option>\n            <option value=\"close\">Close Popup</option>\n        </select>\n    </div>\n    <div id=\"prop-logic-link-url-row\" style=\"margin-bottom:8px;\">\n        <label style=\"display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px; text-transform:uppercase;\">Destination URL</label>\n        <input type=\"text\" id=\"prop-logic-link-url\" class=\"widefat wppoppop-input wppoppop-gp-bind\" data-prop=\"linkUrl\" placeholder=\"https://example.com\" style=\"font-size:12px;\" />\n    </div>\n    <div id=\"prop-logic-goto-canvas-row\" style=\"display:none; margin-bottom:8px;\">\n        <label style=\"display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px; text-transform:uppercase;\">Target Canvas #</label>\n        <input type=\"number\" id=\"prop-logic-goto-canvas\" class=\"widefat wppoppop-input wppoppop-gp-bind\" data-prop=\"goto_canvas\" min=\"1\" max=\"10\" value=\"2\" style=\"font-size:12px;\" />\n    </div>\n    <div>\n        <label style=\"display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px; text-transform:uppercase;\">Custom JS Callback (OnClick)</label>\n        <input type=\"text\" id=\"prop-logic-custom-js\" class=\"widefat wppoppop-input wppoppop-gp-bind\" data-prop=\"customJs\" placeholder=\"e.g. console.log('Clicked')\" style=\"font-size:12px;\" />\n    </div>\n</div>\n";
    var FALLBACK_DATA_HTML  = "<!-- DATA & VALUE BINDING SUITE -->\n<div class=\"wppoppop-insp-section\" id=\"insp-section-form-data\" style=\"background:#0f172a; border:1px solid #1e293b; border-radius:8px; padding:12px;\">\n    <div style=\"font-size:10px; font-weight:800; color:#38bdf8; text-transform:uppercase; margin-bottom:10px;\">DATA &amp; VALUE BINDING</div>\n    <div style=\"margin-bottom:10px;\">\n        <label style=\"display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px; text-transform:uppercase;\">Default Value</label>\n        <input type=\"text\" id=\"prop-default-value\" class=\"widefat wppoppop-input wppoppop-data-bind\" data-data-prop=\"defaultValue\" placeholder=\"Initial field value...\" style=\"font-size:12px;\" />\n    </div>\n    <div style=\"display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;\">\n        <label for=\"prop-dynamic-default\" style=\"font-size:11px; font-weight:700; color:#e2e8f0; margin:0; cursor:pointer;\">Dynamic Default (URL / Cookie)</label>\n        <input type=\"checkbox\" id=\"prop-dynamic-default\" class=\"wppoppop-switch wppoppop-data-bind\" data-data-prop=\"dynamicDefault\" />\n    </div>\n    <div id=\"prop-dynamic-param-row\" style=\"margin-bottom:10px;\">\n        <label style=\"display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px; text-transform:uppercase;\">URL Parameter / Cookie Name</label>\n        <input type=\"text\" id=\"prop-dynamic-param\" class=\"widefat wppoppop-input wppoppop-data-bind\" data-data-prop=\"paramName\" placeholder=\"e.g. utm_source or user_email\" style=\"font-size:12px;\" />\n    </div>\n    <div style=\"display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;\">\n        <label for=\"prop-readonly\" style=\"font-size:11px; font-weight:700; color:#e2e8f0; margin:0; cursor:pointer;\">Read Only Field</label>\n        <input type=\"checkbox\" id=\"prop-readonly\" class=\"wppoppop-switch wppoppop-data-bind\" data-data-prop=\"readOnly\" />\n    </div>\n    <div style=\"display:flex; justify-content:space-between; align-items:center;\">\n        <label for=\"prop-save-to-db\" style=\"font-size:11px; font-weight:700; color:#e2e8f0; margin:0; cursor:pointer;\">Save to Database</label>\n        <input type=\"checkbox\" id=\"prop-save-to-db\" class=\"wppoppop-switch wppoppop-data-bind\" data-data-prop=\"saveToDb\" checked />\n    </div>\n</div>\n";
    var FALLBACK_ADV_HTML   = "<!-- ADVANCED FIELD IDENTIFIER, FILTERS & VALIDATORS SUITE -->\n<div class=\"wppoppop-insp-section\" id=\"insp-section-form-advanced\" style=\"background:#0f172a; border:1px solid #1e293b; border-radius:8px; padding:12px; margin-bottom:12px;\">\n    <div style=\"font-size:10px; font-weight:800; color:#38bdf8; text-transform:uppercase; margin-bottom:10px;\">FIELD IDENTIFIER &amp; INPUT MASK</div>\n    <div style=\"margin-bottom:10px;\">\n        <label style=\"display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px; text-transform:uppercase;\">ID / Merge Token Name</label>\n        <input type=\"text\" id=\"prop-field-name\" class=\"widefat wppoppop-input wppoppop-adv-bind\" data-adv-prop=\"fieldName\" placeholder=\"e.g. email or first_name\" style=\"font-size:12px;\" />\n        <small style=\"font-size:10px; color:#64748b; display:block; margin-top:3px;\">Use as <code>{field_name}</code> in email notifications &amp; CRM webhooks.</small>\n    </div>\n    <div>\n        <label style=\"display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px; text-transform:uppercase;\">Input Mask</label>\n        <input type=\"text\" id=\"prop-input-mask\" class=\"widefat wppoppop-input wppoppop-adv-bind\" data-adv-prop=\"inputMask\" placeholder=\"e.g. (999) 999-9999\" style=\"font-size:12px;\" />\n    </div>\n</div>\n\n<div class=\"wppoppop-insp-section\" id=\"insp-section-form-filters\" style=\"background:#0f172a; border:1px solid #1e293b; border-radius:8px; padding:12px; margin-bottom:12px;\">\n    <div style=\"font-size:10px; font-weight:800; color:#38bdf8; text-transform:uppercase; margin-bottom:8px;\">SANITIZATION FILTERS</div>\n    <div style=\"display:grid; grid-template-columns:1fr 1fr; gap:8px;\">\n        <label style=\"font-size:11px; color:#cbd5e1; cursor:pointer; display:flex; align-items:center; gap:6px;\"><input type=\"checkbox\" class=\"prop-filter-chk\" value=\"trim\" /> Trim Whitespace</label>\n        <label style=\"font-size:11px; color:#cbd5e1; cursor:pointer; display:flex; align-items:center; gap:6px;\"><input type=\"checkbox\" class=\"prop-filter-chk\" value=\"striptags\" /> Strip Tags</label>\n        <label style=\"font-size:11px; color:#cbd5e1; cursor:pointer; display:flex; align-items:center; gap:6px;\"><input type=\"checkbox\" class=\"prop-filter-chk\" value=\"alpha\" /> Alpha Only</label>\n        <label style=\"font-size:11px; color:#cbd5e1; cursor:pointer; display:flex; align-items:center; gap:6px;\"><input type=\"checkbox\" class=\"prop-filter-chk\" value=\"digits\" /> Digits Only</label>\n        <label style=\"font-size:11px; color:#cbd5e1; cursor:pointer; display:flex; align-items:center; gap:6px;\"><input type=\"checkbox\" class=\"prop-filter-chk\" value=\"lowercase\" /> Lowercase</label>\n        <label style=\"font-size:11px; color:#cbd5e1; cursor:pointer; display:flex; align-items:center; gap:6px;\"><input type=\"checkbox\" class=\"prop-filter-chk\" value=\"uppercase\" /> Uppercase</label>\n    </div>\n</div>\n\n<div class=\"wppoppop-insp-section\" id=\"insp-section-form-validators\" style=\"background:#0f172a; border:1px solid #1e293b; border-radius:8px; padding:12px;\">\n    <div style=\"font-size:10px; font-weight:800; color:#38bdf8; text-transform:uppercase; margin-bottom:8px;\">VALIDATION RULES</div>\n    <div style=\"display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:10px;\">\n        <label style=\"font-size:11px; color:#cbd5e1; cursor:pointer; display:flex; align-items:center; gap:6px;\"><input type=\"checkbox\" class=\"prop-validator-chk\" value=\"required\" /> Required *</label>\n        <label style=\"font-size:11px; color:#cbd5e1; cursor:pointer; display:flex; align-items:center; gap:6px;\"><input type=\"checkbox\" class=\"prop-validator-chk\" value=\"email\" /> RFC Email</label>\n        <label style=\"font-size:11px; color:#cbd5e1; cursor:pointer; display:flex; align-items:center; gap:6px;\"><input type=\"checkbox\" class=\"prop-validator-chk\" value=\"equal\" /> Equal Match</label>\n        <label style=\"font-size:11px; color:#cbd5e1; cursor:pointer; display:flex; align-items:center; gap:6px;\"><input type=\"checkbox\" class=\"prop-validator-chk\" value=\"inarray\" /> In Array</label>\n        <label style=\"font-size:11px; color:#cbd5e1; cursor:pointer; display:flex; align-items:center; gap:6px;\"><input type=\"checkbox\" class=\"prop-validator-chk\" value=\"noduplicates\" /> No Duplicates</label>\n    </div>\n    <div>\n        <label style=\"display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px; text-transform:uppercase;\">Custom Validation Error Message</label>\n        <input type=\"text\" id=\"prop-validator-error\" class=\"widefat wppoppop-input wppoppop-adv-bind\" data-adv-prop=\"errorMessage\" placeholder=\"Please enter a valid value...\" style=\"font-size:12px;\" />\n    </div>\n</div>\n";

    var FORM_TYPES = [
        'text', 'textfield', 'email', 'number', 'spinner', 'phone', 'tel',
        'textarea', 'select', 'dropdown', 'checkbox', 'checkboxes',
        'radio', 'radios', 'multiselect', 'imageselect', 'tile',
        'date', 'time', 'upload', 'file', 'password', 'rating'
    ];

    var MEDIA_PANEL_MAP = {
        'shape': '#panel-elem-shape',
        'rect': '#panel-elem-shape',
        'rectangle': '#panel-elem-shape',
        'square': '#panel-elem-shape',
        'image': '#panel-elem-image',
        'video': '#panel-elem-video',
        'label': '#panel-elem-label',
        'title': '#panel-elem-label',
        'paragraph': '#panel-elem-label',
        'html': '#panel-elem-html',
        'close': '#panel-elem-close',
        'close_icon': '#panel-elem-close',
        'icon': '#panel-elem-icon',
        'fontawesome': '#panel-elem-icon',
        'progress': '#panel-elem-progress',
        'button': '#panel-elem-button',
        'link': '#panel-elem-button',
        'link_btn': '#panel-elem-button',
        'step': '#panel-elem-button',
        'step_btn': '#panel-elem-button',
        'submit': '#panel-elem-submit',
        'submit_btn': '#panel-elem-submit',
        'text': '#panel-elem-text',
        'textfield': '#panel-elem-text',
        'email': '#panel-elem-email',
        'number': '#panel-elem-number',
        'spinner': '#panel-elem-number',
        'phone': '#panel-elem-phone',
        'tel': '#panel-elem-phone',
        'textarea': '#panel-elem-textarea',
        'select': '#panel-elem-select',
        'dropdown': '#panel-elem-select',
        'checkbox': '#panel-elem-checkbox',
        'checkboxes': '#panel-elem-checkbox',
        'radio': '#panel-elem-radio',
        'radios': '#panel-elem-radio',
        'multiselect': '#panel-elem-multiselect',
        'imageselect': '#panel-elem-imageselect',
        'tile': '#panel-elem-tile',
        'date': '#panel-elem-date',
        'time': '#panel-elem-time',
        'upload': '#panel-elem-upload',
        'file': '#panel-elem-upload',
        'password': '#panel-elem-password',
        'rating': '#panel-elem-rating'
    };

    function normalizeId(raw) {
        if (raw === undefined || raw === null) return '';
        return String(raw)
            .replace(/^wppoppop-layer-item-/, '')
            .replace(/^wppoppop-layer-/, '')
            .replace(/^layer-/, '')
            .replace(/^wppoppop-el-/, '')
            .replace(/^el-/, '')
            .trim();
    }

    function extractAllCanvasElements() {
        var results = [];
        // 1. Try WpPopPopBuilderCanvas.getActiveElements()
        if (window.WpPopPopBuilderCanvas && typeof window.WpPopPopBuilderCanvas.getActiveElements === 'function') {
            try {
                var activeList = window.WpPopPopBuilderCanvas.getActiveElements();
                if (Array.isArray(activeList)) {
                    results = results.concat(activeList);
                } else if (activeList && typeof activeList === 'object') {
                    Object.keys(activeList).forEach(function(k) {
                        if (activeList[k] && typeof activeList[k] === 'object') results.push(activeList[k]);
                    });
                }
            } catch (e) {}
        }

        // 2. Try WpPopPopBuilderCore.state.canvases across current and all canvas keys
        var core = window.WpPopPopBuilderCore;
        if (core && core.state && core.state.canvases) {
            var canvases = core.state.canvases;
            var curKey = core.state.currentCanvas || 1;
            var keysToCheck = [curKey].concat(Object.keys(canvases));
            keysToCheck.forEach(function(ck) {
                var cData = canvases[ck];
                if (!cData) return;
                var pool = Array.isArray(cData) ? cData : (cData.elements ? cData.elements : cData);
                if (Array.isArray(pool)) {
                    results = results.concat(pool);
                } else if (pool && typeof pool === 'object') {
                    Object.keys(pool).forEach(function(ek) {
                        if (pool[ek] && typeof pool[ek] === 'object' && (pool[ek].id || pool[ek].type)) {
                            if (!pool[ek].id) pool[ek].id = ek;
                            results.push(pool[ek]);
                        }
                    });
                }
            });
        }
        return results;
    }

    function resolveElement(idOrObj) {
        if (idOrObj && typeof idOrObj === 'object' && (idOrObj.id || idOrObj.type)) return idOrObj;
        var insp = window.WpPopPopBuilderInspector;
        var rawId = (idOrObj !== undefined && idOrObj !== null && typeof idOrObj !== 'object')
            ? String(idOrObj)
            : (insp && (insp.activeElementId || insp.activeId)
                ? String(insp.activeElementId || insp.activeId)
                : (window.WpPopPopBuilderCore && window.WpPopPopBuilderCore.state ? String(window.WpPopPopBuilderCore.state.activeId || '') : ''));
        var cleanId = normalizeId(rawId);
        if (!cleanId) return null;

        var all = extractAllCanvasElements();
        for (var i = 0; i < all.length; i++) {
            var item = all[i];
            if (!item) continue;
            if (String(item.id) === rawId || normalizeId(item.id) === cleanId) {
                return item;
            }
        }

        // DOM Fallback so resolveElement NEVER returns null when a layer or stage node exists
        var $stageNode = $('#wppoppop-el-' + cleanId + ', #el-' + cleanId + ', .wppoppop-canvas-item[data-id="' + rawId + '"], .wppoppop-canvas-item[data-id="' + cleanId + '"]').first();
        var $layerNode = $('.wppoppop-layer-item[data-id="' + rawId + '"], .wppoppop-layer-item[data-id="' + cleanId + '"]').first();
        if ($stageNode.length || $layerNode.length) {
            var inferredType = ($stageNode.attr('data-type') || $layerNode.attr('data-type') || 'label').toLowerCase();
            var fallbackObj = {
                id: $stageNode.attr('data-id') || $layerNode.attr('data-id') || cleanId,
                type: inferredType,
                name: $layerNode.find('.wppoppop-layer-title, .wppoppop-layer-name').text().trim() || inferredType.toUpperCase(),
                left: parseInt($stageNode.css('left'), 10) || 20,
                top: parseInt($stageNode.css('top'), 10) || 20,
                width: $stageNode.outerWidth() || 180,
                height: $stageNode.outerHeight() || 44
            };
            return fallbackObj;
        }
        return null;
    }

    // Guarantee all 5 tab panes exist inside the scroll body and are NEVER empty
    function ensureFiveTabsDom() {
        var $drawer = $('#wppoppop-inspector-drawer');
        if (!$drawer.length) return;

        // Ensure #wppoppop-inspector-drawer is strictly inside .wppoppop-builder-workspace and 360px wide
        var $ws = $('.wppoppop-builder-workspace, #wppoppop-builder-workspace').first();
        if ($ws.length && $drawer.parent()[0] !== $ws[0]) {
            $ws.append($drawer);
        }
        $drawer.css({
            position: 'absolute',
            top: '0px',
            right: '0px',
            bottom: '0px',
            left: 'auto',
            width: '360px',
            maxWidth: '360px',
            height: '100%'
        });

        var $body = $drawer.find('#wppoppop-inspector-body, .wppoppop-inspector-body').first();
        if (!$body.length) {
            $body = $('<div id="wppoppop-inspector-body" class="wppoppop-inspector-body" style="flex:1 1 auto;overflow-y:auto;overflow-x:hidden;padding:16px;box-sizing:border-box;width:100%;"></div>');
            $drawer.append($body);
        } else {
            $body.attr('id', 'wppoppop-inspector-body').addClass('wppoppop-inspector-body');
        }

        // Deduplicate any duplicate tab panes and move canonical panes inside #wppoppop-inspector-body
        var paneSpecs = [
            { id: 'insp-tab-basic', fallback: null },
            { id: 'insp-tab-style', fallback: FALLBACK_STYLE_HTML },
            { id: 'insp-tab-logic', fallback: FALLBACK_LOGIC_HTML },
            { id: 'insp-tab-data', fallback: FALLBACK_DATA_HTML },
            { id: 'insp-tab-advanced', fallback: FALLBACK_ADV_HTML }
        ];

        for (var i = 0; i < paneSpecs.length; i++) {
            var spec = paneSpecs[i];
            var $panes = $('[id="' + spec.id + '"]');
            if ($panes.length > 1) {
                $panes.slice(1).remove();
            }
            var $pane = $('#' + spec.id);
            if (!$pane.length) {
                $pane = $('<div id="' + spec.id + '" class="wppoppop-tab-pane wppoppop-insp-content" style="display:none;"></div>');
                $body.append($pane);
            } else if ($pane.parent()[0] !== $body[0]) {
                $body.append($pane);
            }

            // If Style, Logic, Data, or Advanced pane is empty, inject authoritative markup
            if (spec.fallback && $pane.children().length === 0) {
                $pane.html(spec.fallback);
            }
        }

        // Keep Coordinates & Bounds and Animation strictly inside #insp-tab-basic
        var $basic = $('#insp-tab-basic');
        $('#insp-section-coords, #insp-section-animation, #insp-section-gp-basic-shared').each(function() {
            if ($basic.length && !$.contains($basic[0], this)) {
                $basic.append(this);
            }
        });

        // Ensure all 5 tab buttons exist in the tab bar
        var $tabsBar = $('#wppoppop-inspector-tabs-bar, .wppoppop-inspector-tabs').first();
        if ($tabsBar.length) {
            if (!$tabsBar.find('[data-tab="data"]').length) {
                $tabsBar.append('<button type="button" class="wppoppop-insp-tab wppoppop-form-only-tab" data-tab="data" style="display:none;">Data</button>');
            }
            if (!$tabsBar.find('[data-tab="advanced"]').length) {
                $tabsBar.append('<button type="button" class="wppoppop-insp-tab wppoppop-form-only-tab" data-tab="advanced" style="display:none;">Advanced</button>');
            }
        }
    }

    function executeSwitchTab(tabName) {
        ensureFiveTabsDom();
        var target = String(tabName || 'basic').toLowerCase().trim();
        if (target === 'content') target = 'basic';

        var $tabsBar = $('#wppoppop-inspector-tabs-bar, .wppoppop-inspector-tabs');
        $tabsBar.find('.wppoppop-insp-tab, [data-tab]').removeClass('active');
        $tabsBar.find('[data-tab="' + target + '"]').addClass('active');

        var allTabs = ['basic', 'style', 'logic', 'data', 'advanced'];
        for (var i = 0; i < allTabs.length; i++) {
            var pane = document.getElementById('insp-tab-' + allTabs[i]);
            if (pane) {
                if (allTabs[i] === target) {
                    $(pane).addClass('active');
                    pane.style.setProperty('display', 'block', 'important');
                    // Ensure direct section cards inside active tab are visible unless explicitly scoped off
                    $(pane).children('.wppoppop-insp-section').not('#wppoppop-form-style-module, #prop-btn-hover-section').show();
                } else {
                    $(pane).removeClass('active');
                    pane.style.setProperty('display', 'none', 'important');
                }
            }
        }
    }

    function renderInlineStylesRepeater(el) {
        var $list = $('#wppoppop-gp-inline-styles-list');
        if (!$list.length) return;
        $list.empty();
        var styles = Array.isArray(el.inlineStyles) ? el.inlineStyles : [];
        for (var i = 0; i < styles.length; i++) {
            var r = styles[i] || {};
            var $row = $(
                '<div class="wppoppop-inline-style-row" style="display:grid;grid-template-columns:1fr 1fr auto;gap:6px;align-items:center;">' +
                    '<input type="text" class="wppoppop-input wppoppop-style-prop" placeholder="property (e.g. border-color)" style="font-size:11px;" />' +
                    '<input type="text" class="wppoppop-input wppoppop-style-val" placeholder="value (e.g. #38bdf8)" style="font-size:11px;" />' +
                    '<button type="button" class="button button-small wppoppop-remove-inline-style" style="color:#f87171;">&times;</button>' +
                '</div>'
            );
            $row.find('.wppoppop-style-prop').val(r.prop || '');
            $row.find('.wppoppop-style-val').val(r.val || '');
            $list.append($row);
        }
    }

    /* WPPOPPOP_LOGIC_SCOPE: current-stage, form-field-only, de-duplicated candidates */
    function getLogicFieldCandidates(selfEl) {
        var LOGIC_TYPES = ['text','textfield','email','number','spinner','phone','tel','textarea','select','dropdown','checkbox','checkboxes','radio','radios','multiselect','imageselect','tile','date','time','upload','file','password','rating'];
        var pool = [];
        var core = window.WpPopPopBuilderCore;
        if (core && core.state && core.state.canvases) {
            var cur = core.state.currentCanvas || 1;
            var cData = core.state.canvases[cur];
            if (cData) pool = Array.isArray(cData) ? cData : (Array.isArray(cData.elements) ? cData.elements : []);
        }
        if (!pool.length && window.WpPopPopBuilderCanvas && typeof window.WpPopPopBuilderCanvas.getActiveElements === 'function') {
            try { var a = window.WpPopPopBuilderCanvas.getActiveElements(); if (Array.isArray(a)) pool = a; } catch (e) {}
        }
        var selfId = normalizeId(selfEl && selfEl.id);
        var seen = {}, out = [];
        for (var i = 0; i < pool.length; i++) {
            var f = pool[i];
            if (!f || !f.id) continue;
            var fid = normalizeId(f.id);
            if (!fid || fid === selfId || seen[fid]) continue;
            if (LOGIC_TYPES.indexOf(String(f.type || '').toLowerCase()) === -1) continue;
            seen[fid] = true;
            out.push(f);
        }
        return out;
    }


    function renderLogicRulesList(el) {
        var $list = $('#wppoppop-logic-rules-list');
        if (!$list.length) return;
        $list.empty();
        var rules = Array.isArray(el.logicRules) ? el.logicRules : [];
        var allEls = getLogicFieldCandidates(el);
        var cleanSelf = normalizeId(el.id);

        if (!rules.length) {
            $list.append('<div style="font-size:11px; color:#64748b; font-style:italic;">No rules yet. Click "+ Add rule" to add a condition.</div>');
            return;
        }

        for (var i = 0; i < rules.length; i++) {
            var r = rules[i] || {};
            var fieldOpts = '';
            for (var j = 0; j < allEls.length; j++) {
                var f = allEls[j];
                if (!f || normalizeId(f.id) === cleanSelf) continue;
                var fId = normalizeId(f.id);
                var usedElsewhere = false;
                for (var u = 0; u < rules.length; u++) { if (u !== i && rules[u] && normalizeId(rules[u].field) === fId) { usedElsewhere = true; break; } }
                if (usedElsewhere) continue;
                var fName = f.fieldName || f.name || f.label || f.placeholder || (String(f.type).toUpperCase() + ' #' + fId);
                var sel = (fId === normalizeId(r.field)) ? ' selected' : '';
                fieldOpts += '<option value="' + fId + '"' + sel + '>' + $('<div>').text(fName).html() + '</option>';
            }

            var ops = [
                { k: 'equal', l: 'Equals (==)' },
                { k: 'not_equal', l: 'Not Equal (!=)' },
                { k: 'contains', l: 'Contains' },
                { k: 'not_contains', l: 'Does Not Contain' },
                { k: 'greater', l: 'Greater (>)' },
                { k: 'less', l: 'Less (<)' },
                { k: 'empty', l: 'Is Empty' },
                { k: 'not_empty', l: 'Is Not Empty' }
            ];
            var opHtml = ops.map(function(o) {
                return '<option value="' + o.k + '"' + (o.k === (r.operator || 'equal') ? ' selected' : '') + '>' + o.l + '</option>';
            }).join('');

            var $row = $(
                '<div class="wppoppop-logic-rule-row" style="background:#090d16; border:1px solid #1e293b; border-radius:6px; padding:8px; display:grid; grid-template-columns:1fr 1fr; gap:6px;">' +
                    '<select class="widefat wppoppop-input rule-field" style="font-size:11px; grid-column:span 2;"><option value="">-- Select Field --</option>' + fieldOpts + '</select>' +
                    '<select class="widefat wppoppop-input rule-operator" style="font-size:11px;">' + opHtml + '</select>' +
                    '<div style="display:flex; gap:4px;">' +
                        '<input type="text" class="widefat wppoppop-input rule-value" placeholder="Value..." style="flex:1; font-size:11px;" />' +
                        '<button type="button" class="button button-small btn-remove-logic-rule" style="color:#ef4444; padding:0 6px;">&times;</button>' +
                    '</div>' +
                '</div>'
            );
            $row.find('.rule-value').val(r.value || '');
            $list.append($row);
        }
    }

    function hydrateAllFiveTabs(el) {
        ensureFiveTabsDom();
        if (!el) return;

        var type = String(el.type || 'label').toLowerCase().trim();
        var isForm = FORM_TYPES.indexOf(type) !== -1;
        var isButton = ['button', 'link', 'link_btn', 'step', 'step_btn', 'submit', 'submit_btn'].indexOf(type) !== -1;

        // 1. 3-Tab (Media/Action) vs 5-Tab (Form) Routing
        var $tabsBar = $('#wppoppop-inspector-tabs-bar, .wppoppop-inspector-tabs');
        $tabsBar.find('[data-tab="data"], [data-tab="advanced"]').each(function() {
            this.style.setProperty('display', isForm ? 'inline-block' : 'none', 'important');
        });

        var curTab = $tabsBar.find('.wppoppop-insp-tab.active').attr('data-tab') || 'basic';
        if (!isForm && (curTab === 'data' || curTab === 'advanced')) {
            curTab = 'basic';
        }
        executeSwitchTab(curTab);

        // 2. CONTENT TAB (#insp-tab-basic)
        $('#prop-layer-name, #prop-element-name').val(el.name || el.label || type.toUpperCase());
        $('#prop-pos-top, #prop-top').val(parseInt(el.top, 10) || 0);
        $('#prop-pos-left, #prop-left').val(parseInt(el.left, 10) || 0);
        $('#prop-size-width, #prop-width').val(parseInt(el.width, 10) || 160);
        $('#prop-size-height, #prop-height').val(parseInt(el.height, 10) || 44);
        $('#prop-required').prop('checked', !!el.required);

        var targetPanel = MEDIA_PANEL_MAP[type];
        if (targetPanel && $(targetPanel).length) {
            $('[id$="-legacy"]').hide();
            $('.element-panel, .wppoppop-panel-specific').hide();
            $(targetPanel).show();
            $(targetPanel).find('.wppoppop-gp-bind').each(function() {
                var $inp = $(this);
                var p = $inp.data('prop');
                if (!p) return;
                var v = el[p];
                if (v === undefined) {
                    if (p === 'content') v = el.label || el.titleText || el.text || '';
                    if (p === 'placeholder') v = el.label || '';
                    if (p === 'optionsList') v = el.options || el.selectOptions || '';
                }
                if ($inp.is(':checkbox')) $inp.prop('checked', !!v);
                else if (v !== undefined && v !== null) $inp.val(v);
            });
        }

        if ((type === 'phone' || type === 'tel') && !el.autocomplete) { /* WPPOPPOP_PHONE_AC_DEFAULT */
            el.autocomplete = 'tel';
            $('#prop-phone-autocomplete').val('tel');
        }
        if (type === 'checkbox' || type === 'checkboxes') { $('#prop-checkbox-layout').val(el.layout || 'vertical'); } /* WPPOPPOP_CHECKBOX_LAYOUT */
        if (type === 'radio' || type === 'radios') { $('#prop-radio-layout').val(el.layout || 'vertical'); } /* WPPOPPOP_RADIO_LAYOUT */
        if ((type === 'checkbox' || type === 'checkboxes') && !el.bgColor) { /* WPPOPPOP_CB_BG */
            $('#prop-bg-color').val('#ffffff');
            $('#prop-bg-transparent').prop('checked', true);
        }
        if ((type === 'radio' || type === 'radios') && !el.bgColor) { /* WPPOPPOP_RADIO_BG */
            $('#prop-bg-color').val('#ffffff');
            $('#prop-bg-transparent').prop('checked', true);
        }
        if (type === 'imageselect') { /* WPPOPPOP_IMAGESELECT_HYDRATE */
            var isOpts = el.optionsList || el.options || '';
            if (Array.isArray(isOpts)) { isOpts = isOpts.join('\n'); }
            $('#prop-imageselect-options').val(isOpts);
            if (window.__wppImgSelRebuild) { window.__wppImgSelRebuild(true); } /* WPPOPPOP_IMGSEL_REBUILD */
            $('#prop-imageselect-cols').val(String(parseInt(el.gridCols || el.columns, 10) || 3));
            $('#prop-imageselect-accent').val(el.activeColor || '#38bdf8');
            $('#prop-imageselect-multi').prop('checked', el.multiSelect === true || el.multiSelect === 1 || el.multiSelect === '1' || el.multiSelect === 'true');
        }
        if (type === 'tile' && !el.bgColor) { /* WPPOPPOP_TILE_BG_DEFAULT */
            $('#prop-bg-color').val('#f1f5f9');
            $('#prop-bg-transparent').prop('checked', false);
        }
        if (type === 'tile') { /* WPPOPPOP_TILE_HYDRATE */
            var tlOpts = el.optionsList || el.options || '';
            if (Array.isArray(tlOpts)) { tlOpts = tlOpts.join('\n'); }
            $('#prop-tile-options').val(tlOpts);
            $('#prop-tile-cols').val(String(parseInt(el.gridCols || el.columns, 10) || 3));
            $('#prop-tile-accent').val(el.activeColor || '#38bdf8');
            $('#prop-tile-multi').prop('checked', el.multiSelect === true || el.multiSelect === 1 || el.multiSelect === '1' || el.multiSelect === 'true');
        }
        if (type === 'shape') {
            $('#prop-shape-fill').val(el.shapeFill || '#3b82f6');
            $('#prop-shape-transparent').prop('checked', !!el.shapeNoFill);
        }

        // 3. STYLE TAB (#insp-tab-style)
        $('#wppoppop-form-style-module').toggle(isForm);
        $('#prop-btn-hover-section').toggle(isButton);
        $('#insp-section-opacity, #insp-section-bg-color, #insp-section-typography, #insp-section-padding, #insp-section-border, #insp-section-shadow, #insp-section-custom-css').show();
        $('#insp-section-typography').toggle(['shape', 'rect', 'rectangle', 'square'].indexOf(type) === -1);
        $('#insp-tab-style').toggleClass('wppoppop-rd-style', ['radio', 'radios', 'multiselect', 'imageselect'].indexOf(String(type).toLowerCase()) !== -1); /* WPPOPPOP_RD_STYLE */
        $('#insp-tab-style').toggleClass('wppoppop-tl-style', String(type).toLowerCase() === 'tile'); /* WPPOPPOP_TL_STYLE */
        $('#insp-tab-style').toggleClass('wppoppop-cb-style', ['checkbox', 'checkboxes'].indexOf(String(type).toLowerCase()) !== -1); /* WPPOPPOP_CB_STYLE */
        $('#insp-tab-style').toggleClass('wppoppop-tf-style-lean', ['text', 'textfield', 'email', 'number', 'phone', 'tel', 'textarea', 'select', 'dropdown'].indexOf(String(type).toLowerCase()) !== -1); /* WPPOPPOP_TF_STYLE_LEAN */
        $('#insp-tab-style').toggleClass('wppoppop-date-style', ['date', 'time'].indexOf(String(type).toLowerCase()) !== -1); /* WPPOPPOP_DATE_STYLE + WPPOPPOP_TIME_STYLE */

        var op = (el.opacity !== undefined && el.opacity !== null && el.opacity !== '') ? parseFloat(el.opacity) : 100;
        if (op <= 1 && op > 0) op = Math.round(op * 100);
        $('#prop-opacity').val(op);
        $('#prop-opacity-val').text(op + '%');

        $('#prop-bg-color').val((el.bgColor && el.bgColor !== 'transparent') ? el.bgColor : '#3b82f6');
        $('#prop-bg-transparent').prop('checked', !!el.transparentFill || el.bgColor === 'transparent' || (type === 'shape' && !el.bgColor));
        $('#prop-font-size').val(parseInt(el.fontSize, 10) || ((type === 'checkbox' || type === 'checkboxes' || type === 'radio' || type === 'radios' || type === 'tile') ? 12 : 16));
        $('#prop-font-weight').val(el.fontWeight || '400');
        $('#prop-line-height').val(el.lineHeight || 1.4);
        $('#prop-letter-spacing').val(el.letterSpacing || 0);
        $('#prop-color').val(el.color || '#ffffff');

        $('#prop-pad-top').val(parseInt(el.paddingTop, 10) || 0);
        $('#prop-pad-right').val(parseInt(el.paddingRight, 10) || 0);
        $('#prop-pad-bottom').val(parseInt(el.paddingBottom, 10) || 0);
        $('#prop-pad-left').val(parseInt(el.paddingLeft, 10) || 0);

        $('#prop-border-width').val(parseInt(el.borderWidth, 10) || 0);
        $('#prop-border-style').val(el.borderStyle || 'solid');
        $('#prop-border-color').val(el.borderColor || '#334155');
        $('#prop-border-radius').val(parseInt(el.borderRadius, 10) || 4);

        $('#prop-shadow-x').val(parseInt(el.shadowX, 10) || 0);
        $('#prop-shadow-y').val(parseInt(el.shadowY, 10) || 0);
        $('#prop-shadow-blur').val(parseInt(el.shadowBlur, 10) || 0);
        $('#prop-shadow-spread').val(parseInt(el.shadowSpread, 10) || 0);
        $('#prop-shadow-color').val(el.shadowColor || '#000000');

        $('#prop-elem-hover-bg').val(el.hoverBgColor || '#0284c7');
        $('#prop-elem-hover-color').val(el.hoverTextColor || '#ffffff');
        $('#prop-elem-hover-border').val(el.hoverBorderColor || '#0284c7');

        var align = el.textAlign || 'left';
        $('#wppoppop-form-align-group .wppoppop-form-align-btn')
            .removeClass('active button-primary')
            .removeClass('active is-active button-primary');
        $('#prop-form-icon-left').val(el.iconLeft || '');
        $('#prop-form-icon-left-offset').val(el.iconLeftOffset !== undefined ? el.iconLeftOffset : 10);
        $('#prop-form-icon-right').val(el.iconRight || '');
        $('#prop-form-icon-right-offset').val(el.iconRightOffset !== undefined ? el.iconRightOffset : 10);
        $('#prop-custom-class, #prop-form-custom-class').val(el.customClass || el.custom_css_class || '');
        $('#prop-custom-id').val(el.customId || el.custom_css_id || '');
        renderInlineStylesRepeater(el);

        // 4. LOGIC TAB (#insp-tab-logic)
        $('#prop-logic-enabled').prop('checked', !!el.logicEnabled);
        $('#wppoppop-logic-body').toggle(!!el.logicEnabled);
        $('#prop-logic-action').val(el.logicAction || 'show');
        $('#prop-logic-match').val(el.logicMatch || 'all');
        renderLogicRulesList(el);
        $('#prop-logic-click-action').val(el.buttonAction || 'link');
        $('#prop-logic-link-url').val(el.linkUrl || el.url || '');
        $('#prop-logic-goto-canvas').val(el.goto_canvas || 2);
        $('#prop-logic-custom-js').val(el.customJs || '');

        // 5. DATA TAB (#insp-tab-data)
        $('#prop-default-value').val(el.defaultValue || '');
        $('#prop-dynamic-default').prop('checked', !!el.dynamicDefault);
        $('#prop-dynamic-param').val(el.paramName || el.dynamicParam || '');
        $('#prop-readonly').prop('checked', !!el.readOnly);
        $('#prop-save-to-db').prop('checked', el.saveToDb !== false);

        // 6. ADVANCED TAB (#insp-tab-advanced)
        $('#prop-field-name').val(el.fieldName || el.fieldId || '');
        $('#prop-input-mask').val(el.inputMask || '');
        $('#prop-validator-error').val(el.errorMessage || '');
        /* WPPOPPOP_ADV_TAB: hydrate */
        $('#prop-validator-equal').val(el.validatorEqual || '');
        $('#prop-validator-list').val(el.validatorList || '');
        var advV = Array.isArray(el.validators) ? el.validators : [];
        $('#wppoppop-val-extra-equal').toggle(advV.indexOf('equal') !== -1);
        $('#wppoppop-val-extra-inarray').toggle(advV.indexOf('inarray') !== -1);
        /* WPPOPPOP_REQ_MSG: hydrate */
        $('#insp-row-gp-required').toggle(isForm);
        $('#prop-gp-required').prop('checked', !!el.required);
        $('#prop-gp-required-msg').val(el.requiredMessage || '');
        $('#insp-row-gp-required-msg').toggle(isForm && !!el.required);

        var filters = Array.isArray(el.filters) ? el.filters : [];
        $('.prop-filter-chk').each(function() {
            $(this).prop('checked', filters.indexOf($(this).val()) !== -1);
        });

        var validators = Array.isArray(el.validators) ? el.validators : [];
        if (el.required && validators.indexOf('required') === -1) validators.push('required');
        $('.prop-validator-chk').each(function() {
            $(this).prop('checked', validators.indexOf($(this).val()) !== -1);
        });
    }

    // =========================================================================
    // AUTHORITATIVE LAYER SELECTION DISPATCHER (CALLED BY CAPTURE & BUBBLE)
    // =========================================================================
    var _selectingGuard = false;
    window.selectLayerEverywhere = function(idOrObj) {
        if (_selectingGuard) return;
        var el = resolveElement(idOrObj);
        if (!el) return;

        _selectingGuard = true;
        try {
            var actualId = String(el.id);
            var cleanId = normalizeId(actualId);

            // 1. Update Core & Canvas state
            if (window.WpPopPopBuilderCore && window.WpPopPopBuilderCore.state) {
                window.WpPopPopBuilderCore.state.activeId = actualId;
            }
            if (window.WpPopPopBuilderCanvas) {
                window.WpPopPopBuilderCanvas.selectedId = actualId;
            }

            // 2. Highlight in Floating Layers Panel
            $('#wppoppop-layers-list .wppoppop-layer-item, #wppoppop-layers-list > *').removeClass('active selected is-selected');
            $('#wppoppop-layers-list [data-id="' + actualId + '"], #wppoppop-layers-list [data-id="' + cleanId + '"], #wppoppop-layers-list [data-id="el-' + cleanId + '"], #wppoppop-layers-list [data-id="wppoppop-el-' + cleanId + '"]')
                .addClass('active selected is-selected');

            // 3. Highlight on Canvas Stage
            $('.wppoppop-canvas-item, .wppoppop-element').removeClass('wppoppop-selected selected is-selected');
            $('#wppoppop-el-' + cleanId + ', #el-' + cleanId + ', .wppoppop-canvas-item[data-id="' + actualId + '"], .wppoppop-canvas-item[data-id="' + cleanId + '"]')
                .addClass('wppoppop-selected selected is-selected');

            // 4. Open Inspector Drawer & Push Layers Panel to right: 376px
            if (window.WpPopPopBuilderInspector) {
                window.WpPopPopBuilderInspector.activeElementId = actualId;
                window.WpPopPopBuilderInspector.activeId = actualId;
            }
            var $drawer = $('#wppoppop-inspector-drawer');
            $drawer.css('display', 'flex').removeClass('is-closed').addClass('is-open open');
            $('body, .wppoppop-builder-workspace, .wppoppop-main-frame').addClass('panel-open inspector-open');

            if (window.WpPopPopBuilderLayers && typeof window.WpPopPopBuilderLayers.pushForInspector === 'function') {
                window.WpPopPopBuilderLayers.pushForInspector(true);
            } else {
                $('#wppoppop-floating-layers-panel').css('right', '376px');
            }

            // 5. Hydrate all 5 Tabs
            hydrateAllFiveTabs(el);
        } finally {
            _selectingGuard = false;
        }
    };

    function bindMasterHooks() {
        ensureFiveTabsDom();

        // Hook WpPopPopBuilderInspector methods
        var insp = window.WpPopPopBuilderInspector;
        if (insp) {
            insp.switchTab = executeSwitchTab;
            insp.sanitizeLayerInspectorForElement = hydrateAllFiveTabs;
            if (!insp._masterOpenWrapped) {
                insp._masterOpenWrapped = true;
                var origOpen = insp.open;
                insp.open = function(id) {
                    var res;
                    try {
                        if (typeof origOpen === 'function') res = origOpen.apply(this, arguments);
                    } catch (e) {}
                    var el = resolveElement(id);
                    if (el) hydrateAllFiveTabs(el);
                    return res;
                };
            }
        }

        // 1. CAPTURE-PHASE CLICK LISTENER ON WINDOW (Cannot be blocked by e.stopPropagation()!)
        if (!window._wppoppopMasterCaptureBound) {
            window._wppoppopMasterCaptureBound = true;
            window.addEventListener('click', function(e) {
                var target = e.target;
                if (!target || !target.closest) return;

                // A. Click on Layer Settings Tab Button (Content | Style | Logic | Data | Advanced)
                var tabBtn = target.closest('#wppoppop-inspector-drawer .wppoppop-insp-tab, #wppoppop-inspector-tabs-bar [data-tab], .wppoppop-inspector-tabs [data-tab]');
                if (tabBtn) {
                    e.preventDefault();
                    e.stopPropagation();
                    var tabName = tabBtn.getAttribute('data-tab') || 'basic';
                    executeSwitchTab(tabName);
                    return;
                }

                // B. Click on Layer Item inside Floating Layers Panel
                var layerRow = target.closest('#wppoppop-floating-layers-panel .wppoppop-layer-item, #wppoppop-layers-list .wppoppop-layer-item, #wppoppop-layers-list > li, #wppoppop-layers-list > div');
                if (layerRow) {
                    // Allow action buttons (lock, hide, duplicate, delete) or rename input to work normally
                    if (target.closest('.wppoppop-layer-action-btn, .wppoppop-layer-actions, .wppoppop-layer-btn-lock, .wppoppop-layer-btn-vis, .wppoppop-layer-btn-dup, .wppoppop-layer-btn-del, button, input')) {
                        return;
                    }
                    e.preventDefault();
                    e.stopPropagation();
                    var rawId = layerRow.getAttribute('data-id') || layerRow.getAttribute('data-layer-id') || layerRow.getAttribute('data-el-id') || layerRow.id || '';
                    if (rawId) {
                        window.selectLayerEverywhere(rawId);
                        try {
                            if (window.WpPopPopBuilderLayers && typeof window.WpPopPopBuilderLayers.openLayerInspector === 'function') {
                                window.WpPopPopBuilderLayers.openLayerInspector(rawId);
                            }
                        } catch (errOpen) {}
                    }
                    return;
                }
            }, true); // <-- TRUE = Capture Phase!
        }

        // 2. Two-way live binding for Style, Logic, Data & Advanced inputs
        $(document)
            .off('input.wppoppopMasterSync change.wppoppopMasterSync')
            .on('input.wppoppopMasterSync change.wppoppopMasterSync', '#insp-tab-style input, #insp-tab-style select, #insp-tab-logic input, #insp-tab-logic select, #insp-tab-data input, #insp-tab-advanced input', function() {
                var el = resolveElement();
                if (!el) return;
                var $inp = $(this);
                var prop = $inp.data('prop') || $inp.data('data-prop') || $inp.data('adv-prop') || $inp.data('logic-prop');
                var val = $inp.is(':checkbox') ? $inp.is(':checked') : $inp.val();

                if (this.id === 'prop-opacity') {
                    $('#prop-opacity-val').text(val + '%');
                    el.opacity = val;
                } else if (this.id === 'prop-bg-transparent') {
                    el.transparentFill = val;
                    if (val) el.bgColor = 'transparent';
                } else if (this.id === 'prop-logic-enabled') {
                    el.logicEnabled = val;
                    $('#wppoppop-logic-body').toggle(!!val);
                } else if (prop) {
                    el[prop] = val;
                }

                // Live apply CSS to stage node
                var cleanId = normalizeId(el.id);
                var $node = $('#wppoppop-el-' + cleanId + ', #el-' + cleanId + ', .wppoppop-canvas-item[data-id="' + el.id + '"]').first();
                if ($node.length) {
                    if (el.opacity !== undefined) $node.css('opacity', (parseFloat(el.opacity) > 1 ? parseFloat(el.opacity) / 100 : el.opacity));
                    if (el.bgColor) $node.css('background-color', el.transparentFill ? 'transparent' : el.bgColor);
                    if (el.color) $node.css('color', el.color);
                    if (el.fontSize) $node.css('font-size', el.fontSize + 'px');
                    if (el.fontWeight) $node.css('font-weight', el.fontWeight);
                    if (el.lineHeight) $node.css('line-height', el.lineHeight);
                    if (el.letterSpacing !== undefined) $node.css('letter-spacing', el.letterSpacing + 'px');
                    if (el.borderRadius !== undefined) $node.css('border-radius', el.borderRadius + 'px');
                    if (el.borderWidth !== undefined) $node.css('border', el.borderWidth + 'px ' + (el.borderStyle || 'solid') + ' ' + (el.borderColor || '#334155'));
                }
            });

        // 3. Repeater buttons (+ Add a style, + Add rule, Form Align)
        $(document)
            .off('click.wppoppopMasterRepeaters')
            .on('click.wppoppopMasterRepeaters', '#wppoppop-btn-add-inline-style, #btn-add-logic-rule, #wppoppop-form-align-group .wppoppop-form-align-btn', function(e) {
                e.preventDefault();
                var el = resolveElement();
                if (!el) return;
                if (this.id === 'wppoppop-btn-add-inline-style') {
                    if (!Array.isArray(el.inlineStyles)) el.inlineStyles = [];
                    el.inlineStyles.push({ prop: '', val: '' });
                    renderInlineStylesRepeater(el);
                } else if (this.id === 'btn-add-logic-rule') {
                    if (!Array.isArray(el.logicRules)) el.logicRules = [];
                    el.logicRules.push({ field: '', operator: 'equal', value: '' });
                    renderLogicRulesList(el);
                } else if ($(this).hasClass('wppoppop-form-align-btn')) {
                    el.textAlign = $(this).attr('data-align') || 'left';
                    $('#wppoppop-form-align-group .wppoppop-form-align-btn').removeClass('active button-primary');
                    $(this).addClass('active button-primary');
                }
            });
    }

    $(function() {
        bindMasterHooks();
        setTimeout(bindMasterHooks, 150);
        setTimeout(bindMasterHooks, 450);
    });
    bindMasterHooks();

    /* === WPPOPPOP_LOGIC_RULE_SYNC === */
    function collectLogicRules() {
        var arr = [];
        $('#wppoppop-logic-rules-list .wppoppop-logic-rule-row').each(function() {
            arr.push({
                field: $(this).find('.rule-field').val() || '',
                operator: $(this).find('.rule-operator').val() || 'equal',
                value: $(this).find('.rule-value').val() || ''
            });
        });
        return arr;
    }
    $(document).off('change.wppoppopLogicRules input.wppoppopLogicRules', '#wppoppop-logic-rules-list .rule-field, #wppoppop-logic-rules-list .rule-operator, #wppoppop-logic-rules-list .rule-value')
        .on('change.wppoppopLogicRules input.wppoppopLogicRules', '#wppoppop-logic-rules-list .rule-field, #wppoppop-logic-rules-list .rule-operator, #wppoppop-logic-rules-list .rule-value', function(e) {
            var el = resolveElement();
            if (!el) return;
            el.logicRules = collectLogicRules();
            if ($(this).hasClass('rule-field') && e.type === 'change') renderLogicRulesList(el);
        });
    $(document).off('click.wppoppopLogicRulesDel', '#wppoppop-logic-rules-list .btn-remove-logic-rule')
        .on('click.wppoppopLogicRulesDel', '#wppoppop-logic-rules-list .btn-remove-logic-rule', function(e) {
            e.preventDefault();
            var el = resolveElement();
            if (!el) return;
            var idx = $(this).closest('.wppoppop-logic-rule-row').index();
            var rules = collectLogicRules();
            rules.splice(idx, 1);
            el.logicRules = rules;
            renderLogicRulesList(el);
        });
    /* === /WPPOPPOP_LOGIC_RULE_SYNC === */

    /* === WPPOPPOP_PANEL_GUARD === */
    /* WPPOPPOP_PANEL_GUARD_V2: true when the right panel is already the only one showing, any element type */
    window.__wppPanelSettled = function(typeOrId) {
        var t = String(typeOrId || '').toLowerCase().trim();
        var el = resolveElement(typeOrId);
        if (el && el.type) { t = String(el.type).toLowerCase().trim(); }
        var sel = MEDIA_PANEL_MAP[t];
        if (!sel) { return false; }
        var $vis = $('#wppoppop-inspector-drawer .wppoppop-panel-specific:visible');
        return $vis.length === 1 && $vis.is(sel);
    };
    /* === /WPPOPPOP_PANEL_GUARD === */

    /* WPPOPPOP_REQ_MSG: live */
    $(document).off('input.wppReqMsg change.wppReqMsg', '#prop-gp-required, #prop-gp-required-msg')
        .on('input.wppReqMsg change.wppReqMsg', '#prop-gp-required, #prop-gp-required-msg', function() {
            var el = resolveElement();
            if (!el) return;
            if (this.id === 'prop-gp-required') {
                el.required = $(this).is(':checked');
                $('#insp-row-gp-required-msg').toggle(!!el.required);
            } else {
                el.requiredMessage = $(this).val();
            }
        });

    /* === WPPOPPOP_ADV_TAB: live === */
    function advCollect(el) {
        if ($('#prop-field-name').length) {
            var nm = $.trim($('#prop-field-name').val() || '').replace(/[^A-Za-z0-9_-]/g, '');
            el.fieldName = nm; el.fieldId = nm; el.token = nm;
        }
        if ($('#prop-input-mask').length) { el.inputMask = $.trim($('#prop-input-mask').val() || ''); }
        var f = [], v = [];
        $('.prop-filter-chk:checked').each(function() { f.push($(this).val()); });
        $('.prop-validator-chk:checked').each(function() { v.push($(this).val()); });
        el.filters = f; el.validators = v;
        el.errorMessage = $('#prop-validator-error').val() || '';
        el.validatorEqual = $('#prop-validator-equal').val() || '';
        el.validatorList = $('#prop-validator-list').val() || '';
        $('#wppoppop-val-extra-equal').toggle(v.indexOf('equal') !== -1);
        $('#wppoppop-val-extra-inarray').toggle(v.indexOf('inarray') !== -1);
    }
    $(document).off('input.wppAdv change.wppAdv', '#prop-field-name, #prop-input-mask, .prop-filter-chk, .prop-validator-chk, #prop-validator-error, #prop-validator-equal, #prop-validator-list')
        .on('input.wppAdv change.wppAdv', '#prop-field-name, #prop-input-mask, .prop-filter-chk, .prop-validator-chk, #prop-validator-error, #prop-validator-equal, #prop-validator-list', function(e) {
            var el = resolveElement();
            if (!el) return;
            if (this.id === 'prop-field-name' && e.type === 'change') {
                $(this).val($.trim($(this).val() || '').replace(/[^A-Za-z0-9_-]/g, ''));
            }
            advCollect(el);
            if ($(this).is('.prop-validator-chk[value="required"]')) {
                el.required = $(this).is(':checked');
                $('#prop-gp-required').prop('checked', el.required);
                $('#insp-row-gp-required-msg').toggle(!!el.required);
            }
        });
    $(document).off('change.wppAdvReq', '#prop-gp-required')
        .on('change.wppAdvReq', '#prop-gp-required', function() {
            $('.prop-validator-chk[value="required"]').prop('checked', $(this).is(':checked'));
            var el = resolveElement();
            if (el) advCollect(el);
        });
    /* === /WPPOPPOP_ADV_TAB: live === */

})(window, jQuery);
/* === WPPOPPOP_MASTER_LAYER_INSPECTOR_AND_LAYER_ENGINE_END === */
