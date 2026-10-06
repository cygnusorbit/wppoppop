<?php
/**
 * Plugin Name: WpPopPop
 * Plugin URI: https://github.com/cygnusorbit/wppoppop
 * Description: The ultimate, high-converting WordPress visual popup builder and lead capture platform.
 * Version: 1.0.0
 * Author: cygnusorbit
 * Author URI: https://github.com/cygnusorbit
 * License: GPL-2.0+
 * Text Domain: wppoppop
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define Core Plugin Constants
define('WPPOPPOP_VERSION', '1.0.0');
define('WPPOPPOP_PATH', plugin_dir_path(__FILE__));
define('WPPOPPOP_URL', plugin_dir_url(__FILE__));
define('WPPOPPOP_BASENAME', plugin_basename(__FILE__));

// Load Database Installer and Register Activation
require_once WPPOPPOP_PATH . 'includes/class-wppoppop-installer.php';
register_activation_hook(__FILE__, ['WpPopPop_Installer', 'activate']);

/**
 * Main WpPopPop Application Singleton
 */
final class WpPopPop {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->load_dependencies();
        $this->init_components();
    }

    private function load_dependencies() {
        require_once WPPOPPOP_PATH . 'includes/helpers.php';
        require_once WPPOPPOP_PATH . 'includes/class-wppoppop-admin.php';
        require_once WPPOPPOP_PATH . 'includes/class-wppoppop-ajax.php';
        require_once WPPOPPOP_PATH . 'includes/class-wppoppop-front.php';
        require_once WPPOPPOP_PATH . 'includes/class-wppoppop-rest.php';

        if (file_exists(WPPOPPOP_PATH . 'includes/class-wppoppop-addons.php')) {
            require_once WPPOPPOP_PATH . 'includes/class-wppoppop-addons.php';
        }
        if (file_exists(WPPOPPOP_PATH . 'includes/class-wppoppop-widget.php')) {
            require_once WPPOPPOP_PATH . 'includes/class-wppoppop-widget.php';
        }
    }

    private function init_components() {
        new WpPopPop_Ajax();
        new WpPopPop_Rest();
        new WpPopPop_Front();

        if (is_admin()) {
            new WpPopPop_Admin();
        }
        if (class_exists('WpPopPop_Addons')) {
            new WpPopPop_Addons();
        }
        if (class_exists('WpPopPop_Widget')) {
            add_action('widgets_init', function() {
                register_widget('WpPopPop_Widget');
            });
        }
    }
}

// Initialize Application Core
function wppoppop() {
    return WpPopPop::get_instance();
}

add_action('plugins_loaded', 'wppoppop');
