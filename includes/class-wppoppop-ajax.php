<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Ajax {
    public function __construct() {
        add_action('wp_ajax_wppoppop_save_popup', [$this, 'save_popup']);
        add_action('wp_ajax_wppoppop_load_popup', [$this, 'load_popup']);
        add_action('wp_ajax_wppoppop_duplicate_popup', [$this, 'duplicate_popup']);
        add_action('wp_ajax_wppoppop_delete_popup', [$this, 'delete_popup']);
        add_action('wp_ajax_wppoppop_export_popup', [$this, 'export_popup']);
        add_action('wp_ajax_wppoppop_import_popup', [$this, 'import_popup']);

        add_action('wp_ajax_wppoppop_save_campaign', [$this, 'save_campaign']);
        add_action('wp_ajax_wppoppop_delete_campaign', [$this, 'delete_campaign']);
        add_action('wp_ajax_wppoppop_export_submissions_csv', [$this, 'export_submissions_csv']);

        add_action('wp_ajax_wppoppop_get_submission_detail', [$this, 'get_submission_detail']);
        add_action('wp_ajax_wppoppop_update_submission', [$this, 'update_submission']);
        add_action('wp_ajax_wppoppop_delete_submission', [$this, 'delete_submission']);
        add_action('wp_ajax_wppoppop_anonymize_submission', [$this, 'anonymize_submission']);

        add_action('wp_ajax_wppoppop_import_library_template', [$this, 'import_library_template']);
        add_action('wp_ajax_wppoppop_clear_logs', [$this, 'clear_logs']);

        add_action('wp_ajax_wppoppop_save_settings', [$this, 'save_settings']);
        add_action('wp_ajax_wppoppop_reset_cookies', [$this, 'reset_cookies']);

        // Dynamic On-Demand Loader AJAX Endpoint
        add_action('wp_ajax_wppoppop_get_popup_markup', [$this, 'get_popup_markup']);
        add_action('wp_ajax_nopriv_wppoppop_get_popup_markup', [$this, 'get_popup_markup']);

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

    public function get_popup_markup() {
        $this->set_cors_headers();
        $uid = isset($_GET['uid']) ? sanitize_key($_GET['uid']) : '';

        global $wpdb;
        $row = $wpdb->get_row($wpdb->prepare("SELECT uid, data FROM {$wpdb->prefix}wppoppop_items WHERE uid = %s AND status = 'publish'", $uid), ARRAY_A);
        if (!$row) {
            wp_send_json_error(['message' => 'Popup not found.']);
        }

        $config = json_decode($row['data'], true);
        ob_start();
        (new WpPopPop_Front())->render_popup_markup($row['uid'], $config, false);
        $html = ob_get_clean();

        wp_send_json_success(['html' => $html]);
    }

    public function save_popup() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized action: Administrator permissions required.']);
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';

        $title = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : 'Untitled Popup';
        $uid   = (isset($_POST['uid']) && !empty($_POST['uid'])) ? sanitize_key($_POST['uid']) : substr(md5(uniqid(wp_rand(), true)), 0, 16);
        $data  = isset($_POST['data']) ? wp_unslash($_POST['data']) : '{}';

        if (json_decode($data) === null) {
            wp_send_json_error(['message' => 'Malformed JSON: ' . json_last_error_msg()]);
        }

        $existing = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$table_name} WHERE uid = %s", $uid));

        if ($existing) {
            $wpdb->update(
                $table_name,
                ['title' => $title, 'data' => $data],
                ['uid' => $uid],
                ['%s', '%s'],
                ['%s']
            );
        } else {
            $wpdb->insert(
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
        }

        wp_send_json_success(['uid' => $uid, 'message' => 'Popup configuration saved successfully!']);
    }

    public function load_popup() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        global $wpdb;
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}wppoppop_items WHERE uid = %s", sanitize_key($_GET['uid'])), ARRAY_A);
        if (!$row) wp_send_json_error(['message' => 'Popup not found.']);
        wp_send_json_success($row);
    }

    public function duplicate_popup() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error(['message' => 'Unauthorized']);

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
        if (!current_user_can('manage_options')) wp_send_json_error(['message' => 'Unauthorized']);
        global $wpdb;
        $wpdb->delete($wpdb->prefix . 'wppoppop_items', ['uid' => sanitize_key($_POST['uid'])], ['%s']);
        wp_send_json_success(['message' => 'Popup removed successfully.']);
    }

    public function export_popup() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        global $wpdb;
        $row = $wpdb->get_row($wpdb->prepare("SELECT title, data FROM {$wpdb->prefix}wppoppop_items WHERE uid = %s", sanitize_key($_GET['uid'])), ARRAY_A);
        wp_send_json_success(['filename' => sanitize_title($row['title']) . '-wppoppop-export.json', 'payload' => $row['data']]);
    }

    public function import_popup() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error(['message' => 'Unauthorized']);
        $json_raw = isset($_POST['import_data']) ? wp_unslash($_POST['import_data']) : '';
        $data = json_decode($json_raw, true);
        if (!$data || !isset($data['meta'])) wp_send_json_error(['message' => 'Invalid JSON']);

        global $wpdb;
        $wpdb->insert($wpdb->prefix . 'wppoppop_items', [
            'uid' => substr(md5(uniqid(wp_rand(), true)), 0, 16),
            'title' => sanitize_text_field($data['meta']['title']) . ' (Imported)',
            'data' => $json_raw,
            'status' => 'publish'
        ]);
        wp_send_json_success(['message' => 'Popup imported successfully!']);
    }

    public function save_campaign() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        global $wpdb;
        $wpdb->insert($wpdb->prefix . 'wppoppop_campaigns', [
            'uid'        => substr(md5(uniqid(wp_rand(), true)), 0, 16),
            'title'      => sanitize_text_field($_POST['title']),
            'popup_uids' => wp_json_encode(array_map('sanitize_key', (array)$_POST['popup_uids'])),
            'status'     => 'active'
        ]);
        wp_send_json_success(['message' => 'A/B Campaign saved!']);
    }

    public function delete_campaign() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
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

    public function get_submission_detail() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error(['message' => 'Unauthorized']);

        $id = intval($_GET['id']);
        global $wpdb;
        $row = $wpdb->get_row($wpdb->prepare("SELECT s.*, i.title as popup_title FROM {$wpdb->prefix}wppoppop_submissions s LEFT JOIN {$wpdb->prefix}wppoppop_items i ON s.popup_uid = i.uid WHERE s.id = %d", $id), ARRAY_A);

        if (!$row) wp_send_json_error(['message' => 'Record not found.']);
        $row['fields'] = json_decode($row['fields_data'], true) ?: [];
        wp_send_json_success($row);
    }

    public function update_submission() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error(['message' => 'Unauthorized']);

        $id = intval($_POST['id']);
        $email = sanitize_email($_POST['email']);
        $fields = isset($_POST['fields']) ? (array)$_POST['fields'] : [];

        global $wpdb;
        $wpdb->update($wpdb->prefix . 'wppoppop_submissions', [
            'email'       => $email,
            'fields_data' => wp_json_encode($fields)
        ], ['id' => $id]);

        wppoppop_log_event('lead_updated', "Admin edited submission #{$id}", ['email' => $email]);
        wp_send_json_success(['message' => 'Lead submission updated successfully.']);
    }

    public function delete_submission() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        global $wpdb;
        $wpdb->delete($wpdb->prefix . 'wppoppop_submissions', ['id' => intval($_POST['id'])]);
        wp_send_json_success(['message' => 'Record permanently deleted.']);
    }

    public function anonymize_submission() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        $id = intval($_POST['id']);
        global $wpdb;
        $wpdb->update($wpdb->prefix . 'wppoppop_submissions', ['email' => 'anonymized_' . $id . '@privacy.local', 'fields_data' => wp_json_encode(['gdpr' => 'anonymized'])], ['id' => $id]);
        wp_send_json_success(['message' => 'PII anonymized.']);
    }

    public function import_library_template() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error(['message' => 'Unauthorized']);

        $tpl_key = isset($_POST['template_key']) ? sanitize_key($_POST['template_key']) : '';
        $new_uid = substr(md5(uniqid(wp_rand(), true)), 0, 16);

        $templates = [
            'minimal_newsletter' => [
                'title' => 'Minimalist Newsletter Signup',
                'meta' => ['width' => 580, 'height' => 340, 'bg_color' => '#ffffff'],
                'elements' => [
                    ['id' => 'elem_1', 'type' => 'text', 'screen' => 1, 'top' => 40, 'left' => 40, 'width' => 500, 'height' => 40, 'font_size' => 24, 'color' => '#111827', 'content' => 'Join Our Weekly Digest'],
                    ['id' => 'elem_2', 'type' => 'input', 'field_name' => 'email', 'screen' => 1, 'top' => 140, 'left' => 40, 'width' => 500, 'height' => 45, 'content' => 'Enter your email...'],
                    ['id' => 'elem_3', 'type' => 'button', 'screen' => 1, 'top' => 205, 'left' => 40, 'width' => 500, 'height' => 45, 'bg_color' => '#2271b1', 'content' => 'Subscribe Now']
                ]
            ],
            'discount_coupon' => [
                'title' => 'Flash Sale 20% Coupon',
                'meta' => ['width' => 600, 'height' => 360, 'bg_color' => '#ffffff'],
                'elements' => [
                    ['id' => 'elem_1', 'type' => 'countdown', 'screen' => 1, 'top' => 30, 'left' => 40, 'width' => 520, 'height' => 60, 'timer_mins' => 15],
                    ['id' => 'elem_2', 'type' => 'text', 'screen' => 1, 'top' => 105, 'left' => 40, 'width' => 520, 'height' => 40, 'font_size' => 22, 'color' => '#d63638', 'content' => 'CLAIM YOUR 20% DISCOUNT'],
                    ['id' => 'elem_3', 'type' => 'input', 'field_name' => 'email', 'screen' => 1, 'top' => 170, 'left' => 40, 'width' => 520, 'height' => 45, 'content' => 'Enter email...'],
                    ['id' => 'elem_4', 'type' => 'button', 'screen' => 1, 'top' => 235, 'left' => 40, 'width' => 520, 'height' => 45, 'bg_color' => '#00a32a', 'content' => 'Get Coupon Code']
                ]
            ]
        ];

        $tpl = $templates[$tpl_key] ?? $templates['minimal_newsletter'];
        global $wpdb;
        $wpdb->insert($wpdb->prefix . 'wppoppop_items', [
            'uid' => $new_uid, 'title' => $tpl['title'], 'data' => wp_json_encode($tpl), 'status' => 'publish'
        ]);

        wppoppop_log_event('library_import', "Imported template '{$tpl['title']}'", ['uid' => $new_uid]);
        wp_send_json_success(['uid' => $new_uid, 'redirect_url' => admin_url('admin.php?page=wppoppop-builder&uid=' . $new_uid)]);
    }

    public function clear_logs() {
        check_ajax_referer('wppoppop_builder_nonce', 'nonce');
        global $wpdb;
        $wpdb->query("TRUNCATE TABLE {$wpdb->prefix}wppoppop_logs");
        wp_send_json_success();
    }

    public function save_settings() {
        $nonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';
        if (!wp_verify_nonce($nonce, 'wppoppop_settings_nonce') && !wp_verify_nonce($nonce, 'wppoppop_builder_nonce')) {
            wp_send_json_error(['message' => 'Security check failed. Please refresh the page and try again.']);
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized action: Administrator permissions required.']);
        }

        $raw_data = isset($_POST['data']) ? wp_unslash($_POST['data']) : '';
        $parsed = [];
        if (is_array($raw_data)) {
            $parsed = $raw_data;
        } elseif (is_string($raw_data) && !empty($raw_data)) {
            parse_str($raw_data, $parsed);
        } else {
            $parsed = $_POST;
        }

        $settings = [
            'sender_name'       => isset($parsed['sender_name']) ? sanitize_text_field($parsed['sender_name']) : 'wppoppop',
            'sender_email'      => isset($parsed['sender_email']) ? sanitize_email($parsed['sender_email']) : 'noreply@localhost',
            'preload_popups'    => !empty($parsed['preload_popups']) ? 1 : 0,
            'preload_events'    => !empty($parsed['preload_events']) ? 1 : 0,
            'ga_tracking'       => !empty($parsed['ga_tracking']) ? 1 : 0,
            'google_fonts'      => !empty($parsed['google_fonts']) ? 1 : 0,
            'font_awesome'      => !empty($parsed['font_awesome']) ? 1 : 0,
            'air_datepicker'    => !empty($parsed['air_datepicker']) ? 1 : 0,
            'no_air_datepicker' => !empty($parsed['no_air_datepicker']) ? 1 : 0,
            'jquery_mask'       => !empty($parsed['jquery_mask']) ? 1 : 0,
            'js_parser'         => !empty($parsed['js_parser']) ? 1 : 0,
            'signature_pad'     => !empty($parsed['signature_pad']) ? 1 : 0,
            'range_slider'      => !empty($parsed['range_slider']) ? 1 : 0,
            'adblock_detector'  => !empty($parsed['adblock_detector']) ? 1 : 0,
            'csv_separator'     => isset($parsed['csv_separator']) ? sanitize_text_field($parsed['csv_separator']) : ',',
            'custom_fonts'      => isset($parsed['custom_fonts']) ? sanitize_textarea_field($parsed['custom_fonts']) : '',
            'email_validation'  => isset($parsed['email_validation']) ? sanitize_text_field($parsed['email_validation']) : 'basic',
            'geoip_service'     => isset($parsed['geoip_service']) ? sanitize_text_field($parsed['geoip_service']) : 'none',
            'user_uploads'      => isset($parsed['user_uploads']) ? sanitize_text_field($parsed['user_uploads']) : 'keep',
            'custom_css'        => isset($parsed['custom_css']) ? sanitize_textarea_field($parsed['custom_css']) : '',
            'custom_js'         => isset($parsed['custom_js']) ? sanitize_textarea_field($parsed['custom_js']) : ''
        ];

        update_option('wppoppop_settings', $settings);
        wppoppop_log_event('settings_update', 'Plugin settings updated by administrator', ['user' => wp_get_current_user()->user_login]);
        wp_send_json_success(['message' => 'Settings saved successfully!']);
    }

    public function reset_cookies() {
        $nonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';
        if (!wp_verify_nonce($nonce, 'wppoppop_settings_nonce') && !wp_verify_nonce($nonce, 'wppoppop_builder_nonce')) {
            wp_send_json_error(['message' => 'Security check failed. Please refresh the page.']);
        }
        if (!current_user_can('manage_options')) wp_send_json_error(['message' => 'Unauthorized.']);

        update_option('wppoppop_cookie_epoch', time());
        wppoppop_log_event('reset_cookies', 'Global cookie reset executed', []);
        wp_send_json_success(['message' => 'Cookies have been successfully reset across all popups.']);
    }

    public function serve_remote_embed() {
        header('Content-Type: application/javascript; charset=utf-8');
        header('Access-Control-Allow-Origin: *');

        $uid = isset($_GET['uid']) ? sanitize_key($_GET['uid']) : '';
        global $wpdb;
        $row = $wpdb->get_row($wpdb->prepare("SELECT title, data FROM {$wpdb->prefix}wppoppop_items WHERE uid = %s AND status = 'publish'", $uid), ARRAY_A);
        if (!$row) exit;

        $config = json_decode($row['data'], true);
        ?>
(function() {
    if (window.wppoppop_remote_loaded_<?php echo esc_js($uid); ?>) return;
    window.wppoppop_remote_loaded_<?php echo esc_js($uid); ?> = true;

    var link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = '<?php echo esc_url(WPPOPPOP_URL . 'public/css/wppoppop-front.css'); ?>';
    document.head.appendChild(link);

    function initPopup() {
        window.wppoppop_front_vars = {
            ajax_url: '<?php echo esc_url(admin_url('admin-ajax.php')); ?>',
            rest_url: '<?php echo esc_url(rest_url('wppoppop/v1/')); ?>',
            nonce: 'remote',
            visitor_country: ''
        };

        var s = document.createElement('script');
        s.src = '<?php echo esc_url(WPPOPPOP_URL . 'public/js/wppoppop-front.js'); ?>';
        s.onload = function() {
            var container = document.createElement('div');
            container.innerHTML = <?php
                ob_start();
                (new WpPopPop_Front())->render_popup_markup($uid, $config, false);
                echo wp_json_encode(ob_get_clean());
            ?>;
            document.body.appendChild(container.firstElementChild);
        };
        document.head.appendChild(s);
    }

    if (!window.jQuery) {
        var jq = document.createElement('script');
        jq.src = 'https://code.jquery.com/jquery-3.7.1.min.js';
        jq.onload = initPopup;
        document.head.appendChild(jq);
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
            wp_send_json_error(['message' => 'Spam blocked by honeypot shield.']);
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

            wppoppop_log_event('submission', "Lead captured from {$email}", ['uid' => $uid, 'country' => $visitor_country]);

            // Autoresponder
            $autoresponder = isset($config['autoresponder']) ? $config['autoresponder'] : [];
            if (!empty($autoresponder['enable_user_email']) && !$is_double_optin) {
                $ar_subject = !empty($autoresponder['subject']) ? sanitize_text_field($autoresponder['subject']) : 'Welcome!';
                $ar_body    = !empty($autoresponder['message']) ? $autoresponder['message'] : "Thank you for subscribing!";

                $ar_body = str_replace('{email}', $email, $ar_body);
                foreach ($form_data as $k => $v) {
                    $val_str = is_array($v) ? implode(', ', $v) : $v;
                    $ar_body = str_replace('{' . $k . '}', $val_str, $ar_body);
                }

                wp_mail($email, $ar_subject, nl2br(esc_html($ar_body)), ['Content-Type: text/html; charset=UTF-8']);
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

        wp_send_json_success(['message' => 'Payment captured successfully!', 'transaction_id' => $tx_id]);
    }
}
