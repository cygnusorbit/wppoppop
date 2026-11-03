<?php
if (!defined('ABSPATH')) exit;
?>
<!-- DECOMPOSED MODULE 03: VIDEO EMBED (video) -->
<div class="element-panel wppoppop-panel-specific" id="panel-elem-video" data-elem-family="media" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;">3. Video Embed &amp; Playback</div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px;">
        <div>
            <label for="prop-video-width" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">WIDTH (PX)</label>
            <input type="number" id="prop-video-width" class="widefat wppoppop-input" min="16" step="1" value="200" style="font-size:12px;">
        </div>
        <div>
            <label for="prop-video-height" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">HEIGHT (PX)</label>
            <input type="number" id="prop-video-height" class="widefat wppoppop-input" min="16" step="1" value="150" style="font-size:12px;">
        </div>
    </div>
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
    <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">VIDEO THUMBNAIL</label>
    <div style="display:flex;gap:6px;margin-bottom:6px;">
        <input type="text" id="prop-video-thumb" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="videoThumb" placeholder="https://... (optional)" style="flex:1;min-width:0;font-size:12px;">
        <button type="button" id="prop-video-thumb-btn" class="button button-secondary" style="height:36px;padding:0 10px;font-size:11px;font-weight:700;white-space:nowrap;">WP Media</button>
    </div>
    <div id="prop-video-thumb-preview" role="button" tabindex="0" title="Click to choose a thumbnail from the Media Library" style="position:relative;height:90px;margin-bottom:10px;border:1px dashed #334155;border-radius:6px;background:#020617 center / cover no-repeat;cursor:pointer;display:flex;align-items:center;justify-content:center;overflow:hidden;">
        <span class="wppoppop-video-thumb-hint" style="font-size:11px;color:#64748b;pointer-events:none;">Click to add thumbnail</span>
        <button type="button" id="prop-video-thumb-clear" title="Remove thumbnail" style="display:none;position:absolute;top:4px;right:4px;width:22px;height:22px;line-height:20px;padding:0;border:0;border-radius:50%;background:rgba(15,23,42,.85);color:#fff;font-size:14px;cursor:pointer;">&times;</button>
    </div>
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
