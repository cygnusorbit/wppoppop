<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<header class="wppoppop-builder-header">
    <div class="wppoppop-hdr-left">
        <a href="<?php echo esc_url(admin_url('admin.php?page=wppoppop')); ?>" class="wppoppop-hdr-exit" title="Exit to Dashboard">
            <span class="dashicons dashicons-arrow-left-alt"></span>
            <span class="wppoppop-btn-txt">Exit</span>
        </a>
        <input type="text" id="wppoppop-builder-title" value="<?php echo esc_attr($title); ?>" placeholder="Popup Campaign Name..." title="Campaign Name">
        
        <button type="button" id="wppoppop-btn-settings" class="wppoppop-hdr-btn" title="Campaign Settings">
            <span class="dashicons dashicons-admin-generic"></span>
            <span class="wppoppop-btn-txt">Settings</span>
        </button>

        <div class="wppoppop-history-btns">
            <button type="button" id="wppoppop-btn-undo" title="Undo (Cmd/Ctrl+Z)" disabled>
                <span class="dashicons dashicons-undo"></span>
            </button>
            <button type="button" id="wppoppop-btn-redo" title="Redo (Cmd/Ctrl+Y)" disabled>
                <span class="dashicons dashicons-redo"></span>
            </button>
        </div>
    </div>

    <div class="wppoppop-hdr-center">
        <div class="wppoppop-screen-tabs">
            <button type="button" class="wppoppop-screen-tab active" data-screen="1">Screen 1</button>
            <button type="button" class="wppoppop-screen-tab" data-screen="2">Screen 2</button>
            <button type="button" class="wppoppop-screen-tab" data-screen="3">Screen 3</button>
        </div>
    </div>

    <div class="wppoppop-hdr-right">
        <div class="wppoppop-viewport-toggles">
            <button type="button" class="wppoppop-viewport-btn active" data-mode="desktop" title="Desktop Viewport (640px)">
                <span class="dashicons dashicons-desktop"></span>
            </button>
            <button type="button" class="wppoppop-viewport-btn" data-mode="mobile" title="Mobile Viewport (360px)">
                <span class="dashicons dashicons-smartphone"></span>
            </button>
        </div>

        <button type="button" id="wppoppop-btn-preview" class="wppoppop-hdr-btn" title="Live Preview">
            <span class="dashicons dashicons-visibility"></span>
            <span class="wppoppop-btn-txt">Preview</span>
        </button>

        <button type="button" id="wppoppop-btn-embed" class="wppoppop-hdr-btn" title="Embed & Shortcodes">
            <span class="dashicons dashicons-shortcode"></span>
            <span class="wppoppop-btn-txt">Embed</span>
        </button>

        <button type="button" id="wppoppop-btn-save" class="wppoppop-hdr-btn wppoppop-btn-primary" title="Save Campaign (Cmd/Ctrl+S)">
            <span class="dashicons dashicons-saved"></span>
            <span class="wppoppop-btn-txt">Save</span>
        </button>
    </div>
</header>
