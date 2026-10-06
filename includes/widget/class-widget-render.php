<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Widget_Render {
    public function render_widget($widget, $args, $instance) {
        $popup_uid   = !empty($instance['popup_uid']) ? sanitize_key($instance['popup_uid']) : '';
        $mode        = !empty($instance['mode']) ? $instance['mode'] : 'inline';
        $button_text = !empty($instance['button_text']) ? $instance['button_text'] : 'Open Popup';

        if (empty($popup_uid)) {
            return;
        }

        echo $args['before_widget'];

        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }

        if ($mode === 'button') {
            ?>
            <div class="wppoppop-widget-trigger-wrap" style="text-align:center;padding:8px 0;">
                <button type="button" 
                        class="button button-primary wppoppop-open-btn" 
                        data-target-uid="<?php echo esc_attr($popup_uid); ?>">
                    <?php echo esc_html($button_text); ?>
                </button>
            </div>
            <?php
        } else {
            // Inline Embed Delivery Mode
            global $wpdb;
            $items_table = $wpdb->prefix . 'wppoppop_items';
            $row = $wpdb->get_row($wpdb->prepare("SELECT data FROM {$items_table} WHERE uid = %s AND status = 'publish'", $popup_uid), ARRAY_A);

            if ($row) {
                $config = json_decode($row['data'], true);
                if (!empty($config)) {
                    if (class_exists('WpPopPop_Front_Renderer')) {
                        (new WpPopPop_Front_Renderer())->render_popup_markup($popup_uid, $config, true);
                    } elseif (class_exists('WpPopPop_Front')) {
                        (new WpPopPop_Front())->render_popup_markup($popup_uid, $config, true);
                    }
                }
            }
        }

        echo $args['after_widget'];
    }
}
