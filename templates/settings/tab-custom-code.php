<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-settings-section">
    <h3>Global Custom Styling & Scripts</h3>
    <table class="form-table">
        <tr>
            <th scope="row"><label for="custom_css">Global Custom CSS</label></th>
            <td>
                <textarea name="custom_css" id="custom_css" rows="6" class="large-text code" placeholder=".wppoppop-box { font-family: sans-serif; }"><?php echo esc_textarea($settings['custom_css'] ?? ''); ?></textarea>
                <p class="description">CSS rules injected across all pages where WpPopPop popups render.</p>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="custom_js">Global Custom JavaScript</label></th>
            <td>
                <textarea name="custom_js" id="custom_js" rows="6" class="large-text code" placeholder="jQuery(document).on('wppoppop_rendered', function() { ... });"><?php echo esc_textarea($settings['custom_js'] ?? ''); ?></textarea>
                <p class="description">JavaScript callbacks executed during the popup initialization lifecycle.</p>
            </td>
        </tr>
    </table>
</div>
