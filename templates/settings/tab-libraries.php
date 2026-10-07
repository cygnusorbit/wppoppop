<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-settings-section">
    <h3><?php esc_html_e('Typography & JavaScript Libraries', 'wppoppop'); ?></h3>
    <table class="form-table">
        <tr>
            <th scope="row"><?php esc_html_e('Google Fonts', 'wppoppop'); ?></th>
            <td>
                <label>
                    <input type="checkbox" name="wppoppop_settings[google_fonts]" value="1" <?php checked(!empty($settings['google_fonts'])); ?>>
                    <?php esc_html_e('Enqueue Google Fonts stylesheet on public pages', 'wppoppop'); ?>
                </label>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php esc_html_e('Font Awesome', 'wppoppop'); ?></th>
            <td>
                <label>
                    <input type="checkbox" name="wppoppop_settings[font_awesome]" value="1" <?php checked(!empty($settings['font_awesome'])); ?>>
                    <?php esc_html_e('Load Font Awesome icon font library', 'wppoppop'); ?>
                </label>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php esc_html_e('Custom Fonts', 'wppoppop'); ?></th>
            <td>
                <textarea name="wppoppop_settings[custom_fonts]" id="custom_fonts" rows="3" class="large-text" placeholder="FontName: url(https://.../font.woff2)"><?php echo esc_textarea($settings['custom_fonts'] ?? ''); ?></textarea>
                <p class="description"><?php esc_html_e('Custom web fonts definitions (one declaration per line).', 'wppoppop'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php esc_html_e('Component Extensions', 'wppoppop'); ?></th>
            <td>
                <fieldset>
                    <label><input type="checkbox" name="wppoppop_settings[air_datepicker]" value="1" <?php checked(!empty($settings['air_datepicker'])); ?>> <?php esc_html_e('Air Datepicker (Calendar Layers)', 'wppoppop'); ?></label><br>
                    <label><input type="checkbox" name="wppoppop_settings[jquery_mask]" value="1" <?php checked(!empty($settings['jquery_mask'])); ?>> <?php esc_html_e('jQuery Mask (Phone & Format Masks)', 'wppoppop'); ?></label><br>
                    <label><input type="checkbox" name="wppoppop_settings[signature_pad]" value="1" <?php checked(!empty($settings['signature_pad'])); ?>> <?php esc_html_e('Digital Signature Pad (HTML5 Canvas)', 'wppoppop'); ?></label><br>
                    <label><input type="checkbox" name="wppoppop_settings[range_slider]" value="1" <?php checked(!empty($settings['range_slider'])); ?>> <?php esc_html_e('Interactive Range Sliders', 'wppoppop'); ?></label><br>
                    <label><input type="checkbox" name="wppoppop_settings[js_parser]" value="1" <?php checked(!empty($settings['js_parser'])); ?>> <?php esc_html_e('Real-Time Dynamic Math Expression Parser', 'wppoppop'); ?></label>
                </fieldset>
            </td>
        </tr>
    </table>
</div>
