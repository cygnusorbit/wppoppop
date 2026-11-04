<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<!-- 13. WooCommerce Conversion Suite -->
            <div class="wppoppop-acc-item">
                <button type="button" class="wppoppop-acc-header"><?php esc_html_e('WooCommerce Conversion Suite', 'wppoppop'); ?></button>
                <div class="wppoppop-acc-body" style="display:none;">
                    <label><input type="checkbox" id="set-wc-coupon" value="1"> <?php esc_html_e('Auto-Generate Dynamic Coupon', 'wppoppop'); ?></label>
                    <input type="text" id="set-wc-amount" placeholder="<?php esc_attr_e('Discount Amount (%)', 'wppoppop'); ?>" class="widefat" style="margin-top:6px;">
                </div>
            </div>
