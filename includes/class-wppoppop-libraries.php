<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * WpPopPop Component & Typography Libraries Manager
 * Manages conditional asset enqueuing for typography, icons, and frontend interactive libraries.
 */
class WpPopPop_Libraries {

    /**
     * Catalog of third-party component extensions and assets.
     *
     * @return array
     */
    public static function get_library_definitions() {
        return [
            'font_awesome' => [
                'name'    => 'Font Awesome 6',
                'type'    => 'style',
                'handle'  => 'wppoppop-font-awesome',
                'src'     => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
                'deps'    => [],
                'ver'     => '6.5.1',
                'setting' => 'font_awesome',
            ],
            'canvas_confetti' => [
                'name'      => 'Canvas Confetti',
                'type'      => 'script',
                'handle'    => 'wppoppop-canvas-confetti',
                'src'       => 'https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.2/dist/confetti.browser.min.js',
                'deps'      => [],
                'ver'       => '1.9.2',
                'in_footer' => true,
                'setting'   => 'load_canvas_confetti',
            ],
            'canvas_fireworks' => [
                'name'      => 'Canvas Fireworks',
                'type'      => 'script',
                'handle'    => 'wppoppop-canvas-fireworks',
                'src'       => 'https://cdn.jsdelivr.net/npm/fireworks-js@2.10.7/dist/index.umd.js',
                'deps'      => [],
                'ver'       => '2.10.7',
                'in_footer' => true,
                'setting'   => 'load_canvas_fireworks',
            ],
            'air_datepicker_css' => [
                'name'    => 'Air Datepicker CSS',
                'type'    => 'style',
                'handle'  => 'wppoppop-air-datepicker-css',
                'src'     => 'https://cdn.jsdelivr.net/npm/air-datepicker@3.5.0/air-datepicker.min.css',
                'deps'    => [],
                'ver'     => '3.5.0',
                'setting' => 'load_air_datepicker',
            ],
            'air_datepicker_js' => [
                'name'      => 'Air Datepicker JS',
                'type'      => 'script',
                'handle'    => 'wppoppop-air-datepicker-js',
                'src'       => 'https://cdn.jsdelivr.net/npm/air-datepicker@3.5.0/air-datepicker.min.js',
                'deps'      => [],
                'ver'       => '3.5.0',
                'in_footer' => true,
                'setting'   => 'load_air_datepicker',
            ],
            'jquery_mask' => [
                'name'      => 'jQuery Mask Plugin',
                'type'      => 'script',
                'handle'    => 'wppoppop-jquery-mask',
                'src'       => 'https://cdn.jsdelivr.net/npm/jquery-mask-plugin@1.14.16/dist/jquery.mask.min.js',
                'deps'      => ['jquery'],
                'ver'       => '1.14.16',
                'in_footer' => true,
                'setting'   => 'load_jquery_mask',
            ],
            'signature_pad' => [
                'name'      => 'Signature Pad',
                'type'      => 'script',
                'handle'    => 'wppoppop-signature-pad',
                'src'       => 'https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js',
                'deps'      => [],
                'ver'       => '4.1.7',
                'in_footer' => true,
                'setting'   => 'load_signature_pad',
            ],
            'range_slider_css' => [
                'name'    => 'Ion Range Slider CSS',
                'type'    => 'style',
                'handle'  => 'wppoppop-range-slider-css',
                'src'     => 'https://cdn.jsdelivr.net/npm/ion-rangeslider@2.3.1/css/ion.rangeSlider.min.css',
                'deps'    => [],
                'ver'     => '2.3.1',
                'setting' => 'load_range_slider',
            ],
            'range_slider_js' => [
                'name'      => 'Ion Range Slider JS',
                'type'      => 'script',
                'handle'    => 'wppoppop-range-slider-js',
                'src'       => 'https://cdn.jsdelivr.net/npm/ion-rangeslider@2.3.1/js/ion.rangeSlider.min.js',
                'deps'      => ['jquery'],
                'ver'       => '2.3.1',
                'in_footer' => true,
                'setting'   => 'load_range_slider',
            ],
            'math_parser' => [
                'name'      => 'Math Formula Parser',
                'type'      => 'script',
                'handle'    => 'wppoppop-math-parser',
                'src'       => 'https://cdn.jsdelivr.net/npm/expr-eval@2.0.2/dist/bundle.min.js',
                'deps'      => [],
                'ver'       => '2.0.2',
                'in_footer' => true,
                'setting'   => 'load_math_parser',
            ],
        ];
    }

    /**
     * Check if a library feature is active in settings.
     *
     * @param string $key
     * @return bool
     */
    public static function is_library_enabled($key) {
        $settings = get_option('wppoppop_settings', []);
        if (!is_array($settings)) {
            $settings = [];
        }

        switch ($key) {
            case 'font_awesome':
                return !empty($settings['font_awesome']);
            case 'google_fonts':
                return empty($settings['disable_google_fonts']) && !empty($settings['google_fonts']);
            case 'canvas_confetti':
                return !empty($settings['load_canvas_confetti']);
            case 'canvas_fireworks':
                return !empty($settings['load_canvas_fireworks']);
            case 'air_datepicker':
            case 'load_air_datepicker':
                return !empty($settings['load_air_datepicker']) || !empty($settings['air_datepicker']);
            case 'jquery_mask':
            case 'load_jquery_mask':
                return !empty($settings['load_jquery_mask']) || !empty($settings['jquery_mask']);
            case 'signature_pad':
            case 'load_signature_pad':
                return !empty($settings['load_signature_pad']) || !empty($settings['signature_pad']);
            case 'range_slider':
            case 'load_range_slider':
                return !empty($settings['load_range_slider']) || !empty($settings['range_slider']);
            case 'math_parser':
            case 'load_math_parser':
            case 'js_parser':
                return !empty($settings['load_math_parser']) || !empty($settings['js_parser']);
            default:
                return !empty($settings[$key]);
        }
    }

    /**
     * Enqueue active component extension scripts & styles for public frontend.
     */
    public static function enqueue_frontend_libraries() {
        $settings = get_option('wppoppop_settings', []);
        if (!is_array($settings)) {
            $settings = [];
        }

        // 1. Font Awesome 6 Icons
        if (!empty($settings['font_awesome'])) {
            wp_enqueue_style(
                'wppoppop-font-awesome',
                'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
                [],
                '6.5.1'
            );
        }

        // 2. Canvas Confetti
        if (!empty($settings['load_canvas_confetti'])) {
            wp_enqueue_script(
                'wppoppop-canvas-confetti',
                'https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.2/dist/confetti.browser.min.js',
                [],
                '1.9.2',
                true
            );
        }

        // 3. Canvas Fireworks
        if (!empty($settings['load_canvas_fireworks'])) {
            wp_enqueue_script(
                'wppoppop-canvas-fireworks',
                'https://cdn.jsdelivr.net/npm/fireworks-js@2.10.7/dist/index.umd.js',
                [],
                '2.10.7',
                true
            );
        }

        // 4. Air Datepicker (CSS & JS)
        if (!empty($settings['load_air_datepicker']) || !empty($settings['air_datepicker'])) {
            wp_enqueue_style(
                'wppoppop-air-datepicker-css',
                'https://cdn.jsdelivr.net/npm/air-datepicker@3.5.0/air-datepicker.min.css',
                [],
                '3.5.0'
            );
            wp_enqueue_script(
                'wppoppop-air-datepicker-js',
                'https://cdn.jsdelivr.net/npm/air-datepicker@3.5.0/air-datepicker.min.js',
                [],
                '3.5.0',
                true
            );
        }

        // 5. jQuery Mask Plugin
        if (!empty($settings['load_jquery_mask']) || !empty($settings['jquery_mask'])) {
            wp_enqueue_script(
                'wppoppop-jquery-mask',
                'https://cdn.jsdelivr.net/npm/jquery-mask-plugin@1.14.16/dist/jquery.mask.min.js',
                ['jquery'],
                '1.14.16',
                true
            );
        }

        // 6. Digital Signature Pad
        if (!empty($settings['load_signature_pad']) || !empty($settings['signature_pad'])) {
            wp_enqueue_script(
                'wppoppop-signature-pad',
                'https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js',
                [],
                '4.1.7',
                true
            );
        }

        // 7. Ion Range Slider (CSS & JS)
        if (!empty($settings['load_range_slider']) || !empty($settings['range_slider'])) {
            wp_enqueue_style(
                'wppoppop-range-slider-css',
                'https://cdn.jsdelivr.net/npm/ion-rangeslider@2.3.1/css/ion.rangeSlider.min.css',
                [],
                '2.3.1'
            );
            wp_enqueue_script(
                'wppoppop-range-slider-js',
                'https://cdn.jsdelivr.net/npm/ion-rangeslider@2.3.1/js/ion.rangeSlider.min.js',
                ['jquery'],
                '2.3.1',
                true
            );
        }

        // 8. Math Formula Parser (Expr-Eval)
        if (!empty($settings['load_math_parser']) || !empty($settings['js_parser'])) {
            wp_enqueue_script(
                'wppoppop-math-parser',
                'https://cdn.jsdelivr.net/npm/expr-eval@2.0.2/dist/bundle.min.js',
                [],
                '2.0.2',
                true
            );
        }
    }

    /**
     * Enqueue typography & icon assets in the Visual Builder for WYSIWYG accuracy.
     */
    public static function enqueue_builder_libraries() {
        $settings = get_option('wppoppop_settings', []);
        if (!is_array($settings)) {
            $settings = [];
        }

        if (!empty($settings['font_awesome'])) {
            wp_enqueue_style(
                'wppoppop-font-awesome',
                'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
                [],
                '6.5.1'
            );
        }

        if (empty($settings['disable_google_fonts']) && !empty($settings['google_fonts'])) {
            $default_font = !empty($settings['default_font']) ? $settings['default_font'] : '';
            $font_url = self::get_google_font_url($default_font);
            if ($font_url) {
                wp_enqueue_style('wppoppop-google-fonts', $font_url, [], null);
            }
        }
    }

    /**
     * Output Google Fonts preconnect, stylesheet, and custom @font-face rules in <head>.
     */
    public static function print_font_head_tags() {
        $settings = get_option('wppoppop_settings', []);
        if (!is_array($settings)) {
            $settings = [];
        }

        $disable_google_fonts = !empty($settings['disable_google_fonts']);
        $google_fonts_enabled = !empty($settings['google_fonts']);
        $default_font         = !empty($settings['default_font']) ? $settings['default_font'] : 'inherit';

        if (!$disable_google_fonts && ($google_fonts_enabled || ($default_font !== 'inherit' && !empty($default_font)))) {
            $font_url = self::get_google_font_url($default_font);
            if ($font_url) {
                echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "
";
                echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "
";
                echo '<link rel="stylesheet" href="' . esc_url($font_url) . '">' . "
";
            }
        }

        $custom_fonts_css = self::generate_custom_fonts_css();
        if (!empty($custom_fonts_css)) {
            echo '<style id="wppoppop-custom-fonts">' . "
" . $custom_fonts_css . "
" . '</style>' . "
";
        }
    }

    /**
     * Resolve Google Fonts v2 API stylesheet URL.
     *
     * @param string $font_family
     * @return string
     */
    public static function get_google_font_url($font_family = '') {
        $font_map = [
            'Inter, sans-serif'      => 'Inter:wght@300;400;500;600;700',
            'Roboto, sans-serif'     => 'Roboto:wght@300;400;500;700',
            'Montserrat, sans-serif' => 'Montserrat:wght@400;500;600;700',
            'Poppins, sans-serif'    => 'Poppins:wght@300;400;500;600;700',
            'Open Sans, sans-serif'  => 'Open+Sans:wght@400;600;700',
            'Lato, sans-serif'       => 'Lato:wght@300;400;700',
        ];

        if (isset($font_map[$font_family])) {
            $family_query = $font_map[$font_family];
        } else {
            $family_query = 'Inter:wght@400;600;700&family=Roboto:wght@400;500;700&family=Poppins:wght@400;600;700';
        }

        return 'https://fonts.googleapis.com/css2?family=' . $family_query . '&display=swap';
    }

    /**
     * Parse and compile custom font declarations into CSS @font-face blocks.
     *
     * @return string
     */
    public static function generate_custom_fonts_css() {
        $raw = function_exists('wppoppop_get_setting') ? wppoppop_get_setting('custom_fonts', '') : '';
        if (empty(trim($raw))) {
            $settings = get_option('wppoppop_settings', []);
            $raw = !empty($settings['custom_fonts']) ? $settings['custom_fonts'] : '';
        }

        if (empty(trim($raw))) {
            return '';
        }

        if (strpos($raw, '@font-face') !== false) {
            return wp_strip_all_tags($raw);
        }

        $css = '';
        $lines = explode("
", str_replace("", '', $raw));
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }
            if (strpos($line, ':') !== false) {
                $parts = explode(':', $line, 2);
                $font_name = trim(sanitize_text_field($parts[0]));
                $font_src  = isset($parts[1]) ? trim($parts[1]) : '';
                if (!empty($font_name) && !empty($font_src)) {
                    $css .= "@font-face {\n  font-family: '" . esc_attr($font_name) . "';\n  src: " . esc_attr($font_src) . ";\n  font-display: swap;\n}\n";
                }
            }
        }
        return $css;
    }

    /**
     * Extract unique custom font family names for the Visual Builder font dropdown.
     *
     * @return array
     */
    public static function get_custom_font_families() {
        $raw = function_exists('wppoppop_get_setting') ? wppoppop_get_setting('custom_fonts', '') : '';
        if (empty(trim($raw))) {
            $settings = get_option('wppoppop_settings', []);
            $raw = !empty($settings['custom_fonts']) ? $settings['custom_fonts'] : '';
        }

        $names = [];
        if (empty(trim($raw))) {
            return $names;
        }

        $lines = explode("
", str_replace("", '', $raw));
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }
            if (preg_match("/font-family\s*:\s*['\"]?([^'\";]+)/i", $line, $matches)) {
                $names[] = trim($matches[1]);
            } elseif (strpos($line, ':') !== false) {
                $parts = explode(':', $line, 2);
                $names[] = trim(sanitize_text_field($parts[0]));
            }
        }

        $filtered = array_filter($names);
        $unique   = array_unique($filtered);
        return array_values($unique);
    }
}
