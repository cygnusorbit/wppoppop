<?php
if (!defined('ABSPATH')) exit;
?>
<!-- DECOMPOSED MODULE 25: STAR RATING (rating) -->
<div class="element-panel wppoppop-panel-specific" id="panel-elem-rating" data-elem-family="form" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#f59e0b;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;">25. Interactive Rating Scale</div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px;">
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">SCALE MAX</label>
            <select id="prop-rating-max" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="maxStars" style="font-size:12px;">
                <option value="5">5 Stars / Icons</option>
                <option value="10">10 Stars / Icons</option>
            </select>
        </div>
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">ICON GLYPH</label>
            <select id="prop-rating-glyph" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="ratingGlyph" style="font-size:12px;">
                <option value="star">★ Star</option>
                <option value="heart">♥ Heart</option>
                <option value="thumb">👍 Thumbs Up</option>
            </select>
        </div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">ACTIVE COLOR</label>
            <input type="color" id="prop-rating-active-color" class="widefat wppoppop-gp-bind" data-prop="activeColor" value="#f59e0b" style="height:34px;padding:2px;background:#090d16;border:1px solid #1e293b;border-radius:6px;cursor:pointer;">
        </div>
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">INACTIVE COLOR</label>
            <input type="color" id="prop-rating-inactive-color" class="widefat wppoppop-gp-bind" data-prop="inactiveColor" value="#475569" style="height:34px;padding:2px;background:#090d16;border:1px solid #1e293b;border-radius:6px;cursor:pointer;">
        </div>
    </div>
</div>
