<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Front_Element_Typography {

    public static function register() {
        WpPopPop_Front_Element_Registry::register('title', [__CLASS__, 'render_title']);
        WpPopPop_Front_Element_Registry::register('paragraph', [__CLASS__, 'render_paragraph']);
        WpPopPop_Front_Element_Registry::register('text', [__CLASS__, 'render_paragraph']);
    }

    public static function render_title(array $el, array $config = []) {
        $text = isset($el['titleText']) && $el['titleText'] !== '' ? $el['titleText'] : (isset($el['content']) ? $el['content'] : 'Headline');
        $tag = !empty($el['headingTag']) ? sanitize_key($el['headingTag']) : (!empty($el['tag']) ? sanitize_key($el['tag']) : 'h2');
        if (!in_array($tag, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div'], true)) {
            $tag = 'h2';
        }
        $transform = !empty($el['textTransform']) ? esc_attr($el['textTransform']) : 'none';
        $align = !empty($el['textAlign']) ? esc_attr($el['textAlign']) : 'left';
        $line_height = !empty($el['lineHeight']) ? esc_attr($el['lineHeight']) : 'inherit';
        $letter_spacing = isset($el['letterSpacing']) && $el['letterSpacing'] !== '' ? esc_attr($el['letterSpacing']) : 'inherit';

        return sprintf(
            '<%1$s class="wppoppop-front-title" style="margin:0;padding:0;width:100%%;height:100%%;font-size:inherit;font-weight:inherit;color:inherit;text-align:%2$s;text-transform:%3$s;line-height:%4$s;letter-spacing:%5$s;display:flex;align-items:center;">%6$s</%1$s>',
            $tag,
            $align,
            $transform,
            $line_height,
            $letter_spacing,
            wp_kses_post($text)
        );
    }

    public static function render_paragraph(array $el, array $config = []) {
        $text = isset($el['content']) ? $el['content'] : (isset($el['text']) ? $el['text'] : '');
        $tag = !empty($el['tag']) ? sanitize_key($el['tag']) : 'p';
        if (!in_array($tag, ['p', 'div', 'span'], true)) {
            $tag = 'p';
        }
        $line_height = !empty($el['lineHeight']) ? esc_attr($el['lineHeight']) : 'inherit';
        $letter_spacing = isset($el['letterSpacing']) && $el['letterSpacing'] !== '' ? esc_attr($el['letterSpacing']) : 'inherit';

        return sprintf(
            '<%1$s class="wppoppop-front-text" style="margin:0;padding:0;width:100%%;height:100%%;font-size:inherit;font-weight:inherit;color:inherit;text-align:inherit;line-height:%2$s;letter-spacing:%3$s;word-break:break-word;">%4$s</%1$s>',
            $tag,
            $line_height,
            $letter_spacing,
            wp_kses_post($text)
        );
    }
}
