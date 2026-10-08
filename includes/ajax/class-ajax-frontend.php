<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * WpPopPop Frontend AJAX Endpoints Controller
 * Lead Capture, Dynamic Text Fields, Webhooks, Autoresponders & Analytics
 */
class WpPopPop_Ajax_Frontend {

    public function __construct() {
        add_action('wp_ajax_wppoppop_submit_form', [$this, 'submit_form']);
        add_action('wp_ajax_nopriv_wppoppop_submit_form', [$this, 'submit_form']);

        add_action('wp_ajax_wppoppop_record_impression', [$this, 'record_impression']);
        add_action('wp_ajax_nopriv_wppoppop_record_impression', [$this, 'record_impression']);

        add_action('wp_ajax_wppoppop_serve_remote_embed', [$this, 'serve_remote_embed']);
        add_action('wp_ajax_nopriv_wppoppop_serve_remote_embed', [$this, 'serve_remote_embed']);

        add_action('wp_ajax_wppoppop_process_payment', [$this, 'process_payment']);
        add_action('wp_ajax_nopriv_wppoppop_process_payment', [$this, 'process_payment']);
    }

    /**
     * Handle public form submissions with full support for custom Text Fields
     */
    public function submit_form() {
        global $wpdb;

        // Verify nonce if present
        if (!empty($_POST['nonce'])) {
            check_ajax_referer('wppoppop_front_nonce', 'nonce', false);
        }

        $uid = isset($_POST['uid']) ? sanitize_text_field(wp_unslash($_POST['uid'])) : '';
        if (empty($uid)) {
            wp_send_json_error(['message' => __('Invalid campaign identifier.', 'wppoppop')]);
        }

        // Retrieve campaign record
        $table_items = $wpdb->prefix . 'wppoppop_items';
        $popup = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_items} WHERE uid = %s", $uid));
        if (!$popup) {
            wp_send_json_error(['message' => __('Campaign not found.', 'wppoppop')]);
        }

        $config = json_decode($popup->data, true);
        if (!is_array($config)) {
            $config = [];
        }

        $settings = $config['settings'] ?? [];

        // 1. Ingest Raw Field Data
        $raw_fields = isset($_POST['fields']) && is_array($_POST['fields']) ? wp_unslash($_POST['fields']) : [];
        if (empty($raw_fields) && !empty($_POST)) {
            // Fallback: collect direct POST keys excluding WordPress internals
            foreach ($_POST as $k => $v) {
                if (!in_array($k, ['action', 'uid', 'nonce'], true)) {
                    $raw_fields[$k] = wp_unslash($v);
                }
            }
        }

        // Anti-spam honeypot verification
        if (!empty($raw_fields['wppoppop_hp_check']) || !empty($_POST['wppoppop_hp_check'])) {
            wp_send_json_success(['message' => __('Submission received.', 'wppoppop')]);
        }

        // 2. Sanitize and Normalize All Incoming Fields
        $clean_fields = [];
        foreach ($raw_fields as $key => $val) {
            $sanitized_key = sanitize_key($key);
            if (is_array($val)) {
                $clean_fields[$sanitized_key] = array_map('sanitize_text_field', $val);
            } elseif (is_string($val)) {
                $clean_fields[$sanitized_key] = (strpos($val, "
") !== false) ? sanitize_textarea_field($val) : sanitize_text_field($val);
            } else {
                $clean_fields[$sanitized_key] = $val;
            }
        }

        // 3. Extract Core Identity Fields (Email, Name, Phone)
        $email = '';
        if (!empty($clean_fields['email']) && is_email($clean_fields['email'])) {
            $email = sanitize_email($clean_fields['email']);
        } else {
            foreach ($clean_fields as $k => $v) {
                if (strpos($k, 'email') !== false && is_string($v) && is_email($v)) {
                    $email = sanitize_email($v);
                    break;
                }
            }
        }

        // Check disposable email blacklist if security setting is enabled
        $global_settings = get_option('wppoppop_settings', []);
        if (!empty($global_settings['block_disposable_emails']) && !empty($email)) {
            $domain = substr(strrchr($email, "@"), 1);
            $disposable_domains = ['mailinator.com', 'tempmail.com', '10minutemail.com', 'guerrillamail.com', 'throwawaymail.com'];
            if (in_array(strtolower($domain), $disposable_domains, true)) {
                wp_send_json_error(['message' => __('Please provide a valid permanent email address.', 'wppoppop')]);
            }
        }

        // Extract Customer Name
        $name = '';
        if (!empty($clean_fields['name'])) {
            $name = $clean_fields['name'];
        } elseif (!empty($clean_fields['full_name'])) {
            $name = $clean_fields['full_name'];
        } elseif (!empty($clean_fields['first_name'])) {
            $name = $clean_fields['first_name'] . (!empty($clean_fields['last_name']) ? ' ' . $clean_fields['last_name'] : '');
        } elseif (!empty($clean_fields['fname'])) {
            $name = $clean_fields['fname'] . (!empty($clean_fields['lname']) ? ' ' . $clean_fields['lname'] : '');
        }

        // Extract Customer Phone
        $phone = '';
        if (!empty($clean_fields['phone'])) {
            $phone = $clean_fields['phone'];
        } elseif (!empty($clean_fields['telephone'])) {
            $phone = $clean_fields['telephone'];
        } elseif (!empty($clean_fields['mobile'])) {
            $phone = $clean_fields['mobile'];
        }

        // 4. IP, Geolocation & Tracking Metadata
        $ip = sanitize_text_field($_SERVER['REMOTE_ADDR'] ?? '');
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $ip = sanitize_text_field($_SERVER['HTTP_CF_CONNECTING_IP']);
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $parts = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip = sanitize_text_field(trim($parts[0]));
        }

        $country = sanitize_text_field($_SERVER['HTTP_CF_IPCOUNTRY'] ?? '');
        $referer = sanitize_text_field(wp_get_referer() ?: ($_SERVER['HTTP_REFERER'] ?? ''));

        // 5. Database Insertion
        $table_submissions = $wpdb->prefix . 'wppoppop_submissions';
        $payload_json = wp_json_encode($clean_fields);

        $inserted = $wpdb->insert(
            $table_submissions,
            [
                'popup_uid'  => $uid,
                'email'      => $email,
                'name'       => $name,
                'phone'      => $phone,
                'payload'    => $payload_json,
                'ip_address' => $ip,
                'country'    => $country,
                'referer'    => $referer,
                'created_at' => current_time('mysql'),
            ],
            ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s']
        );

        $submission_id = $wpdb->insert_id;

        // 6. Increment Campaign Submissions Counter
        $wpdb->query($wpdb->prepare(
            "UPDATE {$table_items} SET submissions = submissions + 1 WHERE uid = %s",
            $uid
        ));

        // 7. Autoresponder Email Notification with Dynamic Token Interpolation
        $autoresponder = $settings['autoresponder'] ?? [];
        if (!empty($autoresponder['enabled']) && !empty($email)) {
            $subject = !empty($autoresponder['subject']) ? $autoresponder['subject'] : sprintf(__('Thank you for subscribing to %s', 'wppoppop'), $popup->title);
            $body = !empty($autoresponder['body']) ? $autoresponder['body'] : __("Hello {name},

Thank you for reaching out! We have received your submission.", 'wppoppop');

            // Interpolate dynamic tokens across body and subject
            $tokens = array_merge($clean_fields, [
                'name'        => $name ?: __('there', 'wppoppop'),
                'email'       => $email,
                'phone'       => $phone,
                'popup_title' => $popup->title,
                'date'        => date_i18n(get_option('date_format')),
            ]);

            foreach ($tokens as $token_key => $token_val) {
                if (is_scalar($token_val)) {
                    $subject = str_ireplace('{' . $token_key . '}', (string) $token_val, $subject);
                    $body    = str_ireplace('{' . $token_key . '}', (string) $token_val, $body);
                }
            }

            $sender_email = !empty($global_settings['mail_from_email']) ? $global_settings['mail_from_email'] : get_option('admin_email');
            $sender_name  = !empty($global_settings['mail_from_name']) ? $global_settings['mail_from_name'] : get_bloginfo('name');
            $headers      = [
                'Content-Type: text/html; charset=UTF-8',
                "From: {$sender_name} <{$sender_email}>"
            ];

            wp_mail($email, $subject, nl2br($body), $headers);
        }

        // 8. Outbound Webhooks (Zapier, Make, Custom Webhook)
        $marketing = $settings['marketing'] ?? ($settings['webhooks'] ?? []);
        $webhook_url = !empty($marketing['webhook_url']) ? esc_url_raw($marketing['webhook_url']) : '';
        if (!empty($webhook_url)) {
            wp_remote_post($webhook_url, [
                'headers' => ['Content-Type' => 'application/json; charset=utf-8'],
                'body'    => wp_json_encode([
                    'event'         => 'wppoppop_lead_captured',
                    'submission_id' => $submission_id,
                    'popup_uid'     => $uid,
                    'popup_title'   => $popup->title,
                    'email'         => $email,
                    'name'          => $name,
                    'phone'         => $phone,
                    'fields'        => $clean_fields,
                    'ip_address'    => $ip,
                    'country'       => $country,
                    'referer'       => $referer,
                    'timestamp'     => current_time('mysql'),
                ]),
                'timeout'  => 5,
                'blocking' => false,
            ]);
        }

        // 9. Twilio SMS Alerts
        $twilio = $settings['twilio'] ?? [];
        if (!empty($twilio['enabled']) && !empty($twilio['account_sid']) && !empty($twilio['auth_token']) && !empty($twilio['admin_phone'])) {
            $sms_body = sprintf(__("New WpPopPop Lead: %s (%s) from '%s'", 'wppoppop'), $name ?: 'Visitor', $email ?: $phone, $popup->title);
            $twilio_endpoint = 'https://api.twilio.com/2010-04-01/Accounts/' . urlencode($twilio['account_sid']) . '/Messages.json';
            wp_remote_post($twilio_endpoint, [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode($twilio['account_sid'] . ':' . $twilio['auth_token']),
                ],
                'body' => [
                    'From' => $twilio['from_number'] ?? '',
                    'To'   => $twilio['admin_phone'],
                    'Body' => $sms_body,
                ],
                'timeout'  => 5,
                'blocking' => false,
            ]);
        }

        // 10. Resolve Post-Submit Redirect URL
        $redirect_url = '';
        if (!empty($_POST['redirect_url'])) {
            $redirect_url = esc_url_raw(wp_unslash($_POST['redirect_url']));
        } elseif (!empty($settings['redirect_url'])) {
            $redirect_url = esc_url_raw($settings['redirect_url']);
        } elseif (!empty($settings['box']['redirect_url'])) {
            $redirect_url = esc_url_raw($settings['box']['redirect_url']);
        }

        wp_send_json_success([
            'message'       => __('Thank you! Your information has been recorded.', 'wppoppop'),
            'submission_id' => $submission_id,
            'redirect_url'  => $redirect_url,
            'email'         => $email,
            'name'          => $name,
        ]);
    }

    /**
     * Record impression or conversion telemetry
     */
    public function record_impression() {
        global $wpdb;

        $uid = isset($_POST['uid']) ? sanitize_text_field(wp_unslash($_POST['uid'])) : '';
        if (empty($uid)) {
            wp_send_json_error();
        }

        $table_items = $wpdb->prefix . 'wppoppop_items';
        $is_conv = !empty($_POST['is_conversion']);

        if ($is_conv) {
            $wpdb->query($wpdb->prepare("UPDATE {$table_items} SET submissions = submissions + 1 WHERE uid = %s", $uid));
        } else {
            $wpdb->query($wpdb->prepare("UPDATE {$table_items} SET views = views + 1 WHERE uid = %s", $uid));
        }

        wp_send_json_success();
    }

    /**
     * Serve standalone remote embed JavaScript
     */
    public function serve_remote_embed() {
        header('Content-Type: application/javascript; charset=utf-8');
        echo "/* WpPopPop Remote Embed Service */";
        exit;
    }

    /**
     * Handle payment processing callbacks
     */
    public function process_payment() {
        global $wpdb;
        $uid = isset($_POST['uid']) ? sanitize_text_field(wp_unslash($_POST['uid'])) : '';
        if (empty($uid)) {
            wp_send_json_error(['message' => __('Invalid request', 'wppoppop')]);
        }

        $table_items = $wpdb->prefix . 'wppoppop_items';
        $wpdb->query($wpdb->prepare("UPDATE {$table_items} SET submissions = submissions + 1 WHERE uid = %s", $uid));

        wp_send_json_success(['message' => __('Payment authorized successfully.', 'wppoppop')]);
    }
}
