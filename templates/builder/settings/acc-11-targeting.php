<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<!-- 11. Targeting & Attribution -->
            <div class="wppoppop-acc-item">
                <button type="button" class="wppoppop-acc-header"><?php esc_html_e('Targeting & Attribution', 'wppoppop'); ?></button>
                <div class="wppoppop-acc-body" style="display:none;">
                    <label><?php esc_html_e('Visitor Authentication', 'wppoppop'); ?></label>
                    <select id="set-target-auth" class="widefat" style="margin-bottom:6px;">
                        <option value="all"><?php esc_html_e('All Visitors', 'wppoppop'); ?></option>
                        <option value="guest"><?php esc_html_e('Guests Only', 'wppoppop'); ?></option>
                        <option value="user"><?php esc_html_e('Logged-In Users Only', 'wppoppop'); ?></option>
                    </select>
                </div>
            </div>
