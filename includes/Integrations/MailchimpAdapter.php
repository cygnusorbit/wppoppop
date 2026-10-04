<?php
namespace WPPopPop\Integrations;

use WPPopPop\Admin\SettingsManager;

class MailchimpAdapter implements CRMAdapterInterface {
    public function sync_contact(string $email, string $name, array $custom_fields = []): array {
        $api_key = SettingsManager::get('mailchimp_api_key', '');
        $list_id = SettingsManager::get('mailchimp_list_id', '');

        if (empty($api_key) || empty($list_id)) {
            return ['success' => false, 'message' => 'Mailchimp credentials unconfigured.'];
        }

        $parts = explode('-', $api_key);
        $data_center = isset($parts[1]) ? $parts[1] : 'us1';
        $subscriber_hash = md5(strtolower(trim($email)));
        $url = "https://{$data_center}.api.mailchimp.com/3.0/lists/{$list_id}/members/{$subscriber_hash}";

        $name_parts = explode(' ', trim($name), 2);
        $first_name = $name_parts[0] ?? '';
        $last_name  = $name_parts[1] ?? '';

        $body = [
            'email_address' => $email,
            'status_if_new' => 'subscribed',
            'merge_fields'  => [
                'FNAME' => $first_name,
                'LNAME' => $last_name,
            ],
        ];

        $response = wp_remote_request($url, [
            'method'  => 'PUT',
            'timeout' => 8,
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode('user:' . $api_key),
                'Content-Type'  => 'application/json',
            ],
            'body'    => wp_json_encode($body),
        ]);

        if (is_wp_error($response)) {
            return ['success' => false, 'message' => $response->get_error_message()];
        }

        $code = wp_remote_retrieve_response_code($response);
        return [
            'success' => ($code >= 200 && $code < 300),
            'code'    => $code,
            'body'    => wp_remote_retrieve_body($response),
        ];
    }
}
