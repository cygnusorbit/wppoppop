<?php
namespace WPPopPop\Core;

use WPPopPop\Targeting\PopupPostType;

class GutenbergBlocks {
    public function init(): void {
        add_action('init', [$this, 'register_blocks']);
        add_action('enqueue_block_editor_assets', [$this, 'enqueue_editor_assets']);
    }

    public function register_blocks(): void {
        if (!function_exists('register_block_type')) {
            return;
        }

        register_block_type('wppoppop/popup-box', [
            'api_version'     => 2,
            'editor_script'   => 'wppoppop-blocks',
            'editor_style'    => 'wppoppop-frontend',
            'render_callback' => [$this, 'render_popup_box_block'],
            'attributes'      => [
                'popupId' => [
                    'type'    => 'number',
                    'default' => 0,
                ],
            ],
        ]);

        register_block_type('wppoppop/trigger-button', [
            'api_version'     => 2,
            'editor_script'   => 'wppoppop-blocks',
            'editor_style'    => 'wppoppop-frontend',
            'render_callback' => [$this, 'render_trigger_button_block'],
            'attributes'      => [
                'popupId' => [
                    'type'    => 'number',
                    'default' => 0,
                ],
                'buttonText' => [
                    'type'    => 'string',
                    'default' => __('Open Popup', 'wppoppop'),
                ],
            ],
        ]);
    }

    public function enqueue_editor_assets(): void {
        $popups = get_posts([
            'post_type'      => PopupPostType::POST_TYPE,
            'post_status'    => 'publish',
            'posts_per_page' => 100,
            'orderby'        => 'title',
            'order'          => 'ASC',
        ]);

        $options = [
            ['value' => 0, 'label' => __('-- Select a Popup --', 'wppoppop')]
        ];

        foreach ($popups as $p) {
            $options[] = [
                'value' => $p->ID,
                'label' => $p->post_title . ' (#' . $p->ID . ')',
            ];
        }

        wp_enqueue_script(
            'wppoppop-blocks',
            WPPOPPOP_URL . 'assets/js/blocks.js',
            ['wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n'],
            WPPOPPOP_VERSION,
            true
        );

        wp_localize_script('wppoppop-blocks', 'WPPopPopEditor', [
            'popups' => $options,
        ]);
    }

    public function render_popup_box_block(array $attributes): string {
        $popup_id = absint($attributes['popupId'] ?? 0);
        if (!$popup_id) {
            return '';
        }
        $popup = get_post($popup_id);
        if (!$popup || $popup->post_type !== PopupPostType::POST_TYPE) {
            return '';
        }

        return '<div class="wppoppop-inline-container">' . 
               LayerRenderer::render_layers($popup_id, apply_filters('the_content', $popup->post_content)) . 
               '</div>';
    }

    public function render_trigger_button_block(array $attributes): string {
        $popup_id    = absint($attributes['popupId'] ?? 0);
        $button_text = sanitize_text_field($attributes['buttonText'] ?? __('Open Popup', 'wppoppop'));
        if (!$popup_id) {
            return '';
        }

        return sprintf(
            '<button type="button" class="wppoppop-trigger wppoppop-btn-inline" data-popup-id="%d">%s</button>',
            $popup_id,
            esc_html($button_text)
        );
    }
}
