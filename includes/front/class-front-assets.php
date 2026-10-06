<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Front_Assets {
    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    public function enqueue_assets() {
        wp_enqueue_style('wppoppop-front-css', WPPOPPOP_URL . 'public/css/wppoppop-front.css', [], WPPOPPOP_VERSION);

        // Decomposed Public Frontend JavaScript Subsystems
        wp_enqueue_script('wppoppop-front-triggers-js', WPPOPPOP_URL . 'public/js/front/front-triggers.js', ['jquery'], WPPOPPOP_VERSION, true);
        wp_enqueue_script('wppoppop-front-modal-js', WPPOPPOP_URL . 'public/js/front/front-modal.js', ['jquery'], WPPOPPOP_VERSION, true);
        wp_enqueue_script('wppoppop-front-elements-js', WPPOPPOP_URL . 'public/js/front/front-elements.js', ['jquery'], WPPOPPOP_VERSION, true);
        wp_enqueue_script('wppoppop-front-form-js', WPPOPPOP_URL . 'public/js/front/front-form.js', ['jquery'], WPPOPPOP_VERSION, true);

        // Master Coordinator
        wp_enqueue_script('wppoppop-front-js', WPPOPPOP_URL . 'public/js/wppoppop-front.js', [
            'jquery',
            'wppoppop-front-triggers-js',
            'wppoppop-front-modal-js',
            'wppoppop-front-elements-js',
            'wppoppop-front-form-js'
        ], WPPOPPOP_VERSION, true);

        $settings = get_option('wppoppop_settings', []);
        $epoch = isset($settings['cookie_epoch']) ? intval($settings['cookie_epoch']) : 1;

        $payload = [
            'ajax_url'     => admin_url('admin-ajax.php'),
            'rest_url'     => esc_url_raw(rest_url('wppoppop/v1/')),
            'nonce'        => wp_create_nonce('wppoppop_front_nonce'),
            'cookie_epoch' => $epoch,
            'is_user'      => is_user_logged_in(),
            'user_role'    => is_user_logged_in() ? (wp_get_current_user()->roles[0] ?? '') : 'guest'
        ];

        wp_localize_script('wppoppop-front-triggers-js', 'wppoppop_front_vars', $payload);
        wp_localize_script('wppoppop-front-modal-js', 'wppoppop_front_vars', $payload);
        wp_localize_script('wppoppop-front-form-js', 'wppoppop_front_vars', $payload);
        wp_localize_script('wppoppop-front-js', 'wppoppop_front_vars', $payload);
    }
}
