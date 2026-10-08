<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * WpPopPop Public Frontend Markup Renderer
 * Full 26-Element Stage Parity, SVG Geometry, Video Players & Dual Canvas Normalization
 */
class WpPopPop_Front_Renderer {

    /**
     * Render the complete popup HTML structure
     *
     * @param string $uid
     * @param array  $config
     * @param bool   $is_inline
     * @return string
     */
    public function render_popup_markup($uid, array $config, $is_inline = false) {
        $canvases = isset($config['canvases']) && is_array($config['canvases'])
            ? $config['canvases']
            : (isset($config['screens']) && is_array($config['screens']) ? $config['screens'] : []);

        if (empty($canvases)) {
            $canvases = [1 => []];
        }

        $canvas_meta = isset($config['canvasMeta']) && is_array($config['canvasMeta'])
            ? $config['canvasMeta']
            : (isset($config['canvas_meta']) && is_array($config['canvas_meta']) ? $config['canvas_meta'] : []);

        $settings = isset($config['settings']) && is_array($config['settings']) ? $config['settings'] : [];
        $box_settings = isset($settings['box']) && is_array($settings['box']) ? $settings['box'] : [];

        $triggers = isset($settings['triggers']) && is_array($settings['triggers']) ? $settings['triggers'] : [];
        $freq_mode = isset($settings['frequency']['mode']) ? sanitize_text_field($settings['frequency']['mode']) : 'always';
        $freq_days = isset($settings['frequency']['days']) ? intval($settings['frequency']['days']) : 7;

        $load_trigger = !empty($triggers['load']) ? '1' : '0';
        $load_delay   = isset($triggers['load_delay']) ? floatval($triggers['load_delay']) : 0;
        $exit_trigger = !empty($triggers['exit']) ? '1' : '0';
        $scroll_trig  = !empty($triggers['scroll']) ? (floatval($triggers['scroll_val'] ?? 50)) : '0';
        $adblock_trig = !empty($triggers['adblock']) ? '1' : '0';
        $back_trig    = !empty($triggers['backbutton']) ? '1' : '0';

        ob_start();
        ?>
        <div id="wppoppop-popup-<?php echo esc_attr($uid); ?>"
             class="wppoppop-popup-wrap <?php echo $is_inline ? 'wppoppop-inline-wrap' : ''; ?>"
             data-uid="<?php echo esc_attr($uid); ?>"
             data-trigger-load="<?php echo esc_attr($load_trigger); ?>"
             data-trigger-delay="<?php echo esc_attr($load_delay); ?>"
             data-trigger-exit="<?php echo esc_attr($exit_trigger); ?>"
             data-trigger-scroll="<?php echo esc_attr($scroll_trig); ?>"
             data-trigger-adblock="<?php echo esc_attr($adblock_trig); ?>"
             data-trigger-back="<?php echo esc_attr($back_trig); ?>"
             data-freq-mode="<?php echo esc_attr($freq_mode); ?>"
             data-freq-days="<?php echo esc_attr($freq_days); ?>"
             style="<?php echo $is_inline ? '' : 'display:none;'; ?>">

            <?php if (!$is_inline) : ?>
                <div class="wppoppop-overlay" style="position:fixed;inset:0;background:rgba(15,23,42,0.65);backdrop-filter:blur(3px);z-index:999999;display:flex;align-items:center;justify-content:center;padding:20px;box-sizing:border-box;">
            <?php endif; ?>

            <?php
            $is_first = true;
            foreach ($canvases as $c_idx => $elements) :
                $c_num = intval($c_idx);
                $meta  = isset($canvas_meta[$c_num]) && is_array($canvas_meta[$c_num]) ? $canvas_meta[$c_num] : [];

                $width  = isset($meta['width']) ? intval($meta['width']) : (isset($box_settings['width']) ? intval($box_settings['width']) : 640);
                $height = isset($meta['height']) ? intval($meta['height']) : (isset($box_settings['height']) ? intval($box_settings['height']) : 400);

                $bg_mode   = isset($meta['bg_mode']) ? $meta['bg_mode'] : (isset($box_settings['bg_mode']) ? $box_settings['bg_mode'] : 'solid');
                $bg_color  = isset($meta['bg_color']) ? $meta['bg_color'] : (isset($box_settings['bg_color']) ? $box_settings['bg_color'] : '#ffffff');
                $grad_c1   = isset($meta['grad_color1']) ? $meta['grad_color1'] : (isset($box_settings['grad_color1']) ? $box_settings['grad_color1'] : '#3b82f6');
                $grad_c2   = isset($meta['grad_color2']) ? $meta['grad_color2'] : (isset($box_settings['grad_color2']) ? $box_settings['grad_color2'] : '#1d4ed8');
                $grad_ang  = isset($meta['grad_angle']) ? intval($meta['grad_angle']) : (isset($box_settings['grad_angle']) ? intval($box_settings['grad_angle']) : 135);

                $bg_css = ($bg_mode === 'gradient')
                    ? "background:linear-gradient({$grad_ang}deg, {$grad_c1}, {$grad_c2});"
                    : "background:{$bg_color};";

                $anim_appearance = isset($meta['anim_appearance']) ? sanitize_text_field($meta['anim_appearance']) : 'fadeIn';
                $anim_duration   = isset($meta['anim_duration']) ? intval($meta['anim_duration']) : 1000;
                $anim_delay      = isset($meta['anim_delay']) ? intval($meta['anim_delay']) : 0;
                $anim_exit       = isset($meta['anim_disappearance']) ? sanitize_text_field($meta['anim_disappearance']) : 'fadeOut';
                ?>
                <div class="wppoppop-canvas-container wppoppop-screen-container wppoppop-screen"
                     data-canvas-index="<?php echo esc_attr($c_num); ?>"
                     data-screen-index="<?php echo esc_attr($c_num); ?>"
                     data-screen="<?php echo esc_attr($c_num); ?>"
                     data-anim-appearance="<?php echo esc_attr($anim_appearance); ?>"
                     data-anim-duration="<?php echo esc_attr($anim_duration); ?>"
                     data-anim-delay="<?php echo esc_attr($anim_delay); ?>"
                     data-anim-disappearance="<?php echo esc_attr($anim_exit); ?>"
                     style="<?php echo $is_first ? 'display:block;' : 'display:none;'; ?>position:relative;width:<?php echo esc_attr($width); ?>px;max-width:100%;height:<?php echo esc_attr($height); ?>px;<?php echo $bg_css; ?>border-radius:8px;box-shadow:0 25px 50px -12px rgba(0,0,0,0.5);overflow:hidden;box-sizing:border-box;">

                    <form class="wppoppop-form" style="width:100%;height:100%;position:relative;margin:0;padding:0;">
                        <?php
                        if (is_array($elements)) {
                            foreach ($elements as $el) {
                                if (is_array($el)) {
                                    echo $this->render_canvas_element($el);
                                }
                            }
                        }
                        ?>
                    </form>
                </div>
                <?php
                $is_first = false;
            endforeach;
            ?>

            <?php if (!$is_inline) : ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Render individual layer element markup with full 26-element stage parity
     *
     * @param array $el
     * @return string
     */
    private function render_canvas_element(array $el) {
        $id     = esc_attr($el['id'] ?? ('el_' . wp_generate_password(6, false)));
        $type   = strtolower(trim($el['type'] ?? 'text'));
        $top    = intval($el['top'] ?? 20);
        $left   = intval($el['left'] ?? 20);
        $width  = intval($el['width'] ?? 180);
        $height = intval($el['height'] ?? 42);
        $z_index = intval($el['zIndex'] ?? ($el['z_index'] ?? 10));

        $opacity = isset($el['opacity']) ? floatval($el['opacity']) : 1.0;
        $border_radius = intval($el['borderRadius'] ?? 4);
        $border_width  = intval($el['borderWidth'] ?? 0);
        $border_style  = sanitize_text_field($el['borderStyle'] ?? 'solid');
        $border_color  = sanitize_text_field($el['borderColor'] ?? '#cbd5e1');
        $bg_color      = sanitize_text_field($el['bgColor'] ?? 'transparent');
        $color         = sanitize_text_field($el['color'] ?? 'inherit');
        $font_family   = sanitize_text_field($el['fontFamily'] ?? 'inherit');
        $font_size     = isset($el['fontSize']) ? intval($el['fontSize']) : 14;
        $font_weight   = sanitize_text_field($el['fontWeight'] ?? '400');
        $text_align    = sanitize_text_field($el['textAlign'] ?? 'left');
        $padding       = intval($el['padding'] ?? 0);
        $box_shadow    = sanitize_text_field($el['boxShadow'] ?? 'none');

        $anim_effect = sanitize_text_field($el['animEffect'] ?? 'none');
        $anim_class  = ($anim_effect !== 'none' && !empty($anim_effect)) ? 'animate__animated animate__' . $anim_effect : '';

        $wrapper_styles = [
            "position:absolute",
            "top:{$top}px",
            "left:{$left}px",
            "width:{$width}px",
            "height:{$height}px",
            "z-index:{$z_index}",
            "opacity:{$opacity}",
            "border-radius:{$border_radius}px",
            "box-sizing:border-box"
        ];

        if ($font_family !== 'inherit' && !empty($font_family)) {
            $wrapper_styles[] = "font-family:" . esc_attr($font_family);
        }
        if ($font_size > 0) {
            $wrapper_styles[] = "font-size:{$font_size}px";
        }
        if (!empty($font_weight)) {
            $wrapper_styles[] = "font-weight:" . esc_attr($font_weight);
        }
        if (!empty($text_align)) {
            $wrapper_styles[] = "text-align:" . esc_attr($text_align);
        }
        if ($padding > 0) {
            $wrapper_styles[] = "padding:{$padding}px";
        }
        if (!empty($color)) {
            $wrapper_styles[] = "color:" . esc_attr($color);
        }
        if (!empty($bg_color)) {
            $wrapper_styles[] = "background-color:" . esc_attr($bg_color);
        }
        if ($box_shadow !== 'none' && !empty($box_shadow)) {
            $wrapper_styles[] = "box-shadow:" . esc_attr($box_shadow);
        }

        // Shape borders are rendered internally via SVG
        if ($type !== 'shape' && $border_width > 0 && $border_style !== 'none') {
            $wrapper_styles[] = "border:{$border_width}px {$border_style} " . esc_attr($border_color);
        } elseif ($border_color === 'transparent') {
            $wrapper_styles[] = "border-color:transparent";
        }

        $style_attr = implode(';', $wrapper_styles) . ';';
        $content    = $el['content'] ?? '';
        $field_name = sanitize_key($el['fieldName'] ?? ($el['field_name'] ?? $type));
        $required   = !empty($el['required']);
        $justify_val = ($text_align === 'center') ? 'center' : (($text_align === 'right') ? 'flex-end' : 'flex-start');

        $html = '<div id="el-' . $id . '" class="wppoppop-front-element ' . esc_attr($anim_class) . '" style="' . esc_attr($style_attr) . '">';

        switch ($type) {
            case 'title':
                $tag = in_array($el['htmlTag'] ?? 'h2', ['h1', 'h2', 'h3', 'h4'], true) ? $el['htmlTag'] : 'h2';
                $html .= '<' . $tag . ' style="width:100%;height:100%;display:flex;align-items:center;justify-content:' . esc_attr($justify_val) . ';margin:0;padding:0 8px;font-size:inherit;font-weight:inherit;color:inherit;line-height:1.2;">' . esc_html($content ?: 'Catchy Campaign Title') . '</' . $tag . '>';
                break;

            case 'text':
                $tag = in_array($el['htmlTag'] ?? 'p', ['p', 'h1', 'h2', 'h3', 'span', 'div'], true) ? $el['htmlTag'] : 'p';
                $html .= '<' . $tag . ' style="width:100%;height:100%;display:flex;align-items:center;justify-content:' . esc_attr($justify_val) . ';margin:0;padding:0 8px;font-size:inherit;color:inherit;line-height:1.4;">' . wp_kses_post($content) . '</' . $tag . '>';
                break;

            case 'image':
                $img_url = esc_url($el['imageUrl'] ?? ($content ?: ''));
                $img_alt = esc_attr($el['imageAlt'] ?? '');
                $img_fit = esc_attr($el['imageFit'] ?? 'cover');
                if (!empty($img_url)) {
                    $html .= '<img src="' . $img_url . '" alt="' . $img_alt . '" style="width:100%;height:100%;object-fit:' . $img_fit . ';display:block;border-radius:inherit;">';
                }
                break;

            case 'video':
                $raw_url = trim($el['videoUrl'] ?? ($content ?: ''));
                $autoplay = !empty($el['videoAutoplay']);
                $controls = ($el['videoControls'] ?? true) !== false;

                if (!empty($raw_url)) {
                    if (strpos($raw_url, 'youtube.com') !== false || strpos($raw_url, 'youtu.be') !== false) {
                        preg_match('/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([\w-]{11})/', $raw_url, $matches);
                        $yt_id = $matches[1] ?? '';
                        $embed_url = 'https://www.youtube.com/embed/' . $yt_id . ($autoplay ? '?autoplay=1&mute=1' : '');
                        $html .= '<iframe src="' . esc_url($embed_url) . '" style="width:100%;height:100%;border:none;border-radius:inherit;display:block;" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe>';
                    } elseif (strpos($raw_url, 'vimeo.com') !== false) {
                        $parts = explode('/', parse_url($raw_url, PHP_URL_PATH));
                        $vimeo_id = end($parts);
                        $embed_url = 'https://player.vimeo.com/video/' . $vimeo_id . ($autoplay ? '?autoplay=1&muted=1' : '');
                        $html .= '<iframe src="' . esc_url($embed_url) . '" style="width:100%;height:100%;border:none;border-radius:inherit;display:block;" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
                    } else {
                        $html .= '<video src="' . esc_url($raw_url) . '" ' . ($controls ? 'controls' : '') . ' ' . ($autoplay ? 'autoplay muted' : '') . ' playsinline style="width:100%;height:100%;object-fit:cover;border-radius:inherit;"></video>';
                    }
                }
                break;

            case 'shape':
                $preset = sanitize_key($el['shapePreset'] ?? 'circle');
                $fill   = ($el['shapeFill'] ?? '') === 'transparent' ? 'none' : esc_attr($el['shapeFill'] ?? '#3b82f6');
                $stroke = ($el['shapeStroke'] ?? '') === 'transparent' ? 'none' : esc_attr($el['shapeStroke'] ?? '#1d4ed8');
                $stroke_w = intval($el['shapeStrokeWidth'] ?? 0);
                $rot    = intval($el['shapeRotate'] ?? 0);

                $stroke_attr = ($stroke !== 'none' && $stroke_w > 0)
                    ? 'stroke="' . $stroke . '" stroke-width="' . $stroke_w . '" stroke-linejoin="round" vector-effect="non-scaling-stroke"'
                    : 'stroke="none"';
                $fill_attr = 'fill="' . $fill . '"';

                $svg_inner = '';
                switch ($preset) {
                    case 'circle':
                        $svg_inner = '<ellipse cx="50" cy="50" rx="46" ry="46" ' . $fill_attr . ' ' . $stroke_attr . ' />';
                        break;
                    case 'square':
                        $svg_inner = '<rect x="4" y="4" width="92" height="92" ' . $fill_attr . ' ' . $stroke_attr . ' />';
                        break;
                    case 'rounded_square':
                        $svg_inner = '<rect x="4" y="4" width="92" height="92" rx="16" ry="16" ' . $fill_attr . ' ' . $stroke_attr . ' />';
                        break;
                    case 'star':
                        $svg_inner = '<polygon points="50,4 64,34 97,36 71,58 80,90 50,71 20,90 29,58 3,36 36,34" ' . $fill_attr . ' ' . $stroke_attr . ' />';
                        break;
                    case 'triangle':
                        $svg_inner = '<polygon points="50,6 94,92 6,92" ' . $fill_attr . ' ' . $stroke_attr . ' />';
                        break;
                    case 'diamond':
                        $svg_inner = '<polygon points="50,5 95,50 50,95 5,50" ' . $fill_attr . ' ' . $stroke_attr . ' />';
                        break;
                    case 'heart':
                        $svg_inner = '<path d="M 50,32 C 50,32 44,14 26,14 C 11,14 4,28 4,44 C 4,68 40,88 50,94 C 60,88 96,68 96,44 C 96,28 89,14 74,14 C 56,14 50,32 50,32 Z" ' . $fill_attr . ' ' . $stroke_attr . ' />';
                        break;
                    case 'hexagon':
                        $svg_inner = '<polygon points="50,4 92,26 92,74 50,96 8,74 8,26" ' . $fill_attr . ' ' . $stroke_attr . ' />';
                        break;
                    case 'octagon':
                        $svg_inner = '<polygon points="30,4 70,4 96,30 96,70 70,96 30,96 4,70 4,30" ' . $fill_attr . ' ' . $stroke_attr . ' />';
                        break;
                    case 'shield':
                        $svg_inner = '<path d="M 50,4 L 92,18 L 92,54 C 92,78 50,96 50,96 C 50,96 8,78 8,54 L 8,18 Z" ' . $fill_attr . ' ' . $stroke_attr . ' />';
                        break;
                    case 'cross':
                        $svg_inner = '<polygon points="35,4 65,4 65,35 96,35 96,65 65,65 65,96 35,96 35,65 4,65 4,35 35,35" ' . $fill_attr . ' ' . $stroke_attr . ' />';
                        break;
                    default:
                        $svg_inner = '<rect x="4" y="4" width="92" height="92" ' . $fill_attr . ' ' . $stroke_attr . ' />';
                        break;
                }

                $html .= '<svg viewBox="0 0 100 100" preserveAspectRatio="none" style="width:100%;height:100%;display:block;transform:rotate(' . $rot . 'deg);overflow:visible;">' . $svg_inner . '</svg>';
                break;

            case 'textfield':
                $html .= '<input type="text" name="' . esc_attr($field_name) . '" placeholder="' . esc_attr($content ?: 'Enter text here...') . '" ' . ($required ? 'required' : '') . ' style="width:100%;height:100%;padding:0 10px;border:1px solid #cbd5e1;border-radius:inherit;font-size:inherit;color:inherit;box-sizing:border-box;">';
                break;

            case 'email':
                $html .= '<input type="email" name="' . esc_attr($field_name ?: 'email') . '" placeholder="' . esc_attr($content ?: 'Enter your email...') . '" ' . ($required ? 'required' : '') . ' style="width:100%;height:100%;padding:0 10px;border:1px solid #cbd5e1;border-radius:inherit;font-size:inherit;color:inherit;box-sizing:border-box;">';
                break;

            case 'number':
                $min  = isset($el['min']) ? floatval($el['min']) : 0;
                $max  = isset($el['max']) ? floatval($el['max']) : 100;
                $step = isset($el['step']) ? floatval($el['step']) : 1;
                $html .= '<input type="number" name="' . esc_attr($field_name ?: 'quantity') . '" min="' . esc_attr($min) . '" max="' . esc_attr($max) . '" step="' . esc_attr($step) . '" value="' . esc_attr($content ?: '1') . '" style="width:100%;height:100%;padding:0 10px;border:1px solid #cbd5e1;border-radius:inherit;font-size:inherit;color:inherit;box-sizing:border-box;">';
                break;

            case 'select':
                $options = !empty($el['options']) && is_array($el['options']) ? $el['options'] : explode(',', $content ?: 'Option 1, Option 2');
                $html .= '<select name="' . esc_attr($field_name ?: 'dropdown') . '" style="width:100%;height:100%;padding:0 10px;border:1px solid #cbd5e1;border-radius:inherit;font-size:inherit;color:inherit;box-sizing:border-box;">';
                foreach ($options as $opt) {
                    $opt_clean = trim($opt);
                    $html .= '<option value="' . esc_attr($opt_clean) . '">' . esc_html($opt_clean) . '</option>';
                }
                $html .= '</select>';
                break;

            case 'radios':
                $r_opts = !empty($el['options']) && is_array($el['options']) ? $el['options'] : explode(',', $content ?: 'Choice A, Choice B');
                $html .= '<div style="display:flex;gap:12px;align-items:center;justify-content:' . esc_attr($justify_val) . ';height:100%;padding:0 8px;font-size:inherit;box-sizing:border-box;">';
                foreach ($r_opts as $idx => $r_val) {
                    $r_clean = trim($r_val);
                    $html .= '<label style="cursor:pointer;display:inline-flex;align-items:center;gap:4px;"><input type="radio" name="' . esc_attr($field_name ?: 'radio_choice') . '" value="' . esc_attr($r_clean) . '" ' . ($idx === 0 ? 'checked' : '') . '> ' . esc_html($r_clean) . '</label>';
                }
                $html .= '</div>';
                break;

            case 'checkboxes':
                $html .= '<div style="display:flex;align-items:center;justify-content:' . esc_attr($justify_val) . ';gap:6px;height:100%;padding:0 8px;font-size:inherit;box-sizing:border-box;"><label style="cursor:pointer;display:inline-flex;align-items:center;gap:6px;"><input type="checkbox" name="' . esc_attr($field_name ?: 'terms') . '" value="1" ' . (!empty($el['checked']) ? 'checked' : '') . '> <span>' . esc_html($content ?: 'I agree to the terms') . '</span></label></div>';
                break;

            case 'rating':
                $star_count = intval($content ?: 5);
                $star_color = esc_attr($el['ratingColor'] ?? '#f59e0b');
                $html .= '<div class="wppoppop-field-rating" style="display:flex;gap:4px;align-items:center;justify-content:' . esc_attr($justify_val) . ';height:100%;color:' . $star_color . ';font-size:20px;cursor:pointer;">';
                for ($s = 1; $s <= 5; $s++) {
                    $star_active = ($s <= $star_count) ? '' : 'color:#cbd5e1;';
                    $html .= '<span class="wppoppop-rating-star" data-val="' . $s . '" style="cursor:pointer;' . $star_active . '">★</span>';
                }
                $html .= '<input type="hidden" name="' . esc_attr($field_name ?: 'rating') . '" value="' . $star_count . '"></div>';
                break;

            case 'date':
                $html .= '<input type="date" name="' . esc_attr($field_name ?: 'date') . '" value="' . esc_attr($content) . '" ' . ($required ? 'required' : '') . ' style="width:100%;height:100%;padding:0 10px;border:1px solid #cbd5e1;border-radius:inherit;font-size:inherit;color:inherit;box-sizing:border-box;">';
                break;

            case 'slider':
                $s_min = intval($el['min'] ?? 0);
                $s_max = intval($el['max'] ?? 100);
                $s_val = intval($content ?: 50);
                $html .= '<div style="padding:0 10px;height:100%;display:flex;align-items:center;box-sizing:border-box;"><input type="range" name="' . esc_attr($field_name ?: 'range_val') . '" min="' . $s_min . '" max="' . $s_max . '" value="' . $s_val . '" style="width:100%;"></div>';
                break;

            case 'signature':
                $pen_color = esc_attr($el['penColor'] ?? '#0f172a');
                $html .= '<div class="wppoppop-sig-wrap" style="width:100%;height:100%;position:relative;background:#fff;border-radius:inherit;"><canvas class="wppoppop-sig-canvas" width="' . $width . '" height="' . $height . '" data-pen-color="' . $pen_color . '" style="width:100%;height:100%;border:1px dashed #94a3b8;border-radius:inherit;cursor:crosshair;"></canvas><button type="button" class="wppoppop-sig-clear button" style="position:absolute;bottom:4px;right:4px;font-size:9px;padding:1px 6px;height:20px;background:#e2e8f0;border:none;cursor:pointer;">' . esc_html($el['clearLabel'] ?? 'Clear') . '</button><input type="hidden" name="' . esc_attr($field_name ?: 'digital_signature') . '"></div>';
                break;

            case 'wheel':
                $slices = esc_attr($content ?: '10% OFF, FREE SHIPPING, 25% OFF, JACKPOT');
                $btn_text = esc_html($el['btnText'] ?? 'SPIN TO WIN!');
                $html .= '<div class="wppoppop-wheel-container" data-slices="' . $slices . '" style="width:100%;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;"><canvas class="wppoppop-wheel-canvas" width="160" height="160" style="border-radius:50%;box-shadow:0 4px 12px rgba(0,0,0,0.2);"></canvas><button type="button" class="wppoppop-wheel-btn button" style="margin-top:6px;background:#4338ca;color:#fff;border:none;font-weight:700;font-size:11px;padding:3px 10px;border-radius:4px;cursor:pointer;">' . $btn_text . '</button></div>';
                break;

            case 'scratch':
                $prize_text = esc_html($content ?: 'YOU WON 25% OFF!');
                $foil_color = esc_attr($el['foilColor'] ?? '#94a3b8');
                $html .= '<div class="wppoppop-scratch-container" style="width:100%;height:100%;position:relative;overflow:hidden;border-radius:inherit;"><div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:#fef08a;color:#854d0e;font-weight:700;font-size:13px;padding:8px;text-align:center;box-sizing:border-box;">' . $prize_text . '</div><canvas class="wppoppop-scratch-canvas" width="' . $width . '" height="' . $height . '" data-foil="' . $foil_color . '" style="position:absolute;inset:0;width:100%;height:100%;cursor:crosshair;"></canvas></div>';
                break;

            case 'countdown':
                $secs = intval($el['countdownSeconds'] ?? 900);
                $mins = floor($secs / 60);
                $rem_secs = $secs % 60;
                $display = sprintf('%02d : %02d', $mins, $rem_secs);
                $html .= '<div class="wppoppop-countdown" data-secs="' . $secs . '" style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-family:monospace;font-weight:700;font-size:16px;background:#1e293b;color:#f8fafc;border-radius:inherit;"><span class="cd-display">' . esc_html($display) . '</span></div>';
                break;

            case 'progress':
                $pct = intval($content ?: 65);
                $bar_color = esc_attr($el['progressColor'] ?? '#2563eb');
                $html .= '<div style="width:100%;height:100%;background:#e2e8f0;border-radius:inherit;overflow:hidden;position:relative;"><div style="width:' . $pct . '%;height:100%;background:' . $bar_color . ';transition:width 0.3s ease;"></div></div>';
                break;

            case 'file':
                $exts = esc_attr($el['fileExts'] ?? '.jpg,.jpeg,.png,.pdf');
                $html .= '<div style="width:100%;height:100%;border:1px dashed #cbd5e1;display:flex;align-items:center;justify-content:center;font-size:11px;color:#64748b;border-radius:inherit;padding:4px;box-sizing:border-box;"><input type="file" name="' . esc_attr($field_name ?: 'attachment') . '" accept="' . $exts . '" style="width:100%;font-size:11px;"></div>';
                break;

            case 'submit':
                $action = esc_attr($el['submitAction'] ?? 'default');
                $html .= '<button type="submit" class="wppoppop-submit-btn wppoppop-next-step" data-action="' . $action . '" style="width:100%;height:100%;background:inherit;color:inherit;font-size:inherit;font-weight:inherit;border:none;border-radius:inherit;cursor:pointer;">' . esc_html($content ?: 'Submit Form') . '</button>';
                break;

            case 'link_btn':
                $url = esc_url($el['linkUrl'] ?? ($el['actionUrl'] ?? '#'));
                $target = (!empty($el['linkBlank'])) ? '_blank' : '_self';
                $html .= '<a href="' . $url . '" target="' . $target . '" rel="noopener" class="wppoppop-link-btn" style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:inherit;color:inherit;font-size:inherit;font-weight:inherit;text-decoration:none;border-radius:inherit;box-sizing:border-box;">' . esc_html($content ?: 'Learn More →') . '</a>';
                break;

            case 'step_btn':
                $target_c = intval($el['goto_canvas'] ?? ($el['goto_screen'] ?? 2));
                $html .= '<button type="button" class="wppoppop-next-canvas-btn wppoppop-next-screen-btn wppoppop-next-step" data-goto-canvas="' . $target_c . '" data-goto-screen="' . $target_c . '" style="width:100%;height:100%;background:inherit;color:inherit;font-size:inherit;font-weight:inherit;border:none;border-radius:inherit;cursor:pointer;">' . esc_html($content ?: ('Canvas ' . $target_c . ' →')) . '</button>';
                break;

            case 'pay':
                $amount = esc_attr($el['payAmount'] ?? '19.99');
                $curr   = esc_attr($el['payCurrency'] ?? 'USD');
                $gw     = esc_attr($el['payGateway'] ?? 'stripe');
                $html .= '<button type="button" class="wppoppop-pay-btn" data-gateway="' . $gw . '" data-amount="' . $amount . '" data-currency="' . $curr . '" style="width:100%;height:100%;background:inherit;color:inherit;font-size:inherit;font-weight:inherit;border:none;border-radius:inherit;cursor:pointer;">' . esc_html($content ?: 'Checkout Now') . '</button>';
                break;

            case 'close_icon':
                $act = esc_attr($el['closeAction'] ?? 'close');
                $style = ($el['closeIconStyle'] ?? 'times') === 'dashicon' ? '<span class="dashicons dashicons-no-alt" style="font-size:inherit;width:auto;height:auto;line-height:1;"></span>' : '&times;';
                $html .= '<button type="button" class="wppoppop-close-btn" data-close-action="' . $act . '" style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:transparent;border:none;color:inherit;font-size:inherit;font-weight:700;line-height:1;cursor:pointer;padding:0;">' . $style . '</button>';
                break;

            case 'html':
                $html .= '<div style="width:100%;height:100%;overflow:hidden;box-sizing:border-box;">' . $content . '</div>';
                break;

            default:
                $html .= '<div style="padding:6px;font-size:inherit;color:inherit;">' . esc_html($content) . '</div>';
                break;
        }

        $html .= '</div>';
        return $html;
    }
}
