<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-kpi-grid" style="display:grid;grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));gap:16px;margin-bottom:24px;">
    <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;padding:18px 20px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        <span style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;"><?php esc_html_e('Evaluated Submissions', 'wppoppop'); ?></span>
        <div style="font-size:28px;font-weight:700;color:#2563eb;margin-top:6px;"><?php echo number_format($total_submissions_count); ?></div>
        <span style="font-size:12px;color:#94a3b8;margin-top:4px;display:block;"><?php esc_html_e('Lead response records', 'wppoppop'); ?></span>
    </div>
    <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;padding:18px 20px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        <span style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;"><?php esc_html_e('Custom Fields Analyzed', 'wppoppop'); ?></span>
        <div style="font-size:28px;font-weight:700;color:#10b981;margin-top:6px;"><?php echo count($fields_summary); ?></div>
        <span style="font-size:12px;color:#94a3b8;margin-top:4px;display:block;"><?php esc_html_e('Active form attributes', 'wppoppop'); ?></span>
    </div>
    <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;padding:18px 20px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        <span style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;"><?php esc_html_e('Average Star Rating', 'wppoppop'); ?></span>
        <div style="font-size:28px;font-weight:700;color:#f59e0b;margin-top:6px;">
            <?php echo $total_rating_count > 0 ? esc_html($avg_rating) . ' / 5.0' : '—'; ?>
        </div>
        <span style="font-size:12px;color:#94a3b8;margin-top:4px;display:block;"><?php /* translators: %s: number of reviews. */ echo esc_html(sprintf(__('%s customer reviews', 'wppoppop'), number_format_i18n($total_rating_count))); ?></span>
    </div>
    <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;padding:18px 20px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        <span style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;"><?php esc_html_e('Top Overall Choice', 'wppoppop'); ?></span>
        <div style="font-size:20px;font-weight:700;color:#6366f1;margin-top:8px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="<?php echo esc_attr($top_choice_label); ?>">
            <?php echo esc_html($top_choice_label); ?>
        </div>
        <span style="font-size:12px;color:#94a3b8;margin-top:4px;display:block;"><?php /* translators: %s: number of selections. */ echo esc_html(sprintf(__('%s selections', 'wppoppop'), number_format_i18n($top_choice_votes))); ?></span>
    </div>
</div>
