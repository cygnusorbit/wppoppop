<?php
if (!defined('ABSPATH')) exit;
?>
<!-- DECOMPOSED MODULE 26: FORM SUBMIT BUTTON (submit / submit_btn) -->
<div class="element-panel wppoppop-panel-specific" id="panel-elem-submit" data-elem-family="media" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#10b981;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;">26. Submit Button &amp; Post-Submit Routing</div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px;">
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">SUBMIT LABEL</label>
            <input type="text" id="prop-submit-label" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="content" placeholder="Subscribe Now" style="font-size:12px;">
        </div>
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">LOADING TEXT</label>
            <input type="text" id="prop-submit-loading" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="loadingText" placeholder="Sending..." style="font-size:12px;">
        </div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px;">
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">POST-SUBMIT ACTION</label>
            <select id="prop-submit-action" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="submitAction" style="font-size:12px;">
                <option value="default">Show Confirmation &amp; Close</option>
                <option value="next_canvas">Go to Canvas Step</option>
                <option value="redirect">Redirect to URL</option>
            </select>
        </div>
        <div id="prop-submit-canvas-wrap">
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">NEXT CANVAS #</label>
            <select id="prop-submit-canvas" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="goto_canvas" style="font-size:12px;"><option value="">Select a canvas...</option></select>
            <span style="display:block;font-size:9px;color:#64748b;margin-top:3px;">Canvas shown after a successful submit.</span>
        </div>
    </div>
    <div id="prop-submit-redirect-wrap">
        <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">POST-SUBMIT REDIRECT URL</label>
        <input type="text" id="prop-submit-redirect" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="redirectUrl" placeholder="https://example.com/thank-you" style="font-size:12px;">
    </div>
    <div id="prop-submit-confirm-wrap" style="margin-top:10px;">
        <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">CONFIRMATION MESSAGE</label>
        <input type="text" id="prop-submit-confirm" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="confirmMessage" placeholder="Thank you for your submission!" style="font-size:12px;">
    </div>
</div>
