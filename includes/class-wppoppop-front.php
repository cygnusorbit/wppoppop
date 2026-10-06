<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Modular Frontend Coordinator
 * Boots targeting, markup rendering, and display dispatchers.
 */
class WpPopPop_Front {
    protected $targeting;
    protected $renderer;
    protected $display;

    public function __construct() {
        require_once WPPOPPOP_PATH . 'includes/front/class-front-targeting.php';
        require_once WPPOPPOP_PATH . 'includes/front/class-front-renderer.php';
        require_once WPPOPPOP_PATH . 'includes/front/class-front-display.php';

        $this->targeting = new WpPopPop_Front_Targeting();
        $this->renderer  = new WpPopPop_Front_Renderer();
        $this->display   = new WpPopPop_Front_Display($this->targeting, $this->renderer);
    }

    /**
     * Backward-compatibility proxy for external callers (e.g., Remote Embed AJAX endpoint).
     */
    public function render_popup_markup($uid, array $config, $is_inline = false) {
        return $this->renderer->render_popup_markup($uid, $config, $is_inline);
    }

    public function detect_visitor_country() {
        return $this->targeting->detect_visitor_country();
    }
}
