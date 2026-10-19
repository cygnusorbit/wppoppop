<?php
if (!defined('ABSPATH')) {
    exit;
}
$wppoppop_total     = isset($submissions) ? count($submissions) : 0;
$wppoppop_confirmed = 0;
if (!empty($submissions)) {
    foreach ($submissions as $__s) {
        if ($__s->status === 'confirmed') {
            $wppoppop_confirmed++;
        }
    }
}
$wppoppop_pending = $wppoppop_total - $wppoppop_confirmed;
?>
<style>
.wppoppop-subs-topbar{display:flex;align-items:center;background:#fff;border:1px solid #dcdcde;height:44px;margin:10px 0 14px;padding:0 12px;gap:14px}
.wppoppop-subs-topbar h1{font-size:13px;font-weight:600;margin:0;padding:0;line-height:1}
.wppoppop-subs-topbar .spacer{flex:1}
.wppoppop-subs-toolbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:8px}
.wppoppop-subs-tabs{display:flex;gap:4px}
.wppoppop-subs-tabs button{background:#f6f7f7;border:1px solid #c3c4c7;color:#2c3338;padding:4px 10px;font-size:11px;cursor:pointer;border-radius:2px;line-height:1.4}
.wppoppop-subs-tabs button small{font-size:9px;color:#b32d2e}
.wppoppop-subs-tabs button.active{background:#c1272d;border-color:#c1272d;color:#fff;font-weight:600}
.wppoppop-subs-tabs button.active small{color:#fff}
.wppoppop-subs-searchbox{display:flex;align-items:center;gap:6px}
.wppoppop-subs-searchbox input[type=search]{width:140px;height:28px}
</style>
<div class="wppoppop-subs-topbar">
    <span class="dashicons dashicons-email-alt" style="color:#c1272d;"></span>
    <h1><?php echo esc_html__('Entries for', 'wppoppop'); ?>
        <select id="wppoppop-subs-popup-filter" data-base="<?php echo esc_url(admin_url('admin.php?page=wppoppop-submissions')); ?>" style="margin-left:6px;max-width:220px;">
            <option value="">All popups</option>
            <?php foreach ((array) $all_popups as $__p) : ?>
                <option value="<?php echo esc_attr($__p->uid); ?>" <?php selected($sel_uid, $__p->uid); ?>><?php echo esc_html($__p->title ? $__p->title : $__p->uid); ?></option>
            <?php endforeach; ?>
        </select>
    </h1>
    <span class="spacer"></span>
    <button type="button" class="button button-primary" id="wppoppop-btn-export-csv" style="display:inline-flex;align-items:center;gap:4px;">
        <span class="dashicons dashicons-download" style="font-size:16px;width:16px;height:16px;"></span> <?php echo esc_html__('Export to CSV', 'wppoppop'); ?>
    </button>
    <button type="button" class="button" id="wppoppop-table-options-btn" title="Table options" aria-label="Table options" style="display:inline-flex;align-items:center;">
        <span class="dashicons dashicons-admin-generic" style="font-size:18px;width:18px;height:18px;"></span>
    </button>
</div>
<div id="wppoppop-subs-notice" style="display:none;" class="notice is-dismissible"></div>
<div class="wppoppop-subs-toolbar">
    <div class="wppoppop-subs-tabs" id="wppoppop-subs-tabs">
        <button type="button" class="active" data-filter="all">All <small>(<span data-count="all"><?php echo (int) $wppoppop_total; ?></span>)</small></button>
        <button type="button" data-filter="pending">Pending <small>(<span data-count="pending"><?php echo (int) $wppoppop_pending; ?></span>)</small></button>
        <button type="button" data-filter="confirmed">Confirmed <small>(<span data-count="confirmed"><?php echo (int) $wppoppop_confirmed; ?></span>)</small></button>
    </div>
    <div class="wppoppop-subs-searchbox">
        <span class="dashicons dashicons-tag" style="color:#50575e;"></span>
        <input type="search" id="wppoppop-subs-search" placeholder="">
        <button type="button" class="button button-primary" id="wppoppop-subs-search-btn">Search</button>
    </div>
</div>
