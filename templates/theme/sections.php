<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<datalist id="wpp-font-presets">
    <?php foreach (WpPopPop_Admin_Theme::font_presets() as $preset) : ?>
        <option value="<?php echo esc_attr($preset); ?>"></option>
    <?php endforeach; ?>
</datalist>

<nav class="wpp-theme-tabs" role="tablist" aria-label="<?php esc_attr_e('Theme sections', 'wppoppop'); ?>">
    <?php $first = true; foreach ($theme_schema as $gid => $group) : ?>
        <button type="button" role="tab" class="wpp-theme-tab<?php echo $first ? ' is-active' : ''; ?>" data-tab="<?php echo esc_attr($gid); ?>"><?php echo esc_html($group['label']); ?></button>
    <?php $first = false; endforeach; ?>
</nav>

<?php $first = true; foreach ($theme_schema as $gid => $group) : ?>
    <section class="wppoppop-box wpp-theme-panel<?php echo $first ? ' is-active' : ''; ?>" data-panel="<?php echo esc_attr($gid); ?>">
        <h2><?php echo esc_html($group['label']); ?></h2>
        <div class="wpp-theme-fields">
            <?php foreach ($group['fields'] as $key => $field) :
                $val   = $theme_values[$key];
                $name  = 'wpp_theme[' . $key . ']';
                $id    = 'wpp-f-' . $key;
                $var   = WpPopPop_Admin_Theme::css_var($key);
                $is_color = ($field['type'] === 'color');
                $is_wide  = in_array($field['type'], ['font', 'textarea', 'shadow', 'media'], true);
                ?>
                <div class="wpp-theme-field<?php echo $is_wide ? ' is-wide' : ''; ?><?php echo $field['type'] === 'bool' ? ' is-bool' : ''; ?>" data-label="<?php echo esc_attr(strtolower($field['label'] . ' ' . $key)); ?>">
                    <label for="<?php echo esc_attr($id); ?>"><?php echo esc_html($field['label']); ?></label>
                    <?php if ($field['type'] === 'bool') : ?>
                        <input type="checkbox" id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($name); ?>" value="1" data-default="<?php echo (int) $field['default']; ?>" <?php checked(!empty($val)); ?>>
                    <?php elseif ($field['type'] === 'media') :
                        $wpp_logo_url = $val ? wp_get_attachment_image_url((int) $val, 'full') : '';
                        $wpp_def_logo = WPPOPPOP_URL . 'admin/img/logo-temp.svg';
                        ?>
                        <div class="wpp-theme-media" data-default="<?php echo esc_url($wpp_def_logo); ?>">
                            <input type="hidden" id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($name); ?>" value="<?php echo (int) $val; ?>" data-default="0">
                            <div class="wpp-theme-media-preview" style="display:inline-flex;align-items:center;justify-content:center;min-width:56px;min-height:44px;padding:6px 10px;margin:0 0 8px;border:1px solid #c3c4c7;border-radius:3px;background:#fff;">
                                <img src="<?php echo esc_url($wpp_logo_url ? $wpp_logo_url : $wpp_def_logo); ?>" alt="" style="display:block;max-width:140px;max-height:44px;object-fit:contain;">
                            </div>
                            <div>
                                <button type="button" class="button wpp-theme-media-pick"><?php echo $val ? esc_html__('Replace logo', 'wppoppop') : esc_html__('Upload logo', 'wppoppop'); ?></button>
                                <button type="button" class="button wpp-theme-media-remove"<?php echo $val ? '' : ' style="display:none;"'; ?>><?php esc_html_e('Remove', 'wppoppop'); ?></button>
                            </div>
                            <p class="description" style="margin:6px 0 0;"><?php esc_html_e('Shown in the top bar of every WpPopPop page. Leave empty to use the default logo.', 'wppoppop'); ?></p>
                        </div>
                    <?php elseif ($field['type'] === 'enum') : ?>
                        <select id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($name); ?>" data-wpp-var="<?php echo esc_attr($var); ?>" data-default="<?php echo esc_attr($field['default']); ?>">
                            <?php foreach ($field['options'] as $ov => $ol) : ?>
                                <option value="<?php echo esc_attr($ov); ?>" <?php selected($val, (string) $ov); ?>><?php echo esc_html($ol); ?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php elseif ($field['type'] === 'textarea') : ?>
                        <textarea id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($name); ?>" rows="8" data-default=""><?php echo esc_textarea($val); ?></textarea>
                    <?php else : ?>
                        <div class="wpp-theme-input-row">
                            <?php if ($is_color) : ?>
                                <input type="color" class="wpp-theme-picker" value="<?php echo esc_attr(preg_match('/^#[0-9a-f]{6}$/i', $val) ? $val : '#000000'); ?>" aria-label="<?php echo esc_attr($field['label']); ?>">
                            <?php endif; ?>
                            <input type="text" id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($name); ?>"
                                   value="<?php echo esc_attr($val); ?>"
                                   data-wpp-var="<?php echo esc_attr($var); ?>"
                                   data-default="<?php echo esc_attr($field['default']); ?>"
                                   <?php echo $field['type'] === 'font' ? 'list="wpp-font-presets"' : ''; ?>>
                            <button type="button" class="wpp-theme-field-reset" title="<?php esc_attr_e('Reset to default', 'wppoppop'); ?>">&#8634;</button>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
<?php $first = false; endforeach; ?>
