<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!-- DECOMPOSED TAB MODULE: CONTENT SHARED FOOTER (AUTOCOMPLETE, ANIMATION, REQUIRED) -->
<div class="wppoppop-insp-card" id="insp-section-gp-basic-shared" style="background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-top:12px;">
    <div class="wppoppop-prop-row" id="insp-row-gp-autocomplete" style="display:none;margin-bottom:12px;">
        <label class="wppoppop-prop-label" for="prop-gp-autocomplete" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">AUTOCOMPLETE ATTRIBUTE</label>
        <select id="prop-gp-autocomplete" class="widefat wppoppop-select wppoppop-input" style="font-size:12px;">
            <option value="off">Disabled (off)</option>
            <option value="on">Enabled (on)</option>
            <option value="email">Email (email)</option>
            <option value="name">Full Name (name)</option>
            <option value="given-name">First Name (given-name)</option>
            <option value="family-name">Last Name (family-name)</option>
            <option value="tel">Telephone (tel)</option>
            <option value="organization">Organization (organization)</option>
            <option value="street-address">Street Address (street-address)</option>
            <option value="postal-code">Postal Code (postal-code)</option>
        </select>
    </div>

    <div class="wppoppop-prop-row" id="insp-row-gp-animation">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
            <label class="wppoppop-prop-label" style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin:0;">ANIMATION</label>
            <button type="button" id="wppoppop-btn-play-elem-anim" class="button button-small" style="background:#1e293b;border:1px solid #334155;color:#38bdf8;font-size:10px;line-height:1.8;padding:0 8px;height:22px;cursor:pointer;">&#9654; Play</button>
        </div>
        <div class="wppoppop-gp-2col-grid">
            <div class="wppoppop-gp-col">
                <select id="prop-anim-in" class="widefat wppoppop-select wppoppop-input" style="font-size:12px;">
                    <option value="none">None</option>
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
                <span class="wppoppop-gp-sublabel">Appearance</span>
            </div>
            <div class="wppoppop-gp-col">
                <select id="prop-anim-out" class="widefat wppoppop-select wppoppop-input" style="font-size:12px;">
                    <option value="none">None</option>
                    <option value="fadeOut">fadeOut</option>
                    <option value="fadeOutUp">fadeOutUp</option>
                    <option value="fadeOutDown">fadeOutDown</option>
                    <option value="zoomOut">zoomOut</option>
                    <option value="bounceOut">bounceOut</option>
                </select>
                <span class="wppoppop-gp-sublabel">Disappearance</span>
            </div>
            <div class="wppoppop-gp-col">
                <input type="number" id="prop-anim-duration" class="widefat wppoppop-input" value="1000" min="0" step="50" style="font-size:12px;">
                <span class="wppoppop-gp-sublabel">Duration (ms)</span>
            </div>
            <div class="wppoppop-gp-col">
                <input type="number" id="prop-anim-delay" class="widefat wppoppop-input" value="0" min="0" step="50" style="font-size:12px;">
                <span class="wppoppop-gp-sublabel">Start delay (ms)</span>
            </div>
        </div>
    </div>

    <div class="wppoppop-prop-row wppoppop-gp-switch-row" id="insp-row-gp-required" style="display:none;margin-top:12px;padding-top:10px;border-top:1px solid #1e293b;">
        <label class="wppoppop-prop-label" for="prop-gp-required" style="font-size:11px;font-weight:700;color:#e2e8f0;text-transform:uppercase;">REQUIRED FIELD</label>
        <label class="wppoppop-gp-switch">
            <input type="checkbox" id="prop-gp-required">
            <span class="wppoppop-gp-slider"></span>
        </label>
    </div>
</div>
