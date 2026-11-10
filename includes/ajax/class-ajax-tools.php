<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Tools domain: backup/restore service, schema repair, counters, maintenance.
 * One import/export implementation shared by the AJAX and the native-POST paths.
 */
class WpPopPop_Ajax_Tools {
    const MAX_IMPORT_BYTES = 10485760; // 10 MB

    public function __construct() {
        add_action('wp_ajax_wppoppop_bulk_export', [$this, 'bulk_export']);
        add_action('admin_post_wppoppop_bulk_export', [$this, 'bulk_export_download']);
        add_action('wp_ajax_wppoppop_bulk_import', [$this, 'bulk_import']);
        add_action('wp_ajax_wppoppop_repair_tables', [$this, 'repair_tables']);
        add_action('wp_ajax_wppoppop_reset_counters', [$this, 'reset_counters']);
        add_action('wp_ajax_wppoppop_purge_logs', [$this, 'purge_logs']);
        add_action('wp_ajax_wppoppop_optimize_tables', [$this, 'optimize_tables']);
        add_action('wp_ajax_wppoppop_orphan_report', [$this, 'orphan_report']);
    }

    private function verify_security() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Unauthorized action.', 'wppoppop')]);
        }
        $nonce = isset($_REQUEST['nonce']) ? sanitize_text_field(wp_unslash($_REQUEST['nonce'])) : '';
        if (!wp_verify_nonce($nonce, 'wppoppop_builder_nonce') && !wp_verify_nonce($nonce, 'wppoppop_admin_nonce')) {
            wp_send_json_error(['message' => __('Security check failed. Please refresh the page.', 'wppoppop')]);
        }
    }

    /* ------------------------------------------------------ service */
    /** Table key => [label, name]. Companion plugins add their own tables with the wppoppop_tools_tables filter. */
    public static function table_defs() {
        global $wpdb;
        $defs = [
            'items'        => ['label' => __('Popups & Designs', 'wppoppop'), 'name' => $wpdb->prefix . 'wppoppop_items'],
            'submissions'  => ['label' => __('Captured Leads', 'wppoppop'), 'name' => $wpdb->prefix . 'wppoppop_submissions'],
            'logs'         => ['label' => __('Audit & System Logs', 'wppoppop'), 'name' => $wpdb->prefix . 'wppoppop_logs'],
            'transactions' => ['label' => __('Payment Records', 'wppoppop'), 'name' => $wpdb->prefix . 'wppoppop_transactions'],
        ];
        $filtered = apply_filters('wppoppop_tools_tables', $defs);
        return is_array($filtered) ? $filtered : $defs;
    }

    public static function tables() {
        $out = [];
        foreach (self::table_defs() as $key => $def) {
            $out[$key] = $def['name'];
        }
        return $out;
    }

    public static function table_status() {
        global $wpdb;
        $out = [];
        foreach (self::tables() as $key => $name) {
            $exists = ($wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($name))) === $name);
            $rows = 0;
            $size = '0 KB';
            if ($exists) {
                $rows = (int) /* phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching */ $wpdb->get_var("SELECT COUNT(*) FROM `{$name}`");
                $st = $wpdb->get_row($wpdb->prepare('SHOW TABLE STATUS LIKE %s', $wpdb->esc_like($name)), ARRAY_A);
                if ($st && isset($st['Data_length'])) {
                    $size = size_format((int) $st['Data_length'] + (int) $st['Index_length'], 2);
                }
            }
            $out[$key] = [
                'table'  => $name,
                'exists' => $exists,
                'rows'   => number_format_i18n($rows),
                'size'   => $size,
                'status' => $exists ? 'Optimal' : 'Missing',
            ];
        }
        return $out;
    }

    public static function build_export() {
        global $wpdb;
        $items     = $wpdb->get_results("SELECT uid, title, data, status FROM {$wpdb->prefix}wppoppop_items ORDER BY id ASC", ARRAY_A);
        $settings  = get_option('wppoppop_settings', []);
        $theme     = get_option(class_exists('WpPopPop_Admin_Theme') ? WpPopPop_Admin_Theme::OPTION : 'wppoppop_theme', []);
        $out = [
            'generator' => 'WpPopPop ' . (defined('WPPOPPOP_VERSION') ? WPPOPPOP_VERSION : '1.0.0'),
            'format'    => 2,
            'exported'  => current_time('mysql'),
            'items'     => $items ? $items : [],
            'settings'  => is_array($settings) ? $settings : [],
            'theme'     => is_array($theme) ? $theme : [],
        ];
        $filtered = apply_filters('wppoppop_export_data', $out);
        return is_array($filtered) ? $filtered : $out;
    }

    /** Keep the original uid (so existing embeds keep working) unless it is taken. */
    private static function free_uid($wanted, $title) {
        global $wpdb;
        $t = $wpdb->prefix . 'wppoppop_items';
        $wanted = sanitize_key($wanted);
        /* phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- table name is built from $wpdb->prefix and every value is prepared */ if ($wanted !== '' && !$wpdb->get_var($wpdb->prepare("SELECT COUNT(1) FROM {$t} WHERE uid = %s", $wanted))) {
            return $wanted;
        }
        $base = trim(preg_replace('/[^a-z0-9-]+/', '-', strtolower(sanitize_title((string) $title))), '-');
        $base = rtrim(substr($base, 0, 29), '-');
        if ($base === '') {
            $base = 'popup';
        }
        $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        for ($try = 0; $try < 30; $try++) {
            $s = '';
            for ($i = 0; $i < 6; $i++) {
                $s .= $chars[wp_rand(0, strlen($chars) - 1)];
            }
            $cand = $base . '-' . $s;
            /* phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- table name is built from $wpdb->prefix and every value is prepared */ if (!$wpdb->get_var($wpdb->prepare("SELECT COUNT(1) FROM {$t} WHERE uid = %s", $cand))) {
                return $cand;
            }
        }
        return substr($base, 0, 20) . '-' . strtolower(wp_generate_password(6, false, false));
    }

    public static function import_payload($raw, $dry_run = false, $with_settings = false) {
        global $wpdb;
        if (!is_string($raw) || $raw === '') {
            return new WP_Error('empty', __('No backup data was provided for restoration.', 'wppoppop'));
        }
        if (strlen($raw) > self::MAX_IMPORT_BYTES) {
            return new WP_Error('size', __('Backup file is too large (10 MB maximum).', 'wppoppop'));
        }
        $decoded = json_decode($raw, true);
        $has_data = is_array($decoded) && !empty($decoded['items']);
        $has_data = (bool) apply_filters('wppoppop_import_has_data', $has_data, $decoded);
        if (!is_array($decoded) || !$has_data) {
            return new WP_Error('format', __('Invalid or unrecognized backup format.', 'wppoppop'));
        }

        $res = ['items' => 0, 'campaigns' => 0, 'skipped' => 0, 'renamed' => 0, 'settings' => false, 'dry_run' => (bool) $dry_run];
        $uid_map = [];
        $items_table = $wpdb->prefix . 'wppoppop_items';
        if (!$dry_run) { $wpdb->query('START TRANSACTION'); }

        if (!empty($decoded['items']) && is_array($decoded['items'])) {
            foreach ($decoded['items'] as $item) {
                if (!is_array($item)) { $res['skipped']++; continue; }
                $title = isset($item['title']) ? sanitize_text_field($item['title']) : '';
                if ($title === '') { $title = __('Restored Popup', 'wppoppop'); }
                $data = isset($item['data']) ? $item['data'] : '{}';
                if (is_array($data) || is_object($data)) { $data = wp_json_encode($data); }
                if (!is_string($data) || json_decode($data) === null) { $res['skipped']++; continue; }
                $obj = json_decode($data);
                if (is_object($obj) || is_array($obj)) {
                    if (!class_exists('WpPopPop_Sanitizer')) { require_once WPPOPPOP_PATH . 'includes/class-wppoppop-sanitizer.php'; }
                    $enc = wp_json_encode(WpPopPop_Sanitizer::anim_payload($obj));
                    if (is_string($enc) && $enc !== '') { $data = $enc; }
                }
                $status = (isset($item['status']) && in_array($item['status'], ['publish', 'draft'], true)) ? $item['status'] : 'draft';
                $old = isset($item['uid']) ? sanitize_key($item['uid']) : '';
                $new = self::free_uid($old, $title);
                if ($old !== '' && $new !== $old) { $res['renamed']++; }
                if ($old !== '') { $uid_map[$old] = $new; }
                if (!$dry_run) {
                    $ok = $wpdb->insert($items_table, [
                        'uid' => $new, 'title' => $title, 'data' => $data,
                        'status' => $status, 'impressions' => 0, 'submissions' => 0, 'confirmations' => 0,
                    ], ['%s', '%s', '%s', '%s', '%d', '%d', '%d']);
                    if ($ok === false) { $wpdb->query('ROLLBACK'); return new WP_Error('db', __('Database error during restore. No changes were kept.', 'wppoppop')); }
                }
                $res['items']++;
            }
        }

        /* Companion plugins restore their own data here. A WP_Error rolls the whole import back. */
        $extra = apply_filters('wppoppop_import_extra', ['campaigns' => 0, 'skipped' => 0], $decoded, $uid_map, (bool) $dry_run);
        if (is_wp_error($extra)) {
            if (!$dry_run) { $wpdb->query('ROLLBACK'); }
            return $extra;
        }
        if (is_array($extra)) {
            $res['campaigns'] = (int) (isset($extra['campaigns']) ? $extra['campaigns'] : 0);
            $res['skipped']  += (int) (isset($extra['skipped']) ? $extra['skipped'] : 0);
        }

        if (!$dry_run) { $wpdb->query('COMMIT'); }

        if ($with_settings && !$dry_run) {
            if (!empty($decoded['settings']) && is_array($decoded['settings']) && class_exists('WpPopPop_Admin_Settings')) {
                $existing = get_option('wppoppop_settings', []);
                $incoming = $decoded['settings'];
                $incoming['cookie_epoch'] = is_array($existing) && isset($existing['cookie_epoch']) ? (int) $existing['cookie_epoch'] : (int) get_option('wppoppop_cookie_epoch', 1);
                update_option('wppoppop_settings', WpPopPop_Admin_Settings::sanitize_settings($incoming));
                if (function_exists('wppoppop_flush_settings_cache')) { wppoppop_flush_settings_cache(); }
                $res['settings'] = true;
            }
            if (!empty($decoded['theme']) && is_array($decoded['theme']) && class_exists('WpPopPop_Admin_Theme')) {
                update_option(WpPopPop_Admin_Theme::OPTION, WpPopPop_Admin_Theme::sanitize_all($decoded['theme']));
                $res['settings'] = true;
            }
        }
        return $res;
    }

    private static function summary($r) {
        /* translators: 1: action label (e.g. Successfully restored), 2: number of popups. */
        $msg = sprintf(__('%1$s %2$d popups', 'wppoppop'), !empty($r['dry_run']) ? __('Preview: would restore', 'wppoppop') : __('Successfully restored', 'wppoppop'), $r['items']);
        if (!empty($r['campaigns'])) {
            /* translators: %d: number of A/B campaigns. */
            $msg .= sprintf(__(' and %d A/B campaigns', 'wppoppop'), (int) $r['campaigns']);
        }
        if (!empty($r['renamed'])) { $msg .= sprintf(' (%d uid(s) changed because they already exist)', $r['renamed']); }
        if (!empty($r['skipped'])) { $msg .= sprintf('; %d entr%s skipped as invalid', $r['skipped'], $r['skipped'] === 1 ? 'y' : 'ies'); }
        if (!empty($r['settings'])) { $msg .= '; settings/theme applied'; }
        return $msg . '.';
    }

    public static function repair() {
        require_once WPPOPPOP_PATH . 'includes/class-wppoppop-installer.php';
        WpPopPop_Installer::create_tables();
        $status  = self::table_status();
        $missing = array_filter($status, function ($t) { return empty($t['exists']); });
        $msg = count($missing)
            ? __('Some tables are still missing after repair. Check that the database user has CREATE/ALTER privileges.', 'wppoppop')
            : __('All database tables successfully verified and schema confirmed healthy!', 'wppoppop');
        return [$msg, $status, count($missing) === 0];
    }

    public static function reset() {
        global $wpdb;
        $n = $wpdb->query("UPDATE `{$wpdb->prefix}wppoppop_items` SET impressions = 0, submissions = 0, confirmations = 0");
        return $n !== false ? (int) $n : 0;
    }

    /** Native POST path used by templates/tools-view.php. Returns [message, type]. */
    public static function handle_post() {
        if (!current_user_can('manage_options')) {
            return [__('Unauthorized.', 'wppoppop'), 'error'];
        }
        check_admin_referer('wppoppop_tools_action', 'wppoppop_tools_nonce');
        if (isset($_POST['wppoppop_repair_tables'])) {
            $r = self::repair();
            return [$r[0], $r[2] ? 'success' : 'error'];
        }
        if (isset($_POST['wppoppop_reset_counters'])) {
            self::reset();
            return [__('All popup impressions, leads, and confirmation counters have been reset to 0.', 'wppoppop'), 'success'];
        }
        if (isset($_POST['wppoppop_bulk_import'])) {
            $raw = '';
            $wppoppop_tmp = isset($_FILES['wppoppop_bulk_import_file']['tmp_name']) ? $_FILES['wppoppop_bulk_import_file']['tmp_name'] : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- PHP-generated temp path, validated with is_uploaded_file()
            if (is_string($wppoppop_tmp) && $wppoppop_tmp !== '' && is_uploaded_file($wppoppop_tmp)) {
                if (filesize($wppoppop_tmp) > self::MAX_IMPORT_BYTES) {
                    return [__('Backup file is too large (10 MB maximum).', 'wppoppop'), 'error'];
                }
                /* phpcs:ignore WordPress.WP.AlternativeFunctions -- reads a local plugin or upload temp file, not a remote URL */ $raw = (string) file_get_contents($wppoppop_tmp);
            }
            $r = self::import_payload($raw, false, !empty($_POST['import_settings']));
            return is_wp_error($r) ? [$r->get_error_message(), 'error'] : [self::summary($r), 'success'];
        }
        return ['', 'success'];
    }

    /* -------------------------------------------------------- AJAX */
    public function bulk_export() {
        $this->verify_security();
        wp_send_json_success([
            'filename' => 'wppoppop-bulk-backup-' . gmdate('Y-m-d') . '.json',
            'payload'  => self::build_export(),
        ]);
    }

    public function bulk_export_download() {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('Unauthorized action.', 'wppoppop'), '', ['response' => 403]);
        }
        check_admin_referer('wppoppop_tools_action', 'wppoppop_tools_nonce');
        while (ob_get_level()) {
            ob_end_clean();
        }
        nocache_headers();
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="wppoppop-bulk-backup-' . gmdate('Y-m-d') . '.json"');
        echo wp_json_encode(self::build_export(), JSON_PRETTY_PRINT);
        exit;
    }

    public function bulk_import() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => esc_html__( 'Unauthorized user capability.', 'wppoppop' ) ), 403 );
            wp_die();
        }
        $wpp_verified = check_ajax_referer( 'wppoppop_nonce', 'nonce', false )
            || check_ajax_referer( 'wppoppop_nonce', '_ajax_nonce', false )
            || check_ajax_referer( 'wppoppop_admin', 'nonce', false )
            || check_ajax_referer( 'wppoppop_nonce', 'security', false )
            || check_ajax_referer( 'wppoppop_builder_nonce', 'nonce', false )
            || check_ajax_referer( 'wppoppop_admin_nonce', 'nonce', false )
            || check_ajax_referer( 'wppoppop_settings_nonce', 'nonce', false );
        if ( ! $wpp_verified ) {
            wp_send_json_error( array( 'message' => esc_html__( 'Invalid security token.', 'wppoppop' ) ), 403 );
            wp_die();
        }

        $this->verify_security();
        $raw = '';
        $wppoppop_tmp = isset($_FILES['wppoppop_bulk_import_file']['tmp_name']) ? $_FILES['wppoppop_bulk_import_file']['tmp_name'] : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- PHP-generated temp path, validated with is_uploaded_file()
        if (is_string($wppoppop_tmp) && $wppoppop_tmp !== '') {
            if (!is_uploaded_file($wppoppop_tmp)) {
                wp_send_json_error(['message' => __('The uploaded file could not be read.', 'wppoppop')]);
            }
            if (filesize($wppoppop_tmp) > self::MAX_IMPORT_BYTES) {
                wp_send_json_error(['message' => __('Backup file is too large (10 MB maximum).', 'wppoppop')]);
            }
            /* phpcs:ignore WordPress.WP.AlternativeFunctions -- reads a local plugin or upload temp file, not a remote URL */ $raw = (string) file_get_contents($wppoppop_tmp);
        } elseif (!empty($_POST['import_data'])) {
            $raw = wp_unslash($_POST['import_data']); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- JSON decoded and sanitised by the import pipeline
        }
        $r = self::import_payload($raw, !empty($_POST['dry_run']), !empty($_POST['import_settings']));
        if (is_wp_error($r)) {
            wp_send_json_error(['message' => $r->get_error_message()]);
        }
        wp_send_json_success([
            'message'            => self::summary($r),
            'imported_items'     => $r['items'],
            'imported_campaigns' => $r['campaigns'],
            'dry_run'            => $r['dry_run'],
        ]);
    }

    public function repair_tables() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => esc_html__( 'Unauthorized user capability.', 'wppoppop' ) ), 403 );
            wp_die();
        }
        $wpp_verified = check_ajax_referer( 'wppoppop_nonce', 'nonce', false )
            || check_ajax_referer( 'wppoppop_nonce', '_ajax_nonce', false )
            || check_ajax_referer( 'wppoppop_admin', 'nonce', false )
            || check_ajax_referer( 'wppoppop_nonce', 'security', false )
            || check_ajax_referer( 'wppoppop_builder_nonce', 'nonce', false )
            || check_ajax_referer( 'wppoppop_admin_nonce', 'nonce', false )
            || check_ajax_referer( 'wppoppop_settings_nonce', 'nonce', false );
        if ( ! $wpp_verified ) {
            wp_send_json_error( array( 'message' => esc_html__( 'Invalid security token.', 'wppoppop' ) ), 403 );
            wp_die();
        }

        $this->verify_security();
        $r = self::repair();
        if (!$r[2]) {
            wp_send_json_error(['message' => $r[0], 'tables' => $r[1]]);
        }
        wp_send_json_success(['message' => $r[0], 'tables' => $r[1]]);
    }

    public function reset_counters() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => esc_html__( 'Unauthorized user capability.', 'wppoppop' ) ), 403 );
            wp_die();
        }
        $wpp_verified = check_ajax_referer( 'wppoppop_nonce', 'nonce', false )
            || check_ajax_referer( 'wppoppop_nonce', '_ajax_nonce', false )
            || check_ajax_referer( 'wppoppop_admin', 'nonce', false )
            || check_ajax_referer( 'wppoppop_nonce', 'security', false )
            || check_ajax_referer( 'wppoppop_builder_nonce', 'nonce', false )
            || check_ajax_referer( 'wppoppop_admin_nonce', 'nonce', false )
            || check_ajax_referer( 'wppoppop_settings_nonce', 'nonce', false );
        if ( ! $wpp_verified ) {
            wp_send_json_error( array( 'message' => esc_html__( 'Invalid security token.', 'wppoppop' ) ), 403 );
            wp_die();
        }

        $this->verify_security();
        wp_send_json_success([
            'message'  => __('All popup impressions, leads captured, and confirmation counters have been reset to 0.', 'wppoppop'),
            'affected' => self::reset(),
        ]);
    }

    public function purge_logs() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => esc_html__( 'Unauthorized user capability.', 'wppoppop' ) ), 403 );
            wp_die();
        }
        $wpp_verified = check_ajax_referer( 'wppoppop_nonce', 'nonce', false )
            || check_ajax_referer( 'wppoppop_nonce', '_ajax_nonce', false )
            || check_ajax_referer( 'wppoppop_admin', 'nonce', false )
            || check_ajax_referer( 'wppoppop_nonce', 'security', false )
            || check_ajax_referer( 'wppoppop_builder_nonce', 'nonce', false )
            || check_ajax_referer( 'wppoppop_admin_nonce', 'nonce', false )
            || check_ajax_referer( 'wppoppop_settings_nonce', 'nonce', false );
        if ( ! $wpp_verified ) {
            wp_send_json_error( array( 'message' => esc_html__( 'Invalid security token.', 'wppoppop' ) ), 403 );
            wp_die();
        }

        $this->verify_security();
        global $wpdb;
        $days = isset($_POST['days']) ? max(1, min(3650, (int) $_POST['days'])) : 30;
        $n = $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->prefix}wppoppop_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL %d DAY)", $days));
        wp_send_json_success(['message' => sprintf(
            /* translators: 1: number of log entries removed, 2: number of days. */
            _n('Removed %1$d log entry older than %2$d days.', 'Removed %1$d log entries older than %2$d days.', (int) $n, 'wppoppop'),
            (int) $n,
            $days
        )]);
    }

    public function optimize_tables() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => esc_html__( 'Unauthorized user capability.', 'wppoppop' ) ), 403 );
            wp_die();
        }
        $wpp_verified = check_ajax_referer( 'wppoppop_nonce', 'nonce', false )
            || check_ajax_referer( 'wppoppop_nonce', '_ajax_nonce', false )
            || check_ajax_referer( 'wppoppop_admin', 'nonce', false )
            || check_ajax_referer( 'wppoppop_nonce', 'security', false )
            || check_ajax_referer( 'wppoppop_builder_nonce', 'nonce', false )
            || check_ajax_referer( 'wppoppop_admin_nonce', 'nonce', false )
            || check_ajax_referer( 'wppoppop_settings_nonce', 'nonce', false );
        if ( ! $wpp_verified ) {
            wp_send_json_error( array( 'message' => esc_html__( 'Invalid security token.', 'wppoppop' ) ), 403 );
            wp_die();
        }

        $this->verify_security();
        global $wpdb;
        $done = 0;
        foreach (self::table_status() as $t) {
            if (!empty($t['exists'])) {
                // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
                $wpdb->query('OPTIMIZE TABLE `' . $t['table'] . '`');
                $done++;
            }
        }
        wp_send_json_success(['message' => sprintf(
            /* translators: %d: number of database tables optimized. */
            _n('Optimized %d table.', 'Optimized %d tables.', (int) $done, 'wppoppop'),
            (int) $done
        ), 'tables' => self::table_status()]);
    }

    /** Read-only: nothing is deleted. */
    public function orphan_report() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => esc_html__( 'Unauthorized user capability.', 'wppoppop' ) ), 403 );
            wp_die();
        }
        $wpp_verified = check_ajax_referer( 'wppoppop_nonce', 'nonce', false )
            || check_ajax_referer( 'wppoppop_nonce', '_ajax_nonce', false )
            || check_ajax_referer( 'wppoppop_admin', 'nonce', false )
            || check_ajax_referer( 'wppoppop_nonce', 'security', false )
            || check_ajax_referer( 'wppoppop_builder_nonce', 'nonce', false )
            || check_ajax_referer( 'wppoppop_admin_nonce', 'nonce', false )
            || check_ajax_referer( 'wppoppop_settings_nonce', 'nonce', false );
        if ( ! $wpp_verified ) {
            wp_send_json_error( array( 'message' => esc_html__( 'Invalid security token.', 'wppoppop' ) ), 403 );
            wp_die();
        }

        $this->verify_security();
        global $wpdb;
        $p = $wpdb->prefix;
        $subs  = (int) /* phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, PluginCheck.Security.DirectDB.UnescapedDBParameter -- table name is built from $wpdb->prefix and the query takes no user input */ $wpdb->get_var("SELECT COUNT(*) FROM {$p}wppoppop_submissions s LEFT JOIN {$p}wppoppop_items i ON s.popup_uid = i.uid WHERE i.uid IS NULL");
        $trans = (int) /* phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, PluginCheck.Security.DirectDB.UnescapedDBParameter -- table name is built from $wpdb->prefix and the query takes no user input */ $wpdb->get_var("SELECT COUNT(*) FROM {$p}wppoppop_transactions t LEFT JOIN {$p}wppoppop_items i ON t.popup_uid = i.uid WHERE i.uid IS NULL");
        wp_send_json_success(['message' => sprintf(
            /* translators: 1: number of orphaned submissions, 2: number of orphaned transactions. */
            __('Orphaned records (popup no longer exists): %1$d submissions, %2$d transactions. Nothing was deleted.', 'wppoppop'),
            $subs,
            $trans
        )]);
    }
}
