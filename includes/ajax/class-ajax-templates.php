<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * User-saved popup templates ("Save As Template" -> Popups Library).
 * Stored in option wppoppop_user_templates (non-autoloaded).
 */
class WpPopPop_Ajax_Templates {
    const OPT = 'wppoppop_user_templates';

    public function __construct() {
        add_action('wp_ajax_wppoppop_template_save', array($this, 'save'));
        add_action('wp_ajax_wppoppop_template_use', array($this, 'use_template'));
        add_action('wp_ajax_wppoppop_template_delete', array($this, 'delete'));
        add_action('wp_ajax_wppoppop_template_download', array($this, 'download'));
        add_action('wp_ajax_wppoppop_template_get', array($this, 'get_one'));
        add_action('wp_ajax_wppoppop_template_update', array($this, 'update_info'));
        add_action('wp_ajax_wppoppop_library_cats_save', array($this, 'save_categories'));
        add_action('wp_ajax_wppoppop_template_preview', array($this, 'preview'));
    }

    private function guard() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Unauthorized action.', 'wppoppop')));
        }
        $nonce = isset($_REQUEST['nonce']) ? sanitize_text_field(wp_unslash($_REQUEST['nonce'])) : '';
        if (!wp_verify_nonce($nonce, 'wppoppop_builder_nonce') && !wp_verify_nonce($nonce, 'wppoppop_admin_nonce')) {
            wp_send_json_error(array('message' => __('Security check failed. Please refresh the page.', 'wppoppop')));
        }
    }

    private function all() {
        $t = get_option(self::OPT, array());
        return is_array($t) ? $t : array();
    }

    public function save() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => esc_html__( 'Unauthorized user capability.', 'wppoppop' ) ), 403 );
            wp_die();
        }
        $wpp_verified = check_ajax_referer( 'wppoppop_nonce', 'nonce', false )
            || check_ajax_referer( 'wppoppop_nonce', '_ajax_nonce', false )
            || check_ajax_referer( 'wppoppop_admin', 'nonce', false )
            || check_ajax_referer( 'wppoppop_nonce', 'security', false )
            || check_ajax_referer( 'wppoppop_builder_nonce', 'nonce', false )
            || check_ajax_referer( 'wppoppop_admin_nonce', 'nonce', false )
            || check_ajax_referer( 'wppoppop_settings_nonce', 'nonce', false );
        if ( ! $wpp_verified ) {
            wp_send_json_error( array( 'message' => esc_html__( 'Invalid security token.', 'wppoppop' ) ), 403 );
            wp_die();
        }

        $this->guard();

        $s = get_option('wppoppop_settings', array());
        if (is_array($s) && array_key_exists('builder_save_template', $s) && empty($s['builder_save_template'])) {
            wp_send_json_error(array('message' => __('Save As Template is turned off in Settings > Builder.', 'wppoppop')));
        }

        $name = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
        if ($name === '') {
            wp_send_json_error(array('message' => __('Please enter a popup name.', 'wppoppop')));
        }
        $desc  = isset($_POST['description']) ? sanitize_textarea_field(wp_unslash($_POST['description'])) : '';
        $image = isset($_POST['image']) ? esc_url_raw(wp_unslash($_POST['image'])) : '';

        $raw = isset($_POST['data']) ? wp_unslash($_POST['data']) : '';
        $obj = json_decode($raw);
        if (!is_object($obj)) {
            wp_send_json_error(array('message' => __('Malformed popup definition.', 'wppoppop')));
        }
        if (!class_exists('WpPopPop_Sanitizer')) {
            require_once WPPOPPOP_PATH . 'includes/class-wppoppop-sanitizer.php';
        }
        /* WPPOPPOP_TPL_WHITELIST: parity with the popup save whitelists */
        if (isset($obj->settings) && is_object($obj->settings)) {
            if (!class_exists('WpPopPop_Overlay')) { require_once WPPOPPOP_PATH . 'includes/class-wppoppop-overlay.php'; }
            $obj->settings->overlay = json_decode(wp_json_encode(
                WpPopPop_Overlay::sanitize(isset($obj->settings->overlay) ? $obj->settings->overlay : array())
            ));
            $cel_ok = array('default', 'none', 'burst', 'cannon', 'fireworks', 'rain', 'stars', 'both');
            $cel    = (isset($obj->settings->celebration) && is_object($obj->settings->celebration) && isset($obj->settings->celebration->effect))
                ? (string) $obj->settings->celebration->effect : 'default';
            $obj->settings->celebration = (object) array('effect' => in_array($cel, $cel_ok, true) ? $cel : 'default');
            if (!class_exists('WpPopPop_Popup_Position')) { require_once WPPOPPOP_PATH . 'includes/class-wppoppop-popup-position.php'; }
            $obj->settings->popup_position = json_decode(wp_json_encode( /* WPPOPPOP_POPUP_POSITION_WHITELIST */
                WpPopPop_Popup_Position::sanitize(isset($obj->settings->popup_position) ? $obj->settings->popup_position : array())
            ));
            if (!class_exists('WpPopPop_Targeting_Triggers')) { require_once WPPOPPOP_PATH . 'includes/class-wppoppop-targeting-triggers.php'; }
            $obj->settings->targeting_triggers = json_decode(wp_json_encode( /* WPPOPPOP_TARGETING_TRIGGERS_WHITELIST */
                WpPopPop_Targeting_Triggers::sanitize(isset($obj->settings->targeting_triggers) ? $obj->settings->targeting_triggers : array())
            ));
            $bgc = isset($obj->settings->close_on_backdrop) ? $obj->settings->close_on_backdrop : false; /* WPPOPPOP_BG_CLOSE_WHITELIST */
            $obj->settings->close_on_backdrop = in_array($bgc, array(true, 1, '1', 'true'), true);
            if (!class_exists('WpPopPop_Autoresponder')) { require_once WPPOPPOP_PATH . 'includes/class-wppoppop-autoresponder.php'; }
            $obj->settings->autoresponder = json_decode(wp_json_encode( /* WPPOPPOP_AUTORESPONDER_WHITELIST */
                WpPopPop_Autoresponder::sanitize(isset($obj->settings->autoresponder) ? $obj->settings->autoresponder : array())
            ));
            if (!class_exists('WpPopPop_Side_Tabs')) { require_once WPPOPPOP_PATH . 'includes/class-wppoppop-side-tabs.php'; }
            $obj->settings->sidetab = json_decode(wp_json_encode( /* WPPOPPOP_SIDE_TABS_WHITELIST */
                WpPopPop_Side_Tabs::sanitize(isset($obj->settings->sidetab) ? $obj->settings->sidetab : array())
            ));
            if (!class_exists('WpPopPop_Webhooks')) { require_once WPPOPPOP_PATH . 'includes/class-wppoppop-webhooks.php'; }
            $obj->settings->webhooks = json_decode(wp_json_encode( /* WPPOPPOP_WEBHOOKS_WHITELIST */
                WpPopPop_Webhooks::sanitize(isset($obj->settings->webhooks) ? $obj->settings->webhooks : array())
            ));
            if (!class_exists('WpPopPop_Woocommerce')) { require_once WPPOPPOP_PATH . 'includes/class-wppoppop-woocommerce.php'; }
            $obj->settings->woocommerce = json_decode(wp_json_encode( /* WPPOPPOP_WOOCOMMERCE_WHITELIST */
                WpPopPop_Woocommerce::sanitize(isset($obj->settings->woocommerce) ? $obj->settings->woocommerce : array())
            ));
            if (!class_exists('WpPopPop_Downloads')) { require_once WPPOPPOP_PATH . 'includes/class-wppoppop-downloads.php'; }
            $obj->settings->downloads = json_decode(wp_json_encode( /* WPPOPPOP_DOWNLOADS_WHITELIST */
                WpPopPop_Downloads::sanitize(isset($obj->settings->downloads) ? $obj->settings->downloads : array())
            ));
            if (!class_exists('WpPopPop_Logic_Math')) { require_once WPPOPPOP_PATH . 'includes/class-wppoppop-logic-math.php'; } /* WPPOPPOP_LOGIC_MATH_WHITELIST */
            $wppoppop_lm = WpPopPop_Logic_Math::sanitize(isset($obj->settings->logic_math) ? $obj->settings->logic_math : array());
            if ($wppoppop_lm['formula'] === '' && $wppoppop_lm['target'] === '') {
                unset($obj->settings->logic_math);
            } else {
                $obj->settings->logic_math = json_decode(wp_json_encode($wppoppop_lm));
            }
            if (!class_exists('WpPopPop_Logic_Payments')) { require_once WPPOPPOP_PATH . 'includes/class-wppoppop-logic-payments.php'; }
            $wppoppop_pay = WpPopPop_Logic_Payments::sanitize(isset($obj->settings->payments) ? $obj->settings->payments : array());
            if ($wppoppop_pay === WpPopPop_Logic_Payments::defaults()) {
                unset($obj->settings->payments);
            } else {
                $obj->settings->payments = json_decode(wp_json_encode($wppoppop_pay));
            }
            if (!class_exists('WpPopPop_Video_Listeners')) { require_once WPPOPPOP_PATH . 'includes/class-wppoppop-video-listeners.php'; }
            $wppoppop_vid = WpPopPop_Video_Listeners::sanitize(isset($obj->settings->video) ? $obj->settings->video : array());
            if ($wppoppop_vid === WpPopPop_Video_Listeners::defaults()) {
                unset($obj->settings->video);
            } else {
                $obj->settings->video = json_decode(wp_json_encode($wppoppop_vid));
            }
            if (!class_exists('WpPopPop_Sms')) { require_once WPPOPPOP_PATH . 'includes/class-wppoppop-sms.php'; }
            $wppoppop_sms = WpPopPop_Sms::sanitize(isset($obj->settings->sms) ? $obj->settings->sms : array());
            if ($wppoppop_sms === WpPopPop_Sms::defaults()) {
                unset($obj->settings->sms);
            } else {
                $obj->settings->sms = json_decode(wp_json_encode($wppoppop_sms));
            }
            if (!class_exists('WpPopPop_Targeting')) { require_once WPPOPPOP_PATH . 'includes/class-wppoppop-targeting.php'; }
            $wppoppop_tg = WpPopPop_Targeting::sanitize(isset($obj->settings->targeting) ? $obj->settings->targeting : array());
            if ($wppoppop_tg === WpPopPop_Targeting::defaults()) {
                unset($obj->settings->targeting);
            } else {
                $obj->settings->targeting = json_decode(wp_json_encode($wppoppop_tg));
            }
            if (!class_exists('WpPopPop_Quiz')) { require_once WPPOPPOP_PATH . 'includes/class-wppoppop-quiz.php'; }
            $wppoppop_qz = WpPopPop_Quiz::sanitize(isset($obj->settings->quiz) ? $obj->settings->quiz : array());
            if ($wppoppop_qz === WpPopPop_Quiz::defaults()) {
                unset($obj->settings->quiz);
            } else {
                $obj->settings->quiz = json_decode(wp_json_encode($wppoppop_qz));
            }
            if (isset($obj->settings->frequency)) {
                if (!class_exists('WpPopPop_Frequency')) { require_once WPPOPPOP_PATH . 'includes/class-wppoppop-frequency.php'; }
                $wppoppop_fq = WpPopPop_Frequency::sanitize($obj->settings->frequency);
                $obj->settings->frequency = json_decode(wp_json_encode($wppoppop_fq));
            }
            if (isset($obj->settings->custom_code)) {
                if (!class_exists('WpPopPop_Custom_Code')) { require_once WPPOPPOP_PATH . 'includes/class-wppoppop-custom-code.php'; }
                $wppoppop_cc = WpPopPop_Custom_Code::sanitize($obj->settings->custom_code, array(), current_user_can('unfiltered_html'));
                if ($wppoppop_cc === WpPopPop_Custom_Code::defaults()) {
                    unset($obj->settings->custom_code);
                } else {
                    $obj->settings->custom_code = json_decode(wp_json_encode($wppoppop_cc));
                }
            }
            if (isset($obj->settings->mailchimp)) { /* WPPOPPOP_MAILCHIMP_WHITELIST: templates never carry API keys */
                if (!class_exists('WpPopPop_Mailchimp')) { require_once WPPOPPOP_PATH . 'includes/class-wppoppop-mailchimp.php'; }
                $wppoppop_mc_tpl = WpPopPop_Mailchimp::sanitize($obj->settings->mailchimp);
                foreach ($wppoppop_mc_tpl as $wppoppop_mc_i => $wppoppop_mc_it) {
                    $wppoppop_mc_tpl[$wppoppop_mc_i]['api_key'] = '';
                    $wppoppop_mc_tpl[$wppoppop_mc_i]['list_id'] = '';
                    $wppoppop_mc_tpl[$wppoppop_mc_i]['list_name'] = '';
                }
                $obj->settings->mailchimp = json_decode(wp_json_encode($wppoppop_mc_tpl));
                if (!is_array($obj->settings->mailchimp)) { $obj->settings->mailchimp = array(); }
            }
            unset($obj->settings->module_order); /* order is global (Settings > Module) */
            if (class_exists('WpPopPop_Modules_Engine')) { WpPopPop_Modules_Engine::sanitize_save($obj->settings); }
        }
        $obj  = WpPopPop_Sanitizer::anim_payload($obj);
        $json = wp_json_encode($obj);
        if (!is_string($json) || $json === '') {
            wp_send_json_error(array('message' => __('Could not encode template.', 'wppoppop')));
        }

        // Recompute counter + size server-side (never trust the client).
        $d       = json_decode($json, true);
        $canv    = array();
        if (isset($d['canvases']) && is_array($d['canvases'])) { $canv = $d['canvases']; }
        elseif (isset($d['screens']) && is_array($d['screens'])) { $canv = $d['screens']; }
        $counts  = array();
        $preview = array();
        $first   = true;
        foreach ($canv as $list) {
            if (!is_array($list)) { continue; }
            foreach ($list as $el) {
                if (!is_array($el)) { continue; }
                $type = isset($el['type']) ? sanitize_key((string) $el['type']) : 'element';
                if ($type === '') { $type = 'element'; }
                $counts[$type] = isset($counts[$type]) ? $counts[$type] + 1 : 1;
                if ($first && count($preview) < 60) {
                    $preview[] = array(
                        'type'   => $type,
                        'left'   => isset($el['left']) ? intval($el['left']) : 0,
                        'top'    => isset($el['top']) ? intval($el['top']) : 0,
                        'width'  => isset($el['width']) ? intval($el['width']) : 100,
                        'height' => isset($el['height']) ? intval($el['height']) : 40,
                    );
                }
            }
            $first = false;
        }
        $meta = (isset($d['canvasMeta'][1]) && is_array($d['canvasMeta'][1])) ? $d['canvasMeta'][1] : ((isset($d['canvasMeta']['1']) && is_array($d['canvasMeta']['1'])) ? $d['canvasMeta']['1'] : array());
        $w = isset($meta['width']) ? max(100, intval($meta['width'])) : 640;
        $h = isset($meta['height']) ? max(100, intval($meta['height'])) : 400;

        $all = $this->all();
        if (count($all) >= 200) {
            wp_send_json_error(array('message' => __('Template limit reached (200). Delete some templates first.', 'wppoppop')));
        }
        $cat = isset($_POST['category']) ? sanitize_key(wp_unslash($_POST['category'])) : 'custom';
        $valid = array();
        foreach (self::get_categories() as $c) { $valid[] = $c['slug']; }
        if (!in_array($cat, $valid, true)) { $cat = 'custom'; }
        $id = 'user-' . substr(md5(uniqid('', true)), 0, 10);
        $all[$id] = array(
            'id'          => $id,
            'title'       => $name,
            'description' => $desc,
            'image'       => $image,
            'category'    => $cat,
            'counts'      => $counts,
            'total'       => array_sum($counts),
            'width'       => $w,
            'height'      => $h,
            'elements'    => $preview,
            'data'        => $json,
            'created'     => time(),
        );
        update_option(self::OPT, $all, false);

        wp_send_json_success(array(
            'id'      => $id,
            'message' => __('Template saved to Popups Library!', 'wppoppop'),
            'library' => admin_url('admin.php?page=wppoppop-library'),
        ));
    }

    public function use_template() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => esc_html__( 'Unauthorized user capability.', 'wppoppop' ) ), 403 );
            wp_die();
        }
        $wpp_verified = check_ajax_referer( 'wppoppop_nonce', 'nonce', false )
            || check_ajax_referer( 'wppoppop_nonce', '_ajax_nonce', false )
            || check_ajax_referer( 'wppoppop_admin', 'nonce', false )
            || check_ajax_referer( 'wppoppop_nonce', 'security', false )
            || check_ajax_referer( 'wppoppop_builder_nonce', 'nonce', false )
            || check_ajax_referer( 'wppoppop_admin_nonce', 'nonce', false )
            || check_ajax_referer( 'wppoppop_settings_nonce', 'nonce', false );
        if ( ! $wpp_verified ) {
            wp_send_json_error( array( 'message' => esc_html__( 'Invalid security token.', 'wppoppop' ) ), 403 );
            wp_die();
        }

        $this->guard();
        global $wpdb;
        $id  = isset($_POST['id']) ? sanitize_key(wp_unslash($_POST['id'])) : '';
        $all = $this->with_presets();
        if ($id === '' || !isset($all[$id])) {
            wp_send_json_error(array('message' => __('Template not found.', 'wppoppop')));
        }
        $t    = $all[$id];
        $base = sanitize_title($t['title']);
        $base = rtrim(substr($base === '' ? 'popup' : $base, 0, 29), '-');
        if ($base === '') { $base = 'popup'; }
        $uid = '';
        for ($i = 0; $i < 30; $i++) {
            $cand = $base . '-' . strtolower(wp_generate_password(6, false, false));
            if (!$wpdb->get_var($wpdb->prepare("SELECT COUNT(1) FROM {$wpdb->prefix}wppoppop_items WHERE uid = %s", $cand))) {
                $uid = $cand;
                break;
            }
        }
        if ($uid === '') {
            wp_send_json_error(array('message' => __('Could not generate a unique UID.', 'wppoppop')));
        }
        $ok = $wpdb->insert($wpdb->prefix . 'wppoppop_items', array(
            'uid'    => $uid,
            'title'  => $t['title'],
            'data'   => $t['data'],
            'status' => 'draft',
        ), array('%s', '%s', '%s', '%s'));
        if ($ok === false) {
            wp_send_json_error(array('message' => __('Database error while creating popup.', 'wppoppop')));
        }
        wp_send_json_success(array(
            'uid'      => $uid,
            'redirect' => admin_url('admin.php?page=wppoppop-builder&uid=' . rawurlencode($uid)),
        ));
    }

    public function delete() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => esc_html__( 'Unauthorized user capability.', 'wppoppop' ) ), 403 );
            wp_die();
        }
        $wpp_verified = check_ajax_referer( 'wppoppop_nonce', 'nonce', false )
            || check_ajax_referer( 'wppoppop_nonce', '_ajax_nonce', false )
            || check_ajax_referer( 'wppoppop_admin', 'nonce', false )
            || check_ajax_referer( 'wppoppop_nonce', 'security', false )
            || check_ajax_referer( 'wppoppop_builder_nonce', 'nonce', false )
            || check_ajax_referer( 'wppoppop_admin_nonce', 'nonce', false )
            || check_ajax_referer( 'wppoppop_settings_nonce', 'nonce', false );
        if ( ! $wpp_verified ) {
            wp_send_json_error( array( 'message' => esc_html__( 'Invalid security token.', 'wppoppop' ) ), 403 );
            wp_die();
        }

        $this->guard();
        $id  = isset($_POST['id']) ? sanitize_key(wp_unslash($_POST['id'])) : '';
        $all = $this->all();
        if ($id === '' || !isset($all[$id])) {
            wp_send_json_error(array('message' => __('Template not found.', 'wppoppop')));
        }
        unset($all[$id]);
        update_option(self::OPT, $all, false);
        wp_send_json_success(array('message' => __('Template deleted.', 'wppoppop')));
    }

    /** Stage a saved template so the real frontend renderer can show it (admins only). */
    public function preview() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => esc_html__( 'Unauthorized user capability.', 'wppoppop' ) ), 403 );
            wp_die();
        }
        $wpp_verified = check_ajax_referer( 'wppoppop_nonce', 'nonce', false )
            || check_ajax_referer( 'wppoppop_nonce', '_ajax_nonce', false )
            || check_ajax_referer( 'wppoppop_admin', 'nonce', false )
            || check_ajax_referer( 'wppoppop_nonce', 'security', false )
            || check_ajax_referer( 'wppoppop_builder_nonce', 'nonce', false )
            || check_ajax_referer( 'wppoppop_admin_nonce', 'nonce', false )
            || check_ajax_referer( 'wppoppop_settings_nonce', 'nonce', false );
        if ( ! $wpp_verified ) {
            wp_send_json_error( array( 'message' => esc_html__( 'Invalid security token.', 'wppoppop' ) ), 403 );
            wp_die();
        }

        $this->guard();
        $id  = isset($_POST['id']) ? sanitize_key(wp_unslash($_POST['id'])) : '';
        $all = $this->with_presets();
        if ($id === '' || !isset($all[$id]) || empty($all[$id]['data'])) {
            wp_send_json_error(array('message' => __('Template not found.', 'wppoppop')));
        }
        $decoded = json_decode($all[$id]['data'], true);
        if (!is_array($decoded)) {
            wp_send_json_error(array('message' => __('Template data is damaged.', 'wppoppop')));
        }
        $uid = sanitize_key('tpl-' . $id);
        /* Same key scheme as WpPopPop_Front_Preview::transient_key() */
        set_transient('wppoppop_prev_' . get_current_user_id() . '_' . md5($uid), wp_json_encode($decoded), HOUR_IN_SECONDS);
        $url = add_query_arg(array(
            'wppoppop_preview' => $uid,
            'wppoppop_draft'   => 1,
            'wppoppop_t'       => time(),
        ), home_url('/'));
        wp_send_json_success(array(
            'uid'       => $uid,
            'frame_url' => add_query_arg('wppoppop_frame', 1, $url),
        ));
    }

    const CATS_OPT = 'wppoppop_library_categories';

    public static function default_categories() {
        return array(
            array('slug' => 'lead-gen',     'label' => __('Lead Generation', 'wppoppop')),
            array('slug' => 'ecommerce',    'label' => __('E-Commerce & Coupons', 'wppoppop')),
            array('slug' => 'gamified',     'label' => __('Gamified & Interactive', 'wppoppop')),
            array('slug' => 'feedback',     'label' => __('Surveys & Feedback', 'wppoppop')),
            array('slug' => 'announcement', 'label' => __('Announcements', 'wppoppop')),
            array('slug' => 'custom',       'label' => __('My Templates', 'wppoppop')),
        );
    }

    /** Ordered filter categories (excluding the fixed "All" pill). */
    public static function get_categories() {
        $c = get_option(self::CATS_OPT, null);
        if (!is_array($c) || empty($c)) {
            return self::default_categories();
        }
        $out = array();
        $has_custom = false;
        foreach ($c as $r) {
            if (!is_array($r) || empty($r['slug']) || !isset($r['label']) || $r['label'] === '') { continue; }
            if ($r['slug'] === 'custom') { $has_custom = true; }
            $out[] = array('slug' => (string) $r['slug'], 'label' => (string) $r['label']);
        }
        if (!$has_custom) {
            $out[] = array('slug' => 'custom', 'label' => __('My Templates', 'wppoppop'));
        }
        return $out;
    }

    public function save_categories() {
        $this->guard();
        $raw = isset($_POST['cats']) ? json_decode(wp_unslash($_POST['cats']), true) : null;
        if (!is_array($raw)) {
            wp_send_json_error(array('message' => __('Malformed category list.', 'wppoppop')));
        }
        $out  = array();
        $seen = array();
        foreach (array_slice($raw, 0, 30) as $r) {
            if (!is_array($r)) { continue; }
            $label = isset($r['label']) && is_scalar($r['label']) ? sanitize_text_field((string) $r['label']) : '';
            $label = function_exists('mb_substr') ? mb_substr($label, 0, 40) : substr($label, 0, 40);
            if ($label === '') { continue; }
            $slug = isset($r['slug']) && is_scalar($r['slug']) ? sanitize_key((string) $r['slug']) : '';
            if ($slug === '') { $slug = sanitize_title($label); }
            if ($slug === '') { $slug = 'cat'; }
            if ($slug === 'all') { $slug = 'all-cat'; }
            $base = $slug;
            $i = 2;
            while (isset($seen[$slug])) { $slug = $base . '-' . $i; $i++; }
            $seen[$slug] = 1;
            $out[] = array('slug' => $slug, 'label' => $label);
        }
        if (!isset($seen['custom'])) {
            $out[] = array('slug' => 'custom', 'label' => __('My Templates', 'wppoppop'));
        }
        update_option(self::CATS_OPT, $out, false);
        wp_send_json_success(array('cats' => $out, 'message' => __('Categories saved.', 'wppoppop')));
    }

    public function get_one() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => esc_html__( 'Unauthorized user capability.', 'wppoppop' ) ), 403 );
            wp_die();
        }
        $wpp_verified = check_ajax_referer( 'wppoppop_nonce', 'nonce', false )
            || check_ajax_referer( 'wppoppop_nonce', '_ajax_nonce', false )
            || check_ajax_referer( 'wppoppop_admin', 'nonce', false )
            || check_ajax_referer( 'wppoppop_nonce', 'security', false )
            || check_ajax_referer( 'wppoppop_builder_nonce', 'nonce', false )
            || check_ajax_referer( 'wppoppop_admin_nonce', 'nonce', false )
            || check_ajax_referer( 'wppoppop_settings_nonce', 'nonce', false );
        if ( ! $wpp_verified ) {
            wp_send_json_error( array( 'message' => esc_html__( 'Invalid security token.', 'wppoppop' ) ), 403 );
            wp_die();
        }

        $this->guard();
        $id  = isset($_POST['id']) ? sanitize_key(wp_unslash($_POST['id'])) : '';
        $all = $this->all();
        if ($id === '' || !isset($all[$id])) {
            wp_send_json_error(array('message' => __('Template not found.', 'wppoppop')));
        }
        $t = $all[$id];
        wp_send_json_success(array(
            'id'          => $id,
            'title'       => isset($t['title']) ? $t['title'] : '',
            'description' => isset($t['description']) ? $t['description'] : '',
            'image'       => isset($t['image']) ? $t['image'] : '',
            'category'    => (isset($t['category']) && $t['category'] !== '') ? $t['category'] : 'custom',
            'counts'      => isset($t['counts']) && is_array($t['counts']) ? $t['counts'] : array(),
            'total'       => isset($t['total']) ? (int) $t['total'] : 0,
            'width'       => isset($t['width']) ? (int) $t['width'] : 640,
            'height'      => isset($t['height']) ? (int) $t['height'] : 400,
        ));
    }

    public function update_info() {
        $this->guard();
        $id  = isset($_POST['id']) ? sanitize_key(wp_unslash($_POST['id'])) : '';
        $all = $this->all();
        if ($id === '' || !isset($all[$id])) {
            wp_send_json_error(array('message' => __('Template not found.', 'wppoppop')));
        }
        $name = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
        if ($name === '') {
            wp_send_json_error(array('message' => __('Please enter a popup name.', 'wppoppop')));
        }
        $valid = array();
        foreach (self::get_categories() as $c) { $valid[] = $c['slug']; }
        $cat = isset($_POST['category']) ? sanitize_key(wp_unslash($_POST['category'])) : 'custom';
        if (!in_array($cat, $valid, true)) { $cat = 'custom'; }
        $all[$id]['title']       = $name;
        $all[$id]['description'] = isset($_POST['description']) ? sanitize_textarea_field(wp_unslash($_POST['description'])) : '';
        $all[$id]['image']       = isset($_POST['image']) ? esc_url_raw(wp_unslash($_POST['image'])) : '';
        $all[$id]['category']    = $cat;
        update_option(self::OPT, $all, false);
        wp_send_json_success(array('message' => __('Template updated.', 'wppoppop')));
    }

    /** Returns a portable template JSON (drop-in format for /library-presets/). */
    public function download() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => esc_html__( 'Unauthorized user capability.', 'wppoppop' ) ), 403 );
            wp_die();
        }
        $wpp_verified = check_ajax_referer( 'wppoppop_nonce', 'nonce', false )
            || check_ajax_referer( 'wppoppop_nonce', '_ajax_nonce', false )
            || check_ajax_referer( 'wppoppop_admin', 'nonce', false )
            || check_ajax_referer( 'wppoppop_nonce', 'security', false )
            || check_ajax_referer( 'wppoppop_builder_nonce', 'nonce', false )
            || check_ajax_referer( 'wppoppop_admin_nonce', 'nonce', false )
            || check_ajax_referer( 'wppoppop_settings_nonce', 'nonce', false );
        if ( ! $wpp_verified ) {
            wp_send_json_error( array( 'message' => esc_html__( 'Invalid security token.', 'wppoppop' ) ), 403 );
            wp_die();
        }

        $this->guard();
        $id  = isset($_POST['id']) ? sanitize_key(wp_unslash($_POST['id'])) : '';
        $all = method_exists($this, 'with_presets') ? $this->with_presets() : $this->all();
        if ($id === '' || !isset($all[$id]) || empty($all[$id]['data'])) {
            wp_send_json_error(array('message' => __('Template not found.', 'wppoppop')));
        }
        $t    = $all[$id];
        $data = json_decode($t['data']);
        if (!is_object($data)) {
            wp_send_json_error(array('message' => __('Template data is damaged.', 'wppoppop')));
        }
        $payload = array(
            'template' => array(
                'title'       => isset($t['title']) ? (string) $t['title'] : '',
                'description' => isset($t['description']) ? (string) $t['description'] : '',
                'category'    => (isset($t['category']) && $t['category'] !== '') ? (string) $t['category'] : 'custom',
                'image'       => isset($t['image']) ? (string) $t['image'] : '',
            ),
            'data' => $data,
        );
        $json = wp_json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if (!is_string($json) || $json === '') {
            wp_send_json_error(array('message' => __('Could not encode template.', 'wppoppop')));
        }
        $name = sanitize_title(isset($t['title']) ? $t['title'] : '');
        wp_send_json_success(array(
            'filename' => ($name !== '' ? $name : 'template') . '-wppoppop-template.json',
            'payload'  => $json,
        ));
    }

    /** Saved templates + premade files from /library-presets/ (premade are read-only, never written back). */
    private function with_presets() {
        if (!class_exists('WpPopPop_Library_Presets')) {
            require_once WPPOPPOP_PATH . 'includes/class-wppoppop-library-presets.php';
        }
        return $this->all() + WpPopPop_Library_Presets::all();
    }
}
