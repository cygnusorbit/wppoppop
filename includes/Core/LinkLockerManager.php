<?php
namespace WPPopPop\Core;

use WPPopPop\Targeting\PopupPostType;

class LinkLockerManager {
    public static function init(): void {
        add_shortcode('wppoppop_lock', [__CLASS__, 'render_lock_shortcode']);
    }

    public static function render_lock_shortcode(array $atts = [], ?string $content = null): string {
        $atts = shortcode_atts([
            'id'     => 0,
            'url'    => '#',
            'target' => '_self',
            'class'  => 'wppoppop-locked-link-btn',
        ], $atts, 'wppoppop_lock');

        $popup_id = absint($atts['id']);
        $url      = esc_url($atts['url']);
        $target   = esc_attr($atts['target']);
        $class    = sanitize_html_class($atts['class']);
        $label    = do_shortcode($content ?: __('Unlock Protected Link', 'wppoppop'));

        if (!$popup_id) {
            return sprintf('<a href="%s" target="%s" class="%s">%s</a>', $url, $target, $class, $label);
        }

        return sprintf(
            '<a href="%s" target="%s" class="%s wppoppop-lock-trigger" data-wppoppop-lock="1" data-popup-id="%d" data-dest-url="%s" data-dest-target="%s">%s</a>',
            $url,
            $target,
            $class,
            $popup_id,
            $url,
            $target,
            $label
        );
    }
}
