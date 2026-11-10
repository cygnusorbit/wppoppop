<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Library "Select": makes sure every local image of a template (jpg, png, svg, ...) lives in the Media Library
 * before the popup is created. Premade (plugin folder) images are copied to Uploads/wppoppop-templates/<id>/;
 * Uploads images without an attachment are registered. URLs inside the popup data are rewritten to the Media URLs.
 */
class WpPopPop_Template_Media {
    const MAX_IMAGES = 40;

    protected static function mime($ext) {
        $m = array(
            'jpg'  => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif',
            'webp' => 'image/webp', 'avif' => 'image/avif', 'svg' => 'image/svg+xml',
        );
        return isset($m[$ext]) ? $m[$ext] : '';
    }

    /** @return array json (string), count (images now in the Media Library) */
    public static function localize($json, $tplid) {
        if (!class_exists('WpPopPop_Template_Bundle')) {
            require_once WPPOPPOP_PATH . 'includes/class-wppoppop-template-bundle.php';
        }
        $out  = array('json' => (string) $json, 'count' => 0);
        $data = json_decode((string) $json);
        if (!is_object($data) && !is_array($data)) {
            return $out;
        }
        $cache = array();
        $seen  = 0;
        $data  = WpPopPop_Template_Bundle::map_strings($data, function ($s) use (&$cache, &$seen, $tplid) {
            if ($s === '' || stripos($s, 'http') === false) {
                return $s;
            }
            $r = preg_replace_callback(WpPopPop_Template_Bundle::url_regex(), function ($m) use (&$cache, &$seen, $tplid) {
                $u = $m[0];
                if (!array_key_exists($u, $cache)) {
                    $cache[$u] = ($seen >= WpPopPop_Template_Media::MAX_IMAGES) ? '' : WpPopPop_Template_Media::one($u, $tplid);
                    if ($cache[$u] !== '') {
                        $seen++;
                    }
                }
                return $cache[$u] !== '' ? $cache[$u] : $u;
            }, $s);
            return $r === null ? $s : $r;
        });
        $enc = wp_json_encode($data);
        if (is_string($enc) && $enc !== '') {
            $out['json']  = $enc;
            $out['count'] = $seen;
        }
        return $out;
    }

    /** @return string Media Library URL, or '' when the image is external / invalid / unreadable */
    public static function one($url, $tplid) {
        $res = WpPopPop_Template_Bundle::resolve($url);
        if ($res[0] !== 'ok') {
            return '';
        }
        $path = $res[1];
        $ext  = WpPopPop_Template_Bundle::ext_of($path);
        $size = filesize($path);
        if (self::mime($ext) === '' || $size === false || $size > WpPopPop_Template_Bundle::MAX_FILE) {
            return '';
        }
        /* phpcs:ignore WordPress.WP.AlternativeFunctions -- reads a local plugin or upload temp file, not a remote URL */ $bytes = (string) file_get_contents($path);
        if (!WpPopPop_Template_Bundle::valid_image_bytes($bytes, $ext)) {
            return '';
        }

        $up   = wp_get_upload_dir();
        $base = empty($up['error']) ? realpath($up['basedir']) : false;
        if ($base !== false && strpos($path, $base . DIRECTORY_SEPARATOR) === 0) {
            if (!attachment_url_to_postid($url)) {
                self::attach($path, $tplid, '');
            }
            return $url;
        }

        $root = realpath(WPPOPPOP_PATH);
        $rel  = $root !== false ? ltrim(str_replace('\\', '/', substr($path, strlen($root))), '/') : '';
        if ($rel === '') {
            return '';
        }
        $key = 'plugin:' . $rel . ':' . md5($bytes);
        $id  = self::lookup($key);
        if ($id) {
            $u = wp_get_attachment_url($id);
            return $u ? $u : '';
        }
        $dir = trailingslashit($up['basedir']) . WpPopPop_Template_Bundle::SUBDIR . '/' . sanitize_key($tplid) . '/';
        if (!wp_mkdir_p($dir)) {
            return '';
        }
        if (!file_exists($dir . 'index.php')) {
            /* phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions -- best-effort index.php guard; local file or stream handle owned by this plugin; WP_Filesystem would need FTP credentials */ @file_put_contents($dir . 'index.php', "<?php\n// Silence is golden.\n");
        }
        $name = wp_unique_filename($dir, sanitize_file_name(basename($path)));
        $dest = $dir . $name;
        if (/* phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions -- the result is checked and reported; local file or stream handle owned by this plugin; WP_Filesystem would need FTP credentials */ @file_put_contents($dest, $bytes) === false) {
            return '';
        }
        $id = self::attach($dest, $tplid, $key);
        if (!$id) {
            return '';
        }
        $u = wp_get_attachment_url($id);
        return $u ? $u : '';
    }

    protected static function lookup($key) {
        $ids = get_posts(array(
            'post_type' => 'attachment', 'post_status' => 'inherit', 'numberposts' => 1, 'fields' => 'ids',
            // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key, WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- one attachment is looked up by its own import key (numberposts 1)
            'meta_key' => '_wppoppop_src', 'meta_value' => $key, 'no_found_rows' => true,
        ));
        if (empty($ids)) {
            return 0;
        }
        $f = get_attached_file($ids[0]);
        return ($f && file_exists($f)) ? (int) $ids[0] : 0;
    }

    protected static function attach($file, $tplid, $key) {
        $ext = WpPopPop_Template_Bundle::ext_of($file);
        $id  = wp_insert_attachment(array(
            'post_mime_type' => self::mime($ext),
            'post_title'     => sanitize_text_field(pathinfo($file, PATHINFO_FILENAME)),
            'post_content'   => '',
            'post_status'    => 'inherit',
        ), $file);
        if (!$id || is_wp_error($id)) {
            return 0;
        }
        update_post_meta($id, '_wppoppop_template', sanitize_key($tplid));
        if ($key !== '') {
            update_post_meta($id, '_wppoppop_src', $key);
        }
        if ($ext !== 'svg') {
            if (!function_exists('wp_generate_attachment_metadata')) {
                require_once ABSPATH . 'wp-admin/includes/image.php';
            }
            $m = wp_generate_attachment_metadata($id, $file);
            if (is_array($m)) {
                wp_update_attachment_metadata($id, $m);
            }
        }
        return (int) $id;
    }
}
