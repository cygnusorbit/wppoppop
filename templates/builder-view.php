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
<div class="wppoppop-builder-wrap">
    <?php wp_nonce_field('wppoppop_builder_nonce', 'wppoppop_builder_nonce_field'); ?>

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

            <!-- Gamified & Conversion Elements -->
            <button type="button" class="ribbon-btn" data-type="wheel" title="Lucky Wheel"><span class="dashicons dashicons-chart-pie"></span></button>
            <button type="button" class="ribbon-btn" data-type="scratch" title="Scratch Card"><span class="dashicons dashicons-tickets-alt"></span></button>
            <button type="button" class="ribbon-btn" data-type="progress" title="Progress Bar"><span class="dashicons dashicons-ellipsis"></span></button>
            
            <?php if ($feat_signature_pad): ?>
                <button type="button" class="ribbon-btn" data-type="signature" title="Signature Pad"><span class="dashicons dashicons-welcome-write-blog"></span></button>
            <?php endif; ?>

            <button type="button" class="ribbon-btn" data-type="pay_btn" title="Payment Button"><span class="dashicons dashicons-cart"></span></button>
        </div>
    </div>

    <!-- 4. Canvas Viewport with Transparency Grid -->
    <main class="wppoppop-canvas-viewport">
        <!-- Stage / Popup Window -->
        <div class="wppoppop-stage" id="wppoppop-stage" style="width: 620px; height: 380px;">
            <!-- Canvas elements render here -->
        </div>

        <!-- 5. Floating Layers Panel on Canvas Right -->
        <aside class="wppoppop-floating-layers" id="wppoppop-floating-layers">
            <div class="layers-header">
                <span class="layers-title">LAYERS</span>
                <span class="dashicons dashicons-move layers-drag-handle" title="Move Layers Box"></span>
            </div>
            <ul id="wppoppop-layers-list" class="layers-list">
                <!-- Dynamically populated layers list -->
            </ul>
            <div class="layers-footer-hint">
                1. Click any button on elements toolbar to add new layer. 2. Sort layers to change z-index.
            </div>
        </aside>
    </main>

    <!-- Overlay Backdrop for Slide-out Drawers -->
    <div id="wppoppop-drawer-backdrop" class="wppoppop-drawer-backdrop"></div>

    <!-- 6. Slide-out Campaign Settings Drawer -->
    <aside id="wppoppop-settings-drawer" class="wppoppop-slide-drawer">
        <div class="drawer-header">
            <h3><span class="dashicons dashicons-admin-generic"></span> Campaign Settings</h3>
            <button type="button" class="btn-close-drawer">&times;</button>
        </div>
        <div class="drawer-content">
            <div class="wppoppop-accordion">
                <!-- Box & Backdrop Styling -->
                <div class="accordion-item active" data-accordion="backdrop">
                    <div class="accordion-header"><span>Box & Dimensions</span><span class="dashicons dashicons-arrow-down-alt2"></span></div>
                    <div class="accordion-body">
                        <div style="display:flex;gap:8px;margin-bottom:8px;">
                            <label style="flex:1;">Width (px): <input type="number" id="stage-width" value="620" class="widefat"></label>
                            <label style="flex:1;">Height (px): <input type="number" id="stage-height" value="380" class="widefat"></label>
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
                            <label>Glassmorphism Blur (px):</label>
                            <input type="number" id="style-backdrop-blur" value="5" min="0" max="25" class="widefat">
                        </div>
                        <div class="form-group">
                            <label>Corner Radius (px):</label>
                            <input type="number" id="box-border-radius" value="4" min="0" max="40" class="widefat">
                        </div>
                        <div class="form-group">
                            <label>Stage Background Image URL:</label>
                            <input type="text" id="box-bg-image" placeholder="https://..." class="widefat">
                        </div>
                        <div class="form-group">
                            <label>Stage Background Color:</label>
                            <input type="color" id="box-bg-color" value="#ffffff" style="width:100%;height:32px;">
                        </div>
                        <label><input type="checkbox" id="style-close-esc" checked> Close on ESC key</label><br>
                        <label><input type="checkbox" id="style-close-backdrop" checked> Close on backdrop click</label>
                    </div>
                </div>

                <!-- Display Triggers -->
                <div class="accordion-item" data-accordion="triggers">
                    <div class="accordion-header"><span>Display Triggers</span><span class="dashicons dashicons-arrow-down-alt2"></span></div>
                    <div class="accordion-body">
                        <label><input type="checkbox" id="trig-load" checked> On Page Load</label>
                        <div style="margin-left:20px;margin-bottom:6px;">Delay (sec): <input type="number" id="trig-load-delay" value="0" min="0" style="width:60px;"></div>
                        <label><input type="checkbox" id="trig-exit"> On Exit Intent</label><br>
                        <label><input type="checkbox" id="trig-scroll"> On Scroll Depth (> 50%)</label><br>
                        <label><input type="checkbox" id="trig-idle"> On User Inactivity (15s)</label><br>
                        <label><input type="checkbox" id="trig-mobile-back"> On Mobile Back Button</label>
                        <?php if ($feat_adblock_detector): ?>
                            <br><label><input type="checkbox" id="trig-adblock"> On AdBlock Detected</label>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Conditional Logic & Math -->
                <div class="accordion-item" data-accordion="logic">
                    <div class="accordion-header"><span>Conditional Logic & Math</span><span class="dashicons dashicons-arrow-down-alt2"></span></div>
                    <div class="accordion-body">
                        <div class="form-group">
                            <label>Math Formula (e.g. {qty} * 25):</label>
                            <input type="text" id="math-expression" class="widefat">
                        </div>
                        <div class="form-group">
                            <label>Target Layer ID for Calculation Output:</label>
                            <input type="text" id="math-output-target" class="widefat">
                        </div>
                    </div>
                </div>

                <!-- WooCommerce & Coupons -->
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

                <!-- Sticky Side Tabs -->
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

                <!-- Payments -->
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

                <!-- Secure Downloads -->
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

                <!-- Video Listeners -->
                <div class="accordion-item" data-accordion="video">
                    <div class="accordion-header"><span>Video Listeners</span><span class="dashicons dashicons-arrow-down-alt2"></span></div>
                    <div class="accordion-body">
                        <label><input type="checkbox" id="vid-enable"> Trigger Popup when Video Ends</label>
                    </div>
                </div>

                <!-- Autoresponder -->
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

                <!-- Marketing & Webhooks -->
                <div class="accordion-item" data-accordion="marketing">
                    <div class="accordion-header"><span>Marketing & Webhooks</span><span class="dashicons dashicons-arrow-down-alt2"></span></div>
                    <div class="accordion-body">
                        <div class="form-group">
                            <label>Webhook URL:</label>
                            <input type="url" id="mkt-webhook-url" placeholder="https://..." class="widefat">
                        </div>
                        <div class="form-group">
                            <label>HMAC-SHA256 Secret:</label>
                            <input type="password" id="mkt-webhook-secret" class="widefat">
                        </div>
                    </div>
                </div>

                <!-- Twilio SMS Alerts -->
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
                    </div>
                </div>

                <!-- Targeting & Attribution -->
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

                <!-- Frequency & Cookies -->
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

                <!-- Scoped Code & Hooks -->
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

    <!-- 7. Slide-out Layer Inspector Drawer -->
    <aside id="wppoppop-inspector-drawer" class="wppoppop-slide-drawer">
        <div class="drawer-header">
            <h3><span class="dashicons dashicons-art"></span> Layer Properties</h3>
            <button type="button" class="btn-close-drawer">&times;</button>
        </div>
        <div class="drawer-content">
            <div id="inspector-controls">
                <div class="form-group">
                    <label>Layer Label / Name:</label>
                    <input type="text" id="prop-layer-name" class="widefat" placeholder="e.g. Header, Button...">
                </div>
                <div class="form-group">
                    <label>Binding Key (e.g. email, phone):</label>
                    <input type="text" id="prop-field-name" class="widefat">
                </div>
                <div class="form-group" id="group-prop-content">
                    <label>Text Content / HTML:</label>
                    <textarea id="prop-content" rows="3" class="widefat"></textarea>
                </div>
                <div class="form-group" id="group-prop-options" style="display:none;">
                    <label>Options / Slices (comma-separated):</label>
                    <input type="text" id="prop-options" class="widefat">
                </div>

                <div class="form-group">
                    <label>Font Family:</label>
                    <select id="prop-font-family" class="widefat">
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
                    </select>
                </div>
                <div style="display:flex;gap:8px;">
                    <div class="form-group" style="flex:1;">
                        <label>Font Size (px):</label>
                        <input type="number" id="prop-font-size" value="16" min="8" max="72" class="widefat">
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label>Corner Radius (px):</label>
                        <input type="number" id="prop-border-radius" value="4" min="0" max="100" class="widefat">
                    </div>
                </div>
                <div style="display:flex;gap:8px;">
                    <div class="form-group" style="flex:1;">
                        <label>Text Color:</label>
                        <input type="color" id="prop-color" value="#ffffff" style="width:100%;height:32px;">
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label>Background Color:</label>
                        <input type="color" id="prop-bg-color" value="#000000" style="width:100%;height:32px;">
                    </div>
                </div>
                <div class="form-group">
                    <label>Opacity (0.1 - 1.0):</label>
                    <input type="number" id="prop-opacity" value="1.0" min="0.1" max="1.0" step="0.1" class="widefat">
                </div>
                <div class="form-group">
                    <label><input type="checkbox" id="prop-required"> Mandatory Field (Required)</label>
                </div>

                <div style="display:flex;gap:8px;margin-top:16px;">
                    <button type="button" class="button button-secondary" id="prop-duplicate-element" style="flex:1;">Duplicate</button>
                    <button type="button" class="button button-link-delete" id="prop-delete-element" style="flex:1;">Delete Layer</button>
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
                    <input type="text" id="embed-code-shortcode" class="widefat" readonly onclick="this.select();">
                </div>
                <div class="form-group">
                    <label>Button Trigger Shortcode:</label>
                    <input type="text" id="embed-code-button" class="widefat" readonly onclick="this.select();">
                </div>
                <div class="form-group">
                    <label>HTML Class Trigger:</label>
                    <input type="text" id="embed-code-class" class="widefat" readonly onclick="this.select();">
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
