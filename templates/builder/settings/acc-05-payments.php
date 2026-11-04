<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<!-- 5. Payments & Checkout -->
            <div class="wppoppop-acc-item">
                <button type="button" class="wppoppop-acc-header"><?php esc_html_e('Payments & Checkout', 'wppoppop'); ?></button>
                <div class="wppoppop-acc-body" style="display:none;">
                    <label><?php esc_html_e('Gateway', 'wppoppop'); ?></label>
                    <select id="set-pay-gateway" class="widefat" style="margin-bottom:6px;">
                        <option value="stripe">Stripe</option>
                        <option value="paypal">PayPal</option>
                    </select>
                    <input type="number" step="0.01" id="set-pay-amount" placeholder="<?php esc_attr_e('Amount (e.g. 19.99)', 'wppoppop'); ?>" class="widefat">
                </div>
            </div>
