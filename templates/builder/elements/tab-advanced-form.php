<?php
if (!defined('ABSPATH')) exit;
?>
<div class="wppoppop-insp-section" id="insp-section-form-advanced" style="margin-bottom:12px;">
    <div class="wppoppop-insp-section-title">FIELD IDENTIFIER &amp; MASK</div>
    <div class="wppoppop-prop-row" style="margin-bottom:10px;">
        <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px; text-transform:uppercase;">ID / Merge Token Name</label>
        <input type="text" id="prop-field-name" class="widefat wppoppop-input wppoppop-adv-bind" data-adv-prop="fieldName" placeholder="e.g. email or first_name" style="font-size:12px;" />
        <small style="font-size:10px; color:#64748b; display:block; margin-top:3px;">Usable as <code>{field_name}</code> merge token in webhooks and emails.</small>
    </div>
    <div class="wppoppop-prop-row">
        <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px; text-transform:uppercase;">Input Mask</label>
        <input type="text" id="prop-input-mask" class="widefat wppoppop-input wppoppop-adv-bind" data-adv-prop="inputMask" placeholder="e.g. (999) 999-9999" style="font-size:12px;" />
    </div>
</div>

<div class="wppoppop-insp-section" id="insp-section-form-filters" style="margin-bottom:12px;">
    <div class="wppoppop-insp-section-title">SANITIZATION FILTERS</div>
    <div class="wppoppop-prop-grid-2" style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
        <label class="wppoppop-check-label" style="font-size:11px; color:#cbd5e1; cursor:pointer;"><input type="checkbox" class="prop-filter-chk" value="trim" /> Trim Whitespace</label>
        <label class="wppoppop-check-label" style="font-size:11px; color:#cbd5e1; cursor:pointer;"><input type="checkbox" class="prop-filter-chk" value="striptags" /> Strip Tags</label>
        <label class="wppoppop-check-label" style="font-size:11px; color:#cbd5e1; cursor:pointer;"><input type="checkbox" class="prop-filter-chk" value="alpha" /> Alpha Only</label>
        <label class="wppoppop-check-label" style="font-size:11px; color:#cbd5e1; cursor:pointer;"><input type="checkbox" class="prop-filter-chk" value="digits" /> Digits Only</label>
        <label class="wppoppop-check-label" style="font-size:11px; color:#cbd5e1; cursor:pointer;"><input type="checkbox" class="prop-filter-chk" value="lowercase" /> Lowercase</label>
        <label class="wppoppop-check-label" style="font-size:11px; color:#cbd5e1; cursor:pointer;"><input type="checkbox" class="prop-filter-chk" value="uppercase" /> Uppercase</label>
    </div>
</div>

<div class="wppoppop-insp-section" id="insp-section-form-validators">
    <div class="wppoppop-insp-section-title">VALIDATION RULES</div>
    <div class="wppoppop-prop-grid-2" style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:10px;">
        <label class="wppoppop-check-label" style="font-size:11px; color:#cbd5e1; cursor:pointer;"><input type="checkbox" class="prop-validator-chk" value="required" /> Required *</label>
        <label class="wppoppop-check-label" style="font-size:11px; color:#cbd5e1; cursor:pointer;"><input type="checkbox" class="prop-validator-chk" value="email" /> RFC Email</label>
        <label class="wppoppop-check-label" style="font-size:11px; color:#cbd5e1; cursor:pointer;"><input type="checkbox" class="prop-validator-chk" value="equal" /> Equal Match</label>
        <label class="wppoppop-check-label" style="font-size:11px; color:#cbd5e1; cursor:pointer;"><input type="checkbox" class="prop-validator-chk" value="inarray" /> In Array</label>
        <label class="wppoppop-check-label" style="font-size:11px; color:#cbd5e1; cursor:pointer;"><input type="checkbox" class="prop-validator-chk" value="noduplicates" /> No Duplicates</label>
    </div>
    <div class="wppoppop-prop-row">
        <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px; text-transform:uppercase;">Custom Error Message</label>
        <input type="text" id="prop-validator-error" class="widefat wppoppop-input wppoppop-adv-bind" data-adv-prop="errorMessage" placeholder="Please enter a valid value..." style="font-size:12px;" />
    </div>
</div>
