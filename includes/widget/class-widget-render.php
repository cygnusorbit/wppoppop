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
            /* Legacy click-trigger mode (kept so previously saved widgets keep working) */
            ?>
            <div class="wppoppop-widget-trigger-wrap" style="text-align:center;padding:8px 0;">
                <button type="button" class="button button-primary wppoppop-open-btn wppoppop-trigger-btn" data-target-uid="<?php echo esc_attr($popup_uid); ?>">
                    <?php echo esc_html($button_text); ?>
                </button>
            </div>
            <?php
        } elseif ($mode === 'onload') {
            /* Onload Popup: footer renderer prints the popup once and forces the load trigger */
            if (class_exists('WpPopPop_Front_Shortcode')) {
                WpPopPop_Front_Shortcode::register_onload($popup_uid, isset($instance['onload_delay']) ? (int) $instance['onload_delay'] : 0);
            }
        } else {
            /* Inline Popup: shortcode handler owns lookup, aliases, test-mode and duplicate protection */
            echo do_shortcode('[wppoppop uid="' . esc_attr($popup_uid) . '" mode="inline"]');
        }

        echo $args['after_widget'];
    }
}
