<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<!-- 4. Sticky Side Tabs -->
            <div class="wppoppop-acc-item">
                <button type="button" class="wppoppop-acc-header"><?php esc_html_e('Sticky Side Tabs', 'wppoppop'); ?></button>
                <div class="wppoppop-acc-body" style="display:none;">
                    <label><input type="checkbox" id="set-sidetab-enable" value="1"> <?php esc_html_e('Enable Sticky Side Tab', 'wppoppop'); ?></label>
                    <input type="text" id="set-sidetab-label" placeholder="<?php esc_attr_e('Feedback / Offer', 'wppoppop'); ?>" class="widefat" style="margin:6px 0;">
                    <select id="set-sidetab-pos" class="widefat">
                        <option value="left"><?php esc_html_e('Left Edge', 'wppoppop'); ?></option>
                        <option value="right"><?php esc_html_e('Right Edge', 'wppoppop'); ?></option>
                    </select>
                </div>
            </div>
