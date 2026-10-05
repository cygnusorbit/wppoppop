<?php
namespace WPPopPop\Core;

use WPPopPop\Targeting\PopupPostType;

class MultiStepManager {
    public static function init(): void {
        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_assets']);
        add_filter('wppoppop_render_layers_output', [__CLASS__, 'wrap_multistep_content'], 10, 3);
        add_filter('wppoppop_process_lead_fields', [__CLASS__, 'record_multistep_answers'], 10, 2);
    }

    public static function enqueue_assets(): void {
        wp_enqueue_style(
            'wppoppop-multistep-css',
            WPPOPPOP_URL . 'assets/css/multistep.css',
            [],
            WPPOPPOP_VERSION
        );

        wp_enqueue_script(
            'wppoppop-multistep-js',
            WPPOPPOP_URL . 'assets/js/multistep.js',
            [],
            WPPOPPOP_VERSION,
            true
        );
    }

    public static function is_multistep(int $popup_id): bool {
        return get_post_meta($popup_id, '_wppoppop_multistep_enabled', true) === '1';
    }

    public static function wrap_multistep_content(string $content, int $popup_id, array $layers): string {
        if (!self::is_multistep($popup_id)) {
            return $content;
        }

        $step1_question = get_post_meta($popup_id, '_wppoppop_step1_question', true) ?: __('What is your primary goal?', 'wppoppop');
        $step1_choices_raw = get_post_meta($popup_id, '_wppoppop_step1_choices', true) ?: "Grow Website Traffic|2\nBoost Sales & Conversions|2\nBuild an Email List|2";
        $step3_headline = get_post_meta($popup_id, '_wppoppop_step3_headline', true) ?: __('Here is your exclusive coupon!', 'wppoppop');
        $step3_coupon   = get_post_meta($popup_id, '_wppoppop_step3_coupon', true) ?: 'WELCOME20';

        $choices = [];
        $lines = explode("\n", str_replace("\r", "", trim($step1_choices_raw)));
        foreach ($lines as $line) {
            $parts = explode('|', trim($line));
            $label = trim($parts[0] ?? '');
            $target = absint($parts[1] ?? 2);
            if (!empty($label)) {
                $choices[] = ['label' => $label, 'target' => $target];
            }
        }

        ob_start();
        ?>
        <div class="wppoppop-multistep-container" data-popup-id="<?php echo esc_attr($popup_id); ?>">
            <!-- Progress Tracker -->
            <div class="wppoppop-step-tracker">
                <div class="wppoppop-step-header">
                    <span class="wppoppop-step-text"><?php echo esc_html__('Step 1 of 3', 'wppoppop'); ?></span>
                    <span class="wppoppop-step-percent">33%</span>
                </div>
                <div class="wppoppop-progress-bar">
                    <div class="wppoppop-progress-fill" style="width: 33%;"></div>
                </div>
            </div>

            <!-- STEP 1: Micro-Commitment Choice -->
            <div class="wppoppop-step-pane wppoppop-step-active" data-step="1">
                <h3 class="wppoppop-funnel-question"><?php echo esc_html($step1_question); ?></h3>
                <div class="wppoppop-choice-grid">
                    <?php foreach ($choices as $c): ?>
                        <button type="button" class="wppoppop-choice-card" data-choice="<?php echo esc_attr($c['label']); ?>" data-next-step="<?php echo esc_attr($c['target']); ?>">
                            <span class="wppoppop-choice-icon">&#10003;</span>
                            <span class="wppoppop-choice-label"><?php echo esc_html($c['label']); ?></span>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- STEP 2: Lead Capture Form -->
            <div class="wppoppop-step-pane" data-step="2">
                <div class="wppoppop-step-inner-form">
                    <?php echo $content; ?>
                </div>
                <div class="wppoppop-step-footer">
                    <button type="button" class="wppoppop-step-nav-btn wppoppop-prev-btn" data-step-target="1">&larr; <?php echo esc_html__('Back', 'wppoppop'); ?></button>
                </div>
            </div>

            <!-- STEP 3: Reward / Voucher Delivery -->
            <div class="wppoppop-step-pane" data-step="3">
                <div class="wppoppop-step-reward-box">
                    <div class="wppoppop-reward-badge">&#127881; <?php echo esc_html__('Success!', 'wppoppop'); ?></div>
                    <h3 class="wppoppop-reward-headline"><?php echo esc_html($step3_headline); ?></h3>
                    <p style="color:#64748b; font-size:14px; margin-bottom:18px;">
                        <?php echo esc_html__('Use this promotional code during checkout:', 'wppoppop'); ?>
                    </p>
                    <div class="wppoppop-coupon-display">
                        <code class="wppoppop-code-text"><?php echo esc_html($step3_coupon); ?></code>
                    </div>
                </div>
            </div>

            <input type="hidden" name="fields[multistep_choice]" class="wppoppop-hidden-choice" value="" />
        </div>
        <?php
        return ob_get_clean();
    }

    public static function record_multistep_answers(array $fields, int $lead_id): array {
        if (!empty($_POST['fields']['multistep_choice'])) {
            $choice = sanitize_text_field($_POST['fields']['multistep_choice']);
            update_post_meta($lead_id, '_wppoppop_funnel_choice', $choice);
            $fields['funnel_choice'] = $choice;
        }
        return $fields;
    }
}
