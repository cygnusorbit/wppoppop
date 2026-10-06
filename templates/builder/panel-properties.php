<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-property-panel" id="wppoppop-property-panel">
    <div class="wppoppop-prop-header">
        <button type="button" class="wppoppop-prop-close-btn" id="wppoppop-property-close" title="Close Panel">
            <span class="dashicons dashicons-no-alt"></span>
        </button>
        <div class="wppoppop-prop-tabs">
            <button type="button" class="wppoppop-prop-tab active" data-tab="basic">Basic</button>
            <button type="button" class="wppoppop-prop-tab" data-tab="style">Style</button>
            <button type="button" class="wppoppop-prop-tab" data-tab="logic">Logic</button>
        </div>
    </div>

    <div class="wppoppop-prop-body">
        <!-- Basic Tab -->
        <div class="wppoppop-prop-section active" data-section="basic">
            <label>NAME</label>
            <input type="text" id="wppoppop-prop-name" class="widefat">

            <div style="display:flex;gap:10px;margin-top:10px;">
                <div style="flex:1;"><label>TOP (PX)</label><input type="number" id="wppoppop-prop-top"></div>
                <div style="flex:1;"><label>LEFT (PX)</label><input type="number" id="wppoppop-prop-left"></div>
            </div>

            <div style="display:flex;gap:10px;margin-top:10px;">
                <div style="flex:1;"><label>WIDTH (PX)</label><input type="number" id="wppoppop-prop-width"></div>
                <div style="flex:1;"><label>HEIGHT (PX)</label><input type="number" id="wppoppop-prop-height"></div>
            </div>

            <label style="margin-top:12px;display:block;">CONTENT / VALUE</label>
            <textarea id="wppoppop-prop-content" class="widefat" rows="3"></textarea>

            <label style="margin-top:12px;display:block;">TARGET SCREEN (Next Step)</label>
            <input type="number" id="wppoppop-prop-goto" value="2">

            <label style="margin-top:12px;display:block;">CLICK ACTION</label>
            <select id="wppoppop-prop-click-act" class="widefat">
                <option value="none">None</option>
                <option value="close">Close Popup</option>
                <option value="submit">Submit Form</option>
            </select>
        </div>

        <!-- Style Tab -->
        <div class="wppoppop-prop-section" data-section="style" style="display:none;">
            <label>FONT FAMILY</label>
            <select id="wppoppop-prop-font" class="widefat">
                <option value="inherit">Inherit</option>
                <option value="Inter">Inter</option>
                <option value="Roboto">Roboto</option>
                <option value="Poppins">Poppins</option>
                <option value="Montserrat">Montserrat</option>
            </select>

            <div style="display:flex;gap:10px;margin-top:10px;">
                <div style="flex:1;"><label>FONT SIZE (PX)</label><input type="number" id="wppoppop-prop-fontsize" value="16"></div>
                <div style="flex:1;"><label>TEXT COLOR</label><input type="color" id="wppoppop-prop-color" value="#1e293b"></div>
            </div>

            <div style="display:flex;gap:10px;margin-top:10px;">
                <div style="flex:1;"><label>BACKGROUND</label><input type="color" id="wppoppop-prop-bgcolor" value="#2271b1"></div>
                <div style="flex:1;"><label>BORDER RADIUS</label><input type="number" id="wppoppop-prop-radius" value="4"></div>
            </div>

            <label style="margin-top:12px;display:block;">ANIMATION ENTRANCE</label>
            <select id="wppoppop-prop-anim" class="widefat">
                <option value="none">None</option>
                <option value="fade">Fade In</option>
                <option value="slideDown">Slide Down</option>
                <option value="bounceIn">Bounce In</option>
                <option value="zoomIn">Zoom In</option>
            </select>
        </div>

        <!-- Logic Tab -->
        <div class="wppoppop-prop-section" data-section="logic" style="display:none;">
            <label><input type="checkbox" id="wppoppop-prop-req"> Mandatory Field (Required)</label>
            <label style="margin-top:10px;display:block;">FIELD NAME / PARAM KEY</label>
            <input type="text" id="wppoppop-prop-fieldname" class="widefat" placeholder="e.g. email or qty">
            <label style="margin-top:10px;display:block;">OPTIONS (For Select, Radio, Wheel, Scratch)</label>
            <textarea id="wppoppop-prop-options" class="widefat" rows="4" placeholder="Option 1:10&#10;Option 2:20"></textarea>
        </div>
    </div>
</div>
