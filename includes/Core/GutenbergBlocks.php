<?php
namespace WPPopPop\Core;

use WPPopPop\Targeting\PopupPostType;

class GutenbergBlocks {
    public static function init(): void {
        add_action('init', [__CLASS__, 'register_blocks']);
        add_action('enqueue_block_editor_assets', [__CLASS__, 'enqueue_editor_assets']);
    }

    public static function enqueue_editor_assets(): void {
        wp_enqueue_script(
            'wppoppop-gutenberg-blocks',
            WPPOPPOP_URL . 'assets/js/blocks.js',
            ['wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-server-side-render'],
            WPPOPPOP_VERSION,
            true
        );

        $popups = get_posts([
            'post_type'      => PopupPostType::POST_TYPE,
            'post_status'    => 'publish',
            'posts_per_page' => 100,
            'orderby'        => 'title',
            'order'          => 'ASC',
        ]);

        $popup_options = [
            ['value' => 0, 'label' => __('-- Select a Popup --', 'wppoppop')]
        ];

        foreach ($popups as $p) {
            $popup_options[] = [
                'value' => $p->ID,
                'label' => esc_html($p->post_title) . ' (#' . $p->ID . ')',
            ];
        }

        wp_localize_script('wppoppop-gutenberg-blocks', 'WPPopPopGutenberg', [
            'popups' => $popup_options,
        ]);

        wp_enqueue_style(
            'wppoppop-block-editor-css',
            WPPOPPOP_URL . 'assets/css/wppoppop.css',
            [],
            WPPOPPOP_VERSION
        );
    }

    public static function register_blocks(): void {
        if (!function_exists('register_block_type')) {
            return;
        }

        // 1. Inline Popup Box Block
        register_block_type('wppoppop/popup-box', [
            'attributes' => [
                'popupId' => [
                    'type'    => 'number',
                    'default' => 0,
                ],
            ],
            'render_callback' => [__CLASS__, 'render_popup_box'],
        ]);

        // 2. Trigger Button Block
        register_block_type('wppoppop/trigger-button', [
            'attributes' => [
                'popupId' => [
                    'type'    => 'number',
                    'default' => 0,
                ],
                'buttonText' => [
                    'type'    => 'string',
                    'default' => 'Open Popup',
                ],
                'alignment' => [
                    'type'    => 'string',
                    'default' => 'center',
                ],
            ],
            'render_callback' => [__CLASS__, 'render_trigger_button'],
        ]);
    }

    public static function render_popup_box(array $attributes): string {
        $popup_id = absint($attributes['popupId'] ?? 0);
        if (!$popup_id) {
            return '<div class="wppoppop-block-placeholder" style="padding:20px;background:#f8fafc;border:1px dashed #cbd5e1;text-align:center;color:#64748b;font-size:13px;">' .
                esc_html__('Please select a popup from the block settings sidebar.', 'wppoppop') .
                '</div>';
        }

        $popup = get_post($popup_id);
        if (!$popup || $popup->post_type !== PopupPostType::POST_TYPE || $popup->post_status !== 'publish') {
            return '';
        }

        return '<div class="wppoppop-gutenberg-inline-wrap" data-popup-id="' . esc_attr($popup_id) . '">' .
            LayerRenderer::render_layers($popup_id, apply_filters('the_content', $popup->post_content)) .
            '</div>';
    }

    public static function render_trigger_button(array $attributes): string {
        $popup_id    = absint($attributes['popupId'] ?? 0);
        $button_text = sanitize_text_field($attributes['buttonText'] ?? 'Open Popup');
        $alignment   = sanitize_text_field($attributes['alignment'] ?? 'center');

        if (!$popup_id) {
            return '<div class="wppoppop-block-placeholder" style="padding:15px;background:#f8fafc;border:1px dashed #cbd5e1;text-align:center;color:#64748b;font-size:13px;">' .
                esc_html__('Select a target popup in button block settings.', 'wppoppop') .
                '</div>';
        }

        $style = 'text-align:' . esc_attr($alignment) . '; margin: 16px 0;';

        return sprintf(
            '<div class="wppoppop-button-block-wrap" style="%s">' .
            '  <button type="button" class="wppoppop-trigger wppoppop-btn-pink wppoppop-gutenberg-btn" data-popup-id="%d">%s</button>' .
            '</div>',
            $style,
            $popup_id,
            esc_html($button_text)
        );
    }
}
