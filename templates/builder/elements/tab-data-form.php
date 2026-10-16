<?php
if (!defined('ABSPATH')) exit;
?>
<div class="wppoppop-insp-section" id="insp-section-form-data">
    <div class="wppoppop-insp-section-title">DATA &amp; VALUE BINDING</div>
    <div class="wppoppop-prop-row" style="margin-bottom:10px;">
        <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px; text-transform:uppercase;">Default Value</label>
        <input type="text" id="prop-default-value" class="widefat wppoppop-input wppoppop-data-bind" data-data-prop="defaultValue" placeholder="Initial field value..." style="font-size:12px;" />
    </div>
    <div class="wppoppop-prop-row wppoppop-switch-row" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
        <label for="prop-dynamic-default" style="font-size:11px; font-weight:700; color:#e2e8f0; margin:0;">Dynamic Default (URL / Cookie)</label>
        <input type="checkbox" id="prop-dynamic-default" class="wppoppop-switch wppoppop-data-bind" data-data-prop="dynamicDefault" />
    </div>
    <div class="wppoppop-prop-row" id="prop-dynamic-param-row" style="display:none; margin-bottom:10px;">
        <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px; text-transform:uppercase;">URL Parameter / Cookie Name</label>
        <input type="text" id="prop-dynamic-param" class="widefat wppoppop-input wppoppop-data-bind" data-data-prop="paramName" placeholder="e.g. utm_source or user_email" style="font-size:12px;" />
    </div>
    <div class="wppoppop-prop-row wppoppop-switch-row" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
        <label for="prop-readonly" style="font-size:11px; font-weight:700; color:#e2e8f0; margin:0;">Read Only</label>
        <input type="checkbox" id="prop-readonly" class="wppoppop-switch wppoppop-data-bind" data-data-prop="readOnly" />
    </div>
    <div class="wppoppop-prop-row wppoppop-switch-row" style="display:flex; justify-content:space-between; align-items:center;">
        <label for="prop-save-to-db" style="font-size:11px; font-weight:700; color:#e2e8f0; margin:0;">Save to Database</label>
        <input type="checkbox" id="prop-save-to-db" class="wppoppop-switch wppoppop-data-bind" data-data-prop="saveToDb" checked />
    </div>
</div>
