<?php
if (!defined('ABSPATH')) {
    exit;
}
global $wpdb;
$bar_title   = isset($bar_title) ? $bar_title : __('Dashboard', 'wppoppop');
$bar_icon    = isset($bar_icon) ? $bar_icon : 'dashicons-screenoptions';
$bar_actions = isset($bar_actions) ? $bar_actions : '';
$bar_after_title = isset($bar_after_title) ? $bar_after_title : '';
$bar_popups  = $wpdb->get_results("SELECT uid, title FROM {$wpdb->prefix}wppoppop_items ORDER BY id DESC LIMIT 30");

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
$bar_logo    = WPPOPPOP_URL . 'admin/img/logo-temp.svg';
?>
<div class="wppoppop-dash-bar" role="navigation" aria-label="WpPopPop">
    <div class="cell logo"><img src="<?php echo esc_url($bar_logo); ?>" alt="WpPopPop"></div>

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
        <button type="button" class="bar-btn" data-dd="wppoppop-dd-popups" aria-haspopup="true" aria-expanded="false" title="<?php echo esc_attr__('Popups', 'wppoppop'); ?>">
            <span class="dashicons dashicons-portfolio"></span>
        </button>
        <div class="wppoppop-dd" id="wppoppop-dd-popups">
            <?php if (empty($bar_popups)) : ?>
                <div class="dd-empty"><?php echo esc_html__('No popups yet.', 'wppoppop'); ?></div>
            <?php else : foreach ($bar_popups as $bp) : ?>
                <a href="<?php echo esc_url(admin_url('admin.php?page=' . (isset($bar_popup_page) ? $bar_popup_page : 'wppoppop-builder') . '&' . ((isset($bar_popup_page) && $bar_popup_page === 'wppoppop-submissions') ? 'popup_uid' : 'uid') . '=' . rawurlencode($bp->uid))); ?>"><span class="dashicons dashicons-edit"></span><?php echo esc_html($bp->title !== '' ? $bp->title : $bp->uid); ?></a>
            <?php endforeach; endif; ?>
            <div class="dd-sep"></div>
            <a href="<?php echo esc_url(admin_url('admin.php?page=wppoppop')); ?>"><span class="dashicons dashicons-list-view"></span><?php echo esc_html__('View all popups', 'wppoppop'); ?></a>
            <a href="<?php echo esc_url(admin_url('admin.php?page=wppoppop-submissions')); ?>"><span class="dashicons dashicons-email-alt"></span><?php echo esc_html__('Submissions', 'wppoppop'); ?></a>
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
