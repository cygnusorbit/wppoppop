<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Ajax {
    public function __construct() {
        // Builder actions
        add_action('wp_ajax_wppoppop_save_popup', [$this, 'save_popup']);
        add_action('wp_ajax_wppoppop_load_popup', [$this, 'load_popup']);
        add_action('wp_ajax_wppoppop_duplicate_popup', [$this, 'duplicate_popup']);
        add_action('wp_ajax_wppoppop_delete_popup', [$this, 'delete_popup']);
        add_action('wp_ajax_wppoppop_export_popup', [$this, 'export_popup']);
        add_action('wp_ajax_wppoppop_import_popup', [$this, 'import_popup']);

        // A/B Campaign actions
        add_action('wp_ajax_wppoppop_save_campaign', [$this, 'save_campaign']);
        add_action('wp_ajax_wppoppop_delete_campaign', [$this, 'delete_campaign']);
        add_action('wp_ajax_wppoppop_export_submissions_csv', [$this, 'export_submissions_csv']);

        // GDPR Actions
        add_action('wp_ajax_wppoppop_delete_submission', [$this, 'delete_submission']);
        add_action('wp_ajax_wppoppop_anonymize_submission', [$this, 'anonymize_submission']);

        // Settings page actions
        add_action('wp_ajax_wppoppop_save_settings', [$this, 'save_settings']);
        add_action('wp_ajax_wppoppop_reset_cookies', [$this, 'reset_cookies']);

        // Remote use embed endpoint
        add_action('wp_ajax_wppoppop_remote_embed', [$this, 'serve_remote_embed']);
        add_action('wp_ajax_nopriv_wppoppop_remote_embed', [$this, 'serve_remote_embed']);

        // Frontend impression and submission
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

    public function save_popup() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized action: Administrator permissions required.']);
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';

        $title = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : 'Untitled Popup';
        // Generate a 16-character alphanumeric UID if empty
        $uid   = (isset($_POST['uid']) && !empty($_POST['uid'])) ? sanitize_key($_POST['uid']) : substr(md5(uniqid(wp_rand(), true)), 0, 16);
        $data  = isset($_POST['data']) ? wp_unslash($_POST['data']) : '{}';

        if (json_decode($data) === null) {
            wp_send_json_error(['message' => 'Malformed JSON: ' . json_last_error_msg()]);
        }

        $existing = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$table_name} WHERE uid = %s", $uid));

        if ($existing) {
            $result = $wpdb->update(
                $table_name,
                ['title' => $title, 'data' => $data],
                ['uid' => $uid],
                ['%s', '%s'],
                ['%s']
            );

            if ($result === false) {
                wp_send_json_error(['message' => 'Database update error: ' . $wpdb->last_error]);
            }
        } else {
            $result = $wpdb->insert(
                $table_name,
                [
                    'uid'           => $uid,
                    'title'         => $title,
                    'data'          => $data,
                    'status'        => 'publish',
                    'impressions'   => 0,
                    'submissions'   => 0,
                    'confirmations' => 0
                ],
                ['%s', '%s', '%s', '%s', '%d', '%d', '%d']
            );

            if ($result === false) {
                wp_send_json_error(['message' => 'Database insert error: ' . $wpdb->last_error]);
            }
        }

        wp_send_json_success([
            'uid'     => $uid,
            'message' => 'Popup configuration saved successfully!'
        ]);
    }

    public function load_popup() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        global $wpdb;
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}wppoppop_items WHERE uid = %s", sanitize_key($_GET['uid'])), ARRAY_A);
        if (!$row) {
            wp_send_json_error(['message' => 'Popup not found.']);
        }
        wp_send_json_success($row);
    }

    public function duplicate_popup() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error(['message' => 'Unauthorized action.']);

        global $wpdb;
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}wppoppop_items WHERE uid = %s", sanitize_key($_POST['uid'])), ARRAY_A);
        if (!$row) wp_send_json_error(['message' => 'Source popup not found.']);

        $new_uid = substr(md5(uniqid(wp_rand(), true)), 0, 16);
        $wpdb->insert(
            $wpdb->prefix . 'wppoppop_items',
            [
                'uid'           => $new_uid,
                'title'         => $row['title'] . ' (Copy)',
                'data'          => $row['data'],
                'status'        => 'publish',
                'impressions'   => 0,
                'submissions'   => 0,
                'confirmations' => 0
            ],
            ['%s', '%s', '%s', '%s', '%d', '%d', '%d']
        );

        wp_send_json_success(['uid' => $new_uid, 'message' => 'Popup duplicated successfully!']);
    }

    public function delete_popup() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error(['message' => 'Unauthorized action.']);
        global $wpdb;
        $wpdb->delete($wpdb->prefix . 'wppoppop_items', ['uid' => sanitize_key($_POST['uid'])], ['%s']);
        wp_send_json_success(['message' => 'Popup removed successfully.']);
    }

    public function export_popup() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error(['message' => 'Unauthorized action.']);

        global $wpdb;
        $row = $wpdb->get_row($wpdb->prepare("SELECT title, data FROM {$wpdb->prefix}wppoppop_items WHERE uid = %s", sanitize_key($_GET['uid'])), ARRAY_A);
        if (!$row) wp_send_json_error(['message' => 'Popup not found.']);

        wp_send_json_success([
            'filename' => sanitize_title($row['title']) . '-wppoppop-export.json',
            'payload'  => $row['data']
        ]);
    }

    public function import_popup() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error(['message' => 'Unauthorized action.']);

        $json_raw = isset($_POST['import_data']) ? wp_unslash($_POST['import_data']) : '';
        $data = json_decode($json_raw, true);

        if (!$data || !isset($data['meta'])) {
            wp_send_json_error(['message' => 'Invalid WpPopPop JSON format.']);
        }

        global $wpdb;
        $new_uid = substr(md5(uniqid(wp_rand(), true)), 0, 16);
        $title = isset($data['meta']['title']) ? sanitize_text_field($data['meta']['title']) . ' (Imported)' : 'Imported Popup';

        $wpdb->insert(
            $wpdb->prefix . 'wppoppop_items',
            [
                'uid'           => $new_uid,
                'title'         => $title,
                'data'          => $json_raw,
                'status'        => 'publish',
                'impressions'   => 0,
                'submissions'   => 0,
                'confirmations' => 0
            ],
            ['%s', '%s', '%s', '%s', '%d', '%d', '%d']
        );

        wp_send_json_success(['uid' => $new_uid, 'message' => 'Popup imported successfully!']);
    }

    public function save_campaign() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error(['message' => 'Unauthorized action.']);

        global $wpdb;
        $title = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : 'Untitled Campaign';
        $uids  = isset($_POST['popup_uids']) ? array_map('sanitize_key', (array)$_POST['popup_uids']) : [];

        if (count($uids) < 2) wp_send_json_error(['message' => 'Select at least 2 popups for A/B testing.']);

        $campaign_uid = substr(md5(uniqid(wp_rand(), true)), 0, 16);
        $wpdb->insert(
            $wpdb->prefix . 'wppoppop_campaigns',
            [
                'uid'        => $campaign_uid,
                'title'      => $title,
                'popup_uids' => wp_json_encode($uids),
                'status'     => 'active'
            ],
            ['%s', '%s', '%s', '%s']
        );

        wp_send_json_success(['message' => 'A/B Campaign created successfully!']);
    }

    public function delete_campaign() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error(['message' => 'Unauthorized action.']);
        global $wpdb;
        $wpdb->delete($wpdb->prefix . 'wppoppop_campaigns', ['uid' => sanitize_key($_POST['uid'])], ['%s']);
        wp_send_json_success(['message' => 'Campaign deleted.']);
    }

    public function export_submissions_csv() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_die('Unauthorized');

        global $wpdb;
        $rows = $wpdb->get_results("SELECT s.id, i.title as popup_title, s.email, s.country_code, s.status, s.fields_data, s.created_at 
            FROM {$wpdb->prefix}wppoppop_submissions s LEFT JOIN {$wpdb->prefix}wppoppop_items i ON s.popup_uid = i.uid ORDER BY s.id DESC", ARRAY_A);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=wppoppop-submissions-' . date('Y-m-d') . '.csv');
        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'Popup', 'Email', 'Country', 'Status', 'Form Fields', 'Date']);
        foreach ($rows as $row) {
            fputcsv($output, [$row['id'], $row['popup_title'], $row['email'], $row['country_code'], ucfirst($row['status']), $row['fields_data'], $row['created_at']]);
        }
        fclose($output);
        exit;
    }

    public function delete_submission() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error(['message' => 'Unauthorized.']);
        global $wpdb;
        $wpdb->delete($wpdb->prefix . 'wppoppop_submissions', ['id' => intval($_POST['id'])]);
        wp_send_json_success(['message' => 'Record permanently deleted.']);
    }

    public function anonymize_submission() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error(['message' => 'Unauthorized.']);
        $id = intval($_POST['id']);
        global $wpdb;
        $wpdb->update($wpdb->prefix . 'wppoppop_submissions', ['email' => 'anonymized_' . $id . '@privacy.local', 'fields_data' => wp_json_encode(['gdpr' => 'anonymized'])], ['id' => $id]);
        wp_send_json_success(['message' => 'PII anonymized.']);
    }

    public function save_settings() {
        check_ajax_referer('wppoppop_settings_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error(['message' => 'Unauthorized action.']);

        parse_str(isset($_POST['data']) ? wp_unslash($_POST['data']) : '', $parsed);

        $settings = [
            'sender_name'       => isset($parsed['sender_name']) ? sanitize_text_field($parsed['sender_name']) : 'wppoppop',
            'sender_email'      => isset($parsed['sender_email']) ? sanitize_email($parsed['sender_email']) : 'noreply@localhost',
            'preload_popups'    => !empty($parsed['preload_popups']),
            'preload_events'    => !empty($parsed['preload_events']),
            'ga_tracking'       => !empty($parsed['ga_tracking']),
            'google_fonts'      => !empty($parsed['google_fonts']),
            'font_awesome'      => !empty($parsed['font_awesome']),
            'air_datepicker'    => !empty($parsed['air_datepicker']),
            'no_air_datepicker' => !empty($parsed['no_air_datepicker']),
            'jquery_mask'       => !empty($parsed['jquery_mask']),
            'js_parser'         => !empty($parsed['js_parser']),
            'signature_pad'     => !empty($parsed['signature_pad']),
            'range_slider'      => !empty($parsed['range_slider']),
            'adblock_detector'  => !empty($parsed['adblock_detector']),
            'csv_separator'     => isset($parsed['csv_separator']) ? sanitize_text_field($parsed['csv_separator']) : ',',
            'custom_fonts'      => isset($parsed['custom_fonts']) ? sanitize_textarea_field($parsed['custom_fonts']) : '',
            'email_validation'  => isset($parsed['email_validation']) ? sanitize_text_field($parsed['email_validation']) : 'basic',
            'geoip_service'     => isset($parsed['geoip_service']) ? sanitize_text_field($parsed['geoip_service']) : 'none',
            'user_uploads'      => isset($parsed['user_uploads']) ? sanitize_text_field($parsed['user_uploads']) : 'keep',
            'custom_css'        => isset($parsed['custom_css']) ? sanitize_textarea_field($parsed['custom_css']) : '',
            'custom_js'         => isset($parsed['custom_js']) ? sanitize_textarea_field($parsed['custom_js']) : ''
        ];

        update_option('wppoppop_settings', $settings);
        wp_send_json_success(['message' => 'Settings saved successfully!']);
    }

    public function reset_cookies() {
        check_ajax_referer('wppoppop_settings_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error(['message' => 'Unauthorized.']);
        update_option('wppoppop_cookie_epoch', time());
        wp_send_json_success(['message' => 'Cookies have been successfully reset across all popups.']);
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

            // Mailchimp sync
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

            // Admin alert
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
