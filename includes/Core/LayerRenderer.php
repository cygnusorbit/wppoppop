<?php
namespace WPPopPop\Core;

class LayerRenderer {
    public static function render_layers(int $popup_id, string $fallback_content = ''): string {
        $raw_layers = get_post_meta($popup_id, '_wppoppop_builder_layers', true);
        $canvas_cfg = get_post_meta($popup_id, '_wppoppop_canvas_config', true) ?: [];
        $c_width    = absint($canvas_cfg['width'] ?? 640);
        $c_height   = absint($canvas_cfg['height'] ?? 440);
        $c_bg       = esc_attr($canvas_cfg['bgColor'] ?? '#ffffff');
        $c_radius   = absint($canvas_cfg['borderRadius'] ?? 8);

        // If no visual builder layers are configured, render default template layout
        if (empty($raw_layers) || $raw_layers === '[]') {
            $badge = get_post_meta($popup_id, '_wppoppop_layer_badge', true) ?: 'SPECIAL OFFER';
            $cta   = get_post_meta($popup_id, '_wppoppop_layer_cta_text', true) ?: 'Subscribe Now';
            ob_start();
            ?>
            <div class="wppoppop-layer-default">
                <span class="wppoppop-badge"><?php echo esc_html($badge); ?></span>
                <div class="wppoppop-body"><?php echo wp_kses_post($fallback_content ?: '<p>Get exclusive updates delivered directly to your inbox.</p>'); ?></div>
                <form class="wppoppop-form" data-popup-id="<?php echo esc_attr($popup_id); ?>">
                    <input type="text" name="name" placeholder="Your Name" required class="wppoppop-input" />
                    <input type="email" name="email" placeholder="Your Email" required class="wppoppop-input" />
                    <button type="submit" class="wppoppop-btn-pink"><?php echo esc_html($cta); ?></button>
                </form>
            </div>
            <?php
            $output = ob_get_clean();
        return apply_filters('wppoppop_render_layers_output', $output, $popup_id, $layers);
        }

        $layers = json_decode($raw_layers, true);
        if (!is_array($layers)) {
            return $fallback_content;
        }

        ob_start();
        ?>
        <div class="wppoppop-rendered-canvas" style="position:relative; width:640px; max-width:100%; height:400px; margin:0 auto; overflow:hidden;">
            <form class="wppoppop-form" data-popup-id="<?php echo esc_attr($popup_id); ?>" style="width:100%; height:100%; position:relative; margin:0; padding:0;">
                <?php
                foreach ($layers as $idx => $l):
                    if (isset($l['visible']) && $l['visible'] === false) {
                        continue;
                    }

                    $type     = $l['type'] ?? 'text';
                    $x        = absint($l['x'] ?? 0);
                    $y        = absint($l['y'] ?? 0);
                    $w        = absint($l['w'] ?? 200);
                    $h        = absint($l['h'] ?? 40);
                    $z        = absint($l['z'] ?? ($idx + 1));
                    $color    = esc_attr($l['color'] ?? '#111827');
                    $bg       = esc_attr($l['bg'] ?? 'transparent');
                    $fontSize = absint($l['fontSize'] ?? 14);
                    $radius   = absint($l['radius'] ?? 4);
                    $raw_content = $l['content'] ?? '';
                    $filtered_content = apply_filters('wppoppop_layer_content', $raw_content, $popup_id);
                    $content = esc_html($filtered_content);

                    $style = sprintf(
                        'position:absolute; left:%dpx; top:%dpx; width:%dpx; height:%dpx; z-index:%d; color:%s; background-color:%s; font-size:%dpx; border-radius:%dpx; box-sizing:border-box;',
                        $x, $y, $w, $h, $z, $color, $bg, $fontSize, $radius
                    );

                    echo '<div class="wppoppop-layer-item wppoppop-layer-' . esc_attr($type) . '" style="' . $style . '">';

                    if ($type === 'text') {
                        echo '<div style="width:100%; height:100%; display:flex; align-items:center; line-height:1.2; font-weight:600;">' . $content . '</div>';
                    } elseif ($type === 'email') {
                        echo '<input type="email" name="email" required placeholder="' . ($content ?: 'Enter your email...') . '" class="wppoppop-input" style="width:100%; height:100%; margin:0; padding:0 12px; border:1px solid #d1d5db; border-radius:' . $radius . 'px; font-size:' . $fontSize . 'px;" />';
                    } elseif ($type === 'input') {
                        echo '<input type="text" name="name" placeholder="' . ($content ?: 'Your Name') . '" class="wppoppop-input" style="width:100%; height:100%; margin:0; padding:0 12px; border:1px solid #d1d5db; border-radius:' . $radius . 'px; font-size:' . $fontSize . 'px;" />';
                                                                                } elseif ($type === 'clock' || $type === 'countdown') {
                        $timer_mode   = esc_attr($l['timerMode'] ?? 'evergreen');
                        $target_date  = esc_attr($l['targetDate'] ?? '');
                        $duration_min = absint($l['durationMin'] ?? 15);
                        $on_expiry    = esc_attr($l['onExpiry'] ?? 'none');
                        $redirect_url = esc_url($l['redirectUrl'] ?? '');

                        echo '<div class="wppoppop-countdown-wrap" data-timer-id="' . esc_attr($popup_id) . '" data-timer-mode="' . $timer_mode . '" data-target-date="' . $target_date . '" data-duration-min="' . $duration_min . '" data-on-expiry="' . $on_expiry . '" data-redirect-url="' . $redirect_url . '">';
                        echo '  <div class="wppoppop-countdown-unit"><div class="wppoppop-countdown-card wppoppop-cd-days">00</div><div class="wppoppop-countdown-label">Days</div></div>';
                        echo '  <span class="wppoppop-countdown-separator">:</span>';
                        echo '  <div class="wppoppop-countdown-unit"><div class="wppoppop-countdown-card wppoppop-cd-hours">00</div><div class="wppoppop-countdown-label">Hours</div></div>';
                        echo '  <span class="wppoppop-countdown-separator">:</span>';
                        echo '  <div class="wppoppop-countdown-unit"><div class="wppoppop-countdown-card wppoppop-cd-mins">00</div><div class="wppoppop-countdown-label">Mins</div></div>';
                        echo '  <span class="wppoppop-countdown-separator">:</span>';
                        echo '  <div class="wppoppop-countdown-unit"><div class="wppoppop-countdown-card wppoppop-cd-secs">00</div><div class="wppoppop-countdown-label">Secs</div></div>';
                        echo '</div>';
                    } elseif ($type === 'wheel') {
                        $slices_json = esc_attr(wp_json_encode(\WPPopPop\Core\WheelManager::get_popup_slices($popup_id)));
                        echo '<div class="wppoppop-wheel-stage" data-popup-id="' . esc_attr($popup_id) . '" data-slices="' . $slices_json . '">';
                        echo '  <div class="wppoppop-wheel-pointer"></div>';
                        echo '  <canvas class="wppoppop-wheel-canvas"></canvas>';
                        echo '  <button type="button" class="wppoppop-wheel-hub">SPIN</button>';
                        echo '</div>';
                        echo '<div class="wppoppop-wheel-result-banner"></div>';
                        echo '<input type="hidden" name="fields[won_prize]" value="" />';
                    } elseif ($type === 'signature') {
                        echo '<div class="wppoppop-signature-wrap" style="width:100%;height:100%;">';
                        echo '  <canvas class="wppoppop-signature-canvas"></canvas>';
                        echo '  <input type="hidden" name="fields[signature]" value="" />';
                        echo '  <div class="wppoppop-sig-actions"><button type="button" class="wppoppop-sig-clear">Clear Signature</button></div>';
                        echo '</div>';
                    } elseif ($type === 'rangeslider') {
                        $min = absint($l['min'] ?? 1);
                        $max = absint($l['max'] ?? 100);
                        $val = absint($l['default'] ?? 25);
                        echo '<div class="wppoppop-slider-wrap">';
                        echo '  <div class="wppoppop-slider-header"><span>' . ($content ?: 'Select Quantity') . '</span><span class="wppoppop-slider-val">' . $val . '</span></div>';
                        echo '  <input type="range" name="fields[' . sanitize_key($content ?: 'range') . ']" min="' . $min . '" max="' . $max . '" value="' . $val . '" class="wppoppop-range-input" />';
                        echo '</div>';
                    } elseif ($type === 'calc') {
                        $formula = esc_attr($l['formula'] ?? '{qty} * 10');
                        echo '<div class="wppoppop-calc-box" data-calc-formula="' . $formula . '" data-bind-amount="1">';
                        echo '  <span>' . ($content ?: 'Estimated Total') . '</span><span class="wppoppop-calc-val">$0.00</span>';
                        echo '</div>';
                    } elseif ($type === 'submit') {
                        echo '<button type="submit" class="wppoppop-btn-pink" style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; border:none; cursor:pointer; font-weight:600; background-color:' . $bg . '; color:' . $color . '; border-radius:' . $radius . 'px; font-size:' . $fontSize . 'px;">' . ($content ?: 'Submit') . '</button>';
                    } elseif ($type === 'ribbon') {
                        echo '<div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">' . $content . '</div>';
                    } elseif ($type === 'close') {
                        echo '<span class="wppoppop-close" style="position:static; display:flex; align-items:center; justify-content:center; width:100%; height:100%; cursor:pointer;">&times;</span>';
                    } elseif ($type === 'divider') {
                        echo '<hr style="width:100%; border:0; border-top:1px solid #e5e7eb; margin:0; position:relative; top:50%;" />';
                    } elseif ($type === 'rectangle') {
                        // Background container box
                        echo '';
                    } else {
                        echo '<div style="width:100%; height:100%; display:flex; align-items:center;">' . $content . '</div>';
                    }

                    echo '</div>';
                endforeach;
                ?>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }
}
