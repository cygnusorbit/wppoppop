<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * WpPopPop Gutenberg block: "WpPopPop Popup"
 * Same controls as the sidebar widget (Popup, Embed Type, Onload Delay),
 * rendered through the [wppoppop] shortcode so every path shares one code route.
 */
class WpPopPop_Widget_Block {
    public function __construct() {
        add_action('init', [$this, 'register_block']);
    }

    public function register_block() {
        if (!function_exists('register_block_type')) {
            return;
        }

        $rel    = 'admin/js/widget/widget-block.js';
        $file   = WPPOPPOP_PATH . $rel;
        $ver    = file_exists($file) ? (string) filemtime($file) : WPPOPPOP_VERSION;

        wp_register_script(
            'wppoppop-widget-block',
            WPPOPPOP_URL . $rel,
            ['wp-blocks', 'wp-element', 'wp-components', 'wp-block-editor', 'wp-i18n'],
            $ver,
            true
        );

        wp_localize_script('wppoppop-widget-block', 'wppoppopBlockData', [
            'popups' => $this->get_popups(),
        ]);

        register_block_type('wppoppop/popup', [
            'editor_script'   => 'wppoppop-widget-block',
            'render_callback' => [$this, 'render_block'],
            'attributes'      => [
                'uid'   => ['type' => 'string',  'default' => ''],
                'mode'  => ['type' => 'string',  'default' => 'inline'],
                'delay' => ['type' => 'integer', 'default' => 0],
            ],
        ]);
    }

    protected function get_popups() {
        global $wpdb;
        $table = $wpdb->prefix . 'wppoppop_items';
        if ($wpdb->get_var("SHOW TABLES LIKE '{$table}'") !== $table) {
            return [];
        }
        $rows = $wpdb->get_results("SELECT uid, title FROM {$table} WHERE status = 'publish' ORDER BY title ASC", ARRAY_A);
        $out  = [];
        foreach ((array) $rows as $r) {
            $out[] = ['uid' => $r['uid'], 'title' => $r['title']];
        }
        return $out;
    }

    /* Backend whitelist: only known attributes / values reach the shortcode */
    public function render_block($attrs) {
        $uid   = isset($attrs['uid']) ? sanitize_key($attrs['uid']) : '';
        $mode  = (isset($attrs['mode']) && in_array($attrs['mode'], ['inline', 'onload'], true)) ? $attrs['mode'] : 'inline';
        $delay = isset($attrs['delay']) ? max(0, min(3600, (int) $attrs['delay'])) : 0;
        if ($uid === '') {
            return '';
        }
        return do_shortcode('[wppoppop uid="' . esc_attr($uid) . '" mode="' . $mode . '" delay="' . $delay . '"]');
    }
}
