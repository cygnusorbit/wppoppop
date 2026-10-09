<?php
if (!defined('ABSPATH')) {
    exit;
}

class WpPopPop_Front_Renderer {
    public function render_popup_markup($uid, array $config, $is_inline = false, $preload = false) {
        $uid_attr = esc_attr($uid);
        $style_rules = isset($config['style']) && is_array($config['style']) ? $config['style'] : [];
        $canvases = isset($config['canvases']) && is_array($config['canvases']) ? $config['canvases'] : (isset($config['screens']) && is_array($config['screens']) ? $config['screens'] : []);

        if (empty($canvases)) {
            $canvases = [
                '1' => [
                    'name'    => 'Canvas 1',
                    'width'   => 640,
                    'height'  => 400,
                    'bg_mode' => 'solid',
                    'bg_color'=> '#ffffff',
                    'layers'  => []
                ]
            ];
        }

        $default_font   = wppoppop_get_setting('default_font', 'inherit');
        $show_watermark = (bool) wppoppop_get_setting('powered_by_badge', false);
        $minify_css     = (bool) wppoppop_get_setting('minify_css', false);

        // Build inline style blocks
        $custom_inline_css = "
        #wppoppop-wrap-{$uid_attr} {
            font-family: {$default_font};
        }
        ";

        if ($minify_css) {
            $custom_inline_css = wppoppop_minify_css($custom_inline_css);
        }

        ob_start();
        ?>
        <div id="wppoppop-wrap-<?php echo $uid_attr; ?>" class="wppoppop-popup-wrap <?php echo $preload ? 'wppoppop-preloaded' : ''; ?>" data-uid="<?php echo $uid_attr; ?>" style="display:none;" aria-hidden="true" role="dialog">
            <style><?php echo $custom_inline_css; ?></style>
            <div class="wppoppop-backdrop" data-uid="<?php echo $uid_attr; ?>"></div>
            <div class="wppoppop-box-container" data-uid="<?php echo $uid_attr; ?>">
                
                <?php foreach ($canvases as $c_idx => $canvas) : 
                    $canvas_num = esc_attr($c_idx);
                    $c_width    = isset($canvas['width']) ? (int) $canvas['width'] : 640;
                    $c_height   = isset($canvas['height']) ? (int) $canvas['height'] : 400;
                    $bg_mode    = isset($canvas['bg_mode']) ? $canvas['bg_mode'] : 'solid';
                    $bg_color   = isset($canvas['bg_color']) ? $canvas['bg_color'] : '#ffffff';
                    $is_first   = ((string)$c_idx === '1' || $c_idx === array_key_first($canvases));
                    
                    $canvas_style = "width:100%;max-width:{$c_width}px;min-height:{$c_height}px;";
                    if ($bg_mode === 'solid') {
                        $canvas_style .= "background-color:{$bg_color};";
                    }
                ?>
                    <div id="wppoppop-canvas-<?php echo $uid_attr; ?>-<?php echo $canvas_num; ?>" 
                         class="wppoppop-canvas-stage <?php echo $is_first ? 'active' : ''; ?>" 
                         data-canvas="<?php echo $canvas_num; ?>" 
                         data-screen="<?php echo $canvas_num; ?>" 
                         style="<?php echo esc_attr($canvas_style); ?><?php echo !$is_first ? 'display:none;' : ''; ?>">
                         
                        <button type="button" class="wppoppop-close-btn" data-uid="<?php echo $uid_attr; ?>" aria-label="Close popup">&times;</button>
                        
                        <div class="wppoppop-canvas-content">
                            <?php 
                            if (!empty($canvas['layers']) && is_array($canvas['layers'])) {
                                foreach ($canvas['layers'] as $layer) {
                                    $this->render_layer_element($layer);
                                }
                            }
                            ?>
                        </div>

                        <?php if ($show_watermark) : ?>
                            <div class="wppoppop-watermark-badge">
                                <a href="https://github.com/cygnusorbit/wppoppop" target="_blank" rel="noopener noreferrer">
                                    <span>Powered by</span> <strong>WpPopPop</strong>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>

            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    protected function render_layer_element($layer) {
        $type = isset($layer['type']) ? sanitize_key($layer['type']) : 'text';
        $x    = isset($layer['x']) ? (int)$layer['x'] : 20;
        $y    = isset($layer['y']) ? (int)$layer['y'] : 20;
        $w    = isset($layer['w']) ? (int)$layer['w'] : 200;
        $h    = isset($layer['h']) ? (int)$layer['h'] : 40;
        $content = isset($layer['content']) ? $layer['content'] : '';

        echo "<div class='wppoppop-element wppoppop-el-{$type}' style='position:absolute;left:{$x}px;top:{$y}px;width:{$w}px;min-height:{$h}px;'>";
        if ($type === 'text') {
            echo wp_kses_post($content);
        } elseif ($type === 'button') {
            echo "<button type='button' class='wppoppop-submit-btn'>" . esc_html($content ? $content : 'Submit') . "</button>";
        } else {
            echo wp_kses_post($content);
        }
        echo "</div>";
    }
}
