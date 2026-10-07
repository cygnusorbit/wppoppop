<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Front_Renderer {
    protected $preview_popup = null;
    protected $rendered_uids = array();

    public function __construct() {
        add_action('wp', array($this, 'init_preview_mode'));
        add_action('wp_footer', array($this, 'render_footer_popups'), 9999);
        add_shortcode('wppoppop', array($this, 'render_shortcode'));
    }

    public function init_preview_mode() {
        if (!isset($_GET['wppoppop_preview'])) {
            return;
        }

        if (!current_user_can('manage_options')) {
            return;
        }

        $uid = sanitize_key($_GET['wppoppop_preview']);
        if (empty($uid)) {
            return;
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'wppoppop_items';
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_name} WHERE uid = %s", $uid));

        if ($row) {
            $this->preview_popup = $row;
        }
    }

    public function render_footer_popups() {
        if ($this->preview_popup) {
            $this->render_preview_instance($this->preview_popup);
        }
    }

    public function render_shortcode($atts) {
        $a = shortcode_atts(array('uid' => ''), $atts);
        if (empty($a['uid'])) {
            return '';
        }

        global $wpdb;
        $table = $wpdb->prefix . 'wppoppop_items';
        $popup = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE uid = %s", sanitize_key($a['uid'])));
        if (!$popup) {
            return '';
        }

        ob_start();
        $this->render_popup_markup($popup, false);
        return ob_get_clean();
    }

    public function render_preview_instance($popup) {
        $this->render_popup_markup($popup, true);

        $raw_data = !empty($popup->data) ? json_decode($popup->data, true) : array();
        $screens = !empty($raw_data['screens']) && is_array($raw_data['screens']) ? $raw_data['screens'] : array(
            array('id' => 1, 'name' => 'Screen 1', 'width' => 640, 'height' => 400)
        );
        $builder_url = admin_url('admin.php?page=wppoppop-builder&uid=' . urlencode($popup->uid));
        $uid = esc_attr($popup->uid);
        ?>
        <!-- WpPopPop Interactive Live Preview Studio Dock -->
        <div id="wppoppop-live-preview-dock" style="position:fixed;bottom:20px;left:50%;transform:translateX(-50%);z-index:999999999;background:rgba(15,23,42,0.96);backdrop-filter:blur(8px);color:#ffffff;padding:8px 16px;border-radius:40px;box-shadow:0 20px 40px rgba(0,0,0,0.6),0 0 0 1px rgba(59,130,246,0.3);display:flex;align-items:center;gap:12px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;font-size:12px;user-select:none;max-width:95vw;overflow-x:auto;">
            <!-- Indicator & Title -->
            <div style="display:flex;align-items:center;gap:7px;border-right:1px solid rgba(255,255,255,0.15);padding-right:12px;white-space:nowrap;">
                <span style="width:9px;height:9px;border-radius:50%;background:#10b981;box-shadow:0 0 10px #10b981;display:inline-block;"></span>
                <span style="font-weight:700;color:#93c5fd;">Live Preview:</span>
                <span style="max-width:140px;overflow:hidden;text-overflow:ellipsis;color:#f1f5f9;"><?php echo esc_html($popup->title); ?></span>
            </div>

            <!-- Multi-Screen Sequence Switcher -->
            <?php if (count($screens) > 1) : ?>
                <div style="display:flex;align-items:center;gap:4px;border-right:1px solid rgba(255,255,255,0.15);padding-right:12px;">
                    <span style="color:#94a3b8;font-size:11px;margin-right:2px;">Screen:</span>
                    <?php foreach ($screens as $idx => $sc) : ?>
                        <?php $sc_id = isset($sc['id']) ? intval($sc['id']) : ($idx + 1); ?>
                        <button type="button" class="wppoppop-preview-screen-btn" data-screen-id="<?php echo $sc_id; ?>" style="background:<?php echo ($idx === 0) ? '#2563eb' : '#334155'; ?>;color:#ffffff;border:none;border-radius:12px;padding:3px 9px;font-size:11px;font-weight:600;cursor:pointer;transition:all 0.15s ease;">
                            <?php echo esc_html(!empty($sc['name']) ? $sc['name'] : 'Screen ' . $sc_id); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Viewport Switcher (Desktop vs Mobile) -->
            <div style="display:flex;align-items:center;gap:4px;border-right:1px solid rgba(255,255,255,0.15);padding-right:12px;">
                <button type="button" id="wppoppop-preview-viewport-desktop" style="background:#2563eb;color:#ffffff;border:none;border-radius:6px;padding:4px 8px;font-size:11px;cursor:pointer;display:flex;align-items:center;gap:4px;" title="Desktop View">
                    <span>Desktop</span>
                </button>
                <button type="button" id="wppoppop-preview-viewport-mobile" style="background:#334155;color:#94a3b8;border:none;border-radius:6px;padding:4px 8px;font-size:11px;cursor:pointer;display:flex;align-items:center;gap:4px;" title="Mobile View (360px)">
                    <span>Mobile</span>
                </button>
            </div>

            <!-- Action Controls (Replay, Toggle, Edit) -->
            <div style="display:flex;align-items:center;gap:8px;">
                <button type="button" id="wppoppop-preview-replay-btn" style="background:#475569;color:#ffffff;border:none;border-radius:6px;padding:4px 9px;font-size:11px;font-weight:600;cursor:pointer;" title="Replay Entrance Animation">
                    &#x21bb; Replay
                </button>
                <button type="button" id="wppoppop-preview-toggle-btn" style="background:#475569;color:#ffffff;border:none;border-radius:6px;padding:4px 9px;font-size:11px;font-weight:600;cursor:pointer;" title="Show or hide popup">
                    Toggle
                </button>
                <a href="<?php echo esc_url($builder_url); ?>" style="background:#3b82f6;color:#ffffff;padding:5px 12px;border-radius:16px;text-decoration:none;font-weight:700;font-size:11px;display:inline-flex;align-items:center;gap:4px;white-space:nowrap;transition:background 0.15s ease;">
                    &larr; Back to Builder
                </a>
            </div>
        </div>

        <!-- Client-Side Runtime for Preview Controls -->
        <script>
        jQuery(document).ready(function($) {
            var $overlay = $('#wppoppop-modal-<?php echo esc_js($uid); ?>');
            var $dialog = $overlay.find('.wppoppop-modal-dialog');
            var screensData = $overlay.data('screens') || [];
            var currentScreen = 1;
            var isMobile = false;

            function applyScreen(sId, replayAnim) {
                currentScreen = sId;
                $('.wppoppop-screen-pane[data-popup="<?php echo esc_js($uid); ?>"]').hide();
                var $targetPane = $('#wppoppop-screen-<?php echo esc_js($uid); ?>-' + sId);
                if ($targetPane.length) {
                    $targetPane.fadeIn(150);
                }

                // Update Dock Button States
                $('.wppoppop-preview-screen-btn').css('background', '#334155');
                $('.wppoppop-preview-screen-btn[data-screen-id="' + sId + '"]').css('background', '#2563eb');

                // Find Screen Configuration
                var sc = null;
                for (var i = 0; i < screensData.length; i++) {
                    if (parseInt(screensData[i].id, 10) === parseInt(sId, 10)) {
                        sc = screensData[i];
                        break;
                    }
                }

                if (sc) {
                    var w = isMobile ? 360 : (parseInt(sc.width, 10) || 640);
                    var h = parseInt(sc.height, 10) || 400;
                    $dialog.css({ 'width': w + 'px', 'min-height': h + 'px' });

                    // Background Mode
                    if (sc.bgMode === 'gradient') {
                        var c1 = sc.gradColor1 || '#3b82f6';
                        var c2 = sc.gradColor2 || '#1d4ed8';
                        var deg = sc.gradAngle || 135;
                        $dialog.css('background', 'linear-gradient(' + deg + 'deg, ' + c1 + ', ' + c2 + ')');
                    } else if (sc.bgColor) {
                        $dialog.css('background', sc.bgColor);
                    }

                    // Animate.css Entrance
                    if (replayAnim) {
                        var animIn = sc.animIn || 'animate__fadeInDown';
                        var duration = (parseInt(sc.animDuration, 10) || 400) + 'ms';
                        var delay = (parseInt(sc.animDelay, 10) || 0) + 'ms';

                        $dialog.removeClass(function(index, className) {
                            return (className.match(/(^|\s)animate__\S+/g) || []).join(' ');
                        });

                        $dialog.addClass('animate__animated ' + animIn).css({
                            '--animate-duration': duration,
                            'animation-delay': delay
                        });
                    }
                }
            }

            // Screen Switcher Buttons
            $('.wppoppop-preview-screen-btn').on('click', function(e) {
                e.preventDefault();
                var sId = $(this).data('screen-id');
                applyScreen(sId, true);
            });

            // Viewport Switcher
            $('#wppoppop-preview-viewport-desktop').on('click', function() {
                isMobile = false;
                $(this).css({ 'background': '#2563eb', 'color': '#ffffff' });
                $('#wppoppop-preview-viewport-mobile').css({ 'background': '#334155', 'color': '#94a3b8' });
                applyScreen(currentScreen, false);
            });

            $('#wppoppop-preview-viewport-mobile').on('click', function() {
                isMobile = true;
                $(this).css({ 'background': '#2563eb', 'color': '#ffffff' });
                $('#wppoppop-preview-viewport-desktop').css({ 'background': '#334155', 'color': '#94a3b8' });
                applyScreen(currentScreen, false);
            });

            // Replay Animation
            $('#wppoppop-preview-replay-btn').on('click', function() {
                if ($overlay.is(':hidden')) {
                    $overlay.css('display', 'flex').hide().fadeIn(150);
                }
                applyScreen(currentScreen, true);
            });

            // Toggle Visibility
            $('#wppoppop-preview-toggle-btn').on('click', function() {
                $overlay.fadeToggle(150);
            });

            // Internal Popup Step Button Navigation (Screen 1 -> Screen 2)
            $overlay.on('click', '.wppoppop-step-trigger', function(e) {
                e.preventDefault();
                var targetScreen = $(this).data('target-screen');
                if (targetScreen) {
                    applyScreen(targetScreen, true);
                } else {
                    var next = currentScreen + 1;
                    applyScreen(next, true);
                }
            });

            // Launch immediately on page load
            $overlay.css('display', 'flex').hide().fadeIn(200, function() {
                applyScreen(1, true);
            });
        });
        </script>
        <?php
    }

    public function render_popup_markup($popup, $is_preview = false) {
        $uid = esc_attr($popup->uid);
        if (in_array($uid, $this->rendered_uids, true) && !$is_preview) {
            return;
        }
        $this->rendered_uids[] = $uid;

        $raw_data = !empty($popup->data) ? json_decode($popup->data, true) : array();
        $screens = !empty($raw_data['screens']) && is_array($raw_data['screens']) ? $raw_data['screens'] : array(
            array('id' => 1, 'name' => 'Screen 1', 'width' => 640, 'height' => 400)
        );
        $elements = !empty($raw_data['elements']) && is_array($raw_data['elements']) ? $raw_data['elements'] : array();
        $settings = !empty($raw_data['settings']) && is_array($raw_data['settings']) ? $raw_data['settings'] : array();

        $screen1 = $screens[0];
        $width = !empty($screen1['width']) ? intval($screen1['width']) : 640;
        $height = !empty($screen1['height']) ? intval($screen1['height']) : 400;
        $bg_color = !empty($screen1['bgColor']) ? $screen1['bgColor'] : (!empty($settings['bgColor']) ? $settings['bgColor'] : '#ffffff');
        $bg_mode = !empty($screen1['bgMode']) ? $screen1['bgMode'] : (!empty($settings['bgMode']) ? $settings['bgMode'] : 'solid');
        $grad_c1 = !empty($screen1['gradColor1']) ? $screen1['gradColor1'] : '#3b82f6';
        $grad_c2 = !empty($screen1['gradColor2']) ? $screen1['gradColor2'] : '#1d4ed8';
        $grad_deg = !empty($screen1['gradAngle']) ? intval($screen1['gradAngle']) : 135;

        $bg_css = ($bg_mode === 'gradient')
            ? "background: linear-gradient({$grad_deg}deg, {$grad_c1}, {$grad_c2});"
            : "background: {$bg_color};";

        $custom_css = !empty($settings['customCss']) ? $settings['customCss'] : '';
        ?>
        <?php if (!empty($custom_css)) : ?>
            <style><?php echo wp_strip_all_tags($custom_css); ?></style>
        <?php endif; ?>

        <div id="wppoppop-modal-<?php echo $uid; ?>" class="wppoppop-modal-overlay" data-screens="<?php echo esc_attr(wp_json_encode($screens)); ?>" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.65);z-index:9999999;align-items:center;justify-content:center;backdrop-filter:blur(3px);">
            <div class="wppoppop-modal-dialog" style="position:relative;width:<?php echo $width; ?>px;min-height:<?php echo $height; ?>px;<?php echo $bg_css; ?>border-radius:10px;box-shadow:0 25px 50px rgba(0,0,0,0.5);overflow:visible;box-sizing:border-box;transition:width 0.25s ease, min-height 0.25s ease, background 0.25s ease;">
                <!-- Close Button -->
                <button type="button" class="wppoppop-modal-close-x" onclick="jQuery('#wppoppop-modal-<?php echo $uid; ?>').fadeOut(150);" style="position:absolute;top:-12px;right:-12px;background:#ef4444;color:#ffffff;border:none;width:28px;height:28px;border-radius:50%;font-size:16px;cursor:pointer;display:flex;align-items:center;justify-content:center;line-height:1;box-shadow:0 4px 6px rgba(0,0,0,0.3);z-index:1000;" title="Close">&times;</button>

                <!-- Screen Panes -->
                <?php foreach ($screens as $s_idx => $sc) : ?>
                    <?php
                    $sc_id = isset($sc['id']) ? intval($sc['id']) : ($s_idx + 1);
                    $pane_display = ($s_idx === 0) ? 'block' : 'none';
                    $sc_h = !empty($sc['height']) ? intval($sc['height']) : $height;
                    ?>
                    <div id="wppoppop-screen-<?php echo $uid; ?>-<?php echo $sc_id; ?>" class="wppoppop-screen-pane" data-popup="<?php echo $uid; ?>" data-screen-id="<?php echo $sc_id; ?>" style="display:<?php echo $pane_display; ?>;position:relative;width:100%;min-height:<?php echo $sc_h; ?>px;box-sizing:border-box;">
                        <?php foreach ($elements as $el) : ?>
                            <?php
                            $el_screen = isset($el['screen']) ? intval($el['screen']) : 1;
                            if ($el_screen !== $sc_id) {
                                continue;
                            }

                            $top = isset($el['top']) ? intval($el['top']) : 0;
                            $left = isset($el['left']) ? intval($el['left']) : 0;
                            $el_w = isset($el['width']) ? intval($el['width']) : 200;
                            $el_h = isset($el['height']) ? intval($el['height']) : 40;
                            $el_bg = isset($el['bgColor']) ? esc_attr($el['bgColor']) : 'transparent';
                            $el_color = isset($el['color']) ? esc_attr($el['color']) : '#1e293b';
                            $el_fs = isset($el['fontSize']) ? intval($el['fontSize']) : 14;
                            $el_br = isset($el['borderRadius']) ? intval($el['borderRadius']) : 4;
                            $el_txt = !empty($el['content']) ? $el['content'] : (!empty($el['label']) ? $el['label'] : '');
                            $el_type = !empty($el['type']) ? $el['type'] : 'text';
                            $target_sc = !empty($el['targetScreen']) ? intval($el['targetScreen']) : ($sc_id + 1);
                            ?>
                            <div style="position:absolute;top:<?php echo $top; ?>px;left:<?php echo $left; ?>px;width:<?php echo $el_w; ?>px;height:<?php echo $el_h; ?>px;background:<?php echo $el_bg; ?>;color:<?php echo $el_color; ?>;font-size:<?php echo $el_fs; ?>px;border-radius:<?php echo $el_br; ?>px;box-sizing:border-box;">
                                <?php if ($el_type === 'step_btn') : ?>
                                    <button type="button" class="wppoppop-step-trigger" data-target-screen="<?php echo $target_sc; ?>" style="width:100%;height:100%;background:inherit;color:inherit;font-size:inherit;border:none;border-radius:inherit;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;">
                                        <?php echo esc_html($el_txt ?: 'Next Step &rarr;'); ?>
                                    </button>
                                <?php elseif ($el_type === 'submit') : ?>
                                    <button type="button" class="wppoppop-step-trigger" data-target-screen="<?php echo $target_sc; ?>" style="width:100%;height:100%;background:inherit;color:inherit;font-size:inherit;border:none;border-radius:inherit;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;">
                                        <?php echo esc_html($el_txt ?: 'Submit'); ?>
                                    </button>
                                <?php elseif ($el_type === 'email' || $el_type === 'number') : ?>
                                    <input type="<?php echo ($el_type === 'email') ? 'email' : 'number'; ?>" placeholder="<?php echo esc_attr($el_txt ?: 'Enter value...'); ?>" style="width:100%;height:100%;padding:0 12px;border:1px solid #cbd5e1;border-radius:inherit;font-size:inherit;box-sizing:border-box;" />
                                <?php elseif ($el_type === 'html') : ?>
                                    <div style="width:100%;height:100%;overflow:hidden;"><?php echo wp_kses_post($el_txt); ?></div>
                                <?php else : ?>
                                    <div style="width:100%;height:100%;display:flex;align-items:center;padding:0 8px;"><?php echo esc_html($el_txt); ?></div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }
}
