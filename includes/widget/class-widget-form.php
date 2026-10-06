<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Widget_Form {
    public function render_form($widget, $instance) {
        $title       = !empty($instance['title']) ? $instance['title'] : '';
        $popup_uid   = !empty($instance['popup_uid']) ? $instance['popup_uid'] : '';
        $mode        = !empty($instance['mode']) ? $instance['mode'] : 'inline';
        $button_text = !empty($instance['button_text']) ? $instance['button_text'] : 'Open Popup';

        global $wpdb;
        $items_table = $wpdb->prefix . 'wppoppop_items';
        $popups = $wpdb->get_results("SELECT uid, title FROM {$items_table} WHERE status = 'publish' ORDER BY title ASC");
        ?>
        <p>
            <label for="<?php echo esc_attr($widget->get_field_id('title')); ?>">
                <strong><?php esc_html_e('Widget Title:', 'wppoppop'); ?></strong>
            </label>
            <input class="widefat" 
                   id="<?php echo esc_attr($widget->get_field_id('title')); ?>" 
                   name="<?php echo esc_attr($widget->get_field_name('title')); ?>" 
                   type="text" 
                   value="<?php echo esc_attr($title); ?>">
        </p>

        <p>
            <label for="<?php echo esc_attr($widget->get_field_id('popup_uid')); ?>">
                <strong><?php esc_html_e('Select Popup Campaign:', 'wppoppop'); ?></strong>
            </label>
            <select class="widefat" 
                    id="<?php echo esc_attr($widget->get_field_id('popup_uid')); ?>" 
                    name="<?php echo esc_attr($widget->get_field_name('popup_uid')); ?>">
                <option value=""><?php esc_html_e('— Choose a Campaign —', 'wppoppop'); ?></option>
                <?php if (!empty($popups)) : ?>
                    <?php foreach ($popups as $p) : ?>
                        <option value="<?php echo esc_attr($p->uid); ?>" <?php selected($popup_uid, $p->uid); ?>>
                            <?php echo esc_html($p->title); ?> (<?php echo esc_html($p->uid); ?>)
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </p>

        <p>
            <label for="<?php echo esc_attr($widget->get_field_id('mode')); ?>">
                <strong><?php esc_html_e('Display Mode:', 'wppoppop'); ?></strong>
            </label>
            <select class="widefat" 
                    id="<?php echo esc_attr($widget->get_field_id('mode')); ?>" 
                    name="<?php echo esc_attr($widget->get_field_name('mode')); ?>">
                <option value="inline" <?php selected($mode, 'inline'); ?>>
                    <?php esc_html_e('Inline Embedded Popup (Render Inside Sidebar)', 'wppoppop'); ?>
                </option>
                <option value="button" <?php selected($mode, 'button'); ?>>
                    <?php esc_html_e('Click Trigger Button (Opens Center Modal)', 'wppoppop'); ?>
                </option>
            </select>
        </p>

        <p>
            <label for="<?php echo esc_attr($widget->get_field_id('button_text')); ?>">
                <strong><?php esc_html_e('Button Label (For Click Mode):', 'wppoppop'); ?></strong>
            </label>
            <input class="widefat" 
                   id="<?php echo esc_attr($widget->get_field_id('button_text')); ?>" 
                   name="<?php echo esc_attr($widget->get_field_name('button_text')); ?>" 
                   type="text" 
                   value="<?php echo esc_attr($button_text); ?>">
        </p>
        <p class="description" style="font-size:11px;color:#64748b;">
            <?php esc_html_e('Tip: You can also embed any popup directly in content using the [wppoppop uid="..."] shortcode.', 'wppoppop'); ?>
        </p>
        <?php
    }

    public function sanitize_settings($new_instance, $old_instance) {
        $instance = [];
        $instance['title']       = !empty($new_instance['title']) ? sanitize_text_field($new_instance['title']) : '';
        $instance['popup_uid']   = !empty($new_instance['popup_uid']) ? sanitize_key($new_instance['popup_uid']) : '';
        $instance['mode']        = (isset($new_instance['mode']) && in_array($new_instance['mode'], ['inline', 'button'], true)) ? $new_instance['mode'] : 'inline';
        $instance['button_text'] = !empty($new_instance['button_text']) ? sanitize_text_field($new_instance['button_text']) : 'Open Popup';
        return $instance;
    }
}
