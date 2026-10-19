<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-builder-header">
    <!-- Left Section: Navigation, Settings, Title -->
    <div class="wppoppop-hdr-group wppoppop-hdr-left">
        <a href="<?php echo esc_url(admin_url('admin.php?page=wppoppop')); ?>" class="button button-secondary wppoppop-hdr-dash-btn" title="Back to Popup List" aria-label="Back to Popup List">Popup List</a>
                        <!-- Day / Night Mode Toggle -->
        <button type="button" class="wppoppop-btn-icon wppoppop-btn-theme" id="wppoppop-btn-theme-toggle" title="Toggle Day / Night Mode" aria-label="Toggle Day / Night Mode" style="cursor:pointer;pointer-events:auto;">
            <svg class="wppoppop-icon-sun" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;pointer-events:none;"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
            <svg class="wppoppop-icon-moon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block;pointer-events:none;"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
        </button>
        <button type="button" class="wppoppop-btn-icon wppoppop-btn-settings" id="wppoppop-btn-settings" data-action="settings" title="Campaign Settings" aria-label="Campaign Settings" style="cursor:pointer;pointer-events:auto;"><span class="dashicons dashicons-admin-generic" style="pointer-events:none;"></span></button>
        <input type="text" id="wppoppop-builder-title" value="<?php echo esc_attr($popup ? $popup->title : 'Untitled Popup Campaign'); ?>" placeholder="Campaign Title..." title="Campaign Title">
    </div>

    <!-- Center Section: Canvas Label (Outside Workspace) & Sequence Tabs -->
    <div class="wppoppop-hdr-group wppoppop-hdr-center">
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
        <select id="wppoppop-zoom-select" class="wppoppop-zoom-select" autocomplete="off" title="Stage Zoom" aria-label="Stage Zoom">
            <option value="0.5">50%</option>
            <option value="0.75">75%</option>
            <option value="1" selected>100%</option>
            <option value="1.25">125%</option>
            <option value="fit">Fit</option>
        </select>
        <div class="wppoppop-snap-group" role="group" aria-label="Snapping">
<label class="wppoppop-snap-switch" title="Snap to other objects' edges and centers (hold Alt to bypass)">
                <input type="checkbox" id="wppoppop-snap-object" class="wppoppop-snap-toggle" data-snap="object">
                <span class="wppoppop-snap-slider" aria-hidden="true"></span>
                <span class="wppoppop-snap-text">Snap Object</span>
            </label>
        </div>
        <button type="button" class="wppoppop-btn wppoppop-btn-secondary" id="wppoppop-btn-preview">Preview</button>
        <button type="button" id="wppoppop-btn-embed" class="button" title="Get Embed Codes">Embed</button>
        <button type="button" class="wppoppop-btn wppoppop-btn-primary wppoppop-btn-save" id="wppoppop-btn-save">Save Popup</button>
    </div>
</div>
