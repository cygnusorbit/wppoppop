<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Front_Renderer {

    public function render_popup_markup($uid, array $config, $is_inline = false, $preload = false) {
        $box = $config['box'] ?? [];
        $width = intval($box['width'] ?? 640);
        $height = intval($box['height'] ?? 400);
        $bg_mode = $box['bg_mode'] ?? 'solid';
        $bg_color = $box['bg_color'] ?? '#ffffff';
        $grad_c1 = $box['grad_color1'] ?? '#3b82f6';
        $grad_c2 = $box['grad_color2'] ?? '#1d4ed8';
        $grad_ang = intval($box['grad_angle'] ?? 135);

        $box_bg = ($bg_mode === 'gradient')
            ? "background:linear-gradient({$grad_ang}deg, {$grad_c1}, {$grad_c2});"
            : "background:{$bg_color};";

        $canvases = $config['canvases'] ?? $config['screens'] ?? [];
        if (empty($canvases) || !is_array($canvases)) {
            $canvases = [1 => []];
        }

        $canvas_meta = $config['canvasMeta'] ?? $config['canvas_meta'] ?? [];
        $encoded_config = esc_attr(wp_json_encode($config));

        // Settings Integrations
        $default_font   = function_exists('wppoppop_get_setting') ? wppoppop_get_setting('default_font', 'inherit') : 'inherit';
        $show_watermark = function_exists('wppoppop_get_setting') ? (bool) wppoppop_get_setting('powered_by_badge', false) : false;
        $minify_css     = function_exists('wppoppop_get_setting') ? (bool) wppoppop_get_setting('minify_css', false) : false;

        $custom_inline_css = "
        #wppoppop-popup-{$uid}, #wppoppop-wrap-{$uid} {
            font-family: {$default_font};
        }
        ";

        if ($minify_css && function_exists('wppoppop_minify_css')) {
            $custom_inline_css = wppoppop_minify_css($custom_inline_css);
        }

        $overlay_style = $is_inline ? 'display:block;position:relative;' : 'display:none;position:fixed;inset:0;z-index:999999;';
        $preload_class = $preload ? 'wppoppop-preloaded' : '';
        ?>
        <div class="wppoppop-overlay wppoppop-popup-wrap <?php echo $preload_class; ?>"
             id="wppoppop-popup-<?php echo esc_attr($uid); ?>"
             data-uid="<?php echo esc_attr($uid); ?>"
             data-config="<?php echo $encoded_config; ?>"
             style="<?php echo esc_attr($overlay_style); ?>align-items:center;justify-content:center;background:rgba(15,23,42,0.65);-webkit-backdrop-filter:blur(3px);backdrop-filter:blur(3px);"
             aria-hidden="true"
             role="dialog">

            <style><?php echo $custom_inline_css; ?></style>

            <div class="wppoppop-backdrop" data-uid="<?php echo esc_attr($uid); ?>"></div>

            <div class="wppoppop-box wppoppop-box-container"
                 data-uid="<?php echo esc_attr($uid); ?>"
                 style="position:relative;width:<?php echo esc_attr($width); ?>px;max-width:92vw;min-height:<?php echo esc_attr($height); ?>px;border-radius:8px;box-shadow:0 25px 50px -12px rgba(0,0,0,0.4);overflow:visible !important;<?php echo esc_attr($box_bg); ?>">

                <!-- Close Button -->
                <?php if (!$is_inline): ?>
                    <button type="button" class="wppoppop-close-btn" data-uid="<?php echo esc_attr($uid); ?>" aria-label="Close" style="position:absolute;top:10px;right:10px;background:transparent;border:none;color:#64748b;font-size:22px;cursor:pointer;line-height:1;z-index:9999999;">&times;</button>
                <?php endif; ?>

                <!-- Submission Status Overlay -->
                <div class="wppoppop-status-overlay" style="display:none;position:absolute;inset:0;background:rgba(255,255,255,0.95);z-index:999999;align-items:center;justify-content:center;flex-direction:column;padding:20px;text-align:center;border-radius:inherit;">
                    <span class="dashicons dashicons-yes-alt" style="font-size:42px;width:42px;height:42px;color:#10b981;margin-bottom:8px;"></span>
                    <p class="wppoppop-status-message" style="font-size:15px;font-weight:700;color:#1e293b;margin:0;"></p>
                </div>

                <!-- Sequence Canvases -->
                <?php foreach ($canvases as $index => $elements): 
                    $canvas_num = intval($index) ?: 1;
                    $is_active = ($canvas_num === 1);
                    $display_style = $is_active ? 'display:block;' : 'display:none;';

                    $cm = $canvas_meta[$canvas_num] ?? [];
                    $c_width = intval($cm['width'] ?? $width);
                    $c_height = intval($cm['height'] ?? $height);
                    $c_anim_app = esc_attr($cm['anim_appearance'] ?? 'fade');
                    $c_anim_dur = intval($cm['anim_duration'] ?? 1000);
                    $c_anim_del = intval($cm['anim_delay'] ?? 0);
                    $c_anim_dis = esc_attr($cm['anim_disappearance'] ?? 'fade');

                    $c_bg_mode = $cm['bg_mode'] ?? 'solid';
                    $c_bg_color = $cm['bg_color'] ?? '';
                    $c_bg_style = '';
                    if (!empty($c_bg_color)) {
                        if ($c_bg_mode === 'gradient') {
                            $g1 = esc_attr($cm['grad_color1'] ?? '#3b82f6');
                            $g2 = esc_attr($cm['grad_color2'] ?? '#1d4ed8');
                            $ga = intval($cm['grad_angle'] ?? 135);
                            $c_bg_style = "background:linear-gradient({$ga}deg, {$g1}, {$g2});";
                        } else {
                            $c_bg_style = "background:" . esc_attr($c_bg_color) . ";";
                        }
                    }
                ?>
                    <div class="wppoppop-canvas-container wppoppop-canvas-stage wppoppop-screen-container <?php echo $is_active ? 'wppoppop-canvas-active wppoppop-screen-active active' : ''; ?>"
                         id="wppoppop-canvas-<?php echo esc_attr($uid); ?>-<?php echo esc_attr($canvas_num); ?>"
                         data-canvas="<?php echo esc_attr($canvas_num); ?>"
                         data-screen="<?php echo esc_attr($canvas_num); ?>"
                         data-canvas-index="<?php echo esc_attr($canvas_num); ?>"
                         data-screen-index="<?php echo esc_attr($canvas_num); ?>"
                         data-width="<?php echo esc_attr($c_width); ?>"
                         data-height="<?php echo esc_attr($c_height); ?>"
                         data-anim-appearance="<?php echo $c_anim_app; ?>"
                         data-anim-duration="<?php echo $c_anim_dur; ?>"
                         data-anim-delay="<?php echo $c_anim_del; ?>"
                         data-anim-disappearance="<?php echo $c_anim_dis; ?>"
                         style="<?php echo esc_attr($display_style . $c_bg_style); ?>width:100%;min-height:<?php echo esc_attr($c_height); ?>px;position:relative;">

                        <div class="wppoppop-canvas-content">
                            <?php 
                            if (is_array($elements)) {
                                foreach ($elements as $el) {
                                    if (is_array($el) && empty($el['hidden'])) {
                                        echo $this->render_canvas_element($el);
                                    }
                                }
                            }
                            ?>
                        </div>

                        <!-- Settings: Watermark Badge -->
                        <?php if ($show_watermark): ?>
                            <div class="wppoppop-watermark-badge" style="position:absolute;bottom:8px;right:12px;z-index:9999;user-select:none;">
                                <a href="https://github.com/cygnusorbit/wppoppop" target="_blank" rel="noopener noreferrer" style="display:inline-flex;align-items:center;gap:4px;background:rgba(255,255,255,0.85);border:1px solid #e2e8f0;border-radius:12px;padding:2px 8px;font-size:10px;color:#64748b;text-decoration:none;box-shadow:0 1px 2px rgba(0,0,0,0.05);">
                                    <span>Powered by</span> <strong style="color:#1e293b;">WpPopPop</strong>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    private function render_shape_svg($preset, $fill, $stroke, $stroke_width, $rotate) {
        $preset = !empty($preset) ? sanitize_key($preset) : 'circle';
        $fill = !empty($fill) ? esc_attr($fill) : '#3b82f6';
        $stroke = !empty($stroke) ? esc_attr($stroke) : 'transparent';
        $stroke_width = intval($stroke_width);
        $rotate = intval($rotate);

        $rot_css = $rotate ? sprintf('transform:rotate(%ddeg);-webkit-transform:rotate(%ddeg);', $rotate, $rotate) : '';
        $svg_style = sprintf('width:100%;height:100%;display:block;overflow:visible;%s', $rot_css);
        $s_attr = ($stroke_width > 0 && $stroke !== 'transparent') 
            ? sprintf('stroke="%s" stroke-width="%d" vector-effect="non-scaling-stroke"', $stroke, $stroke_width) 
            : '';

        switch ($preset) {
            case 'square':
                $path = sprintf('<rect x="4" y="4" width="92" height="92" fill="%s" %s />', $fill, $s_attr);
                break;
            case 'rounded_square':
                $path = sprintf('<rect x="4" y="4" width="92" height="92" rx="16" ry="16" fill="%s" %s />', $fill, $s_attr);
                break;
            case 'star':
                $path = sprintf('<polygon points="50,4 64,34 97,38 73,61 80,94 50,78 20,94 27,61 3,38 36,34" fill="%s" %s stroke-linejoin="round" />', $fill, $s_attr);
                break;
            case 'triangle':
                $path = sprintf('<polygon points="50,6 96,92 4,92" fill="%s" %s stroke-linejoin="round" />', $fill, $s_attr);
                break;
            case 'diamond':
                $path = sprintf('<polygon points="50,4 96,50 50,96 4,50" fill="%s" %s stroke-linejoin="round" />', $fill, $s_attr);
                break;
            case 'heart':
                $path = sprintf('<path d="M50 88 C20 70 4 50 4 30 C4 14 16 4 30 4 C40 4 47 11 50 17 C53 11 60 4 70 4 C84 4 96 14 96 30 C96 50 80 70 50 88 Z" fill="%s" %s stroke-linejoin="round" />', $fill, $s_attr);
                break;
            case 'hexagon':
                $path = sprintf('<polygon points="25,6 75,6 96,50 75,94 25,94 4,50" fill="%s" %s stroke-linejoin="round" />', $fill, $s_attr);
                break;
            case 'octagon':
                $path = sprintf('<polygon points="30,4 70,4 96,30 96,70 70,96 30,96 4,70 4,30" fill="%s" %s stroke-linejoin="round" />', $fill, $s_attr);
                break;
            case 'shield':
                $path = sprintf('<path d="M50 4 L92 18 L92 54 C92 76 50 96 50 96 C50 96 8 76 8 54 L8 18 Z" fill="%s" %s stroke-linejoin="round" />', $fill, $s_attr);
                break;
            case 'cross':
                $path = sprintf('<polygon points="36,4 64,4 64,36 96,36 96,64 64,64 64,96 36,96 36,64 4,64 4,36 36,36" fill="%s" %s stroke-linejoin="round" />', $fill, $s_attr);
                break;
            case 'circle':
            default:
                $path = sprintf('<ellipse cx="50" cy="50" rx="46" ry="46" fill="%s" %s />', $fill, $s_attr);
                break;
        }

        return sprintf('<svg viewBox="0 0 100 100" preserveAspectRatio="none" style="%s">%s</svg>', esc_attr($svg_style), $path);
    }

    private function render_canvas_element(array $el) {
        $type   = $el['type'] ?? 'text';
        $top    = intval($el['top'] ?? $el['y'] ?? 20);
        $left   = intval($el['left'] ?? $el['x'] ?? 20);
        $width  = intval($el['width'] ?? $el['w'] ?? 200);
        $height = intval($el['height'] ?? $el['h'] ?? 40);
        $zIndex = intval($el['zIndex'] ?? 10);
        $bRad   = intval($el['borderRadius'] ?? 4);
        $opac   = floatval($el['opacity'] ?? 1);
        $content = $el['content'] ?? '';

        $style = [
            'position:absolute',
            "top:{$top}px",
            "left:{$left}px",
            "width:{$width}px",
            "height:{$height}px",
            "z-index:{$zIndex}",
            "border-radius:{$bRad}px",
            "opacity:{$opac}",
            'box-sizing:border-box'
        ];

        if (!empty($el['fontFamily']) && $el['fontFamily'] !== 'inherit') $style[] = 'font-family:' . esc_attr($el['fontFamily']);
        if (!empty($el['fontSize'])) $style[] = 'font-size:' . intval($el['fontSize']) . 'px';
        if (!empty($el['color'])) $style[] = 'color:' . esc_attr($el['color']);
        if (!empty($el['bgColor']) && $type !== 'shape') $style[] = 'background-color:' . esc_attr($el['bgColor']);

                $pad_top    = intval($el['paddingTop'] ?? ($el['padding_top'] ?? ($el['padding'] ?? 0)));
        $pad_right  = intval($el['paddingRight'] ?? ($el['padding_right'] ?? ($el['padding'] ?? 0)));
        $pad_bottom = intval($el['paddingBottom'] ?? ($el['padding_bottom'] ?? ($el['padding'] ?? 0)));
        $pad_left   = intval($el['paddingLeft'] ?? ($el['padding_left'] ?? ($el['padding'] ?? 0)));
        if ($pad_top > 0 || $pad_right > 0 || $pad_bottom > 0 || $pad_left > 0) {
            $style[] = "padding:{$pad_top}px {$pad_right}px {$pad_bottom}px {$pad_left}px";
        }

        $style_attr = esc_attr(implode(';', $style));
        $anim_class = !empty($el['animEffect']) && $el['animEffect'] !== 'none' ? 'anim-' . esc_attr($el['animEffect']) : '';

        switch ($type) {
            case 'text':
                return '<div class="wppoppop-layer-item wppoppop-element wppoppop-el-text ' . $anim_class . '" style="' . $style_attr . '"><span class="wppoppop-text-render">' . wp_kses_post($content) . '</span></div>';

            case 'image':
                $img_url = !empty($content) ? esc_url($content) : esc_url($el['imgUrl'] ?? '');
                $alt = esc_attr($el['altText'] ?? 'Popup Image');
                $fit = esc_attr($el['objectFit'] ?? 'cover');
                if (empty($img_url)) {
                    return '';
                }
                return '<div class="wppoppop-layer-item wppoppop-element wppoppop-el-image ' . $anim_class . '" style="' . $style_attr . ';overflow:hidden;"><img src="' . $img_url . '" alt="' . $alt . '" style="width:100%;height:100%;object-fit:' . $fit . ';border-radius:inherit;display:block;"></div>';

            case 'shape':
                $preset = !empty($el['shapePreset']) ? $el['shapePreset'] : (!empty($el['content']) ? $el['content'] : 'circle');
                $fill = esc_attr($el['bgColor'] ?? '#3b82f6');
                $stroke = esc_attr($el['borderColor'] ?? 'transparent');
                $stroke_width = intval($el['borderWidth'] ?? 0);
                $rotate = intval($el['rotation'] ?? 0);
                $svg = $this->render_shape_svg($preset, $fill, $stroke, $stroke_width, $rotate);
                $shape_style = [
                    'position:absolute',
                    "top:{$top}px",
                    "left:{$left}px",
                    "width:{$width}px",
                    "height:{$height}px",
                    "z-index:{$zIndex}",
                    "opacity:{$opac}",
                    'box-sizing:border-box'
                ];
                $shape_style_attr = esc_attr(implode(';', $shape_style));
                return '<div class="wppoppop-layer-item wppoppop-element wppoppop-el-shape ' . $anim_class . '" style="' . $shape_style_attr . '">' . $svg . '</div>';

            case 'email':
                $ph = !empty($content) ? esc_attr($content) : 'Enter your email...';
                return '<div class="wppoppop-layer-item wppoppop-element wppoppop-el-email ' . $anim_class . '" style="' . $style_attr . '"><input type="email" name="email" class="wppoppop-field-email" placeholder="' . $ph . '" required style="width:100%;height:100%;padding:0 10px;border:1px solid #cbd5e1;border-radius:inherit;box-sizing:border-box;"></div>';

            case 'number':
                $ph = !empty($content) ? esc_attr($content) : 'Enter number...';
                return '<div class="wppoppop-layer-item wppoppop-element ' . $anim_class . '" style="' . $style_attr . '"><input type="number" name="number_field" placeholder="' . $ph . '" style="width:100%;height:100%;padding:0 10px;border:1px solid #cbd5e1;border-radius:inherit;box-sizing:border-box;"></div>';

            case 'select':
                $opts = array_map('trim', explode(',', $content ?: 'Option 1, Option 2'));
                $html = '<div class="wppoppop-layer-item wppoppop-element ' . $anim_class . '" style="' . $style_attr . '"><select name="dropdown_field" style="width:100%;height:100%;padding:0 8px;border:1px solid #cbd5e1;border-radius:inherit;box-sizing:border-box;">';
                foreach ($opts as $o) {
                    $html .= '<option value="' . esc_attr($o) . '">' . esc_html($o) . '</option>';
                }
                $html .= '</select></div>';
                return $html;

            case 'radios':
                $items = array_map('trim', explode(',', $content ?: 'Choice A, Choice B'));
                $html = '<div class="wppoppop-layer-item wppoppop-element ' . $anim_class . '" style="' . $style_attr . ';display:flex;align-items:center;gap:12px;font-size:12px;">';
                foreach ($items as $idx => $it) {
                    $html .= '<label style="cursor:pointer;"><input type="radio" name="radio_choice" value="' . esc_attr($it) . '" ' . ($idx === 0 ? 'checked' : '') . '> ' . esc_html($it) . '</label>';
                }
                $html .= '</div>';
                return $html;

            case 'checkboxes':
                $label = !empty($content) ? esc_html($content) : 'I agree to the terms';
                return '<div class="wppoppop-layer-item wppoppop-element ' . $anim_class . '" style="' . $style_attr . ';display:flex;align-items:center;gap:6px;font-size:12px;"><label style="cursor:pointer;"><input type="checkbox" name="agreement" value="1" checked> <span>' . $label . '</span></label></div>';

            case 'rating':
                return '<div class="wppoppop-layer-item wppoppop-element wppoppop-field-rating ' . $anim_class . '" style="' . $style_attr . ';display:flex;align-items:center;justify-content:center;color:#f59e0b;font-size:20px;cursor:pointer;"><input type="hidden" name="rating" value="5">★ ★ ★ ★ ★</div>';

            case 'date':
                return '<div class="wppoppop-layer-item wppoppop-element ' . $anim_class . '" style="' . $style_attr . '"><input type="text" name="date_picker" class="wppoppop-datepicker-input" style="width:100%;height:100%;padding:0 8px;border:1px solid #cbd5e1;border-radius:inherit;box-sizing:border-box;" placeholder="Select date..."></div>';

            case 'slider':
                return '<div class="wppoppop-layer-item wppoppop-element ' . $anim_class . '" style="' . $style_attr . ';display:flex;align-items:center;padding:0 8px;"><input type="range" name="range_slider" min="1" max="100" style="width:100%;"></div>';

            case 'signature':
                return '<div class="wppoppop-layer-item wppoppop-element ' . $anim_class . '" style="' . $style_attr . ';position:relative;"><canvas class="wppoppop-sig-canvas wppoppop-signature-canvas" width="' . $width . '" height="' . $height . '" style="width:100%;height:100%;border:1px dashed #94a3b8;border-radius:inherit;"></canvas><input type="hidden" name="signature" class="wppoppop-sig-input"><button type="button" class="wppoppop-sig-clear-btn" style="position:absolute;bottom:2px;right:2px;font-size:9px;background:#e2e8f0;border:none;border-radius:2px;cursor:pointer;padding:1px 4px;">Clear</button></div>';

            case 'wheel':
                return '<div class="wppoppop-layer-item wppoppop-element wppoppop-wheel-wrapper ' . $anim_class . '" style="' . $style_attr . ';text-align:center;"><canvas class="wppoppop-wheel-canvas" width="180" height="180" style="width:180px;height:180px;border-radius:50%;margin-bottom:6px;"></canvas><input type="hidden" name="prize"><br><button type="button" class="wppoppop-wheel-spin-btn button" style="background:#4338ca;color:#fff;border:none;padding:4px 10px;border-radius:4px;font-weight:700;cursor:pointer;font-size:11px;">SPIN PRIZE WHEEL</button></div>';

            case 'scratch':
                return '<div class="wppoppop-layer-item wppoppop-element wppoppop-scratch-wrapper ' . $anim_class . '" style="' . $style_attr . ';position:relative;"><div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:#fef08a;color:#854d0e;font-weight:700;font-size:13px;border-radius:inherit;">' . esc_html($content ?: 'YOU WON 25% OFF!') . '</div><canvas class="wppoppop-scratch-canvas" width="' . $width . '" height="' . $height . '" style="position:absolute;inset:0;width:100%;height:100%;cursor:crosshair;border-radius:inherit;"></canvas></div>';

            case 'countdown':
                return '<div class="wppoppop-layer-item wppoppop-element wppoppop-countdown-timer ' . $anim_class . '" data-seconds="900" style="' . $style_attr . ';display:flex;align-items:center;justify-content:center;background:#1e293b;color:#f8fafc;font-family:monospace;font-weight:700;font-size:16px;"><span class="cd-mins">15</span>&nbsp;:&nbsp;<span class="cd-secs">00</span></div>';

            case 'progress':
                return '<div class="wppoppop-layer-item wppoppop-element ' . $anim_class . '" style="' . $style_attr . ';background:#e2e8f0;overflow:hidden;"><div class="wppoppop-progress-fill" style="width:50%;height:100%;background:#2563eb;transition:width 0.3s ease;"></div></div>';

            case 'file':
                return '<div class="wppoppop-layer-item wppoppop-element ' . $anim_class . '" style="' . $style_attr . ';display:flex;align-items:center;justify-content:center;border:1px dashed #cbd5e1;"><input type="file" name="uploaded_file" style="font-size:11px;width:95%;"></div>';

            case 'step_btn':
                $target_canvas = !empty($el['goto_canvas']) ? intval($el['goto_canvas']) : (!empty($el['goto_screen']) ? intval($el['goto_screen']) : 2);
                $btn_text = !empty($content) ? esc_html($content) : 'Next Canvas &rarr;';
                return '<div class="wppoppop-layer-item wppoppop-element ' . $anim_class . '" style="' . $style_attr . '"><button type="button" class="wppoppop-next-canvas-btn wppoppop-next-screen-btn wppoppop-next-step" data-goto-canvas="' . esc_attr($target_canvas) . '" data-goto-screen="' . esc_attr($target_canvas) . '" data-goto="' . esc_attr($target_canvas) . '" style="width:100%;height:100%;background:#2563eb;color:#ffffff;border:none;border-radius:inherit;font-weight:700;font-size:13px;cursor:pointer;">' . $btn_text . '</button></div>';

            case 'submit':
            case 'button':
                $btn_text = !empty($content) ? esc_html($content) : 'Submit Form';
                return '<div class="wppoppop-layer-item wppoppop-element wppoppop-el-button ' . $anim_class . '" style="' . $style_attr . '"><button type="button" class="wppoppop-submit-trigger wppoppop-submit-btn" style="width:100%;height:100%;background:#c2185b;color:#ffffff;border:none;border-radius:inherit;font-weight:700;font-size:13px;cursor:pointer;">' . $btn_text . '</button></div>';

            case 'pay':
                $btn_text = !empty($content) ? esc_html($content) : 'Pay Now';
                return '<div class="wppoppop-layer-item wppoppop-element ' . $anim_class . '" style="' . $style_attr . '"><button type="button" class="wppoppop-pay-trigger" data-amount="19.99" data-currency="USD" style="width:100%;height:100%;background:#059669;color:#ffffff;border:none;border-radius:inherit;font-weight:700;font-size:13px;cursor:pointer;">' . $btn_text . '</button></div>';

            case 'html':
                return '<div class="wppoppop-layer-item wppoppop-element ' . $anim_class . '" style="' . $style_attr . ';overflow:hidden;">' . $content . '</div>';

            default:
                return '<div class="wppoppop-layer-item wppoppop-element ' . $anim_class . '" style="' . $style_attr . '">' . esc_html($content) . '</div>';
        }
    }
}
