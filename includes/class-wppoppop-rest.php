<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Rest {
    public function __construct() {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes() {
        register_rest_route('wppoppop/v1', '/impression', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handle_impression'],
            'permission_callback' => '__return_true'
        ]);

        register_rest_route('wppoppop/v1', '/submit', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handle_submission'],
            'permission_callback' => '__return_true'
        ]);
    }

    public function handle_impression(WP_REST_Request $request) {
        $params = $request->get_json_params();
        $uid = isset($params['uid']) ? sanitize_key($params['uid']) : '';

        if (!empty($uid)) {
            global $wpdb;
            $wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}wppoppop_items SET impressions = impressions + 1 WHERE uid = %s", $uid));
        }

        return new WP_REST_Response(['success' => true], 200);
    }

    public function handle_submission(WP_REST_Request $request) {
        $params = $request->get_json_params();
        $uid   = isset($params['uid']) ? sanitize_key($params['uid']) : '';
        $email = isset($params['email']) ? sanitize_email($params['email']) : '';
        $fields = isset($params['fields']) ? (array)$params['fields'] : [];
        $country = isset($params['country']) ? sanitize_text_field($params['country']) : '';

        // Honeypot validation
        if (!empty($params['_wppoppop_hp_email'])) {
            return new WP_REST_Response(['success' => false, 'message' => 'Spam blocked.'], 400);
        }

        if (!is_email($email)) {
            return new WP_REST_Response(['success' => false, 'message' => 'Invalid email address.'], 400);
        }

        global $wpdb;
        $table_items = $wpdb->prefix . 'wppoppop_items';
        $table_subs  = $wpdb->prefix . 'wppoppop_submissions';

        $row = $wpdb->get_row($wpdb->prepare("SELECT title, data FROM {$table_items} WHERE uid = %s", $uid), ARRAY_A);
        if (!$row) {
            return new WP_REST_Response(['success' => false, 'message' => 'Popup not found.'], 404);
        }

        $wpdb->query($wpdb->prepare("UPDATE {$table_items} SET submissions = submissions + 1, confirmations = confirmations + 1 WHERE uid = %s", $uid));

        $wpdb->insert($table_subs, [
            'popup_uid'     => $uid,
            'email'         => $email,
            'fields_data'   => wp_json_encode($fields),
            'status'        => 'confirmed',
            'confirm_token' => '',
            'country_code'  => $country
        ]);

        return new WP_REST_Response([
            'success' => true,
            'message' => 'Thank you! Submission processed successfully.'
        ], 200);
    }
}
