<?php
namespace WPPopPop\Targeting;

class PopupPostType {
    public const POST_TYPE = 'wppoppop';
    public const LEAD_POST_TYPE = 'wppoppop_lead';

    public function init(): void {
        add_action('init', [$this, 'register_post_types']);
        add_shortcode('wppoppop', [$this, 'render_shortcode']);
    }

    public function register_post_types(): void {
        register_post_type(self::POST_TYPE, [
            'labels' => [
                'name'          => __('Popups', 'wppoppop'),
                'singular_name' => __('Popup', 'wppoppop'),
            ],
            'public'              => false,
            'show_ui'             => true,
            'show_in_menu'        => false,
            'supports'            => ['title', 'editor'],
            'has_archive'         => false,
        ]);

        register_post_type(self::LEAD_POST_TYPE, [
            'labels' => [
                'name'          => __('Captured Leads', 'wppoppop'),
                'singular_name' => __('Lead', 'wppoppop'),
            ],
            'public'              => false,
            'show_ui'             => false,
            'supports'            => ['title'],
        ]);
    }

    public function render_shortcode(array $atts): string {
        $atts = shortcode_atts(['id' => 0, 'text' => __('Open Popup', 'wppoppop')], $atts, 'wppoppop');
        $popup_id = absint($atts['id']);
        if (!$popup_id) return '';

        return sprintf(
            '<button type="button" class="wppoppop-trigger wppoppop-btn-inline" data-popup-id="%d">%s</button>',
            $popup_id,
            esc_html($atts['text'])
        );
    }

    public function add_locker_meta_box(): void {
        add_meta_box(
            'wppoppop_locker_settings',
            __('Content Locker Mode & Access Gate', 'wppoppop'),
            [$this, 'render_locker_metabox'],
            self::POST_TYPE,
            'side',
            'default'
        );
    }

    public function render_locker_metabox(\WP_Post $post): void {
        $is_locker = get_post_meta($post->ID, '_wppoppop_is_locker', true);
        $duration  = get_post_meta($post->ID, '_wppoppop_unlock_duration', true) ?: 30;
        wp_nonce_field('wppoppop_locker_nonce_action', 'wppoppop_locker_nonce');
        ?>
        <p>
            <label>
                <input type="checkbox" name="_wppoppop_is_locker" value="1" <?php checked($is_locker, '1'); ?> />
                <strong><?php echo esc_html__('Enable Page Locker Mode', 'wppoppop'); ?></strong>
            </label>
        </p>
        <p class="description">
            <?php echo esc_html__('Disables the close button, blurs the page, and prevents closing until the form is submitted.', 'wppoppop'); ?>
        </p>
        <p>
            <label for="wppoppop_unlock_duration"><?php echo esc_html__('Unlock Duration (Days):', 'wppoppop'); ?></label>
            <input type="number" id="wppoppop_unlock_duration" name="_wppoppop_unlock_duration" value="<?php echo esc_attr($duration); ?>" min="1" max="365" style="width: 100%;" />
        </p>
        <hr style="border:0;border-top:1px solid #e2e8f0;margin:12px 0;" />
        <p style="font-size:12px;color:#64748b;margin:0;">
            <strong><?php echo esc_html__('Inline Gated Shortcode:', 'wppoppop'); ?></strong><br>
            <code>[wppoppop_locker id="<?php echo esc_attr($post->ID); ?>"]Premium content[/wppoppop_locker]</code>
        </p>
        <?php
    }

    public function save_locker_meta(int $post_id): void {
        if (!isset($_POST['wppoppop_locker_nonce']) || !wp_verify_nonce($_POST['wppoppop_locker_nonce'], 'wppoppop_locker_nonce_action')) {
            return;
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        update_post_meta($post_id, '_wppoppop_is_locker', isset($_POST['_wppoppop_is_locker']) ? '1' : '0');
        update_post_meta($post_id, '_wppoppop_unlock_duration', absint($_POST['_wppoppop_unlock_duration'] ?? 30));
    }
}
