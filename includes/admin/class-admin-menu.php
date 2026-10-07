<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Admin_Menu {
    protected $pages;

    public function __construct(WpPopPop_Admin_Pages $pages) {
        $this->pages = $pages;
        add_action('admin_menu', [$this, 'register_menus']);
        add_filter('set-screen-option', [$this, 'save_screen_options'], 10, 3);
    }

    public function register_menus() {
        $main_hook = add_menu_page(
            'WpPopPop',
            'WpPopPop',
            'edit_wppoppop_campaigns',
            'wppoppop',
            [$this->pages, 'render_dashboard'],
            'dashicons-external',
            30
        );

        add_submenu_page('wppoppop', __('Popups', 'wppoppop'), __('Popups', 'wppoppop'), 'edit_wppoppop_campaigns', 'wppoppop', [$this->pages, 'render_dashboard']);
        add_submenu_page('wppoppop', __('Create Popup', 'wppoppop'), __('Create Popup', 'wppoppop'), 'edit_wppoppop_campaigns', 'wppoppop-builder', [$this->pages, 'render_builder']);
        add_submenu_page('wppoppop', __('A/B Campaigns', 'wppoppop'), __('A/B Campaigns', 'wppoppop'), 'edit_wppoppop_campaigns', 'wppoppop-ab', [$this->pages, 'render_ab']);
        $subs_hook = add_submenu_page('wppoppop', __('Submissions', 'wppoppop'), __('Submissions', 'wppoppop'), 'read_wppoppop_submissions', 'wppoppop-submissions', [$this->pages, 'render_submissions']);
        $log_hook  = add_submenu_page('wppoppop', __('Activity Log', 'wppoppop'), __('Activity Log', 'wppoppop'), 'read_wppoppop_submissions', 'wppoppop-log', [$this->pages, 'render_log']);
        add_submenu_page('wppoppop', __('Statistics', 'wppoppop'), __('Statistics', 'wppoppop'), 'read_wppoppop_submissions', 'wppoppop-stats', [$this->pages, 'render_stats']);
        add_submenu_page('wppoppop', __('Field Analytics', 'wppoppop'), __('Field Analytics', 'wppoppop'), 'read_wppoppop_submissions', 'wppoppop-field-analytics', [$this->pages, 'render_field_analytics']);
        $pay_hook  = add_submenu_page('wppoppop', __('Transactions', 'wppoppop'), __('Transactions', 'wppoppop'), 'read_wppoppop_submissions', 'wppoppop-payments', [$this->pages, 'render_payments']);
        add_submenu_page('wppoppop', __('Popups Library', 'wppoppop'), __('Popups Library', 'wppoppop'), 'edit_wppoppop_campaigns', 'wppoppop-library', [$this->pages, 'render_library']);
        add_submenu_page('wppoppop', __('Settings', 'wppoppop'), __('Settings', 'wppoppop'), 'manage_wppoppop', 'wppoppop-settings', [$this->pages, 'render_settings']);
        add_submenu_page('wppoppop', __('Tools & Export', 'wppoppop'), __('Tools & Export', 'wppoppop'), 'manage_wppoppop', 'wppoppop-tools', [$this->pages, 'render_tools']);

        // Register native Screen Options for all tabular screens
        if ($main_hook) {
            add_action('load-' . $main_hook, function() {
                add_screen_option('per_page', [
                    'label'   => __('Campaigns per page', 'wppoppop'),
                    'default' => 25,
                    'option'  => 'wppoppop_campaigns_per_page',
                ]);
            });
        }

        if ($subs_hook) {
            add_action('load-' . $subs_hook, function() {
                add_screen_option('per_page', [
                    'label'   => __('Submissions per page', 'wppoppop'),
                    'default' => 25,
                    'option'  => 'wppoppop_submissions_per_page',
                ]);
            });
        }

        if ($log_hook) {
            add_action('load-' . $log_hook, function() {
                add_screen_option('per_page', [
                    'label'   => __('Log entries per page', 'wppoppop'),
                    'default' => 50,
                    'option'  => 'wppoppop_logs_per_page',
                ]);
            });
        }

        if ($pay_hook) {
            add_action('load-' . $pay_hook, function() {
                add_screen_option('per_page', [
                    'label'   => __('Transactions per page', 'wppoppop'),
                    'default' => 25,
                    'option'  => 'wppoppop_payments_per_page',
                ]);
            });
        }
    }

    public function save_screen_options($status, $option, $value) {
        $allowed = [
            'wppoppop_campaigns_per_page',
            'wppoppop_submissions_per_page',
            'wppoppop_logs_per_page',
            'wppoppop_payments_per_page',
        ];

        if (in_array($option, $allowed, true)) {
            return (int) $value;
        }

        return $status;
    }
}
