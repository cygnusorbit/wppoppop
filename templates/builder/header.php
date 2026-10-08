<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-builder-header">
    <!-- Left Section: Navigation, Settings, Title -->
    <div class="wppoppop-hdr-group wppoppop-hdr-left">
        <a href="<?php echo esc_url(admin_url('admin.php?page=wppoppop')); ?>" class="button button-secondary wppoppop-hdr-dash-btn" title="Back to Popups Dashboard">
            <span class="dashicons dashicons-arrow-left-alt2"></span>
            <span class="wppoppop-btn-label">Dashboard</span>
        </a>
        <button type="button" id="wppoppop-btn-settings" class="button button-secondary" title="Campaign Settings">
            <span class="dashicons dashicons-admin-generic"></span>
            <span class="wppoppop-btn-label">Settings</span>
        </button>
        <input type="text" id="wppoppop-builder-title" value="<?php echo esc_attr($popup ? $popup->title : 'Untitled Popup Campaign'); ?>" placeholder="Campaign Title..." title="Campaign Title">
    </div>

    <!-- Center Section: Canvas Label (Outside Workspace) & Sequence Tabs -->
    <div class="wppoppop-hdr-group wppoppop-hdr-center">
        <span class="wppoppop-hdr-canvas-label" style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;margin-right:2px;user-select:none;">Canvas:</span>
        <div class="wppoppop-canvas-tabs wppoppop-screen-tabs" id="wppoppop-canvas-tabs-container">
            <div class="wppoppop-canvas-tab-wrapper active">
                <button type="button" class="wppoppop-canvas-tab wppoppop-screen-tab" data-canvas="1" data-screen="1">Canvas 1</button>
            </div>
            <div class="wppoppop-canvas-tab-wrapper">
                <button type="button" class="wppoppop-canvas-tab wppoppop-screen-tab" data-canvas="2" data-screen="2">Canvas 2</button>
                <span class="wppoppop-tab-delete-canvas" data-canvas="2" title="Delete Canvas">&times;</span>
            </div>
            <button type="button" id="wppoppop-add-canvas-btn" class="wppoppop-canvas-add-btn" title="Add Canvas">+</button>
        </div>

        <!-- Quick Dimension Display -->
        <div class="wppoppop-quick-dimensions-wrap" title="Canvas Stage Dimensions">
            <span>W:</span>
            <input type="number" id="quick-box-width" value="640" min="200" max="1400">
            <span>H:</span>
            <input type="number" id="quick-box-height" value="400" min="150" max="1000">
        </div>

        <div class="wppoppop-viewport-toggles">
            <button type="button" class="wppoppop-viewport-btn active" data-mode="desktop" title="Desktop Viewport (640px)">
                <span class="dashicons dashicons-desktop"></span>
            </button>
            <button type="button" class="wppoppop-viewport-btn" data-mode="mobile" title="Mobile Viewport (360px)">
                <span class="dashicons dashicons-smartphone"></span>
            </button>
        </div>
    </div>

    <!-- Right Section: Preview, Embed, Save Actions -->
    <div class="wppoppop-hdr-group wppoppop-hdr-right">
        <button type="button" id="wppoppop-btn-preview" class="button" title="Interactive Sandbox Preview">
            <span class="dashicons dashicons-visibility"></span>
            <span class="wppoppop-btn-label">Preview</span>
        </button>
        <button type="button" id="wppoppop-btn-embed" class="button" title="Get Embed Codes">
            <span class="dashicons dashicons-editor-code"></span>
            <span class="wppoppop-btn-label">Embed</span>
        </button>
        <button type="button" id="wppoppop-btn-save" class="button button-primary" title="Save Popup Campaign">
            <span class="dashicons dashicons-saved"></span>
            <span class="wppoppop-save-text">Save Popup</span>
        </button>
    </div>
</div>
