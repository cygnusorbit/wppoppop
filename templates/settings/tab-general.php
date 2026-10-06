<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-settings-section">
    <h3>General & Mailing Settings</h3>
    <table class="form-table">
        <tr>
            <th scope="row"><label for="sender_name">Sender Name</label></th>
            <td>
                <input type="text" name="sender_name" id="sender_name" class="regular-text" value="<?php echo esc_attr($settings['sender_name'] ?? 'WpPopPop'); ?>">
                <p class="description">Default display name used in automated subscriber autoresponder emails.</p>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="sender_email">Sender Email</label></th>
            <td>
                <input type="email" name="sender_email" id="sender_email" class="regular-text" value="<?php echo esc_attr($settings['sender_email'] ?? get_option('admin_email')); ?>">
                <p class="description">Email address from which notifications and confirmation links originate.</p>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="csv_separator">CSV Delimiter</label></th>
            <td>
                <select name="csv_separator" id="csv_separator">
                    <option value="," <?php selected($settings['csv_separator'] ?? ',', ','); ?>>Comma (,)</option>
                    <option value=";" <?php selected($settings['csv_separator'] ?? ',', ';'); ?>>Semicolon (;)</option>
                    <option value="tab" <?php selected($settings['csv_separator'] ?? ',', 'tab'); ?>>Tab</option>
                </select>
                <p class="description">Column delimiter for exported leads CSV spreadsheets.</p>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="user_uploads">File Uploads Storage</label></th>
            <td>
                <select name="user_uploads" id="user_uploads">
                    <option value="keep" <?php selected($settings['user_uploads'] ?? 'keep', 'keep'); ?>>Retain Uploads Permanently</option>
                    <option value="delete" <?php selected($settings['user_uploads'] ?? 'keep', 'delete'); ?>>Purge When Lead Record is Deleted</option>
                </select>
                <p class="description">Lifecycle policy for files submitted through form upload layers.</p>
            </td>
        </tr>
    </table>
</div>
