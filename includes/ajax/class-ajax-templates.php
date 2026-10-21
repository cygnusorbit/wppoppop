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
        if (is_array($s) && !empty($s['builder_disable_save_template'])) {
            wp_send_json_error(array('message' => 'Save As Template is disabled in Settings > Builder.'));
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
        $id = 'user-' . substr(md5(uniqid('', true)), 0, 10);
        $all[$id] = array(
            'id'          => $id,
            'title'       => $name,
            'description' => $desc,
            'image'       => $image,
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
}
