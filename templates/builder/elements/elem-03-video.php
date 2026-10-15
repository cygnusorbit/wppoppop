<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!-- DECOMPOSED MODULE 03: VIDEO (video) -->
<div class="wppoppop-elem-panel element-panel wppoppop-panel-specific" id="panel-elem-video" data-elem-family="media" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;"><?php esc_html_e( '3. Video Embed & Playback', 'wppoppop' ); ?></div>
    <div class="wppoppop-gp-2col-grid" style="margin-bottom:10px;">
        <div class="wppoppop-gp-col">
            <label for="prop-video-provider" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'PROVIDER', 'wppoppop' ); ?></label>
            <select class="widefat wppoppop-input wppoppop-gp-bind" id="prop-video-provider" data-prop="videoProvider" style="font-size:12px;">
                <option value="youtube">YouTube</option>
                <option value="vimeo">Vimeo</option>
                <option value="custom_mp4">Direct MP4 URL</option>
            </select>
        </div>
        <div class="wppoppop-gp-col">
            <label for="prop-video-ratio" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'ASPECT RATIO', 'wppoppop' ); ?></label>
            <select class="widefat wppoppop-input wppoppop-gp-bind" id="prop-video-ratio" data-prop="aspectRatio" style="font-size:12px;">
                <option value="16:9">16:9 Widescreen</option>
                <option value="4:3">4:3 Standard</option>
                <option value="1:1">1:1 Square</option>
                <option value="9:16">9:16 Vertical</option>
            </select>
        </div>
    </div>
    <div style="margin-bottom:10px;">
        <label for="prop-video-url" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'VIDEO URL OR EMBED ID', 'wppoppop' ); ?></label>
        <input type="text" class="widefat wppoppop-input wppoppop-gp-bind" id="prop-video-url" data-prop="videoUrl" placeholder="https://www.youtube.com/watch?v=..." style="font-size:12px;">
    </div>
    <div class="wppoppop-gp-2col-grid">
        <label style="font-size:11px;color:#cbd5e1;display:flex;align-items:center;gap:6px;cursor:pointer;">
            <input type="checkbox" class="wppoppop-gp-bind" id="prop-video-autoplay" data-prop="autoplay"> <?php esc_html_e( 'Autoplay', 'wppoppop' ); ?>
        </label>
        <label style="font-size:11px;color:#cbd5e1;display:flex;align-items:center;gap:6px;cursor:pointer;">
            <input type="checkbox" class="wppoppop-gp-bind" id="prop-video-mute" data-prop="muted"> <?php esc_html_e( 'Muted', 'wppoppop' ); ?>
        </label>
        <label style="font-size:11px;color:#cbd5e1;display:flex;align-items:center;gap:6px;cursor:pointer;">
            <input type="checkbox" class="wppoppop-gp-bind" id="prop-video-loop" data-prop="loop"> <?php esc_html_e( 'Loop', 'wppoppop' ); ?>
        </label>
        <label style="font-size:11px;color:#cbd5e1;display:flex;align-items:center;gap:6px;cursor:pointer;">
            <input type="checkbox" class="wppoppop-gp-bind" id="prop-video-controls" data-prop="controls" checked> <?php esc_html_e( 'Show Controls', 'wppoppop' ); ?>
        </label>
    </div>
</div>
