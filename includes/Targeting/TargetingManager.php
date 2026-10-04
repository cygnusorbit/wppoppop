<?php
namespace WPPopPop\Targeting;

class TargetingManager {
    public const MATRIX_OPTION = 'wppoppop_targeting_matrix';

    public static function init(): void {
        add_action('wp_ajax_wppoppop_save_targeting_matrix', [__CLASS__, 'ajax_save_matrix']);
    }

    public static function get_default_matrix(): array {
        return [
            'onload'        => ['active' => [], 'passive' => []],
            'onscroll'      => ['active' => [], 'passive' => []],
            'onexit'        => ['active' => [], 'passive' => []],
            'oninactivity'  => ['active' => [], 'passive' => []],
            'contentstart'  => ['active' => [], 'passive' => []],
            'contentend'    => ['active' => [], 'passive' => []],
        ];
    }

    public static function get_matrix(): array {
        $matrix = get_option(self::MATRIX_OPTION, []);
        $defaults = self::get_default_matrix();
        return wp_parse_args($matrix, $defaults);
    }

    public static function get_active_popups_for_event(string $event): array {
        $matrix = self::get_matrix();
        return isset($matrix[$event]['active']) ? array_map('absint', $matrix[$event]['active']) : [];
    }

    public static function ajax_save_matrix(): void {
        check_ajax_referer('wppoppop_admin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied.', 'wppoppop')]);
        }

        $event   = sanitize_key($_POST['event'] ?? 'onload');
        $active  = isset($_POST['active']) && is_array($_POST['active']) ? array_map('absint', $_POST['active']) : [];
        $passive = isset($_POST['passive']) && is_array($_POST['passive']) ? array_map('absint', $_POST['passive']) : [];

        $matrix = self::get_matrix();
        $matrix[$event] = [
            'active'  => $active,
            'passive' => $passive,
        ];

        update_option(self::MATRIX_OPTION, $matrix);
        wp_send_json_success(['message' => __('Targeting matrix updated.', 'wppoppop')]);
    }
}
