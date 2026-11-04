<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<!-- 9. Marketing & Webhooks -->
            <div class="wppoppop-acc-item">
                <button type="button" class="wppoppop-acc-header"><?php esc_html_e('Marketing & Webhooks', 'wppoppop'); ?></button>
                <div class="wppoppop-acc-body" style="display:none;">
                    <label><?php esc_html_e('Webhook URL (POST)', 'wppoppop'); ?></label>
                    <input type="url" id="set-webhook-url" placeholder="https://..." class="widefat" style="margin-bottom:6px;">
                    <input type="text" id="set-webhook-secret" placeholder="<?php esc_attr_e('HMAC Secret Key', 'wppoppop'); ?>" class="widefat">
                </div>
            </div>
