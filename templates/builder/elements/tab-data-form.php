<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!-- DECOMPOSED TAB MODULE: DATA TAB (Form Family Elements 10-25) -->
<div class="wppoppop-insp-card" id="insp-section-gp-data-form" style="background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;">Data &amp; Value Ingestion</div>

    <div class="wppoppop-prop-row" style="margin-bottom:12px;">
        <label class="wppoppop-prop-label" for="prop-data-default" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">DEFAULT VALUE</label>
        <input type="text" id="prop-data-default" class="widefat wppoppop-input" placeholder="Initial default value..." style="font-size:12px;">
        <span class="wppoppop-gp-sublabel">Enter default value of the field.</span>
    </div>

    <div class="wppoppop-prop-row wppoppop-gp-switch-row" style="margin-bottom:12px;padding-top:8px;border-top:1px solid #1e293b;">
        <div>
            <label class="wppoppop-prop-label" for="prop-data-dynamic" style="display:block;font-size:11px;font-weight:700;color:#e2e8f0;text-transform:uppercase;">DYNAMIC DEFAULT VALUE</label>
            <span class="wppoppop-gp-sublabel">Populate from URL query parameter or cookie</span>
        </div>
        <label class="wppoppop-gp-switch">
            <input type="checkbox" id="prop-data-dynamic">
            <span class="wppoppop-gp-slider"></span>
        </label>
    </div>

    <div class="wppoppop-prop-row" id="insp-row-data-param" style="display:none;margin-bottom:12px;">
        <label class="wppoppop-prop-label" for="prop-data-param" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">URL PARAMETER / COOKIE NAME</label>
        <input type="text" id="prop-data-param" class="widefat wppoppop-input" placeholder="e.g. utm_source or email" style="font-size:12px;">
    </div>

    <div class="wppoppop-prop-row wppoppop-gp-switch-row" style="margin-bottom:12px;padding-top:8px;border-top:1px solid #1e293b;">
        <div>
            <label class="wppoppop-prop-label" for="prop-data-readonly" style="display:block;font-size:11px;font-weight:700;color:#e2e8f0;text-transform:uppercase;">READ ONLY</label>
            <span class="wppoppop-gp-sublabel">Prevent visitor from modifying field value</span>
        </div>
        <label class="wppoppop-gp-switch">
            <input type="checkbox" id="prop-data-readonly">
            <span class="wppoppop-gp-slider"></span>
        </label>
    </div>

    <div class="wppoppop-prop-row wppoppop-gp-switch-row" style="padding-top:8px;border-top:1px solid #1e293b;">
        <div>
            <label class="wppoppop-prop-label" for="prop-data-savedb" style="display:block;font-size:11px;font-weight:700;color:#e2e8f0;text-transform:uppercase;">SAVE TO DATABASE</label>
            <span class="wppoppop-gp-sublabel">Store submitted value in Leads database</span>
        </div>
        <label class="wppoppop-gp-switch">
            <input type="checkbox" id="prop-data-savedb" checked>
            <span class="wppoppop-gp-slider"></span>
        </label>
    </div>
</div>
