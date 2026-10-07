<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-settings-section">
    <h3><?php esc_html_e('General & Mailing Settings', 'wppoppop'); ?></h3>
    <table class="form-table">
        <tr>
            <th scope="row"><label for="sender_name"><?php esc_html_e('Sender Name', 'wppoppop'); ?></label></th>
            <td>
                <input type="text" name="wppoppop_settings[sender_name]" id="sender_name" class="regular-text" value="<?php echo esc_attr($settings['sender_name'] ?? 'WpPopPop'); ?>">
                <p class="description"><?php esc_html_e('Default display name used in automated subscriber autoresponder emails.', 'wppoppop'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="sender_email"><?php esc_html_e('Sender Email', 'wppoppop'); ?></label></th>
            <td>
                <input type="email" name="wppoppop_settings[sender_email]" id="sender_email" class="regular-text" value="<?php echo esc_attr($settings['sender_email'] ?? get_option('admin_email')); ?>">
                <p class="description"><?php esc_html_e('Email address from which notifications and confirmation links originate.', 'wppoppop'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="csv_separator"><?php esc_html_e('CSV Delimiter', 'wppoppop'); ?></label></th>
            <td>
                <select name="wppoppop_settings[csv_separator]" id="csv_separator">
                    <option value="," <?php selected($settings['csv_separator'] ?? ',', ','); ?>><?php esc_html_e('Comma (,)', 'wppoppop'); ?></option>
                    <option value=";" <?php selected($settings['csv_separator'] ?? ',', ';'); ?>><?php esc_html_e('Semicolon (;)', 'wppoppop'); ?></option>
                    <option value="tab" <?php selected($settings['csv_separator'] ?? ',', 'tab'); ?>><?php esc_html_e('Tab', 'wppoppop'); ?></option>
                </select>
                <p class="description"><?php esc_html_e('Column delimiter for exported leads CSV spreadsheets.', 'wppoppop'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="user_uploads"><?php esc_html_e('File Uploads Storage', 'wppoppop'); ?></label></th>
            <td>
                <select name="wppoppop_settings[user_uploads]" id="user_uploads">
                    <option value="keep" <?php selected($settings['user_uploads'] ?? 'keep', 'keep'); ?>><?php esc_html_e('Retain Uploads Permanently', 'wppoppop'); ?></option>
                    <option value="delete" <?php selected($settings['user_uploads'] ?? 'keep', 'delete'); ?>><?php esc_html_e('Purge When Lead Record is Deleted', 'wppoppop'); ?></option>
                </select>
                <p class="description"><?php esc_html_e('Lifecycle policy for files submitted through form upload layers.', 'wppoppop'); ?></p>
            </td>
        </tr>
    </table>
</div>
