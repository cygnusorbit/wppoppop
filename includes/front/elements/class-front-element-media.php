<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Front_Element_Media {

    private static $preset_svgs = [
        'circle'       => '<circle cx="50" cy="50" r="44"/>',
        'square'       => '<rect x="6" y="6" width="88" height="88"/>',
        'rounded_rect' => '<rect x="6" y="6" width="88" height="88" rx="16" ry="16"/>',
        'star'         => '<polygon points="50,6 63,36 96,36 69,56 80,88 50,68 20,88 31,56 4,36 37,36"/>',
        'triangle'     => '<polygon points="50,8 94,88 6,88"/>',
        'diamond'      => '<polygon points="50,6 94,50 50,94 6,50"/>',
        'heart'        => '<path d="M50 84 C20 60 6 42 6 26 A 18 18 0 0 1 44 16 L 50 22 L 56 16 A 18 18 0 0 1 94 26 C 94 42 80 60 50 84 Z"/>',
        'hexagon'      => '<polygon points="50,6 90,28 90,72 50,94 10,72 10,28"/>',
        'octagon'      => '<polygon points="32,6 68,6 94,32 94,68 68,94 32,94 6,68 6,32"/>',
        'shield'       => '<path d="M50 6 L90 20 L90 54 C90 74 50 94 50 94 C50 94 10 74 10 54 L10 20 Z"/>',
        'cross'        => '<polygon points="36,6 64,6 64,36 94,36 94,64 64,64 64,94 36,94 36,64 6,64 6,36 36,36"/>'
    ];

    public static function register() {
        WpPopPop_Front_Element_Registry::register('image', [__CLASS__, 'render_image']);
        WpPopPop_Front_Element_Registry::register('video', [__CLASS__, 'render_video']);
        WpPopPop_Front_Element_Registry::register('shape', [__CLASS__, 'render_shape']);
        WpPopPop_Front_Element_Registry::register('divider', [__CLASS__, 'render_divider']);
        WpPopPop_Front_Element_Registry::register('icon', [__CLASS__, 'render_icon']);
        WpPopPop_Front_Element_Registry::register('html', [__CLASS__, 'render_html']);
    }

    public static function render_image(array $el, array $config = []) {
        $url = !empty($el['imgUrl']) ? esc_url($el['imgUrl']) : (!empty($el['content']) ? esc_url($el['content']) : '');
        $alt = !empty($el['altText']) ? esc_attr($el['altText']) : 'Popup Image';
        $fit = !empty($el['objectFit']) ? esc_attr($el['objectFit']) : 'cover';

        if (!$url) {
            return '';
        }

        return sprintf(
            '<img src="%1$s" alt="%2$s" style="width:100%;height:100%;object-fit:%3$s;border:inherit;border-radius:inherit;display:block;">',
            $url,
            $alt,
            $fit
        );
    }

    public static function render_video(array $el, array $config = []) {
        $raw_url = !empty($el['videoUrl']) ? trim($el['videoUrl']) : (!empty($el['content']) ? trim($el['content']) : '');
        $autoplay = !empty($el['videoAutoplay']) ? 1 : 0;
        $mute = !empty($el['videoMute']) || $autoplay ? 1 : 0;
        $loop = !empty($el['videoLoop']) ? 1 : 0;

        if (empty($raw_url)) {
            return '';
        }

        // YouTube Embed
        if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/))([a-zA-Z0-9_-]+)/', $raw_url, $m)) {
            $embed_src = 'https://www.youtube.com/embed/' . $m[1] . '?enablejsapi=1';
            if ($autoplay) $embed_src .= '&autoplay=1&mute=1';
            if ($loop) $embed_src .= '&loop=1&playlist=' . $m[1];

            return sprintf(
                '<iframe src="%1$s" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="width:100%;height:100%;border:inherit;border-radius:inherit;display:block;"></iframe>',
                esc_url($embed_src)
            );
        }

        // Vimeo Embed
        if (preg_match('/vimeo\.com\/(?:video\/)?([0-9]+)/', $raw_url, $m)) {
            $embed_src = 'https://player.vimeo.com/video/' . $m[1] . '?';
            if ($autoplay) $embed_src .= '&autoplay=1&muted=1';
            if ($loop) $embed_src .= '&loop=1';

            return sprintf(
                '<iframe src="%1$s" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen style="width:100%;height:100%;border:inherit;border-radius:inherit;display:block;"></iframe>',
                esc_url($embed_src)
            );
        }

        // HTML5 Self-Hosted Video
        $auto_attr = $autoplay ? 'autoplay muted' : '';
        $loop_attr = $loop ? 'loop' : '';
        return sprintf(
            '<video src="%1$s" %2$s %3$s controls style="width:100%;height:100%;object-fit:cover;border:inherit;border-radius:inherit;display:block;"></video>',
            esc_url($raw_url),
            $auto_attr,
            $loop_attr
        );
    }

    public static function render_shape(array $el, array $config = []) {
        $preset = !empty($el['shapePreset']) ? sanitize_key($el['shapePreset']) : 'circle';
        $geom = isset(self::$preset_svgs[$preset]) ? self::$preset_svgs[$preset] : self::$preset_svgs['circle'];
        $fill = !empty($el['shapeFill']) ? esc_attr($el['shapeFill']) : '#3b82f6';
        $stroke = !empty($el['shapeStroke']) ? esc_attr($el['shapeStroke']) : '#1d4ed8';
        $stroke_w = isset($el['shapeStrokeWidth']) ? intval($el['shapeStrokeWidth']) : 2;
        $rot = !empty($el['shapeRotate']) ? intval($el['shapeRotate']) : 0;
        $rot_style = $rot ? 'transform:rotate(' . $rot . 'deg);transform-origin:center;' : '';

        return sprintf(
            '<svg viewBox="0 0 100 100" style="width:100%;height:100%;display:block;overflow:visible;%1$s" fill="%2$s" stroke="%3$s" stroke-width="%4$d" vector-effect="non-scaling-stroke">%5$s</svg>',
            $rot_style,
            $fill,
            $stroke,
            $stroke_w,
            $geom
        );
    }

    public static function render_divider(array $el, array $config = []) {
        $style = !empty($el['dividerStyle']) ? esc_attr($el['dividerStyle']) : 'solid';
        $color = !empty($el['dividerColor']) ? esc_attr($el['dividerColor']) : '#cbd5e1';
        $thick = isset($el['dividerWeight']) ? intval($el['dividerWeight']) : (isset($el['dividerThickness']) ? intval($el['dividerThickness']) : 2);
        $is_vert = (!empty($el['dividerOrientation']) && $el['dividerOrientation'] === 'vertical');

        if ($is_vert) {
            return sprintf(
                '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;box-sizing:border-box;"><div style="height:100%%;border-left:%1$dpx %2$s %3$s;"></div></div>',
                $thick,
                $style,
                $color
            );
        }

        return sprintf(
            '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;box-sizing:border-box;"><div style="width:100%%;border-top:%1$dpx %2$s %3$s;"></div></div>',
            $thick,
            $style,
            $color
        );
    }

    public static function render_icon(array $el, array $config = []) {
        $glyph = !empty($el['iconGlyph']) ? sanitize_html_class($el['iconGlyph']) : 'dashicons-star-filled';
        $size = !empty($el['iconSize']) ? intval($el['iconSize']) : 32;
        $color = !empty($el['iconColor']) ? esc_attr($el['iconColor']) : '#3b82f6';

        return sprintf(
            '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;box-sizing:border-box;"><span class="dashicons %1$s" style="font-size:%2$dpx;width:%2$dpx;height:%2$dpx;line-height:1;display:flex;align-items:center;justify-content:center;color:%3$s;"></span></div>',
            $glyph,
            $size,
            $color
        );
    }

    public static function render_html(array $el, array $config = []) {
        $raw = isset($el['content']) ? $el['content'] : '';
        return '<div class="wppoppop-front-raw-html" style="width:100%;height:100%;overflow:auto;">' . wp_kses_post($raw) . '</div>';
    }
}
