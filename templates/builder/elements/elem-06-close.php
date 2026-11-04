<?php
if (!defined('ABSPATH')) exit;
?>
<!-- DECOMPOSED MODULE 06: CLOSE ICON (close / close_icon) -->
<div class="element-panel wppoppop-panel-specific" id="panel-elem-close" data-elem-family="media" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;"><?php esc_html_e('6. Close Icon & Dismissal', 'wppoppop'); ?></div>
    <div style="display:grid;grid-template-columns:2fr 1fr;gap:8px;margin-bottom:10px;">
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e('ICON GLYPH', 'wppoppop'); ?></label>
            <select id="prop-close-glyph" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="content" style="font-size:12px;">
                <option value="×"><?php esc_html_e('Standard Cross (×)', 'wppoppop'); ?></option>
                <option value="✕"><?php esc_html_e('Thin Cross (✕)', 'wppoppop'); ?></option>
                <option value="✖"><?php esc_html_e('Bold Cross (✖)', 'wppoppop'); ?></option>
                <option value="⊗"><?php esc_html_e('Circle Cross (⊗)', 'wppoppop'); ?></option>
                <option value="CLOSE"><?php esc_html_e('Text Label (CLOSE)', 'wppoppop'); ?></option>
            </select>
        </div>
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e('SIZE (PX)', 'wppoppop'); ?></label>
            <input type="number" id="prop-close-size" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="iconSize" min="10" max="80" value="20" style="font-size:12px;">
        </div>
    </div>
    <div style="display:grid;grid-template-columns:2fr 1fr;gap:8px;">
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e('DISMISS BEHAVIOR', 'wppoppop'); ?></label>
            <select id="prop-close-action" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="closeAction" style="font-size:12px;">
                <option value="close"><?php esc_html_e('Close Immediately', 'wppoppop'); ?></option>
                <option value="close_period"><?php esc_html_e('Suppress for Session', 'wppoppop'); ?></option>
                <option value="close_forever"><?php esc_html_e('Suppress via Cookie', 'wppoppop'); ?></option>
            </select>
        </div>
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e('GLYPH COLOR', 'wppoppop'); ?></label>
            <input type="color" id="prop-close-glyph-color" class="widefat wppoppop-gp-bind" data-prop="color" value="#64748b" style="width:100%;height:32px;padding:2px;border:1px solid #334155;border-radius:4px;background:#090d16;cursor:pointer;box-sizing:border-box;font-size:12px;">
        </div>
    </div>
    <div style="margin-top:8px;" id="prop-close-cookie-wrap">
        <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e('COOKIE DAYS', 'wppoppop'); ?></label>
        <input type="number" id="prop-close-days" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="cookieDays" min="0" max="365" value="30" style="font-size:12px;">
    </div>
</div>
