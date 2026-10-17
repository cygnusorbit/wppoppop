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
        // WPPOPPOP_IMAGE_RENDER_V2
        $raw = '';
        foreach (array('imgUrl', 'imageUrl', 'src', 'content') as $k) {
            if (!empty($el[$k]) && is_string($el[$k])) { $raw = trim($el[$k]); break; }
        }
        $url = $raw !== '' ? esc_url($raw) : '';
        if (!$url) {
            return '';
        }

        $alt_raw = '';
        foreach (array('altText', 'alt', 'imageAlt') as $k) {
            if (isset($el[$k]) && is_string($el[$k]) && $el[$k] !== '') { $alt_raw = $el[$k]; break; }
        }
        $alt = esc_attr($alt_raw);

        $fit_raw = !empty($el['objectFit']) ? $el['objectFit'] : (!empty($el['imageFit']) ? $el['imageFit'] : 'cover');
        $fit = in_array($fit_raw, array('cover', 'contain', 'fill', 'none', 'scale-down'), true) ? $fit_raw : 'cover';

        $img = sprintf(
            '<img src="%1$s" alt="%2$s" loading="lazy" draggable="false" style="width:100%%;height:100%%;object-fit:%3$s;border:inherit;border-radius:inherit;display:block;">',
            $url,
            $alt,
            esc_attr($fit)
        );

        $link = !empty($el['linkUrl']) && is_string($el['linkUrl']) ? esc_url($el['linkUrl']) : '';
        if ($link) {
            $blank = !empty($el['targetBlank']) && $el['targetBlank'] !== 'false';
            $img = '<a href="' . $link . '"' . ($blank ? ' target="_blank" rel="noopener noreferrer"' : '') . ' style="display:block;width:100%;height:100%;">' . $img . '</a>';
        }
        return $img;
    }

    public static function render_video(array $el, array $config = []) {
        // WPPOPPOP_VIDEO_RENDER_V2
        $truthy = function ($keys, $default = false) use ($el) {
            foreach ($keys as $k) {
                if (array_key_exists($k, $el)) {
                    $v = $el[$k];
                    if ($v === false || $v === 'false' || $v === '0' || $v === 0 || $v === '' || $v === null) { return false; }
                    return true;
                }
            }
            return $default;
        };

        $raw_url = '';
        foreach (array('videoUrl', 'content', 'url', 'src') as $k) {
            if (!empty($el[$k]) && is_string($el[$k])) { $raw_url = trim($el[$k]); break; }
        }
        if ($raw_url === '') {
            return '';
        }

        $autoplay = $truthy(array('videoAutoplay', 'autoplay')) ? 1 : 0;
        $mute     = ($autoplay || $truthy(array('videoMute', 'mute', 'muted'))) ? 1 : 0;
        $loop     = $truthy(array('videoLoop', 'loop')) ? 1 : 0;
        $controls = $truthy(array('videoControls', 'controls'), true) ? 1 : 0;

        $frame_style = 'width:100%;height:100%;border:0;border-radius:inherit;display:block;';

        // YouTube
        if (preg_match('~(?:youtu\.be/|youtube(?:-nocookie)?\.com/(?:watch\?(?:[^#]*&)?v=|embed/|shorts/|v/))([A-Za-z0-9_-]{11})~', $raw_url, $m)) {
            $src = 'https://www.youtube.com/embed/' . $m[1] . '?enablejsapi=1&playsinline=1&rel=0'
                 . '&autoplay=' . $autoplay . '&mute=' . $mute . '&controls=' . $controls;
            if ($loop) { $src .= '&loop=1&playlist=' . $m[1]; }
            return '<iframe src="' . esc_url($src) . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen loading="lazy" style="' . esc_attr($frame_style) . '"></iframe>';
        }

        // Vimeo
        if (preg_match('~vimeo\.com/(?:video/|channels/[^/]+/|groups/[^/]+/videos/)?([0-9]+)~', $raw_url, $m)) {
            $src = 'https://player.vimeo.com/video/' . $m[1] . '?playsinline=1'
                 . '&autoplay=' . $autoplay . '&muted=' . $mute . '&loop=' . $loop . '&controls=' . $controls;
            return '<iframe src="' . esc_url($src) . '" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen loading="lazy" style="' . esc_attr($frame_style) . '"></iframe>';
        }

        // HTML5 / self-hosted
        $url = esc_url($raw_url);
        if (!$url) {
            return '';
        }
        $attrs  = ' playsinline webkit-playsinline preload="metadata"';
        $attrs .= $controls ? ' controls' : '';
        $attrs .= $autoplay ? ' autoplay' : '';
        $attrs .= $mute ? ' muted' : '';
        $attrs .= $loop ? ' loop' : '';
        return '<video src="' . $url . '"' . $attrs . ' style="width:100%;height:100%;object-fit:cover;border-radius:inherit;display:block;background:#000;"></video>';
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
            '<svg viewBox="0 0 100 100" style="width:100%%;height:100%%;display:block;overflow:visible;%1$s" fill="%2$s" stroke="%3$s" stroke-width="%4$d" vector-effect="non-scaling-stroke">%5$s</svg>',
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
                '<div style="width:100%%;height:100%%;display:flex;align-items:center;justify-content:center;box-sizing:border-box;"><div style="height:100%%;border-left:%1$dpx %2$s %3$s;"></div></div>',
                $thick,
                $style,
                $color
            );
        }

        return sprintf(
            '<div style="width:100%%;height:100%%;display:flex;align-items:center;justify-content:center;box-sizing:border-box;"><div style="width:100%%;border-top:%1$dpx %2$s %3$s;"></div></div>',
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
            '<div style="width:100%%;height:100%%;display:flex;align-items:center;justify-content:center;box-sizing:border-box;"><span class="dashicons %1$s" style="font-size:%2$dpx;width:%2$dpx;height:%2$dpx;line-height:1;display:flex;align-items:center;justify-content:center;color:%3$s;"></span></div>',
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
