<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- template variables are local to the method that includes this file
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-fa-header" style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;padding-top:10px;">
    <div>
        <h1 class="wp-heading-inline" style="margin:0;font-size:24px;font-weight:700;color:#1e293b;"><?php esc_html_e('Field Analytics & Response Intelligence', 'wppoppop'); ?></h1>
        <p style="margin:4px 0 0;color:#64748b;font-size:13px;"><?php esc_html_e('Analyze aggregated form choices, option distributions, and customer satisfaction ratings.', 'wppoppop'); ?></p>
    </div>
    <div style="display:flex;align-items:center;gap:10px;">
        <label for="wppoppop-fa-popup-filter" style="font-size:12px;font-weight:600;color:#475569;"><?php esc_html_e('Filter Campaign:', 'wppoppop'); ?></label>
        <select id="wppoppop-fa-popup-filter" onchange="location = this.value;" style="padding:6px 12px;font-size:13px;border-radius:4px;border:1px solid #cbd5e1;background:#fff;">
            <option value="<?php echo esc_url(admin_url('admin.php?page=wppoppop-field-analytics')); ?>"><?php esc_html_e('All Campaigns (Aggregated)', 'wppoppop'); ?></option>
            <?php foreach ($all_popups as $p) : ?>
                <option value="<?php echo esc_url(admin_url('admin.php?page=wppoppop-field-analytics&popup_uid=' . $p->uid)); ?>" <?php selected($selected_uid, $p->uid); ?>>
                    <?php echo esc_html($p->title); ?> (<?php echo esc_html($p->uid); ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>
