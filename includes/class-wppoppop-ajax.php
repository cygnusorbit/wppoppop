<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Ajax {
    public function __construct() {
        if (file_exists(WPPOPPOP_PATH . 'includes/ajax/class-ajax-builder.php')) {
            require_once WPPOPPOP_PATH . 'includes/ajax/class-ajax-builder.php';
            if (class_exists('WpPopPop_Ajax_Builder')) {
                new WpPopPop_Ajax_Builder();
            }
        }
        if (file_exists(WPPOPPOP_PATH . 'includes/ajax/class-ajax-settings.php')) {
            require_once WPPOPPOP_PATH . 'includes/ajax/class-ajax-settings.php';
            if (class_exists('WpPopPop_Ajax_Settings')) {
                new WpPopPop_Ajax_Settings();
            }
        }
    }
}
