<?php
if (!defined('ABSPATH')) {
    exit;
}

$default_font          = isset($settings['default_font']) ? $settings['default_font'] : 'inherit';
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
                    <option value="Inter, sans-serif" <?php selected($default_font, 'Inter, sans-serif'); ?>>Inter</option>
                    <option value="Roboto, sans-serif" <?php selected($default_font, 'Roboto, sans-serif'); ?>>Roboto</option>
                    <option value="Montserrat, sans-serif" <?php selected($default_font, 'Montserrat, sans-serif'); ?>>Montserrat</option>
                    <option value="Poppins, sans-serif" <?php selected($default_font, 'Poppins, sans-serif'); ?>>Poppins</option>
                    <option value="Open Sans, sans-serif" <?php selected($default_font, 'Open Sans, sans-serif'); ?>>Open Sans</option>
                    <option value="Lato, sans-serif" <?php selected($default_font, 'Lato, sans-serif'); ?>>Lato</option>
                </select>
                <p class="description"><?php esc_html_e('Fallback typography applied to campaign elements when no specific custom font is defined.', 'wppoppop'); ?></p>
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
                <textarea id="setting-custom-fonts" name="settings[custom_fonts]" rows="3" class="large-text" placeholder="FontName: url(https://.../font.woff2)"><?php echo esc_textarea($custom_fonts); ?></textarea>
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
