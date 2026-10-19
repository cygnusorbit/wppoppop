<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Front_Renderer {

    public function render_popup_markup($uid, array $config, $is_inline = false, $preload = false) {
        $box = $config['box'] ?? ($config['settings']['box'] ?? []);
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

        $trg = (isset($config['settings']['triggers']) && is_array($config['settings']['triggers'])) ? $config['settings']['triggers'] : [];
        $trg_on = function ($k) use ($trg) { return !empty($trg[$k]) && $trg[$k] !== 'false' && $trg[$k] !== '0'; };
        $trg_attrs = '';
        if ($trg_on('load'))   { $trg_attrs .= ' data-trigger-load="1" data-trigger-load-delay="' . max(0, intval($trg['load_delay'] ?? 0)) . '"'; }
        if ($trg_on('exit'))   { $trg_attrs .= ' data-trigger-exit="1" data-exit-sens="' . max(1, intval($config['settings']['exit_intent_sensitivity'] ?? 20)) . '"'; }
        if ($trg_on('scroll')) { $trg_attrs .= ' data-trigger-scroll="' . max(1, min(100, intval($trg['scroll_val'] ?? ($config['settings']['scroll_depth'] ?? 50)))) . '"'; }
        if ($trg_on('adblock')) { $trg_attrs .= ' data-trigger-adblock="1"'; }
        if ($trg_on('backbutton') || $trg_on('back')) { $trg_attrs .= ' data-trigger-back="1"'; }
        if ($trg_on('idle'))   { $trg_attrs .= ' data-trigger-idle="' . max(1, intval($trg['idle_seconds'] ?? ($trg['idle_time'] ?? 15))) . '"'; }

        // Settings Integrations
        $default_font   = function_exists('wppoppop_get_setting') ? wppoppop_get_setting('default_font', 'inherit') : 'inherit';
        $show_watermark = function_exists('wppoppop_get_setting') ? (bool) wppoppop_get_setting('powered_by_badge', false) : false;
        $minify_css     = function_exists('wppoppop_get_setting') ? (bool) wppoppop_get_setting('minify_css', false) : false;

        $custom_inline_css = "
        #wppoppop-popup-{$uid}, #wppoppop-wrap-{$uid} {
            font-family: {$default_font};
        }
        ";

        // WPPOPPOP_CANVAS_OVERFLOW: let the box show overflow only when a canvas allows it
        $wppoppop_any_overflow = false;
        if (is_array($canvas_meta)) {
            foreach ($canvas_meta as $wppoppop_cm_chk) {
                if (is_array($wppoppop_cm_chk) && !empty($wppoppop_cm_chk['allow_overflow']) && $wppoppop_cm_chk['allow_overflow'] !== 'false') {
                    $wppoppop_any_overflow = true;
                    break;
                }
            }
        }
        if ($wppoppop_any_overflow) {
            $custom_inline_css .= "
        #wppoppop-popup-{$uid} .wppoppop-box { overflow: visible !important; }
        ";
        }

        if ($minify_css && function_exists('wppoppop_minify_css')) {
            $custom_inline_css = wppoppop_minify_css($custom_inline_css);
        }

        $overlay_style = $is_inline ? 'display:block;position:relative;' : 'display:none;position:fixed;inset:0;z-index:999999;';
        $preload_class = $preload ? 'wppoppop-preloaded' : '';
        ?>
        <div class="wppoppop-overlay wppoppop-popup-wrap <?php echo $preload_class; ?>"
             id="wppoppop-popup-<?php echo esc_attr($uid); ?>"
             data-uid="<?php echo esc_attr($uid); ?>"
             data-config="<?php echo $encoded_config; ?>"<?php echo $trg_attrs; ?>
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
                    $c_allow_ovf = !empty($cm['allow_overflow']) && $cm['allow_overflow'] !== 'false';
                    $c_overflow_style = $c_allow_ovf ? 'overflow:visible;' : 'overflow:hidden;';

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
                         data-anim-exit="<?php echo (isset($cm['anim_exit_enabled']) && in_array($cm['anim_exit_enabled'], array(false, 0, '0', 'false'), true)) ? '0' : '1'; ?>"
                         data-allow-overflow="<?php echo $c_allow_ovf ? '1' : '0'; ?>"
                         style="<?php echo esc_attr($display_style . $c_bg_style . $c_overflow_style); ?>width:100%;min-height:<?php echo esc_attr($c_height); ?>px;position:relative;">

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
        $fill = !empty($fill) ? esc_attr($fill) : '#3b82f6'; // 'none' allowed for transparent fill
        $stroke = !empty($stroke) ? esc_attr($stroke) : 'transparent';
        $stroke_width = intval($stroke_width);
        $rotate = intval($rotate);

        $rot_css = $rotate ? sprintf('transform:rotate(%ddeg);-webkit-transform:rotate(%ddeg);', $rotate, $rotate) : '';
        $svg_style = sprintf('width:100%%;height:100%%;display:block;overflow:visible;%s', $rot_css);
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
            case 'rectangle':
            case 'rect':
                $path = sprintf('<rect x="2" y="2" width="96" height="96" fill="%s" %s />', $fill, $s_attr);
                break;
            case 'rounded':
                $path = sprintf('<rect x="2" y="2" width="96" height="96" rx="16" ry="16" fill="%s" %s />', $fill, $s_attr);
                break;
            case 'pill':
                $path = sprintf('<rect x="2" y="15" width="96" height="70" rx="35" ry="35" fill="%s" %s />', $fill, $s_attr);
                break;
            case 'pentagon':
                $path = sprintf('<polygon points="50,3 97,38 79,94 21,94 3,38" fill="%s" %s stroke-linejoin="round" />', $fill, $s_attr);
                break;
            case 'badge':
                $path = sprintf('<polygon points="50,2 62,13 78,10 83,25 97,33 93,49 99,64 86,74 83,90 67,91 56,99 44,91 28,90 25,74 12,64 18,49 14,33 28,25 33,10 48,13" fill="%s" %s stroke-linejoin="round" />', $fill, $s_attr);
                break;
            case 'circle':
            default:
                $path = sprintf('<ellipse cx="50" cy="50" rx="46" ry="46" fill="%s" %s />', $fill, $s_attr);
                break;
        }

        return sprintf('<svg viewBox="0 0 100 100" preserveAspectRatio="none" style="%s">%s</svg>', esc_attr($svg_style), $path);
    }

    private function render_canvas_element(array $el, array $config = array()) {
        /* WPPOPPOP_LOAD_DECOMPOSED: the autoloader cannot find elements/class-front-decomposed-26.php */
        if (!class_exists('WpPopPop_Front_Decomposed_26')) {
            $wppoppop_dec_file = WPPOPPOP_PATH . 'includes/front/elements/class-front-decomposed-26.php';
            if (file_exists($wppoppop_dec_file)) { require_once $wppoppop_dec_file; }
        }
        $el_id = !empty($el['id']) ? sanitize_html_class($el['id']) : uniqid('el_');
        $custom_class = !empty($el['custom_css_class']) ? esc_attr($el['custom_css_class']) : '';
        $custom_id = !empty($el['custom_css_id']) ? (' id="' . esc_attr($el['custom_css_id']) . '"') : '';
        
        // Build Box Shadow style
        $box_shadow_val = !empty($el['boxShadow']) ? $el['boxShadow'] : '';
        if (empty($box_shadow_val) && (!empty($el['shadowBlur']) || !empty($el['shadowSpread']) || !empty($el['shadowX']) || !empty($el['shadowY']))) {
            $sx = isset($el['shadowX']) ? intval($el['shadowX']) : 0;
            $sy = isset($el['shadowY']) ? intval($el['shadowY']) : 0;
            $sb = isset($el['shadowBlur']) ? intval($el['shadowBlur']) : 0;
            $ss = isset($el['shadowSpread']) ? intval($el['shadowSpread']) : 0;
            $sc = !empty($el['shadowColor']) ? esc_attr($el['shadowColor']) : 'rgba(0,0,0,0.2)';
            $box_shadow_val = "{$sx}px {$sy}px {$sb}px {$ss}px {$sc}";
        }

        // Opacity
        $opacity_style = (isset($el['opacity']) && floatval($el['opacity']) < 1.0) ? ('opacity: ' . floatval($el['opacity']) . ';') : '';

        // Dynamic Button Hover Style Injection
        $hover_style_tag = '';
        $hover_bg = !empty($el['hoverBg']) ? $el['hoverBg'] : (!empty($el['btnHoverBg']) ? $el['btnHoverBg'] : (!empty($el['hoverBgColor']) ? $el['hoverBgColor'] : ''));
        $hover_color = !empty($el['hoverColor']) ? $el['hoverColor'] : (!empty($el['btnHoverColor']) ? $el['btnHoverColor'] : (!empty($el['hoverTextColor']) ? $el['hoverTextColor'] : ''));
        $hover_border = !empty($el['hoverBorder']) ? $el['hoverBorder'] : (!empty($el['btnHoverBorder']) ? $el['btnHoverBorder'] : (!empty($el['hoverBorderColor']) ? $el['hoverBorderColor'] : ''));
        if (!empty($hover_bg) || !empty($hover_color) || !empty($hover_border)) {
            $hover_rules = '';
            if (!empty($hover_bg)) $hover_rules .= 'background-color: ' . esc_attr($hover_bg) . ' !important;';
            if (!empty($hover_color)) $hover_rules .= 'color: ' . esc_attr($hover_color) . ' !important;';
            if (!empty($hover_border)) $hover_rules .= 'border-color: ' . esc_attr($hover_border) . ' !important;';
            $hover_style_tag = '<style>#el-' . $el_id . ':hover, #el-' . $el_id . ' button:hover, #el-' . $el_id . ' a:hover { ' . $hover_rules . ' }</style>';
        }
        $type = $el['type'] ?? 'text';
        /* WPPOPPOP_AC_ATTR */
        $wppoppop_ac_val = !empty($el['autocomplete']) ? preg_replace('/[^a-z0-9 \-]/', '', strtolower((string) $el['autocomplete'])) : '';
        $wppoppop_ac_attr = $wppoppop_ac_val !== '' ? ' autocomplete="' . esc_attr($wppoppop_ac_val) . '"' : '';
        $wrapper_styles = array();
        $wrapper_classes = array();
        // Phase 2: Opacity, Custom Class & Button Hover Properties
        if (isset($el['opacity']) && $el['opacity'] !== '' && $el['opacity'] !== null) {
            $wrapper_styles[] = 'opacity:' . (floatval($el['opacity']) / 100) . ';';
        }
        if (!empty($el['customClass'])) {
            $wrapper_classes[] = sanitize_html_class($el['customClass']);
        } elseif (!empty($el['custom_class'])) {
            $wrapper_classes[] = sanitize_html_class($el['custom_class']);
        }
        if (in_array($type, ['submit', 'link', 'step', 'pay', 'close', 'close_icon'], true)) {
            $hover_bg = !empty($el['hoverBgColor']) ? $el['hoverBgColor'] : (!empty($el['hover_bg_color']) ? $el['hover_bg_color'] : '');
            $hover_color = !empty($el['hoverTextColor']) ? $el['hoverTextColor'] : (!empty($el['hover_text_color']) ? $el['hover_text_color'] : '');
            $hover_border = !empty($el['hoverBorderColor']) ? $el['hoverBorderColor'] : (!empty($el['hover_border_color']) ? $el['hover_border_color'] : '');
            if (!empty($hover_bg)) {
                $wrapper_styles[] = '--wppoppop-hover-bg:' . esc_attr($hover_bg) . ';';
            }
            if (!empty($hover_color)) {
                $wrapper_styles[] = '--wppoppop-hover-color:' . esc_attr($hover_color) . ';';
            }
            if (!empty($hover_border)) {
                $wrapper_styles[] = '--wppoppop-hover-border:' . esc_attr($hover_border) . ';';
            }
        }

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

        $shape_rot = 0;
        foreach (array('rotate', 'shapeRotate', 'rotation') as $rk) {
            if (!empty($el[$rk])) { $shape_rot = intval($el[$rk]); break; }
        }
        if ($shape_rot !== 0) {
            $style[] = "transform:rotate({$shape_rot}deg)";
        }

        $style_attr = esc_attr(implode(';', $style));
                $anim_name = '';
        $fx_map = array('fade' => 'fadeIn', 'slideDown' => 'slideInDown', 'slideUp' => 'slideInUp', 'slideLeft' => 'slideInRight', 'slideRight' => 'slideInLeft', 'zoom' => 'zoomIn', 'flip' => 'flipInX');
        foreach (array('animIn', 'anim_appearance', 'animation', 'animEffect', 'anim_effect') as $anim_key) {
            if (!empty($el[$anim_key]) && $el[$anim_key] !== 'none') {
                $raw_fx = preg_replace('/^(animate__|wppoppop-anim-|anim-)/', '', (string) $el[$anim_key]);
                $raw_fx = sanitize_html_class($raw_fx);
                $anim_name = isset($fx_map[$raw_fx]) ? $fx_map[$raw_fx] : $raw_fx;
                break;
            }
        }
        $anim_class = ($anim_name !== '') ? 'wppoppop-fx-run wppoppop-fx-' . $anim_name : '';
        $anim_style = '';
        if ($anim_class !== '') {
            $a_dur = 1000;
            foreach (array('animDuration', 'anim_duration', 'duration') as $dk) {
                if (isset($el[$dk]) && $el[$dk] !== '' && is_numeric($el[$dk])) { $a_dur = max(0, intval($el[$dk])); break; }
            }
            $a_del = 0;
            foreach (array('animDelay', 'anim_delay', 'delay') as $dk) {
                if (isset($el[$dk]) && $el[$dk] !== '' && is_numeric($el[$dk])) { $a_del = max(0, intval($el[$dk])); break; }
            }
            if ($a_dur < 1) { $a_dur = 1; }
            $anim_style .= ';-webkit-animation-duration:' . $a_dur . 'ms;animation-duration:' . $a_dur . 'ms';
            $anim_style .= ';-webkit-animation-delay:' . $a_del . 'ms;animation-delay:' . $a_del . 'ms';
        }
        $style_attr = esc_attr(implode(';', $style) . $anim_style);

        switch ($type) {
            case 'divider':
                $d_style = !empty($el['dividerStyle']) ? sanitize_text_field($el['dividerStyle']) : 'solid';
                $d_weight = !empty($el['dividerWeight']) ? intval($el['dividerWeight']) : 2;
                if ($d_style === 'double' && $d_weight < 3) $d_weight = 4;
                $d_color = !empty($el['dividerColor']) ? sanitize_text_field($el['dividerColor']) : '#94a3b8';
                $d_pct = !empty($el['dividerWidthPct']) ? intval($el['dividerWidthPct']) : 100;
                $d_align = !empty($el['dividerAlign']) ? sanitize_text_field($el['dividerAlign']) : 'center';
                $margin = 'margin:0 auto;';
                if ($d_align === 'left') $margin = 'margin:0 auto 0 0;';
                if ($d_align === 'right') $margin = 'margin:0 0 0 auto;';
                $inner_html = '<div style="width:100%;height:100%;display:flex;align-items:center;box-sizing:border-box;"><hr style="width:' . $d_pct . '%;border:none;border-top:' . $d_weight . 'px ' . esc_attr($d_style) . ' ' . esc_attr($d_color) . ';' . $margin . '" /></div>';
                break;

            case 'icon':
            case 'fontawesome':
                /* WPPOPPOP_ICON_CASE_V2: normalise class, load icon CSS, size the glyph */
                $i_raw = !empty($el['iconClass']) ? $el['iconClass'] : (!empty($el['iconGlyph']) ? $el['iconGlyph'] : (!empty($el['content']) ? $el['content'] : 'dashicons-star-filled'));
                $i_tokens = array_values(array_filter(array_map('sanitize_html_class', preg_split('/\s+/', trim((string) $i_raw)))));
                if (empty($i_tokens)) { $i_tokens = array('dashicons-star-filled'); }
                $i_is_dash = false; $i_is_fa = false; $i_has_base = false;
                foreach ($i_tokens as $i_t) {
                    if (strpos($i_t, 'dashicons-') === 0 || $i_t === 'dashicons') { $i_is_dash = true; }
                    if (preg_match('/^(fa|fas|far|fab|fal|fad|fa-solid|fa-regular|fa-brands)$/', $i_t) || strpos($i_t, 'fa-') === 0) { $i_is_fa = true; }
                    if (in_array($i_t, array('dashicons','fa','fas','far','fab','fal','fad','fa-solid','fa-regular','fa-brands'), true)) { $i_has_base = true; }
                }
                if ($i_is_dash && !in_array('dashicons', $i_tokens, true)) { array_unshift($i_tokens, 'dashicons'); }
                elseif ($i_is_fa && !$i_has_base) { array_unshift($i_tokens, 'fas'); }
                /* WPPOPPOP_ICON_CSS_INLINE: footer enqueue is too late, print the link next to the icon once */
                static $wppoppop_dash_css_done = false, $wppoppop_fa_css_done = false;
                $i_css_links = '';
                if ($i_is_dash && !$wppoppop_dash_css_done) {
                    $wppoppop_dash_css_done = true;
                    $i_css_links .= '<link rel="stylesheet" href="' . esc_url(includes_url('css/dashicons.min.css')) . '" />';
                }
                if ($i_is_fa && !$wppoppop_fa_css_done) {
                    $wppoppop_fa_css_done = true;
                    $i_css_links .= '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />';
                }
                $i_color = !empty($el['iconColor']) ? sanitize_text_field($el['iconColor']) : (!empty($el['color']) ? sanitize_text_field($el['color']) : '#38bdf8');
                $i_size = !empty($el['iconSize']) ? intval($el['iconSize']) : 36;
                $i_rot = !empty($el['rotate']) ? intval($el['rotate']) : 0;
                $i_rot_css = $i_rot ? 'transform:rotate(' . $i_rot . 'deg);' : '';
                $i_box = $i_is_dash ? ('width:' . $i_size . 'px;height:' . $i_size . 'px;') : '';
                $inner_html = $i_css_links . '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:' . esc_attr($i_color) . ';pointer-events:none;">'
                    . '<i class="' . esc_attr(implode(' ', $i_tokens)) . '" aria-hidden="true" style="font-size:' . $i_size . 'px;' . $i_box . 'line-height:1;display:inline-block;' . $i_rot_css . '"></i></div>';
                break;

            case 'label':
            case 'text':
                
        $decomposed_types = array('label', 'button', 'text', 'phone', 'tel', 'textarea', 'multiselect', 'imageselect', 'tile', 'time', 'upload', 'file', 'password', 'rating');$raw_el_type = isset($el['type']) ? strtolower(trim($el['type'])) : '';
        if (in_array($raw_el_type, $decomposed_types, true) && class_exists('WpPopPop_Front_Decomposed_26')) {$decomposed_inner = WpPopPop_Front_Decomposed_26::render_element($raw_el_type,$el);
            if ($decomposed_inner !== null) {
                $inner_html =$decomposed_inner;
            }
        }

        if (isset($inner_html) && $inner_html !== '') {
            /* WPPOPPOP_TF_NOPAD_FRONT: Text Field wrapper never carries padding (input fills the layer box) */
            $tf_wrap_pad = in_array($raw_el_type, array('text', 'phone', 'tel', 'textarea', 'multiselect'), true) ? ';padding:0!important' : ''; /* WPPOPPOP_MULTISELECT_WRAP */
            $tf_wrap_cls = in_array($raw_el_type, array('text', 'phone', 'tel', 'textarea'), true) ? ' wppoppop-el-textfield' : '';
            return '<div class="wppoppop-layer-item wppoppop-element wppoppop-el-text' . $tf_wrap_cls . ' ' . $anim_class . '" style="' . $style_attr . $tf_wrap_pad . '">' . $inner_html . '</div>';
        }

        return '<div class="wppoppop-layer-item wppoppop-element wppoppop-el-text ' . $anim_class . '" style="' . $style_attr . '"><span class="wppoppop-text-render">' . wp_kses_post($content) . '</span></div>';

            case 'video':
                if (!class_exists('WpPopPop_Front_Element_Media')) {
                    require_once WPPOPPOP_PATH . 'includes/front/elements/class-front-element-media.php';
                }
                $wppoppop_v_inner = WpPopPop_Front_Element_Media::render_video($el);
                return '<div class="wppoppop-layer-item wppoppop-element wppoppop-el-video ' . $anim_class . '" style="' . $style_attr . ';overflow:hidden;">' . $wppoppop_v_inner . '</div>';
            case 'image':
                $img_src = esc_url($el['imgUrl'] ?? ($el['imageUrl'] ?? ($el['src'] ?? ($el['content'] ?? ''))));
                $img_fit_raw = $el['objectFit'] ?? ($el['imageFit'] ?? 'cover');
                $img_fit = esc_attr(in_array($img_fit_raw, array('cover', 'contain', 'fill', 'none', 'scale-down'), true) ? $img_fit_raw : 'cover');
                $img_alt = esc_attr($el['altText'] ?? ($el['alt'] ?? ($el['imageAlt'] ?? '')));
                $inner_html = "<img src=\"{$img_src}\" alt=\"{$img_alt}\" style=\"width:100%;height:100%;object-fit:{$img_fit};display:block;border-radius:inherit;\" />";
                break;
            case 'shape':
                $preset = !empty($el['shapePreset']) ? $el['shapePreset'] : (!empty($el['content']) ? $el['content'] : 'circle');
                $fill = !empty($el['shapeNoFill']) ? 'none' : esc_attr(!empty($el['shapeFill']) ? $el['shapeFill'] : '#3b82f6');
                $stroke = esc_attr(!empty($el['strokeColor']) ? $el['strokeColor'] : (!empty($el['shapeStroke']) ? $el['shapeStroke'] : 'transparent'));
                $stroke_width = intval($el['strokeWidth'] ?? ($el['shapeStrokeWidth'] ?? 0));
                $rotate = !empty($el['rotate']) ? intval($el['rotate']) : (!empty($el['shapeRotate']) ? intval($el['shapeRotate']) : intval($el['rotation'] ?? 0));
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
                if (!empty($el['bgColor']) && empty($el['transparentFill'])) { $shape_style[] = 'background-color:' . sanitize_text_field($el['bgColor']); }
                $shape_style[] = 'border-radius:' . $bRad . 'px';
                $shape_style_attr = esc_attr(implode(';', $shape_style) . $anim_style);
                return '<div class="wppoppop-layer-item wppoppop-element wppoppop-el-shape ' . $anim_class . '" style="' . $shape_style_attr . '">' . $svg . '</div>';

            case 'email':
                /* WPPOPPOP_EMAIL_DECOMPOSED */
                if (!class_exists('WpPopPop_Front_Decomposed_26')) {
                    $wppoppop_em_file = WPPOPPOP_PATH . 'includes/front/elements/class-front-decomposed-26.php';
                    if (file_exists($wppoppop_em_file)) { require_once $wppoppop_em_file; }
                }
                if (class_exists('WpPopPop_Front_Decomposed_26')) {
                    $wppoppop_em_inner = WpPopPop_Front_Decomposed_26::render_element('email', $el);
                    if ($wppoppop_em_inner !== null && $wppoppop_em_inner !== '') {
                        return '<div class="wppoppop-layer-item wppoppop-element wppoppop-el-email wppoppop-el-textfield ' . $anim_class . '" style="' . $style_attr . ';padding:0!important">' . $wppoppop_em_inner . '</div>';
                    }
                }
                $ph = !empty($content) ? esc_attr($content) : 'Enter your email...';
                return '<div class="wppoppop-layer-item wppoppop-element wppoppop-el-email ' . $anim_class . '" style="' . $style_attr . '"><input type="email" name="email"' . $wppoppop_ac_attr . ' class="wppoppop-field-email" placeholder="' . $ph . '" required style="width:100%;height:100%;padding:0 10px;border:1px solid #cbd5e1;border-radius:inherit;box-sizing:border-box;"></div>';

            case 'number':
                /* WPPOPPOP_NUMBER_DECOMPOSED */
                if (!class_exists('WpPopPop_Front_Decomposed_26')) {
                    $wppoppop_nm_file = WPPOPPOP_PATH . 'includes/front/elements/class-front-decomposed-26.php';
                    if (file_exists($wppoppop_nm_file)) { require_once $wppoppop_nm_file; }
                }
                if (class_exists('WpPopPop_Front_Decomposed_26')) {
                    $wppoppop_nm_inner = WpPopPop_Front_Decomposed_26::render_element('number', $el);
                    if ($wppoppop_nm_inner !== null && $wppoppop_nm_inner !== '') {
                        return '<div class="wppoppop-layer-item wppoppop-element wppoppop-el-number wppoppop-el-textfield ' . $anim_class . '" style="' . $style_attr . ';padding:0!important">' . $wppoppop_nm_inner . '</div>';
                    }
                }
                $ph = !empty($content) ? esc_attr($content) : 'Enter number...';
                return '<div class="wppoppop-layer-item wppoppop-element ' . $anim_class . '" style="' . $style_attr . '"><input type="number" name="number_field"' . $wppoppop_ac_attr . ' placeholder="' . $ph . '" style="width:100%;height:100%;padding:0 10px;border:1px solid #cbd5e1;border-radius:inherit;box-sizing:border-box;"></div>';

            case 'select':
            case 'dropdown':
                /* WPPOPPOP_SELECT_DECOMPOSED */
                if (!class_exists('WpPopPop_Front_Decomposed_26')) {
                    $wppoppop_sl_file = WPPOPPOP_PATH . 'includes/front/elements/class-front-decomposed-26.php';
                    if (file_exists($wppoppop_sl_file)) { require_once $wppoppop_sl_file; }
                }
                if (class_exists('WpPopPop_Front_Decomposed_26')) {
                    $wppoppop_sl_inner = WpPopPop_Front_Decomposed_26::render_element('select', $el);
                    if ($wppoppop_sl_inner !== null && $wppoppop_sl_inner !== '') {
                        return '<div class="wppoppop-layer-item wppoppop-element wppoppop-el-select wppoppop-el-textfield ' . $anim_class . '" style="' . $style_attr . ';padding:0!important">' . $wppoppop_sl_inner . '</div>';
                    }
                }
                $opts = array_map('trim', explode(',', $content ?: 'Option 1, Option 2'));
                $html = '<div class="wppoppop-layer-item wppoppop-element ' . $anim_class . '" style="' . $style_attr . '"><select name="dropdown_field" style="width:100%;height:100%;padding:0 8px;border:1px solid #cbd5e1;border-radius:inherit;box-sizing:border-box;">';
                foreach ($opts as $o) {
                    $html .= '<option value="' . esc_attr($o) . '">' . esc_html($o) . '</option>';
                }
                $html .= '</select></div>';
                return $html;

            case 'radio':
            case 'radios':
                /* WPPOPPOP_RADIO_DECOMPOSED */
                if (!class_exists('WpPopPop_Front_Decomposed_26')) {
                    $wppoppop_rd_file = WPPOPPOP_PATH . 'includes/front/elements/class-front-decomposed-26.php';
                    if (file_exists($wppoppop_rd_file)) { require_once $wppoppop_rd_file; }
                }
                if (class_exists('WpPopPop_Front_Decomposed_26')) {
                    $wppoppop_rd_inner = WpPopPop_Front_Decomposed_26::render_element('radio', $el);
                    if ($wppoppop_rd_inner !== null && $wppoppop_rd_inner !== '') {
                        return '<div class="wppoppop-layer-item wppoppop-element wppoppop-el-radio ' . $anim_class . '" style="' . $style_attr . ';padding:0!important">' . $wppoppop_rd_inner . '</div>';
                    }
                }
                $items = array_map('trim', explode(',', $content ?: 'Choice A, Choice B'));
                $html = '<div class="wppoppop-layer-item wppoppop-element ' . $anim_class . '" style="' . $style_attr . ';display:flex;align-items:center;gap:12px;font-size:12px;">';
                foreach ($items as $idx => $it) {
                    $html .= '<label style="cursor:pointer;"><input type="radio" name="radio_choice" value="' . esc_attr($it) . '" ' . ($idx === 0 ? 'checked' : '') . '> ' . esc_html($it) . '</label>';
                }
                $html .= '</div>';
                return $html;

            case 'checkbox':
            case 'checkboxes':
                /* WPPOPPOP_CHECKBOX_DECOMPOSED */
                if (!class_exists('WpPopPop_Front_Decomposed_26')) {
                    $wppoppop_cb_file = WPPOPPOP_PATH . 'includes/front/elements/class-front-decomposed-26.php';
                    if (file_exists($wppoppop_cb_file)) { require_once $wppoppop_cb_file; }
                }
                if (class_exists('WpPopPop_Front_Decomposed_26')) {
                    $wppoppop_cb_inner = WpPopPop_Front_Decomposed_26::render_element('checkbox', $el);
                    if ($wppoppop_cb_inner !== null && $wppoppop_cb_inner !== '') {
                        return '<div class="wppoppop-layer-item wppoppop-element wppoppop-el-checkbox ' . $anim_class . '" style="' . $style_attr . ';padding:0!important' . (!empty($box_shadow_val) ? ';box-shadow:' . esc_attr($box_shadow_val) : '') . '">' . $wppoppop_cb_inner . '</div>';
                    }
                }
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
                /* WPPOPPOP_PROGRESS_CASE: real value / colours / stripes / label */
                $pg_pct = 65;
                foreach (array('progressValue', 'progressPercent', 'progressVal') as $pg_k) {
                    if (isset($el[$pg_k]) && $el[$pg_k] !== '' && is_numeric($el[$pg_k])) { $pg_pct = max(0, min(100, (int) round((float) $el[$pg_k]))); break; }
                }
                $pg_on = function ($keys, $def) use ($el) {
                    foreach ($keys as $k) { if (isset($el[$k])) { return !empty($el[$k]) && $el[$k] !== 'false' && $el[$k] !== '0'; } }
                    return $def;
                };
                $pg_hex = function ($v, $d) { return (is_string($v) && preg_match('/^#[0-9a-fA-F]{3,8}$/', $v)) ? $v : $d; };
                $pg_fill = $pg_hex(!empty($el['fillColor']) ? $el['fillColor'] : (!empty($el['progressColor']) ? $el['progressColor'] : ''), '#10b981');
                $pg_track = $pg_hex(!empty($el['trackColor']) ? $el['trackColor'] : '', '#334155');
                $pg_striped = $pg_on(array('progressStriped', 'striped'), false);
                $pg_anim = $pg_striped && $pg_on(array('progressAnimated'), false);
                $pg_showpct = $pg_on(array('showPercentLabel'), true);
                $pg_lbl = isset($el['progressLabel']) ? trim((string) $el['progressLabel']) : '';
                /* WPPOPPOP_PROGRESS_MODE */
                $pg_mode = (isset($el['progressMode']) && in_array($el['progressMode'], array('form', 'steps'), true)) ? $el['progressMode'] : 'manual';
                if ($pg_mode === 'form') { $pg_pct = 0; }
                $pg_badge = $pg_lbl !== '' ? ($pg_lbl . ($pg_showpct ? ' (' . $pg_pct . '%)' : '')) : ($pg_showpct ? ($pg_pct . '%') : '');
                $pg_stripe = $pg_striped ? 'background-image:linear-gradient(45deg,rgba(255,255,255,.2) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.2) 50%,rgba(255,255,255,.2) 75%,transparent 75%,transparent);background-size:20px 20px;' : '';
                static $pg_css_done = false;
                $pg_css = '';
                if (!$pg_css_done) {
                    $pg_css_done = true;
                    $pg_css = '<style>@-webkit-keyframes wppProgStripe{from{background-position:20px 0}to{background-position:0 0}}@keyframes wppProgStripe{from{background-position:20px 0}to{background-position:0 0}}.wppoppop-prog-anim{-webkit-animation:wppProgStripe 1s linear infinite;animation:wppProgStripe 1s linear infinite}</style>';
                }
                return $pg_css . '<div class="wppoppop-layer-item wppoppop-element wppoppop-el-progress ' . $anim_class . '" data-progress-mode="' . esc_attr($pg_mode) . '" data-progress-label="' . esc_attr($pg_lbl) . '" data-progress-showpct="' . ($pg_showpct ? '1' : '0') . '" style="' . $style_attr . ';overflow:hidden;">'
                    . '<div style="width:100%;height:100%;background:' . esc_attr($pg_track) . ';border-radius:inherit;overflow:hidden;position:relative;display:flex;align-items:center;">'
                    . '<div class="wppoppop-progress-fill' . ($pg_anim ? ' wppoppop-prog-anim' : '') . '" style="width:' . $pg_pct . '%;height:100%;background-color:' . esc_attr($pg_fill) . ';' . $pg_stripe . 'transition:width .3s ease;"></div>'
                    . ($pg_badge !== '' ? '<span class="wppoppop-progress-badge" style="position:absolute;left:0;right:0;text-align:center;font-size:11px;font-weight:700;color:#fff;text-shadow:0 1px 2px rgba(0,0,0,.45);">' . esc_html($pg_badge) . '</span>' : '')
                    . '</div></div>';

            case 'file':
                return '<div class="wppoppop-layer-item wppoppop-element ' . $anim_class . '" style="' . $style_attr . ';display:flex;align-items:center;justify-content:center;border:1px dashed #cbd5e1;"><input type="file" name="uploaded_file" style="font-size:11px;width:95%;"></div>';

            case 'step_btn':
                $target_canvas = !empty($el['goto_canvas']) ? intval($el['goto_canvas']) : (!empty($el['goto_screen']) ? intval($el['goto_screen']) : 2);
                $btn_text = !empty($content) ? esc_html($content) : 'Next Canvas &rarr;';
                return '<div class="wppoppop-layer-item wppoppop-element ' . $anim_class . '" style="' . $style_attr . '"><button type="button" class="wppoppop-next-canvas-btn wppoppop-next-screen-btn wppoppop-next-step" data-goto-canvas="' . esc_attr($target_canvas) . '" data-goto-screen="' . esc_attr($target_canvas) . '" data-goto="' . esc_attr($target_canvas) . '" style="width:100%;height:100%;background:#2563eb;color:#ffffff;border:none;border-radius:inherit;font-weight:700;font-size:13px;cursor:pointer;">' . $btn_text . '</button></div>';

            case 'button':
            case 'link':
            case 'link_btn':
                /* WPPOPPOP_BUTTON_CASE: honour label, click action, new tab, nofollow and visual style */
                $bt_label = (isset($el['content']) && trim((string) $el['content']) !== '') ? esc_html($el['content']) : 'Click Here';
                $bt_act = isset($el['buttonAction']) ? (string) $el['buttonAction'] : 'link';
                if ($bt_act === 'url') { $bt_act = 'link'; }
                if ($bt_act === 'goto_canvas') { $bt_act = 'step'; }
                $bt_on = function ($keys) use ($el) {
                    foreach ($keys as $k) { if (isset($el[$k])) { return !empty($el[$k]) && $el[$k] !== 'false' && $el[$k] !== '0'; } }
                    return false;
                };
                $bt_blank = $bt_on(array('linkBlank', 'targetBlank'));
                $bt_nofollow = $bt_on(array('nofollow'));
                $bt_url = !empty($el['linkUrl']) ? esc_url($el['linkUrl']) : (!empty($el['url']) ? esc_url($el['url']) : '#');
                if ($bt_url === '') { $bt_url = '#'; }
                $bt_step = !empty($el['goto_canvas']) ? intval($el['goto_canvas']) : (!empty($el['goto_screen']) ? intval($el['goto_screen']) : 2);
                $bt_x = array();
                if (!empty($el['fontWeight']) && preg_match('/^[0-9a-z]{3,10}$/i', (string) $el['fontWeight'])) { $bt_x[] = 'font-weight:' . $el['fontWeight']; }
                if (isset($el['letterSpacing']) && is_numeric($el['letterSpacing']) && floatval($el['letterSpacing']) != 0) { $bt_x[] = 'letter-spacing:' . floatval($el['letterSpacing']) . 'px'; }
                if (!empty($el['lineHeight']) && is_numeric($el['lineHeight'])) { $bt_x[] = 'line-height:' . floatval($el['lineHeight']); }
                if (!empty($el['borderWidth']) && intval($el['borderWidth']) > 0) {
                    $bt_bs = (!empty($el['borderStyle']) && in_array($el['borderStyle'], array('solid', 'dashed', 'dotted', 'double'), true)) ? $el['borderStyle'] : 'solid';
                    $bt_bc = (!empty($el['borderColor']) && preg_match('/^#[0-9a-fA-F]{3,8}$/', $el['borderColor'])) ? $el['borderColor'] : '#334155';
                    $bt_x[] = 'border:' . intval($el['borderWidth']) . 'px ' . $bt_bs . ' ' . $bt_bc;
                }
                if (!empty($box_shadow_val)) { $bt_x[] = 'box-shadow:' . $box_shadow_val; }
                $bt_just = 'center';
                if (!empty($el['textAlign'])) {
                    if ($el['textAlign'] === 'left') { $bt_just = 'flex-start'; }
                    elseif ($el['textAlign'] === 'right') { $bt_just = 'flex-end'; }
                }
                $bt_x[] = 'transition:background-color .15s ease,color .15s ease,border-color .15s ease';
                $bt_wrap_style = esc_attr(implode(';', $style) . $anim_style . ';' . implode(';', $bt_x));
                $bt_in = 'width:100%;height:100%;display:flex;align-items:center;justify-content:' . $bt_just . ';box-sizing:border-box;margin:0;padding:0 12px;border:inherit;border-radius:inherit;background:inherit;color:inherit;font:inherit;letter-spacing:inherit;line-height:inherit;text-decoration:none;cursor:pointer;outline:none;-webkit-appearance:none;appearance:none;';
                if ($bt_act === 'step') {
                    $bt_html = '<button type="button" class="wppoppop-front-btn wppoppop-next-canvas-btn wppoppop-next-screen-btn" data-goto-canvas="' . $bt_step . '" data-goto-screen="' . $bt_step . '" data-goto="' . $bt_step . '" style="' . $bt_in . '">' . $bt_label . '</button>';
                } elseif ($bt_act === 'close') {
                    $bt_html = '<button type="button" class="wppoppop-front-btn wppoppop-close-trigger" data-close-action="close" style="' . $bt_in . '">' . $bt_label . '</button>';
                } else {
                    $bt_rel = array();
                    if ($bt_blank) { $bt_rel[] = 'noopener'; $bt_rel[] = 'noreferrer'; }
                    if ($bt_nofollow) { $bt_rel[] = 'nofollow'; }
                    $bt_html = '<a href="' . $bt_url . '" class="wppoppop-front-btn wppoppop-link-btn"' . ($bt_blank ? ' target="_blank"' : '') . (!empty($bt_rel) ? ' rel="' . esc_attr(implode(' ', $bt_rel)) . '"' : '') . ' style="' . $bt_in . '">' . $bt_label . '</a>';
                }
                return '<div id="el-' . $el_id . '" class="wppoppop-layer-item wppoppop-element wppoppop-front-element wppoppop-el-button ' . $anim_class . '" style="' . $bt_wrap_style . '">' . $bt_html . $hover_style_tag . '</div>';

            case 'submit':
                $btn_text = !empty($content) ? esc_html($content) : 'Submit Form';
                return '<div class="wppoppop-layer-item wppoppop-element wppoppop-el-button ' . $anim_class . '" style="' . $style_attr . '"><button type="button" class="wppoppop-submit-trigger wppoppop-submit-btn" style="width:100%;height:100%;background:#c2185b;color:#ffffff;border:none;border-radius:inherit;font-weight:700;font-size:13px;cursor:pointer;">' . $btn_text . '</button></div>';

            case 'pay':
                $btn_text = !empty($content) ? esc_html($content) : 'Pay Now';
                return '<div class="wppoppop-layer-item wppoppop-element ' . $anim_class . '" style="' . $style_attr . '"><button type="button" class="wppoppop-pay-trigger" data-amount="19.99" data-currency="USD" style="width:100%;height:100%;background:#059669;color:#ffffff;border:none;border-radius:inherit;font-weight:700;font-size:13px;cursor:pointer;">' . $btn_text . '</button></div>';

            case 'close':
            case 'close_icon':
                /* WPPOPPOP_CLOSE_CASE: render the Close layer as a real, clickable button */
                if (!class_exists('WpPopPop_Front_Element_Actions')) {
                    $wppoppop_act_file = WPPOPPOP_PATH . 'includes/front/elements/class-front-element-actions.php';
                    if (file_exists($wppoppop_act_file)) { require_once $wppoppop_act_file; }
                }
                $wppoppop_close_inner = class_exists('WpPopPop_Front_Element_Actions')
                    ? WpPopPop_Front_Element_Actions::render_close($el)
                    : '&times;';
                $wppoppop_hv_vars = '';
                foreach ($wrapper_styles as $wppoppop_ws) {
                    if (strpos($wppoppop_ws, '--') === 0) { $wppoppop_hv_vars .= $wppoppop_ws; }
                }
                return '<div id="el-' . $el_id . '" class="wppoppop-layer-item wppoppop-element wppoppop-front-element wppoppop-el-close ' . $anim_class . '" style="'
                    . esc_attr(implode(';', $style) . $anim_style) . ';' . esc_attr($wppoppop_hv_vars) . 'pointer-events:auto;">'
                    . $wppoppop_close_inner . $hover_style_tag . '</div>';

            case 'html':
                return '<div class="wppoppop-layer-item wppoppop-element ' . $anim_class . '" style="' . $style_attr . ';overflow:hidden;">' . $content . '</div>';

            default:
                /* WPPOPPOP_DECOMPOSED_DEFAULT: form types without their own case */
                $wppoppop_dt = strtolower(trim((string) $type));
                if (in_array($wppoppop_dt, array('phone','tel','textarea','multiselect','imageselect','tile','time','upload','password'), true) && class_exists('WpPopPop_Front_Decomposed_26')) {
                    $wppoppop_dec = WpPopPop_Front_Decomposed_26::render_element($wppoppop_dt, $el);
                    if ($wppoppop_dec !== null) {
                        return '<div class="wppoppop-layer-item wppoppop-element wppoppop-el-text ' . $anim_class . '" style="' . $style_attr . ($wppoppop_dt === 'imageselect' ? ';padding:0!important' : '') . '">' . $wppoppop_dec . '</div>'; /* WPPOPPOP_IMAGESELECT_WRAP */
                    }
                }
                return '<div class="wppoppop-layer-item wppoppop-element ' . $anim_class . '" style="' . $style_attr . '">' . esc_html($content) . '</div>';
        }
        return '<div class="wppoppop-layer-item wppoppop-element ' . $anim_class . '" style="' . $style_attr . '">' . (isset($inner_html) ? $inner_html : '') . $hover_style_tag . '</div>';
    }
}
