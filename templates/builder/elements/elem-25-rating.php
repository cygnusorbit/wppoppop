<?php
if (!defined('ABSPATH')) exit;
?>
<!-- DECOMPOSED MODULE 25: STAR RATING (rating) -->
<div class="element-panel wppoppop-panel-specific" id="panel-elem-rating" data-elem-family="form" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#f59e0b;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;"><?php esc_html_e('25. Interactive Rating Scale', 'wppoppop'); ?></div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px;">
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e('SCALE MAX', 'wppoppop'); ?></label>
            <select id="prop-rating-scale" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="ratingMax" style="font-size:12px;">
                <option value="5"><?php esc_html_e('5 Stars / Icons', 'wppoppop'); ?></option>
                <option value="10"><?php esc_html_e('10 Stars / Icons', 'wppoppop'); ?></option>
            </select>
        </div>
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e('ICON GLYPH', 'wppoppop'); ?></label>
            <select id="prop-rating-glyph" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="ratingShape" style="font-size:12px;">
                <option value="star"><?php esc_html_e('★ Star', 'wppoppop'); ?></option>
                <option value="heart"><?php esc_html_e('♥ Heart', 'wppoppop'); ?></option>
                <option value="thumb"><?php esc_html_e('👍 Thumbs Up', 'wppoppop'); ?></option>
            </select>
        </div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e('ACTIVE COLOR', 'wppoppop'); ?></label>
            <input type="color" id="prop-rating-active-color" class="widefat wppoppop-gp-bind" data-prop="ratingColor" value="#f59e0b" style="height:34px;padding:2px;background:#090d16;border:1px solid #1e293b;border-radius:6px;cursor:pointer;">
        </div>
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e('INACTIVE COLOR', 'wppoppop'); ?></label>
            <input type="color" id="prop-rating-inactive-color" class="widefat wppoppop-gp-bind" data-prop="ratingInactiveColor" value="#475569" style="height:34px;padding:2px;background:#090d16;border:1px solid #1e293b;border-radius:6px;cursor:pointer;">
        </div>
    </div>
</div>
