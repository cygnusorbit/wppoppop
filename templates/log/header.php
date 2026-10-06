<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-log-header" style="margin-bottom:20px;padding-top:10px;">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;">
        <div>
            <h1 class="wp-heading-inline" style="margin:0;font-size:24px;font-weight:700;color:#1e293b;">Activity & Delivery Event Log</h1>
            <p style="margin:4px 0 0;color:#64748b;font-size:13px;">Audit integration dispatches, webhook payloads, SMS deliveries, and background processes.</p>
        </div>
        <div style="display:flex;gap:10px;align-items:center;">
            <form method="post" onsubmit="return confirm('Are you sure you want to permanently clear all activity event logs?');" style="margin:0;">
                <?php wp_nonce_field('wppoppop_clear_logs_action', 'wppoppop_clear_logs_nonce'); ?>
                <button type="submit" name="wppoppop_clear_logs" class="button" style="color:#ef4444;border-color:#fca5a5;display:inline-flex;align-items:center;gap:4px;">
                    <span class="dashicons dashicons-trash" style="font-size:16px;width:16px;height:16px;"></span> Clear Event Log
                </button>
            </form>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div style="display:flex;gap:12px;align-items:center;margin-top:16px;flex-wrap:wrap;">
        <label for="wppoppop-log-filter" style="font-size:12px;font-weight:600;color:#475569;">Filter Event:</label>
        <select id="wppoppop-log-filter" onchange="location = this.value;" style="padding:6px 12px;font-size:13px;border-radius:4px;border:1px solid #cbd5e1;background:#fff;">
            <option value="<?php echo esc_url(admin_url('admin.php?page=wppoppop-log')); ?>">All Event Types</option>
            <option value="<?php echo esc_url(admin_url('admin.php?page=wppoppop-log&type=submission')); ?>" <?php selected($selected_type, 'submission'); ?>>Submissions & Leads</option>
            <option value="<?php echo esc_url(admin_url('admin.php?page=wppoppop-log&type=optin_confirmed')); ?>" <?php selected($selected_type, 'optin_confirmed'); ?>>Double Opt-In Confirmed</option>
            <option value="<?php echo esc_url(admin_url('admin.php?page=wppoppop-log&type=sms_dispatched')); ?>" <?php selected($selected_type, 'sms_dispatched'); ?>>Twilio SMS Sent</option>
            <option value="<?php echo esc_url(admin_url('admin.php?page=wppoppop-log&type=wc_coupon_created')); ?>" <?php selected($selected_type, 'wc_coupon_created'); ?>>WooCommerce Coupons</option>
            <option value="<?php echo esc_url(admin_url('admin.php?page=wppoppop-log&type=errors')); ?>" <?php selected($selected_type, 'errors'); ?>>Failures & Errors Only</option>
        </select>

        <input type="text" id="wppoppop-log-search" placeholder="Search message text or payload..." style="width:260px;padding:6px 12px;font-size:13px;border-radius:4px;border:1px solid #cbd5e1;">
    </div>
</div>
<?php if (!empty($notice_msg)) : ?>
    <div class="notice notice-success is-dismissible" style="margin-bottom:20px;"><p><?php echo esc_html($notice_msg); ?></p></div>
<?php endif; ?>
