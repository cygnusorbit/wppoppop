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
        add_shortcode('wppoppop_locker', [$this, 'render_locker_shortcode']);
    }

    public function enqueue_frontend_assets() {
        wp_enqueue_style('wppoppop-front-css', WPPOPPOP_URL . 'public/css/wppoppop-front.css', [], WPPOPPOP_VERSION);
        wp_enqueue_script('wppoppop-front-js', WPPOPPOP_URL . 'public/js/wppoppop-front.js', ['jquery'], WPPOPPOP_VERSION, true);

        $current_user_data = [
            'logged_in' => is_user_logged_in(),
            'email'     => '',
            'name'      => '',
            'login'     => '',
            'roles'     => []
        ];

        if (is_user_logged_in()) {
            $user = wp_get_current_user();
            $current_user_data['email'] = $user->user_email;
            $current_user_data['name']  = $user->display_name;
            $current_user_data['login'] = $user->user_login;
            $current_user_data['roles'] = (array)$user->roles;
        }

        wp_localize_script('wppoppop-front-js', 'wppoppop_front_vars', [
            'ajax_url'        => admin_url('admin-ajax.php'),
            'rest_url'        => esc_url(rest_url('wppoppop/v1/')),
            'nonce'           => wp_create_nonce('wppoppop_front_nonce'),
            'visitor_country' => $this->detect_visitor_country(),
            'cookie_epoch'    => get_option('wppoppop_cookie_epoch', '1'),
            'current_user'    => $current_user_data
        ]);
    }

    public function detect_visitor_country() {
        if (!empty($_SERVER['HTTP_CF_IPCOUNTRY'])) return strtoupper(sanitize_text_field($_SERVER['HTTP_CF_IPCOUNTRY']));
        if (!empty($_SERVER['HTTP_X_COUNTRY_CODE'])) return strtoupper(sanitize_text_field($_SERVER['HTTP_X_COUNTRY_CODE']));
        return '';
    }

    public function render_targeted_popups() {
        if (is_admin()) return;
        global $wpdb;
        $popups = $wpdb->get_results("SELECT uid, data FROM {$wpdb->prefix}wppoppop_items WHERE status = 'publish'");
        if (empty($popups)) return;

        foreach ($popups as $popup) {
            $config = json_decode($popup->data, true);
            if (!empty($config) && $this->matches_targeting($config)) {
                $this->render_popup_markup($popup->uid, $config, false);
            }
        }
    }

    private function matches_targeting(array $config) {
        $targeting = $config['targeting'] ?? [];
        $scope = $targeting['scope'] ?? 'everywhere';

        // 1. Device Viewport
        $device = $targeting['devices'] ?? 'all';
        $is_mobile = wp_is_mobile();
        if ($device === 'desktop' && $is_mobile) return false;
        if ($device === 'mobile' && !$is_mobile) return false;

        // 2. Geolocation Filter
        $geo_mode = $targeting['geo_mode'] ?? 'all';
        if ($geo_mode !== 'all' && !empty($targeting['geo_countries'])) {
            $countries = array_map('trim', explode(',', strtoupper($targeting['geo_countries'])));
            $visitor_country = $this->detect_visitor_country();
            if ($geo_mode === 'whitelist' && (!in_array($visitor_country, $countries, true))) return false;
            if ($geo_mode === 'blacklist' && in_array($visitor_country, $countries, true)) return false;
        }

        // 3. User Authentication & Role
        $auth_mode = $targeting['auth_mode'] ?? 'all';
        $is_logged_in = is_user_logged_in();
        if ($auth_mode === 'guests_only' && $is_logged_in) return false;
        if ($auth_mode === 'logged_in_only') {
            if (!$is_logged_in) return false;
            if (!empty($targeting['target_roles'])) {
                $target_roles = array_map('trim', explode(',', strtolower($targeting['target_roles'])));
                $user = wp_get_current_user();
                $has_role = array_intersect($target_roles, (array)$user->roles);
                if (empty($has_role)) return false;
            }
        }

        // 4. URL Query / UTM Targeting
        if (!empty($targeting['url_param_key'])) {
            $key = sanitize_key($targeting['url_param_key']);
            if (!isset($_GET[$key])) return false;
            if (!empty($targeting['url_param_val']) && sanitize_text_field($_GET[$key]) !== sanitize_text_field($targeting['url_param_val'])) {
                return false;
            }
        }

        // 5. Taxonomy Category Filter
        if (!empty($targeting['category_slugs']) && is_single()) {
            $cats = array_map('trim', explode(',', $targeting['category_slugs']));
            if (!has_category($cats)) return false;
        }

        // 6. Page Scope
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

    public function render_locker_shortcode($atts, $content = null) {
        $atts = shortcode_atts(['uid' => ''], $atts, 'wppoppop_locker');
        if (empty($atts['uid'])) return $content;

        $cookie_key = 'wppoppop_unlocked_' . sanitize_key($atts['uid']);
        if (isset($_COOKIE[$cookie_key])) return do_shortcode($content);

        ob_start();
        ?>
        <div class="wppoppop-locker-wrap" data-uid="<?php echo esc_attr($atts['uid']); ?>">
            <div class="wppoppop-locker-blurred"><?php echo do_shortcode($content); ?></div>
            <div class="wppoppop-locker-overlay">
                <button type="button" class="wppoppop-locker-btn wppoppop-open-btn" data-target-uid="<?php echo esc_attr($atts['uid']); ?>">
                    Unlock Exclusive Content
                </button>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function render_popup_markup($uid, array $config, $is_inline = false) {
        $meta = $config['meta'] ?? [];
        $width = intval($meta['width'] ?? 640);
        $height = intval($meta['height'] ?? 400);
        $bg_color = esc_attr($meta['bg_color'] ?? '#ffffff');
        $elements = (array)($config['elements'] ?? []);
        $styling  = $config['styling'] ?? [];
        $tabs     = $config['tabs'] ?? [];
        $config_json = esc_attr(wp_json_encode($config));

        $backdrop_blur = intval($styling['backdrop_blur'] ?? 0);
        $overlay_style = $backdrop_blur > 0 ? "backdrop-filter: blur({$backdrop_blur}px); -webkit-backdrop-filter: blur({$backdrop_blur}px);" : "";

        $wrapper_class = $is_inline ? 'wppoppop-inline-container' : 'wppoppop-overlay';
        $display_style = $is_inline ? 'position: relative;' : 'display: none; ' . $overlay_style;

        // Group elements by screen
        $screens = [];
        foreach ($elements as $el) {
            $s = intval($el['screen'] ?? 1);
            if (!isset($screens[$s])) $screens[$s] = [];
            $screens[$s][] = $el;
        }
        if (empty($screens)) $screens[1] = [];
        ksort($screens);
        ?>
        <!-- Sticky Side Tab Trigger -->
        <?php if (!empty($tabs['enable'])) : ?>
            <div class="wppoppop-side-tab wppoppop-side-tab-<?php echo esc_attr($tabs['position'] ?? 'left'); ?>"
                 data-target-uid="<?php echo esc_attr($uid); ?>"
                 style="background-color:<?php echo esc_attr($tabs['bg_color'] ?? '#2271b1'); ?>; color:<?php echo esc_attr($tabs['color'] ?? '#ffffff'); ?>;">
                <span><?php echo esc_html($tabs['text'] ?? 'Special Offer'); ?></span>
            </div>
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

                <!-- Anti-Spam Honeypot Field -->
                <div style="display:none !important;" aria-hidden="true">
                    <input type="text" name="_wppoppop_hp_email" class="_wppoppop_hp_field" tabindex="-1" autocomplete="off">
                </div>

                <?php foreach ($screens as $idx => $screen_elements) : 
                    $is_active = ($idx === 1);
                    ?>
                    <div class="wppoppop-screen-container <?php echo $is_active ? 'wppoppop-screen-active' : ''; ?>" data-screen-index="<?php echo $idx; ?>" style="<?php echo $is_active ? '' : 'display:none;'; ?>">
                        <div class="wppoppop-box-content">
                            <?php foreach ($screen_elements as $el) : 
                                $this->render_canvas_element($el);
                            endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="wppoppop-status-overlay" style="display:none;">
                    <div class="wppoppop-status-message"></div>
                </div>
            </div>
        </div>
        <?php
    }

    private function render_canvas_element(array $el) {
        $left = intval($el['left'] ?? 0);
        $top = intval($el['top'] ?? 0);
        $w = intval($el['width'] ?? 160);
        $h = intval($el['height'] ?? 40);
        $z = intval($el['z_index'] ?? 1);
        $field_name = esc_attr($el['field_name'] ?? '');
        $font_family = (!empty($el['font_family']) && $el['font_family'] !== 'Inherit') ? esc_attr($el['font_family']) : 'inherit';
        $anim = esc_attr($el['anim'] ?? 'none');
        $anim_delay = intval($el['anim_delay'] ?? 0);
        $anim_dur = intval($el['anim_duration'] ?? 500);
        $is_required = !empty($el['required']);
        $req_attr = $is_required ? 'required' : '';
        $options = (array)($el['options'] ?? []);
        $styles = "position:absolute;left:{$left}px;top:{$top}px;width:{$w}px;height:{$h}px;z-index:{$z};font-family:{$font_family};";
        ?>
        <div id="<?php echo esc_attr($el['id']); ?>"
             class="wppoppop-layer-item wppoppop-anim-<?php echo $anim; ?>"
             style="<?php echo $styles; ?>"
             data-anim="<?php echo $anim; ?>"
             data-anim-delay="<?php echo $anim_delay; ?>"
             data-anim-duration="<?php echo $anim_dur; ?>"
             data-type="<?php echo esc_attr($el['type']); ?>"
             data-field-name="<?php echo $field_name; ?>"
             data-required="<?php echo $is_required ? '1' : '0'; ?>"
             data-error-msg="<?php echo esc_attr($el['error_msg'] ?? 'This field is required'); ?>">

            <?php if ($el['type'] === 'text') : ?>
                <div class="wppoppop-text-render" data-raw-template="<?php echo esc_attr($el['content'] ?? ''); ?>" style="font-size:<?php echo esc_attr($el['font_size'] ?? '16'); ?>px;color:<?php echo esc_attr($el['color'] ?? '#222'); ?>;">
                    <?php echo esc_html($el['content'] ?? ''); ?>
                </div>

            <?php elseif ($el['type'] === 'input') : ?>
                <input type="email" class="wppoppop-field-email" name="<?php echo $field_name ?: 'email'; ?>" placeholder="<?php echo esc_attr($el['content'] ?? 'Enter email...'); ?>" <?php echo $req_attr; ?> style="width:100%;height:100%;padding:0 12px;box-sizing:border-box;">

            <?php elseif ($el['type'] === 'number') : ?>
                <input type="number" name="<?php echo $field_name ?: 'qty'; ?>" value="<?php echo esc_attr($el['content'] ?? '1'); ?>" class="wppoppop-field-number" style="width:100%;height:100%;padding:0 10px;box-sizing:border-box;">

            <?php elseif ($el['type'] === 'dropdown') : ?>
                <select name="<?php echo $field_name ?: 'choice'; ?>" class="wppoppop-field-select" style="width:100%;height:100%;">
                    <?php foreach ($options as $opt) : ?>
                        <option value="<?php echo esc_attr($opt); ?>"><?php echo esc_html($opt); ?></option>
                    <?php endforeach; ?>
                </select>

            <?php elseif ($el['type'] === 'radio') : ?>
                <div class="wppoppop-field-radios" style="display:flex;gap:12px;align-items:center;height:100%;">
                    <?php foreach ($options as $k => $opt) : ?>
                        <label><input type="radio" name="<?php echo $field_name ?: 'radio_opt'; ?>" value="<?php echo esc_attr($opt); ?>" <?php echo $k === 0 ? 'checked' : ''; ?>> <?php echo esc_html($opt); ?></label>
                    <?php endforeach; ?>
                </div>

            <?php elseif ($el['type'] === 'checkbox') : ?>
                <div class="wppoppop-field-checkboxes" style="display:flex;gap:12px;align-items:center;height:100%;">
                    <label><input type="checkbox" name="<?php echo $field_name ?: 'terms'; ?>" value="1" <?php echo $req_attr; ?>> <?php echo esc_html($el['content'] ?? 'I agree to the terms'); ?></label>
                </div>

            <?php elseif ($el['type'] === 'rating') : ?>
                <div class="wppoppop-field-rating" data-stars="5" style="font-size:22px;color:#f59e0b;cursor:pointer;">
                    &#9733;&#9733;&#9733;&#9733;&#9733;
                    <input type="hidden" name="<?php echo $field_name ?: 'rating'; ?>" value="5">
                </div>

            <?php elseif ($el['type'] === 'date') : ?>
                <input type="date" name="<?php echo $field_name ?: 'date'; ?>" class="wppoppop-field-date" style="width:100%;height:100%;padding:0 10px;box-sizing:border-box;">

            <?php elseif ($el['type'] === 'slider') : ?>
                <div style="width:100%;height:100%;display:flex;align-items:center;">
                    <input type="range" name="<?php echo $field_name ?: 'range'; ?>" min="0" max="100" value="50" style="width:100%;">
                </div>

            <?php elseif ($el['type'] === 'signature') : ?>
                <div class="wppoppop-signature-wrap" style="width:100%;height:100%;border:1px dashed #cbd5e1;background:#fff;position:relative;">
                    <canvas class="wppoppop-sig-canvas" width="<?php echo $w; ?>" height="<?php echo $h; ?>"></canvas>
                    <button type="button" class="wppoppop-sig-clear-btn" style="position:absolute;top:2px;right:2px;font-size:10px;">Clear</button>
                    <input type="hidden" name="<?php echo $field_name ?: 'signature'; ?>" class="wppoppop-sig-input" value="">
                </div>

            <?php elseif ($el['type'] === 'wheel') : ?>
                <div class="wppoppop-wheel-wrapper">
                    <canvas class="wppoppop-wheel-canvas" width="220" height="220" data-slices="<?php echo esc_attr(wp_json_encode($options)); ?>"></canvas>
                    <div class="wppoppop-wheel-pointer">&#9660;</div>
                    <button type="button" class="wppoppop-wheel-spin-btn">SPIN!</button>
                    <input type="hidden" name="<?php echo $field_name ?: 'prize'; ?>" value="">
                </div>

            <?php elseif ($el['type'] === 'scratch') : ?>
                <div class="wppoppop-scratch-wrapper" style="width:100%;height:100%;position:relative;">
                    <div class="wppoppop-scratch-secret" style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:18px;background:#fef08a;">
                        <?php echo esc_html($el['content'] ?? 'SAVE50'); ?>
                    </div>
                    <canvas class="wppoppop-scratch-canvas" width="<?php echo $w; ?>" height="<?php echo $h; ?>" style="position:absolute;inset:0;"></canvas>
                </div>

            <?php elseif ($el['type'] === 'countdown') : ?>
                <div class="wppoppop-countdown-timer" data-seconds="<?php echo intval($el['content'] ?? 900); ?>" style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:18px;background:#1e293b;color:#fff;border-radius:4px;">
                    <span class="cd-mins">15</span>m : <span class="cd-secs">00</span>s
                </div>

            <?php elseif ($el['type'] === 'progress') : ?>
                <div style="width:100%;height:100%;background:#e2e8f0;border-radius:10px;overflow:hidden;position:relative;">
                    <div class="wppoppop-progress-fill" style="width:50%;height:100%;background:#2271b1;transition:width 0.3s ease;"></div>
                </div>

            <?php elseif ($el['type'] === 'file') : ?>
                <input type="file" name="<?php echo $field_name ?: 'attachment'; ?>" style="width:100%;height:100%;font-size:12px;">

            <?php elseif ($el['type'] === 'nextstep') : ?>
                <button type="button" class="wppoppop-next-screen-btn" data-goto="<?php echo intval($el['goto_screen'] ?? 2); ?>" style="width:100%;height:100%;background-color:<?php echo esc_attr($el['bg_color'] ?? '#2271b1'); ?>;color:#fff;border:none;border-radius:4px;cursor:pointer;font-weight:600;">
                    <?php echo esc_html($el['content'] ?? 'Next Step &rarr;'); ?>
                </button>

            <?php elseif ($el['type'] === 'button') : ?>
                <button type="button" class="wppoppop-submit-trigger" style="width:100%;height:100%;background-color:<?php echo esc_attr($el['bg_color'] ?? '#00a32a'); ?>;color:#fff;border:none;border-radius:4px;cursor:pointer;font-weight:600;">
                    <?php echo esc_html($el['content'] ?? 'Submit'); ?>
                </button>

            <?php elseif ($el['type'] === 'pay_btn') : ?>
                <button type="button" class="wppoppop-pay-trigger" data-amount="<?php echo esc_attr($el['amount'] ?? '10.00'); ?>" data-currency="<?php echo esc_attr($el['currency'] ?? 'USD'); ?>" style="width:100%;height:100%;background-color:#0284c7;color:#fff;border:none;border-radius:4px;cursor:pointer;font-weight:700;">
                    Pay <?php echo esc_html($el['amount'] ?? '10.00'); ?> <?php echo esc_html($el['currency'] ?? 'USD'); ?>
                </button>

            <?php elseif ($el['type'] === 'html') : ?>
                <div><?php echo wp_kses_post($el['content'] ?? ''); ?></div>
            <?php endif; ?>
        </div>
        <?php
    }
}
