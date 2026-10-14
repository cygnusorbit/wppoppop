<?php
if (!defined('ABSPATH')) exit;
// Media Family panels decomposed into elem-01-shape.php .. elem-09-button.php & elem-26-submit.php
?>


<!-- 1. Rectangle / Square (shape) -->
<div class="wppoppop-elem-panel element-panel wppoppop-panel-specific" id="panel-elem-shape" style="display:none;">
    <div class="wppoppop-panel-card">
        <div class="wppoppop-panel-card-title"><?php esc_html_e('1. Shape Vector & Geometry', 'wppoppop'); ?></div>
        <div class="wppoppop-form-group">
            <label class="wppoppop-form-label"><?php esc_html_e('SVG Shape Preset (11 GreenPop Shapes)', 'wppoppop'); ?></label>
            <select class="wppoppop-form-control wppoppop-gp-bind" id="prop-shape-preset" data-prop="shapePreset">
                <option value="rectangle"><?php esc_html_e('Rectangle / Square', 'wppoppop'); ?></option>
                <option value="rounded"><?php esc_html_e('Rounded Card', 'wppoppop'); ?></option>
                <option value="circle"><?php esc_html_e('Circle / Oval', 'wppoppop'); ?></option>
                <option value="pill"><?php esc_html_e('Pill / Stadium', 'wppoppop'); ?></option>
                <option value="triangle"><?php esc_html_e('Triangle', 'wppoppop'); ?></option>
                <option value="diamond"><?php esc_html_e('Diamond / Rhombus', 'wppoppop'); ?></option>
                <option value="star"><?php esc_html_e('5-Point Star', 'wppoppop'); ?></option>
                <option value="heart"><?php esc_html_e('Heart', 'wppoppop'); ?></option>
                <option value="pentagon"><?php esc_html_e('Pentagon', 'wppoppop'); ?></option>
                <option value="hexagon"><?php esc_html_e('Hexagon', 'wppoppop'); ?></option>
                <option value="badge"><?php esc_html_e('Seal / Badge', 'wppoppop'); ?></option>
            </select>
        </div>
        <div class="wppoppop-form-row" style="margin-top:10px;display:grid;grid-template-columns:1fr 1fr;gap:8px;align-items:end;">
            <div>
                <label class="wppoppop-form-label"><?php esc_html_e('Rotation Angle (0°–360°)', 'wppoppop'); ?></label>
                <input type="number" class="wppoppop-form-control wppoppop-gp-bind" id="prop-shape-rotate" data-prop="rotate" value="0" min="0" max="360">
            </div>
            <div style="padding-bottom:6px;">
                <label style="font-size:12px;display:flex;align-items:center;gap:6px;cursor:pointer;">
                    <input type="checkbox" class="wppoppop-gp-bind" id="prop-shape-transparent" data-prop="transparentFill" value="1">
                    <?php esc_html_e('Transparent Fill', 'wppoppop'); ?>
                </label>
            </div>
        </div>
    </div>
</div>


<!-- 2. Image (image) -->
<div class="wppoppop-elem-panel element-panel wppoppop-panel-specific" id="panel-elem-image" style="display:none;">
    <div class="wppoppop-panel-card">
        <div class="wppoppop-panel-card-title"><?php esc_html_e('2. Image Source & Fitting', 'wppoppop'); ?></div>
        <div class="wppoppop-form-group">
            <label class="wppoppop-form-label"><?php esc_html_e('Image URL / Media Library', 'wppoppop'); ?></label>
            <div style="display:flex;gap:6px;">
                <input type="text" class="wppoppop-form-control wppoppop-gp-bind" id="prop-image-url" data-prop="src" placeholder="https://..." style="flex:1;">
                <button type="button" class="button button-secondary" id="prop-image-media-btn" title="<?php esc_attr_e('Select from WordPress Media Library', 'wppoppop'); ?>">
                    <span class="dashicons dashicons-admin-media" style="margin-top:3px;"></span>
                </button>
            </div>
        </div>
        <div class="wppoppop-form-row" style="margin-top:10px;display:grid;grid-template-columns:1fr 1fr;gap:8px;">
            <div>
                <label class="wppoppop-form-label"><?php esc_html_e('Alt Text', 'wppoppop'); ?></label>
                <input type="text" class="wppoppop-form-control wppoppop-gp-bind" id="prop-image-alt" data-prop="alt" placeholder="<?php esc_attr_e('Image description', 'wppoppop'); ?>">
            </div>
            <div>
                <label class="wppoppop-form-label"><?php esc_html_e('Object Fit', 'wppoppop'); ?></label>
                <select class="wppoppop-form-control wppoppop-gp-bind" id="prop-image-fit" data-prop="objectFit">
                    <option value="contain"><?php esc_html_e('Contain', 'wppoppop'); ?></option>
                    <option value="cover"><?php esc_html_e('Cover (Crop)', 'wppoppop'); ?></option>
                    <option value="fill"><?php esc_html_e('Fill (Stretch)', 'wppoppop'); ?></option>
                    <option value="scale-down"><?php esc_html_e('Scale Down', 'wppoppop'); ?></option>
                </select>
            </div>
        </div>
    </div>
</div>


<!-- 3. Video (video) -->
<div class="wppoppop-elem-panel element-panel wppoppop-panel-specific" id="panel-elem-video" style="display:none;">
    <div class="wppoppop-panel-card">
        <div class="wppoppop-panel-card-title"><?php esc_html_e('3. Video Embed & Playback', 'wppoppop'); ?></div>
        <div class="wppoppop-form-row" style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
            <div>
                <label class="wppoppop-form-label"><?php esc_html_e('Provider', 'wppoppop'); ?></label>
                <select class="wppoppop-form-control wppoppop-gp-bind" id="prop-video-provider" data-prop="videoProvider">
                    <option value="youtube">YouTube</option>
                    <option value="vimeo">Vimeo</option>
                    <option value="custom_mp4">Direct MP4 URL</option>
                </select>
            </div>
            <div>
                <label class="wppoppop-form-label"><?php esc_html_e('Aspect Ratio', 'wppoppop'); ?></label>
                <select class="wppoppop-form-control wppoppop-gp-bind" id="prop-video-ratio" data-prop="aspectRatio">
                    <option value="16:9">16:9 Widescreen</option>
                    <option value="4:3">4:3 Standard</option>
                    <option value="1:1">1:1 Square</option>
                    <option value="9:16">9:16 Vertical</option>
                </select>
            </div>
        </div>
        <div class="wppoppop-form-group" style="margin-top:10px;">
            <label class="wppoppop-form-label"><?php esc_html_e('Video URL or Embed ID', 'wppoppop'); ?></label>
            <input type="text" class="wppoppop-form-control wppoppop-gp-bind" id="prop-video-url" data-prop="videoUrl" placeholder="https://www.youtube.com/watch?v=...">
        </div>
        <div class="wppoppop-form-group" style="margin-top:10px;display:grid;grid-template-columns:1fr 1fr;gap:6px;">
            <label style="font-size:12px;display:flex;align-items:center;gap:6px;cursor:pointer;">
                <input type="checkbox" class="wppoppop-gp-bind" id="prop-video-autoplay" data-prop="autoplay"> <?php esc_html_e('Autoplay', 'wppoppop'); ?>
            </label>
            <label style="font-size:12px;display:flex;align-items:center;gap:6px;cursor:pointer;">
                <input type="checkbox" class="wppoppop-gp-bind" id="prop-video-mute" data-prop="muted"> <?php esc_html_e('Muted', 'wppoppop'); ?>
            </label>
            <label style="font-size:12px;display:flex;align-items:center;gap:6px;cursor:pointer;">
                <input type="checkbox" class="wppoppop-gp-bind" id="prop-video-loop" data-prop="loop"> <?php esc_html_e('Loop', 'wppoppop'); ?>
            </label>
            <label style="font-size:12px;display:flex;align-items:center;gap:6px;cursor:pointer;">
                <input type="checkbox" class="wppoppop-gp-bind" id="prop-video-controls" data-prop="controls" checked> <?php esc_html_e('Controls', 'wppoppop'); ?>
            </label>
        </div>
    </div>
</div>


<!-- 4. Label (label) -->
<div class="wppoppop-elem-panel element-panel wppoppop-panel-specific" id="panel-elem-label" style="display:none;">
    <div class="wppoppop-panel-card">
        <div class="wppoppop-panel-card-title"><?php esc_html_e('4. Label / Headline Content', 'wppoppop'); ?></div>
        <div class="wppoppop-form-group">
            <label class="wppoppop-form-label"><?php esc_html_e('Text / Headline Content', 'wppoppop'); ?></label>
            <textarea class="wppoppop-form-control wppoppop-gp-bind" id="prop-label-text" data-prop="content" rows="3" placeholder="<?php esc_attr_e('Enter label or headline text...', 'wppoppop'); ?>"></textarea>
        </div>
        <div class="wppoppop-form-row" style="margin-top:10px;display:grid;grid-template-columns:1fr 1fr;gap:8px;">
            <div>
                <label class="wppoppop-form-label"><?php esc_html_e('Semantic HTML Tag', 'wppoppop'); ?></label>
                <select class="wppoppop-form-control wppoppop-gp-bind" id="prop-label-tag" data-prop="tag">
                    <option value="h1">H1</option>
                    <option value="h2">H2</option>
                    <option value="h3">H3</option>
                    <option value="h4">H4</option>
                    <option value="p">Paragraph (P)</option>
                    <option value="div">DIV</option>
                    <option value="span">SPAN</option>
                </select>
            </div>
            <div>
                <label class="wppoppop-form-label"><?php esc_html_e('Text Transform', 'wppoppop'); ?></label>
                <select class="wppoppop-form-control wppoppop-gp-bind" id="prop-label-transform" data-prop="textTransform">
                    <option value="none"><?php esc_html_e('None', 'wppoppop'); ?></option>
                    <option value="uppercase">UPPERCASE</option>
                    <option value="lowercase">lowercase</option>
                    <option value="capitalize">Capitalize</option>
                </select>
            </div>
        </div>
    </div>
</div>


<!-- 5. HTML Content (html) -->
<div class="wppoppop-elem-panel element-panel wppoppop-panel-specific" id="panel-elem-html" style="display:none;">
    <div class="wppoppop-panel-card">
        <div class="wppoppop-panel-card-title"><?php esc_html_e('5. Custom HTML / SVG Code', 'wppoppop'); ?></div>
        <div class="wppoppop-form-group">
            <label class="wppoppop-form-label"><?php esc_html_e('Raw HTML / SVG Markup', 'wppoppop'); ?></label>
            <textarea class="wppoppop-form-control wppoppop-gp-bind" id="prop-html-code" data-prop="content" rows="5" style="font-family:monospace;font-size:11px;" placeholder="&lt;div&gt;Custom HTML or SVG&lt;/div&gt;"></textarea>
        </div>
        <div class="wppoppop-form-row" style="margin-top:10px;display:grid;grid-template-columns:1fr 1fr;gap:8px;">
            <div>
                <label class="wppoppop-form-label"><?php esc_html_e('Wrapper Tag', 'wppoppop'); ?></label>
                <select class="wppoppop-form-control wppoppop-gp-bind" id="prop-html-tag" data-prop="tag">
                    <option value="div">&lt;div&gt;</option>
                    <option value="span">&lt;span&gt;</option>
                </select>
            </div>
            <div>
                <label class="wppoppop-form-label"><?php esc_html_e('Overflow Mode', 'wppoppop'); ?></label>
                <select class="wppoppop-form-control wppoppop-gp-bind" id="prop-html-overflow" data-prop="overflow">
                    <option value="hidden"><?php esc_html_e('Hidden / Clip', 'wppoppop'); ?></option>
                    <option value="auto"><?php esc_html_e('Scrollable (Auto)', 'wppoppop'); ?></option>
                    <option value="visible"><?php esc_html_e('Visible', 'wppoppop'); ?></option>
                </select>
            </div>
        </div>
    </div>
</div>


<!-- 6. Close icon (close) -->
<div class="wppoppop-elem-panel element-panel wppoppop-panel-specific" id="panel-elem-close" style="display:none;">
    <div class="wppoppop-panel-card">
        <div class="wppoppop-panel-card-title"><?php esc_html_e('6. Close Icon & Dismissal', 'wppoppop'); ?></div>
        <div class="wppoppop-form-row" style="display:grid;grid-template-columns:2fr 1fr;gap:8px;">
            <div>
                <label class="wppoppop-form-label"><?php esc_html_e('Icon Glyph Preset', 'wppoppop'); ?></label>
                <select class="wppoppop-form-control wppoppop-gp-bind" id="prop-close-symbol" data-prop="closeSymbol">
                    <option value="times">&times; Classic Cross</option>
                    <option value="dashicons-no-alt">Thin Cross</option>
                    <option value="dashicons-dismiss">Circle Dismiss</option>
                    <option value="dashicons-arrow-left-alt2">Back Arrow</option>
                </select>
            </div>
            <div>
                <label class="wppoppop-form-label"><?php esc_html_e('Glyph Size (px)', 'wppoppop'); ?></label>
                <input type="number" class="wppoppop-form-control wppoppop-gp-bind" id="prop-close-size" data-prop="iconSize" value="20" min="10" max="80">
            </div>
        </div>
        <div class="wppoppop-form-group" style="margin-top:10px;">
            <label class="wppoppop-form-label"><?php esc_html_e('Dismiss Behavior', 'wppoppop'); ?></label>
            <select class="wppoppop-form-control wppoppop-gp-bind" id="prop-close-action" data-prop="closeAction">
                <option value="close"><?php esc_html_e('Close Popup Immediately', 'wppoppop'); ?></option>
                <option value="close_period"><?php esc_html_e('Dismiss for Browser Session', 'wppoppop'); ?></option>
                <option value="close_forever"><?php esc_html_e('Dismiss with Cookie Suppression', 'wppoppop'); ?></option>
            </select>
        </div>
        <div class="wppoppop-form-group" id="prop-close-cookie-wrap" style="display:none;margin-top:10px;">
            <label class="wppoppop-form-label"><?php esc_html_e('Cookie Suppression Days', 'wppoppop'); ?></label>
            <input type="number" class="wppoppop-form-control wppoppop-gp-bind" id="prop-close-cookie-days" data-prop="cookieDays" value="30" min="1" max="365">
        </div>
    </div>
</div>


<!-- 7. Font Awesome (icon) -->
<div class="wppoppop-elem-panel element-panel wppoppop-panel-specific" id="panel-elem-icon" style="display:none;">
    <div class="wppoppop-panel-card">
        <div class="wppoppop-panel-card-title"><?php esc_html_e('7. Font Awesome / Vector Icon', 'wppoppop'); ?></div>
        <div class="wppoppop-form-group">
            <label class="wppoppop-form-label"><?php esc_html_e('Icon Class (Font Awesome or Dashicons)', 'wppoppop'); ?></label>
            <input type="text" class="wppoppop-form-control wppoppop-gp-bind" id="prop-icon-class" data-prop="iconClass" value="dashicons-star-filled" placeholder="fas fa-star or dashicons-star-filled">
        </div>
        <div style="display:flex;gap:5px;flex-wrap:wrap;margin-top:8px;">
            <button type="button" class="button button-small wppoppop-icon-preset" data-icon="dashicons-star-filled">★ Star</button>
            <button type="button" class="button button-small wppoppop-icon-preset" data-icon="dashicons-yes-alt">✔ Check</button>
            <button type="button" class="button button-small wppoppop-icon-preset" data-icon="dashicons-heart">♥ Heart</button>
            <button type="button" class="button button-small wppoppop-icon-preset" data-icon="dashicons-bell">🔔 Bell</button>
            <button type="button" class="button button-small wppoppop-icon-preset" data-icon="dashicons-awards">🏆 Award</button>
        </div>
        <div class="wppoppop-form-row" style="margin-top:10px;display:grid;grid-template-columns:1fr 1fr;gap:8px;">
            <div>
                <label class="wppoppop-form-label"><?php esc_html_e('Icon Size (px)', 'wppoppop'); ?></label>
                <input type="number" class="wppoppop-form-control wppoppop-gp-bind" id="prop-icon-size" data-prop="iconSize" value="36" min="12" max="160">
            </div>
            <div>
                <label class="wppoppop-form-label"><?php esc_html_e('Rotation (0°–360°)', 'wppoppop'); ?></label>
                <input type="number" class="wppoppop-form-control wppoppop-gp-bind" id="prop-icon-rotate" data-prop="rotate" value="0" min="0" max="360">
            </div>
        </div>
    </div>
</div>


<!-- 8. Progress Bar (progress) -->
<div class="wppoppop-elem-panel element-panel wppoppop-panel-specific" id="panel-elem-progress" style="display:none;">
    <div class="wppoppop-panel-card">
        <div class="wppoppop-panel-card-title"><?php esc_html_e('8. Progress Bar Settings', 'wppoppop'); ?></div>
        <div class="wppoppop-form-row" style="display:grid;grid-template-columns:2fr 1fr;gap:8px;">
            <div>
                <label class="wppoppop-form-label"><?php esc_html_e('Progress Mode', 'wppoppop'); ?></label>
                <select class="wppoppop-form-control wppoppop-gp-bind" id="prop-progress-mode" data-prop="progressMode">
                    <option value="manual"><?php esc_html_e('Manual Percentage', 'wppoppop'); ?></option>
                    <option value="canvas_steps"><?php esc_html_e('Auto Canvas Step Sync', 'wppoppop'); ?></option>
                </select>
            </div>
            <div id="prop-progress-val-wrap">
                <label class="wppoppop-form-label"><?php esc_html_e('Fill (%)', 'wppoppop'); ?></label>
                <input type="number" class="wppoppop-form-control wppoppop-gp-bind" id="prop-progress-val" data-prop="progressValue" value="65" min="0" max="100">
            </div>
        </div>
        <div class="wppoppop-form-group" style="margin-top:10px;display:flex;flex-direction:column;gap:6px;">
            <label style="font-size:12px;display:flex;align-items:center;gap:6px;cursor:pointer;">
                <input type="checkbox" class="wppoppop-gp-bind" id="prop-progress-show-label" data-prop="showPercentLabel" checked> <?php esc_html_e('Show percentage text badge', 'wppoppop'); ?>
            </label>
            <label style="font-size:12px;display:flex;align-items:center;gap:6px;cursor:pointer;">
                <input type="checkbox" class="wppoppop-gp-bind" id="prop-progress-striped" data-prop="striped"> <?php esc_html_e('Animated diagonal stripes', 'wppoppop'); ?>
            </label>
        </div>
    </div>
</div>


<!-- 9. Button (button) -->
<div class="wppoppop-elem-panel element-panel wppoppop-panel-specific" id="panel-elem-button" style="display:none;">
    <div class="wppoppop-panel-card">
        <div class="wppoppop-panel-card-title"><?php esc_html_e('9. Button Label & Click Action', 'wppoppop'); ?></div>
        <div class="wppoppop-form-group">
            <label class="wppoppop-form-label"><?php esc_html_e('Button Label Text', 'wppoppop'); ?></label>
            <input type="text" class="wppoppop-form-control wppoppop-gp-bind" id="prop-button-text" data-prop="content" value="Click Here">
        </div>
        <div class="wppoppop-form-group" style="margin-top:10px;">
            <label class="wppoppop-form-label"><?php esc_html_e('Click Action Routing', 'wppoppop'); ?></label>
            <select class="wppoppop-form-control wppoppop-gp-bind" id="prop-button-action" data-prop="buttonAction">
                <option value="url"><?php esc_html_e('Open Destination URL', 'wppoppop'); ?></option>
                <option value="next_canvas"><?php esc_html_e('Advance to Next Canvas Step', 'wppoppop'); ?></option>
                <option value="prev_canvas"><?php esc_html_e('Return to Previous Canvas Step', 'wppoppop'); ?></option>
                <option value="close"><?php esc_html_e('Close Popup Modal', 'wppoppop'); ?></option>
            </select>
        </div>
        <div class="wppoppop-form-group" id="prop-button-url-wrap" style="margin-top:10px;">
            <label class="wppoppop-form-label"><?php esc_html_e('Destination URL', 'wppoppop'); ?></label>
            <input type="text" class="wppoppop-form-control wppoppop-gp-bind" id="prop-button-url" data-prop="url" placeholder="https://example.com">
        </div>
        <div class="wppoppop-form-group" style="margin-top:10px;display:flex;gap:12px;flex-wrap:wrap;">
            <label style="font-size:12px;display:flex;align-items:center;gap:6px;cursor:pointer;">
                <input type="checkbox" class="wppoppop-gp-bind" id="prop-button-blank" data-prop="targetBlank"> <?php esc_html_e('New tab (_blank)', 'wppoppop'); ?>
            </label>
            <label style="font-size:12px;display:flex;align-items:center;gap:6px;cursor:pointer;">
                <input type="checkbox" class="wppoppop-gp-bind" id="prop-button-nofollow" data-prop="nofollow"> <?php esc_html_e('rel="nofollow"', 'wppoppop'); ?>
            </label>
        </div>
    </div>
</div>


<!-- 26. Button (submit) -->
<div class="wppoppop-elem-panel element-panel wppoppop-panel-specific" id="panel-elem-submit" style="display:none;">
    <div class="wppoppop-panel-card">
        <div class="wppoppop-panel-card-title"><?php esc_html_e('26. Form Submit Button & Post-Submit Routing', 'wppoppop'); ?></div>
        <div class="wppoppop-form-row" style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
            <div>
                <label class="wppoppop-form-label"><?php esc_html_e('Submit Button Label', 'wppoppop'); ?></label>
                <input type="text" class="wppoppop-form-control wppoppop-gp-bind" id="prop-submit-text" data-prop="content" value="Submit Form">
            </div>
            <div>
                <label class="wppoppop-form-label"><?php esc_html_e('Loading Spinner Text', 'wppoppop'); ?></label>
                <input type="text" class="wppoppop-form-control wppoppop-gp-bind" id="prop-submit-loading" data-prop="loadingText" value="Submitting...">
            </div>
        </div>
        <div class="wppoppop-form-group" style="margin-top:10px;">
            <label class="wppoppop-form-label"><?php esc_html_e('On Successful Submission', 'wppoppop'); ?></label>
            <select class="wppoppop-form-control wppoppop-gp-bind" id="prop-submit-action" data-prop="submitAction">
                <option value="message"><?php esc_html_e('Display Confirmation Message', 'wppoppop'); ?></option>
                <option value="next_canvas"><?php esc_html_e('Advance to Next Canvas Step', 'wppoppop'); ?></option>
                <option value="redirect"><?php esc_html_e('Redirect to External URL', 'wppoppop'); ?></option>
            </select>
        </div>
        <div class="wppoppop-form-group" id="prop-submit-msg-wrap" style="margin-top:10px;">
            <label class="wppoppop-form-label"><?php esc_html_e('Confirmation Message', 'wppoppop'); ?></label>
            <textarea class="wppoppop-form-control wppoppop-gp-bind" id="prop-submit-message" data-prop="confirmMessage" rows="2" placeholder="<?php esc_attr_e('Thank you for subscribing!', 'wppoppop'); ?>"></textarea>
        </div>
        <div class="wppoppop-form-group" id="prop-submit-redir-wrap" style="display:none;margin-top:10px;">
            <label class="wppoppop-form-label"><?php esc_html_e('Redirect URL', 'wppoppop'); ?></label>
            <input type="text" class="wppoppop-form-control wppoppop-gp-bind" id="prop-submit-redirect" data-prop="redirectUrl" placeholder="https://...">
        </div>
    </div>
</div>
