<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!-- DECOMPOSED MODULE 16: CHECKBOX (checkbox / checkboxes) -->
<div class="wppoppop-elem-panel element-panel wppoppop-panel-specific" id="panel-elem-checkbox" data-elem-family="form" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;"><?php esc_html_e( '16. Checkbox / Consent Settings', 'wppoppop' ); ?></div>
    <div style="margin-bottom:10px;">
        <label for="prop-checkbox-label" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'CHECKBOX / GDPR CONSENT LABEL', 'wppoppop' ); ?></label>
        <input type="text" id="prop-checkbox-label" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="checkboxLabel" value="I agree to the Terms and Privacy Policy" style="font-size:12px;">
    </div>
    <div style="margin-bottom:10px;">
        <label for="prop-checkbox-options" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'MULTI-CHECKBOX OPTIONS (OPTIONAL)', 'wppoppop' ); ?></label>
        <textarea id="prop-checkbox-options" rows="2" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="options" placeholder="<?php esc_attr_e( 'Leave blank for single checkbox, or list options...', 'wppoppop' ); ?>" style="font-size:12px;"></textarea>
    </div>
    <div class="wppoppop-gp-2col-grid" style="align-items:center;">
        <div class="wppoppop-gp-col">
            <label for="prop-checkbox-layout" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'LAYOUT', 'wppoppop' ); ?></label>
            <select id="prop-checkbox-layout" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="checkboxLayout" style="font-size:12px;">
                <option value="vertical"><?php esc_html_e( 'Vertical (Stacked)', 'wppoppop' ); ?></option>
                <option value="horizontal"><?php esc_html_e( 'Horizontal (Inline)', 'wppoppop' ); ?></option>
            </select>
        </div>
        <div class="wppoppop-gp-col" style="padding-top:14px;">
            <label style="display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:600;color:#cbd5e1;cursor:pointer;">
                <input type="checkbox" id="prop-checkbox-checked" class="wppoppop-gp-bind" data-prop="checkedDefault" value="1"> <?php esc_html_e( 'Checked by Default', 'wppoppop' ); ?>
            </label>
        </div>
    </div>
</div>
