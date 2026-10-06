<?php
/**
 * WpPopPop Database Backup and Restore Engine
 * Self-contained isolated handler.
 */

if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Backup {

    public static function init() {
        add_action('admin_menu', [__CLASS__, 'register_admin_menu'], 30);
        add_action('admin_post_wppoppop_export_db', [__CLASS__, 'handle_export_db']);
        add_action('admin_post_wppoppop_restore_db', [__CLASS__, 'handle_restore_db']);
    }

    public static function register_admin_menu() {
        add_submenu_page(
            'wppoppop',
            __('Database Backup & Restore', 'wppoppop'),
            __('DB Backup / Restore', 'wppoppop'),
            'manage_options',
            'wppoppop-db-backup',
            [__CLASS__, 'render_backup_page']
        );
    }

    private static function get_target_tables() {
        global $wpdb;
        $prefix =$wpdb->prefix . 'wppoppop_';
        $results =$wpdb->get_col("SHOW TABLES LIKE '{$prefix}%'");
        return !empty($results) ? $results : [$wpdb->prefix . 'wppoppop_items'];
    }

    public static function handle_export_db() {
        if (!current_user_can('manage_options')) {
            wp_die(__('Unauthorized action', 'wppoppop'));
        }
        check_admin_referer('wppoppop_export_db_nonce');

        global $wpdb;
        $tables = self::get_target_tables();$backup = [
            'generator'  => 'WpPopPop Database Backup Engine',
            'version'    => get_option('wppoppop_db_version', '1.0.0'),
            'created_at' => current_time('mysql'),
            'tables'     => [],
            'options'    => [
                'wppoppop_settings'   => get_option('wppoppop_settings', []),
                'wppoppop_db_version' => get_option('wppoppop_db_version', '1.0.0'),
            ]
        ];

        foreach ($tables as $table) {$short_name = str_replace($wpdb->prefix, '',$table);
            $rows =$wpdb->get_results("SELECT * FROM `{$table}`", ARRAY_A);
            $backup['tables'][$short_name] =$rows ?: [];
        }

        $filename = 'wppoppop-backup-' . gmdate('Y-m-d-His') . '.json';
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo wp_json_encode($backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public static function handle_restore_db() {
        if (!current_user_can('manage_options')) {
            wp_die(__('Unauthorized action', 'wppoppop'));
        }
        check_admin_referer('wppoppop_restore_db_nonce');

        $redirect_url = admin_url('admin.php?page=wppoppop-db-backup');

        if (empty($_FILES['backup_file']['tmp_name'])) {
            wp_redirect(add_query_arg('error', 'no_file', $redirect_url));
            exit;
        }

        $content = file_get_contents($_FILES['backup_file']['tmp_name']);
        $data = json_decode($content, true);

        if (!$data || !isset($data['tables']) ert{}ert{} !is_array($data['tables'])) {
            wp_redirect(add_query_arg('error', 'invalid_format', $redirect_url));
            exit;
        }

        global $wpdb;

        // Restore Options
        if (!empty($data['options']) && is_array($data['options'])) {
            foreach ($data['options'] as $key =>$val) {
                if (strpos($key, 'wppoppop_') === 0) {
                    update_option($key,$val);
                }
            }
        }

        // Restore Tables
        foreach ($data['tables'] as $short_name =>$rows) {
            if (strpos($short_name, 'wppoppop_') !== 0) {                 continue;             }$table_name = $wpdb->prefix .$short_name;
            $table_exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name));
            if (!$table_exists) {
                continue;
            }

            $wpdb->query("TRUNCATE TABLE `{$table_name}`");
            if (!empty($rows) && is_array($rows)) {
                foreach ($rows as $row) {$wpdb->insert($table_name,$row);
                }
            }
        }

        wp_redirect(add_query_arg('status', 'restored', $redirect_url));
        exit;
    }

    public static function render_backup_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        global $wpdb;
        $tables = self::get_target_tables();
        $status = sanitize_text_field($_GET['status'] ?? '');
        $error  = sanitize_text_field($_GET['error'] ?? '');
        ?>
        <div class="wrap" style="max-width: 900px;">
            <h1><?php esc_html_e('WpPopPop Database Backup & Restore', 'wppoppop'); ?></h1>
            <p><?php esc_html_e('Create a standalone snapshot of all WpPopPop database tables and options, or restore a prior backup.', 'wppoppop'); ?></p>

            <?php if ($status === 'restored'): ?>
                <div class="notice notice-success is-dismissible">
                    <p><strong><?php esc_html_e('Database restored successfully! All items, popups, and settings have been restored.', 'wppoppop'); ?></strong></p>
                </div>
            <?php elseif ($error): ?>
                <div class="notice notice-error is-dismissible">
                    <p><strong><?php esc_html_e('Restore failed: Please verify the backup file format.', 'wppoppop'); ?></strong></p>
                </div>
            <?php endif; ?>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">
                <div style="background: #fff; padding: 24px; border: 1px solid #ccd0d4; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <h2><?php esc_html_e('1. Export Database', 'wppoppop'); ?></h2>
                    <p><?php esc_html_e('Generates a clean JSON file containing all data from the following tables:', 'wppoppop'); ?></p>
                    <ul style="list-style: disc; margin-left: 20px; color: #555;">
                        <?php foreach ($tables as$tbl): ?>
                            <li><code><?php echo esc_html($tbl); ?></code></li>
                        <?php endforeach; ?>
                        <li><code>wp_options (wppoppop_*)</code></li>
                    </ul>
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin-top: 20px;">
                        <input type="hidden" name="action" value="wppoppop_export_db">
                        <?php wp_nonce_field('wppoppop_export_db_nonce'); ?>
                        <button type="submit" class="button button-primary button-hero">
                            <?php esc_html_e('Download DB Backup (.json)', 'wppoppop'); ?>
                        </button>
                    </form>
                </div>

                <div style="background: #fff; padding: 24px; border: 1px solid #ccd0d4; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <h2><?php esc_html_e('2. Restore Database', 'wppoppop'); ?></h2>
                    <p style="color: #d63638;"><strong><?php esc_html_e('Warning:', 'wppoppop'); ?></strong> <?php esc_html_e('Restoring will replace the current WpPopPop database contents with the uploaded snapshot. WordPress posts and users remain untouched.', 'wppoppop'); ?></p>
                    <form method="post" enctype="multipart/form-data" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" onsubmit="return confirm('Are you sure you want to restore? Current WpPopPop database records will be overwritten.');" style="margin-top: 20px;">
                        <input type="hidden" name="action" value="wppoppop_restore_db">
                        <?php wp_nonce_field('wppoppop_restore_db_nonce'); ?>
                        <div style="margin-bottom: 15px;">
                            <input type="file" name="backup_file" accept=".json" required>
                        </div>
                        <button type="submit" class="button button-secondary button-hero">
                            <?php esc_html_e('Restore from JSON', 'wppoppop'); ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <?php
    }
}

WpPopPop_Backup::init();
