<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Admin_Pages {
    public function render_dashboard() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';
        $popups = $wpdb->get_results("SELECT * FROM {$table_name} ORDER BY id DESC");
        include WPPOPPOP_PATH . 'templates/dashboard-view.php';
    }

    public function render_builder() {
        global $wpdb;
        $uid = isset($_GET['uid']) ? sanitize_key($_GET['uid']) : '';
        $popup = null;
        if (!empty($uid)) {
            $table_name = $wpdb->prefix . 'wppoppop_items';
            $popup = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_name} WHERE uid = %s", $uid));
        }
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
