<?php
if (!defined('ABSPATH')) exit;
?>
<!-- DECOMPOSED MODULE 03: VIDEO EMBED (video) -->
<div class="element-panel wppoppop-panel-specific" id="panel-elem-video" data-elem-family="media" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;"><?php esc_html_e('3. Video Embed & Playback', 'wppoppop'); ?></div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px;">
        <div>
            <label for="prop-video-width" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e('WIDTH (PX)', 'wppoppop'); ?></label>
            <input type="number" id="prop-video-width" class="widefat wppoppop-input" min="16" step="1" value="200" style="font-size:12px;">
        </div>
        <div>
            <label for="prop-video-height" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e('HEIGHT (PX)', 'wppoppop'); ?></label>
            <input type="number" id="prop-video-height" class="widefat wppoppop-input" min="16" step="1" value="150" style="font-size:12px;">
        </div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px;">
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e('VIDEO PROVIDER', 'wppoppop'); ?></label>
            <select id="prop-video-provider" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="videoProvider" style="font-size:12px;">
                <option value="youtube"><?php esc_html_e('YouTube', 'wppoppop'); ?></option>
                <option value="vimeo"><?php esc_html_e('Vimeo', 'wppoppop'); ?></option>
                <option value="mp4"><?php esc_html_e('Direct MP4 URL', 'wppoppop'); ?></option>
            </select>
        </div>
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e('ASPECT RATIO', 'wppoppop'); ?></label>
            <select id="prop-video-aspect" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="aspectRatio" style="font-size:12px;">
                <option value="16:9"><?php esc_html_e('16:9 Widescreen', 'wppoppop'); ?></option>
                <option value="4:3"><?php esc_html_e('4:3 Standard', 'wppoppop'); ?></option>
                <option value="1:1"><?php esc_html_e('1:1 Square', 'wppoppop'); ?></option>
                <option value="9:16"><?php esc_html_e('9:16 Vertical', 'wppoppop'); ?></option>
            </select>
        </div>
    </div>
    <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e('VIDEO URL OR EMBED ID', 'wppoppop'); ?></label>
    <input type="text" id="prop-video-url" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="videoUrl" placeholder="https://www.youtube.com/watch?v=..." style="margin-bottom:10px;font-size:12px;">
    <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e('VIDEO THUMBNAIL', 'wppoppop'); ?></label>
    <div style="display:flex;gap:6px;margin-bottom:6px;">
        <input type="text" id="prop-video-thumb" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="videoThumb" placeholder="https://... (optional)" style="flex:1;min-width:0;font-size:12px;">
        <button type="button" id="prop-video-thumb-btn" class="button button-secondary" style="height:36px;padding:0 10px;font-size:11px;font-weight:700;white-space:nowrap;"><?php esc_html_e('WP Media', 'wppoppop'); ?></button>
    </div>
    <div id="prop-video-thumb-preview" role="button" tabindex="0" title="<?php esc_attr_e('Click to choose a thumbnail from the Media Library', 'wppoppop'); ?>" style="position:relative;height:90px;margin-bottom:10px;border:1px dashed #334155;border-radius:6px;background:#020617 center / cover no-repeat;cursor:pointer;display:flex;align-items:center;justify-content:center;overflow:hidden;">
        <span class="wppoppop-video-thumb-hint" style="font-size:11px;color:#64748b;pointer-events:none;"><?php esc_html_e('Click to add thumbnail', 'wppoppop'); ?></span>
        <button type="button" id="prop-video-thumb-clear" title="<?php esc_attr_e('Remove thumbnail', 'wppoppop'); ?>" style="display:none;position:absolute;top:4px;right:4px;width:22px;height:22px;line-height:20px;padding:0;border:0;border-radius:50%;background:rgba(15,23,42,.85);color:#fff;font-size:14px;cursor:pointer;">&times;</button>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;">
        <label style="display:inline-flex;align-items:center;gap:6px;font-size:11px;color:#cbd5e1;cursor:pointer;">
            <input type="checkbox" id="prop-video-autoplay" class="wppoppop-gp-bind" data-prop="videoAutoplay" value="1"> <?php esc_html_e('Autoplay', 'wppoppop'); ?>
        </label>
        <label style="display:inline-flex;align-items:center;gap:6px;font-size:11px;color:#cbd5e1;cursor:pointer;">
            <input type="checkbox" id="prop-video-mute" class="wppoppop-gp-bind" data-prop="videoMute" value="1"> <?php esc_html_e('Muted', 'wppoppop'); ?>
        </label>
        <label style="display:inline-flex;align-items:center;gap:6px;font-size:11px;color:#cbd5e1;cursor:pointer;">
            <input type="checkbox" id="prop-video-loop" class="wppoppop-gp-bind" data-prop="videoLoop" value="1"> <?php esc_html_e('Loop Video', 'wppoppop'); ?>
        </label>
        <label style="display:inline-flex;align-items:center;gap:6px;font-size:11px;color:#cbd5e1;cursor:pointer;">
            <input type="checkbox" id="prop-video-controls" class="wppoppop-gp-bind" data-prop="videoControls" value="1" checked> <?php esc_html_e('Player Controls', 'wppoppop'); ?>
        </label>
    </div>
</div>
