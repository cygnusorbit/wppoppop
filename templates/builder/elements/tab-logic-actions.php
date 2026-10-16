<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<!-- Decomposed Module: Element Action Routing, Cookie Suppression & Merge Tokens -->
<div class="wppoppop-insp-section" id="wppoppop-logic-actions-module" style="margin-bottom:14px;">
    <div class="wppoppop-insp-section-header" style="margin-bottom:8px;">
        <h4 style="margin:0;font-size:11px;font-weight:700;color:#475569;text-transform:uppercase;"><?php esc_html_e('Element Actions & Routing', 'wppoppop'); ?></h4>
    </div>

    <!-- Contextual: Click Redirect URL (Image, Icon, Button, Shape) -->
    <div class="wppoppop-logic-action-group" data-logic-scope="click-url" style="margin-bottom:10px;">
        <label style="display:block;font-size:11px;font-weight:600;color:#475569;margin-bottom:4px;"><?php esc_html_e('On-Click Redirect URL', 'wppoppop'); ?></label>
        <input type="url" id="prop-action-url" class="widefat wppoppop-gp-bind" data-prop="click_url" placeholder="https://..." style="font-size:12px;margin-bottom:6px;">
        <label style="font-size:11.5px;color:#334155;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
            <input type="checkbox" id="prop-action-blank" class="wppoppop-gp-bind" data-prop="click_target" value="_blank">
            <?php esc_html_e('Open link in new tab (_blank)', 'wppoppop'); ?>
        </label>
    </div>

    <!-- Contextual: Close Icon Cookie Suppression (Close Element) -->
    <div class="wppoppop-logic-action-group" data-logic-scope="close-cookie" style="display:none;margin-bottom:10px;">
        <label style="display:block;font-size:11px;font-weight:600;color:#475569;margin-bottom:4px;"><?php esc_html_e('Close Suppression Cookie (Days)', 'wppoppop'); ?></label>
        <input type="number" id="prop-logic-cookie-days" class="widefat wppoppop-gp-bind" data-prop="cookie_days" min="0" max="365" value="0" style="font-size:12px;">
    </div>

    <!-- Contextual: Post-Submit Routing (Submit Button Element) -->
    <div class="wppoppop-logic-action-group" data-logic-scope="submit-routing" style="display:none;margin-bottom:10px;">
        <label style="display:block;font-size:11px;font-weight:600;color:#475569;margin-bottom:4px;"><?php esc_html_e('Post-Submit Routing', 'wppoppop'); ?></label>
        <select id="prop-logic-submit-action" class="widefat wppoppop-gp-bind" data-prop="submit_action" style="font-size:12px;margin-bottom:6px;">
            <option value="message"><?php esc_html_e('Show Confirmation / Close', 'wppoppop'); ?></option>
            <option value="next_canvas"><?php esc_html_e('Advance to Next Canvas Step', 'wppoppop'); ?></option>
            <option value="goto_canvas"><?php esc_html_e('Jump to Specific Canvas Step', 'wppoppop'); ?></option>
            <option value="redirect"><?php esc_html_e('Redirect to External URL', 'wppoppop'); ?></option>
        </select>
        <div style="display:grid;grid-template-columns:1fr 2fr;gap:6px;">
            <input type="number" id="prop-logic-goto-canvas" class="widefat wppoppop-gp-bind" data-prop="goto_canvas" min="1" max="10" placeholder="Step #" style="font-size:12px;">
            <input type="url" id="prop-logic-redirect-url" class="widefat wppoppop-gp-bind" data-prop="redirect_url" placeholder="https://redirect-url..." style="font-size:12px;">
        </div>
    </div>

    <!-- Universal: Custom Element CSS Selectors in Logic Tab -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px;">
        <div>
            <label style="display:block;font-size:10.5px;font-weight:600;color:#64748b;margin-bottom:3px;"><?php esc_html_e('Custom CSS Class', 'wppoppop'); ?></label>
            <input type="text" id="prop-logic-css-class" class="widefat wppoppop-gp-bind" data-prop="custom_css_class" placeholder="my-layer-class" style="font-size:12px;">
        </div>
        <div>
            <label style="display:block;font-size:10.5px;font-weight:600;color:#64748b;margin-bottom:3px;"><?php esc_html_e('Custom CSS ID', 'wppoppop'); ?></label>
            <input type="text" id="prop-logic-css-id" class="widefat wppoppop-gp-bind" data-prop="custom_css_id" placeholder="my-layer-id" style="font-size:12px;">
        </div>
    </div>

    <!-- Universal: Dynamic Personalization Merge Tokens -->
    <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;padding:8px 10px;">
        <label style="display:block;font-size:10.5px;font-weight:700;color:#475569;margin-bottom:4px;"><?php esc_html_e('Dynamic Personalization Merge Tokens', 'wppoppop'); ?></label>
        <div style="display:flex;flex-wrap:wrap;gap:5px;">
            <span class="wppoppop-token-pill wppoppop-tag-chip" data-token="{email}" style="background:#1e293b;color:#38bdf8;font-family:monospace;font-size:10.5px;padding:2px 6px;border-radius:4px;cursor:pointer;">{email}</span>
            <span class="wppoppop-token-pill wppoppop-tag-chip" data-token="{name}" style="background:#1e293b;color:#38bdf8;font-family:monospace;font-size:10.5px;padding:2px 6px;border-radius:4px;cursor:pointer;">{name}</span>
            <span class="wppoppop-token-pill wppoppop-tag-chip" data-token="{phone}" style="background:#1e293b;color:#38bdf8;font-family:monospace;font-size:10.5px;padding:2px 6px;border-radius:4px;cursor:pointer;">{phone}</span>
            <span class="wppoppop-token-pill wppoppop-tag-chip" data-token="{rating}" style="background:#1e293b;color:#38bdf8;font-family:monospace;font-size:10.5px;padding:2px 6px;border-radius:4px;cursor:pointer;">{rating}</span>
        </div>
    </div>
</div>
