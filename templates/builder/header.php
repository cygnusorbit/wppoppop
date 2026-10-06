<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<header class="wppoppop-builder-header">
    <div style="display:flex;align-items:center;gap:10px;">
        <a href="<?php echo esc_url(admin_url('admin.php?page=wppoppop')); ?>" style="color:#94a3b8;text-decoration:none;display:inline-flex;align-items:center;gap:4px;font-size:12px;font-weight:600;">
            <span class="dashicons dashicons-arrow-left-alt"></span> Exit
        </a>
        <input type="text" id="wppoppop-builder-title" value="<?php echo esc_attr($title); ?>" placeholder="Untitled Popup Campaign" style="background:#1f2937;border:1px solid #374151;color:#ffffff;border-radius:4px;padding:4px 10px;font-size:13px;font-weight:600;width:200px;">
        
        <button type="button" id="wppoppop-btn-settings" style="background:#374151;border:1px solid #4b5563;color:#ffffff;border-radius:4px;padding:5px 10px;font-size:11px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:4px;">
            <span class="dashicons dashicons-admin-generic" style="font-size:14px;width:14px;height:14px;"></span> Settings
        </button>

        <!-- Revision Control Buttons -->
        <div style="display:flex;align-items:center;gap:2px;background:#1f2937;border-radius:4px;padding:2px;border:1px solid #374151;">
            <button type="button" id="wppoppop-btn-undo" title="Undo (Cmd/Ctrl+Z)" disabled style="background:transparent;border:none;color:#94a3b8;cursor:pointer;padding:3px 6px;border-radius:3px;display:flex;align-items:center;opacity:0.4;">
                <span class="dashicons dashicons-undo" style="font-size:14px;width:14px;height:14px;"></span>
            </button>
            <button type="button" id="wppoppop-btn-redo" title="Redo (Cmd/Ctrl+Y)" disabled style="background:transparent;border:none;color:#94a3b8;cursor:pointer;padding:3px 6px;border-radius:3px;display:flex;align-items:center;opacity:0.4;">
                <span class="dashicons dashicons-redo" style="font-size:14px;width:14px;height:14px;"></span>
            </button>
        </div>
    </div>

    <!-- Multi-Screen Sequence Tabs -->
    <div class="wppoppop-screen-tabs">
        <button type="button" class="wppoppop-screen-tab active" data-screen="1">Screen 1</button>
        <button type="button" class="wppoppop-screen-tab" data-screen="2">Screen 2</button>
        <button type="button" class="wppoppop-screen-tab" data-screen="3">Screen 3</button>
    </div>

    <!-- Actions & Viewport Controls -->
    <div style="display:flex;align-items:center;gap:8px;">
        <div class="wppoppop-viewport-toggles">
            <button type="button" class="wppoppop-viewport-btn active" data-mode="desktop" title="Desktop Viewport (640px)">
                <span class="dashicons dashicons-desktop" style="font-size:14px;width:14px;height:14px;"></span>
            </button>
            <button type="button" class="wppoppop-viewport-btn" data-mode="mobile" title="Mobile Viewport (360px)">
                <span class="dashicons dashicons-smartphone" style="font-size:14px;width:14px;height:14px;"></span>
            </button>
        </div>

        <button type="button" id="wppoppop-btn-preview" style="background:#1f2937;border:1px solid #374151;color:#ffffff;border-radius:4px;padding:5px 10px;font-size:11px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:4px;">
            <span class="dashicons dashicons-visibility" style="font-size:14px;width:14px;height:14px;"></span> Preview
        </button>

        <button type="button" id="wppoppop-btn-embed" style="background:#1f2937;border:1px solid #374151;color:#ffffff;border-radius:4px;padding:5px 10px;font-size:11px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:4px;">
            <span class="dashicons dashicons-shortcode" style="font-size:14px;width:14px;height:14px;"></span> Embed
        </button>

        <button type="button" id="wppoppop-btn-save" style="background:#2563eb;border:none;color:#ffffff;border-radius:4px;padding:6px 14px;font-size:12px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:5px;">
            <span class="dashicons dashicons-saved" style="font-size:14px;width:14px;height:14px;"></span> Save Popup
        </button>
    </div>
</header>
