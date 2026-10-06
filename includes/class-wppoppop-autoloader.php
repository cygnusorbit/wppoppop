<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Autoloader {
    public static function register() {
        spl_autoload_register([__CLASS__, 'autoload']);
    }

    public static function autoload($class) {
        if (strpos($class, 'WpPopPop') !== 0) {
            return;
        }

        $relative = str_replace('WpPopPop_', '', $class);
        $file = '';

        if (strpos($relative, 'Ajax_') === 0) {
            $slug = strtolower(str_replace('_', '-', substr($relative, 5)));
            $file = WPPOPPOP_PATH . 'includes/ajax/class-ajax-' . $slug . '.php';
        } elseif (strpos($relative, 'Admin_') === 0) {
            $slug = strtolower(str_replace('_', '-', substr($relative, 6)));
            $file = WPPOPPOP_PATH . 'includes/admin/class-admin-' . $slug . '.php';
        } elseif (strpos($relative, 'Front_') === 0) {
            $slug = strtolower(str_replace('_', '-', substr($relative, 6)));
            $file = WPPOPPOP_PATH . 'includes/front/class-front-' . $slug . '.php';
        } elseif (strpos($relative, 'Addon_') === 0) {
            $slug = strtolower(str_replace('_', '-', substr($relative, 6)));
            $file = WPPOPPOP_PATH . 'includes/addons/class-addon-' . $slug . '.php';
        } elseif (strpos($relative, 'Rest_') === 0) {
            $slug = strtolower(str_replace('_', '-', substr($relative, 5)));
            $file = WPPOPPOP_PATH . 'includes/rest/class-rest-' . $slug . '.php';
        } elseif (strpos($relative, 'Widget_') === 0) {
            $slug = strtolower(str_replace('_', '-', substr($relative, 7)));
            $file = WPPOPPOP_PATH . 'includes/widget/class-widget-' . $slug . '.php';
        } else {
            $slug = strtolower(str_replace('_', '-', $relative));
            $file = WPPOPPOP_PATH . 'includes/class-wppoppop-' . $slug . '.php';
        }

        if ($file && file_exists($file)) {
            require_once $file;
        }
    }
}
