<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-dashboard-header" style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;padding-top:10px;">
    <div>
        <h1 class="wp-heading-inline" style="margin:0;font-size:24px;font-weight:700;color:#1e293b;">Popups & Campaigns</h1>
        <p style="margin:4px 0 0;color:#64748b;font-size:13px;">Manage, analyze, and optimize your visual popup campaigns.</p>
    </div>
    <div style="display:flex;gap:10px;">
        <button type="button" class="button button-secondary" id="wppoppop-btn-open-import" style="display:inline-flex;align-items:center;gap:4px;">
            <span class="dashicons dashicons-upload" style="font-size:16px;width:16px;height:16px;"></span> Import JSON
        </button>
        <a href="<?php echo esc_url(admin_url('admin.php?page=wppoppop-builder')); ?>" class="button button-primary" style="display:inline-flex;align-items:center;gap:4px;background:#c2185b;border-color:#ad1457;">
            <span class="dashicons dashicons-plus-alt2" style="font-size:16px;width:16px;height:16px;"></span> Create Popup
        </a>
    </div>
</div>
<div id="wppoppop-dash-notice" style="display:none;" class="notice is-dismissible"></div>
