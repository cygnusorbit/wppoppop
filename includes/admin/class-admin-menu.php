<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Admin_Menu {
    protected $pages;

    public function __construct(WpPopPop_Admin_Pages $pages) {
        $this->pages = $pages;
        add_action('admin_menu', [$this, 'register_menus']);
    }

    public function register_menus() {
        add_menu_page(
            'WpPopPop',
            'WpPopPop',
            'manage_options',
            'wppoppop',
            [$this->pages, 'render_dashboard'],
            'dashicons-external',
            30
        );

        add_submenu_page('wppoppop', 'Popups', 'Popups', 'manage_options', 'wppoppop', [$this->pages, 'render_dashboard']);
        add_submenu_page('wppoppop', 'Create Popup', 'Create Popup', 'manage_options', 'wppoppop-builder', [$this->pages, 'render_builder']);
        add_submenu_page('wppoppop', 'A/B Campaigns', 'A/B Campaigns', 'manage_options', 'wppoppop-ab', [$this->pages, 'render_ab']);
        add_submenu_page('wppoppop', 'Submissions', 'Submissions', 'manage_options', 'wppoppop-submissions', [$this->pages, 'render_submissions']);
        add_submenu_page('wppoppop', 'Activity Log', 'Activity Log', 'manage_options', 'wppoppop-log', [$this->pages, 'render_log']);
        add_submenu_page('wppoppop', 'Statistics', 'Statistics', 'manage_options', 'wppoppop-stats', [$this->pages, 'render_stats']);
        add_submenu_page('wppoppop', 'Field Analytics', 'Field Analytics', 'manage_options', 'wppoppop-field-analytics', [$this->pages, 'render_field_analytics']);
        add_submenu_page('wppoppop', 'Transactions', 'Transactions', 'manage_options', 'wppoppop-payments', [$this->pages, 'render_payments']);
        add_submenu_page('wppoppop', 'Popups Library', 'Popups Library', 'manage_options', 'wppoppop-library', [$this->pages, 'render_library']);
        add_submenu_page('wppoppop', 'Settings', 'Settings', 'manage_options', 'wppoppop-settings', [$this->pages, 'render_settings']);
        add_submenu_page('wppoppop', 'Tools & Export', 'Tools & Export', 'manage_options', 'wppoppop-tools', [$this->pages, 'render_tools']);
    }
}
