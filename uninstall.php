<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * WpPopPop Uninstall Procedure
 * Executes automatically when the plugin is deleted via the WordPress Admin.
 * On a multisite network every site is cleaned in turn, and each site keeps its data
 * unless its own "Clean on uninstall" setting is on.
 */
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

if (!function_exists('wppoppop_uninstall_site')) {
    /**
     * Removes the WpPopPop data of the current site when its "Clean on uninstall" setting is on.
     * Tables added by WpPopPop Pro are removed by Pro.
     */
    function wppoppop_uninstall_site() {
        global $wpdb;

        $wppoppop_uninstall_settings = get_option('wppoppop_settings', []);
        if (!is_array($wppoppop_uninstall_settings) || empty($wppoppop_uninstall_settings['clean_on_uninstall'])) {
            return;
        }

        // 1. Drop the plugin's custom database tables.
        $wppoppop_tables = [
            $wpdb->prefix . 'wppoppop_items',
            $wpdb->prefix . 'wppoppop_submissions',
            $wpdb->prefix . 'wppoppop_logs',
            $wpdb->prefix . 'wppoppop_transactions',
        ];

        foreach ($wppoppop_tables as $wppoppop_table) {
            $wpdb->query('DROP TABLE IF EXISTS `' . esc_sql($wppoppop_table) . '`'); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        }

        // 2. Remove all options
        delete_option('wppoppop_settings');
        delete_option('wppoppop_saved_defaults');
        delete_option('wppoppop_db_version');
        delete_option('wppoppop_cookie_epoch');
        delete_option('wppoppop_activated');
        delete_option('wppoppop_schema_is_read');
        delete_option('wppoppop_modules_state');
        delete_option('wppoppop_modules_orphans');
        delete_option('wppoppop_modules_seeded');
        delete_option('wppoppop_module_categories');
        delete_option('wppoppop_module_category_map');
        delete_option('wppoppop_module_image_map');

        // 2b. Remove the per-user builder guide progress.
        delete_metadata('user', 0, 'wppoppop_guide_state', '', true);

        // 3. Purge cached transients
        $wpdb->query( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                $wpdb->esc_like('_transient_wppoppop_') . '%',
                $wpdb->esc_like('_transient_timeout_wppoppop_') . '%'
            )
        );

        // 4. Remove the uploaded data and module folders of this site (symlinks are unlinked, never followed).
        $wppoppop_upload_dir = wp_upload_dir();
        foreach (['wppoppop', 'wppoppop-modules'] as $wppoppop_sub) {
            $wppoppop_root = $wppoppop_upload_dir['basedir'] . '/' . $wppoppop_sub;
            if (!is_dir($wppoppop_root) || is_link($wppoppop_root)) {
                continue;
            }
            $wppoppop_items = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($wppoppop_root, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($wppoppop_items as $wppoppop_item) {
                if ($wppoppop_item->isLink() || !$wppoppop_item->isDir()) {
                    wp_delete_file($wppoppop_item->getPathname());
                } else {
                    rmdir($wppoppop_item->getPathname()); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_rmdir
                }
            }
            rmdir($wppoppop_root); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_rmdir
        }
    }
}

if (is_multisite()) {
    $wppoppop_batch  = 100;
    $wppoppop_offset = 0;
    do {
        $wppoppop_site_ids = get_sites([
            'fields'  => 'ids',
            'number'  => $wppoppop_batch,
            'offset'  => $wppoppop_offset,
            'orderby' => 'id',
            'order'   => 'ASC',
        ]);
        foreach ($wppoppop_site_ids as $wppoppop_site_id) {
            switch_to_blog((int) $wppoppop_site_id);
            wppoppop_uninstall_site();
            restore_current_blog();
        }
        $wppoppop_offset += $wppoppop_batch;
    } while (count($wppoppop_site_ids) === $wppoppop_batch);
    $wppoppop_network_settings = get_site_option('wppoppop_network_settings', []);
    if (is_array($wppoppop_network_settings) && !empty($wppoppop_network_settings['clean_on_uninstall'])) {
        delete_site_option('wppoppop_network_settings');
    }
} else {
    wppoppop_uninstall_site();
}
