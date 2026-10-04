<?php
namespace WPPopPop\Core;

use WPPopPop\Admin\SettingsManager;

class WebFonts {
    public static function init(): void {
        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_google_fonts'], 5);
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_google_fonts'], 5);
    }

    public static function get_popular_fonts(): array {
        return [
            'System Default'   => '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
            'Roboto'           => 'Roboto, sans-serif',
            'Open Sans'        => '"Open Sans", sans-serif',
            'Montserrat'       => 'Montserrat, sans-serif',
            'Lato'             => 'Lato, sans-serif',
            'Poppins'          => 'Poppins, sans-serif',
            'Inter'            => 'Inter, sans-serif',
            'Playfair Display' => '"Playfair Display", serif',
            'Merriweather'     => 'Merriweather, serif',
            'Oswald'           => 'Oswald, sans-serif',
            'Raleway'          => 'Raleway, sans-serif',
        ];
    }

    public static function get_all_font_options(): array {
        $fonts = self::get_popular_fonts();
        $custom_fonts_raw = SettingsManager::get('custom_fonts', '');

        if (!empty($custom_fonts_raw)) {
            $lines = explode("\n", str_replace("\r", "", $custom_fonts_raw));
            foreach ($lines as $line) {
                $trimmed = trim($line);
                if (!empty($trimmed)) {
                    $fonts[$trimmed] = '"' . esc_attr($trimmed) . '", sans-serif';
                }
            }
        }

        return $fonts;
    }

    public static function enqueue_google_fonts(): void {
        $enabled = SettingsManager::get('google_fonts', 1);
        if (!$enabled) {
            return;
        }

        // Add preconnect resource hints for optimal font delivery
        add_action('wp_head', function () {
            echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
            echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
        }, 1);

        // Enqueue common Google Font families with variable weights
        $font_families = [
            'Roboto:wght@400;500;700',
            'Open+Sans:wght@400;600;700',
            'Montserrat:wght@400;600;700',
            'Poppins:wght@400;500;600;700',
            'Inter:wght@400;500;600;700',
            'Playfair+Display:wght@400;600;700',
        ];

        $query_url = 'https://fonts.googleapis.com/css2?family=' . implode('&family=', $font_families) . '&display=swap';
        wp_enqueue_style('wppoppop-google-fonts', $query_url, [], null);
    }
}
