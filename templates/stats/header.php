<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-stats-header" style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;padding-top:10px;">
    <div>
        <h1 class="wp-heading-inline" style="margin:0;font-size:24px;font-weight:700;color:#1e293b;">Visual Statistics & Analytics</h1>
        <p style="margin:4px 0 0;color:#64748b;font-size:13px;">Track campaign performance, conversion velocity, and audience engagement trends.</p>
    </div>
    <div style="display:flex;gap:8px;align-items:center;background:#ffffff;padding:4px;border-radius:6px;border:1px solid #cbd5e1;">
        <a href="<?php echo esc_url(add_query_arg('range', '7days')); ?>" 
           class="button <?php echo $range === '7days' ? 'button-primary' : ''; ?>" 
           style="font-size:12px;border:none;box-shadow:none;<?php echo $range === '7days' ? 'background:#2563eb;color:#fff;' : 'background:transparent;color:#475569;'; ?>">
           Last 7 Days
        </a>
        <a href="<?php echo esc_url(add_query_arg('range', '30days')); ?>" 
           class="button <?php echo $range === '30days' ? 'button-primary' : ''; ?>" 
           style="font-size:12px;border:none;box-shadow:none;<?php echo $range === '30days' ? 'background:#2563eb;color:#fff;' : 'background:transparent;color:#475569;'; ?>">
           Last 30 Days
        </a>
        <a href="<?php echo esc_url(add_query_arg('range', 'all')); ?>" 
           class="button <?php echo $range === 'all' ? 'button-primary' : ''; ?>" 
           style="font-size:12px;border:none;box-shadow:none;<?php echo $range === 'all' ? 'background:#2563eb;color:#fff;' : 'background:transparent;color:#475569;'; ?>">
           All Time
        </a>
    </div>
</div>
