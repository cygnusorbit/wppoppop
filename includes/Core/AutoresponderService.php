<?php
namespace WPPopPop\Core;

use WPPopPop\Admin\SettingsManager;

class AutoresponderService {
    public static function init(): void {
        // Lifecycle coordinator registration
    }

    public static function send_welcome(int $lead_id, int $popup_id, string $email, string $name): bool {
        $enabled = (bool) SettingsManager::get('autoresponder_enabled', 1);
        if (!$enabled) {
            return false;
        }

        $site_name   = get_bloginfo('name');
        $sender_name = SettingsManager::get('sender_name', 'wppoppop');
        $sender_mail = SettingsManager::get('sender_email', 'noreply@localhost');
        $popup_title = $popup_id ? get_the_title($popup_id) : 'Special Offer';
        $coupon_code = get_post_meta($popup_id, '_wppoppop_step3_coupon', true) ?: 'WELCOME10';

        $template_sub  = SettingsManager::get('autoresponder_subject', 'Welcome to {site_name}! Here is your gift');
        $template_body = SettingsManager::get('autoresponder_body', '');

        if (empty($template_body)) {
            $template_body = "<p>Hi {name},</p>\n<p>Thank you for subscribing to <strong>{popup_title}</strong>! Here is your exclusive voucher code:</p>\n<p style='font-size:20px;font-weight:700;color:#b5295c;letter-spacing:1px;'>{coupon_code}</p>\n<p>Best regards,<br>{site_name}</p>";
        }

        $merge_tags = [
            '{name}'        => esc_html($name ?: 'Subscriber'),
            '{email}'       => esc_html($email),
            '{popup_title}' => esc_html($popup_title),
            '{coupon_code}' => esc_html($coupon_code),
            '{site_name}'   => esc_html($site_name),
        ];

        $subject = str_replace(array_keys($merge_tags), array_values($merge_tags), $template_sub);
        $body    = str_replace(array_keys($merge_tags), array_values($merge_tags), $template_body);

        $headers = [
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . esc_attr($sender_name) . ' <' . sanitize_email($sender_mail) . '>',
        ];

        $full_html = sprintf(
            '<div style="font-family:-apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,sans-serif;max-width:560px;margin:20px auto;padding:24px;border:1px solid #e2e8f0;border-radius:8px;background:#ffffff;line-height:1.6;color:#334155;">' .
            '%s' .
            '</div>',
            wpautop($body)
        );

        $sent = (bool) wp_mail($email, $subject, $full_html, $headers);
        if ($sent) {
            update_post_meta($lead_id, '_wppoppop_autoresponder_sent', 1);
        }

        return $sent;
    }
}
