<?php
namespace WPPopPop\Core;

use WPPopPop\Targeting\PopupPostType;

class ContentLocker {
    public static function init(): void {
        add_shortcode('wppoppop_locker', [__CLASS__, 'render_locker_shortcode']);
    }

    public static function is_unlocked(int $popup_id): bool {
        if (current_user_can('manage_options')) {
            return true; // Administrators bypass lockers
        }

        $cookie_key = 'wppoppop_unlocked_' . $popup_id;
        if (isset($_COOKIE[$cookie_key]) && !empty($_COOKIE[$cookie_key])) {
            return true;
        }

        if (is_user_logged_in()) {
            $unlocked = get_user_meta(get_current_user_id(), '_wppoppop_unlocked_popups', true) ?: [];
            if (in_array($popup_id, (array)$unlocked, true)) {
                return true;
            }
        }

        return false;
    }

    public static function render_locker_shortcode(array $atts = [], ?string $content = null): string {
        $atts = shortcode_atts([
            'id'    => 0,
            'blur'  => '6px',
            'title' => __('Content Locked', 'wppoppop'),
        ], $atts, 'wppoppop_locker');

        $popup_id = absint($atts['id']);
        if (!$popup_id || empty($content)) {
            return do_shortcode($content ?: '');
        }

        $popup = get_post($popup_id);
        if (!$popup || $popup->post_type !== PopupPostType::POST_TYPE || $popup->post_status !== 'publish') {
            return do_shortcode($content);
        }

        // If visitor has already unlocked this locker, reveal content directly
        if (self::is_unlocked($popup_id)) {
            return '<div class="wppoppop-unlocked-block">' . do_shortcode($content) . '</div>';
        }

        $blur_amount = esc_attr($atts['blur']);
        $form_html   = LayerRenderer::render_layers($popup_id, '');

        ob_start();
        ?>
        <div class="wppoppop-inline-locker" data-locker-id="<?php echo esc_attr($popup_id); ?>">
            <div class="wppoppop-locked-preview" style="filter: blur(<?php echo $blur_amount; ?>); user-select: none; pointer-events: none;">
                <?php echo do_shortcode($content); ?>
            </div>
            <div class="wppoppop-locker-overlay">
                <div class="wppoppop-locker-card">
                    <div class="wppoppop-locker-badge"><span class="dashicons dashicons-lock"></span> <?php echo esc_html($atts['title']); ?></div>
                    <div class="wppoppop-locker-form-container">
                        <?php echo $form_html; ?>
                    </div>
                </div>
            </div>
            <div class="wppoppop-revealed-content" style="display:none;">
                <?php echo do_shortcode($content); ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
