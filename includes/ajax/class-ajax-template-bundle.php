<?php
if (!defined('ABSPATH')) {
    exit;
}

/** Library > zip export (template.json + images) and drag & drop / file import (.zip or .json). Admin only. */
class WpPopPop_Ajax_Template_Bundle {
    public function __construct() {
        add_action('wp_ajax_wppoppop_template_export_zip', array($this, 'export_zip'));
        add_action('wp_ajax_wppoppop_template_import_file', array($this, 'import_file'));
        add_action('wp_ajax_wppoppop_template_preview_file', array($this, 'preview_file'));
    }

    private function guard() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Unauthorized action.', 'wppoppop')), 403);
        }
        $nonce = isset($_REQUEST['nonce']) ? sanitize_text_field(wp_unslash($_REQUEST['nonce'])) : '';
        if (!wp_verify_nonce($nonce, 'wppoppop_admin_nonce') && !wp_verify_nonce($nonce, 'wppoppop_builder_nonce')) {
            wp_send_json_error(array('message' => __('Security check failed. Please refresh the page.', 'wppoppop')), 403);
        }
    }

    private function find($id) {
        if ($id === '') {
            return null;
        }
        $all = get_option(WpPopPop_Ajax_Templates::OPT, array());
        if (is_array($all) && isset($all[$id]) && !empty($all[$id]['data'])) {
            return $all[$id];
        }
        if (strpos($id, 'preset-') === 0) {
            $presets = WpPopPop_Library_Presets::all();
            if (isset($presets[$id]) && !empty($presets[$id]['data'])) {
                return $presets[$id];
            }
        }
        return null;
    }

    public function export_zip() {
        $this->guard();
        if (!WpPopPop_Template_Bundle::zip_available()) {
            wp_send_json_error(array('code' => 'no_zip', 'message' => __('The PHP Zip extension is not available on this server.', 'wppoppop')));
        }
        $id = isset($_POST['id']) ? sanitize_key(wp_unslash($_POST['id'])) : '';
        $t  = $this->find($id);
        if (!$t) {
            wp_send_json_error(array('message' => __('Template not found.', 'wppoppop')));
        }
        if (!empty($t['no_download'])) {
            wp_send_json_error(array('message' => __('Download is disabled for this template.', 'wppoppop')), 403);
        }
        $data = json_decode((string) $t['data']);
        if (!is_object($data)) {
            wp_send_json_error(array('message' => __('Template data is damaged.', 'wppoppop')));
        }
        $meta = array(
            'title'       => isset($t['title']) ? (string) $t['title'] : '',
            'description' => isset($t['description']) ? (string) $t['description'] : '',
            'category'    => (isset($t['category']) && $t['category'] !== '') ? (string) $t['category'] : 'custom',
            'image'       => isset($t['image']) ? (string) $t['image'] : '',
            'download'    => false, // exported template.json hides the Download button by default when placed in library-presets/
        );
        $b    = WpPopPop_Template_Bundle::bundle($meta, $data);
        $slug = substr(sanitize_title($meta['title']), 0, 60);
        if ($slug === '') {
            $slug = 'template';
        }
        $tmp = WpPopPop_Template_Bundle::build_zip($slug, $b['meta'], $b['data'], $b['files']);
        if (is_wp_error($tmp)) {
            wp_send_json_error(array('code' => $tmp->get_error_code(), 'message' => $tmp->get_error_message()));
        }

        while (ob_get_level() > 0) {
            /* phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- no output buffer may be active */ @ob_end_clean();
        }
        nocache_headers();
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $slug . '-wppoppop-template.zip"');
        header('Content-Length: ' . filesize($tmp));
        header('X-Wppoppop-Missing: ' . (int) $b['missing']);
        header('X-Wppoppop-Images: ' . count($b['files']));
        /* phpcs:ignore WordPress.WP.AlternativeFunctions -- reads a local plugin or upload temp file, not a remote URL */ readfile($tmp);
        wp_delete_file($tmp);
        exit;
    }

    public function preview_file() {
        $this->guard();
        if (empty($_FILES['file']) || !is_array($_FILES['file'])) {
            wp_send_json_error(array('message' => __('No file received.', 'wppoppop')));
        }
        $f = $_FILES['file']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
        if (!isset($f['error'], $f['tmp_name'], $f['name']) || (int) $f['error'] !== 0) {
            wp_send_json_error(array('message' => __('The upload failed or the file is larger than the server allows.', 'wppoppop')));
        }
        if (!is_uploaded_file($f['tmp_name']) || filesize($f['tmp_name']) > WpPopPop_Template_Bundle::MAX_ZIP) {
            wp_send_json_error(array('message' => __('The file is invalid or too large (max 60 MB).', 'wppoppop')));
        }
        $res = WpPopPop_Template_Preview::build($f['tmp_name'], sanitize_file_name(wp_unslash($f['name'])));
        if (is_wp_error($res)) {
            wp_send_json_error(array('message' => $res->get_error_message()));
        }
        wp_send_json_success($res);
    }

    public function import_file() {
        $this->guard();
        if (empty($_FILES['file']) || !is_array($_FILES['file'])) {
            wp_send_json_error(array('message' => __('No file received.', 'wppoppop')));
        }
        $f = $_FILES['file']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
        if (!isset($f['error'], $f['tmp_name'], $f['name']) || (int) $f['error'] !== 0) {
            wp_send_json_error(array('message' => __('The upload failed or the file is larger than the server allows.', 'wppoppop')));
        }
        if (!is_uploaded_file($f['tmp_name']) || filesize($f['tmp_name']) > WpPopPop_Template_Bundle::MAX_ZIP) {
            wp_send_json_error(array('message' => __('The file is invalid or too large (max 60 MB).', 'wppoppop')));
        }
        $res = WpPopPop_Template_Bundle::import($f['tmp_name'], sanitize_file_name(wp_unslash($f['name'])));
        if (is_wp_error($res)) {
            wp_send_json_error(array('message' => $res->get_error_message()));
        }
        $msg = sprintf(
            /* translators: 1: template title, 2: number of images */
            _n('Imported "%1$s" with %2$d image.', 'Imported "%1$s" with %2$d images.', (int) $res['images'], 'wppoppop'),
            $res['title'],
            (int) $res['images']
        );
        if (!empty($res['skipped'])) {
            $msg .= ' ' . sprintf(/* translators: %d: number of skipped images. */ _n('%d image was skipped (invalid or too large).', '%d images were skipped (invalid or too large).', (int) $res['skipped'], 'wppoppop'), (int) $res['skipped']);
        }
        $res['message'] = $msg;
        wp_send_json_success($res);
    }
}
