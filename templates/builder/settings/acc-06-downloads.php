<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<!-- 6. Secure Downloads -->
            <div class="wppoppop-acc-item">
                <button type="button" class="wppoppop-acc-header"><?php esc_html_e('Secure Downloads', 'wppoppop'); ?></button>
                <div class="wppoppop-acc-body" style="display:none;">
                    <label><input type="checkbox" id="set-dl-enable" value="1"> <?php esc_html_e('Enable Tokenized Download', 'wppoppop'); ?></label>
                    <input type="url" id="set-dl-url" placeholder="<?php esc_attr_e('Protected File URL', 'wppoppop'); ?>" class="widefat" style="margin-top:6px;">
                </div>
            </div>
