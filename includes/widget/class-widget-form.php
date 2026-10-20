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
        $onload_delay = isset($instance['onload_delay']) ? max(0, (int) $instance['onload_delay']) : 0;

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
                <strong><?php esc_html_e('Embed Type:', 'wppoppop'); ?></strong>
            </label>
            <select class="widefat"
                    id="<?php echo esc_attr($widget->get_field_id('mode')); ?>"
                    name="<?php echo esc_attr($widget->get_field_name('mode')); ?>">
                <option value="inline" <?php selected($mode, 'inline'); ?>>
                    <?php esc_html_e('Inline Popup (renders inside this widget area)', 'wppoppop'); ?>
                </option>
                <option value="onload" <?php selected($mode, 'onload'); ?>>
                    <?php esc_html_e('Onload Popup (opens when the page loads)', 'wppoppop'); ?>
                </option>
                <?php if ($mode === 'button') : ?>
                <option value="button" selected="selected">
                    <?php esc_html_e('Click Button (legacy)', 'wppoppop'); ?>
                </option>
                <?php endif; ?>
            </select>
        </p>

        <p>
            <label for="<?php echo esc_attr($widget->get_field_id('onload_delay')); ?>">
                <strong><?php esc_html_e('Onload Delay (seconds):', 'wppoppop'); ?></strong>
            </label>
            <input class="tiny-text"
                   id="<?php echo esc_attr($widget->get_field_id('onload_delay')); ?>"
                   name="<?php echo esc_attr($widget->get_field_name('onload_delay')); ?>"
                   type="number" min="0" step="1"
                   value="<?php echo esc_attr($onload_delay); ?>">
        </p>

        <?php if ($mode === 'button') : ?>
        <p>
            <label for="<?php echo esc_attr($widget->get_field_id('button_text')); ?>">
                <strong><?php esc_html_e('Button Label (legacy):', 'wppoppop'); ?></strong>
            </label>
            <input class="widefat"
                   id="<?php echo esc_attr($widget->get_field_id('button_text')); ?>"
                   name="<?php echo esc_attr($widget->get_field_name('button_text')); ?>"
                   type="text"
                   value="<?php echo esc_attr($button_text); ?>">
        </p>
        <?php endif; ?>
        <p class="description" style="font-size:11px;color:#64748b;">
            <?php esc_html_e('Tip: You can also embed any popup directly in content using the [wppoppop uid="..."] shortcode.', 'wppoppop'); ?>
        </p>
        <?php
    }

    public function sanitize_settings($new_instance, $old_instance) {
        $instance = [];
        $instance['title']       = !empty($new_instance['title']) ? sanitize_text_field($new_instance['title']) : '';
        $instance['popup_uid']   = !empty($new_instance['popup_uid']) ? sanitize_key($new_instance['popup_uid']) : '';
        $instance['mode']        = (isset($new_instance['mode']) && in_array($new_instance['mode'], ['inline', 'onload', 'button'], true)) ? $new_instance['mode'] : 'inline';
        $instance['button_text'] = !empty($new_instance['button_text']) ? sanitize_text_field($new_instance['button_text']) : 'Open Popup';
        $instance['onload_delay'] = isset($new_instance['onload_delay']) ? max(0, min(3600, (int) $new_instance['onload_delay'])) : 0;
        return $instance;
    }
}
