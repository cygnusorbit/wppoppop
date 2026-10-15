<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!-- DECOMPOSED MODULE 09: BUTTON / LINK / STEP TRIGGER (button / link / step_btn) -->
<div class="wppoppop-elem-panel element-panel wppoppop-panel-specific" id="panel-elem-button" data-elem-family="media" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;"><?php esc_html_e( '9. Button Label & Click Action Routing', 'wppoppop' ); ?></div>
    <div class="wppoppop-gp-2col-grid" style="margin-bottom:10px;">
        <div class="wppoppop-gp-col">
            <label for="prop-button-text" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'BUTTON LABEL TEXT', 'wppoppop' ); ?></label>
            <input type="text" class="widefat wppoppop-input wppoppop-gp-bind" id="prop-button-text" data-prop="content" value="Click Here" style="font-size:12px;">
        </div>
        <div class="wppoppop-gp-col">
            <label for="prop-button-icon" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'ICON CLASS (OPT)', 'wppoppop' ); ?></label>
            <input type="text" class="widefat wppoppop-input wppoppop-gp-bind" id="prop-button-icon" data-prop="buttonIcon" placeholder="dashicons-arrow-right-alt" style="font-size:12px;">
        </div>
    </div>
    <div style="margin-bottom:10px;">
        <label for="prop-button-action" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'CLICK ACTION ROUTING', 'wppoppop' ); ?></label>
        <select class="widefat wppoppop-input wppoppop-gp-bind" id="prop-button-action" data-prop="buttonAction" style="font-size:12px;">
            <option value="url"><?php esc_html_e( 'Open Destination URL', 'wppoppop' ); ?></option>
            <option value="next_canvas"><?php esc_html_e( 'Advance to Next Canvas Step', 'wppoppop' ); ?></option>
            <option value="prev_canvas"><?php esc_html_e( 'Return to Previous Canvas Step', 'wppoppop' ); ?></option>
            <option value="goto_canvas"><?php esc_html_e( 'Jump to Specific Canvas Step', 'wppoppop' ); ?></option>
            <option value="close"><?php esc_html_e( 'Close Popup Modal', 'wppoppop' ); ?></option>
        </select>
    </div>
    <div id="prop-button-goto-wrap" style="display:none;margin-bottom:10px;">
        <label for="prop-button-goto-canvas" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'TARGET CANVAS STEP #', 'wppoppop' ); ?></label>
        <input type="number" class="widefat wppoppop-input wppoppop-gp-bind" id="prop-button-goto-canvas" data-prop="gotoCanvas" value="2" min="1" max="20" style="font-size:12px;">
    </div>
    <div id="prop-button-url-wrap" style="margin-bottom:10px;">
        <label for="prop-button-url" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'DESTINATION URL', 'wppoppop' ); ?></label>
        <input type="text" class="widefat wppoppop-input wppoppop-gp-bind" id="prop-button-url" data-prop="url" placeholder="https://example.com" style="font-size:12px;margin-bottom:8px;">
        <div class="wppoppop-gp-2col-grid">
            <label style="font-size:11px;color:#cbd5e1;display:flex;align-items:center;gap:6px;cursor:pointer;">
                <input type="checkbox" class="wppoppop-gp-bind" id="prop-button-blank" data-prop="targetBlank"> <?php esc_html_e( 'New tab (_blank)', 'wppoppop' ); ?>
            </label>
            <label style="font-size:11px;color:#cbd5e1;display:flex;align-items:center;gap:6px;cursor:pointer;">
                <input type="checkbox" class="wppoppop-gp-bind" id="prop-button-nofollow" data-prop="nofollow"> <?php esc_html_e( 'rel="nofollow"', 'wppoppop' ); ?>
            </label>
        </div>
    </div>
</div>
