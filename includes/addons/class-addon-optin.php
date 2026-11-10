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

        // phpcs:ignore PluginCheck.Security.DirectDB.UnescapedDBParameter -- table name is built from $wpdb->prefix and every value is prepared
        $submission = $wpdb->get_row($wpdb->prepare(
            /* phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- table name is built from $wpdb->prefix and every value is prepared */ "SELECT * FROM {$subs_table} WHERE confirm_token = %s LIMIT 1",
            $token
        ), ARRAY_A);

        if (!$submission) {
            wp_die(esc_html__('The confirmation link is invalid or has already been used.', 'wppoppop'), esc_html__('Verification Error', 'wppoppop'), ['response' => 400]);
        }

        if ($submission['status'] === 'confirmed') {
            wp_die(esc_html__('Your subscription has already been confirmed. Thank you!', 'wppoppop'), esc_html__('Already Confirmed', 'wppoppop'), ['response' => 200]);
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
            // phpcs:ignore PluginCheck.Security.DirectDB.UnescapedDBParameter -- table name is built from $wpdb->prefix and every value is prepared
            $wpdb->query($wpdb->prepare(
                /* phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- table name is built from $wpdb->prefix and every value is prepared */ "UPDATE {$items_table} SET confirmations = confirmations + 1 WHERE uid = %s",
                $submission['popup_uid']
            ));
        }

        $wppoppop_row = !empty($submission['popup_uid'])
            /* phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- table name is built from $wpdb->prefix and every value is prepared */ ? $wpdb->get_row($wpdb->prepare("SELECT title, data FROM {$items_table} WHERE uid = %s", $submission['popup_uid']), ARRAY_A) : null;
        $wppoppop_cfg = $wppoppop_row ? json_decode((string) $wppoppop_row['data'], true) : array();
        if (!is_array($wppoppop_cfg)) { $wppoppop_cfg = array(); }
        $wppoppop_thank    = WpPopPop_Module_Bridge::confirmation_thank($wppoppop_cfg, $submission['email']);
        $wppoppop_dopt_msg = $wppoppop_thank['message'];

        if ($wppoppop_row) {
            $wppoppop_fields  = (array) json_decode(isset($submission['fields_data']) ? (string) $submission['fields_data'] : '[]', true);
            $wppoppop_country = isset($submission['country_code']) ? $submission['country_code'] : '';
            // Notifications: "After double opt-in confirmation"
            if (!class_exists('WpPopPop_Notifications')) { require_once WPPOPPOP_PATH . 'includes/class-wppoppop-notifications.php'; }
            WpPopPop_Notifications::dispatch('after_confirmation', $submission['popup_uid'], $wppoppop_row['title'], $wppoppop_cfg, $submission['email'], $wppoppop_fields, (int) $submission['id'], $wppoppop_country);
            // Extension modules (Pro): MailChimp / GetResponse "After double opt-in confirmation"
            WpPopPop_Module_Bridge::after_confirmation(array(
                'uid' => $submission['popup_uid'], 'title' => $wppoppop_row['title'], 'config' => $wppoppop_cfg, 'email' => $submission['email'],
                'fields' => $wppoppop_fields, 'submission_id' => (int) $submission['id'], 'country' => $wppoppop_country,
            ));
        }

        wppoppop_log_event('optin_confirmed', 'Double opt-in confirmed for ' . $submission['email'], [
            'popup_uid'     => $submission['popup_uid'],
            'submission_id' => $submission['id']
        ]);

        if ($wppoppop_thank['url'] !== '') {
            /* phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect -- the thank-you page is chosen by the administrator and may be on another domain */ wp_redirect(esc_url_raw($wppoppop_thank['url']));
            exit;
        }

        // Render clean confirmation screen
        ?>
        <!DOCTYPE html>
        <html <?php language_attributes(); ?>>
        <head>
            <meta charset="<?php bloginfo('charset'); ?>">
            <title><?php esc_html_e('Subscription Confirmed', 'wppoppop'); ?></title>
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
                <?php if ($wppoppop_dopt_msg === '') : ?><h1><?php esc_html_e('Subscription Confirmed!', 'wppoppop'); ?></h1><?php endif; ?>
                <?php if ($wppoppop_dopt_msg !== '') : ?>
                <div class="wppoppop-dopt-thanks"><?php echo wp_kses_post(wpautop($wppoppop_dopt_msg)); ?></div>
                <?php else : ?>
                <p><?php esc_html_e('Thank you. Your email address', 'wppoppop'); ?> <strong><?php echo esc_html($submission['email']); ?></strong> <?php esc_html_e('has been verified successfully.', 'wppoppop'); ?></p>
                <?php endif; ?>
                <a href="<?php echo esc_url(home_url()); ?>" class="btn"><?php esc_html_e('Return to Site', 'wppoppop'); ?></a>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}
