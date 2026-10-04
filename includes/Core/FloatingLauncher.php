<?php
namespace WPPopPop\Core;

use WPPopPop\Targeting\PopupPostType;

class FloatingLauncher {
    public static function init(): void {
        add_action('wp_footer', [__CLASS__, 'render_launchers']);
        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_assets']);
    }

    public static function enqueue_assets(): void {
        wp_enqueue_style(
            'wppoppop-launcher-css',
            WPPOPPOP_URL . 'assets/css/floating-launcher.css',
            [],
            WPPOPPOP_VERSION
        );
    }

    public static function render_launchers(): void {
        if (is_admin()) {
            return;
        }

        $popups = get_posts([
            'post_type'      => PopupPostType::POST_TYPE,
            'post_status'    => 'publish',
            'posts_per_page' => 20,
        ]);

        foreach ($popups as $p) {
            $enabled = get_post_meta($p->ID, '_wppoppop_tab_enabled', true) === '1';
            if (!$enabled) {
                continue;
            }

            $pos   = get_post_meta($p->ID, '_wppoppop_tab_position', true) ?: 'right-middle';
            $mode  = get_post_meta($p->ID, '_wppoppop_tab_mode', true) ?: 'after_close';
            $label = get_post_meta($p->ID, '_wppoppop_tab_label', true) ?: __('Special Offer', 'wppoppop');
            $bg    = get_post_meta($p->ID, '_wppoppop_tab_bg', true) ?: '#b5295c';
            $color = get_post_meta($p->ID, '_wppoppop_tab_color', true) ?: '#ffffff';
            $pulse = get_post_meta($p->ID, '_wppoppop_tab_pulse', true) === '1';

            $classes = ['wppoppop-tab-launcher', 'wppoppop-tab-' . sanitize_html_class($pos)];
            if ($pulse) {
                $classes[] = 'wppoppop-tab-pulse';
            }
            if ($mode === 'after_close') {
                $classes[] = 'wppoppop-tab-after-close';
            } else {
                $classes[] = 'wppoppop-tab-visible';
            }

            $style = sprintf('background-color:%s; color:%s;', esc_attr($bg), esc_attr($color));
            ?>
            <button type="button"
                    class="<?php echo esc_attr(implode(' ', $classes)); ?>"
                    style="<?php echo esc_attr($style); ?>"
                    data-popup-id="<?php echo esc_attr($p->ID); ?>"
                    data-tab-mode="<?php echo esc_attr($mode); ?>"
                    aria-label="<?php echo esc_attr($label); ?>">
                <span class="dashicons dashicons-tag wppoppop-tab-icon"></span>
                <span class="wppoppop-tab-text"><?php echo esc_html($label); ?></span>
            </button>
            <?php
        }
    }
}
