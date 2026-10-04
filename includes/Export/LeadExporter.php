<?php
namespace WPPopPop\Export;

use WPPopPop\Targeting\PopupPostType;

class LeadExporter {
    public function init(): void {
        add_action('admin_post_wppoppop_export_leads', [$this, 'handle_export']);
        add_action('manage_posts_extra_tablenav', [$this, 'render_export_button']);
    }

    public function render_export_button(string $which): void {
        global $typenow;
        if ($typenow !== PopupPostType::LEAD_POST_TYPE || $which !== 'top') {
            return;
        }

        $export_url = wp_nonce_url(
            admin_url('admin-post.php?action=wppoppop_export_leads'),
            'wppoppop_export_nonce',
            '_wpnonce'
        );

        echo '<div class="alignleft actions">';
        echo '<a href="' . esc_url($export_url) . '" class="button button-primary">' . esc_html__('Export All Leads to CSV', 'wppoppop') . '</a>';
        echo '</div>';
    }

    public function handle_export(): void {
        if (!current_user_can('manage_options')) {
            wp_die(__('Unauthorized user capability.', 'wppoppop'));
        }

        check_admin_referer('wppoppop_export_nonce', '_wpnonce');

        $filename = 'wppoppop-leads-' . gmdate('Y-m-d-His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'Email', 'Name', 'Source Popup ID', 'Status', 'Capture Date', 'Confirmed Date', 'IP Address']);

        $query = new \WP_Query([
            'post_type'      => PopupPostType::LEAD_POST_TYPE,
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ]);

        foreach ($query->posts as $lead) {
            $email        = get_post_meta($lead->ID, '_wppoppop_lead_email', true) ?: $lead->post_title;
            $name         = get_post_meta($lead->ID, '_wppoppop_lead_name', true) ?: '';
            $popup_id     = get_post_meta($lead->ID, '_wppoppop_lead_popup_id', true) ?: '';
            $date         = get_post_meta($lead->ID, '_wppoppop_lead_date', true) ?: $lead->post_date;
            $ip           = get_post_meta($lead->ID, '_wppoppop_lead_ip', true) ?: '';
            $is_confirmed = get_post_meta($lead->ID, '_wppoppop_confirmed', true) === '1';
            $status       = $is_confirmed ? 'Confirmed' : 'Pending Confirmation';
            $confirmed_at = get_post_meta($lead->ID, '_wppoppop_confirmed_at', true) ?: ($is_confirmed ? $date : '—');

            fputcsv($output, [$lead->ID, $email, $name, $popup_id, $status, $date, $confirmed_at, $ip]);
        }

        fclose($output);
        exit;
    }
}
