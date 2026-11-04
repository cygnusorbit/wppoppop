<?php
if (!defined('ABSPATH')) exit;
?>
<!-- DECOMPOSED MODULE 24: PASSWORD INPUT (password) -->
<div class="element-panel wppoppop-panel-specific" id="panel-elem-password" data-elem-family="form" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;"><?php esc_html_e('24. Password Input Settings', 'wppoppop'); ?></div>
    <div style="display:grid;grid-template-columns:2fr 1fr;gap:8px;margin-bottom:10px;">
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e('PLACEHOLDER', 'wppoppop'); ?></label>
            <input type="text" id="prop-password-placeholder" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="placeholder" placeholder="<?php esc_attr_e('Enter password...', 'wppoppop'); ?>" style="font-size:12px;">
        </div>
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e('MIN LENGTH', 'wppoppop'); ?></label>
            <input type="number" id="prop-password-minlength" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="minLength" min="1" max="64" placeholder="e.g. 8" style="font-size:12px;">
        </div>
    </div>
    <label style="display:inline-flex;align-items:center;gap:6px;font-size:11px;color:#cbd5e1;cursor:pointer;">
        <input type="checkbox" id="prop-password-eye" class="wppoppop-gp-bind" data-prop="showEyeToggle" value="1" checked>  <?php esc_html_e('Show Password Visibility Eye Icon', 'wppoppop'); ?>
    </label>
    <div style="margin-top:10px;">
        <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e('BROWSER AUTOCOMPLETE', 'wppoppop'); ?></label>
        <select id="prop-password-autocomplete" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="autocomplete" style="font-size:12px;">
                <option value="on"><?php esc_html_e('On (Default) (on)', 'wppoppop'); ?></option>
                <option value="off"><?php esc_html_e('Off (off)', 'wppoppop'); ?></option>
                <option value="name"><?php esc_html_e('Full Name (name)', 'wppoppop'); ?></option>
                <option value="given-name"><?php esc_html_e('First Name (given-name)', 'wppoppop'); ?></option>
                <option value="family-name"><?php esc_html_e('Last Name (family-name)', 'wppoppop'); ?></option>
                <option value="email"><?php esc_html_e('Email Address (email)', 'wppoppop'); ?></option>
                <option value="tel"><?php esc_html_e('Telephone (tel)', 'wppoppop'); ?></option>
                <option value="username"><?php esc_html_e('Username (username)', 'wppoppop'); ?></option>
                <option value="current-password" selected><?php esc_html_e('Current Password (current-password)', 'wppoppop'); ?></option>
                <option value="new-password"><?php esc_html_e('New Password (new-password)', 'wppoppop'); ?></option>
                <option value="one-time-code"><?php esc_html_e('One-Time Code (one-time-code)', 'wppoppop'); ?></option>
                <option value="organization"><?php esc_html_e('Company (organization)', 'wppoppop'); ?></option>
                <option value="organization-title"><?php esc_html_e('Job Title (organization-title)', 'wppoppop'); ?></option>
                <option value="street-address"><?php esc_html_e('Street Address (street-address)', 'wppoppop'); ?></option>
                <option value="address-line1"><?php esc_html_e('Address Line 1 (address-line1)', 'wppoppop'); ?></option>
                <option value="address-level2"><?php esc_html_e('City (address-level2)', 'wppoppop'); ?></option>
                <option value="address-level1"><?php esc_html_e('State / Province (address-level1)', 'wppoppop'); ?></option>
                <option value="postal-code"><?php esc_html_e('ZIP / Postal Code (postal-code)', 'wppoppop'); ?></option>
                <option value="country-name"><?php esc_html_e('Country (country-name)', 'wppoppop'); ?></option>
                <option value="bday"><?php esc_html_e('Birthday (bday)', 'wppoppop'); ?></option>
                <option value="url"><?php esc_html_e('Website URL (url)', 'wppoppop'); ?></option>
            </select>
    </div>
</div>
