<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<!-- 15. Quiz & Lead Scoring -->
            <div class="wppoppop-acc-item">
                <button type="button" class="wppoppop-acc-header">15. Quiz &amp; Lead Scoring</button>
                <div class="wppoppop-acc-body" style="display:none;">
                    <label><input type="checkbox" id="set-quiz-enable" value="1"> Enable Lead Scoring</label>
                    <input type="number" id="set-quiz-pass" placeholder="Pass Threshold Score" class="widefat" style="margin:6px 0;">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:6px;">
                        <div>
                            <label style="font-size:11px;font-weight:600;color:#475569;">Pass Canvas</label>
                            <input type="number" id="set-quiz-pass-canvas" name="set-quiz-pass-screen" value="2" class="widefat" style="font-size:12px;">
                        </div>
                        <div>
                            <label style="font-size:11px;font-weight:600;color:#475569;">Fail Canvas</label>
                            <input type="number" id="set-quiz-fail-canvas" name="set-quiz-fail-screen" value="3" class="widefat" style="font-size:12px;">
                        </div>
                    </div>
                    <label><input type="checkbox" id="set-quiz-confetti" value="1"> Trigger Particle Confetti on Pass</label>
                </div>
            </div>

<!-- WPPOPPOP_CELEBRATION_UI: 16. Success Celebration -->
            <div class="wppoppop-acc-item">
                <button type="button" class="wppoppop-acc-header">16. Success Celebration</button>
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
