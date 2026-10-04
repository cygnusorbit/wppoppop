<?php
namespace WPPopPop\Targeting;

class ABTestingManager {
    public const CAMPAIGN_CPT = 'wppoppop_campaign';

    public static function init(): void {
        add_action('init', [__CLASS__, 'register_campaign_cpt']);
        add_action('wp_ajax_wppoppop_save_campaign', [__CLASS__, 'ajax_save_campaign']);
        add_action('wp_ajax_wppoppop_delete_campaign', [__CLASS__, 'ajax_delete_campaign']);
        add_shortcode('wppoppop_campaign', [__CLASS__, 'render_campaign_shortcode']);
    }

    public static function register_campaign_cpt(): void {
        register_post_type(self::CAMPAIGN_CPT, [
            'labels' => [
                'name'          => __('A/B Campaigns', 'wppoppop'),
                'singular_name' => __('A/B Campaign', 'wppoppop'),
            ],
            'public'              => false,
            'show_ui'             => false,
            'supports'            => ['title'],
            'has_archive'         => false,
            'exclude_from_search' => true,
        ]);
    }

    public static function ajax_save_campaign(): void {
        check_ajax_referer('wppoppop_admin_nonce', 'nonce');
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(['message' => __('Permission denied.', 'wppoppop')]);
        }

        $campaign_id = absint($_POST['campaign_id'] ?? 0);
        $title       = sanitize_text_field($_POST['title'] ?? '');
        $slug        = sanitize_title($_POST['slug'] ?? '');
        $popups      = isset($_POST['popups']) && is_array($_POST['popups']) ? array_map('absint', $_POST['popups']) : [];

        if (empty($title)) {
            wp_send_json_error(['message' => __('Campaign name is required.', 'wppoppop')]);
        }

        if (empty($slug)) {
            $slug = sanitize_title($title);
        }

        if ($campaign_id > 0) {
            wp_update_post([
                'ID'         => $campaign_id,
                'post_title' => $title,
                'post_name'  => $slug,
            ]);
            $saved_id = $campaign_id;
        } else {
            $saved_id = wp_insert_post([
                'post_type'   => self::CAMPAIGN_CPT,
                'post_title'  => $title,
                'post_name'   => $slug,
                'post_status' => 'publish',
            ]);
        }

        if (is_wp_error($saved_id) || !$saved_id) {
            wp_send_json_error(['message' => __('Failed to save campaign.', 'wppoppop')]);
        }

        update_post_meta($saved_id, '_wppoppop_campaign_popups', $popups);
        update_post_meta($saved_id, '_wppoppop_campaign_slug', $slug);

        wp_send_json_success([
            'campaign_id' => $saved_id,
            'message'     => __('Campaign successfully saved!', 'wppoppop'),
        ]);
    }

    public static function ajax_delete_campaign(): void {
        check_ajax_referer('wppoppop_admin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied.', 'wppoppop')]);
        }

        $campaign_id = absint($_POST['campaign_id'] ?? 0);
        if ($campaign_id > 0 && wp_delete_post($campaign_id, true)) {
            wp_send_json_success(['message' => __('Campaign deleted.', 'wppoppop')]);
        }

        wp_send_json_error(['message' => __('Failed to delete campaign.', 'wppoppop')]);
    }

    public static function resolve_campaign_variant(int $campaign_id): int {
        $popups = get_post_meta($campaign_id, '_wppoppop_campaign_popups', true);
        if (empty($popups) || !is_array($popups)) {
            return 0;
        }

        $cookie_key = 'wppoppop_ab_' . $campaign_id;
        if (isset($_COOKIE[$cookie_key]) && in_array(absint($_COOKIE[$cookie_key]), $popups, true)) {
            return absint($_COOKIE[$cookie_key]);
        }

        // Randomly pick a variant
        $selected_popup_id = $popups[array_rand($popups)];
        if (!headers_sent()) {
            setcookie($cookie_key, (string) $selected_popup_id, time() + (30 * 86400), COOKIEPATH, COOKIE_DOMAIN, is_ssl(), false);
        }

        return $selected_popup_id;
    }

    public static function render_campaign_shortcode(array $atts): string {
        $atts = shortcode_atts([
            'id'   => 0,
            'slug' => '',
            'text' => __('Open Campaign Popup', 'wppoppop'),
        ], $atts, 'wppoppop_campaign');

        $campaign_id = absint($atts['id']);
        if (!$campaign_id && !empty($atts['slug'])) {
            $page = get_page_by_path(sanitize_title($atts['slug']), OBJECT, self::CAMPAIGN_CPT);
            if ($page) {
                $campaign_id = $page->ID;
            }
        }

        if (!$campaign_id) {
            return '';
        }

        $chosen_popup_id = self::resolve_campaign_variant($campaign_id);
        if (!$chosen_popup_id) {
            return '';
        }

        return sprintf(
            '<button type="button" class="wppoppop-trigger wppoppop-btn-inline" data-popup-id="%d" data-campaign-id="%d">%s</button>',
            $chosen_popup_id,
            $campaign_id,
            esc_html($atts['text'])
        );
    }
}
