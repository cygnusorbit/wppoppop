<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Front {
    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_assets']);
        add_action('wp_footer', [$this, 'render_targeted_popups']);
        add_shortcode('wppoppop', [$this, 'render_shortcode']);
        add_shortcode('wppoppop_ab', [$this, 'render_ab_shortcode']);
    }

    public function enqueue_frontend_assets() {
        wp_enqueue_style('wppoppop-front-css', WPPOPPOP_URL . 'public/css/wppoppop-front.css', [], WPPOPPOP_VERSION);
        wp_enqueue_script('wppoppop-front-js', WPPOPPOP_URL . 'public/js/wppoppop-front.js', ['jquery'], WPPOPPOP_VERSION, true);

        wp_localize_script('wppoppop-front-js', 'wppoppop_front_vars', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('wppoppop_front_nonce')
        ]);
    }

    public function render_targeted_popups() {
        if (is_admin()) {
            return;
        }

        global $wpdb;
        $popups = $wpdb->get_results("SELECT uid, data FROM {$wpdb->prefix}wppoppop_items WHERE status = 'publish'");

        if (empty($popups)) {
            return;
        }

        foreach ($popups as $popup) {
            $config = json_decode($popup->data, true);
            if (!empty($config) && $this->matches_targeting($config)) {
                $this->render_popup_markup($popup->uid, $config, false);
            }
        }
    }

    private function matches_targeting(array $config) {
        $targeting = isset($config['targeting']) ? $config['targeting'] : [];
        $scope = isset($targeting['scope']) ? $targeting['scope'] : 'everywhere';

        if ($scope === 'everywhere') return true;
        if ($scope === 'posts' && is_single()) return true;
        if ($scope === 'pages' && is_page()) return true;
        if ($scope === 'specific') {
            $allowed_ids = isset($targeting['specific_ids']) ? array_filter(array_map('intval', explode(',', $targeting['specific_ids']))) : [];
            return in_array(get_queried_object_id(), $allowed_ids, true);
        }
        return false;
    }

    public function render_shortcode($atts) {
        $atts = shortcode_atts(['uid' => ''], $atts, 'wppoppop');
        if (empty($atts['uid'])) return '';

        global $wpdb;
        $popup = $wpdb->get_row($wpdb->prepare("SELECT uid, data FROM {$wpdb->prefix}wppoppop_items WHERE uid = %s AND status = 'publish'", sanitize_key($atts['uid'])));
        if (!$popup) return '';

        $config = json_decode($popup->data, true);
        if (!$config) return '';

        ob_start();
        $this->render_popup_markup($popup->uid, $config, true);
        return ob_get_clean();
    }

    public function render_ab_shortcode($atts) {
        $atts = shortcode_atts(['uid' => ''], $atts, 'wppoppop_ab');
        if (empty($atts['uid'])) return '';

        global $wpdb;
        $campaign = $wpdb->get_row($wpdb->prepare("SELECT popup_uids FROM {$wpdb->prefix}wppoppop_campaigns WHERE uid = %s AND status = 'active'", sanitize_key($atts['uid'])));
        if (!$campaign) return '';

        $uids = json_decode($campaign->popup_uids, true);
        if (empty($uids)) return '';

        $selected_uid = $uids[array_rand($uids)];
        return $this->render_shortcode(['uid' => $selected_uid]);
    }

    public function render_popup_markup($uid, array $config, $is_inline = false) {
        $meta = isset($config['meta']) ? $config['meta'] : [];
        $width = isset($meta['width']) ? intval($meta['width']) : 640;
        $height = isset($meta['height']) ? intval($meta['height']) : 400;
        $bg_color = isset($meta['bg_color']) ? esc_attr($meta['bg_color']) : '#ffffff';
        $elements = isset($config['elements']) ? (array)$config['elements'] : [];
        $custom_css = isset($config['custom_css']) ? $config['custom_css'] : '';
        $config_json = esc_attr(wp_json_encode($config));

        $wrapper_class = $is_inline ? 'wppoppop-inline-container' : 'wppoppop-overlay';
        $display_style = $is_inline ? 'position: relative;' : 'display: none;';
        ?>
        <?php if (!empty($custom_css)) : ?>
            <style type="text/css"><?php echo wp_strip_all_tags($custom_css); ?></style>
        <?php endif; ?>

        <div id="wppoppop-popup-<?php echo esc_attr($uid); ?>"
             class="<?php echo esc_attr($wrapper_class); ?>"
             data-uid="<?php echo esc_attr($uid); ?>"
             data-config="<?php echo $config_json; ?>"
             style="<?php echo $display_style; ?>">

            <div class="wppoppop-box" style="width: <?php echo $width; ?>px; height: <?php echo $height; ?>px; background-color: <?php echo $bg_color; ?>;">
                <?php if (!$is_inline) : ?>
                    <button type="button" class="wppoppop-close-btn" aria-label="Close">&times;</button>
                <?php endif; ?>

                <div class="wppoppop-box-content">
                    <?php foreach ($elements as $el) : 
                        $left = isset($el['left']) ? intval($el['left']) : 0;
                        $top = isset($el['top']) ? intval($el['top']) : 0;
                        $w = isset($el['width']) ? intval($el['width']) : 160;
                        $h = isset($el['height']) ? intval($el['height']) : 40;
                        $z = isset($el['z_index']) ? intval($el['z_index']) : 1;
                        $field_name = isset($el['field_name']) ? esc_attr($el['field_name']) : '';
                        $custom_class = isset($el['custom_class']) ? esc_attr($el['custom_class']) : '';
                        $styles = "position:absolute;left:{$left}px;top:{$top}px;width:{$w}px;height:{$h}px;z-index:{$z};";
                        ?>
                        <div id="<?php echo esc_attr($el['id']); ?>"
                             class="wppoppop-layer-item <?php echo $custom_class; ?>"
                             style="<?php echo $styles; ?>"
                             data-type="<?php echo esc_attr($el['type']); ?>"
                             data-field-name="<?php echo $field_name; ?>">
                            <?php if ($el['type'] === 'text') : ?>
                                <div class="wppoppop-text-render" style="font-size:<?php echo esc_attr(isset($el['font_size']) ? $el['font_size'] : '16'); ?>px;color:<?php echo esc_attr(isset($el['color']) ? $el['color'] : '#222'); ?>;">
                                    <?php echo esc_html(isset($el['content']) ? $el['content'] : ''); ?>
                                </div>
                            <?php elseif ($el['type'] === 'input') : ?>
                                <input type="email" class="wppoppop-field-email" name="<?php echo $field_name ? $field_name : 'email'; ?>" placeholder="<?php echo esc_attr(isset($el['content']) ? $el['content'] : 'Enter your email...'); ?>" required style="width:100%;height:100%;padding:0 12px;border:1px solid #ccc;border-radius:4px;box-sizing:border-box;">
                            <?php elseif ($el['type'] === 'number') : ?>
                                <input type="number" class="wppoppop-field-number" name="<?php echo $field_name; ?>" value="1" min="0" style="width:100%;height:100%;padding:0 12px;border:1px solid #ccc;border-radius:4px;box-sizing:border-box;">
                            <?php elseif ($el['type'] === 'button') : ?>
                                <button type="button" class="wppoppop-submit-trigger" style="width:100%;height:100%;background-color:<?php echo esc_attr(isset($el['bg_color']) ? $el['bg_color'] : '#00a32a'); ?>;color:#fff;border:none;border-radius:4px;cursor:pointer;font-weight:600;">
                                    <?php echo esc_html(isset($el['content']) ? $el['content'] : 'Submit'); ?>
                                </button>
                            <?php elseif ($el['type'] === 'paybutton') : ?>
                                <button type="button" class="wppoppop-pay-trigger" style="width:100%;height:100%;background-color:<?php echo esc_attr(isset($el['bg_color']) ? $el['bg_color'] : '#2271b1'); ?>;color:#fff;border:none;border-radius:4px;cursor:pointer;font-weight:600;">
                                    <?php echo esc_html(isset($el['content']) ? $el['content'] : 'Pay Now'); ?>
                                </button>
                            <?php elseif ($el['type'] === 'html') : ?>
                                <div><?php echo wp_kses_post(isset($el['content']) ? $el['content'] : ''); ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="wppoppop-status-overlay" style="display:none;">
                    <div class="wppoppop-status-message"></div>
                </div>
            </div>
        </div>
        <?php
    }
}
