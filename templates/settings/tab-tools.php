<?php
if (!defined('ABSPATH')) {
    exit;
}

$clean_on_uninstall = !empty($settings['clean_on_uninstall']);
$cookie_epoch       = isset($settings['cookie_epoch']) ? (int) $settings['cookie_epoch'] : (int) get_option('wppoppop_cookie_epoch', 1);
$db_version         = get_option('wppoppop_db_version', defined('WPPOPPOP_VERSION') ? WPPOPPOP_VERSION : '1.0.0');
?>
<div class="wppoppop-settings-section">
    <h3><?php esc_html_e('System Diagnostics & Cookie Cache Management', 'wppoppop'); ?></h3>
    <table class="form-table">
        <tr>
            <th scope="row"><?php esc_html_e('Cookie Epoch & Reset', 'wppoppop'); ?></th>
            <td>
                <p style="margin-bottom:10px;">
                    <?php esc_html_e('Current Cookie Epoch Version:', 'wppoppop'); ?>
                    <code><span id="wppoppop-current-cookie-epoch"><?php echo esc_html($cookie_epoch); ?></span></code>
                </p>
                <button type="button" id="wppoppop-reset-cookies-btn" class="button button-secondary">
                    <?php esc_html_e('Reset All Visitor Cookies', 'wppoppop'); ?>
                </button>
                <span id="wppoppop-cookie-reset-status" style="margin-left:12px;font-weight:600;"></span>
                <p class="description" style="margin-top:8px;">
                    <?php esc_html_e('Increments the global cookie epoch. Forces frequency-capped popups to display again even if previously dismissed.', 'wppoppop'); ?>
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php esc_html_e('Database Schema Version', 'wppoppop'); ?></th>
            <td>
                <code><?php echo esc_html($db_version); ?></code>
                <p class="description"><?php esc_html_e('Current database migration schema version installed in the WordPress database.', 'wppoppop'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php esc_html_e('Uninstall Data Removal', 'wppoppop'); ?></th>
            <td>
                <label>
                    <input type="checkbox" name="settings[clean_on_uninstall]" value="1" <?php checked($clean_on_uninstall); ?>>
                    <span style="color:#b91c1c;font-weight:600;"><?php esc_html_e('Purge all database tables and settings upon plugin deletion', 'wppoppop'); ?></span>
                </label>
                <p class="description"><?php esc_html_e('Warning: If enabled, deleting the plugin will permanently drop campaigns, submissions, and analytics tables.', 'wppoppop'); ?></p>
            </td>
        </tr>
    </table>
</div>
