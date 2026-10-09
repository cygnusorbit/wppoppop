<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WpPopPop_Libraries')) {
    require_once WPPOPPOP_PATH . 'includes/class-wppoppop-libraries.php';
}

class WpPopPop_Front_Display {
    protected $targeting;
    protected $renderer;

    public function __construct(WpPopPop_Front_Targeting $targeting, WpPopPop_Front_Renderer $renderer) {
        $this->targeting = $targeting;
        $this->renderer  = $renderer;

        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('wp_head', [$this, 'print_custom_head_assets'], 99);
        add_action('wp_footer', [$this, 'print_custom_footer_assets'], 99);
        add_action('wp_footer', [$this, 'render_popups'], 50);
    }

    protected function is_execution_allowed() {
        $test_mode = (bool) wppoppop_get_setting('test_mode', false);
        if ($test_mode && !current_user_can('manage_options')) {
            return false;
        }
        return true;
    }

    public function enqueue_assets() {
        if (!$this->is_execution_allowed()) {
            return;
        }

        $cache_busting = (bool) wppoppop_get_setting('cache_busting', false);
        $cookie_epoch  = (int) wppoppop_get_setting('cookie_epoch', 1);
        $asset_version = $cache_busting ? WPPOPPOP_VERSION . '.' . $cookie_epoch . '.' . time() : WPPOPPOP_VERSION;

        wp_enqueue_style('wppoppop-front-css', WPPOPPOP_URL . 'public/css/front/modal.css', [], $asset_version);
        wp_enqueue_script('wppoppop-front-js', WPPOPPOP_URL . 'public/js/front/front-display.js', ['jquery'], $asset_version, true);
        wp_enqueue_script('wppoppop-front-form-js', WPPOPPOP_URL . 'public/js/front/front-form.js', ['jquery', 'wppoppop-front-js'], $asset_version, true);

        // Enqueue Google reCAPTCHA if enabled
        $recaptcha_enabled  = (bool) wppoppop_get_setting('enable_recaptcha', false);
        $recaptcha_site_key = wppoppop_get_setting('recaptcha_site_key', '');
        if ($recaptcha_enabled && !empty($recaptcha_site_key)) {
            wp_enqueue_script(
                'wppoppop-recaptcha-api',
                'https://www.google.com/recaptcha/api.js?render=' . esc_attr($recaptcha_site_key),
                [],
                null,
                true
            );
        }

        WpPopPop_Libraries::enqueue_frontend_libraries();

        $runtime_payload = [
            'ajax_url'           => admin_url('admin-ajax.php'),
            'nonce'              => wp_create_nonce('wppoppop_front_nonce'),
            'cookie_epoch'       => $cookie_epoch,
            'render_delay'       => max(0, (int) wppoppop_get_setting('render_delay', 0)),
            'preload_events'     => (bool) wppoppop_get_setting('preload_events', false),
            'ga_tracking'        => (bool) wppoppop_get_setting('ga_tracking', false),
            'adblock_detector'   => (bool) wppoppop_get_setting('adblock_detector', false),
            'test_mode'          => (bool) wppoppop_get_setting('test_mode', false),
            'default_font'       => esc_attr(wppoppop_get_setting('default_font', 'inherit')),
            'enable_recaptcha'   => $recaptcha_enabled,
            'recaptcha_site_key' => esc_attr($recaptcha_site_key),
            'libraries'          => [
                'confetti'   => (bool) wppoppop_get_setting('load_canvas_confetti', false),
                'fireworks'  => (bool) wppoppop_get_setting('load_canvas_fireworks', false),
                'datepicker' => (bool) (wppoppop_get_setting('load_air_datepicker', false) || wppoppop_get_setting('air_datepicker', false)),
                'mask'       => (bool) (wppoppop_get_setting('load_jquery_mask', false) || wppoppop_get_setting('jquery_mask', false)),
                'signature'  => (bool) (wppoppop_get_setting('load_signature_pad', false) || wppoppop_get_setting('signature_pad', false)),
                'slider'     => (bool) (wppoppop_get_setting('load_range_slider', false) || wppoppop_get_setting('range_slider', false)),
                'math'       => (bool) (wppoppop_get_setting('load_math_parser', false) || wppoppop_get_setting('js_parser', false))
            ]
        ];

        wp_localize_script('wppoppop-front-js', 'wppoppop_front_vars', $runtime_payload);
        wp_localize_script('wppoppop-front-form-js', 'wppoppop_front_vars', $runtime_payload);
    }

    public function print_custom_head_assets() {
        if (!$this->is_execution_allowed()) {
            return;
        }

        WpPopPop_Libraries::print_font_head_tags();

        $header_html = wppoppop_get_setting('custom_header_html', '');
        if (!empty($header_html)) {
            echo "\n<!-- WpPopPop Head Markup -->\n" . $header_html . "\n";
        }

        $custom_css = wppoppop_get_setting('custom_css', '');
        if (!empty($custom_css)) {
            if ((bool) wppoppop_get_setting('minify_css', false)) {
                $custom_css = wppoppop_minify_css($custom_css);
            }
            echo "\n<style id=\"wppoppop-global-custom-css\">\n" . $custom_css . "\n</style>\n";
        }
    }

    public function print_custom_footer_assets() {
        if (!$this->is_execution_allowed()) {
            return;
        }

        if ((bool) wppoppop_get_setting('adblock_detector', false)) {
            ?>
            <div id="wppoppop-ad-bait" class="pub_300x250 pub_728x90 text-ad textAd text_ad text_ads text-ads ad-server" style="position:absolute!important;left:-9999px!important;top:-9999px!important;width:1px!important;height:1px!important;pointer-events:none!important;" aria-hidden="true">&nbsp;</div>
            <script>
            (function() {
                var probe = document.getElementById('wppoppop-ad-bait');
                window.wppoppop_adblock_detected = false;
                if (!probe || probe.offsetHeight === 0 || probe.clientHeight === 0 || window.getComputedStyle(probe).display === 'none') {
                    window.wppoppop_adblock_detected = true;
                }
            })();
            </script>
            <?php
        }

        $custom_js = wppoppop_get_setting('custom_js', '');
        if (!empty($custom_js)) {
            echo "\n<script id=\"wppoppop-global-custom-js\">\n" . $custom_js . "\n</script>\n";
        }

        $footer_html = wppoppop_get_setting('custom_footer_html', '');
        if (!empty($footer_html)) {
            echo "\n<!-- WpPopPop Footer Tracking -->\n" . $footer_html . "\n";
        }
    }

    public function render_popups() {
        if (!$this->is_execution_allowed()) {
            return;
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';
        if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") !== $table_name) {
            return;
        }

        $popups = $wpdb->get_results("SELECT * FROM {$table_name} WHERE status = 'publish' ORDER BY id DESC");
        if (empty($popups)) {
            return;
        }

        $preload_popups = (bool) wppoppop_get_setting('preload_popups', false);

        foreach ($popups as $popup) {
            if ($this->targeting->is_eligible($popup)) {
                $config = !empty($popup->config) ? json_decode($popup->config, true) : [];
                if (!is_array($config)) {
                    $config = [];
                }
                echo $this->renderer->render_popup_markup($popup->uid, $config, false, $preload_popups);
            }
        }
    }
}
