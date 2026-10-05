<?php
if (!defined('ABSPATH')) {
    exit;
}

$settings = get_option('wppoppop_settings', []);

$feat_google_fonts     = isset($settings['google_fonts']) ? !empty($settings['google_fonts']) : true;
$feat_air_datepicker   = isset($settings['air_datepicker']) ? !empty($settings['air_datepicker']) : true;
$feat_signature_pad    = !empty($settings['signature_pad']);
$feat_range_slider     = !empty($settings['range_slider']);
$feat_adblock_detector = !empty($settings['adblock_detector']);
$feat_js_parser        = !empty($settings['js_parser']);
$feat_jquery_mask      = !empty($settings['jquery_mask']);
$feat_font_awesome     = !empty($settings['font_awesome']);

$custom_fonts_raw = isset($settings['custom_fonts']) ? $settings['custom_fonts'] : '';
$custom_fonts     = array_filter(array_map('trim', explode("\n", $custom_fonts_raw)));
?>
<div class="wppoppop-builder-wrap">
    <!-- Header -->
    <header class="wppoppop-builder-header">
        <div class="header-left">
            <span class="dashicons dashicons-external" style="font-size: 24px; margin-right: 8px;"></span>
            <input type="text" id="wppoppop-popup-title" value="New Converting Popup" placeholder="Popup Name...">
            <input type="hidden" id="wppoppop-popup-uid" value="">
        </div>
        <div class="header-center">
            <div class="screen-switch-bar">
                <button type="button" class="button btn-screen-toggle active" data-screen="1">Screen 1</button>
                <button type="button" class="button btn-screen-toggle" data-screen="2">Screen 2</button>
            </div>
            <label style="margin-left: 15px;">W: <input type="number" id="stage-width" value="640" style="width: 65px;"> px</label>
            <label style="margin-left: 8px;">H: <input type="number" id="stage-height" value="400" style="width: 65px;"> px</label>
        </div>
        <div class="header-right">
            <button class="button button-secondary" id="wppoppop-btn-reset">Reset</button>
            <button class="button button-primary" id="wppoppop-btn-save">Save Popup</button>
        </div>
    </header>

    <div class="wppoppop-builder-body">
        <!-- Palette Sidebar: All 17 Form & Media Elements -->
        <aside class="wppoppop-panel wppoppop-sidebar-left">
            <div class="panel-tabs">
                <button type="button" class="tab-btn active" data-tab="tab-elements">Elements</button>
                <button type="button" class="tab-btn" data-tab="tab-layers">Layers</button>
            </div>

            <div id="tab-elements" class="tab-pane active">
                <p class="panel-hint">Add element to active screen:</p>
                <div class="element-item" data-type="text"><span class="dashicons dashicons-editor-textcolor"></span> Text Block</div>
                <div class="element-item" data-type="input"><span class="dashicons dashicons-email-alt"></span> Email Field</div>
                <div class="element-item" data-type="number"><span class="dashicons dashicons-calculator"></span> Number Field</div>
                <div class="element-item" data-type="dropdown"><span class="dashicons dashicons-menu-alt"></span> Select Dropdown</div>
                <div class="element-item" data-type="radio"><span class="dashicons dashicons-marker"></span> Radio Options</div>
                <div class="element-item" data-type="checkbox"><span class="dashicons dashicons-yes"></span> Checkbox List</div>
                <div class="element-item" data-type="rating"><span class="dashicons dashicons-star-filled"></span> Star Rating</div>

                <?php if ($feat_air_datepicker) : ?>
                    <div class="element-item" data-type="date"><span class="dashicons dashicons-calendar-alt"></span> Date Picker</div>
                <?php endif; ?>

                <?php if ($feat_range_slider) : ?>
                    <div class="element-item" data-type="slider"><span class="dashicons dashicons-leftright"></span> Range Slider</div>
                <?php endif; ?>

                <?php if ($feat_signature_pad) : ?>
                    <div class="element-item" data-type="signature"><span class="dashicons dashicons-edit"></span> Signature Pad</div>
                <?php endif; ?>

                <div class="element-item" data-type="countdown"><span class="dashicons dashicons-clock"></span> Countdown Timer</div>
                <div class="element-item" data-type="progress"><span class="dashicons dashicons-ellipsis"></span> Progress Bar</div>
                <div class="element-item" data-type="wheel"><span class="dashicons dashicons-chart-pie"></span> Lucky Wheel</div>
                <div class="element-item" data-type="nextstep"><span class="dashicons dashicons-arrow-right-alt"></span> Next Step Button</div>
                <div class="element-item" data-type="button"><span class="dashicons dashicons-button"></span> Submit Button</div>
                <div class="element-item" data-type="paybutton"><span class="dashicons dashicons-cart"></span> Payment Button</div>
                <div class="element-item" data-type="html"><span class="dashicons dashicons-editor-code"></span> Raw HTML</div>
            </div>

            <div id="tab-layers" class="tab-pane">
                <ul id="wppoppop-layers-list" class="layers-list">
                    <li class="empty-layers">No elements on current screen.</li>
                </ul>
            </div>
        </aside>

        <!-- Canvas Viewport -->
        <main class="wppoppop-canvas-viewport">
            <div class="canvas-alignment-toolbar">
                <button type="button" class="button button-small" id="btn-undo" title="Undo (Ctrl+Z)"><span class="dashicons dashicons-undo" style="vertical-align:middle;"></span> Undo</button>
                <button type="button" class="button button-small" id="btn-redo" title="Redo (Ctrl+Y)"><span class="dashicons dashicons-redo" style="vertical-align:middle;"></span> Redo</button>
                <span class="toolbar-sep">|</span>
                <label style="font-size:12px;cursor:pointer;"><input type="checkbox" id="chk-grid-snap" checked> Snap to 10px Grid</label>
                <span class="toolbar-sep">|</span>
                <button type="button" class="button button-small btn-align" data-align="left">Left</button>
                <button type="button" class="button button-small btn-align" data-align="center-h">Center H</button>
                <button type="button" class="button button-small btn-align" data-align="right">Right</button>
                <span class="toolbar-sep">|</span>
                <button type="button" class="button button-small btn-align" data-align="top">Top</button>
                <button type="button" class="button button-small btn-align" data-align="center-v">Center V</button>
                <button type="button" class="button button-small btn-align" data-align="bottom">Bottom</button>
            </div>

            <div class="wppoppop-stage" id="wppoppop-stage" style="width: 640px; height: 400px; background-color: #ffffff;">
                <div class="canvas-grid-guide"></div>
            </div>
        </main>

        <!-- Right Configuration Sidebar (Unified 15 Accordions) -->
        <aside class="wppoppop-panel wppoppop-sidebar-right">
            <div class="wppoppop-accordion">

                <!-- 1. Inspector -->
                <div class="accordion-item active" data-accordion="inspector">
                    <div class="accordion-header">
                        <span class="accordion-title"><span class="dashicons dashicons-admin-generic"></span> Inspector</span>
                        <span class="accordion-icon dashicons dashicons-arrow-down-alt2"></span>
                    </div>
                    <div class="accordion-body">
                        <div id="inspector-empty-state">Select any canvas layer to edit properties.</div>
                        <div id="inspector-controls" style="display: none;">
                            <div class="form-group">
                                <label>Binding Key (e.g. prize, email, qty, signature):</label>
                                <input type="text" id="prop-field-name" placeholder="e.g. email, budget" class="widefat">
                            </div>
                            <div class="form-group">
                                <label>Content / Label / Text:</label>
                                <input type="text" id="prop-content" class="widefat">
                            </div>

                            <!-- Slider Config -->
                            <div class="form-group" id="group-prop-slider" style="display:none; background:#f8fafc; padding:10px; border-radius:4px; border:1px solid #e2e8f0;">
                                <label>Slider Min / Max / Default:</label>
                                <div style="display:flex; gap:6px;">
                                    <input type="number" id="prop-slider-min" value="0" placeholder="Min" style="width:33%;">
                                    <input type="number" id="prop-slider-max" value="100" placeholder="Max" style="width:33%;">
                                    <input type="number" id="prop-slider-val" value="50" placeholder="Val" style="width:33%;">
                                </div>
                                <div style="margin-top:6px;">
                                    <label>Prefix / Currency:</label>
                                    <input type="text" id="prop-slider-prefix" value="$" style="width:60px;">
                                </div>
                            </div>

                            <!-- Countdown Config -->
                            <div class="form-group" id="group-prop-countdown" style="display:none; background:#f8fafc; padding:10px; border-radius:4px; border:1px solid #e2e8f0;">
                                <label>Duration (Minutes):</label>
                                <input type="number" id="prop-countdown-mins" value="15" min="1" max="1440" class="widefat">
                            </div>

                            <!-- Validation Rules -->
                            <div class="form-group" id="group-prop-validation" style="background:#f8fafc; padding:10px; border-radius:4px; border:1px solid #e2e8f0;">
                                <label style="font-weight:700;"><input type="checkbox" id="prop-required"> Mandatory Field (Required)</label>
                                <div style="margin-top:6px;">
                                    <label>Error Bubble Message:</label>
                                    <input type="text" id="prop-error-msg" value="Please fill out this field." class="widefat">
                                </div>
                            </div>

                            <?php if ($feat_jquery_mask) : ?>
                                <div class="form-group" id="group-prop-mask" style="display:none;">
                                    <label>Input Mask (jQuery Mask):</label>
                                    <input type="text" id="prop-mask" placeholder="e.g. (999) 999-9999" class="widefat">
                                </div>
                            <?php endif; ?>

                            <div class="form-group">
                                <label>Font Family:</label>
                                <select id="prop-font-family" class="widefat">
                                    <optgroup label="Standard Fonts">
                                        <option value="Inherit">Inherit Theme Font</option>
                                        <option value="Arial, sans-serif">Arial</option>
                                        <option value="Georgia, serif">Georgia</option>
                                        <option value="'Times New Roman', serif">Times New Roman</option>
                                    </optgroup>
                                    <?php if ($feat_google_fonts) : ?>
                                        <optgroup label="Google Fonts">
                                            <option value="Inter">Inter</option>
                                            <option value="Roboto">Roboto</option>
                                            <option value="Montserrat">Montserrat</option>
                                            <option value="Poppins">Poppins</option>
                                            <option value="Playfair Display">Playfair Display</option>
                                        </optgroup>
                                    <?php endif; ?>
                                    <?php if (!empty($custom_fonts)) : ?>
                                        <optgroup label="Custom Local Fonts">
                                            <?php foreach ($custom_fonts as $cf) : ?>
                                                <option value="<?php echo esc_attr($cf); ?>"><?php echo esc_html($cf); ?></option>
                                            <?php endforeach; ?>
                                        </optgroup>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div class="form-group" id="group-prop-options" style="display:none;">
                                <label>Options / Slices (comma-separated):</label>
                                <input type="text" id="prop-options" placeholder="Option 1, Option 2" class="widefat">
                            </div>
                            <div class="form-group">
                                <label>Font Size (px):</label>
                                <input type="number" id="prop-font-size" value="16" min="10" max="72">
                            </div>
                            <div class="form-group">
                                <label>Text Color:</label>
                                <input type="color" id="prop-color" value="#222222">
                            </div>
                            <div class="form-group">
                                <label>Background Color:</label>
                                <input type="color" id="prop-bg-color" value="#00a32a">
                            </div>
                            <div class="form-row-actions">
                                <button type="button" class="button" id="prop-duplicate-element">Duplicate</button>
                                <button type="button" class="button button-link-delete" id="prop-delete-element">Delete</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Layer Transitions -->
                <div class="accordion-item" data-accordion="animation">
                    <div class="accordion-header">
                        <span class="accordion-title"><span class="dashicons dashicons-controls-play"></span> Layer Animation</span>
                        <span class="accordion-icon dashicons dashicons-arrow-down-alt2"></span>
                    </div>
                    <div class="accordion-body">
                        <div class="form-group">
                            <label>Entrance Effect:</label>
                            <select id="prop-anim-effect" class="widefat">
                                <option value="none">None (Instant)</option>
                                <option value="fade">Fade In</option>
                                <option value="slideDown">Slide Down</option>
                                <option value="zoomIn">Zoom In</option>
                                <option value="bounceIn">Bounce In</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Start Delay (ms):</label>
                            <input type="number" id="prop-anim-delay" value="0" min="0" step="50" class="widefat">
                        </div>
                        <div class="form-group">
                            <label>Duration (ms):</label>
                            <input type="number" id="prop-anim-duration" value="500" min="100" step="50" class="widefat">
                        </div>
                    </div>
                </div>

                <!-- 3. Sound Effects -->
                <div class="accordion-item" data-accordion="audio">
                    <div class="accordion-header">
                        <span class="accordion-title"><span class="dashicons dashicons-format-audio"></span> Sound Effects</span>
                        <span class="accordion-icon dashicons dashicons-arrow-down-alt2"></span>
                    </div>
                    <div class="accordion-body">
                        <label class="trigger-option"><input type="checkbox" id="sound-enable"> Enable Web Audio Synth Chimes</label>
                        <small style="color:#64748b;display:block;margin-top:4px;">Synthesizes entrance and submission sound effects in real time.</small>
                    </div>
                </div>

                <!-- 4. Backdrop & Glassmorphism -->
                <div class="accordion-item" data-accordion="backdrop">
                    <div class="accordion-header">
                        <span class="accordion-title"><span class="dashicons dashicons-art"></span> Backdrop Styling</span>
                        <span class="accordion-icon dashicons dashicons-arrow-down-alt2"></span>
                    </div>
                    <div class="accordion-body">
                        <div class="form-group">
                            <label>Glassmorphism Blur (px):</label>
                            <input type="number" id="style-backdrop-blur" value="5" min="0" max="25" class="widefat">
                        </div>
                        <label class="trigger-option"><input type="checkbox" id="style-close-esc" checked> Close on ESC key</label>
                        <label class="trigger-option"><input type="checkbox" id="style-close-backdrop" checked> Close on backdrop click</label>
                    </div>
                </div>

                <!-- 5. Display Triggers -->
                <div class="accordion-item" data-accordion="triggers">
                    <div class="accordion-header">
                        <span class="accordion-title"><span class="dashicons dashicons-clock"></span> Display Triggers</span>
                        <span class="accordion-icon dashicons dashicons-arrow-down-alt2"></span>
                    </div>
                    <div class="accordion-body">
                        <label class="trigger-option"><input type="checkbox" id="trig-load" checked> On Page Load</label>
                        <div class="sub-option">Delay: <input type="number" id="trig-load-delay" value="0" min="0" style="width: 60px;"> sec</div>
                        <label class="trigger-option"><input type="checkbox" id="trig-exit"> On Exit Intent</label>
                        <label class="trigger-option"><input type="checkbox" id="trig-scroll"> On Scroll Depth (> 50%)</label>
                        <label class="trigger-option"><input type="checkbox" id="trig-idle"> On User Inactivity (15s)</label>
                        <div class="form-group" style="margin-top: 10px;">
                            <label>Click Trigger Selector:</label>
                            <input type="text" id="trig-click-selector" placeholder=".open-popup-btn" class="widefat">
                        </div>
                        <?php if ($feat_adblock_detector) : ?>
                            <hr>
                            <label class="trigger-option"><input type="checkbox" id="trig-adblock"> On AdBlock Detected</label>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- 6. Conditional Logic & Math -->
                <div class="accordion-item" data-accordion="logic">
                    <div class="accordion-header">
                        <span class="accordion-title"><span class="dashicons dashicons-randomize"></span> Conditional Logic & Math</span>
                        <span class="accordion-icon dashicons dashicons-arrow-down-alt2"></span>
                    </div>
                    <div class="accordion-body">
                        <h4>Conditional Rules</h4>
                        <div class="form-group">
                            <label>If Field Name:</label>
                            <input type="text" id="logic-if-field" placeholder="e.g. qty or choice" class="widefat">
                        </div>
                        <div class="form-group">
                            <label>Equals Value:</label>
                            <input type="text" id="logic-equals-val" placeholder="e.g. 5" class="widefat">
                        </div>
                        <div class="form-group">
                            <label>Target Element Layer ID:</label>
                            <input type="text" id="logic-target-layer" placeholder="e.g. elem_12345" class="widefat">
                        </div>
                        <div class="form-group">
                            <label>Action:</label>
                            <select id="logic-action" class="widefat">
                                <option value="show">Show Element</option>
                                <option value="hide">Hide Element</option>
                            </select>
                        </div>
                        <?php if ($feat_js_parser) : ?>
                            <hr>
                            <h4>Math Calculator</h4>
                            <div class="form-group">
                                <label>Formula:</label>
                                <input type="text" id="math-expression" placeholder="e.g. {qty} * 25" class="widefat">
                            </div>
                            <div class="form-group">
                                <label>Target Layer ID:</label>
                                <input type="text" id="math-output-target" placeholder="e.g. elem_12345" class="widefat">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- 7. Sticky Side Tabs -->
                <div class="accordion-item" data-accordion="sidetab">
                    <div class="accordion-header">
                        <span class="accordion-title"><span class="dashicons dashicons-tag"></span> Sticky Side Tab</span>
                        <span class="accordion-icon dashicons dashicons-arrow-down-alt2"></span>
                    </div>
                    <div class="accordion-body">
                        <label class="trigger-option"><input type="checkbox" id="sidetab-enable"> Enable Sticky Side Tab</label>
                        <div class="form-group" style="margin-top: 10px;">
                            <label>Tab Label:</label>
                            <input type="text" id="sidetab-label" value="Special Offer" class="widefat">
                        </div>
                        <div class="form-group">
                            <label>Edge Position:</label>
                            <select id="sidetab-position" class="widefat">
                                <option value="right">Right Edge</option>
                                <option value="left">Left Edge</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Background Color:</label>
                            <input type="color" id="sidetab-bg" value="#2271b1">
                        </div>
                    </div>
                </div>

                <!-- 8. Payments & Monetization -->
                <div class="accordion-item" data-accordion="payment">
                    <div class="accordion-header">
                        <span class="accordion-title"><span class="dashicons dashicons-cart"></span> Payments & Checkout</span>
                        <span class="accordion-icon dashicons dashicons-arrow-down-alt2"></span>
                    </div>
                    <div class="accordion-body">
                        <label class="trigger-option"><input type="checkbox" id="pay-enable"> Enable Payment Processing</label>
                        <div class="form-group" style="margin-top: 10px;">
                            <label>Amount to Charge ($):</label>
                            <input type="number" id="pay-amount" value="19.99" step="0.01" class="widefat">
                        </div>
                        <div class="form-group">
                            <label>Currency:</label>
                            <input type="text" id="pay-currency" value="USD" class="widefat">
                        </div>
                        <div class="form-group">
                            <label>Gateway:</label>
                            <select id="pay-gateway" class="widefat">
                                <option value="Stripe">Stripe Checkout</option>
                                <option value="PayPal">PayPal Commerce</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- 9. Secure Downloads -->
                <div class="accordion-item" data-accordion="downloads">
                    <div class="accordion-header">
                        <span class="accordion-title"><span class="dashicons dashicons-download"></span> Secure Downloads</span>
                        <span class="accordion-icon dashicons dashicons-arrow-down-alt2"></span>
                    </div>
                    <div class="accordion-body">
                        <label class="trigger-option"><input type="checkbox" id="dl-enable"> Enable Encrypted Download</label>
                        <div class="form-group" style="margin-top: 10px;">
                            <label>Protected Media URL:</label>
                            <input type="url" id="dl-file-url" placeholder="https://site.com/uploads/ebook.pdf" class="widefat">
                        </div>
                        <div class="form-group">
                            <label>Token Expiry (Hours):</label>
                            <input type="number" id="dl-expiry-hours" value="24" min="1" class="widefat">
                        </div>
                    </div>
                </div>

                <!-- 10. Video Events Listener -->
                <div class="accordion-item" data-accordion="video">
                    <div class="accordion-header">
                        <span class="accordion-title"><span class="dashicons dashicons-video-alt3"></span> Video Listeners</span>
                        <span class="accordion-icon dashicons dashicons-arrow-down-alt2"></span>
                    </div>
                    <div class="accordion-body">
                        <label class="trigger-option"><input type="checkbox" id="video-enable"> Enable HTML5 Video Triggers</label>
                        <div class="form-group" style="margin-top: 10px;">
                            <label>Trigger Event:</label>
                            <select id="video-trigger-mode" class="widefat">
                                <option value="ended">When Video Finishes Playing</option>
                                <option value="play">When Video Starts Playing</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- 11. Subscriber Autoresponder -->
                <div class="accordion-item" data-accordion="autoresponder">
                    <div class="accordion-header">
                        <span class="accordion-title"><span class="dashicons dashicons-email"></span> Autoresponder Email</span>
                        <span class="accordion-icon dashicons dashicons-arrow-down-alt2"></span>
                    </div>
                    <div class="accordion-body">
                        <label class="trigger-option"><input type="checkbox" id="ar-enable"> Send User Autoresponder Email</label>
                        <div class="form-group" style="margin-top: 10px;">
                            <label>Email Subject:</label>
                            <input type="text" id="ar-subject" value="Congratulations! Here is your reward" class="widefat">
                        </div>
                        <div class="form-group">
                            <label>Message Content ({prize}, {email}):</label>
                            <textarea id="ar-message" class="widefat" rows="5">Hi there,

Thank you for subscribing! Your won reward is: {prize}

Enjoy your exclusive offer.</textarea>
                        </div>
                    </div>
                </div>

                <!-- 12. Marketing Services & Webhooks -->
                <div class="accordion-item" data-accordion="marketing">
                    <div class="accordion-header">
                        <span class="accordion-title"><span class="dashicons dashicons-share-alt"></span> Marketing & Webhooks</span>
                        <span class="accordion-icon dashicons dashicons-arrow-down-alt2"></span>
                    </div>
                    <div class="accordion-body">
                        <h4 style="margin: 0 0 8px 0;">Mailchimp Sync</h4>
                        <label class="trigger-option"><input type="checkbox" id="mc-enable"> Enable Mailchimp</label>
                        <div class="form-group" style="margin-top: 6px;">
                            <label>API Key:</label>
                            <input type="password" id="mc-api-key" placeholder="xxxxxx-us1" class="widefat">
                        </div>
                        <div class="form-group">
                            <label>Audience List ID:</label>
                            <input type="text" id="mc-list-id" placeholder="8a24fb01c2" class="widefat">
                        </div>

                        <hr>
                        <h4 style="margin: 0 0 8px 0;">ActiveCampaign Sync</h4>
                        <label class="trigger-option"><input type="checkbox" id="ac-enable"> Enable ActiveCampaign</label>
                        <div class="form-group" style="margin-top: 6px;">
                            <label>API URL:</label>
                            <input type="url" id="ac-api-url" placeholder="https://youraccount.api-us1.com" class="widefat">
                        </div>
                        <div class="form-group">
                            <label>API Key:</label>
                            <input type="password" id="ac-api-key" class="widefat">
                        </div>

                        <hr>
                        <h4 style="margin: 0 0 8px 0;">Generic Webhook POST</h4>
                        <label class="trigger-option"><input type="checkbox" id="int-enable-webhook"> Enable Webhook Dispatch</label>
                        <div class="form-group" style="margin-top: 6px;">
                            <label>Webhook Endpoint URL:</label>
                            <input type="url" id="int-webhook-url" placeholder="https://hooks.zapier.com/..." class="widefat">
                        </div>
                    </div>
                </div>

                <!-- 13. SMS Gateways (Twilio) -->
                <div class="accordion-item" data-accordion="sms">
                    <div class="accordion-header">
                        <span class="accordion-title"><span class="dashicons dashicons-phone"></span> Twilio SMS Alerts</span>
                        <span class="accordion-icon dashicons dashicons-arrow-down-alt2"></span>
                    </div>
                    <div class="accordion-body">
                        <label class="trigger-option"><input type="checkbox" id="sms-enable"> Enable Twilio SMS Lead Alerts</label>
                        <div class="form-group" style="margin-top: 8px;">
                            <label>Account SID:</label>
                            <input type="text" id="sms-twilio-sid" class="widefat">
                        </div>
                        <div class="form-group">
                            <label>Auth Token:</label>
                            <input type="password" id="sms-twilio-token" class="widefat">
                        </div>
                        <div class="form-group">
                            <label>From Phone #:</label>
                            <input type="text" id="sms-from-phone" class="widefat">
                        </div>
                        <div class="form-group">
                            <label>To Mobile #:</label>
                            <input type="text" id="sms-to-phone" class="widefat">
                        </div>
                    </div>
                </div>

                <!-- 14. Targeting & Rules -->
                <div class="accordion-item" data-accordion="targeting">
                    <div class="accordion-header">
                        <span class="accordion-title"><span class="dashicons dashicons-location-alt"></span> Targeting & Geolocation</span>
                        <span class="accordion-icon dashicons dashicons-arrow-down-alt2"></span>
                    </div>
                    <div class="accordion-body">
                        <div class="form-group">
                            <label>Display Location:</label>
                            <select id="target-scope" class="widefat">
                                <option value="everywhere">Everywhere</option>
                                <option value="posts">Single Posts</option>
                                <option value="pages">Pages Only</option>
                                <option value="specific">Specific Post IDs</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Post Categories (slugs, comma-separated):</label>
                            <input type="text" id="target-cat-slugs" placeholder="news, blog, updates" class="widefat">
                        </div>
                        <hr>
                        <div class="form-group">
                            <label>Country Rules:</label>
                            <select id="target-geo-mode" class="widefat">
                                <option value="all">Allow All Countries</option>
                                <option value="whitelist">Show Only In Selected</option>
                                <option value="blacklist">Block Selected</option>
                            </select>
                        </div>
                        <div class="form-group" id="group-geo-countries" style="display:none;">
                            <label>Country Codes (e.g. US, CA, GB):</label>
                            <input type="text" id="target-geo-countries" class="widefat">
                        </div>
                        <hr>
                        <div class="form-group">
                            <label>Device Viewport:</label>
                            <select id="target-devices" class="widefat">
                                <option value="all">All Devices</option>
                                <option value="desktop">Desktop Only</option>
                                <option value="mobile">Mobile / Tablet Only</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- 15. Frequency & Cookies -->
                <div class="accordion-item" data-accordion="cookies">
                    <div class="accordion-header">
                        <span class="accordion-title"><span class="dashicons dashicons-visibility"></span> Frequency & Cookies</span>
                        <span class="accordion-icon dashicons dashicons-arrow-down-alt2"></span>
                    </div>
                    <div class="accordion-body">
                        <div class="form-group">
                            <label>Display Frequency:</label>
                            <select id="freq-mode" class="widefat">
                                <option value="everytime">Every Page View</option>
                                <option value="once_session">Once Per Session</option>
                                <option value="days">Once Every X Days</option>
                            </select>
                        </div>
                        <div class="form-group" id="group-freq-days" style="display:none;">
                            <label>Days count:</label>
                            <input type="number" id="freq-days-count" value="7" min="1" class="widefat">
                        </div>
                        <label class="trigger-option"><input type="checkbox" id="freq-hide-submitted" checked> Suppress after submission</label>
                    </div>
                </div>

            </div>
        </aside>
    </div>
</div>
