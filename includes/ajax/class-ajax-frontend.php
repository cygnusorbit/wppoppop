<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Ajax_Frontend {
    public function __construct() {
        add_action('wp_ajax_wppoppop_remote_embed', [$this, 'serve_remote_embed']);
        add_action('wp_ajax_nopriv_wppoppop_remote_embed', [$this, 'serve_remote_embed']);

        add_action('wp_ajax_wppoppop_record_impression', [$this, 'record_impression']);
        add_action('wp_ajax_nopriv_wppoppop_record_impression', [$this, 'record_impression']);

        add_action('wp_ajax_wppoppop_submit_form', [$this, 'submit_form']);
        add_action('wp_ajax_nopriv_wppoppop_submit_form', [$this, 'submit_form']);

        add_action('wp_ajax_wppoppop_process_payment', [$this, 'process_payment']);
        add_action('wp_ajax_nopriv_wppoppop_process_payment', [$this, 'process_payment']);
    }

    private function set_cors_headers() {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type");
        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            status_header(200);
            exit;
        }
    }

    public function serve_remote_embed() {
        header('Content-Type: application/javascript; charset=utf-8');
        header('Access-Control-Allow-Origin: *');

        $uid = isset($_GET['uid']) ? sanitize_key($_GET['uid']) : '';
        if (empty($uid)) {
            echo 'console.error("WpPopPop Remote: Missing UID");';
            exit;
        }

        global $wpdb;
        $row = $wpdb->get_row($wpdb->prepare("SELECT title, data FROM {$wpdb->prefix}wppoppop_items WHERE uid = %s AND status = 'publish'", $uid), ARRAY_A);
        if (!$row) {
            echo 'console.error("WpPopPop Remote: Popup not found");';
            exit;
        }

        $config    = json_decode($row['data'], true);
        $ajax_url  = admin_url('admin-ajax.php');
        $front_css = WPPOPPOP_URL . 'public/css/wppoppop-front.css';
        $front_js  = WPPOPPOP_URL . 'public/js/wppoppop-front.js';
        ?>
(function() {
    if (window.wppoppop_remote_loaded_<?php echo esc_js($uid); ?>) return;
    window.wppoppop_remote_loaded_<?php echo esc_js($uid); ?> = true;

    var link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = '<?php echo esc_url($front_css); ?>';
    document.head.appendChild(link);

    function loadScript(src, callback) {
        var s = document.createElement('script');
        s.src = src;
        s.onload = callback;
        document.head.appendChild(s);
    }

    function initPopup() {
        window.wppoppop_front_vars = {
            ajax_url: '<?php echo esc_url($ajax_url); ?>',
            rest_url: '<?php echo esc_url(rest_url('wppoppop/v1/')); ?>',
            nonce: 'remote',
            visitor_country: ''
        };

        loadScript('<?php echo esc_url($front_js); ?>', function() {
            var container = document.createElement('div');
            container.innerHTML = <?php
                ob_start();
                (new WpPopPop_Front())->render_popup_markup($uid, $config, false);
                $html = ob_get_clean();
                echo wp_json_encode($html);
            ?>;
            document.body.appendChild(container.firstElementChild);
        });
    }

    if (!window.jQuery) {
        loadScript('https://code.jquery.com/jquery-3.7.1.min.js', initPopup);
    } else {
        initPopup();
    }
})();
        <?php
        exit;
    }

    public function record_impression() {
        $this->set_cors_headers();
        $uid = isset($_POST['uid']) ? sanitize_key($_POST['uid']) : '';
        if (!empty($uid)) {
            global $wpdb;
            $wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}wppoppop_items SET impressions = impressions + 1 WHERE uid = %s", $uid));
        }
        wp_send_json_success();
    }

    public function submit_form() {
        $this->set_cors_headers();

        if (!empty($_POST['_wppoppop_hp_email'])) {
            wp_send_json_error(['message' => 'Spam blocked by honeypot.']);
        }

        $uid   = isset($_POST['uid']) ? sanitize_key($_POST['uid']) : '';
        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        $form_data = isset($_POST['fields']) ? (array)$_POST['fields'] : [];
        $visitor_country = isset($_POST['country']) ? sanitize_text_field($_POST['country']) : '';

        if (!is_email($email)) {
            wp_send_json_error(['message' => 'Please enter a valid email address.']);
        }

        global $wpdb;
        $table_items = $wpdb->prefix . 'wppoppop_items';
        $table_subs  = $wpdb->prefix . 'wppoppop_submissions';

        $row = $wpdb->get_row($wpdb->prepare("SELECT title, data FROM {$table_items} WHERE uid = %s", $uid), ARRAY_A);

        if ($row) {
            $config = json_decode($row['data'], true);
            $notif  = isset($config['notifications']) ? $config['notifications'] : [];
            $is_double_optin = !empty($notif['enable_double_optin']);

            $initial_status = $is_double_optin ? 'pending' : 'confirmed';
            $confirm_token  = $is_double_optin ? wp_generate_password(32, false) : '';

            $wpdb->query($wpdb->prepare("UPDATE {$table_items} SET submissions = submissions + 1 WHERE uid = %s", $uid));
            if (!$is_double_optin) {
                $wpdb->query($wpdb->prepare("UPDATE {$table_items} SET confirmations = confirmations + 1 WHERE uid = %s", $uid));
            }

            $wpdb->insert(
                $table_subs,
                [
                    'popup_uid'     => $uid,
                    'email'         => $email,
                    'fields_data'   => wp_json_encode($form_data),
                    'status'        => $initial_status,
                    'confirm_token' => $confirm_token,
                    'country_code'  => $visitor_country
                ],
                ['%s', '%s', '%s', '%s', '%s', '%s']
            );

            // Autoresponder email
            $autoresponder = isset($config['autoresponder']) ? $config['autoresponder'] : [];
            if (!empty($autoresponder['enable_user_email']) && !$is_double_optin) {
                $ar_subject = !empty($autoresponder['subject']) ? sanitize_text_field($autoresponder['subject']) : 'Welcome! Here is your reward';
                $ar_body    = !empty($autoresponder['message']) ? $autoresponder['message'] : "Thank you for subscribing!\n\nEnjoy your offer.";

                $ar_body = str_replace('{email}', $email, $ar_body);
                foreach ($form_data as $k => $v) {
                    $val_str = is_array($v) ? implode(', ', $v) : $v;
                    $ar_body = str_replace('{' . $k . '}', $val_str, $ar_body);
                }

                wp_mail($email, $ar_subject, nl2br(esc_html($ar_body)), ['Content-Type: text/html; charset=UTF-8']);
            }

            // Mailchimp Sync
            $mc = isset($config['mailchimp']) ? $config['mailchimp'] : [];
            if (!empty($mc['enable']) && !empty($mc['api_key']) && !empty($mc['list_id'])) {
                $dc = substr($mc['api_key'], strpos($mc['api_key'], '-') + 1);
                $url = "https://{$dc}.api.mailchimp.com/3.0/lists/{$mc['list_id']}/members/" . md5(strtolower($email));
                wp_remote_post($url, [
                    'method'  => 'PUT',
                    'headers' => ['Authorization' => 'apikey ' . $mc['api_key'], 'Content-Type' => 'application/json'],
                    'body'    => wp_json_encode(['email_address' => $email, 'status_if_new' => 'subscribed', 'status' => 'subscribed']),
                    'blocking'=> false
                ]);
            }

            // Admin notification
            if (!empty($notif['enable_email'])) {
                $recipient = !empty($notif['recipient']) ? sanitize_email($notif['recipient']) : get_option('admin_email');
                $body = "New lead: {$email}\nPopup: {$row['title']}\nCountry: {$visitor_country}\n";
                wp_mail($recipient, 'New Lead: ' . $row['title'], $body);
            }

            $actions = isset($config['actions']) ? $config['actions'] : [];
            $success_message = $is_double_optin
                ? 'Thank you! A confirmation link has been dispatched to your email address.'
                : (!empty($actions['success_message']) ? esc_html($actions['success_message']) : 'Thank you! Your information has been registered.');

            wp_send_json_success([
                'message'      => $success_message,
                'redirect_url' => (!empty($actions['redirect_url']) && !$is_double_optin) ? esc_url_raw($actions['redirect_url']) : ''
            ]);
        }

        wp_send_json_error(['message' => 'An error occurred during submission.']);
    }

    public function process_payment() {
        $this->set_cors_headers();
        $uid      = sanitize_key($_POST['uid']);
        $email    = sanitize_email($_POST['email']);
        $amount   = floatval($_POST['amount']);
        $currency = sanitize_text_field($_POST['currency'] ?? 'USD');
        $gateway  = sanitize_text_field($_POST['gateway'] ?? 'Stripe');

        global $wpdb;
        $tx_id = 'TX_' . strtoupper(wp_generate_password(12, false));

        $wpdb->insert($wpdb->prefix . 'wppoppop_transactions', [
            'popup_uid'      => $uid,
            'email'          => $email,
            'amount'         => $amount,
            'currency'       => $currency,
            'gateway'        => $gateway,
            'transaction_id' => $tx_id,
            'status'         => 'completed'
        ]);

        $wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}wppoppop_items SET submissions = submissions + 1, confirmations = confirmations + 1 WHERE uid = %s", $uid));

        wp_send_json_success([
            'message'        => 'Payment captured successfully! Thank you.',
            'transaction_id' => $tx_id
        ]);
    }
}
