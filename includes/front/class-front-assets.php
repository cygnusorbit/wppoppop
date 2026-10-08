<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Front_Assets {
    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_front_assets']);
    }

    public function enqueue_front_assets() {
        // Enqueue Animate.css Library
        wp_enqueue_style('animate-css', WPPOPPOP_URL . 'public/css/vendor/animate.min.css', [], '4.1.1');
        wp_enqueue_style('wppoppop-front-css', WPPOPPOP_URL . 'public/css/wppoppop-front.css', ['animate-css'], WPPOPPOP_VERSION);

        wp_enqueue_script('wppoppop-front-display-js', WPPOPPOP_URL . 'public/js/front/front-display.js', ['jquery'], WPPOPPOP_VERSION, true);
        wp_enqueue_script('wppoppop-front-gamification-js', WPPOPPOP_URL . 'public/js/front/front-gamification.js', ['jquery'], WPPOPPOP_VERSION, true);
        wp_enqueue_script('wppoppop-front-logic-js', WPPOPPOP_URL . 'public/js/front/front-logic.js', ['jquery'], WPPOPPOP_VERSION, true);
        wp_enqueue_script('wppoppop-front-form-js', WPPOPPOP_URL . 'public/js/front/front-form.js', ['jquery'], WPPOPPOP_VERSION, true);
        wp_enqueue_script('wppoppop-front-triggers-js', WPPOPPOP_URL . 'public/js/front/front-triggers.js', ['jquery'], WPPOPPOP_VERSION, true);
        wp_enqueue_script('wppoppop-front-js', WPPOPPOP_URL . 'public/js/front/wppoppop-front.js', [
            'jquery',
            'wppoppop-front-display-js',
            'wppoppop-front-gamification-js',
            'wppoppop-front-logic-js',
            'wppoppop-front-form-js',
            'wppoppop-front-triggers-js'
        ], WPPOPPOP_VERSION, true);

        wp_localize_script('wppoppop-front-js', 'wppoppop_front_vars', [
            'ajax_url'     => admin_url('admin-ajax.php'),
            'rest_url'     => esc_url_raw(rest_url('wppoppop/v1/')),
            'nonce'        => wp_create_nonce('wppoppop_front_nonce'),
            'cookie_epoch' => get_option('wppoppop_cookie_epoch', 1),
            'current_user' => [
                'logged_in' => is_user_logged_in(),
                'email'     => is_user_logged_in() ? wp_get_current_user()->user_email : '',
                'name'      => is_user_logged_in() ? wp_get_current_user()->display_name : '',
                'login'     => is_user_logged_in() ? wp_get_current_user()->user_login : ''
            ]
        ]);
    }
}
