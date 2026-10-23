<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WpPopPop_Security') && file_exists(WPPOPPOP_PATH . 'includes/class-wppoppop-security.php')) {
    require_once WPPOPPOP_PATH . 'includes/class-wppoppop-security.php';
}

/**
 * Retrieve a WpPopPop setting with static in-memory caching.
 */
function wppoppop_settings_store($reset = false) {
    static $settings = null;
    if ($reset) {
        $settings = null;
        return [];
    }
    if ($settings === null) {
        $settings = get_option('wppoppop_settings', []);
        if (!is_array($settings)) {
            $settings = [];
        }
    }
    return $settings;
}

function wppoppop_get_setting($key, $default = '') {
    $settings = wppoppop_settings_store();
    return isset($settings[$key]) ? $settings[$key] : $default;
}

/**
 * Flush in-memory settings cache on update.
 */
function wppoppop_flush_settings_cache() {
    wppoppop_settings_store(true);
}

/**
 * Strip comments and whitespace from inline CSS strings.
 */
function wppoppop_minify_css($css) {
    if (empty($css) || !is_string($css)) {
        return '';
    }
    $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
    $css = str_replace([': ', ' {', '{ ', ' }', '} ', '; '], [':', '{', '{', '}', '}', ';'], $css);
    $css = preg_replace('/\s+/', ' ', $css);
    return trim($css);
}

/**
 * System event logger for auditing and telemetry.
 */
function wppoppop_log_event($event_type, $message, $payload = []) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'wppoppop_logs';
    if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") !== $table_name) {
        return false;
    }
    return $wpdb->insert(
        $table_name,
        [
            'event_type' => sanitize_key($event_type),
            'message'    => sanitize_text_field($message),
            'payload'    => !empty($payload) ? wp_json_encode($payload) : null,
            'created_at' => current_time('mysql')
        ],
        ['%s', '%s', '%s', '%s']
    );
}

/* WPPOPPOP_TRACKING_LOADER */
if (!class_exists('WpPopPop_Tracking') && file_exists(WPPOPPOP_PATH . 'includes/class-wppoppop-tracking.php')) {
    require_once WPPOPPOP_PATH . 'includes/class-wppoppop-tracking.php';
}

/* WPPOPPOP_SETTINGS_RESET_LOADER */
if (!class_exists('WpPopPop_Settings_Reset') && file_exists(WPPOPPOP_PATH . 'includes/class-wppoppop-settings-reset.php')) {
    require_once WPPOPPOP_PATH . 'includes/class-wppoppop-settings-reset.php';
}
