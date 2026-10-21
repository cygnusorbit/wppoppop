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
        add_action('wp_ajax_wppoppop_library_cats_save', array($this, 'save_categories'));
        add_action('wp_ajax_wppoppop_template_preview', array($this, 'preview'));
    }

    private function guard() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Unauthorized action.'));
        }
        $nonce = isset($_REQUEST['nonce']) ? sanitize_text_field(wp_unslash($_REQUEST['nonce'])) : '';
        if (!wp_verify_nonce($nonce, 'wppoppop_builder_nonce') && !wp_verify_nonce($nonce, 'wppoppop_admin_nonce')) {
            wp_send_json_error(array('message' => 'Security check failed. Please refresh the page.'));
        }
    }

    private function all() {
        $t = get_option(self::OPT, array());
        return is_array($t) ? $t : array();
    }

    public function save() {
        $this->guard();

        $s = get_option('wppoppop_settings', array());
        if (is_array($s) && array_key_exists('builder_save_template', $s) && empty($s['builder_save_template'])) {
            wp_send_json_error(array('message' => 'Save As Template is turned off in Settings > Builder.'));
        }

        $name = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
        if ($name === '') {
            wp_send_json_error(array('message' => 'Please enter a popup name.'));
        }
        $desc  = isset($_POST['description']) ? sanitize_textarea_field(wp_unslash($_POST['description'])) : '';
        $image = isset($_POST['image']) ? esc_url_raw(wp_unslash($_POST['image'])) : '';

        $raw = isset($_POST['data']) ? wp_unslash($_POST['data']) : '';
        $obj = json_decode($raw);
        if (!is_object($obj)) {
            wp_send_json_error(array('message' => 'Malformed popup definition.'));
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
            $mo_in = isset($obj->settings->module_order) ? $obj->settings->module_order : array();
            if (is_object($mo_in)) { $mo_in = array_values(get_object_vars($mo_in)); }
            $mo = array();
            if (is_array($mo_in)) {
                foreach (array_slice($mo_in, 0, 60) as $mk) {
                    if (!is_scalar($mk)) { continue; }
                    $mk = sanitize_key((string) $mk);
                    if ($mk !== '' && !in_array($mk, $mo, true)) { $mo[] = $mk; }
                }
            }
            $obj->settings->module_order = $mo;
        }
        $obj  = WpPopPop_Sanitizer::anim_payload($obj);
        $json = wp_json_encode($obj);
        if (!is_string($json) || $json === '') {
            wp_send_json_error(array('message' => 'Could not encode template.'));
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
            wp_send_json_error(array('message' => 'Template limit reached (200). Delete some templates first.'));
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
            'message' => 'Template saved to Popups Library!',
            'library' => admin_url('admin.php?page=wppoppop-library'),
        ));
    }

    public function use_template() {
        $this->guard();
        global $wpdb;
        $id  = isset($_POST['id']) ? sanitize_key(wp_unslash($_POST['id'])) : '';
        $all = $this->all();
        if ($id === '' || !isset($all[$id])) {
            wp_send_json_error(array('message' => 'Template not found.'));
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
            wp_send_json_error(array('message' => 'Could not generate a unique UID.'));
        }
        $ok = $wpdb->insert($wpdb->prefix . 'wppoppop_items', array(
            'uid'    => $uid,
            'title'  => $t['title'],
            'data'   => $t['data'],
            'status' => 'draft',
        ), array('%s', '%s', '%s', '%s'));
        if ($ok === false) {
            wp_send_json_error(array('message' => 'Database error while creating popup.'));
        }
        wp_send_json_success(array(
            'uid'      => $uid,
            'redirect' => admin_url('admin.php?page=wppoppop-builder&uid=' . rawurlencode($uid)),
        ));
    }

    public function delete() {
        $this->guard();
        $id  = isset($_POST['id']) ? sanitize_key(wp_unslash($_POST['id'])) : '';
        $all = $this->all();
        if ($id === '' || !isset($all[$id])) {
            wp_send_json_error(array('message' => 'Template not found.'));
        }
        unset($all[$id]);
        update_option(self::OPT, $all, false);
        wp_send_json_success(array('message' => 'Template deleted.'));
    }

    /** Stage a saved template so the real frontend renderer can show it (admins only). */
    public function preview() {
        $this->guard();
        $id  = isset($_POST['id']) ? sanitize_key(wp_unslash($_POST['id'])) : '';
        $all = $this->all();
        if ($id === '' || !isset($all[$id]) || empty($all[$id]['data'])) {
            wp_send_json_error(array('message' => 'Template not found.'));
        }
        $decoded = json_decode($all[$id]['data'], true);
        if (!is_array($decoded)) {
            wp_send_json_error(array('message' => 'Template data is damaged.'));
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
            wp_send_json_error(array('message' => 'Malformed category list.'));
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
        wp_send_json_success(array('cats' => $out, 'message' => 'Categories saved.'));
    }
}
