<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<!-- 15. Quiz & Lead Scoring -->
            <div class="wppoppop-acc-item">
                <button type="button" class="wppoppop-acc-header"><?php esc_html_e('Quiz & Lead Scoring', 'wppoppop'); ?></button>
                <div class="wppoppop-acc-body" style="display:none;">
                    <label><input type="checkbox" id="set-quiz-enable" value="1"> <?php esc_html_e('Enable Lead Scoring', 'wppoppop'); ?></label>
                    <input type="number" id="set-quiz-pass" placeholder="<?php esc_attr_e('Pass Threshold Score', 'wppoppop'); ?>" class="widefat" style="margin:6px 0;">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:6px;">
                        <div>
                            <label style="font-size:11px;font-weight:600;color:#475569;"><?php esc_html_e('Pass Canvas', 'wppoppop'); ?></label>
                            <input type="number" id="set-quiz-pass-canvas" name="set-quiz-pass-screen" value="2" class="widefat" style="font-size:12px;">
                        </div>
                        <div>
                            <label style="font-size:11px;font-weight:600;color:#475569;"><?php esc_html_e('Fail Canvas', 'wppoppop'); ?></label>
                            <input type="number" id="set-quiz-fail-canvas" name="set-quiz-fail-screen" value="3" class="widefat" style="font-size:12px;">
                        </div>
                    </div>
                    <label><input type="checkbox" id="set-quiz-confetti" value="1"> <?php esc_html_e('Trigger Particle Confetti on Pass', 'wppoppop'); ?></label>
                </div>
            </div>
