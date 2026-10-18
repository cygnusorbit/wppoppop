<?php
if (!defined('ABSPATH')) exit;
?>
<!-- DECOMPOSED MODULE 11: EMAIL ADDRESS (email) -->
<div class="element-panel wppoppop-panel-specific" id="panel-elem-email" data-elem-family="form" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;">11. Email Input Settings</div>
    <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">PLACEHOLDER EMAIL</label>
    <input type="text" id="prop-email-placeholder" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="placeholder" placeholder="Enter your email..." style="margin-bottom:10px;font-size:12px;">
    <div style="display:grid;grid-template-columns:1fr;gap:10px;">
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">MAX LENGTH</label>
            <input type="number" id="prop-email-maxlength" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="maxLength" min="0" max="500" placeholder="Unlimited" style="font-size:12px;">
        </div>
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">BROWSER AUTOCOMPLETE</label>
            <select id="prop-email-autocomplete" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="autocomplete" style="font-size:12px;">
                <option value="on">On (Default) (on)</option>
                <option value="off">Off (off)</option>
                <option value="name">Full Name (name)</option>
                <option value="given-name">First Name (given-name)</option>
                <option value="family-name">Last Name (family-name)</option>
                <option value="organization">Company (organization)</option>
                <option value="honorific-prefix">Title / Prefix (honorific-prefix)</option>
                <option value="additional-name">Middle Name (additional-name)</option>
                <option value="nickname">Nickname (nickname)</option>
                <option value="username">Username (username)</option>
                <option value="organization-title">Job Title (organization-title)</option>
                <option value="email" selected>Email Address (email)</option>
                <option value="tel">Telephone (tel)</option>
                <option value="street-address">Street Address (street-address)</option>
                <option value="address-line1">Address Line 1 (address-line1)</option>
                <option value="address-line2">Address Line 2 (address-line2)</option>
                <option value="address-level2">City (address-level2)</option>
                <option value="address-level1">State / Province (address-level1)</option>
                <option value="postal-code">ZIP / Postal Code (postal-code)</option>
                <option value="country-name">Country (country-name)</option>
                <option value="bday">Birthday (bday)</option>
                <option value="url">Website URL (url)</option>
                <option value="one-time-code">One-Time Code (one-time-code)</option>
            </select>
        </div>
        <div>
            <label style="display:inline-flex;align-items:center;gap:6px;font-size:11px;color:#cbd5e1;cursor:pointer;">
                <input type="checkbox" id="prop-email-disposable" class="wppoppop-gp-bind" data-prop="blockDisposable" value="1"> Block Disposable Emails
            </label>
        </div>
    </div>
</div>
