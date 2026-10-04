<?php
namespace WPPopPop\Admin;

use WPPopPop\Targeting\PopupPostType;
use WPPopPop\Core\LayerRenderer;

class PopupManager {
    public static function init(): void {
        add_action('wp_ajax_wppoppop_duplicate_popup', [__CLASS__, 'ajax_duplicate_popup']);
        add_action('wp_ajax_wppoppop_toggle_status', [__CLASS__, 'ajax_toggle_status']);
        add_action('wp_ajax_wppoppop_delete_popup', [__CLASS__, 'ajax_delete_popup']);
        add_action('wp_ajax_wppoppop_get_preview', [__CLASS__, 'ajax_get_preview']);
        add_action('wp_ajax_wppoppop_import_popup', [__CLASS__, 'ajax_import_popup']);
        add_action('admin_post_wppoppop_export_popup_json', [__CLASS__, 'handle_export_json']);
    }

    public static function ajax_duplicate_popup(): void {
        check_ajax_referer('wppoppop_admin_nonce', 'nonce');
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(['message' => __('Permission denied.', 'wppoppop')]);
        }

        $popup_id = absint($_POST['popup_id'] ?? 0);
        $source = get_post($popup_id);
        if (!$source || $source->post_type !== PopupPostType::POST_TYPE) {
            wp_send_json_error(['message' => __('Popup not found.', 'wppoppop')]);
        }

        $new_id = wp_insert_post([
            'post_title'   => $source->post_title . ' (Copy)',
            'post_content' => $source->post_content,
            'post_status'  => 'draft',
            'post_type'    => PopupPostType::POST_TYPE,
        ]);

        if (is_wp_error($new_id)) {
            wp_send_json_error(['message' => __('Failed to clone popup.', 'wppoppop')]);
        }

        // Clone all meta fields
        $meta = get_post_meta($popup_id);
        foreach ($meta as $key => $values) {
            if (strpos($key, '_wppoppop_') === 0 && !empty($values)) {
                update_post_meta($new_id, $key, maybe_unserialize($values[0]));
            }
        }

        // Reset metrics for clone
        update_post_meta($new_id, '_wppoppop_slug', sanitize_title($source->post_title) . '-copy');
        update_post_meta($new_id, '_wppoppop_impressions', 0);
        update_post_meta($new_id, '_wppoppop_submissions', 0);
        update_post_meta($new_id, '_wppoppop_submissions_count', 0);

        wp_send_json_success([
            'new_id'  => $new_id,
            'message' => __('Popup duplicated successfully as draft.', 'wppoppop'),
        ]);
    }

    public static function ajax_toggle_status(): void {
        check_ajax_referer('wppoppop_admin_nonce', 'nonce');
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(['message' => __('Permission denied.', 'wppoppop')]);
        }

        $popup_id = absint($_POST['popup_id'] ?? 0);
        $post = get_post($popup_id);
        if (!$post || $post->post_type !== PopupPostType::POST_TYPE) {
            wp_send_json_error(['message' => __('Popup not found.', 'wppoppop')]);
        }

        $new_status = ($post->post_status === 'publish') ? 'draft' : 'publish';
        wp_update_post([
            'ID'          => $popup_id,
            'post_status' => $new_status,
        ]);

        wp_send_json_success([
            'status'      => ($new_status === 'publish') ? 'ACTIVE' : 'INACTIVE',
            'badge_class' => ($new_status === 'publish') ? 'wppoppop-badge-active' : 'wppoppop-badge-inactive',
        ]);
    }

    public static function ajax_delete_popup(): void {
        check_ajax_referer('wppoppop_admin_nonce', 'nonce');
        if (!current_user_can('delete_posts')) {
            wp_send_json_error(['message' => __('Permission denied.', 'wppoppop')]);
        }

        $popup_id = absint($_POST['popup_id'] ?? 0);
        if ($popup_id > 0 && wp_trash_post($popup_id)) {
            wp_send_json_success(['message' => __('Popup moved to trash.', 'wppoppop')]);
        }

        wp_send_json_error(['message' => __('Failed to delete popup.', 'wppoppop')]);
    }

    public static function ajax_get_preview(): void {
        check_ajax_referer('wppoppop_admin_nonce', 'nonce');
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(['message' => __('Permission denied.', 'wppoppop')]);
        }

        $popup_id = absint($_GET['popup_id'] ?? 0);
        $post = get_post($popup_id);
        if (!$post || $post->post_type !== PopupPostType::POST_TYPE) {
            wp_send_json_error(['message' => __('Popup not found.', 'wppoppop')]);
        }

        $html = LayerRenderer::render_layers($popup_id, apply_filters('the_content', $post->post_content));
        wp_send_json_success([
            'title' => esc_html($post->post_title),
            'html'  => $html,
        ]);
    }

    public static function handle_export_json(): void {
        if (!current_user_can('edit_posts')) {
            wp_die(__('Permission denied.', 'wppoppop'));
        }

        check_admin_referer('wppoppop_export_popup');
        $popup_id = absint($_GET['popup_id'] ?? 0);
        $post = get_post($popup_id);

        if (!$post || $post->post_type !== PopupPostType::POST_TYPE) {
            wp_die(__('Popup not found.', 'wppoppop'));
        }

        $meta = get_post_meta($popup_id);
        $export_meta = [];
        foreach ($meta as $k => $v) {
            if (strpos($k, '_wppoppop_') === 0 && !in_array($k, ['_wppoppop_impressions', '_wppoppop_submissions', '_wppoppop_submissions_count'])) {
                $export_meta[$k] = maybe_unserialize($v[0]);
            }
        }

        $payload = [
            'generator'    => 'WPPopPop/' . WPPOPPOP_VERSION,
            'title'        => $post->post_title,
            'post_content' => $post->post_content,
            'meta'         => $export_meta,
            'exported_at'  => gmdate('Y-m-d H:i:s'),
        ];

        $filename = 'wppoppop-' . sanitize_title($post->post_title) . '-' . $popup_id . '.json';
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo wp_json_encode($payload, JSON_PRETTY_PRINT);
        exit;
    }

    public static function ajax_import_popup(): void {
        check_ajax_referer('wppoppop_admin_nonce', 'nonce');
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(['message' => __('Permission denied.', 'wppoppop')]);
        }

        if (empty($_FILES['popup_file']['tmp_name'])) {
            wp_send_json_error(['message' => __('Please upload a valid JSON file.', 'wppoppop')]);
        }

        $json_content = file_get_contents($_FILES['popup_file']['tmp_name']);
        $data = json_decode($json_content, true);

        if (!is_array($data) || empty($data['title'])) {
            wp_send_json_error(['message' => __('Invalid popup configuration JSON format.', 'wppoppop')]);
        }

        $new_id = wp_insert_post([
            'post_title'   => sanitize_text_field($data['title']) . ' (Imported)',
            'post_content' => wp_kses_post($data['post_content'] ?? ''),
            'post_status'  => 'draft',
            'post_type'    => PopupPostType::POST_TYPE,
        ]);

        if (is_wp_error($new_id)) {
            wp_send_json_error(['message' => __('Failed to import popup.', 'wppoppop')]);
        }

        if (isset($data['meta']) && is_array($data['meta'])) {
            foreach ($data['meta'] as $mk => $mv) {
                update_post_meta($new_id, sanitize_key($mk), $mv);
            }
        }

        update_post_meta($new_id, '_wppoppop_slug', 'imported-' . gmdate('YmdHis'));
        update_post_meta($new_id, '_wppoppop_impressions', 0);
        update_post_meta($new_id, '_wppoppop_submissions', 0);
        update_post_meta($new_id, '_wppoppop_submissions_count', 0);

        wp_send_json_success([
            'redirect' => admin_url('admin.php?page=wppoppop-add&id=' . $new_id),
            'message'  => __('Popup imported! Opening visual editor...', 'wppoppop'),
        ]);
    }
}
