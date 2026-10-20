<?php
if (!defined('ABSPATH')) exit;
?>
<!-- ADVANCED FIELD IDENTIFIER, FILTERS & VALIDATORS SUITE -->
<div class="wppoppop-insp-section" id="insp-section-form-advanced" style="background:#0f172a; border:1px solid #1e293b; border-radius:8px; padding:12px; margin-bottom:12px;">
    <div style="font-size:10px; font-weight:800; color:#38bdf8; text-transform:uppercase; margin-bottom:10px;">FIELD IDENTIFIER</div>
    <div style="margin-bottom:10px;">
        <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px; text-transform:uppercase;">ID / Merge Token Name</label>
        <input type="text" id="prop-field-name" class="widefat wppoppop-input wppoppop-adv-bind" data-adv-prop="fieldName" placeholder="auto: layer-name-a1b2c3" style="font-size:12px;" />
        <small style="font-size:10px; color:#64748b; display:block; margin-top:3px;">Use as <code>{field_name}</code> in email notifications &amp; CRM webhooks.</small>
    </div>
    <div class="wppoppop-input-mask-row" style="display:none !important;" aria-hidden="true">
        <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px; text-transform:uppercase;">Input Mask</label>
        <input type="text" id="prop-input-mask" class="widefat wppoppop-input wppoppop-adv-bind" data-adv-prop="inputMask" placeholder="e.g. (999) 999-9999" style="font-size:12px;" />
    </div>
</div>

<div class="wppoppop-insp-section" id="insp-section-form-filters" style="background:#0f172a; border:1px solid #1e293b; border-radius:8px; padding:12px; margin-bottom:12px;">
    <div style="font-size:10px; font-weight:800; color:#38bdf8; text-transform:uppercase; margin-bottom:8px;">SANITIZATION FILTERS</div>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
        <label style="font-size:11px; color:#cbd5e1; cursor:pointer; display:flex; align-items:center; gap:6px;"><input type="checkbox" class="prop-filter-chk" value="trim" /> Trim Whitespace</label>
        <label style="font-size:11px; color:#cbd5e1; cursor:pointer; display:flex; align-items:center; gap:6px;"><input type="checkbox" class="prop-filter-chk" value="striptags" /> Strip Tags</label>
        <label style="font-size:11px; color:#cbd5e1; cursor:pointer; display:flex; align-items:center; gap:6px;"><input type="checkbox" class="prop-filter-chk" value="alpha" /> Alpha Only</label>
        <label style="font-size:11px; color:#cbd5e1; cursor:pointer; display:flex; align-items:center; gap:6px;"><input type="checkbox" class="prop-filter-chk" value="digits" /> Digits Only</label>
        <label style="font-size:11px; color:#cbd5e1; cursor:pointer; display:flex; align-items:center; gap:6px;"><input type="checkbox" class="prop-filter-chk" value="lowercase" /> Lowercase</label>
        <label style="font-size:11px; color:#cbd5e1; cursor:pointer; display:flex; align-items:center; gap:6px;"><input type="checkbox" class="prop-filter-chk" value="uppercase" /> Uppercase</label>
    </div>
</div>

<div class="wppoppop-insp-section" id="insp-section-form-validators" style="background:#0f172a; border:1px solid #1e293b; border-radius:8px; padding:12px;">
    <div style="font-size:10px; font-weight:800; color:#38bdf8; text-transform:uppercase; margin-bottom:8px;">VALIDATION RULES</div>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:10px;">
        <label style="font-size:11px; color:#cbd5e1; cursor:pointer; display:flex; align-items:center; gap:6px;"><input type="checkbox" class="prop-validator-chk" value="required" /> Required *</label>
        <label style="font-size:11px; color:#cbd5e1; cursor:pointer; display:flex; align-items:center; gap:6px;"><input type="checkbox" class="prop-validator-chk" value="email" /> RFC Email</label>
        <label style="font-size:11px; color:#cbd5e1; cursor:pointer; display:flex; align-items:center; gap:6px;"><input type="checkbox" class="prop-validator-chk" value="equal" /> Equal Match</label>
        <label style="font-size:11px; color:#cbd5e1; cursor:pointer; display:flex; align-items:center; gap:6px;"><input type="checkbox" class="prop-validator-chk" value="inarray" /> In Array</label>
        <label style="font-size:11px; color:#cbd5e1; cursor:pointer; display:flex; align-items:center; gap:6px;"><input type="checkbox" class="prop-validator-chk" value="noduplicates" /> No Duplicates</label>
    </div>
    <div id="wppoppop-val-extra-equal" class="wppoppop-val-extra" style="display:none; margin-bottom:10px;">
        <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px; text-transform:uppercase;">Equal Match &mdash; Value Must Equal</label>
        <input type="text" id="prop-validator-equal" class="widefat wppoppop-input" placeholder="Exact value, e.g. YES" style="font-size:12px;" />
    </div>
    <div id="wppoppop-val-extra-inarray" class="wppoppop-val-extra" style="display:none; margin-bottom:10px;">
        <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px; text-transform:uppercase;">In Array &mdash; Allowed Values (comma separated)</label>
        <input type="text" id="prop-validator-list" class="widefat wppoppop-input" placeholder="red, green, blue" style="font-size:12px;" />
    </div>
    <div>
        <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px; text-transform:uppercase;">Custom Validation Error Message</label>
        <input type="text" id="prop-validator-error" class="widefat wppoppop-input wppoppop-adv-bind" data-adv-prop="errorMessage" placeholder="Please enter a valid value..." style="font-size:12px;" />
    </div>
</div>
