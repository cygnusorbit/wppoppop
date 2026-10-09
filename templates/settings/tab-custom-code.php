<?php
if (!defined('ABSPATH')) {
    exit;
}

$custom_css         = isset($settings['custom_css']) ? $settings['custom_css'] : '';
$custom_js          = isset($settings['custom_js']) ? $settings['custom_js'] : '';
$custom_header_html = isset($settings['custom_header_html']) ? $settings['custom_header_html'] : '';
$custom_footer_html = isset($settings['custom_footer_html']) ? $settings['custom_footer_html'] : '';
?>
<div class="wppoppop-settings-section">
    <h3><?php esc_html_e('Global Custom Styling & JavaScript Callbacks', 'wppoppop'); ?></h3>
    <table class="form-table">
        <tr>
            <th scope="row"><label for="setting-custom-css"><?php esc_html_e('Global Custom CSS', 'wppoppop'); ?></label></th>
            <td>
                <textarea id="setting-custom-css" name="settings[custom_css]" rows="6" class="large-text code" placeholder=".wppoppop-box { font-family: sans-serif; }"><?php echo esc_textarea($custom_css); ?></textarea>
                <p class="description"><?php esc_html_e('CSS rules injected across all pages where WpPopPop popups render (without wrapping <style> tags).', 'wppoppop'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="setting-custom-js"><?php esc_html_e('Global Custom JavaScript', 'wppoppop'); ?></label></th>
            <td>
                <textarea id="setting-custom-js" name="settings[custom_js]" rows="6" class="large-text code" placeholder="jQuery(document).on('wppoppop_rendered', function() { ... });"><?php echo esc_textarea($custom_js); ?></textarea>
                <p class="description"><?php esc_html_e('JavaScript callbacks executed during popup display lifecycle (without wrapping <script> tags).', 'wppoppop'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="setting-header-html"><?php esc_html_e('Head Injection HTML', 'wppoppop'); ?></label></th>
            <td>
                <textarea id="setting-header-html" name="settings[custom_header_html]" rows="4" class="large-text code" placeholder="<meta ...> or <link ...>"><?php echo esc_textarea($custom_header_html); ?></textarea>
                <p class="description"><?php esc_html_e('Placed in the <head> element for tracking pixels or web font links.', 'wppoppop'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="setting-footer-html"><?php esc_html_e('Footer Tracking HTML', 'wppoppop'); ?></label></th>
            <td>
                <textarea id="setting-footer-html" name="settings[custom_footer_html]" rows="4" class="large-text code" placeholder="<!-- Conversion tracking pixels -->"><?php echo esc_textarea($custom_footer_html); ?></textarea>
                <p class="description"><?php esc_html_e('Placed immediately before the closing </body> tag.', 'wppoppop'); ?></p>
            </td>
        </tr>
    </table>
</div>
