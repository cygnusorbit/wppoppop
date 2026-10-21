<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<!-- 18. Notifications -->
<div class="wppoppop-acc-item" id="wppoppop-ntf-acc">
    <button type="button" class="wppoppop-acc-header">18. Notifications</button>
    <div class="wppoppop-acc-body" style="display:none;">
        <p class="description" style="margin:0 0 10px;font-style:italic;"><?php esc_html_e('After a successful form submission the notification, welcome, thank-you or any other email can be sent. You can customize these emails and use conditional logic.', 'wppoppop'); ?></p>
        <div id="wppoppop-ntf-list"></div>
        <button type="button" class="button" id="wppoppop-ntf-add" style="margin-top:8px;display:inline-flex;align-items:center;justify-content:center;gap:4px;line-height:1;min-height:30px;"><span class="dashicons dashicons-plus" style="display:inline-flex;align-items:center;justify-content:center;font-size:16px;width:16px;height:16px;line-height:1;margin:0;padding:0;"></span> <?php esc_html_e('Add notification', 'wppoppop'); ?></button>
    </div>
</div>
