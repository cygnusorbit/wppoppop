<?php
namespace WPPopPop\Admin;

use WPPopPop\Targeting\PopupPostType;
use WPPopPop\Core\WebFonts;

class Builder {
    public static function init(): void {
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_assets'], 20);
        add_action('wp_ajax_wppoppop_save_builder', [__CLASS__, 'ajax_save_builder']);
        add_action('wp_ajax_wppoppop_load_builder', [__CLASS__, 'ajax_load_builder']);
    }

    public static function enqueue_assets(): void {
        wp_enqueue_media();
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
    }

    public static function render_builder_page(): void {
        self::enqueue_assets();

        $popup_id = isset($_GET['id']) ? absint($_GET['id']) : 0;
        $title    = $popup_id ? get_the_title($popup_id) : ('popup-' . gmdate('Y-m-d-H-i-s'));
        $layers   = [];
        $canvas   = ['width' => 640, 'height' => 440, 'bgColor' => '#ffffff', 'borderRadius' => 8];

        if ($popup_id > 0) {
            $raw_layers = get_post_meta($popup_id, '_wppoppop_builder_layers', true);
            if (!empty($raw_layers)) {
                $decoded = is_string($raw_layers) ? json_decode($raw_layers, true) : $raw_layers;
                if (is_array($decoded)) $layers = $decoded;
            }
            $saved_canvas = get_post_meta($popup_id, '_wppoppop_canvas_config', true);
            if (is_array($saved_canvas)) {
                $canvas = wp_parse_args($saved_canvas, $canvas);
            }
        }
        ?>
        <!-- Inlined CSS for Guaranteed 1:1 Rendering matching Menu-Create Popups.png -->
        <style>
        .wppoppop-builder-app {
            margin: -10px 0 0 -20px;
            background: #f1f3f5;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            color: #1e293b;
            min-height: calc(100vh - 32px);
            display: flex;
            flex-direction: column;
            user-select: none;
        }
        /* Top Navigation Header */
        .wppoppop-hdr {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .wppoppop-hdr h2 {
            margin: 0;
            font-size: 22px;
            font-weight: 500;
            color: #1e293b;
        }
        .wppoppop-hdr-actions {
            display: flex;
            gap: 10px;
        }
        .wppoppop-btn-outline {
            border: 1px solid #b5295c;
            color: #b5295c;
            background: #ffffff;
            border-radius: 3px;
            padding: 5px 14px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .wppoppop-btn-outline:hover {
            background: #b5295c;
            color: #ffffff;
        }

        /* Dark Toolbar Bar */
        .wppoppop-ctrl-bar {
            background: #3c434a;
            color: #ffffff;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 16px;
        }
        .wppoppop-ctrl-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .wppoppop-gear-btn {
            background: transparent;
            border: none;
            color: #ffffff;
            font-size: 22px;
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
        }
        .wppoppop-slug-box {
            background: #2c3338;
            border: 1px solid #4a525d;
            border-radius: 3px;
            color: #ffffff;
            padding: 5px 12px;
            font-size: 13px;
            width: 240px;
        }

        /* Pink Save Button */
        .wppoppop-btn-save {
            background: #b5295c;
            color: #ffffff;
            border: none;
            padding: 14px 28px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            height: 48px;
            margin: -8px -16px -8px 0;
            transition: background 0.15s ease;
        }
        .wppoppop-btn-save:hover {
            background: #961b48;
        }

        /* Multi-Page Tabs */
        .wppoppop-tabs-bar {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            padding: 0 20px;
        }
        .wppoppop-tab-item {
            background: transparent;
            border: none;
            padding: 12px 20px;
            font-size: 13px;
            font-weight: 600;
            color: #64748b;
            cursor: pointer;
            border-bottom: 2px solid transparent;
        }
        .wppoppop-tab-item.active {
            color: #b5295c;
            border-bottom: 2px solid #b5295c;
        }
        .wppoppop-tab-item.add-page {
            color: #334155;
            font-weight: 700;
        }

        /* Horizontal Elements Toolbar matching 27 Green Popups icons */
        .wppoppop-ribbon {
            background: #ffffff;
            border-bottom: 1px solid #cbd5e1;
            padding: 6px 16px;
            display: flex;
            align-items: center;
            gap: 4px;
            overflow-x: auto;
        }
        .wppoppop-ribbon-btn {
            background: transparent;
            border: 1px solid transparent;
            border-radius: 3px;
            padding: 6px 8px;
            cursor: pointer;
            font-size: 15px;
            color: #334155;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 32px;
            transition: all 0.15s ease;
        }
        .wppoppop-ribbon-btn:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
            color: #b5295c;
        }

        /* Checkered Canvas Workspace */
        .wppoppop-canvas-wrap {
            flex: 1;
            position: relative;
            background: #2a2e39;
            background-image: 
                linear-gradient(45deg, #22252f 25%, transparent 25%), 
                linear-gradient(-45deg, #22252f 25%, transparent 25%), 
                linear-gradient(45deg, transparent 75%, #22252f 75%), 
                linear-gradient(-45deg, transparent 75%, #22252f 75%);
            background-size: 20px 20px;
            background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
            overflow: auto;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px;
            min-height: 520px;
        }
        .wppoppop-modal-boundary {
            position: relative;
            border: 1.5px dashed #64748b;
            background: transparent;
            box-shadow: 0 15px 45px rgba(0,0,0,0.35);
        }
        .wppoppop-modal-surface {
            position: relative;
            width: 100%;
            height: 100%;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: visible;
        }
        .wppoppop-canvas-corner-resizer {
            position: absolute;
            right: -6px;
            bottom: -6px;
            width: 12px;
            height: 12px;
            background: #b5295c;
            border: 1.5px solid #ffffff;
            cursor: se-resize;
            z-index: 1000;
        }

        /* Floating LAYERS Card matching Menu-Create Popups.png */
        .wppoppop-layers-card {
            position: absolute;
            right: 30px;
            top: 30px;
            width: 260px;
            background: #ffffff;
            border-radius: 3px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.25);
            z-index: 100;
            overflow: hidden;
            font-size: 12px;
        }
        .wppoppop-layers-header {
            background: #b5295c;
            color: #ffffff;
            padding: 8px 14px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: move;
            letter-spacing: 0.5px;
        }
        .wppoppop-layers-body {
            padding: 12px;
            color: #64748b;
            font-style: italic;
            line-height: 1.4;
            max-height: 280px;
            overflow-y: auto;
        }
        .wppoppop-layer-entry {
            font-style: normal;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 3px;
            padding: 6px 10px;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            color: #1e293b;
            font-weight: 600;
        }
        .wppoppop-layer-entry.active {
            border-color: #b5295c;
            background: #fff1f2;
            color: #b5295c;
        }

        /* Canvas Interactive Nodes */
        .wppoppop-node {
            position: absolute;
            box-sizing: border-box;
            cursor: move;
            border: 1px dashed transparent;
        }
        .wppoppop-node:hover {
            border-color: #94a3b8;
        }
        .wppoppop-node.selected {
            border: 2px solid #b5295c !important;
            box-shadow: 0 0 0 2px rgba(181, 41, 92, 0.25);
            z-index: 9999 !important;
        }
        .wppoppop-node-resizer {
            display: none;
            position: absolute;
            right: -4px;
            bottom: -4px;
            width: 8px;
            height: 8px;
            background: #b5295c;
            border: 1px solid #ffffff;
            cursor: se-resize;
        }
        .wppoppop-node.selected .wppoppop-node-resizer {
            display: block;
        }

        /* Properties Inspector Drawer */
        .wppoppop-props-drawer {
            position: absolute;
            right: 310px;
            top: 30px;
            width: 290px;
            background: #ffffff;
            border-radius: 3px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.25);
            z-index: 100;
            overflow: hidden;
            display: none;
        }
        .wppoppop-props-header {
            background: #2c3338;
            color: #ffffff;
            padding: 8px 14px;
            font-size: 11px;
            font-weight: 700;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .wppoppop-props-body {
            padding: 14px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            max-height: 480px;
            overflow-y: auto;
            font-size: 12px;
        }
        .wppoppop-props-body label {
            font-weight: 600;
            color: #475569;
            font-size: 11px;
        }
        .wppoppop-props-input {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 3px;
            padding: 5px 8px;
            font-size: 12px;
        }
        </style>

        <div id="wppoppop-builder-app" class="wppoppop-builder-app">
            <!-- 1. Header Bar -->
            <div class="wppoppop-hdr">
                <h2><?php echo $popup_id ? esc_html__('Green Popups - Edit Popup', 'wppoppop') : esc_html__('Green Popups - Create Popup', 'wppoppop'); ?></h2>
                <div class="wppoppop-hdr-actions">
                    <a href="<?php echo esc_url(admin_url('admin.php?page=wppoppop-add')); ?>" class="wppoppop-btn-outline"><?php echo esc_html__('Create New Popup', 'wppoppop'); ?></a>
                    <a href="https://greenpopups.com/documentation/" target="_blank" rel="noopener noreferrer" class="wppoppop-btn-outline"><?php echo esc_html__('Online Documentation', 'wppoppop'); ?></a>
                </div>
            </div>

            <!-- 2. Dark Control Bar -->
            <div class="wppoppop-ctrl-bar">
                <div class="wppoppop-ctrl-left">
                    <button type="button" class="wppoppop-gear-btn" id="wppoppop-btn-gear" title="Popup Settings">
                        <span class="dashicons dashicons-admin-generic"></span>
                    </button>
                    <input type="text" id="wppoppop-slug" class="wppoppop-slug-box" value="<?php echo esc_attr($title); ?>" />
                </div>
                <button type="button" class="wppoppop-btn-save" id="wppoppop-btn-save">
                    <span class="dashicons dashicons-media-default" style="font-size:16px;"></span> <?php echo esc_html__('Save', 'wppoppop'); ?>
                </button>
            </div>

            <!-- 3. Multi-Page Tabs -->
            <div class="wppoppop-tabs-bar">
                <button type="button" class="wppoppop-tab-item active" data-page="1"><?php echo esc_html__('Page', 'wppoppop'); ?></button>
                <button type="button" class="wppoppop-tab-item" data-page="confirm"><?php echo esc_html__('Confirmation', 'wppoppop'); ?></button>
                <button type="button" class="wppoppop-tab-item add-page">+ <?php echo esc_html__('Add Page', 'wppoppop'); ?></button>
            </div>

            <!-- 4. 27-Element Green Popups Ribbon Toolbar -->
            <div class="wppoppop-ribbon">
                <button type="button" class="wppoppop-ribbon-btn" data-type="box" title="Box / Container"><span class="dashicons dashicons-screenoptions"></span></button>
                <button type="button" class="wppoppop-ribbon-btn" data-type="image" title="Image"><span class="dashicons dashicons-format-image"></span></button>
                <button type="button" class="wppoppop-ribbon-btn" data-type="video" title="Video Embed"><span class="dashicons dashicons-video-alt3"></span></button>
                <button type="button" class="wppoppop-ribbon-btn" data-type="text" title="Text / Heading"><strong style="font-size:16px;">A</strong></button>
                <button type="button" class="wppoppop-ribbon-btn" data-type="html" title="Custom HTML"><span class="dashicons dashicons-editor-code"></span></button>
                <button type="button" class="wppoppop-ribbon-btn" data-type="close" title="Close Button"><span class="dashicons dashicons-no-alt"></span></button>
                <button type="button" class="wppoppop-ribbon-btn" data-type="ribbon" title="Badge Ribbon"><span class="dashicons dashicons-tag"></span></button>
                <button type="button" class="wppoppop-ribbon-btn" data-type="divider" title="Divider / Spacer"><span class="dashicons dashicons-minus"></span></button>
                <button type="button" class="wppoppop-ribbon-btn" data-type="link" title="Hyperlink / Button"><span class="dashicons dashicons-admin-links"></span></button>
                <button type="button" class="wppoppop-ribbon-btn" data-type="input" title="Text Input"><span class="dashicons dashicons-edit"></span></button>
                <button type="button" class="wppoppop-ribbon-btn" data-type="email" title="Email Field"><span class="dashicons dashicons-email"></span></button>
                <button type="button" class="wppoppop-ribbon-btn" data-type="calc" title="Math Expression (42)"><strong>42</strong></button>
                <button type="button" class="wppoppop-ribbon-btn" data-type="rangeslider" title="Numeric Stepper / Slider"><span class="dashicons dashicons-leftright"></span></button>
                <button type="button" class="wppoppop-ribbon-btn" data-type="textarea" title="Multiline Textarea"><span class="dashicons dashicons-editor-justify"></span></button>
                <button type="button" class="wppoppop-ribbon-btn" data-type="dropdown" title="Dropdown Select"><span class="dashicons dashicons-arrow-down-alt2"></span></button>
                <button type="button" class="wppoppop-ribbon-btn" data-type="checkbox" title="Checkbox"><span class="dashicons dashicons-yes"></span></button>
                <button type="button" class="wppoppop-ribbon-btn" data-type="radio" title="Radio Button"><span class="dashicons dashicons-marker"></span></button>
                <button type="button" class="wppoppop-ribbon-btn" data-type="calendar" title="Datepicker"><span class="dashicons dashicons-calendar-alt"></span></button>
                <button type="button" class="wppoppop-ribbon-btn" data-type="clock" title="Countdown Timer"><span class="dashicons dashicons-clock"></span></button>
                <button type="button" class="wppoppop-ribbon-btn" data-type="upload" title="File Upload"><span class="dashicons dashicons-upload"></span></button>
                <button type="button" class="wppoppop-ribbon-btn" data-type="lock" title="Content Locker"><span class="dashicons dashicons-lock"></span></button>
                <button type="button" class="wppoppop-ribbon-btn" data-type="star" title="Rating Stars"><span class="dashicons dashicons-star-filled"></span></button>
                <button type="button" class="wppoppop-ribbon-btn" data-type="wheel" title="Fortune Wheel"><span class="dashicons dashicons-update"></span></button>
                <button type="button" class="wppoppop-ribbon-btn" data-type="signature" title="Signature Pad"><span class="dashicons dashicons-art"></span></button>
                <button type="button" class="wppoppop-ribbon-btn" data-type="submit" title="Submit Action"><span class="dashicons dashicons-arrow-right-alt"></span></button>
            </div>

            <!-- 5. Construction Workspace -->
            <div class="wppoppop-canvas-wrap">
                <div class="wppoppop-modal-boundary" id="wppoppop-modal-boundary" style="width:<?php echo esc_attr($canvas['width']); ?>px;height:<?php echo esc_attr($canvas['height']); ?>px;">
                    <div class="wppoppop-modal-surface" id="wppoppop-modal-surface" style="background-color:<?php echo esc_attr($canvas['bgColor']); ?>;"></div>
                    <div class="wppoppop-canvas-corner-resizer" id="wppoppop-canvas-resizer"></div>
                </div>

                <!-- Floating LAYERS Card -->
                <div class="wppoppop-layers-card" id="wppoppop-layers-card">
                    <div class="wppoppop-layers-header">
                        <span>LAYERS</span>
                        <span class="dashicons dashicons-move"></span>
                    </div>
                    <div class="wppoppop-layers-body" id="wppoppop-layers-body">
                        1. Click any button on elements toolbar to add new layer. 2. Sort layers to change z-index.
                    </div>
                </div>

                <!-- Element Properties Drawer -->
                <div class="wppoppop-props-drawer" id="wppoppop-props-drawer">
                    <div class="wppoppop-props-header">
                        <span id="wppoppop-props-title">PROPERTIES</span>
                        <span class="dashicons dashicons-no" id="wppoppop-props-close" style="cursor:pointer;"></span>
                    </div>
                    <div class="wppoppop-props-body">
                        <div><label>Content / Label:</label><input type="text" id="prop-val-content" class="wppoppop-props-input" /></div>
                        <div><label>Placeholder:</label><input type="text" id="prop-val-placeholder" class="wppoppop-props-input" /></div>
                        <div><label>CRM Field Slug:</label><input type="text" id="prop-val-slug" class="wppoppop-props-input" /></div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                            <div><label>X (Left):</label><input type="number" id="prop-val-x" class="wppoppop-props-input" /></div>
                            <div><label>Y (Top):</label><input type="number" id="prop-val-y" class="wppoppop-props-input" /></div>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                            <div><label>Width:</label><input type="number" id="prop-val-w" class="wppoppop-props-input" /></div>
                            <div><label>Height:</label><input type="number" id="prop-val-h" class="wppoppop-props-input" /></div>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                            <div><label>Font Size:</label><input type="number" id="prop-val-fontsize" class="wppoppop-props-input" /></div>
                            <div><label>Radius:</label><input type="number" id="prop-val-radius" class="wppoppop-props-input" /></div>
                        </div>
                        <div><label>Text Color:</label><input type="text" id="prop-val-color" class="wppoppop-props-input" /></div>
                        <div><label>Background Color:</label><input type="text" id="prop-val-bgcolor" class="wppoppop-props-input" /></div>
                        <div style="margin-top:10px;display:flex;justify-content:space-between;">
                            <button type="button" class="wppoppop-btn-outline" id="prop-btn-del" style="color:#dc2626;border-color:#dc2626;">Delete</button>
                            <button type="button" class="wppoppop-btn-outline" id="prop-btn-apply" style="background:#b5295c;color:#fff;border-color:#b5295c;">Apply</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
        (function() {
            var layers = <?php echo wp_json_encode($layers); ?> || [];
            var canvas = <?php echo wp_json_encode($canvas); ?> || { width: 640, height: 440, bgColor: '#ffffff' };
            var selectedIdx = -1;
            var popupId = <?php echo $popup_id; ?>;
            var ajaxUrl = "<?php echo esc_url(admin_url('admin-ajax.php')); ?>";
            var nonce   = "<?php echo wp_create_nonce('wppoppop_admin_nonce'); ?>";

            function render() {
                var surface = document.getElementById('wppoppop-modal-surface');
                var list = document.getElementById('wppoppop-layers-body');
                surface.innerHTML = '';

                if (layers.length === 0) {
                    list.innerHTML = '1. Click any button on elements toolbar to add new layer. 2. Sort layers to change z-index.';
                } else {
                    list.innerHTML = '';
                }

                layers.forEach(function(l, i) {
                    // 1. Create Canvas Node
                    var node = document.createElement('div');
                    node.className = 'wppoppop-node' + (i === selectedIdx ? ' selected' : '');
                    node.style.left = l.x + 'px';
                    node.style.top  = l.y + 'px';
                    node.style.width = l.w + 'px';
                    node.style.height = l.h + 'px';
                    node.style.zIndex = l.zIndex || (i + 1);
                    node.style.color = l.color || '#1e293b';
                    node.style.backgroundColor = l.bgColor || 'transparent';
                    node.style.fontSize = (l.fontSize || 14) + 'px';
                    node.style.borderRadius = (l.borderRadius || 0) + 'px';

                    if (l.type === 'text') node.innerHTML = l.content || 'Text Heading';
                    else if (l.type === 'input' || l.type === 'email') node.innerHTML = '<input type="text" readonly placeholder="' + (l.placeholder || 'Enter value...') + '" style="width:100%;height:100%;border:none;background:transparent;padding:0 8px;pointer-events:none;">';
                    else if (l.type === 'submit') node.innerHTML = '<button type="button" style="width:100%;height:100%;background:#b5295c;color:#fff;border:none;border-radius:4px;font-weight:bold;pointer-events:none;">' + (l.content || 'Submit') + '</button>';
                    else if (l.type === 'image') node.innerHTML = '<img src="' + (l.imageSrc || 'https://via.placeholder.com/260x140?text=Image') + '" style="width:100%;height:100%;object-fit:cover;pointer-events:none;">';
                    else if (l.type === 'wheel') node.innerHTML = '<div style="width:100%;height:100%;border-radius:50%;background:radial-gradient(circle, #b5295c 30%, #1e293b 80%);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:bold;">🎡 SPIN</div>';
                    else if (l.type === 'clock') node.innerHTML = '<div style="width:100%;height:100%;background:#1e293b;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:bold;">14 : 59</div>';
                    else if (l.type === 'box') node.style.border = '1px solid #cbd5e1';

                    var resizer = document.createElement('div');
                    resizer.className = 'wppoppop-node-resizer';
                    node.appendChild(resizer);

                    node.addEventListener('mousedown', function(e) {
                        if (e.target === resizer) return;
                        selectLayer(i);
                        dragNode(e, i, node);
                    });

                    resizer.addEventListener('mousedown', function(e) {
                        e.stopPropagation();
                        resizeNode(e, i, node);
                    });

                    surface.appendChild(node);

                    // 2. Append to LAYERS List
                    var item = document.createElement('div');
                    item.className = 'wppoppop-layer-entry' + (i === selectedIdx ? ' active' : '');
                    item.textContent = (l.content || l.type).substring(0, 20);
                    item.addEventListener('click', function() { selectLayer(i); });
                    list.appendChild(item);
                });
            }

            function selectLayer(i) {
                selectedIdx = i;
                render();
                var drawer = document.getElementById('wppoppop-props-drawer');
                if (i >= 0 && i < layers.length) {
                    drawer.style.display = 'block';
                    var l = layers[i];
                    document.getElementById('wppoppop-props-title').textContent = 'PROPERTIES: ' + l.type.toUpperCase();
                    document.getElementById('prop-val-content').value = l.content || '';
                    document.getElementById('prop-val-placeholder').value = l.placeholder || '';
                    document.getElementById('prop-val-slug').value = l.fieldName || '';
                    document.getElementById('prop-val-x').value = l.x;
                    document.getElementById('prop-val-y').value = l.y;
                    document.getElementById('prop-val-w').value = l.w;
                    document.getElementById('prop-val-h').value = l.h;
                    document.getElementById('prop-val-fontsize').value = l.fontSize || 14;
                    document.getElementById('prop-val-radius').value = l.borderRadius || 0;
                    document.getElementById('prop-val-color').value = l.color || '#1e293b';
                    document.getElementById('prop-val-bgcolor').value = l.bgColor || 'transparent';
                } else {
                    drawer.style.display = 'none';
                }
            }

            function dragNode(e, i, node) {
                var startX = e.pageX - layers[i].x;
                var startY = e.pageY - layers[i].y;
                function onMove(me) {
                    var nx = Math.max(0, Math.min(me.pageX - startX, canvas.width - layers[i].w));
                    var ny = Math.max(0, Math.min(me.pageY - startY, canvas.height - layers[i].h));
                    layers[i].x = nx;
                    layers[i].y = ny;
                    node.style.left = nx + 'px';
                    node.style.top = ny + 'px';
                    document.getElementById('prop-val-x').value = nx;
                    document.getElementById('prop-val-y').value = ny;
                }
                function onUp() {
                    window.removeEventListener('mousemove', onMove);
                    window.removeEventListener('mouseup', onUp);
                }
                window.addEventListener('mousemove', onMove);
                window.addEventListener('mouseup', onUp);
            }

            function resizeNode(e, i, node) {
                var startX = e.pageX;
                var startY = e.pageY;
                var sw = layers[i].w;
                var sh = layers[i].h;
                function onMove(me) {
                    var nw = Math.max(20, sw + (me.pageX - startX));
                    var nh = Math.max(16, sh + (me.pageY - startY));
                    layers[i].w = nw;
                    layers[i].h = nh;
                    node.style.width = nw + 'px';
                    node.style.height = nh + 'px';
                    document.getElementById('prop-val-w').value = nw;
                    document.getElementById('prop-val-h').value = nh;
                }
                function onUp() {
                    window.removeEventListener('mousemove', onMove);
                    window.removeEventListener('mouseup', onUp);
                }
                window.addEventListener('mousemove', onMove);
                window.addEventListener('mouseup', onUp);
            }

            // Ribbon Element Click
            document.querySelectorAll('.wppoppop-ribbon-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var type = this.getAttribute('data-type');
                    var newLayer = {
                        type: type,
                        x: Math.round(canvas.width / 2) - 100,
                        y: Math.round(canvas.height / 2) - 25,
                        w: 200,
                        h: 46,
                        content: '',
                        placeholder: '',
                        fieldName: '',
                        color: '#1e293b',
                        bgColor: 'transparent',
                        borderRadius: 4,
                        fontSize: 14
                    };
                    if (type === 'text') { newLayer.content = 'Special Offer Headline'; newLayer.fontSize = 22; newLayer.h = 36; newLayer.w = 260; }
                    else if (type === 'input' || type === 'email') { newLayer.placeholder = type === 'email' ? 'Enter email...' : 'Enter name...'; newLayer.fieldName = type === 'email' ? 'email' : 'name'; newLayer.bgColor = '#ffffff'; newLayer.borderRadius = 6; }
                    else if (type === 'submit') { newLayer.content = 'Claim Discount'; newLayer.bgColor = '#b5295c'; newLayer.color = '#ffffff'; }
                    else if (type === 'wheel') { newLayer.w = 220; newLayer.h = 220; }
                    layers.push(newLayer);
                    selectLayer(layers.length - 1);
                });
            });

            // Properties Form Listeners
            document.getElementById('prop-btn-apply').addEventListener('click', function() {
                if (selectedIdx >= 0) {
                    var l = layers[selectedIdx];
                    l.content = document.getElementById('prop-val-content').value;
                    l.placeholder = document.getElementById('prop-val-placeholder').value;
                    l.fieldName = document.getElementById('prop-val-slug').value;
                    l.x = parseInt(document.getElementById('prop-val-x').value, 10) || 0;
                    l.y = parseInt(document.getElementById('prop-val-y').value, 10) || 0;
                    l.w = parseInt(document.getElementById('prop-val-w').value, 10) || 20;
                    l.h = parseInt(document.getElementById('prop-val-h').value, 10) || 20;
                    l.fontSize = parseInt(document.getElementById('prop-val-fontsize').value, 10) || 14;
                    l.borderRadius = parseInt(document.getElementById('prop-val-radius').value, 10) || 0;
                    l.color = document.getElementById('prop-val-color').value;
                    l.bgColor = document.getElementById('prop-val-bgcolor').value;
                    render();
                }
            });

            document.getElementById('prop-btn-del').addEventListener('click', function() {
                if (selectedIdx >= 0) {
                    layers.splice(selectedIdx, 1);
                    selectLayer(-1);
                }
            });

            document.getElementById('wppoppop-props-close').addEventListener('click', function() {
                selectLayer(-1);
            });

            // Save Popup via AJAX
            document.getElementById('wppoppop-btn-save').addEventListener('click', function() {
                var btn = this;
                var origText = btn.innerHTML;
                btn.innerHTML = 'Saving...';
                btn.disabled = true;

                var data = new FormData();
                data.append('action', 'wppoppop_save_builder');
                data.append('nonce', nonce);
                data.append('popup_id', popupId);
                data.append('title', document.getElementById('wppoppop-slug').value);
                data.append('status', 'publish');
                data.append('layers', JSON.stringify(layers));
                data.append('canvas_config', JSON.stringify(canvas));

                fetch(ajaxUrl, { method: 'POST', body: data })
                    .then(function(r) { return r.json(); })
                    .then(function(res) {
                        btn.innerHTML = origText;
                        btn.disabled = false;
                        if (res.success && res.data) {
                            alert(res.data.message || 'Popup Saved!');
                            if (!popupId && res.data.popup_id) {
                                popupId = res.data.popup_id;
                                window.history.replaceState(null, '', res.data.edit_url);
                            }
                        } else {
                            alert((res.data && res.data.message) ? res.data.message : 'Error saving popup');
                        }
                    })
                    .catch(function() {
                        btn.innerHTML = origText;
                        btn.disabled = false;
                        alert('Network error while saving');
                    });
            });

            render();
        })();
        </script>
        <?php
    }

    public static function ajax_save_builder(): void {
        check_ajax_referer('wppoppop_admin_nonce', 'nonce');
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(['message' => __('Permission denied.', 'wppoppop')]);
        }

        $popup_id   = absint($_POST['popup_id'] ?? 0);
        $title      = sanitize_text_field($_POST['title'] ?? __('Untitled Popup', 'wppoppop'));
        $status     = sanitize_key($_POST['status'] ?? 'publish');
        $raw_layers = wp_unslash($_POST['layers'] ?? '[]');
        $raw_config = wp_unslash($_POST['canvas_config'] ?? '[]');

        $layers = json_decode($raw_layers, true);
        if (!is_array($layers)) $layers = [];

        $canvas_config = json_decode($raw_config, true);
        if (!is_array($canvas_config)) $canvas_config = [];

        if ($popup_id > 0) {
            wp_update_post([
                'ID'          => $popup_id,
                'post_title'  => $title,
                'post_status' => $status,
            ]);
            $saved_id = $popup_id;
        } else {
            $saved_id = wp_insert_post([
                'post_type'   => PopupPostType::POST_TYPE,
                'post_title'  => $title,
                'post_status' => $status,
                'post_name'   => sanitize_title($title),
            ]);
        }

        if (is_wp_error($saved_id) || !$saved_id) {
            wp_send_json_error(['message' => __('Failed to persist popup.', 'wppoppop')]);
        }

        update_post_meta($saved_id, '_wppoppop_builder_layers', wp_json_encode($layers));
        update_post_meta($saved_id, '_wppoppop_canvas_config', $canvas_config);
        if (!get_post_meta($saved_id, '_wppoppop_slug', true)) {
            update_post_meta($saved_id, '_wppoppop_slug', sanitize_title($title));
        }

        wp_send_json_success([
            'popup_id' => $saved_id,
            'edit_url' => admin_url('admin.php?page=wppoppop-add&id=' . $saved_id),
            'message'  => __('Popup successfully saved!', 'wppoppop'),
        ]);
    }

    public static function ajax_load_builder(): void {
        check_ajax_referer('wppoppop_admin_nonce', 'nonce');
        $popup_id = absint($_GET['popup_id'] ?? 0);
        $post = get_post($popup_id);
        if (!$post || $post->post_type !== PopupPostType::POST_TYPE) {
            wp_send_json_error(['message' => __('Popup not found.', 'wppoppop')]);
        }

        $raw_layers = get_post_meta($popup_id, '_wppoppop_builder_layers', true);
        $layers = !empty($raw_layers) ? json_decode($raw_layers, true) : [];
        $config = get_post_meta($popup_id, '_wppoppop_canvas_config', true) ?: [];

        wp_send_json_success([
            'title'         => $post->post_title,
            'status'        => $post->post_status,
            'layers'        => $layers,
            'canvas_config' => $config,
        ]);
    }
}
