<?php
if (!defined('ABSPATH')) exit;
?>
<!-- DECOMPOSED MODULE 24: PASSWORD INPUT (password) -->
<div class="element-panel wppoppop-panel-specific" id="panel-elem-password" data-elem-family="form" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;">24. Password Input Settings</div>
    <div style="display:grid;grid-template-columns:2fr 1fr;gap:8px;margin-bottom:10px;">
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">PLACEHOLDER</label>
            <input type="text" id="prop-password-placeholder" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="placeholder" placeholder="Enter password..." style="font-size:12px;">
        </div>
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">MIN LENGTH</label>
            <input type="number" id="prop-password-minlength" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="minLength" min="1" max="64" value="8" style="font-size:12px;">
        </div>
    </div>
    <label style="display:inline-flex;align-items:center;gap:6px;font-size:11px;color:#cbd5e1;cursor:pointer;">
        <input type="checkbox" id="prop-password-eye" class="wppoppop-gp-bind" data-prop="showEyeToggle" value="1" checked> Show Password Visibility Eye Icon (👁)
    </label>
    <div style="margin-top:10px;">
        <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">BROWSER AUTOCOMPLETE</label>
        <select id="prop-password-autocomplete" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="autocomplete" style="font-size:12px;">
                <option value="on">On (Default) (on)</option>
                <option value="off">Off (off)</option>
                <option value="name">Full Name (name)</option>
                <option value="given-name">First Name (given-name)</option>
                <option value="family-name">Last Name (family-name)</option>
                <option value="email">Email Address (email)</option>
                <option value="tel">Telephone (tel)</option>
                <option value="username">Username (username)</option>
                <option value="current-password" selected>Current Password (current-password)</option>
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
