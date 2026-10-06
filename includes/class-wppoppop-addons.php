<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Modular Addons Coordinator
 * Boots Downloads, Optin, WooCommerce, and SMS subsystems.
 */
class WpPopPop_Addons {
    protected $downloads;
    protected $optin;
    protected $woocommerce;
    protected $sms;

    public function __construct() {
        require_once WPPOPPOP_PATH . 'includes/addons/class-addon-downloads.php';
        require_once WPPOPPOP_PATH . 'includes/addons/class-addon-optin.php';
        require_once WPPOPPOP_PATH . 'includes/addons/class-addon-woocommerce.php';
        require_once WPPOPPOP_PATH . 'includes/addons/class-addon-sms.php';

        $this->downloads   = new WpPopPop_Addon_Downloads();
        $this->optin       = new WpPopPop_Addon_Optin();
        $this->woocommerce = new WpPopPop_Addon_WooCommerce();
        $this->sms         = new WpPopPop_Addon_Sms();
    }

    public function get_downloads_handler() {
        return $this->downloads;
    }

    public function get_optin_handler() {
        return $this->optin;
    }

    public function get_woocommerce_handler() {
        return $this->woocommerce;
    }

    public function get_sms_handler() {
        return $this->sms;
    }

    // Backwards-compatible proxy delegates
    public function generate_download_token($file_url, $popup_uid = '') {
        return $this->downloads->generate_download_token($file_url, $popup_uid);
    }

    public function generate_wc_coupon(array $args) {
        return $this->woocommerce->generate_wc_coupon($args);
    }

    public function auto_apply_wc_coupon($coupon_code) {
        return $this->woocommerce->auto_apply_wc_coupon($coupon_code);
    }

    public function dispatch_twilio_sms($to_number, $body_text, array $credentials) {
        return $this->sms->dispatch_twilio_sms($to_number, $body_text, $credentials);
    }
}
