<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Front_Element_Gamification {

    public static function register() {
        WpPopPop_Front_Element_Registry::register('rating', [__CLASS__, 'render_rating']);
        WpPopPop_Front_Element_Registry::register('slider', [__CLASS__, 'render_slider']);
        WpPopPop_Front_Element_Registry::register('signature', [__CLASS__, 'render_signature']);
        WpPopPop_Front_Element_Registry::register('wheel', [__CLASS__, 'render_wheel']);
        WpPopPop_Front_Element_Registry::register('scratch', [__CLASS__, 'render_scratch']);
        WpPopPop_Front_Element_Registry::register('countdown', [__CLASS__, 'render_countdown']);
        WpPopPop_Front_Element_Registry::register('progress', [__CLASS__, 'render_progress']);
    }

    public static function render_rating(array $el, array $config = []) {
        $count = !empty($el['maxStars']) ? intval($el['maxStars']) : 5;
        $val = isset($el['ratingVal']) ? intval($el['ratingVal']) : (isset($el['content']) ? intval($el['content']) : 5);
        $color = !empty($el['ratingColor']) ? esc_attr($el['ratingColor']) : '#f59e0b';
        $field = !empty($el['field_name']) ? sanitize_key($el['field_name']) : 'rating';
        $size = !empty($el['fontSize']) ? intval($el['fontSize']) : 22;

        $html = sprintf(
            '<div class="wppoppop-front-rating" data-max="%1$d" data-val="%2$d" data-field="%3$s" style="display:flex;align-items:center;gap:4px;height:100%;font-size:%4$dpx;color:%5$s;cursor:pointer;">',
            $count,
            $val,
            esc_attr($field),
            $size,
            $color
        );
        for ($i = 1; $i <= $count; $i++) {
            $star = ($i <= $val) ? '★' : '☆';
            $html .= '<span class="wppoppop-star-glyph" data-index="' . $i . '">' . $star . '</span>';
        }
        $html .= '<input type="hidden" name="' . esc_attr($field) . '" value="' . $val . '"></div>';
        return $html;
    }

    public static function render_slider(array $el, array $config = []) {
        $min = isset($el['min']) ? floatval($el['min']) : 0;
        $max = isset($el['max']) ? floatval($el['max']) : 100;
        $step = !empty($el['step']) ? floatval($el['step']) : 1;
        $val = isset($el['sliderVal']) ? floatval($el['sliderVal']) : (isset($el['content']) ? floatval($el['content']) : 50);
        $color = !empty($el['sliderColor']) ? esc_attr($el['sliderColor']) : '#3b82f6';
        $field = !empty($el['field_name']) ? sanitize_key($el['field_name']) : 'slider_val';

        return sprintf(
            '<div class="wppoppop-front-slider-wrap" style="width:100%;height:100%;display:flex;flex-direction:column;justify-content:center;padding:0 8px;box-sizing:border-box;"><input type="range" name="%1$s" min="%2$s" max="%3$s" step="%4$s" value="%5$s" class="wppoppop-front-slider" style="width:100%;accent-color:%6$s;margin:0;"><div style="display:flex;justify-content:space-between;font-size:11px;color:#64748b;margin-top:4px;"><span>%2$s</span><span class="wppoppop-slider-pill" style="font-weight:700;color:%6$s;">%5$s</span><span>%3$s</span></div></div>',
            esc_attr($field),
            $min,
            $max,
            $step,
            $val,
            $color
        );
    }

    public static function render_signature(array $el, array $config = []) {
        $color = !empty($el['sigColor']) ? esc_attr($el['sigColor']) : '#0f172a';
        $label = !empty($el['clearLabel']) ? esc_html($el['clearLabel']) : 'Clear Signature';
        $field = !empty($el['field_name']) ? sanitize_key($el['field_name']) : 'digital_signature';

        return sprintf(
            '<div class="wppoppop-front-signature-wrap" data-color="%1$s" style="width:100%;height:100%;border:inherit;border-radius:inherit;background:inherit;position:relative;overflow:hidden;"><canvas class="wppoppop-sig-canvas" style="width:100%;height:100%;display:block;cursor:crosshair;"></canvas><button type="button" class="wppoppop-sig-clear-btn" style="position:absolute;bottom:6px;right:6px;background:#e2e8f0;color:#475569;border:none;border-radius:3px;padding:2px 8px;font-size:10px;cursor:pointer;">%2$s</button><input type="hidden" name="%3$s" value=""></div>',
            $color,
            $label,
            esc_attr($field)
        );
    }

    public static function render_wheel(array $el, array $config = []) {
        $slices = !empty($el['slices']) ? esc_attr($el['slices']) : '10% OFF, FREE SHIPPING, 25% OFF, JACKPOT';
        $btn = !empty($el['btnText']) ? esc_html($el['btnText']) : 'SPIN TO WIN!';
        $win_msg = !empty($el['winMsg']) ? esc_attr($el['winMsg']) : 'Congratulations! You won {prize}!';

        return sprintf(
            '<div class="wppoppop-front-wheel-wrap" data-slices="%1$s" data-winmsg="%2$s" style="width:100%;height:100%;position:relative;border-radius:50%;overflow:hidden;"><canvas class="wppoppop-wheel-canvas" style="width:100%;height:100%;display:block;border-radius:50%;"></canvas><button type="button" class="wppoppop-wheel-spin-btn" style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);border-radius:50%;width:42%;height:42%;background:#ffffff;border:none;box-shadow:0 2px 6px rgba(0,0,0,0.25);font-size:10px;font-weight:700;color:#0f172a;cursor:pointer;display:flex;align-items:center;justify-content:center;text-align:center;padding:4px;line-height:1.1;">%3$s</button><div style="position:absolute;top:0;left:50%;transform:translateX(-50%);width:0;height:0;border-left:8px solid transparent;border-right:8px solid transparent;border-top:14px solid #0f172a;z-index:3;"></div></div>',
            $slices,
            $win_msg,
            $btn
        );
    }

    public static function render_scratch(array $el, array $config = []) {
        $prize = !empty($el['prizeText']) ? esc_html($el['prizeText']) : (!empty($el['content']) ? esc_html($el['content']) : 'YOU WON 25% OFF!');
        $foil = !empty($el['foilColor']) ? esc_attr($el['foilColor']) : '#94a3b8';
        $reveal = !empty($el['revealPct']) ? intval($el['revealPct']) : 45;

        return sprintf(
            '<div class="wppoppop-front-scratch-wrap" data-foil="%1$s" data-reveal="%2$d" style="width:100%;height:100%;position:relative;border-radius:inherit;overflow:hidden;box-shadow:inset 0 0 10px rgba(0,0,0,0.15);"><div class="wppoppop-scratch-secret" style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;padding:10px;text-align:center;font-weight:700;color:#0f172a;font-size:13px;line-height:1.2;">%3$s</div><canvas class="wppoppop-scratch-canvas" style="position:absolute;inset:0;width:100%;height:100%;display:block;cursor:crosshair;"></canvas></div>',
            $foil,
            $reveal,
            $prize
        );
    }

    public static function render_countdown(array $el, array $config = []) {
        $sec = !empty($el['countdownSeconds']) ? intval($el['countdownSeconds']) : 900;
        $action = !empty($el['expireAction']) ? esc_attr($el['expireAction']) : 'none';
        $redirect = !empty($el['redirectUrl']) ? esc_url($el['redirectUrl']) : '';
        $m = floor($sec / 60);
        $s = $sec % 60;
        $time_str = sprintf('%02d : %02d', $m, $s);

        return sprintf(
            '<div class="wppoppop-front-countdown" data-seconds="%1$d" data-action="%2$s" data-redirect="%3$s" style="width:100%;height:100%;border-radius:inherit;background:inherit;color:inherit;display:flex;align-items:center;justify-content:center;gap:8px;font-family:monospace;font-weight:700;font-size:22px;letter-spacing:1px;user-select:none;"><span>⏱</span> <span class="wppoppop-countdown-digits">%4$s</span></div>',
            $sec,
            $action,
            $redirect,
            $time_str
        );
    }

    public static function render_progress(array $el, array $config = []) {
        $pct = isset($el['progressVal']) ? intval($el['progressVal']) : (isset($el['content']) ? intval($el['content']) : 60);
        $pct = max(0, min(100, $pct));
        $fill = !empty($el['progressColor']) ? esc_attr($el['progressColor']) : '#3b82f6';
        $track = !empty($el['bgColor']) ? esc_attr($el['bgColor']) : '#e2e8f0';

        return sprintf(
            '<div class="wppoppop-front-progress-wrap" style="width:100%;height:100%;border-radius:inherit;background:%1$s;overflow:hidden;position:relative;display:flex;align-items:center;"><div class="wppoppop-progress-fill" style="width:%2$d%%;height:100%;background:%3$s;transition:width 0.3s ease;"></div><span style="position:absolute;width:100%;text-align:center;font-size:10px;font-weight:700;color:inherit;user-select:none;">%2$d%%</span></div>',
            $track,
            $pct,
            $fill
        );
    }
}
