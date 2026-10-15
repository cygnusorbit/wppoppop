<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!-- DECOMPOSED MODULE 25: STAR RATING (rating) -->
<div class="wppoppop-elem-panel element-panel wppoppop-panel-specific" id="panel-elem-rating" data-elem-family="form" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;"><?php esc_html_e( '25. Star Rating Settings', 'wppoppop' ); ?></div>
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;">
        <div>
            <label for="prop-rating-max" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'SCALE MAX', 'wppoppop' ); ?></label>
            <select id="prop-rating-max" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="ratingMax" style="font-size:12px;">
                <option value="5">5 Stars</option>
                <option value="10">10 Stars</option>
            </select>
        </div>
        <div>
            <label for="prop-rating-shape" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'GLYPH', 'wppoppop' ); ?></label>
            <select id="prop-rating-shape" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="ratingShape" style="font-size:12px;">
                <option value="star">&#9733; Star</option>
                <option value="heart">&#9829; Heart</option>
                <option value="thumb">&#128077; Thumb</option>
            </select>
        </div>
        <div>
            <label for="prop-rating-color" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'ACTIVE COLOR', 'wppoppop' ); ?></label>
            <input type="color" id="prop-rating-color" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="ratingColor" value="#f59e0b" style="height:30px;padding:2px;cursor:pointer;">
        </div>
    </div>
</div>
