<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- template variables are local to the method that includes this file
if (!defined('ABSPATH')) {
    exit;
}
$__ls_overrides = WpPopPop_Layer_Settings::get_overrides();
$__ls_elements  = WpPopPop_Layer_Settings::elements();
$__ls_tabs      = WpPopPop_Layer_Settings::tabs();
$__ls_cat       = WpPopPop_Layer_Settings::catalogue();
$__ls_form      = WpPopPop_Layer_Settings::form_types();
$__ls_first     = key($__ls_elements);
$__ls_json      = empty($__ls_overrides) ? '{}' : wp_json_encode($__ls_overrides);
?>
<div class="wppoppop-ls" id="wppoppop-ls-root">
    <p class="description"><?php esc_html_e('Choose which Layer Settings each element shows in the builder. Use the slide button to enable or disable a setting. Save settings, then reload the builder.', 'wppoppop'); ?></p>
    <input type="hidden" id="wppoppop-ls-json" name="settings[builder_layer_settings]" value="<?php echo esc_attr($__ls_json); ?>">

    <div class="wppoppop-ls-layout">
        <ul class="wppoppop-ls-elements" role="tablist" aria-label="<?php esc_attr_e('Elements', 'wppoppop'); ?>">
            <?php $__ls_n = 0; foreach ($__ls_elements as $__t => $__tl) : $__ls_n++; ?>
                <li>
                    <button type="button" class="wppoppop-ls-el-btn<?php echo $__t === $__ls_first ? ' is-active' : ''; ?><?php echo !empty($__ls_overrides[$__t]) ? ' has-override' : ''; ?>" data-ls-el="<?php echo esc_attr($__t); ?>" role="tab">
                        <span class="wppoppop-ls-num"><?php echo (int) $__ls_n; ?></span>
                        <span class="wppoppop-ls-name"><?php echo esc_html($__tl); ?></span>
                        <span class="wppoppop-ls-dot" title="<?php esc_attr_e('Customised', 'wppoppop'); ?>"></span>
                    </button>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="wppoppop-ls-main">
            <?php foreach ($__ls_elements as $__t => $__tl) : $__is_form = in_array($__t, $__ls_form, true); ?>
                <section class="wppoppop-ls-el" data-ls-el="<?php echo esc_attr($__t); ?>"<?php echo $__t === $__ls_first ? '' : ' hidden'; ?>>
                    <header class="wppoppop-ls-head">
                        <h4><?php echo esc_html($__tl); ?></h4>
                        <button type="button" class="button wppoppop-ls-reset" data-ls-el="<?php echo esc_attr($__t); ?>"><?php esc_html_e('Reset to default', 'wppoppop'); ?></button>
                    </header>

                    <nav class="wppoppop-ls-tabs wpp-theme-tabs" aria-label="<?php esc_attr_e('Settings groups', 'wppoppop'); ?>">
                        <?php $__ls_t1 = true; foreach ($__ls_tabs as $__tab => $__tablabel) : ?>
                            <button type="button" class="wpp-theme-tab wppoppop-ls-tab-btn<?php echo $__ls_t1 ? ' is-active' : ''; ?>" data-ls-tab="<?php echo esc_attr($__tab); ?>"><?php echo esc_html($__tablabel); ?></button>
                        <?php $__ls_t1 = false; endforeach; ?>
                    </nav>

                    <?php $__ls_p1 = true; foreach ($__ls_tabs as $__tab => $__tablabel) : ?>
                        <div class="wppoppop-ls-pane" data-ls-tab="<?php echo esc_attr($__tab); ?>"<?php echo $__ls_p1 ? '' : ' hidden'; ?>>
                            <?php if (($__tab === 'data' || $__tab === 'advanced') && !$__is_form) : ?>
                                <p class="wppoppop-ls-note"><?php esc_html_e('This tab is built for form elements. Every setting below is off by default for this element; enable one to add the tab to it.', 'wppoppop'); ?></p>
                            <?php endif; ?>
                            <ul class="wppoppop-bld-list">
                                <?php
                                foreach ($__ls_cat[$__tab] as $__e) {
                                    $__render_row = function ($e, $is_child) use ($__t, $__tab, $__ls_overrides) {
                                        $on  = WpPopPop_Layer_Settings::is_on($__t, $__tab, $e, $__ls_overrides);
                                        $def = WpPopPop_Layer_Settings::default_on($__t, $e);
                                        $lbl = WpPopPop_Layer_Settings::entry_label($e, $__t);
                                        ?>
                                        <li class="wppoppop-ls-row<?php echo $is_child ? ' is-child' : ''; ?><?php echo $on ? '' : ' is-off'; ?>">
                                            <span class="bld-name"><?php echo esc_html($lbl); ?>
                                                <span class="bld-desc"><?php echo $def ? esc_html__('Default: On', 'wppoppop') : esc_html__('Default: Off (hidden)', 'wppoppop'); ?></span>
                                            </span>
                                            <label class="wppoppop-switch-bld" title="<?php echo esc_attr($lbl); ?>">
                                                <input type="checkbox" class="wppoppop-ls-chk" data-type="<?php echo esc_attr($__t); ?>" data-tab="<?php echo esc_attr($__tab); ?>" data-key="<?php echo esc_attr($e['key']); ?>" data-default="<?php echo $def ? '1' : '0'; ?>" <?php checked($on); ?>>
                                                <span class="trk"></span>
                                            </label>
                                        </li>
                                        <?php
                                    };
                                    $__render_row($__e, false);
                                    foreach ($__e['rows'] as $__r) {
                                        $__render_row($__r, true);
                                    }
                                }
                                ?>
                            </ul>
                        </div>
                    <?php $__ls_p1 = false; endforeach; ?>
                </section>
            <?php endforeach; ?>
        </div>
    </div>
</div>
