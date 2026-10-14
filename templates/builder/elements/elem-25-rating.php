<?php
if (!defined('ABSPATH')) exit;
?>
<!-- DECOMPOSED MODULE 25: STAR / GLYPH RATING (rating) -->
<div class="element-panel wppoppop-panel-specific" id="panel-elem-rating" data-elem-family="form" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;">25. Rating Settings (Form Family — 5 Tabs)</div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px;">
        <div>
            <label for="prop-rating-fieldname" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">FIELD TOKEN</label>
            <input type="text" id="prop-rating-fieldname" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="fieldName" value="rating" style="font-size:12px;">
        </div>
        <div>
            <label for="prop-rating-max" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">SCALE MAX</label>
            <select id="prop-rating-max" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="ratingMax" style="font-size:12px;">
                <option value="5" selected>5 Stars / Points</option>
                <option value="10">10 Stars / Points</option>
            </select>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px;">
        <div>
            <label for="prop-rating-shape" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">GLYPH SHAPE</label>
            <select id="prop-rating-shape" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="ratingShape" style="font-size:12px;">
                <option value="star" selected>★ Stars</option>
                <option value="heart">♥ Hearts</option>
                <option value="thumb">👍 Thumbs Up</option>
            </select>
        </div>
        <div>
            <label for="prop-rating-color" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">ACTIVE COLOR</label>
            <input type="color" id="prop-rating-color" class="widefat wppoppop-gp-bind" data-prop="ratingColor" value="#f59e0b" style="height:34px;padding:2px;background:#090d16;border:1px solid #1e293b;border-radius:6px;cursor:pointer;">
        </div>
    </div>

    
</div>
