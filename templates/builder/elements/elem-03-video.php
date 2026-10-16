<?php
if (!defined('ABSPATH')) exit;
?>
<!-- DECOMPOSED MODULE 03: VIDEO EMBED (video) -->
<div class="element-panel wppoppop-panel-specific" id="panel-elem-video" data-elem-family="media" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;">3. Video Embed &amp; Playback</div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px;">
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">VIDEO PROVIDER</label>
            <select id="prop-video-provider" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="videoProvider" style="font-size:12px;">
                <option value="youtube">YouTube</option>
                <option value="vimeo">Vimeo</option>
                <option value="mp4">Direct MP4 URL</option>
            </select>
        </div>
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">ASPECT RATIO</label>
            <select id="prop-video-aspect" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="aspectRatio" style="font-size:12px;">
                <option value="16:9">16:9 Widescreen</option>
                <option value="4:3">4:3 Standard</option>
                <option value="1:1">1:1 Square</option>
                <option value="9:16">9:16 Vertical</option>
            </select>
        </div>
    </div>
    <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">VIDEO URL OR EMBED ID</label>
    <input type="text" id="prop-video-url" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="videoUrl" placeholder="https://www.youtube.com/watch?v=..." style="margin-bottom:10px;font-size:12px;">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;">
        <label style="display:inline-flex;align-items:center;gap:6px;font-size:11px;color:#cbd5e1;cursor:pointer;">
            <input type="checkbox" id="prop-video-autoplay" class="wppoppop-gp-bind" data-prop="videoAutoplay" value="1"> Autoplay
        </label>
        <label style="display:inline-flex;align-items:center;gap:6px;font-size:11px;color:#cbd5e1;cursor:pointer;">
            <input type="checkbox" id="prop-video-mute" class="wppoppop-gp-bind" data-prop="videoMute" value="1"> Muted
        </label>
        <label style="display:inline-flex;align-items:center;gap:6px;font-size:11px;color:#cbd5e1;cursor:pointer;">
            <input type="checkbox" id="prop-video-loop" class="wppoppop-gp-bind" data-prop="videoLoop" value="1"> Loop Video
        </label>
        <label style="display:inline-flex;align-items:center;gap:6px;font-size:11px;color:#cbd5e1;cursor:pointer;">
            <input type="checkbox" id="prop-video-controls" class="wppoppop-gp-bind" data-prop="videoControls" value="1" checked> Player Controls
        </label>
    </div>
</div>
