<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!-- DECOMPOSED MODULE 02: IMAGE (image) -->
<div class="wppoppop-elem-panel element-panel wppoppop-panel-specific" id="panel-elem-image" data-elem-family="media" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;"><?php esc_html_e( '2. Image Source, Fit & Click Action', 'wppoppop' ); ?></div>
    <div style="margin-bottom:10px;">
        <label for="prop-image-url" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'IMAGE URL / MEDIA LIBRARY', 'wppoppop' ); ?></label>
        <div style="display:flex;gap:6px;">
            <input type="text" class="widefat wppoppop-input wppoppop-gp-bind" id="prop-image-url" data-prop="src" placeholder="https://..." style="flex:1;font-size:12px;">
            <button type="button" class="button button-secondary" id="prop-image-media-btn" title="<?php esc_attr_e( 'Select from WordPress Media Library', 'wppoppop' ); ?>">
                <span class="dashicons dashicons-admin-media" style="margin-top:3px;"></span>
            </button>
        </div>
    </div>
    <div class="wppoppop-gp-2col-grid" style="margin-bottom:10px;">
        <div class="wppoppop-gp-col">
            <label for="prop-image-alt" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'ALT TEXT', 'wppoppop' ); ?></label>
            <input type="text" class="widefat wppoppop-input wppoppop-gp-bind" id="prop-image-alt" data-prop="alt" placeholder="<?php esc_attr_e( 'Image description', 'wppoppop' ); ?>" style="font-size:12px;">
        </div>
        <div class="wppoppop-gp-col">
            <label for="prop-image-fit" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'OBJECT FIT', 'wppoppop' ); ?></label>
            <select class="widefat wppoppop-input wppoppop-gp-bind" id="prop-image-fit" data-prop="objectFit" style="font-size:12px;">
                <option value="contain"><?php esc_html_e( 'Contain', 'wppoppop' ); ?></option>
                <option value="cover"><?php esc_html_e( 'Cover (Crop)', 'wppoppop' ); ?></option>
                <option value="fill"><?php esc_html_e( 'Fill (Stretch)', 'wppoppop' ); ?></option>
                <option value="scale-down"><?php esc_html_e( 'Scale Down', 'wppoppop' ); ?></option>
            </select>
        </div>
    </div>
    <div style="margin-bottom:8px;">
        <label for="prop-image-action" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'ON CLICK ACTION', 'wppoppop' ); ?></label>
        <select class="widefat wppoppop-input wppoppop-gp-bind" id="prop-image-action" data-prop="imageAction" style="font-size:12px;">
            <option value="none"><?php esc_html_e( 'None (Static Image)', 'wppoppop' ); ?></option>
            <option value="url"><?php esc_html_e( 'Open Destination URL', 'wppoppop' ); ?></option>
            <option value="next_canvas"><?php esc_html_e( 'Advance to Next Canvas Step', 'wppoppop' ); ?></option>
            <option value="close"><?php esc_html_e( 'Close Popup Modal', 'wppoppop' ); ?></option>
        </select>
    </div>
    <div id="prop-image-link-wrap" style="display:none;margin-top:8px;">
        <label for="prop-image-link-url" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'DESTINATION URL', 'wppoppop' ); ?></label>
        <input type="text" class="widefat wppoppop-input wppoppop-gp-bind" id="prop-image-link-url" data-prop="linkUrl" placeholder="https://example.com" style="font-size:12px;margin-bottom:6px;">
        <label style="font-size:11px;color:#cbd5e1;display:flex;align-items:center;gap:6px;cursor:pointer;">
            <input type="checkbox" class="wppoppop-gp-bind" id="prop-image-blank" data-prop="targetBlank"> <?php esc_html_e( 'Open link in new tab (_blank)', 'wppoppop' ); ?>
        </label>
    </div>
</div>
