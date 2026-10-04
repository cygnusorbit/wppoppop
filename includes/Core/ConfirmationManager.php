<?php
namespace WPPopPop\Core;

use WPPopPop\Targeting\PopupPostType;

class ConfirmationManager {
    public function init(): void {
        add_action('init', [$this, 'handle_confirmation_request']);
    }

    public static function send_confirmation_email(int $lead_id, int $popup_id, string $email, string $name, string $token): void {
        $confirm_url = add_query_arg(['wppoppop_confirm' => $token], home_url('/'));
        
        $custom_subject = get_post_meta($popup_id, '_wppoppop_double_optin_subject', true);
        $subject = !empty($custom_subject) ? $custom_subject : __('Please confirm your subscription', 'wppoppop');

        $custom_body = get_post_meta($popup_id, '_wppoppop_double_optin_body', true);
        if (empty($custom_body)) {
            $body = "Hi {name},\n\nPlease click the link below to confirm your email subscription:\n{confirm_link}\n\nIf you did not make this request, you can safely ignore this email.";
        } else {
            $body = $custom_body;
        }

        $display_name = !empty($name) ? $name : 'there';
        $body = str_replace(
            ['{name}', '{confirm_link}', '{popup_title}'],
            [$display_name, $confirm_url, get_the_title($popup_id)],
            $body
        );

        wp_mail($email, $subject, $body);
    }

    public function handle_confirmation_request(): void {
        if (!isset($_GET['wppoppop_confirm'])) {
            return;
        }

        $token = sanitize_text_field(wp_unslash($_GET['wppoppop_confirm']));
        if (empty($token)) {
            return;
        }

        $query = new \WP_Query([
            'post_type'      => PopupPostType::LEAD_POST_TYPE,
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'meta_query'     => [
                [
                    'key'     => '_wppoppop_confirmation_token',
                    'value'   => $token,
                    'compare' => '=',
                ],
            ],
        ]);

        if (!$query->have_posts()) {
            wp_die(
                '<h1>' . esc_html__('Invalid or Expired Link', 'wppoppop') . '</h1><p>' . esc_html__('This confirmation link is invalid or has already been used.', 'wppoppop') . '</p>',
                esc_html__('Subscription Confirmation', 'wppoppop'),
                ['response' => 400]
            );
        }

        $lead = $query->posts[0];
        update_post_meta($lead->ID, '_wppoppop_confirmed', '1');
        update_post_meta($lead->ID, '_wppoppop_confirmed_at', current_time('mysql'));
        delete_post_meta($lead->ID, '_wppoppop_confirmation_token');

        // Render standalone verification view
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php esc_html_e('Subscription Confirmed', 'wppoppop'); ?></title>
            <style>
                body {
                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
                    background: #f1f5f9;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    min-height: 100vh;
                    margin: 0;
                    padding: 20px;
                    box-sizing: border-box;
                }
                .wppoppop-confirm-card {
                    background: #ffffff;
                    padding: 40px;
                    border-radius: 16px;
                    box-shadow: 0 10px 25px rgba(0,0,0,0.06);
                    max-width: 440px;
                    width: 100%;
                    text-align: center;
                }
                .wppoppop-icon {
                    width: 56px;
                    height: 56px;
                    background: #dcfce7;
                    color: #16a34a;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin: 0 auto 20px;
                    font-size: 28px;
                }
                h1 {
                    font-size: 22px;
                    color: #0f172a;
                    margin: 0 0 10px;
                }
                p {
                    font-size: 15px;
                    color: #64748b;
                    line-height: 1.5;
                    margin: 0 0 24px;
                }
                a.btn {
                    display: inline-block;
                    background: #2563eb;
                    color: #ffffff;
                    text-decoration: none;
                    font-weight: 600;
                    padding: 12px 24px;
                    border-radius: 8px;
                    font-size: 14px;
                }
            </style>
        </head>
        <body>
            <div class="wppoppop-confirm-card">
                <div class="wppoppop-icon">✓</div>
                <h1><?php esc_html_e('Subscription Confirmed!', 'wppoppop'); ?></h1>
                <p><?php esc_html_e('Thank you for verifying your email address. Your subscription is now complete.', 'wppoppop'); ?></p>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="btn"><?php esc_html_e('Return to Website', 'wppoppop'); ?></a>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}
