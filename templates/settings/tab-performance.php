<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-settings-section">
    <h3><?php esc_html_e('Performance & Preload Engine', 'wppoppop'); ?></h3>
    <table class="form-table">
        <tr>
            <th scope="row"><?php esc_html_e('Popup Preloading', 'wppoppop'); ?></th>
            <td>
                <label>
                    <input type="checkbox" name="wppoppop_settings[preload_popups]" value="1" <?php checked(!empty($settings['preload_popups'])); ?>>
                    <?php esc_html_e('Preload all active popup HTML containers in the page footer', 'wppoppop'); ?>
                </label>
                <p class="description"><?php esc_html_e('If disabled, popups load on-demand via REST/AJAX only when triggers fire.', 'wppoppop'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php esc_html_e('Trigger Events Preload', 'wppoppop'); ?></th>
            <td>
                <label>
                    <input type="checkbox" name="wppoppop_settings[preload_events]" value="1" <?php checked(!empty($settings['preload_events'])); ?>>
                    <?php esc_html_e('Arm trigger event listeners immediately on DOM ready', 'wppoppop'); ?>
                </label>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php esc_html_e('Analytics Integration', 'wppoppop'); ?></th>
            <td>
                <label>
                    <input type="checkbox" name="wppoppop_settings[ga_tracking]" value="1" <?php checked(!empty($settings['ga_tracking'])); ?>>
                    <?php esc_html_e('Automatically push events to gtag() and dataLayer', 'wppoppop'); ?>
                </label>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php esc_html_e('AdBlock Detection', 'wppoppop'); ?></th>
            <td>
                <label>
                    <input type="checkbox" name="wppoppop_settings[adblock_detector]" value="1" <?php checked(!empty($settings['adblock_detector'])); ?>>
                    <?php esc_html_e('Enable global AdBlock extension monitoring script', 'wppoppop'); ?>
                </label>
            </td>
        </tr>
    </table>
</div>
