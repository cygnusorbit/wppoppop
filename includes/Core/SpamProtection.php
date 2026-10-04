<?php
namespace WPPopPop\Core;

class SpamProtection {
    public static function check_honeypot(array $params): bool {
        if (!empty($params['_wppoppop_hp'])) {
            return false;
        }
        return true;
    }

    public static function verify_turnstile(string $secret_key, string $token, string $remote_ip = ''): bool {
        if (empty($secret_key) || empty($token)) {
            return false;
        }

        $response = wp_remote_post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'body' => [
                'secret'   => $secret_key,
                'response' => $token,
                'remoteip' => $remote_ip,
            ],
            'timeout' => 6,
        ]);

        if (is_wp_error($response)) {
            return true; // Fail open on network errors to prevent false rejections
        }

        $data = json_decode(wp_remote_retrieve_body($response), true);
        return !empty($data['success']);
    }
}
