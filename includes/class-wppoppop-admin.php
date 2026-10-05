<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Admin {
    public function __construct() {
        add_action('admin_menu', [$this, 'register_menus']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    public function register_menus() {
        // Main Menu Item
        add_menu_page(
            'WpPopPop',
            'WpPopPop',
            'manage_options',
            'wppoppop',
            [$this, 'render_dashboard'],
            'dashicons-external',
            30
        );

        // All 9 Required Submenus Exactly Preserved
        add_submenu_page('wppoppop', 'Popups', 'Popups', 'manage_options', 'wppoppop', [$this, 'render_dashboard']);
        add_submenu_page('wppoppop', 'Create Popup', 'Create Popup', 'manage_options', 'wppoppop-builder', [$this, 'render_builder']);
        add_submenu_page('wppoppop', 'A/B Campaigns', 'A/B Campaigns', 'manage_options', 'wppoppop-ab', [$this, 'render_ab']);
        add_submenu_page('wppoppop', 'Log', 'Log', 'manage_options', 'wppoppop-log', [$this, 'render_log']);
        add_submenu_page('wppoppop', 'Stats', 'Stats', 'manage_options', 'wppoppop-stats', [$this, 'render_stats']);
        add_submenu_page('wppoppop', 'Field Analytics', 'Field Analytics', 'manage_options', 'wppoppop-field-analytics', [$this, 'render_field_analytics']);
        add_submenu_page('wppoppop', 'Transactions', 'Transactions', 'manage_options', 'wppoppop-payments', [$this, 'render_payments']);
        add_submenu_page('wppoppop', 'Popups Library', 'Popups Library', 'manage_options', 'wppoppop-library', [$this, 'render_library']);
        add_submenu_page('wppoppop', 'Settings', 'Settings', 'manage_options', 'wppoppop-settings', [$this, 'render_settings']);
    }

    public function enqueue_assets($hook) {
        $page = isset($_GET['page']) ? sanitize_text_field($_GET['page']) : '';
        if (strpos($hook, 'wppoppop') === false && strpos($page, 'wppoppop') === false) {
            return;
        }

        wp_enqueue_style('dashicons');

        // 1. Dashboard Assets
        if ($page === 'wppoppop' || $hook === 'toplevel_page_wppoppop') {
            wp_enqueue_style('wppoppop-dash-css', WPPOPPOP_URL . 'admin/css/dashboard.css', [], WPPOPPOP_VERSION);
            wp_enqueue_script('wppoppop-dash-js', WPPOPPOP_URL . 'admin/js/dashboard.js', ['jquery'], WPPOPPOP_VERSION, true);

            wp_localize_script('wppoppop-dash-js', 'wppoppop_dash_vars', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce'    => wp_create_nonce('wppoppop_dash_nonce'),
                'home_url' => home_url('/')
            ]);
            return;
        }

        // 2. Settings Assets
        if ($page === 'wppoppop-settings' || strpos($hook, 'wppoppop-settings') !== false) {
            wp_enqueue_style('wppoppop-settings-css', WPPOPPOP_URL . 'admin/css/settings.css', [], WPPOPPOP_VERSION);
            wp_enqueue_script('jquery');
            wp_enqueue_script('wppoppop-settings-js', WPPOPPOP_URL . 'admin/js/settings.js', ['jquery'], WPPOPPOP_VERSION, true);
            wp_localize_script('wppoppop-settings-js', 'wppoppop_settings_vars', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce'    => wp_create_nonce('wppoppop_settings_nonce')
            ]);
            return;
        }

        // 3. Library Assets
        if ($page === 'wppoppop-library') {
            wp_enqueue_style('wppoppop-library-css', WPPOPPOP_URL . 'admin/css/library.css', [], WPPOPPOP_VERSION);
            wp_enqueue_script('jquery');
            wp_enqueue_script('wppoppop-library-js', WPPOPPOP_URL . 'admin/js/library.js', ['jquery'], WPPOPPOP_VERSION, true);
            wp_localize_script('wppoppop-library-js', 'wppoppop_lib_vars', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce'    => wp_create_nonce('wppoppop_builder_nonce')
            ]);
            return;
        }

        // 4. Builder Assets with Active Dynamic Feature Flags
        if ($page === 'wppoppop-builder' || strpos($hook, 'wppoppop-builder') !== false) {
            wp_enqueue_style('wppoppop-builder-css', WPPOPPOP_URL . 'admin/css/builder.css', [], WPPOPPOP_VERSION);
            wp_enqueue_script('jquery');
            wp_enqueue_script('jquery-ui-draggable');
            wp_enqueue_script('jquery-ui-resizable');
            wp_enqueue_script('wppoppop-builder-js', WPPOPPOP_URL . 'admin/js/builder.js', ['jquery', 'jquery-ui-draggable', 'jquery-ui-resizable'], WPPOPPOP_VERSION, true);

            $settings = get_option('wppoppop_settings', []);
            $features = [
                'google_fonts'     => isset($settings['google_fonts']) ? !empty($settings['google_fonts']) : true,
                'font_awesome'     => !empty($settings['font_awesome']),
                'air_datepicker'   => isset($settings['air_datepicker']) ? !empty($settings['air_datepicker']) : true,
                'jquery_mask'      => !empty($settings['jquery_mask']),
                'js_parser'        => !empty($settings['js_parser']),
                'signature_pad'    => !empty($settings['signature_pad']),
                'range_slider'     => !empty($settings['range_slider']),
                'adblock_detector' => !empty($settings['adblock_detector'])
            ];

            wp_localize_script('wppoppop-builder-js', 'wppoppop_vars', [
                'ajax_url'    => admin_url('admin-ajax.php'),
                'nonce'       => wp_create_nonce('wppoppop_builder_nonce'),
                'current_uid' => isset($_GET['uid']) ? sanitize_key($_GET['uid']) : '',
                'features'    => $features
            ]);
        }
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
        $file = WPPOPPOP_PATH . 'templates/ab-view.php';
        if (file_exists($file)) {
            include $file;
        } else {
            echo '<div class="wrap"><h1>A/B Campaigns</h1><p>Manage and track A/B split-testing campaigns across your popups.</p><p><a href="' . admin_url('admin.php?page=wppoppop') . '" class="button">&larr; Back to Popups</a></p></div>';
        }
    }

    public function render_log() {
        $file = WPPOPPOP_PATH . 'templates/log-view.php';
        if (file_exists($file)) {
            include $file;
        } else {
            global $wpdb;
            $logs = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}wppoppop_logs ORDER BY id DESC LIMIT 100");
            echo '<div class="wrap"><h1>System Event Logs</h1>';
            echo '<table class="wp-list-table widefat fixed striped"><thead><tr><th>ID</th><th>Type</th><th>Message</th><th>Context</th><th>Date</th></tr></thead><tbody>';
            if (empty($logs)) {
                echo '<tr><td colspan="5">No logs recorded yet.</td></tr>';
            } else {
                foreach ($logs as $l) {
                    echo '<tr><td>' . esc_html($l->id) . '</td><td><strong>' . esc_html($l->event_type) . '</strong></td><td>' . esc_html($l->message) . '</td><td><code>' . esc_html($l->context) . '</code></td><td>' . esc_html($l->created_at) . '</td></tr>';
                }
            }
            echo '</tbody></table></div>';
        }
    }

    public function render_stats() {
        $file = WPPOPPOP_PATH . 'templates/stats-view.php';
        if (file_exists($file)) {
            include $file;
        } else {
            global $wpdb;
            $uid = isset($_GET['uid']) ? sanitize_key($_GET['uid']) : '';
            $items_table = $wpdb->prefix . 'wppoppop_items';
            $item = $uid ? $wpdb->get_row($wpdb->prepare("SELECT * FROM {$items_table} WHERE uid = %s", $uid)) : null;

            echo '<div class="wrap"><h1>Campaign Statistics</h1>';
            if ($item) {
                echo '<div class="notice notice-info"><p>Showing metrics for: <strong>' . esc_html($item->title) . '</strong> (' . esc_html($item->uid) . ')</p></div>';
                echo '<p><strong>Total Impressions:</strong> ' . intval($item->impressions) . ' | <strong>Total Submissions:</strong> ' . intval($item->submissions) . '</p>';
            } else {
                echo '<p>Select any popup from the <a href="' . admin_url('admin.php?page=wppoppop') . '">Popups</a> list to view detailed analytics.</p>';
            }
            echo '<p><a href="' . admin_url('admin.php?page=wppoppop') . '" class="button">&larr; Back to Popups</a></p></div>';
        }
    }

    public function render_field_analytics() {
        $file = WPPOPPOP_PATH . 'templates/field-analytics-view.php';
        if (file_exists($file)) {
            include $file;
        } else {
            echo '<div class="wrap"><h1>Field Analytics</h1><p>View collected responses and conversion rates per field. <a href="' . admin_url('admin.php?page=wppoppop') . '" class="button">&larr; Back to Popups</a></p></div>';
        }
    }

    public function render_payments() {
        $file = WPPOPPOP_PATH . 'templates/payments-view.php';
        if (file_exists($file)) {
            include $file;
        } else {
            global $wpdb;
            $txs = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}wppoppop_transactions ORDER BY id DESC LIMIT 100");
            echo '<div class="wrap"><h1>Transaction History</h1>';
            echo '<table class="wp-list-table widefat fixed striped"><thead><tr><th>Tx ID</th><th>Popup</th><th>Email</th><th>Amount</th><th>Gateway</th><th>Status</th><th>Date</th></tr></thead><tbody>';
            if (empty($txs)) {
                echo '<tr><td colspan="7">No payment transactions recorded yet.</td></tr>';
            } else {
                foreach ($txs as $t) {
                    echo '<tr><td><code>' . esc_html($t->transaction_id) . '</code></td><td>' . esc_html($t->popup_uid) . '</td><td>' . esc_html($t->email) . '</td><td>' . esc_html($t->amount . ' ' . $t->currency) . '</td><td>' . esc_html($t->gateway) . '</td><td>' . esc_html($t->status) . '</td><td>' . esc_html($t->created_at) . '</td></tr>';
                }
            }
            echo '</tbody></table></div>';
        }
    }

    public function render_library() {
        $file = WPPOPPOP_PATH . 'templates/library-view.php';
        if (file_exists($file)) {
            include $file;
        } else {
            echo '<div class="wrap"><h1>Popups Library</h1><p>Pre-designed high-converting templates ready for 1-click import.</p><p><a href="' . admin_url('admin.php?page=wppoppop') . '" class="button">&larr; Back to Popups</a></p></div>';
        }
    }

    public function render_settings() {
        include WPPOPPOP_PATH . 'templates/settings-view.php';
    }
}
