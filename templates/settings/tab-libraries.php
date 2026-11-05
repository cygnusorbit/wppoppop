<?php
if (!defined('ABSPATH')) {
    exit;
}

$default_font          = isset($settings['default_font']) ? $settings['default_font'] : 'inherit';
$df_weight             = isset($settings['default_font_weight']) ? (string) $settings['default_font_weight'] : 'inherit'; /* WPPOPPOP_DEFAULT_TYPO */
$df_size               = isset($settings['default_font_size']) ? (int) $settings['default_font_size'] : 0;
$df_align              = isset($settings['default_text_align']) ? (string) $settings['default_text_align'] : 'inherit';
$df_italic             = !empty($settings['default_font_italic']);
$df_underline          = !empty($settings['default_font_underline']);
$df_color              = isset($settings['default_font_color']) ? (string) $settings['default_font_color'] : '';
$df_weights            = ['inherit' => 'inherit - Inherit', '300' => '300 - Light', '400' => '400 - Regular', '500' => '500 - Medium', '600' => '600 - Semi Bold', '700' => '700 - Bold'];
$google_fonts          = !empty($settings['google_fonts']);
$font_awesome          = !empty($settings['font_awesome']);
$custom_fonts          = isset($settings['custom_fonts']) ? $settings['custom_fonts'] : '';
$load_canvas_confetti  = !empty($settings['load_canvas_confetti']);
$load_canvas_fireworks = !empty($settings['load_canvas_fireworks']);
$air_datepicker        = (!empty($settings['air_datepicker']) || !empty($settings['load_air_datepicker']));
$jquery_mask           = (!empty($settings['jquery_mask']) || !empty($settings['load_jquery_mask']));
$signature_pad         = (!empty($settings['signature_pad']) || !empty($settings['load_signature_pad']));
$range_slider          = (!empty($settings['range_slider']) || !empty($settings['load_range_slider']));
$js_parser             = (!empty($settings['js_parser']) || !empty($settings['load_math_parser']));
?>
<div class="wppoppop-settings-section">
    <h3><?php esc_html_e('Typography & Component Extensions', 'wppoppop'); ?></h3>
    <table class="form-table">
        <tr>
            <th scope="row"><label for="setting-default-font"><?php esc_html_e('Default Font Family', 'wppoppop'); ?></label></th>
            <td>
                <select id="setting-default-font" name="settings[default_font]">
                    <option value="inherit" <?php selected($default_font, 'inherit'); ?>><?php esc_html_e('Theme Inherited (Default)', 'wppoppop'); ?></option>
                    <option value="Inter, sans-serif" <?php selected($default_font, 'Inter, sans-serif'); ?>><?php esc_html_e('Inter', 'wppoppop'); ?></option>
                    <option value="Roboto, sans-serif" <?php selected($default_font, 'Roboto, sans-serif'); ?>><?php esc_html_e('Roboto', 'wppoppop'); ?></option>
                    <option value="Montserrat, sans-serif" <?php selected($default_font, 'Montserrat, sans-serif'); ?>><?php esc_html_e('Montserrat', 'wppoppop'); ?></option>
                    <option value="Poppins, sans-serif" <?php selected($default_font, 'Poppins, sans-serif'); ?>><?php esc_html_e('Poppins', 'wppoppop'); ?></option>
                    <option value="Open Sans, sans-serif" <?php selected($default_font, 'Open Sans, sans-serif'); ?>><?php esc_html_e('Open Sans', 'wppoppop'); ?></option>
                    <option value="Lato, sans-serif" <?php selected($default_font, 'Lato, sans-serif'); ?>><?php esc_html_e('Lato', 'wppoppop'); ?></option>
                </select>
                <p class="description"><?php esc_html_e('Fallback typography applied to campaign elements when no specific custom font is defined.', 'wppoppop'); ?></p>
            </td>
        </tr>
        <tr class="wppoppop-typo-row">
            <th scope="row"><?php esc_html_e('Default Font Style', 'wppoppop'); ?></th>
            <td>
                <div class="wppoppop-typo-grid">
                    <div class="wppoppop-typo-cell">
                        <select id="setting-default-font-weight" name="settings[default_font_weight]">
                            <?php foreach ($df_weights as $wv => $wl) : ?>
                                <option value="<?php echo esc_attr($wv); ?>" <?php selected($df_weight, (string) $wv); ?>><?php echo esc_html($wl); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="wppoppop-typo-cap"><?php esc_html_e('Font weight', 'wppoppop'); ?></span>
                    </div>
                    <div class="wppoppop-typo-cell">
                        <div class="wppoppop-typo-color">
                            <input type="color" id="setting-default-font-color-picker" value="<?php echo esc_attr($df_color !== '' ? $df_color : '#444444'); ?>" aria-label="<?php esc_attr_e('Pick color', 'wppoppop'); ?>">
                            <input type="text" id="setting-default-font-color" name="settings[default_font_color]" value="<?php echo esc_attr($df_color); ?>" placeholder="<?php esc_attr_e('inherit', 'wppoppop'); ?>" maxlength="7" autocomplete="off" spellcheck="false">
                        </div>
                        <span class="wppoppop-typo-cap"><?php esc_html_e('Color', 'wppoppop'); ?></span>
                    </div>
                    <div class="wppoppop-typo-cell">
                        <div class="wppoppop-typo-size">
                            <input type="number" id="setting-default-font-size" name="settings[default_font_size]" value="<?php echo $df_size > 0 ? esc_attr($df_size) : ''; ?>" min="0" max="200" step="1" placeholder="<?php esc_attr_e('auto', 'wppoppop'); ?>">
                            <span class="wppoppop-typo-unit">px</span>
                        </div>
                        <span class="wppoppop-typo-cap"><?php esc_html_e('Size', 'wppoppop'); ?></span>
                    </div>
                    <div class="wppoppop-typo-cell">
                        <div class="wppoppop-typo-seg" role="radiogroup" aria-label="<?php esc_attr_e('Alignment', 'wppoppop'); ?>">
                            <label title="<?php esc_attr_e('Inherit', 'wppoppop'); ?>"><input type="radio" name="settings[default_text_align]" value="inherit" <?php checked($df_align === 'left' || $df_align === 'center' || $df_align === 'right', false); ?>><span><?php esc_html_e('Auto', 'wppoppop'); ?></span></label>
                            <label title="<?php esc_attr_e('Left', 'wppoppop'); ?>"><input type="radio" name="settings[default_text_align]" value="left" <?php checked($df_align, 'left'); ?>><span class="dashicons dashicons-editor-alignleft"></span></label>
                            <label title="<?php esc_attr_e('Center', 'wppoppop'); ?>"><input type="radio" name="settings[default_text_align]" value="center" <?php checked($df_align, 'center'); ?>><span class="dashicons dashicons-editor-aligncenter"></span></label>
                            <label title="<?php esc_attr_e('Right', 'wppoppop'); ?>"><input type="radio" name="settings[default_text_align]" value="right" <?php checked($df_align, 'right'); ?>><span class="dashicons dashicons-editor-alignright"></span></label>
                        </div>
                        <span class="wppoppop-typo-cap"><?php esc_html_e('Alignment', 'wppoppop'); ?></span>
                    </div>
                    <div class="wppoppop-typo-cell">
                        <div class="wppoppop-typo-seg">
                            <label title="<?php esc_attr_e('Italic', 'wppoppop'); ?>"><input type="checkbox" name="settings[default_font_italic]" value="1" <?php checked($df_italic); ?>><span class="dashicons dashicons-editor-italic"></span></label>
                            <label title="<?php esc_attr_e('Underline', 'wppoppop'); ?>"><input type="checkbox" name="settings[default_font_underline]" value="1" <?php checked($df_underline); ?>><span class="dashicons dashicons-editor-underline"></span></label>
                        </div>
                        <span class="wppoppop-typo-cap"><?php esc_html_e('Style', 'wppoppop'); ?></span>
                    </div>
                </div>
                <p class="description"><?php esc_html_e('Default weight, size, color, alignment and style for popup text. Layers with their own typography override these. Leave on inherit / auto / blank to keep theme behavior.', 'wppoppop'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php esc_html_e('Font Asset Delivery', 'wppoppop'); ?></th>
            <td>
                <label style="display:block;margin-bottom:6px;">
                    <input type="checkbox" name="settings[google_fonts]" value="1" <?php checked($google_fonts); ?>>
                    <?php esc_html_e('Enqueue Google Fonts stylesheets on public frontend pages', 'wppoppop'); ?>
                </label>
                <label style="display:block;">
                    <input type="checkbox" name="settings[font_awesome]" value="1" <?php checked($font_awesome); ?>>
                    <?php esc_html_e('Enqueue Font Awesome icon font library for popup icon layers', 'wppoppop'); ?>
                </label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="setting-custom-fonts"><?php esc_html_e('Custom Fonts Definitions', 'wppoppop'); ?></label></th>
            <td>
                <textarea id="setting-custom-fonts" name="settings[custom_fonts]" rows="3" class="large-text" placeholder="<?php esc_attr_e('FontName: url(https://.../font.woff2)', 'wppoppop'); ?>"><?php echo esc_textarea($custom_fonts); ?></textarea>
                <p class="description"><?php esc_html_e('Custom web font declarations (one declaration per line) for the visual builder dropdown.', 'wppoppop'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php esc_html_e('Library Extensions', 'wppoppop'); ?></th>
            <td>
                <fieldset>
                    <legend class="screen-reader-text"><?php esc_html_e('Library Extensions', 'wppoppop'); ?></legend>
                    <label style="display:block;margin-bottom:8px;">
                        <input type="checkbox" name="settings[load_canvas_confetti]" value="1" <?php checked($load_canvas_confetti); ?>>
                        <strong><?php esc_html_e('Canvas Confetti', 'wppoppop'); ?></strong> — <?php esc_html_e('Celebratory confetti particle bursts on lead capture and prize unlocks.', 'wppoppop'); ?>
                    </label>
                    <label style="display:block;margin-bottom:8px;">
                        <input type="checkbox" name="settings[load_canvas_fireworks]" value="1" <?php checked($load_canvas_fireworks); ?>>
                        <strong><?php esc_html_e('Canvas Fireworks', 'wppoppop'); ?></strong> — <?php esc_html_e('Particle fireworks display for discount unlock screens.', 'wppoppop'); ?>
                    </label>
                    <label style="display:block;margin-bottom:8px;">
                        <input type="checkbox" name="settings[air_datepicker]" value="1" <?php checked($air_datepicker); ?>>
                        <strong><?php esc_html_e('Air Datepicker', 'wppoppop'); ?></strong> — <?php esc_html_e('Interactive calendar date and appointment picking controls.', 'wppoppop'); ?>
                    </label>
                    <label style="display:block;margin-bottom:8px;">
                        <input type="checkbox" name="settings[jquery_mask]" value="1" <?php checked($jquery_mask); ?>>
                        <strong><?php esc_html_e('jQuery Mask', 'wppoppop'); ?></strong> — <?php esc_html_e('Input format masks for phone numbers, postal codes, and currency.', 'wppoppop'); ?>
                    </label>
                    <label style="display:block;margin-bottom:8px;">
                        <input type="checkbox" name="settings[signature_pad]" value="1" <?php checked($signature_pad); ?>>
                        <strong><?php esc_html_e('Signature Pad', 'wppoppop'); ?></strong> — <?php esc_html_e('HTML5 canvas digital signature capture input fields.', 'wppoppop'); ?>
                    </label>
                    <label style="display:block;margin-bottom:8px;">
                        <input type="checkbox" name="settings[range_slider]" value="1" <?php checked($range_slider); ?>>
                        <strong><?php esc_html_e('Range Slider UI', 'wppoppop'); ?></strong> — <?php esc_html_e('Visual range value sliders for interactive surveys and cost estimators.', 'wppoppop'); ?>
                    </label>
                    <label style="display:block;">
                        <input type="checkbox" name="settings[js_parser]" value="1" <?php checked($js_parser); ?>>
                        <strong><?php esc_html_e('Math Formula Parser', 'wppoppop'); ?></strong> — <?php esc_html_e('Dynamic client-side math evaluation for quote calculators.', 'wppoppop'); ?>
                    </label>
                </fieldset>
            </td>
        </tr>
    </table>
</div>
<script>
(function () {
    'use strict';
    function init() {
        var gf  = document.querySelector('input[name="settings[google_fonts]"]');
        var off = document.querySelector('input[name="settings[disable_google_fonts]"]');
        if (!gf || !off) { return; }
        function sync() {
            if (off.checked) { gf.checked = false; }
            gf.disabled = off.checked;
        }
        off.addEventListener('change', sync);
        sync();
    }
    if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', init); } else { init(); }
})();
</script>
