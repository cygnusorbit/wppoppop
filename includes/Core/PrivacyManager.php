<?php
namespace WPPopPop\Core;

use WPPopPop\Targeting\PopupPostType;

class PrivacyManager {
    public function init(): void {
        add_filter('wp_privacy_personal_data_exporters', [$this, 'register_exporter']);
        add_filter('wp_privacy_personal_data_erasers', [$this, 'register_eraser']);
    }

    public function register_exporter(array $exporters): array {
        $exporters['wppoppop-leads'] = [
            'exporter_friendly_name' => __('WP Pop Pop Captured Leads', 'wppoppop'),
            'callback'               => [$this, 'export_personal_data'],
        ];
        return $exporters;
    }

    public function export_personal_data(string $email_address, int $page = 1): array {
        $number = 50;
        $query = new \WP_Query([
            'post_type'      => PopupPostType::LEAD_POST_TYPE,
            'post_status'    => 'publish',
            'posts_per_page' => $number,
            'paged'          => $page,
            'meta_query'     => [
                [
                    'key'     => '_wppoppop_lead_email',
                    'value'   => $email_address,
                    'compare' => '=',
                ],
            ],
        ]);

        $export_items = [];

        foreach ($query->posts as $lead) {
            $name     = get_post_meta($lead->ID, '_wppoppop_lead_name', true) ?: '—';
            $popup_id = get_post_meta($lead->ID, '_wppoppop_lead_popup_id', true) ?: '—';
            $ip       = get_post_meta($lead->ID, '_wppoppop_lead_ip', true) ?: '—';
            $date     = get_post_meta($lead->ID, '_wppoppop_lead_date', true) ?: $lead->post_date;

            $export_items[] = [
                'group_id'    => 'wppoppop_leads',
                'group_label' => __('WP Pop Pop Captured Leads', 'wppoppop'),
                'item_id'     => "wppoppop-lead-{$lead->ID}",
                'data'        => [
                    ['name' => __('Email Address', 'wppoppop'), 'value' => $email_address],
                    ['name' => __('Subscriber Name', 'wppoppop'), 'value' => $name],
                    ['name' => __('Popup ID', 'wppoppop'), 'value' => $popup_id],
                    ['name' => __('IP Address', 'wppoppop'), 'value' => $ip],
                    ['name' => __('Captured At', 'wppoppop'), 'value' => $date],
                ],
            ];
        }

        $done = $query->max_num_pages <= $page;

        return [
            'data' => $export_items,
            'done' => $done,
        ];
    }

    public function register_eraser(array $erasers): array {
        $erasers['wppoppop-leads'] = [
            'eraser_friendly_name' => __('WP Pop Pop Captured Leads', 'wppoppop'),
            'callback'             => [$this, 'erase_personal_data'],
        ];
        return $erasers;
    }

    public function erase_personal_data(string $email_address, int $page = 1): array {
        $number = 50;
        $query = new \WP_Query([
            'post_type'      => PopupPostType::LEAD_POST_TYPE,
            'post_status'    => 'publish',
            'posts_per_page' => $number,
            'paged'          => $page,
            'meta_query'     => [
                [
                    'key'     => '_wppoppop_lead_email',
                    'value'   => $email_address,
                    'compare' => '=',
                ],
            ],
        ]);

        $items_removed  = 0;
        $items_retained = 0;
        $messages       = [];

        foreach ($query->posts as $lead) {
            $deleted = wp_delete_post($lead->ID, true);
            if ($deleted) {
                $items_removed++;
            } else {
                $items_retained++;
                $messages[] = sprintf(__('Could not delete captured lead record ID %d.', 'wppoppop'), $lead->ID);
            }
        }

        $done = $query->max_num_pages <= $page;

        return [
            'items_removed'  => $items_removed,
            'items_retained' => $items_retained,
            'messages'       => $messages,
            'done'           => $done,
        ];
    }
}
