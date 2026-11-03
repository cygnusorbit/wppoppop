<?php
/**
 * WpPopPop Settings Defaults & Reset Lifecycle Manager
 * Autonomous Decomposed Modular Architecture
 *
 * @package WpPopPop
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WpPopPop_Settings_Defaults {

    /**
     * Active instance.
     * @var WpPopPop_Settings_Defaults|null
     */
    protected static $instance = null;

    /**
     * Get singleton instance.
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor.
     */
    public function __construct() {
        add_filter( 'wppoppop_default_settings', [ $this, 'filter_default_settings' ], 999 );
        add_filter( 'wppoppop_settings_defaults', [ $this, 'filter_default_settings' ], 999 );
        add_filter( 'default_option_wppoppop_settings', [ $this, 'filter_default_option' ], 999 );
        add_action( 'wp_ajax_wppoppop_reset_settings', [ $this, 'handle_reset_settings' ], 1 );
        add_action( 'admin_action_wppoppop_reset_settings', [ $this, 'handle_reset_settings' ], 1 );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_settings_script' ], 20 );
    }

    /**
     * Return authoritative defaults array.
     */
    public static function get_canonical_defaults() {
        // Task 1: Builder Tools - All disabled (0)
        $builder_tools = [
            'grid'          => 0,
            'ruler'         => 0,
            'guides'        => 0,
            'snapping'      => 0,
            'auto_save'     => 0,
            'history'       => 0,
            'zoom'          => 0,
            'shortcuts'     => 0,
            'code_editor'   => 0,
            'color_picker'  => 0,
            'font_manager'  => 0,
            'layer_lock'    => 0,
        ];

        // Task 2: Campaign Settings Modules - All disabled except 16, 17, 18, 19, 20
        $campaign_modules = [];
        for ( $i = 1; $i <= 30; $i++ ) {
            $is_enabled = in_array( $i, [ 16, 17, 18, 19, 20 ], true ) ? 1 : 0;
            $campaign_modules[ (string) $i ]           = $is_enabled;
            $campaign_modules[ 'module_' . $i ]        = $is_enabled;
        }

        // Task 3: Pages - Default checkmark = "True" for wppoppop-log, wppoppop-field-analytics, wppoppop-payments
        $pages = [
            'wppoppop-log'             => 1,
            'wppoppop-field-analytics' => 1,
            'wppoppop-payments'        => 1,
        ];

        return [
            'builder_tools'             => $builder_tools,
            'campaign_settings_modules' => $campaign_modules,
            'modules'                   => $campaign_modules,
            'pages'                     => $pages,
        ];
    }

    /**
     * Filter default settings array.
     */
    public function filter_default_settings( $defaults = [] ) {
        $canonical = self::get_canonical_defaults();

        if ( ! is_array( $defaults ) ) {
            $defaults = [];
        }

        // Merge Builder Tools (Force all disabled)
        $defaults['builder_tools'] = array_fill_keys(
            array_keys( isset( $defaults['builder_tools'] ) && is_array( $defaults['builder_tools'] ) ? $defaults['builder_tools'] : $canonical['builder_tools'] ),
            0
        );

        // Merge Campaign Modules (All disabled except 16, 17, 18, 19, 20)
        if ( isset( $defaults['campaign_settings_modules'] ) && is_array( $defaults['campaign_settings_modules'] ) ) {
            foreach ( $defaults['campaign_settings_modules'] as $mod_key => $val ) {
                preg_match( '/\d+/', (string) $mod_key, $m );
                $mod_num = ! empty( $m ) ? (int) $m[0] : null;
                $defaults['campaign_settings_modules'][ $mod_key ] = ( in_array( $mod_num, [ 16, 17, 18, 19, 20 ], true ) ) ? 1 : 0;
            }
        } else {
            $defaults['campaign_settings_modules'] = $canonical['campaign_settings_modules'];
        }

        if ( isset( $defaults['modules'] ) && is_array( $defaults['modules'] ) ) {
            foreach ( $defaults['modules'] as $mod_key => $val ) {
                preg_match( '/\d+/', (string) $mod_key, $m );
                $mod_num = ! empty( $m ) ? (int) $m[0] : null;
                $defaults['modules'][ $mod_key ] = ( in_array( $mod_num, [ 16, 17, 18, 19, 20 ], true ) ) ? 1 : 0;
            }
        }

        // Merge Pages (Force true for target pages)
        if ( ! isset( $defaults['pages'] ) || ! is_array( $defaults['pages'] ) ) {
            $defaults['pages'] = [];
        }
        $defaults['pages']['wppoppop-log']             = 1;
        $defaults['pages']['wppoppop-field-analytics'] = 1;
        $defaults['pages']['wppoppop-payments']        = 1;

        return $defaults;
    }

    /**
     * Filter fallback for empty get_option('wppoppop_settings').
     */
    public function filter_default_option( $default ) {
        return $this->filter_default_settings( is_array( $default ) ? $default : [] );
    }

    /**
     * Task 4: AJAX & Form Reset Handler
     */
    public function handle_reset_settings() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Unauthorized user.', 'wppoppop' ) );
        }

        check_admin_referer( 'wppoppop_reset_settings_nonce', 'nonce' );

        $defaults = self::get_canonical_defaults();
        update_option( 'wppoppop_settings', $defaults );
        update_option( 'wppoppop_builder_tools', $defaults['builder_tools'] );
        update_option( 'wppoppop_campaign_modules', $defaults['campaign_settings_modules'] );
        update_option( 'wppoppop_pages', $defaults['pages'] );

        if ( wp_doing_ajax() ) {
            wp_send_json_success( [
                'message'  => __( 'Settings successfully reset to updated defaults.', 'wppoppop' ),
                'defaults' => $defaults,
            ] );
        }

        wp_safe_redirect( add_query_arg( [ 'page' => 'wppoppop-settings', 'status' => 'reset' ], admin_url( 'admin.php' ) ) );
        exit;
    }

    /**
     * Enqueue Reset Handler and Live UI Synchronizer on Settings Page.
     */
    public function enqueue_settings_script( $hook ) {
        if ( strpos( $hook, 'wppoppop-settings' ) === false && ( ! isset( $_GET['page'] ) || $_GET['page'] !== 'wppoppop-settings' ) ) {
            return;
        }

        $canonical = self::get_canonical_defaults();
        ?>
        <script type="text/javascript" id="wppoppop-settings-sync-script">
        (function() {
            'use strict';

            var canonicalDefaults = <?php echo wp_json_encode( $canonical ); ?>;

            function applySettingsDefaultsToDOM() {
                // 1. Builder Tools: Uncheck all
                var builderCheckboxes = document.querySelectorAll(
                    '[name^="builder_tools"], [name^="wppoppop_settings[builder_tools]"], [data-section="builder_tools"] input[type="checkbox"], .wppoppop-builder-tool-checkbox'
                );
                builderCheckboxes.forEach(function(cb) {
                    cb.checked = false;
                    cb.removeAttribute('checked');
                    cb.dispatchEvent(new Event('change', { bubbles: true }));
                });

                // 2. Campaign Settings Modules: Uncheck all except 16, 17, 18, 19, 20
                var moduleCheckboxes = document.querySelectorAll(
                    '[name*="campaign_modules"], [name*="campaign_settings_modules"], [name*="modules["], .wppoppop-module-checkbox'
                );
                moduleCheckboxes.forEach(function(cb) {
                    var val = (cb.value || cb.name || cb.id || '').toLowerCase();
                    var numMatches = val.match(/\d+/);
                    var modNum = numMatches ? parseInt(numMatches[0], 10) : null;
                    var shouldEnable = [16, 17, 18, 19, 20].indexOf(modNum) !== -1;

                    cb.checked = shouldEnable;
                    if (shouldEnable) {
                        cb.setAttribute('checked', 'checked');
                    } else {
                        cb.removeAttribute('checked');
                    }
                    cb.dispatchEvent(new Event('change', { bubbles: true }));
                });

                // 3. Pages: Check wppoppop-log, wppoppop-field-analytics, wppoppop-payments
                var targetPages = ['wppoppop-log', 'wppoppop-field-analytics', 'wppoppop-payments'];
                targetPages.forEach(function(slug) {
                    var pageCheckboxes = document.querySelectorAll(
                        'input[type="checkbox"][value="' + slug + '"], input[name*="[' + slug + ']"], #' + slug
                    );
                    pageCheckboxes.forEach(function(cb) {
                        cb.checked = true;
                        cb.setAttribute('checked', 'checked');
                        cb.dispatchEvent(new Event('change', { bubbles: true }));
                    });
                });
            }

            // Task 4: Hook Reset Button
            document.addEventListener('click', function(e) {
                var btn = e.target.closest('#wppoppop-reset-settings, .wppoppop-reset-button, [data-action="reset-settings"], [name="reset_settings"]');
                if (!btn) return;

                if (!confirm('Are you sure you want to reset all settings to defaults?')) {
                    e.preventDefault();
                    return;
                }

                // Immediately synchronize DOM inputs to new default values
                applySettingsDefaultsToDOM();
            }, true);

            // If query param is reset, ensure inputs match immediately
            if (window.location.search.indexOf('status=reset') !== -1) {
                document.addEventListener('DOMContentLoaded', applySettingsDefaultsToDOM);
            }
        })();
        </script>
        <?php
    }
}

// Bootstrap
WpPopPop_Settings_Defaults::get_instance();
