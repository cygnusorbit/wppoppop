<?php
namespace WPPopPop\Integrations;

use WPPopPop\Admin\SettingsManager;

class ActiveCampaignAdapter implements CRMAdapterInterface {
    public function sync_contact(string $email, string $name, array $custom_fields = []): array {
        $api_url = SettingsManager::get('activecampaign_api_url', '');
        $api_key = SettingsManager::get('activecampaign_api_key', '');
        $list_id = SettingsManager::get('activecampaign_list_id', '');

        if (empty($api_url) || empty($api_key)) {
            return ['success' => false, 'message' => 'ActiveCampaign credentials unconfigured.'];
        }

        $api_url = rtrim($api_url, '/');
        $endpoint = "{$api_url}/api/3/contact/sync";

        $name_parts = explode(' ', trim($name), 2);
        $first_name = $name_parts[0] ?? '';
        $last_name  = $name_parts[1] ?? '';

        $body = [
            'contact' => [
                'email'     => $email,
                'firstName' => $first_name,
                'lastName'  => $last_name,
            ],
        ];

        $response = wp_remote_post($endpoint, [
            'timeout' => 8,
            'headers' => [
                'Api-Token'    => $api_key,
                'Content-Type' => 'application/json',
            ],
            'body'    => wp_json_encode($body),
        ]);

        if (is_wp_error($response)) {
            return ['success' => false, 'message' => $response->get_error_message()];
        }

        $code = wp_remote_retrieve_response_code($response);
        $res_body = json_decode(wp_remote_retrieve_body($response), true);
        $contact_id = $res_body['contact']['id'] ?? 0;

        // If list ID is specified, associate contact with list
        if ($contact_id > 0 && !empty($list_id)) {
            wp_remote_post("{$api_url}/api/3/contactLists", [
                'timeout' => 5,
                'headers' => [
                    'Api-Token'    => $api_key,
                    'Content-Type' => 'application/json',
                ],
                'body'    => wp_json_encode([
                    'contactList' => [
                        'list'    => $list_id,
                        'contact' => $contact_id,
                        'status'  => 1,
                    ],
                ]),
            ]);
        }

        return [
            'success' => ($code >= 200 && $code < 300),
            'code'    => $code,
        ];
    }
}
