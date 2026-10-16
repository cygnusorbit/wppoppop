<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<!-- 5. Payments & Checkout -->
            <div class="wppoppop-acc-item">
                <button type="button" class="wppoppop-acc-header">5. Payments &amp; Checkout</button>
                <div class="wppoppop-acc-body" style="display:none;">
                    <label>Gateway</label>
                    <select id="set-pay-gateway" class="widefat" style="margin-bottom:6px;">
                        <option value="stripe">Stripe</option>
                        <option value="paypal">PayPal</option>
                    </select>
                    <input type="number" step="0.01" id="set-pay-amount" placeholder="Amount (e.g. 19.99)" class="widefat">
                </div>
            </div>
