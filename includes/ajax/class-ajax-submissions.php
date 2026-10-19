<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Ajax_Submissions {
    public function __construct() {
        add_action('wp_ajax_wppoppop_export_submissions_csv', [$this, 'export_submissions_csv']);
        add_action('wp_ajax_wppoppop_delete_submission', [$this, 'delete_submission']);
        add_action('wp_ajax_wppoppop_anonymize_submission', [$this, 'anonymize_submission']);
        add_action('wp_ajax_wppoppop_mark_submission_read', [$this, 'mark_submission_read']);
    }

    private function verify_nonce() {
        $n = isset($_REQUEST['nonce']) ? sanitize_text_field(wp_unslash($_REQUEST['nonce'])) : '';
        if (!wp_verify_nonce($n, 'wppoppop_builder_nonce') && !wp_verify_nonce($n, 'wppoppop_admin_nonce')) {
            wp_send_json_error(['message' => 'Security verification failed.'], 403);
        }
    }

    public function export_submissions_csv() {
        $this->verify_nonce();
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }

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

    public function mark_submission_read() {
        $n = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
        if (!wp_verify_nonce($n, 'wppoppop_builder_nonce') && !wp_verify_nonce($n, 'wppoppop_admin_nonce')) {
            wp_send_json_error(['message' => 'Security verification failed.'], 403);
        }
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized action.'], 403);
        }
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        if ($id > 0) {
            global $wpdb;
            $wpdb->update($wpdb->prefix . 'wppoppop_submissions', ['is_read' => 1], ['id' => $id], ['%d'], ['%d']);
        }
        wp_send_json_success(['id' => $id]);
    }

    public function delete_submission() {
        $this->verify_nonce();
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized action.']);
        }
        global $wpdb;
        $wpdb->delete($wpdb->prefix . 'wppoppop_submissions', ['id' => intval($_POST['id'])]);
        wp_send_json_success(['message' => 'Record permanently deleted.']);
    }

    public function anonymize_submission() {
        $this->verify_nonce();
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized action.']);
        }
        $id = intval($_POST['id']);
        global $wpdb;
        $wpdb->update($wpdb->prefix . 'wppoppop_submissions', ['email' => 'anonymized_' . $id . '@privacy.local', 'fields_data' => wp_json_encode(['gdpr' => 'anonymized'])], ['id' => $id]);
        wp_send_json_success(['message' => 'PII anonymized.']);
    }
}
