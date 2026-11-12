<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!-- DECOMPOSED TAB MODULE: CONTENT SHARED FOOTER (AUTOCOMPLETE, ANIMATION, REQUIRED) -->
<div class="wppoppop-insp-card" id="insp-section-gp-basic-shared" style="background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-top:12px;">
    <div class="wppoppop-prop-row" id="insp-row-gp-autocomplete" style="display:none;margin-bottom:12px;">
        <label class="wppoppop-prop-label" for="prop-gp-autocomplete" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e('AUTOCOMPLETE ATTRIBUTE', 'wppoppop'); ?></label>
        <select id="prop-gp-autocomplete" class="widefat wppoppop-select wppoppop-input" style="font-size:12px;">
            <option value="off"><?php esc_html_e('Disabled (off)', 'wppoppop'); ?></option>
            <option value="on"><?php esc_html_e('Enabled (on)', 'wppoppop'); ?></option>
            <option value="email"><?php esc_html_e('Email (email)', 'wppoppop'); ?></option>
            <option value="name"><?php esc_html_e('Full Name (name)', 'wppoppop'); ?></option>
            <option value="given-name"><?php esc_html_e('First Name (given-name)', 'wppoppop'); ?></option>
            <option value="family-name"><?php esc_html_e('Last Name (family-name)', 'wppoppop'); ?></option>
            <option value="tel"><?php esc_html_e('Telephone (tel)', 'wppoppop'); ?></option>
            <option value="organization"><?php esc_html_e('Organization (organization)', 'wppoppop'); ?></option>
            <option value="street-address"><?php esc_html_e('Street Address (street-address)', 'wppoppop'); ?></option>
            <option value="postal-code"><?php esc_html_e('Postal Code (postal-code)', 'wppoppop'); ?></option>
        </select>
    </div>

    <div class="wppoppop-prop-row" id="insp-row-gp-animation">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
            <label class="wppoppop-prop-label" style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin:0;"><?php esc_html_e('ANIMATION', 'wppoppop'); ?></label>
            <button type="button" id="wppoppop-btn-play-elem-anim" class="button button-small" style="background:#1e293b;border:1px solid #334155;color:#38bdf8;font-size:10px;line-height:1.8;padding:0 8px;height:22px;cursor:pointer;">&#9654; Play</button>
        </div>
        <div class="wppoppop-gp-2col-grid">
            <div class="wppoppop-gp-col" style="grid-column:1 / -1;">
                <select id="prop-anim-in" class="widefat wppoppop-select wppoppop-input" style="font-size:12px;">
                    <option value="none"><?php esc_html_e('None', 'wppoppop'); ?></option>
                    <option value="fadeIn">fadeIn</option>
                    <option value="fadeInUp">fadeInUp</option>
                    <option value="fadeInDown">fadeInDown</option>
                    <option value="fadeInLeft">fadeInLeft</option>
                    <option value="fadeInRight">fadeInRight</option>
                    <option value="zoomIn">zoomIn</option>
                    <option value="bounceIn">bounceIn</option>
                    <option value="slideInUp">slideInUp</option>
                    <option value="slideInDown">slideInDown</option>
                </select>
                <span class="wppoppop-gp-sublabel"><?php esc_html_e('Appearance', 'wppoppop'); ?></span>
            </div>
            <div class="wppoppop-gp-col">
                <input type="number" id="prop-anim-duration" class="widefat wppoppop-input" value="1000" min="0" step="50" style="font-size:12px;">
                <span class="wppoppop-gp-sublabel"><?php esc_html_e('Duration (ms)', 'wppoppop'); ?></span>
            </div>
            <div class="wppoppop-gp-col">
                <input type="number" id="prop-anim-delay" class="widefat wppoppop-input" value="0" min="0" step="50" style="font-size:12px;">
                <span class="wppoppop-gp-sublabel"><?php esc_html_e('Start delay (ms)', 'wppoppop'); ?></span>
            </div>
            <div class="wppoppop-gp-col" style="grid-column:1 / -1;">
                <select id="prop-anim-start" class="widefat wppoppop-select wppoppop-input" style="font-size:12px;">
                    <option value="after"><?php esc_html_e('Start animation after below layer (Default)', 'wppoppop'); ?></option>
                    <option value="with"><?php esc_html_e('Start same time when load', 'wppoppop'); ?></option>
                </select>
                <span class="wppoppop-gp-sublabel"><?php esc_html_e('Start Animation', 'wppoppop'); ?></span>
            </div>
        </div>
    </div>

    <div class="wppoppop-prop-row wppoppop-gp-switch-row" id="insp-row-gp-required" style="display:none;margin-top:12px;padding-top:10px;border-top:1px solid #1e293b;">
        <label class="wppoppop-prop-label" for="prop-gp-required" style="font-size:11px;font-weight:700;color:#e2e8f0;text-transform:uppercase;"><?php esc_html_e('REQUIRED FIELD', 'wppoppop'); ?></label>
        <label class="wppoppop-gp-switch">
            <input type="checkbox" id="prop-gp-required">
            <span class="wppoppop-gp-slider"></span>
        </label>
    </div>
</div>
