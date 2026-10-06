<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-fa-header" style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;padding-top:10px;">
    <div>
        <h1 class="wp-heading-inline" style="margin:0;font-size:24px;font-weight:700;color:#1e293b;">Field Analytics & Response Intelligence</h1>
        <p style="margin:4px 0 0;color:#64748b;font-size:13px;">Analyze aggregated form choices, option distributions, and customer satisfaction ratings.</p>
    </div>
    <div style="display:flex;align-items:center;gap:10px;">
        <label for="wppoppop-fa-popup-filter" style="font-size:12px;font-weight:600;color:#475569;">Filter Campaign:</label>
        <select id="wppoppop-fa-popup-filter" onchange="location = this.value;" style="padding:6px 12px;font-size:13px;border-radius:4px;border:1px solid #cbd5e1;background:#fff;">
            <option value="<?php echo esc_url(admin_url('admin.php?page=wppoppop-field-analytics')); ?>">All Campaigns (Aggregated)</option>
            <?php foreach ($all_popups as $p) : ?>
                <option value="<?php echo esc_url(admin_url('admin.php?page=wppoppop-field-analytics&popup_uid=' . $p->uid)); ?>" <?php selected($selected_uid, $p->uid); ?>>
                    <?php echo esc_html($p->title); ?> (<?php echo esc_html($p->uid); ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>
