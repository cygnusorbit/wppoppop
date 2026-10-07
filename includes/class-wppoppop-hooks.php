<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * WpPopPop Hooks & Extensibility Event Bus Registry
 * Catalogs and standardizes all actions and filter hooks for developers and add-ons.
 */
class WpPopPop_Hooks {

    public function __construct() {
        // Core initialization hook point
        do_action('wppoppop_init');
    }

    /**
     * Retrieve the catalog of official WpPopPop action and filter hooks.
     *
     * @return array
     */
    public static function get_registered_hooks() {
        return [
            'filters' => [
                'wppoppop_pre_save_popup_data'     => 'Filters popup configuration array before persisting to database.',
                'wppoppop_load_popup_config'       => 'Filters loaded popup configuration before returning to visual builder.',
                'wppoppop_export_campaign_payload' => 'Filters JSON export payload prior to download.',
                'wppoppop_pre_import_campaign_data'=> 'Filters uploaded campaign data prior to import insertion.',
                'wppoppop_pre_process_submission'  => 'Filters incoming lead form field data before database insertion.',
                'wppoppop_autoresponder_mail'      => 'Filters autoresponder email arguments (to, subject, body, headers).',
                'wppoppop_webhook_payload'         => 'Filters outbound HTTP webhook payload before transmission.',
                'wppoppop_visitor_country'         => 'Filters detected two-letter ISO country code for visitor targeting.',
                'wppoppop_targeting_decision'      => 'Filters the final boolean display determination for a popup campaign.',
            ],
            'actions' => [
                'wppoppop_init'                    => 'Fires when the WpPopPop core system completes initialization.',
                'wppoppop_before_save_popup'       => 'Fires immediately before a popup is inserted or updated in the database.',
                'wppoppop_after_save_popup'        => 'Fires immediately after a popup is successfully saved.',
                'wppoppop_after_duplicate_popup'   => 'Fires after a popup campaign is cloned.',
                'wppoppop_before_delete_popup'      => 'Fires before a campaign and its linked records are deleted.',
                'wppoppop_after_delete_popup'       => 'Fires after a campaign and its submissions have been deleted.',
                'wppoppop_before_submission_save'  => 'Fires before a new lead submission is written to the database.',
                'wppoppop_after_submission_saved'   => 'Fires after a new lead is inserted into wp_wppoppop_submissions.',
                'wppoppop_webhook_dispatched'      => 'Fires after an outbound webhook response is received.',
                'wppoppop_after_save_ab_campaign'  => 'Fires after an A/B split-testing campaign is created.',
                'wppoppop_after_delete_ab_campaign'=> 'Fires after an A/B split-testing campaign is removed.',
            ]
        ];
    }
}
