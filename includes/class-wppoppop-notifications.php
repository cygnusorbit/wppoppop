<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * WpPopPop Notifications: per-popup email notifications (settings.notifications).
 * Events: after_submission, after_confirmation (double opt-in confirmed).
 */
class WpPopPop_Notifications {

    public static function defaults() {
        return array(
            'id'           => '',
            'name'         => 'Notification',
            'enable'       => true,
            'send'         => 'after_submission',
            'recipient'    => '',
            'subject'      => __('New submission from {{form-name}}', 'wppoppop'),
            'message'      => '{{form-data}}',
            'attachments'  => array(),
            'reply_to'     => '',
            'from_email'   => '{{global-from-email}}',
            'from_name'    => '{{global-from-name}}',
            'logic_enable' => false,
            'format'       => 'html',
            'logic_match'  => 'all',
            'rules'        => array(),
        );
    }

    /* WPPOPPOP_NTF_FORMAT: Settings > Modules switch (a deleted module is off) */
    public static function module_active() {
        if (!class_exists('WpPopPop_Modules')) {
            return true;
        }
        if (!WpPopPop_Modules::get('acc-notifications')) {
            $orphans = WpPopPop_Modules::orphans();
            return !isset($orphans['acc-notifications']);
        }
        if (!class_exists('WpPopPop_Admin_Settings')) {
            require_once WPPOPPOP_PATH . 'includes/admin/class-admin-settings.php';
        }
        return WpPopPop_Admin_Settings::module_enabled('acc-notifications');
    }

    /** True when the popup has at least one enabled notification. */
    public static function has_enabled($config) {
        foreach (self::get_items($config) as $n) {
            if (!empty($n['enable']) && $n['enable'] !== 'false') {
                return true;
            }
        }
        return false;
    }

    public static function get_items($config) {
        $s = (is_array($config) && isset($config['settings']) && is_array($config['settings'])) ? $config['settings'] : array();
        $list = (isset($s['notifications']) && is_array($s['notifications'])) ? $s['notifications'] : array();
        $out = array();
        foreach ($list as $n) {
            if (is_array($n)) { $out[] = array_merge(self::defaults(), $n); }
        }
        return $out;
    }

    /** Field key => Layer Name (then label / placeholder) for every form layer of a popup; same rules as the Submissions table. */
    public static function field_labels($config) {
        $out = array();
        if (!is_array($config)) { return $out; }
        $types = array('text', 'textfield', 'email', 'number', 'phone', 'textarea', 'select', 'dropdown', 'checkbox', 'checkboxes',
            'radio', 'radios', 'checkboxgroup', 'multiselect', 'imageselect', 'tile', 'date', 'time', 'upload', 'file', 'password', 'rating', 'slider', 'signature');
        $cvs = isset($config['canvases']) ? $config['canvases'] : (isset($config['screens']) ? $config['screens'] : array());
        if (empty($cvs) && !empty($config['elements']) && is_array($config['elements'])) { $cvs = array($config['elements']); }
        if (!is_array($cvs)) { return $out; }
        foreach ($cvs as $cv) {
            $els = (is_array($cv) && isset($cv['elements']) && is_array($cv['elements'])) ? $cv['elements'] : $cv;
            if (!is_array($els)) { continue; }
            foreach ($els as $el) {
                if (!is_array($el) || empty($el['type'])) { continue; }
                $t = strtolower(trim((string) $el['type']));
                if (!in_array($t, $types, true)) { continue; }
                $key = '';
                foreach (array('fieldName', 'token', 'field_name', 'fieldId') as $p) {
                    if (!empty($el[$p])) {
                        $key = sanitize_key((string) $el[$p]);
                        if ($key !== '') { break; }
                    }
                }
                if ($key === '') { $key = sanitize_key($t); }
                if ($key === '' || isset($out[$key])) { continue; }
                $lbl = '';
                foreach (array('name', 'label_text', 'label', 'placeholder') as $p) {
                    if (!empty($el[$p]) && is_string($el[$p])) {
                        $lbl = trim(wp_strip_all_tags($el[$p]));
                        if ($lbl !== '') { break; }
                    }
                }
                if ($lbl === '') { continue; }
                $lbl = mb_substr($lbl, 0, 60);
                $out[$key] = $lbl;
                $out['fields' . $key] = $lbl;
                $out['fields_' . $key] = $lbl;
            }
        }
        return $out;
    }

    public static function merge($tpl, $vars) {
        return preg_replace_callback('/\{\{\s*([A-Za-z0-9_\-]+)\s*\}\}/', function ($m) use ($vars) {
            $k = strtolower($m[1]);
            return isset($vars[$k]) ? $vars[$k] : '';
        }, (string) $tpl);
    }

    private static function flat($fields) {
        $out = array();
        foreach ((array) $fields as $k => $v) {
            $ck = sanitize_key($k);
            if ($ck === '') { continue; }
            $out[$ck] = is_array($v) ? implode(', ', array_map('strval', $v)) : (string) $v;
        }
        return $out;
    }

    private static function sender() {
        $g = get_option('wppoppop_settings', array());
        if (!is_array($g)) { $g = array(); }
        return array(
            !empty($g['sender_email']) ? $g['sender_email'] : get_option('admin_email'),
            !empty($g['sender_name']) ? $g['sender_name'] : get_bloginfo('name'),
        );
    }

    private static function rule_ok($rule, $flat) {
        $key = sanitize_key(isset($rule['field']) ? $rule['field'] : '');
        $val = isset($flat[$key]) ? trim((string) $flat[$key]) : '';
        $cmp = isset($rule['value']) ? trim((string) $rule['value']) : '';
        switch (isset($rule['operator']) ? $rule['operator'] : 'is') {
            case 'is_not':      return strcasecmp($val, $cmp) !== 0;
            case 'contains':    return $cmp !== '' && stripos($val, $cmp) !== false;
            case 'not_contains': return $cmp === '' || stripos($val, $cmp) === false;
            case 'empty':       return $val === '';
            case 'not_empty':   return $val !== '';
            case 'is':
            default:            return strcasecmp($val, $cmp) === 0;
        }
    }

    private static function passes($n, $flat) {
        if (empty($n['logic_enable']) || empty($n['rules']) || !is_array($n['rules'])) { return true; }
        $any = (isset($n['logic_match']) && $n['logic_match'] === 'any');
        foreach ($n['rules'] as $r) {
            if (!is_array($r)) { continue; }
            $ok = self::rule_ok($r, $flat);
            if ($any && $ok) { return true; }
            if (!$any && !$ok) { return false; }
        }
        return !$any;
    }

    private static function files($list) {
        $files = array();
        $up = wp_upload_dir();
        $base = isset($up['basedir']) ? realpath($up['basedir']) : false;
        foreach ((array) $list as $a) {
            $id = is_array($a) && isset($a['id']) ? absint($a['id']) : 0;
            if (!$id) { continue; }
            $path = get_attached_file($id);
            $real = $path ? realpath($path) : false;
            if ($real && $base && strpos($real, $base) === 0 && is_readable($real)) { $files[] = $real; }
        }
        return array_slice($files, 0, 10);
    }

    public static function dispatch($event, $uid, $title, $config, $email, $fields, $submission_id = 0, $country = '') {
        $items = self::get_items($config);
        if (empty($items) || !self::module_active()) { return 0; }
        list($g_email, $g_name) = self::sender();
        $flat = self::flat($fields);
        foreach (array_keys($flat) as $wppoppop_fk) {
            if ($wppoppop_fk === '_name' || $wppoppop_fk === '_phone' || substr((string) $wppoppop_fk, -5) === '_path') { unset($flat[$wppoppop_fk]); }
        }
        /* WPPOPPOP_PROTECT_MAIL: technical info (IP-address, User-Agent) joins {{form-data}} only when Security & Protections allows it */
        $wppoppop_tech = array();
        foreach (array('_ip', '_ua') as $wppoppop_tk) {
            if (isset($flat[$wppoppop_tk])) {
                $wppoppop_tech[$wppoppop_tk] = $flat[$wppoppop_tk];
                unset($flat[$wppoppop_tk]);
            }
        }
        $wppoppop_tech_rows = WpPopPop_Module_Bridge::notification_tech_rows($wppoppop_tech, is_array($config) ? $config : array());
        $sent = 0;

        $rows = '';
        $wppoppop_labels = self::field_labels($config); /* WPPOPPOP_NTF_LABELS */
        foreach ($flat as $k => $v) {
            $rows .= '<tr><td style="padding:4px 10px;border:1px solid #e2e8f0;"><strong>' . esc_html(isset($wppoppop_labels[$k]) ? $wppoppop_labels[$k] : $k) . '</strong></td><td style="padding:4px 10px;border:1px solid #e2e8f0;">' . esc_html($v) . '</td></tr>';
        }
        foreach ($wppoppop_tech_rows as $wppoppop_tk => $wppoppop_tv) {
            $rows .= '<tr><td style="padding:4px 10px;border:1px solid #e2e8f0;"><strong>' . esc_html($wppoppop_tk) . '</strong></td><td style="padding:4px 10px;border:1px solid #e2e8f0;">' . esc_html($wppoppop_tv) . '</td></tr>';
        }
        $form_data = $rows !== '' ? '<table style="border-collapse:collapse;">' . $rows . '</table>' : '';

        $plain = array_merge($flat, array(
            'email'             => (string) $email,
            'form-name'         => (string) $title,
            'site-name'         => get_bloginfo('name'),
            'submission-id'     => (string) $submission_id,
            'country'           => (string) $country,
            'global-from-email' => (string) $g_email,
            'global-from-name'  => (string) $g_name,
            'form-data'         => wp_strip_all_tags(str_replace(array('</tr>', '</td><td'), array("\n", ': </td><td'), $form_data)),
        ));
        $html = array_map('esc_html', $plain);
        $html['form-data'] = $form_data;

        foreach ($items as $n) {
            if (empty($n['enable']) || $n['enable'] === 'false') { continue; }
            if (!in_array($n['send'], array('after_submission', 'after_confirmation'), true) || ($event !== '*' && $n['send'] !== $event)) { continue; } // '*' = manual resend, every event
            if (!self::passes($n, $flat)) { continue; }

            $to = array();
            foreach (explode(',', (string) self::merge($n['recipient'], $plain)) as $addr) {
                $addr = sanitize_email(trim($addr));
                if ($addr !== '' && is_email($addr)) { $to[] = $addr; }
            }
            if (empty($to)) { $to[] = get_option('admin_email'); }

            $subject = sanitize_text_field(self::merge($n['subject'], $plain));
            $wppoppop_text = (isset($n['format']) && $n['format'] === 'text');
            $body    = $wppoppop_text
                ? trim(wp_strip_all_tags(self::merge($n['message'], $plain)))
                : wpautop(wp_kses_post(self::merge($n['message'], $html)));

            $from_email = sanitize_email(trim(self::merge($n['from_email'], $plain)));
            if (!is_email($from_email)) { $from_email = sanitize_email($g_email); }
            $from_name = preg_replace('/[<>"\r\n]+/', '', trim(self::merge($n['from_name'], $plain)));
            if ($from_name === '') { $from_name = preg_replace('/[<>"\r\n]+/', '', (string) $g_name); }

            $headers = array('Content-Type: ' . ($wppoppop_text ? 'text/plain' : 'text/html') . '; charset=UTF-8');
            if (is_email($from_email)) { $headers[] = 'From: ' . $from_name . ' <' . $from_email . '>'; }
            $reply = sanitize_email(trim(self::merge($n['reply_to'], $plain)));
            if ($reply !== '' && is_email($reply)) { $headers[] = 'Reply-To: ' . $reply; }

            $ok = wp_mail($to, $subject, $body, $headers, self::files($n['attachments']));
            if ($ok) { $sent++; }
            if (function_exists('wppoppop_log_event')) {
                wppoppop_log_event($ok ? 'notification_sent' : 'notification_failed', 'Notification "' . sanitize_text_field($n['name']) . '" ' . ($ok ? 'sent to ' : 'FAILED for ') . implode(', ', $to), array('popup_uid' => $uid, 'submission_id' => $submission_id));
            }
        }
        return $sent;
    }
}
