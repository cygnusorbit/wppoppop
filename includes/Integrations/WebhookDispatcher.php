<?php
namespace WPPopPop\Integrations;

use WPPopPop\Admin\SettingsManager;

class WebhookDispatcher {
    public static function dispatch(int $lead_id, int $popup_id, array $data): void {
        $settings = SettingsManager::get_all();
        $global_webhook = $settings['webhook_url'] ?? '';
        $popup_webhook  = get_post_meta($popup_id, '_wppoppop_webhook_url', true);
        $webhook_url    = !empty($popup_webhook) ? $popup_webhook : $global_webhook;

        $payload = [
            'event'       => 'lead_captured',
            'lead_id'     => $lead_id,
            'popup_id'    => $popup_id,
            'popup_title' => get_the_title($popup_id),
            'email'       => $data['email'] ?? '',
            'name'        => $data['name'] ?? '',
            'amount'      => $data['amount'] ?? '0.00',
            'status'      => $data['status'] ?? 'Completed',
            'timestamp'   => gmdate('Y-m-d H:i:s'),
            'site_url'    => get_site_url(),
        ];

        // 1. Dispatch Webhook JSON payload
        if (!empty($webhook_url) && filter_var($webhook_url, FILTER_VALIDATE_URL)) {
            wp_remote_post($webhook_url, [
                'method'      => 'POST',
                'timeout'     => 5,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking'    => false,
                'headers'     => [
                    'Content-Type' => 'application/json',
                    'User-Agent'   => 'WPPopPop/' . WPPOPPOP_VERSION,
                ],
                'body'        => wp_json_encode($payload),
            ]);
        }

        // 2. Dispatch Admin Notification Email
        $admin_notify = $settings['admin_notify_email'] ?? '';
        if (!empty($admin_notify) && is_email($admin_notify)) {
            $sender_name  = $settings['sender_name'] ?? 'wppoppop';
            $sender_email = $settings['sender_email'] ?? 'noreply@localhost';
            $headers = [
                'Content-Type: text/html; charset=UTF-8',
                'From: ' . esc_attr($sender_name) . ' <' . sanitize_email($sender_email) . '>',
            ];

            $subject = sprintf('[%s] New Lead Captured: %s', get_bloginfo('name'), $data['email'] ?? 'Subscriber');
            $message = sprintf(
                '<h3>New Lead Captured</h3>' .
                '<p><strong>Popup:</strong> %s (#%d)</p>' .
                '<p><strong>Email:</strong> %s</p>' .
                '<p><strong>Name:</strong> %s</p>' .
                '<p><strong>Amount:</strong> $%s</p>' .
                '<p><strong>Timestamp:</strong> %s</p>' .
                '<p><a href="%s">View All Leads in Dashboard</a></p>',
                esc_html(get_the_title($popup_id)),
                $popup_id,
                esc_html($data['email'] ?? ''),
                esc_html($data['name'] ?? '—'),
                esc_html(number_format((float)($data['amount'] ?? 0), 2)),
                gmdate('Y-m-d H:i:s'),
                esc_url(admin_url('admin.php?page=wppoppop-log'))
            );

            wp_mail($admin_notify, $subject, $message, $headers);
        }
    }
}
