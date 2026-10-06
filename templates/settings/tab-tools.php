<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-settings-section">
    <h3>System Tools & Cookie Cache Management</h3>
    <table class="form-table">
        <tr>
            <th scope="row">Cookie Epoch Reset</th>
            <td>
                <button type="button" class="button button-secondary" id="wppoppop-reset-cookies-btn">
                    Reset All Visitor Cookies
                </button>
                <p class="description">
                    Increments the global cookie epoch (Current: <code><?php echo esc_html(get_option('wppoppop_cookie_epoch', '1')); ?></code>). 
                    Forces popups with frequency caps to re-appear for visitors immediately.
                </p>
                <div id="wppoppop-cookie-reset-status" style="margin-top: 8px;"></div>
            </td>
        </tr>
        <tr>
            <th scope="row">Database Version</th>
            <td>
                <code><?php echo esc_html(get_option('wppoppop_db_version', WPPOPPOP_VERSION)); ?></code>
                <p class="description">Installed database migration schema version.</p>
            </td>
        </tr>
    </table>
</div>
