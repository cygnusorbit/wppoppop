<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WpPopPop_Security')) {
    require_once WPPOPPOP_PATH . 'includes/class-wppoppop-security.php';
}

class WpPopPop_Ajax_Frontend {
    public function __construct() {
        add_action('wp_ajax_wppoppop_submit_form', [$this, 'submit_form']);
        add_action('wp_ajax_nopriv_wppoppop_submit_form', [$this, 'submit_form']);
        add_action('wp_ajax_wppoppop_delete_submission', [$this, 'delete_submission']);
        add_action('wp_ajax_wppoppop_bulk_delete_submissions', [$this, 'bulk_delete_submissions']);
    }

    public function submit_form() {
        $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
        if (!wp_verify_nonce($nonce, 'wppoppop_front_nonce') && !wp_verify_nonce($nonce, 'wppoppop_submit_nonce')) {
            wp_send_json_error(['message' => __('Session expired. Please reload the page and try again.', 'wppoppop')]);
        }

        $ip = WpPopPop_Security::get_client_ip();

        // 1. IP Rate Limiting Verification
        $max_per_hour = (int) wppoppop_get_setting('max_submissions_per_ip', 10);
        if (WpPopPop_Security::is_rate_limited($ip,$max_per_hour)) {
            wppoppop_log_event('security_rate_limit', "Rate limit exceeded for IP: {$ip}", ['ip' => $ip]);
            wp_send_json_error([
                'message' => __('Submission rate limit exceeded. Please wait a while before submitting again.', 'wppoppop')
            ]);
        }

        // 2. Google reCAPTCHA Verification
        if ((bool) wppoppop_get_setting('enable_recaptcha', false)) {
            $token = isset($_POST['recaptcha_token']) ? sanitize_text_field(wp_unslash($_POST['recaptcha_token'])) : (isset($_POST['g-recaptcha-response']) ? sanitize_text_field(wp_unslash($_POST['g-recaptcha-response'])) : '');
            if (!WpPopPop_Security::verify_recaptcha($token,$ip)) {
                wppoppop_log_event('security_recaptcha_fail', "reCAPTCHA verification failed for IP: {$ip}", ['ip' => $ip]);
                wp_send_json_error([
                    'message' => __('Bot verification failed. Please refresh the page and try again.', 'wppoppop')
                ]);
            }
        }

        // 3. Extract & Sanitize Form Fields
        $raw_fields = isset($_POST['fields']) && is_array($_POST['fields']) ? wp_unslash($_POST['fields']) : [];
        if (empty($raw_fields)) {
            foreach ($_POST as $k =>$v) {
                if (!in_array($k, ['action', 'nonce', 'popup_uid', 'g-recaptcha-response', 'recaptcha_token'], true)) {$raw_fields[$k] = wp_unslash($v);
                }
            }
        }

        $clean_fields = [];$email = '';
        $name = '';$phone = '';

        foreach ($raw_fields as $key =>$val) {
            $clean_key = sanitize_key($key);
            if (is_array($val)) {$clean_fields[$clean_key] = array_map('sanitize_text_field',$val);
            } else {
                $clean_val = sanitize_text_field($val);$clean_fields[$clean_key] =$clean_val;

                if (empty($email) && (strpos($clean_key, 'email') !== false || filter_var($clean_val, FILTER_VALIDATE_EMAIL))) {
                    $email = sanitize_email($clean_val);
                }
                if (empty($name) && in_array($clean_key, ['name', 'first_name', 'full_name'], true)) {
                    $name =$clean_val;
                }
                if (empty($phone) && (strpos($clean_key, 'phone') !== false || strpos($clean_key, 'tel') !== false)) {
                    $phone =$clean_val;
                }
            }
        }

        // 4. Handle Multipart File Uploads
        if (!empty($_FILES)) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
            foreach ($_FILES as $file_key =>$file_data) {
                if (!empty($file_data['name']) &&$file_data['error'] === UPLOAD_ERR_OK) {
                    $upload_overrides = ['test_form' => false];$movefile = wp_handle_upload($file_data,$upload_overrides);
                    if ($movefile && !isset($movefile['error'])) {
                        $clean_fields[$file_key] = esc_url_raw($movefile['url']);$clean_fields[$file_key . '_path'] = sanitize_text_field($movefile['file']);
                    }
                }
            }
        }

        // 5. Disposable Email Address Filtering
        $email_mode = wppoppop_get_setting('email_validation', 'basic');
        if (!empty($email) &&$email_mode === 'disposable_filter') {
            if (WpPopPop_Security::is_disposable_email($email)) {
                wppoppop_log_event('security_disposable_email', "Rejected disposable email: {$email}", ['ip' => $ip, 'email' =>$email]);
                wp_send_json_error([
                    'message' => __('Disposable or temporary email addresses are not permitted. Please use a valid personal or business email.', 'wppoppop')
                ]);
            }
        }

        // 6. Database Persistence
        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_submissions';$popup_uid  = isset($_POST['popup_uid']) ? sanitize_text_field(wp_unslash($_POST['popup_uid'])) : '';

        $wpdb->insert($table_name,
            [
                'popup_uid'  => $popup_uid,
                'name'       => $name,
                'email'      => $email,
                'phone'      => $phone,
                'payload'    => wp_json_encode($clean_fields),
                'ip_address' => $ip,
                'created_at' => current_time('mysql')
            ],
            ['%s', '%s', '%s', '%s', '%s', '%s', '%s']
        );

        $submission_id =$wpdb->insert_id;

        // 7. Automated Email Notification
        $admin_notification_email = wppoppop_get_setting('admin_email', get_option('admin_email'));
        $from_name                = wppoppop_get_setting('sender_name', wppoppop_get_setting('from_name', get_bloginfo('name')));$from_email               = wppoppop_get_setting('sender_email', wppoppop_get_setting('from_email', get_option('admin_email')));

        if (!empty($admin_notification_email) && is_email($admin_notification_email)) {$lead_identifier = !empty($name) ?$name : (!empty($email) ?$email : 'Anonymous');
            $subject = sprintf('[%s] New Lead Captured: %s', get_bloginfo('name'),$lead_identifier);

            $headers = [
                'Content-Type: text/html; charset=UTF-8',
                'From: ' . esc_attr($from_name) . ' <' . sanitize_email($from_email) . '>',
                'Reply-To: ' . (!empty($email) ? sanitize_email($email) : sanitize_email($from_email))
            ];

            $msg  = '<div style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;max-width:600px;margin:0 auto;padding:24px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;">';
            $msg .= '<h2 style="color:#0f172a;margin-top:0;">' . esc_html__('New Lead Captured', 'wppoppop') . '</h2>';
            $msg .= '<p style="color:#475569;">' . sprintf(esc_html__('A new submission was received on %s for Campaign UID: %s.', 'wppoppop'), esc_html(current_time('mysql')), '<code>' . esc_html($popup_uid) . '</code>') . '</p>';$msg .= '<table style="width:100%;border-collapse:collapse;margin-top:16px;background:#ffffff;border-radius:6px;overflow:hidden;border:1px solid #e2e8f0;">';

            foreach ($clean_fields as $field_label =>$field_val) {
                if (strpos($field_label, '_path') !== false) {
                    continue;
                }
                $msg .= '<tr>';$msg .= '<td style="padding:10px 14px;border-bottom:1px solid #f1f5f9;font-weight:600;color:#334155;width:35%;">' . esc_html(ucwords(str_replace(['_', '-'], ' ', $field_label))) . '</td>';$msg .= '<td style="padding:10px 14px;border-bottom:1px solid #f1f5f9;color:#64748b;">' . esc_html(is_array($field_val) ? implode(', ', $field_val) : $field_val) . '</td>';$msg .= '</tr>';
            }

            $msg .= '<tr><td style="padding:10px 14px;font-weight:600;color:#334155;">' . esc_html__('IP Address', 'wppoppop') . '</td><td style="padding:10px 14px;color:#64748b;">' . esc_html($ip) . '</td></tr>';$msg .= '</table></div>';

            wp_mail($admin_notification_email,$subject, $msg,$headers);
        }

        // Resolve optional redirect destination
        $redirect_url = '';
        if (!empty($_POST['redirect_url'])) {
            $redirect_url = esc_url_raw(wp_unslash($_POST['redirect_url']));
        }

        wp_send_json_success([
            'message'       => __('Thank you! Your submission has been received.', 'wppoppop'),
            'submission_id' => $submission_id,
            'redirect_url'  => $redirect_url
        ]);
    }

    public function delete_submission() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Unauthorized capability.', 'wppoppop')]);
        }
        $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
        if (!wp_verify_nonce($nonce, 'wppoppop_admin_nonce')) {
            wp_send_json_error(['message' => __('Security verification failed.', 'wppoppop')]);
        }

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if (!$id) {
            wp_send_json_error(['message' => __('Invalid submission ID.', 'wppoppop')]);
        }

        global $wpdb;
        $table_name =$wpdb->prefix . 'wppoppop_submissions';

        // Purge files if user_uploads is set to 'delete'
        $user_uploads = wppoppop_get_setting('user_uploads', 'keep');
        if ($user_uploads === 'delete') {$row = $wpdb->get_row($wpdb->prepare("SELECT payload FROM {$table_name} WHERE id = %d", $id));
            if ($row && !empty($row->payload)) {
                $payload = json_decode($row->payload, true);
                if (is_array($payload)) {$upload_dir = wp_upload_dir();
                    $base_dir = realpath($upload_dir['basedir']);
                    foreach ($payload as $key =>$val) {
                        if (is_string($val) && (strpos($key, '_path') !== false || file_exists($val))) {
                            $real_path = realpath($val);
                            if ($real_path && strpos($real_path,$base_dir) === 0) {
                                @unlink($real_path);
                            }
                        }
                    }
                }
            }
        }

        $deleted =$wpdb->delete($table_name, ['id' =>$id], ['%d']);
        if ($deleted) {
            wp_send_json_success(['message' => __('Submission deleted successfully.', 'wppoppop'), 'id' => $id]);
        } else {
            wp_send_json_error(['message' => __('Failed to delete submission.', 'wppoppop')]);
        }
    }

    public function bulk_delete_submissions() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Unauthorized capability.', 'wppoppop')]);
        }
        $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
        if (!wp_verify_nonce($nonce, 'wppoppop_admin_nonce')) {
            wp_send_json_error(['message' => __('Security verification failed.', 'wppoppop')]);
        }

        $ids = isset($_POST['ids']) && is_array($_POST['ids']) ? array_map('intval', $_POST['ids']) : [];
        if (empty($ids)) {
            wp_send_json_error(['message' => __('No submissions selected.', 'wppoppop')]);
        }

        global $wpdb;
        $table_name =$wpdb->prefix . 'wppoppop_submissions';

        $user_uploads = wppoppop_get_setting('user_uploads', 'keep');$upload_dir = wp_upload_dir();
        $base_dir = realpath($upload_dir['basedir']);

        foreach ($ids as$id) {
            if ($user_uploads === 'delete') {$row = $wpdb->get_row($wpdb->prepare("SELECT payload FROM {$table_name} WHERE id = %d", $id));
                if ($row && !empty($row->payload)) {
                    $payload = json_decode($row->payload, true);
                    if (is_array($payload)) {
                        foreach ($payload as $key =>$val) {
                            if (is_string($val) && (strpos($key, '_path') !== false || file_exists($val))) {
                                $real_path = realpath($val);
                                if ($real_path && strpos($real_path,$base_dir) === 0) {
                                    @unlink($real_path);
                                }
                            }
                        }
                    }
                }
            }
            $wpdb->delete($table_name, ['id' =>$id], ['%d']);
        }

        wp_send_json_success(['message' => __('Selected submissions deleted successfully.', 'wppoppop')]);
    }
}
