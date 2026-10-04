<?php
namespace WPPopPop\Core;

class CountdownTimer {
    public static function init(): void {
        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_assets']);
    }

    public static function enqueue_assets(): void {
        wp_enqueue_style(
            'wppoppop-countdown-css',
            WPPOPPOP_URL . 'assets/css/countdown.css',
            [],
            WPPOPPOP_VERSION
        );

        wp_enqueue_script(
            'wppoppop-countdown-js',
            WPPOPPOP_URL . 'assets/js/countdown.js',
            [],
            WPPOPPOP_VERSION,
            true
        );
    }
}
