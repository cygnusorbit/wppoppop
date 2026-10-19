<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Dashboard (Popups list) AJAX: Quick Edit + Active toggle.
 * Whitelist: uid (sanitize_key), title (text), status (publish|draft only).
 */
class WpPopPop_Ajax_Dashboard {
    public function __construct() {
        add_action('wp_ajax_wppoppop_quick_edit', [$this, 'quick_edit']);
        add_action('wp_ajax_wppoppop_toggle_status', [$this, 'toggle_status']);
        add_action('wp_ajax_wppoppop_export_selected', [$this, 'export_selected']);
    }

    private function verify_nonce() {
        $nonce = isset($_REQUEST['nonce']) ? sanitize_text_field(wp_unslash($_REQUEST['nonce'])) : '';
        if (!wp_verify_nonce($nonce, 'wppoppop_admin_nonce') && !wp_verify_nonce($nonce, 'wppoppop_builder_nonce')) {
            wp_send_json_error(['message' => __('Security check failed. Please refresh the page.', 'wppoppop')], 403);
        }
    }

    private function can($cap, $uid = '') {
        return current_user_can('manage_options') || ($uid !== '' ? current_user_can($cap, $uid) : current_user_can($cap));
    }

    private function counts() {
        global $wpdb;
        $rows = $wpdb->get_col("SELECT status FROM {$wpdb->prefix}wppoppop_items");
        $c = ['all' => 0, 'active' => 0, 'inactive' => 0, 'trash' => 0];
        foreach ((array) $rows as $st) {
            if ($st === 'trash') { $c['trash']++; continue; }
            $c['all']++;
            if ($st === 'publish' || $st === 'published') { $c['active']++; } else { $c['inactive']++; }
        }
        return $c;
    }

    private function load_row($uid) {
        global $wpdb;
        return $wpdb->get_row($wpdb->prepare("SELECT uid, title, status, data FROM {$wpdb->prefix}wppoppop_items WHERE uid = %s", $uid), ARRAY_A);
    }

    public function quick_edit() {
        $this->verify_nonce();
        global $wpdb;

        $uid    = isset($_POST['uid']) ? sanitize_key(wp_unslash($_POST['uid'])) : '';
        $title  = isset($_POST['title']) ? trim(sanitize_text_field(wp_unslash($_POST['title']))) : '';
        $status = isset($_POST['status']) ? sanitize_key(wp_unslash($_POST['status'])) : '';

        if ($uid === '' || !$this->can('edit_wppoppop_campaign', $uid)) {
            wp_send_json_error(['message' => __('You do not have permission to edit this campaign.', 'wppoppop')], 403);
        }
        if ($title === '') {
            wp_send_json_error(['message' => __('Title cannot be empty.', 'wppoppop')]);
        }
        if (!in_array($status, ['publish', 'draft'], true)) {
            wp_send_json_error(['message' => __('Invalid status.', 'wppoppop')]);
        }

        $row = $this->load_row($uid);
        if (!$row || $row['status'] === 'trash') {
            wp_send_json_error(['message' => __('Campaign not found.', 'wppoppop')]);
        }

        $current_on = in_array($row['status'], ['publish', 'published'], true);
        $new_on     = ($status === 'publish');
        if ($current_on !== $new_on && !$this->can('publish_wppoppop_campaigns')) {
            wp_send_json_error(['message' => __('You do not have permission to change campaign status.', 'wppoppop')], 403);
        }

        $update = ['title' => $title, 'status' => $status, 'updated_at' => current_time('mysql')];
        $fmt    = ['%s', '%s', '%s'];

        // Keep builder meta title in sync (if present).
        $decoded = json_decode((string) $row['data'], true);
        if (is_array($decoded) && isset($decoded['meta']) && is_array($decoded['meta']) && array_key_exists('title', $decoded['meta'])) {
            $decoded['meta']['title'] = $title;
            $enc = wp_json_encode($decoded);
            if (is_string($enc) && $enc !== '') {
                $update['data'] = $enc;
                $fmt[] = '%s';
            }
        }

        $ok = $wpdb->update($wpdb->prefix . 'wppoppop_items', $update, ['uid' => $uid], $fmt, ['%s']);
        if ($ok === false) {
            wp_send_json_error(['message' => __('Database error while saving.', 'wppoppop')]);
        }

        wp_send_json_success([
            'uid'    => $uid,
            'title'  => $title,
            'status' => $status,
            'counts' => $this->counts(),
        ]);
    }

    public function toggle_status() {
        $this->verify_nonce();
        global $wpdb;

        $uid    = isset($_POST['uid']) ? sanitize_key(wp_unslash($_POST['uid'])) : '';
        $active = isset($_POST['active']) && (string) wp_unslash($_POST['active']) !== '0' && $_POST['active'] !== 'false' && $_POST['active'] !== '';

        if ($uid === '' || !$this->can('publish_wppoppop_campaigns', $uid)) {
            wp_send_json_error(['message' => __('You do not have permission to change campaign status.', 'wppoppop')], 403);
        }
        $row = $this->load_row($uid);
        if (!$row || $row['status'] === 'trash') {
            wp_send_json_error(['message' => __('Campaign not found.', 'wppoppop')]);
        }

        $status = $active ? 'publish' : 'draft';
        $ok = $wpdb->update($wpdb->prefix . 'wppoppop_items', ['status' => $status, 'updated_at' => current_time('mysql')], ['uid' => $uid], ['%s', '%s'], ['%s']);
        if ($ok === false) {
            wp_send_json_error(['message' => __('Database error while saving.', 'wppoppop')]);
        }

        wp_send_json_success(['uid' => $uid, 'status' => $status, 'counts' => $this->counts()]);
    }

    public function export_selected() {
        $this->verify_nonce();
        if (!$this->can('edit_wppoppop_campaigns')) {
            wp_send_json_error(['message' => __('You do not have permission to export campaigns.', 'wppoppop')], 403);
        }
        global $wpdb;
        $raw  = isset($_POST['uids']) ? (array) wp_unslash($_POST['uids']) : [];
        $uids = array_values(array_unique(array_filter(array_map('sanitize_key', $raw))));
        if (empty($uids)) {
            wp_send_json_error(['message' => __('No campaigns selected.', 'wppoppop')]);
        }
        $ph    = implode(',', array_fill(0, count($uids), '%s'));
        $items = $wpdb->get_results($wpdb->prepare("SELECT uid, title, data, status FROM {$wpdb->prefix}wppoppop_items WHERE uid IN ($ph) AND status != 'trash'", $uids), ARRAY_A);
        if (empty($items)) {
            wp_send_json_error(['message' => __('Selected campaigns were not found.', 'wppoppop')]);
        }
        wp_send_json_success([
            'filename' => 'wppoppop-selected-' . gmdate('Y-m-d') . '.json',
            'payload'  => [
                'generator' => 'WpPopPop ' . (defined('WPPOPPOP_VERSION') ? WPPOPPOP_VERSION : '1.0.0'),
                'exported'  => current_time('mysql'),
                'items'     => $items,
                'campaigns' => [],
            ],
        ]);
    }
}
