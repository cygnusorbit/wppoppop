<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Admin_Pages {
    public function render_dashboard() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';
        $popups = $wpdb->get_results("SELECT * FROM {$table_name} ORDER BY id DESC");
        include WPPOPPOP_PATH . 'templates/dashboard-view.php';
    }

    public function render_builder() {
        global $wpdb;
        $uid = isset($_GET['uid']) ? sanitize_text_field(wp_unslash($_GET['uid'])) : '';
        $popup = null;
        if (!empty($uid)) {
            $table_name = $wpdb->prefix . 'wppoppop_items';
            $popup = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_name} WHERE uid = %s", $uid));
        }
        include WPPOPPOP_PATH . 'templates/builder-view.php';
    }

    public function render_ab() {
        global $wpdb;
        $campaigns = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}wppoppop_campaigns ORDER BY id DESC");
        include WPPOPPOP_PATH . 'templates/ab-view.php';
    }

    public function render_submissions() {
        global $wpdb;
        $submissions = $wpdb->get_results("SELECT s.*, i.title as popup_title FROM {$wpdb->prefix}wppoppop_submissions s LEFT JOIN {$wpdb->prefix}wppoppop_items i ON s.popup_uid = i.uid ORDER BY s.id DESC LIMIT 200");
        include WPPOPPOP_PATH . 'templates/submissions-view.php';
    }

    public function render_log() {
        include WPPOPPOP_PATH . 'templates/log-view.php';
    }

    public function render_stats() {
        include WPPOPPOP_PATH . 'templates/stats-view.php';
    }

    public function render_field_analytics() {
        include WPPOPPOP_PATH . 'templates/field-analytics-view.php';
    }

    public function render_payments() {
        global $wpdb;
        $transactions = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}wppoppop_transactions ORDER BY id DESC LIMIT 100");
        include WPPOPPOP_PATH . 'templates/payments-view.php';
    }

    public function render_library() {
        include WPPOPPOP_PATH . 'templates/library-view.php';
    }

    public function render_settings() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'wppoppop'));
        }

        $notice = '';
        if ('POST' === $_SERVER['REQUEST_METHOD'] && isset($_POST['wppoppop_settings_nonce'])) {
            if (check_admin_referer('wppoppop_save_settings_action', 'wppoppop_settings_nonce')) {
                $raw = isset($_POST['settings']) && is_array($_POST['settings']) ? wp_unslash($_POST['settings']) : wp_unslash($_POST);
                $existing = get_option('wppoppop_settings', []);
                if (!is_array($existing)) {
                    $existing = [];
                }

                $clean = [];

                // Tab 1: General & Mailing
                $clean['sender_name']        = !empty($raw['sender_name']) ? sanitize_text_field($raw['sender_name']) : (!empty($raw['from_name']) ? sanitize_text_field($raw['from_name']) : get_bloginfo('name'));
                $clean['from_name']          = $clean['sender_name'];
                $clean['sender_email']       = !empty($raw['sender_email']) ? sanitize_email($raw['sender_email']) : (!empty($raw['from_email']) ? sanitize_email($raw['from_email']) : get_option('admin_email'));
                $clean['from_email']         = $clean['sender_email'];
                $clean['admin_email']        = !empty($raw['admin_email']) ? sanitize_email($raw['admin_email']) : get_option('admin_email');
                $clean['csv_separator']      = !empty($raw['csv_separator']) ? sanitize_text_field($raw['csv_separator']) : ',';
                $clean['user_uploads']       = !empty($raw['user_uploads']) ? sanitize_text_field($raw['user_uploads']) : 'keep';
                $clean['powered_by_badge']   = !empty($raw['powered_by_badge']) ? 1 : 0;
                $clean['test_mode']          = !empty($raw['test_mode']) ? 1 : 0;

                // Tab 2: Performance & Preload
                $clean['preload_popups']       = !empty($raw['preload_popups']) ? 1 : 0;
                $clean['preload_events']       = !empty($raw['preload_events']) ? 1 : 0;
                $clean['disable_google_fonts'] = !empty($raw['disable_google_fonts']) ? 1 : 0;
                $clean['minify_css']           = !empty($raw['minify_css']) ? 1 : 0;
                $clean['cache_busting']        = !empty($raw['cache_busting']) ? 1 : 0;
                $clean['render_delay']         = isset($raw['render_delay']) ? max(0, (int) $raw['render_delay']) : 0;
                $clean['ga_tracking']          = !empty($raw['ga_tracking']) ? 1 : 0;
                $clean['adblock_detector']     = !empty($raw['adblock_detector']) ? 1 : 0;

                // Tab 3: Typography & Libraries
                $clean['default_font']          = !empty($raw['default_font']) ? sanitize_text_field($raw['default_font']) : 'inherit';
                $clean['google_fonts']          = !empty($raw['google_fonts']) ? 1 : 0;
                $clean['font_awesome']          = !empty($raw['font_awesome']) ? 1 : 0;
                $clean['custom_fonts']          = isset($raw['custom_fonts']) ? sanitize_textarea_field($raw['custom_fonts']) : '';
                $clean['load_canvas_confetti']  = !empty($raw['load_canvas_confetti']) ? 1 : 0;
                $clean['load_canvas_fireworks'] = !empty($raw['load_canvas_fireworks']) ? 1 : 0;
                $clean['air_datepicker']        = (!empty($raw['air_datepicker']) || !empty($raw['load_air_datepicker'])) ? 1 : 0;
                $clean['load_air_datepicker']   = $clean['air_datepicker'];
                $clean['jquery_mask']           = (!empty($raw['jquery_mask']) || !empty($raw['load_jquery_mask'])) ? 1 : 0;
                $clean['load_jquery_mask']      = $clean['jquery_mask'];
                $clean['signature_pad']         = (!empty($raw['signature_pad']) || !empty($raw['load_signature_pad'])) ? 1 : 0;
                $clean['load_signature_pad']    = $clean['signature_pad'];
                $clean['range_slider']          = (!empty($raw['range_slider']) || !empty($raw['load_range_slider'])) ? 1 : 0;
                $clean['load_range_slider']     = $clean['range_slider'];
                $clean['js_parser']             = (!empty($raw['js_parser']) || !empty($raw['load_math_parser'])) ? 1 : 0;
                $clean['load_math_parser']      = $clean['js_parser'];

                // Tab 4: Security & Geolocation
                $clean['email_validation']       = !empty($raw['email_validation']) ? sanitize_text_field($raw['email_validation']) : 'basic';
                $clean['enable_recaptcha']       = !empty($raw['enable_recaptcha']) ? 1 : 0;
                $clean['recaptcha_site_key']     = !empty($raw['recaptcha_site_key']) ? sanitize_text_field($raw['recaptcha_site_key']) : '';
                $clean['recaptcha_secret_key']   = !empty($raw['recaptcha_secret_key']) ? sanitize_text_field($raw['recaptcha_secret_key']) : '';
                $clean['max_submissions_per_ip'] = isset($raw['max_submissions_per_ip']) ? max(1, (int) $raw['max_submissions_per_ip']) : 10;
                $clean['ip_geotargeting']        = !empty($raw['ip_geotargeting']) ? 1 : 0;
                $clean['geoip_service']          = !empty($raw['geoip_service']) ? sanitize_text_field($raw['geoip_service']) : (!empty($raw['geoip_api_service']) ? sanitize_text_field($raw['geoip_api_service']) : 'none');
                $clean['geoip_api_service']      = $clean['geoip_service'];

                // Tab 5: Custom Code & Scripts
                if (current_user_can('unfiltered_html')) {
                    $clean['custom_css']         = isset($raw['custom_css']) ? $raw['custom_css'] : '';
                    $clean['custom_js']          = isset($raw['custom_js']) ? $raw['custom_js'] : '';
                    $clean['custom_header_html'] = isset($raw['custom_header_html']) ? $raw['custom_header_html'] : '';
                    $clean['custom_footer_html'] = isset($raw['custom_footer_html']) ? $raw['custom_footer_html'] : '';
                } else {
                    $clean['custom_css']         = isset($raw['custom_css']) ? wp_strip_all_tags($raw['custom_css']) : '';
                    $clean['custom_js']          = isset($raw['custom_js']) ? wp_strip_all_tags($raw['custom_js']) : '';
                    $clean['custom_header_html'] = isset($raw['custom_header_html']) ? wp_kses_post($raw['custom_header_html']) : '';
                    $clean['custom_footer_html'] = isset($raw['custom_footer_html']) ? wp_kses_post($raw['custom_footer_html']) : '';
                }

                // Tab 6: System Tools
                $clean['clean_on_uninstall'] = !empty($raw['clean_on_uninstall']) ? 1 : 0;
                $clean['cookie_epoch']       = isset($existing['cookie_epoch']) ? (int) $existing['cookie_epoch'] : (int) get_option('wppoppop_cookie_epoch', 1);

                update_option('wppoppop_settings', $clean);
                $notice = 'saved';
            }
        }

        $settings = get_option('wppoppop_settings', []);
        include WPPOPPOP_PATH . 'templates/settings-view.php';
    }

    public function render_tools() {
        include WPPOPPOP_PATH . 'templates/tools-view.php';
    }
}
