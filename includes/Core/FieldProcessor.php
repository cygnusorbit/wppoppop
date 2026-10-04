<?php
namespace WPPopPop\Core;

use WPPopPop\Admin\SettingsManager;

class FieldProcessor {
    public static function init(): void {
        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_field_assets']);
        add_filter('wppoppop_process_lead_fields', [__CLASS__, 'process_submission_fields'], 10, 2);
    }

    public static function enqueue_field_assets(): void {
        wp_enqueue_style(
            'wppoppop-interactive-fields',
            WPPOPPOP_URL . 'assets/css/form-interactive.css',
            [],
            WPPOPPOP_VERSION
        );

        wp_enqueue_script(
            'wppoppop-interactive-fields',
            WPPOPPOP_URL . 'assets/js/form-interactive.js',
            [],
            WPPOPPOP_VERSION,
            true
        );
    }

    public static function process_submission_fields(array $fields, int $lead_id): array {
        // Handle Base64 Signature Image Storage
        if (!empty($fields['signature']) && strpos($fields['signature'], 'data:image/png;base64,') === 0) {
            $upload_dir = wp_upload_dir();
            $target_dir = $upload_dir['basedir'] . '/wppoppop/signatures';
            wp_mkdir_p($target_dir);

            $img_data = base64_decode(str_replace('data:image/png;base64,', '', $fields['signature']));
            $filename = 'sig-' . $lead_id . '-' . wp_hash($fields['signature'] . time()) . '.png';
            $file_path = $target_dir . '/' . $filename;

            if (file_put_contents($file_path, $img_data)) {
                $fields['signature_url'] = $upload_dir['baseurl'] . '/wppoppop/signatures/' . $filename;
                unset($fields['signature']); // Free database payload space
                update_post_meta($lead_id, '_wppoppop_signature_path', $file_path);
            }
        }

        return $fields;
    }

    public static function purge_expired_uploads(): void {
        $retention = SettingsManager::get('user_uploads', 'keep');
        if ($retention !== 'delete_30') {
            return;
        }

        $upload_dir = wp_upload_dir();
        $target_dir = $upload_dir['basedir'] . '/wppoppop/signatures';
        if (!is_dir($target_dir)) {
            return;
        }

        $threshold = time() - (30 * 86400);
        $files = glob($target_dir . '/*');
        foreach ($files as $file) {
            if (is_file($file) && filemtime($file) < $threshold) {
                unlink($file);
            }
        }
    }
}
