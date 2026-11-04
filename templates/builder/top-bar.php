<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-builder-header">
    <div class="wppoppop-header-left">
        <button type="button" class="wppoppop-hdr-btn" id="wppoppop-btn-settings" title="<?php esc_attr_e('Popup Settings', 'wppoppop'); ?>"><?php esc_html_e('Settings', 'wppoppop'); ?></button>
        <span class="wppoppop-brand-title"><?php esc_html_e('WpPopPop Builder', 'wppoppop'); ?></span>
        <div class="wppoppop-viewport-switch">
            <button type="button" class="wppoppop-hdr-btn active" id="wppoppop-viewport-desktop" data-viewport="desktop" title="<?php esc_attr_e('Desktop View (640px)', 'wppoppop'); ?>"><?php esc_html_e('Desktop', 'wppoppop'); ?></button>
            <button type="button" class="wppoppop-hdr-btn" id="wppoppop-viewport-mobile" data-viewport="mobile" title="<?php esc_attr_e('Mobile View (360px)', 'wppoppop'); ?>"><?php esc_html_e('Mobile', 'wppoppop'); ?></button>
        </div>
        <div class="wppoppop-screens-nav">
            <button type="button" class="wppoppop-screen-tab active" data-screen="1"><?php esc_html_e('Screen 1', 'wppoppop'); ?></button>
            <button type="button" class="wppoppop-screen-tab" data-screen="2"><?php esc_html_e('Screen 2', 'wppoppop'); ?></button>
            <button type="button" class="wppoppop-screen-tab" data-screen="3"><?php esc_html_e('Screen 3', 'wppoppop'); ?></button>
        </div>
    </div>

    <div class="wppoppop-header-center">
        <div class="wppoppop-align-tools">
            <button type="button" class="wppoppop-align-btn" data-align="left" title="<?php esc_attr_e('Align Left', 'wppoppop'); ?>">&#8676;</button>
            <button type="button" class="wppoppop-align-btn" data-align="center-h" title="<?php esc_attr_e('Center Horizontally', 'wppoppop'); ?>">&#8596;</button>
            <button type="button" class="wppoppop-align-btn" data-align="right" title="<?php esc_attr_e('Align Right', 'wppoppop'); ?>">&#8677;</button>
            <span class="wppoppop-v-sep"></span>
            <button type="button" class="wppoppop-align-btn" data-align="top" title="<?php esc_attr_e('Align Top', 'wppoppop'); ?>">&#8679;</button>
            <button type="button" class="wppoppop-align-btn" data-align="center-v" title="<?php esc_attr_e('Center Vertically', 'wppoppop'); ?>">&#8597;</button>
            <button type="button" class="wppoppop-align-btn" data-align="bottom" title="<?php esc_attr_e('Align Bottom', 'wppoppop'); ?>">&#8681;</button>
        </div>
        <div class="wppoppop-history-tools">
            <button type="button" class="wppoppop-hdr-btn" id="wppoppop-btn-undo" title="<?php esc_attr_e('Undo (Cmd+Z)', 'wppoppop'); ?>"><?php esc_html_e('Undo', 'wppoppop'); ?></button>
            <button type="button" class="wppoppop-hdr-btn" id="wppoppop-btn-redo" title="<?php esc_attr_e('Redo (Cmd+Y)', 'wppoppop'); ?>"><?php esc_html_e('Redo', 'wppoppop'); ?></button>
        </div>
    </div>

    <div class="wppoppop-header-right">
        <button type="button" class="wppoppop-hdr-btn" id="wppoppop-btn-embed" title="<?php esc_attr_e('Embed & Shortcode', 'wppoppop'); ?>"><?php esc_html_e('Embed', 'wppoppop'); ?></button>
        <button type="button" class="wppoppop-hdr-btn" id="wppoppop-btn-preview" title="<?php esc_attr_e('Live Preview', 'wppoppop'); ?>"><?php esc_html_e('Preview', 'wppoppop'); ?></button>
        <button type="button" class="wppoppop-hdr-btn wppoppop-btn-primary" id="wppoppop-btn-save"><span class="wppoppop-save-text"><?php esc_html_e('Save Popup', 'wppoppop'); ?></span></button>
    </div>
</div>
