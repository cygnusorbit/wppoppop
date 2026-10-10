<?php
/**
 * Plugin Name: WpPopPop
 * Plugin URI: https://github.com/cygnusorbit/wppoppop
 * Description: The ultimate, high-converting WordPress visual popup builder and lead capture platform.
 * Version: 3.0.608
 * Author: cygnusorbit
 * Author URI: https://github.com/cygnusorbit
 * License: GPL-2.0+
 * Text Domain: wppoppop
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define Core Plugin Constants
define('WPPOPPOP_VERSION', '3.0.608');
define('WPPOPPOP_PATH', plugin_dir_path(__FILE__));
define('WPPOPPOP_URL', plugin_dir_url(__FILE__));
define('WPPOPPOP_BASENAME', plugin_basename(__FILE__));

// Load Dynamic Autoloader
require_once WPPOPPOP_PATH . 'includes/class-wppoppop-autoloader.php';
WpPopPop_Autoloader::register();

// Load Global Helper Utilities
require_once WPPOPPOP_PATH . 'includes/helpers.php';

// Register Database Installer & Activation Routine
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
        $this->init_components();
    }

    private function init_components() {
        new WpPopPop_Hooks();
        new WpPopPop_Capabilities();
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
