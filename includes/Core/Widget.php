<?php
namespace WPPopPop\Core;

use WPPopPop\Targeting\PopupPostType;

class Widget extends \WP_Widget {
    public function __construct() {
        parent::__construct(
            'wppoppop_widget',
            __('Green Popups (WP Pop Pop)', 'wppoppop'),
            [
                'description' => __('Display a popup form or trigger button in your sidebar or footer.', 'wppoppop'),
                'classname'   => 'wppoppop-sidebar-widget',
            ]
        );
    }

    public static function register(): void {
        register_widget(__CLASS__);
    }

    public function widget($args, $instance): void {
        $popup_id = absint($instance['popup_id'] ?? 0);
        if (!$popup_id) {
            return;
        }

        $popup = get_post($popup_id);
        if (!$popup || $popup->post_type !== PopupPostType::POST_TYPE || $popup->post_status !== 'publish') {
            return;
        }

        $title = !empty($instance['title']) ? $instance['title'] : '';
        $title = apply_filters('widget_title', $title, $instance, $this->id_base);
        $mode  = !empty($instance['mode']) ? $instance['mode'] : 'inline';
        $btn   = !empty($instance['button_text']) ? $instance['button_text'] : __('Open Popup', 'wppoppop');

        echo $args['before_widget'];
        if (!empty($title)) {
            echo $args['before_title'] . esc_html($title) . $args['after_title'];
        }

        if ($mode === 'inline') {
            echo '<div class="wppoppop-widget-inline" data-popup-id="' . esc_attr($popup_id) . '">';
            echo LayerRenderer::render_layers($popup_id, apply_filters('the_content', $popup->post_content));
            echo '</div>';
        } else {
            echo '<button type="button" class="wppoppop-btn-pink wppoppop-trigger wppoppop-widget-trigger" data-popup-id="' . esc_attr($popup_id) . '">';
            echo esc_html($btn);
            echo '</button>';
        }

        echo $args['after_widget'];
    }

    public function form($instance): void {
        $title       = $instance['title'] ?? '';
        $popup_id    = absint($instance['popup_id'] ?? 0);
        $mode        = $instance['mode'] ?? 'inline';
        $button_text = $instance['button_text'] ?? __('Open Popup', 'wppoppop');

        $popups = get_posts([
            'post_type'      => PopupPostType::POST_TYPE,
            'post_status'    => 'publish',
            'posts_per_page' => 100,
            'orderby'        => 'title',
            'order'          => 'ASC',
        ]);
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php echo esc_html__('Widget Title:', 'wppoppop'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('popup_id')); ?>"><?php echo esc_html__('Select Popup:', 'wppoppop'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('popup_id')); ?>" name="<?php echo esc_attr($this->get_field_name('popup_id')); ?>">
                <option value="0"><?php echo esc_html__('-- Choose a Popup --', 'wppoppop'); ?></option>
                <?php foreach ($popups as $p): ?>
                    <option value="<?php echo esc_attr($p->ID); ?>" <?php selected($popup_id, $p->ID); ?>>
                        <?php echo esc_html($p->post_title); ?> (#<?php echo esc_html($p->ID); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('mode')); ?>"><?php echo esc_html__('Display Mode:', 'wppoppop'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('mode')); ?>" name="<?php echo esc_attr($this->get_field_name('mode')); ?>">
                <option value="inline" <?php selected($mode, 'inline'); ?>><?php echo esc_html__('Inline (Render Form In Sidebar)', 'wppoppop'); ?></option>
                <option value="button" <?php selected($mode, 'button'); ?>><?php echo esc_html__('Button (Trigger Modal Popup)', 'wppoppop'); ?></option>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('button_text')); ?>"><?php echo esc_html__('Button Label (if Button mode):', 'wppoppop'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('button_text')); ?>" name="<?php echo esc_attr($this->get_field_name('button_text')); ?>" type="text" value="<?php echo esc_attr($button_text); ?>" />
        </p>
        <?php
    }

    public function update($new_instance, $old_instance): array {
        $instance = [];
        $instance['title']       = sanitize_text_field($new_instance['title'] ?? '');
        $instance['popup_id']    = absint($new_instance['popup_id'] ?? 0);
        $instance['mode']        = in_array($new_instance['mode'] ?? '', ['inline', 'button'], true) ? $new_instance['mode'] : 'inline';
        $instance['button_text'] = sanitize_text_field($new_instance['button_text'] ?? __('Open Popup', 'wppoppop'));
        return $instance;
    }
}
