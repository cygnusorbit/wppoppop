<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Ajax_Element_Actions {

    public static function register() {
        $types = ['submit', 'submit_btn', 'step', 'step_btn', 'next_step', 'link', 'link_btn', 'pay', 'pay_btn', 'close', 'close_icon'];
        foreach ($types as $t) {
            WpPopPop_Ajax_Element_Registry::register($t, [__CLASS__, 'sanitize_action']);
        }
    }

    public static function sanitize_action(array $el, array $clean) {
        if (isset($el['submit_action'])) $clean['submit_action'] = sanitize_text_field($el['submit_action']);
        if (isset($el['action']))        $clean['action']        = sanitize_text_field($el['action']);
        if (isset($el['redirect_url']))  $clean['redirect_url']  = esc_url_raw($el['redirect_url']);
        if (isset($el['redirectUrl']))   $clean['redirectUrl']   = esc_url_raw($el['redirectUrl']);
        if (isset($el['goto_canvas']))   $clean['goto_canvas']   = intval($el['goto_canvas']);
        if (isset($el['goto_screen']))   $clean['goto_screen']   = intval($el['goto_screen']);
        if (isset($el['link_url']))      $clean['link_url']      = esc_url_raw($el['link_url']);
        if (isset($el['url']))           $clean['url']           = esc_url_raw($el['url']);
        if (isset($el['link_target']))   $clean['link_target']   = sanitize_text_field($el['link_target']);
        if (isset($el['target']))        $clean['target']        = sanitize_text_field($el['target']);
        if (isset($el['nofollow']))      $clean['nofollow']      = (bool)$el['nofollow'];
        if (isset($el['gateway']))       $clean['gateway']       = sanitize_text_field($el['gateway']);
        if (isset($el['amount']))        $clean['amount']        = sanitize_text_field($el['amount']);
        if (isset($el['currency']))      $clean['currency']      = sanitize_text_field($el['currency']);
        if (isset($el['item_desc']))     $clean['item_desc']     = sanitize_text_field($el['item_desc']);
        if (isset($el['glyph_style']))   $clean['glyph_style']   = sanitize_text_field($el['glyph_style']);
        if (isset($el['icon_size']))     $clean['icon_size']     = intval($el['icon_size']);
        if (isset($el['close_action']))  $clean['close_action']  = sanitize_text_field($el['close_action']);
        if (isset($el['closeAction']))   $clean['closeAction']   = sanitize_text_field($el['closeAction']);
        if (isset($el['cookie_days']))   $clean['cookie_days']   = intval($el['cookie_days']);
        if (isset($el['btnText']))       $clean['btnText']       = sanitize_text_field($el['btnText']);
        if (isset($el['hoverBg']))       $clean['hoverBg']       = sanitize_hex_color($el['hoverBg']) ?: sanitize_text_field($el['hoverBg']);
        if (isset($el['hoverColor']))    $clean['hoverColor']    = sanitize_hex_color($el['hoverColor']) ?: sanitize_text_field($el['hoverColor']);
        return $clean;
    }
}
