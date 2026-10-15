<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!-- DECOMPOSED MODULE 26: FORM SUBMIT BUTTON (submit / submit_btn) -->
<div class="wppoppop-elem-panel element-panel wppoppop-panel-specific" id="panel-elem-submit" data-elem-family="media" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;"><?php esc_html_e( '26. Submit Button & Post-Submit Routing', 'wppoppop' ); ?></div>
    <div class="wppoppop-gp-2col-grid" style="margin-bottom:10px;">
        <div class="wppoppop-gp-col">
            <label for="prop-submit-text" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'SUBMIT BUTTON LABEL', 'wppoppop' ); ?></label>
            <input type="text" class="widefat wppoppop-input wppoppop-gp-bind" id="prop-submit-text" data-prop="content" value="Submit Form" style="font-size:12px;">
        </div>
        <div class="wppoppop-gp-col">
            <label for="prop-submit-loading" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'LOADING SPINNER TEXT', 'wppoppop' ); ?></label>
            <input type="text" class="widefat wppoppop-input wppoppop-gp-bind" id="prop-submit-loading" data-prop="loadingText" value="Submitting..." style="font-size:12px;">
        </div>
    </div>
    <div style="margin-bottom:10px;">
        <label for="prop-submit-icon" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'BUTTON ICON CLASS (OPTIONAL)', 'wppoppop' ); ?></label>
        <input type="text" class="widefat wppoppop-input wppoppop-gp-bind" id="prop-submit-icon" data-prop="buttonIcon" placeholder="dashicons-yes-alt" style="font-size:12px;">
    </div>
    <div style="margin-bottom:10px;">
        <label for="prop-submit-action" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'ON SUCCESSFUL SUBMISSION', 'wppoppop' ); ?></label>
        <select class="widefat wppoppop-input wppoppop-gp-bind" id="prop-submit-action" data-prop="submitAction" style="font-size:12px;">
            <option value="message"><?php esc_html_e( 'Display Confirmation Message', 'wppoppop' ); ?></option>
            <option value="next_canvas"><?php esc_html_e( 'Advance to Next Canvas Step', 'wppoppop' ); ?></option>
            <option value="goto_canvas"><?php esc_html_e( 'Jump to Specific Canvas Step', 'wppoppop' ); ?></option>
            <option value="redirect"><?php esc_html_e( 'Redirect to External URL', 'wppoppop' ); ?></option>
            <option value="close"><?php esc_html_e( 'Close Popup Immediately', 'wppoppop' ); ?></option>
        </select>
    </div>
    <div id="prop-submit-msg-wrap" style="margin-bottom:10px;">
        <label for="prop-submit-message" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'CONFIRMATION MESSAGE', 'wppoppop' ); ?></label>
        <textarea class="widefat wppoppop-input wppoppop-gp-bind" id="prop-submit-message" data-prop="confirmMessage" rows="2" placeholder="<?php esc_attr_e( 'Thank you for subscribing!', 'wppoppop' ); ?>" style="font-size:12px;"></textarea>
    </div>
    <div id="prop-submit-goto-wrap" style="display:none;margin-bottom:10px;">
        <label for="prop-submit-goto-canvas" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'TARGET CANVAS STEP #', 'wppoppop' ); ?></label>
        <input type="number" class="widefat wppoppop-input wppoppop-gp-bind" id="prop-submit-goto-canvas" data-prop="gotoCanvas" value="2" min="1" max="20" style="font-size:12px;">
    </div>
    <div id="prop-submit-redir-wrap" style="display:none;margin-bottom:10px;">
        <label for="prop-submit-redirect" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'REDIRECT URL', 'wppoppop' ); ?></label>
        <input type="text" class="widefat wppoppop-input wppoppop-gp-bind" id="prop-submit-redirect" data-prop="redirectUrl" placeholder="https://..." style="font-size:12px;">
    </div>
</div>
