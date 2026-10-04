<?php
namespace WPPopPop\Integrations;

class MailchimpClient {
    public static function subscribe(string $api_key, string $list_id, string $email, string $name, bool $double_optin = false): array {
        $api_key = trim($api_key);
        $list_id = trim($list_id);
        $email   = trim($email);

        if (empty($api_key) || empty($list_id) || empty($email)) {
            return ['success' => false, 'message' => 'Missing Mailchimp configuration parameters.'];
        }

        $key_parts = explode('-', $api_key);
        if (count($key_parts) < 2) {
            return ['success' => false, 'message' => 'Invalid Mailchimp API key format.'];
        }
        $dc = end($key_parts);

        $subscriber_hash = md5(strtolower($email));
        $endpoint = "https://{$dc}.api.mailchimp.com/3.0/lists/{$list_id}/members/{$subscriber_hash}";

        $name_parts = explode(' ', trim($name), 2);
        $first_name = $name_parts[0] ?? '';
        $last_name  = $name_parts[1] ?? '';

        $status = $double_optin ? 'pending' : 'subscribed';

        $payload = [
            'email_address' => $email,
            'status_if_new' => $status,
            'merge_fields'  => [
                'FNAME' => $first_name,
                'LNAME' => $last_name,
            ],
        ];

        $response = wp_remote_request($endpoint, [
            'method'  => 'PUT',
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode('user:' . $api_key),
                'Content-Type'  => 'application/json; charset=utf-8',
            ],
            'body'    => wp_json_encode($payload),
            'timeout' => 8,
        ]);

        if (is_wp_error($response)) {
            return ['success' => false, 'message' => $response->get_error_message()];
        }

        $code = wp_remote_retrieve_response_code($response);
        return [
            'success' => ($code >= 200 && $code < 300),
            'code'    => $code,
        ];
    }
}
