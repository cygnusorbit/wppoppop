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
        // === WPPOPPOP_IMAGE_LIFECYCLE_SANITIZE ===
        $wppoppop_img_url = '';
        foreach (array('src', 'imageUrl', 'imgUrl') as $wppoppop_k) {
            if (isset($el[$wppoppop_k]) && !is_array($el[$wppoppop_k])) {
                $wppoppop_v = esc_url_raw(trim(wp_unslash((string) $el[$wppoppop_k])));
                if ($wppoppop_v !== '' && $wppoppop_img_url === '') {
                    $wppoppop_img_url = $wppoppop_v;
                }
            }
        }
        if ($wppoppop_img_url !== '' || isset($el['src']) || isset($el['imageUrl']) || isset($el['imgUrl'])) {
            $clean['src']      = $wppoppop_img_url;
            $clean['imageUrl'] = $wppoppop_img_url;
            $clean['imgUrl']   = $wppoppop_img_url;
        }
        if (isset($el['linkUrl']) && !is_array($el['linkUrl'])) {
            $clean['linkUrl'] = esc_url_raw(trim(wp_unslash((string) $el['linkUrl'])));
        }
        $wppoppop_alt = null;
        foreach (array('alt', 'altText', 'imageAlt') as $wppoppop_k) {
            if (isset($el[$wppoppop_k]) && !is_array($el[$wppoppop_k])) {
                $wppoppop_alt = sanitize_text_field(wp_unslash((string) $el[$wppoppop_k]));
                break;
            }
        }
        if ($wppoppop_alt !== null) {
            $clean['alt'] = $wppoppop_alt;
            $clean['altText'] = $wppoppop_alt;
            $clean['imageAlt'] = $wppoppop_alt;
        }
        $wppoppop_fit_ok = array('cover', 'contain', 'fill', 'none', 'scale-down');
        foreach (array('objectFit', 'imageFit') as $wppoppop_k) {
            if (isset($el[$wppoppop_k]) && !is_array($el[$wppoppop_k])) {
                $wppoppop_fit = sanitize_text_field((string) $el[$wppoppop_k]);
                if (in_array($wppoppop_fit, $wppoppop_fit_ok, true)) {
                    $clean['objectFit'] = $wppoppop_fit;
                    $clean['imageFit']  = $wppoppop_fit;
                    break;
                }
            }
        }
        // === END WPPOPPOP_IMAGE_LIFECYCLE_SANITIZE ===
        if (isset($el['imgUrl']))             $clean['imgUrl']             = esc_url_raw($el['imgUrl']);
        if (isset($el['altText']))            $clean['altText']            = sanitize_text_field($el['altText']);
        if (isset($el['objectFit']))          $clean['objectFit']          = sanitize_text_field($el['objectFit']);
        if (isset($el['videoUrl']))           $clean['videoUrl']           = esc_url_raw($el['videoUrl']);
        if (isset($el['videoAutoplay'])) $clean['videoAutoplay'] = !in_array($el['videoAutoplay'], array(false, 'false', '0', 0, '', null, 'off'), true);
        if (isset($el['videoMute'])) $clean['videoMute'] = !in_array($el['videoMute'], array(false, 'false', '0', 0, '', null, 'off'), true);
        if (isset($el['videoLoop'])) $clean['videoLoop'] = !in_array($el['videoLoop'], array(false, 'false', '0', 0, '', null, 'off'), true);
        // WPPOPPOP_VIDEO_WHITELIST_V2
        $wppoppop_vbool = function ($v) { return !in_array($v, array(false, 'false', '0', 0, '', null, 'off'), true); };
        foreach (array('autoplay' => 'videoAutoplay', 'mute' => 'videoMute', 'muted' => 'videoMute', 'loop' => 'videoLoop', 'controls' => 'videoControls') as $wppoppop_src => $wppoppop_dst) {
            if (isset($el[$wppoppop_src]) && !is_array($el[$wppoppop_src]) && !isset($clean[$wppoppop_dst])) {
                $clean[$wppoppop_dst] = $wppoppop_vbool($el[$wppoppop_src]);
            }
        }
        if (isset($el['videoControls']) && !is_array($el['videoControls'])) $clean['videoControls'] = $wppoppop_vbool($el['videoControls']);
        if (isset($el['videoProvider']) && in_array($el['videoProvider'], array('youtube', 'vimeo', 'mp4'), true)) $clean['videoProvider'] = $el['videoProvider'];
        if (isset($el['aspectRatio']) && !in_array($el['aspectRatio'], array('16:9', '4:3', '1:1', '9:16'), true)) unset($el['aspectRatio']);
        if (isset($el['aspectRatio']))        $clean['aspectRatio']        = sanitize_text_field($el['aspectRatio']);
        if (isset($el['shapePreset']))        $clean['shapePreset']        = sanitize_key($el['shapePreset']);
        if (isset($el['shapeFill']))          $clean['shapeFill']          = sanitize_hex_color($el['shapeFill']) ?: sanitize_text_field($el['shapeFill']);
        if (isset($el['shapeStroke']))        $clean['shapeStroke']        = sanitize_hex_color($el['shapeStroke']) ?: sanitize_text_field($el['shapeStroke']);
        if (isset($el['shapeStrokeWidth']))   $clean['shapeStrokeWidth']   = intval($el['shapeStrokeWidth']);
        if (isset($el['shapeRotate']))        $clean['shapeRotate']        = intval($el['shapeRotate']);
        if (isset($el['shapeNoFill']))        $clean['shapeNoFill']        = !empty($el['shapeNoFill']) && $el['shapeNoFill'] !== 'false';
        if (isset($el['strokeWidth']))        $clean['strokeWidth']        = max(0, intval($el['strokeWidth']));
        if (isset($el['strokeNone']))         $clean['strokeNone']         = !empty($el['strokeNone']) && $el['strokeNone'] !== 'false';
        if (isset($el['strokeWidthPrev']))    $clean['strokeWidthPrev']    = max(0, intval($el['strokeWidthPrev']));
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
