<?php
if (!defined('ABSPATH')) {
    exit;
}
global $wpdb;
$bar_title   = isset($bar_title) ? $bar_title : __('Dashboard', 'wppoppop');
$bar_icon    = isset($bar_icon) ? $bar_icon : 'dashicons-screenoptions';
$bar_actions = isset($bar_actions) ? $bar_actions : '';
$bar_after_title = isset($bar_after_title) ? $bar_after_title : '';
$bar_items  = $wpdb->prefix . 'wppoppop_items';
$bar_subs   = $wpdb->prefix . 'wppoppop_submissions';
$bar_popups = $wpdb->get_results("SELECT i.uid, i.title,
        SUM(CASE WHEN s.id IS NOT NULL AND COALESCE(s.is_read, 0) = 0 THEN 1 ELSE 0 END) AS unread_count,
        SUM(CASE WHEN s.id IS NOT NULL AND COALESCE(s.is_read, 0) <> 0 THEN 1 ELSE 0 END) AS read_count
    FROM {$bar_items} i LEFT JOIN {$bar_subs} s ON s.popup_uid = i.uid
    WHERE i.status <> 'trash'
    GROUP BY i.id, i.uid, i.title ORDER BY i.id DESC LIMIT 30");

$bar_links = [
    ['wppoppop-dashboard',       'Dashboard',        'dashicons-screenoptions'],
    ['wppoppop',                 'Popups',           'dashicons-megaphone'],
    ['wppoppop-builder',         'Create Popup',     'dashicons-plus-alt'],
    ['wppoppop-ab',              'A/B Campaigns',    'dashicons-randomize'],
    ['wppoppop-submissions',     'Submissions',      'dashicons-email-alt'],
    ['wppoppop-log',             'Activity Log',     'dashicons-list-view'],
    ['wppoppop-stats',           'Statistics',       'dashicons-chart-bar'],
    ['wppoppop-field-analytics', 'Field Analytics',  'dashicons-chart-pie'],
    ['wppoppop-payments',        'Transactions',     'dashicons-money-alt'],
    ['wppoppop-library',         'Popups Library',   'dashicons-category'],
    ['wppoppop-settings',        'Settings',         'dashicons-admin-generic'],
    ['wppoppop-theme',           'Theme',            'dashicons-art'],
    ['wppoppop-tools',           'Tools & Export',   'dashicons-admin-tools'],
];
$bar_builder = admin_url('admin.php?page=wppoppop-builder');
$bar_logo_custom = class_exists('WpPopPop_Admin_Theme') ? WpPopPop_Admin_Theme::logo_url() : '';
$bar_logo        = ($bar_logo_custom !== '') ? $bar_logo_custom : WPPOPPOP_URL . 'admin/img/logo-temp.svg';
?>
<div class="wppoppop-dash-bar" role="navigation" aria-label="WpPopPop">
    <div class="cell logo"><img src="<?php echo esc_url($bar_logo); ?>" alt="WpPopPop"<?php echo ($bar_logo_custom !== '') ? ' style="width:auto;max-width:140px;height:28px;object-fit:contain;"' : ''; ?>></div>

    <div class="cell">
        <button type="button" class="bar-btn" data-dd="wppoppop-dd-menu" aria-haspopup="true" aria-expanded="false" title="<?php echo esc_attr__('Menu', 'wppoppop'); ?>">
            <span class="dashicons dashicons-menu"></span>
        </button>
        <div class="wppoppop-dd" id="wppoppop-dd-menu">
            <?php foreach ($bar_links as $ml) : ?>
                <a href="<?php echo esc_url(admin_url('admin.php?page=' . $ml[0])); ?>"><span class="dashicons <?php echo esc_attr($ml[2]); ?>"></span><?php echo esc_html($ml[1]); ?></a>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="cell">
        <?php /* WPPOPPOP_BAR_UNREAD: per-popup submissions dropdown */ ?>
        <button type="button" class="bar-btn" data-dd="wppoppop-dd-popups" aria-haspopup="true" aria-expanded="false" title="<?php echo esc_attr__('Submissions', 'wppoppop'); ?>">
            <span class="dashicons dashicons-email-alt"></span>
        </button>
        <div class="wppoppop-dd" id="wppoppop-dd-popups">
            <?php if (empty($bar_popups)) : ?>
                <div class="dd-empty"><?php echo esc_html__('No popups yet.', 'wppoppop'); ?></div>
            <?php else : foreach ($bar_popups as $bp) :
                $bp_unread = (int) $bp->unread_count;
                $bp_read   = (int) $bp->read_count;
                $bp_name   = ((string) $bp->title !== '') ? $bp->title : $bp->uid;
                $bp_url    = admin_url('admin.php?page=wppoppop-submissions&popup_uid=' . rawurlencode($bp->uid));
                ?>
                <a href="<?php echo esc_url($bp_url); ?>">
                    <span class="dashicons dashicons-email-alt"></span>
                    <span style="flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;"><?php echo esc_html($bp_name); ?></span>
                    <?php if ($bp_unread > 0) : ?>
                        <span title="<?php echo esc_attr__('Unread', 'wppoppop'); ?>" style="background:var(--wpp-c-primary,#c1272d);color:#fff;border-radius:10px;padding:0 7px;font-size:11px;font-weight:700;line-height:18px;"><?php echo (int) $bp_unread; ?></span>
                    <?php endif; ?>
                    <span title="<?php echo esc_attr__('Read', 'wppoppop'); ?>" style="color:var(--wpp-c-muted,#787c82);font-size:11px;white-space:nowrap;">( <?php echo (int) $bp_read; ?> )</span>
                </a>
            <?php endforeach; endif; ?>
            <div class="dd-sep"></div>
            <a href="<?php echo esc_url(admin_url('admin.php?page=wppoppop')); ?>"><span class="dashicons dashicons-list-view"></span><?php echo esc_html__('View all popups', 'wppoppop'); ?></a>
            <a href="<?php echo esc_url(admin_url('admin.php?page=wppoppop-submissions')); ?>"><span class="dashicons dashicons-email-alt"></span><?php echo esc_html__('All submissions', 'wppoppop'); ?></a>
        </div>
    </div>

    <div class="cell">
        <a class="bar-btn" href="<?php echo esc_url($bar_builder); ?>" title="<?php echo esc_attr__('Add New', 'wppoppop'); ?>"><span class="dashicons dashicons-plus-alt2"></span></a>
    </div>

    <div class="cell title">
        <span class="dashicons <?php echo esc_attr($bar_icon); ?>"></span>
        <span><?php echo esc_html($bar_title); ?></span>
    </div>

    <?php if ($bar_after_title !== '' && file_exists($bar_after_title)) { include $bar_after_title; } ?>

    <?php if ($bar_actions !== '' && file_exists($bar_actions)) { include $bar_actions; } ?>
</div>
