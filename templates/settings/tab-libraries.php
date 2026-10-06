<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-settings-section">
    <h3>Typography & JavaScript Libraries</h3>
    <table class="form-table">
        <tr>
            <th scope="row">Google Fonts</th>
            <td>
                <label>
                    <input type="checkbox" name="google_fonts" value="1" <?php checked(!empty($settings['google_fonts'])); ?>>
                    Enqueue Google Fonts stylesheet on public pages
                </label>
            </td>
        </tr>
        <tr>
            <th scope="row">Font Awesome</th>
            <td>
                <label>
                    <input type="checkbox" name="font_awesome" value="1" <?php checked(!empty($settings['font_awesome'])); ?>>
                    Load Font Awesome icon font library
                </label>
            </td>
        </tr>
        <tr>
            <th scope="row">Custom Fonts</th>
            <td>
                <textarea name="custom_fonts" id="custom_fonts" rows="3" class="large-text" placeholder="FontName: url(https://.../font.woff2)"><?php echo esc_textarea($settings['custom_fonts'] ?? ''); ?></textarea>
                <p class="description">Custom web fonts definitions (one declaration per line).</p>
            </td>
        </tr>
        <tr>
            <th scope="row">Component Extensions</th>
            <td>
                <fieldset>
                    <label><input type="checkbox" name="air_datepicker" value="1" <?php checked(!empty($settings['air_datepicker'])); ?>> Air Datepicker (Calendar Layers)</label><br>
                    <label><input type="checkbox" name="jquery_mask" value="1" <?php checked(!empty($settings['jquery_mask'])); ?>> jQuery Mask (Phone & Format Masks)</label><br>
                    <label><input type="checkbox" name="signature_pad" value="1" <?php checked(!empty($settings['signature_pad'])); ?>> Digital Signature Pad (HTML5 Canvas)</label><br>
                    <label><input type="checkbox" name="range_slider" value="1" <?php checked(!empty($settings['range_slider'])); ?>> Interactive Range Sliders</label><br>
                    <label><input type="checkbox" name="js_parser" value="1" <?php checked(!empty($settings['js_parser'])); ?>> Real-Time Dynamic Math Expression Parser</label>
                </fieldset>
            </td>
        </tr>
    </table>
</div>
