<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Ajax {
    public function __construct() {
        $modules = [
            'WpPopPop_Ajax_Builder'     => 'class-ajax-builder.php',
            'WpPopPop_Ajax_Settings'    => 'class-ajax-settings.php',
            'WpPopPop_Ajax_Frontend'    => 'class-ajax-frontend.php',
            'WpPopPop_Ajax_Submissions' => 'class-ajax-submissions.php',
        ];

        foreach ($modules as $class => $file) {
            $path = WPPOPPOP_PATH . 'includes/ajax/' . $file;
            if (file_exists($path)) {
                require_once $path;
            }
            if (!class_exists($class)) {
                continue;
            }
            // Avoid double registration if another loader already booted it.
            if ($class === 'WpPopPop_Ajax_Frontend' && has_action('wp_ajax_nopriv_wppoppop_submit_form')) {
                continue;
            }
            if ($class === 'WpPopPop_Ajax_Submissions' && has_action('wp_ajax_wppoppop_delete_submission')) {
                continue;
            }
            new $class();
        }
    }
}
