<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- template variables are local to the method that includes this file
if (!defined('ABSPATH')) {
    exit;
}

$preload_popups       = !empty($settings['preload_popups']);
$preload_events       = !empty($settings['preload_events']);
$disable_google_fonts = !empty($settings['disable_google_fonts']);
$minify_css           = !empty($settings['minify_css']);
$cache_busting        = !empty($settings['cache_busting']);
$render_delay         = isset($settings['render_delay']) ? (int) $settings['render_delay'] : 0;
$default_embed_type   = (isset($settings['default_embed_type']) && $settings['default_embed_type'] === 'onload') ? 'onload' : 'inline';
?>
<div class="wppoppop-settings-section">
    <h3><?php esc_html_e('Speed, Asset Delivery & Preload Engine', 'wppoppop'); ?></h3>
    <table class="form-table">
        <tr>
            <th scope="row"><label for="setting-default-embed-type"><?php esc_html_e('Default Embed Type', 'wppoppop'); ?></label></th>
            <td>
                <select id="setting-default-embed-type" name="settings[default_embed_type]">
                    <option value="inline" <?php selected($default_embed_type, 'inline'); ?>><?php esc_html_e('Inline Popup', 'wppoppop'); ?></option>
                    <option value="onload" <?php selected($default_embed_type, 'onload'); ?>><?php esc_html_e('Onload Popup', 'wppoppop'); ?></option>
                </select>
                <p class="description"><?php esc_html_e('Used by [wppoppop uid="xxx"] when the shortcode has no mode attribute.', 'wppoppop'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php esc_html_e('Popup Preloading', 'wppoppop'); ?></th>
            <td>
                <label>
                    <input type="checkbox" name="settings[preload_popups]" value="1" <?php checked($preload_popups); ?>>
                    <?php esc_html_e('Preload published popup HTML containers in the page footer', 'wppoppop'); ?>
                </label>
                <p class="description"><?php esc_html_e('Eliminates network latency on rapid exit-intent or short countdown timers.', 'wppoppop'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php esc_html_e('Trigger Events Preload', 'wppoppop'); ?></th>
            <td>
                <label>
                    <input type="checkbox" name="settings[preload_events]" value="1" <?php checked($preload_events); ?>>
                    <?php esc_html_e('Arm exit-intent, scroll, and timer event listeners immediately on DOM ready', 'wppoppop'); ?>
                </label>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php esc_html_e('External Web Fonts', 'wppoppop'); ?></th>
            <td>
                <label>
                    <input type="checkbox" name="settings[disable_google_fonts]" value="1" <?php checked($disable_google_fonts); ?>>
                    <?php esc_html_e('Disable external Google Fonts (Enforce local system font fallbacks & GDPR compliance)', 'wppoppop'); ?>
                </label>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php esc_html_e('Inline Minification', 'wppoppop'); ?></th>
            <td>
                <label>
                    <input type="checkbox" name="settings[minify_css]" value="1" <?php checked($minify_css); ?>>
                    <?php esc_html_e('Minify inline popup styling rules to reduce DOM footprint', 'wppoppop'); ?>
                </label>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php esc_html_e('Asset Cache Invalidation', 'wppoppop'); ?></th>
            <td>
                <label>
                    <input type="checkbox" name="settings[cache_busting]" value="1" <?php checked($cache_busting); ?>>
                    <?php esc_html_e('Append dynamic version stamps to popup asset URLs to bypass proxy caches', 'wppoppop'); ?>
                </label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="setting-render-delay"><?php esc_html_e('Runtime Injection Delay', 'wppoppop'); ?></label></th>
            <td>
                <input type="number" id="setting-render-delay" name="settings[render_delay]" value="<?php echo esc_attr($render_delay); ?>" min="0" step="50" class="small-text"> ms
                <p class="description"><?php esc_html_e('Delay before initializing the frontend runtime in milliseconds (0 = immediate DOMReady execution).', 'wppoppop'); ?></p>
            </td>
        </tr>
        <?php do_action('wppoppop_settings_performance_rows', $settings); ?>
    </table>
</div>
