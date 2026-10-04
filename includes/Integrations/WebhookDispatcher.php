<?php
namespace WPPopPop\Integrations;

class WebhookDispatcher {
    public static function dispatch(int $popup_id, array $lead_data): void {
        $webhook_url = get_post_meta($popup_id, '_wppoppop_webhook_url', true);
        if (empty($webhook_url) || !filter_var($webhook_url, FILTER_VALIDATE_URL)) {
            return;
        }

        wp_remote_post($webhook_url, [
            'headers'     => ['Content-Type' => 'application/json; charset=utf-8'],
            'body'        => wp_json_encode($lead_data),
            'timeout'     => 5,
            'blocking'    => false, // Non-blocking async dispatch
            'data_format' => 'body',
        ]);
    }
}
