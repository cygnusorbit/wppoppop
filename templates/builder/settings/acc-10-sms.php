<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<!-- 10. Twilio SMS Alerts -->
            <div class="wppoppop-acc-item">
                <button type="button" class="wppoppop-acc-header"><?php esc_html_e('Twilio SMS Alerts', 'wppoppop'); ?></button>
                <div class="wppoppop-acc-body" style="display:none;">
                    <label><input type="checkbox" id="set-sms-enable" value="1"> <?php esc_html_e('Enable Lead SMS Notification', 'wppoppop'); ?></label>
                    <input type="text" id="set-sms-phone" placeholder="+1234567890" class="widefat" style="margin-top:6px;">
                </div>
            </div>
