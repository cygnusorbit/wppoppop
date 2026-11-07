<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Module lifecycle endpoints: install, uninstall, delete, upload (module.zip), usage.
 * Every action requires manage_options and a valid nonce; file operations are further limited by the installer.
 */
class WpPopPop_Ajax_Modules {

    public function __construct() {
        foreach (['install', 'uninstall', 'delete', 'upload', 'usage', 'cats_save', 'set_category'] as $a) {
            add_action('wp_ajax_wppoppop_module_' . $a, [$this, $a]);
        }
    }

    protected function verify() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Unauthorized action.', 'wppoppop')], 403);
        }
        $nonce = isset($_REQUEST['nonce']) ? sanitize_text_field(wp_unslash($_REQUEST['nonce'])) : '';
        foreach (['wppoppop_admin_nonce', 'wppoppop_settings_nonce', 'wppoppop_builder_nonce'] as $action) {
            if (wp_verify_nonce($nonce, $action)) {
                return;
            }
        }
        wp_send_json_error(['message' => __('Security check failed. Please refresh the page.', 'wppoppop')], 403);
    }

    protected function slug() {
        $s = isset($_POST['slug']) ? sanitize_key(wp_unslash($_POST['slug'])) : '';
        return WpPopPop_Modules::slug_valid($s) ? $s : '';
    }

    protected function flag($key) {
        return !empty($_POST[$key]) && $_POST[$key] !== 'false' && $_POST[$key] !== '0';
    }

    protected function respond($res) {
        if (is_wp_error($res)) {
            wp_send_json_error([
                'code'    => $res->get_error_code(),
                'message' => $res->get_error_message(),
                'data'    => $res->get_error_data(),
            ]);
        }
        wp_send_json_success($res);
    }

    public function install() {
        $this->verify();
        $this->respond(WpPopPop_Modules_Installer::install($this->slug()));
    }

    public function uninstall() {
        $this->verify();
        $this->respond(WpPopPop_Modules_Installer::uninstall($this->slug(), $this->flag('force'), $this->flag('remove_data')));
    }

    public function delete() {
        $this->verify();
        $this->respond(WpPopPop_Modules_Installer::delete($this->slug(), $this->flag('force'), $this->flag('remove_data')));
    }

    public function usage() {
        $this->verify();
        $slug = $this->slug();
        if ($slug === '' || !WpPopPop_Modules::get($slug)) {
            wp_send_json_error(['message' => __('Module not found.', 'wppoppop')]);
        }
        wp_send_json_success(WpPopPop_Modules_Installer::usage($slug));
    }

    public function upload() {
        $this->verify();
        $f = isset($_FILES['module_zip']) && is_array($_FILES['module_zip']) ? $_FILES['module_zip'] : null;
        if (!$f || !isset($f['error']) || is_array($f['error'])) {
            wp_send_json_error(['code' => 'no_file', 'message' => __('Choose a module .zip file first.', 'wppoppop')]);
        }
        if ((int) $f['error'] !== UPLOAD_ERR_OK) {
            wp_send_json_error(['code' => 'upload_error', 'message' => __('The upload failed. The file may exceed the server upload limit.', 'wppoppop')]);
        }
        $name = isset($f['name']) ? sanitize_file_name(wp_unslash($f['name'])) : '';
        $tmp  = isset($f['tmp_name']) ? (string) $f['tmp_name'] : '';
        if (strtolower(pathinfo($name, PATHINFO_EXTENSION)) !== 'zip' || !is_uploaded_file($tmp)) {
            wp_send_json_error(['code' => 'bad_file', 'message' => __('Only a .zip file can be uploaded.', 'wppoppop')]);
        }
        $this->respond(WpPopPop_Modules_Installer::install_zip($tmp, $this->flag('replace')));
    }

    /** Module Category manager: replaces the user category list (name, order). */
    public function cats_save() {
        $this->verify();
        $raw = isset($_POST['cats']) ? json_decode(wp_unslash($_POST['cats']), true) : null;
        if (!is_array($raw)) {
            wp_send_json_error(['message' => __('Malformed category list.', 'wppoppop')]);
        }
        wp_send_json_success([
            'categories' => WpPopPop_Module_Categories::save($raw),
            'message'    => __('Categories saved.', 'wppoppop'),
        ]);
    }

    /** Extend page: put one module into a category. */
    public function set_category() {
        $this->verify();
        $slug = $this->slug();
        $cat  = isset($_POST['category']) ? sanitize_text_field(wp_unslash($_POST['category'])) : '';
        $res  = WpPopPop_Module_Categories::assign($slug, $cat);
        if (is_wp_error($res)) {
            $this->respond($res);
        }
        $m = WpPopPop_Modules::get($slug);
        wp_send_json_success([
            'module'  => $m ? WpPopPop_Modules_Installer::summary($m) : null,
            'message' => __('Category updated.', 'wppoppop'),
        ]);
    }
}
