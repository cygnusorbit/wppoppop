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
.wppoppop-subs-searchbox input[type=search]{width:140px;height:28px}
</style>
<?php
$bar_title   = __('Submissions', 'wppoppop');
$bar_icon    = 'dashicons-email-alt';
$bar_actions = WPPOPPOP_PATH . 'templates/submissions/bar-actions.php';
$bar_after_title = WPPOPPOP_PATH . 'templates/submissions/bar-filter.php';
$bar_popup_page = 'wppoppop-submissions';
?>
<h1 class="screen-reader-text"><?php esc_html_e('Submissions', 'wppoppop'); ?></h1>
<div class="wppoppop-dash" style="margin:0 0 20px;max-width:none;">
    <?php include WPPOPPOP_PATH . 'templates/partials/dash-bar.php'; ?>
</div>
<div id="wppoppop-subs-notice" style="display:none;" class="notice is-dismissible"></div>
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
