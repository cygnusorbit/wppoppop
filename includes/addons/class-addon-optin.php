<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Addon_Optin {
    public function __construct() {
        add_action('template_redirect', [$this, 'handle_confirm_token']);
    }

    public function handle_confirm_token() {
        if (!isset($_GET['wppoppop_confirm'])) {
            return;
        }

        $token = sanitize_text_field(wp_unslash($_GET['wppoppop_confirm']));
        if (empty($token)) {
            return;
        }

        global $wpdb;
        $subs_table  = $wpdb->prefix . 'wppoppop_submissions';
        $items_table = $wpdb->prefix . 'wppoppop_items';

        $submission = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$subs_table} WHERE confirm_token = %s LIMIT 1",
            $token
        ), ARRAY_A);

        if (!$submission) {
            wp_die('The confirmation link is invalid or has already been used.', 'Verification Error', ['response' => 400]);
        }

        if ($submission['status'] === 'confirmed') {
            wp_die('Your subscription has already been confirmed. Thank you!', 'Already Confirmed', ['response' => 200]);
        }

        // Transition status to confirmed and invalidate token
        $wpdb->update(
            $subs_table,
            ['status' => 'confirmed', 'confirm_token' => ''],
            ['id' => $submission['id']],
            ['%s', '%s'],
            ['%d']
        );

        // Increment confirmation counter on popup record
        if (!empty($submission['popup_uid'])) {
            $wpdb->query($wpdb->prepare(
                "UPDATE {$items_table} SET confirmations = confirmations + 1 WHERE uid = %s",
                $submission['popup_uid']
            ));
        }

        wppoppop_log_event('optin_confirmed', 'Double opt-in confirmed for ' . $submission['email'], [
            'popup_uid'     => $submission['popup_uid'],
            'submission_id' => $submission['id']
        ]);

        // Render clean confirmation screen
        ?>
        <!DOCTYPE html>
        <html <?php language_attributes(); ?>>
        <head>
            <meta charset="<?php bloginfo('charset'); ?>">
            <title>Subscription Confirmed</title>
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <style>
                body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f8fafc; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
                .card { background: #ffffff; border-radius: 12px; padding: 40px; max-width: 480px; text-align: center; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1); }
                .icon { font-size: 48px; color: #10b981; margin-bottom: 16px; }
                h1 { margin: 0 0 12px 0; color: #1e293b; font-size: 24px; }
                p { color: #64748b; font-size: 15px; line-height: 1.5; margin-bottom: 24px; }
                a.btn { display: inline-block; background: #2563eb; color: #fff; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: 600; }
            </style>
        </head>
        <body>
            <div class="card">
                <div class="icon">&#10004;</div>
                <h1>Subscription Confirmed!</h1>
                <p>Thank you. Your email address <strong><?php echo esc_html($submission['email']); ?></strong> has been verified successfully.</p>
                <a href="<?php echo esc_url(home_url()); ?>" class="btn">Return to Site</a>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}
