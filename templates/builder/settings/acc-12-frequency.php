<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<!-- 12. Frequency Capping & Cookies -->
            <div class="wppoppop-acc-item">
                <button type="button" class="wppoppop-acc-header"><?php esc_html_e('Frequency Capping & Cookies', 'wppoppop'); ?></button>
                <div class="wppoppop-acc-body" style="display:none;">
                    <label><?php esc_html_e('Show Frequency', 'wppoppop'); ?></label>
                    <select id="set-freq-mode" class="widefat">
                        <option value="always"><?php esc_html_e('Always Display', 'wppoppop'); ?></option>
                        <option value="session"><?php esc_html_e('Once Per Browser Session', 'wppoppop'); ?></option>
                        <option value="days"><?php esc_html_e('Once Every X Days', 'wppoppop'); ?></option>
                    </select>
                </div>
            </div>
