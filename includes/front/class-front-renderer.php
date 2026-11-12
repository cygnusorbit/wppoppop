<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Front_Renderer {

    /* WPPOPPOP_TRANSPARENT_CANVAS: single source of truth for "canvas background is Transparent" */
    public static function is_transparent_bg($bg) {
        $bg = strtolower(trim((string) $bg));
        $bg = str_replace(' ', '', $bg);
        return $bg === 'transparent' || $bg === 'rgba(0,0,0,0)' || $bg === 'rgba(255,255,255,0)' || $bg === '#00000000' || $bg === '#fff0';
    }

    public static function config_has_transparent_canvas(array $config) {
        $meta = $config['canvasMeta'] ?? $config['canvas_meta'] ?? [];
        if (!is_array($meta)) { return false; }
        foreach ($meta as $cm) {
            if (is_array($cm) && isset($cm['bg_color']) && self::is_transparent_bg($cm['bg_color'])) { return true; }
        }
        return false;
    }

    public function render_popup_markup($uid, array $config, $is_inline = false, $preload = false) {
        /* WPPOPPOP_MODULE_RENDER_GUARD: not-installed modules are skipped; a "block" module in use hides the popup */
        if (class_exists('WpPopPop_Modules')) {
            $wppoppop_mg = WpPopPop_Modules::prepare_render($config);
            if (!empty($wppoppop_mg['blocked'])) {
                return current_user_can('manage_options')
                    ? '<!-- wppoppop: popup hidden, module not installed: ' . esc_html(implode(', ', $wppoppop_mg['blocked'])) . ' -->'
                    : '';
            }
            $config = $wppoppop_mg['config'];
            if (class_exists('WpPopPop_Modules_Engine')) { WpPopPop_Modules_Engine::front((string) $uid, $config); }
        }
        $box = $config['box'] ?? ($config['settings']['box'] ?? []);
        $width = intval($box['width'] ?? 300);
        $height = intval($box['height'] ?? 400);
        $bg_mode = $box['bg_mode'] ?? 'solid';
        $bg_color = $box['bg_color'] ?? '#ffffff';
        $grad_c1 = $box['grad_color1'] ?? '#3b82f6';
        $grad_c2 = $box['grad_color2'] ?? '#1d4ed8';
        $grad_ang = intval($box['grad_angle'] ?? 135);

        $box_bg = ($bg_mode === 'gradient')
            ? "background:linear-gradient({$grad_ang}deg, {$grad_c1}, {$grad_c2});"
            : "background:{$bg_color};";

        $canvases = $config['canvases'] ?? $config['screens'] ?? [];
        if (empty($canvases) || !is_array($canvases)) {
            $canvases = [1 => []];
        }

        $canvas_meta = $config['canvasMeta'] ?? $config['canvas_meta'] ?? [];
        /* WPPOPPOP_TRANSPARENT_CANVAS: when every canvas paints its own background the box must not add a white fill / halo behind it */
        $wppoppop_self_paint = is_array($canvas_meta) && !empty($canvas_meta);
        foreach ($canvases as $wppoppop_cn => $wppoppop_unused) {
            if (!isset($canvas_meta[$wppoppop_cn]) || !is_array($canvas_meta[$wppoppop_cn]) || empty($canvas_meta[$wppoppop_cn]['bg_color'])) { $wppoppop_self_paint = false; break; }
        }
        $box_shadow_css = 'box-shadow:0 25px 50px -12px rgba(0,0,0,0.4);';
        $box_shadow_css_solid = $box_shadow_css;
        if ($wppoppop_self_paint) {
            $box_bg = 'background:transparent;';
            $box_shadow_css = 'box-shadow:none;';
        }
        $box_radius_css = 'border-radius:8px;';
        if (is_array($canvas_meta) && !empty($canvas_meta)) {
            $first_cm = isset($canvas_meta[1]) ? $canvas_meta[1] : reset($canvas_meta);
            if (is_array($first_cm) && isset($first_cm['canvas_radius'])) {
                $box_radius_css = 'border-radius:' . max(0, min(500, intval($first_cm['canvas_radius']))) . 'px;';
            }
        }
        $encoded_config = esc_attr(wp_json_encode($config));

        $trg = (isset($config['settings']['triggers']) && is_array($config['settings']['triggers'])) ? $config['settings']['triggers'] : [];
        $trg_on = function ($k) use ($trg) { return !empty($trg[$k]) && $trg[$k] !== 'false' && $trg[$k] !== '0'; };
        $trg_attrs = '';
        if ($trg_on('load'))   { $trg_attrs .= ' data-trigger-load="1" data-trigger-load-delay="' . max(0, intval($trg['load_delay'] ?? 0)) . '"'; }
        if ($trg_on('exit'))   { $trg_attrs .= ' data-trigger-exit="1" data-exit-sens="' . max(1, intval($config['settings']['exit_intent_sensitivity'] ?? 20)) . '"'; }
        if ($trg_on('scroll')) { $trg_attrs .= ' data-trigger-scroll="' . max(1, min(100, intval($trg['scroll_val'] ?? ($config['settings']['scroll_depth'] ?? 50)))) . '"'; }
        if ($trg_on('idle'))   { $trg_attrs .= ' data-trigger-idle="' . max(1, intval($trg['idle_seconds'] ?? ($trg['idle_time'] ?? 15))) . '"'; }

        /* WPPOPPOP_TRIGGER_ALWAYS_REPEAT */
        if ($trg_on('always') || $trg_on('always_embed')) {
            if (!$trg_on('load')) { $trg_attrs .= ' data-trigger-load="1" data-trigger-load-delay="0"'; }
            $trg_attrs .= ' data-trigger-always="1"';
        }
        if ($trg_on('repeat') && intval($trg['repeat_hours'] ?? 0) > 0) {
            $trg_attrs .= ' data-repeat-hours="' . max(1, min(720, intval($trg['repeat_hours']))) . '"';
        }

        /* WPPOPPOP_EXT_WRAPPER_ATTRS: extension modules (Pro) add their data-* attributes in a fixed order */
        $trg_attrs = (string) apply_filters('wppoppop_popup_wrapper_attrs', $trg_attrs,
            (isset($config['settings']) && is_array($config['settings'])) ? $config['settings'] : array(),
            array('uid' => $uid, 'is_inline' => (bool) $is_inline, 'config' => $config));

        /* WPPOPPOP_BG_CLOSE_ATTR: backdrop click closes only when enabled (default off) */
        $bgc_raw = isset($config['settings']['close_on_backdrop']) ? $config['settings']['close_on_backdrop'] : false;
        $trg_attrs .= ' data-close-backdrop="' . (in_array($bgc_raw, array(true, 1, '1', 'true'), true) ? '1' : '0') . '"';

        // Settings Integrations
        $default_font   = function_exists('wppoppop_get_setting') ? wppoppop_get_setting('default_font', 'inherit') : 'inherit';
        $show_watermark = function_exists('wppoppop_get_setting') ? (bool) wppoppop_get_setting('powered_by_badge', false) : false;
        $minify_css     = function_exists('wppoppop_get_setting') ? (bool) wppoppop_get_setting('minify_css', false) : false;

        /* WPPOPPOP_DEFAULT_TYPO: global fallback text style; layer-level styles still override */
        $wppoppop_typo = '';
        if (function_exists('wppoppop_get_setting')) {
            $wppoppop_tw = (string) wppoppop_get_setting('default_font_weight', 'inherit');
            if (in_array($wppoppop_tw, array('300', '400', '500', '600', '700'), true)) { $wppoppop_typo .= ' font-weight: ' . $wppoppop_tw . ';'; }
            $wppoppop_ts = (int) wppoppop_get_setting('default_font_size', 0);
            if ($wppoppop_ts > 0 && $wppoppop_ts <= 200) { $wppoppop_typo .= ' font-size: ' . $wppoppop_ts . 'px;'; }
            $wppoppop_ta = (string) wppoppop_get_setting('default_text_align', 'inherit');
            if (in_array($wppoppop_ta, array('left', 'center', 'right'), true)) { $wppoppop_typo .= ' text-align: ' . $wppoppop_ta . ';'; }
            if (wppoppop_get_setting('default_font_italic', 0)) { $wppoppop_typo .= ' font-style: italic;'; }
            if (wppoppop_get_setting('default_font_underline', 0)) { $wppoppop_typo .= ' text-decoration: underline;'; }
            $wppoppop_tc = sanitize_hex_color((string) wppoppop_get_setting('default_font_color', ''));
            if ($wppoppop_tc) { $wppoppop_typo .= ' color: ' . $wppoppop_tc . ';'; }
        }

        $custom_inline_css = "
        #wppoppop-popup-{$uid}, #wppoppop-wrap-{$uid} {
            font-family: {$default_font};{$wppoppop_typo}
        }
        ";

        // WPPOPPOP_CANVAS_OVERFLOW: let the box show overflow only when a canvas allows it
        $wppoppop_any_overflow = false;
        if (is_array($canvas_meta)) {
            foreach ($canvas_meta as $wppoppop_cm_chk) {
                if (is_array($wppoppop_cm_chk) && !empty($wppoppop_cm_chk['allow_overflow']) && $wppoppop_cm_chk['allow_overflow'] !== 'false') {
                    $wppoppop_any_overflow = true;
                    break;
                }
            }
        }
        if ($wppoppop_any_overflow) {
            $custom_inline_css .= "
        #wppoppop-popup-{$uid} .wppoppop-box { overflow: visible !important; }
        ";
        }

        /* WPPOPPOP_INLINE_CANVAS_ONLY: shortcode embed shows the canvas only (no overlay / backdrop / box chrome) */
        if ($is_inline) {
            $custom_inline_css .= "
        #wppoppop-popup-{$uid}[data-inline] {
            background: transparent !important;
            -webkit-backdrop-filter: none !important;
            backdrop-filter: none !important;
            position: relative !important;
            inset: auto !important;
            width: auto !important;
            height: auto !important;
            display: block !important;
            padding: 0 !important;
        }
        #wppoppop-popup-{$uid}[data-inline] .wppoppop-backdrop { display: none !important; }
        #wppoppop-popup-{$uid}[data-inline] .wppoppop-box {
            background: transparent !important;
            box-shadow: none !important;
            border-radius: 0 !important;
            max-width: 100% !important;
            margin: 0 auto;
        }
        ";
        }

        /* WPPOPPOP_CUSTOM_CODE_RENDER: an active Custom Code module adds the popup's own CSS (scoped to this popup) and queues its JS */
        $custom_inline_css = (string) apply_filters('wppoppop_popup_inline_css', $custom_inline_css, (isset($config['settings']) && is_array($config['settings'])) ? $config['settings'] : array(), $uid);

        if ($minify_css && function_exists('wppoppop_minify_css')) {
            $custom_inline_css = wppoppop_minify_css($custom_inline_css);
        }

        /* WPPOPPOP_OVERLAY_SETTINGS */
        $wppoppop_ov = WpPopPop_Module_Bridge::overlay((isset($config['settings']) && is_array($config['settings'])) ? $config['settings'] : array());
        /* WPPOPPOP_POPUP_POSITION_RENDER */
        $wppoppop_pp = WpPopPop_Module_Bridge::position((isset($config['settings']) && is_array($config['settings'])) ? $config['settings'] : array());
        $overlay_style = $is_inline ? 'display:block;position:relative;' : 'display:none;position:fixed;inset:0;z-index:999999;';
        $preload_class = $preload ? 'wppoppop-preloaded' : '';
        ?>
        <div class="wppoppop-overlay wppoppop-popup-wrap <?php echo esc_attr( $preload_class ); ?>"
             id="wppoppop-popup-<?php echo esc_attr($uid); ?>"
             data-uid="<?php echo esc_attr($uid); ?>"
             data-config="<?php echo esc_attr( $encoded_config ); ?>"<?php echo (!$is_inline && $wppoppop_pp['pos'] !== '') ? ' data-wpp-pos="' . esc_attr($wppoppop_pp['pos']) . '"' : ''; ?><?php echo wp_kses_post( $trg_attrs ); ?><?php echo $is_inline ? ' data-inline="1"' : ''; ?><?php echo ($is_inline && $show_watermark) ? ' data-wpp-wm="1"' : ''; ?>
             style="<?php echo esc_attr($overlay_style); ?><?php echo $is_inline ? 'background:transparent;' : esc_attr($wppoppop_ov['css'] . $wppoppop_pp['css']); ?>"
             aria-hidden="true"
             role="dialog">

            <?php /* WPPOPPOP_LAYER_FONTS_FRONT */ $wppoppop_lf = WpPopPop_Libraries::get_layer_fonts_url($config); if ($wppoppop_lf) : ?>
            <?php WpPopPop_Libraries::print_style('wppoppop-layer-fonts'); ?>
            <?php endif; ?>
            <style><?php echo wp_strip_all_tags( $custom_inline_css ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS text, tags stripped ?></style>

            <?php if (!$is_inline): ?>
            <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo $wppoppop_ov['layers']; ?>
            <div class="wppoppop-backdrop" data-uid="<?php echo esc_attr($uid); ?>"></div>
            <?php endif; ?>

            <div class="wppoppop-fit-stage" data-wpp-fit="1">
            <div class="wppoppop-box wppoppop-box-container"
                 data-uid="<?php echo esc_attr($uid); ?>"
                 style="position:relative;width:auto;max-width:92vw;overflow:visible !important;<?php echo $is_inline ? 'background:transparent;box-shadow:none;border-radius:0;' : esc_attr($box_radius_css . $box_shadow_css . $box_bg); ?>">

                <?php /* WPPOPPOP_DEFAULT_CLOSE: the default close button is now drawn inside each canvas */ ?>

                <!-- Submission Status Overlay -->
                <div class="wppoppop-status-overlay" style="display:none;position:absolute;inset:0;background:rgba(255,255,255,0.95);z-index:999999;align-items:center;justify-content:center;flex-direction:column;padding:20px;text-align:center;border-radius:inherit;">
                    <span class="dashicons dashicons-yes-alt" style="font-size:42px;width:42px;height:42px;color:#10b981;margin-bottom:8px;"></span>
                    <p class="wppoppop-status-message" style="font-size:15px;font-weight:700;color:#1e293b;margin:0;"></p>
                </div>

                <!-- Sequence Canvases -->
                <?php foreach ($canvases as $index => $elements): 
                    $canvas_num = intval($index) ?: 1;
                    $is_active = ($canvas_num === 1);
                    $display_style = $is_active ? 'display:block;' : 'display:none;';

                    $cm = $canvas_meta[$canvas_num] ?? [];
                    $c_width = intval($cm['width'] ?? $width);
                    $c_height = intval($cm['height'] ?? $height);
                    $c_anim_app = esc_attr($cm['anim_appearance'] ?? 'fade');
                    $c_anim_dur = intval($cm['anim_duration'] ?? 1000);
                    $c_anim_del = intval($cm['anim_delay'] ?? 0);
                    $c_anim_dis = esc_attr($cm['anim_disappearance'] ?? 'none');
                    $c_radius_style = (is_array($cm) && isset($cm['canvas_radius']) && $cm['canvas_radius'] !== '') ? 'border-radius:' . max(0, min(500, intval($cm['canvas_radius']))) . 'px;' : '';
                    $c_allow_ovf = !empty($cm['allow_overflow']) && $cm['allow_overflow'] !== 'false';
                    $c_overflow_style = $c_allow_ovf ? 'overflow:visible;' : 'overflow:hidden;';
                    $c_close_on = !(is_array($cm) && isset($cm['close_btn_enabled']) && in_array($cm['close_btn_enabled'], array(false, 0, '0', 'false'), true)); /* WPPOPPOP_DEFAULT_CLOSE */
                    $c_autoclose = (is_array($cm) && isset($cm['autoclose_delay']) && $cm['autoclose_delay'] !== '') ? intval($cm['autoclose_delay']) : ((is_array($cm) && isset($cm['autoclose'])) ? intval($cm['autoclose']) : 0);
                    $c_autoclose = max(0, min(300, $c_autoclose)); /* WPPOPPOP_AUTOCLOSE_BADGE */

                    $c_bg_mode = $cm['bg_mode'] ?? 'solid';
                    $c_bg_color = $cm['bg_color'] ?? '';
                    $c_bg_style = '';
                    if (!empty($c_bg_color)) {
                        if (self::is_transparent_bg($c_bg_color)) {
                            $c_bg_style = 'background:transparent;box-shadow:none;'; /* WPPOPPOP_TRANSPARENT_CANVAS: overrides gradient mode + stage halo */
                        } elseif ($c_bg_mode === 'gradient') {
                            $g1 = esc_attr($cm['grad_color1'] ?? '#3b82f6');
                            $g2 = esc_attr($cm['grad_color2'] ?? '#1d4ed8');
                            $ga = intval($cm['grad_angle'] ?? 135);
                            $c_bg_style = "background:linear-gradient({$ga}deg, {$g1}, {$g2});";
                        } else {
                            $c_bg_style = "background:" . esc_attr($c_bg_color) . ";";
                        }
                        if ($wppoppop_self_paint && !self::is_transparent_bg($c_bg_color)) {
                            $c_bg_style .= $box_shadow_css_solid;
                        }
                    }
                ?>
                    <div class="wppoppop-canvas-container wppoppop-canvas-stage wppoppop-screen-container <?php echo $is_active ? 'wppoppop-canvas-active wppoppop-screen-active active' : ''; ?>"
                         id="wppoppop-canvas-<?php echo esc_attr($uid); ?>-<?php echo esc_attr($canvas_num); ?>"
                         data-canvas="<?php echo esc_attr($canvas_num); ?>"
                         data-screen="<?php echo esc_attr($canvas_num); ?>"
                         data-canvas-index="<?php echo esc_attr($canvas_num); ?>"
                         data-screen-index="<?php echo esc_attr($canvas_num); ?>"
                         data-width="<?php echo esc_attr($c_width); ?>"
                         data-height="<?php echo esc_attr($c_height); ?>"
                         data-anim-appearance="<?php echo esc_attr( $c_anim_app ); ?>"
                         data-anim-duration="<?php echo esc_attr( $c_anim_dur ); ?>"
                         data-anim-delay="<?php echo esc_attr( $c_anim_del ); ?>"
                         data-anim-disappearance="<?php echo esc_attr( $c_anim_dis ); ?>"
                         data-anim-exit="<?php echo (isset($cm['anim_exit_enabled']) && in_array($cm['anim_exit_enabled'], array(true, 1, '1', 'true'), true)) ? '1' : '0'; ?>"
                         data-allow-overflow="<?php echo $c_allow_ovf ? '1' : '0'; ?>"
                         data-autoclose="<?php echo esc_attr($c_autoclose); ?>"
                         style="<?php echo esc_attr($display_style . $c_bg_style . $c_overflow_style . $c_radius_style); ?>width:<?php echo esc_attr($c_width); ?>px;height:<?php echo esc_attr($c_height); ?>px;max-width:100%;flex:0 0 auto;box-sizing:border-box;position:relative;">

                        <?php if (!$is_inline && $c_close_on): ?>
                            <button type="button" class="wppoppop-close-btn wppoppop-default-close" data-uid="<?php echo esc_attr($uid); ?>" aria-label="<?php esc_attr_e('Close', 'wppoppop'); ?>" style="position:absolute;top:10px;right:10px;width:auto;height:auto;display:block;padding:0 4px;background:transparent;border:none;color:#64748b;font-size:22px;cursor:pointer;line-height:1;z-index:9999999;">&times;</button>
                        <?php endif; ?>
                        <?php if (!$is_inline && $c_autoclose > 0): ?>
                            <span class="wppoppop-autoclose-timer" role="timer" aria-hidden="true" style="right:<?php echo $c_close_on ? 40 : 10; ?>px;"><span class="wppoppop-autoclose-num"><?php echo esc_html($c_autoclose); ?></span>s</span>
                        <?php endif; ?>
                        <div class="wppoppop-canvas-content">
                            <?php 
                            if (is_array($elements)) {
                                foreach ($elements as $el) {
                                    if (is_array($el) && empty($el['hidden'])) {
                                        /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- element markup is escaped inside the element renderers */ echo $this->wppoppop_apply_logic($this->wppoppop_apply_fg($this->render_canvas_element($this->wppoppop_fit_shape_to_edges($el, $c_width, $c_height, !$c_allow_ovf)), $el), $el); /* WPPOPPOP_LOGIC_ATTRS */
                                    }
                                }
                            }
                            ?>
                        </div>

                    </div>
                <?php endforeach; ?>

                <?php /* WPPOPPOP_WATERMARK_OUTSIDE: one badge below the canvas, bottom center, 10px away; scales with the fit stage */ ?>
                <?php if ($show_watermark): ?>
                    <div class="wppoppop-watermark-badge">
                        <a href="https://wppoppop.com" target="_blank" rel="noopener noreferrer">
                            <span><?php esc_html_e('Powered by', 'wppoppop'); ?></span> <strong>WpPopPop</strong>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            </div><!-- /.wppoppop-fit-stage -->
        </div>
        <?php
    }

    /* WPPOPPOP_LOGIC_ATTRS: expose layer id + conditional-logic config on the layer wrapper for the frontend runtime */
    private function wppoppop_apply_logic($html, array $el) {
        if (!is_string($html) || $html === '') { return $html; }
        $norm = function ($raw) {
            $s = (string) $raw;
            foreach (array('/^wppoppop-layer-item-/', '/^wppoppop-layer-/', '/^layer-/', '/^wppoppop-el-/', '/^el-/') as $re) {
                $s = preg_replace($re, '', $s);
            }
            return trim($s);
        };
        $attrs = '';
        $id = isset($el['id']) ? $norm($el['id']) : '';
        if ($id !== '') { $attrs .= ' data-layer-id="' . esc_attr($id) . '"'; }
        $on = !empty($el['logicEnabled']) && $el['logicEnabled'] !== 'false' && $el['logicEnabled'] !== '0';
        if ($on && !empty($el['logicRules']) && is_array($el['logicRules'])) {
            $rules = array();
            foreach ($el['logicRules'] as $r) {
                if (!is_array($r) || !isset($r['field']) || $r['field'] === '') { continue; }
                $rules[] = array(
                    'field'    => $norm($r['field']),
                    'operator' => isset($r['operator']) ? sanitize_key($r['operator']) : 'equal',
                    'value'    => isset($r['value']) ? (string) $r['value'] : '',
                );
            }
            if (!empty($rules)) {
                $action = (isset($el['logicAction']) && $el['logicAction'] === 'hide') ? 'hide' : 'show';
                $match  = (isset($el['logicMatch']) && $el['logicMatch'] === 'any') ? 'any' : 'all';
                $attrs .= ' data-logic-enabled="1" data-logic-action="' . $action . '" data-logic-match="' . $match . '" data-logic-rules="' . esc_attr(wp_json_encode($rules)) . '"';
            }
        }
        if ($attrs === '') { return $html; }
        return preg_replace_callback('/<div\b(?=[^>]*\bwppoppop-layer-item\b)([^>]*)>/', function ($m) use ($attrs) {
            return '<div' . $m[1] . $attrs . '>';
        }, $html, 1);
    }

    /* WPPOPPOP_FG_COLOR: expose the layer's foreground colour as a CSS variable on its wrapper */
    private function wppoppop_apply_fg($html, array $el) {
        $c = isset($el['fgColor']) ? trim((string) $el['fgColor']) : '';
        if ($c === '' || !preg_match('/^#[0-9a-fA-F]{3,8}$/', $c)) { return $html; }
        return preg_replace_callback('/<div\b[^>]*class="[^"]*wppoppop-layer-item[^"]*"[^>]*\sstyle="/', function ($m) use ($c) {
            return $m[0] . '--wppoppop-fg:' . $c . ';';
        }, $html, 1);
    }

    private function render_shape_svg($preset, $fill, $stroke, $stroke_width, $rotate) {
        $preset = !empty($preset) ? sanitize_key($preset) : 'circle';
        $fill = !empty($fill) ? esc_attr($fill) : '#3b82f6'; // 'none' allowed for transparent fill
        $stroke = !empty($stroke) ? esc_attr($stroke) : 'transparent';
        $stroke_width = intval($stroke_width);
        $rotate = intval($rotate);

        $rot_css = $rotate ? sprintf('transform:rotate(%ddeg);-webkit-transform:rotate(%ddeg);', $rotate, $rotate) : '';
        $svg_style = sprintf('width:100%%;height:100%%;display:block;overflow:visible;%s', $rot_css);
        $s_attr = ($stroke_width > 0 && $stroke !== 'transparent') 
            ? sprintf('stroke="%s" stroke-width="%d" vector-effect="non-scaling-stroke"', $stroke, $stroke_width) 
            : '';

        /* WPPOPPOP_SHAPE_GEOM_PARITY: same geometry as the builder stage (registry-media.js) */
        $geom_open = array(
            'square'    => '<rect x="0" y="0" width="100" height="100"',
            'rectangle' => '<rect x="0" y="0" width="100" height="100"',
            'rect'      => '<rect x="0" y="0" width="100" height="100"',
            'rounded'   => '<rect x="0" y="0" width="100" height="100" rx="16" ry="16"',
            'rounded_square' => '<rect x="0" y="0" width="100" height="100" rx="16" ry="16"',
            'circle'    => '<circle cx="50" cy="50" r="48"',
            'pill'      => '<rect x="2" y="15" width="96" height="70" rx="35" ry="35"',
        );
        $geom_poly = array(
            'triangle' => '50,4 96,96 4,96',
            'diamond'  => '50,2 98,50 50,98 2,50',
            'star'     => '50,3 63,36 98,38 70,60 79,95 50,75 21,95 30,60 2,38 37,36',
            'pentagon' => '50,3 97,38 79,94 21,94 3,38',
            'hexagon'  => '25,5 75,5 98,50 75,95 25,95 2,50',
            'badge'    => '50,2 62,13 78,10 83,25 97,33 93,49 99,64 86,74 83,90 67,91 56,99 44,91 28,90 25,74 12,64 18,49 14,33 28,25 33,10 48,13',
            'octagon'  => '30,4 70,4 96,30 96,70 70,96 30,96 4,70 4,30',
            'cross'    => '36,4 64,4 64,36 96,36 96,64 64,64 64,96 36,96 36,64 4,64 4,36 36,36',
        );
        $geom_path = array(
            'heart'  => 'M50,88 C50,88 10,60 10,32 C10,16 23,6 37,6 C45,6 50,12 50,18 C50,12 55,6 63,6 C77,6 90,16 90,32 C90,60 50,88 50,88 Z',
            'shield' => 'M50 4 L92 18 L92 54 C92 76 50 96 50 96 C50 96 8 76 8 54 L8 18 Z',
        );
        if (isset($geom_open[$preset])) {
            $path = sprintf('%s fill="%s" %s />', $geom_open[$preset], $fill, $s_attr);
        } elseif (isset($geom_poly[$preset])) {
            $path = sprintf('<polygon points="%s" fill="%s" %s stroke-linejoin="round" />', $geom_poly[$preset], $fill, $s_attr);
        } elseif (isset($geom_path[$preset])) {
            $path = sprintf('<path d="%s" fill="%s" %s stroke-linejoin="round" />', $geom_path[$preset], $fill, $s_attr);
        } else {
            $path = sprintf('%s fill="%s" %s />', $geom_open['circle'], $fill, $s_attr);
        }

        return sprintf('<svg viewBox="0 0 100 100" preserveAspectRatio="none" style="%s">%s</svg>', esc_attr($svg_style), $path);
    }

    /* WPPOPPOP_SHAPE_EDGE_FIT: a shape ending within 2px of a canvas edge is extended to that edge (+1px bleed when the canvas clips) so no sub-pixel/rounded white line shows */
    private function wppoppop_fit_shape_to_edges(array $el, $cw, $ch, $clip) {
        if (($el['type'] ?? '') !== 'shape') { return $el; }
        $t = (float) ($el['top'] ?? $el['y'] ?? 20);
        $l = (float) ($el['left'] ?? $el['x'] ?? 20);
        $w = (float) ($el['width'] ?? $el['w'] ?? 200);
        $h = (float) ($el['height'] ?? $el['h'] ?? 40);
        if ($w < 20 || $h < 20 || $cw <= 0 || $ch <= 0) { return $el; }
        $tol = 2;
        $bleed = $clip ? 1 : 0;
        $r = $l + $w;
        $b = $t + $h;
        $nt = abs($t) <= $tol ? -$bleed : $t;
        $nl = abs($l) <= $tol ? -$bleed : $l;
        $nb = abs($b - $ch) <= $tol ? $ch + $bleed : $b;
        $nr = abs($r - $cw) <= $tol ? $cw + $bleed : $r;
        $el['top'] = (int) round($nt);
        $el['left'] = (int) round($nl);
        $el['height'] = (int) round($nb - $nt);
        $el['width'] = (int) round($nr - $nl);
        return $el;
    }

    private function render_canvas_element(array $el, array $config = array()) {
        /* WPPOPPOP_LOAD_DECOMPOSED: the autoloader cannot find elements/class-front-decomposed-26.php */
        if (!class_exists('WpPopPop_Front_Decomposed_26')) {
            $wppoppop_dec_file = WPPOPPOP_PATH . 'includes/front/elements/class-front-decomposed-26.php';
            if (file_exists($wppoppop_dec_file)) { require_once $wppoppop_dec_file; }
        }
        /* WPPOPPOP_STYLE_TOGGLES */
        if (isset($el['borderEnabled']) && (empty($el['borderEnabled']) || $el['borderEnabled'] === 'false')) {
            $el['borderWidth'] = 0; $el['borderRadius'] = 0; $el['borderStyle'] = 'none';
        }
        if (isset($el['shadowEnabled']) && (empty($el['shadowEnabled']) || $el['shadowEnabled'] === 'false')) {
            unset($el['boxShadow'], $el['shadowX'], $el['shadowY'], $el['shadowBlur'], $el['shadowSpread']);
        }
        $el_id = !empty($el['id']) ? sanitize_html_class($el['id']) : uniqid('el_');
        $custom_class = !empty($el['custom_css_class']) ? esc_attr($el['custom_css_class']) : '';
        $custom_id = !empty($el['custom_css_id']) ? (' id="' . esc_attr($el['custom_css_id']) . '"') : '';
        
        // Build Box Shadow style
        $box_shadow_val = !empty($el['boxShadow']) ? $el['boxShadow'] : '';
        if (empty($box_shadow_val) && (!empty($el['shadowBlur']) || !empty($el['shadowSpread']) || !empty($el['shadowX']) || !empty($el['shadowY']))) {
            $sx = isset($el['shadowX']) ? intval($el['shadowX']) : 0;
            $sy = isset($el['shadowY']) ? intval($el['shadowY']) : 0;
            $sb = isset($el['shadowBlur']) ? intval($el['shadowBlur']) : 0;
            $ss = isset($el['shadowSpread']) ? intval($el['shadowSpread']) : 0;
            $sc = !empty($el['shadowColor']) ? esc_attr($el['shadowColor']) : 'rgba(0,0,0,0.2)';
            $box_shadow_val = "{$sx}px {$sy}px {$sb}px {$ss}px {$sc}";
        }

        // Opacity
        $opacity_style = (isset($el['opacity']) && floatval($el['opacity']) < 1.0) ? ('opacity: ' . floatval($el['opacity']) . ';') : '';

        // Dynamic Button Hover Style Injection
        $hover_style_tag = '';
        $hover_bg = !empty($el['hoverBg']) ? $el['hoverBg'] : (!empty($el['btnHoverBg']) ? $el['btnHoverBg'] : (!empty($el['hoverBgColor']) ? $el['hoverBgColor'] : ''));
        $hover_color = !empty($el['hoverColor']) ? $el['hoverColor'] : (!empty($el['btnHoverColor']) ? $el['btnHoverColor'] : (!empty($el['hoverTextColor']) ? $el['hoverTextColor'] : ''));
        $hover_border = !empty($el['hoverBorder']) ? $el['hoverBorder'] : (!empty($el['btnHoverBorder']) ? $el['btnHoverBorder'] : (!empty($el['hoverBorderColor']) ? $el['hoverBorderColor'] : ''));
        if (!empty($hover_bg) || !empty($hover_color) || !empty($hover_border)) {
            $hover_rules = '';
            if (!empty($hover_bg)) $hover_rules .= 'background-color: ' . esc_attr($hover_bg) . ' !important;';
            if (!empty($hover_color)) $hover_rules .= 'color: ' . esc_attr($hover_color) . ' !important;';
            if (!empty($hover_border)) $hover_rules .= 'border-color: ' . esc_attr($hover_border) . ' !important;';
            $hover_style_tag = '<style>#el-' . $el_id . ':hover, #el-' . $el_id . ' button:hover, #el-' . $el_id . ' a:hover { ' . $hover_rules . ' }</style>';
        }
        $type = $el['type'] ?? 'text';
        /* WPPOPPOP_AC_ATTR */
        $wppoppop_ac_val = !empty($el['autocomplete']) ? preg_replace('/[^a-z0-9 \-]/', '', strtolower((string) $el['autocomplete'])) : '';
        $wppoppop_ac_attr = $wppoppop_ac_val !== '' ? ' autocomplete="' . esc_attr($wppoppop_ac_val) . '"' : '';
        $wrapper_styles = array();
        $wrapper_classes = array();
        // Phase 2: Opacity, Custom Class & Button Hover Properties
        if (isset($el['opacity']) && $el['opacity'] !== '' && $el['opacity'] !== null) {
            $wrapper_styles[] = 'opacity:' . (floatval($el['opacity']) / 100) . ';';
        }
        if (!empty($el['customClass'])) {
            $wrapper_classes[] = sanitize_html_class($el['customClass']);
        } elseif (!empty($el['custom_class'])) {
            $wrapper_classes[] = sanitize_html_class($el['custom_class']);
        }
        if (in_array($type, ['submit', 'link', 'step', 'pay', 'close', 'close_icon'], true)) {
            $hover_bg = !empty($el['hoverBgColor']) ? $el['hoverBgColor'] : (!empty($el['hover_bg_color']) ? $el['hover_bg_color'] : '');
            $hover_color = !empty($el['hoverTextColor']) ? $el['hoverTextColor'] : (!empty($el['hover_text_color']) ? $el['hover_text_color'] : '');
            $hover_border = !empty($el['hoverBorderColor']) ? $el['hoverBorderColor'] : (!empty($el['hover_border_color']) ? $el['hover_border_color'] : '');
            if (!empty($hover_bg)) {
                $wrapper_styles[] = '--wppoppop-hover-bg:' . esc_attr($hover_bg) . ';';
            }
            if (!empty($hover_color)) {
                $wrapper_styles[] = '--wppoppop-hover-color:' . esc_attr($hover_color) . ';';
            }
            if (!empty($hover_border)) {
                $wrapper_styles[] = '--wppoppop-hover-border:' . esc_attr($hover_border) . ';';
            }
        }

        $type   = $el['type'] ?? 'text';
        $top    = intval($el['top'] ?? $el['y'] ?? 20);
        $left   = intval($el['left'] ?? $el['x'] ?? 20);
        $width  = intval($el['width'] ?? $el['w'] ?? 200);
        $height = intval($el['height'] ?? $el['h'] ?? 40);
        /* WPPOPPOP_BTN_GEOM_GUARD: empty/zero size falls back to the alias, then the builder default */
        if ($type === 'submit') {
            if ($width < 24)  { $width  = intval($el['w'] ?? 0); }
            if ($height < 16) { $height = intval($el['h'] ?? 0); }
            if ($width < 24)  { $width  = 200; }
            if ($height < 16) { $height = 46; }
        }
        $zIndex = intval($el['zIndex'] ?? 10);
        $bRad   = intval($el['borderRadius'] ?? 0);
        $opac   = floatval($el['opacity'] ?? 1); if ($opac > 1) { $opac = $opac / 100; } /* WPPOPPOP_OPACITY_PCT */
        $content = $el['content'] ?? '';

        $style = [
            'position:absolute',
            "top:{$top}px",
            "left:{$left}px",
            "width:{$width}px",
            "height:{$height}px",
            "z-index:{$zIndex}",
            "border-radius:{$bRad}px",
            "opacity:{$opac}",
            'box-sizing:border-box'
        ];

        if (!empty($el['fontFamily']) && $el['fontFamily'] !== 'inherit') $style[] = 'font-family:' . esc_attr($el['fontFamily']);
        if (!empty($el['fontSize'])) $style[] = 'font-size:' . intval($el['fontSize']) . 'px';
        if (!empty($el['fontWeight']) && preg_match('/^[0-9a-z]{3,10}$/i', (string) $el['fontWeight'])) $style[] = 'font-weight:' . $el['fontWeight']; /* WPPOPPOP_FRONT_FONT_WEIGHT: form inputs inherit this */
        if (!empty($el['color'])) $style[] = 'color:' . esc_attr($el['color']);
        if (!empty($el['bgColor']) && $type !== 'shape' && (empty($el['transparentFill']) || $el['transparentFill'] === 'false')) $style[] = 'background-color:' . esc_attr($el['bgColor']);

                $pad_top    = intval($el['paddingTop'] ?? ($el['padding_top'] ?? ($el['padding'] ?? 0)));
        $pad_right  = intval($el['paddingRight'] ?? ($el['padding_right'] ?? ($el['padding'] ?? 0)));
        $pad_bottom = intval($el['paddingBottom'] ?? ($el['padding_bottom'] ?? ($el['padding'] ?? 0)));
        $pad_left   = intval($el['paddingLeft'] ?? ($el['padding_left'] ?? ($el['padding'] ?? 0)));
        if ($pad_top > 0 || $pad_right > 0 || $pad_bottom > 0 || $pad_left > 0) {
            $style[] = "padding:{$pad_top}px {$pad_right}px {$pad_bottom}px {$pad_left}px";
        }

        /* WPPOPPOP_LABEL_STYLE_FRONT: border + shadow for text-like layers */
        if (in_array($type, array('label', 'text', 'title', 'paragraph', 'html'), true)) {
            $lb_bw = isset($el['borderWidth']) ? intval($el['borderWidth']) : 0;
            if ($lb_bw > 0) {
                $lb_bs = (!empty($el['borderStyle']) && in_array($el['borderStyle'], array('solid', 'dashed', 'dotted', 'double'), true)) ? $el['borderStyle'] : 'solid';
                $lb_bc = (!empty($el['borderColor']) && preg_match('/^#[0-9a-fA-F]{3,8}$/', (string) $el['borderColor'])) ? $el['borderColor'] : '#334155';
                $style[] = 'border:' . $lb_bw . 'px ' . $lb_bs . ' ' . $lb_bc;
            }
            if (!empty($box_shadow_val)) { $style[] = 'box-shadow:' . $box_shadow_val; }
        }

        $shape_rot = 0;
        foreach (array('rotate', 'shapeRotate', 'rotation') as $rk) {
            if (!empty($el[$rk])) { $shape_rot = intval($el[$rk]); break; }
        }
        if ($shape_rot !== 0) {
            $style[] = "transform:rotate({$shape_rot}deg)";
        }

        /* WPPOPPOP_FORM_BORDER_FIX: wrapper owns border + radius; inner box radius = outer radius - border width */
        if (in_array($type, array('text', 'textfield', 'email', 'number', 'phone', 'tel', 'textarea', 'select', 'dropdown', 'date', 'time', 'password', 'upload', 'file', 'multiselect'), true)) {
            $fb_bw = isset($el['borderWidth']) ? max(0, intval($el['borderWidth'])) : 0;
            if ($fb_bw > 0) {
                if ($type !== 'text') {
                    $fb_bs = (!empty($el['borderStyle']) && in_array($el['borderStyle'], array('solid', 'dashed', 'dotted', 'double'), true)) ? $el['borderStyle'] : 'solid';
                    $fb_bc = (!empty($el['borderColor']) && preg_match('/^#[0-9a-fA-F]{3,8}$/', (string) $el['borderColor'])) ? $el['borderColor'] : '#334155';
                    $style[] = 'border:' . $fb_bw . 'px ' . $fb_bs . ' ' . $fb_bc;
                }
                $style[] = '--wppoppop-wb:' . $fb_bw . 'px';
            }
            $style[] = '--wppoppop-ibr:' . max(0, $bRad - $fb_bw) . 'px';
        }

        $style_attr = esc_attr(implode(';', $style));
                $anim_name = '';
        $fx_map = array('fade' => 'fadeIn', 'slideDown' => 'slideInDown', 'slideUp' => 'slideInUp', 'slideLeft' => 'slideInRight', 'slideRight' => 'slideInLeft', 'zoom' => 'zoomIn', 'flip' => 'flipInX');
        foreach (array('animIn', 'anim_appearance', 'animation', 'animEffect', 'anim_effect') as $anim_key) {
            if (!empty($el[$anim_key]) && $el[$anim_key] !== 'none') {
                $raw_fx = preg_replace('/^(animate__|wppoppop-anim-|anim-)/', '', (string) $el[$anim_key]);
                $raw_fx = sanitize_html_class($raw_fx);
                $anim_name = isset($fx_map[$raw_fx]) ? $fx_map[$raw_fx] : $raw_fx;
                break;
            }
        }
        $anim_class = ($anim_name !== '') ? 'wppoppop-fx-run wppoppop-fx-' . $anim_name : '';
        $anim_style = '';
        if ($anim_class !== '') {
            $a_dur = 1000;
            foreach (array('animDuration', 'anim_duration', 'duration') as $dk) {
                if (isset($el[$dk]) && $el[$dk] !== '' && is_numeric($el[$dk])) { $a_dur = max(0, intval($el[$dk])); break; }
            }
            $a_del = 0;
            foreach (array('animDelay', 'anim_delay', 'delay') as $dk) {
                if (isset($el[$dk]) && $el[$dk] !== '' && is_numeric($el[$dk])) { $a_del = max(0, intval($el[$dk])); break; }
            }
            if ($a_dur < 1) { $a_dur = 1; }
            $anim_style .= ';-webkit-animation-duration:' . $a_dur . 'ms;animation-duration:' . $a_dur . 'ms';
            $anim_style .= ';-webkit-animation-delay:' . $a_del . 'ms;animation-delay:' . $a_del . 'ms';
        }
        $style_attr = esc_attr(implode(';', $style) . $anim_style);

        switch ($type) {
            case 'divider':
                $d_style = !empty($el['dividerStyle']) ? sanitize_text_field($el['dividerStyle']) : 'solid';
                $d_weight = !empty($el['dividerWeight']) ? intval($el['dividerWeight']) : 2;
                if ($d_style === 'double' && $d_weight < 3) $d_weight = 4;
                $d_color = !empty($el['dividerColor']) ? sanitize_text_field($el['dividerColor']) : '#94a3b8';
                $d_pct = !empty($el['dividerWidthPct']) ? intval($el['dividerWidthPct']) : 100;
                $d_align = !empty($el['dividerAlign']) ? sanitize_text_field($el['dividerAlign']) : 'center';
                $margin = 'margin:0 auto;';
                if ($d_align === 'left') $margin = 'margin:0 auto 0 0;';
                if ($d_align === 'right') $margin = 'margin:0 0 0 auto;';
                $inner_html = '<div style="width:100%;height:100%;display:flex;align-items:center;box-sizing:border-box;"><hr style="width:' . $d_pct . '%;border:none;border-top:' . $d_weight . 'px ' . esc_attr($d_style) . ' ' . esc_attr($d_color) . ';' . $margin . '" /></div>';
                break;

            case 'icon':
            case 'fontawesome':
                /* WPPOPPOP_ICON_CASE_V2: normalise class, load icon CSS, size the glyph */
                $i_raw = !empty($el['iconClass']) ? $el['iconClass'] : (!empty($el['iconGlyph']) ? $el['iconGlyph'] : (!empty($el['content']) ? $el['content'] : 'dashicons-star-filled'));
                $i_tokens = array_values(array_filter(array_map('sanitize_html_class', preg_split('/\s+/', trim((string) $i_raw)))));
                if (empty($i_tokens)) { $i_tokens = array('dashicons-star-filled'); }
                $i_is_dash = false; $i_is_fa = false; $i_has_base = false;
                foreach ($i_tokens as $i_t) {
                    if (strpos($i_t, 'dashicons-') === 0 || $i_t === 'dashicons') { $i_is_dash = true; }
                    if (preg_match('/^(fa|fas|far|fab|fal|fad|fa-solid|fa-regular|fa-brands)$/', $i_t) || strpos($i_t, 'fa-') === 0) { $i_is_fa = true; }
                    if (in_array($i_t, array('dashicons','fa','fas','far','fab','fal','fad','fa-solid','fa-regular','fa-brands'), true)) { $i_has_base = true; }
                }
                if ($i_is_dash && !in_array('dashicons', $i_tokens, true)) { array_unshift($i_tokens, 'dashicons'); }
                elseif ($i_is_fa && !$i_has_base) { array_unshift($i_tokens, 'fas'); }
                /* WPPOPPOP_ICON_CSS_INLINE: footer enqueue is too late, print the link next to the icon once */
                static $wppoppop_dash_css_done = false, $wppoppop_fa_css_done = false;
                $i_css_links = '';
                if ($i_is_dash && !$wppoppop_dash_css_done) {
                    $wppoppop_dash_css_done = true;
                    $i_css_links .= WpPopPop_Libraries::style_tag('dashicons');
                }
                if ($i_is_fa && !$wppoppop_fa_css_done) {
                    $wppoppop_fa_css_done = true;
                    $i_css_links .= WpPopPop_Libraries::style_tag('wppoppop-font-awesome');
                }
                $i_color = !empty($el['iconColor']) ? sanitize_text_field($el['iconColor']) : (!empty($el['color']) ? sanitize_text_field($el['color']) : '#38bdf8');
                $i_size = !empty($el['iconSize']) ? intval($el['iconSize']) : 36;
                $i_rot = !empty($el['rotate']) ? intval($el['rotate']) : 0;
                $i_rot_css = $i_rot ? 'transform:rotate(' . $i_rot . 'deg);' : '';
                $i_box = $i_is_dash ? ('width:' . $i_size . 'px;height:' . $i_size . 'px;') : '';
                $inner_html = $i_css_links . '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:' . esc_attr($i_color) . ';pointer-events:none;">'
                    . '<i class="' . esc_attr(implode(' ', $i_tokens)) . '" aria-hidden="true" style="font-size:' . $i_size . 'px;' . $i_box . 'line-height:1;display:inline-block;' . $i_rot_css . '"></i></div>';
                break;

            case 'label':
            case 'text':
                
        $decomposed_types = array('label', 'button', 'text', 'phone', 'tel', 'textarea', 'multiselect', 'time', 'upload', 'file', 'password');$raw_el_type = isset($el['type']) ? strtolower(trim($el['type'])) : '';
        if (in_array($raw_el_type, $decomposed_types, true) && class_exists('WpPopPop_Front_Decomposed_26')) {$decomposed_inner = WpPopPop_Front_Decomposed_26::render_element($raw_el_type,$el);
            if ($decomposed_inner !== null) {
                $inner_html =$decomposed_inner;
            }
        }

        if (isset($inner_html) && $inner_html !== '') {
            /* WPPOPPOP_TF_NOPAD_FRONT: Text Field wrapper never carries padding (input fills the layer box) */
            $tf_wrap_pad = in_array($raw_el_type, array('text', 'phone', 'tel', 'textarea', 'multiselect', 'time'), true) ? ';padding:0!important' : ''; /* WPPOPPOP_MULTISELECT_WRAP */
            $tf_wrap_cls = in_array($raw_el_type, array('text', 'phone', 'tel', 'textarea'), true) ? ' wppoppop-el-textfield' : '';
            return '<div class="wppoppop-layer-item wppoppop-element wppoppop-el-text' . $tf_wrap_cls . ' ' . $anim_class . '" style="' . $style_attr . $tf_wrap_pad . '">' . $inner_html . '</div>';
        }

        return '<div class="wppoppop-layer-item wppoppop-element wppoppop-el-text ' . $anim_class . '" style="' . $style_attr . '"><span class="wppoppop-text-render">' . wp_kses_post($content) . '</span></div>';

            case 'video':
                if (!class_exists('WpPopPop_Front_Element_Media')) {
                    require_once WPPOPPOP_PATH . 'includes/front/elements/class-front-element-media.php';
                }
                $wppoppop_v_inner = WpPopPop_Front_Element_Media::render_video($el);
                return '<div class="wppoppop-layer-item wppoppop-element wppoppop-el-video ' . $anim_class . '" style="' . $style_attr . ';overflow:hidden;">' . $wppoppop_v_inner . '</div>';
            case 'image':
                if (!class_exists('WpPopPop_Front_Element_Media')) {
                    require_once WPPOPPOP_PATH . 'includes/front/elements/class-front-element-media.php';
                }
                $wppoppop_i_inner = WpPopPop_Front_Element_Media::render_image($el);
                if ($wppoppop_i_inner !== '') { $inner_html = $wppoppop_i_inner; break; }
                $img_src = esc_url($el['imgUrl'] ?? ($el['imageUrl'] ?? ($el['src'] ?? ($el['content'] ?? ''))));
                $img_fit_raw = $el['objectFit'] ?? ($el['imageFit'] ?? 'contain');
                $img_fit = esc_attr(in_array($img_fit_raw, array('cover', 'contain', 'fill', 'none', 'scale-down'), true) ? $img_fit_raw : 'contain');
                $img_alt = esc_attr($el['altText'] ?? ($el['alt'] ?? ($el['imageAlt'] ?? '')));
                $inner_html = "<img src=\"{$img_src}\" alt=\"{$img_alt}\" style=\"width:100%;height:100%;object-fit:{$img_fit};display:block;border-radius:inherit;\" />";
                break;
            case 'shape':
                $preset = !empty($el['shapePreset']) ? $el['shapePreset'] : (!empty($el['content']) ? $el['content'] : 'square');
                $fill = !empty($el['shapeNoFill']) ? 'none' : esc_attr(!empty($el['shapeFill']) ? $el['shapeFill'] : '#3b82f6');
                $stroke = esc_attr(!empty($el['strokeColor']) ? $el['strokeColor'] : (!empty($el['shapeStroke']) ? $el['shapeStroke'] : 'transparent'));
                $stroke_width = intval($el['strokeWidth'] ?? ($el['shapeStrokeWidth'] ?? 0));
                $rotate = !empty($el['rotate']) ? intval($el['rotate']) : (!empty($el['shapeRotate']) ? intval($el['shapeRotate']) : intval($el['rotation'] ?? 0));
                $svg = $this->render_shape_svg($preset, $fill, $stroke, $stroke_width, $rotate);
                $shape_style = [
                    'position:absolute',
                    "top:{$top}px",
                    "left:{$left}px",
                    "width:{$width}px",
                    "height:{$height}px",
                    "z-index:{$zIndex}",
                    "opacity:{$opac}",
                    'box-sizing:border-box'
                ];
                if (!empty($el['bgColor']) && empty($el['transparentFill'])) { $shape_style[] = 'background-color:' . sanitize_text_field($el['bgColor']); }
                $shape_style[] = 'border-radius:' . $bRad . 'px';
                $shape_style_attr = esc_attr(implode(';', $shape_style) . $anim_style);
                return '<div class="wppoppop-layer-item wppoppop-element wppoppop-el-shape ' . $anim_class . '" style="' . $shape_style_attr . '">' . $svg . '</div>';

            case 'email':
                /* WPPOPPOP_EMAIL_DECOMPOSED */
                if (!class_exists('WpPopPop_Front_Decomposed_26')) {
                    $wppoppop_em_file = WPPOPPOP_PATH . 'includes/front/elements/class-front-decomposed-26.php';
                    if (file_exists($wppoppop_em_file)) { require_once $wppoppop_em_file; }
                }
                if (class_exists('WpPopPop_Front_Decomposed_26')) {
                    $wppoppop_em_inner = WpPopPop_Front_Decomposed_26::render_element('email', $el);
                    if ($wppoppop_em_inner !== null && $wppoppop_em_inner !== '') {
                        return '<div class="wppoppop-layer-item wppoppop-element wppoppop-el-email wppoppop-el-textfield ' . $anim_class . '" style="' . $style_attr . ';padding:0!important">' . $wppoppop_em_inner . '</div>';
                    }
                }
                $ph = !empty($content) ? esc_attr($content) : esc_attr__('Enter your email...', 'wppoppop');
                return '<div class="wppoppop-layer-item wppoppop-element wppoppop-el-email ' . $anim_class . '" style="' . $style_attr . '"><input type="email" name="email"' . $wppoppop_ac_attr . ' class="wppoppop-field-email" placeholder="' . $ph . '" required style="width:100%;height:100%;padding:0 10px;border:1px solid #cbd5e1;border-radius:inherit;box-sizing:border-box;"></div>';

            case 'number':
                /* WPPOPPOP_NUMBER_DECOMPOSED */
                if (!class_exists('WpPopPop_Front_Decomposed_26')) {
                    $wppoppop_nm_file = WPPOPPOP_PATH . 'includes/front/elements/class-front-decomposed-26.php';
                    if (file_exists($wppoppop_nm_file)) { require_once $wppoppop_nm_file; }
                }
                if (class_exists('WpPopPop_Front_Decomposed_26')) {
                    $wppoppop_nm_inner = WpPopPop_Front_Decomposed_26::render_element('number', $el);
                    if ($wppoppop_nm_inner !== null && $wppoppop_nm_inner !== '') {
                        return '<div class="wppoppop-layer-item wppoppop-element wppoppop-el-number wppoppop-el-textfield ' . $anim_class . '" style="' . $style_attr . ';padding:0!important">' . $wppoppop_nm_inner . '</div>';
                    }
                }
                $ph = !empty($content) ? esc_attr($content) : esc_attr__('Enter number...', 'wppoppop');
                return '<div class="wppoppop-layer-item wppoppop-element ' . $anim_class . '" style="' . $style_attr . '"><input type="number" name="number_field"' . $wppoppop_ac_attr . ' placeholder="' . $ph . '" style="width:100%;height:100%;padding:0 10px;border:1px solid #cbd5e1;border-radius:inherit;box-sizing:border-box;"></div>';

            case 'select':
            case 'dropdown':
                /* WPPOPPOP_SELECT_DECOMPOSED */
                if (!class_exists('WpPopPop_Front_Decomposed_26')) {
                    $wppoppop_sl_file = WPPOPPOP_PATH . 'includes/front/elements/class-front-decomposed-26.php';
                    if (file_exists($wppoppop_sl_file)) { require_once $wppoppop_sl_file; }
                }
                if (class_exists('WpPopPop_Front_Decomposed_26')) {
                    $wppoppop_sl_inner = WpPopPop_Front_Decomposed_26::render_element('select', $el);
                    if ($wppoppop_sl_inner !== null && $wppoppop_sl_inner !== '') {
                        return '<div class="wppoppop-layer-item wppoppop-element wppoppop-el-select wppoppop-el-textfield ' . $anim_class . '" style="' . $style_attr . ';padding:0!important">' . $wppoppop_sl_inner . '</div>';
                    }
                }
                $opts = array_map('trim', explode(',', $content ?: 'Option 1, Option 2'));
                $html = '<div class="wppoppop-layer-item wppoppop-element ' . $anim_class . '" style="' . $style_attr . '"><select name="dropdown_field" style="width:100%;height:100%;padding:0 8px;border:1px solid #cbd5e1;border-radius:inherit;box-sizing:border-box;">';
                foreach ($opts as $o) {
                    $html .= '<option value="' . esc_attr($o) . '">' . esc_html($o) . '</option>';
                }
                $html .= '</select></div>';
                return $html;

            case 'radio':
            case 'radios':
                /* WPPOPPOP_RADIO_DECOMPOSED */
                if (!class_exists('WpPopPop_Front_Decomposed_26')) {
                    $wppoppop_rd_file = WPPOPPOP_PATH . 'includes/front/elements/class-front-decomposed-26.php';
                    if (file_exists($wppoppop_rd_file)) { require_once $wppoppop_rd_file; }
                }
                if (class_exists('WpPopPop_Front_Decomposed_26')) {
                    $wppoppop_rd_inner = WpPopPop_Front_Decomposed_26::render_element('radio', $el);
                    if ($wppoppop_rd_inner !== null && $wppoppop_rd_inner !== '') {
                        return '<div class="wppoppop-layer-item wppoppop-element wppoppop-el-radio ' . $anim_class . '" style="' . $style_attr . ';padding:0!important">' . $wppoppop_rd_inner . '</div>';
                    }
                }
                $items = array_map('trim', explode(',', $content ?: 'Choice A, Choice B'));
                $html = '<div class="wppoppop-layer-item wppoppop-element ' . $anim_class . '" style="' . $style_attr . ';display:flex;align-items:center;gap:12px;font-size:12px;">';
                foreach ($items as $idx => $it) {
                    $html .= '<label style="cursor:pointer;"><input type="radio" name="radio_choice" value="' . esc_attr($it) . '" ' . ($idx === 0 ? 'checked' : '') . '> ' . esc_html($it) . '</label>';
                }
                $html .= '</div>';
                return $html;

            case 'checkboxgroup':
                /* WPPOPPOP_CBGROUP_DECOMPOSED */
                if (!class_exists('WpPopPop_Front_Decomposed_26')) {
                    $wppoppop_cg_file = WPPOPPOP_PATH . 'includes/front/elements/class-front-decomposed-26.php';
                    if (file_exists($wppoppop_cg_file)) { require_once $wppoppop_cg_file; }
                }
                $wppoppop_cg_inner = class_exists('WpPopPop_Front_Decomposed_26') ? WpPopPop_Front_Decomposed_26::render_element('checkboxgroup', $el) : '';
                return '<div class="wppoppop-layer-item wppoppop-element wppoppop-el-checkboxgroup ' . $anim_class . '" style="' . $style_attr . ';padding:0!important">' . (string) $wppoppop_cg_inner . '</div>';

            case 'checkbox':
            case 'checkboxes':
                /* WPPOPPOP_CHECKBOX_DECOMPOSED */
                if (!class_exists('WpPopPop_Front_Decomposed_26')) {
                    $wppoppop_cb_file = WPPOPPOP_PATH . 'includes/front/elements/class-front-decomposed-26.php';
                    if (file_exists($wppoppop_cb_file)) { require_once $wppoppop_cb_file; }
                }
                if (class_exists('WpPopPop_Front_Decomposed_26')) {
                    $wppoppop_cb_inner = WpPopPop_Front_Decomposed_26::render_element('checkbox', $el);
                    if ($wppoppop_cb_inner !== null && $wppoppop_cb_inner !== '') {
                        return '<div class="wppoppop-layer-item wppoppop-element wppoppop-el-checkbox ' . $anim_class . '" style="' . $style_attr . ';padding:0!important' . (!empty($box_shadow_val) ? ';box-shadow:' . esc_attr($box_shadow_val) : '') . '">' . $wppoppop_cb_inner . '</div>';
                    }
                }
                $label = !empty($content) ? esc_html($content) : esc_html__('I agree to the terms', 'wppoppop');
                return '<div class="wppoppop-layer-item wppoppop-element ' . $anim_class . '" style="' . $style_attr . ';display:flex;align-items:center;gap:6px;font-size:12px;"><label style="cursor:pointer;"><input type="checkbox" name="agreement" value="1" checked> <span>' . $label . '</span></label></div>';

            case 'date':
                /* WPPOPPOP_DATE_FRONT_HOOK */
                if (class_exists('WpPopPop_Front_Decomposed_26')) {
                    $wppoppop_dt_inner = WpPopPop_Front_Decomposed_26::render_element('date', $el);
                    if ($wppoppop_dt_inner !== null && $wppoppop_dt_inner !== '') {
                        return '<div class="wppoppop-layer-item wppoppop-element wppoppop-el-date ' . $anim_class . '" style="' . $style_attr . ';padding:0!important">' . $wppoppop_dt_inner . '</div>';
                    }
                }
                return '<div class="wppoppop-layer-item wppoppop-element ' . $anim_class . '" style="' . $style_attr . '"><input type="text" name="date_picker" class="wppoppop-datepicker-input wppoppop-datepicker wppoppop-front-date" style="width:100%;height:100%;padding:0 8px;border:1px solid #cbd5e1;border-radius:inherit;box-sizing:border-box;" placeholder="' . esc_attr__('Select date...', 'wppoppop') . '"></div>'; /* WPPOPPOP_DATE_FIX: class parity with decomposed renderer */

            case 'slider':
                return '<div class="wppoppop-layer-item wppoppop-element ' . $anim_class . '" style="' . $style_attr . ';display:flex;align-items:center;padding:0 8px;"><input type="range" name="range_slider" min="1" max="100" style="width:100%;"></div>';

            case 'signature':
                return '<div class="wppoppop-layer-item wppoppop-element ' . $anim_class . '" style="' . $style_attr . ';position:relative;"><canvas class="wppoppop-sig-canvas wppoppop-signature-canvas" width="' . $width . '" height="' . $height . '" style="width:100%;height:100%;border:1px dashed #94a3b8;border-radius:inherit;"></canvas><input type="hidden" name="signature" class="wppoppop-sig-input"><button type="button" class="wppoppop-sig-clear-btn" style="position:absolute;bottom:2px;right:2px;font-size:9px;background:#e2e8f0;border:none;border-radius:2px;cursor:pointer;padding:1px 4px;">Clear</button></div>';

            case 'wheel':
                return '<div class="wppoppop-layer-item wppoppop-element wppoppop-wheel-wrapper ' . $anim_class . '" style="' . $style_attr . ';text-align:center;"><canvas class="wppoppop-wheel-canvas" width="180" height="180" style="width:180px;height:180px;border-radius:50%;margin-bottom:6px;"></canvas><input type="hidden" name="prize"><br><button type="button" class="wppoppop-wheel-spin-btn button" style="background:#4338ca;color:#fff;border:none;padding:4px 10px;border-radius:4px;font-weight:700;cursor:pointer;font-size:11px;">SPIN PRIZE WHEEL</button></div>';

            case 'scratch':
                return '<div class="wppoppop-layer-item wppoppop-element wppoppop-scratch-wrapper ' . $anim_class . '" style="' . $style_attr . ';position:relative;"><div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:#fef08a;color:#854d0e;font-weight:700;font-size:13px;border-radius:inherit;">' . esc_html($content ?: 'YOU WON 25% OFF!') . '</div><canvas class="wppoppop-scratch-canvas" width="' . $width . '" height="' . $height . '" style="position:absolute;inset:0;width:100%;height:100%;cursor:crosshair;border-radius:inherit;"></canvas></div>';

            case 'countdown':
                return '<div class="wppoppop-layer-item wppoppop-element wppoppop-countdown-timer ' . $anim_class . '" data-seconds="900" style="' . $style_attr . ';display:flex;align-items:center;justify-content:center;background:#1e293b;color:#f8fafc;font-family:monospace;font-weight:700;font-size:16px;"><span class="cd-mins">15</span>&nbsp;:&nbsp;<span class="cd-secs">00</span></div>';

            case 'file':
                return '<div class="wppoppop-layer-item wppoppop-element ' . $anim_class . '" style="' . $style_attr . ';display:flex;align-items:center;justify-content:center;border:1px dashed #cbd5e1;"><input type="file" name="uploaded_file" style="font-size:11px;width:95%;"></div>';

            case 'step_btn':
                $target_canvas = !empty($el['goto_canvas']) ? intval($el['goto_canvas']) : (!empty($el['goto_screen']) ? intval($el['goto_screen']) : 2);
                $btn_text = !empty($content) ? esc_html($content) : esc_html__('Next Canvas →', 'wppoppop');
                return '<div class="wppoppop-layer-item wppoppop-element ' . $anim_class . '" style="' . $style_attr . '"><button type="button" class="wppoppop-next-canvas-btn wppoppop-next-screen-btn wppoppop-next-step" data-goto-canvas="' . esc_attr($target_canvas) . '" data-goto-screen="' . esc_attr($target_canvas) . '" data-goto="' . esc_attr($target_canvas) . '" style="width:100%;height:100%;background:#2563eb;color:#ffffff;border:none;border-radius:inherit;font-weight:700;font-size:13px;cursor:pointer;">' . $btn_text . '</button></div>';

            case 'button':
            case 'link':
            case 'link_btn':
                /* WPPOPPOP_BUTTON_CASE: honour label, click action, new tab, nofollow and visual style */
                $bt_label = (isset($el['content']) && trim((string) $el['content']) !== '') ? esc_html($el['content']) : esc_html__('Click Here', 'wppoppop');
                $bt_act = isset($el['buttonAction']) ? (string) $el['buttonAction'] : 'link';
                if ($bt_act === 'url') { $bt_act = 'link'; }
                if ($bt_act === 'goto_canvas') { $bt_act = 'step'; }
                $bt_on = function ($keys) use ($el) {
                    foreach ($keys as $k) { if (isset($el[$k])) { return !empty($el[$k]) && $el[$k] !== 'false' && $el[$k] !== '0'; } }
                    return false;
                };
                $bt_blank = $bt_on(array('linkBlank', 'targetBlank'));
                $bt_nofollow = $bt_on(array('nofollow'));
                $bt_url = !empty($el['linkUrl']) ? esc_url($el['linkUrl']) : (!empty($el['url']) ? esc_url($el['url']) : '#');
                if ($bt_url === '') { $bt_url = '#'; }
                $bt_step = !empty($el['goto_canvas']) ? intval($el['goto_canvas']) : (!empty($el['goto_screen']) ? intval($el['goto_screen']) : 2);
                $bt_x = array();
                if (!empty($el['fontWeight']) && preg_match('/^[0-9a-z]{3,10}$/i', (string) $el['fontWeight'])) { $bt_x[] = 'font-weight:' . $el['fontWeight']; }
                if (isset($el['letterSpacing']) && is_numeric($el['letterSpacing']) && floatval($el['letterSpacing']) != 0) { $bt_x[] = 'letter-spacing:' . floatval($el['letterSpacing']) . 'px'; }
                if (!empty($el['lineHeight']) && is_numeric($el['lineHeight'])) { $bt_x[] = 'line-height:' . floatval($el['lineHeight']); }
                if (!empty($el['borderWidth']) && intval($el['borderWidth']) > 0) {
                    $bt_bs = (!empty($el['borderStyle']) && in_array($el['borderStyle'], array('solid', 'dashed', 'dotted', 'double'), true)) ? $el['borderStyle'] : 'solid';
                    $bt_bc = (!empty($el['borderColor']) && preg_match('/^#[0-9a-fA-F]{3,8}$/', $el['borderColor'])) ? $el['borderColor'] : '#334155';
                    $bt_x[] = 'border:' . intval($el['borderWidth']) . 'px ' . $bt_bs . ' ' . $bt_bc;
                }
                if (!empty($box_shadow_val)) { $bt_x[] = 'box-shadow:' . $box_shadow_val; }
                $bt_just = 'center';
                if (!empty($el['textAlign'])) {
                    if ($el['textAlign'] === 'left') { $bt_just = 'flex-start'; }
                    elseif ($el['textAlign'] === 'right') { $bt_just = 'flex-end'; }
                }
                $bt_x[] = 'transition:background-color .15s ease,color .15s ease,border-color .15s ease';
                $bt_wrap_style = esc_attr(implode(';', $style) . $anim_style . ';' . implode(';', $bt_x));
                $bt_in = 'width:100%;height:100%;display:flex;align-items:center;justify-content:' . $bt_just . ';box-sizing:border-box;margin:0;padding:0 12px;border:inherit;border-radius:inherit;background:inherit;color:inherit;font:inherit;letter-spacing:inherit;line-height:inherit;text-decoration:none;cursor:pointer;outline:none;-webkit-appearance:none;appearance:none;';
                if ($bt_act === 'step') {
                    $bt_html = '<button type="button" class="wppoppop-front-btn wppoppop-next-canvas-btn wppoppop-next-screen-btn" data-goto-canvas="' . $bt_step . '" data-goto-screen="' . $bt_step . '" data-goto="' . $bt_step . '" style="' . $bt_in . '">' . $bt_label . '</button>';
                } elseif ($bt_act === 'close') {
                    $bt_html = '<button type="button" class="wppoppop-front-btn wppoppop-close-trigger" data-close-action="close" style="' . $bt_in . '">' . $bt_label . '</button>';
                } else {
                    $bt_rel = array();
                    if ($bt_blank) { $bt_rel[] = 'noopener'; $bt_rel[] = 'noreferrer'; }
                    if ($bt_nofollow) { $bt_rel[] = 'nofollow'; }
                    $bt_html = '<a href="' . $bt_url . '" class="wppoppop-front-btn wppoppop-link-btn"' . ($bt_blank ? ' target="_blank"' : '') . (!empty($bt_rel) ? ' rel="' . esc_attr(implode(' ', $bt_rel)) . '"' : '') . ' style="' . $bt_in . '">' . $bt_label . '</a>';
                }
                return '<div id="el-' . $el_id . '" class="wppoppop-layer-item wppoppop-element wppoppop-front-element wppoppop-el-button ' . $anim_class . '" style="' . $bt_wrap_style . '">' . $bt_html . $hover_style_tag . '</div>';

            case 'submit':
                /* WPPOPPOP_SUBMIT_CASE: label, loading text, post-submit routing, typography, hover */
                $sb_label = (isset($el['content']) && trim((string) $el['content']) !== '') ? esc_html($el['content']) : esc_html__('Submit Form', 'wppoppop');
                $sb_loading = (isset($el['loadingText']) && trim((string) $el['loadingText']) !== '') ? (string) $el['loadingText'] : 'Submitting...';
                $sb_act = (isset($el['submitAction']) && in_array($el['submitAction'], array('default', 'next_canvas', 'redirect'), true)) ? $el['submitAction'] : 'default';
                $sb_canvas = !empty($el['goto_canvas']) ? max(1, intval($el['goto_canvas'])) : (!empty($el['goto_screen']) ? max(1, intval($el['goto_screen'])) : 2);
                $sb_url = !empty($el['redirectUrl']) ? esc_url($el['redirectUrl']) : '';
                $sb_confirm = isset($el['confirmMessage']) ? trim((string) $el['confirmMessage']) : '';
                $sb_just = 'center';
                if (!empty($el['textAlign'])) {
                    if ($el['textAlign'] === 'left') { $sb_just = 'flex-start'; }
                    elseif ($el['textAlign'] === 'right') { $sb_just = 'flex-end'; }
                }
                $sb_fg = (!empty($el['color']) && preg_match('/^#[0-9a-fA-F]{3,8}$/', (string) $el['color']) && strtolower((string) $el['color']) !== '#1e293b') ? $el['color'] : '#ffffff'; /* WPPOPPOP_SUBMIT_BLUE */
                $sb_custom = !empty($el['bgCustom']) && $el['bgCustom'] !== 'false' && $el['bgCustom'] !== '0'; /* WPPOPPOP_SUBMIT_BG_CUSTOM */
                $sb_trans = !empty($el['transparentFill']) && $el['transparentFill'] !== 'false' && $el['transparentFill'] !== '0';
                $sb_hexok = !empty($el['bgColor']) && preg_match('/^#[0-9a-fA-F]{3,8}$/', (string) $el['bgColor']);
                if ($sb_custom && $sb_trans) { $sb_bg = 'transparent'; }
                elseif ($sb_custom && $sb_hexok) { $sb_bg = $el['bgColor']; }
                elseif ($sb_hexok && !in_array(strtolower((string) $el['bgColor']), array('#ffffff', '#fff'), true) && !$sb_trans) { $sb_bg = $el['bgColor']; }
                else { $sb_bg = '#2563eb'; }
                $sb_hover = ($hover_style_tag !== '') ? $hover_style_tag : '<style>#el-' . $el_id . ' button:hover{background-color:#1d4ed8 !important;}</style>';
                $sb_fs = (!empty($el['fontSize']) && is_numeric($el['fontSize'])) ? max(8, intval($el['fontSize'])) : 13;
                $sb_fw = (!empty($el['fontWeight']) && preg_match('/^[0-9a-z]{3,10}$/i', (string) $el['fontWeight'])) ? $el['fontWeight'] : '700';
                $sb_x = 'font-size:' . $sb_fs . 'px;font-weight:' . $sb_fw . ';color:' . $sb_fg . ';';
                if (!empty($el['lineHeight']) && is_numeric($el['lineHeight'])) { $sb_x .= 'line-height:' . floatval($el['lineHeight']) . ';'; }
                if (isset($el['letterSpacing']) && is_numeric($el['letterSpacing']) && floatval($el['letterSpacing']) != 0) { $sb_x .= 'letter-spacing:' . floatval($el['letterSpacing']) . 'px;'; }
                if (!empty($el['fontFamily']) && $el['fontFamily'] !== 'inherit' && preg_match('/^[A-Za-z0-9 ,\'"-]+$/', (string) $el['fontFamily'])) { $sb_x .= 'font-family:' . esc_attr($el['fontFamily']) . ';'; }
                $sb_data = ' data-submit-action="' . esc_attr($sb_act) . '" data-loading-text="' . esc_attr($sb_loading) . '"';
                if ($sb_act === 'next_canvas') { $sb_data .= ' data-goto-canvas="' . $sb_canvas . '"'; }
                if ($sb_act === 'redirect' && $sb_url !== '') { $sb_data .= ' data-redirect-url="' . esc_attr($sb_url) . '"'; }
                if ($sb_act === 'default' && $sb_confirm !== '') { $sb_data .= ' data-confirm-message="' . esc_attr($sb_confirm) . '"'; }
                /* WPPOPPOP_BLOCK_EMPTY_SUBMIT: default ON; custom message is optional */
                if (!(isset($el['blockEmptySubmit']) && in_array($el['blockEmptySubmit'], array(false, 0, '0', 'false'), true))) {
                    $sb_data .= ' data-block-empty="1"';
                    if (isset($el['emptySubmitMessage']) && trim((string) $el['emptySubmitMessage']) !== '') { $sb_data .= ' data-empty-msg="' . esc_attr(trim((string) $el['emptySubmitMessage'])) . '"'; }
                }
                /* WPPOPPOP_SUBMIT_ICONS: left/right icons + offsets + colours, box shadow */
                $sb_ic = array('left' => array('cls' => '', 'off' => 10), 'right' => array('cls' => '', 'off' => 10));
                $sb_dash = false;
                foreach (array('iconLeft' => 'left', 'iconRight' => 'right') as $sb_k => $sb_side) {
                    $sb_r = isset($el[$sb_k]) ? trim((string) $el[$sb_k]) : '';
                    if ($sb_r !== '') {
                        $sb_tk = array_values(array_filter(array_map('sanitize_html_class', preg_split('/\s+/', $sb_r))));
                        if (!empty($sb_tk)) {
                            if (strpos($sb_tk[0], 'fa-') === 0) { array_unshift($sb_tk, 'fas'); }
                            elseif (strpos($sb_tk[0], 'dashicons-') === 0) { array_unshift($sb_tk, 'dashicons'); $sb_dash = true; }
                            $sb_ic[$sb_side]['cls'] = implode(' ', $sb_tk);
                        }
                    }
                    $sb_ok = $sb_k . 'Offset';
                    if (isset($el[$sb_ok]) && $el[$sb_ok] !== '') { $sb_ic[$sb_side]['off'] = max(0, intval($el[$sb_ok])); }
                }
                $sb_col = function ($key) use ($el) {
                    foreach (array($key, 'fgColor') as $k) {
                        if (!empty($el[$k]) && preg_match('/^#[0-9a-fA-F]{3,8}$/', (string) $el[$k])) { return $el[$k]; }
                    }
                    return 'currentColor';
                };
                $sb_tag = function ($side, $ckey) use ($sb_ic, $sb_col) {
                    if ($sb_ic[$side]['cls'] === '') { return ''; }
                    return '<i class="' . esc_attr($sb_ic[$side]['cls']) . '" aria-hidden="true" style="position:absolute;' . $side . ':' . intval($sb_ic[$side]['off']) . 'px;top:50%;transform:translateY(-50%);color:' . esc_attr($sb_col($ckey)) . ';font-size:14px;line-height:1;pointer-events:none;"></i>';
                };
                static $sb_css_done = false;
                $sb_css = '';
                if (!$sb_css_done && ($sb_ic['left']['cls'] !== '' || $sb_ic['right']['cls'] !== '')) {
                    $sb_css_done = true;
                    $sb_css = WpPopPop_Libraries::style_tag('wppoppop-font-awesome');
                    if ($sb_dash) { $sb_css .= WpPopPop_Libraries::style_tag('dashicons'); }
                }
                $sb_pl = ($sb_ic['left']['cls'] !== '') ? ($sb_ic['left']['off'] + 22) : 12;
                $sb_pr = ($sb_ic['right']['cls'] !== '') ? ($sb_ic['right']['off'] + 22) : 12;
                $sb_shadow = !empty($box_shadow_val) ? (';box-shadow:' . esc_attr($box_shadow_val)) : '';
                return $sb_css . '<div id="el-' . $el_id . '" class="wppoppop-layer-item wppoppop-element wppoppop-front-element wppoppop-el-button ' . $anim_class . '" style="' . $style_attr . ';padding:0!important;background-color:transparent;pointer-events:auto' . $sb_shadow . ';">'
                    . '<button type="button" class="wppoppop-submit-trigger wppoppop-submit-btn"' . $sb_data . ' style="position:relative;width:100%;height:100%;display:flex;align-items:center;justify-content:' . $sb_just . ';box-sizing:border-box;margin:0;padding:0 ' . $sb_pr . 'px 0 ' . $sb_pl . 'px;background:' . esc_attr(isset($sb_bg) ? $sb_bg : '#2563eb') . ';border:none;border-radius:inherit;cursor:pointer;' . $sb_x . '">'
                    . $sb_tag('left', 'iconLeftColor') . '<span>' . $sb_label . '</span>' . $sb_tag('right', 'iconRightColor')
                    . '</button>'
                    . (isset($sb_hover) ? $sb_hover : $hover_style_tag) . '</div>';
            case 'pay':
                $btn_text = !empty($content) ? esc_html($content) : esc_html__('Pay Now', 'wppoppop');
                return '<div class="wppoppop-layer-item wppoppop-element ' . $anim_class . '" style="' . $style_attr . '"><button type="button" class="wppoppop-pay-trigger"' . WpPopPop_Module_Bridge::pay_attrs($el) . ' style="width:100%;height:100%;background:#059669;color:#ffffff;border:none;border-radius:inherit;font-weight:700;font-size:13px;cursor:pointer;">' . $btn_text . '</button></div>';

            case 'close':
            case 'close_icon':
                /* WPPOPPOP_CLOSE_CASE: render the Close layer as a real, clickable button */
                if (!class_exists('WpPopPop_Front_Element_Actions')) {
                    $wppoppop_act_file = WPPOPPOP_PATH . 'includes/front/elements/class-front-element-actions.php';
                    if (file_exists($wppoppop_act_file)) { require_once $wppoppop_act_file; }
                }
                $wppoppop_close_inner = class_exists('WpPopPop_Front_Element_Actions')
                    ? WpPopPop_Front_Element_Actions::render_close($el)
                    : '&times;';
                $wppoppop_hv_vars = '';
                foreach ($wrapper_styles as $wppoppop_ws) {
                    if (strpos($wppoppop_ws, '--') === 0) { $wppoppop_hv_vars .= $wppoppop_ws; }
                }
                return '<div id="el-' . $el_id . '" class="wppoppop-layer-item wppoppop-element wppoppop-front-element wppoppop-el-close ' . $anim_class . '" style="'
                    . esc_attr(implode(';', $style) . $anim_style) . ';' . esc_attr($wppoppop_hv_vars) . 'pointer-events:auto;">'
                    . $wppoppop_close_inner . $hover_style_tag . '</div>';

            case 'html':
                return '<div class="wppoppop-layer-item wppoppop-element ' . $anim_class . '" style="' . $style_attr . ';overflow:hidden;">' . $content . '</div>';

            default:
                /* Companion plugins render their own element types here; the callback must return escaped HTML. */
                $wppoppop_ext_html = apply_filters('wppoppop_render_element', '', $type, $el, array('style_attr' => $style_attr, 'anim_class' => $anim_class, 'content' => $content));
                if (is_string($wppoppop_ext_html) && $wppoppop_ext_html !== '') {
                    return $wppoppop_ext_html;
                }
                /* WPPOPPOP_DECOMPOSED_DEFAULT: form types without their own case */
                $wppoppop_dt = strtolower(trim((string) $type));
                if (in_array($wppoppop_dt, array('phone','tel','textarea','multiselect','time','upload','file','password'), true) && class_exists('WpPopPop_Front_Decomposed_26')) {
                    $wppoppop_dec = WpPopPop_Front_Decomposed_26::render_element($wppoppop_dt, $el);
                    if ($wppoppop_dec !== null) {
                        return '<div class="wppoppop-layer-item wppoppop-element wppoppop-el-text ' . $anim_class . '" style="' . $style_attr . (in_array($wppoppop_dt, array('upload', 'file', 'password'), true) ? ';padding:0!important' : '') . '">' . $wppoppop_dec . '</div>'; /* WPPOPPOP_IMAGESELECT_WRAP */
                    }
                }
                return '<div class="wppoppop-layer-item wppoppop-element ' . $anim_class . '" style="' . $style_attr . '">' . esc_html($content) . '</div>';
        }
        return '<div class="wppoppop-layer-item wppoppop-element ' . $anim_class . '" style="' . $style_attr . '">' . (isset($inner_html) ? $inner_html : '') . $hover_style_tag . '</div>';
    }
}
