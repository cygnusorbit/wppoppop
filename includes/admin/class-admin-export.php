<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Admin_Export {
    public function __construct() {
        add_action('admin_post_wppoppop_export_submissions', [$this, 'export_submissions_csv']);
        add_action('wp_ajax_wppoppop_export_submissions', [$this, 'export_submissions_csv']);
    }

    public function export_submissions_csv() {
        if (!current_user_can('manage_options')) {
            wp_die(__('Unauthorized capability.', 'wppoppop'));
        }

        $nonce = isset($_REQUEST['nonce']) ? sanitize_text_field(wp_unslash($_REQUEST['nonce'])) : '';
        if (!wp_verify_nonce($nonce, 'wppoppop_export_nonce') && !wp_verify_nonce($nonce, 'wppoppop_admin_nonce')) {
            wp_die(__('Security verification failed.', 'wppoppop'));
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_submissions';$popup_uid  = isset($_REQUEST['popup_uid']) ? sanitize_text_field(wp_unslash($_REQUEST['popup_uid'])) : '';

        if (!empty($popup_uid)) {
            $rows =$wpdb->get_results($wpdb->prepare("SELECT * FROM {$table_name} WHERE popup_uid = %s ORDER BY id DESC", $popup_uid));
        } else {
            $rows = $wpdb->get_results("SELECT * FROM {$table_name} ORDER BY id DESC LIMIT 5000");
        }

        // Resolve CSV delimiter from Settings
        $csv_sep_setting = wppoppop_get_setting('csv_separator', ',');$delimiter = ',';
        if ($csv_sep_setting === ';') {$delimiter = ';';
        } elseif ($csv_sep_setting === 'tab' ||$csv_sep_setting === "\t") {
            $delimiter = "\t";
        }

        $filename = 'wppoppop-submissions-' . gmdate('Y-m-d-His') . '.csv';

        if (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

        // Output UTF-8 BOM for Microsoft Excel / Apple Numbers compatibility
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($output, ['ID', 'Popup UID', 'Name', 'Email', 'Phone', 'IP Address', 'Created At', 'Payload JSON'],$delimiter);

        if (!empty($rows)) {
            foreach ($rows as$row) {
                fputcsv($output, [
                    $row->id,$row->popup_uid,
                    $row->name,$row->email,
                    $row->phone,$row->ip_address,
                    $row->created_at,$row->payload
                ], $delimiter);
            }
        }

        fclose($output);
        exit;
    }
}
