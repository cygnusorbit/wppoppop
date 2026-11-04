<?php
if (!defined('ABSPATH')) exit;
?>
<!-- DECOMPOSED MODULE 02: IMAGE (image) -->
<div class="element-panel wppoppop-panel-specific" id="panel-elem-image" data-elem-family="media" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;"><?php esc_html_e('2. Image Source & Fitting', 'wppoppop'); ?></div>
    <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e('IMAGE SOURCE URL', 'wppoppop'); ?></label>
    <div style="display:flex;gap:6px;margin-bottom:10px;">
        <input type="text" id="prop-image-url" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="src" placeholder="https://..." style="flex:1;font-size:12px;">
        <button type="button" id="wppoppop-btn-media-upload" class="button button-secondary wppoppop-media-lib-trigger" style="height:36px;padding:0 10px;font-size:11px;font-weight:700;white-space:nowrap;"><?php esc_html_e('WP Media', 'wppoppop'); ?></button>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px;">
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e('OBJECT FIT', 'wppoppop'); ?></label>
            <select id="prop-image-fit" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="objectFit" style="font-size:12px;">
                <option value="contain" selected><?php esc_html_e('Contain (Full)', 'wppoppop'); ?></option>
                <option value="cover"><?php esc_html_e('Cover (Crop)', 'wppoppop'); ?></option>
                <option value="fill"><?php esc_html_e('Stretch Fill', 'wppoppop'); ?></option>
                <option value="scale-down"><?php esc_html_e('Scale Down', 'wppoppop'); ?></option>
            </select>
        </div>
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e('ALT TEXT', 'wppoppop'); ?></label>
            <input type="text" id="prop-image-alt" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="alt" placeholder="<?php esc_attr_e('Describe image...', 'wppoppop'); ?>" style="font-size:12px;">
        </div>
    </div>
    <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e('OPTIONAL CLICK REDIRECT URL', 'wppoppop'); ?></label>
    <div style="display:grid;grid-template-columns:minmax(0,1fr) 112px;gap:8px;">
        <input type="text" id="prop-image-link" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="linkUrl" placeholder="https://example.com" style="font-size:12px;min-width:0;">
        <select id="prop-image-link-target" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="linkTarget" title="<?php esc_attr_e('Link target', 'wppoppop'); ?>" style="font-size:12px;">
            <option value="_self" selected><?php esc_html_e('Same tab', 'wppoppop'); ?></option>
            <option value="_blank"><?php esc_html_e('New tab', 'wppoppop'); ?></option>
        </select>
    </div>
</div>
