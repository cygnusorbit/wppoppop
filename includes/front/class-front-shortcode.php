<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * WpPopPop Front: [wppoppop] shortcode
 *   [wppoppop uid="pop_x"]                              inline embed (default)
 *   [wppoppop uid="pop_x" mode="button" text="Open"]    button that opens the popup
 *   [wppoppop uid="pop_x" mode="onload" delay="3"]   opens on page load after 3s
 *   [wppoppop id="12"]                                  numeric DB id also accepted
 */
class WpPopPop_Front_Shortcode {
    protected static $inline_uids = [];
    protected static $button_uids = [];
    /* WPPOPPOP_ONLOAD_REGISTRY: uid => delay (seconds) for widget "Onload Popup" mode */
    protected static $onload_uids = [];
    protected $renderer;

    public function __construct(WpPopPop_Front_Renderer $renderer) {
        $this->renderer = $renderer;
        add_shortcode('wppoppop', [$this, 'render']);
    }

    public static function is_button_requested($uid) {
        return isset(self::$button_uids[$uid]);
    }

    public static function register_onload($uid, $delay = 0) {
        self::$onload_uids[$uid] = max(0, (int) $delay);
    }

    public static function is_onload_requested($uid) {
        return isset(self::$onload_uids[$uid]);
    }

    public static function get_onload_delay($uid) {
        return isset(self::$onload_uids[$uid]) ? (int) self::$onload_uids[$uid] : 0;
    }

    public static function is_inline_rendered($uid) {
        return isset(self::$inline_uids[$uid]);
    }

    protected function find_row($uid, $id) {
        global $wpdb;
        $table = $wpdb->prefix . 'wppoppop_items';
        if ($uid !== '') {
            $row = $wpdb->get_row($wpdb->prepare("SELECT uid, data FROM {$table} WHERE uid = %s AND status = 'publish'", $uid), ARRAY_A);
            if (!$row && preg_match('/^(.+)-([a-z0-9]{6})$/', $uid, $m)) {
                /* RULE: uids are name-xxxxxx. Match on the 6-char suffix, then confirm the name ignoring hyphens. */
                $cands = $wpdb->get_results($wpdb->prepare("SELECT uid, data FROM {$table} WHERE uid LIKE %s AND status = 'publish' LIMIT 10", '%-' . $wpdb->esc_like($m[2])), ARRAY_A);
                $want  = preg_replace('/[^a-z0-9]/', '', $m[1]);
                foreach ((array) $cands as $c) {
                    if (preg_match('/^(.+)-([a-z0-9]{6})$/', $c['uid'], $cm) && preg_replace('/[^a-z0-9]/', '', $cm[1]) === $want) {
                        return $c;
                    }
                }
            }
            return $row;
        }
        if ($id > 0) {
            return $wpdb->get_row($wpdb->prepare("SELECT uid, data FROM {$table} WHERE id = %d AND status = 'publish'", $id), ARRAY_A);
        }
        return null;
    }

    public function render($atts) {
        $atts = shortcode_atts([
            'uid'   => '',
            'id'    => 0,
            'mode'  => 'inline',
            'text'  => __('Open Popup', 'wppoppop'),
            'class' => '',
            'delay' => 0,
        ], $atts, 'wppoppop');

        if (function_exists('wppoppop_get_setting') && (bool) wppoppop_get_setting('test_mode', false) && !current_user_can('manage_options')) {
            return '';
        }

        $uid = sanitize_key($atts['uid']);
        $id  = (int) $atts['id'];
        $row = $this->find_row($uid, $id);
        if (!$row && $uid !== '') {
            $aliases = get_option('wppoppop_uid_aliases', []);
            $hops = 0;
            while (!$row && is_array($aliases) && isset($aliases[$uid]) && $hops++ < 5) {
                $uid = $aliases[$uid];
                $row = $this->find_row($uid, 0);
            }
        }
        if (!$row) {
            return current_user_can('manage_options')
                ? '<!-- WpPopPop: popup not found or not published -->'
                : '';
        }
        $uid = $row['uid'];

        if ($atts['mode'] === 'onload') {
            /* WPPOPPOP_SC_ONLOAD: footer renderer prints the popup once and forces the load trigger */
            self::register_onload($uid, max(0, (int) $atts['delay']));
            return '';
        }

        if ($atts['mode'] === 'button') {
            self::$button_uids[$uid] = true;
            $extra = trim(preg_replace('/[^A-Za-z0-9_\- ]/', '', (string) $atts['class']));
            return '<button type="button" class="wppoppop-trigger-btn wppoppop-open-btn ' . esc_attr($extra) . '" data-target-uid="' . esc_attr($uid) . '">'
                . esc_html($atts['text']) . '</button>';
        }

        if (self::is_inline_rendered($uid)) {
            return '';
        }
        $config = json_decode($row['data'], true);
        if (!is_array($config)) {
            return '';
        }
        self::$inline_uids[$uid] = true;

        ob_start();
        $ret = $this->renderer->render_popup_markup($uid, $config, true, false);
        $buf = ob_get_clean();
        return $buf . (is_string($ret) ? $ret : '');
    }
}
