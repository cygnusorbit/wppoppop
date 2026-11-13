<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Core side of the module extension points.
 *
 * Popup settings that belong to extension modules (the Pro plugin) are allow-listed through the
 * `wppoppop_sanitize_popup_settings` filter. When no extension handles a key, the posted value is dropped and,
 * on a normal save, the value already stored for the popup is kept, so deactivating an extension never loses data.
 */
class WpPopPop_Module_Bridge {

    /** Popup-settings keys owned by extension modules. */
    const EXT_KEYS = array(
        'overlay', 'celebration', 'popup_position', 'targeting_triggers', 'sidetab', 'logic_math',
        'payments', 'video', 'targeting', 'quiz', 'frequency', 'custom_code',
        'double_optin', 'autoresponder', 'webhooks', 'woocommerce', 'downloads', 'sms', 'mailchimp', 'getresponse', 'security',
    );

    public static function keys() {
        $keys = apply_filters('wppoppop_extension_settings_keys', self::EXT_KEYS);
        return is_array($keys) ? array_values(array_filter($keys, 'is_string')) : self::EXT_KEYS;
    }

    /** Settings array currently stored for a popup (empty when the popup is new). */
    public static function stored_settings($uid) {
        global $wpdb;
        if ($uid === '' || !is_string($uid)) {
            return array();
        }
        $json = $wpdb->get_var($wpdb->prepare("SELECT data FROM {$wpdb->prefix}wppoppop_items WHERE uid = %s", $uid));
        $data = json_decode((string) $json, true);
        return (is_array($data) && isset($data['settings']) && is_array($data['settings'])) ? $data['settings'] : array();
    }

    /**
     * @param object $settings Decoded `settings` object of a popup payload.
     * @param string $context  'save' (builder save) or 'template' (save as / import template).
     * @param string $uid      Popup uid (save context only).
     * @return object
     */
    public static function sanitize_settings($settings, $context = 'save', $uid = '') {
        if (!is_object($settings)) {
            return $settings;
        }
        $keys = self::keys();
        $prev = ($context === 'save') ? self::stored_settings($uid) : array();
        $raw  = array();
        foreach ($keys as $key) {
            if (property_exists($settings, $key)) {
                $raw[$key] = $settings->$key;
            }
            unset($settings->$key);
        }

        $out = apply_filters('wppoppop_sanitize_popup_settings', $settings, array(
            'context' => $context,
            'raw'     => $raw,
            'prev'    => $prev,
            'uid'     => $uid,
        ));
        if (!is_object($out)) {
            $out = $settings;
        }

        $handled = (array) apply_filters('wppoppop_handled_settings_keys', array());
        foreach ($keys as $key) {
            if (in_array($key, $handled, true)) {
                continue;
            }
            unset($out->$key);
            if ($context === 'save' && isset($prev[$key])) {
                $out->$key = json_decode(wp_json_encode($prev[$key]));
            }
        }
        return $out;
    }

    /** Plain dimmed backdrop used when no extension supplies overlay settings. */
    const DEFAULT_OVERLAY_CSS = 'align-items:center;justify-content:center;background:rgba(15,23,42,0.65);-webkit-backdrop-filter:blur(3px);backdrop-filter:blur(3px);';

    /** @return array{css:string,layers:string} */
    public static function overlay(array $settings) {
        $default = array('css' => self::DEFAULT_OVERLAY_CSS, 'layers' => '');
        $res     = apply_filters('wppoppop_popup_overlay', $default, $settings);
        if (!is_array($res)) {
            return $default;
        }
        return array(
            'css'    => isset($res['css']) && is_string($res['css']) ? $res['css'] : $default['css'],
            'layers' => isset($res['layers']) && is_string($res['layers']) ? $res['layers'] : '',
        );
    }

    /** @return array{css:string,pos:string} */
    public static function position(array $settings) {
        $default = array('css' => '', 'pos' => '');
        $res     = apply_filters('wppoppop_popup_position', $default, $settings);
        if (!is_array($res)) {
            return $default;
        }
        return array(
            'css' => isset($res['css']) && is_string($res['css']) ? $res['css'] : '',
            'pos' => isset($res['pos']) && is_string($res['pos']) ? $res['pos'] : '',
        );
    }

    /** Attributes of a Pay layer button; an extension may refine them through `wppoppop_pay_button_attrs`. */
    public static function pay_attrs(array $el) {
        return (string) apply_filters('wppoppop_pay_button_attrs', self::pay_button_attrs($el), $el);
    }

    /** Fallback attributes of a Pay layer when no extension answers `wppoppop_pay_button_attrs`. */
    public static function pay_button_attrs(array $el) {
        $gateway = (isset($el['gateway']) && in_array($el['gateway'], array('stripe', 'paypal'), true)) ? $el['gateway'] : 'stripe';
        $amount  = (isset($el['amount']) && is_scalar($el['amount']) && is_numeric($el['amount']) && is_finite((float) $el['amount']) && (float) $el['amount'] > 0)
            ? round(min((float) $el['amount'], 9999999.99), 2) : 19.99;
        $currency = (isset($el['currency']) && is_string($el['currency']) && preg_match('/^[A-Za-z]{3}$/', $el['currency'])) ? strtoupper($el['currency']) : 'USD';
        return ' data-gateway="' . esc_attr($gateway) . '" data-amount="' . esc_attr(number_format($amount, 2, '.', '')) . '" data-currency="' . esc_attr($currency) . '"';
    }

    /* ------------------------------------------------------------------------------------------------
     * Submission pipeline. Core stores the lead and calls these wrappers; the Pro plugin answers the hooks.
     * Without it a lead is stored as confirmed and none of the extension steps run.
     * ---------------------------------------------------------------------------------------------- */

    /**
     * Pre-storage checks (antibot, technical info capture).
     *
     * @return array{deny:string,tech:?array,mail_skip:array} `tech` stays null when nothing captured it.
     */
    public static function precheck($uid, array $fields, array $config) {
        $default = array('deny' => '', 'tech' => null, 'mail_skip' => array());
        $res     = apply_filters('wppoppop_submission_precheck', $default, (string) $uid, $fields, $config);
        if (!is_array($res)) {
            return $default;
        }
        return array(
            'deny'      => isset($res['deny']) && is_string($res['deny']) ? $res['deny'] : '',
            'tech'      => isset($res['tech']) && is_array($res['tech']) ? $res['tech'] : null,
            'mail_skip' => isset($res['mail_skip']) && is_array($res['mail_skip']) ? $res['mail_skip'] : array(),
        );
    }

    /** @return array{active:bool,cfg:array} Double Opt-In state of a popup (never active without an extension). */
    public static function double_optin(array $config) {
        $default = array('active' => false, 'cfg' => array());
        $res     = apply_filters('wppoppop_double_optin_config', $default, $config);
        if (!is_array($res)) {
            return $default;
        }
        return array(
            'active' => !empty($res['active']),
            'cfg'    => isset($res['cfg']) && is_array($res['cfg']) ? $res['cfg'] : array(),
        );
    }

    /** Sends the Double Opt-In confirmation email; false when no extension sent it. */
    public static function send_double_optin($uid, array $cfg, $email, array $fields, $token, $submission_id) {
        return (bool) apply_filters('wppoppop_double_optin_send', false, array(
            'uid' => (string) $uid, 'cfg' => $cfg, 'email' => (string) $email, 'fields' => $fields,
            'token' => (string) $token, 'submission_id' => (int) $submission_id,
        ));
    }

    /**
     * After-submission steps of the extension modules (confirmation mail, autoresponder, webhook, MailChimp,
     * GetResponse, SMS), then the values the form response may carry.
     *
     * @param array $ctx uid, title, config, email, fields, submission_id, country, double_optin, double_optin_cfg, confirm_token.
     * @return array{download_url:string,coupon_code:string}
     */
    public static function dispatch(array $ctx) {
        do_action('wppoppop_submission_dispatch', $ctx);
        $default = array('download_url' => '', 'coupon_code' => '');
        $res     = apply_filters('wppoppop_submission_extras', $default, $ctx);
        if (!is_array($res)) {
            return $default;
        }
        return array(
            'download_url' => isset($res['download_url']) && is_string($res['download_url']) ? $res['download_url'] : '',
            'coupon_code'  => isset($res['coupon_code']) && is_string($res['coupon_code']) ? $res['coupon_code'] : '',
        );
    }

    /** @return array{message:string,url:string} Thank-you text / address shown after an email is confirmed. */
    public static function confirmation_thank(array $config, $email) {
        $default = array('message' => '', 'url' => '');
        $res     = apply_filters('wppoppop_confirmation_thank', $default, $config, (string) $email);
        if (!is_array($res)) {
            return $default;
        }
        return array(
            'message' => isset($res['message']) && is_string($res['message']) ? $res['message'] : '',
            'url'     => isset($res['url']) && is_string($res['url']) ? $res['url'] : '',
        );
    }

    /** Extension steps after a Double Opt-In link was confirmed (same context keys as dispatch(), without the Double Opt-In ones). */
    public static function after_confirmation(array $ctx) {
        do_action('wppoppop_after_confirmation', $ctx);
    }

    /** Submissions > Resend: the user-facing email (confirmation link or autoresponder); false when none was sent. */
    public static function resend_user_email(array $sub, array $config, $email, array $fields, $id) {
        return (bool) apply_filters('wppoppop_resend_user_email', false, $sub, $config, (string) $email, $fields, (int) $id);
    }

    /** Labelled technical-info rows ({{form-data}}) an extension allows in notification emails. */
    public static function notification_tech_rows(array $tech, array $config) {
        $rows = apply_filters('wppoppop_notification_tech_rows', array(), $tech, $config);
        return is_array($rows) ? $rows : array();
    }

    /** WPPOPPOP_EMAIL_MODULES: true when an email module is in use for this popup; the built-in "New Lead Captured" mail is then not sent. */
    public static function email_modules_active(array $config) {
        if (!class_exists('WpPopPop_Notifications')) {
            require_once WPPOPPOP_PATH . 'includes/class-wppoppop-notifications.php';
        }
        $on = WpPopPop_Notifications::module_active() && WpPopPop_Notifications::has_enabled($config);
        return (bool) apply_filters('wppoppop_email_modules_active', $on, $config);
    }

    /** Submission details: an extension may hide the technical info. */
    public static function record_fields($fields, $uid) {
        $res = apply_filters('wppoppop_submission_record_fields', $fields, (string) $uid);
        return is_array($res) ? $res : $fields;
    }

    /** Removes private account data from a popup config before it is printed or returned by a public route. */
    public static function redact($config) {
        if (has_filter('wppoppop_redact_config')) {
            $res = apply_filters('wppoppop_redact_config', $config);
            return is_array($res) ? $res : $config;
        }
        /* No extension is active: the keys of the extension integrations are never exposed. */
        if (is_array($config) && isset($config['settings']) && is_array($config['settings'])) {
            foreach (array('webhooks', 'mailchimp', 'getresponse', 'sms', 'downloads') as $key) {
                unset($config['settings'][$key]);
            }
        }
        return $config;
    }

    /** Two-letter ISO country code of the visitor, '' when unknown (an extension resolves it, e.g. through GeoIP). */
    public static function visitor_country() {
        $c = apply_filters('wppoppop_visitor_country', '');
        return (is_string($c) && preg_match('/^[A-Za-z]{2}$/', $c)) ? strtoupper($c) : '';
    }
}
