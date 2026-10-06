<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Modular AJAX Dispatcher
 * Loads decomposed AJAX sub-controllers.
 */
class WpPopPop_Ajax {
    public function __construct() {
        require_once WPPOPPOP_PATH . 'includes/ajax/class-ajax-builder.php';
        require_once WPPOPPOP_PATH . 'includes/ajax/class-ajax-frontend.php';
        require_once WPPOPPOP_PATH . 'includes/ajax/class-ajax-submissions.php';
        require_once WPPOPPOP_PATH . 'includes/ajax/class-ajax-settings.php';

        new WpPopPop_Ajax_Builder();
        new WpPopPop_Ajax_Frontend();
        new WpPopPop_Ajax_Submissions();
        new WpPopPop_Ajax_Settings();
    }
}
