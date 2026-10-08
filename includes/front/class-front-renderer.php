<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Front_Renderer {

    public function render_popup_markup($uid, array $config, $is_inline = false) {
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

        // Normalization: Support modern 'canvases' with fallback to legacy 'screens'
        $canvases = $config['canvases'] ?? $config['screens'] ?? [];
        if (empty($canvases) || !is_array($canvases)) {
            $canvases = [1 => []];
        }

        $encoded_config = esc_attr(wp_json_encode($config));
        $overlay_style = $is_inline ? 'display:block;position:relative;' : 'display:none;position:fixed;inset:0;z-index:999999;';
        ?>
        <div class="wppoppop-overlay <?php echo $is_inline ? 'wppoppop-inline-container' : ''; ?>"
             id="wppoppop-popup-<?php echo esc_attr($uid); ?>"
             data-uid="<?php echo esc_attr($uid); ?>"
             data-config="<?php echo $encoded_config; ?>"
             style="<?php echo esc_attr($overlay_style); ?>align-items:center;justify-content:center;background:rgba(15,23,42,0.65);backdrop-filter:blur(3px);">

            <div class="wppoppop-box"
                 style="position:relative;width:<?php echo esc_attr($width); ?>px;max-width:92vw;min-height:<?php echo esc_attr($height); ?>px;border-radius:8px;box-shadow:0 25px 50px -12px rgba(0,0,0,0.4);overflow:hidden;<?php echo esc_attr($box_bg); ?>">

                <!-- Close Button -->
                <?php if (!$is_inline): ?>
                    <button type="button" class="wppoppop-close-btn" aria-label="Close" style="position:absolute;top:10px;right:10px;background:transparent;border:none;color:#64748b;font-size:22px;cursor:pointer;line-height:1;z-index:9999999;">&times;</button>
                <?php endif; ?>

                <!-- Submission Status Overlay -->
                <div class="wppoppop-status-overlay" style="display:none;position:absolute;inset:0;background:rgba(255,255,255,0.95);z-index:999999;align-items:center;justify-content:center;flex-direction:column;padding:20px;text-align:center;">
                    <span class="dashicons dashicons-yes-alt" style="font-size:42px;width:42px;height:42px;color:#10b981;margin-bottom:8px;"></span>
                    <p class="wppoppop-status-message" style="font-size:15px;font-weight:700;color:#1e293b;margin:0;"></p>
                </div>

                <!-- Sequence Canvases (Dual-Compatible Classes & Attributes) -->
                <?php foreach ($canvases as $index => $elements): 
                    $canvas_num = intval($index) ?: 1;
                    $is_active = ($canvas_num === 1);
                    $display_style = $is_active ? 'display:block;' : 'display:none;';
                ?>
                    <div class="wppoppop-canvas-container wppoppop-screen-container <?php echo $is_active ? 'wppoppop-canvas-active wppoppop-screen-active' : ''; ?>"
                         data-canvas-index="<?php echo esc_attr($canvas_num); ?>"
                         data-screen-index="<?php echo esc_attr($canvas_num); ?>"
                         style="<?php echo esc_attr($display_style); ?>width:100%;min-height:<?php echo esc_attr($height); ?>px;position:relative;">

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
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    private function render_canvas_element(array $el) {
        $type   = $el['type'] ?? 'text';
        $top    = intval($el['top'] ?? 20);
        $left   = intval($el['left'] ?? 20);
        $width  = intval($el['width'] ?? 200);
        $height = intval($el['height'] ?? 40);
        $zIndex = intval($el['zIndex'] ?? 10);
        $bRad   = intval($el['borderRadius'] ?? 4);
        $opac   = floatval($el['opacity'] ?? 1);
        $content = $el['content'] ?? '';

        $style = [
            "position:absolute",
            "top:{$top}px",
            "left:{$left}px",
            "width:{$width}px",
            "height:{$height}px",
            "z-index:{$zIndex}",
            "border-radius:{$bRad}px",
            "opacity:{$opac}",
            "box-sizing:border-box"
        ];

        if (!empty($el['fontFamily']) && $el['fontFamily'] !== 'inherit') $style[] = "font-family:" . esc_attr($el['fontFamily']);
        if (!empty($el['fontSize'])) $style[] = "font-size:" . intval($el['fontSize']) . "px";
        if (!empty($el['color'])) $style[] = "color:" . esc_attr($el['color']);
        if (!empty($el['bgColor'])) $style[] = "background-color:" . esc_attr($el['bgColor']);

        $style_attr = esc_attr(implode(';', $style));
        $anim_class = !empty($el['animEffect']) && $el['animEffect'] !== 'none' ? 'anim-' . esc_attr($el['animEffect']) : '';

        // Preserving all 19 Interactive Elements with Dual Bindings
        switch ($type) {
            case 'text':
                return '<div class="wppoppop-layer-item ' . $anim_class . '" style="' . $style_attr . '"><span class="wppoppop-text-render">' . wp_kses_post($content) . '</span></div>';

            case 'email':
                $ph = !empty($content) ? esc_attr($content) : 'Enter your email...';
                return '<div class="wppoppop-layer-item ' . $anim_class . '" style="' . $style_attr . '"><input type="email" name="email" class="wppoppop-field-email" placeholder="' . $ph . '" required style="width:100%;height:100%;padding:0 10px;border:1px solid #cbd5e1;border-radius:inherit;box-sizing:border-box;"></div>';

            case 'number':
                $ph = !empty($content) ? esc_attr($content) : 'Enter number...';
                return '<div class="wppoppop-layer-item ' . $anim_class . '" style="' . $style_attr . '"><input type="number" name="number_field" placeholder="' . $ph . '" style="width:100%;height:100%;padding:0 10px;border:1px solid #cbd5e1;border-radius:inherit;box-sizing:border-box;"></div>';

            case 'select':
                $opts = array_map('trim', explode(',', $content ?: 'Option 1, Option 2'));
                $html = '<div class="wppoppop-layer-item ' . $anim_class . '" style="' . $style_attr . '"><select name="dropdown_field" style="width:100%;height:100%;padding:0 8px;border:1px solid #cbd5e1;border-radius:inherit;box-sizing:border-box;">';
                foreach ($opts as $o) {
                    $html .= '<option value="' . esc_attr($o) . '">' . esc_html($o) . '</option>';
                }
                $html .= '</select></div>';
                return $html;

            case 'radios':
                $items = array_map('trim', explode(',', $content ?: 'Choice A, Choice B'));
                $html = '<div class="wppoppop-layer-item ' . $anim_class . '" style="' . $style_attr . ';display:flex;align-items:center;gap:12px;font-size:12px;">';
                foreach ($items as $idx => $it) {
                    $html .= '<label style="cursor:pointer;"><input type="radio" name="radio_choice" value="' . esc_attr($it) . '" ' . ($idx === 0 ? 'checked' : '') . '> ' . esc_html($it) . '</label>';
                }
                $html .= '</div>';
                return $html;

            case 'checkboxes':
                $label = !empty($content) ? esc_html($content) : 'I agree to the terms';
                return '<div class="wppoppop-layer-item ' . $anim_class . '" style="' . $style_attr . ';display:flex;align-items:center;gap:6px;font-size:12px;"><label style="cursor:pointer;"><input type="checkbox" name="agreement" value="1" checked> <span>' . $label . '</span></label></div>';

            case 'rating':
                return '<div class="wppoppop-layer-item wppoppop-field-rating ' . $anim_class . '" style="' . $style_attr . ';display:flex;align-items:center;justify-content:center;color:#f59e0b;font-size:20px;cursor:pointer;"><input type="hidden" name="rating" value="5">★ ★ ★ ★ ★</div>';

            case 'date':
                return '<div class="wppoppop-layer-item ' . $anim_class . '" style="' . $style_attr . '"><input type="date" name="date_picker" style="width:100%;height:100%;padding:0 8px;border:1px solid #cbd5e1;border-radius:inherit;box-sizing:border-box;"></div>';

            case 'slider':
                return '<div class="wppoppop-layer-item ' . $anim_class . '" style="' . $style_attr . ';display:flex;align-items:center;padding:0 8px;"><input type="range" name="range_slider" min="1" max="100" style="width:100%;"></div>';

            case 'signature':
                return '<div class="wppoppop-layer-item ' . $anim_class . '" style="' . $style_attr . ';position:relative;"><canvas class="wppoppop-sig-canvas" width="' . $width . '" height="' . $height . '" style="width:100%;height:100%;border:1px dashed #94a3b8;border-radius:inherit;"></canvas><input type="hidden" name="signature" class="wppoppop-sig-input"><button type="button" class="wppoppop-sig-clear-btn" style="position:absolute;bottom:2px;right:2px;font-size:9px;background:#e2e8f0;border:none;border-radius:2px;cursor:pointer;padding:1px 4px;">Clear</button></div>';

            case 'wheel':
                return '<div class="wppoppop-layer-item wppoppop-wheel-wrapper ' . $anim_class . '" style="' . $style_attr . ';text-align:center;"><canvas class="wppoppop-wheel-canvas" width="180" height="180" style="width:180px;height:180px;border-radius:50%;margin-bottom:6px;"></canvas><input type="hidden" name="prize"><br><button type="button" class="wppoppop-wheel-spin-btn button" style="background:#4338ca;color:#fff;border:none;padding:4px 10px;border-radius:4px;font-weight:700;cursor:pointer;font-size:11px;">SPIN PRIZE WHEEL</button></div>';

            case 'scratch':
                return '<div class="wppoppop-layer-item wppoppop-scratch-wrapper ' . $anim_class . '" style="' . $style_attr . ';position:relative;"><div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:#fef08a;color:#854d0e;font-weight:700;font-size:13px;border-radius:inherit;">' . esc_html($content ?: 'YOU WON 25% OFF!') . '</div><canvas class="wppoppop-scratch-canvas" width="' . $width . '" height="' . $height . '" style="position:absolute;inset:0;width:100%;height:100%;cursor:crosshair;border-radius:inherit;"></canvas></div>';

            case 'countdown':
                return '<div class="wppoppop-layer-item wppoppop-countdown-timer ' . $anim_class . '" data-seconds="900" style="' . $style_attr . ';display:flex;align-items:center;justify-content:center;background:#1e293b;color:#f8fafc;font-family:monospace;font-weight:700;font-size:16px;"><span class="cd-mins">15</span>&nbsp;:&nbsp;<span class="cd-secs">00</span></div>';

            case 'progress':
                return '<div class="wppoppop-layer-item ' . $anim_class . '" style="' . $style_attr . ';background:#e2e8f0;overflow:hidden;"><div class="wppoppop-progress-fill" style="width:50%;height:100%;background:#2563eb;transition:width 0.3s ease;"></div></div>';

            case 'file':
                return '<div class="wppoppop-layer-item ' . $anim_class . '" style="' . $style_attr . ';display:flex;align-items:center;justify-content:center;border:1px dashed #cbd5e1;"><input type="file" name="uploaded_file" style="font-size:11px;width:95%;"></div>';

            case 'step_btn':
                // Dual Target Binding: goto_canvas with fallback to goto_screen
                $target_canvas = !empty($el['goto_canvas']) ? intval($el['goto_canvas']) : (!empty($el['goto_screen']) ? intval($el['goto_screen']) : 2);
                $btn_text = !empty($content) ? esc_html($content) : 'Next Canvas &rarr;';
                return '<div class="wppoppop-layer-item ' . $anim_class . '" style="' . $style_attr . '"><button type="button" class="wppoppop-next-canvas-btn wppoppop-next-screen-btn wppoppop-next-step" data-goto-canvas="' . esc_attr($target_canvas) . '" data-goto-screen="' . esc_attr($target_canvas) . '" data-goto="' . esc_attr($target_canvas) . '" style="width:100%;height:100%;background:#2563eb;color:#ffffff;border:none;border-radius:inherit;font-weight:700;font-size:13px;cursor:pointer;">' . $btn_text . '</button></div>';

            case 'submit':
                $btn_text = !empty($content) ? esc_html($content) : 'Submit Form';
                return '<div class="wppoppop-layer-item ' . $anim_class . '" style="' . $style_attr . '"><button type="button" class="wppoppop-submit-trigger" style="width:100%;height:100%;background:#c2185b;color:#ffffff;border:none;border-radius:inherit;font-weight:700;font-size:13px;cursor:pointer;">' . $btn_text . '</button></div>';

            case 'pay':
                $btn_text = !empty($content) ? esc_html($content) : 'Pay Now';
                return '<div class="wppoppop-layer-item ' . $anim_class . '" style="' . $style_attr . '"><button type="button" class="wppoppop-pay-trigger" data-amount="19.99" data-currency="USD" style="width:100%;height:100%;background:#059669;color:#ffffff;border:none;border-radius:inherit;font-weight:700;font-size:13px;cursor:pointer;">' . $btn_text . '</button></div>';

            case 'html':
                return '<div class="wppoppop-layer-item ' . $anim_class . '" style="' . $style_attr . ';overflow:hidden;">' . $content . '</div>';

            default:
                return '<div class="wppoppop-layer-item" style="' . $style_attr . '">' . esc_html($content) . '</div>';
        }
    }
}
