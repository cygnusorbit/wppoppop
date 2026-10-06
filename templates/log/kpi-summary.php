<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-kpi-grid" style="display:grid;grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));gap:16px;margin-bottom:24px;">
    <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;padding:18px 20px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        <span style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">Total Logged Events</span>
        <div style="font-size:28px;font-weight:700;color:#2563eb;margin-top:6px;"><?php echo number_format($total_logs); ?></div>
        <span style="font-size:12px;color:#94a3b8;margin-top:4px;display:block;">Audit entries in system</span>
    </div>
    <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;padding:18px 20px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        <span style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">Integration Failures</span>
        <div style="font-size:28px;font-weight:700;color:<?php echo $error_logs > 0 ? '#ef4444' : '#10b981'; ?>;margin-top:6px;">
            <?php echo number_format($error_logs); ?>
        </div>
        <span style="font-size:12px;color:#94a3b8;margin-top:4px;display:block;">Errors and API exceptions</span>
    </div>
    <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;padding:18px 20px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        <span style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">SMS & Webhooks Dispatched</span>
        <div style="font-size:28px;font-weight:700;color:#6366f1;margin-top:6px;"><?php echo number_format($dispatch_logs); ?></div>
        <span style="font-size:12px;color:#94a3b8;margin-top:4px;display:block;">Outgoing API notifications</span>
    </div>
    <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;padding:18px 20px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        <span style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">Verifications & Leads</span>
        <div style="font-size:28px;font-weight:700;color:#10b981;margin-top:6px;"><?php echo number_format($optin_logs); ?></div>
        <span style="font-size:12px;color:#94a3b8;margin-top:4px;display:block;">Confirmed opt-ins logged</span>
    </div>
</div>
