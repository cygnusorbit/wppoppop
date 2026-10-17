<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Ajax_Element_Media {

    public static function register() {
        $types = ['image', 'video', 'shape', 'divider', 'icon', 'html'];
        foreach ($types as $t) {
            WpPopPop_Ajax_Element_Registry::register($t, [__CLASS__, 'sanitize_media']);
        }
    }

    public static function sanitize_media(array $el, array $clean) {
        if (isset($el['imgUrl']))             $clean['imgUrl']             = esc_url_raw($el['imgUrl']);
        if (isset($el['altText']))            $clean['altText']            = sanitize_text_field($el['altText']);
        if (isset($el['objectFit']))          $clean['objectFit']          = sanitize_text_field($el['objectFit']);
        if (isset($el['videoUrl']))           $clean['videoUrl']           = esc_url_raw($el['videoUrl']);
        if (isset($el['videoAutoplay']))      $clean['videoAutoplay']      = (bool)$el['videoAutoplay'];
        if (isset($el['videoMute']))          $clean['videoMute']          = (bool)$el['videoMute'];
        if (isset($el['videoLoop']))          $clean['videoLoop']          = (bool)$el['videoLoop'];
        if (isset($el['aspectRatio']))        $clean['aspectRatio']        = sanitize_text_field($el['aspectRatio']);
        if (isset($el['shapePreset']))        $clean['shapePreset']        = sanitize_key($el['shapePreset']);
        if (isset($el['shapeFill']))          $clean['shapeFill']          = sanitize_hex_color($el['shapeFill']) ?: sanitize_text_field($el['shapeFill']);
        if (isset($el['shapeStroke']))        $clean['shapeStroke']        = sanitize_hex_color($el['shapeStroke']) ?: sanitize_text_field($el['shapeStroke']);
        if (isset($el['shapeStrokeWidth']))   $clean['shapeStrokeWidth']   = intval($el['shapeStrokeWidth']);
        if (isset($el['shapeRotate']))        $clean['shapeRotate']        = intval($el['shapeRotate']);
        if (isset($el['shapeNoFill']))        $clean['shapeNoFill']        = !empty($el['shapeNoFill']) && $el['shapeNoFill'] !== 'false';
        if (isset($el['strokeWidth']))        $clean['strokeWidth']        = max(0, intval($el['strokeWidth']));
        if (isset($el['strokeColor']))        $clean['strokeColor']        = sanitize_hex_color($el['strokeColor']) ?: sanitize_text_field($el['strokeColor']);
        if (isset($el['rotate']))             $clean['rotate']             = intval($el['rotate']);
        if (isset($el['bgColor']))            $clean['bgColor']            = sanitize_hex_color($el['bgColor']) ?: sanitize_text_field($el['bgColor']);
        if (isset($el['transparentFill']))    $clean['transparentFill']    = !empty($el['transparentFill']) && $el['transparentFill'] !== 'false';
        if (isset($el['dividerStyle']))       $clean['dividerStyle']       = sanitize_text_field($el['dividerStyle']);
        if (isset($el['dividerColor']))       $clean['dividerColor']       = sanitize_hex_color($el['dividerColor']) ?: sanitize_text_field($el['dividerColor']);
        if (isset($el['dividerThickness']))   $clean['dividerThickness']   = intval($el['dividerThickness']);
        if (isset($el['dividerOrientation'])) $clean['dividerOrientation'] = sanitize_text_field($el['dividerOrientation']);
        if (isset($el['iconGlyph']))          $clean['iconGlyph']          = sanitize_text_field($el['iconGlyph']);
        if (isset($el['iconSize']))           $clean['iconSize']           = intval($el['iconSize']);
        if (isset($el['iconColor']))          $clean['iconColor']          = sanitize_hex_color($el['iconColor']) ?: sanitize_text_field($el['iconColor']);
        if (isset($el['dividerWeight']))      $clean['dividerWeight']      = max(1, intval($el['dividerWeight']));
        if (isset($el['dividerWidthPct']))    $clean['dividerWidthPct']    = min(100, max(1, intval($el['dividerWidthPct'])));
        if (isset($el['dividerAlign']))       $clean['dividerAlign']       = in_array($el['dividerAlign'], array('left','center','right'), true) ? $el['dividerAlign'] : 'center';
        if (isset($el['iconClass']))          $clean['iconClass']          = sanitize_text_field($el['iconClass']);
        return $clean;
    }
}
