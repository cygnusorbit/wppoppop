<?php
if (!defined('ABSPATH')) exit;
?>
<!-- DATA & VALUE BINDING SUITE -->
<div class="wppoppop-insp-section" id="insp-section-form-data" style="background:#0f172a; border:1px solid #1e293b; border-radius:8px; padding:12px;">
    <div style="font-size:10px; font-weight:800; color:#38bdf8; text-transform:uppercase; margin-bottom:10px;">DATA &amp; VALUE BINDING</div>
    <div style="margin-bottom:10px;">
        <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px; text-transform:uppercase;">Default Value</label>
        <input type="text" id="prop-default-value" class="widefat wppoppop-input wppoppop-data-bind" data-data-prop="defaultValue" placeholder="Initial field value..." style="font-size:12px;" />
    </div>
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
        <label for="prop-dynamic-default" style="font-size:11px; font-weight:700; color:#e2e8f0; margin:0; cursor:pointer;">Dynamic Default (URL / Cookie)</label>
        <input type="checkbox" id="prop-dynamic-default" class="wppoppop-switch wppoppop-data-bind" data-data-prop="dynamicDefault" />
    </div>
    <div id="prop-dynamic-param-row" style="margin-bottom:10px;">
        <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px; text-transform:uppercase;">URL Parameter / Cookie Name</label>
        <input type="text" id="prop-dynamic-param" class="widefat wppoppop-input wppoppop-data-bind" data-data-prop="paramName" placeholder="e.g. utm_source or user_email" style="font-size:12px;" />
    </div>
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
        <label for="prop-readonly" style="font-size:11px; font-weight:700; color:#e2e8f0; margin:0; cursor:pointer;">Read Only Field</label>
        <input type="checkbox" id="prop-readonly" class="wppoppop-switch wppoppop-data-bind" data-data-prop="readOnly" />
    </div>
    <div style="display:flex; justify-content:space-between; align-items:center;">
        <label for="prop-save-to-db" style="font-size:11px; font-weight:700; color:#e2e8f0; margin:0; cursor:pointer;">Save to Database</label>
        <input type="checkbox" id="prop-save-to-db" class="wppoppop-switch wppoppop-data-bind" data-data-prop="saveToDb" checked />
    </div>
</div>
