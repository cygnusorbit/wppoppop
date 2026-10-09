<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Admin_Menu {
    protected $pages;
    protected $dashboard_hook = '';

    public function __construct($pages = null) {
        if ($pages instanceof WpPopPop_Admin_Pages) {
            $this->pages = $pages;
        } else {
            if (!class_exists('WpPopPop_Admin_Pages') && file_exists(WPPOPPOP_PATH . 'includes/admin/class-admin-pages.php')) {
                require_once WPPOPPOP_PATH . 'includes/admin/class-admin-pages.php';
            }
            $this->pages = new WpPopPop_Admin_Pages();
        }

        add_action('admin_menu', [$this, 'register_menus']);
        add_filter('set-screen-option', [$this, 'set_screen_option'], 10, 3);
    }

    public function register_menus() {
        $this->dashboard_hook = add_menu_page(
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

        if (!empty($this->dashboard_hook)) {
            add_action("load-{$this->dashboard_hook}", [$this, 'load_dashboard_screen_options']);
        }
    }

    public function load_dashboard_screen_options() {
        add_screen_option('per_page', [
            'label'   => __('Campaigns per page', 'wppoppop'),
            'default' => 25,
            'option'  => 'wppoppop_campaigns_per_page',
        ]);
    }

    public function set_screen_option($status, $option, $value) {
        if ('wppoppop_campaigns_per_page' === $option) {
            return (int) $value;
        }
        return $status;
    }
}
