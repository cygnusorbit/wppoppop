<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Module registry.
 *
 * Merges built-in manifests (modules/<slug>/module.json) with uploaded ones
 * (uploads/wppoppop-modules/<slug>/module.json) into one validated list.
 * Read-only: nothing here installs, deletes or renders a module.
 *
 * Manifest schema v1: slug (= folder name), name, version, author, description,
 * locked (built-in only), default_active, order, on_missing (skip|block),
 * requires[], data_keys[], min_version, deprecated, template (built-in only).
 *
 * States: installed | available | deleted.
 * Built-in default = installed, uploaded default = available. Locked = always installed.
 */
class WpPopPop_Modules {

    const SCHEMA    = 1;
    const STATE_OPT = 'wppoppop_modules_state';
    const MAX_JSON  = 65536;
    const MAX_FIELDS = 40;
    const FIELD_TYPES = ['toggle', 'text', 'textarea', 'number', 'select', 'color', 'url'];
    const MAX_HTML   = 65536;
    const ADOPTABLE  = ['triggers', 'exit_intent_sensitivity', 'scroll_depth'];
    const ORPHAN_OPT = 'wppoppop_modules_orphans';

    protected static $cache = null;

    public static function core_dir() {
        return WPPOPPOP_PATH . 'modules';
    }

    public static function uploads_dir() {
        $u = wp_upload_dir(null, false);
        return empty($u['basedir']) ? '' : rtrim($u['basedir'], '/\\') . '/wppoppop-modules';
    }

    public static function flush() {
        self::$cache = null;
    }

    public static function slug_valid($slug) {
        return is_string($slug) && preg_match('/^[a-z0-9][a-z0-9-]{1,63}$/', $slug) === 1;
    }

    protected static function text($raw, $key, $max) {
        if (!isset($raw[$key]) || !is_scalar($raw[$key])) {
            return '';
        }
        return mb_substr(trim(wp_strip_all_tags((string) $raw[$key])), 0, $max);
    }

    protected static function slug_list($raw, $key, $strict_slug) {
        $out = [];
        if (isset($raw[$key]) && is_array($raw[$key])) {
            foreach ($raw[$key] as $v) {
                if (!is_string($v)) {
                    continue;
                }
                $ok = $strict_slug ? self::slug_valid($v) : (preg_match('/^[A-Za-z0-9_.-]{1,64}$/', $v) === 1);
                if ($ok && !in_array($v, $out, true)) {
                    $out[] = $v;
                }
            }
        }
        return $out;
    }

    /** Clean the declarative field list of a manifest. Invalid fields are dropped. */
    protected static function fields($raw) {
        $out  = [];
        $seen = [];
        if (!isset($raw['fields']) || !is_array($raw['fields'])) {
            return $out;
        }
        foreach ($raw['fields'] as $f) {
            if (count($out) >= self::MAX_FIELDS) {
                break;
            }
            if (!is_array($f)) {
                continue;
            }
            $key  = (isset($f['key']) && is_string($f['key'])) ? $f['key'] : '';
            $type = (isset($f['type']) && is_string($f['type'])) ? $f['type'] : '';
            if (preg_match('/^[a-z][a-z0-9_]{0,39}$/', $key) !== 1 || isset($seen[$key]) || !in_array($type, self::FIELD_TYPES, true)) {
                continue;
            }
            $label = self::text($f, 'label', 80);
            $n = [
                'key'        => $key,
                'type'       => $type,
                'label'      => ($label !== '' ? $label : $key),
                'help'       => self::text($f, 'help', 160),
                'default'    => (isset($f['default']) && is_scalar($f['default'])) ? $f['default'] : null,
                'options'    => [],
                'min'        => 0,
                'max'        => 1000000,
                'step'       => 1,
                'max_length' => ($type === 'textarea' || $type === 'url') ? 2000 : 255,
            ];
            if ($type === 'select') {
                $opts = (isset($f['options']) && is_array($f['options'])) ? $f['options'] : [];
                foreach ($opts as $ok => $ov) {
                    if (count($n['options']) >= 50) {
                        break;
                    }
                    $val = is_array($ov) ? (isset($ov['value']) ? $ov['value'] : null) : $ok;
                    $lab = is_array($ov) ? (isset($ov['label']) ? $ov['label'] : $val) : $ov;
                    if (!is_scalar($val) || !is_scalar($lab) || preg_match('/^[A-Za-z0-9_.:-]{1,64}$/', (string) $val) !== 1) {
                        continue;
                    }
                    $n['options'][(string) $val] = mb_substr(trim(wp_strip_all_tags((string) $lab)), 0, 80);
                }
                if (!$n['options']) {
                    continue;
                }
            }
            if ($type === 'number') {
                foreach (['min', 'max', 'step'] as $nk) {
                    if (isset($f[$nk]) && is_numeric($f[$nk])) {
                        $n[$nk] = $f[$nk] + 0;
                    }
                }
                if ($n['step'] <= 0) {
                    $n['step'] = 1;
                }
                if ($n['min'] > $n['max']) {
                    $n['max'] = $n['min'];
                }
            }
            if (isset($f['max_length']) && is_numeric($f['max_length']) && in_array($type, ['text', 'textarea'], true)) {
                $n['max_length'] = max(1, min(2000, (int) $f['max_length']));
            }
            $seen[$key] = true;
            $out[]      = $n;
        }
        return $out;
    }

    /** Validate and clean one manifest. Returns null when it is unusable. */
    public static function normalize($raw, $source, $folder) {
        if (!is_array($raw)) {
            return null;
        }
        $slug = isset($raw['slug']) && is_string($raw['slug']) ? $raw['slug'] : '';
        if (!self::slug_valid($slug) || $slug !== $folder) {
            return null;
        }
        $name = self::text($raw, 'name', 80);
        if ($name === '') {
            return null;
        }
        $version = self::text($raw, 'version', 20);
        if (preg_match('/^\d+(\.\d+){0,3}([-+][0-9A-Za-z.-]+)?$/', $version) !== 1) {
            $version = '1.0.0';
        }
        $min = self::text($raw, 'min_version', 20);
        if (preg_match('/^\d+(\.\d+){0,3}$/', $min) !== 1) {
            $min = '';
        }
        $core     = ($source === 'core');
        $on_miss  = (isset($raw['on_missing']) && $raw['on_missing'] === 'block') ? 'block' : 'skip';
        $template = '';
        if ($core && isset($raw['template']) && is_string($raw['template'])
            && preg_match('#^[A-Za-z0-9_/.-]+\.php$#', $raw['template']) === 1 && strpos($raw['template'], '..') === false) {
            $template = $raw['template'];
        }
        $order = isset($raw['order']) && is_numeric($raw['order']) ? max(0, min(9999, (int) $raw['order'])) : 1000;

        /* Declarative modules own exactly one namespaced settings key; they can never claim a built-in key. */
        $fields      = self::fields($raw);
        /* "accordion": "accordion.html" = static Inspector markup. Such a module may keep owning a few legacy settings keys. */
        $html        = (!$core && isset($raw['accordion']) && $raw['accordion'] === 'accordion.html');
        $declarative = ($fields && $template === '' && !$html);
        $ext_key     = 'ext_' . str_replace('-', '_', $slug);
        $data_keys   = self::slug_list($raw, 'data_keys', false);
        $in_use      = self::slug_list($raw, 'in_use', false);
        if ($declarative) {
            $fkeys     = array_column($fields, 'key');
            $data_keys = [$ext_key];
            $in_use    = array_map(function ($k) use ($ext_key) {
                return $ext_key . '.' . $k;
            }, array_values(array_intersect($in_use, $fkeys)));
        } elseif ($html) {
            $allow     = apply_filters('wppoppop_module_adoptable_keys', self::ADOPTABLE);
            $allow     = is_array($allow) ? $allow : self::ADOPTABLE;
            $data_keys = array_values(array_intersect($data_keys, $allow));
            $in_use    = array_values(array_filter($in_use, function ($p) use ($data_keys) {
                $root = explode('.', $p);
                return in_array($root[0], $data_keys, true);
            }));
        } elseif (!$core) {
            $data_keys = [];
            $in_use    = [];
        }

        return [
            'schema'         => self::SCHEMA,
            'slug'           => $slug,
            'name'           => $name,
            'version'        => $version,
            'author'         => self::text($raw, 'author', 80),
            'description'    => self::text($raw, 'description', 240),
            'locked'         => $core && !empty($raw['locked']),
            'default_active' => !empty($raw['default_active']),
            'order'          => $order,
            'on_missing'     => $on_miss,
            'requires'       => self::slug_list($raw, 'requires', true),
            'data_keys'      => $data_keys,
            'in_use'         => $in_use,
            'fields'         => $declarative ? $fields : [],
            'declarative'    => $declarative,
            'html'           => $html,
            'ext_key'        => $declarative ? $ext_key : '',
            'assets'         => ['css' => false, 'js' => false, 'admin_js' => false],
            'dir'            => '',
            'min_version'    => $min,
            'compatible'     => ($min === '' || !defined('WPPOPPOP_VERSION') || version_compare(WPPOPPOP_VERSION, $min, '>=')),
            'deprecated'     => !empty($raw['deprecated']),
            'template'       => $template,
            'source'         => $core ? 'core' : 'uploaded',
        ];
    }

    protected static function inside($path, $root) {
        $p = realpath($path);
        $r = realpath($root);
        if ($p === false || $r === false) {
            return false;
        }
        return strpos($p . DIRECTORY_SEPARATOR, rtrim($r, '/\\') . DIRECTORY_SEPARATOR) === 0;
    }

    protected static function read_manifest($dir, $source) {
        $file = $dir . '/module.json';
        if (is_link($file) || !is_file($file) || !is_readable($file) || filesize($file) > self::MAX_JSON) {
            return null;
        }
        $raw = json_decode((string) file_get_contents($file), true);
        $m   = self::normalize($raw, $source, basename($dir));
        if ($m) {
            $m['dir'] = $dir;
            foreach (['css' => 'module.css', 'js' => 'module.js', 'admin_js' => 'admin.js'] as $k => $name) {
                $f = $dir . '/' . $name;
                $m['assets'][$k] = (is_file($f) && !is_link($f) && is_readable($f) && self::inside($f, $dir));
            }
            if ($m['html']) {
                $f         = $dir . '/accordion.html';
                $m['html'] = (is_file($f) && !is_link($f) && is_readable($f) && self::inside($f, $dir) && filesize($f) <= self::MAX_HTML);
            }
        }
        return $m;
    }

    public static function uploads_url() {
        $u = wp_upload_dir(null, false);
        return empty($u['baseurl']) ? '' : rtrim($u['baseurl'], '/') . '/wppoppop-modules';
    }

    /** Public URL of a module folder (trailing slash). */
    public static function module_url($m) {
        if (!is_array($m) || empty($m['slug'])) {
            return '';
        }
        $base = ($m['source'] === 'core') ? WPPOPPOP_URL . 'modules' : self::uploads_url();
        return $base === '' ? '' : $base . '/' . $m['slug'] . '/';
    }

    /** True unless the module is known AND not installed (gate for backend/front features). */
    public static function active($slug) {
        return !self::get($slug) || self::is_installed($slug);
    }

    protected static function scan($root, $source) {
        $out = [];
        if ($root === '' || !is_dir($root)) {
            return $out;
        }
        $dirs = glob(rtrim($root, '/\\') . '/*', GLOB_ONLYDIR);
        if (!is_array($dirs)) {
            return $out;
        }
        foreach ($dirs as $dir) {
            if (is_link($dir) || !self::inside($dir, $root)) {
                continue;
            }
            $m = self::read_manifest($dir, $source);
            if ($m) {
                $out[] = $m;
            }
        }
        return $out;
    }

    /** Every valid module, sorted by manifest order. A built-in slug cannot be shadowed by an upload. */
    public static function all() {
        if (self::$cache !== null) {
            return self::$cache;
        }
        $out = [];
        foreach (self::scan(self::core_dir(), 'core') as $m) {
            $out[$m['slug']] = $m;
        }
        foreach (self::scan(self::uploads_dir(), 'uploaded') as $m) {
            if (!isset($out[$m['slug']])) {
                $out[$m['slug']] = $m;
            }
        }
        uasort($out, function ($a, $b) {
            return $a['order'] === $b['order'] ? strcmp($a['slug'], $b['slug']) : ($a['order'] < $b['order'] ? -1 : 1);
        });
        self::$cache = $out;
        return $out;
    }

    public static function get($slug) {
        $all = self::all();
        return (is_string($slug) && isset($all[$slug])) ? $all[$slug] : null;
    }

    /** Stored per-module state map (slug => installed|available|deleted). */
    public static function states() {
        $s = get_option(self::STATE_OPT, []);
        return is_array($s) ? $s : [];
    }

    public static function state($slug) {
        $m = self::get($slug);
        if (!$m) {
            return 'unknown';
        }
        if ($m['locked']) {
            return 'installed';
        }
        if ($m['source'] === 'uploaded' && !$m['compatible']) {
            return 'available';
        }
        $s = self::states();
        if (isset($s[$slug]) && in_array($s[$slug], ['installed', 'available', 'deleted'], true)) {
            return $s[$slug];
        }
        return $m['source'] === 'core' ? 'installed' : 'available';
    }

    /** Persist a module state (installed|available|deleted). Locked modules never change. */
    public static function set_state($slug, $state) {
        $m = self::get($slug);
        if (!$m || $m['locked'] || !in_array($state, ['installed', 'available', 'deleted'], true)) {
            return false;
        }
        $s = self::states();
        $s[$slug] = $state;
        update_option(self::STATE_OPT, $s, false);
        self::flush();
        return true;
    }

    public static function forget_state($slug) {
        $s = self::states();
        if (isset($s[$slug])) {
            unset($s[$slug]);
            update_option(self::STATE_OPT, $s, false);
        }
        self::flush();
    }

    /** Installed modules that list $slug in "requires" (slug => manifest). */
    public static function required_by($slug) {
        $out = [];
        foreach (self::installed() as $k => $m) {
            if ($k !== $slug && in_array($slug, $m['requires'], true)) {
                $out[$k] = $m;
            }
        }
        return $out;
    }

    public static function is_installed($slug) {
        return self::state($slug) === 'installed';
    }

    public static function installed() {
        $out = [];
        foreach (self::all() as $slug => $m) {
            if (self::is_installed($slug)) {
                $out[$slug] = $m;
            }
        }
        return $out;
    }

    /** slug => display name for installed modules (built-in names are translatable). */
    public static function labels() {
        $out = [];
        foreach (self::installed() as $slug => $m) {
            $out[$slug] = ($m['source'] === 'core') ? translate($m['name'], 'wppoppop') : $m['name'];
        }
        return $out;
    }

    public static function locked() {
        $out = [];
        foreach (self::installed() as $slug => $m) {
            if ($m['locked']) {
                $out[] = $slug;
            }
        }
        return $out;
    }

    /** slug => 1|0 default switch for installed modules (locked = 1). */
    public static function defaults() {
        $out = [];
        foreach (self::installed() as $slug => $m) {
            $out[$slug] = ($m['locked'] || $m['default_active']) ? 1 : 0;
        }
        return $out;
    }

    /** What a popup does when it uses this module but it is not installed: skip | block. */
    public static function on_missing($slug) {
        $m = self::get($slug);
        return ($m && $m['on_missing'] === 'block') ? 'block' : 'skip';
    }

    /** data key => slug for settings keys owned ONLY by modules that are not installed. */
    public static function missing_keys() {
        $kept = [];
        $out  = [];
        foreach (self::all() as $slug => $m) {
            if ($m['locked'] || self::is_installed($slug)) {
                foreach ($m['data_keys'] as $k) {
                    $kept[$k] = true;
                }
            }
        }
        foreach (self::all() as $slug => $m) {
            if ($m['locked'] || self::is_installed($slug)) {
                continue;
            }
            foreach ($m['data_keys'] as $k) {
                if (!isset($kept[$k]) && !isset($out[$k])) {
                    $out[$k] = $slug;
                }
            }
        }
        /* Keys of a deleted uploaded module stay protected (data kept, nothing rendered) until it is reinstalled or its data is removed. */
        $all = self::all();
        foreach (self::orphans() as $slug => $keys) {
            if (isset($all[$slug]) || !is_array($keys)) {
                continue;
            }
            foreach ($keys as $k) {
                if (is_string($k) && !isset($kept[$k]) && !isset($out[$k])) {
                    $out[$k] = $slug;
                }
            }
        }
        return $out;
    }

    public static function orphans() {
        $o = get_option(self::ORPHAN_OPT, []);
        return is_array($o) ? $o : [];
    }

    public static function set_orphan($slug, array $keys) {
        $o = self::orphans();
        if ($keys) {
            $o[$slug] = array_values($keys);
        } else {
            unset($o[$slug]);
        }
        update_option(self::ORPHAN_OPT, $o, false);
    }

    public static function clear_orphan($slug) {
        $o = self::orphans();
        if (isset($o[$slug])) {
            unset($o[$slug]);
            update_option(self::ORPHAN_OPT, $o, false);
        }
    }

    /**
     * Save-time guard. Settings owned by a not-installed module are never overwritten:
     * the previously stored value is restored, and a new value is dropped.
     * $settings is the decoded settings object; $prev_json the popup's stored JSON (or '').
     */
    public static function guard_save($settings, $prev_json) {
        $keys = self::missing_keys();
        if (!$keys || !is_object($settings)) {
            return $settings;
        }
        $prev = json_decode((string) $prev_json);
        $prev = (is_object($prev) && isset($prev->settings) && is_object($prev->settings)) ? $prev->settings : null;
        foreach (array_keys($keys) as $k) {
            if ($prev && property_exists($prev, $k)) {
                $settings->$k = $prev->$k;
            } else {
                unset($settings->$k);
            }
        }
        return $settings;
    }

    public static function path_value($arr, $path) {
        foreach (explode('.', $path) as $p) {
            if (!is_array($arr) || !array_key_exists($p, $arr)) {
                return null;
            }
            $arr = $arr[$p];
        }
        return $arr;
    }

    public static function truthy($v) {
        if (is_string($v)) {
            $v = trim($v);
            if (is_numeric($v)) {
                return (float) $v != 0;
            }
            return !in_array(strtolower($v), ['', 'false', 'off', 'no'], true);
        }
        return is_array($v) ? !empty($v) : (bool) $v;
    }

    /**
     * Render-time guard for a decoded popup config.
     * Returns ['config' => config without not-installed module data, 'blocked' => slugs].
     * A module blocks a popup only when its policy is "block" AND the popup really uses it (in_use).
     */
    public static function prepare_render(array $config) {
        $res = ['config' => $config, 'blocked' => []];
        $s   = (isset($config['settings']) && is_array($config['settings'])) ? $config['settings'] : [];
        foreach (self::all() as $slug => $m) {
            if ($m['locked'] || self::is_installed($slug) || $m['on_missing'] !== 'block') {
                continue;
            }
            foreach ($m['in_use'] as $path) {
                if (self::truthy(self::path_value($s, $path))) {
                    $res['blocked'][] = $slug;
                    break;
                }
            }
        }
        if (isset($res['config']['settings']) && is_array($res['config']['settings'])) {
            foreach (array_keys(self::missing_keys()) as $k) {
                unset($res['config']['settings'][$k]);
            }
        }
        return $res;
    }

    /** Slugs of known modules that are not installed (used to keep their stored toggles). */
    public static function not_installed() {
        $out = [];
        foreach (self::all() as $slug => $m) {
            if (!$m['locked'] && !self::is_installed($slug)) {
                $out[] = $slug;
            }
        }
        return $out;
    }
}
