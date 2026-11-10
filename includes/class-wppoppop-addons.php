<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Modular Addons Coordinator
 * Boots the Double Opt-In confirmation link. Downloads, WooCommerce and SMS live in the Pro plugin.
 */
class WpPopPop_Addons {
    protected $optin;

    public function __construct() {
        require_once WPPOPPOP_PATH . 'includes/addons/class-addon-optin.php';

        $this->optin = new WpPopPop_Addon_Optin();
    }

    public function get_optin_handler() {
        return $this->optin;
    }
}
