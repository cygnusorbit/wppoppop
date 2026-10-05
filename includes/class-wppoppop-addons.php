<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Addons {
    public function __construct() {
        add_shortcode('wppoppop_locker', [$this, 'render_content_locker']);
        add_action('init', [$this, 'handle_secure_download_request']);
        add_action('wp_footer', [$this, 'render_sticky_side_tabs']);
    }

    /**
     * Inline Content Locker Shortcode [wppoppop_locker uid="..."]Content[/wppoppop_locker]
     */
    public function render_content_locker($atts, $content = null) {
        $atts = shortcode_atts(['uid' => ''], $atts, 'wppoppop_locker');
        if (empty($atts['uid']) || empty($content)) {
            return $content;
        }

        $cookie_name = 'wppoppop_unlocked_' . sanitize_key($atts['uid']);
        if (isset($_COOKIE[$cookie_name])) {
            return do_shortcode($content);
        }

        ob_start();
        ?>
        <div class="wppoppop-content-locker" data-locker-uid="<?php echo esc_attr($atts['uid']); ?>">
            <div class="wppoppop-locked-content">
                <?php echo do_shortcode($content); ?>
            </div>
            <div class="wppoppop-locker-overlay">
                <div class="wppoppop-locker-prompt">
                    <span class="dashicons dashicons-lock" style="font-size: 32px; width: 32px; height: 32px; margin-bottom: 8px;"></span>
                    <p>This premium content is locked. Complete the form to unlock full access.</p>
                    <button type="button" class="button button-primary wppoppop-open-btn" data-target-uid="<?php echo esc_attr($atts['uid']); ?>">
                        Unlock Content
                    </button>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Streams Secure Downloads after token verification
     */
    public function handle_secure_download_request() {
        if (!isset($_GET['wppoppop_download'])) {
            return;
        }

        $token = sanitize_key($_GET['wppoppop_download']);
        global $wpdb;
        $table_downloads = $wpdb->prefix . 'wppoppop_downloads';

        $record = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table_downloads} WHERE token = %s AND expires_at > NOW()",
            $token
        ));

        if (!$record) {
            wp_die('Invalid or expired download link.', 'Download Expired', ['response' => 403]);
        }

        // Increment download counter
        $wpdb->query($wpdb->prepare(
            "UPDATE {$table_downloads} SET downloads_count = downloads_count + 1 WHERE id = %d",
            $record->id
        ));

        // Redirect to secure media file
        wp_redirect(esc_url_raw($record->file_url));
        exit;
    }

    /**
     * Renders sticky side tabs in the footer
     */
    public function render_sticky_side_tabs() {
        if (is_admin()) {
            return;
        }

        global $wpdb;
        $popups = $wpdb->get_results("SELECT uid, data FROM {$wpdb->prefix}wppoppop_items WHERE status = 'publish'");

        if (empty($popups)) {
            return;
        }

        foreach ($popups as $popup) {
            $config = json_decode($popup->data, true);
            $sidetab = isset($config['sidetab']) ? $config['sidetab'] : [];

            if (!empty($sidetab['enable'])) {
                $position = !empty($sidetab['position']) ? $sidetab['position'] : 'right';
                $label    = !empty($sidetab['label']) ? esc_html($sidetab['label']) : 'Open Form';
                $bg_color = !empty($sidetab['bg_color']) ? esc_attr($sidetab['bg_color']) : '#2271b1';
                $color    = !empty($sidetab['color']) ? esc_attr($sidetab['color']) : '#ffffff';
                ?>
                <div class="wppoppop-sidetab wppoppop-sidetab-<?php echo esc_attr($position); ?>"
                     data-target-uid="<?php echo esc_attr($popup->uid); ?>"
                     style="background-color: <?php echo $bg_color; ?>; color: <?php echo $color; ?>;">
                    <span><?php echo $label; ?></span>
                </div>
                <?php
            }
        }
    }
}
