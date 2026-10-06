<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Front_Assets {
    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    public function enqueue_assets() {
        if (file_exists(WPPOPPOP_PATH . 'public/css/vendor/animate.min.css')) {
            wp_enqueue_style('wppoppop-animate-css', WPPOPPOP_URL . 'public/css/vendor/animate.min.css', [], '4.1.1');
        } else {
            wp_enqueue_style('wppoppop-animate-css', 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css', [], '4.1.1');
        }

        wp_enqueue_style('wppoppop-front-css', WPPOPPOP_URL . 'public/css/wppoppop-front.css', ['wppoppop-animate-css'], WPPOPPOP_VERSION);
        wp_enqueue_script('wppoppop-front-js', WPPOPPOP_URL . 'public/js/wppoppop-front.js', ['jquery'], WPPOPPOP_VERSION, true);
        wp_localize_script('wppoppop-front-js', 'wppoppop_front_vars', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'rest_url' => esc_url(rest_url('wppoppop/v1/')),
            'nonce'    => wp_create_nonce('wppoppop_front_nonce')
        ]);
    }
}
