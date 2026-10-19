<?php
if (!defined('ABSPATH')) {
    exit;
}

$menu_links = [
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
    ['wppoppop-tools',           'Tools & Export',   'dashicons-admin-tools'],
];

$builder_url = admin_url('admin.php?page=wppoppop-builder');
$logo_url    = WPPOPPOP_URL . 'admin/img/logo-temp.svg';
?>
<div class="wrap wppoppop-dash">
    <h1 class="screen-reader-text"><?php echo esc_html__('Dashboard', 'wppoppop'); ?></h1>

    <!-- Top Bar -->
    <div class="wppoppop-dash-bar" role="navigation" aria-label="WpPopPop">
        <div class="cell logo">
            <img src="<?php echo esc_url($logo_url); ?>" alt="WpPopPop">
        </div>

        <div class="cell">
            <button type="button" class="bar-btn" data-dd="wppoppop-dd-menu" aria-haspopup="true" aria-expanded="false" title="<?php echo esc_attr__('Menu', 'wppoppop'); ?>">
                <span class="dashicons dashicons-menu"></span>
            </button>
            <div class="wppoppop-dd" id="wppoppop-dd-menu">
                <?php foreach ($menu_links as $ml) : ?>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=' . $ml[0])); ?>">
                        <span class="dashicons <?php echo esc_attr($ml[2]); ?>"></span><?php echo esc_html($ml[1]); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="cell">
            <button type="button" class="bar-btn" data-dd="wppoppop-dd-popups" aria-haspopup="true" aria-expanded="false" title="<?php echo esc_attr__('Popups', 'wppoppop'); ?>">
                <span class="dashicons dashicons-portfolio"></span>
            </button>
            <div class="wppoppop-dd" id="wppoppop-dd-popups">
                <?php if (empty($dash_menu_popups)) : ?>
                    <div class="dd-empty"><?php echo esc_html__('No popups yet.', 'wppoppop'); ?></div>
                <?php else : foreach ($dash_menu_popups as $p) : ?>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=wppoppop-builder&uid=' . rawurlencode($p->uid))); ?>">
                        <span class="dashicons dashicons-edit"></span><?php echo esc_html($p->title !== '' ? $p->title : $p->uid); ?>
                    </a>
                <?php endforeach; endif; ?>
                <div class="dd-sep"></div>
                <a href="<?php echo esc_url(admin_url('admin.php?page=wppoppop')); ?>"><span class="dashicons dashicons-list-view"></span><?php echo esc_html__('View all popups', 'wppoppop'); ?></a>
                <a href="<?php echo esc_url(admin_url('admin.php?page=wppoppop-submissions')); ?>"><span class="dashicons dashicons-email-alt"></span><?php echo esc_html__('Submissions', 'wppoppop'); ?></a>
            </div>
        </div>

        <div class="cell">
            <a class="bar-btn" href="<?php echo esc_url($builder_url); ?>" title="<?php echo esc_attr__('Add New', 'wppoppop'); ?>">
                <span class="dashicons dashicons-plus-alt2"></span>
            </a>
        </div>

        <div class="cell title">
            <span class="dashicons dashicons-screenoptions"></span>
            <span><?php echo esc_html__('Dashboard', 'wppoppop'); ?></span>
        </div>
    </div>

    <!-- Boxes -->
    <div class="wppoppop-dash-grid">
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
    </div>
</div>
