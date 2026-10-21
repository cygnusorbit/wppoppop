<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<!-- WPPOPPOP_CELEBRATION_UI: 19. Success Celebration -->
            <div class="wppoppop-acc-item">
                <button type="button" class="wppoppop-acc-header">19. Success Celebration</button>
                <div class="wppoppop-acc-body" style="display:none;">
                    <label for="set-celebration-effect">Animation on successful form submit</label>
                    <select id="set-celebration-effect" class="widefat">
                        <option value="default">Default (confetti + enabled libraries)</option>
                        <option value="none">None</option>
                        <option value="burst">Confetti Burst (built-in)</option>
                        <option value="cannon">Confetti Cannons (needs Canvas Confetti library)</option>
                        <option value="fireworks">Fireworks (needs Canvas Fireworks library)</option>
                        <option value="rain">Confetti Rain (built-in)</option>
                        <option value="stars">Star Burst (built-in)</option>
                        <option value="both">Burst + Fireworks</option>
                    </select>
                    <p style="font-size:11px;color:#64748b;margin:6px 0 0;">Library-based effects require enabling the matching library under WpPopPop &rarr; Settings &rarr; Libraries.</p>
                </div>
            </div>
