<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Modular Admin Bootstrap
 * Initializes segregated menu, asset, and page controllers.
 */
class WpPopPop_Admin {
    protected $pages;
    protected $menu;
    protected $assets;

    public function __construct() {
        require_once WPPOPPOP_PATH . 'includes/admin/class-admin-pages.php';
        require_once WPPOPPOP_PATH . 'includes/admin/class-admin-menu.php';
        require_once WPPOPPOP_PATH . 'includes/admin/class-admin-assets.php';

        $this->pages  = new WpPopPop_Admin_Pages();
        $this->menu   = new WpPopPop_Admin_Menu($this->pages);
        $this->assets = new WpPopPop_Admin_Assets();
    }
}
