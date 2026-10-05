<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Front {
    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_assets']);
        add_action('wp_footer', [$this, 'render_frontend_delivery']);
        add_shortcode('wppoppop', [$this, 'render_shortcode']);
        add_shortcode('wppoppop_ab', [$this, 'render_ab_shortcode']);
    }

    public function enqueue_frontend_assets() {
        wp_enqueue_style('wppoppop-front-css', WPPOPPOP_URL . 'public/css/wppoppop-front.css', [], WPPOPPOP_VERSION);
        wp_enqueue_script('wppoppop-front-js', WPPOPPOP_URL . 'public/js/wppoppop-front.js', ['jquery'], WPPOPPOP_VERSION, true);

        wp_localize_script('wppoppop-front-js', 'wppoppop_front_vars', [
            'ajax_url'        => admin_url('admin-ajax.php'),
            'rest_url'        => esc_url(rest_url('wppoppop/v1/')),
            'nonce'           => wp_create_nonce('wppoppop_front_nonce'),
            'visitor_country' => $this->detect_visitor_country()
        ]);
    }

    public function detect_visitor_country() {
        if (!empty($_SERVER['HTTP_CF_IPCOUNTRY'])) return strtoupper(sanitize_text_field($_SERVER['HTTP_CF_IPCOUNTRY']));
        if (!empty($_SERVER['HTTP_X_COUNTRY_CODE'])) return strtoupper(sanitize_text_field($_SERVER['HTTP_X_COUNTRY_CODE']));
        return '';
    }

    public function render_frontend_delivery() {
        if (is_admin()) return;

        global $wpdb;
        $popups = $wpdb->get_results("SELECT uid, data FROM {$wpdb->prefix}wppoppop_items WHERE status = 'publish'");
        if (empty($popups)) return;

        $preload_popups = !empty(wppoppop_get_setting('preload_popups'));
        $manifest = [];

        foreach ($popups as $popup) {
            $config = json_decode($popup->data, true);
            if (empty($config) || !$this->matches_targeting($config)) continue;

            if ($preload_popups) {
                $this->render_popup_markup($popup->uid, $config, false);
            } else {
                $manifest[] = [
                    'uid'      => $popup->uid,
                    'triggers' => $config['triggers'] ?? [],
                    'styling'  => $config['styling'] ?? [],
                    'sound_fx' => $config['sound_fx'] ?? []
                ];
            }
        }

        if (!$preload_popups && !empty($manifest)) {
            ?>
            <script type="text/javascript" id="wppoppop-ondemand-manifest">
                window.wppoppop_manifest = <?php echo wp_json_encode($manifest); ?>;
            </script>
            <?php
        }
    }

    private function matches_targeting(array $config) {
        $targeting = isset($config['targeting']) ? $config['targeting'] : [];
        $scope = isset($targeting['scope']) ? $targeting['scope'] : 'everywhere';

        $device_target = isset($targeting['devices']) ? $targeting['devices'] : 'all';
        $is_mobile = wp_is_mobile();
        if ($device_target === 'desktop' && $is_mobile) return false;
        if ($device_target === 'mobile' && !$is_mobile) return false;

        $geo_mode = isset($targeting['geo_mode']) ? $targeting['geo_mode'] : 'all';
        if ($geo_mode !== 'all' && !empty($targeting['geo_countries'])) {
            $countries = array_map('trim', explode(',', strtoupper($targeting['geo_countries'])));
            $visitor_country = $this->detect_visitor_country();
            if ($geo_mode === 'whitelist' && (!in_array($visitor_country, $countries, true))) return false;
            if ($geo_mode === 'blacklist' && in_array($visitor_country, $countries, true)) return false;
        }

        if (!empty($targeting['category_slugs']) && is_single()) {
            $cats = array_map('trim', explode(',', $targeting['category_slugs']));
            if (!has_category($cats)) return false;
        }

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

        return $this->render_shortcode(['uid' => $uids[array_rand($uids)]]);
    }

    public function render_popup_markup($uid, array $config, $is_inline = false) {
        $meta = isset($config['meta']) ? $config['meta'] : [];
        $width = isset($meta['width']) ? intval($meta['width']) : 640;
        $height = isset($meta['height']) ? intval($meta['height']) : 400;
        $bg_color = isset($meta['bg_color']) ? esc_attr($meta['bg_color']) : '#ffffff';
        $elements = isset($config['elements']) ? (array)$config['elements'] : [];
        $styling  = isset($config['styling']) ? $config['styling'] : [];
        $sound_fx = isset($config['sound_fx']) ? $config['sound_fx'] : [];
        $config_json = esc_attr(wp_json_encode($config));

        $backdrop_blur = !empty($styling['backdrop_blur']) ? intval($styling['backdrop_blur']) : 0;
        $overlay_style = $backdrop_blur > 0 ? "backdrop-filter: blur({$backdrop_blur}px); -webkit-backdrop-filter: blur({$backdrop_blur}px);" : "";

        $wrapper_class = $is_inline ? 'wppoppop-inline-container' : 'wppoppop-overlay';
        $display_style = $is_inline ? 'position: relative;' : 'display: none; ' . $overlay_style;
        ?>
        <div id="wppoppop-popup-<?php echo esc_attr($uid); ?>"
             class="<?php echo esc_attr($wrapper_class); ?>"
             data-uid="<?php echo esc_attr($uid); ?>"
             data-config="<?php echo $config_json; ?>"
             data-sound-fx="<?php echo esc_attr($sound_fx['enable'] ?? 0); ?>"
             style="<?php echo $display_style; ?>">

            <div class="wppoppop-box" style="width: <?php echo $width; ?>px; height: <?php echo $height; ?>px; background-color: <?php echo $bg_color; ?>;">
                <?php if (!$is_inline) : ?>
                    <button type="button" class="wppoppop-close-btn" aria-label="Close">&times;</button>
                <?php endif; ?>

                <div style="display:none !important;" aria-hidden="true">
                    <input type="text" name="_wppoppop_hp_email" class="_wppoppop_hp_field" tabindex="-1" autocomplete="off">
                </div>

                <!-- Screen 1 -->
                <div class="wppoppop-screen-container wppoppop-screen-active" data-screen-index="1">
                    <div class="wppoppop-box-content">
                        <?php foreach ($elements as $el) : 
                            if ((isset($el['screen']) ? intval($el['screen']) : 1) !== 1) continue;
                            $this->render_canvas_element($el);
                        endforeach; ?>
                    </div>
                </div>

                <!-- Screen 2 -->
                <div class="wppoppop-screen-container" data-screen-index="2" style="display:none;">
                    <div class="wppoppop-box-content">
                        <?php foreach ($elements as $el) : 
                            if ((isset($el['screen']) ? intval($el['screen']) : 1) !== 2) continue;
                            $this->render_canvas_element($el);
                        endforeach; ?>
                    </div>
                </div>

                <div class="wppoppop-status-overlay" style="display:none;">
                    <div class="wppoppop-status-message"></div>
                </div>
            </div>
        </div>
        <?php
    }

    private function render_canvas_element(array $el) {
        $left = isset($el['left']) ? intval($el['left']) : 0;
        $top = isset($el['top']) ? intval($el['top']) : 0;
        $w = isset($el['width']) ? intval($el['width']) : 160;
        $h = isset($el['height']) ? intval($el['height']) : 40;
        $z = isset($el['z_index']) ? intval($el['z_index']) : 1;
        $field_name = isset($el['field_name']) ? esc_attr($el['field_name']) : '';
        $font_family = !empty($el['font_family']) && $el['font_family'] !== 'Inherit' ? esc_attr($el['font_family']) : 'inherit';
        $anim = isset($el['anim']) ? esc_attr($el['anim']) : 'none';
        $anim_delay = isset($el['anim_delay']) ? intval($el['anim_delay']) : 0;
        $anim_dur = isset($el['anim_duration']) ? intval($el['anim_duration']) : 500;
        $custom_class = isset($el['custom_class']) ? esc_attr($el['custom_class']) : '';
        $options = isset($el['options']) ? (array)$el['options'] : [];
        $is_required = !empty($el['required']);
        $req_attr = $is_required ? 'required' : '';

        $styles = "position:absolute;left:{$left}px;top:{$top}px;width:{$w}px;height:{$h}px;z-index:{$z};font-family:{$font_family};";
        ?>
        <div id="<?php echo esc_attr($el['id']); ?>"
             class="wppoppop-layer-item wppoppop-anim-<?php echo $anim; ?> <?php echo $custom_class; ?>"
             style="<?php echo $styles; ?>"
             data-anim="<?php echo $anim; ?>"
             data-anim-delay="<?php echo $anim_delay; ?>"
             data-anim-duration="<?php echo $anim_dur; ?>"
             data-type="<?php echo esc_attr($el['type']); ?>"
             data-field-name="<?php echo $field_name; ?>"
             data-required="<?php echo $is_required ? '1' : '0'; ?>"
             data-error-msg="<?php echo esc_attr($el['error_msg'] ?? 'Please fill out this field.'); ?>">

            <?php if ($el['type'] === 'countdown') : 
                $timer_mins = isset($el['timer_mins']) ? intval($el['timer_mins']) : 15;
                ?>
                <div class="wppoppop-countdown-widget" data-mins="<?php echo $timer_mins; ?>">
                    <div class="countdown-col"><span class="unit-val unit-mins"><?php echo sprintf('%02d', $timer_mins); ?></span><span class="unit-label">Mins</span></div>
                    <span class="countdown-sep">:</span>
                    <div class="countdown-col"><span class="unit-val unit-secs">00</span><span class="unit-label">Secs</span></div>
                </div>

            <?php elseif ($el['type'] === 'progress') : 
                $pct = isset($el['progress_pct']) ? intval($el['progress_pct']) : 50;
                ?>
                <div class="wppoppop-progress-track">
                    <div class="wppoppop-progress-fill" style="width: <?php echo $pct; ?>%; background-color: <?php echo esc_attr($el['bg_color'] ?: '#2271b1'); ?>;">
                        <span class="wppoppop-progress-label"><?php echo esc_html($el['content'] ?: $pct . '% Completed'); ?></span>
                    </div>
                </div>

            <?php elseif ($el['type'] === 'slider') : 
                $s_min = isset($el['slider_min']) ? intval($el['slider_min']) : 0;
                $s_max = isset($el['slider_max']) ? intval($el['slider_max']) : 100;
                $s_val = isset($el['slider_val']) ? intval($el['slider_val']) : 50;
                $s_prefix = isset($el['slider_prefix']) ? esc_attr($el['slider_prefix']) : '$';
                ?>
                <div class="wppoppop-slider-widget">
                    <div class="wppoppop-slider-header">
                        <span class="slider-field-title"><?php echo esc_html($el['content'] ?: 'Select Value'); ?>:</span>
                        <span class="slider-live-value"><?php echo $s_prefix . $s_val; ?></span>
                    </div>
                    <input type="range" class="wppoppop-slider-control" name="<?php echo $field_name ?: 'slider_value'; ?>" min="<?php echo $s_min; ?>" max="<?php echo $s_max; ?>" value="<?php echo $s_val; ?>" data-prefix="<?php echo $s_prefix; ?>">
                </div>

            <?php elseif ($el['type'] === 'signature') : ?>
                <div class="wppoppop-signature-wrap" style="width:100%;height:100%;">
                    <canvas class="wppoppop-sig-canvas" width="<?php echo $w; ?>" height="<?php echo $h; ?>"></canvas>
                    <button type="button" class="wppoppop-sig-clear-btn">Clear</button>
                    <input type="hidden" name="<?php echo $field_name ?: 'signature'; ?>" class="wppoppop-sig-input" value="">
                </div>

            <?php elseif ($el['type'] === 'wheel') : ?>
                <div class="wppoppop-wheel-wrapper">
                    <canvas class="wppoppop-wheel-canvas" width="220" height="220" data-slices="<?php echo esc_attr(wp_json_encode($options)); ?>"></canvas>
                    <div class="wppoppop-wheel-pointer">&#9660;</div>
                    <button type="button" class="wppoppop-wheel-spin-btn">SPIN!</button>
                    <input type="hidden" name="<?php echo $field_name ?: 'prize'; ?>" value="">
                </div>

            <?php elseif ($el['type'] === 'text') : ?>
                <div class="wppoppop-text-render" data-raw-template="<?php echo esc_attr($el['content']); ?>" style="font-size:<?php echo esc_attr(isset($el['font_size']) ? $el['font_size'] : '16'); ?>px;color:<?php echo esc_attr(isset($el['color']) ? $el['color'] : '#222'); ?>;">
                    <?php echo esc_html(isset($el['content']) ? $el['content'] : ''); ?>
                </div>

            <?php elseif ($el['type'] === 'input') : ?>
                <input type="email" class="wppoppop-field-email" name="<?php echo $field_name ?: 'email'; ?>" placeholder="<?php echo esc_attr(isset($el['content']) ? $el['content'] : 'Enter your email...'); ?>" <?php echo $req_attr; ?> style="width:100%;height:100%;padding:0 12px;border:1px solid #ccc;border-radius:4px;box-sizing:border-box;">

            <?php elseif ($el['type'] === 'button') : ?>
                <button type="button" class="wppoppop-submit-trigger" style="width:100%;height:100%;background-color:<?php echo esc_attr(isset($el['bg_color']) ? $el['bg_color'] : '#00a32a'); ?>;color:#fff;border:none;border-radius:4px;cursor:pointer;font-weight:600;">
                    <?php echo esc_html(isset($el['content']) ? $el['content'] : 'Submit'); ?>
                </button>

            <?php elseif ($el['type'] === 'nextstep') : ?>
                <button type="button" class="wppoppop-next-screen-btn" data-goto="2" style="width:100%;height:100%;background-color:<?php echo esc_attr(isset($el['bg_color']) ? $el['bg_color'] : '#2271b1'); ?>;color:#fff;border:none;border-radius:4px;cursor:pointer;font-weight:600;">
                    <?php echo esc_html(isset($el['content']) ? $el['content'] : 'Next &rarr;'); ?>
                </button>

            <?php elseif ($el['type'] === 'html') : ?>
                <div><?php echo wp_kses_post(isset($el['content']) ? $el['content'] : ''); ?></div>
            <?php endif; ?>
        </div>
        <?php
    }
}
