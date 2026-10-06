<?php
if (!defined('ABSPATH')) {
    exit;
}

$total_popups = count($popups);
$total_impressions = 0;
$total_submissions = 0;
$total_confirmations = 0;

foreach ($popups as $p) {
    $total_impressions   += intval($p->impressions ?? 0);
    $total_submissions   += intval($p->submissions ?? 0);
    $total_confirmations += intval($p->confirmations ?? 0);
}

$global_conversion_rate = $total_impressions > 0 ? round(($total_submissions / $total_impressions) * 100, 1) : 0;
?>
<div class="wppoppop-kpi-grid" style="display:grid;grid-template-columns:repeat(auto-fit, minmax(210px, 1fr));gap:16px;margin-bottom:24px;">
    <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;padding:16px 20px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        <span style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">Total Campaigns</span>
        <div style="font-size:26px;font-weight:700;color:#1e293b;margin-top:6px;"><?php echo number_format($total_popups); ?></div>
    </div>
    <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;padding:16px 20px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        <span style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">Total Impressions</span>
        <div style="font-size:26px;font-weight:700;color:#2563eb;margin-top:6px;"><?php echo number_format($total_impressions); ?></div>
    </div>
    <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;padding:16px 20px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        <span style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">Leads Captured</span>
        <div style="font-size:26px;font-weight:700;color:#10b981;margin-top:6px;"><?php echo number_format($total_submissions); ?></div>
    </div>
    <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;padding:16px 20px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        <span style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">Conversion Rate</span>
        <div style="font-size:26px;font-weight:700;color:#f59e0b;margin-top:6px;"><?php echo esc_html($global_conversion_rate); ?>%</div>
    </div>
</div>
