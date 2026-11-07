<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * WpPopPop Uninstall Procedure
 * Executes automatically when the plugin is deleted via the WordPress Admin.
 */
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

$settings = get_option('wppoppop_settings', []);
$clean_on_uninstall = !empty($settings['clean_on_uninstall']);

if ($clean_on_uninstall) {
    global $wpdb;

    // 1. Drop all 5 custom database tables
    $tables = [$wpdb->prefix . 'wppoppop_items',
        $wpdb->prefix . 'wppoppop_submissions',$wpdb->prefix . 'wppoppop_campaigns',
        $wpdb->prefix . 'wppoppop_logs',$wpdb->prefix . 'wppoppop_transactions'
    ];

    foreach ($tables as $table) {$wpdb->query("DROP TABLE IF EXISTS `{$table}`");
    }

    // 2. Remove all options
    delete_option('wppoppop_settings');
    delete_option('wppoppop_db_version');
    delete_option('wppoppop_cookie_epoch');
    delete_option('wppoppop_activated');
    delete_option('wppoppop_schema_is_read');
    delete_option('wppoppop_modules_state');
    delete_option('wppoppop_modules_orphans');
    delete_option('wppoppop_modules_seeded');
    delete_option('wppoppop_module_categories');
    delete_option('wppoppop_module_category_map');

    // 3. Purge cached transients
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_wppoppop_%' OR option_name LIKE '_transient_timeout_wppoppop_%'");

    // 4. Remove uploaded files directory if present
    $upload_dir = wp_upload_dir();
    $wppoppop_dir =$upload_dir['basedir'] . '/wppoppop';
    if (is_dir($wppoppop_dir)) {$files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($wppoppop_dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($files as$fileinfo) {
            $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
            @$todo($fileinfo->getRealPath());
        }
        @rmdir($wppoppop_dir);
    }

    // 5. Remove uploaded modules (symlinks are unlinked, never followed)
    $wppoppop_mod_dir = $upload_dir['basedir'] . '/wppoppop-modules';
    if (is_dir($wppoppop_mod_dir) && !is_link($wppoppop_mod_dir)) {
        $mod_files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($wppoppop_mod_dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($mod_files as $mod_file) {
            if ($mod_file->isLink() || !$mod_file->isDir()) {
                @unlink($mod_file->getPathname());
            } else {
                @rmdir($mod_file->getPathname());
            }
        }
        @rmdir($wppoppop_mod_dir);
    }
}
