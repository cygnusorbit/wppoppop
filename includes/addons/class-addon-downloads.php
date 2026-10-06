<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Addon_Downloads {
    public function __construct() {
        add_action('template_redirect', [$this, 'handle_secure_download']);
    }

    public function generate_download_token($file_url, $popup_uid = '') {
        $token = wp_generate_password(32, false);
        set_transient('wppoppop_dl_' . $token, [
            'file_url'  => esc_url_raw($file_url),
            'popup_uid' => sanitize_key($popup_uid),
            'created'   => time()
        ], HOUR_IN_SECONDS * 24);
        return $token;
    }

    public function handle_secure_download() {
        if (!isset($_GET['wppoppop_download'])) {
            return;
        }

        $token = sanitize_key($_GET['wppoppop_download']);
        $transient_key = 'wppoppop_dl_' . $token;
        $data = get_transient($transient_key);

        if (!$data || empty($data['file_url'])) {
            wp_die('Download link has expired or is invalid.', 'Invalid Download', ['response' => 403]);
        }

        // Delete transient to enforce single-use protection
        delete_transient($transient_key);

        $file_url = $data['file_url'];
        $upload_dir = wp_upload_dir();
        
        // Convert URL to absolute server path if local
        if (strpos($file_url, $upload_dir['baseurl']) !== false) {
            $file_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $file_url);
            if (file_exists($file_path)) {
                $filename = basename($file_path);
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($file_path));
                readfile($file_path);
                exit;
            }
        }

        // External file fallback
        wp_redirect($file_url);
        exit;
    }
}
