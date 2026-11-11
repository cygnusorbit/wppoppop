<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- template variables are local to the method that includes this file
if (!defined('ABSPATH')) {
    exit;
}

$__gs_rows    = WpPopPop_Admin_Guide::resolved_rows();
$__gs_targets = WpPopPop_Admin_Guide::targets();
$__gs_before  = [
    ''                    => __('No extra action', 'wppoppop'),
    'open-inspector'      => __('Open the Inspector', 'wppoppop'),
    'inspector-tab:basic' => __('Inspector: Content tab', 'wppoppop'),
    'inspector-tab:style' => __('Inspector: Style tab', 'wppoppop'),
    'inspector-tab:logic' => __('Inspector: Logic tab', 'wppoppop'),
    'inspector-tab:data'  => __('Inspector: Data tab', 'wppoppop'),
    'inspector-tab:advanced' => __('Inspector: Advanced tab', 'wppoppop'),
    'open-settings'       => __('Open Popup Settings', 'wppoppop'),
];
$__gs_places  = [
    'auto'   => __('Automatic', 'wppoppop'),
    'bottom' => __('Below the target', 'wppoppop'),
    'top'    => __('Above the target', 'wppoppop'),
    'left'   => __('Left of the target', 'wppoppop'),
    'right'  => __('Right of the target', 'wppoppop'),
];

$__gs_row = function ($r, $i) use ($__gs_targets, $__gs_before, $__gs_places) {
    $name   = 'settings[builder_guide_steps][' . $i . ']';
    $custom = !empty($r['custom']);
    $is_cs  = ('custom' === $r['target']);
    $num    = is_int($i) ? $i + 1 : '';
    ?>
    <div class="wppoppop-gs-row<?php echo empty($r['enabled']) ? ' is-off' : ''; ?>" data-custom="<?php echo $custom ? 1 : 0; ?>">
        <input type="hidden" name="<?php echo esc_attr($name); ?>[id]" value="<?php echo esc_attr($r['id']); ?>">
        <div class="wppoppop-gs-head">
            <span class="wppoppop-gs-num" aria-hidden="true"><?php echo esc_html($num); ?></span>
            <label class="wppoppop-switch-bld" title="<?php esc_attr_e('Show this step', 'wppoppop'); ?>">
                <input type="hidden" name="<?php echo esc_attr($name); ?>[enabled]" value="0">
                <input type="checkbox" class="wppoppop-gs-enabled" name="<?php echo esc_attr($name); ?>[enabled]" value="1" <?php checked(!empty($r['enabled'])); ?> aria-label="<?php esc_attr_e('Show this step', 'wppoppop'); ?>">
                <span class="trk"></span>
            </label>
            <input type="text" class="wppoppop-gs-title" name="<?php echo esc_attr($name); ?>[title]" value="<?php echo esc_attr($r['title']); ?>" maxlength="120" placeholder="<?php esc_attr_e('Step title', 'wppoppop'); ?>" aria-label="<?php esc_attr_e('Step title', 'wppoppop'); ?>">
            <span class="wppoppop-gs-tag"><?php echo $custom ? esc_html__('Custom', 'wppoppop') : esc_html__('Built-in', 'wppoppop'); ?></span>
            <span class="wppoppop-gs-actions">
                <button type="button" class="button" data-gs-act="up" title="<?php esc_attr_e('Move up', 'wppoppop'); ?>" aria-label="<?php esc_attr_e('Move up', 'wppoppop'); ?>"><span class="dashicons dashicons-arrow-up-alt2" aria-hidden="true"></span></button>
                <button type="button" class="button" data-gs-act="down" title="<?php esc_attr_e('Move down', 'wppoppop'); ?>" aria-label="<?php esc_attr_e('Move down', 'wppoppop'); ?>"><span class="dashicons dashicons-arrow-down-alt2" aria-hidden="true"></span></button>
                <button type="button" class="button" data-gs-act="edit" aria-expanded="false" title="<?php esc_attr_e('Edit step', 'wppoppop'); ?>" aria-label="<?php esc_attr_e('Edit step', 'wppoppop'); ?>"><span class="dashicons dashicons-edit" aria-hidden="true"></span></button>
                <?php if ($custom) : ?>
                <button type="button" class="button wppoppop-gs-del" data-gs-act="del" title="<?php esc_attr_e('Delete step', 'wppoppop'); ?>" aria-label="<?php esc_attr_e('Delete step', 'wppoppop'); ?>"><span class="dashicons dashicons-trash" aria-hidden="true"></span></button>
                <?php endif; ?>
            </span>
        </div>
        <div class="wppoppop-gs-body" hidden>
            <label class="wppoppop-gs-field wppoppop-gs-wide">
                <span><?php esc_html_e('Text (use **bold** for emphasis, 400 characters max)', 'wppoppop'); ?></span>
                <textarea name="<?php echo esc_attr($name); ?>[text]" rows="3" maxlength="400"><?php echo esc_textarea($r['text']); ?></textarea>
            </label>
            <label class="wppoppop-gs-field">
                <span><?php esc_html_e('Highlight', 'wppoppop'); ?></span>
                <select class="wppoppop-gs-target" name="<?php echo esc_attr($name); ?>[target]">
                    <?php foreach ($__gs_targets as $k => $t) : ?>
                        <option value="<?php echo esc_attr($k); ?>" <?php selected($r['target'], $k); ?>><?php echo esc_html($t['label']); ?></option>
                    <?php endforeach; ?>
                    <option value="custom" <?php selected($r['target'], 'custom'); ?>><?php esc_html_e('Custom CSS selector…', 'wppoppop'); ?></option>
                </select>
            </label>
            <label class="wppoppop-gs-field">
                <span><?php esc_html_e('Card position', 'wppoppop'); ?></span>
                <select name="<?php echo esc_attr($name); ?>[placement]">
                    <?php foreach ($__gs_places as $k => $label) : ?>
                        <option value="<?php echo esc_attr($k); ?>" <?php selected($r['placement'], $k); ?>><?php echo esc_html($label); ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label class="wppoppop-gs-field wppoppop-gs-if-custom"<?php echo $is_cs ? '' : ' hidden'; ?>>
                <span><?php esc_html_e('CSS selector', 'wppoppop'); ?></span>
                <input type="text" name="<?php echo esc_attr($name); ?>[selector]" value="<?php echo esc_attr($r['selector']); ?>" maxlength="200" placeholder="#wppoppop-btn-save" spellcheck="false" autocomplete="off">
            </label>
            <label class="wppoppop-gs-field wppoppop-gs-if-custom"<?php echo $is_cs ? '' : ' hidden'; ?>>
                <span><?php esc_html_e('Before showing the step', 'wppoppop'); ?></span>
                <select name="<?php echo esc_attr($name); ?>[before]">
                    <?php foreach ($__gs_before as $k => $label) : ?>
                        <option value="<?php echo esc_attr($k); ?>" <?php selected($r['before'], $k); ?>><?php echo esc_html($label); ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
        </div>
    </div>
    <?php
};

$__gs_blank = ['id' => '', 'enabled' => 1, 'title' => '', 'text' => '', 'target' => 'center', 'selector' => '', 'before' => '', 'placement' => 'auto', 'custom' => 1];
?>
<p class="description"><?php esc_html_e('Edit the builder tour shown to every admin. Turn steps on or off, reorder them, rewrite their text, point them at another part of the builder, or add your own. Save settings, then reload the builder. Pro and extensions can still add steps on top of these.', 'wppoppop'); ?></p>
<div id="wppoppop-gs-root" class="wppoppop-gs" data-max="<?php echo (int) WpPopPop_Admin_Guide::MAX_STEPS; ?>" data-defaults="<?php echo esc_attr(wp_json_encode(WpPopPop_Admin_Guide::editor_defaults())); ?>">
    <div class="wppoppop-gs-toolbar">
        <span class="wppoppop-gs-count" id="wppoppop-gs-count" aria-live="polite"></span>
        <span class="wppoppop-gs-toolbar-btns">
            <button type="button" class="button" id="wppoppop-gs-reset"><?php esc_html_e('Reset to defaults', 'wppoppop'); ?></button>
            <button type="button" class="button button-primary" id="wppoppop-gs-add"><?php esc_html_e('Add step', 'wppoppop'); ?></button>
        </span>
    </div>
    <div class="wppoppop-gs-list">
        <?php foreach ($__gs_rows as $__gs_i => $__gs_r) { $__gs_row($__gs_r, $__gs_i); } ?>
    </div>
    <template id="wppoppop-gs-template"><?php $__gs_row($__gs_blank, '__i__'); ?></template>
</div>
