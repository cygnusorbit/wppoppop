<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Admin_Pages {
    public function __construct() {
        add_action('admin_post_wppoppop_bulk_export', [$this, 'handle_bulk_export']);
    }

    public function handle_bulk_export() {
        if (!current_user_can('manage_options')) {
            wp_die(__('Unauthorized action', 'wppoppop'));
        }
        check_admin_referer('wppoppop_tools_action', 'wppoppop_tools_nonce');

        global $wpdb;
        $items = $wpdb->get_results("SELECT uid, title, data, status FROM {$wpdb->prefix}wppoppop_items", ARRAY_A);
        $campaigns = $wpdb->get_results("SELECT uid, title, popup_uids, status FROM {$wpdb->prefix}wppoppop_campaigns", ARRAY_A);

        $backup_payload = [
            'generator' => 'WpPopPop ' . (defined('WPPOPPOP_VERSION') ? WPPOPPOP_VERSION : '1.0.0'),
            'exported'  => current_time('mysql'),
            'items'     => $items ?: [],
            'campaigns' => $campaigns ?: []
        ];

        $json_out = wp_json_encode($backup_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $filename = 'wppoppop-bulk-backup-' . gmdate('Y-m-d') . '.json';

        // Clear any residual output buffer
        if (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($json_out));
        header('Pragma: no-cache');
        header('Expires: 0');

        echo $json_out;
        exit;
    }

    public function render_dashboard() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';
        $popups = $wpdb->get_results("SELECT * FROM {$table_name} ORDER BY id DESC");
        include WPPOPPOP_PATH . 'templates/dashboard-view.php';
    }

    public function render_builder() {
        include WPPOPPOP_PATH . 'templates/builder-view.php';
    }

    public function render_ab() {
        global $wpdb;
        $campaigns = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}wppoppop_campaigns ORDER BY id DESC");
        include WPPOPPOP_PATH . 'templates/ab-view.php';
    }

    public function render_submissions() {
        global $wpdb;
        $submissions = $wpdb->get_results("SELECT s.*, i.title as popup_title FROM {$wpdb->prefix}wppoppop_submissions s LEFT JOIN {$wpdb->prefix}wppoppop_items i ON s.popup_uid = i.uid ORDER BY s.id DESC LIMIT 200");
        include WPPOPPOP_PATH . 'templates/submissions-view.php';
    }

    public function render_log() {
        include WPPOPPOP_PATH . 'templates/log-view.php';
    }

    public function render_stats() {
        include WPPOPPOP_PATH . 'templates/stats-view.php';
    }

    public function render_field_analytics() {
        include WPPOPPOP_PATH . 'templates/field-analytics-view.php';
    }

    public function render_payments() {
        global $wpdb;
        $transactions = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}wppoppop_transactions ORDER BY id DESC LIMIT 100");
        include WPPOPPOP_PATH . 'templates/payments-view.php';
    }

    public function render_library() {
        include WPPOPPOP_PATH . 'templates/library-view.php';
    }

    public function render_settings() {
        $settings = get_option('wppoppop_settings', []);
        include WPPOPPOP_PATH . 'templates/settings-view.php';
    }

    public function render_tools() {
        include WPPOPPOP_PATH . 'templates/tools-view.php';
    }
}
