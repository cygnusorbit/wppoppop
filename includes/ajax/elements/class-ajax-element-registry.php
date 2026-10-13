<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * WpPopPop Backend AJAX Element Sanitization Registry
 * Modular schema validation for builder element payloads
 */
class WpPopPop_Ajax_Element_Registry {

    /**
     * Sanitizer map: type => callable
     */
    private static $sanitizers = [];

    /**
     * Initialization flag
     */
    private static $initialized = false;

    /**
     * Bootstrap domain sanitizers
     */
    public static function init() {
        if (self::$initialized) {
            return;
        }

        $dir = dirname(__FILE__);
        require_once $dir . '/class-ajax-element-typography.php';
        require_once $dir . '/class-ajax-element-inputs.php';
        require_once $dir . '/class-ajax-element-actions.php';
        require_once $dir . '/class-ajax-element-gamification.php';
        require_once $dir . '/class-ajax-element-media.php';

        WpPopPop_Ajax_Element_Typography::register();
        WpPopPop_Ajax_Element_Inputs::register();
        WpPopPop_Ajax_Element_Actions::register();
        WpPopPop_Ajax_Element_Gamification::register();
        WpPopPop_Ajax_Element_Media::register();

        self::$initialized = true;
    }

    /**
     * Register a type sanitizer
     */
    public static function register($type, callable $sanitizer) {
        $type = strtolower(trim($type));
        self::$sanitizers[$type] = $sanitizer;
    }

    /**
     * Check if type is registered
     */
    public static function has($type) {
        self::init();
        $type = strtolower(trim($type));
        return isset(self::$sanitizers[$type]);
    }

    /**
     * Sanitize element payload using registered domain schema
     */
    public static function sanitize($type, array $el, array $clean) {
        self::init();
        $type = strtolower(trim($type));
        if (isset(self::$sanitizers[$type]) && is_callable(self::$sanitizers[$type])) {
            return call_user_func(self::$sanitizers[$type], $el, $clean);
        }
        return $clean;
    }
}
