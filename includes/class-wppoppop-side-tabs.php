<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Side Tabs module: sanitize (save) + front attributes (render).
 * Shape: settings.sidetab = { enable: bool, label: string, pos: 'left'|'right' }
 * The tab itself is built in the browser by the package's module.js from the data-sidetab-* attributes.
 */
class WpPopPop_Side_Tabs {

    const MODULE_KEY = 'acc-side-tabs';
    const MAX_LABEL  = 40;

    public static function defaults() {
        return array('enable' => false, 'label' => '', 'pos' => 'left');
    }

    /** Clean, allow-listed config from a saved or posted value (object or array). */
    public static function sanitize($raw) {
        if (is_object($raw)) {
            $raw = get_object_vars($raw);
        }
        if (!is_array($raw)) {
            $raw = array();
        }
        $label = (isset($raw['label']) && is_scalar($raw['label'])) ? sanitize_text_field((string) $raw['label']) : '';
        $label = function_exists('mb_substr') ? mb_substr($label, 0, self::MAX_LABEL) : substr($label, 0, self::MAX_LABEL);
        $pos   = (isset($raw['pos']) && is_scalar($raw['pos'])) ? (string) $raw['pos'] : 'left';
        return array(
            'enable' => isset($raw['enable']) && in_array($raw['enable'], array(true, 1, '1', 'true', 'on'), true),
            'label'  => $label,
            'pos'    => $pos === 'right' ? 'right' : 'left',
        );
    }

    /** Settings > Module switch. A module that is switched off or deleted never affects the front end. */
    public static function module_active() {
        if (!class_exists('WpPopPop_Modules')) {
            return true;
        }
        /* Not registered: the bundled package is seeded on the first admin load, so stay on until then. A deleted module is off. */
        if (!WpPopPop_Modules::get(self::MODULE_KEY)) {
            $orphans = WpPopPop_Modules::orphans();
            return !isset($orphans[self::MODULE_KEY]);
        }
        if (!class_exists('WpPopPop_Admin_Settings')) {
            require_once WPPOPPOP_PATH . 'includes/admin/class-admin-settings.php';
        }
        return WpPopPop_Admin_Settings::module_enabled(self::MODULE_KEY);
    }

    /** Escaped data attributes for the popup wrapper, or '' (module off / tab not enabled). */
    public static function frontend($settings) {
        if (!is_array($settings) || !isset($settings['sidetab']) || !is_array($settings['sidetab'])) {
            return '';
        }
        $t = self::sanitize($settings['sidetab']);
        if (!$t['enable'] || !self::module_active()) {
            return '';
        }
        $label = ($t['label'] !== '') ? $t['label'] : __('Offer', 'wppoppop');
        return ' data-sidetab="' . esc_attr($t['pos']) . '" data-sidetab-label="' . esc_attr($label) . '"';
    }
}
