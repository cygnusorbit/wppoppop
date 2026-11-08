<?php
if (!defined('ABSPATH')) exit;
?>
<!-- ADVANCED FIELD IDENTIFIER, FILTERS & VALIDATORS SUITE -->
<div class="wppoppop-insp-section" id="insp-section-form-advanced" style="background:#0f172a; border:1px solid #1e293b; border-radius:8px; padding:12px; margin-bottom:12px;">
    <div style="font-size:10px; font-weight:800; color:#38bdf8; text-transform:uppercase; margin-bottom:10px;"><?php esc_html_e('FIELD IDENTIFIER', 'wppoppop'); ?></div>
    <div id="insp-row-adv-field-name" style="margin-bottom:10px;">
        <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px; text-transform:uppercase;"><?php esc_html_e('ID / Merge Token Name', 'wppoppop'); ?></label>
        <input type="text" id="prop-field-name" class="widefat wppoppop-input wppoppop-adv-bind" data-adv-prop="fieldName" placeholder="<?php esc_attr_e('auto: layer-name-a1b2c3', 'wppoppop'); ?>" style="font-size:12px;" />
        <small style="font-size:10px; color:#64748b; display:block; margin-top:3px;"><?php esc_html_e('Use as <code>{field_name}</code> in email notifications & CRM webhooks.', 'wppoppop'); ?></small>
    </div>
    <div class="wppoppop-input-mask-row" id="insp-row-adv-input-mask" style="display:none;" aria-hidden="true">
        <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px; text-transform:uppercase;"><?php esc_html_e('Input Mask', 'wppoppop'); ?></label>
        <input type="text" id="prop-input-mask" class="widefat wppoppop-input wppoppop-adv-bind" data-adv-prop="inputMask" placeholder="e.g. (999) 999-9999" style="font-size:12px;" />
    </div>
</div>

<div class="wppoppop-insp-section" id="insp-section-form-filters" style="background:#0f172a; border:1px solid #1e293b; border-radius:8px; padding:12px; margin-bottom:12px;">
    <div style="font-size:10px; font-weight:800; color:#38bdf8; text-transform:uppercase; margin-bottom:8px;"><?php esc_html_e('SANITIZATION FILTERS', 'wppoppop'); ?></div>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
        <label style="font-size:11px; color:#cbd5e1; cursor:pointer; display:flex; align-items:center; gap:6px;"><input type="checkbox" class="prop-filter-chk" value="trim" /> <?php esc_html_e('Trim Whitespace', 'wppoppop'); ?></label>
        <label style="font-size:11px; color:#cbd5e1; cursor:pointer; display:flex; align-items:center; gap:6px;"><input type="checkbox" class="prop-filter-chk" value="striptags" /> <?php esc_html_e('Strip Tags', 'wppoppop'); ?></label>
        <label style="font-size:11px; color:#cbd5e1; cursor:pointer; display:flex; align-items:center; gap:6px;"><input type="checkbox" class="prop-filter-chk" value="alpha" /> <?php esc_html_e('Alpha Only', 'wppoppop'); ?></label>
        <label style="font-size:11px; color:#cbd5e1; cursor:pointer; display:flex; align-items:center; gap:6px;"><input type="checkbox" class="prop-filter-chk" value="digits" /> <?php esc_html_e('Digits Only', 'wppoppop'); ?></label>
        <label style="font-size:11px; color:#cbd5e1; cursor:pointer; display:flex; align-items:center; gap:6px;"><input type="checkbox" class="prop-filter-chk" value="lowercase" /> <?php esc_html_e('Lowercase', 'wppoppop'); ?></label>
        <label style="font-size:11px; color:#cbd5e1; cursor:pointer; display:flex; align-items:center; gap:6px;"><input type="checkbox" class="prop-filter-chk" value="uppercase" /> <?php esc_html_e('Uppercase', 'wppoppop'); ?></label>
    </div>
</div>

<div class="wppoppop-insp-section" id="insp-section-form-validators" style="background:#0f172a; border:1px solid #1e293b; border-radius:8px; padding:12px;">
    <div style="font-size:10px; font-weight:800; color:#38bdf8; text-transform:uppercase; margin-bottom:8px;"><?php esc_html_e('VALIDATION RULES', 'wppoppop'); ?></div>
    <div id="insp-row-val-rules" style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:10px;">
        <label style="font-size:11px; color:#cbd5e1; cursor:pointer; display:flex; align-items:center; gap:6px;"><input type="checkbox" class="prop-validator-chk" value="required" /> <?php esc_html_e('Required *', 'wppoppop'); ?></label>
        <label style="font-size:11px; color:#cbd5e1; cursor:pointer; display:flex; align-items:center; gap:6px;"><input type="checkbox" class="prop-validator-chk" value="email" /> <?php esc_html_e('RFC Email', 'wppoppop'); ?></label>
        <label style="font-size:11px; color:#cbd5e1; cursor:pointer; display:flex; align-items:center; gap:6px;"><input type="checkbox" class="prop-validator-chk" value="equal" /> <?php esc_html_e('Equal Match', 'wppoppop'); ?></label>
        <label style="font-size:11px; color:#cbd5e1; cursor:pointer; display:flex; align-items:center; gap:6px;"><input type="checkbox" class="prop-validator-chk" value="inarray" /> <?php esc_html_e('In Array', 'wppoppop'); ?></label>
        <label style="font-size:11px; color:#cbd5e1; cursor:pointer; display:flex; align-items:center; gap:6px;"><input type="checkbox" class="prop-validator-chk" value="noduplicates" /> <?php esc_html_e('No Duplicates', 'wppoppop'); ?></label>
    </div>
    <div id="wppoppop-val-extra-equal" class="wppoppop-val-extra" style="display:none; margin-bottom:10px;">
        <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px; text-transform:uppercase;"><?php esc_html_e('Equal Match — Value Must Equal', 'wppoppop'); ?></label>
        <input type="text" id="prop-validator-equal" class="widefat wppoppop-input" placeholder="<?php esc_attr_e('Exact value, e.g. YES', 'wppoppop'); ?>" style="font-size:12px;" />
    </div>
    <div id="wppoppop-val-extra-inarray" class="wppoppop-val-extra" style="display:none; margin-bottom:10px;">
        <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px; text-transform:uppercase;"><?php esc_html_e('In Array — Allowed Values (comma separated)', 'wppoppop'); ?></label>
        <input type="text" id="prop-validator-list" class="widefat wppoppop-input" placeholder="<?php esc_attr_e('red, green, blue', 'wppoppop'); ?>" style="font-size:12px;" />
    </div>
    <div id="insp-row-val-error">
        <label style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:4px; text-transform:uppercase;"><?php esc_html_e('Custom Validation Error Message', 'wppoppop'); ?></label>
        <input type="text" id="prop-validator-error" class="widefat wppoppop-input wppoppop-adv-bind" data-adv-prop="errorMessage" placeholder="<?php esc_attr_e('Please enter a valid value...', 'wppoppop'); ?>" style="font-size:12px;" />
    </div>
</div>
