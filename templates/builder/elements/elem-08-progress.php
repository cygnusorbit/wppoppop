<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!-- DECOMPOSED MODULE 08: PROGRESS BAR (progress) -->
<div class="wppoppop-elem-panel element-panel wppoppop-panel-specific" id="panel-elem-progress" data-elem-family="media" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;"><?php esc_html_e( '8. Progress Bar Settings', 'wppoppop' ); ?></div>
    <div class="wppoppop-gp-2col-grid" style="margin-bottom:10px;">
        <div class="wppoppop-gp-col">
            <label for="prop-progress-mode" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'PROGRESS MODE', 'wppoppop' ); ?></label>
            <select class="widefat wppoppop-input wppoppop-gp-bind" id="prop-progress-mode" data-prop="progressMode" style="font-size:12px;">
                <option value="manual"><?php esc_html_e( 'Manual Percentage', 'wppoppop' ); ?></option>
                <option value="canvas_steps"><?php esc_html_e( 'Auto Canvas Step Sync', 'wppoppop' ); ?></option>
            </select>
        </div>
        <div class="wppoppop-gp-col" id="prop-progress-val-wrap">
            <label for="prop-progress-val" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'FILL (%)', 'wppoppop' ); ?></label>
            <input type="number" class="widefat wppoppop-input wppoppop-gp-bind" id="prop-progress-val" data-prop="progressValue" value="65" min="0" max="100" style="font-size:12px;">
        </div>
    </div>
    <div style="margin-bottom:10px;">
        <label for="prop-progress-label" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'CUSTOM BAR LABEL (OPTIONAL)', 'wppoppop' ); ?></label>
        <input type="text" class="widefat wppoppop-input wppoppop-gp-bind" id="prop-progress-label" data-prop="progressLabel" placeholder="e.g. Step 1 of 2 — Almost Complete!" style="font-size:12px;">
    </div>
    <div class="wppoppop-gp-2col-grid" style="margin-bottom:10px;">
        <div class="wppoppop-gp-col">
            <label for="prop-progress-fill-color" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'FILL BAR COLOR', 'wppoppop' ); ?></label>
            <input type="color" class="widefat wppoppop-input wppoppop-gp-bind" id="prop-progress-fill-color" data-prop="fillColor" value="#c2185b" style="height:30px;padding:2px;cursor:pointer;">
        </div>
        <div class="wppoppop-gp-col">
            <label for="prop-progress-track-color" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'TRACK COLOR', 'wppoppop' ); ?></label>
            <input type="color" class="widefat wppoppop-input wppoppop-gp-bind" id="prop-progress-track-color" data-prop="trackColor" value="#334155" style="height:30px;padding:2px;cursor:pointer;">
        </div>
    </div>
    <div class="wppoppop-gp-2col-grid">
        <label style="font-size:11px;color:#cbd5e1;display:flex;align-items:center;gap:6px;cursor:pointer;">
            <input type="checkbox" class="wppoppop-gp-bind" id="prop-progress-show-label" data-prop="showPercentLabel" checked> <?php esc_html_e( 'Show % badge', 'wppoppop' ); ?>
        </label>
        <label style="font-size:11px;color:#cbd5e1;display:flex;align-items:center;gap:6px;cursor:pointer;">
            <input type="checkbox" class="wppoppop-gp-bind" id="prop-progress-striped" data-prop="striped"> <?php esc_html_e( 'Animated stripes', 'wppoppop' ); ?>
        </label>
    </div>
</div>
