<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-kpi-grid" style="display:grid;grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));gap:16px;margin-bottom:24px;">
    <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;padding:18px 20px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        <span style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">Gross Revenue</span>
        <div style="font-size:28px;font-weight:700;color:#10b981;margin-top:6px;">$<?php echo number_format($total_revenue, 2); ?></div>
        <span style="font-size:12px;color:#94a3b8;margin-top:4px;display:block;">All captured checkouts</span>
    </div>
    <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;padding:18px 20px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        <span style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">Successful Orders</span>
        <div style="font-size:28px;font-weight:700;color:#2563eb;margin-top:6px;"><?php echo number_format($completed_count); ?></div>
        <span style="font-size:12px;color:#94a3b8;margin-top:4px;display:block;">Completed transactions</span>
    </div>
    <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;padding:18px 20px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        <span style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">Average Order Value (AOV)</span>
        <div style="font-size:28px;font-weight:700;color:#f59e0b;margin-top:6px;">$<?php echo number_format($avg_order_val, 2); ?></div>
        <span style="font-size:12px;color:#94a3b8;margin-top:4px;display:block;">Per completed sale</span>
    </div>
    <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;padding:18px 20px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        <span style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">Payment Gateways</span>
        <div style="font-size:18px;font-weight:700;color:#6366f1;margin-top:8px;">
            Stripe (<?php echo $stripe_count; ?>) &bull; PayPal (<?php echo $paypal_count; ?>)
        </div>
        <span style="font-size:12px;color:#94a3b8;margin-top:4px;display:block;">Gateway distribution</span>
    </div>
</div>
