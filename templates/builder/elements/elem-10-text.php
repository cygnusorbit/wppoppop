<?php
if (!defined('ABSPATH')) exit;
?>
<!-- DECOMPOSED MODULE 10: FORM TEXT INPUT (text / textfield) -->
<div class="element-panel wppoppop-panel-specific" id="panel-elem-text" data-elem-family="form" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;">10. Text Input Settings (Form Family — 5 Tabs)</div>

    <label for="prop-textfield-placeholder" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">PLACEHOLDER TEXT</label>
    <input type="text" id="prop-textfield-placeholder" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="placeholder" placeholder="e.g. Enter your name..." style="margin-bottom:10px;font-size:12px;">
    <input type="hidden" id="prop-input-placeholder" class="wppoppop-gp-bind" data-prop="placeholder">

    <label for="prop-textfield-fieldname" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">FIELD NAME / TOKEN</label>
    <input type="text" id="prop-textfield-fieldname" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="fieldName" placeholder="e.g. first_name" style="margin-bottom:10px;font-size:12px;">
    <input type="hidden" id="prop-input-default" class="wppoppop-gp-bind" data-prop="defaultValue">

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px;">
        <div>
            <label for="prop-textfield-autocomplete" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">AUTOCOMPLETE</label>
            <select id="prop-textfield-autocomplete" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="autocomplete" style="font-size:12px;">
                <option value="on">Enabled (on)</option>
                <option value="off">Disabled (off)</option>
                <option value="name">Full Name (name)</option>
                <option value="given-name">First Name (given-name)</option>
                <option value="family-name">Last Name (family-name)</option>
                <option value="username">Username (username)</option>
                <option value="organization">Organization</option>
                <option value="street-address">Street Address</option>
                <option value="postal-code">Postal / ZIP</option>
                <option value="country">Country</option>
            </select>
        </div>
        <div>
            <label for="prop-textfield-maxlen" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">MAX CHARACTERS</label>
            <input type="number" id="prop-textfield-maxlen" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="maxLength" min="1" max="2000" placeholder="255" style="font-size:12px;">
        </div>
    </div>

    <label style="display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:600;color:#cbd5e1;cursor:pointer;">
        <input type="checkbox" id="prop-textfield-required" class="wppoppop-gp-bind" data-prop="required" value="1"> Required Field
    </label>
</div>
