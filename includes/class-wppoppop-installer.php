<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Installer {
    public static function activate() {
        self::create_tables();
        self::init_defaults();
        update_option('wppoppop_db_version', WPPOPPOP_VERSION);
    }

    public static function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        // 1. Popups Table
        $table_items = $wpdb->prefix . 'wppoppop_items';
        $sql_items = "CREATE TABLE {$table_items} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            uid varchar(64) NOT NULL,
            title varchar(255) NOT NULL,
            data longtext NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'publish',
            impressions bigint(20) NOT NULL DEFAULT 0,
            submissions bigint(20) NOT NULL DEFAULT 0,
            confirmations bigint(20) NOT NULL DEFAULT 0,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY uid (uid),
            KEY status (status)
        ) {$charset_collate};";
        dbDelta($sql_items);

        // 2. Submissions Table
        $table_submissions = $wpdb->prefix . 'wppoppop_submissions';
        $sql_submissions = "CREATE TABLE {$table_submissions} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            popup_uid varchar(64) NOT NULL,
            email varchar(255) NOT NULL,
            fields_data longtext NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'confirmed',
            confirm_token varchar(64) DEFAULT '',
            country_code varchar(8) DEFAULT '',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY popup_uid (popup_uid),
            KEY email (email)
        ) {$charset_collate};";
        dbDelta($sql_submissions);

        // 3. A/B Campaigns Table
        $table_campaigns = $wpdb->prefix . 'wppoppop_campaigns';
        $sql_campaigns = "CREATE TABLE {$table_campaigns} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            uid varchar(64) NOT NULL,
            title varchar(255) NOT NULL,
            popup_uids longtext NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'active',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY uid (uid)
        ) {$charset_collate};";
        dbDelta($sql_campaigns);

        // 4. Activity Logs Table
        $table_logs = $wpdb->prefix . 'wppoppop_logs';
        $sql_logs = "CREATE TABLE {$table_logs} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            event_type varchar(64) NOT NULL,
            message text NOT NULL,
            payload longtext DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY event_type (event_type)
        ) {$charset_collate};";
        dbDelta($sql_logs);

        // 5. Transactions Table
        $table_transactions = $wpdb->prefix . 'wppoppop_transactions';
        $sql_transactions = "CREATE TABLE {$table_transactions} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            popup_uid varchar(64) NOT NULL,
            email varchar(255) NOT NULL,
            amount decimal(10,2) NOT NULL DEFAULT '0.00',
            currency varchar(10) NOT NULL DEFAULT 'USD',
            gateway varchar(50) NOT NULL DEFAULT 'Stripe',
            transaction_id varchar(100) NOT NULL DEFAULT '',
            status varchar(20) NOT NULL DEFAULT 'completed',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY popup_uid (popup_uid),
            KEY transaction_id (transaction_id)
        ) {$charset_collate};";
        dbDelta($sql_transactions);
    }

    public static function init_defaults() {
        if (!get_option('wppoppop_settings')) {
            update_option('wppoppop_settings', [
                'sender_name'       => 'WpPopPop',
                'sender_email'      => get_option('admin_email'),
                'preload_popups'    => 1,
                'preload_events'    => 1,
                'ga_tracking'       => 0,
                'google_fonts'      => 1,
                'font_awesome'      => 1,
                'air_datepicker'    => 1,
                'no_air_datepicker' => 0,
                'jquery_mask'       => 1,
                'js_parser'         => 0,
                'signature_pad'     => 1,
                'range_slider'      => 1,
                'adblock_detector'  => 0,
                'csv_separator'     => ',',
                'custom_fonts'      => '',
                'email_validation'  => 'basic',
                'geoip_service'     => 'none',
                'user_uploads'      => 'keep',
                'custom_css'        => '',
                'custom_js'         => ''
            ]);
        }

        if (!get_option('wppoppop_cookie_epoch')) {
            update_option('wppoppop_cookie_epoch', time());
        }
    }
}
