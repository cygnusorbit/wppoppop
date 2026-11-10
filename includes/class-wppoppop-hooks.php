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
                'wppoppop_autoresponder_mail'      => 'Filters autoresponder email arguments (array: to, subject, body, headers).',
                'wppoppop_webhook_payload'         => 'Filters outbound HTTP webhook payload before transmission.',
                'wppoppop_visitor_country'         => 'Filters detected two-letter ISO country code for visitor targeting.',
                'wppoppop_targeting_decision'      => 'Filters the final boolean display determination for a popup campaign.',
                'wppoppop_pro_active'              => 'Companion plugin reports that it is active (bool).',
                'wppoppop_pro_licensed'            => 'Companion plugin reports a valid licence (bool).',
                'wppoppop_feature_enabled'         => 'Companion plugin switches a feature on or off (bool, feature key).',
                'wppoppop_layer_elements'          => 'Filters the element type => label list used by the builder.',
                'wppoppop_layer_form_types'        => 'Filters the list of form-field element types.',
                'wppoppop_layer_style_layout'      => 'Filters the per-element Style tab layout map.',
                'wppoppop_layer_catalogue'         => 'Filters the layer-settings catalogue (tab => entries) before it is cached.',
                'wppoppop_module_roots'            => 'Extra module folders: list of array(path, url).',
                'wppoppop_preset_roots'            => 'Extra library template folders: list of array(path, url).',
                'wppoppop_render_element'          => 'Renders an element type that core does not handle (escaped HTML).',
                'wppoppop_builder_payload'         => 'Filters the data passed to the builder JavaScript.',
                'wppoppop_bar_links'               => 'Filters the dashboard bar links: list of array(page slug, label, dashicon).',
                'wppoppop_tools_tables'            => 'Filters the database tables listed in Tools: key => array(label, name).',
                'wppoppop_export_data'             => 'Filters the full backup payload before download.',
                'wppoppop_import_has_data'         => 'Filters whether an uploaded backup contains restorable data (bool, decoded payload).',
                'wppoppop_import_extra'            => 'Restores extra backup data; return the counts array or a WP_Error to roll back.',
                'wppoppop_system_report_lines'     => 'Filters the extra text lines of the Tools system report.',
                'wppoppop_extension_settings_keys' => 'Filters the popup-settings keys owned by extension modules (list of keys).',
                'wppoppop_sanitize_popup_settings' => 'Allow-lists the extension popup settings on save and on template save / import (settings object, context array).',
                'wppoppop_handled_settings_keys'   => 'Extensions list the settings keys they sanitised; other extension keys keep their stored value.',
                'wppoppop_popup_wrapper_attrs'     => 'Adds data-* attributes to the popup wrapper (attribute string, settings, args).',
                'wppoppop_popup_inline_css'        => 'Filters the popup inline CSS before minifying (css, settings, uid).',
                'wppoppop_popup_overlay'           => 'Filters the popup backdrop: array(css, layers) (default, settings).',
                'wppoppop_popup_position'          => 'Filters the popup position: array(css, pos) (default, settings).',
                'wppoppop_pay_button_attrs'        => 'Filters the data-* attributes of a Pay layer button (attribute string, element).',
                'wppoppop_popup_eligible'          => 'Filters whether a published popup may be shown (bool, popup row, decoded config).',
                'wppoppop_submission_precheck'     => 'Antibot / technical info before a lead is stored: array(deny, tech, mail_skip) (default, uid, fields, config).',
                'wppoppop_double_optin_config'     => 'Double Opt-In state of a popup: array(active, cfg) (default, config).',
                'wppoppop_double_optin_send'       => 'Sends the Double Opt-In confirmation email (bool sent, args array).',
                'wppoppop_submission_extras'       => 'Values added to the form response: array(download_url, coupon_code) (default, context).',
                'wppoppop_confirmation_thank'      => 'Thank-you text / address after a confirmed email: array(message, url) (default, config, email).',
                'wppoppop_resend_user_email'       => 'Submissions > Resend: sends the user email (bool sent, submission, config, email, fields, id).',
                'wppoppop_notification_tech_rows'  => 'Technical-info rows allowed in notification emails (rows, tech fields, config).',
                'wppoppop_submission_record_fields'=> 'Filters the fields shown on a submission detail (fields, popup uid).',
                'wppoppop_redact_config'           => 'Removes private account data from a popup config before it is printed or exported (config).',
                'wppoppop_diagnostic_sms'          => 'Sends the diagnostic test SMS (result, to number, credentials).',
            ],
            'actions' => [
                'wppoppop_init'                    => 'Fires when the WpPopPop core system completes initialization.',
                'wppoppop_loaded'                  => 'Fires after all core components are created; companion plugins boot here.',
                'wppoppop_admin_menu'              => 'Fires after core submenus are added; receives the parent slug.',
                'wppoppop_admin_builder_assets'    => 'Fires after builder scripts are enqueued; companion plugins enqueue theirs here.',
                'wppoppop_builder_ribbon_tools'    => 'Prints extra element buttons in the builder ribbon.',
                'wppoppop_builder_element_panels'  => 'Prints extra element inspector panels in the builder.',
                'wppoppop_register_element_sanitizers' => 'Companion plugins register backend element sanitizers here.',
                'wppoppop_before_save_popup'       => 'Fires immediately before a popup is inserted or updated in the database.',
                'wppoppop_after_save_popup'        => 'Fires immediately after a popup is successfully saved.',
                'wppoppop_after_duplicate_popup'   => 'Fires after a popup campaign is cloned.',
                'wppoppop_before_delete_popup'      => 'Fires before a campaign and its linked records are deleted.',
                'wppoppop_after_delete_popup'       => 'Fires after a campaign and its submissions have been deleted.',
                'wppoppop_before_submission_save'  => 'Fires before a new lead submission is written to the database.',
                'wppoppop_after_submission_saved'   => 'Fires after a new lead is inserted into wp_wppoppop_submissions.',
                'wppoppop_webhook_dispatched'      => 'Fires after an outbound webhook request is sent (non-blocking: args are the response stub, payload, popup uid).',
                'wppoppop_create_tables'           => 'Fires while core creates its tables; companion plugins create theirs here.',
                'wppoppop_impression_recorded'     => 'Fires after a popup impression is counted (popup uid).',
                'wppoppop_submission_saved'        => 'Fires right after a lead row is inserted (submission id).',
                'wppoppop_popup_uid_renamed'       => 'Fires after a popup uid is renamed (old uid, new uid).',
                'wppoppop_tracking_tab_sections'   => 'Prints extra sections in Settings > Tracking (value getter, checkbox row, number row).',
                'wppoppop_after_save_ab_campaign'  => 'Fires after an A/B split-testing campaign is created.',
                'wppoppop_after_delete_ab_campaign'=> 'Fires after an A/B split-testing campaign is removed.',
                'wppoppop_submission_dispatch'     => 'Fires after a lead is stored and the Notifications module ran; extension modules send their messages here (context).',
                'wppoppop_after_confirmation'      => 'Fires after a Double Opt-In link was confirmed (context).',
            ]
        ];
    }
}
