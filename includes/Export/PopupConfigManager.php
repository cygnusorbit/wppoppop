<?php
namespace WPPopPop\Export;

use WPPopPop\Targeting\PopupPostType;

class PopupConfigManager {
    public function init(): void {
        add_action('admin_post_wppoppop_export_config', [$this, 'handle_export_config']);
        add_action('admin_post_wppoppop_import_config', [$this, 'handle_import_config']);
        add_filter('post_row_actions', [$this, 'add_export_row_action'], 10, 2);
        add_action('manage_posts_extra_tablenav', [$this, 'render_import_button']);
    }

    public function add_export_row_action(array $actions, \WP_Post $post): array {
        if ($post->post_type !== PopupPostType::POST_TYPE) {
            return $actions;
        }

        $export_url = wp_nonce_url(
            admin_url('admin-post.php?action=wppoppop_export_config&popup_id=' . $post->ID),
            'wppoppop_export_config_nonce'
        );

        $actions['export_json'] = '<a href="' . esc_url($export_url) . '">' . esc_html__('Export JSON', 'wppoppop') . '</a>';
        return $actions;
    }

    public function render_import_button(string $which): void {
        global $typenow;
        if ($typenow !== PopupPostType::POST_TYPE || $which !== 'top') {
            return;
        }
        ?>
        <div class="alignleft actions" style="display:inline-flex; align-items:center; gap:6px;">
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" enctype="multipart/form-data" style="display:inline-flex; align-items:center; gap:6px;">
                <input type="hidden" name="action" value="wppoppop_import_config">
                <?php wp_nonce_field('wppoppop_import_config_nonce', '_wpnonce'); ?>
                <label class="button" style="cursor:pointer;">
                    <?php esc_html_e('Import Popup JSON', 'wppoppop'); ?>
                    <input type="file" name="import_file" accept=".json" required style="display:none;" onchange="this.form.submit();">
                </label>
            </form>
        </div>
        <?php
    }

    public function handle_export_config(): void {
        if (!current_user_can('manage_options')) {
            wp_die(__('Unauthorized user capability.', 'wppoppop'));
        }

        check_admin_referer('wppoppop_export_config_nonce');

        $popup_id = absint($_GET['popup_id'] ?? 0);
        $popup = get_post($popup_id);

        if (!$popup || $popup->post_type !== PopupPostType::POST_TYPE) {
            wp_die(__('Invalid popup requested for export.', 'wppoppop'));
        }

        $meta = get_post_meta($popup_id);
        $clean_meta = [];
        foreach ($meta as $k => $vals) {
            if (str_starts_with($k, '_wppoppop_')) {
                $clean_meta[$k] = maybe_unserialize($vals[0]);
            }
        }

        $export_payload = [
            'generator'   => 'WPPopPop v1.0.0',
            'exported_at' => current_time('mysql'),
            'title'       => $popup->post_title,
            'content'     => $popup->post_content,
            'meta'        => $clean_meta,
        ];

        $filename = 'wppoppop-config-' . sanitize_title($popup->post_title) . '-' . gmdate('Y-m-d') . '.json';

        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo wp_json_encode($export_payload, JSON_PRETTY_PRINT);
        exit;
    }

    public function handle_import_config(): void {
        if (!current_user_can('manage_options')) {
            wp_die(__('Unauthorized user capability.', 'wppoppop'));
        }

        check_admin_referer('wppoppop_import_config_nonce');

        if (empty($_FILES['import_file']['tmp_name'])) {
            wp_die(__('No import file uploaded.', 'wppoppop'));
        }

        $file_content = file_get_contents($_FILES['import_file']['tmp_name']);
        $data = json_decode($file_content, true);

        if (!$data || empty($data['title']) || !isset($data['meta'])) {
            wp_die(__('Invalid or corrupted JSON popup configuration file.', 'wppoppop'));
        }

        $new_post_id = wp_insert_post([
            'post_type'    => PopupPostType::POST_TYPE,
            'post_title'   => sanitize_text_field($data['title']) . ' (Imported)',
            'post_content' => wp_kses_post($data['content'] ?? ''),
            'post_status'  => 'draft',
        ]);

        if (is_wp_error($new_post_id)) {
            wp_die($new_post_id->get_error_message());
        }

        foreach ($data['meta'] as $meta_key => $meta_val) {
            if (str_starts_with($meta_key, '_wppoppop_')) {
                // Reset impression and submission statistics on freshly imported popups
                if (in_array($meta_key, ['_wppoppop_impressions', '_wppoppop_submissions'], true)) {
                    update_post_meta($new_post_id, $meta_key, 0);
                } else {
                    update_post_meta($new_post_id, $meta_key, $meta_val);
                }
            }
        }

        wp_safe_redirect(admin_url('post.php?action=edit&post=' . $new_post_id));
        exit;
    }
}
