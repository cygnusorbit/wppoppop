<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-settings-section">
    <h3>Performance & Preload Engine</h3>
    <table class="form-table">
        <tr>
            <th scope="row">Popup Preloading</th>
            <td>
                <label>
                    <input type="checkbox" name="preload_popups" value="1" <?php checked(!empty($settings['preload_popups'])); ?>>
                    Preload all active popup HTML containers in the page footer
                </label>
                <p class="description">If disabled, popups load on-demand via REST/AJAX only when triggers fire.</p>
            </td>
        </tr>
        <tr>
            <th scope="row">Trigger Events Preload</th>
            <td>
                <label>
                    <input type="checkbox" name="preload_events" value="1" <?php checked(!empty($settings['preload_events'])); ?>>
                    Arm trigger event listeners immediately on DOM ready
                </label>
            </td>
        </tr>
        <tr>
            <th scope="row">Analytics Integration</th>
            <td>
                <label>
                    <input type="checkbox" name="ga_tracking" value="1" <?php checked(!empty($settings['ga_tracking'])); ?>>
                    Automatically push events to <code>gtag()</code> and <code>dataLayer</code>
                </label>
            </td>
        </tr>
        <tr>
            <th scope="row">AdBlock Detection</th>
            <td>
                <label>
                    <input type="checkbox" name="adblock_detector" value="1" <?php checked(!empty($settings['adblock_detector'])); ?>>
                    Enable global AdBlock extension monitoring script
                </label>
            </td>
        </tr>
    </table>
</div>
