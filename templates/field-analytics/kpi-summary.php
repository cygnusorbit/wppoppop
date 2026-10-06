<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-kpi-grid" style="display:grid;grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));gap:16px;margin-bottom:24px;">
    <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;padding:18px 20px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        <span style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">Evaluated Submissions</span>
        <div style="font-size:28px;font-weight:700;color:#2563eb;margin-top:6px;"><?php echo number_format($total_submissions_count); ?></div>
        <span style="font-size:12px;color:#94a3b8;margin-top:4px;display:block;">Lead response records</span>
    </div>
    <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;padding:18px 20px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        <span style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">Custom Fields Analyzed</span>
        <div style="font-size:28px;font-weight:700;color:#10b981;margin-top:6px;"><?php echo count($fields_summary); ?></div>
        <span style="font-size:12px;color:#94a3b8;margin-top:4px;display:block;">Active form attributes</span>
    </div>
    <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;padding:18px 20px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        <span style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">Average Star Rating</span>
        <div style="font-size:28px;font-weight:700;color:#f59e0b;margin-top:6px;">
            <?php echo $total_rating_count > 0 ? esc_html($avg_rating) . ' / 5.0' : '—'; ?>
        </div>
        <span style="font-size:12px;color:#94a3b8;margin-top:4px;display:block;"><?php echo number_format($total_rating_count); ?> customer reviews</span>
    </div>
    <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;padding:18px 20px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        <span style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">Top Overall Choice</span>
        <div style="font-size:20px;font-weight:700;color:#6366f1;margin-top:8px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="<?php echo esc_attr($top_choice_label); ?>">
            <?php echo esc_html($top_choice_label); ?>
        </div>
        <span style="font-size:12px;color:#94a3b8;margin-top:4px;display:block;"><?php echo number_format($top_choice_votes); ?> selections</span>
    </div>
</div>
