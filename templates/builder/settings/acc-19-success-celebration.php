<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<!-- WPPOPPOP_CELEBRATION_UI: 19. Success Celebration -->
            <div class="wppoppop-acc-item">
                <button type="button" class="wppoppop-acc-header"><?php esc_html_e('Success Celebration', 'wppoppop'); ?></button>
                <div class="wppoppop-acc-body" style="display:none;">
                    <label for="set-celebration-effect"><?php esc_html_e('Animation on successful form submit', 'wppoppop'); ?></label>
                    <select id="set-celebration-effect" class="widefat">
                        <option value="default"><?php esc_html_e('Default (confetti + enabled libraries)', 'wppoppop'); ?></option>
                        <option value="none"><?php esc_html_e('None', 'wppoppop'); ?></option>
                        <option value="burst"><?php esc_html_e('Confetti Burst (built-in)', 'wppoppop'); ?></option>
                        <option value="cannon"><?php esc_html_e('Confetti Cannons (needs Canvas Confetti library)', 'wppoppop'); ?></option>
                        <option value="fireworks"><?php esc_html_e('Fireworks (needs Canvas Fireworks library)', 'wppoppop'); ?></option>
                        <option value="rain"><?php esc_html_e('Confetti Rain (built-in)', 'wppoppop'); ?></option>
                        <option value="stars"><?php esc_html_e('Star Burst (built-in)', 'wppoppop'); ?></option>
                        <option value="both"><?php esc_html_e('Burst + Fireworks', 'wppoppop'); ?></option>
                    </select>
                    <p style="font-size:11px;color:#64748b;margin:6px 0 0;"><?php esc_html_e('Library-based effects require enabling the matching library under WpPopPop → Settings → Libraries.', 'wppoppop'); ?></p>
                </div>
            </div>
