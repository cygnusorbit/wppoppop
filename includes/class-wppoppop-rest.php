<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Modular REST API Coordinator
 * Registers decomposed delivery, submissions, and diagnostic controllers.
 */
class WpPopPop_Rest {
    protected $delivery;
    protected $submissions;
    protected $diagnostics;

    public function __construct() {
        require_once WPPOPPOP_PATH . 'includes/rest/class-rest-delivery.php';
        require_once WPPOPPOP_PATH . 'includes/rest/class-rest-submissions.php';
        require_once WPPOPPOP_PATH . 'includes/rest/class-rest-diagnostics.php';

        $this->delivery    = new WpPopPop_Rest_Delivery();
        $this->submissions = new WpPopPop_Rest_Submissions();
        $this->diagnostics = new WpPopPop_Rest_Diagnostics();

        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes() {
        $this->delivery->register_routes();
        $this->submissions->register_routes();
        $this->diagnostics->register_routes();
    }
}
