<?php
namespace WPPopPop\Admin;

class MenuManager {
    public static function init(): void {
        add_action('admin_menu', [__CLASS__, 'register_menus'], 9);
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_assets']);
        add_action('wp_ajax_wppoppop_save_general_settings', [__CLASS__, 'ajax_save_settings']);
        add_action('wp_ajax_wppoppop_reset_cookies', [__CLASS__, 'ajax_reset_cookies']);
        add_action('wp_ajax_wppoppop_import_library_template', [__CLASS__, 'ajax_import_template']);
    }

    public static function register_menus(): void {
        add_menu_page(
            __('WP Pop Pop', 'wppoppop'),
            __('WP Pop Pop', 'wppoppop'),
            'manage_options',
            'wppoppop',
            ['\\WPPopPop\\Admin\\AdminPages', 'render_popups_page'],
            'dashicons-format-gallery',
            30
        );

        add_submenu_page('wppoppop', __('Popups', 'wppoppop'), __('Popups', 'wppoppop'), 'manage_options', 'wppoppop', ['\\WPPopPop\\Admin\\AdminPages', 'render_popups_page']);
        add_submenu_page('wppoppop', __('Create Popup', 'wppoppop'), __('Create Popup', 'wppoppop'), 'manage_options', 'wppoppop-add', ['\\WPPopPop\\Admin\\AdminPages', 'render_create_page']);
        add_submenu_page('wppoppop', __('A/B Campaigns', 'wppoppop'), __('A/B Campaigns', 'wppoppop'), 'manage_options', 'wppoppop-campaigns', ['\\WPPopPop\\Admin\\AdminPages', 'render_campaigns_page']);
        add_submenu_page('wppoppop', __('Targeting', 'wppoppop'), __('Targeting', 'wppoppop'), 'manage_options', 'wppoppop-targeting', ['\\WPPopPop\\Admin\\AdminPages', 'render_targeting_page']);
        add_submenu_page('wppoppop', __('Log', 'wppoppop'), __('Log', 'wppoppop'), 'manage_options', 'wppoppop-log', ['\\WPPopPop\\Admin\\AdminPages', 'render_log_page']);
        add_submenu_page('wppoppop', __('Stats', 'wppoppop'), __('Stats', 'wppoppop'), 'manage_options', 'wppoppop-stats', ['\\WPPopPop\\Admin\\AdminPages', 'render_stats_page']);
        add_submenu_page('wppoppop', __('Field Analytics', 'wppoppop'), __('Field Analytics', 'wppoppop'), 'manage_options', 'wppoppop-analytics', ['\\WPPopPop\\Admin\\AdminPages', 'render_analytics_page']);
        add_submenu_page('wppoppop', __('Transactions', 'wppoppop'), __('Transactions', 'wppoppop'), 'manage_options', 'wppoppop-transactions', ['\\WPPopPop\\Admin\\AdminPages', 'render_transactions_page']);
        add_submenu_page('wppoppop', __('Popups Library', 'wppoppop'), __('Popups Library', 'wppoppop'), 'manage_options', 'wppoppop-library', ['\\WPPopPop\\Admin\\AdminPages', 'render_library_page']);
        add_submenu_page('wppoppop', __('Settings', 'wppoppop'), __('Settings', 'wppoppop'), 'manage_options', 'wppoppop-settings', ['\\WPPopPop\\Admin\\AdminPages', 'render_settings_page']);
    }

    public static function enqueue_assets(string $hook): void {
        if (strpos($hook, 'wppoppop') === false) return;

        wp_enqueue_style('wppoppop-admin-menu', WPPOPPOP_URL . 'assets/css/admin-menu.css', ['dashicons'], WPPOPPOP_VERSION);
        wp_enqueue_script('wppoppop-admin-menu-js', WPPOPPOP_URL . 'assets/js/admin-menu.js', ['jquery'], WPPOPPOP_VERSION, true);

        wp_localize_script('wppoppop-admin-menu-js', 'WPPopPopAdmin', [
            'ajax_url'    => admin_url('admin-ajax.php'),
            'nonce'       => wp_create_nonce('wppoppop_admin_nonce'),
            'builder_url' => admin_url('admin.php?page=wppoppop-add'),
        ]);
    }

    public static function ajax_save_settings(): void {
        check_ajax_referer('wppoppop_admin_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error();

        $settings = isset($_POST['settings']) && is_array($_POST['settings']) ? wp_unslash($_POST['settings']) : [];
        update_option('wppoppop_general_settings', $settings);
        wp_send_json_success(['message' => __('Settings saved successfully.', 'wppoppop')]);
    }

    public static function ajax_reset_cookies(): void {
        check_ajax_referer('wppoppop_admin_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error();

        update_option('wppoppop_cookie_reset_timestamp', time());
        wp_send_json_success(['message' => __('Visitor cookies reset successfully.', 'wppoppop')]);
    }

    public static function ajax_import_template(): void {
        check_ajax_referer('wppoppop_admin_nonce', 'nonce');
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(['message' => 'Permission denied.']);
        }

        $template_id = sanitize_text_field($_POST['template_id'] ?? '27124');
        $title = 'Template #' . $template_id;

        $new_id = wp_insert_post([
            'post_title'  => $title,
            'post_type'   => 'wppoppop',
            'post_status' => 'publish',
        ]);

        if (is_wp_error($new_id)) {
            wp_send_json_error(['message' => 'Failed to import template.']);
        }

        // Load blueprint layers from TemplateLibrary
        $layers = \WPPopPop\Admin\TemplateLibrary::get_template_layers($template_id);
        update_post_meta($new_id, '_wppoppop_slug', 'template-' . $template_id);
        update_post_meta($new_id, '_wppoppop_builder_layers', $layers);

        wp_send_json_success([
            'redirect' => admin_url('admin.php?page=wppoppop-add&id=' . $new_id),
            'message'  => __('Template successfully imported! Opening editor...', 'wppoppop')
        ]);
        return;
    }

    private static function _legacy_import_unused(): void {
        check_ajax_referer('wppoppop_admin_nonce', 'nonce');
        if (!current_user_can('edit_posts')) wp_send_json_error();

        $template_id = sanitize_text_field($_POST['template_id'] ?? '27124');
        $new_id = wp_insert_post([
            'post_title'  => 'Imported Template #' . $template_id,
            'post_type'   => 'wppoppop',
            'post_status' => 'publish',
        ]);

        wp_send_json_success([
            'redirect' => admin_url('admin.php?page=wppoppop-add&id=' . $new_id),
            'message'  => __('Template imported! Opening editor...', 'wppoppop')
        ]);
    }
}
