<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Modular Admin Coordinator
 */
class WpPopPop_Admin {
    public function __construct() {
        require_once WPPOPPOP_PATH . 'includes/admin/class-admin-pages.php';
        require_once WPPOPPOP_PATH . 'includes/admin/class-admin-menu.php';
        require_once WPPOPPOP_PATH . 'includes/admin/class-admin-assets.php';
        require_once WPPOPPOP_PATH . 'includes/admin/class-admin-settings.php';
        require_once WPPOPPOP_PATH . 'includes/admin/class-admin-help.php';

        $pages = new WpPopPop_Admin_Pages();
        new WpPopPop_Admin_Menu($pages);
        new WpPopPop_Admin_Assets();
        new WpPopPop_Admin_Settings();
        new WpPopPop_Admin_Help();
    }
}
