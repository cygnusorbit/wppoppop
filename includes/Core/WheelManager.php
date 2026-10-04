<?php
namespace WPPopPop\Core;

use WPPopPop\Targeting\PopupPostType;

class WheelManager {
    public static function init(): void {
        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_wheel_assets']);
        add_filter('wppoppop_process_lead_fields', [__CLASS__, 'record_prize_metadata'], 10, 2);
    }

    public static function enqueue_wheel_assets(): void {
        wp_enqueue_style(
            'wppoppop-fortune-wheel',
            WPPOPPOP_URL . 'assets/css/fortune-wheel.css',
            [],
            WPPOPPOP_VERSION
        );

        wp_enqueue_script(
            'wppoppop-fortune-wheel',
            WPPOPPOP_URL . 'assets/js/fortune-wheel.js',
            [],
            WPPOPPOP_VERSION,
            true
        );
    }

    public static function get_default_slices(): array {
        return [
            ['label' => '10% OFF',       'coupon' => 'SAVE10',     'color' => '#b5295c', 'weight' => 25],
            ['label' => 'Free Shipping', 'coupon' => 'FREESHIP',   'color' => '#1e293b', 'weight' => 20],
            ['label' => 'Try Again',     'coupon' => 'NONE',       'color' => '#64748b', 'weight' => 15],
            ['label' => '25% OFF',       'coupon' => 'SAVE25',     'color' => '#059669', 'weight' => 15],
            ['label' => '5% OFF',        'coupon' => 'SAVE05',     'color' => '#3b82f6', 'weight' => 15],
            ['label' => 'Jackpot $50',   'coupon' => 'JACKPOT50',  'color' => '#f59e0b', 'weight' => 10],
        ];
    }

    public static function get_popup_slices(int $popup_id): array {
        $raw = get_post_meta($popup_id, '_wppoppop_wheel_slices', true);
        if (empty($raw)) {
            return self::get_default_slices();
        }

        $slices = [];
        $lines = explode("\n", str_replace("\r", "", trim($raw)));
        $palette = ['#b5295c', '#1e293b', '#059669', '#3b82f6', '#f59e0b', '#8b5cf6', '#0284c7', '#dc2626'];

        foreach ($lines as $i => $line) {
            $parts = explode('|', $line);
            $label = trim($parts[0] ?? '');
            if (empty($label)) continue;

            $slices[] = [
                'label'  => esc_html($label),
                'coupon' => sanitize_text_field(trim($parts[1] ?? 'NONE')),
                'color'  => $palette[$i % count($palette)],
                'weight' => isset($parts[2]) ? absint($parts[2]) : 15,
            ];
        }

        return !empty($slices) ? $slices : self::get_default_slices();
    }

    public static function record_prize_metadata(array $fields, int $lead_id): array {
        if (!empty($_POST['fields']['won_prize'])) {
            $prize = sanitize_text_field($_POST['fields']['won_prize']);
            update_post_meta($lead_id, '_wppoppop_won_prize', $prize);
            $fields['won_prize'] = $prize;
        }
        return $fields;
    }
}
