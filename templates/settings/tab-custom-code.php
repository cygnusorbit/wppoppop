<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-settings-section">
    <h3><?php esc_html_e('Global Custom Styling & Scripts', 'wppoppop'); ?></h3>
    <table class="form-table">
        <tr>
            <th scope="row"><label for="custom_css"><?php esc_html_e('Global Custom CSS', 'wppoppop'); ?></label></th>
            <td>
                <textarea name="wppoppop_settings[custom_css]" id="custom_css" rows="6" class="large-text code" placeholder=".wppoppop-box { font-family: sans-serif; }"><?php echo esc_textarea($settings['custom_css'] ?? ''); ?></textarea>
                <p class="description"><?php esc_html_e('CSS rules injected across all pages where WpPopPop popups render.', 'wppoppop'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="custom_js"><?php esc_html_e('Global Custom JavaScript', 'wppoppop'); ?></label></th>
            <td>
                <textarea name="wppoppop_settings[custom_js]" id="custom_js" rows="6" class="large-text code" placeholder="jQuery(document).on('wppoppop_rendered', function() { ... });"><?php echo esc_textarea($settings['custom_js'] ?? ''); ?></textarea>
                <p class="description"><?php esc_html_e('JavaScript callbacks executed during the popup initialization lifecycle.', 'wppoppop'); ?></p>
            </td>
        </tr>
    </table>
</div>
