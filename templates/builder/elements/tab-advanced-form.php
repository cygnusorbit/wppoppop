<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!-- DECOMPOSED TAB MODULE: ADVANCED TAB (Form Family Elements 10-25) -->
<div class="wppoppop-insp-card" id="insp-section-gp-advanced-form" style="background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;">Field ID, Filters &amp; Validators</div>

    <div class="wppoppop-prop-row" style="margin-bottom:12px;">
        <label class="wppoppop-prop-label" for="prop-adv-id" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">ID / MERGE TOKEN NAME</label>
        <input type="text" id="prop-adv-id" class="widefat wppoppop-input" placeholder="e.g. email or first_name" style="font-size:12px;">
        <span class="wppoppop-gp-sublabel">Use in confirmation messages or webhooks as {id}.</span>
    </div>

    <div class="wppoppop-prop-row" id="insp-row-adv-mask" style="margin-bottom:12px;">
        <label class="wppoppop-prop-label" for="prop-adv-mask" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">INPUT MASK</label>
        <input type="text" id="prop-adv-mask" class="widefat wppoppop-input" placeholder="e.g. (999) 999-9999" style="font-size:12px;">
    </div>

    <div class="wppoppop-prop-row" style="margin-bottom:12px;padding-top:8px;border-top:1px solid #1e293b;">
        <label class="wppoppop-prop-label" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:6px;text-transform:uppercase;">FILTERS (PRE-SUBMISSION SANITIZATION)</label>
        <div class="wppoppop-gp-2col-grid" id="wppoppop-adv-filters-group">
            <label style="font-size:11px;color:#cbd5e1;display:flex;align-items:center;gap:6px;"><input type="checkbox" class="gp-adv-filter" value="trim" checked> Trim whitespace</label>
            <label style="font-size:11px;color:#cbd5e1;display:flex;align-items:center;gap:6px;"><input type="checkbox" class="gp-adv-filter" value="strip_tags"> Strip HTML tags</label>
            <label style="font-size:11px;color:#cbd5e1;display:flex;align-items:center;gap:6px;"><input type="checkbox" class="gp-adv-filter" value="alpha"> Alpha only</label>
            <label style="font-size:11px;color:#cbd5e1;display:flex;align-items:center;gap:6px;"><input type="checkbox" class="gp-adv-filter" value="alnum"> Alphanumeric</label>
            <label style="font-size:11px;color:#cbd5e1;display:flex;align-items:center;gap:6px;"><input type="checkbox" class="gp-adv-filter" value="digits"> Digits only</label>
            <label style="font-size:11px;color:#cbd5e1;display:flex;align-items:center;gap:6px;"><input type="checkbox" class="gp-adv-filter" value="lowercase"> Lowercase</label>
        </div>
    </div>

    <div class="wppoppop-prop-row" style="padding-top:8px;border-top:1px solid #1e293b;">
        <label class="wppoppop-prop-label" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:6px;text-transform:uppercase;">VALIDATORS</label>
        <div class="wppoppop-gp-2col-grid" id="wppoppop-adv-validators-group">
            <label style="font-size:11px;color:#cbd5e1;display:flex;align-items:center;gap:6px;"><input type="checkbox" class="gp-adv-validator" value="email"> Valid Email (RFC)</label>
            <label style="font-size:11px;color:#cbd5e1;display:flex;align-items:center;gap:6px;"><input type="checkbox" class="gp-adv-validator" value="no_duplicates"> No Duplicates</label>
            <label style="font-size:11px;color:#cbd5e1;display:flex;align-items:center;gap:6px;"><input type="checkbox" class="gp-adv-validator" value="in_array"> In Options Array</label>
            <label style="font-size:11px;color:#cbd5e1;display:flex;align-items:center;gap:6px;"><input type="checkbox" class="gp-adv-validator" value="url"> Valid URL</label>
        </div>
    </div>
</div>
