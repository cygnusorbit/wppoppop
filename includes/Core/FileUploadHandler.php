<?php
namespace WPPopPop\Core;

class FileUploadHandler {
    public static function handle_upload(array $file, int $max_mb = 10): array {
        if (empty($file['name']) || empty($file['tmp_name'])) {
            return ['success' => false, 'message' => 'No file uploaded.'];
        }

        $max_bytes = $max_mb * 1024 * 1024;
        if ($file['size'] > $max_bytes) {
            return [
                'success' => false,
                'message' => sprintf(__('File size exceeds the allowed limit of %dMB.', 'wppoppop'), $max_mb),
            ];
        }

        $allowed_mimes = [
            'pdf'  => 'application/pdf',
            'zip'  => 'application/zip',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'txt'  => 'text/plain',
        ];

        if (!function_exists('wp_handle_upload')) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }

        $overrides = [
            'test_form' => false,
            'mimes'     => $allowed_mimes,
        ];

        $movefile = wp_handle_upload($file, $overrides);

        if ($movefile && !isset($movefile['error'])) {
            return [
                'success' => true,
                'url'     => $movefile['url'],
                'file'    => $movefile['file'],
                'type'    => $movefile['type'],
            ];
        }

        return [
            'success' => false,
            'message' => $movefile['error'] ?? __('File upload could not be completed.', 'wppoppop'),
        ];
    }
}
