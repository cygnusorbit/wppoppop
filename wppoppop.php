<?php
/**
 * Plugin Name:       WP Pop Pop
 * Plugin URI:        https://example.com/wppoppop
 * Description:       High-performance, modular layered popup plugin.
 * Version:           1.0.0
 * Author:            Your Name
 * License:           GPL-2.0-or-later
 * Text Domain:       wppoppop
 */

defined('ABSPATH') || exit;

define('WPPOPPOP_PATH', plugin_dir_path(__FILE__));
define('WPPOPPOP_URL', plugin_dir_url(__FILE__));
define('WPPOPPOP_VERSION', '1.0.0');

spl_autoload_register(function ($class) {
    $prefix = 'WPPopPop\\';$base_dir = WPPOPPOP_PATH . 'includes/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class,$len) !== 0) {
        return;
    }
    $relative_class = substr($class, $len);$file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

add_action('plugins_loaded', function () {
    \WPPopPop\Core\Plugin::get_instance()->init();
});
