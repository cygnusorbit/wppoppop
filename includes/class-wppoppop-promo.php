<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Promotional placements driven by defaults/promo.json.
 * Core only: every slot disappears while a companion plugin is active (WpPopPop_Pro_API::is_active()).
 * Filters: wppoppop_promo_file (path), wppoppop_promo_config (array).
 */
class WpPopPop_Promo {

    const MAX_SLIDES = 8;
    const PARENT     = 'wppoppop-dashboard';

    private static $config = null;

    public static function file() {
        $file = WPPOPPOP_PATH . 'defaults/promo.json';
        if (!is_readable($file) && is_readable(WPPOPPOP_PATH . 'promo/promo.json')) {
            $file = WPPOPPOP_PATH . 'promo/promo.json'; // older location
        }
        return (string) apply_filters('wppoppop_promo_file', $file);
    }

    public static function config() {
        if (self::$config !== null) {
            return self::$config;
        }
        $cfg  = array('enabled' => false, 'slots' => array());
        $file = self::file();
        if ($file !== '' && is_readable($file)) {
            $raw  = file_get_contents($file); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local plugin file
            $json = is_string($raw) ? json_decode($raw, true) : null;
            if (is_array($json)) {
                $cfg = array(
                    'enabled' => !empty($json['enabled']),
                    'remember_dismiss' => !array_key_exists('remember_dismiss', $json) || !empty($json['remember_dismiss']),
                    'show_close' => !array_key_exists('show_close', $json) || !empty($json['show_close']),
                    'slots'   => (isset($json['slots']) && is_array($json['slots'])) ? $json['slots'] : array(),
                );
            }
        }
        $cfg = apply_filters('wppoppop_promo_config', $cfg);
        self::$config = is_array($cfg) ? $cfg : array('enabled' => false, 'slots' => array());
        return self::$config;
    }

    /** False while Pro is active or when the master switch is off. */
    public static function visible() {
        if (class_exists('WpPopPop_Pro_API') && WpPopPop_Pro_API::is_active()) {
            return false;
        }
        $cfg = self::config();
        return !empty($cfg['enabled']);
    }

    private static function ver() {
        return defined('WPPOPPOP_VERSION') ? WPPOPPOP_VERSION : '1.0';
    }

    /** Plugin pages only. The script loads in <head> so closed items are hidden before first paint (no flash). */
    public static function enqueue() {
        if (!self::visible()) {
            return;
        }
        wp_enqueue_style('dashicons');
        wp_enqueue_style('wppoppop-promo-css', WPPOPPOP_URL . 'admin/css/promo.css', array('wppoppop-theme-global-css'), self::ver());
        wp_enqueue_script('wppoppop-promo-js', WPPOPPOP_URL . 'admin/js/promo.js', array(), self::ver(), false);

        // Items that must come back after every refresh: any stored "closed" record for them is ignored and purged.
        $volatile = array();
        foreach (array('header_text', 'builder_box', 'dashboard_slider') as $slot) {
            $it = self::item($slot);
            if ($it && (empty($it['remember']) || empty($it['dismissible']))) { // no close button: ignore any old "closed" record
                $volatile[] = $it['id'];
            }
        }
        if ($volatile) {
            wp_add_inline_script('wppoppop-promo-js', 'window.wppoppopPromo=' . wp_json_encode(array('volatile' => $volatile)) . ';', 'before');
        }
    }

    /* ------------------------------------------------------------ admin menu link */

    /** Appends the link after the menu preferences are applied, so it never shows up in Settings > Pages. */
    public static function register_menu() {
        global $submenu;
        if (empty($submenu[self::PARENT]) || !is_array($submenu[self::PARENT])) {
            return;
        }
        $item = self::item('menu_item');
        if (!$item) {
            return;
        }
        $label = wp_strip_all_tags($item['text']);
        // WordPress prints a URL used as the menu slug as-is, so it is escaped here.
        $submenu[self::PARENT][] = array(esc_html($label), 'manage_options', esc_url($item['url']), $label, 'wpp-go-pro');
    }

    private static function menu_primary() {
        $color = '#c1272d';
        if (class_exists('WpPopPop_Admin_Theme')) {
            $v = WpPopPop_Admin_Theme::get_values();
            $c = isset($v['c_primary']) ? sanitize_hex_color($v['c_primary']) : '';
            if ($c) {
                $color = $c;
            }
        }
        return $color;
    }

    /** Colours and optional new-tab behaviour for the menu link (every admin screen shows the menu). */
    public static function enqueue_menu() {
        if (!current_user_can('manage_options')) {
            return;
        }
        $item = self::item('menu_item');
        if (!$item) {
            return;
        }
        $bg = $item['bg_color'] !== '' ? $item['bg_color'] : self::menu_primary();
        $fg = $item['text_color'] !== '' ? $item['text_color'] : '#ffffff';
        $css = '#adminmenu .wp-submenu a.wpp-go-pro{display:block;background:' . $bg . ';color:' . $fg . ';font-weight:600;border-radius:3px}'
            . '#adminmenu .wp-submenu a.wpp-go-pro:hover,#adminmenu .wp-submenu a.wpp-go-pro:focus{background:' . $bg . ';color:' . $fg . ';-webkit-filter:brightness(1.12);filter:brightness(1.12)}';
        wp_register_style('wppoppop-promo-menu', false, array(), self::ver());
        wp_enqueue_style('wppoppop-promo-menu');
        wp_add_inline_style('wppoppop-promo-menu', $css);

        if (!empty($item['new_tab'])) {
            $js = "(function(){var l=document.querySelectorAll('#adminmenu a.wpp-go-pro');for(var i=0;i<l.length;i++){l[i].setAttribute('target','_blank');l[i].setAttribute('rel','noopener noreferrer');}})();";
            wp_register_script('wppoppop-promo-menu', false, array(), self::ver(), true);
            wp_enqueue_script('wppoppop-promo-menu');
            wp_add_inline_script('wppoppop-promo-menu', $js);
        }
    }

    /* ------------------------------------------------------------ sanitising */

    private static function allowed_html() {
        return array(
            'a'      => array('href' => true, 'target' => true, 'rel' => true, 'class' => true),
            'strong' => array(),
            'b'      => array(),
            'em'     => array(),
            'i'      => array(),
            'br'     => array(),
        );
    }

    private static function color_keys() {
        return array('text_color', 'bg_color', 'cta_text_color', 'cta_bg_color');
    }

    private static function str($arr, $key) {
        return (isset($arr[$key]) && is_scalar($arr[$key])) ? trim((string) $arr[$key]) : '';
    }

    private static function flag($arr, $key, $default) {
        return array_key_exists($key, $arr) ? !empty($arr[$key]) : $default;
    }

    private static function color($arr, $key) {
        $c = sanitize_hex_color(self::str($arr, $key));
        return $c ? $c : '';
    }

    private static function image($v) {
        if ($v === '' || strpos($v, '..') !== false) {
            return '';
        }
        if (preg_match('#^(https?:)?//#i', $v)) {
            return esc_url_raw($v, array('http', 'https'));
        }
        return WPPOPPOP_URL . 'promo/' . ltrim($v, '/\\');
    }

    private static function in_window($raw) {
        $today = wp_date('Y-m-d');
        $start = self::str($raw, 'start');
        $end   = self::str($raw, 'end');
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $start) && $today < $start) {
            return false;
        }
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $end) && $today > $end) {
            return false;
        }
        return true;
    }

    private static function entry($raw) {
        $e = array(
            'title'   => wp_strip_all_tags(self::str($raw, 'title')),
            'text'    => wp_kses(self::str($raw, 'text'), self::allowed_html()),
            'cta'     => wp_strip_all_tags(self::str($raw, 'cta')),
            'url'     => esc_url_raw(self::str($raw, 'url'), array('http', 'https')),
            'new_tab' => self::flag($raw, 'new_tab', true),
            'image'   => self::image(self::str($raw, 'image')),
        );
        foreach (self::color_keys() as $k) {
            $e[$k] = self::color($raw, $k);
        }
        return $e;
    }

    /** Normalised slot data, or null when the slot must not be shown. */
    public static function item($slot) {
        if (!self::visible()) {
            return null;
        }
        $cfg = self::config();
        if (empty($cfg['slots'][$slot]) || !is_array($cfg['slots'][$slot])) {
            return null;
        }
        $raw = $cfg['slots'][$slot];
        if (!self::flag($raw, 'enabled', true) || !self::in_window($raw)) {
            return null;
        }

        $item = self::entry($raw);
        if ($slot === 'menu_item') {
            $item['dismissible'] = false;
        } else {
            // show_close: slot value, else the older "dismissible" key, else the top-level value
            $show = !isset($cfg['show_close']) || !empty($cfg['show_close']);
            if (!array_key_exists('show_close', $raw) && array_key_exists('dismissible', $raw)) {
                $show = !empty($raw['dismissible']);
            }
            $item['dismissible'] = self::flag($raw, 'show_close', $show);
        }
        $item['reshow_days'] = max(0, min(365, (int) (isset($raw['reshow_days']) && is_scalar($raw['reshow_days']) ? $raw['reshow_days'] : 0)));
        // false = closing only hides the item until the next refresh
        $item['remember']    = ($slot === 'menu_item') ? true : self::flag($raw, 'remember_dismiss', !isset($cfg['remember_dismiss']) || !empty($cfg['remember_dismiss']));

        if ($slot === 'dashboard_slider') {
            $slides = array();
            if (isset($raw['slides']) && is_array($raw['slides'])) {
                foreach ($raw['slides'] as $s) {
                    if (!is_array($s) || count($slides) >= self::MAX_SLIDES) {
                        continue;
                    }
                    $e = self::entry($s);
                    if ($e['title'] === '' && $e['text'] === '' && $e['image'] === '') {
                        continue;
                    }
                    foreach (self::color_keys() as $k) {
                        if ($e[$k] === '') {
                            $e[$k] = $item[$k];
                        }
                    }
                    $slides[] = $e;
                }
            }
            if (!$slides) {
                return null;
            }
            $item['slides']   = $slides;
            $item['interval'] = max(2000, min(30000, (int) (isset($raw['interval']) ? $raw['interval'] : 6000)));
        } elseif ($slot === 'menu_item') {
            if ($item['text'] === '' || $item['url'] === '') {
                return null;
            }
        } elseif ($slot === 'header_text') {
            if ($item['text'] === '') {
                return null;
            }
        } elseif ($item['title'] === '' && $item['text'] === '') {
            return null;
        }

        $id = sanitize_key(self::str($raw, 'id'));
        if ($id === '') {
            $id = substr(md5((string) wp_json_encode($item)), 0, 12);
        }
        $item['id'] = $slot . '-' . $id;
        return $item;
    }

    /* ------------------------------------------------------------ rendering */

    private static function vars($e) {
        $map = array(
            'text_color'     => '--wpp-promo-fg',
            'bg_color'       => '--wpp-promo-bg',
            'cta_text_color' => '--wpp-promo-cta-fg',
            'cta_bg_color'   => '--wpp-promo-cta-bg',
        );
        $css = '';
        foreach ($map as $k => $var) {
            if (!empty($e[$k])) {
                $css .= $var . ':' . $e[$k] . ';';
            }
        }
        return $css === '' ? '' : ' style="' . esc_attr($css) . '"';
    }

    private static function link($e, $class) {
        if ($e['cta'] === '' || $e['url'] === '') {
            return '';
        }
        $attr = $e['new_tab'] ? ' target="_blank" rel="noopener noreferrer"' : '';
        return '<a class="' . esc_attr($class) . '" href="' . esc_url($e['url']) . '"' . $attr . '>' . esc_html($e['cta']) . '</a>';
    }

    private static function dismiss_button($item) {
        if (empty($item['dismissible'])) {
            return;
        }
        echo '<button type="button" class="wpp-promo-x" data-wpp-promo-dismiss="1" aria-label="' . esc_attr__('Dismiss', 'wppoppop') . '"><span class="dashicons dashicons-no-alt" aria-hidden="true"></span></button>';
    }

    private static function root_attrs($item) {
        return ' data-wpp-promo-id="' . esc_attr($item['id']) . '" data-wpp-promo-days="' . (int) $item['reshow_days'] . '"' . (empty($item['remember']) ? ' data-wpp-promo-remember="0"' : '') . self::vars($item);
    }

    public static function render($slot) {
        if ($slot === 'menu_item') {
            return;
        }
        $item = self::item($slot);
        if (!$item) {
            return;
        }
        $allowed = self::allowed_html();

        if ($slot === 'header_text') {
            echo '<div class="cell promo"' . self::root_attrs($item) . '>';
            echo '<span class="wpp-promo-text">' . wp_kses($item['text'], $allowed) . '</span>';
            echo wp_kses(self::link($item, 'wpp-promo-cta'), $allowed);
            self::dismiss_button($item);
            echo '</div>';
            return;
        }

        if ($slot === 'builder_box') {
            echo '<div class="wppoppop-promo wppoppop-promo--box"' . self::root_attrs($item) . '>';
            self::dismiss_button($item);
            if ($item['image'] !== '') {
                echo '<img class="wpp-promo-img" src="' . esc_url($item['image']) . '" alt="" loading="lazy">';
            }
            if ($item['title'] !== '') {
                echo '<strong class="wpp-promo-title">' . esc_html($item['title']) . '</strong>';
            }
            if ($item['text'] !== '') {
                echo '<p class="wpp-promo-body">' . wp_kses($item['text'], $allowed) . '</p>';
            }
            echo wp_kses(self::link($item, 'button button-primary wpp-promo-btn'), $allowed);
            echo '</div>';
            return;
        }

        if ($slot === 'dashboard_slider') {
            $total = count($item['slides']);
            // Same box markup as the other dashboard boxes, so dash-layout.js can drag it.
            echo '<div class="wppoppop-box wppoppop-promo wpp-promo-slider" id="wppoppop-box-promo" data-box-title="' . esc_attr__('Promotions', 'wppoppop') . '"' . self::root_attrs($item) . ' data-interval="' . (int) $item['interval'] . '" role="region" aria-roledescription="carousel" aria-label="' . esc_attr__('Promotions', 'wppoppop') . '">';
            self::dismiss_button($item);
            foreach ($item['slides'] as $n => $s) {
                $has = ($s['image'] !== '');
                echo '<div class="wpp-promo-slide' . ($n === 0 ? ' is-active' : '') . ($has ? ' has-img' : '') . '"' . self::vars($s) . ' role="group" aria-roledescription="slide" aria-label="' . esc_attr(($n + 1) . ' / ' . $total) . '"' . ($n === 0 ? '' : ' aria-hidden="true"') . '>';
                if ($has) {
                    echo '<img class="wpp-promo-bg" src="' . esc_url($s['image']) . '" alt="" loading="lazy">';
                }
                echo '<div class="wpp-promo-slide-body">';
                if ($s['title'] !== '') {
                    echo '<strong class="wpp-promo-title">' . esc_html($s['title']) . '</strong>';
                }
                if ($s['text'] !== '') {
                    echo '<p class="wpp-promo-body">' . wp_kses($s['text'], $allowed) . '</p>';
                }
                echo wp_kses(self::link($s, 'button button-primary wpp-promo-btn'), $allowed);
                echo '</div></div>';
            }
            if ($total > 1) {
                echo '<button type="button" class="wpp-promo-nav wpp-promo-prev" aria-label="' . esc_attr__('Previous', 'wppoppop') . '"><span class="dashicons dashicons-arrow-left-alt2" aria-hidden="true"></span></button>';
                echo '<button type="button" class="wpp-promo-nav wpp-promo-next" aria-label="' . esc_attr__('Next', 'wppoppop') . '"><span class="dashicons dashicons-arrow-right-alt2" aria-hidden="true"></span></button>';
                echo '<div class="wpp-promo-dots">';
                for ($d = 0; $d < $total; $d++) {
                    echo '<button type="button" class="wpp-promo-dot' . ($d === 0 ? ' is-active' : '') . '" aria-label="' . esc_attr(($d + 1) . ' / ' . $total) . '"></button>';
                }
                echo '</div>';
            }
            echo '</div>';
        }
    }
}
