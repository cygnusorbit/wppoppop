<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<!-- 7. Video Playback Listeners -->
            <div class="wppoppop-acc-item">
                <button type="button" class="wppoppop-acc-header"><?php esc_html_e('Video Playback Listeners', 'wppoppop'); ?></button>
                <div class="wppoppop-acc-body" style="display:none;">
                    <label><input type="checkbox" id="set-vid-enable" value="1"> <?php esc_html_e('Listen for Video Playback', 'wppoppop'); ?></label>
                    <input type="number" id="set-vid-time" placeholder="<?php esc_attr_e('Trigger at timestamp (seconds)', 'wppoppop'); ?>" class="widefat" style="margin-top:6px;">
                </div>
            </div>
