<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-property-panel" id="wppoppop-property-panel">
    <div class="wppoppop-prop-header">
        <button type="button" class="wppoppop-prop-close-btn" id="wppoppop-property-close" title="<?php esc_attr_e('Close Panel', 'wppoppop'); ?>">
            <span class="dashicons dashicons-no-alt"></span>
        </button>
        <div class="wppoppop-prop-tabs">
            <button type="button" class="wppoppop-prop-tab active" data-tab="basic"><?php esc_html_e('Basic', 'wppoppop'); ?></button>
            <button type="button" class="wppoppop-prop-tab" data-tab="style"><?php esc_html_e('Style', 'wppoppop'); ?></button>
            <button type="button" class="wppoppop-prop-tab" data-tab="logic"><?php esc_html_e('Logic', 'wppoppop'); ?></button>
        </div>
    </div>

    <div class="wppoppop-prop-body">
        <!-- Basic Tab -->
        <div class="wppoppop-prop-section active" data-section="basic">
            <label><?php esc_html_e('NAME', 'wppoppop'); ?></label>
            <input type="text" id="wppoppop-prop-name" class="widefat">

            <div style="display:flex;gap:10px;margin-top:10px;">
                <div style="flex:1;"><label><?php esc_html_e('TOP (PX)', 'wppoppop'); ?></label><input type="number" id="wppoppop-prop-top"></div>
                <div style="flex:1;"><label><?php esc_html_e('LEFT (PX)', 'wppoppop'); ?></label><input type="number" id="wppoppop-prop-left"></div>
            </div>

            <div style="display:flex;gap:10px;margin-top:10px;">
                <div style="flex:1;"><label><?php esc_html_e('WIDTH (PX)', 'wppoppop'); ?></label><input type="number" id="wppoppop-prop-width"></div>
                <div style="flex:1;"><label><?php esc_html_e('HEIGHT (PX)', 'wppoppop'); ?></label><input type="number" id="wppoppop-prop-height"></div>
            </div>

            <label style="margin-top:12px;display:block;"><?php esc_html_e('CONTENT / VALUE', 'wppoppop'); ?></label>
            <textarea id="wppoppop-prop-content" class="widefat" rows="3"></textarea>

            <label style="margin-top:12px;display:block;"><?php esc_html_e('TARGET SCREEN (Next Step)', 'wppoppop'); ?></label>
            <input type="number" id="wppoppop-prop-goto" value="2">

            <label style="margin-top:12px;display:block;"><?php esc_html_e('CLICK ACTION', 'wppoppop'); ?></label>
            <select id="wppoppop-prop-click-act" class="widefat">
                <option value="none"><?php esc_html_e('None', 'wppoppop'); ?></option>
                <option value="close"><?php esc_html_e('Close Popup', 'wppoppop'); ?></option>
                <option value="submit"><?php esc_html_e('Submit Form', 'wppoppop'); ?></option>
            </select>
        </div>

        <!-- Style Tab -->
        <div class="wppoppop-prop-section" data-section="style" style="display:none;">
            <label><?php esc_html_e('FONT FAMILY', 'wppoppop'); ?></label>
            <select id="wppoppop-prop-font" class="widefat">
                <option value="inherit"><?php esc_html_e('Inherit', 'wppoppop'); ?></option>
                <option value="Inter"><?php esc_html_e('Inter', 'wppoppop'); ?></option>
                <option value="Roboto"><?php esc_html_e('Roboto', 'wppoppop'); ?></option>
                <option value="Poppins"><?php esc_html_e('Poppins', 'wppoppop'); ?></option>
                <option value="Montserrat"><?php esc_html_e('Montserrat', 'wppoppop'); ?></option>
            </select>

            <div style="display:flex;gap:10px;margin-top:10px;">
                <div style="flex:1;"><label><?php esc_html_e('FONT SIZE (PX)', 'wppoppop'); ?></label><input type="number" id="wppoppop-prop-fontsize" value="16"></div>
                <div style="flex:1;"><label><?php esc_html_e('TEXT COLOR', 'wppoppop'); ?></label><input type="color" id="wppoppop-prop-color" value="#1e293b"></div>
            </div>

            <div style="display:flex;gap:10px;margin-top:10px;">
                <div style="flex:1;"><label><?php esc_html_e('BACKGROUND', 'wppoppop'); ?></label><input type="color" id="wppoppop-prop-bgcolor" value="#2271b1"></div>
                <div style="flex:1;"><label><?php esc_html_e('BORDER RADIUS', 'wppoppop'); ?></label><input type="number" id="wppoppop-prop-radius" value="0"></div>
            </div>

            <label style="margin-top:12px;display:block;"><?php esc_html_e('ANIMATION ENTRANCE', 'wppoppop'); ?></label>
            <select id="wppoppop-prop-anim" class="widefat">
                <option value="none"><?php esc_html_e('None', 'wppoppop'); ?></option>
                <option value="fade"><?php esc_html_e('Fade In', 'wppoppop'); ?></option>
                <option value="slideDown"><?php esc_html_e('Slide Down', 'wppoppop'); ?></option>
                <option value="bounceIn"><?php esc_html_e('Bounce In', 'wppoppop'); ?></option>
                <option value="zoomIn"><?php esc_html_e('Zoom In', 'wppoppop'); ?></option>
            </select>
        </div>

        <!-- Logic Tab -->
        <div class="wppoppop-prop-section" data-section="logic" style="display:none;">
            <label><input type="checkbox" id="wppoppop-prop-req"> <?php esc_html_e('Mandatory Field (Required)', 'wppoppop'); ?></label>
            <label style="margin-top:10px;display:block;"><?php esc_html_e('FIELD NAME / PARAM KEY', 'wppoppop'); ?></label>
            <input type="text" id="wppoppop-prop-fieldname" class="widefat" placeholder="<?php esc_attr_e('e.g. email or qty', 'wppoppop'); ?>">
            <label style="margin-top:10px;display:block;"><?php esc_html_e('OPTIONS (For Select, Radio, Wheel, Scratch)', 'wppoppop'); ?></label>
            <textarea id="wppoppop-prop-options" class="widefat" rows="4" placeholder="Option 1:10&#10;Option 2:20"></textarea>
        </div>
    </div>
</div>
