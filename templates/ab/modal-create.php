<?php
if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$available_popups = $wpdb->get_results("SELECT uid, title FROM {$wpdb->prefix}wppoppop_items WHERE status = 'publish' ORDER BY id DESC");
?>
<div class="wppoppop-modal-backdrop" id="wppoppop-create-ab-modal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,0.7);backdrop-filter:blur(3px);z-index:99999;align-items:center;justify-content:center;">
    <div style="background:#ffffff;border-radius:8px;width:540px;max-width:92vw;box-shadow:0 20px 25px -5px rgba(0,0,0,0.25);overflow:hidden;display:flex;flex-direction:column;max-height:85vh;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;background:#0f172a;color:#ffffff;">
            <h3 style="margin:0;font-size:15px;font-weight:700;">Create A/B Split Testing Experiment</h3>
            <button type="button" id="wppoppop-create-ab-close" style="background:transparent;border:none;color:#94a3b8;font-size:22px;cursor:pointer;line-height:1;">&times;</button>
        </div>
        <div style="padding:20px;overflow-y:auto;flex:1;">
            <label style="display:block;font-size:12px;font-weight:700;color:#334155;margin-bottom:6px;">Campaign Title</label>
            <input type="text" id="wppoppop-ab-title-input" class="widefat" placeholder="e.g. Summer Promo Headline Test" style="padding:8px 12px;font-size:13px;border-radius:4px;border:1px solid #cbd5e1;">

            <label style="display:block;font-size:12px;font-weight:700;color:#334155;margin:16px 0 6px 0;">Select Popups for Testing (Minimum 2)</label>
            <p style="font-size:11px;color:#64748b;margin:0 0 10px 0;">Traffic will be distributed randomly across your selected variations.</p>

            <div style="max-height:220px;overflow-y:auto;border:1px solid #e2e8f0;border-radius:6px;padding:10px;background:#f8fafc;">
                <?php if (empty($available_popups)) : ?>
                    <p style="margin:0;color:#ef4444;font-size:12px;">No published popups available. Please create and publish popups in the builder first.</p>
                <?php else : ?>
                    <?php foreach ($available_popups as $ap) : ?>
                        <label style="display:flex;align-items:center;gap:8px;padding:6px;cursor:pointer;font-size:13px;color:#1e293b;border-bottom:1px solid #f1f5f9;">
                            <input type="checkbox" class="wppoppop-ab-popup-checkbox" value="<?php echo esc_attr($ap->uid); ?>">
                            <span><strong><?php echo esc_html($ap->title); ?></strong> <code style="font-size:11px;color:#64748b;">(<?php echo esc_html($ap->uid); ?>)</code></span>
                        </label>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <div style="display:flex;justify-content:flex-end;gap:8px;padding:14px 20px;background:#f8fafc;border-top:1px solid #e2e8f0;">
            <button type="button" class="button" id="wppoppop-create-ab-cancel">Cancel</button>
            <button type="button" class="button button-primary" id="wppoppop-create-ab-submit" style="background:#c2185b;border-color:#ad1457;">Launch Campaign</button>
        </div>
    </div>
</div>
