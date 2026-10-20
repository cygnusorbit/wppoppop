<?php
if (!defined('ABSPATH')) {
    exit;
}
$wppoppop_total     = isset($submissions) ? count($submissions) : 0;
$wppoppop_confirmed = 0;
$wppoppop_unread    = 0;
if (!empty($submissions)) {
    foreach ($submissions as $__s) {
        if (empty($__s->is_read)) {
            $wppoppop_unread++;
        }
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
.wppoppop-subs-popup-tabs{display:flex;flex-wrap:wrap;gap:0;margin:0 0 12px;border-bottom:1px solid #c3c4c7}
.wppoppop-subs-popup-tabs .wppoppop-ptab{display:inline-block;padding:8px 14px;margin:0 4px -1px 0;font-size:12px;color:#2c3338;text-decoration:none;background:#f6f7f7;border:1px solid #c3c4c7;border-bottom-color:#c3c4c7;max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;box-sizing:border-box}
.wppoppop-subs-popup-tabs .wppoppop-ptab:hover{background:#fff;color:#c1272d}
.wppoppop-subs-popup-tabs .wppoppop-ptab:focus{outline:0;box-shadow:0 0 0 1px #c1272d}
.wppoppop-subs-popup-tabs .wppoppop-ptab.active{background:#fff;color:#c1272d;font-weight:600;border-bottom-color:#fff;border-top:2px solid #c1272d}
.wppoppop-subs-popup-tabs .wppoppop-ptab small{font-size:10px;color:#787c82}
.wppoppop-subs-searchbox input[type=search]{width:140px;height:28px}
</style>
<?php
$bar_title   = __('Submissions', 'wppoppop');
$bar_icon    = 'dashicons-email-alt';
$bar_actions = WPPOPPOP_PATH . 'templates/submissions/bar-actions.php';
$bar_popup_page = 'wppoppop-submissions';
?>
<h1 class="screen-reader-text"><?php esc_html_e('Submissions', 'wppoppop'); ?></h1>
<div class="wppoppop-dash" style="margin:0 0 20px;max-width:none;">
    <?php include WPPOPPOP_PATH . 'templates/partials/dash-bar.php'; ?>
</div>
<div id="wppoppop-subs-notice" style="display:none;" class="notice is-dismissible"></div>
<?php
$wppoppop_tab_base   = admin_url('admin.php?page=wppoppop-submissions');
$wppoppop_tab_counts = [];
$wppoppop_tab_total  = 0;
if (!empty($all_popups) || true) {
    $__rows = $wpdb->get_results("SELECT popup_uid, COUNT(*) AS c FROM {$wpdb->prefix}wppoppop_submissions GROUP BY popup_uid");
    foreach ((array) $__rows as $__r) {
        $wppoppop_tab_counts[(string) $__r->popup_uid] = (int) $__r->c;
        $wppoppop_tab_total += (int) $__r->c;
    }
}
?>
<div class="wppoppop-subs-popup-tabs" id="wppoppop-subs-popup-tabs" role="tablist" aria-label="<?php echo esc_attr__('Select popup', 'wppoppop'); ?>">
    <a role="tab" class="wppoppop-ptab<?php echo $sel_uid === '' ? ' active' : ''; ?>" aria-selected="<?php echo $sel_uid === '' ? 'true' : 'false'; ?>" href="<?php echo esc_url($wppoppop_tab_base); ?>"><?php esc_html_e('All popups', 'wppoppop'); ?> <small>(<?php echo (int) $wppoppop_tab_total; ?>)</small></a>
    <?php foreach ((array) $all_popups as $__p) : $__on = ($sel_uid === $__p->uid); ?>
        <a role="tab" class="wppoppop-ptab<?php echo $__on ? ' active' : ''; ?>" aria-selected="<?php echo $__on ? 'true' : 'false'; ?>" href="<?php echo esc_url(add_query_arg('popup_uid', rawurlencode($__p->uid), $wppoppop_tab_base)); ?>"><?php echo esc_html($__p->title ? $__p->title : $__p->uid); ?> <small>(<?php echo isset($wppoppop_tab_counts[$__p->uid]) ? (int) $wppoppop_tab_counts[$__p->uid] : 0; ?>)</small></a>
    <?php endforeach; ?>
</div>
<div class="wppoppop-subs-toolbar">
    <div class="wppoppop-subs-tabs" id="wppoppop-subs-tabs">
        <button type="button" class="active" data-filter="all">All <small>(<span data-count="all"><?php echo (int) $wppoppop_total; ?></span>)</small></button>
        <button type="button" data-filter="unread">Unread <small>(<span data-count="unread"><?php echo (int) $wppoppop_unread; ?></span>)</small></button>
        <button type="button" data-filter="pending">Pending <small>(<span data-count="pending"><?php echo (int) $wppoppop_pending; ?></span>)</small></button>
        <button type="button" data-filter="confirmed">Confirmed <small>(<span data-count="confirmed"><?php echo (int) $wppoppop_confirmed; ?></span>)</small></button>
    </div>
    <div class="wppoppop-subs-searchbox">
        <span class="dashicons dashicons-tag" style="color:#50575e;"></span>
        <input type="search" id="wppoppop-subs-search" placeholder="">
        <button type="button" class="button button-primary" id="wppoppop-subs-search-btn">Search</button>
    </div>
</div>
