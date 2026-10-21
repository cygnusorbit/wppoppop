<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Shared payload sanitizers. Used by builder save and by backup restore,
 * so both paths apply the exact same animation whitelist.
 */
class WpPopPop_Sanitizer {
    public static function anim_payload($node) {
        if (is_object($node)) {
            foreach (get_object_vars($node) as $k => $v) {
                $node->$k = self::anim_value($k, $v);
            }
            return $node;
        }
        if (is_array($node)) {
            foreach ($node as $k => $v) {
                $node[$k] = self::anim_value($k, $v);
            }
        }
        return $node;
    }

    protected static function anim_value($k, $v) {
        static $txt = ['anim_appearance', 'anim_disappearance', 'anim_effect', 'animEffect', 'animation', 'animIn', 'animOut'];
        static $int = ['anim_duration', 'anim_delay', 'animDuration', 'animDelay'];
        static $bool = ['close_btn_enabled']; /* WPPOPPOP_DEFAULT_CLOSE */
        if (is_scalar($v) || $v === null) {
            if (in_array($k, $bool, true)) {
                return in_array($v, [true, 1, '1', 'true', 'on', 'yes'], true);
            }
            if (in_array($k, $txt, true)) {
                return preg_replace('/[^A-Za-z0-9_\-]/', '', (string) $v);
            }
            if (in_array($k, $int, true)) {
                return max(0, intval($v));
            }
            return $v;
        }
        return self::anim_payload($v);
    }
}
