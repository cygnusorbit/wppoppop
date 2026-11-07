<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Rest_Submissions {
    public function register_routes() {
        register_rest_route('wppoppop/v1', '/submit', [
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => [$this, 'handle_submission'],
            'permission_callback' => '__return_true',
        ]);

        register_rest_route('wppoppop/v1', '/impression', [
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => [$this, 'handle_impression'],
            'permission_callback' => '__return_true',
        ]);
    }

    public function handle_impression(WP_REST_Request $request) {
        return WpPopPop_Tracking::rest_impression($request);
    }

    public function handle_submission(WP_REST_Request $request) {
        $params = $request->get_json_params() ?: $request->get_body_params();

        if (!WpPopPop_Tracking::throttle('submit', (int) WpPopPop_Tracking::get('rate_limit_submit'))) {
            return new WP_Error('rate_limited', __('Too many requests. Please try again shortly.', 'wppoppop'), ['status' => 429]);
        }

        // 1. Honeypot Bot Trap Validation
        if (!empty($params['_wppoppop_hp_email'])) {
            return new WP_Error('spam_detected', __('Spam detected by security shield.', 'wppoppop'), ['status' => 400]);
        }

        $uid   = isset($params['uid']) ? sanitize_text_field($params['uid']) : '';
        $email = isset($params['email']) ? sanitize_email($params['email']) : '';
        $fields = isset($params['fields']) ? (array)$params['fields'] : [];
        $visitor_country = isset($params['country']) ? sanitize_text_field($params['country']) : '';
        $quiz_score = isset($params['quiz_score']) ? intval($params['quiz_score']) : null;

        if ($email === '' && is_array($fields)) {
            foreach ($fields as $fk => $fv) {
                if (is_string($fv) && is_email($fv)) { $email = sanitize_email($fv); break; }
            }
        }
        $clean = [];
        foreach ((array) $fields as $fk => $fv) {
            $ck = sanitize_key($fk);
            if ($ck === '') { continue; }
            $clean[$ck] = is_array($fv) ? array_map('sanitize_text_field', $fv)
                : (($ck === 'signature' && is_string($fv) && strpos($fv, 'data:image/') === 0) ? $fv : sanitize_text_field($fv));
        }
        $fields = $clean;
        if ($email !== '' && !is_email($email)) {
            return new WP_Error('invalid_email', __('Please enter a valid email address.', 'wppoppop'), ['status' => 422]);
        }

        // 2. Disposable Email Filter
        $disposable_domains = ['mailinator.com', '10minutemail.com', 'guerrillamail.com', 'trashmail.com', 'tempmail.com', 'sharklasers.com'];
        $domain = $email !== '' ? substr(strrchr($email, '@'), 1) : '';
        if (in_array(strtolower($domain), $disposable_domains, true)) {
            return new WP_Error('disposable_email', __('Disposable and temporary email addresses are not allowed.', 'wppoppop'), ['status' => 422]);
        }

        global $wpdb;
        $items_table = $wpdb->prefix . 'wppoppop_items';
        $subs_table  = $wpdb->prefix . 'wppoppop_submissions';

        $row = $wpdb->get_row($wpdb->prepare("SELECT title, data FROM {$items_table} WHERE uid = %s", $uid), ARRAY_A);
        if (!$row) {
            return new WP_Error('popup_not_found', __('Associated popup configuration was not found.', 'wppoppop'), ['status' => 404]);
        }

        $config = json_decode($row['data'], true) ?: [];

        /* WPPOPPOP_PROTECT_REST: Security & Protections (antibot before anything is stored, technical info only when allowed) */
        $wppoppop_tech = [];
        if (!class_exists('WpPopPop_Protect')) { require_once WPPOPPOP_PATH . 'includes/class-wppoppop-protect.php'; }
        if (WpPopPop_Protect::module_active()) { /* WPPOPPOP_MODULE_GATE */
            $wppoppop_sp   = WpPopPop_Protect::get_config($config);
            $wppoppop_deny = WpPopPop_Protect::antibot($uid, $fields, $wppoppop_sp);
            if ($wppoppop_deny !== '') {
                return new WP_Error('repeated_submission', $wppoppop_deny, ['status' => 429]);
            }
            $wppoppop_tech = WpPopPop_Protect::capture($wppoppop_sp);
        }
        $notif  = $config['notifications'] ?? [];
        if (!class_exists('WpPopPop_Double_Optin')) { require_once WPPOPPOP_PATH . 'includes/class-wppoppop-double-optin.php'; }
        $dopt_cfg        = WpPopPop_Double_Optin::get_config($config);
        $is_double_optin = !empty($dopt_cfg['enable']) || !empty($notif['enable_double_optin']);

        $initial_status = $is_double_optin ? 'pending' : 'confirmed';
        $confirm_token  = $is_double_optin ? wp_generate_password(32, false) : '';

        // Increment submissions counter
        $wpdb->query($wpdb->prepare("UPDATE {$items_table} SET submissions = submissions + 1 WHERE uid = %s", $uid));
        if (!$is_double_optin) {
            $wpdb->query($wpdb->prepare("UPDATE {$items_table} SET confirmations = confirmations + 1 WHERE uid = %s", $uid));
        }

        if ($quiz_score !== null && $quiz_score > 0) {
            $fields['quiz_score'] = $quiz_score;
        }

        // Insert lead record
        $wpdb->insert(
            $subs_table,
            [
                'popup_uid'     => $uid,
                'email'         => $email,
                'fields_data'   => wp_json_encode(array_merge($fields, $wppoppop_tech)),
                'status'        => $initial_status,
                'confirm_token' => $confirm_token,
                'country_code'  => $visitor_country,
            ],
            ['%s', '%s', '%s', '%s', '%s', '%s']
        );
        $submission_id = $wpdb->insert_id;
        if (class_exists('WpPopPop_Admin_Stats')) { WpPopPop_Admin_Stats::record_device($submission_id); }

        // Notifications: "After successful form submission"
        if (!class_exists('WpPopPop_Notifications')) { require_once WPPOPPOP_PATH . 'includes/class-wppoppop-notifications.php'; }
        WpPopPop_Notifications::dispatch('after_submission', $uid, $row['title'], $config, $email, array_merge($fields, $wppoppop_tech), $submission_id, $visitor_country);

        // Double Opt-In: send the confirmation email
        if ($is_double_optin) {
            WpPopPop_Double_Optin::send_confirmation($uid, $dopt_cfg, $email, $fields, $confirm_token, $submission_id);
        }

        // Autoresponder dispatch
        if (!class_exists('WpPopPop_Autoresponder')) { require_once WPPOPPOP_PATH . 'includes/class-wppoppop-autoresponder.php'; }
        $ar_cfg = WpPopPop_Autoresponder::get_config($config);
        if (!empty($ar_cfg['enable']) && !$is_double_optin && $email !== '' && WpPopPop_Autoresponder::module_active()) { /* WPPOPPOP_MODULE_GATE */
            WpPopPop_Autoresponder::send($email, $fields, $ar_cfg);
        }

        // Outgoing Webhook Dispatch
        if (!class_exists('WpPopPop_Webhooks')) { require_once WPPOPPOP_PATH . 'includes/class-wppoppop-webhooks.php'; }
        $wh_cfg = WpPopPop_Webhooks::get_config($config);
        if (!empty($wh_cfg['url']) && WpPopPop_Webhooks::module_active()) { /* WPPOPPOP_MODULE_GATE */
            WpPopPop_Webhooks::send($uid, $submission_id, $email, $fields, $visitor_country, $wh_cfg);
        }

        // MailChimp (module acc-mailchimp): "After successful form submission"
        if (!class_exists('WpPopPop_Mailchimp')) { require_once WPPOPPOP_PATH . 'includes/class-wppoppop-mailchimp.php'; }
        if (WpPopPop_Mailchimp::module_active()) { /* WPPOPPOP_MODULE_GATE */
            WpPopPop_Mailchimp::dispatch('after_submission', $uid, isset($row['title']) ? $row['title'] : '', $config, $email, $fields, $submission_id, $visitor_country);
        }

        // GetResponse (module acc-getresponse): "After successful form submission"
        if (!class_exists('WpPopPop_Getresponse')) { require_once WPPOPPOP_PATH . 'includes/class-wppoppop-getresponse.php'; }
        if (WpPopPop_Getresponse::module_active()) { /* WPPOPPOP_MODULE_GATE */
            WpPopPop_Getresponse::dispatch('after_submission', $uid, isset($row['title']) ? $row['title'] : '', $config, $email, $fields, $submission_id, $visitor_country);
        }

        // Twilio SMS alert (module acc-sms)
        if (!class_exists('WpPopPop_Sms')) { require_once WPPOPPOP_PATH . 'includes/class-wppoppop-sms.php'; }
        $sms_cfg = WpPopPop_Sms::get_config($config, $uid);
        if (!empty($sms_cfg['enable']) && WpPopPop_Sms::module_active()) { /* WPPOPPOP_MODULE_GATE */
            WpPopPop_Sms::notify($uid, isset($row['title']) ? $row['title'] : '', $email, $sms_cfg);
        }

        // WooCommerce coupon (module acc-woocommerce); never before the email is confirmed
        $wc_code = '';
        if (!$is_double_optin) {
            if (!class_exists('WpPopPop_Woocommerce')) { require_once WPPOPPOP_PATH . 'includes/class-wppoppop-woocommerce.php'; }
            $wc_code = WpPopPop_Woocommerce::issue($uid, $email, WpPopPop_Woocommerce::get_config($config)); /* WPPOPPOP_MODULE_GATE */
        }

        // Downloads (module acc-downloads): one-time link; never before the email is confirmed
        $dl_url = '';
        if (!$is_double_optin) {
            if (!class_exists('WpPopPop_Downloads')) { require_once WPPOPPOP_PATH . 'includes/class-wppoppop-downloads.php'; }
            $dl_url = WpPopPop_Downloads::issue($uid, WpPopPop_Downloads::get_config($config)); /* WPPOPPOP_MODULE_GATE */
        }

        $actions = $config['actions'] ?? [];
        $success_message = $is_double_optin
            ? __('Thank you! A verification link has been dispatched to your email address.', 'wppoppop')
            : (!empty($actions['success_message']) ? esc_html($actions['success_message']) : __('Thank you! Your information has been registered.', 'wppoppop'));

        $res = new WP_REST_Response([
            'success'      => true,
            'message'      => $success_message,
            'redirect_url' => (!empty($actions['redirect_url']) && !$is_double_optin) ? esc_url_raw($actions['redirect_url']) : '',
            'download_url' => $dl_url,
            'coupon_code'  => $wc_code,
        ], 200);

        WpPopPop_Tracking::cors($res);
        return $res;
    }
}
