<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Premade library templates: one JSON file per template in /library-presets/.
 * Accepts a raw popup export, or {"template": {...meta}, "data": {...export}}.
 */
class WpPopPop_Library_Presets {
    const MAX_FILES = 100;
    const MAX_BYTES = 1572864;

    public static function dir() {
        return WPPOPPOP_PATH . 'library-presets/';
    }

    /** @return array id => template (same shape as saved user templates, plus 'preset' => true) */
    public static function all() {
        static $cache = null;
        if ($cache !== null) {
            return $cache;
        }
        $cache = array();
        $files = glob(self::dir() . '*.json');
        if (!is_array($files)) {
            $files = array();
        }
        /* folder-per-template: library-presets/<name>/template.json (+ images) */
        $dirs = glob(self::dir() . '*/template.json');
        if (is_array($dirs)) {
            $files = array_merge($files, $dirs);
        }
        sort($files);
        if (!class_exists('WpPopPop_Sanitizer')) {
            require_once WPPOPPOP_PATH . 'includes/class-wppoppop-sanitizer.php';
        }
        foreach (array_slice($files, 0, self::MAX_FILES) as $f) {
            if (!is_readable($f) || filesize($f) > self::MAX_BYTES) { continue; }
            $is_dir   = (basename($f) === 'template.json');
            $folder   = $is_dir ? basename(dirname($f)) : '';
            $base_dir = $is_dir ? dirname($f) . '/' : self::dir();
            $base_url = WPPOPPOP_URL . 'library-presets/' . ($is_dir ? rawurlencode($folder) . '/' : '');
            if ($is_dir && (!preg_match('/^[A-Za-z0-9._-]+$/', $folder) || is_link(dirname($f)))) { continue; }
            $j = json_decode((string) file_get_contents($f));
            if (!is_object($j)) { continue; }

            $meta = array();
            $data = $j;
            if (isset($j->template, $j->data) && is_object($j->template) && is_object($j->data)) {
                $meta = get_object_vars($j->template);
                $data = $j->data;
            }
            $data = WpPopPop_Sanitizer::anim_payload($data);
            if ($is_dir) {
                if (!class_exists('WpPopPop_Template_Bundle')) { require_once WPPOPPOP_PATH . 'includes/class-wppoppop-template-bundle.php'; }
                $data = WpPopPop_Template_Bundle::resolve_folder_assets($data, $base_dir, $base_url);
            }
            $json = wp_json_encode($data);
            $d    = json_decode((string) $json, true);
            if (!is_array($d)) { continue; }

            $canv = array();
            if (isset($d['canvases']) && is_array($d['canvases'])) { $canv = $d['canvases']; }
            elseif (isset($d['screens']) && is_array($d['screens'])) { $canv = $d['screens']; }

            $counts = array();
            $thumb  = array();
            $first  = true;
            foreach ($canv as $list) {
                if (!is_array($list)) { continue; }
                foreach ($list as $el) {
                    if (!is_array($el)) { continue; }
                    $type = isset($el['type']) ? sanitize_key((string) $el['type']) : 'element';
                    if ($type === '') { $type = 'element'; }
                    $counts[$type] = isset($counts[$type]) ? $counts[$type] + 1 : 1;
                    if ($first && count($thumb) < 60) {
                        $thumb[] = array(
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
            if (isset($d['canvasMeta'][1]) && is_array($d['canvasMeta'][1])) { $cm = $d['canvasMeta'][1]; }
            elseif (isset($d['canvasMeta']['1']) && is_array($d['canvasMeta']['1'])) { $cm = $d['canvasMeta']['1']; }

            $slug = sanitize_key($is_dir ? $folder : basename($f, '.json'));
            if ($slug === '') { continue; }

            $title = isset($meta['title']) ? sanitize_text_field((string) $meta['title']) : '';
            if ($title === '' && isset($d['title'])) { $title = sanitize_text_field((string) $d['title']); }
            if ($title === '') { $title = ucwords(str_replace(array('-', '_'), ' ', $slug)); }

            $img = isset($meta['image']) ? trim((string) $meta['image']) : '';
            if ($img !== '' && strpos($img, '..') === false && !preg_match('#^https?://#i', $img)) {
                $img = $base_url . ltrim($img, '/');
            }
            $img = ($img !== '' && strpos($img, '..') === false) ? esc_url_raw($img) : '';

            $cat = isset($meta['category']) ? sanitize_key((string) $meta['category']) : '';
            $id  = 'preset-' . $slug;
            if (isset($cache[$id])) { continue; }
            $cache[$id] = array(
                'id'          => $id,
                'title'       => $title,
                'description' => isset($meta['description']) ? sanitize_textarea_field((string) $meta['description']) : '',
                'image'       => $img,
                'category'    => $cat !== '' ? $cat : 'premade',
                'counts'      => $counts,
                'total'       => array_sum($counts),
                'width'       => isset($cm['width']) ? max(100, intval($cm['width'])) : 640,
                'height'      => isset($cm['height']) ? max(100, intval($cm['height'])) : 400,
                'elements'    => $thumb,
                'data'        => (string) $json,
                'preset'      => true,
            );
        }
        return $cache;
    }
}
