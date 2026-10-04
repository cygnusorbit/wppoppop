<?php
namespace WPPopPop\Core;

class WebFonts {
    public static function get_fonts(): array {
        return [
            'default'          => __('Default / Inherit System Font', 'wppoppop'),
            'Montserrat'       => __('Montserrat (Geometric Sans)', 'wppoppop'),
            'Roboto'           => __('Roboto (Modern Clean Sans)', 'wppoppop'),
            'Open Sans'        => __('Open Sans (Neutral & Readable)', 'wppoppop'),
            'Poppins'          => __('Poppins (Contemporary Geometric)', 'wppoppop'),
            'Playfair Display' => __('Playfair Display (Editorial Serif)', 'wppoppop'),
            'Oswald'           => __('Oswald (Condensed Impact)', 'wppoppop'),
            'Lato'             => __('Lato (Warm Humanist Sans)', 'wppoppop'),
            'Merriweather'     => __('Merriweather (Classic Serif)', 'wppoppop'),
            'Inter'            => __('Inter (Precision UI Sans)', 'wppoppop'),
            'Space Grotesk'    => __('Space Grotesk (Tech Modern)', 'wppoppop'),
        ];
    }

    public static function init(): void {
        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_active_fonts']);
        add_filter('wp_resource_hints', [__CLASS__, 'resource_hints'], 10, 2);
    }

    public static function resource_hints(array $urls, string $relation_type): array {
        if ($relation_type === 'preconnect') {
            $urls[] = [
                'href' => 'https://fonts.googleapis.com',
                'crossorigin' => 'anonymous',
            ];
            $urls[] = [
                'href' => 'https://fonts.gstatic.com',
                'crossorigin' => 'anonymous',
            ];
        }
        return $urls;
    }

    public static function enqueue_active_fonts(): void {
        $popups = get_posts([
            'post_type'      => 'wppoppop',
            'post_status'    => 'publish',
            'posts_per_page' => 20,
        ]);

        $enqueued = [];
        foreach ($popups as $popup) {
            $font = get_post_meta($popup->ID, '_wppoppop_font_family', true);
            if (!empty($font) && $font !== 'default' && !isset($enqueued[$font])) {
                $enqueued[$font] = true;
                $family_param = str_replace(' ', '+', $font) . ':wght@400;500;600;700';
                $url = 'https://fonts.googleapis.com/css2?family=' . $family_param . '&display=swap';
                wp_enqueue_style(
                    'wppoppop-font-' . sanitize_title($font),
                    $url,
                    [],
                    null
                );
            }
        }
    }
}
