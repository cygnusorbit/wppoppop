<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Front_Display {
    protected $targeting;
    protected $renderer;

    public function __construct(WpPopPop_Front_Targeting $targeting, WpPopPop_Front_Renderer $renderer) {
        $this->targeting = $targeting;
        $this->renderer  = $renderer;

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
            'visitor_country' => $this->targeting->detect_visitor_country(),
            'cookie_epoch'    => get_option('wppoppop_cookie_epoch', '1'),
            'current_user'    => $current_user_data
        ]);
    }

    public function render_targeted_popups() {
        if (is_admin()) {
            return;
        }

        // Live Site Preview Interceptor (?wppoppop_preview=UID)
        if (isset($_GET['wppoppop_preview']) && current_user_can('manage_options')) {
            $preview_uid = sanitize_key($_GET['wppoppop_preview']);
            global $wpdb;
            $preview_row = $wpdb->get_row($wpdb->prepare("SELECT title, data FROM {$wpdb->prefix}wppoppop_items WHERE uid = %s", $preview_uid), ARRAY_A);
            if ($preview_row) {
                $preview_config = json_decode($preview_row['data'], true) ?: [];
                $preview_config['triggers']['on_load'] = true;
                $preview_config['triggers']['on_load_delay'] = 0;
                ?>
                <!-- Admin Live Site Preview Banner -->
                <div id="wppoppop-preview-bar" style="position:fixed;top:0;left:0;right:0;height:40px;background:#1e293b;color:#f8fafc;z-index:999999;display:flex;align-items:center;justify-content:space-between;padding:0 20px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;font-size:13px;box-shadow:0 2px 8px rgba(0,0,0,0.2);">
                    <div>
                        <strong>WpPopPop Preview:</strong> <?php echo esc_html($preview_row['title']); ?> &bull; <code><?php echo esc_html($preview_uid); ?></code>
                    </div>
                    <div style="display:flex;gap:12px;">
                        <a href="<?php echo esc_url(admin_url('admin.php?page=wppoppop-builder&uid=' . $preview_uid)); ?>" style="color:#38bdf8;text-decoration:none;font-weight:600;">&larr; Return to Builder</a>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=wppoppop')); ?>" style="color:#94a3b8;text-decoration:none;">Dashboard</a>
                    </div>
                </div>
                <?php
                $this->renderer->render_popup_markup($preview_uid, $preview_config, false);
                return;
            }
        }

        // Normal Delivery Pipeline
        global $wpdb;
        $popups = $wpdb->get_results("SELECT uid, data FROM {$wpdb->prefix}wppoppop_items WHERE status = 'publish'");
        if (empty($popups)) {
            return;
        }

        foreach ($popups as $popup) {
            $config = json_decode($popup->data, true);
            if (!empty($config) && $this->targeting->matches_targeting($config)) {
                $this->renderer->render_popup_markup($popup->uid, $config, false);
            }
        }
    }

    public function render_shortcode($atts) {
        $atts = shortcode_atts(['uid' => ''], $atts, 'wppoppop');
        if (empty($atts['uid'])) {
            return '';
        }
        global $wpdb;
        $popup = $wpdb->get_row($wpdb->prepare("SELECT uid, data FROM {$wpdb->prefix}wppoppop_items WHERE uid = %s AND status = 'publish'", sanitize_key($atts['uid'])));
        if (!$popup) {
            return '';
        }
        $config = json_decode($popup->data, true);
        if (!$config) {
            return '';
        }

        ob_start();
        $this->renderer->render_popup_markup($popup->uid, $config, true);
        return ob_get_clean();
    }

    public function render_ab_shortcode($atts) {
        $atts = shortcode_atts(['uid' => ''], $atts, 'wppoppop_ab');
        if (empty($atts['uid'])) {
            return '';
        }
        global $wpdb;
        $campaign = $wpdb->get_row($wpdb->prepare("SELECT popup_uids FROM {$wpdb->prefix}wppoppop_campaigns WHERE uid = %s AND status = 'active'", sanitize_key($atts['uid'])));
        if (!$campaign) {
            return '';
        }
        $uids = json_decode($campaign->popup_uids, true);
        if (empty($uids)) {
            return '';
        }
        return $this->render_shortcode(['uid' => $uids[array_rand($uids)]]);
    }

    public function render_locker_shortcode($atts, $content = null) {
        $atts = shortcode_atts(['uid' => ''], $atts, 'wppoppop_locker');
        if (empty($atts['uid'])) {
            return $content;
        }

        $cookie_key = 'wppoppop_unlocked_' . sanitize_key($atts['uid']);
        if (isset($_COOKIE[$cookie_key])) {
            return do_shortcode($content);
        }

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
}
