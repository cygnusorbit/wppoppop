<?php
if (!defined('ABSPATH')) {
    exit;
}

$builder_url = admin_url('admin.php?page=wppoppop-builder');
?>
<div class="wrap wppoppop-dash wppoppop-overview-wrap">
    <h1 class="screen-reader-text"><?php echo esc_html__('Dashboard', 'wppoppop'); ?></h1>

    <!-- Top Bar (shared partial) -->
    <?php
    $bar_title = __('Dashboard', 'wppoppop');
    $bar_icon  = 'dashicons-screenoptions';
    include WPPOPPOP_PATH . 'templates/partials/dash-bar.php';
    ?>

    <!-- Boxes -->
    <div class="wppoppop-dash-grid" id="wppoppop-dash-grid">
        <?php if (!class_exists('WpPopPop_Ajax_Dash_Layout')) { require_once WPPOPPOP_PATH . 'includes/ajax/class-ajax-dash-layout.php'; } $wppoppop_boxes = []; ob_start(); ?>
        <div class="wppoppop-box" id="wppoppop-box-popups">
            <h2><span class="dashicons dashicons-megaphone"></span><?php echo esc_html__('Popups', 'wppoppop'); ?></h2>

            <?php if (empty($dash_recent_popups)) : ?>
                <div class="notice-info">
                    <?php echo esc_html__('No popups yet,', 'wppoppop'); ?>
                    <a href="<?php echo esc_url($builder_url); ?>"><?php echo esc_html__('click here to create one', 'wppoppop'); ?></a>.
                </div>
            <?php else : ?>
                <ul class="wppoppop-pop-list">
                    <?php foreach ($dash_recent_popups as $p) : ?>
                        <li>
                            <span class="t"><?php echo esc_html($p->title !== '' ? $p->title : $p->uid); ?></span>
                            <span class="m"><?php echo (int) $p->submissions; ?> <?php echo esc_html__('entries', 'wppoppop'); ?></span>
                            <span class="a">
                                <a href="<?php echo esc_url(admin_url('admin.php?page=wppoppop-builder&uid=' . rawurlencode($p->uid))); ?>"><?php echo esc_html__('Edit', 'wppoppop'); ?></a>
                                <a href="<?php echo esc_url(admin_url('admin.php?page=wppoppop-submissions&popup_uid=' . rawurlencode($p->uid))); ?>"><?php echo esc_html__('Entries', 'wppoppop'); ?></a>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <a class="wppoppop-btn-red" href="<?php echo esc_url($builder_url); ?>"><?php echo esc_html__('Add New', 'wppoppop'); ?></a>
        </div>

        <?php $wppoppop_boxes['popups'] = ob_get_clean(); ob_start(); ?>
        <!-- Recent Submissions -->
        <div class="wppoppop-box" id="wppoppop-box-recent">
            <h2><span class="dashicons dashicons-email-alt"></span><?php echo esc_html__('Recent Submissions', 'wppoppop'); ?></h2>

            <?php if (empty($dash_recent_subs)) : ?>
                <div class="notice-info"><?php echo esc_html__('No recent entries yet.', 'wppoppop'); ?></div>
            <?php else : ?>
                <ul class="wppoppop-sub-list">
                    <?php foreach ($dash_recent_subs as $s) :
                        $label = (string) $s->email;
                        if ($label === '') {
                            $f = json_decode($s->fields_data, true);
                            if (is_array($f)) {
                                foreach ($f as $fk => $fv) {
                                    if (is_string($fv) && $fv !== '' && $fk[0] !== '_') {
                                        $label = $fv;
                                        break;
                                    }
                                }
                            }
                        }
                        if ($label === '') {
                            $label = '#' . (int) $s->id;
                        }
                        $ts   = strtotime($s->created_at);
                        $when = $ts ? date_i18n('j M Y, g:i A', $ts) : $s->created_at;
                        $href = admin_url('admin.php?page=wppoppop-submissions' . ($s->popup_uid !== '' ? '&popup_uid=' . rawurlencode($s->popup_uid) : ''));
                        $unread = empty($s->is_read);
                        ?>
                        <li class="<?php echo $unread ? 'is-unread' : ''; ?>">
                            <span class="dot" aria-hidden="true"></span>
                            <a class="who" href="<?php echo esc_url($href); ?>"><?php echo esc_html($label); ?></a>
                            <span class="pop"><?php echo esc_html($s->popup_title ? $s->popup_title : __('Untitled Popup', 'wppoppop')); ?></span>
                            <span class="when"><?php echo esc_html($when); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <a class="wppoppop-btn-red" href="<?php echo esc_url(admin_url('admin.php?page=wppoppop-submissions')); ?>"><?php echo esc_html__('View all submissions', 'wppoppop'); ?></a>
        </div>

        <?php $wppoppop_boxes['recent'] = ob_get_clean(); ob_start(); ?>
        <!-- Tools -->
        <div class="wppoppop-box" id="wppoppop-box-tools">
            <h2><span class="dashicons dashicons-admin-tools"></span><?php echo esc_html__('Tools', 'wppoppop'); ?></h2>
            <div class="wppoppop-tool-tiles">
                <a class="tile" href="<?php echo esc_url($dash_export_url); ?>">
                    <span class="ico"><span class="dashicons dashicons-media-spreadsheet"></span></span>
                    <span class="lbl"><?php echo esc_html__('Export Submissions', 'wppoppop'); ?></span>
                </a>
                <a class="tile" href="<?php echo esc_url(admin_url('admin.php?page=wppoppop-tools')); ?>">
                    <span class="ico"><span class="dashicons dashicons-media-code"></span></span>
                    <span class="lbl"><?php echo esc_html__('Export Popup', 'wppoppop'); ?></span>
                </a>
                <a class="tile" href="<?php echo esc_url(admin_url('admin.php?page=wppoppop-tools')); ?>">
                    <span class="ico"><span class="dashicons dashicons-database-import"></span></span>
                    <span class="lbl"><?php echo esc_html__('Import Popup', 'wppoppop'); ?></span>
                </a>
            </div>
        </div>
        <?php $wppoppop_boxes['tools'] = ob_get_clean();
        foreach (WpPopPop_Ajax_Dash_Layout::get_order() as $wppoppop_bk) {
            if (isset($wppoppop_boxes[$wppoppop_bk])) { echo $wppoppop_boxes[$wppoppop_bk]; }
        } ?>
    </div>
</div>
