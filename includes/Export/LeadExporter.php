<?php
namespace WPPopPop\Export;

use WPPopPop\Admin\SettingsManager;
use WPPopPop\Targeting\PopupPostType;

class LeadExporter {
    public static function init(): void {
        add_action('admin_post_wppoppop_export_leads_csv', [__CLASS__, 'handle_export']);
    }

    public static function handle_export(): void {
        if (!current_user_can('manage_options')) {
            wp_die(__('Permission denied.', 'wppoppop'));
        }

        check_admin_referer('wppoppop_export_leads');

        $separator_setting = SettingsManager::get('csv_separator', ',');
        $delimiter = ',';
        if ($separator_setting === ';') {
            $delimiter = ';';
        } elseif ($separator_setting === 'tab') {
            $delimiter = "\t";
        }

        $popup_filter = isset($_GET['popup_id']) ? absint($_GET['popup_id']) : 0;
        $args = [
            'post_type'      => PopupPostType::LEAD_POST_TYPE,
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ];

        if ($popup_filter > 0) {
            $args['meta_key']   = '_wppoppop_lead_popup_id';
            $args['meta_value'] = $popup_filter;
        }

        $leads = get_posts($args);
        $filename = 'wppoppop-leads-' . gmdate('Y-m-d-His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');
        // UTF-8 BOM for Microsoft Excel compatibility
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // CSV Header
        fputcsv($output, ['Lead ID', 'Email', 'Name', 'Popup ID', 'Popup Title', 'Status', 'Amount ($)', 'Created At'], $delimiter);

        foreach ($leads as $lead) {
            $pid    = (int) get_post_meta($lead->ID, '_wppoppop_lead_popup_id', true);
            $name   = get_post_meta($lead->ID, '_wppoppop_lead_name', true) ?: '';
            $status = get_post_meta($lead->ID, '_wppoppop_status', true) ?: 'Completed';
            $amount = get_post_meta($lead->ID, '_wppoppop_amount', true) ?: '0.00';
            $title  = $pid ? get_the_title($pid) : 'Direct/Unknown';

            fputcsv($output, [
                $lead->ID,
                $lead->post_title,
                $name,
                $pid,
                $title,
                $status,
                number_format((float)$amount, 2, '.', ''),
                get_the_date('Y-m-d H:i:s', $lead),
            ], $delimiter);
        }

        fclose($output);
        exit;
    }
}
