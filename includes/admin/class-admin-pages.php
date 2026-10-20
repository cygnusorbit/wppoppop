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

    public function render_overview() {
        global $wpdb;
        $items = $wpdb->prefix . 'wppoppop_items';
        $dash_menu_popups   = $wpdb->get_results("SELECT uid, title FROM {$items} ORDER BY id DESC LIMIT 30");
        $dash_recent_popups = $wpdb->get_results("SELECT uid, title, submissions FROM {$items} ORDER BY id DESC LIMIT 5");
        $subs = $wpdb->prefix . 'wppoppop_submissions';
        $dash_recent_subs = $wpdb->get_results("SELECT s.id, s.popup_uid, s.email, s.status, s.is_read, s.fields_data, s.created_at, i.title AS popup_title FROM {$subs} s LEFT JOIN {$items} i ON s.popup_uid = i.uid ORDER BY s.id DESC LIMIT 6");
        $dash_export_url  = add_query_arg([
            'action' => 'wppoppop_export_submissions',
            'nonce'  => wp_create_nonce('wppoppop_admin_nonce'),
        ], admin_url('admin-post.php'));
        include WPPOPPOP_PATH . 'templates/dashboard-overview.php';
    }

    public function render_builder() {
        global $wpdb;
        $uid = isset($_GET['uid']) ? sanitize_text_field(wp_unslash($_GET['uid'])) : '';
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
        $filter_uid = isset($_GET['popup_uid']) ? sanitize_text_field(wp_unslash($_GET['popup_uid'])) : '';
        $where = $filter_uid !== '' ? $wpdb->prepare(' WHERE s.popup_uid = %s', $filter_uid) : '';
        $submissions = $wpdb->get_results("SELECT s.*, i.title as popup_title FROM {$wpdb->prefix}wppoppop_submissions s LEFT JOIN {$wpdb->prefix}wppoppop_items i ON s.popup_uid = i.uid{$where} ORDER BY s.id DESC LIMIT 200");
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
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'wppoppop'));
        }

        $notice = '';
        if ('POST' === $_SERVER['REQUEST_METHOD'] && isset($_POST['wppoppop_settings_nonce'])) {
            if (check_admin_referer('wppoppop_save_settings_action', 'wppoppop_settings_nonce')) {
                $raw = isset($_POST['settings']) && is_array($_POST['settings']) ? wp_unslash($_POST['settings']) : wp_unslash($_POST);
                $existing = get_option('wppoppop_settings', []);
                if (!is_array($existing)) {
                    $existing = [];
                }

                if (!class_exists('WpPopPop_Admin_Settings')) {
                    require_once WPPOPPOP_PATH . 'includes/admin/class-admin-settings.php';
                }

                // Cookie epoch is server-owned: never trust the client value.
                $raw['cookie_epoch'] = isset($existing['cookie_epoch'])
                    ? (int) $existing['cookie_epoch']
                    : (int) get_option('wppoppop_cookie_epoch', 1);

                $clean = WpPopPop_Admin_Settings::sanitize_settings($raw);

                update_option('wppoppop_settings', $clean);
                if (function_exists('wppoppop_flush_settings_cache')) {
                    wppoppop_flush_settings_cache();
                }
                $notice = 'saved';
            }
        }

        $settings = get_option('wppoppop_settings', []);
        include WPPOPPOP_PATH . 'templates/settings-view.php';
    }

    public function render_theme() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'wppoppop'));
        }
        include WPPOPPOP_PATH . 'templates/theme-view.php';
    }

    public function render_tools() {
        include WPPOPPOP_PATH . 'templates/tools-view.php';
    }
}
