<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
<?php
if (!defined('ABSPATH')) {
    exit;
}
$__wpp_set   = get_option('wppoppop_settings', []);
$__wpp_set   = is_array($__wpp_set) ? $__wpp_set : [];
$__wpp_vp_on = !array_key_exists('builder_viewport_toggle', $__wpp_set) || !empty($__wpp_set['builder_viewport_toggle']);
$__wpp_zm_on = !array_key_exists('builder_zoom_select', $__wpp_set) || !empty($__wpp_set['builder_zoom_select']);
?>
<div class="wppoppop-builder-header">
    <!-- Left Section: Navigation, Settings, Title -->
    <div class="wppoppop-hdr-group wppoppop-hdr-left">
                        <!-- Day / Night Mode Toggle -->
        <button type="button" class="wppoppop-btn-icon wppoppop-btn-theme" id="wppoppop-btn-theme-toggle" title="Toggle Day / Night Mode" aria-label="Toggle Day / Night Mode" style="cursor:pointer;pointer-events:auto;">
            <svg class="wppoppop-icon-sun" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;pointer-events:none;"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
            <svg class="wppoppop-icon-moon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block;pointer-events:none;"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
        </button>
        <button type="button" class="wppoppop-btn-icon wppoppop-btn-settings" id="wppoppop-btn-settings" data-action="settings" title="Popup Settings" aria-label="Popup Settings" style="cursor:pointer;pointer-events:auto;"><span class="dashicons dashicons-admin-generic" style="pointer-events:none;"></span></button>
        <input type="text" id="wppoppop-builder-title" value="<?php echo esc_attr($popup ? $popup->title : 'Untitled Popup'); ?>" placeholder="Popup Title..." title="Popup Title">
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
    </div>

    <!-- Center Section: Canvas Label (Outside Workspace) & Sequence Tabs -->
    <div class="wppoppop-hdr-group wppoppop-hdr-center">

        <div class="wppoppop-viewport-toggles"<?php echo $__wpp_vp_on ? '' : ' style="display:none !important;" aria-hidden="true"'; ?>>
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
        <select id="wppoppop-zoom-select" class="wppoppop-zoom-select"<?php echo $__wpp_zm_on ? '' : ' style="display:none !important;" aria-hidden="true" tabindex="-1"'; ?> autocomplete="off" title="Stage Zoom" aria-label="Stage Zoom">
            <option value="0.5">50%</option>
            <option value="0.75">75%</option>
            <option value="1" selected>100%</option>
            <option value="1.25">125%</option>
            <option value="fit">Fit</option>
        </select>
        <button type="button" class="wppoppop-btn wppoppop-btn-secondary wppoppop-hdr-icon-btn" id="wppoppop-btn-preview" title="Preview" aria-label="Preview"><i class="fa-solid fa-eye" aria-hidden="true"></i></button>
        <button type="button" id="wppoppop-btn-embed" class="button wppoppop-hdr-icon-btn" title="Get Embed Codes" aria-label="Embed"><i class="fa-solid fa-code" aria-hidden="true"></i></button>
        <button type="button" class="wppoppop-btn wppoppop-btn-primary wppoppop-btn-save" id="wppoppop-btn-save">Save</button>
    </div>
</div>
