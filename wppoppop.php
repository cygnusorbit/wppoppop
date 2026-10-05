<?php
/**
 * Plugin Name: WpPopPop
 * Description: Fully functional drag-and-drop popup builder inspired by Green Popups.
 * Version: 1.0.0
 * Author: WpPopPop Team
 */

if (!defined('ABSPATH')) {
    exit;
}

define('WPPOPPOP_VERSION', '1.0.0');
define('WPPOPPOP_PATH', plugin_dir_path(__FILE__));
define('WPPOPPOP_URL', plugin_dir_url(__FILE__));

// Global audit logging helper
if (!function_exists('wppoppop_log_event')) {
    function wppoppop_log_event($event_type, $message, $context = []) {
        global $wpdb;
        $table_logs = $wpdb->prefix . 'wppoppop_logs';
        if ($wpdb->get_var("SHOW TABLES LIKE '{$table_logs}'") === $table_logs) {
            $wpdb->insert($table_logs, [
                'event_type' => sanitize_text_field($event_type),
                'message'    => sanitize_text_field($message),
                'context'    => wp_json_encode($context),
                'created_at' => current_time('mysql')
            ], ['%s', '%s', '%s', '%s']);
        }
    }
}

// Complete database migration and self-healing schema
function wppoppop_run_db_migration() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    // 1. Popups table
    $sql_items = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}wppoppop_items (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        uid varchar(64) NOT NULL,
        title varchar(255) NOT NULL,
        data longtext NOT NULL,
        status varchar(20) DEFAULT 'publish' NOT NULL,
        impressions bigint(20) DEFAULT 0 NOT NULL,
        submissions bigint(20) DEFAULT 0 NOT NULL,
        confirmations bigint(20) DEFAULT 0 NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY uid (uid)
    ) $charset_collate;";
    dbDelta($sql_items);

    // 2. Submissions / Leads table
    $sql_subs = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}wppoppop_submissions (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        popup_uid varchar(64) NOT NULL,
        email varchar(255) DEFAULT '' NOT NULL,
        country_code varchar(10) DEFAULT '' NOT NULL,
        fields_data longtext NOT NULL,
        user_ip varchar(100) DEFAULT '' NOT NULL,
        user_agent text,
        status varchar(20) DEFAULT 'confirmed' NOT NULL,
        confirm_token varchar(64) DEFAULT '' NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id),
        KEY popup_uid (popup_uid)
    ) $charset_collate;";
    dbDelta($sql_subs);

    // 3. A/B Testing Campaigns table
    $sql_camps = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}wppoppop_campaigns (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        uid varchar(64) NOT NULL,
        title varchar(255) NOT NULL,
        popup_uids longtext NOT NULL,
        status varchar(20) DEFAULT 'active' NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY uid (uid)
    ) $charset_collate;";
    dbDelta($sql_camps);

    // 4. Audit & Event Logs table
    $sql_logs = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}wppoppop_logs (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        event_type varchar(50) NOT NULL,
        message text NOT NULL,
        context longtext,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";
    dbDelta($sql_logs);

    // 5. Payment Transactions table
    $sql_txs = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}wppoppop_transactions (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        popup_uid varchar(64) NOT NULL,
        email varchar(255) NOT NULL,
        amount decimal(10,2) DEFAULT '0.00' NOT NULL,
        currency varchar(10) DEFAULT 'USD' NOT NULL,
        gateway varchar(50) DEFAULT 'Stripe' NOT NULL,
        transaction_id varchar(100) NOT NULL,
        status varchar(20) DEFAULT 'completed' NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id),
        KEY popup_uid (popup_uid)
    ) $charset_collate;";
    dbDelta($sql_txs);
}
register_activation_hook(__FILE__, 'wppoppop_run_db_migration');

// Autoload modules
require_once WPPOPPOP_PATH . 'includes/class-wppoppop-admin.php';
require_once WPPOPPOP_PATH . 'includes/class-wppoppop-ajax.php';
require_once WPPOPPOP_PATH . 'includes/class-wppoppop-front.php';

add_action('plugins_loaded', function () {
    wppoppop_run_db_migration();
    new WpPopPop_Admin();
    new WpPopPop_Ajax();
    new WpPopPop_Front();
});
