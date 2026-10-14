<?php
if (!defined('ABSPATH')) exit;
?>
<!-- DECOMPOSED MODULE 06: CLOSE ICON (close / close_icon) -->
<div class="element-panel wppoppop-panel-specific" id="panel-elem-close" data-elem-family="media" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;">6. Close Icon Settings (Media Family)</div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px;">
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">ICON GLYPH</label>
            <select id="prop-close-glyph" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="content" style="font-size:12px;">
                <option value="×">Standard Cross (×)</option>
                <option value="✕">Thin Cross (✕)</option>
                <option value="✖">Bold Cross (✖)</option>
                <option value="⊗">Circle Cross (⊗)</option>
                <option value="CLOSE">Text Label (CLOSE)</option>
            </select>
        </div>
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">DISMISS BEHAVIOR</label>
            <select id="prop-close-action" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="closeAction" style="font-size:12px;">
                <option value="close">Close Immediately</option>
                <option value="close_period">Suppress for Session</option>
                <option value="close_forever">Suppress via Cookie</option>
            </select>
        </div>
    </div>
    <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">COOKIE SUPPRESSION DAYS</label>
    <input type="number" id="prop-close-days" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="cookieDays" min="0" max="365" value="30" style="font-size:12px;">
</div>
