<?php
/**
 * Plugin Name: WpPopPop
 * Description: Fully functional drag-and-drop popup builder inspired by Green Popups.
 * Version: 1.1.0
 * Author: WpPopPop Team
 */

if (!defined('ABSPATH')) {
    exit;
}

define('WPPOPPOP_VERSION', '1.1.0');
define('WPPOPPOP_PATH', plugin_dir_path(__FILE__));
define('WPPOPPOP_URL', plugin_dir_url(__FILE__));

// Activation hook for custom database tables
register_activation_hook(__FILE__, function () {
    global $wpdb;
    $table_name = $wpdb->prefix . 'wppoppop_items';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        uid varchar(32) NOT NULL,
        title varchar(255) NOT NULL,
        data longtext NOT NULL,
        status varchar(20) DEFAULT 'publish' NOT NULL,
        impressions bigint(20) DEFAULT 0 NOT NULL,
        submissions bigint(20) DEFAULT 0 NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY uid (uid)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
});

// Require modules
require_once WPPOPPOP_PATH . 'includes/class-wppoppop-admin.php';
require_once WPPOPPOP_PATH . 'includes/class-wppoppop-ajax.php';
require_once WPPOPPOP_PATH . 'includes/class-wppoppop-front.php';

add_action('plugins_loaded', function () {
    new WpPopPop_Admin();
    new WpPopPop_Ajax();
    new WpPopPop_Front();
});
