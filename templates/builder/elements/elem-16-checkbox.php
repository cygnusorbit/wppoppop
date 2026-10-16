<?php
if (!defined('ABSPATH')) exit;
?>
<!-- DECOMPOSED MODULE 16: CHECKBOX / CONSENT (checkbox / checkboxes) -->
<div class="element-panel wppoppop-panel-specific" id="panel-elem-checkbox" data-elem-family="form" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;">16. Checkbox &amp; GDPR Consent</div>
    <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">CHECKBOX OPTIONS / CONSENT LABEL (ONE PER LINE)</label>
    <textarea id="prop-checkbox-options" rows="3" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="optionsList" placeholder="I agree to the Terms &amp; Privacy Policy" style="margin-bottom:10px;font-size:12px;"></textarea>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">LAYOUT DIRECTION</label>
            <select id="prop-checkbox-layout" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="layout" style="font-size:12px;">
                <option value="vertical">Vertical (Stacked)</option>
                <option value="horizontal">Horizontal (Inline)</option>
            </select>
        </div>
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">TERMS / PRIVACY URL</label>
            <input type="text" id="prop-checkbox-terms" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="termsUrl" placeholder="https://example.com/privacy" style="font-size:12px;">
        </div>
    </div>
</div>
