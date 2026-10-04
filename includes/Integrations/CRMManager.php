<?php
namespace WPPopPop\Integrations;

use WPPopPop\Admin\SettingsManager;

class CRMManager {
    public static function init(): void {
        // Lifecycle coordinator registration
    }

    public static function sync_lead(int $lead_id, int $popup_id, string $email, string $name, array $custom_fields = []): void {
        $provider = SettingsManager::get('crm_provider', 'none');

        // Check if popup overrides the global CRM provider
        $popup_provider = get_post_meta($popup_id, '_wppoppop_crm_provider', true);
        if (!empty($popup_provider) && $popup_provider !== 'default') {
            $provider = $popup_provider;
        }

        if ($provider === 'none' || empty($provider)) {
            return;
        }

        $adapter = null;
        switch ($provider) {
            case 'mailchimp':
                $adapter = new MailchimpAdapter();
                break;
            case 'activecampaign':
                $adapter = new ActiveCampaignAdapter();
                break;
            case 'brevo':
                $adapter = new BrevoAdapter();
                break;
        }

        if (!$adapter) {
            return;
        }

        $result = $adapter->sync_contact($email, $name, $custom_fields);
        $status = $result['success'] ? 'Synced' : 'Failed';

        update_post_meta($lead_id, '_wppoppop_crm_provider', $provider);
        update_post_meta($lead_id, '_wppoppop_crm_sync_status', $status);
        update_post_meta($lead_id, '_wppoppop_crm_sync_time', gmdate('Y-m-d H:i:s'));
    }
}
