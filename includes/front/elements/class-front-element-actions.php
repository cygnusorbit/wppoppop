<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Front_Element_Actions {

    public static function register() {
        WpPopPop_Front_Element_Registry::register('submit', [__CLASS__, 'render_submit']);
        WpPopPop_Front_Element_Registry::register('submit_btn', [__CLASS__, 'render_submit']);
        WpPopPop_Front_Element_Registry::register('step', [__CLASS__, 'render_step']);
        WpPopPop_Front_Element_Registry::register('step_btn', [__CLASS__, 'render_step']);
        WpPopPop_Front_Element_Registry::register('next_step', [__CLASS__, 'render_step']);
        WpPopPop_Front_Element_Registry::register('link', [__CLASS__, 'render_link']);
        WpPopPop_Front_Element_Registry::register('link_btn', [__CLASS__, 'render_link']);
        WpPopPop_Front_Element_Registry::register('pay', [__CLASS__, 'render_pay']);
        WpPopPop_Front_Element_Registry::register('pay_btn', [__CLASS__, 'render_pay']);
        WpPopPop_Front_Element_Registry::register('close', [__CLASS__, 'render_close']);
        WpPopPop_Front_Element_Registry::register('close_icon', [__CLASS__, 'render_close']);
    }

    public static function render_submit(array $el, array $config = []) {
        $label = !empty($el['btnText']) ? $el['btnText'] : (!empty($el['content']) ? $el['content'] : 'Submit Now');
        $action = !empty($el['submit_action']) ? esc_attr($el['submit_action']) : (!empty($el['action']) ? esc_attr($el['action']) : 'close');
        $redirect = !empty($el['redirect_url']) ? esc_url($el['redirect_url']) : '';

        return sprintf(
            '<button type="submit" class="wppoppop-submit-btn" data-action="%1$s" data-redirect="%2$s" style="width:100%%;height:100%%;border:inherit;border-radius:inherit;background:inherit;color:inherit;font-size:inherit;font-weight:inherit;cursor:pointer;display:flex;align-items:center;justify-content:center;padding:0 16px;box-sizing:border-box;outline:none;">%3$s</button>',
            $action,
            $redirect,
            wp_kses_post($label)
        );
    }

    public static function render_step(array $el, array $config = []) {
        $label = !empty($el['btnText']) ? $el['btnText'] : (!empty($el['content']) ? $el['content'] : 'Next Step →');
        $target = !empty($el['goto_canvas']) ? intval($el['goto_canvas']) : (!empty($el['goto_screen']) ? intval($el['goto_screen']) : 2);

        return sprintf(
            '<button type="button" class="wppoppop-next-canvas-btn wppoppop-next-screen-btn wppoppop-next-step" data-goto-canvas="%1$d" data-goto-screen="%1$d" style="width:100%%;height:100%%;border:inherit;border-radius:inherit;background:inherit;color:inherit;font-size:inherit;font-weight:inherit;cursor:pointer;display:flex;align-items:center;justify-content:center;padding:0 16px;box-sizing:border-box;outline:none;">%2$s</button>',
            $target,
            wp_kses_post($label)
        );
    }

    public static function render_link(array $el, array $config = []) {
        $label = !empty($el['btnText']) ? $el['btnText'] : (!empty($el['content']) ? $el['content'] : 'Learn More →');
        $url = !empty($el['link_url']) ? esc_url($el['link_url']) : (!empty($el['url']) ? esc_url($el['url']) : '#');
        $target = (!empty($el['link_target']) && $el['link_target'] === '_blank') ? '_blank' : '_self';
        $rel = !empty($el['nofollow']) ? 'rel="nofollow noopener"' : 'rel="noopener"';

        return sprintf(
            '<a href="%1$s" target="%2$s" %3$s class="wppoppop-link-btn" style="width:100%%;height:100%%;border:inherit;border-radius:inherit;background:inherit;color:inherit;font-size:inherit;font-weight:inherit;text-decoration:none;cursor:pointer;display:flex;align-items:center;justify-content:center;padding:0 16px;box-sizing:border-box;">%4$s</a>',
            $url,
            $target,
            $rel,
            wp_kses_post($label)
        );
    }

    public static function render_pay(array $el, array $config = []) {
        $label = !empty($el['btnText']) ? $el['btnText'] : (!empty($el['content']) ? $el['content'] : 'Pay Now');
        $gateway = !empty($el['gateway']) ? esc_attr($el['gateway']) : 'stripe';
        $amount = !empty($el['amount']) ? esc_attr($el['amount']) : '19.99';
        $currency = !empty($el['currency']) ? esc_attr($el['currency']) : 'USD';
        $item_desc = !empty($el['item_desc']) ? esc_attr($el['item_desc']) : 'Popup Order';

        return sprintf(
            '<button type="button" class="wppoppop-pay-btn" data-gateway="%1$s" data-amount="%2$s" data-currency="%3$s" data-desc="%4$s" style="width:100%%;height:100%%;border:inherit;border-radius:inherit;background:inherit;color:inherit;font-size:inherit;font-weight:inherit;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px;padding:0 16px;box-sizing:border-box;outline:none;"><span>💳</span> <span>%5$s</span></button>',
            $gateway,
            $amount,
            $currency,
            $item_desc,
            wp_kses_post($label)
        );
    }

    public static function render_close(array $el, array $config = []) {
        /* WPPOPPOP_CLOSE_RENDER_V2: same data model as the builder stage */
        $allowed = array('×', '✕', '✖', '⊗', 'CLOSE');
        $raw = '';
        if (!empty($el['closeGlyph'])) $raw = $el['closeGlyph'];
        elseif (!empty($el['content'])) $raw = $el['content'];
        $glyph = '×';
        if (in_array($raw, $allowed, true)) {
            $glyph = $raw;
        } else {
            $style = !empty($el['glyph_style']) ? $el['glyph_style'] : 'times';
            if ($style === 'cross') $glyph = '✕';
            elseif ($style === 'circle_x') $glyph = 'ⓧ';
            elseif ($style === 'custom_text') $glyph = !empty($el['content']) ? $el['content'] : 'CLOSE';
        }
        $action = !empty($el['close_action']) ? $el['close_action'] : (!empty($el['closeAction']) ? $el['closeAction'] : 'close');
        $cookie_days = !empty($el['cookie_days']) ? intval($el['cookie_days']) : (!empty($el['cookieDays']) ? intval($el['cookieDays']) : 30);
        $size = !empty($el['iconSize']) ? intval($el['iconSize']) : (!empty($el['icon_size']) ? intval($el['icon_size']) : 20);
        /* WPPOPPOP_CLOSE_COLOR */
        $glyph_color = (!empty($el['color']) && preg_match('/^#[0-9a-fA-F]{3,8}$/', $el['color'])) ? $el['color'] : 'inherit';
        if ($glyph === 'CLOSE') $size = max(10, (int) round($size * 0.65));

        return '<button type="button" class="wppoppop-close-btn wppoppop-close-trigger" data-close-action="' . esc_attr($action)
            . '" data-cookie-days="' . $cookie_days . '" style="position:static;width:100%;height:100%;border:inherit;border-radius:inherit;background:inherit;color:' . $glyph_color . ';font-size:'
            . $size . 'px;line-height:1;cursor:pointer;display:flex;align-items:center;justify-content:center;padding:0;margin:0;box-sizing:border-box;outline:none;-webkit-appearance:none;appearance:none;">'
            . esc_html($glyph) . '</button>';
    }
}
