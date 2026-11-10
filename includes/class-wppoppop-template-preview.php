<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Library import: read-only preview of a template .zip / .json (nothing is written to the site).
 * Returns title, description, category, element + image counts and a thumbnail the browser can show before the user clicks Import.
 */
class WpPopPop_Template_Preview {
    const MAX_THUMB = 3145728; // 3 MB

    public static function build($tmp, $orig) {
        $size = filesize($tmp);
        if ($size === false || $size < 2) {
            return new WP_Error('size', __('The template file is empty.', 'wppoppop'));
        }
        /* phpcs:ignore WordPress.WP.AlternativeFunctions -- reads a local plugin or upload temp file, not a remote URL */ $head = (string) file_get_contents($tmp, false, null, 0, 4);
        if ($head === "PK\x03\x04") {
            return self::from_zip($tmp, $orig, (int) $size);
        }
        if ($size > WpPopPop_Template_Bundle::MAX_JSON) {
            return new WP_Error('size', __('The template file is empty or too large.', 'wppoppop'));
        }
        /* phpcs:ignore WordPress.WP.AlternativeFunctions -- reads a local plugin or upload temp file, not a remote URL */ $p = self::parse((string) file_get_contents($tmp));
        if (is_wp_error($p)) {
            return $p;
        }
        $thumb = isset($p[0]['image']) ? self::same_site_url($p[0]['image']) : '';
        return self::card($p[0], $p[1], $orig, (int) $size, 0, $thumb, 'json');
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

    protected static function same_site_url($u) {
        $u = trim((string) $u);
        if ($u === '') {
            return '';
        }
        if (preg_match('#^/(?!/)#', $u)) {
            return esc_url_raw($u);
        }
        if (!preg_match('#^https?://#i', $u)) {
            return '';
        }
        $h1 = strtolower((string) wp_parse_url($u, PHP_URL_HOST));
        $h2 = strtolower((string) wp_parse_url(home_url(), PHP_URL_HOST));
        return ($h1 !== '' && $h1 === $h2) ? esc_url_raw($u) : '';
    }

    protected static function card($meta, $data, $orig, $size, $images, $thumb, $kind) {
        $title = isset($meta['title']) ? sanitize_text_field((string) $meta['title']) : '';
        if ($title === '') {
            $title = sanitize_text_field(preg_replace('/\.(zip|json)$/i', '', (string) $orig));
        }
        $desc = isset($meta['description']) ? wp_strip_all_tags((string) $meta['description']) : '';
        if (function_exists('mb_substr')) {
            $desc = mb_substr($desc, 0, 220);
        } else {
            $desc = substr($desc, 0, 220);
        }
        $sum = WpPopPop_Template_Bundle::summary($data);
        return array(
            'kind'        => $kind,
            'title'       => $title,
            'description' => $desc,
            'category'    => isset($meta['category']) ? sanitize_text_field((string) $meta['category']) : '',
            'elements'    => isset($sum['total']) ? (int) $sum['total'] : 0,
            'images'      => (int) $images,
            'size'        => (int) $size,
            'thumb'       => (string) $thumb,
        );
    }

    protected static function thumb_from_zip($zip, $idx, $name) {
        $b = $zip->getFromIndex($idx, self::MAX_THUMB + 1);
        if (!is_string($b) || $b === '' || strlen($b) > self::MAX_THUMB) {
            return '';
        }
        $ext = WpPopPop_Template_Bundle::ext_of($name);
        if ($ext === 'svg') {
            return stripos($b, '<svg') !== false ? /* phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- inline image data URI, not obfuscation */ 'data:image/svg+xml;base64,' . base64_encode($b) : '';
        }
        $info = function_exists('getimagesizefromstring') ? /* phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- invalid image data must not raise a warning */ @getimagesizefromstring($b) : false;
        if (!is_array($info) || empty($info['mime']) || !in_array($info['mime'], array('image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/avif'), true)) {
            return '';
        }
        return /* phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- inline image data URI, not obfuscation */ 'data:' . $info['mime'] . ';base64,' . base64_encode($b);
    }

    protected static function from_zip($tmp, $orig, $size) {
        if (!WpPopPop_Template_Bundle::zip_available()) {
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
            } elseif (WpPopPop_Template_Bundle::is_image_name($name)) {
                $imgs[$name] = array($i, isset($st['size']) ? (int) $st['size'] : 0);
            }
        }
        if ($json_i < 0) {
            $zip->close();
            return new WP_Error('nojson', __('template.json was not found in this zip.', 'wppoppop'));
        }
        $raw = $zip->getFromIndex($json_i, WpPopPop_Template_Bundle::MAX_JSON + 1);
        if (!is_string($raw) || strlen($raw) > WpPopPop_Template_Bundle::MAX_JSON) {
            $zip->close();
            return new WP_Error('size', __('template.json is empty or too large.', 'wppoppop'));
        }
        $p = self::parse($raw);
        if (is_wp_error($p)) {
            $zip->close();
            return $p;
        }
        $prefix = substr($json_n, 0, strlen($json_n) - strlen('template.json'));
        $local  = array();
        foreach ($imgs as $n => $v) {
            if ($prefix === '' || strpos($n, $prefix) === 0) {
                $local[$n] = $v;
            }
        }
        ksort($local);

        $thumb = '';
        $img   = isset($p[0]['image']) ? trim((string) $p[0]['image']) : '';
        if ($img !== '') {
            $rel  = ltrim(preg_replace('#^\./#', '', str_replace('\\', '/', $img)), '/');
            $try  = array($prefix . $rel, $prefix . 'images/' . basename($rel));
            foreach ($try as $cand) {
                if (isset($local[$cand]) && $local[$cand][1] <= self::MAX_THUMB) {
                    $thumb = self::thumb_from_zip($zip, $local[$cand][0], $cand);
                    if ($thumb !== '') {
                        break;
                    }
                }
            }
            if ($thumb === '') {
                $thumb = self::same_site_url($img);
            }
        }
        if ($thumb === '') {
            $tries = 0;
            foreach ($local as $n => $v) {
                if ($v[1] > self::MAX_THUMB) {
                    continue;
                }
                $thumb = self::thumb_from_zip($zip, $v[0], $n);
                if ($thumb !== '' || ++$tries >= 5) {
                    break;
                }
            }
        }
        $zip->close();
        return self::card($p[0], $p[1], $orig, $size, count($local), $thumb, 'zip');
    }
}
