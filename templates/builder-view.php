<?php
if (!defined('ABSPATH')) {
    exit;
}

$current_uid = isset($_GET['uid']) ? sanitize_key($_GET['uid']) : '';
$settings    = get_option('wppoppop_settings', []);

$feat_google_fonts     = isset($settings['google_fonts']) ? !empty($settings['google_fonts']) : true;
$feat_air_datepicker   = isset($settings['air_datepicker']) ? !empty($settings['air_datepicker']) : true;
$feat_signature_pad    = !empty($settings['signature_pad']);
$feat_range_slider     = !empty($settings['range_slider']);
$feat_adblock_detector = !empty($settings['adblock_detector']);
$feat_jquery_mask      = !empty($settings['jquery_mask']);

$custom_fonts_raw = isset($settings['custom_fonts']) ? $settings['custom_fonts'] : '';
$custom_fonts     = array_filter(array_map('trim', explode("\n", $custom_fonts_raw)));
?>
<div class="wppoppop-builder-wrap" id="wppoppop-builder-wrap">
    <?php wp_nonce_field('wppoppop_builder_nonce', 'wppoppop_builder_nonce_field'); ?>

    <!-- MAIN PUSHABLE FRAME -->
    <div class="wppoppop-main-frame">
        <!-- 1. Top Dark Control Bar -->
        <header class="wppoppop-top-bar">
            <div class="top-bar-left">
                <button type="button" class="btn-top-icon" id="wppoppop-btn-settings" title="Campaign & Popup Settings">
                    <span class="dashicons dashicons-admin-generic"></span>
                </button>
                <input type="text" id="wppoppop-popup-title" value="yes-no-3" placeholder="Popup Name...">
                <input type="hidden" id="wppoppop-popup-uid" value="<?php echo esc_attr($current_uid); ?>">
                <input type="hidden" id="wppoppop-popup-status" value="publish">
            </div>
            <div class="top-bar-right">
                <button type="button" class="btn-top-icon" id="wppoppop-btn-preview" title="Live Preview">
                    <span class="dashicons dashicons-visibility"></span>
                </button>
                <button type="button" class="btn-top-icon" id="wppoppop-btn-embed" title="Embed Shortcode & HTML">
                    <span class="dashicons dashicons-editor-code"></span>
                </button>
                <button type="button" class="btn-top-save" id="wppoppop-btn-save">
                    <span class="dashicons dashicons-saved"></span> Save
                </button>
            </div>
        </header>

        <!-- 2. Screens / Pages Tab Strip -->
        <div class="wppoppop-pages-bar">
            <div class="pages-tabs-list">
                <button type="button" class="btn-page-tab active" data-screen="1">Yes No</button>
                <button type="button" class="btn-page-tab" data-screen="2">Confirmation</button>
                <button type="button" class="btn-page-tab btn-add-page" data-screen="3"><span class="dashicons dashicons-plus-alt2"></span> Add Page</button>
            </div>
        </div>

        <!-- 3. Horizontal Elements Ribbon Toolbar -->
        <div class="wppoppop-elements-ribbon">
            <div class="ribbon-items-scroll">
                <button type="button" class="ribbon-btn" data-type="box" title="Box / Container"><span class="dashicons dashicons-screenoptions"></span></button>
                <button type="button" class="ribbon-btn" data-type="image" title="Image Block"><span class="dashicons dashicons-format-image"></span></button>
                <button type="button" class="ribbon-btn" data-type="video" title="Video Player"><span class="dashicons dashicons-video-alt3"></span></button>
                <button type="button" class="ribbon-btn" data-type="text" title="Text / Heading"><span style="font-weight:800;font-size:15px;line-height:1;">A</span></button>
                <button type="button" class="ribbon-btn" data-type="html" title="Raw HTML Code"><span class="dashicons dashicons-editor-code"></span></button>
                <button type="button" class="ribbon-btn" data-type="close" title="Close Icon (X)"><span class="dashicons dashicons-no-alt"></span></button>
                <button type="button" class="ribbon-btn" data-type="nextstep" title="Next Page / Step Button"><span class="dashicons dashicons-arrow-right-alt"></span></button>
                <button type="button" class="ribbon-btn" data-type="divider" title="Divider / Line"><span class="dashicons dashicons-minus"></span></button>
                <button type="button" class="ribbon-btn" data-type="link" title="Hyperlink Button"><span class="dashicons dashicons-admin-links"></span></button>
                <button type="button" class="ribbon-btn" data-type="textarea" title="Textarea Field"><span class="dashicons dashicons-edit"></span></button>
                <button type="button" class="ribbon-btn" data-type="input" title="Email / Input Field"><span class="dashicons dashicons-email-alt"></span></button>
                <button type="button" class="ribbon-btn" data-type="number" title="Number Field"><span style="font-weight:700;font-size:12px;">42</span></button>
                
                <?php if ($feat_range_slider): ?>
                    <button type="button" class="ribbon-btn" data-type="slider" title="Range Slider"><span class="dashicons dashicons-leftright"></span></button>
                <?php endif; ?>

                <button type="button" class="ribbon-btn" data-type="dropdown" title="Select Dropdown"><span class="dashicons dashicons-menu-alt"></span></button>
                <button type="button" class="ribbon-btn" data-type="checkbox" title="Checkbox"><span class="dashicons dashicons-yes"></span></button>
                <button type="button" class="ribbon-btn" data-type="radio" title="Radio Options"><span class="dashicons dashicons-marker"></span></button>
                <button type="button" class="ribbon-btn" data-type="list" title="Bullet List"><span class="dashicons dashicons-editor-ul"></span></button>
                <button type="button" class="ribbon-btn" data-type="gallery" title="Image Gallery"><span class="dashicons dashicons-images-alt2"></span></button>
                <button type="button" class="ribbon-btn" data-type="coupon" title="Coupon / Badge"><span style="font-size:10px;font-weight:800;border:1px solid currentColor;padding:1px 3px;border-radius:2px;">tile</span></button>

                <?php if ($feat_air_datepicker): ?>
                    <button type="button" class="ribbon-btn" data-type="date" title="Date Picker"><span class="dashicons dashicons-calendar-alt"></span></button>
                <?php endif; ?>

                <button type="button" class="ribbon-btn" data-type="countdown" title="Countdown Timer"><span class="dashicons dashicons-clock"></span></button>
                <button type="button" class="ribbon-btn" data-type="file" title="File Upload"><span class="dashicons dashicons-upload"></span></button>
                <button type="button" class="ribbon-btn" data-type="rating" title="Star Rating"><span class="dashicons dashicons-star-filled"></span></button>
                <button type="button" class="ribbon-btn" data-type="button" title="Submit Button"><span class="dashicons dashicons-share-alt2"></span></button>
                <button type="button" class="ribbon-btn" data-type="wheel" title="Lucky Wheel"><span class="dashicons dashicons-chart-pie"></span></button>
                <button type="button" class="ribbon-btn" data-type="scratch" title="Scratch Card"><span class="dashicons dashicons-tickets-alt"></span></button>
                <button type="button" class="ribbon-btn" data-type="progress" title="Progress Bar"><span class="dashicons dashicons-ellipsis"></span></button>
                
                <?php if ($feat_signature_pad): ?>
                    <button type="button" class="ribbon-btn" data-type="signature" title="Signature Pad"><span class="dashicons dashicons-welcome-write-blog"></span></button>
                <?php endif; ?>

                <button type="button" class="ribbon-btn" data-type="pay_btn" title="Payment Button"><span class="dashicons dashicons-cart"></span></button>
            </div>
        </div>

        <!-- 4. Canvas Viewport with Alignment Toolbar & Direct Stage Resizing -->
        <main class="wppoppop-canvas-viewport">
            <div class="canvas-alignment-toolbar">
                <button type="button" class="button button-small" id="btn-undo" title="Undo (Ctrl+Z)"><span class="dashicons dashicons-undo"></span> Undo</button>
                <button type="button" class="button button-small" id="btn-redo" title="Redo (Ctrl+Y)"><span class="dashicons dashicons-redo"></span> Redo</button>
                <span class="toolbar-sep">|</span>
                <label style="font-size:12px;cursor:pointer;"><input type="checkbox" id="chk-grid-snap" checked> 10px Grid Snap</label>
                <span class="toolbar-sep">|</span>
                <button type="button" class="button button-small btn-align" data-align="left">Left</button>
                <button type="button" class="button button-small btn-align" data-align="center-h">Center H</button>
                <button type="button" class="button button-small btn-align" data-align="right">Right</button>
                <span class="toolbar-sep">|</span>
                <button type="button" class="button button-small btn-align" data-align="top">Top</button>
                <button type="button" class="button button-small btn-align" data-align="center-v">Center V</button>
                <button type="button" class="button button-small btn-align" data-align="bottom">Bottom</button>
            </div>

            <!-- Resizable Stage Canvas with Live Dimension Badge -->
            <div class="wppoppop-stage" id="wppoppop-stage" style="width: 620px; height: 380px;">
                <!-- Canvas elements render here -->
                <div class="stage-dimension-badge" id="stage-dimension-badge">
                    <span class="dashicons dashicons-editor-expand"></span> <span class="badge-text">620 &times; 380 px</span>
                </div>
            </div>

            <!-- Floating Magenta LAYERS Panel -->
            <aside class="wppoppop-floating-layers" id="wppoppop-floating-layers">
                <div class="layers-header">
                    <span class="layers-title">LAYERS</span>
                    <span class="dashicons dashicons-move layers-drag-handle" title="Move Layers Box"></span>
                </div>
                <ul id="wppoppop-layers-list" class="layers-list">
                    <!-- Populated dynamically -->
                </ul>
                <div class="layers-footer-hint">
                    1. Click any button on elements toolbar to add new layer. 2. Sort layers to change z-index.
                </div>
            </aside>
        </main>
    </div>

    <!-- 5. SLIDE-IN PROPERTY PANEL PUSHING FRAME -->
    <aside class="wppoppop-inspector-panel" id="wppoppop-inspector-panel">
        <div class="inspector-tabs-header">
            <button type="button" class="btn-inspector-close" id="btn-close-inspector" title="Close Properties">
                <span class="dashicons dashicons-no-alt"></span>
            </button>
            <button type="button" class="inspector-tab-btn active" data-tab="insp-tab-basic">Basic</button>
            <button type="button" class="inspector-tab-btn" data-tab="insp-tab-style">Style</button>
            <button type="button" class="inspector-tab-btn" data-tab="insp-tab-logic">Logic</button>
        </div>

        <div class="inspector-tabs-content">
            <!-- TAB: Basic -->
            <div id="insp-tab-basic" class="inspector-tab-pane active">
                <div class="form-group">
                    <label class="form-label-caps">NAME</label>
                    <input type="text" id="prop-layer-name" class="prop-input-full" placeholder="Layer Name">
                </div>

                <div class="form-group">
                    <label class="form-label-caps">POSITION <span class="help-badge" title="Position relative to canvas stage">?</span></label>
                    <div class="form-row-duo">
                        <div class="input-with-sublabel">
                            <div class="input-with-suffix">
                                <input type="number" id="prop-pos-top" value="0">
                                <span class="input-suffix">px</span>
                            </div>
                            <span class="sublabel">Top</span>
                        </div>
                        <div class="input-with-sublabel">
                            <div class="input-with-suffix">
                                <input type="number" id="prop-pos-left" value="0">
                                <span class="input-suffix">px</span>
                            </div>
                            <span class="sublabel">Left</span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label-caps">SIZE <span class="help-badge" title="Element width and height">?</span></label>
                    <div class="form-row-duo">
                        <div class="input-with-sublabel">
                            <div class="input-with-suffix">
                                <input type="number" id="prop-size-width" value="400">
                                <span class="input-suffix">px</span>
                            </div>
                            <span class="sublabel">Width</span>
                        </div>
                        <div class="input-with-sublabel">
                            <div class="input-with-suffix">
                                <input type="number" id="prop-size-height" value="240">
                                <span class="input-suffix">px</span>
                            </div>
                            <span class="sublabel">Height</span>
                        </div>
                    </div>
                </div>

                <!-- Image Controls Block -->
                <div class="form-group" id="group-prop-image" style="display:none;">
                    <label class="form-label-caps">IMAGE <span class="help-badge" title="Image source URL or media asset">?</span></label>
                    <div class="input-with-icon-btn">
                        <input type="text" id="prop-image-url" placeholder="http://... image URL">
                        <button type="button" id="btn-prop-media-picker" class="btn-input-addon" title="Choose from Media Library">
                            <span class="dashicons dashicons-format-image"></span>
                        </button>
                    </div>
                    <span class="sublabel">Image URL</span>

                    <div class="image-controls-grid" style="margin-top:8px;">
                        <div class="ctrl-subgroup">
                            <div class="btn-group-segmented" id="group-img-size">
                                <button type="button" class="btn-seg active" data-val="auto">Auto</button>
                                <button type="button" class="btn-seg" data-val="contain" title="Fit"><span class="dashicons dashicons-fullscreen-alt"></span></button>
                                <button type="button" class="btn-seg" data-val="cover" title="Fill"><span class="dashicons dashicons-editor-expand"></span></button>
                            </div>
                            <span class="sublabel">Size</span>
                        </div>
                        <div class="ctrl-subgroup">
                            <div class="btn-group-segmented" id="group-img-pos-h">
                                <button type="button" class="btn-seg" data-val="left"><span class="dashicons dashicons-arrow-left-alt"></span></button>
                                <button type="button" class="btn-seg active" data-val="center"><span class="dashicons dashicons-marker"></span></button>
                                <button type="button" class="btn-seg" data-val="right"><span class="dashicons dashicons-arrow-right-alt"></span></button>
                            </div>
                            <span class="sublabel">Horizontal position</span>
                        </div>
                    </div>

                    <div class="image-controls-grid" style="margin-top:8px;">
                        <div class="ctrl-subgroup">
                            <div class="btn-group-segmented" id="group-img-pos-v">
                                <button type="button" class="btn-seg" data-val="top"><span class="dashicons dashicons-arrow-up-alt"></span></button>
                                <button type="button" class="btn-seg active" data-val="center"><span class="dashicons dashicons-marker"></span></button>
                                <button type="button" class="btn-seg" data-val="bottom"><span class="dashicons dashicons-arrow-down-alt"></span></button>
                            </div>
                            <span class="sublabel">Vertical position</span>
                        </div>
                        <div class="ctrl-subgroup">
                            <div class="btn-group-segmented" id="group-img-repeat">
                                <button type="button" class="btn-seg" data-val="repeat"><span class="dashicons dashicons-move"></span></button>
                                <button type="button" class="btn-seg" data-val="repeat-x">&harr;</button>
                                <button type="button" class="btn-seg" data-val="repeat-y">&#8597;</button>
                                <button type="button" class="btn-seg active" data-val="no-repeat">No</button>
                            </div>
                            <span class="sublabel">Repeat</span>
                        </div>
                    </div>
                </div>

                <!-- Video Controls Block -->
                <div class="form-group" id="group-prop-video" style="display:none;">
                    <label class="form-label-caps">VIDEO SOURCE URL <span class="help-badge" title="YouTube, Vimeo, or MP4 URL">?</span></label>
                    <input type="text" id="prop-video-url" class="prop-input-full" placeholder="https://www.youtube.com/watch?v=...">
                    <span class="sublabel">Embed URL or direct MP4 stream</span>
                </div>

                <!-- Content & Label Block -->
                <div class="form-group" id="group-prop-content">
                    <label class="form-label-caps">CONTENT / TEXT / HTML <span class="help-badge" title="Textual label or HTML">?</span></label>
                    <textarea id="prop-content" rows="3" class="prop-input-full"></textarea>
                    <span class="sublabel">Dynamic tags supported: {coupon_code}, {name}, {prize}</span>
                </div>

                <!-- Input Binding Key -->
                <div class="form-group" id="group-prop-field-name">
                    <label class="form-label-caps">FIELD BINDING KEY <span class="help-badge" title="Parameter name passed to submissions">?</span></label>
                    <input type="text" id="prop-field-name" class="prop-input-full" placeholder="e.g. email, phone, quantity">
                    <span class="sublabel">Passed to webhooks, autoresponders & emails</span>
                </div>

                <!-- Slices & Options -->
                <div class="form-group" id="group-prop-options" style="display:none;">
                    <label class="form-label-caps">OPTIONS / SLICES <span class="help-badge" title="Comma-separated items">?</span></label>
                    <input type="text" id="prop-options" class="prop-input-full">
                    <span class="sublabel">Separate items with commas</span>
                </div>

                <!-- Range Slider Parameters -->
                <div class="form-group" id="group-prop-slider" style="display:none;">
                    <label class="form-label-caps">SLIDER CONFIGURATION</label>
                    <div class="form-row-duo">
                        <div class="input-with-sublabel">
                            <input type="number" id="prop-slider-min" value="0" class="prop-input-full">
                            <span class="sublabel">Min</span>
                        </div>
                        <div class="input-with-sublabel">
                            <input type="number" id="prop-slider-max" value="100" class="prop-input-full">
                            <span class="sublabel">Max</span>
                        </div>
                        <div class="input-with-sublabel">
                            <input type="number" id="prop-slider-step" value="1" class="prop-input-full">
                            <span class="sublabel">Step</span>
                        </div>
                    </div>
                </div>

                <!-- Countdown Timer Minutes -->
                <div class="form-group" id="group-prop-countdown" style="display:none;">
                    <label class="form-label-caps">COUNTDOWN DURATION (MINUTES)</label>
                    <input type="number" id="prop-countdown-minutes" value="15" min="1" max="1440" class="prop-input-full">
                </div>

                <div class="form-group">
                    <label class="form-label-caps">URL <span class="help-badge" title="Hyperlink target">?</span></label>
                    <input type="text" id="prop-url" class="prop-input-full" placeholder="https://...">
                </div>

                <div class="form-group">
                    <label class="form-label-caps">OPEN LINK IN NEW TAB <span class="help-badge" title="Target _blank attribute">?</span></label>
                    <label class="toggle-switch">
                        <input type="checkbox" id="prop-target-blank">
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="form-group">
                    <label class="form-label-caps">CLOSE <span class="help-badge" title="Dismissal action on click">?</span></label>
                    <div class="btn-group-segmented" id="group-prop-close-action">
                        <button type="button" class="btn-seg active" data-val="none">None</button>
                        <button type="button" class="btn-seg" data-val="close">Just close</button>
                        <button type="button" class="btn-seg" data-val="close_period">Close for period</button>
                        <button type="button" class="btn-seg" data-val="close_forever">Close forever</button>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label-caps">ONCLICK <span class="help-badge" title="Custom JavaScript callback">?</span></label>
                    <input type="text" id="prop-onclick" class="prop-input-full" placeholder="e.g. console.log('clicked')">
                </div>

                <div class="form-group">
                    <label class="form-label-caps">ANIMATION <span class="help-badge" title="Entrance and exit transition effects">?</span></label>
                    <select id="prop-anim-appear" class="prop-input-full">
                        <option value="fade" selected>Fade</option>
                        <option value="slideDown">Slide Down</option>
                        <option value="zoomIn">Zoom In</option>
                        <option value="bounceIn">Bounce In</option>
                        <option value="none">None (Instant)</option>
                    </select>
                    <span class="sublabel">Appearance</span>

                    <div class="form-row-duo" style="margin-top:6px;">
                        <div class="input-with-sublabel">
                            <div class="input-with-suffix">
                                <input type="number" id="prop-anim-duration" value="1000">
                                <span class="input-suffix">ms</span>
                            </div>
                            <span class="sublabel">Duration</span>
                        </div>
                        <div class="input-with-sublabel">
                            <div class="input-with-suffix">
                                <input type="number" id="prop-anim-delay" value="0">
                                <span class="input-suffix">ms</span>
                            </div>
                            <span class="sublabel">Start delay</span>
                        </div>
                    </div>

                    <div style="margin-top:6px;">
                        <select id="prop-anim-disappear" class="prop-input-full">
                            <option value="fade" selected>Fade</option>
                            <option value="slideUp">Slide Up</option>
                            <option value="zoomOut">Zoom Out</option>
                            <option value="none">None (Instant)</option>
                        </select>
                        <span class="sublabel">Disappearance</span>
                    </div>
                </div>
            </div>

            <!-- TAB: Style -->
            <div id="insp-tab-style" class="inspector-tab-pane">
                <div class="form-group">
                    <label class="form-label-caps">FONT FAMILY</label>
                    <select id="prop-font-family" class="prop-input-full">
                        <option value="Inherit">Inherit Theme Font</option>
                        <option value="Arial, sans-serif">Arial</option>
                        <option value="Georgia, serif">Georgia</option>
                        <option value="'Times New Roman', serif">Times New Roman</option>
                        <?php if ($feat_google_fonts): ?>
                            <option value="Inter">Inter</option>
                            <option value="Roboto">Roboto</option>
                            <option value="Montserrat">Montserrat</option>
                            <option value="Poppins">Poppins</option>
                        <?php endif; ?>
                        <?php if (!empty($custom_fonts)): foreach ($custom_fonts as $cf): ?>
                            <option value="<?php echo esc_attr($cf); ?>"><?php echo esc_html($cf); ?></option>
                        <?php endforeach; endif; ?>
                    </select>
                </div>
                <div class="form-row-duo">
                    <div class="form-group" style="flex:1;">
                        <label class="form-label-caps">FONT SIZE (PX)</label>
                        <input type="number" id="prop-font-size" value="16" min="8" max="72" class="prop-input-full">
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label class="form-label-caps">CORNER RADIUS</label>
                        <input type="number" id="prop-border-radius" value="4" min="0" max="100" class="prop-input-full">
                    </div>
                </div>
                <div class="form-row-duo">
                    <div class="form-group" style="flex:1;">
                        <label class="form-label-caps">TEXT COLOR</label>
                        <input type="color" id="prop-color" value="#ffffff" style="width:100%;height:32px;">
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label class="form-label-caps">BG COLOR</label>
                        <input type="color" id="prop-bg-color" value="#000000" style="width:100%;height:32px;">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label-caps">OPACITY (0.1 - 1.0)</label>
                    <input type="number" id="prop-opacity" value="1.0" min="0.1" max="1.0" step="0.1" class="prop-input-full">
                </div>
                <div class="form-group">
                    <label><input type="checkbox" id="prop-required"> Mandatory Field (Required)</label>
                    <div style="margin-top:4px;">
                        <input type="text" id="prop-error-msg" placeholder="Validation error text" value="Please complete this field." class="prop-input-full">
                    </div>
                </div>
            </div>

            <!-- TAB: Logic -->
            <div id="insp-tab-logic" class="inspector-tab-pane">
                <div class="form-group">
                    <label class="form-label-caps">CALCULATION / FORMULA</label>
                    <input type="text" id="prop-calc-formula" class="prop-input-full" placeholder="e.g. {qty} * 25">
                    <span class="sublabel">Calculates numeric field output</span>
                </div>
                <div class="form-group">
                    <label class="form-label-caps">TARGET LAYER ID FOR CALC OUTPUT</label>
                    <input type="text" id="prop-calc-target" class="prop-input-full" placeholder="e.g. elem_12345">
                </div>
                <div class="form-group">
                    <label class="form-label-caps">GOTO SCREEN ON CLICK</label>
                    <select id="prop-goto-screen" class="prop-input-full">
                        <option value="none">None (Default)</option>
                        <option value="1">Screen 1 (Yes No)</option>
                        <option value="2">Screen 2 (Confirmation)</option>
                        <option value="3">Screen 3</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="inspector-footer-actions">
            <button type="button" class="button button-secondary" id="prop-duplicate-element">Duplicate</button>
            <button type="button" class="button button-link-delete" id="prop-delete-element">Delete Layer</button>
        </div>
    </aside>

    <!-- Slide-out Campaign Settings Drawer (Left side) -->
    <aside id="wppoppop-settings-drawer" class="wppoppop-slide-drawer">
        <div class="drawer-header">
            <h3><span class="dashicons dashicons-admin-generic"></span> Campaign Settings</h3>
            <button type="button" class="btn-close-drawer" id="btn-close-settings">&times;</button>
        </div>
        <div class="drawer-content">
            <div class="wppoppop-accordion">
                <!-- 1. Box & Dimensions -->
                <div class="accordion-item active" data-accordion="backdrop">
                    <div class="accordion-header"><span>Box & Dimensions</span><span class="dashicons dashicons-arrow-down-alt2"></span></div>
                    <div class="accordion-body">
                        <div style="display:flex;gap:8px;margin-bottom:8px;">
                            <label style="flex:1;">Width (px): <input type="number" id="stage-width" value="620" class="widefat"></label>
                            <label style="flex:1;">Height (px): <input type="number" id="stage-height" value="380" class="widefat"></label>
                        </div>
                        <div class="form-group">
                            <label>Background Fill Type:</label>
                            <select id="box-fill-type" class="widefat">
                                <option value="solid">Solid Background Color</option>
                                <option value="gradient">Linear Gradient</option>
                            </select>
                        </div>
                        <div class="form-group" id="group-box-solid-color">
                            <label>Background Color:</label>
                            <input type="color" id="box-bg-color" value="#ffffff" style="width:100%;height:32px;">
                        </div>
                        <div id="group-box-gradient-controls" style="display:none;background:#0f172a;padding:8px;border-radius:4px;margin-bottom:8px;">
                            <label>Angle (deg): <input type="range" id="box-grad-angle" min="0" max="360" value="135" class="widefat"></label>
                            <div style="display:flex;gap:6px;margin-top:6px;">
                                <label style="flex:1;">Color 1: <input type="color" id="box-grad-c1" value="#1e293b" style="width:100%;height:28px;"></label>
                                <label style="flex:1;">Color 2: <input type="color" id="box-grad-c2" value="#0f172a" style="width:100%;height:28px;"></label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Stage Background Image URL:</label>
                            <input type="text" id="box-bg-image" placeholder="https://..." class="widefat">
                        </div>
                        <div class="form-group">
                            <label>Corner Radius (px):</label>
                            <input type="number" id="box-border-radius" value="4" min="0" max="40" class="widefat">
                        </div>
                        <div class="form-group">
                            <label>Box Shadow Preset:</label>
                            <select id="box-shadow" class="widefat">
                                <option value="deep">Deep Floating (0 25px 50px)</option>
                                <option value="subtle">Subtle Elevation</option>
                                <option value="glow">Neon Halo Glow</option>
                                <option value="none">None</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Display Mode:</label>
                            <select id="style-position-mode" class="widefat">
                                <option value="modal">Centered Modal Window</option>
                                <option value="ribbon_top">Sticky Top Ribbon</option>
                                <option value="ribbon_bottom">Sticky Bottom Ribbon</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Backdrop Blur (px):</label>
                            <input type="number" id="style-backdrop-blur" value="5" min="0" max="25" class="widefat">
                        </div>
                        <label><input type="checkbox" id="style-close-esc" checked> Close on ESC key</label><br>
                        <label><input type="checkbox" id="style-close-backdrop" checked> Close on backdrop click</label>
                    </div>
                </div>

                <!-- 2. 1-Click Theme Palettes -->
                <div class="accordion-item" data-accordion="palettes">
                    <div class="accordion-header"><span>1-Click Theme Palettes</span><span class="dashicons dashicons-arrow-down-alt2"></span></div>
                    <div class="accordion-body">
                        <div class="theme-presets-bar">
                            <button type="button" class="btn-theme-preset" data-theme="midnight" title="Midnight Slate"><span class="swatch" style="background:#1e293b;"></span> Slate</button>
                            <button type="button" class="btn-theme-preset" data-theme="emerald" title="Emerald Wealth"><span class="swatch" style="background:#064e3b;"></span> Emerald</button>
                            <button type="button" class="btn-theme-preset" data-theme="sunset" title="Sunset Rose"><span class="swatch" style="background:#881337;"></span> Sunset</button>
                            <button type="button" class="btn-theme-preset" data-theme="neon" title="Cyberpunk Neon"><span class="swatch" style="background:#ec4899;"></span> Neon</button>
                            <button type="button" class="btn-theme-preset" data-theme="clean" title="Corporate Clean"><span class="swatch" style="background:#2563eb;"></span> Clean</button>
                        </div>
                    </div>
                </div>

                <!-- 3. Sound Effects -->
                <div class="accordion-item" data-accordion="sound">
                    <div class="accordion-header"><span>Sound Effects</span><span class="dashicons dashicons-arrow-down-alt2"></span></div>
                    <div class="accordion-body">
                        <label><input type="checkbox" id="sound-synth-chimes" checked> Enable Web Audio Chimes</label>
                    </div>
                </div>

                <!-- 4. Display Triggers -->
                <div class="accordion-item" data-accordion="triggers">
                    <div class="accordion-header"><span>Display Triggers</span><span class="dashicons dashicons-arrow-down-alt2"></span></div>
                    <div class="accordion-body">
                        <label><input type="checkbox" id="trig-load" checked> On Page Load</label>
                        <div style="margin-left:20px;margin-bottom:6px;">Delay (sec): <input type="number" id="trig-load-delay" value="0" min="0" style="width:60px;"></div>
                        <label><input type="checkbox" id="trig-exit"> On Cursor Exit Intent</label><br>
                        <label><input type="checkbox" id="trig-mobile-back" checked> Mobile Back Button Exit Interceptor</label><br>
                        <label><input type="checkbox" id="trig-scroll"> On Scroll Depth (> 50%)</label><br>
                        <label><input type="checkbox" id="trig-idle"> On User Inactivity (15s)</label><br>
                        <div class="form-group" style="margin-top:6px;">
                            <label>Custom Click Selector:</label>
                            <input type="text" id="trig-selector" placeholder="e.g. .open-promo" class="widefat">
                        </div>
                        <?php if ($feat_adblock_detector): ?>
                            <label><input type="checkbox" id="trig-adblock"> On AdBlock Detected</label>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- 5. Conditional Logic -->
                <div class="accordion-item" data-accordion="logic">
                    <div class="accordion-header"><span>Conditional Logic & Rules</span><span class="dashicons dashicons-arrow-down-alt2"></span></div>
                    <div class="accordion-body">
                        <div class="form-group">
                            <label>Math Formula Expression:</label>
                            <input type="text" id="math-expression" class="widefat" placeholder="{qty} * 25">
                        </div>
                        <div class="form-group">
                            <label>Output Target Layer ID:</label>
                            <input type="text" id="math-output-target" class="widefat" placeholder="elem_12345">
                        </div>
                    </div>
                </div>

                <!-- 6. WooCommerce & Coupons -->
                <div class="accordion-item" data-accordion="coupons">
                    <div class="accordion-header"><span>WooCommerce & Coupons</span><span class="dashicons dashicons-arrow-down-alt2"></span></div>
                    <div class="accordion-body">
                        <label><input type="checkbox" id="cpn-enable"> Generate Unique Dynamic Coupon</label>
                        <div class="form-group" style="margin-top:6px;">
                            <label>Coupon Prefix:</label>
                            <input type="text" id="cpn-prefix" value="POP-" class="widefat">
                        </div>
                        <div class="form-group">
                            <label>Discount Type:</label>
                            <select id="cpn-type" class="widefat">
                                <option value="percent">Percentage (%)</option>
                                <option value="fixed_cart">Fixed Cart ($)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Amount:</label>
                            <input type="number" id="cpn-amount" value="15" class="widefat">
                        </div>
                        <label><input type="checkbox" id="cpn-auto-apply" checked> Auto-apply to cart</label>
                    </div>
                </div>

                <!-- 7. Sticky Side Tabs -->
                <div class="accordion-item" data-accordion="sidetabs">
                    <div class="accordion-header"><span>Sticky Side Tabs</span><span class="dashicons dashicons-arrow-down-alt2"></span></div>
                    <div class="accordion-body">
                        <label><input type="checkbox" id="tab-enable"> Enable Sticky Tab</label>
                        <div class="form-group" style="margin-top:6px;">
                            <label>Tab Label:</label>
                            <input type="text" id="tab-text" value="Special Offer" class="widefat">
                        </div>
                        <div class="form-group">
                            <label>Position:</label>
                            <select id="tab-pos" class="widefat">
                                <option value="left">Left Edge</option>
                                <option value="right">Right Edge</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- 8. Payments -->
                <div class="accordion-item" data-accordion="payments">
                    <div class="accordion-header"><span>Payments & Checkout</span><span class="dashicons dashicons-arrow-down-alt2"></span></div>
                    <div class="accordion-body">
                        <label><input type="checkbox" id="pay-enable"> Enable Checkout</label>
                        <div class="form-group" style="margin-top:6px;">
                            <label>Default Amount:</label>
                            <input type="number" id="pay-amount" value="10.00" step="0.01" class="widefat">
                        </div>
                        <div class="form-group">
                            <label>Currency:</label>
                            <input type="text" id="pay-currency" value="USD" class="widefat">
                        </div>
                    </div>
                </div>

                <!-- 9. Secure Downloads -->
                <div class="accordion-item" data-accordion="downloads">
                    <div class="accordion-header"><span>Secure Downloads</span><span class="dashicons dashicons-arrow-down-alt2"></span></div>
                    <div class="accordion-body">
                        <label><input type="checkbox" id="dl-enable"> Enable File Download on Submit</label>
                        <div class="form-group" style="margin-top:6px;">
                            <label>Media File URL:</label>
                            <input type="url" id="dl-url" placeholder="https://site.com/file.zip" class="widefat">
                        </div>
                    </div>
                </div>

                <!-- 10. Video Listeners -->
                <div class="accordion-item" data-accordion="video">
                    <div class="accordion-header"><span>Video Listeners</span><span class="dashicons dashicons-arrow-down-alt2"></span></div>
                    <div class="accordion-body">
                        <label><input type="checkbox" id="vid-enable"> Trigger Popup when Video Ends</label>
                    </div>
                </div>

                <!-- 11. Autoresponder -->
                <div class="accordion-item" data-accordion="autoresponder">
                    <div class="accordion-header"><span>Autoresponder</span><span class="dashicons dashicons-arrow-down-alt2"></span></div>
                    <div class="accordion-body">
                        <label><input type="checkbox" id="ar-enable"> Send User Autoresponder Email</label>
                        <div class="form-group" style="margin-top:6px;">
                            <label>Subject:</label>
                            <input type="text" id="ar-subject" value="Thank you!" class="widefat">
                        </div>
                        <div class="form-group">
                            <label>Message Content ({coupon_code}):</label>
                            <textarea id="ar-message" class="widefat" rows="3">Thank you for subscribing! Here is your coupon code: {coupon_code}</textarea>
                        </div>
                    </div>
                </div>

                <!-- 12. Marketing & Webhooks -->
                <div class="accordion-item" data-accordion="marketing">
                    <div class="accordion-header"><span>Marketing & Webhooks</span><span class="dashicons dashicons-arrow-down-alt2"></span></div>
                    <div class="accordion-body">
                        <div class="form-group">
                            <label>Webhook URL (POST):</label>
                            <input type="url" id="mkt-webhook-url" placeholder="https://..." class="widefat">
                        </div>
                        <div class="form-group">
                            <label>HMAC-SHA256 Secret:</label>
                            <input type="password" id="mkt-webhook-secret" class="widefat">
                        </div>
                        <button type="button" class="button button-small" id="btn-test-webhook">Test Webhook Ping</button>
                    </div>
                </div>

                <!-- 13. Twilio SMS Alerts -->
                <div class="accordion-item" data-accordion="twilio">
                    <div class="accordion-header"><span>Twilio SMS Alerts</span><span class="dashicons dashicons-arrow-down-alt2"></span></div>
                    <div class="accordion-body">
                        <label><input type="checkbox" id="sms-enable"> Enable Real-Time SMS</label>
                        <div class="form-group" style="margin-top:6px;">
                            <label>Account SID:</label>
                            <input type="text" id="sms-sid" class="widefat">
                        </div>
                        <div class="form-group">
                            <label>Auth Token:</label>
                            <input type="password" id="sms-token" class="widefat">
                        </div>
                        <div class="form-group">
                            <label>To Phone Number:</label>
                            <input type="text" id="sms-to" class="widefat">
                        </div>
                        <button type="button" class="button button-small" id="btn-test-sms">Test SMS Ping</button>
                    </div>
                </div>

                <!-- 14. Targeting & Attribution -->
                <div class="accordion-item" data-accordion="targeting">
                    <div class="accordion-header"><span>Targeting & Attribution</span><span class="dashicons dashicons-arrow-down-alt2"></span></div>
                    <div class="accordion-body">
                        <div class="form-group">
                            <label>Visitor Authentication:</label>
                            <select id="target-auth-mode" class="widefat">
                                <option value="all">All Visitors</option>
                                <option value="guests_only">Guests / Logged-Out Only</option>
                                <option value="logged_in_only">Logged-In Users Only</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Display Scope:</label>
                            <select id="target-scope" class="widefat">
                                <option value="everywhere">Everywhere</option>
                                <option value="posts">Single Posts</option>
                                <option value="pages">Pages Only</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Country Rules:</label>
                            <select id="target-geo-mode" class="widefat">
                                <option value="all">All Countries</option>
                                <option value="whitelist">Show in Selected</option>
                                <option value="blacklist">Block Selected</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- 15. Frequency & Cookies -->
                <div class="accordion-item" data-accordion="cookies">
                    <div class="accordion-header"><span>Frequency & Cookies</span><span class="dashicons dashicons-arrow-down-alt2"></span></div>
                    <div class="accordion-body">
                        <div class="form-group">
                            <label>Display Frequency:</label>
                            <select id="freq-mode" class="widefat">
                                <option value="everytime">Every Page View</option>
                                <option value="once_session">Once Per Session</option>
                                <option value="days">Once Every X Days</option>
                            </select>
                        </div>
                        <label><input type="checkbox" id="freq-hide-submitted" checked> Suppress after submission</label>
                    </div>
                </div>

                <!-- 16. Scoped Code & Hooks -->
                <div class="accordion-item" data-accordion="customcode">
                    <div class="accordion-header"><span>Scoped Code & Hooks</span><span class="dashicons dashicons-arrow-down-alt2"></span></div>
                    <div class="accordion-body">
                        <div class="form-group">
                            <label>Custom Scoped CSS:</label>
                            <textarea id="code-custom-css" rows="3" class="widefat" placeholder="#wppoppop-popup-UID { ... }"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Custom JS Lifecycle Callback:</label>
                            <textarea id="code-custom-js" rows="3" class="widefat" placeholder="function(uid) { console.log(uid); }"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </aside>

    <!-- Embed Code Modal -->
    <div id="wppoppop-embed-modal" class="wppoppop-modal-backdrop" style="display:none;">
        <div class="wppoppop-modal-box">
            <div class="modal-box-header">
                <h3><span class="dashicons dashicons-editor-code"></span> Embed Snippets</h3>
                <button type="button" class="btn-close-modal">&times;</button>
            </div>
            <div class="modal-box-body">
                <div class="form-group">
                    <label>WordPress Shortcode:</label>
                    <div class="snippet-copy-row">
                        <input type="text" id="embed-code-shortcode" class="widefat" readonly>
                        <button type="button" class="button btn-copy-snippet" data-target="embed-code-shortcode">Copy</button>
                    </div>
                </div>
                <div class="form-group">
                    <label>Button Trigger Shortcode:</label>
                    <div class="snippet-copy-row">
                        <input type="text" id="embed-code-button" class="widefat" readonly>
                        <button type="button" class="button btn-copy-snippet" data-target="embed-code-button">Copy</button>
                    </div>
                </div>
                <div class="form-group">
                    <label>HTML Class Trigger:</label>
                    <div class="snippet-copy-row">
                        <input type="text" id="embed-code-class" class="widefat" readonly>
                        <button type="button" class="button btn-copy-snippet" data-target="embed-code-class">Copy</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Preview Modal -->
    <div id="wppoppop-live-preview-modal" class="wppoppop-modal-backdrop" style="display:none;">
        <div class="wppoppop-modal-box" style="width:720px;max-width:95%;">
            <div class="modal-box-header">
                <h3><span class="dashicons dashicons-visibility"></span> Live Interactive Preview</h3>
                <button type="button" class="btn-close-modal">&times;</button>
            </div>
            <div class="modal-box-body" style="background:#090d16;display:flex;align-items:center;justify-content:center;padding:30px;min-height:420px;">
                <div id="wppoppop-preview-stage-mount"></div>
            </div>
        </div>
    </div>
</div>
