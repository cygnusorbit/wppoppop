<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!-- DECOMPOSED TAB MODULE: FORM INPUT STYLE (Elements 10-25 + Shared Custom CSS) -->
<div class="wppoppop-insp-card" id="insp-section-form-input-style" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div class="wppoppop-gp-notice-banner" id="gp-form-global-style-notice">
        <span class="dashicons dashicons-info-outline"></span>
        <span>Configure style of input fields through <strong>Popup Settings &raquo; Style Tab &raquo; Inputs</strong>.</span>
    </div>

    <div class="wppoppop-prop-row" id="gp-form-text-align-row" style="margin-top:14px;">
        <label class="wppoppop-prop-label" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:6px;text-transform:uppercase;">TEXT ALIGNMENT</label>
        <div class="wppoppop-align-seg-group" id="gp-form-text-align-group">
            <button type="button" class="wppoppop-align-seg-btn active" data-align="left" title="Left"><span class="dashicons dashicons-editor-alignleft"></span></button>
            <button type="button" class="wppoppop-align-seg-btn" data-align="center" title="Center"><span class="dashicons dashicons-editor-aligncenter"></span></button>
            <button type="button" class="wppoppop-align-seg-btn" data-align="right" title="Right"><span class="dashicons dashicons-editor-alignright"></span></button>
        </div>
    </div>

    <div class="wppoppop-prop-row" id="gp-form-input-icons-row" style="margin-top:14px;">
        <label class="wppoppop-prop-label" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:6px;text-transform:uppercase;">INPUT ICONS</label>
        <div class="wppoppop-gp-2col-grid">
            <div class="wppoppop-gp-col">
                <input type="text" id="prop-input-icon-left" class="widefat wppoppop-input" placeholder="fas fa-envelope" style="font-size:12px;">
                <span class="wppoppop-gp-sublabel">Left side icon class</span>
            </div>
            <div class="wppoppop-gp-col">
                <input type="number" id="prop-input-icon-left-offset" class="widefat wppoppop-input" value="10" style="font-size:12px;">
                <span class="wppoppop-gp-sublabel">Left offset (px)</span>
            </div>
            <div class="wppoppop-gp-col">
                <input type="text" id="prop-input-icon-right" class="widefat wppoppop-input" placeholder="fas fa-check" style="font-size:12px;">
                <span class="wppoppop-gp-sublabel">Right side icon class</span>
            </div>
            <div class="wppoppop-gp-col">
                <input type="number" id="prop-input-icon-right-offset" class="widefat wppoppop-input" value="10" style="font-size:12px;">
                <span class="wppoppop-gp-sublabel">Right offset (px)</span>
            </div>
        </div>
    </div>

    <div class="wppoppop-prop-row" style="margin-top:14px;">
        <label class="wppoppop-prop-label" for="prop-gp-style-css-class" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">CUSTOM CSS CLASS</label>
        <input type="text" id="prop-gp-style-css-class" class="widefat wppoppop-input" placeholder="e.g. custom-layer-box" style="font-size:12px;">
    </div>

    <div class="wppoppop-prop-row" style="margin-top:14px;">
        <label class="wppoppop-prop-label" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:6px;text-transform:uppercase;">CSS STYLES</label>
        <div id="wppoppop-gp-inline-styles-list" class="wppoppop-gp-repeater-list"></div>
        <button type="button" class="wppoppop-gp-btn-outline" id="wppoppop-btn-add-css-style">+ Add a style</button>
    </div>
</div>
