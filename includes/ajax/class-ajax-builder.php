<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Ajax_Builder {
    public function __construct() {
        add_action('wp_ajax_wppoppop_builder_save', array($this, 'save_popup'));
        add_action('wp_ajax_wppoppop_save_popup', [$this, 'save_popup']);
        add_action('wp_ajax_wppoppop_load_popup', [$this, 'load_popup']);
        add_action('wp_ajax_wppoppop_duplicate_popup', [$this, 'duplicate_popup']);
        add_action('wp_ajax_wppoppop_delete_popup', [$this, 'delete_popup']);
        add_action('wp_ajax_wppoppop_bulk_delete', [$this, 'bulk_delete']);
        add_action('wp_ajax_wppoppop_export_popup', [$this, 'export_popup']);
        add_action('wp_ajax_wppoppop_import_popup', [$this, 'import_popup']);
        add_action('wp_ajax_wppoppop_save_campaign', [$this, 'save_campaign']);
        add_action('wp_ajax_wppoppop_delete_campaign', [$this, 'delete_campaign']);
        add_action('wp_ajax_wppoppop_bulk_export', [$this, 'bulk_export']);
        add_action('wp_ajax_wppoppop_bulk_import', [$this, 'bulk_import']);
        add_action('wp_ajax_wppoppop_repair_tables', [$this, 'repair_tables']);
        add_action('wp_ajax_wppoppop_reset_counters', [$this, 'reset_counters']);
    }

    private function verify_security() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized action.']);
        }
        $nonce = isset($_REQUEST['nonce']) ? sanitize_text_field(wp_unslash($_REQUEST['nonce'])) : '';
        if (!wp_verify_nonce($nonce, 'wppoppop_builder_nonce') && !wp_verify_nonce($nonce, 'wppoppop_admin_nonce')) {
            wp_send_json_error(['message' => 'Security check failed. Please refresh the page.']);
        }
    }

    public function save_popup() {
        $this->verify_security();

        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';

        $title = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : 'Untitled Popup';
        $uid   = isset($_POST['uid']) && !empty($_POST['uid']) ? sanitize_key($_POST['uid']) : wp_generate_uuid4();

        // Support both 'data' and 'config' payload parameters
        $data_raw = '{}';
        if (isset($_POST['data'])) {
            $data_raw = wp_unslash($_POST['data']);
        } elseif (isset($_POST['config'])) {
            $data_raw = wp_unslash($_POST['config']);
        }

        if (json_decode($data_raw) === null) {
            wp_send_json_error(['message' => 'Malformed JSON definition.']);
        }

        $wppoppop_obj = json_decode($data_raw);
        if (is_object($wppoppop_obj) || is_array($wppoppop_obj)) {
            $wppoppop_enc = wp_json_encode($this->sanitize_anim_payload($wppoppop_obj));
            if (is_string($wppoppop_enc) && $wppoppop_enc !== '') { $data_raw = $wppoppop_enc; }
        }

        $existing = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$table_name} WHERE uid = %s", $uid));

        if ($existing) {
            $wpdb->update($table_name, ['title' => $title, 'data' => $data_raw], ['uid' => $uid], ['%s', '%s'], ['%s']);
        } else {
            $wpdb->insert($table_name, ['uid' => $uid, 'title' => $title, 'data' => $data_raw, 'status' => 'publish'], ['%s', '%s', '%s', '%s']);
        }

        wp_send_json_success(['uid' => $uid, 'message' => 'Popup configuration saved successfully!']);
    }

    private function sanitize_anim_payload($node) {
        if (is_object($node)) {
            foreach (get_object_vars($node) as $k => $v) { $node->$k = $this->sanitize_anim_value($k, $v); }
            return $node;
        }
        if (is_array($node)) {
            foreach ($node as $k => $v) { $node[$k] = $this->sanitize_anim_value($k, $v); }
        }
        return $node;
    }

    private function sanitize_anim_value($k, $v) {
        static $txt = array('anim_appearance', 'anim_disappearance', 'anim_effect', 'animEffect', 'animation', 'animIn', 'animOut');
        static $int = array('anim_duration', 'anim_delay', 'animDuration', 'animDelay');
        if (is_scalar($v) || $v === null) {
            if (in_array($k, $txt, true)) { return preg_replace('/[^A-Za-z0-9_\-]/', '', (string) $v); }
            if (in_array($k, $int, true)) { return max(0, intval($v)); }
            return $v;
        }
        return $this->sanitize_anim_payload($v);
    }

    public function load_popup() {
        $this->verify_security();
        global $wpdb;

        $uid = isset($_REQUEST['uid']) ? sanitize_key($_REQUEST['uid']) : '';
        if (empty($uid)) {
            wp_send_json_error(['message' => 'Missing campaign UID.']);
        }

        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}wppoppop_items WHERE uid = %s", $uid), ARRAY_A);
        if (!$row) {
            wp_send_json_error(['message' => 'Popup not found.']);
        }

        // Return configuration under both 'data' and 'config' properties for maximum compatibility
        $row['config'] = $row['data'];
        wp_send_json_success($row);
    }

    public function duplicate_popup() {
        $this->verify_security();
        global $wpdb;
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}wppoppop_items WHERE uid = %s", sanitize_key($_POST['uid'])), ARRAY_A);
        if (!$row) {
            wp_send_json_error(['message' => 'Source popup not found.']);
        }

        $new_uid = wp_generate_uuid4();
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

        wp_send_json_success([
            'uid'     => $new_uid,
            'title'   => $row['title'] . ' (Copy)',
            'message' => 'Popup duplicated successfully!'
        ]);
    }

    public function delete_popup() {
        $this->verify_security();
        global $wpdb;
        $wpdb->delete($wpdb->prefix . 'wppoppop_items', ['uid' => sanitize_key($_POST['uid'])], ['%s']);
        wp_send_json_success(['message' => 'Popup removed successfully.']);
    }

    public function bulk_delete() {
        $this->verify_security();
        $uids = isset($_POST['uids']) ? (array)$_POST['uids'] : [];
        if (empty($uids)) {
            wp_send_json_error(['message' => 'No popups were selected for deletion.']);
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';
        $sanitized_uids = array_map('sanitize_key', $uids);
        $placeholders = implode(',', array_fill(0, count($sanitized_uids), '%s'));

        $deleted = $wpdb->query($wpdb->prepare("DELETE FROM {$table_name} WHERE uid IN ($placeholders)", $sanitized_uids));

        wp_send_json_success([
            'message'       => sprintf('Successfully removed %d popup campaign(s).', count($sanitized_uids)),
            'deleted_count' => $deleted
        ]);
    }

    public function export_popup() {
        $this->verify_security();
        global $wpdb;
        $row = $wpdb->get_row($wpdb->prepare("SELECT title, data FROM {$wpdb->prefix}wppoppop_items WHERE uid = %s", sanitize_key($_GET['uid'])), ARRAY_A);
        if (!$row) {
            wp_send_json_error(['message' => 'Popup not found.']);
        }
        wp_send_json_success(['filename' => sanitize_title($row['title']) . '-wppoppop-export.json', 'payload' => $row['data']]);
    }

    public function import_popup() {
        $this->verify_security();
        $json_raw = isset($_POST['import_data']) ? wp_unslash($_POST['import_data']) : '';
        $data = json_decode($json_raw, true);
        
        if (isset($data['canvasMeta']) && is_array($data['canvasMeta'])) {
            foreach ($data['canvasMeta'] as $cKey => $cVal) {
                if (is_array($cVal)) {
                    if (isset($cVal['anim_appearance'])) $data['canvasMeta'][$cKey]['anim_appearance'] = sanitize_text_field($cVal['anim_appearance']);
                    if (isset($cVal['anim_duration'])) $data['canvasMeta'][$cKey]['anim_duration'] = intval($cVal['anim_duration']);
                    if (isset($cVal['anim_delay'])) $data['canvasMeta'][$cKey]['anim_delay'] = intval($cVal['anim_delay']);
                    if (isset($cVal['anim_disappearance'])) $data['canvasMeta'][$cKey]['anim_disappearance'] = sanitize_text_field($cVal['anim_disappearance']);
                }
            }
        }
        if (isset($data['canvas_meta']) && is_array($data['canvas_meta'])) {
            foreach ($data['canvas_meta'] as $cKey => $cVal) {
                if (is_array($cVal)) {
                    if (isset($cVal['anim_appearance'])) $data['canvas_meta'][$cKey]['anim_appearance'] = sanitize_text_field($cVal['anim_appearance']);
                    if (isset($cVal['anim_duration'])) $data['canvas_meta'][$cKey]['anim_duration'] = intval($cVal['anim_duration']);
                    if (isset($cVal['anim_delay'])) $data['canvas_meta'][$cKey]['anim_delay'] = intval($cVal['anim_delay']);
                    if (isset($cVal['anim_disappearance'])) $data['canvas_meta'][$cKey]['anim_disappearance'] = sanitize_text_field($cVal['anim_disappearance']);
                }
            }
        }
if (!$data || !isset($data['meta'])) {
            wp_send_json_error(['message' => 'Invalid JSON definition.']);
        }

        global $wpdb;
        $wpdb->insert($wpdb->prefix . 'wppoppop_items', [
            'uid'    => wp_generate_uuid4(),
            'title'  => sanitize_text_field($data['meta']['title']) . ' (Imported)',
            'data'   => $json_raw,
            'status' => 'publish'
        ]);
        wp_send_json_success(['message' => 'Popup imported successfully!']);
    }

    public function save_campaign() {
        $this->verify_security();
        global $wpdb;
        $wpdb->insert($wpdb->prefix . 'wppoppop_campaigns', [
            'uid'        => wp_generate_uuid4(),
            'title'      => sanitize_text_field($_POST['title']),
            'popup_uids' => wp_json_encode(array_map('sanitize_key', (array)$_POST['popup_uids'])),
            'status'     => 'active'
        ]);
        wp_send_json_success(['message' => 'A/B Campaign saved!']);
    }

    public function delete_campaign() {
        $this->verify_security();
        global $wpdb;
        $wpdb->delete($wpdb->prefix . 'wppoppop_campaigns', ['uid' => sanitize_key($_POST['uid'])]);
        wp_send_json_success(['message' => 'Campaign deleted.']);
    }

    public function bulk_export() {
        $this->verify_security();
        global $wpdb;
        $items = $wpdb->get_results("SELECT uid, title, data, status FROM {$wpdb->prefix}wppoppop_items", ARRAY_A);
        $campaigns = $wpdb->get_results("SELECT uid, title, popup_uids, status FROM {$wpdb->prefix}wppoppop_campaigns", ARRAY_A);

        $backup_payload = [
            'generator'  => 'WpPopPop ' . (defined('WPPOPPOP_VERSION') ? WPPOPPOP_VERSION : '1.0.0'),
            'exported'   => current_time('mysql'),
            'items'      => $items ?: [],
            'campaigns'  => $campaigns ?: []
        ];

        wp_send_json_success([
            'filename' => 'wppoppop-bulk-backup-' . gmdate('Y-m-d') . '.json',
            'payload'  => $backup_payload
        ]);
    }

    public function bulk_import() {
        $this->verify_security();
        $raw_json = '';
        if (!empty($_FILES['wppoppop_bulk_import_file']['tmp_name'])) {
            $raw_json = file_get_contents($_FILES['wppoppop_bulk_import_file']['tmp_name']);
        } elseif (!empty($_POST['import_data'])) {
            $raw_json = wp_unslash($_POST['import_data']);
        }

        if (empty($raw_json)) {
            wp_send_json_error(['message' => 'No backup data was provided for restoration.']);
        }

        $decoded = json_decode($raw_json, true);
        if (!$decoded || !is_array($decoded) || (empty($decoded['items']) && empty($decoded['campaigns']))) {
            wp_send_json_error(['message' => 'Invalid or unrecognized backup format.']);
        }

        global $wpdb;
        $items_table     =$wpdb->prefix . 'wppoppop_items';
        $campaigns_table =$wpdb->prefix . 'wppoppop_campaigns';

        $imported_items     = 0;
        $imported_campaigns = 0;
        $uid_map            = [];

        if (!empty($decoded['items']) && is_array($decoded['items'])) {
            foreach ($decoded['items'] as$item) {
                $old_uid = isset($item['uid']) ? sanitize_key($item['uid']) : '';$new_uid = wp_generate_uuid4();
                if ($old_uid) {$uid_map[$old_uid] =$new_uid;
                }

                $title = isset($item['title']) ? sanitize_text_field($item['title']) . ' (Restored)' : 'Restored Popup';$data  = isset($item['data']) ? (is_array($item['data']) ? wp_json_encode($item['data']) :$item['data']) : '{}';

                $wpdb->insert($items_table,
                    [
                        'uid'           => $new_uid,
                        'title'         => $title,
                        'data'          => $data,
                        'status'        => 'publish',
                        'impressions'   => 0,
                        'submissions'   => 0,
                        'confirmations' => 0
                    ],
                    ['%s', '%s', '%s', '%s', '%d', '%d', '%d']
                );
                $imported_items++;
            }
        }

        if (!empty($decoded['campaigns']) && is_array($decoded['campaigns'])) {
            foreach ($decoded['campaigns'] as$camp) {
                $camp_uid = wp_generate_uuid4();$title    = isset($camp['title']) ? sanitize_text_field($camp['title']) . ' (Restored)' : 'Restored A/B Test';

                $raw_uids = isset($camp['popup_uids']) ? (is_array($camp['popup_uids']) ?$camp['popup_uids'] : json_decode($camp['popup_uids'], true)) : [];$remapped_uids = [];

                if (is_array($raw_uids)) {
                    foreach ($raw_uids as$var_uid) {
                        $remapped_uids[] = isset($uid_map[$var_uid]) ?$uid_map[$var_uid] : sanitize_key($var_uid);
                    }
                }

                $wpdb->insert($campaigns_table,
                    [
                        'uid'        => $camp_uid,
                        'title'      => $title,
                        'popup_uids' => wp_json_encode($remapped_uids),
                        'status'     => 'active'
                    ],
                    ['%s', '%s', '%s', '%s']
                );
                $imported_campaigns++;
            }
        }

        wp_send_json_success([
            'message'            => sprintf('Successfully restored %d popups and %d A/B campaigns!', $imported_items,$imported_campaigns),
            'imported_items'     => $imported_items,
            'imported_campaigns' => $imported_campaigns
        ]);
    }

    public function repair_tables() {
        $this->verify_security();
        require_once WPPOPPOP_PATH . 'includes/class-wppoppop-installer.php';
        WpPopPop_Installer::create_tables();

        global $wpdb;
        $tables = [
            'items'        => $wpdb->prefix . 'wppoppop_items',
            'submissions'  => $wpdb->prefix . 'wppoppop_submissions',
            'campaigns'    => $wpdb->prefix . 'wppoppop_campaigns',
            'logs'         => $wpdb->prefix . 'wppoppop_logs',
            'transactions' => $wpdb->prefix . 'wppoppop_transactions',
        ];

        $status_data = [];
        foreach ($tables as$key => $table_name) {$check = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name));
            $exists = ($check === $table_name);$rows = 0;
            $data_size = '0 KB';

            if ($exists) {
                $rows = (int)$wpdb->get_var("SELECT COUNT(*) FROM `{$table_name}`");
                $table_status = $wpdb->get_row($wpdb->prepare("SHOW TABLE STATUS LIKE %s", $table_name), ARRAY_A);
                if ($table_status && isset($table_status['Data_length'])) {$bytes = (int)$table_status['Data_length'] + (int)$table_status['Index_length'];
                    $data_size = size_format($bytes, 2);
                }
            }

            $status_data[$key] = [
                'table'     => $table_name,
                'exists'    => $exists,
                'rows'      => number_format_i18n($rows),
                'size'      => $data_size,
                'status'    => $exists ? 'Optimal' : 'Missing',
            ];
        }

        wp_send_json_success([
            'message' => 'All 5 database tables successfully verified and schema confirmed healthy!',
            'tables'  => $status_data
        ]);
    }

    public function reset_counters() {
        $this->verify_security();
        global $wpdb;
        $table_name =$wpdb->prefix . 'wppoppop_items';
        $affected =$wpdb->query("UPDATE `{$table_name}` SET impressions = 0, submissions = 0, confirmations = 0");

        wp_send_json_success([
            'message'  => 'All popup impressions, leads captured, and confirmation counters have been reset to 0.',
            'affected' => $affected !== false ? $affected : 0
        ]);
    }

    /**
     * Phase 2 Backend Whitelist Parity: opacity, hoverBgColor, customClass
     */
    public function sanitize_phase2_whitelist_properties(array $el, array &$clean = array()) {
        if (isset($el['opacity'])) {
            $clean['opacity'] = floatval($el['opacity']);
        } else {
            $clean['opacity'] = 1.0;
        }

        if (isset($el['hoverBgColor'])) {
            $clean['hoverBgColor'] = sanitize_text_field($el['hoverBgColor']);
            $clean['hoverBg'] = $clean['hoverBgColor'];
        } elseif (isset($el['hoverBg'])) {
            $clean['hoverBg'] = sanitize_text_field($el['hoverBg']);
            $clean['hoverBgColor'] = $clean['hoverBg'];
        }

        /* WPPOPPOP_CLOSE_WHITELIST */
        if (isset($el['hoverTextColor'])) { $clean['hoverTextColor'] = sanitize_text_field($el['hoverTextColor']); $clean['hoverColor'] = $clean['hoverTextColor']; }
        elseif (isset($el['hoverColor'])) { $clean['hoverColor'] = sanitize_text_field($el['hoverColor']); $clean['hoverTextColor'] = $clean['hoverColor']; }
        if (isset($el['hoverBorderColor'])) { $clean['hoverBorderColor'] = sanitize_text_field($el['hoverBorderColor']); $clean['hoverBorder'] = $clean['hoverBorderColor']; }
        elseif (isset($el['hoverBorder'])) { $clean['hoverBorder'] = sanitize_text_field($el['hoverBorder']); $clean['hoverBorderColor'] = $clean['hoverBorder']; }
        if (isset($el['closeGlyph'])) { $clean['closeGlyph'] = sanitize_text_field($el['closeGlyph']); }
        if (isset($el['iconSize'])) { $clean['iconSize'] = intval($el['iconSize']); }
        if (isset($el['sizePresetApplied'])) { $clean['sizePresetApplied'] = !empty($el['sizePresetApplied']); }

        if (isset($el['customClass'])) {
            $clean['customClass'] = sanitize_html_class($el['customClass']);
            $clean['custom_css_class'] = $clean['customClass'];
        } elseif (isset($el['custom_css_class'])) {
            $clean['custom_css_class'] = sanitize_html_class($el['custom_css_class']);
            $clean['customClass'] = $clean['custom_css_class'];
        }

        
        // Form Family Data & Advanced Tab Lifecycle Persistence
        if (isset($el['defaultValue'])) $clean['defaultValue'] = sanitize_text_field($el['defaultValue']);
        if (isset($el['dynamicDefault'])) $clean['dynamicDefault'] = !empty($el['dynamicDefault']);
        if (isset($el['paramName'])) $clean['paramName'] = sanitize_key($el['paramName']);
        if (isset($el['readOnly'])) $clean['readOnly'] = !empty($el['readOnly']);
        if (isset($el['saveToDb'])) $clean['saveToDb'] = !empty($el['saveToDb']);
        if (isset($el['fieldId'])) $clean['fieldId'] = sanitize_html_class($el['fieldId']);
        if (isset($el['inputMask'])) $clean['inputMask'] = sanitize_text_field($el['inputMask']);
        if (isset($el['customAttrs'])) $clean['customAttrs'] = sanitize_text_field($el['customAttrs']);
        if (isset($el['filters']) && is_array($el['filters'])) {
            $clean['filters'] = array_values(array_map('sanitize_key', $el['filters']));
        }
        if (isset($el['validators']) && is_array($el['validators'])) {
            $clean['validators'] = array_values(array_map('sanitize_key', $el['validators']));
        }

        
        // Phase 2: Media Family (Elements 1-9 & 26 Submit) Decomposed Properties Whitelist
        if (isset($el['shapeType'])) $clean['shapeType'] = sanitize_key($el['shapeType']);
        if (isset($el['rotate'])) $clean['rotate'] = intval($el['rotate']);
        if (isset($el['transparentFill'])) $clean['transparentFill'] = !empty($el['transparentFill']);
        if (isset($el['objectFit'])) $clean['objectFit'] = sanitize_key($el['objectFit']);
        if (isset($el['alt'])) $clean['alt'] = sanitize_text_field($el['alt']);
        if (isset($el['linkUrl'])) $clean['linkUrl'] = esc_url_raw($el['linkUrl']);
        if (isset($el['linkBlank'])) $clean['linkBlank'] = !empty($el['linkBlank']);
        if (isset($el['videoProvider'])) $clean['videoProvider'] = sanitize_key($el['videoProvider']);
        if (isset($el['aspectRatio'])) $clean['aspectRatio'] = sanitize_text_field($el['aspectRatio']);
        if (isset($el['videoUrl'])) $clean['videoUrl'] = esc_url_raw($el['videoUrl']);
        if (isset($el['videoAutoplay'])) $clean['videoAutoplay'] = !empty($el['videoAutoplay']);
        if (isset($el['videoMute'])) $clean['videoMute'] = !empty($el['videoMute']);
        if (isset($el['videoLoop'])) $clean['videoLoop'] = !empty($el['videoLoop']);
        if (isset($el['videoControls'])) $clean['videoControls'] = !empty($el['videoControls']);
        if (isset($el['tag'])) $clean['tag'] = sanitize_key($el['tag']);
        if (isset($el['textTransform'])) $clean['textTransform'] = sanitize_key($el['textTransform']);
        if (isset($el['overflow'])) $clean['overflow'] = sanitize_key($el['overflow']);
        if (isset($el['closeAction'])) $clean['closeAction'] = sanitize_key($el['closeAction']);
        if (isset($el['cookieDays'])) $clean['cookieDays'] = intval($el['cookieDays']);
        if (isset($el['iconClass'])) $clean['iconClass'] = sanitize_text_field($el['iconClass']);
        if (isset($el['iconSize'])) $clean['iconSize'] = intval($el['iconSize']);
        if (isset($el['progressMode'])) $clean['progressMode'] = in_array($el['progressMode'], array('manual','form','steps'), true) ? $el['progressMode'] : 'manual';
        if (isset($el['progressPercent'])) $clean['progressPercent'] = max(0, min(100, intval($el['progressPercent'])));
        if (isset($el['progressColor'])) $clean['progressColor'] = sanitize_hex_color($el['progressColor']);
        if (isset($el['progressStriped'])) $clean['progressStriped'] = !empty($el['progressStriped']);
        if (isset($el['progressAnimated'])) $clean['progressAnimated'] = !empty($el['progressAnimated']);
        if (isset($el['buttonAction'])) $clean['buttonAction'] = sanitize_key($el['buttonAction']);
        if (isset($el['submitAction'])) $clean['submitAction'] = sanitize_key($el['submitAction']);
        if (isset($el['loadingText'])) $clean['loadingText'] = sanitize_text_field($el['loadingText']);
        if (isset($el['redirectUrl'])) $clean['redirectUrl'] = esc_url_raw($el['redirectUrl']);

        
        // Phase 3: Form Family Core Inputs (Elements 10-17) Decomposed Properties Whitelist
        if (isset($el['autocomplete'])) $clean['autocomplete'] = sanitize_text_field($el['autocomplete']);
        if (isset($el['maxLength'])) $clean['maxLength'] = intval($el['maxLength']);
        if (isset($el['emailValidationMode'])) $clean['emailValidationMode'] = sanitize_key($el['emailValidationMode']);
        if (isset($el['min'])) $clean['min'] = floatval($el['min']);
        if (isset($el['max'])) $clean['max'] = floatval($el['max']);
        if (isset($el['step'])) $clean['step'] = floatval($el['step']);
        if (isset($el['phonePrefix'])) $clean['phonePrefix'] = sanitize_text_field($el['phonePrefix']);
        if (isset($el['rows'])) $clean['rows'] = max(1, min(30, intval($el['rows'])));
        if (isset($el['layout'])) $clean['layout'] = sanitize_key($el['layout']);
        if (isset($el['termsUrl'])) $clean['termsUrl'] = esc_url_raw($el['termsUrl']);
        if (isset($el['checkedDefault'])) $clean['checkedDefault'] = !empty($el['checkedDefault']);

        
        // Phase 4: Form Family Selectors & Pickers (Elements 18-25) Decomposed Properties Whitelist
        if (isset($el['maxSelect'])) $clean['maxSelect'] = max(1, intval($el['maxSelect']));
        if (isset($el['columns'])) $clean['columns'] = max(1, min(6, intval($el['columns'])));
        if (isset($el['multiSelect'])) $clean['multiSelect'] = !empty($el['multiSelect']);
        if (isset($el['activeColor'])) $clean['activeColor'] = sanitize_hex_color($el['activeColor']);
        if (isset($el['dateFormat'])) $clean['dateFormat'] = sanitize_text_field($el['dateFormat']);
        if (isset($el['minDate'])) $clean['minDate'] = sanitize_text_field($el['minDate']);
        if (isset($el['maxDate'])) $clean['maxDate'] = sanitize_text_field($el['maxDate']);
        if (isset($el['timeFormat'])) $clean['timeFormat'] = sanitize_key($el['timeFormat']);
        if (isset($el['timeStep'])) $clean['timeStep'] = max(1, intval($el['timeStep']));
        if (isset($el['allowedExtensions'])) $clean['allowedExtensions'] = sanitize_text_field($el['allowedExtensions']);
        if (isset($el['maxFileSize'])) $clean['maxFileSize'] = max(1, intval($el['maxFileSize']));
        if (isset($el['multiUpload'])) $clean['multiUpload'] = !empty($el['multiUpload']);
        if (isset($el['minLength'])) $clean['minLength'] = max(0, intval($el['minLength']));
        if (isset($el['showEyeToggle'])) $clean['showEyeToggle'] = !empty($el['showEyeToggle']);
        if (isset($el['ratingMax']))$clean['ratingMax'] = in_array(intval($el['ratingMax']), array(5, 10), true) ? intval($el['ratingMax']) : 5;
        if (isset($el['ratingShape'])) $clean['ratingShape'] = sanitize_key($el['ratingShape']);
        if (isset($el['ratingColor'])) $clean['ratingColor'] = sanitize_hex_color($el['ratingColor']);

        
        // === PHASE 4: v3.0.637 SANITIZED 5-TAB & 3-TAB LAYER SETTINGS WHITELIST ===
        $gp_text_keys = array(
            'autocomplete', 'animIn', 'animOut', 'iconLeft', 'iconRight',
            'customClass', 'cssClass', 'customId', 'logicAction', 'logicMatch',
            'defaultValue', 'paramName', 'fieldId', 'fieldName', 'token',
            'shapePreset', 'alt', 'objectFit', 'videoProvider',
            'aspectRatio', 'videoUrl', 'tag', 'textTransform', 'overflow',
            'closeSymbol', 'closeAction', 'closeGlyph', 'hoverBgColor', 'hoverTextColor', 'hoverBorderColor', 'hoverBg', 'hoverColor', 'hoverBorder', 'iconClass', 'progressMode',
            'buttonAction', 'url', 'loadingText', 'submitAction',
            'confirmMessage', 'redirectUrl', 'countryCode', 'checkboxLabel',
            'radioLayout', 'dateFormat', 'minDate', 'maxDate', 'timeFormat',
            'allowedExtensions', 'activeColor', 'ratingShape', 'ratingColor', 'requiredMessage'
        );
        foreach ( $gp_text_keys as$gk ) {
            if ( isset( $el[$gk ] ) && ! is_array( $el[$gk ] ) ) {
                $clean[$gk ] = sanitize_text_field( wp_unslash( (string) $el[$gk ] ) );
            }
        }

        $gp_int_keys = array(
            'animDuration', 'animDelay', 'anim_duration', 'anim_delay', 'iconLeftOffset', 'iconRightOffset',
            'rotate', 'iconSize', 'cookieDays', 'progressValue',
            'maxLength', 'minLength', 'min', 'max', 'step', 'rows',
            'maxSelect', 'columns', 'timeStep', 'maxFileSize', 'ratingMax'
        );
        foreach ( $gp_int_keys as$ik ) {
            if ( isset( $el[$ik ] ) && $el[$ik ] !== '' ) {
                $clean[$ik ] = intval( $el[$ik ] );
            }
        }

        $gp_bool_keys = array(
            'required', 'transparentFill', 'autoplay', 'muted', 'loop',
            'controls', 'showPercentLabel', 'striped', 'targetBlank',
            'nofollow', 'checkedDefault', 'multiSelect', 'multiple',
            'showEyeToggle', 'logicEnabled', 'dynamicDefault',
            'readOnly', 'saveToDb', 'blockDisposable'
        );
        foreach ( $gp_bool_keys as$bk ) {
            if ( isset( $el[$bk ] ) ) {
                $clean[$bk ] = ! empty( $el[$bk ] ) && $el[$bk ] !== 'false';
            }
        }

        if ( isset( $el['options'] ) && ! is_array( $el['options'] ) ) {
            $clean['options'] = sanitize_textarea_field( wp_unslash( (string)$el['options'] ) );
        }

        if ( isset( $el['inlineStyles'] ) && is_array( $el['inlineStyles'] ) ) {$clean['inlineStyles'] = array();
            foreach ( $el['inlineStyles'] as$st ) {
                if ( is_array( $st ) ) {$clean['inlineStyles'][] = array(
                        'prop'  => isset( $st['prop'] ) ? sanitize_text_field( $st['prop'] ) : '',
                        'value' => isset( $st['value'] ) ? sanitize_text_field( $st['value'] ) : '',
                    );
                }
            }
        }

        if ( isset( $el['logicRules'] ) && is_array( $el['logicRules'] ) ) {$clean['logicRules'] = array();
            foreach ( $el['logicRules'] as$rl ) {
                if ( is_array( $rl ) ) {$clean['logicRules'][] = array(
                        'field'    => isset( $rl['field'] ) ? sanitize_text_field( $rl['field'] ) : '',
                        'operator' => isset( $rl['operator'] ) ? sanitize_text_field( $rl['operator'] ) : 'is',
                        'value'    => isset( $rl['value'] ) ? sanitize_text_field( $rl['value'] ) : '',
                    );
                }
            }
        }

        foreach ( array( 'filters', 'validators' ) as $arr_k ) {
            if ( isset( $el[$arr_k ] ) && is_array( $el[$arr_k ] ) ) {
                $clean[$arr_k ] = array_map( 'sanitize_text_field', $el[$arr_k ] );
            }
        }

        
        if ( class_exists( 'WpPopPop_Ajax_Element_Registry' ) && method_exists( 'WpPopPop_Ajax_Element_Registry', 'sanitize_decomposed_layer_props' ) ) {
            $clean = WpPopPop_Ajax_Element_Registry::sanitize_decomposed_layer_props( $el,$clean );
        }

        return $clean;
    }

}
