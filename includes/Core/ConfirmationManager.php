<?php
namespace WPPopPop\Core;

use WPPopPop\Targeting\PopupPostType;
use WPPopPop\Admin\SettingsManager;

class ConfirmationManager {
    public static function init(): void {
        add_action('init', [__CLASS__, 'handle_confirmation_request']);
    }

    public static function send_confirmation_request(int $lead_id, int $popup_id, string $email, string $name): bool {
        $token = wp_generate_password(32, false);
        update_post_meta($lead_id, '_wppoppop_confirm_token', $token);
        update_post_meta($lead_id, '_wppoppop_status', 'Pending');
        update_post_meta($lead_id, '_wppoppop_confirmed', 0);

        $confirm_url = add_query_arg('wppoppop_confirm', $token, home_url('/'));
        $site_name   = get_bloginfo('name');
        $sender_name = SettingsManager::get('sender_name', 'wppoppop');
        $sender_mail = SettingsManager::get('sender_email', 'noreply@localhost');

        $custom_subject = get_post_meta($popup_id, '_wppoppop_double_optin_subject', true);
        $subject = !empty($custom_subject) ? $custom_subject : sprintf('[%s] Please confirm your subscription', $site_name);

        $headers = [
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . esc_attr($sender_name) . ' <' . sanitize_email($sender_mail) . '>',
        ];

        $message = sprintf(
            '<div style="font-family:-apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,sans-serif;max-width:540px;margin:20px auto;padding:24px;border:1px solid #e2e8f0;border-radius:8px;background:#ffffff;">' .
            '<h2 style="color:#0f172a;margin-top:0;">Confirm Your Subscription</h2>' .
            '<p style="color:#475569;font-size:15px;line-height:1.6;">Hello %s,</p>' .
            '<p style="color:#475569;font-size:15px;line-height:1.6;">Thank you for your interest! Please click the button below to confirm your email address and activate your subscription.</p>' .
            '<div style="margin:28px 0;text-align:center;">' .
            '  <a href="%s" style="background:#b5295c;color:#ffffff;text-decoration:none;padding:12px 28px;border-radius:4px;font-weight:600;font-size:15px;display:inline-block;">Confirm Subscription</a>' .
            '</div>' .
            '<p style="color:#94a3b8;font-size:12px;line-height:1.5;">If you did not request this subscription, you can safely ignore this email.</p>' .
            '</div>',
            esc_html($name ?: 'there'),
            esc_url($confirm_url)
        );

        return (bool) wp_mail($email, $subject, $message, $headers);
    }

    public static function handle_confirmation_request(): void {
        if (!isset($_GET['wppoppop_confirm'])) {
            return;
        }

        $token = sanitize_text_field($_GET['wppoppop_confirm']);
        if (empty($token)) {
            wp_die(__('Invalid confirmation token.', 'wppoppop'), 400);
        }

        $leads = get_posts([
            'post_type'      => PopupPostType::LEAD_POST_TYPE,
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'meta_key'       => '_wppoppop_confirm_token',
            'meta_value'     => $token,
        ]);

        if (empty($leads)) {
            wp_die(__('This confirmation link is invalid or has already been used.', 'wppoppop'), 404);
        }

        $lead    = $leads[0];
        $lead_id = $lead->ID;
        $popup_id = (int) get_post_meta($lead_id, '_wppoppop_lead_popup_id', true);
        $email   = get_post_meta($lead_id, '_wppoppop_lead_email', true) ?: $lead->post_title;
        $name    = get_post_meta($lead_id, '_wppoppop_lead_name', true) ?: '';

        // 1. Update Lead Status
        update_post_meta($lead_id, '_wppoppop_status', 'Confirmed');
        update_post_meta($lead_id, '_wppoppop_confirmed', 1);
        update_post_meta($lead_id, '_wppoppop_confirmed_at', gmdate('Y-m-d H:i:s'));
        delete_post_meta($lead_id, '_wppoppop_confirm_token'); // One-time use

        // 2. Synchronize "Confirmed" series on Stats timeline (Menu-Stats.png)
        $today = gmdate('Y-m-d');
        $daily_stats = get_option('wppoppop_daily_stats', []);
        if (!isset($daily_stats[$today])) {
            $daily_stats[$today] = ['impressions' => 0, 'submits' => 0, 'confirmed' => 0, 'payments' => 0];
        }
        $daily_stats[$today]['confirmed']++;
        update_option('wppoppop_daily_stats', $daily_stats);

        // 3. Dispatch Automated Welcome Autoresponder
        AutoresponderService::send_welcome($lead_id, $popup_id, $email, $name);

        // 4. Render Responsive Confirmation Landing Page
        status_header(200);
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php echo esc_html__('Subscription Confirmed', 'wppoppop'); ?></title>
            <style>
                body {
                    margin: 0; padding: 0;
                    background: #f8fafc;
                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
                    display: flex; align-items: center; justify-content: center;
                    min-height: 100vh;
                }
                .wppoppop-card {
                    background: #ffffff;
                    border: 1px solid #e2e8f0;
                    border-radius: 12px;
                    padding: 40px;
                    max-width: 460px;
                    width: 90%;
                    text-align: center;
                    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
                }
                .wppoppop-icon {
                    width: 64px; height: 64px;
                    background: #dcfce7; color: #16a34a;
                    border-radius: 50%;
                    display: inline-flex; align-items: center; justify-content: center;
                    font-size: 32px; font-weight: bold; margin-bottom: 20px;
                }
                h1 { margin: 0 0 10px; font-size: 22px; color: #0f172a; }
                p { color: #64748b; font-size: 15px; line-height: 1.5; margin: 0 0 24px; }
                .wppoppop-btn {
                    display: inline-block;
                    background: #b5295c; color: #ffffff;
                    padding: 12px 28px; border-radius: 6px;
                    text-decoration: none; font-weight: 600; font-size: 14px;
                }
            </style>
        </head>
        <body>
            <div class="wppoppop-card">
                <div class="wppoppop-icon">&#10003;</div>
                <h1><?php echo esc_html__('Subscription Confirmed!', 'wppoppop'); ?></h1>
                <p><?php echo esc_html__('Your email address has been successfully verified. Welcome to our community!', 'wppoppop'); ?></p>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="wppoppop-btn"><?php echo esc_html__('Return to Homepage', 'wppoppop'); ?></a>
            </div>
            <script>
                setTimeout(function() {
                    window.location.href = '<?php echo esc_js(home_url('/')); ?>';
                }, 4000);
            </script>
        </body>
        </html>
        <?php
        exit;
    }
}
