<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-kpi-grid" style="display:grid;grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));gap:16px;margin-bottom:24px;">
    <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;padding:18px 20px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        <span style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;"><?php esc_html_e('Total Logged Events', 'wppoppop'); ?></span>
        <div style="font-size:28px;font-weight:700;color:#2563eb;margin-top:6px;"><?php echo number_format($total_logs); ?></div>
        <span style="font-size:12px;color:#94a3b8;margin-top:4px;display:block;"><?php esc_html_e('Audit entries in system', 'wppoppop'); ?></span>
    </div>
    <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;padding:18px 20px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        <span style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;"><?php esc_html_e('Integration Failures', 'wppoppop'); ?></span>
        <div style="font-size:28px;font-weight:700;color:<?php echo $error_logs > 0 ? '#ef4444' : '#10b981'; ?>;margin-top:6px;">
            <?php echo number_format($error_logs); ?>
        </div>
        <span style="font-size:12px;color:#94a3b8;margin-top:4px;display:block;"><?php esc_html_e('Errors and API exceptions', 'wppoppop'); ?></span>
    </div>
    <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;padding:18px 20px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        <span style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;"><?php esc_html_e('SMS & Webhooks Dispatched', 'wppoppop'); ?></span>
        <div style="font-size:28px;font-weight:700;color:#6366f1;margin-top:6px;"><?php echo number_format($dispatch_logs); ?></div>
        <span style="font-size:12px;color:#94a3b8;margin-top:4px;display:block;"><?php esc_html_e('Outgoing API notifications', 'wppoppop'); ?></span>
    </div>
    <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;padding:18px 20px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        <span style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;"><?php esc_html_e('Verifications & Leads', 'wppoppop'); ?></span>
        <div style="font-size:28px;font-weight:700;color:#10b981;margin-top:6px;"><?php echo number_format($optin_logs); ?></div>
        <span style="font-size:12px;color:#94a3b8;margin-top:4px;display:block;"><?php esc_html_e('Confirmed opt-ins logged', 'wppoppop'); ?></span>
    </div>
</div>
