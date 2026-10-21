<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Ajax_Builder {
    public function __construct() {
        add_action('wp_ajax_wppoppop_builder_save', array($this, 'save_popup'));
        add_action('wp_ajax_wppoppop_save_popup', [$this, 'save_popup']);
        add_action('wp_ajax_wppoppop_load_popup', [$this, 'load_popup']);
        add_action('wp_ajax_wppoppop_duplicate_popup', [$this, 'duplicate_popup']);
        add_action('wp_ajax_wppoppop_delete_popup', [$this, 'delete_popup']);
        add_action('wp_ajax_wppoppop_bulk_delete', [$this, 'bulk_delete']);
        add_action('wp_ajax_wppoppop_export_popup', [$this, 'export_popup']);
        add_action('wp_ajax_wppoppop_import_popup', [$this, 'import_popup']);
        add_action('wp_ajax_wppoppop_save_campaign', [$this, 'save_campaign']);
        add_action('wp_ajax_wppoppop_delete_campaign', [$this, 'delete_campaign']);
    }

    private function verify_security() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized action.']);
        }
        $nonce = isset($_REQUEST['nonce']) ? sanitize_text_field(wp_unslash($_REQUEST['nonce'])) : '';
        if (!wp_verify_nonce($nonce, 'wppoppop_builder_nonce') && !wp_verify_nonce($nonce, 'wppoppop_admin_nonce')) {
            wp_send_json_error(['message' => 'Security check failed. Please refresh the page.']);
        }
    }

    public function save_popup() {
        $this->verify_security();

        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';

        $title = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : 'Untitled Popup';
        $uid   = isset($_POST['uid']) && !empty($_POST['uid']) ? sanitize_key($_POST['uid']) : $this->generate_unique_uid($title);

        // Support both 'data' and 'config' payload parameters
        $data_raw = '{}';
        if (isset($_POST['data'])) {
            $data_raw = wp_unslash($_POST['data']);
        } elseif (isset($_POST['config'])) {
            $data_raw = wp_unslash($_POST['config']);
        }

        if (json_decode($data_raw) === null) {
            wp_send_json_error(['message' => 'Malformed JSON definition.']);
        }

        $wppoppop_obj = json_decode($data_raw);
        /* WPPOPPOP_OVERLAY_WHITELIST: validate popup overlay settings before persisting */
        if (is_object($wppoppop_obj) && isset($wppoppop_obj->settings) && is_object($wppoppop_obj->settings)) {
            if (!class_exists('WpPopPop_Overlay')) { require_once WPPOPPOP_PATH . 'includes/class-wppoppop-overlay.php'; }
            $wppoppop_obj->settings->overlay = json_decode(wp_json_encode(
                WpPopPop_Overlay::sanitize(isset($wppoppop_obj->settings->overlay) ? $wppoppop_obj->settings->overlay : array())
            ));
        }
        /* WPPOPPOP_CELEBRATION_WHITELIST: allow-list the success celebration effect */
        if (is_object($wppoppop_obj) && isset($wppoppop_obj->settings) && is_object($wppoppop_obj->settings)) {
            $wppoppop_cel_ok = array('default', 'none', 'burst', 'cannon', 'fireworks', 'rain', 'stars', 'both');
            $wppoppop_cel    = (isset($wppoppop_obj->settings->celebration) && is_object($wppoppop_obj->settings->celebration) && isset($wppoppop_obj->settings->celebration->effect))
                ? (string) $wppoppop_obj->settings->celebration->effect : 'default';
            $wppoppop_obj->settings->celebration = (object) array(
                'effect' => in_array($wppoppop_cel, $wppoppop_cel_ok, true) ? $wppoppop_cel : 'default'
            );
        }
        /* WPPOPPOP_NOTIFICATIONS_WHITELIST: allow-list the Notifications module */
        if (is_object($wppoppop_obj) && isset($wppoppop_obj->settings) && is_object($wppoppop_obj->settings)) {
            $wppoppop_nin = isset($wppoppop_obj->settings->notifications) ? $wppoppop_obj->settings->notifications : array();
            if (is_object($wppoppop_nin)) { $wppoppop_nin = array_values(get_object_vars($wppoppop_nin)); }
            if (!is_array($wppoppop_nin)) { $wppoppop_nin = array(); }
            $wppoppop_nout = array();
            foreach (array_slice($wppoppop_nin, 0, 20) as $wppoppop_n) {
                $wppoppop_n = is_object($wppoppop_n) ? get_object_vars($wppoppop_n) : (is_array($wppoppop_n) ? $wppoppop_n : array());
                if (empty($wppoppop_n)) { continue; }
                $wppoppop_t = function ($k, $d = '') use ($wppoppop_n) {
                    return (isset($wppoppop_n[$k]) && is_scalar($wppoppop_n[$k])) ? sanitize_text_field((string) $wppoppop_n[$k]) : $d;
                };
                $wppoppop_b = function ($k) use ($wppoppop_n) {
                    return isset($wppoppop_n[$k]) && in_array($wppoppop_n[$k], array(true, 1, '1', 'true'), true);
                };
                $wppoppop_att = array();
                $wppoppop_ain = isset($wppoppop_n['attachments']) ? $wppoppop_n['attachments'] : array();
                if (is_object($wppoppop_ain)) { $wppoppop_ain = array_values(get_object_vars($wppoppop_ain)); }
                foreach ((is_array($wppoppop_ain) ? array_slice($wppoppop_ain, 0, 10) : array()) as $wppoppop_a) {
                    $wppoppop_a = is_object($wppoppop_a) ? get_object_vars($wppoppop_a) : (is_array($wppoppop_a) ? $wppoppop_a : array());
                    $wppoppop_aid = isset($wppoppop_a['id']) ? absint($wppoppop_a['id']) : 0;
                    if ($wppoppop_aid) {
                        $wppoppop_att[] = array(
                            'id'   => $wppoppop_aid,
                            'name' => isset($wppoppop_a['name']) && is_scalar($wppoppop_a['name']) ? sanitize_text_field((string) $wppoppop_a['name']) : '',
                            'url'  => isset($wppoppop_a['url']) && is_scalar($wppoppop_a['url']) ? esc_url_raw((string) $wppoppop_a['url']) : ''
                        );
                    }
                }
                $wppoppop_rules = array();
                $wppoppop_rin = isset($wppoppop_n['rules']) ? $wppoppop_n['rules'] : array();
                if (is_object($wppoppop_rin)) { $wppoppop_rin = array_values(get_object_vars($wppoppop_rin)); }
                foreach ((is_array($wppoppop_rin) ? array_slice($wppoppop_rin, 0, 20) : array()) as $wppoppop_r) {
                    $wppoppop_r = is_object($wppoppop_r) ? get_object_vars($wppoppop_r) : (is_array($wppoppop_r) ? $wppoppop_r : array());
                    $wppoppop_op = isset($wppoppop_r['operator']) ? (string) $wppoppop_r['operator'] : 'is';
                    $wppoppop_rules[] = array(
                        'field'    => isset($wppoppop_r['field']) && is_scalar($wppoppop_r['field']) ? sanitize_text_field((string) $wppoppop_r['field']) : '',
                        'operator' => in_array($wppoppop_op, array('is', 'is_not', 'contains', 'not_contains', 'empty', 'not_empty'), true) ? $wppoppop_op : 'is',
                        'value'    => isset($wppoppop_r['value']) && is_scalar($wppoppop_r['value']) ? sanitize_text_field((string) $wppoppop_r['value']) : ''
                    );
                }
                $wppoppop_send = $wppoppop_t('send', 'after_submission');
                $wppoppop_nout[] = array(
                    'id'           => sanitize_key($wppoppop_t('id')),
                    'name'         => $wppoppop_t('name', 'Notification'),
                    'enable'       => $wppoppop_b('enable'),
                    'send'         => in_array($wppoppop_send, array('after_submission', 'after_confirmation'), true) ? $wppoppop_send : 'after_submission',
                    'recipient'    => $wppoppop_t('recipient'),
                    'subject'      => $wppoppop_t('subject'),
                    'message'      => (isset($wppoppop_n['message']) && is_string($wppoppop_n['message'])) ? wp_kses_post($wppoppop_n['message']) : '',
                    'attachments'  => $wppoppop_att,
                    'reply_to'     => $wppoppop_t('reply_to'),
                    'from_email'   => $wppoppop_t('from_email'),
                    'from_name'    => $wppoppop_t('from_name'),
                    'logic_enable' => $wppoppop_b('logic_enable'),
                    'logic_match'  => $wppoppop_t('logic_match', 'all') === 'any' ? 'any' : 'all',
                    'rules'        => $wppoppop_rules
                );
            }
            $wppoppop_obj->settings->notifications = json_decode(wp_json_encode($wppoppop_nout));
            if (!is_array($wppoppop_obj->settings->notifications)) { $wppoppop_obj->settings->notifications = array(); }
        }
        /* WPPOPPOP_DOUBLEOPTIN_WHITELIST: allow-list the Double Opt-In module */
        if (is_object($wppoppop_obj) && isset($wppoppop_obj->settings) && is_object($wppoppop_obj->settings)) {
            $wppoppop_do = (isset($wppoppop_obj->settings->double_optin) && is_object($wppoppop_obj->settings->double_optin))
                ? get_object_vars($wppoppop_obj->settings->double_optin) : array();
            $wppoppop_do_txt = function ($k, $def = '') use ($wppoppop_do) {
                return (isset($wppoppop_do[$k]) && !is_array($wppoppop_do[$k]) && !is_object($wppoppop_do[$k]))
                    ? sanitize_text_field((string) $wppoppop_do[$k]) : $def;
            };
            $wppoppop_do_en = isset($wppoppop_do['enable']) && in_array($wppoppop_do['enable'], array(true, 1, '1', 'true'), true);
            $wppoppop_obj->settings->double_optin = (object) array(
                'enable'        => $wppoppop_do_en,
                'recipient'     => $wppoppop_do_txt('recipient'),
                'subject'       => $wppoppop_do_txt('subject'),
                'message'       => (isset($wppoppop_do['message']) && is_string($wppoppop_do['message'])) ? wp_kses_post($wppoppop_do['message']) : '',
                'from_email'    => $wppoppop_do_txt('from_email'),
                'from_name'     => $wppoppop_do_txt('from_name'),
                'thank_message' => (isset($wppoppop_do['thank_message']) && is_string($wppoppop_do['thank_message'])) ? wp_kses_post($wppoppop_do['thank_message']) : '',
                'thank_url'     => (isset($wppoppop_do['thank_url']) && is_string($wppoppop_do['thank_url'])) ? esc_url_raw($wppoppop_do['thank_url']) : ''
            );
        }
        if (is_object($wppoppop_obj) || is_array($wppoppop_obj)) {
            $wppoppop_enc = wp_json_encode($this->sanitize_anim_payload($wppoppop_obj));
            if (is_string($wppoppop_enc) && $wppoppop_enc !== '') { $data_raw = $wppoppop_enc; }
        }

        $existing = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$table_name} WHERE uid = %s", $uid));

        if ($existing) {
            $wpdb->update($table_name, ['title' => $title, 'data' => $data_raw], ['uid' => $uid], ['%s', '%s'], ['%s']);
            $uid = $this->maybe_rename_uid($uid, $title); /* WPPOPPOP_UID_FOLLOWS_TITLE */
        } else {
            $wpdb->insert($table_name, ['uid' => $uid, 'title' => $title, 'data' => $data_raw, 'status' => 'publish'], ['%s', '%s', '%s', '%s']);
        }

        $wppoppop_saved_status = $wpdb->get_var($wpdb->prepare("SELECT status FROM {$table_name} WHERE uid = %s", $uid));

        wp_send_json_success([
            'uid'     => $uid,
            'status'  => $wppoppop_saved_status ? $wppoppop_saved_status : 'publish',
            'message' => 'Popup configuration saved successfully!'
        ]);
    }

    /**
     * Unique embed UID: <popup-name>-<6 lowercase alphanumerics>.
     * Max length 36 (same as a UUID) so any existing uid column fits.
     */
    private function generate_unique_uid($title = '') {
        global $wpdb;
        $table = $wpdb->prefix . 'wppoppop_items';
        $base  = sanitize_title((string) $title);
        $base  = preg_replace('/[^a-z0-9-]+/', '-', strtolower($base));
        $base  = trim(preg_replace('/-{2,}/', '-', $base), '-');
        $base  = substr($base, 0, 29);
        $base  = rtrim($base, '-');
        if ($base === '') {
            $base = 'popup';
        }
        $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        for ($try = 0; $try < 30; $try++) {
            $suffix = '';
            for ($i = 0; $i < 6; $i++) {
                $suffix .= $chars[wp_rand(0, strlen($chars) - 1)];
            }
            $candidate = $base . '-' . $suffix;
            $taken = $wpdb->get_var($wpdb->prepare("SELECT COUNT(1) FROM {$table} WHERE uid = %s", $candidate));
            if (!$taken) {
                return $candidate;
            }
        }
        return substr($base, 0, 20) . '-' . strtolower(wp_generate_password(6, false, false));
    }

    /** Slug used for the <popup-name> part of a uid (max 29 chars). */
    private function uid_base($title) {
        $base = sanitize_title((string) $title);
        $base = preg_replace('/[^a-z0-9-]+/', '-', strtolower($base));
        $base = trim(preg_replace('/-{2,}/', '-', $base), '-');
        $base = rtrim(substr($base, 0, 29), '-');
        return $base === '' ? 'popup' : $base;
    }

    /** Rename an auto-format uid (name-xxxxxx) when the title slug changed. Returns final uid. */
    private function maybe_rename_uid($uid, $title) {
        global $wpdb;
        if (!preg_match('/^(.+)-([a-z0-9]{6})$/', $uid, $m)) {
            return $uid;
        }
        $base = $this->uid_base($title);
        if ($m[1] === $base) {
            return $uid;
        }
        $items = $wpdb->prefix . 'wppoppop_items';
        $new   = $base . '-' . $m[2];
        if ($wpdb->get_var($wpdb->prepare("SELECT COUNT(1) FROM {$items} WHERE uid = %s", $new))) {
            $new = $this->generate_unique_uid($title);
        }
        $wpdb->update($items, ['uid' => $new], ['uid' => $uid], ['%s'], ['%s']);
        $wpdb->update($wpdb->prefix . 'wppoppop_submissions', ['popup_uid' => $new], ['popup_uid' => $uid], ['%s'], ['%s']);
        $wpdb->update($wpdb->prefix . 'wppoppop_transactions', ['popup_uid' => $new], ['popup_uid' => $uid], ['%s'], ['%s']);
        $camps = $wpdb->prefix . 'wppoppop_campaigns';
        $rows  = $wpdb->get_results("SELECT id, popup_uids FROM {$camps}", ARRAY_A);
        foreach ((array) $rows as $r) {
            $list = json_decode($r['popup_uids'], true);
            if (is_array($list) && in_array($uid, $list, true)) {
                foreach ($list as $k => $v) { if ($v === $uid) { $list[$k] = $new; } }
                $wpdb->update($camps, ['popup_uids' => wp_json_encode(array_values($list))], ['id' => (int) $r['id']], ['%s'], ['%d']);
            }
        }
        /* RULE: a renamed uid keeps working. Record old -> new so existing shortcodes still resolve. */
        $aliases = get_option('wppoppop_uid_aliases', []);
        if (!is_array($aliases)) { $aliases = []; }
        foreach ($aliases as $ak => $av) { if ($av === $uid) { $aliases[$ak] = $new; } }
        $aliases[$uid] = $new;
        unset($aliases[$new]);
        update_option('wppoppop_uid_aliases', array_slice($aliases, -500, null, true), false);
        return $new;
    }

    private function sanitize_anim_payload($node) {
        if (!class_exists('WpPopPop_Sanitizer')) { require_once WPPOPPOP_PATH . 'includes/class-wppoppop-sanitizer.php'; }
        return WpPopPop_Sanitizer::anim_payload($node);
        if (is_object($node)) {
            foreach (get_object_vars($node) as $k => $v) { $node->$k = $this->sanitize_anim_value($k, $v); }
            return $node;
        }
        if (is_array($node)) {
            foreach ($node as $k => $v) { $node[$k] = $this->sanitize_anim_value($k, $v); }
        }
        return $node;
    }

    private function sanitize_anim_value($k, $v) {
        static $txt = array('anim_appearance', 'anim_disappearance', 'anim_effect', 'animEffect', 'animation', 'animIn', 'animOut');
        static $int = array('anim_duration', 'anim_delay', 'animDuration', 'animDelay');
        if (is_scalar($v) || $v === null) {
            if (in_array($k, $txt, true)) { return preg_replace('/[^A-Za-z0-9_\-]/', '', (string) $v); }
            if (in_array($k, $int, true)) { return max(0, intval($v)); }
            return $v;
        }
        return $this->sanitize_anim_payload($v);
    }

    public function load_popup() {
        $this->verify_security();
        global $wpdb;

        $uid = isset($_REQUEST['uid']) ? sanitize_key($_REQUEST['uid']) : '';
        if (empty($uid)) {
            wp_send_json_error(['message' => 'Missing campaign UID.']);
        }

        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}wppoppop_items WHERE uid = %s", $uid), ARRAY_A);
        if (!$row) {
            wp_send_json_error(['message' => 'Popup not found.']);
        }

        // Return configuration under both 'data' and 'config' properties for maximum compatibility
        $row['config'] = $row['data'];
        wp_send_json_success($row);
    }

    public function duplicate_popup() {
        $this->verify_security();
        global $wpdb;
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}wppoppop_items WHERE uid = %s", isset($_POST['uid']) ? sanitize_key(wp_unslash($_POST['uid'])) : ''), ARRAY_A);
        if (!$row) {
            wp_send_json_error(['message' => 'Source popup not found.']);
        }

        $new_uid = $this->generate_unique_uid($row['title']);
        $wpdb->insert(
            $wpdb->prefix . 'wppoppop_items',
            [
                'uid'           => $new_uid,
                'title'         => $row['title'] . ' (Copy)',
                'data'          => $row['data'],
                'status'        => 'draft',
                'impressions'   => 0,
                'submissions'   => 0,
                'confirmations' => 0
            ],
            ['%s', '%s', '%s', '%s', '%d', '%d', '%d']
        );

        wp_send_json_success([
            'uid'     => $new_uid,
            'title'   => $row['title'] . ' (Copy)',
            'message' => 'Popup duplicated successfully!'
        ]);
    }

    public function delete_popup() {
        $this->verify_security();
        global $wpdb;
        $del_uid = isset($_POST['uid']) ? sanitize_key(wp_unslash($_POST['uid'])) : '';
        if ($del_uid === '') {
            wp_send_json_error(['message' => 'Missing campaign UID.']);
        }
        $wpdb->delete($wpdb->prefix . 'wppoppop_items', ['uid' => $del_uid], ['%s']);
        wp_send_json_success(['message' => 'Popup removed successfully.']);
    }

    public function bulk_delete() {
        $this->verify_security();
        $uids = isset($_POST['uids']) ? (array)$_POST['uids'] : [];
        if (empty($uids)) {
            wp_send_json_error(['message' => 'No popups were selected for deletion.']);
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';
        $sanitized_uids = array_map('sanitize_key', $uids);
        $placeholders = implode(',', array_fill(0, count($sanitized_uids), '%s'));

        $deleted = $wpdb->query($wpdb->prepare("DELETE FROM {$table_name} WHERE uid IN ($placeholders)", $sanitized_uids));

        wp_send_json_success([
            'message'       => sprintf('Successfully removed %d popup campaign(s).', count($sanitized_uids)),
            'deleted_count' => $deleted
        ]);
    }

    public function export_popup() {
        $this->verify_security();
        global $wpdb;
        $row = $wpdb->get_row($wpdb->prepare("SELECT title, data FROM {$wpdb->prefix}wppoppop_items WHERE uid = %s", isset($_GET['uid']) ? sanitize_key(wp_unslash($_GET['uid'])) : ''), ARRAY_A);
        if (!$row) {
            wp_send_json_error(['message' => 'Popup not found.']);
        }
        wp_send_json_success(['filename' => sanitize_title($row['title']) . '-wppoppop-export.json', 'payload' => $row['data']]);
    }

    public function import_popup() {
        $this->verify_security();
        $json_raw = isset($_POST['import_data']) ? wp_unslash($_POST['import_data']) : '';
        $data = json_decode($json_raw, true);
        
        if (isset($data['canvasMeta']) && is_array($data['canvasMeta'])) {
            foreach ($data['canvasMeta'] as $cKey => $cVal) {
                if (is_array($cVal)) {
                    if (isset($cVal['anim_appearance'])) $data['canvasMeta'][$cKey]['anim_appearance'] = sanitize_text_field($cVal['anim_appearance']);
                    if (isset($cVal['anim_duration'])) $data['canvasMeta'][$cKey]['anim_duration'] = intval($cVal['anim_duration']);
                    if (isset($cVal['anim_delay'])) $data['canvasMeta'][$cKey]['anim_delay'] = intval($cVal['anim_delay']);
                    if (isset($cVal['anim_disappearance'])) $data['canvasMeta'][$cKey]['anim_disappearance'] = sanitize_text_field($cVal['anim_disappearance']);
                }
            }
        }
        if (isset($data['canvas_meta']) && is_array($data['canvas_meta'])) {
            foreach ($data['canvas_meta'] as $cKey => $cVal) {
                if (is_array($cVal)) {
                    if (isset($cVal['anim_appearance'])) $data['canvas_meta'][$cKey]['anim_appearance'] = sanitize_text_field($cVal['anim_appearance']);
                    if (isset($cVal['anim_duration'])) $data['canvas_meta'][$cKey]['anim_duration'] = intval($cVal['anim_duration']);
                    if (isset($cVal['anim_delay'])) $data['canvas_meta'][$cKey]['anim_delay'] = intval($cVal['anim_delay']);
                    if (isset($cVal['anim_disappearance'])) $data['canvas_meta'][$cKey]['anim_disappearance'] = sanitize_text_field($cVal['anim_disappearance']);
                }
            }
        }
if (!$data || !isset($data['meta'])) {
            wp_send_json_error(['message' => 'Invalid JSON definition.']);
        }

        global $wpdb;
        $wpdb->insert($wpdb->prefix . 'wppoppop_items', [
            'uid'    => $this->generate_unique_uid(sanitize_text_field($data['meta']['title'])),
            'title'  => sanitize_text_field($data['meta']['title']) . ' (Imported)',
            'data'   => $json_raw,
            'status' => 'publish'
        ]);
        wp_send_json_success(['message' => 'Popup imported successfully!']);
    }

    public function save_campaign() {
        $this->verify_security(); /* WPPOPPOP_AB_VALIDATED */
        global $wpdb;

        $title = isset($_POST['title']) ? sanitize_text_field(wp_unslash($_POST['title'])) : '';
        $raw   = isset($_POST['popup_uids']) ? (array) wp_unslash($_POST['popup_uids']) : [];
        $uids  = array_values(array_unique(array_filter(array_map('sanitize_key', $raw))));

        if ($title === '') {
            wp_send_json_error(['message' => 'Please enter a campaign title.']);
        }
        if (count($uids) < 2) {
            wp_send_json_error(['message' => 'Select at least 2 popups for split-testing.']);
        }

        $ok = $wpdb->insert($wpdb->prefix . 'wppoppop_campaigns', [
            'uid'        => wp_generate_uuid4(),
            'title'      => $title,
            'popup_uids' => wp_json_encode($uids),
            'status'     => 'active'
        ]);
        if ($ok === false) {
            wp_send_json_error(['message' => 'Database error: could not save the campaign. Try Tools > Repair Tables.']);
        }
        wp_send_json_success(['message' => 'A/B Campaign saved!']);
    }

    public function delete_campaign() {
        $this->verify_security();
        global $wpdb;

        $uid = isset($_POST['uid']) ? sanitize_key(wp_unslash($_POST['uid'])) : '';
        if ($uid === '') {
            wp_send_json_error(['message' => 'Missing campaign UID.']);
        }
        $deleted = $wpdb->delete($wpdb->prefix . 'wppoppop_campaigns', ['uid' => $uid], ['%s']);
        if (!$deleted) {
            wp_send_json_error(['message' => 'Campaign not found or already deleted.']);
        }
        wp_send_json_success(['message' => 'Campaign deleted.']);
    }






    /**
     * Phase 2 Backend Whitelist Parity: opacity, hoverBgColor, customClass
     */
    public function sanitize_phase2_whitelist_properties(array $el, array &$clean = array()) {
        if (isset($el['opacity'])) {
            $clean['opacity'] = floatval($el['opacity']);
        } else {
            $clean['opacity'] = 1.0;
        }

        if (isset($el['hoverBgColor'])) {
            $clean['hoverBgColor'] = sanitize_text_field($el['hoverBgColor']);
            $clean['hoverBg'] = $clean['hoverBgColor'];
        } elseif (isset($el['hoverBg'])) {
            $clean['hoverBg'] = sanitize_text_field($el['hoverBg']);
            $clean['hoverBgColor'] = $clean['hoverBg'];
        }

        /* WPPOPPOP_CLOSE_WHITELIST */
        if (isset($el['hoverTextColor'])) { $clean['hoverTextColor'] = sanitize_text_field($el['hoverTextColor']); $clean['hoverColor'] = $clean['hoverTextColor']; }
        elseif (isset($el['hoverColor'])) { $clean['hoverColor'] = sanitize_text_field($el['hoverColor']); $clean['hoverTextColor'] = $clean['hoverColor']; }
        if (isset($el['hoverBorderColor'])) { $clean['hoverBorderColor'] = sanitize_text_field($el['hoverBorderColor']); $clean['hoverBorder'] = $clean['hoverBorderColor']; }
        elseif (isset($el['hoverBorder'])) { $clean['hoverBorder'] = sanitize_text_field($el['hoverBorder']); $clean['hoverBorderColor'] = $clean['hoverBorder']; }
        if (isset($el['closeGlyph'])) { $clean['closeGlyph'] = sanitize_text_field($el['closeGlyph']); }
        if (isset($el['iconSize'])) { $clean['iconSize'] = intval($el['iconSize']); }
        if (isset($el['sizePresetApplied'])) { $clean['sizePresetApplied'] = !empty($el['sizePresetApplied']); }

        if (isset($el['customClass'])) {
            $clean['customClass'] = sanitize_html_class($el['customClass']);
            $clean['custom_css_class'] = $clean['customClass'];
        } elseif (isset($el['custom_css_class'])) {
            $clean['custom_css_class'] = sanitize_html_class($el['custom_css_class']);
            $clean['customClass'] = $clean['custom_css_class'];
        }

        
        // Form Family Data & Advanced Tab Lifecycle Persistence
        if (isset($el['defaultValue'])) $clean['defaultValue'] = sanitize_text_field($el['defaultValue']);
        if (isset($el['dynamicDefault'])) $clean['dynamicDefault'] = !empty($el['dynamicDefault']);
        if (isset($el['paramName'])) $clean['paramName'] = sanitize_key($el['paramName']);
        if (isset($el['readOnly'])) $clean['readOnly'] = !empty($el['readOnly']);
        if (isset($el['saveToDb'])) $clean['saveToDb'] = !empty($el['saveToDb']);
        if (isset($el['fieldId'])) $clean['fieldId'] = sanitize_html_class($el['fieldId']);
        if (isset($el['inputMask'])) $clean['inputMask'] = sanitize_text_field($el['inputMask']);
        if (isset($el['customAttrs'])) $clean['customAttrs'] = sanitize_text_field($el['customAttrs']);
        if (isset($el['filters']) && is_array($el['filters'])) {
            $clean['filters'] = array_values(array_map('sanitize_key', $el['filters']));
        }
        if (isset($el['validators']) && is_array($el['validators'])) {
            $clean['validators'] = array_values(array_map('sanitize_key', $el['validators']));
        }

        
        // Phase 2: Media Family (Elements 1-9 & 26 Submit) Decomposed Properties Whitelist
        if (isset($el['shapeType'])) $clean['shapeType'] = sanitize_key($el['shapeType']);
        if (isset($el['rotate'])) $clean['rotate'] = intval($el['rotate']);
        if (isset($el['transparentFill'])) $clean['transparentFill'] = !empty($el['transparentFill']);
        if (isset($el['objectFit'])) $clean['objectFit'] = sanitize_key($el['objectFit']);
        if (isset($el['alt'])) $clean['alt'] = sanitize_text_field($el['alt']);
        if (isset($el['linkUrl'])) $clean['linkUrl'] = esc_url_raw($el['linkUrl']);
        if (isset($el['linkBlank'])) $clean['linkBlank'] = !empty($el['linkBlank']);
        if (isset($el['videoProvider'])) $clean['videoProvider'] = sanitize_key($el['videoProvider']);
        if (isset($el['aspectRatio'])) $clean['aspectRatio'] = sanitize_text_field($el['aspectRatio']);
        if (isset($el['videoUrl'])) $clean['videoUrl'] = esc_url_raw($el['videoUrl']);
        if (isset($el['videoAutoplay'])) $clean['videoAutoplay'] = !empty($el['videoAutoplay']);
        if (isset($el['videoMute'])) $clean['videoMute'] = !empty($el['videoMute']);
        if (isset($el['videoLoop'])) $clean['videoLoop'] = !empty($el['videoLoop']);
        if (isset($el['videoControls'])) $clean['videoControls'] = !empty($el['videoControls']);
        if (isset($el['tag'])) $clean['tag'] = sanitize_key($el['tag']);
        if (isset($el['textTransform'])) $clean['textTransform'] = sanitize_key($el['textTransform']);
        if (isset($el['overflow'])) $clean['overflow'] = sanitize_key($el['overflow']);
        if (isset($el['closeAction'])) $clean['closeAction'] = sanitize_key($el['closeAction']);
        if (isset($el['cookieDays'])) $clean['cookieDays'] = intval($el['cookieDays']);
        if (isset($el['iconClass'])) $clean['iconClass'] = sanitize_text_field($el['iconClass']);
        if (isset($el['iconSize'])) $clean['iconSize'] = intval($el['iconSize']);
        if (isset($el['progressMode'])) $clean['progressMode'] = in_array($el['progressMode'], array('manual','form','steps'), true) ? $el['progressMode'] : 'manual';
        if (isset($el['progressPercent'])) $clean['progressPercent'] = max(0, min(100, intval($el['progressPercent'])));
        if (isset($el['progressColor'])) $clean['progressColor'] = sanitize_hex_color($el['progressColor']);
        if (isset($el['progressStriped'])) $clean['progressStriped'] = !empty($el['progressStriped']);
        if (isset($el['progressAnimated'])) $clean['progressAnimated'] = !empty($el['progressAnimated']);
        if (isset($el['buttonAction'])) $clean['buttonAction'] = sanitize_key($el['buttonAction']);
        if (isset($el['submitAction'])) $clean['submitAction'] = sanitize_key($el['submitAction']);
        if (isset($el['loadingText'])) $clean['loadingText'] = sanitize_text_field($el['loadingText']);
        if (isset($el['redirectUrl'])) $clean['redirectUrl'] = esc_url_raw($el['redirectUrl']);

        
        // Phase 3: Form Family Core Inputs (Elements 10-17) Decomposed Properties Whitelist
        if (isset($el['autocomplete'])) $clean['autocomplete'] = sanitize_text_field($el['autocomplete']);
        if (isset($el['maxLength'])) $clean['maxLength'] = intval($el['maxLength']);
        if (isset($el['emailValidationMode'])) $clean['emailValidationMode'] = sanitize_key($el['emailValidationMode']);
        if (isset($el['min'])) $clean['min'] = floatval($el['min']);
        if (isset($el['max'])) $clean['max'] = floatval($el['max']);
        if (isset($el['step'])) $clean['step'] = floatval($el['step']);
        if (isset($el['phonePrefix'])) $clean['phonePrefix'] = sanitize_text_field($el['phonePrefix']);
        if (isset($el['rows'])) $clean['rows'] = max(1, min(30, intval($el['rows'])));
        if (isset($el['layout'])) $clean['layout'] = sanitize_key($el['layout']);
        if (isset($el['termsUrl'])) $clean['termsUrl'] = esc_url_raw($el['termsUrl']);
        if (isset($el['checkedDefault'])) $clean['checkedDefault'] = !empty($el['checkedDefault']);

        
        // Phase 4: Form Family Selectors & Pickers (Elements 18-25) Decomposed Properties Whitelist
        if (isset($el['maxSelect'])) $clean['maxSelect'] = max(1, intval($el['maxSelect']));
        if (isset($el['columns'])) $clean['columns'] = max(1, min(6, intval($el['columns'])));
        if (isset($el['multiSelect'])) $clean['multiSelect'] = !empty($el['multiSelect']);
        if (isset($el['activeColor'])) $clean['activeColor'] = sanitize_hex_color($el['activeColor']);
        if (isset($el['dateFormat'])) $clean['dateFormat'] = sanitize_text_field($el['dateFormat']);
        if (isset($el['minDate'])) $clean['minDate'] = sanitize_text_field($el['minDate']);
        if (isset($el['maxDate'])) $clean['maxDate'] = sanitize_text_field($el['maxDate']);
        if (isset($el['timeFormat'])) $clean['timeFormat'] = sanitize_key($el['timeFormat']);
        if (isset($el['timeStep'])) $clean['timeStep'] = max(1, intval($el['timeStep']));
        if (isset($el['allowedExtensions'])) $clean['allowedExtensions'] = sanitize_text_field($el['allowedExtensions']);
        if (isset($el['maxFileSize'])) $clean['maxFileSize'] = max(1, intval($el['maxFileSize']));
        if (isset($el['multiUpload'])) $clean['multiUpload'] = !empty($el['multiUpload']);
        if (isset($el['maxFiles'])) $clean['maxFiles'] = min(20, max(0, intval($el['maxFiles']))); /* WPPOPPOP_UPLOAD_LIMITS */
        if (isset($el['uploadErrorMessage']) && !is_array($el['uploadErrorMessage'])) $clean['uploadErrorMessage'] = sanitize_text_field(wp_unslash((string) $el['uploadErrorMessage']));
        if (isset($el['minLength'])) $clean['minLength'] = max(0, intval($el['minLength']));
        if (isset($el['showEyeToggle'])) $clean['showEyeToggle'] = !empty($el['showEyeToggle']);
        if (isset($el['ratingMax']))$clean['ratingMax'] = in_array(intval($el['ratingMax']), array(5, 10), true) ? intval($el['ratingMax']) : 5;
        if (isset($el['ratingShape'])) $clean['ratingShape'] = sanitize_key($el['ratingShape']);
        if (isset($el['ratingColor'])) $clean['ratingColor'] = sanitize_hex_color($el['ratingColor']);
        if (isset($el['ratingInactiveColor']) && !is_array($el['ratingInactiveColor'])) { $wppoppop_ric = sanitize_hex_color((string) $el['ratingInactiveColor']); if ($wppoppop_ric) { $clean['ratingInactiveColor'] = $wppoppop_ric; } } /* WPPOPPOP_RATING */
        foreach (array('iconLeftColor', 'iconRightColor', 'fgColor') as $wppoppop_ic) { if (isset($el[$wppoppop_ic]) && !is_array($el[$wppoppop_ic])) { $wppoppop_icv = sanitize_hex_color((string) $el[$wppoppop_ic]); if ($wppoppop_icv) { $clean[$wppoppop_ic] = $wppoppop_icv; } } } /* WPPOPPOP_ICON_COLOR */

        
        // === PHASE 4: v3.0.637 SANITIZED 5-TAB & 3-TAB LAYER SETTINGS WHITELIST ===
        $gp_text_keys = array(
            'autocomplete', 'animIn', 'animOut', 'iconLeft', 'iconRight',
            'customClass', 'cssClass', 'customId', 'logicAction', 'logicMatch',
            'defaultValue', 'paramName', 'fieldId', 'fieldName', 'token',
            'shapePreset', 'alt', 'objectFit', 'videoProvider',
            'aspectRatio', 'videoUrl', 'tag', 'textTransform', 'overflow',
            'closeSymbol', 'closeAction', 'closeGlyph', 'hoverBgColor', 'hoverTextColor', 'hoverBorderColor', 'hoverBg', 'hoverColor', 'hoverBorder', 'iconClass', 'progressMode',
            'buttonAction', 'url', 'loadingText', 'submitAction',
            'confirmMessage', 'redirectUrl', 'countryCode', 'checkboxLabel',
            'radioLayout', 'dateFormat', 'minDate', 'maxDate', 'timeFormat',
            'allowedExtensions', 'activeColor', 'ratingShape', 'ratingColor', 'requiredMessage', 'errorMessage', 'validatorEqual', 'validatorList', 'optionsList', 'firstOption', 'minValue', 'maxValue', 'stepValue', 'unitSuffix', 'uploadErrorMessage'
        );
        foreach ( $gp_text_keys as$gk ) {
            if ( isset( $el[$gk ] ) && ! is_array( $el[$gk ] ) ) {
                $clean[$gk ] = sanitize_text_field( wp_unslash( (string) $el[$gk ] ) );
            }
        }

        $gp_int_keys = array(
            'animDuration', 'animDelay', 'anim_duration', 'anim_delay', 'iconLeftOffset', 'iconRightOffset',
            'rotate', 'iconSize', 'cookieDays', 'progressValue',
            'maxLength', 'minLength', 'min', 'max', 'step', 'rows',
            'maxSelect', 'maxSelections', 'columns', 'gridCols', 'timeStep', 'maxFileSize', 'ratingMax'
        );
        foreach ( $gp_int_keys as$ik ) {
            if ( isset( $el[$ik ] ) && $el[$ik ] !== '' ) {
                $clean[$ik ] = intval( $el[$ik ] );
            }
        }

        $gp_bool_keys = array(
            'required', 'transparentFill', 'autoplay', 'muted', 'loop',
            'controls', 'showPercentLabel', 'striped', 'targetBlank',
            'nofollow', 'checkedDefault', 'multiSelect', 'multiple',
            'showEyeToggle', 'bgCustom', 'logicEnabled', 'dynamicDefault',
            'readOnly', 'saveToDb', 'blockDisposable'
        );
        foreach ( $gp_bool_keys as$bk ) {
            if ( isset( $el[$bk ] ) ) {
                $clean[$bk ] = ! empty( $el[$bk ] ) && $el[$bk ] !== 'false';
            }
        }

        if ( isset( $el['options'] ) && ! is_array( $el['options'] ) ) {
            $clean['options'] = sanitize_textarea_field( wp_unslash( (string)$el['options'] ) );
        }

        if ( isset( $el['inlineStyles'] ) && is_array( $el['inlineStyles'] ) ) {$clean['inlineStyles'] = array();
            foreach ( $el['inlineStyles'] as$st ) {
                if ( is_array( $st ) ) {$clean['inlineStyles'][] = array(
                        'prop'  => isset( $st['prop'] ) ? sanitize_text_field( $st['prop'] ) : '',
                        'value' => isset( $st['value'] ) ? sanitize_text_field( $st['value'] ) : '',
                    );
                }
            }
        }

        if ( isset( $el['logicRules'] ) && is_array( $el['logicRules'] ) ) {$clean['logicRules'] = array();
            foreach ( $el['logicRules'] as$rl ) {
                if ( is_array( $rl ) ) {$clean['logicRules'][] = array(
                        'field'    => isset( $rl['field'] ) ? sanitize_text_field( $rl['field'] ) : '',
                        'operator' => isset( $rl['operator'] ) ? sanitize_text_field( $rl['operator'] ) : 'is',
                        'value'    => isset( $rl['value'] ) ? sanitize_text_field( $rl['value'] ) : '',
                    );
                }
            }
        }

        foreach ( array( 'filters', 'validators' ) as $arr_k ) {
            if ( isset( $el[$arr_k ] ) && is_array( $el[$arr_k ] ) ) {
                $clean[$arr_k ] = array_map( 'sanitize_text_field', $el[$arr_k ] );
            }
        }

        
        if ( class_exists( 'WpPopPop_Ajax_Element_Registry' ) && method_exists( 'WpPopPop_Ajax_Element_Registry', 'sanitize_decomposed_layer_props' ) ) {
            $clean = WpPopPop_Ajax_Element_Registry::sanitize_decomposed_layer_props( $el,$clean );
        }

        return $clean;
    }

}
