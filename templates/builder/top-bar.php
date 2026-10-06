<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-builder-header">
    <div class="wppoppop-header-left">
        <button type="button" class="wppoppop-hdr-btn" id="wppoppop-btn-settings" title="Campaign Settings">
            <span class="dashicons dashicons-admin-generic"></span>
        </button>
        <span class="wppoppop-brand-title">WpPopPop Builder</span>
        <div class="wppoppop-viewport-switch">
            <button type="button" class="wppoppop-hdr-btn active" id="wppoppop-viewport-desktop" data-viewport="desktop" title="Desktop View (640px)">
                <span class="dashicons dashicons-desktop"></span>
            </button>
            <button type="button" class="wppoppop-hdr-btn" id="wppoppop-viewport-mobile" data-viewport="mobile" title="Mobile View (360px)">
                <span class="dashicons dashicons-smartphone"></span>
            </button>
        </div>
        <div class="wppoppop-screens-nav">
            <button type="button" class="wppoppop-screen-tab active" data-screen="1">Screen 1</button>
            <button type="button" class="wppoppop-screen-tab" data-screen="2">Screen 2</button>
            <button type="button" class="wppoppop-screen-tab" data-screen="3">Screen 3</button>
        </div>
    </div>

    <div class="wppoppop-header-center">
        <div class="wppoppop-align-tools">
            <button type="button" class="wppoppop-align-btn" data-align="left" title="Align Left">&#8676;</button>
            <button type="button" class="wppoppop-align-btn" data-align="center-h" title="Center Horizontally">&#8596;</button>
            <button type="button" class="wppoppop-align-btn" data-align="right" title="Align Right">&#8677;</button>
            <span class="wppoppop-v-sep"></span>
            <button type="button" class="wppoppop-align-btn" data-align="top" title="Align Top">&#8679;</button>
            <button type="button" class="wppoppop-align-btn" data-align="center-v" title="Center Vertically">&#8597;</button>
            <button type="button" class="wppoppop-align-btn" data-align="bottom" title="Align Bottom">&#8681;</button>
        </div>
        <div class="wppoppop-history-tools">
            <button type="button" class="wppoppop-hdr-btn" id="wppoppop-btn-undo" title="Undo (Cmd+Z)">
                <span class="dashicons dashicons-undo"></span>
            </button>
            <button type="button" class="wppoppop-hdr-btn" id="wppoppop-btn-redo" title="Redo (Cmd+Y)">
                <span class="dashicons dashicons-redo"></span>
            </button>
        </div>
    </div>

    <div class="wppoppop-header-right">
        <button type="button" class="wppoppop-hdr-btn" id="wppoppop-btn-embed" title="Embed & Shortcode">
            <span class="dashicons dashicons-editor-code"></span> Embed
        </button>
        <button type="button" class="wppoppop-hdr-btn" id="wppoppop-btn-preview" title="Live Preview">
            <span class="dashicons dashicons-visibility"></span> Preview
        </button>
        <button type="button" class="wppoppop-hdr-btn wppoppop-btn-primary" id="wppoppop-btn-save">
            <span class="dashicons dashicons-saved"></span> Save Popup
        </button>
    </div>
</div>
