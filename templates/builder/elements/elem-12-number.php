<?php
if (!defined('ABSPATH')) exit;
?>
<!-- DECOMPOSED MODULE 12: NUMBER / SPINNER (number / spinner) -->
<div class="element-panel wppoppop-panel-specific" id="panel-elem-number" data-elem-family="form" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;">12. Number &amp; Stepper Bounds</div>
    <div style="display:grid;grid-template-columns:2fr 1fr;gap:8px;margin-bottom:10px;">
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">PLACEHOLDER</label>
            <input type="text" id="prop-number-placeholder" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="placeholder" placeholder="0" style="font-size:12px;">
        </div>
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">UNIT SUFFIX</label>
            <input type="text" id="prop-number-unit" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="unitSuffix" placeholder="e.g. kg, %" style="font-size:12px;">
        </div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;">
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">MIN VALUE</label>
            <input type="number" id="prop-number-min" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="minValue" placeholder="0" style="font-size:12px;">
        </div>
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">MAX VALUE</label>
            <input type="number" id="prop-number-max" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="maxValue" placeholder="100" style="font-size:12px;">
        </div>
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">STEP</label>
            <input type="number" id="prop-number-step" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="stepValue" value="1" step="any" style="font-size:12px;">
        </div>
    </div>
    <div style="margin-top:10px;">
        <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">BROWSER AUTOCOMPLETE</label>
        <select id="prop-number-autocomplete" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="autocomplete" style="font-size:12px;">
                <option value="on">On (Default) (on)</option>
                <option value="off" selected>Off (off)</option>
                <option value="name">Full Name (name)</option>
                <option value="given-name">First Name (given-name)</option>
                <option value="family-name">Last Name (family-name)</option>
                <option value="email">Email Address (email)</option>
                <option value="tel">Telephone (tel)</option>
                <option value="username">Username (username)</option>
                <option value="current-password">Current Password (current-password)</option>
                <option value="new-password">New Password (new-password)</option>
                <option value="one-time-code">One-Time Code (one-time-code)</option>
                <option value="organization">Company (organization)</option>
                <option value="organization-title">Job Title (organization-title)</option>
                <option value="street-address">Street Address (street-address)</option>
                <option value="address-line1">Address Line 1 (address-line1)</option>
                <option value="address-level2">City (address-level2)</option>
                <option value="address-level1">State / Province (address-level1)</option>
                <option value="postal-code">ZIP / Postal Code (postal-code)</option>
                <option value="country-name">Country (country-name)</option>
                <option value="bday">Birthday (bday)</option>
                <option value="url">Website URL (url)</option>
            </select>
    </div>
</div>
