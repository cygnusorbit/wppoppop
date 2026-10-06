<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Retrieve a WpPopPop setting with fallback default value.
 */
function wppoppop_get_setting($key, $default = '') {
    $settings = get_option('wppoppop_settings', []);
    return isset($settings[$key]) ? $settings[$key] : $default;
}

/**
 * Record an activity or system audit event to the logs table.
 */
function wppoppop_log_event($event_type, $message, $payload = []) {
    global $wpdb;
    $table = $wpdb->prefix . 'wppoppop_logs';

    $wpdb->insert(
        $table,
        [
            'event_type' => sanitize_key($event_type),
            'message'    => sanitize_text_field($message),
            'payload'    => wp_json_encode($payload),
            'created_at' => current_time('mysql')
        ],
        ['%s', '%s', '%s', '%s']
    );
}
