<?php
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('WpPopPop_Targeting_Triggers')) {
    require_once WPPOPPOP_PATH . 'includes/class-wppoppop-targeting-triggers.php';
}

$wppoppop_tt_roles = array_merge(
    array('all' => __('All roles', 'wppoppop'), 'visitor' => __('Visitors (logged out)', 'wppoppop')),
    WpPopPop_Targeting_Triggers::roles_list()
);
$wppoppop_tt_cats  = WpPopPop_Targeting_Triggers::terms_list('category');
$wppoppop_tt_tags  = WpPopPop_Targeting_Triggers::terms_list('post_tag');
$wppoppop_tt_posts = WpPopPop_Targeting_Triggers::posts_list('post');
$wppoppop_tt_pages = WpPopPop_Targeting_Triggers::posts_list('page');

$wppoppop_tt_seg = function ($group, $label, $opts, $extra = '') {
    ?>
    <div class="tt-segs" role="radiogroup" aria-label="<?php echo esc_attr($label); ?>"<?php echo $extra; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
        <?php foreach ($opts as $val => $txt) : ?>
            <button type="button" class="tt-seg" role="radio" aria-checked="false" data-tt-group="<?php echo esc_attr($group); ?>" data-tt-val="<?php echo esc_attr($val); ?>"><?php echo esc_html($txt); ?></button>
        <?php endforeach; ?>
    </div>
    <?php
};

$wppoppop_tt_list = function ($key, $items, $with_filter = true) {
    ?>
    <?php if ($with_filter) : ?>
        <input type="search" class="widefat tt-filter" data-tt-filter="<?php echo esc_attr($key); ?>" placeholder="<?php esc_attr_e('Search...', 'wppoppop'); ?>" autocomplete="off">
    <?php endif; ?>
    <div class="tt-list" data-tt-list="<?php echo esc_attr($key); ?>">
        <?php if (empty($items)) : ?>
            <p class="tt-none"><?php esc_html_e('Nothing found.', 'wppoppop'); ?></p>
        <?php endif; ?>
        <?php foreach ($items as $val => $txt) : ?>
            <label class="tt-chk"><input type="checkbox" data-tt-item="<?php echo esc_attr($key); ?>" value="<?php echo esc_attr($val); ?>"><span><?php echo esc_html($txt); ?></span></label>
        <?php endforeach; ?>
    </div>
    <?php
};

$wppoppop_tt_allspec = function ($key, $label_all, $label_spec) use ($wppoppop_tt_seg) {
    $wppoppop_tt_seg($key . '_all', '', array('true' => $label_all, 'false' => $label_spec));
};
?>
<!-- WPPOPPOP_TARGETING_TRIGGERS_UI: 22. Targeting Triggers -->
            <div class="wppoppop-acc-item" id="wppoppop-tt-item">
                <button type="button" class="wppoppop-acc-header"><?php esc_html_e('Targeting Triggers', 'wppoppop'); ?></button>
                <div class="wppoppop-acc-body" style="display:none;">
                    <style>
                        #wppoppop-tt-item{position:relative}
                        #wppoppop-tt-item > .wppoppop-acc-header{min-height:38px;box-sizing:border-box;padding-right:76px}
                        #wppoppop-tt-item .tt-master{position:absolute;top:0;right:34px;height:38px;display:flex;align-items:center;margin:0;padding:0}
                        #wppoppop-tt-item .tt-master input{position:absolute;opacity:0;width:1px;height:1px;margin:0}
                        #wppoppop-tt-item .tt-track{position:relative;display:block;width:34px;height:18px;border-radius:9px;background:#cbd5e1;transition:background .15s;cursor:pointer}
                        #wppoppop-tt-item .tt-track:after{content:"";position:absolute;top:2px;left:2px;width:14px;height:14px;border-radius:50%;background:#fff;box-shadow:0 1px 2px rgba(0,0,0,.3);transition:left .15s}
                        #wppoppop-tt-item .tt-master input:checked + .tt-track{background:#b4234f}
                        #wppoppop-tt-item .tt-master input:checked + .tt-track:after{left:18px}
                        #wppoppop-tt-item .tt-master input:focus + .tt-track{box-shadow:0 0 0 2px #2271b1}
                        #wppoppop-tt-fs{border:0;margin:0;padding:0;min-width:0;display:block}
                        #wppoppop-tt-item.is-off #wppoppop-tt-fs{opacity:.45}
                        #wppoppop-tt-item .tt-h{display:block;font-size:11px;font-weight:700;color:#475569;text-transform:uppercase;margin:0 0 6px}
                        #wppoppop-tt-item .tt-sec{margin:0 0 14px}
                        #wppoppop-tt-item .tt-props{border-top:1px solid #e2e8f0;padding-top:12px;margin-top:2px}
                        #wppoppop-tt-item .tt-segs{display:flex;flex-wrap:wrap;gap:6px}
                        #wppoppop-tt-item .tt-seg{flex:1 1 auto;margin:0;padding:6px 8px;font-size:11px;font-weight:600;line-height:1.3;color:#334155;background:#f8fafc;border:1px solid #cbd5e1;border-radius:6px;-webkit-appearance:none;appearance:none;text-align:center}
                        #wppoppop-tt-item .tt-seg:hover{border-color:#be4a6f;background:#fff7fa}
                        #wppoppop-tt-item .tt-seg.is-on{color:#fff;background:#b4234f;border-color:#b4234f}
                        #wppoppop-tt-item .tt-seg:focus{outline:none}
                        #wppoppop-tt-item .tt-seg:focus-visible{outline:2px solid #2271b1;outline-offset:1px}
                        #wppoppop-tt-item .tt-row{display:flex;align-items:center;gap:8px;margin:8px 0 0}
                        #wppoppop-tt-item .tt-row label{margin:0;flex:1 1 50%;font-size:11px;font-weight:600;color:#475569}
                        #wppoppop-tt-item .tt-row input{flex:1 1 40%;min-width:0;width:100%}
                        #wppoppop-tt-item .tt-unit{font-size:11px;color:#64748b;flex:none}
                        #wppoppop-tt-item .tt-hint{font-size:11px;color:#64748b;margin:6px 0 0}
                        #wppoppop-tt-item .tt-sub{margin:10px 0 0;padding:10px;border:1px solid #e2e8f0;border-radius:6px;background:#f8fafc}
                        #wppoppop-tt-item .tt-list{max-height:150px;overflow:auto;border:1px solid #e2e8f0;border-radius:6px;background:#fff;margin:6px 0 0;padding:4px 6px}
                        #wppoppop-tt-item .tt-chk{display:flex;align-items:flex-start;gap:6px;margin:0;padding:3px 0;font-size:11px;font-weight:500;color:#334155;line-height:1.35}
                        #wppoppop-tt-item .tt-chk input{flex:none;margin:2px 0 0}
                        #wppoppop-tt-item .tt-chk span{min-width:0;word-break:break-word}
                        #wppoppop-tt-item .tt-none{margin:4px 0;font-size:11px;color:#94a3b8}
                        #wppoppop-tt-item .tt-filter{margin:8px 0 0}
                        #wppoppop-tt-item textarea{width:100%;min-height:64px;resize:vertical;box-sizing:border-box}
                    </style>

                    <fieldset id="wppoppop-tt-fs">
                        <div class="tt-sec">
                            <span class="tt-h"><?php esc_html_e('Popup Targeting', 'wppoppop'); ?></span>
                            <?php $wppoppop_tt_seg('event', __('Popup Targeting', 'wppoppop'), array(
                                'onload'       => __('OnLoad', 'wppoppop'),
                                'onscroll'     => __('OnScroll', 'wppoppop'),
                                'onexit'       => __('OnExit', 'wppoppop'),
                                'oninactivity' => __('OnInactivity', 'wppoppop'),
                            )); ?>
                            <p class="tt-hint" data-tt-when="event:"><?php esc_html_e('Choose when the popup appears. Until one is selected the popup keeps its default behavior.', 'wppoppop'); ?></p>
                        </div>

                        <div class="tt-props" data-tt-has="event" style="display:none;">
                            <span class="tt-h"><?php esc_html_e('Target Properties', 'wppoppop'); ?></span>

                            <div class="tt-sec">
                                <span class="tt-h"><?php esc_html_e('Periodicity', 'wppoppop'); ?></span>
                                <?php $wppoppop_tt_seg('mode', __('Periodicity', 'wppoppop'), array(
                                    'every-time'      => __('Every time', 'wppoppop'),
                                    'once-only'       => __('Once only', 'wppoppop'),
                                    'once-per-period' => __('Once per period', 'wppoppop'),
                                )); ?>
                                <div class="tt-row" data-tt-when="mode:once-per-period" style="display:none;">
                                    <label for="set-tt-period"><?php esc_html_e('Show again after', 'wppoppop'); ?></label>
                                    <input type="number" id="set-tt-period" class="widefat" data-tt-field="period_hours" min="1" max="8760" step="1" value="24">
                                    <span class="tt-unit"><?php esc_html_e('hrs', 'wppoppop'); ?></span>
                                </div>
                            </div>

                            <div class="tt-sec" data-tt-when="event:onload" style="display:none;">
                                <div class="tt-row">
                                    <label for="set-tt-start"><?php esc_html_e('Start delay', 'wppoppop'); ?></label>
                                    <input type="number" id="set-tt-start" class="widefat" data-tt-field="start_delay" min="0" max="3600" step="1" value="0">
                                    <span class="tt-unit"><?php esc_html_e('sec', 'wppoppop'); ?></span>
                                </div>
                                <div class="tt-row">
                                    <label for="set-tt-close"><?php esc_html_e('Close delay', 'wppoppop'); ?></label>
                                    <input type="number" id="set-tt-close" class="widefat" data-tt-field="close_delay" min="0" max="3600" step="1" value="0">
                                    <span class="tt-unit"><?php esc_html_e('sec', 'wppoppop'); ?></span>
                                </div>
                                <p class="tt-hint"><?php esc_html_e('Close delay 0 keeps the popup open until it is closed.', 'wppoppop'); ?></p>
                            </div>

                            <div class="tt-sec" data-tt-when="event:onscroll" style="display:none;">
                                <div class="tt-row">
                                    <label for="set-tt-offset"><?php esc_html_e('Scrolling offset', 'wppoppop'); ?></label>
                                    <input type="number" id="set-tt-offset" class="widefat" data-tt-field="scroll_offset" min="0" max="100000" step="1" value="600">
                                </div>
                                <div style="margin-top:8px;"><?php $wppoppop_tt_seg('scroll_unit', __('Scrolling offset unit', 'wppoppop'), array('px' => 'px', '%' => '%')); ?></div>
                            </div>

                            <div class="tt-sec" data-tt-when="event:oninactivity" style="display:none;">
                                <div class="tt-row">
                                    <label for="set-tt-idle"><?php esc_html_e('Inactivity delay', 'wppoppop'); ?></label>
                                    <input type="number" id="set-tt-idle" class="widefat" data-tt-field="idle_delay" min="1" max="3600" step="1" value="15">
                                    <span class="tt-unit"><?php esc_html_e('sec', 'wppoppop'); ?></span>
                                </div>
                            </div>

                            <div class="tt-sec">
                                <span class="tt-h"><?php esc_html_e('Activity', 'wppoppop'); ?></span>
                                <?php $wppoppop_tt_seg('activity', __('Activity', 'wppoppop'), array(
                                    'always' => __('Always', 'wppoppop'),
                                    'period' => __('Certain period', 'wppoppop'),
                                )); ?>
                                <div data-tt-when="activity:period" style="display:none;">
                                    <div class="tt-row">
                                        <label for="set-tt-from"><?php esc_html_e('From', 'wppoppop'); ?></label>
                                        <input type="date" id="set-tt-from" class="widefat" data-tt-field="from" placeholder="YYYY-MM-DD">
                                    </div>
                                    <div class="tt-row">
                                        <label for="set-tt-to"><?php esc_html_e('End', 'wppoppop'); ?></label>
                                        <input type="date" id="set-tt-to" class="widefat" data-tt-field="to" placeholder="YYYY-MM-DD">
                                    </div>
                                </div>
                            </div>

                            <div class="tt-sec">
                                <span class="tt-h"><?php esc_html_e('User Roles', 'wppoppop'); ?></span>
                                <div class="tt-list" data-tt-list="roles" style="margin-top:0;">
                                    <?php foreach ($wppoppop_tt_roles as $wppoppop_tt_slug => $wppoppop_tt_name) : ?>
                                        <label class="tt-chk"><input type="checkbox" data-tt-item="roles" value="<?php echo esc_attr($wppoppop_tt_slug); ?>"><span><?php echo esc_html($wppoppop_tt_name); ?></span></label>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="tt-sec">
                                <span class="tt-h"><?php esc_html_e('Site Area', 'wppoppop'); ?></span>
                                <?php $wppoppop_tt_seg('area', __('Site Area', 'wppoppop'), array(
                                    'sitewide' => __('Sitewide', 'wppoppop'),
                                    'homepage' => __('Homepage', 'wppoppop'),
                                    'posts'    => __('Posts', 'wppoppop'),
                                    'pages'    => __('Pages', 'wppoppop'),
                                    'url'      => __('URL', 'wppoppop'),
                                )); ?>

                                <div data-tt-when="area:posts" style="display:none;">
                                    <div class="tt-sub">
                                        <span class="tt-h"><?php esc_html_e('Categories', 'wppoppop'); ?></span>
                                        <?php $wppoppop_tt_allspec('cats', __('All', 'wppoppop'), __('Specific', 'wppoppop')); ?>
                                        <div data-tt-when="cats_all:false" style="display:none;"><?php $wppoppop_tt_list('cats', $wppoppop_tt_cats); ?></div>
                                        <label class="tt-chk" style="margin-top:6px;"><input type="checkbox" data-tt-flag="cats_archive"><span><?php esc_html_e('Also show on category archive pages', 'wppoppop'); ?></span></label>
                                    </div>
                                    <div class="tt-sub">
                                        <span class="tt-h"><?php esc_html_e('Tags', 'wppoppop'); ?></span>
                                        <?php $wppoppop_tt_allspec('tags', __('All', 'wppoppop'), __('Specific', 'wppoppop')); ?>
                                        <div data-tt-when="tags_all:false" style="display:none;"><?php $wppoppop_tt_list('tags', $wppoppop_tt_tags); ?></div>
                                        <label class="tt-chk" style="margin-top:6px;"><input type="checkbox" data-tt-flag="tags_archive"><span><?php esc_html_e('Also show on tag archive pages', 'wppoppop'); ?></span></label>
                                    </div>
                                    <div class="tt-sub">
                                        <span class="tt-h"><?php esc_html_e('Posts', 'wppoppop'); ?></span>
                                        <?php $wppoppop_tt_allspec('posts', __('All', 'wppoppop'), __('Specific', 'wppoppop')); ?>
                                        <div data-tt-when="posts_all:false" style="display:none;"><?php $wppoppop_tt_list('posts', $wppoppop_tt_posts); ?></div>
                                    </div>
                                    <p class="tt-hint"><?php esc_html_e('A post matches when it is in any selected category, tag or post list.', 'wppoppop'); ?></p>
                                </div>

                                <div data-tt-when="area:pages" style="display:none;">
                                    <div class="tt-sub">
                                        <span class="tt-h"><?php esc_html_e('Pages', 'wppoppop'); ?></span>
                                        <?php $wppoppop_tt_allspec('pages', __('All pages', 'wppoppop'), __('Specific pages', 'wppoppop')); ?>
                                        <div data-tt-when="pages_all:false" style="display:none;"><?php $wppoppop_tt_list('pages', $wppoppop_tt_pages); ?></div>
                                    </div>
                                </div>

                                <div data-tt-when="area:url" style="display:none;">
                                    <div class="tt-sub">
                                        <label for="set-tt-url" class="tt-h"><?php esc_html_e('URL contains', 'wppoppop'); ?></label>
                                        <textarea id="set-tt-url" class="widefat" data-tt-field="url_keywords" rows="3" placeholder="/pricing&#10;/blog/summer-sale"></textarea>
                                        <p class="tt-hint"><?php esc_html_e('One per line. The popup shows when the page address contains any of them. Empty = every URL.', 'wppoppop'); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </div>
                <label class="tt-master" title="<?php esc_attr_e('Turn the entire Targeting Triggers setting on or off', 'wppoppop'); ?>">
                    <input type="checkbox" id="set-tt-enabled" checked aria-label="<?php esc_attr_e('Enable Targeting Triggers', 'wppoppop'); ?>">
                    <span class="tt-track" aria-hidden="true"></span>
                </label>
            </div>
