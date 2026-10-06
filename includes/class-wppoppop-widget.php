<?php
if (!defined('ABSPATH')) {
    exit;
}

require_once WPPOPPOP_PATH . 'includes/widget/class-widget-form.php';
require_once WPPOPPOP_PATH . 'includes/widget/class-widget-render.php';

/**
 * Modular WpPopPop Sidebar Widget
 * Delegates form controls and frontend output to isolated domain handlers.
 */
class WpPopPop_Widget extends WP_Widget {
    protected $form_handler;
    protected $render_handler;

    public function __construct() {
        parent::__construct(
            'wppoppop_widget',
            'WpPopPop Popup Widget',
            [
                'classname'   => 'widget_wppoppop',
                'description' => 'Display an embedded popup campaign or click trigger button in any sidebar or widget area.',
            ]
        );

        $this->form_handler   = new WpPopPop_Widget_Form();
        $this->render_handler = new WpPopPop_Widget_Render();
    }

    public function widget($args, $instance) {
        $this->render_handler->render_widget($this, $args, $instance);
    }

    public function form($instance) {
        $this->form_handler->render_form($this, $instance);
    }

    public function update($new_instance, $old_instance) {
        return $this->form_handler->sanitize_settings($new_instance, $old_instance);
    }
}
