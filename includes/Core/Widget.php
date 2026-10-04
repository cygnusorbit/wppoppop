<?php
namespace WPPopPop\Core;

class Widget extends \WP_Widget {
    public function __construct() {
        parent::__construct(
            'wppoppop_widget',
            __('WPPopPop: Popup / Form Widget', 'wppoppop'),
            ['description' => __('Display a popup launcher button or an inline lead form in widget areas.', 'wppoppop')]
        );
    }

    public static function init(): void {
        add_action('widgets_init', function () {
            register_widget(self::class);
        });
    }

    public function widget($args, $instance): void {
        $title    = !empty($instance['title']) ? apply_filters('widget_title', $instance['title']) : '';
        $popup_id = !empty($instance['popup_id']) ? absint($instance['popup_id']) : 0;
        $mode     = !empty($instance['mode']) ? sanitize_text_field($instance['mode']) : 'trigger';
        $btn_text = !empty($instance['btn_text']) ? sanitize_text_field($instance['btn_text']) : __('Open Offer', 'wppoppop');

        if (!$popup_id) {
            return;
        }

        echo $args['before_widget'];
        if (!empty($title)) {
            echo $args['before_title'] . esc_html($title) . $args['after_title'];
        }

        echo '<div class="wppoppop-widget-container">';
        if ($mode === 'inline') {
            $content = get_post_field('post_content', $popup_id);
            echo LayerRenderer::render_layers($popup_id, $content ?: '');
        } else {
            echo sprintf(
                '<button type="button" class="wppoppop-btn wppoppop-trigger wppoppop-widget-trigger" data-popup-id="%d">%s</button>',
                $popup_id,
                esc_html($btn_text)
            );
        }
        echo '</div>';

        echo $args['after_widget'];
    }

    public function form($instance): void {
        $title    = !empty($instance['title']) ? $instance['title'] : __('Exclusive Promotion', 'wppoppop');
        $popup_id = !empty($instance['popup_id']) ? absint($instance['popup_id']) : 0;
        $mode     = !empty($instance['mode']) ? $instance['mode'] : 'trigger';
        $btn_text = !empty($instance['btn_text']) ? $instance['btn_text'] : __('Open Offer', 'wppoppop');

        $popups = get_posts([
            'post_type'      => 'wppoppop',
            'post_status'    => 'publish',
            'posts_per_page' => 50,
        ]);
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Widget Title:', 'wppoppop'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('popup_id')); ?>"><?php esc_html_e('Linked Popup:', 'wppoppop'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('popup_id')); ?>" name="<?php echo esc_attr($this->get_field_name('popup_id')); ?>">
                <option value=""><?php esc_html_e('— Select a Popup —', 'wppoppop'); ?></option>
                <?php foreach ($popups as $p): ?>
                    <option value="<?php echo esc_attr($p->ID); ?>" <?php selected($popup_id, $p->ID); ?>>
                        <?php echo esc_html($p->post_title . ' (#' . $p->ID . ')'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('mode')); ?>"><?php esc_html_e('Display Mode:', 'wppoppop'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('mode')); ?>" name="<?php echo esc_attr($this->get_field_name('mode')); ?>">
                <option value="trigger" <?php selected($mode, 'trigger'); ?>><?php esc_html_e('Trigger Button (Opens Modal)', 'wppoppop'); ?></option>
                <option value="inline" <?php selected($mode, 'inline'); ?>><?php esc_html_e('Inline Embedded Form', 'wppoppop'); ?></option>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('btn_text')); ?>"><?php esc_html_e('Button Text (Trigger Mode):', 'wppoppop'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('btn_text')); ?>" name="<?php echo esc_attr($this->get_field_name('btn_text')); ?>" type="text" value="<?php echo esc_attr($btn_text); ?>">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance): array {
        $instance = [];
        $instance['title']    = sanitize_text_field($new_instance['title'] ?? '');
        $instance['popup_id'] = absint($new_instance['popup_id'] ?? 0);
        $instance['mode']     = sanitize_text_field($new_instance['mode'] ?? 'trigger');
        $instance['btn_text'] = sanitize_text_field($new_instance['btn_text'] ?? __('Open Offer', 'wppoppop'));
        return $instance;
    }
}
