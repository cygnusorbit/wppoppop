<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'wppoppop_widget',
            'WpPopPop Popup Widget',
            ['description' => 'Display a WpPopPop campaign inline within your sidebars.']
        );
    }

    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? apply_filters('widget_title', $instance['title']) : '';
        $uid   = !empty($instance['uid']) ? sanitize_key($instance['uid']) : '';

        echo $args['before_widget'];
        if ($title) {
            echo $args['before_title'] . esc_html($title) . $args['after_title'];
        }

        if (!empty($uid)) {
            echo do_shortcode('[wppoppop uid="' . esc_attr($uid) . '"]');
        }

        echo $args['after_widget'];
    }

    public function form($instance) {
        global $wpdb;
        $popups = $wpdb->get_results("SELECT uid, title FROM {$wpdb->prefix}wppoppop_items WHERE status = 'publish' ORDER BY title ASC");

        $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
        $current_uid = isset($instance['uid']) ? sanitize_key($instance['uid']) : '';
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">Widget Title:</label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo $title; ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('uid')); ?>">Select Popup:</label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('uid')); ?>" name="<?php echo esc_attr($this->get_field_name('uid')); ?>">
                <option value="">-- Select Popup --</option>
                <?php foreach ($popups as $popup) : ?>
                    <option value="<?php echo esc_attr($popup->uid); ?>" <?php selected($current_uid, $popup->uid); ?>>
                        <?php echo esc_html($popup->title); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = [];
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['uid']   = (!empty($new_instance['uid'])) ? sanitize_key($new_instance['uid']) : '';
        return $instance;
    }
}
