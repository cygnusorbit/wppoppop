<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Ajax_Element_Gamification {

    public static function register() {
        $types = ['rating', 'slider', 'signature', 'wheel', 'scratch', 'countdown', 'progress'];
        foreach ($types as $t) {
            WpPopPop_Ajax_Element_Registry::register($t, [__CLASS__, 'sanitize_gamification']);
        }
    }

    public static function sanitize_gamification(array $el, array $clean) {
        if (isset($el['ratingVal']))        $clean['ratingVal']        = intval($el['ratingVal']);
        if (isset($el['maxStars']))         $clean['maxStars']         = intval($el['maxStars']);
        if (isset($el['ratingColor']))      $clean['ratingColor']      = sanitize_hex_color($el['ratingColor']) ?: sanitize_text_field($el['ratingColor']);
        if (isset($el['sliderVal']))        $clean['sliderVal']        = floatval($el['sliderVal']);
        if (isset($el['sliderColor']))      $clean['sliderColor']      = sanitize_hex_color($el['sliderColor']) ?: sanitize_text_field($el['sliderColor']);
        if (isset($el['sigColor']))         $clean['sigColor']         = sanitize_hex_color($el['sigColor']) ?: sanitize_text_field($el['sigColor']);
        if (isset($el['clearLabel']))       $clean['clearLabel']       = sanitize_text_field($el['clearLabel']);
        if (isset($el['slices']))           $clean['slices']           = sanitize_textarea_field($el['slices']);
        if (isset($el['btnText']))          $clean['btnText']          = sanitize_text_field($el['btnText']);
        if (isset($el['winMsg']))           $clean['winMsg']           = sanitize_text_field($el['winMsg']);
        if (isset($el['prizeText']))        $clean['prizeText']        = sanitize_text_field($el['prizeText']);
        if (isset($el['foilColor']))        $clean['foilColor']        = sanitize_hex_color($el['foilColor']) ?: sanitize_text_field($el['foilColor']);
        if (isset($el['revealPct']))        $clean['revealPct']        = intval($el['revealPct']);
        if (isset($el['countdownSeconds'])) $clean['countdownSeconds'] = intval($el['countdownSeconds']);
        if (isset($el['expireAction']))     $clean['expireAction']     = sanitize_text_field($el['expireAction']);
        if (isset($el['redirectUrl']))      $clean['redirectUrl']      = esc_url_raw($el['redirectUrl']);
        if (isset($el['progressVal']))      $clean['progressVal']      = intval($el['progressVal']);
        if (isset($el['progressColor']))    $clean['progressColor']    = sanitize_hex_color($el['progressColor']) ?: sanitize_text_field($el['progressColor']);
        return $clean;
    }
}
