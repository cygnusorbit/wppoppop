<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-log-header" style="margin-bottom:20px;padding-top:10px;">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;">
        <div>
            <h1 class="wp-heading-inline" style="margin:0;font-size:24px;font-weight:700;color:#1e293b;"><?php esc_html_e('Activity & Delivery Event Log', 'wppoppop'); ?></h1>
            <p style="margin:4px 0 0;color:#64748b;font-size:13px;"><?php esc_html_e('Audit integration dispatches, webhook payloads, SMS deliveries, and background processes.', 'wppoppop'); ?></p>
        </div>
        <div style="display:flex;gap:10px;align-items:center;">
            <form method="post" onsubmit="return confirm('Are you sure you want to permanently clear all activity event logs?');" style="margin:0;">
                <?php wp_nonce_field('wppoppop_clear_logs_action', 'wppoppop_clear_logs_nonce'); ?>
                <button type="submit" name="wppoppop_clear_logs" class="button" style="color:#ef4444;border-color:#fca5a5;display:inline-flex;align-items:center;gap:4px;">
                    <span class="dashicons dashicons-trash" style="font-size:16px;width:16px;height:16px;"></span> <?php esc_html_e('Clear Event Log', 'wppoppop'); ?>
                </button>
            </form>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div style="display:flex;gap:12px;align-items:center;margin-top:16px;flex-wrap:wrap;">
        <label for="wppoppop-log-filter" style="font-size:12px;font-weight:600;color:#475569;"><?php esc_html_e('Filter Event:', 'wppoppop'); ?></label>
        <select id="wppoppop-log-filter" onchange="location = this.value;" style="padding:6px 12px;font-size:13px;border-radius:4px;border:1px solid #cbd5e1;background:#fff;">
            <option value="<?php echo esc_url(admin_url('admin.php?page=wppoppop-log')); ?>"><?php esc_html_e('All Event Types', 'wppoppop'); ?></option>
            <option value="<?php echo esc_url(admin_url('admin.php?page=wppoppop-log&type=submission')); ?>" <?php selected($selected_type, 'submission'); ?>><?php esc_html_e('Submissions & Leads', 'wppoppop'); ?></option>
            <option value="<?php echo esc_url(admin_url('admin.php?page=wppoppop-log&type=optin_confirmed')); ?>" <?php selected($selected_type, 'optin_confirmed'); ?>><?php esc_html_e('Double Opt-In Confirmed', 'wppoppop'); ?></option>
            <option value="<?php echo esc_url(admin_url('admin.php?page=wppoppop-log&type=sms_dispatched')); ?>" <?php selected($selected_type, 'sms_dispatched'); ?>><?php esc_html_e('Twilio SMS Sent', 'wppoppop'); ?></option>
            <option value="<?php echo esc_url(admin_url('admin.php?page=wppoppop-log&type=wc_coupon_created')); ?>" <?php selected($selected_type, 'wc_coupon_created'); ?>><?php esc_html_e('WooCommerce Coupons', 'wppoppop'); ?></option>
            <option value="<?php echo esc_url(admin_url('admin.php?page=wppoppop-log&type=errors')); ?>" <?php selected($selected_type, 'errors'); ?>><?php esc_html_e('Failures & Errors Only', 'wppoppop'); ?></option>
        </select>

        <input type="text" id="wppoppop-log-search" placeholder="<?php esc_attr_e('Search message text or payload...', 'wppoppop'); ?>" style="width:260px;padding:6px 12px;font-size:13px;border-radius:4px;border:1px solid #cbd5e1;">
    </div>
</div>
<?php if (!empty($notice_msg)) : ?>
    <div class="notice notice-success is-dismissible" style="margin-bottom:20px;"><p><?php echo esc_html($notice_msg); ?></p></div>
<?php endif; ?>
