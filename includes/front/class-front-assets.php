<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Front_Assets {
    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
    }

    public function enqueue_assets() {
        $is_preview = isset($_GET['wppoppop_preview']) && current_user_can('manage_options');

        if ($is_preview || !is_admin()) {
            wp_enqueue_style('dashicons');
            wp_enqueue_style('wppoppop-animate', 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css', array(), '4.1.1');

            if (file_exists(WPPOPPOP_PATH . 'public/css/wppoppop-front.css')) {
                wp_enqueue_style('wppoppop-front-css', WPPOPPOP_URL . 'public/css/wppoppop-front.css', array(), WPPOPPOP_VERSION);
            }

            wp_enqueue_script('jquery');

            if (file_exists(WPPOPPOP_PATH . 'public/js/front/front-display.js')) {
                wp_enqueue_script('wppoppop-front-js', WPPOPPOP_URL . 'public/js/front/front-display.js', array('jquery'), WPPOPPOP_VERSION, true);
            } elseif (file_exists(WPPOPPOP_PATH . 'public/js/wppoppop-front.js')) {
                wp_enqueue_script('wppoppop-front-js', WPPOPPOP_URL . 'public/js/wppoppop-front.js', array('jquery'), WPPOPPOP_VERSION, true);
            }

            wp_localize_script('jquery', 'wppoppop_front_vars', array(
                'ajax_url'    => admin_url('admin-ajax.php'),
                'is_preview'  => $is_preview ? 1 : 0,
                'preview_uid' => $is_preview ? sanitize_key($_GET['wppoppop_preview']) : ''
            ));
        }
    }
}
