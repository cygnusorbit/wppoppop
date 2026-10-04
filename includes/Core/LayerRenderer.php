<?php
namespace WPPopPop\Core;

class LayerRenderer {
    public static function render_layers(int $popup_id, string $default_content): string {
        $badge_text       = get_post_meta($popup_id, '_wppoppop_layer_badge', true) ?: '';
        $headline         = get_the_title($popup_id);
        $cta_text         = get_post_meta($popup_id, '_wppoppop_layer_cta_text', true) ?: 'Subscribe Now';
        $multistep        = get_post_meta($popup_id, '_wppoppop_multistep_enabled', true) === '1';
        $step1_question   = get_post_meta($popup_id, '_wppoppop_step1_question', true) ?: 'Would you like an exclusive 20% discount today?';
        $step1_opt1       = get_post_meta($popup_id, '_wppoppop_step1_opt1', true) ?: 'Yes, I want the discount!';
        $step1_opt2       = get_post_meta($popup_id, '_wppoppop_step1_opt2', true) ?: 'No thanks, I prefer paying full price';
        $step3_coupon     = get_post_meta($popup_id, '_wppoppop_step3_coupon', true) ?: 'SAVE20NOW';

        $font_family      = get_post_meta($popup_id, '_wppoppop_font_family', true) ?: '';
        $font_weight      = get_post_meta($popup_id, '_wppoppop_font_weight', true) ?: '';
        $heading_size     = get_post_meta($popup_id, '_wppoppop_heading_size', true) ?: '';
        $body_size        = get_post_meta($popup_id, '_wppoppop_body_size', true) ?: '';

        $css_vars = [];
        if (!empty($font_family) && $font_family !== 'default') {
            $css_vars[] = "--wppoppop-font: '" . esc_attr($font_family) . "', sans-serif;";
        }
        if (!empty($font_weight)) {
            $css_vars[] = "--wppoppop-font-weight: " . esc_attr($font_weight) . ";";
        }
        if (!empty($heading_size)) {
            $css_vars[] = "--wppoppop-h-size: " . absint($heading_size) . "px;";
        }
        if (!empty($body_size)) {
            $css_vars[] = "--wppoppop-b-size: " . absint($body_size) . "px;";
        }
        $inline_style = !empty($css_vars) ? 'style="' . implode(' ', $css_vars) . '"' : '';

        ob_start();
        ?>
        <div class="wppoppop-layer-wrapper <?php echo $multistep ? 'wppoppop-multistep-active' : ''; ?>" <?php echo $inline_style; ?>>
            <?php if (!empty($badge_text)): ?>
                <div class="wppoppop-layer wppoppop-layer-badge">
                    <span><?php echo esc_html($badge_text); ?></span>
                </div>
            <?php endif; ?>

            <?php if ($multistep): ?>
                <div class="wppoppop-progress-track">
                    <div class="wppoppop-progress-bar" id="wppoppop-progress-bar" style="width: 33%;"></div>
                </div>

                <div class="wppoppop-step wppoppop-step-1 wppoppop-step-active" data-step="1">
                    <div class="wppoppop-layer wppoppop-layer-header">
                        <h3><?php echo esc_html($step1_question); ?></h3>
                    </div>
                    <div class="wppoppop-step-choices">
                        <button type="button" class="wppoppop-btn wppoppop-choice-btn" data-step-next="2" data-choice="positive">
                            <?php echo esc_html($step1_opt1); ?>
                        </button>
                        <button type="button" class="wppoppop-choice-dismiss" id="wppoppop-choice-decline">
                            <?php echo esc_html($step1_opt2); ?>
                        </button>
                    </div>
                </div>

                <div class="wppoppop-step wppoppop-step-2" data-step="2">
                    <div class="wppoppop-layer wppoppop-layer-header">
                        <h3><?php echo esc_html($headline); ?></h3>
                    </div>
                    <div class="wppoppop-layer wppoppop-layer-body">
                        <?php echo wp_kses_post($default_content); ?>
                    </div>
                    <form id="wppoppop-form" class="wppoppop-form" novalidate>
                        <input type="hidden" name="popup_id" value="<?php echo esc_attr($popup_id); ?>">
                        <input type="hidden" name="step1_choice" id="wppoppop-step1-choice" value="">
                        <div class="wppoppop-form-group">
                            <label for="wppoppop-name"><?php esc_html_e('Your Name', 'wppoppop'); ?></label>
                            <input type="text" id="wppoppop-name" name="name" class="wppoppop-input" placeholder="e.g. Jane Doe" required>
                        </div>
                        <div class="wppoppop-form-group">
                            <label for="wppoppop-email"><?php esc_html_e('Email Address', 'wppoppop'); ?></label>
                            <input type="email" id="wppoppop-email" name="email" class="wppoppop-input" placeholder="jane@example.com" required>
                        </div>
                        <div class="wppoppop-form-gdpr">
                            <label>
                                <input type="checkbox" name="gdpr" id="wppoppop-gdpr" value="1" required>
                                <span><?php esc_html_e('I accept the privacy terms and consent to receive emails.', 'wppoppop'); ?></span>
                            </label>
                        </div>
                        <div class="wppoppop-feedback" id="wppoppop-feedback" style="display:none;" aria-live="polite"></div>
                        <button type="submit" id="wppoppop-submit-btn" class="wppoppop-btn">
                            <span class="wppoppop-btn-text"><?php echo esc_html($cta_text); ?></span>
                        </button>
                    </form>
                </div>

                <div class="wppoppop-step wppoppop-step-3" data-step="3">
                    <div class="wppoppop-layer wppoppop-layer-header">
                        <h3><?php esc_html_e('Congratulations!', 'wppoppop'); ?></h3>
                    </div>
                    <div class="wppoppop-layer wppoppop-layer-body">
                        <p><?php esc_html_e('Your subscription is complete. Here is your promo code:', 'wppoppop'); ?></p>
                        <div class="wppoppop-coupon-box">
                            <code><?php echo esc_html($step3_coupon); ?></code>
                        </div>
                        <p class="description" style="margin-top:10px;"><?php esc_html_e('Copy this code and apply it during checkout.', 'wppoppop'); ?></p>
                    </div>
                    <button type="button" class="wppoppop-btn" id="wppoppop-step3-finish">
                        <?php esc_html_e('Start Shopping', 'wppoppop'); ?>
                    </button>
                </div>

            <?php else: ?>
                <div class="wppoppop-layer wppoppop-layer-header">
                    <h3><?php echo esc_html($headline); ?></h3>
                </div>
                <div class="wppoppop-layer wppoppop-layer-body">
                    <?php echo wp_kses_post($default_content); ?>
                </div>
                <form id="wppoppop-form" class="wppoppop-form" novalidate>
                    <input type="hidden" name="popup_id" value="<?php echo esc_attr($popup_id); ?>">
                    <div class="wppoppop-form-group">
                        <label for="wppoppop-name"><?php esc_html_e('Your Name', 'wppoppop'); ?></label>
                        <input type="text" id="wppoppop-name" name="name" class="wppoppop-input" placeholder="e.g. Jane Doe" required>
                    </div>
                    <div class="wppoppop-form-group">
                        <label for="wppoppop-email"><?php esc_html_e('Email Address', 'wppoppop'); ?></label>
                        <input type="email" id="wppoppop-email" name="email" class="wppoppop-input" placeholder="jane@example.com" required>
                    </div>
                    <div class="wppoppop-form-gdpr">
                        <label>
                            <input type="checkbox" name="gdpr" id="wppoppop-gdpr" value="1" required>
                            <span><?php esc_html_e('I accept the privacy terms and consent to receive emails.', 'wppoppop'); ?></span>
                        </label>
                    </div>
                    <div class="wppoppop-feedback" id="wppoppop-feedback" style="display:none;" aria-live="polite"></div>
                    <button type="submit" id="wppoppop-submit-btn" class="wppoppop-btn">
                        <span class="wppoppop-btn-text"><?php echo esc_html($cta_text); ?></span>
                    </button>
                </form>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
