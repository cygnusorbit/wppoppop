<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Front_Renderer {
    public function render_popup_markup($uid, array $config, $is_inline = false) {
        $meta        = $config['meta'] ?? [];
        $width       = intval($meta['width'] ?? 640);
        $height      = intval($meta['height'] ?? 400);
        $bg_color    = esc_attr($meta['bg_color'] ?? '#ffffff');
        $elements    = (array)($config['elements'] ?? []);
        $styling     = $config['styling'] ?? [];
        $tabs        = $config['tabs'] ?? [];
        $ribbon      = $config['ribbon'] ?? [];
        $config_json = esc_attr(wp_json_encode($config));

        $backdrop_blur = intval($styling['backdrop_blur'] ?? 0);
        $overlay_style = $backdrop_blur > 0 ? "backdrop-filter: blur({$backdrop_blur}px); -webkit-backdrop-filter: blur({$backdrop_blur}px);" : "";

        $is_ribbon = !empty($ribbon['enable']);
        $wrapper_class = $is_inline ? 'wppoppop-inline-container' : ($is_ribbon ? 'wppoppop-ribbon-bar wppoppop-ribbon-' . esc_attr($ribbon['position'] ?? 'top') : 'wppoppop-overlay');
        $display_style = $is_inline ? 'position: relative;' : ($is_ribbon ? 'display: none;' : 'display: none; ' . $overlay_style);

        // Group elements by screen
        $screens = [];
        foreach ($elements as $el) {
            $s = intval($el['screen'] ?? 1);
            if (!isset($screens[$s])) {
                $screens[$s] = [];
            }
            $screens[$s][] = $el;
        }
        if (empty($screens)) {
            $screens[1] = [];
        }
        ksort($screens);
        ?>
        <!-- Sticky Side Tab Trigger -->
        <?php if (!empty($tabs['enable']) && !$is_inline && !$is_ribbon) : ?>
            <div class="wppoppop-side-tab wppoppop-side-tab-<?php echo esc_attr($tabs['position'] ?? 'left'); ?>"
                 data-target-uid="<?php echo esc_attr($uid); ?>"
                 style="background-color:<?php echo esc_attr($tabs['bg_color'] ?? '#2271b1'); ?>; color:<?php echo esc_attr($tabs['color'] ?? '#ffffff'); ?>;">
                <span><?php echo esc_html($tabs['text'] ?? 'Special Offer'); ?></span>
            </div>
        <?php endif; ?>

        <div id="wppoppop-popup-<?php echo esc_attr($uid); ?>"
             class="<?php echo esc_attr($wrapper_class); ?>"
             data-uid="<?php echo esc_attr($uid); ?>"
             data-config="<?php echo $config_json; ?>"
             style="<?php echo $display_style; ?>">

            <div class="wppoppop-box" style="width: <?php echo $width; ?>px; height: <?php echo $height; ?>px; background-color: <?php echo $bg_color; ?>;">
                <?php if (!$is_inline) : ?>
                    <button type="button" class="wppoppop-close-btn" aria-label="Close">&times;</button>
                <?php endif; ?>

                <!-- Anti-Spam Honeypot Field -->
                <div style="display:none !important;" aria-hidden="true">
                    <input type="text" name="_wppoppop_hp_email" class="_wppoppop_hp_field" tabindex="-1" autocomplete="off">
                </div>

                <?php foreach ($screens as $idx => $screen_elements) : 
                    $is_active = ($idx === 1);
                    ?>
                    <div class="wppoppop-screen-container <?php echo $is_active ? 'wppoppop-screen-active' : ''; ?>" data-screen-index="<?php echo $idx; ?>" style="<?php echo $is_active ? '' : 'display:none;'; ?>">
                        <div class="wppoppop-box-content">
                            <?php foreach ($screen_elements as $el) : 
                                $this->render_canvas_element($el);
                            endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="wppoppop-status-overlay" style="display:none;">
                    <div class="wppoppop-status-message"></div>
                </div>
            </div>
        </div>
        <?php
    }

    public function render_canvas_element(array $el) {
        $left        = intval($el['left'] ?? 0);
        $top         = intval($el['top'] ?? 0);
        $w           = intval($el['width'] ?? 160);
        $h           = intval($el['height'] ?? 40);
        $z           = intval($el['z_index'] ?? 1);
        $field_name  = esc_attr($el['field_name'] ?? '');
        $font_family = (!empty($el['font_family']) && $el['font_family'] !== 'Inherit') ? esc_attr($el['font_family']) : 'inherit';
        $anim        = esc_attr($el['anim'] ?? 'none');
        $anim_delay  = intval($el['anim_delay'] ?? 0);
        $anim_dur    = intval($el['anim_duration'] ?? 500);
        $is_required = !empty($el['required']);
        $req_attr    = $is_required ? 'required' : '';
        $options     = (array)($el['options'] ?? []);
        $styles      = "position:absolute;left:{$left}px;top:{$top}px;width:{$w}px;height:{$h}px;z-index:{$z};font-family:{$font_family};";
        ?>
        <div id="<?php echo esc_attr($el['id']); ?>"
             class="wppoppop-layer-item wppoppop-anim-<?php echo $anim; ?>"
             style="<?php echo $styles; ?>"
             data-anim="<?php echo $anim; ?>"
             data-anim-delay="<?php echo $anim_delay; ?>"
             data-anim-duration="<?php echo $anim_dur; ?>"
             data-type="<?php echo esc_attr($el['type']); ?>"
             data-field-name="<?php echo $field_name; ?>"
             data-required="<?php echo $is_required ? '1' : '0'; ?>"
             data-error-msg="<?php echo esc_attr($el['error_msg'] ?? 'This field is required'); ?>">

            <?php if ($el['type'] === 'text') : ?>
                <div class="wppoppop-text-render" data-raw-template="<?php echo esc_attr($el['content'] ?? ''); ?>" style="font-size:<?php echo esc_attr($el['font_size'] ?? '16'); ?>px;color:<?php echo esc_attr($el['color'] ?? '#222'); ?>;">
                    <?php echo esc_html($el['content'] ?? ''); ?>
                </div>

            <?php elseif ($el['type'] === 'input') : ?>
                <input type="email" class="wppoppop-field-email" name="<?php echo $field_name ?: 'email'; ?>" placeholder="<?php echo esc_attr($el['content'] ?? 'Enter email...'); ?>" <?php echo $req_attr; ?> style="width:100%;height:100%;padding:0 12px;box-sizing:border-box;">

            <?php elseif ($el['type'] === 'number') : ?>
                <input type="number" name="<?php echo $field_name ?: 'qty'; ?>" value="<?php echo esc_attr($el['content'] ?? '1'); ?>" class="wppoppop-field-number" style="width:100%;height:100%;padding:0 10px;box-sizing:border-box;">

            <?php elseif ($el['type'] === 'dropdown') : ?>
                <select name="<?php echo $field_name ?: 'choice'; ?>" class="wppoppop-field-select" style="width:100%;height:100%;">
                    <?php foreach ($options as $opt) : ?>
                        <option value="<?php echo esc_attr($opt); ?>"><?php echo esc_html($opt); ?></option>
                    <?php endforeach; ?>
                </select>

            <?php elseif ($el['type'] === 'radio') : ?>
                <div class="wppoppop-field-radios" style="display:flex;gap:12px;align-items:center;height:100%;">
                    <?php foreach ($options as $k => $opt) : ?>
                        <label><input type="radio" name="<?php echo $field_name ?: 'radio_opt'; ?>" value="<?php echo esc_attr($opt); ?>" <?php echo $k === 0 ? 'checked' : ''; ?>> <?php echo esc_html($opt); ?></label>
                    <?php endforeach; ?>
                </div>

            <?php elseif ($el['type'] === 'checkbox') : ?>
                <div class="wppoppop-field-checkboxes" style="display:flex;gap:12px;align-items:center;height:100%;">
                    <label><input type="checkbox" name="<?php echo $field_name ?: 'terms'; ?>" value="1" <?php echo $req_attr; ?>> <?php echo esc_html($el['content'] ?? 'I agree to the terms'); ?></label>
                </div>

            <?php elseif ($el['type'] === 'rating') : ?>
                <div class="wppoppop-field-rating" data-stars="5" style="font-size:22px;color:#f59e0b;cursor:pointer;">
                    &#9733;&#9733;&#9733;&#9733;&#9733;
                    <input type="hidden" name="<?php echo $field_name ?: 'rating'; ?>" value="5">
                </div>

            <?php elseif ($el['type'] === 'date') : ?>
                <input type="date" name="<?php echo $field_name ?: 'date'; ?>" class="wppoppop-field-date" style="width:100%;height:100%;padding:0 10px;box-sizing:border-box;">

            <?php elseif ($el['type'] === 'slider') : ?>
                <div style="width:100%;height:100%;display:flex;align-items:center;">
                    <input type="range" name="<?php echo $field_name ?: 'range'; ?>" min="0" max="100" value="50" style="width:100%;">
                </div>

            <?php elseif ($el['type'] === 'signature') : ?>
                <div class="wppoppop-signature-wrap" style="width:100%;height:100%;border:1px dashed #cbd5e1;background:#fff;position:relative;">
                    <canvas class="wppoppop-sig-canvas" width="<?php echo $w; ?>" height="<?php echo $h; ?>"></canvas>
                    <button type="button" class="wppoppop-sig-clear-btn" style="position:absolute;top:2px;right:2px;font-size:10px;">Clear</button>
                    <input type="hidden" name="<?php echo $field_name ?: 'signature'; ?>" class="wppoppop-sig-input" value="">
                </div>

            <?php elseif ($el['type'] === 'wheel') : ?>
                <div class="wppoppop-wheel-wrapper">
                    <canvas class="wppoppop-wheel-canvas" width="220" height="220" data-slices="<?php echo esc_attr(wp_json_encode($options)); ?>"></canvas>
                    <div class="wppoppop-wheel-pointer">&#9660;</div>
                    <button type="button" class="wppoppop-wheel-spin-btn">SPIN!</button>
                    <input type="hidden" name="<?php echo $field_name ?: 'prize'; ?>" value="">
                </div>

            <?php elseif ($el['type'] === 'scratch') : ?>
                <div class="wppoppop-scratch-wrapper" style="width:100%;height:100%;position:relative;">
                    <div class="wppoppop-scratch-secret" style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:18px;background:#fef08a;">
                        <?php echo esc_html($el['content'] ?? 'SAVE50'); ?>
                    </div>
                    <canvas class="wppoppop-scratch-canvas" width="<?php echo $w; ?>" height="<?php echo $h; ?>" style="position:absolute;inset:0;"></canvas>
                </div>

            <?php elseif ($el['type'] === 'countdown') : ?>
                <div class="wppoppop-countdown-timer" data-seconds="<?php echo intval($el['content'] ?? 900); ?>" style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:18px;background:#1e293b;color:#fff;border-radius:4px;">
                    <span class="cd-mins">15</span>m : <span class="cd-secs">00</span>s
                </div>

            <?php elseif ($el['type'] === 'progress') : ?>
                <div style="width:100%;height:100%;background:#e2e8f0;border-radius:10px;overflow:hidden;position:relative;">
                    <div class="wppoppop-progress-fill" style="width:50%;height:100%;background:#2271b1;transition:width 0.3s ease;"></div>
                </div>

            <?php elseif ($el['type'] === 'file') : ?>
                <input type="file" name="<?php echo $field_name ?: 'attachment'; ?>" style="width:100%;height:100%;font-size:12px;">

            <?php elseif ($el['type'] === 'nextstep') : ?>
                <button type="button" class="wppoppop-next-screen-btn" data-goto="<?php echo intval($el['goto_screen'] ?? 2); ?>" style="width:100%;height:100%;background-color:<?php echo esc_attr($el['bg_color'] ?? '#2271b1'); ?>;color:#fff;border:none;border-radius:4px;cursor:pointer;font-weight:600;">
                    <?php echo esc_html($el['content'] ?? 'Next Step &rarr;'); ?>
                </button>

            <?php elseif ($el['type'] === 'button') : ?>
                <button type="button" class="wppoppop-submit-trigger" style="width:100%;height:100%;background-color:<?php echo esc_attr($el['bg_color'] ?? '#00a32a'); ?>;color:#fff;border:none;border-radius:4px;cursor:pointer;font-weight:600;">
                    <?php echo esc_html($el['content'] ?? 'Submit'); ?>
                </button>

            <?php elseif ($el['type'] === 'pay_btn') : ?>
                <button type="button" class="wppoppop-pay-trigger" data-amount="<?php echo esc_attr($el['amount'] ?? '10.00'); ?>" data-currency="<?php echo esc_attr($el['currency'] ?? 'USD'); ?>" style="width:100%;height:100%;background-color:#0284c7;color:#fff;border:none;border-radius:4px;cursor:pointer;font-weight:700;">
                    Pay <?php echo esc_html($el['amount'] ?? '10.00'); ?> <?php echo esc_html($el['currency'] ?? 'USD'); ?>
                </button>

            <?php elseif ($el['type'] === 'html') : ?>
                <div><?php echo wp_kses_post($el['content'] ?? ''); ?></div>
            <?php endif; ?>
        </div>
        <?php
    }
}
