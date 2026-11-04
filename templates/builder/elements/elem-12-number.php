<?php
if (!defined('ABSPATH')) exit;
?>
<!-- DECOMPOSED MODULE 12: NUMBER / SPINNER (number / spinner) -->
<div class="element-panel wppoppop-panel-specific" id="panel-elem-number" data-elem-family="form" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;"><?php esc_html_e('12. Number & Stepper Bounds', 'wppoppop'); ?></div>
    <div style="display:grid;grid-template-columns:2fr 1fr;gap:8px;margin-bottom:10px;">
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e('PLACEHOLDER', 'wppoppop'); ?></label>
            <input type="text" id="prop-number-placeholder" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="placeholder" placeholder="0" style="font-size:12px;">
        </div>
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e('UNIT SUFFIX', 'wppoppop'); ?></label>
            <input type="text" id="prop-number-unit" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="unitSuffix" placeholder="<?php esc_attr_e('e.g. kg, %', 'wppoppop'); ?>" style="font-size:12px;">
        </div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;">
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e('MIN VALUE', 'wppoppop'); ?></label>
            <input type="number" id="prop-number-min" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="minValue" placeholder="0" style="font-size:12px;">
        </div>
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e('MAX VALUE', 'wppoppop'); ?></label>
            <input type="number" id="prop-number-max" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="maxValue" placeholder="100" style="font-size:12px;">
        </div>
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e('STEP', 'wppoppop'); ?></label>
            <input type="number" id="prop-number-step" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="stepValue" value="1" step="any" style="font-size:12px;">
        </div>
    </div>
    <div style="margin-top:10px;">
        <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e('BROWSER AUTOCOMPLETE', 'wppoppop'); ?></label>
        <select id="prop-number-autocomplete" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="autocomplete" style="font-size:12px;">
                <option value="on"><?php esc_html_e('On (Default) (on)', 'wppoppop'); ?></option>
                <option value="off" selected><?php esc_html_e('Off (off)', 'wppoppop'); ?></option>
                <option value="name"><?php esc_html_e('Full Name (name)', 'wppoppop'); ?></option>
                <option value="given-name"><?php esc_html_e('First Name (given-name)', 'wppoppop'); ?></option>
                <option value="family-name"><?php esc_html_e('Last Name (family-name)', 'wppoppop'); ?></option>
                <option value="email"><?php esc_html_e('Email Address (email)', 'wppoppop'); ?></option>
                <option value="tel"><?php esc_html_e('Telephone (tel)', 'wppoppop'); ?></option>
                <option value="username"><?php esc_html_e('Username (username)', 'wppoppop'); ?></option>
                <option value="current-password"><?php esc_html_e('Current Password (current-password)', 'wppoppop'); ?></option>
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
