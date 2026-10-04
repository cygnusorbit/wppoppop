<?php
namespace WPPopPop\Integrations;

class SmsDispatcher {
    public static function dispatch(int $popup_id, array $lead_data): array {
        $sms_enabled = get_post_meta($popup_id, '_wppoppop_sms_enabled', true) === '1';
        if (!$sms_enabled) {
            return ['success' => false, 'message' => 'SMS notifications disabled.'];
        }

        $sid       = trim((string) get_post_meta($popup_id, '_wppoppop_twilio_sid', true));
        $token     = trim((string) get_post_meta($popup_id, '_wppoppop_twilio_token', true));
        $from_num  = trim((string) get_post_meta($popup_id, '_wppoppop_twilio_from', true));
        $to_num    = trim((string) get_post_meta($popup_id, '_wppoppop_twilio_to', true));
        $tpl       = (string) get_post_meta($popup_id, '_wppoppop_sms_template', true);

        if (empty($sid) || empty($token) || empty($from_num) || empty($to_num)) {
            return ['success' => false, 'message' => 'Incomplete Twilio SMS configuration.'];
        }

        if (empty($tpl)) {
            $tpl = "New Lead Captured!\nName: {name}\nEmail: {email}\nPopup: {popup_title}";
        }

        $message_body = str_replace(
            ['{name}', '{email}', '{popup_title}', '{date}'],
            [
                $lead_data['name'] ?? 'Subscriber',
                $lead_data['email'] ?? '',
                get_the_title($popup_id),
                current_time('mysql'),
            ],
            $tpl
        );

        $endpoint = "https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json";

        $response = wp_remote_post($endpoint, [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode("{$sid}:{$token}"),
                'Content-Type'  => 'application/x-www-form-urlencoded',
            ],
            'body' => [
                'From' => $from_num,
                'To'   => $to_num,
                'Body' => $message_body,
            ],
            'timeout'  => 8,
            'blocking' => false, // Non-blocking asynchronous dispatch
        ]);

        if (is_wp_error($response)) {
            return ['success' => false, 'message' => $response->get_error_message()];
        }

        return ['success' => true];
    }
}
