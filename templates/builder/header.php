<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-builder-header" style="background:#111827;height:52px;display:flex;align-items:center;justify-content:space-between;padding:0 16px;color:#fff;border-bottom:1px solid #1f2937;z-index:9999;position:relative;box-sizing:border-box;">
    <div style="display:flex;align-items:center;gap:10px;">
        <a href="<?php echo esc_url(admin_url('admin.php?page=wppoppop')); ?>" id="wppoppop-btn-back-dashboard" class="button button-secondary" style="background:#1f2937;color:#f3f4f6;border-color:#374151;display:inline-flex;align-items:center;gap:6px;height:32px;padding:0 10px;text-decoration:none;" title="Return to Dashboard">
            <span class="dashicons dashicons-arrow-left-alt" style="font-size:16px;width:16px;height:16px;"></span> Dashboard
        </a>
        <button type="button" id="wppoppop-btn-settings" class="button button-secondary" style="background:#1f2937;color:#f3f4f6;border-color:#374151;display:inline-flex;align-items:center;gap:6px;height:32px;padding:0 10px;" title="Campaign Settings">
            <span class="dashicons dashicons-admin-generic" style="font-size:16px;width:16px;height:16px;"></span> Settings
        </button>
        <input type="text" id="wppoppop-builder-title" value="<?php echo esc_attr(isset($popup) && $popup ? $popup->title : 'Untitled Popup Campaign'); ?>" placeholder="Campaign Title..." style="background:#1f2937;border:1px solid #374151;color:#fff;font-weight:600;font-size:13px;border-radius:4px;padding:4px 10px;width:220px;height:32px;">
    </div>

    <!-- Screen Sequence Tabs & Viewport Switcher -->
    <div style="display:flex;align-items:center;gap:14px;">
        <div class="wppoppop-screen-tabs" style="display:flex;gap:4px;background:#1f2937;padding:3px;border-radius:6px;border:1px solid #374151;">
            <button type="button" class="wppoppop-screen-tab active" data-screen="1" style="background:#2563eb;color:#fff;border:none;border-radius:4px;padding:4px 10px;font-size:11px;font-weight:700;cursor:pointer;">Screen 1</button>
            <button type="button" class="wppoppop-screen-tab" data-screen="2" style="background:transparent;color:#9ca3af;border:none;border-radius:4px;padding:4px 10px;font-size:11px;font-weight:700;cursor:pointer;">Screen 2</button>
            <button type="button" class="wppoppop-screen-tab" data-screen="3" style="background:transparent;color:#9ca3af;border:none;border-radius:4px;padding:4px 10px;font-size:11px;font-weight:700;cursor:pointer;">Screen 3</button>
        </div>

        <div class="wppoppop-viewport-toggles" style="display:flex;background:#1f2937;border-radius:6px;border:1px solid #374151;overflow:hidden;">
            <button type="button" class="wppoppop-viewport-btn active" data-mode="desktop" style="background:#374151;color:#fff;border:none;padding:5px 10px;font-size:11px;cursor:pointer;" title="Desktop Viewport (640px)">
                <span class="dashicons dashicons-desktop" style="font-size:14px;width:14px;height:14px;"></span>
            </button>
            <button type="button" class="wppoppop-viewport-btn" data-mode="mobile" style="background:transparent;color:#9ca3af;border:none;padding:5px 10px;font-size:11px;cursor:pointer;" title="Mobile Viewport (360px)">
                <span class="dashicons dashicons-smartphone" style="font-size:14px;width:14px;height:14px;"></span>
            </button>
        </div>
    </div>

    <!-- Top Action Triggers -->
    <div style="display:flex;align-items:center;gap:8px;">
        <button type="button" id="wppoppop-btn-preview" class="button" style="background:#1f2937;border-color:#374151;color:#f3f4f6;display:inline-flex;align-items:center;gap:4px;height:32px;" title="Live Preview">
            <span class="dashicons dashicons-visibility" style="font-size:16px;width:16px;height:16px;"></span> Preview
        </button>
        <button type="button" id="wppoppop-btn-embed" class="button" style="background:#1f2937;border-color:#374151;color:#f3f4f6;display:inline-flex;align-items:center;gap:4px;height:32px;" title="Embed Codes">
            <span class="dashicons dashicons-editor-code" style="font-size:16px;width:16px;height:16px;"></span> Embed
        </button>
        <button type="button" id="wppoppop-btn-save" class="button button-primary" style="background:#c2185b;border-color:#ad1457;font-weight:700;display:inline-flex;align-items:center;gap:6px;height:32px;padding:0 14px;">
            <span class="dashicons dashicons-saved" style="font-size:16px;width:16px;height:16px;"></span> Save Popup
        </button>
    </div>
</div>
