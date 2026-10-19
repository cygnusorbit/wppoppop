<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * WpPopPop Front: [wppoppop_ab uid="..."] shortcode.
 * Picks one variation of an A/B campaign and renders it through [wppoppop].
 * Visitors keep their variant via a 30-day cookie.
 */
class WpPopPop_Front_Ab {
    public function __construct() {
        add_shortcode('wppoppop_ab', [$this, 'render']);
    }

    protected function get_campaign($uid) {
        global $wpdb;
        return $wpdb->get_row(
            $wpdb->prepare("SELECT uid, popup_uids, status FROM {$wpdb->prefix}wppoppop_campaigns WHERE uid = %s", $uid),
            ARRAY_A
        );
    }

    protected function published_variants($uids) {
        global $wpdb;
        $uids = array_values(array_filter(array_map('sanitize_key', (array) $uids)));
        if (empty($uids)) {
            return [];
        }
        $ph   = implode(',', array_fill(0, count($uids), '%s'));
        $rows = $wpdb->get_col($wpdb->prepare(
            "SELECT uid FROM {$wpdb->prefix}wppoppop_items WHERE status = 'publish' AND uid IN ($ph)",
            $uids
        ));
        // keep the campaign's original order
        return array_values(array_intersect($uids, (array) $rows));
    }

    public function render($atts) {
        $atts = shortcode_atts([
            'uid'   => '',
            'mode'  => 'inline',
            'text'  => __('Open Popup', 'wppoppop'),
            'class' => '',
        ], $atts, 'wppoppop_ab');

        $uid = sanitize_key($atts['uid']);
        if ($uid === '') {
            return current_user_can('manage_options') ? '<!-- WpPopPop A/B: missing uid -->' : '';
        }

        $camp = $this->get_campaign($uid);
        if (!$camp || strtolower((string) $camp['status']) !== 'active') {
            return current_user_can('manage_options') ? '<!-- WpPopPop A/B: campaign not found or not active -->' : '';
        }

        $variants = $this->published_variants(json_decode($camp['popup_uids'], true));
        if (empty($variants)) {
            return current_user_can('manage_options') ? '<!-- WpPopPop A/B: no published variations -->' : '';
        }

        $cookie = 'wppoppop_ab_' . $uid;
        $chosen = isset($_COOKIE[$cookie]) ? sanitize_key(wp_unslash($_COOKIE[$cookie])) : '';
        if ($chosen === '' || !in_array($chosen, $variants, true)) {
            $chosen = $variants[array_rand($variants)];
            if (!headers_sent()) {
                setcookie($cookie, $chosen, time() + 30 * DAY_IN_SECONDS, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN, is_ssl(), true);
            }
            $_COOKIE[$cookie] = $chosen;
        }

        $sc = '[wppoppop uid="' . esc_attr($chosen) . '" mode="' . esc_attr($atts['mode']) . '"';
        if ($atts['mode'] === 'button') {
            $sc .= ' text="' . esc_attr($atts['text']) . '" class="' . esc_attr($atts['class']) . '"';
        }
        $sc .= ']';
        return do_shortcode($sc);
    }
}
