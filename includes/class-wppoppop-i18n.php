<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * WpPopPop internationalization bootstrap.
 *
 * - Loads the "wppoppop" text domain from /languages.
 * - Gives every WpPopPop script the wp-i18n dependency and JSON translations,
 *   so strings wrapped with wp.i18n.__() in admin/public JS are translatable.
 */
class WpPopPop_I18n {
    const DOMAIN = 'wppoppop';

    public function __construct() {
        add_action('init', [$this, 'load_textdomain'], 0);
        add_action('admin_enqueue_scripts', [$this, 'attach_script_translations'], 999);
        add_action('wp_enqueue_scripts', [$this, 'attach_script_translations'], 999);
        add_action('wp_print_scripts', [$this, 'attach_script_translations'], 0);
        add_action('wp_print_footer_scripts', [$this, 'attach_script_translations'], 0);
        add_action('admin_print_footer_scripts', [$this, 'attach_script_translations'], 0);
    }

    public static function languages_dir() {
        return WPPOPPOP_PATH . 'languages';
    }

    public function load_textdomain() {
        load_plugin_textdomain(self::DOMAIN, false, dirname(WPPOPPOP_BASENAME) . '/languages');
    }

    public function attach_script_translations() {
        $scripts = wp_scripts();
        if (!$scripts instanceof WP_Scripts) {
            return;
        }

        foreach ($scripts->registered as $handle => $script) {
            if (strpos($handle, 'wppoppop') !== 0 || !is_string($script->src) || strpos($script->src, WPPOPPOP_URL) !== 0) {
                continue;
            }
            if (!in_array('wp-i18n', $script->deps, true)) {
                $script->deps[] = 'wp-i18n';
            }
            if (empty($script->textdomain)) {
                wp_set_script_translations($handle, self::DOMAIN, self::languages_dir());
            }
        }
    }
}
