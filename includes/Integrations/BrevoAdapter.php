<?php
namespace WPPopPop\Integrations;

use WPPopPop\Admin\SettingsManager;

class BrevoAdapter implements CRMAdapterInterface {
    public function sync_contact(string $email, string $name, array $custom_fields = []): array {
        $api_key = SettingsManager::get('brevo_api_key', '');
        $list_id = SettingsManager::get('brevo_list_id', '');

        if (empty($api_key)) {
            return ['success' => false, 'message' => 'Brevo API key unconfigured.'];
        }

        $url = 'https://api.brevo.com/v3/contacts';
        $name_parts = explode(' ', trim($name), 2);

        $body = [
            'email'      => $email,
            'attributes' => [
                'FIRSTNAME' => $name_parts[0] ?? '',
                'LASTNAME'  => $name_parts[1] ?? '',
            ],
            'updateEnabled' => true,
        ];

        if (!empty($list_id)) {
            $body['listIds'] = [absint($list_id)];
        }

        $response = wp_remote_post($url, [
            'timeout' => 8,
            'headers' => [
                'api-key'      => $api_key,
                'Content-Type' => 'application/json',
            ],
            'body'    => wp_json_encode($body),
        ]);

        if (is_wp_error($response)) {
            return ['success' => false, 'message' => $response->get_error_message()];
        }

        $code = wp_remote_retrieve_response_code($response);
        return [
            'success' => ($code === 201 || $code === 204 || $code === 200),
            'code'    => $code,
        ];
    }
}
