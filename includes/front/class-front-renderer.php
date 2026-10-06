<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Front_Renderer {
    public function render_popup($popup, $is_preview = false) {
        if (!$popup || empty($popup->data)) {
            return '';
        }

        $config = json_decode($popup->data, true);
        if (!$config) {
            return '';
        }

        $uid      = esc_attr($popup->uid);
        $settings = isset($config['settings']) ? $config['settings'] : [];
        $elements = isset($config['elements']) ? $config['elements'] : [];
        $screens  = (isset($config['screens']) && is_array($config['screens']) && !empty($config['screens'])) 
                    ? $config['screens'] 
                    : [
                        [
                            'id'             => 1,
                            'title'          => 'Screen 1',
                            'width'          => $settings['width'] ?? 640,
                            'height'         => $settings['height'] ?? 400,
                            'bgMode'         => $settings['bgMode'] ?? 'solid',
                            'bgColor'        => $settings['bgColor'] ?? '#ffffff',
                            'gradColor1'     => $settings['gradColor1'] ?? '#3b82f6',
                            'gradColor2'     => $settings['gradColor2'] ?? '#1d4ed8',
                            'gradAngle'      => $settings['gradAngle'] ?? 135,
                            'animIn'         => 'animate__fadeIn',
                            'animInDuration' => 1000,
                            'animInDelay'    => 0,
                            'animOut'        => 'animate__fadeOut',
                            'logic'          => ['enable' => false]
                        ]
                    ];

        $initial_screen = $screens[0];
        $init_w         = intval($initial_screen['width'] ?? 640);
        $init_h         = intval($initial_screen['height'] ?? 400);
        $bg_mode        = $initial_screen['bgMode'] ?? 'solid';

        $bg_style = '';
        if ($bg_mode === 'gradient') {
            $c1    = esc_attr($initial_screen['gradColor1'] ?? '#3b82f6');
            $c2    = esc_attr($initial_screen['gradColor2'] ?? '#1d4ed8');
            $angle = intval($initial_screen['gradAngle'] ?? 135);
            $bg_style = "background: linear-gradient({$angle}deg, {$c1}, {$c2});";
        } else {
            $solid = esc_attr($initial_screen['bgColor'] ?? '#ffffff');
            $bg_style = "background: {$solid};";
        }

        $init_anim_in   = esc_attr($initial_screen['animIn'] ?? 'animate__fadeIn');
        $init_duration  = (intval($initial_screen['animInDuration'] ?? 1000) / 1000) . 's';
        $init_delay     = (intval($initial_screen['animInDelay'] ?? 0) / 1000) . 's';
        $custom_css     = $config['custom_css'] ?? ($settings['customCss'] ?? '');

        ob_start();
        ?>
        <div id="wppoppop-modal-<?php echo $uid; ?>" 
             class="wppoppop-modal-overlay" 
             data-popup-uid="<?php echo $uid; ?>" 
             data-popup-config="<?php echo esc_attr(wp_json_encode($config)); ?>"
             style="display:none;">
            
            <div class="wppoppop-modal-backdrop"></div>
            
            <div class="wppoppop-popup-box animate__animated <?php echo ($init_anim_in !== 'none') ? $init_anim_in : ''; ?>" 
                 style="width:<?php echo $init_w; ?>px; height:<?php echo $init_h; ?>px; <?php echo $bg_style; ?> --animate-duration: <?php echo $init_duration; ?>; animation-delay: <?php echo $init_delay; ?>;">
                
                <button type="button" class="wppoppop-modal-close" aria-label="Close popup">&times;</button>
                
                <?php foreach ($screens as $index => $screen): 
                    $s_id = intval($screen['id']);
                    $is_active = ($index === 0);
                    $screen_elements = array_filter($elements, function($el) use ($s_id) {
                        return intval($el['screen'] ?? 1) === $s_id && empty($el['hidden']);
                    });
                ?>
                    <div class="wppoppop-screen-viewport" 
                         data-screen-id="<?php echo $s_id; ?>" 
                         style="<?php echo $is_active ? 'display:block;' : 'display:none;'; ?>">
                        
                        <?php foreach ($screen_elements as $el): 
                            $el_id    = esc_attr($el['id'] ?? uniqid('el_'));
                            $el_type  = esc_attr($el['type'] ?? 'text');
                            $top      = intval($el['top'] ?? 0);
                            $left     = intval($el['left'] ?? 0);
                            $w        = intval($el['width'] ?? 200);
                            $h        = intval($el['height'] ?? 40);
                            $content  = esc_html($el['content'] ?? ($el['label'] ?? ''));
                            $font_sz  = intval($el['fontSize'] ?? 14);
                            $radius   = intval($el['borderRadius'] ?? 4);
                            $color    = esc_attr($el['color'] ?? '#1e293b');
                            $bg       = esc_attr($el['bgColor'] ?? 'transparent');
                            $anim_el  = (!empty($el['animEffect']) && $el['animEffect'] !== 'none') ? esc_attr($el['animEffect']) : '';
                            $action   = esc_attr($el['actionClose'] ?? 'none');
                            $target   = intval($el['actionTargetScreen'] ?? 2);
                            $url      = esc_url($el['actionUrl'] ?? '');
                            $blank    = !empty($el['actionBlank']) ? 'target="_blank"' : '';
                        ?>
                            <div id="wppoppop-el-<?php echo $el_id; ?>" 
                                 class="wppoppop-element wppoppop-el-<?php echo $el_type; ?> <?php echo !empty($anim_el) ? 'animate__animated ' . $anim_el : ''; ?>"
                                 data-el-id="<?php echo $el_id; ?>"
                                 data-el-type="<?php echo $el_type; ?>"
                                 data-action="<?php echo $action; ?>"
                                 data-target-screen="<?php echo $target; ?>"
                                 style="position:absolute; top:<?php echo $top; ?>px; left:<?php echo $left; ?>px; width:<?php echo $w; ?>px; height:<?php echo $h; ?>px; font-size:<?php echo $font_sz; ?>px; border-radius:<?php echo $radius; ?>px; color:<?php echo $color; ?>; background:<?php echo $bg; ?>;">
                                
                                <?php if ($el_type === 'step_btn' || $el_type === 'submit' || $el_type === 'pay'): ?>
                                    <button type="button" class="wppoppop-btn-action" style="width:100%; height:100%; background:inherit; color:inherit; border:none; border-radius:inherit; font-size:inherit; font-weight:700; cursor:pointer;">
                                        <?php echo $content; ?>
                                    </button>
                                <?php elseif ($el_type === 'email'): ?>
                                    <input type="email" name="wppoppop_email_<?php echo $el_id; ?>" class="wppoppop-input" placeholder="<?php echo $content ?: 'Enter your email...'; ?>" style="width:100%; height:100%; padding:0 12px; border:1px solid #cbd5e1; border-radius:inherit; font-size:inherit; box-sizing:border-box;">
                                <?php elseif ($el_type === 'number'): ?>
                                    <input type="number" name="wppoppop_num_<?php echo $el_id; ?>" class="wppoppop-input" placeholder="<?php echo $content ?: '0'; ?>" style="width:100%; height:100%; padding:0 12px; border:1px solid #cbd5e1; border-radius:inherit; font-size:inherit; box-sizing:border-box;">
                                <?php elseif ($el_type === 'select'): ?>
                                    <select name="wppoppop_sel_<?php echo $el_id; ?>" class="wppoppop-input" style="width:100%; height:100%; padding:0 10px; border:1px solid #cbd5e1; border-radius:inherit; font-size:inherit; box-sizing:border-box;">
                                        <option value="">Select an option...</option>
                                        <option value="VIP">VIP</option>
                                        <option value="Standard">Standard</option>
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select>
                                <?php elseif ($el_type === 'radios'): ?>
                                    <div style="display:flex; align-items:center; gap:12px; width:100%; height:100%; padding:0 8px; box-sizing:border-box;">
                                        <label style="display:flex; align-items:center; gap:4px; font-size:inherit; cursor:pointer;"><input type="radio" name="wppoppop_rad_<?php echo $el_id; ?>" value="Choice A" checked> Choice A</label>
                                        <label style="display:flex; align-items:center; gap:4px; font-size:inherit; cursor:pointer;"><input type="radio" name="wppoppop_rad_<?php echo $el_id; ?>" value="Choice B"> Choice B</label>
                                    </div>
                                <?php elseif ($el_type === 'checkboxes'): ?>
                                    <label style="display:flex; align-items:center; gap:8px; width:100%; height:100%; padding:0 8px; box-sizing:border-box; cursor:pointer;">
                                        <input type="checkbox" name="wppoppop_chk_<?php echo $el_id; ?>" value="1" checked>
                                        <span style="font-size:inherit;"><?php echo $content ?: 'I accept the terms & conditions'; ?></span>
                                    </label>
                                <?php elseif ($el_type === 'html'): ?>
                                    <div style="width:100%; height:100%; overflow:hidden;"><?php echo wp_kses_post($el['content'] ?? ''); ?></div>
                                <?php else: ?>
                                    <div style="width:100%; height:100%; display:flex; align-items:center; padding:0 8px; box-sizing:border-box; line-height:1.4;">
                                        <?php echo $content; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (!empty($custom_css)): ?>
                <style><?php echo wp_strip_all_tags($custom_css); ?></style>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
