<?php
/**
 * Plugin Name: WpPopPop
 * Description: Fully functional drag-and-drop popup builder inspired by Green Popups.
 * Version: 1.4.0
 * Author: WpPopPop Team
 */

if (!defined('ABSPATH')) {
    exit;
}

define('WPPOPPOP_VERSION', '1.4.0');
define('WPPOPPOP_PATH', plugin_dir_path(__FILE__));
define('WPPOPPOP_URL', plugin_dir_url(__FILE__));

/**
 * Creates and updates tables for Popups, Submissions, A/B Campaigns, Payments, and Downloads.
 */
function wppoppop_install_schema() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $table_items        = $wpdb->prefix . 'wppoppop_items';
    $table_submissions  = $wpdb->prefix . 'wppoppop_submissions';
    $table_campaigns    = $wpdb->prefix . 'wppoppop_campaigns';
    $table_transactions = $wpdb->prefix . 'wppoppop_transactions';
    $table_downloads    = $wpdb->prefix . 'wppoppop_downloads';

    $sql = "CREATE TABLE {$table_items} (
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
    ) $charset_collate;
    CREATE TABLE {$table_submissions} (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        popup_uid varchar(32) NOT NULL,
        email varchar(255) NOT NULL,
        fields_data longtext NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id),
        KEY popup_uid (popup_uid)
    ) $charset_collate;
    CREATE TABLE {$table_campaigns} (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        uid varchar(32) NOT NULL,
        title varchar(255) NOT NULL,
        popup_uids text NOT NULL,
        status varchar(20) DEFAULT 'active' NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY uid (uid)
    ) $charset_collate;
    CREATE TABLE {$table_transactions} (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        popup_uid varchar(32) NOT NULL,
        email varchar(255) NOT NULL,
        amount decimal(10,2) NOT NULL,
        currency varchar(10) DEFAULT 'USD' NOT NULL,
        gateway varchar(50) NOT NULL,
        transaction_id varchar(100) NOT NULL,
        status varchar(20) DEFAULT 'completed' NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id),
        KEY popup_uid (popup_uid)
    ) $charset_collate;
    CREATE TABLE {$table_downloads} (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        popup_uid varchar(32) NOT NULL,
        token varchar(64) NOT NULL,
        file_url text NOT NULL,
        downloads_count int(11) DEFAULT 0 NOT NULL,
        expires_at datetime NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY token (token)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    // Direct fallback execution
    $wpdb->query("CREATE TABLE IF NOT EXISTS `{$table_transactions}` (
        `id` bigint(20) NOT NULL AUTO_INCREMENT,
        `popup_uid` varchar(32) NOT NULL,
        `email` varchar(255) NOT NULL,
        `amount` decimal(10,2) NOT NULL,
        `currency` varchar(10) DEFAULT 'USD' NOT NULL,
        `gateway` varchar(50) NOT NULL,
        `transaction_id` varchar(100) NOT NULL,
        `status` varchar(20) DEFAULT 'completed' NOT NULL,
        `created_at` datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY (`id`),
        KEY `popup_uid` (`popup_uid`)
    ) ENGINE=InnoDB {$charset_collate};");

    $wpdb->query("CREATE TABLE IF NOT EXISTS `{$table_downloads}` (
        `id` bigint(20) NOT NULL AUTO_INCREMENT,
        `popup_uid` varchar(32) NOT NULL,
        `token` varchar(64) NOT NULL,
        `file_url` text NOT NULL,
        `downloads_count` int(11) DEFAULT 0 NOT NULL,
        `expires_at` datetime NOT NULL,
        `created_at` datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `token` (`token`)
    ) ENGINE=InnoDB {$charset_collate};");

    update_option('wppoppop_db_version', WPPOPPOP_VERSION);
}

register_activation_hook(__FILE__, 'wppoppop_install_schema');

require_once WPPOPPOP_PATH . 'includes/class-wppoppop-admin.php';
require_once WPPOPPOP_PATH . 'includes/class-wppoppop-ajax.php';
require_once WPPOPPOP_PATH . 'includes/class-wppoppop-front.php';
require_once WPPOPPOP_PATH . 'includes/class-wppoppop-addons.php';

add_action('plugins_loaded', function () {
    global $wpdb;
    $table_tx = $wpdb->prefix . 'wppoppop_transactions';
    
    if (get_option('wppoppop_db_version') !== WPPOPPOP_VERSION || 
        $wpdb->get_var("SHOW TABLES LIKE '{$table_tx}'") !== $table_tx) {
        wppoppop_install_schema();
    }

    new WpPopPop_Admin();
    new WpPopPop_Ajax();
    new WpPopPop_Front();
    new WpPopPop_Addons();
});
