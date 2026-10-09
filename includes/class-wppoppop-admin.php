<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WpPopPop_Admin_Pages') && file_exists(WPPOPPOP_PATH . 'includes/admin/class-admin-pages.php')) {
    require_once WPPOPPOP_PATH . 'includes/admin/class-admin-pages.php';
}
if (!class_exists('WpPopPop_Admin_Menu') && file_exists(WPPOPPOP_PATH . 'includes/admin/class-admin-menu.php')) {
    require_once WPPOPPOP_PATH . 'includes/admin/class-admin-menu.php';
}
if (!class_exists('WpPopPop_Admin_Assets') && file_exists(WPPOPPOP_PATH . 'includes/admin/class-admin-assets.php')) {
    require_once WPPOPPOP_PATH . 'includes/admin/class-admin-assets.php';
}
if (!class_exists('WpPopPop_Admin_Export') && file_exists(WPPOPPOP_PATH . 'includes/admin/class-admin-export.php')) {
    require_once WPPOPPOP_PATH . 'includes/admin/class-admin-export.php';
}

class WpPopPop_Admin {
    protected $pages;
    protected $menu;
    protected $assets;
    protected $export;

    public function __construct() {
        $this->pages  = new WpPopPop_Admin_Pages();
        $this->menu   = new WpPopPop_Admin_Menu($this->pages);
        $this->assets = new WpPopPop_Admin_Assets();
        if (class_exists('WpPopPop_Admin_Export')) {
            $this->export = new WpPopPop_Admin_Export();
        }
    }
}
