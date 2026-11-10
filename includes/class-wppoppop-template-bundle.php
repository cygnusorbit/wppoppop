<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Template bundles: a template's JSON plus the local images it uses, as one portable folder / zip.
 *
 *   <slug>/template.json   {"template": {title, description, category, image, assets}, "data": {...popup...}}
 *   <slug>/images/*        every image that lived on this site's Uploads (or in the plugin folder)
 *
 * Export rewrites those image URLs to relative "images/<file>" paths. Import (zip) copies the files into
 * Uploads/wppoppop-templates/<id>/, registers them in the Media Library and rewrites the paths back to real URLs.
 * The same folder dropped into /library-presets/ is resolved by WpPopPop_Library_Presets.
 */
class WpPopPop_Template_Bundle {
    const MAX_FILE  = 10485760;  // 10 MB per image
    const MAX_TOTAL = 52428800;  // 50 MB of images per template
    const MAX_FILES = 150;
    const MAX_JSON  = 1572864;   // template.json
    const MAX_ZIP   = 62914560;  // uploaded .zip
    const SUBDIR    = 'wppoppop-templates';

    public static function zip_available() {
        return class_exists('ZipArchive');
    }

    public static function exts() {
        return array('jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'svg');
    }

    public static function ext_of($name) {
        return strtolower(pathinfo((string) $name, PATHINFO_EXTENSION));
    }

    public static function is_image_name($name) {
        return in_array(self::ext_of($name), self::exts(), true);
    }

    /* ---------------------------------------------------------------- helpers */

    /** Walks arrays / objects (decoded JSON) and replaces every string leaf with $fn(string). */
    public static function map_strings($node, $fn, $depth = 0) {
        if ($depth > 80) {
            return $node;
        }
        if (is_string($node)) {
            return call_user_func($fn, $node);
        }
        if (is_array($node)) {
            foreach ($node as $k => $v) {
                $node[$k] = self::map_strings($v, $fn, $depth + 1);
            }
            return $node;
        }
        if (is_object($node)) {
            foreach (get_object_vars($node) as $k => $v) {
                $node->$k = self::map_strings($v, $fn, $depth + 1);
            }
        }
        return $node;
    }

    public static function url_regex() {
        return '~https?://[^\s"\'<>()\\\\\[\]{},|^`]+?\.(?:jpe?g|png|gif|webp|avif|svg)(?=$|[\s"\'<>()\\\\\[\]{},;?#])~i';
    }

    /** Raster images must really be that type; SVG must not carry scripts. */
    public static function valid_image_bytes($bytes, $ext) {
        if (!is_string($bytes) || $bytes === '') {
            return false;
        }
        if ($ext === 'svg') {
            if (stripos($bytes, '<svg') === false) {
                return false;
            }
            return !preg_match('/<\s*(script|foreignobject|iframe|embed|object)\b|\bon[a-z]+\s*=|javascript:|data:\s*text\/html/i', $bytes);
        }
        if ($ext === 'avif') {
            return preg_match('/^ftyp(avif|avis)/', substr($bytes, 4, 8)) === 1;
        }
        $info = /* phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- invalid image data must not raise a warning */ @getimagesizefromstring($bytes);
        if (!$info) {
            return false;
        }
        $types = array(1 => array('gif'), 2 => array('jpg', 'jpeg'), 3 => array('png'), 18 => array('webp'));
        return isset($types[$info[2]]) && in_array($ext, $types[$info[2]], true);
    }

    protected static function strip_scheme($url) {
        return rtrim(preg_replace('#^https?:#i', '', (string) $url), '/');
    }

    /** URL prefix => directory pairs whose files may be bundled. */
    protected static function bases() {
        $out = array();
        $up  = wp_get_upload_dir();
        if (empty($up['error']) && !empty($up['baseurl']) && !empty($up['basedir'])) {
            $out[] = array(self::strip_scheme($up['baseurl']), $up['basedir']);
        }
        $out[] = array(self::strip_scheme(WPPOPPOP_URL), WPPOPPOP_PATH);
        foreach (WpPopPop_Pro_API::roots('preset') as $r) {
            $out[] = array(self::strip_scheme($r['url']), $r['path']);
        }
        return $out;
    }

    /** @return array(status, path) status: ok | missing (ours but unreadable) | external */
    public static function resolve($url) {
        $norm = preg_replace('#^https?:#i', '', (string) $url);
        foreach (self::bases() as $b) {
            if (stripos($norm, $b[0] . '/') !== 0) {
                continue;
            }
            $rel = rawurldecode(substr($norm, strlen($b[0]) + 1));
            if ($rel === '' || strpos($rel, "\0") !== false || strpos($rel, '\\') !== false || preg_match('#(^|/)\.\.(/|$)#', $rel)) {
                return array('missing', '');
            }
            $root = realpath($b[1]);
            $real = realpath(rtrim($b[1], '/\\') . '/' . $rel);
            if ($root === false || $real === false || strpos($real, $root . DIRECTORY_SEPARATOR) !== 0 || !is_file($real) || !is_readable($real)) {
                return array('missing', '');
            }
            return array('ok', $real);
        }
        return array('external', '');
    }

    public static function unique_name($name, array &$used) {
        $name = sanitize_file_name($name);
        $ext  = self::ext_of($name);
        $stem = $ext !== '' ? substr($name, 0, -(strlen($ext) + 1)) : $name;
        if ($stem === '') {
            $stem = 'image';
        }
        $out = $stem . ($ext !== '' ? '.' . $ext : '');
        $i   = 1;
        while (isset($used[strtolower($out)])) {
            $i++;
            $out = $stem . '-' . $i . ($ext !== '' ? '.' . $ext : '');
        }
        $used[strtolower($out)] = true;
        return $out;
    }

    /* ----------------------------------------------------------------- export */

    /**
     * Collects the local images of a template and rewrites their URLs to relative "images/<file>" paths.
     * External (other-site) URLs are left untouched.
     *
     * @return array meta, data (object), files (relative => absolute path), missing (count)
     */
    public static function bundle(array $meta, $data) {
        $st = (object) array('files' => array(), 'map' => array(), 'paths' => array(), 'used' => array(), 'missing' => 0, 'total' => 0);

        $conv = function ($str) use ($st) {
            if (!is_string($str) || stripos($str, 'http') === false) {
                return $str;
            }
            $r = preg_replace_callback(WpPopPop_Template_Bundle::url_regex(), function ($m) use ($st) {
                $url = $m[0];
                if (isset($st->map[$url])) {
                    return $st->map[$url];
                }
                $res = WpPopPop_Template_Bundle::resolve($url);
                if ($res[0] === 'external') {
                    return $url;
                }
                $st->map[$url] = $url;
                if ($res[0] !== 'ok') {
                    $st->missing++;
                    return $url;
                }
                $real = $res[1];
                if (isset($st->paths[$real])) {
                    $st->map[$url] = $st->paths[$real];
                    return $st->paths[$real];
                }
                $size = (int) filesize($real);
                $ext  = WpPopPop_Template_Bundle::ext_of($real);
                if ($size < 1 || $size > WpPopPop_Template_Bundle::MAX_FILE || $st->total + $size > WpPopPop_Template_Bundle::MAX_TOTAL
                    || count($st->files) >= WpPopPop_Template_Bundle::MAX_FILES
                    /* phpcs:ignore WordPress.WP.AlternativeFunctions -- reads a local plugin or upload temp file, not a remote URL */ || !WpPopPop_Template_Bundle::valid_image_bytes((string) file_get_contents($real), $ext)) {
                    $st->missing++;
                    return $url;
                }
                $name = WpPopPop_Template_Bundle::unique_name(basename($real), $st->used);
                $rel  = 'images/' . $name;
                $st->files[$rel]  = $real;
                $st->paths[$real] = $rel;
                $st->map[$url]    = $rel;
                $st->total       += $size;
                return $rel;
            }, $str);
            return $r === null ? $str : $r;
        };

        $data = json_decode(wp_json_encode($data));
        $data = self::map_strings($data, $conv);
        if (!empty($meta['image'])) {
            $meta['image'] = $conv((string) $meta['image']);
        }
        $meta['assets'] = array_keys($st->files);

        return array('meta' => $meta, 'data' => $data, 'files' => $st->files, 'missing' => $st->missing);
    }

    /** @return string|WP_Error path of a temporary .zip (caller deletes it) */
    public static function build_zip($slug, array $meta, $data, array $files) {
        if (!self::zip_available()) {
            return new WP_Error('no_zip', __('The PHP Zip extension is not available on this server.', 'wppoppop'));
        }
        $json = wp_json_encode(array('template' => $meta, 'data' => $data), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if (!is_string($json) || $json === '') {
            return new WP_Error('encode', __('Could not encode template.', 'wppoppop'));
        }
        $tmp = wp_tempnam('wppoppop-template.zip');
        if (!$tmp) {
            return new WP_Error('tmp', __('Could not create a temporary file.', 'wppoppop'));
        }
        $zip = new ZipArchive();
        if ($zip->open($tmp, ZipArchive::OVERWRITE) !== true) {
            wp_delete_file($tmp);
            return new WP_Error('zip', __('Could not create the zip file.', 'wppoppop'));
        }
        $zip->addFromString($slug . '/template.json', $json);
        foreach ($files as $rel => $abs) {
            $zip->addFile($abs, $slug . '/' . $rel);
        }
        if (!$zip->close()) {
            wp_delete_file($tmp);
            return new WP_Error('zip', __('Could not write the zip file.', 'wppoppop'));
        }
        return $tmp;
    }

    /* ------------------------------------------------- relative path -> URL */

    /** Replaces "images/<file>" style relative paths (only keys that contain a "/") inside every string. */
    public static function rewrite_relative($node, array $map) {
        $keys = array();
        foreach ($map as $rel => $url) {
            if (strpos($rel, '/') !== false) {
                $keys[] = $rel;
            }
        }
        if (empty($keys)) {
            return $node;
        }
        usort($keys, function ($a, $b) {
            return strlen($b) - strlen($a);
        });
        $alt = array();
        foreach ($keys as $k) {
            $alt[] = preg_quote($k, '~');
        }
        $re = '~(?<![A-Za-z0-9_./:%-])(?:' . implode('|', $alt) . ')(?![A-Za-z0-9_-])~';
        return self::map_strings($node, function ($s) use ($re, $map) {
            if ($s === '') {
                return $s;
            }
            $r = preg_replace_callback($re, function ($m) use ($map) {
                return $map[$m[0]];
            }, $s);
            return $r === null ? $s : $r;
        });
    }

    /** rel path => URL for every image inside a template folder (used by /library-presets/<folder>/). */
    public static function scan_folder_images($dir, $url) {
        static $cache = array();
        if (isset($cache[$dir])) {
            return $cache[$dir];
        }
        $map = array();
        $n   = 0;
        try {
            $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::LEAVES_ONLY);
            foreach ($it as $f) {
                if ($n >= 200) {
                    break;
                }
                if (!$f->isFile() || $f->isLink() || !self::is_image_name($f->getFilename()) || $f->getSize() > self::MAX_FILE) {
                    continue;
                }
                $rel = ltrim(str_replace('\\', '/', substr($f->getPathname(), strlen($dir))), '/');
                if ($rel === '' || $rel[0] === '.') {
                    continue;
                }
                $map[$rel] = $url . implode('/', array_map('rawurlencode', explode('/', $rel)));
                $n++;
            }
        } catch (Exception $e) {
            $map = array();
        }
        $cache[$dir] = $map;
        return $map;
    }

    public static function resolve_folder_assets($data, $dir, $url) {
        $map = self::scan_folder_images($dir, $url);
        return empty($map) ? $data : self::rewrite_relative($data, $map);
    }

    /* ----------------------------------------------------------------- import */

    /** @return array|WP_Error id, title, images, skipped */
    public static function import($tmp, $orig) {
        /* phpcs:ignore WordPress.WP.AlternativeFunctions -- reads a local plugin or upload temp file, not a remote URL */ $head = (string) file_get_contents($tmp, false, null, 0, 4);
        if ($head === "PK\x03\x04") {
            return self::import_zip($tmp, $orig);
        }
        $size = filesize($tmp);
        if ($size === false || $size < 2 || $size > self::MAX_JSON) {
            return new WP_Error('size', __('The template file is empty or too large.', 'wppoppop'));
        }
        /* phpcs:ignore WordPress.WP.AlternativeFunctions -- reads a local plugin or upload temp file, not a remote URL */ return self::finish((string) file_get_contents($tmp), $orig, array());
    }

    protected static function import_zip($tmp, $orig) {
        if (!self::zip_available()) {
            return new WP_Error('no_zip', __('The PHP Zip extension is not available on this server. Upload the template .json instead.', 'wppoppop'));
        }
        $zip = new ZipArchive();
        if ($zip->open($tmp) !== true) {
            return new WP_Error('zip', __('This zip file could not be opened.', 'wppoppop'));
        }
        if ($zip->numFiles > 1000) {
            $zip->close();
            return new WP_Error('zip', __('This zip contains too many files.', 'wppoppop'));
        }
        $json_i = -1;
        $json_n = '';
        $depth  = 99;
        $imgs   = array();
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $st = $zip->statIndex($i);
            if (!is_array($st) || !isset($st['name'])) {
                continue;
            }
            $name = str_replace('\\', '/', (string) $st['name']);
            if ($name === '' || substr($name, -1) === '/' || strpos($name, '__MACOSX/') === 0 || strpos($name, '/__MACOSX/') !== false || basename($name) === '.DS_Store' || basename($name) === 'Thumbs.db') {
                continue;
            }
            if ($name[0] === '/' || strpos($name, "\0") !== false || strpos($name, ':') !== false || preg_match('#(^|/)\.\.(/|$)#', $name)) {
                $zip->close();
                return new WP_Error('unsafe', __('This zip contains unsafe file paths and was rejected.', 'wppoppop'));
            }
            if (basename($name) === 'template.json') {
                $d = substr_count($name, '/');
                if ($d <= 1 && $d < $depth) {
                    $depth  = $d;
                    $json_i = $i;
                    $json_n = $name;
                }
            } elseif (self::is_image_name($name)) {
                $imgs[] = array($i, $name, isset($st['size']) ? (int) $st['size'] : 0);
            }
        }
        if ($json_i < 0) {
            $zip->close();
            return new WP_Error('nojson', __('template.json was not found in this zip.', 'wppoppop'));
        }
        $raw = $zip->getFromIndex($json_i, self::MAX_JSON + 1);
        if (!is_string($raw) || strlen($raw) > self::MAX_JSON) {
            $zip->close();
            return new WP_Error('size', __('template.json is empty or too large.', 'wppoppop'));
        }
        $prefix = substr($json_n, 0, strlen($json_n) - strlen('template.json'));
        $list   = array();
        foreach ($imgs as $im) {
            if ($prefix !== '' && strpos($im[1], $prefix) !== 0) {
                continue;
            }
            if ($im[2] > self::MAX_FILE || count($list) >= self::MAX_FILES) {
                continue;
            }
            $idx    = $im[0];
            $list[] = array('rel' => substr($im[1], strlen($prefix)), 'read' => function () use ($zip, $idx) {
                return $zip->getFromIndex($idx, WpPopPop_Template_Bundle::MAX_FILE + 1);
            });
        }
        $res = self::finish($raw, $orig, $list);
        $zip->close();
        return $res;
    }

    protected static function parse($raw) {
        $j = json_decode((string) $raw);
        if (!is_object($j)) {
            return new WP_Error('json', __('This is not a valid template file.', 'wppoppop'));
        }
        $meta = array();
        $data = $j;
        if (isset($j->template, $j->data) && is_object($j->template) && is_object($j->data)) {
            $meta = get_object_vars($j->template);
            $data = $j->data;
        }
        $has = (isset($data->canvases) && (is_object($data->canvases) || is_array($data->canvases)))
            || (isset($data->screens) && (is_object($data->screens) || is_array($data->screens)));
        if (!$has) {
            return new WP_Error('json', __('This file does not look like a WpPopPop template.', 'wppoppop'));
        }
        return array($meta, $data);
    }

    protected static function dest($tplid) {
        $up = wp_get_upload_dir();
        if (!empty($up['error']) || empty($up['basedir'])) {
            return new WP_Error('upload', __('The uploads folder is not available.', 'wppoppop'));
        }
        $root = trailingslashit($up['basedir']) . self::SUBDIR . '/';
        $dir  = $root . $tplid . '/';
        if (!wp_mkdir_p($dir)) {
            return new WP_Error('upload', __('Could not create the template image folder in uploads.', 'wppoppop'));
        }
        foreach (array($root, $dir) as $d) {
            if (!file_exists($d . 'index.php')) {
                /* phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions -- best-effort index.php guard; local file or stream handle owned by this plugin; WP_Filesystem would need FTP credentials */ @file_put_contents($d . 'index.php', "<?php\n// Silence is golden.\n");
            }
        }
        return array('dir' => $dir, 'url' => trailingslashit($up['baseurl']) . self::SUBDIR . '/' . $tplid . '/');
    }

    protected static function register_attachment($file, $name, $tplid) {
        $ft = wp_check_filetype($name);
        if (empty($ft['type']) || strpos($ft['type'], 'image/') !== 0 || $ft['type'] === 'image/svg+xml') {
            return;
        }
        if (!function_exists('wp_generate_attachment_metadata')) {
            require_once ABSPATH . 'wp-admin/includes/image.php';
        }
        $id = wp_insert_attachment(array(
            'post_mime_type' => $ft['type'],
            'post_title'     => sanitize_text_field(pathinfo($name, PATHINFO_FILENAME)),
            'post_content'   => '',
            'post_status'    => 'inherit',
        ), $file);
        if (!$id || is_wp_error($id)) {
            return;
        }
        update_post_meta($id, '_wppoppop_template', $tplid);
        $m = wp_generate_attachment_metadata($id, $file);
        if (is_array($m)) {
            wp_update_attachment_metadata($id, $m);
        }
    }

    /** Preview counters / size, computed server-side (same rules as Save As Template). */
    public static function summary($data) {
        $d    = json_decode(wp_json_encode($data), true);
        $canv = array();
        if (isset($d['canvases']) && is_array($d['canvases'])) {
            $canv = $d['canvases'];
        } elseif (isset($d['screens']) && is_array($d['screens'])) {
            $canv = $d['screens'];
        }
        $counts = array();
        $prev   = array();
        $first  = true;
        foreach ($canv as $list) {
            if (!is_array($list)) {
                continue;
            }
            foreach ($list as $el) {
                if (!is_array($el)) {
                    continue;
                }
                $type = isset($el['type']) ? sanitize_key((string) $el['type']) : 'element';
                if ($type === '') {
                    $type = 'element';
                }
                $counts[$type] = isset($counts[$type]) ? $counts[$type] + 1 : 1;
                if ($first && count($prev) < 60) {
                    $prev[] = array(
                        'type'   => $type,
                        'left'   => isset($el['left']) ? intval($el['left']) : 0,
                        'top'    => isset($el['top']) ? intval($el['top']) : 0,
                        'width'  => isset($el['width']) ? intval($el['width']) : 100,
                        'height' => isset($el['height']) ? intval($el['height']) : 40,
                    );
                }
            }
            $first = false;
        }
        $cm = array();
        if (isset($d['canvasMeta'][1]) && is_array($d['canvasMeta'][1])) {
            $cm = $d['canvasMeta'][1];
        } elseif (isset($d['canvasMeta']['1']) && is_array($d['canvasMeta']['1'])) {
            $cm = $d['canvasMeta']['1'];
        }
        return array(
            'counts'   => $counts,
            'total'    => array_sum($counts),
            'width'    => isset($cm['width']) ? max(100, intval($cm['width'])) : 640,
            'height'   => isset($cm['height']) ? max(100, intval($cm['height'])) : 400,
            'elements' => $prev,
        );
    }

    protected static function finish($raw, $orig, array $images) {
        $parsed = self::parse($raw);
        if (is_wp_error($parsed)) {
            return $parsed;
        }
        list($meta, $data) = $parsed;

        if (!class_exists('WpPopPop_Ajax_Templates')) {
            require_once WPPOPPOP_PATH . 'includes/ajax/class-ajax-templates.php';
        }
        $all = get_option(WpPopPop_Ajax_Templates::OPT, array());
        $all = is_array($all) ? $all : array();
        if (count($all) >= 200) {
            return new WP_Error('limit', __('Template limit reached (200). Delete some templates first.', 'wppoppop'));
        }

        $tplid   = 'user-' . substr(md5(uniqid('', true)), 0, 10);
        $map     = array();
        $done    = 0;
        $skipped = 0;
        if (!empty($images)) {
            $dest = self::dest($tplid);
            if (is_wp_error($dest)) {
                return $dest;
            }
            $used   = array();
            $total  = 0;
            $attach = count($images) <= 40;
            foreach ($images as $img) {
                $rel   = $img['rel'];
                $ext   = self::ext_of($rel);
                $bytes = call_user_func($img['read']);
                if (!is_string($bytes) || strlen($bytes) > self::MAX_FILE || $total + strlen($bytes) > self::MAX_TOTAL || !self::valid_image_bytes($bytes, $ext)) {
                    $skipped++;
                    continue;
                }
                $name = self::unique_name(basename($rel), $used);
                $path = $dest['dir'] . $name;
                if (/* phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions -- the result is checked and reported; local file or stream handle owned by this plugin; WP_Filesystem would need FTP credentials */ @file_put_contents($path, $bytes) === false) {
                    $skipped++;
                    continue;
                }
                $total     += strlen($bytes);
                $map[$rel]  = $dest['url'] . rawurlencode($name);
                $done++;
                if ($attach) {
                    self::register_attachment($path, $name, $tplid);
                }
            }
        }

        $data = self::rewrite_relative($data, $map);
        $data = WpPopPop_Ajax_Templates::sanitize_object($data);
        $json = wp_json_encode($data);
        if (!is_string($json) || $json === '') {
            return new WP_Error('encode', __('Could not encode template.', 'wppoppop'));
        }

        $title = isset($meta['title']) ? sanitize_text_field((string) $meta['title']) : '';
        if ($title === '' && isset($data->title) && is_string($data->title)) {
            $title = sanitize_text_field($data->title);
        }
        if ($title === '') {
            $title = ucwords(str_replace(array('-', '_'), ' ', pathinfo((string) $orig, PATHINFO_FILENAME)));
        }
        if ($title === '') {
            $title = __('Imported template', 'wppoppop');
        }
        if (function_exists('mb_substr')) {
            $title = mb_substr($title, 0, 120);
        }

        $img = isset($meta['image']) ? trim((string) $meta['image']) : '';
        $key = ltrim($img, '/');
        if (isset($map[$key])) {
            $img = $map[$key];
        } elseif (!preg_match('#^https?://#i', $img)) {
            $img = '';
        }
        $img = ($img !== '' && strpos($img, '..') === false) ? esc_url_raw($img) : '';

        $cat   = isset($meta['category']) ? sanitize_key((string) $meta['category']) : '';
        $valid = array();
        foreach (WpPopPop_Ajax_Templates::get_categories() as $c) {
            $valid[] = $c['slug'];
        }
        if (!in_array($cat, $valid, true)) {
            $cat = 'custom';
        }

        $sum         = self::summary($data);
        $all[$tplid] = array(
            'id'          => $tplid,
            'title'       => $title,
            'description' => isset($meta['description']) ? sanitize_textarea_field((string) $meta['description']) : '',
            'image'       => $img,
            'category'    => $cat,
            'counts'      => $sum['counts'],
            'total'       => $sum['total'],
            'width'       => $sum['width'],
            'height'      => $sum['height'],
            'elements'    => $sum['elements'],
            'data'        => $json,
            'created'     => time(),
        );
        update_option(WpPopPop_Ajax_Templates::OPT, $all, false);

        return array('id' => $tplid, 'title' => $title, 'images' => $done, 'skipped' => $skipped);
    }
}
