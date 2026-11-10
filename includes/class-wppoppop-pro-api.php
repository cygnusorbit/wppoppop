<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Extension API for companion plugins.
 * Core contains no licence or paid-feature code; an add-on reports itself through these filters.
 */
class WpPopPop_Pro_API {

    const API_VERSION = 1;
    const ROOT_KINDS  = array('module', 'preset');

    public static function api_version() {
        return self::API_VERSION;
    }

    /** True while a companion plugin announces itself (define WPPOPPOP_PRO_VERSION or use the filter). */
    public static function is_active() {
        return (bool) apply_filters('wppoppop_pro_active', defined('WPPOPPOP_PRO_VERSION'));
    }

    /** True only when the companion plugin reports a valid licence. */
    public static function is_licensed() {
        return self::is_active() && (bool) apply_filters('wppoppop_pro_licensed', false);
    }

    /** Per-feature switch; the companion plugin decides, core defaults to $default. */
    public static function feature($key, $default = false) {
        return (bool) apply_filters('wppoppop_feature_enabled', (bool) $default, sanitize_key((string) $key));
    }

    /**
     * Extra folders a companion plugin contributes. Filter: wppoppop_{kind}_roots
     * Each item: array('path' => absolute folder, 'url' => public URL of that folder).
     *
     * @return array[] list of array('path' => string, 'url' => string with trailing slash)
     */
    public static function roots($kind) {
        $kind = sanitize_key((string) $kind);
        if (!in_array($kind, self::ROOT_KINDS, true)) {
            return array();
        }
        $list = apply_filters('wppoppop_' . $kind . '_roots', array());
        $out  = array();
        if (!is_array($list)) {
            return $out;
        }
        foreach ($list as $r) {
            if (!is_array($r) || empty($r['path']) || empty($r['url']) || !is_string($r['path']) || !is_string($r['url'])) {
                continue;
            }
            $path = rtrim($r['path'], '/\\');
            $url  = esc_url_raw($r['url']);
            if ($path === '' || $url === '' || !is_dir($path)) {
                continue;
            }
            $out[] = array('path' => $path, 'url' => trailingslashit($url));
        }
        return $out;
    }
}
