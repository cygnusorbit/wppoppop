<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Front {
    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_assets']);
        add_action('wp_footer', [$this, 'render_live_site_preview'], 1);
        add_action('wp_footer', [$this, 'render_targeted_popups'], 10);
    }

    public function enqueue_frontend_assets() {
        wp_enqueue_style('wppoppop-front-css', WPPOPPOP_URL . 'public/css/wppoppop-front.css', [], WPPOPPOP_VERSION);
        wp_enqueue_script('wppoppop-front-js', WPPOPPOP_URL . 'public/js/wppoppop-front.js', ['jquery'], WPPOPPOP_VERSION, true);

        wp_localize_script('wppoppop-front-js', 'wppoppop_front_vars', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('wppoppop_front_nonce')
        ]);
    }

    public function render_live_site_preview() {
        if (!isset($_GET['wppoppop_preview']) || !current_user_can('manage_options')) {
            return;
        }

        $preview_uid = sanitize_key($_GET['wppoppop_preview']);
        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';
        $popup = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_name} WHERE uid = %s", $preview_uid));

        if (!$popup) {
            return;
        }

        $config = json_decode($popup->data, true);
        if (!is_array($config)) {
            $config = [];
        }

        // Force immediate trigger on live site preview
        if (!isset($config['triggers'])) $config['triggers'] = [];
        $config['triggers']['on_load'] = true;
        $config['triggers']['on_load_delay'] = 0;
        ?>
        <!-- Live Preview Administrative Banner -->
        <div id="wppoppop-live-preview-bar" style="position:fixed;top:0;left:0;right:0;height:42px;background:#0f172a;color:#f8fafc;z-index:999999999;display:flex;align-items:center;justify-content:space-between;padding:0 20px;font-family:-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,sans-serif;font-size:13px;box-shadow:0 2px 10px rgba(0,0,0,0.25);">
            <div>
                <strong style="color:#38bdf8;">LIVE SITE PREVIEW:</strong> <span><?php echo esc_html($popup->title); ?></span> <code>(<?php echo esc_html($popup->uid); ?>)</code>
            </div>
            <div style="display:flex;gap:12px;align-items:center;">
                <a href="<?php echo esc_url(admin_url('admin.php?page=wppoppop-builder&uid=' . esc_attr($popup->uid))); ?>" style="color:#ffffff;background:#0284c7;padding:5px 12px;border-radius:4px;text-decoration:none;font-weight:600;font-size:12px;">Edit in Builder &rarr;</a>
                <a href="<?php echo esc_url(admin_url('admin.php?page=wppoppop')); ?>" style="color:#94a3b8;text-decoration:none;font-size:12px;">&larr; Back to Dashboard</a>
                <button type="button" onclick="document.getElementById('wppoppop-live-preview-bar').style.display='none';" style="background:none;border:none;color:#94a3b8;font-size:18px;cursor:pointer;padding:0 5px;">&times;</button>
            </div>
        </div>

        <?php
        $this->output_popup_html($popup->uid, $config);
    }

    public function render_targeted_popups() {
        if (is_admin() || (isset($_GET['wppoppop_preview']) && current_user_can('manage_options'))) {
            return;
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';
        $popups = $wpdb->get_results("SELECT uid, data FROM {$table_name} WHERE status = 'publish'");

        if (empty($popups)) {
            return;
        }

        foreach ($popups as $p) {
            $config = json_decode($p->data, true);
            if (!empty($config)) {
                $this->output_popup_html($p->uid, $config);
            }
        }
    }

    private function output_popup_html($uid, array $config) {
        $meta = isset($config['meta']) ? $config['meta'] : [];
        $width = isset($meta['width']) ? intval($meta['width']) : 640;
        $height = isset($meta['height']) ? intval($meta['height']) : 400;
        $elements = isset($config['elements']) ? (array)$config['elements'] : [];
        ?>
        <div id="wppoppop-popup-<?php echo esc_attr($uid); ?>"
             class="wppoppop-overlay"
             data-uid="<?php echo esc_attr($uid); ?>"
             data-config="<?php echo esc_attr(wp_json_encode($config)); ?>"
             style="display:none;">
            <div class="wppoppop-container" style="width:<?php echo $width; ?>px; min-height:<?php echo $height; ?>px; position:relative; background:#ffffff; border-radius:8px; box-shadow:0 20px 40px rgba(0,0,0,0.25);">
                <button type="button" class="wppoppop-close-btn" aria-label="Close" style="position:absolute;top:12px;right:16px;background:none;border:none;font-size:24px;cursor:pointer;line-height:1;z-index:9999;">&times;</button>
                <div class="wppoppop-elements-wrap" style="position:relative;width:100%;height:<?php echo $height; ?>px;">
                    <?php foreach ($elements as $el) : 
                        $left = isset($el['left']) ? intval($el['left']) : 0;
                        $top = isset($el['top']) ? intval($el['top']) : 0;
                        $w = isset($el['width']) ? intval($el['width']) : 160;
                        $h = isset($el['height']) ? intval($el['height']) : 40;
                    ?>
                        <div class="wppoppop-element-layer" style="position:absolute;left:<?php echo $left; ?>px;top:<?php echo $top; ?>px;width:<?php echo $w; ?>px;height:<?php echo $h; ?>px;">
                            <?php if ($el['type'] === 'text') : ?>
                                <div style="font-size:<?php echo esc_attr($el['font_size'] ?? 16); ?>px;color:<?php echo esc_attr($el['color'] ?? '#222'); ?>;">
                                    <?php echo esc_html($el['content'] ?? ''); ?>
                                </div>
                            <?php elseif ($el['type'] === 'input') : ?>
                                <input type="email" placeholder="<?php echo esc_attr($el['content'] ?? 'Enter email...'); ?>" style="width:100%;height:100%;padding:0 10px;border:1px solid #cbd5e1;border-radius:4px;">
                            <?php elseif ($el['type'] === 'button') : ?>
                                <button type="button" class="wppoppop-submit-trigger" style="width:100%;height:100%;background:<?php echo esc_attr($el['bg_color'] ?? '#0284c7'); ?>;color:#fff;border:none;border-radius:4px;cursor:pointer;font-weight:600;">
                                    <?php echo esc_html($el['content'] ?? 'Submit'); ?>
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php
    }
}
