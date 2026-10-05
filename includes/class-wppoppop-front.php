<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Front {
    private $preview_item = null;

    public function __construct() {
        add_action('init', [$this, 'detect_live_site_preview']);
        add_action('wp_footer', [$this, 'render_popups_in_footer']);
    }

    public function detect_live_site_preview() {
        if (!isset($_GET['wppoppop_preview'])) {
            return;
        }

        if (!current_user_can('manage_options')) {
            return;
        }

        $uid = sanitize_key($_GET['wppoppop_preview']);
        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_name} WHERE uid = %s", $uid));

        if ($row) {
            $this->preview_item = $row;
        }
    }

    public function render_popups_in_footer() {
        // 1. Live Site Preview Mode on Homepage
        if ($this->preview_item) {
            $this->render_live_site_preview_stage($this->preview_item);
            return;
        }

        // 2. Regular Published Popups
        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';
        $active_popups = $wpdb->get_results("SELECT * FROM {$table_name} WHERE status = 'publish'");

        if (empty($active_popups)) {
            return;
        }

        foreach ($active_popups as $p) {
            $config = json_decode($p->data, true);
            if (!$config) continue;
            $this->render_popup_instance($p->uid, $p->title, $config, false);
        }
    }

    private function render_live_site_preview_stage($item) {
        $config = json_decode($item->data, true);
        if (!$config) return;

        // Force triggers to open immediately on the live homepage
        $config['triggers'] = [
            'on_load'       => true,
            'on_load_delay' => 0,
            'on_exit'       => false,
            'on_scroll'     => 0,
            'on_idle'       => 0
        ];
        $config['frequency'] = ['mode' => 'everytime', 'days' => 0];

        // Output Top Floating Admin Preview Banner
        ?>
        <div id="wppoppop-live-preview-banner" style="position:fixed;top:0;left:0;right:0;z-index:99999999;background:#0f172a;color:#f8fafc;padding:10px 20px;font-family:-apple-system,BlinkMacSystemFont,sans-serif;font-size:13px;display:flex;align-items:center;justify-content:space-between;box-shadow:0 2px 10px rgba(0,0,0,0.35);">
            <div style="display:flex;align-items:center;gap:10px;">
                <span style="background:#e11d48;color:#fff;padding:3px 8px;border-radius:4px;font-weight:700;font-size:11px;letter-spacing:0.5px;">LIVE PREVIEW</span>
                <span>Previewing on Live Site: <strong><?php echo esc_html($item->title); ?></strong> (<code><?php echo esc_html($item->uid); ?></code>)</span>
            </div>
            <div style="display:flex;gap:15px;align-items:center;">
                <a href="<?php echo esc_url(admin_url('admin.php?page=wppoppop-builder&uid=' . $item->uid)); ?>" style="color:#38bdf8;text-decoration:none;font-weight:600;">Edit in Builder &rarr;</a>
                <a href="<?php echo esc_url(admin_url('admin.php?page=wppoppop')); ?>" style="color:#94a3b8;text-decoration:none;">Back to Dashboard</a>
                <a href="<?php echo esc_url(home_url('/')); ?>" style="color:#f43f5e;text-decoration:none;font-size:18px;line-height:1;margin-left:8px;" title="Exit Preview">&times;</a>
            </div>
        </div>
        <?php

        $this->render_popup_instance($item->uid, $item->title, $config, true);
    }

    private function render_popup_instance($uid, $title, $config, $is_preview = false) {
        $width  = isset($config['meta']['width']) ? intval($config['meta']['width']) : 640;
        $height = isset($config['meta']['height']) ? intval($config['meta']['height']) : 400;

        $box_radius = isset($config['box_styling']['radius']) ? intval($config['box_styling']['radius']) : 8;
        $bg_type    = isset($config['box_styling']['bg_type']) ? $config['box_styling']['bg_type'] : 'solid';
        $bg_style   = 'background: #ffffff;';

        if ($bg_type === 'solid') {
            $c = isset($config['box_styling']['solid_color']) ? $config['box_styling']['solid_color'] : '#ffffff';
            $bg_style = "background: {$c};";
        } else {
            $c1 = isset($config['box_styling']['grad_c1']) ? $config['box_styling']['grad_c1'] : '#1e293b';
            $c2 = isset($config['box_styling']['grad_c2']) ? $config['box_styling']['grad_c2'] : '#0f172a';
            $angle = isset($config['box_styling']['grad_angle']) ? intval($config['box_styling']['grad_angle']) : 135;
            $bg_style = "background: linear-gradient({$angle}deg, {$c1}, {$c2});";
        }

        $elements = isset($config['elements']) && is_array($config['elements']) ? $config['elements'] : [];
        ?>
        <div id="wppoppop-modal-<?php echo esc_attr($uid); ?>" class="wppoppop-live-modal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,0.7);z-index:999999;align-items:center;justify-content:center;backdrop-filter:blur(4px);">
            <div class="wppoppop-live-box" style="position:relative;width:<?php echo esc_attr($width); ?>px;height:<?php echo esc_attr($height); ?>px;border-radius:<?php echo esc_attr($box_radius); ?>px;<?php echo esc_attr($bg_style); ?>box-shadow:0 15px 40px rgba(0,0,0,0.3);overflow:hidden;">
                <!-- Close Button -->
                <button type="button" class="wppoppop-close-btn" style="position:absolute;top:10px;right:10px;background:none;border:none;font-size:22px;color:#64748b;cursor:pointer;z-index:100;">&times;</button>
                
                <!-- Render Elements -->
                <?php foreach ($elements as $el) : 
                    $el_top    = isset($el['top']) ? intval($el['top']) : 0;
                    $el_left   = isset($el['left']) ? intval($el['left']) : 0;
                    $el_width  = isset($el['width']) ? intval($el['width']) : 150;
                    $el_height = isset($el['height']) ? intval($el['height']) : 35;
                    $el_type   = isset($el['type']) ? $el['type'] : 'text';
                    $el_color  = isset($el['color']) ? $el['color'] : '#222222';
                    $el_bg     = isset($el['bg_color']) ? $el['bg_color'] : 'transparent';
                    $el_radius = isset($el['border_radius']) ? intval($el['border_radius']) : 0;
                    $content   = isset($el['content']) ? $el['content'] : '';
                ?>
                    <div style="position:absolute;top:<?php echo esc_attr($el_top); ?>px;left:<?php echo esc_attr($el_left); ?>px;width:<?php echo esc_attr($el_width); ?>px;height:<?php echo esc_attr($el_height); ?>px;border-radius:<?php echo esc_attr($el_radius); ?>px;box-sizing:border-box;">
                        <?php if ($el_type === 'input') : ?>
                            <input type="email" placeholder="<?php echo esc_attr($content ?: 'Enter email...'); ?>" style="width:100%;height:100%;padding:6px 12px;border:1px solid #cbd5e1;border-radius:<?php echo esc_attr($el_radius); ?>px;">
                        <?php elseif ($el_type === 'button') : ?>
                            <button type="button" style="width:100%;height:100%;background:<?php echo esc_attr($el_bg); ?>;color:#ffffff;border:none;border-radius:<?php echo esc_attr($el_radius); ?>px;font-weight:600;cursor:pointer;"><?php echo esc_html($content ?: 'Submit'); ?></button>
                        <?php else : ?>
                            <div style="color:<?php echo esc_attr($el_color); ?>;font-size:15px;line-height:1.3;"><?php echo esc_html($content ?: $el_type); ?></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <script>
        (function() {
            var modal = document.getElementById('wppoppop-modal-<?php echo esc_js($uid); ?>');
            if (!modal) return;

            function showModal() {
                modal.style.display = 'flex';
            }

            function closeModal() {
                modal.style.display = 'none';
            }

            var closeBtn = modal.querySelector('.wppoppop-close-btn');
            if (closeBtn) closeBtn.onclick = closeModal;

            modal.onclick = function(e) {
                if (e.target === modal) closeModal();
            };

            <?php if ($is_preview || (!empty($config['triggers']['on_load']))) : ?>
                var delay = <?php echo ($is_preview) ? '250' : intval($config['triggers']['on_load_delay'] ?? 0) * 1000; ?>;
                setTimeout(showModal, delay);
            <?php endif; ?>
        })();
        </script>
        <?php
    }
}
