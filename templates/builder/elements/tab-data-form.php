<?php
if (!defined('ABSPATH')) exit;
?>
<!-- DATA & VALUE BINDING SUITE -->
<div class="wppoppop-insp-section" id="insp-section-form-data" style="background:#0f172a; border:1px solid #1e293b; border-radius:8px; padding:12px;">
    <div style="font-size:10px; font-weight:800; color:#38bdf8; text-transform:uppercase; margin-bottom:10px;"><?php esc_html_e('DATA & VALUE BINDING', 'wppoppop'); ?></div>
    <div id="insp-row-data-default" style="margin-bottom:10px;">
        <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px; text-transform:uppercase;"><?php esc_html_e('Default Value', 'wppoppop'); ?></label>
        <input type="text" id="prop-default-value" class="widefat wppoppop-input wppoppop-data-bind" data-data-prop="defaultValue" placeholder="<?php esc_attr_e('Initial field value...', 'wppoppop'); ?>" style="font-size:12px;" />
    </div>
    <div id="insp-row-data-dynamic" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
        <label for="prop-dynamic-default" style="font-size:11px; font-weight:700; color:#e2e8f0; margin:0; cursor:pointer;"><?php esc_html_e('Dynamic Default (URL / Cookie)', 'wppoppop'); ?></label>
        <label class="wppoppop-insp-switch" title="<?php esc_attr_e('Toggle', 'wppoppop'); ?>"><input type="checkbox" id="prop-dynamic-default" class="wppoppop-data-bind" data-data-prop="dynamicDefault" /><span class="wppoppop-insp-slider"></span></label>
    </div>
    <div id="prop-dynamic-param-row" style="margin-bottom:10px;">
        <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px; text-transform:uppercase;"><?php esc_html_e('URL Parameter / Cookie Name', 'wppoppop'); ?></label>
        <input type="text" id="prop-dynamic-param" class="widefat wppoppop-input wppoppop-data-bind" data-data-prop="paramName" placeholder="<?php esc_attr_e('e.g. utm_source or user_email', 'wppoppop'); ?>" style="font-size:12px;" />
    </div>
    <div id="insp-row-data-readonly" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
        <label for="prop-readonly" style="font-size:11px; font-weight:700; color:#e2e8f0; margin:0; cursor:pointer;"><?php esc_html_e('Read Only Field', 'wppoppop'); ?></label>
        <label class="wppoppop-insp-switch" title="<?php esc_attr_e('Toggle', 'wppoppop'); ?>"><input type="checkbox" id="prop-readonly" class="wppoppop-data-bind" data-data-prop="readOnly" /><span class="wppoppop-insp-slider"></span></label>
    </div>
    <div id="insp-row-data-save" style="display:flex; justify-content:space-between; align-items:center;">
        <label for="prop-save-to-db" style="font-size:11px; font-weight:700; color:#e2e8f0; margin:0; cursor:pointer;"><?php esc_html_e('Save to Database', 'wppoppop'); ?></label>
        <label class="wppoppop-insp-switch" title="<?php esc_attr_e('Toggle', 'wppoppop'); ?>"><input type="checkbox" id="prop-save-to-db" class="wppoppop-data-bind" data-data-prop="saveToDb" checked /><span class="wppoppop-insp-slider"></span></label>
    </div>
</div>
