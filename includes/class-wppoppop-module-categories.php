<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Module categories (Settings > Module > Module Category, filter pills on the Extend page).
 * A manageable, ordered list plus a per-module assignment. "All Modules" is the fixed default:
 * it cannot be renamed, moved or deleted, and it shows every module.
 */
class WpPopPop_Module_Categories {

    const OPT          = 'wppoppop_module_categories';
    const MAP_OPT      = 'wppoppop_module_category_map';
    const IMG_OPT      = 'wppoppop_module_image_map';
    const DEFAULT_SLUG = 'all-modules';
    const MAX          = 30;
    const NAME_MAX     = 40;

    public static function default_name() {
        return __('All Modules', 'wppoppop');
    }

    protected static function cut($s, $max) {
        return function_exists('mb_substr') ? mb_substr($s, 0, $max) : substr($s, 0, $max);
    }

    /** User categories only, in saved order. */
    public static function custom() {
        $raw = get_option(self::OPT, []);
        return self::clean(is_array($raw) ? $raw : []);
    }

    /** Default category first, then the user categories. */
    public static function all() {
        $out = [['slug' => self::DEFAULT_SLUG, 'name' => self::default_name(), 'locked' => true]];
        foreach (self::custom() as $c) {
            $out[] = ['slug' => $c['slug'], 'name' => $c['name'], 'locked' => false];
        }
        return $out;
    }

    /** Sanitise a list of {slug, name}: trimmed names, unique slugs, reserved names skipped, capped length and count. */
    public static function clean($raw) {
        $out  = [];
        $seen = [self::DEFAULT_SLUG => 1, 'all' => 1];
        $dflt = strtolower(self::default_name());
        foreach (array_slice(array_values($raw), 0, self::MAX) as $r) {
            if (!is_array($r)) {
                continue;
            }
            $name = (isset($r['name']) && is_scalar($r['name'])) ? sanitize_text_field((string) $r['name']) : '';
            $name = trim(self::cut($name, self::NAME_MAX));
            $low  = strtolower($name);
            if ($name === '' || $low === $dflt || $low === 'all modules') {
                continue;
            }
            $slug = (isset($r['slug']) && is_scalar($r['slug'])) ? sanitize_key((string) $r['slug']) : '';
            if ($slug === '') {
                $t    = sanitize_title($name);
                $slug = (preg_match('/%[0-9a-f]{2}/i', $t) === 1) ? 'cat-' . substr(md5($name), 0, 6) : sanitize_key($t);
            }
            if ($slug === '') {
                $slug = 'category';
            }
            $slug = self::cut($slug, self::NAME_MAX);
            $base = $slug;
            $i    = 2;
            while (isset($seen[$slug])) {
                $slug = $base . '-' . $i;
                $i++;
            }
            $seen[$slug] = 1;
            $out[]       = ['slug' => $slug, 'name' => $name];
        }
        return $out;
    }

    /** Slug of the category a slug or a name refers to (case-insensitive), '' when unknown. */
    public static function match($value, $cats = null) {
        $v = is_scalar($value) ? trim((string) $value) : '';
        if ($v === '') {
            return self::DEFAULT_SLUG;
        }
        $key = sanitize_key($v);
        $low = strtolower($v);
        if ($low === 'all modules') {
            return self::DEFAULT_SLUG;
        }
        foreach (($cats === null ? self::all() : $cats) as $c) {
            if ($c['slug'] === $key || strtolower($c['name']) === $low) {
                return $c['slug'];
            }
        }
        return '';
    }

    public static function map() {
        $m = get_option(self::MAP_OPT, []);
        return is_array($m) ? $m : [];
    }

    /** Category slug of a module: the user's choice, else the manifest "category", else All Modules. */
    public static function of($module) {
        if (!is_array($module) || empty($module['slug'])) {
            return self::DEFAULT_SLUG;
        }
        $cats = self::all();
        $map  = self::map();
        if (isset($map[$module['slug']])) {
            $c = self::match($map[$module['slug']], $cats);
            if ($c !== '') {
                return $c;
            }
        }
        $c = self::match(isset($module['category']) ? $module['category'] : '', $cats);
        return $c === '' ? self::DEFAULT_SLUG : $c;
    }

    /** Save the user categories. Assignments pointing at a removed category are dropped, so those modules fall back. */
    public static function save($raw) {
        update_option(self::OPT, self::clean(is_array($raw) ? $raw : []), false);
        $valid = array_column(self::all(), 'slug');
        $map   = [];
        foreach (self::map() as $mod => $cat) {
            if (is_string($cat) && in_array($cat, $valid, true)) {
                $map[$mod] = $cat;
            }
        }
        update_option(self::MAP_OPT, $map, false);
        return self::all();
    }

    /** Create a category from a module.json name when it does not exist yet. Returns its slug. */
    public static function ensure($name) {
        $hit = self::match($name);
        if ($hit !== '') {
            return $hit;
        }
        $list = self::custom();
        if (count($list) >= self::MAX) {
            return self::DEFAULT_SLUG;
        }
        $list[] = ['slug' => '', 'name' => $name];
        self::save($list);
        $hit = self::match($name);
        return $hit === '' ? self::DEFAULT_SLUG : $hit;
    }

    /** Thumbnail URL ready for <img>: a module-folder path (images/icon.png) is resolved against the module's public URL. */
    public static function image_of($module) {
        $v = self::image_raw($module);
        if ($v !== '' && $v[0] !== '/' && preg_match('#^https?://#i', $v) !== 1) {
            $base = WpPopPop_Modules::module_url($module);
            return $base === '' ? '' : $base . $v;
        }
        return $v;
    }

    /** Image value as stored: the saved override, else the manifest "image". */
    public static function image_raw($module) {
        if (!is_array($module) || empty($module['slug'])) {
            return '';
        }
        $map = get_option(self::IMG_OPT, []);
        if (is_array($map) && isset($map[$module['slug']]) && is_string($map[$module['slug']])) {
            return $map[$module['slug']];
        }
        return isset($module['image']) && is_string($module['image']) ? $module['image'] : '';
    }

    /** Writes "category" (and "image" when given; '' removes it) into a module's module.json. False when the file cannot be updated. */
    protected static function write_manifest($m, $value, $image = null) {
        $dir  = (is_array($m) && !empty($m['dir'])) ? $m['dir'] : '';
        $file = $dir . '/module.json';
        if ($dir === '' || is_link($file) || !is_file($file) || !is_writable($file) || filesize($file) > WpPopPop_Modules::MAX_JSON) {
            return false;
        }
        $raw = json_decode((string) file_get_contents($file));
        if (!is_object($raw)) {
            return false;
        }
        $img = ($image !== null) ? $image : ((isset($raw->image) && is_string($raw->image)) ? $raw->image : '');
        $out = new stdClass();
        foreach (get_object_vars($raw) as $k => $v) {
            if ($k === 'category' || $k === 'image') {
                continue;
            }
            $out->$k = $v;
            if ($k === 'description') {
                $out->category = $value;
                if ($img !== '') {
                    $out->image = $img;
                }
            }
        }
        if (!isset($out->category)) {
            $out->category = $value;
        }
        if ($img !== '' && !isset($out->image)) {
            $out->image = $img;
        }
        $json = wp_json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if (!is_string($json) || $json === '') {
            return false;
        }
        return @file_put_contents($file, $json . "\n", LOCK_EX) !== false;
    }

    /**
     * Edit a module's category. module.json gets the category (its name, or the default slug) so the choice travels with the
     * module; the database keeps the stable slug so renaming a category never detaches its modules.
     * Returns ['category' => slug, 'json' => bool] or WP_Error.
     */
    public static function assign($module_slug, $cat, $image = null) {
        $m = WpPopPop_Modules::slug_valid($module_slug) ? WpPopPop_Modules::get($module_slug) : null;
        if (!$m) {
            return new WP_Error('not_found', __('Module not found.', 'wppoppop'));
        }
        $cat = self::match($cat);
        if ($cat === '') {
            return new WP_Error('bad_category', __('That category no longer exists. Reload the page.', 'wppoppop'));
        }
        $value = self::DEFAULT_SLUG;
        foreach (self::all() as $c) {
            if ($c['slug'] === $cat && empty($c['locked'])) {
                $value = $c['name'];
            }
        }
        if ($image !== null) {
            $image = trim((string) $image);
            if ($image !== '' && WpPopPop_Modules::image_url(['image' => $image]) === '') {
                return new WP_Error('bad_image', __('Enter a valid image URL or path (up to 500 characters).', 'wppoppop'));
            }
            $image = WpPopPop_Modules::image_url(['image' => $image]);
        }
        $json              = self::write_manifest($m, $value, $image);
        $map               = self::map();
        $map[$module_slug] = $cat;
        update_option(self::MAP_OPT, $map, false);
        if ($image !== null) {
            $imgs = get_option(self::IMG_OPT, []);
            $imgs = is_array($imgs) ? $imgs : [];
            $imgs[$module_slug] = $image;
            update_option(self::IMG_OPT, $imgs, false);
        }
        WpPopPop_Modules::flush();
        return ['category' => $cat, 'json' => $json];
    }
}
