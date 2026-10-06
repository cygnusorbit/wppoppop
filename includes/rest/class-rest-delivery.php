<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Rest_Delivery {
    public function register_routes() {
        register_rest_route('wppoppop/v1', '/popup/(?P<uid>[a-zA-Z0-9_-]+)', [
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => [$this, 'get_popup_payload'],
            'permission_callback' => '__return_true',
            'args'                => [
                'uid' => [
                    'required'          => true,
                    'sanitize_callback' => 'sanitize_key',
                ],
            ],
        ]);
    }

    public function get_popup_payload(WP_REST_Request $request) {
        $uid = $request->get_param('uid');
        if (empty($uid)) {
            return new WP_Error('invalid_uid', 'Missing popup identifier.', ['status' => 400]);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'wppoppop_items';
        $row   = $wpdb->get_row($wpdb->prepare("SELECT title, data FROM {$table} WHERE uid = %s AND status = 'publish'", $uid), ARRAY_A);

        if (!$row) {
            return new WP_Error('not_found', 'Popup not found or inactive.', ['status' => 404]);
        }

        $config = json_decode($row['data'], true);
        if (!$config) {
            return new WP_Error('corrupted_data', 'Popup configuration data corrupted.', ['status' => 500]);
        }

        ob_start();
        if (class_exists('WpPopPop_Front_Renderer')) {
            (new WpPopPop_Front_Renderer())->render_popup_markup($uid, $config, false);
        } elseif (class_exists('WpPopPop_Front')) {
            (new WpPopPop_Front())->render_popup_markup($uid, $config, false);
        }
        $html = ob_get_clean();

        $response = new WP_REST_Response([
            'success' => true,
            'uid'     => $uid,
            'title'   => $row['title'],
            'html'    => $html,
            'config'  => $config,
        ], 200);

        $response->header('Access-Control-Allow-Origin', '*');
        $response->header('Access-Control-Allow-Methods', 'GET, OPTIONS');

        return $response;
    }
}
