<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-builder-header">
    <!-- Left Section: Navigation, Settings, Title -->
    <div class="wppoppop-hdr-group wppoppop-hdr-left">
        <a href="<?php echo esc_url(admin_url('admin.php?page=wppoppop')); ?>" class="button button-secondary wppoppop-hdr-dash-btn" title="Back to Popups Dashboard">Dashboard</a>
                <button type="button" class="wppoppop-btn-icon wppoppop-btn-settings" id="wppoppop-btn-settings" data-action="settings" title="Campaign Settings" aria-label="Campaign Settings" onclick="if(window.WpPopPopBuilderSettings&&typeof window.WpPopPopBuilderSettings.toggleDrawer==='function'){window.WpPopPopBuilderSettings.toggleDrawer();}else if(window.WpPopPopSettings){window.WpPopPopSettings.toggleDrawer();}" style="cursor:pointer;pointer-events:auto;"><span class="dashicons dashicons-admin-generic" style="pointer-events:none;"></span></button>Settings</button>
        <input type="text" id="wppoppop-builder-title" value="<?php echo esc_attr($popup ? $popup->title : 'Untitled Popup Campaign'); ?>" placeholder="Campaign Title..." title="Campaign Title">
    </div>

    <!-- Center Section: Canvas Label (Outside Workspace) & Sequence Tabs -->
    <div class="wppoppop-hdr-group wppoppop-hdr-center">
        <span class="wppoppop-hdr-canvas-label" style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;margin-right:2px;user-select:none;">Canvas:</span>
        <div class="wppoppop-canvas-tabs wppoppop-screen-tabs" id="wppoppop-canvas-tabs-container">
            <div class="wppoppop-canvas-tab-wrapper active">
                <button type="button" class="wppoppop-canvas-tab wppoppop-screen-tab" data-canvas="1" data-screen="1">Canvas 1</button>
            </div>
        <button type="button" class="wppoppop-btn-tool" id="wppoppop-canvas-duplicate-btn" title="Duplicate Active Canvas" aria-label="Duplicate Active Canvas">
            <span class="dashicons dashicons-admin-page"></span>
        </button>
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

                <div class="wppoppop-header-history-tools">
            <button type="button" class="wppoppop-btn-tool" id="wppoppop-btn-undo" title="Undo (Cmd/Ctrl + Z)" aria-label="Undo" disabled>
                <span class="dashicons dashicons-undo"></span>
            </button>
            <button type="button" class="wppoppop-btn-tool" id="wppoppop-btn-redo" title="Redo (Cmd/Ctrl + Y)" aria-label="Redo" disabled>
                <span class="dashicons dashicons-redo"></span>
            </button>
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

    <!-- Right Section: Preview, Embed, Save Actions (Clean Text Buttons) -->
    <div class="wppoppop-hdr-group wppoppop-hdr-right">
        <select id="wppoppop-zoom-select" class="wppoppop-zoom-select" title="Stage Zoom" aria-label="Stage Zoom">
            <option value="0.5">50%</option>
            <option value="0.75">75%</option>
            <option value="1" selected>100%</option>
            <option value="1.25">125%</option>
            <option value="fit">Fit</option>
        </select>
        <button type="button" class="wppoppop-btn wppoppop-btn-secondary" id="wppoppop-btn-preview">Preview</button>
        <button type="button" id="wppoppop-btn-embed" class="button" title="Get Embed Codes">Embed</button>
        <button type="button" class="wppoppop-btn wppoppop-btn-primary wppoppop-btn-save" id="wppoppop-btn-save">Save Popup</button>
    </div>
</div>
