<?php
/**
 * Plugin Name: WpPopPop
 * Description: Fully functional drag-and-drop popup builder inspired by Green Popups.
 * Version: 2.4.0
 * Author: WpPopPop Team
 */

if (!defined('ABSPATH')) {
    exit;
}

define('WPPOPPOP_VERSION', '2.4.0');
define('WPPOPPOP_PATH', plugin_dir_path(__FILE__));
define('WPPOPPOP_URL', plugin_dir_url(__FILE__));

function wppoppop_get_setting($key, $default = '') {
    $settings = get_option('wppoppop_settings', []);
    return isset($settings[$key]) ? $settings[$key] : $default;
}

function wppoppop_log_event($type, $message, $context = []) {
    global $wpdb;
    $table_logs = $wpdb->prefix . 'wppoppop_logs';
    $wpdb->insert(
        $table_logs,
        [
            'event_type' => sanitize_text_field($type),
            'message'    => sanitize_text_field($message),
            'context'    => wp_json_encode($context)
        ],
        ['%s', '%s', '%s']
    );
}

function wppoppop_install_schema() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $table_items        = $wpdb->prefix . 'wppoppop_items';
    $table_submissions  = $wpdb->prefix . 'wppoppop_submissions';
    $table_campaigns    = $wpdb->prefix . 'wppoppop_campaigns';
    $table_transactions = $wpdb->prefix . 'wppoppop_transactions';
    $table_downloads    = $wpdb->prefix . 'wppoppop_downloads';
    $table_logs         = $wpdb->prefix . 'wppoppop_logs';

    $sql = "CREATE TABLE {$table_items} (
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
    ) $charset_collate;
    CREATE TABLE {$table_submissions} (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        popup_uid varchar(64) NOT NULL,
        email varchar(255) NOT NULL,
        fields_data longtext NOT NULL,
        status varchar(20) DEFAULT 'confirmed' NOT NULL,
        confirm_token varchar(64) DEFAULT '' NOT NULL,
        country_code varchar(4) DEFAULT '' NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id),
        KEY popup_uid (popup_uid),
        KEY confirm_token (confirm_token)
    ) $charset_collate;
    CREATE TABLE {$table_campaigns} (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        uid varchar(64) NOT NULL,
        title varchar(255) NOT NULL,
        popup_uids text NOT NULL,
        status varchar(20) DEFAULT 'active' NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY uid (uid)
    ) $charset_collate;
    CREATE TABLE {$table_transactions} (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        popup_uid varchar(64) NOT NULL,
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
        popup_uid varchar(64) NOT NULL,
        token varchar(64) NOT NULL,
        file_url text NOT NULL,
        downloads_count int(11) DEFAULT 0 NOT NULL,
        expires_at datetime NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY token (token)
    ) $charset_collate;
    CREATE TABLE {$table_logs} (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        event_type varchar(50) NOT NULL,
        message text NOT NULL,
        context longtext NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    $wpdb->query("ALTER TABLE `{$table_items}` MODIFY COLUMN `uid` varchar(64) NOT NULL;");
    $wpdb->query("ALTER TABLE `{$table_submissions}` MODIFY COLUMN `popup_uid` varchar(64) NOT NULL;");

    update_option('wppoppop_db_version', WPPOPPOP_VERSION);
}

register_activation_hook(__FILE__, 'wppoppop_install_schema');

require_once WPPOPPOP_PATH . 'includes/class-wppoppop-widget.php';
require_once WPPOPPOP_PATH . 'includes/class-wppoppop-admin.php';
require_once WPPOPPOP_PATH . 'includes/class-wppoppop-ajax.php';
require_once WPPOPPOP_PATH . 'includes/class-wppoppop-rest.php';
require_once WPPOPPOP_PATH . 'includes/class-wppoppop-front.php';
require_once WPPOPPOP_PATH . 'includes/class-wppoppop-addons.php';

add_action('widgets_init', function () {
    register_widget('WpPopPop_Widget');
});

add_action('plugins_loaded', function () {
    global $wpdb;
    $table_items = $wpdb->prefix . 'wppoppop_items';

    if (get_option('wppoppop_db_version') !== WPPOPPOP_VERSION || 
        $wpdb->get_var("SHOW TABLES LIKE '{$table_items}'") !== $table_items) {
        wppoppop_install_schema();
    }

    new WpPopPop_Admin();
    new WpPopPop_Ajax();
    new WpPopPop_Rest();
    new WpPopPop_Front();
    new WpPopPop_Addons();
});

add_action('template_redirect', function () {
    if (isset($_GET['wppoppop_confirm']) && !empty($_GET['wppoppop_confirm'])) {
        global $wpdb;
        $token = sanitize_key($_GET['wppoppop_confirm']);
        $table_subs  = $wpdb->prefix . 'wppoppop_submissions';
        $table_items = $wpdb->prefix . 'wppoppop_items';

        $sub = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_subs} WHERE confirm_token = %s AND status = 'pending'", $token));
        if ($sub) {
            $wpdb->update($table_subs, ['status' => 'confirmed', 'confirm_token' => ''], ['id' => $sub->id]);
            $wpdb->query($wpdb->prepare("UPDATE {$table_items} SET confirmations = confirmations + 1 WHERE uid = %s", $sub->popup_uid));
            wppoppop_log_event('confirmation', 'Subscriber email verified: ' . $sub->email, ['uid' => $sub->popup_uid]);

            wp_die('
                <div style="max-width:550px;margin:80px auto;text-align:center;font-family:sans-serif;padding:30px;background:#fff;border:1px solid #ddd;border-radius:6px;box-shadow:0 4px 15px rgba(0,0,0,0.08);">
                    <h2 style="color:#00a32a;margin-top:0;">Subscription Confirmed!</h2>
                    <p style="color:#555;font-size:16px;">Thank you! Your email address has been successfully verified.</p>
                    <a href="' . esc_url(home_url('/')) . '" style="display:inline-block;margin-top:15px;padding:10px 20px;background:#2271b1;color:#fff;text-decoration:none;border-radius:4px;font-weight:600;">Back to Home</a>
                </div>',
                'Subscription Confirmed',
                ['response' => 200]
            );
        }
    }
});
