<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Declarative module engine.
 * A module that declares "fields" in module.json gets, with no PHP of its own:
 *   Inspector accordion (render_accordion) -> JS model (builder-modules-ext.js) ->
 *   save whitelist (sanitize_save) -> frontend payload (front) -> module.css / module.js.
 * Values live in settings.ext_<slug with - as _>.
 */
class WpPopPop_Modules_Engine {

    protected static $front_ready = [];

    /** Installed, compatible, declarative modules (slug => manifest). */
    public static function installed_declarative() {
        $out = [];
        foreach (WpPopPop_Modules::installed() as $slug => $m) {
            if ($m['declarative'] && $m['compatible']) {
                $out[$slug] = $m;
            }
        }
        return $out;
    }

    /** Installed, compatible modules that draw an Inspector accordion from the registry (fields or accordion.html). */
    public static function installed_ui() {
        $out = [];
        foreach (WpPopPop_Modules::installed() as $slug => $m) {
            if (($m['declarative'] || !empty($m['html'])) && $m['compatible'] && WpPopPop_Modules::ui_ok($m)) {
                $out[$slug] = $m;
            }
        }
        return $out;
    }

    /** Markup an accordion.html may use. Everything else (script, iframe, style, event attributes) is stripped. */
    protected static function html_allowed() {
        $c = ['class' => true, 'id' => true, 'title' => true];
        return [
            'div'      => $c + ['data-for' => true],
            'span'     => $c,
            'small'    => $c,
            'strong'   => $c,
            'em'       => $c,
            'p'        => $c,
            'br'       => [],
            'hr'       => $c,
            'label'    => $c + ['for' => true],
            'input'    => $c + ['type' => true, 'name' => true, 'value' => true, 'min' => true, 'max' => true, 'step' => true, 'placeholder' => true, 'checked' => true, 'maxlength' => true, 'data-for' => true],
            'select'   => $c + ['name' => true],
            'option'   => ['value' => true, 'selected' => true],
            'textarea' => $c + ['name' => true, 'rows' => true, 'maxlength' => true, 'placeholder' => true],
        ];
    }

    /** Accordion shell (header + body) around a module's accordion.html. */
    public static function render_html($m) {
        $file = $m['dir'] . '/accordion.html';
        $raw  = (is_file($file) && !is_link($file) && is_readable($file) && filesize($file) <= WpPopPop_Modules::MAX_HTML) ? (string) file_get_contents($file) : '';
        $h    = '<div class="wppoppop-acc-item" data-html-module="' . esc_attr($m['slug']) . '">';
        $h   .= '<button type="button" class="wppoppop-acc-header">' . esc_html($m['name']) . '</button>';
        $h   .= '<div class="wppoppop-acc-body" style="display:none;">' . wp_kses($raw, self::html_allowed()) . '</div>';
        return $h . '</div>';
    }

    /** ext settings key => manifest, for every known declarative module (installed or not). */
    public static function by_key() {
        $out = [];
        foreach (WpPopPop_Modules::all() as $m) {
            if ($m['declarative']) {
                $out[$m['ext_key']] = $m;
            }
        }
        return $out;
    }

    protected static function truthy($v) {
        if (is_string($v)) {
            $v = strtolower(trim($v));
            return !in_array($v, ['', '0', 'false', 'off', 'no'], true);
        }
        return (bool) $v;
    }

    /** Clean one value by field type. Falls back to the field default. */
    public static function sanitize_value($f, $v, $use_default = true) {
        $def = $use_default ? self::default_value($f) : null;
        switch ($f['type']) {
            case 'toggle':
                return self::truthy($v);
            case 'number':
                if (!is_scalar($v) || !is_numeric($v)) {
                    return $def;
                }
                $n = max($f['min'], min($f['max'], $v + 0));
                return (is_int($f['step']) && floor($n) == $n) ? (int) $n : (float) $n;
            case 'select':
                return (is_scalar($v) && array_key_exists((string) $v, $f['options'])) ? (string) $v : $def;
            case 'color':
                return (is_string($v) && preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $v) === 1) ? strtolower($v) : $def;
            case 'url':
                if (!is_string($v)) {
                    return $def;
                }
                return mb_substr((string) esc_url_raw(trim($v)), 0, $f['max_length']);
            case 'textarea':
                return is_scalar($v) ? mb_substr((string) sanitize_textarea_field((string) $v), 0, $f['max_length']) : $def;
            default:
                return is_scalar($v) ? mb_substr((string) sanitize_text_field((string) $v), 0, $f['max_length']) : $def;
        }
    }

    public static function default_value($f) {
        $d = $f['default'];
        switch ($f['type']) {
            case 'toggle':
                return self::truthy($d);
            case 'number':
                $n = (is_numeric($d) ? $d + 0 : $f['min']);
                $n = max($f['min'], min($f['max'], $n));
                return (is_int($f['step']) && floor($n) == $n) ? (int) $n : (float) $n;
            case 'select':
                return (is_scalar($d) && array_key_exists((string) $d, $f['options'])) ? (string) $d : (string) array_key_first($f['options']);
            case 'color':
                return (is_string($d) && preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $d) === 1) ? strtolower($d) : '#000000';
            default:
                return is_scalar($d) ? self::sanitize_value($f, (string) $d, false) : '';
        }
    }

    /** Full, clean value map for one module (missing fields get defaults, unknown keys are dropped). */
    public static function sanitize($m, $raw) {
        $raw = is_object($raw) ? (array) $raw : (is_array($raw) ? $raw : []);
        $out = [];
        foreach ($m['fields'] as $f) {
            $out[$f['key']] = array_key_exists($f['key'], $raw) ? self::sanitize_value($f, $raw[$f['key']]) : self::default_value($f);
        }
        return $out;
    }

    /**
     * Save whitelist for settings.ext_* keys (object, edited in place).
     * Installed module: sanitized. Not-installed module: left for the lifecycle guard.
     * Unknown ext_* key: removed.
     */
    public static function sanitize_save($settings) {
        if (!is_object($settings)) {
            return $settings;
        }
        $known = self::by_key();
        foreach (array_keys(get_object_vars($settings)) as $k) {
            if (strpos($k, 'ext_') !== 0) {
                continue;
            }
            if (!isset($known[$k])) {
                unset($settings->$k);
                continue;
            }
            if (WpPopPop_Modules::is_installed($known[$k]['slug'])) {
                $settings->$k = (object) self::sanitize($known[$k], $settings->$k);
            }
        }
        return $settings;
    }

    /** Inspector accordion markup for one declarative module (already escaped). */
    public static function render_accordion($m) {
        if (!empty($m['html'])) {
            return self::render_html($m);
        }
        $name = ($m['source'] === 'core') ? translate($m['name'], 'wppoppop') : $m['name'];
        $id   = $m['ext_key'];
        $h    = '<div class="wppoppop-acc-item" data-ext-module="' . esc_attr($id) . '">';
        $h   .= '<button type="button" class="wppoppop-acc-header">' . esc_html($name) . '</button>';
        $h   .= '<div class="wppoppop-acc-body" style="display:none;">';
        foreach ($m['fields'] as $f) {
            $fid  = 'set-' . str_replace('_', '-', $id) . '-' . str_replace('_', '-', $f['key']);
            $def  = self::default_value($f);
            $attr = ' id="' . esc_attr($fid) . '" data-ext-field="' . esc_attr($f['key']) . '" data-ext-type="' . esc_attr($f['type']) . '" data-ext-default="' . esc_attr(is_bool($def) ? ($def ? '1' : '0') : (string) $def) . '"';
            $lab  = esc_html($f['label']);
            if ($f['type'] === 'toggle') {
                $h .= '<div style="margin-top:8px;"><label><input type="checkbox" value="1"' . $attr . '> ' . $lab . '</label></div>';
            } else {
                $h .= '<div style="margin-top:8px;"><label for="' . esc_attr($fid) . '">' . $lab . '</label>';
                if ($f['type'] === 'select') {
                    $h .= '<select class="widefat"' . $attr . '>';
                    foreach ($f['options'] as $ov => $ol) {
                        $h .= '<option value="' . esc_attr($ov) . '">' . esc_html($ol) . '</option>';
                    }
                    $h .= '</select>';
                } elseif ($f['type'] === 'textarea') {
                    $h .= '<textarea class="widefat" rows="3" maxlength="' . (int) $f['max_length'] . '"' . $attr . '></textarea>';
                } elseif ($f['type'] === 'number') {
                    $h .= '<input type="number" class="widefat" min="' . esc_attr($f['min']) . '" max="' . esc_attr($f['max']) . '" step="' . esc_attr($f['step']) . '"' . $attr . '>';
                } elseif ($f['type'] === 'color') {
                    $h .= '<input type="color"' . $attr . '>';
                } elseif ($f['type'] === 'url') {
                    $h .= '<input type="url" class="widefat" maxlength="' . (int) $f['max_length'] . '"' . $attr . '>';
                } else {
                    $h .= '<input type="text" class="widefat" maxlength="' . (int) $f['max_length'] . '"' . $attr . '>';
                }
                if ($f['help'] !== '') {
                    $h .= '<p style="margin:3px 0 0;font-size:11px;color:#64748b;">' . esc_html($f['help']) . '</p>';
                }
                $h .= '</div>';
            }
        }
        return $h . '</div></div>';
    }

    protected static function ver($m) {
        return $m['version'];
    }

    /** Builder (admin) assets: JS model bridge plus each installed module's module.css / admin.js. */
    public static function enqueue_builder() {
        wp_enqueue_script('wppoppop-builder-ext-js', WPPOPPOP_URL . 'admin/js/builder/builder-modules-ext.js', ['jquery', 'wppoppop-builder-core-js', 'wppoppop-builder-settings-js'], WPPOPPOP_VERSION, true);
        foreach (self::installed_ui() as $slug => $m) {
            $url = WpPopPop_Modules::module_url($m);
            if ($url === '') {
                continue;
            }
            if ($m['assets']['css']) {
                wp_enqueue_style('wppoppop-ext-' . $slug, $url . 'module.css', [], self::ver($m));
            }
            if ($m['assets']['admin_js']) {
                wp_enqueue_script('wppoppop-ext-' . $slug . '-admin', $url . 'admin.js', ['jquery', 'wppoppop-builder-ext-js'], self::ver($m), true);
            }
        }
    }

    /**
     * Frontend: for every installed + switched-on declarative module that this popup has data for,
     * expose the clean values to window.WpPopPopExt and load module.css / module.js once.
     */
    public static function front($uid, array $config) {
        $s = (isset($config['settings']) && is_array($config['settings'])) ? $config['settings'] : [];
        if (!$s || $uid === '' || !is_string($uid)) {
            return;
        }
        foreach (self::installed_declarative() as $slug => $m) {
            if (!isset($s[$m['ext_key']]) || !is_array($s[$m['ext_key']])) {
                continue;
            }
            if (class_exists('WpPopPop_Admin_Settings') && !WpPopPop_Admin_Settings::module_enabled($slug)) {
                continue;
            }
            self::emit($uid, $m, self::sanitize($m, $s[$m['ext_key']]));
        }
        self::front_html($s);
    }

    /** Installed accordion.html modules that ship a module.js: load it once when this popup holds data for the module. */
    protected static function front_html(array $s) {
        foreach (WpPopPop_Modules::installed() as $slug => $m) {
            if ($m['declarative'] || empty($m['html']) || !$m['compatible'] || empty($m['assets']['js']) || !WpPopPop_Modules::ui_ok($m)) {
                continue;
            }
            if (isset(self::$front_ready[$slug . ':html'])) {
                continue;
            }
            $has = false;
            foreach ($m['data_keys'] as $k) {
                if (!empty($s[$k])) {
                    $has = true;
                    break;
                }
            }
            $url = WpPopPop_Modules::module_url($m);
            if (!$has || $url === '') {
                continue;
            }
            self::$front_ready[$slug . ':html'] = true;
            $deps = ['jquery'];
            if (wp_script_is('wppoppop-front-triggers-js', 'registered')) {
                $deps[] = 'wppoppop-front-triggers-js';
            }
            wp_enqueue_script('wppoppop-ext-' . $slug . '-front', $url . 'module.js', $deps, self::ver($m), true);
        }
    }

    protected static function emit($uid, $m, $vals) {
        if (!wp_script_is('wppoppop-ext', 'registered')) {
            wp_register_script('wppoppop-ext', WPPOPPOP_URL . 'public/js/front/front-ext.js', ['jquery'], WPPOPPOP_VERSION, true);
        }
        wp_enqueue_script('wppoppop-ext');
        $flags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
        wp_add_inline_script(
            'wppoppop-ext',
            'window.WpPopPopExt=window.WpPopPopExt||{data:{},h:{}};(WpPopPopExt.data[' . wp_json_encode($uid, $flags) . ']=WpPopPopExt.data[' . wp_json_encode($uid, $flags) . ']||{})[' . wp_json_encode($m['slug'], $flags) . ']=' . wp_json_encode($vals, $flags) . ';',
            'before'
        );
        if (isset(self::$front_ready[$m['slug']])) {
            return;
        }
        self::$front_ready[$m['slug']] = true;
        $url = WpPopPop_Modules::module_url($m);
        if ($url === '') {
            return;
        }
        if ($m['assets']['css']) {
            wp_enqueue_style('wppoppop-ext-' . $m['slug'], $url . 'module.css', [], self::ver($m));
        }
        if ($m['assets']['js']) {
            wp_enqueue_script('wppoppop-ext-' . $m['slug'], $url . 'module.js', ['jquery', 'wppoppop-ext'], self::ver($m), true);
        }
    }
}
