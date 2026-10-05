<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-builder-wrap">
    <!-- Header Toolbar -->
    <header class="wppoppop-builder-header">
        <div class="header-left">
            <span class="dashicons dashicons-external" style="font-size: 24px; margin-right: 8px;"></span>
            <input type="text" id="wppoppop-popup-title" value="New Converting Popup" placeholder="Popup Name...">
            <input type="hidden" id="wppoppop-popup-uid" value="">
        </div>
        <div class="header-center">
            <div class="screen-switch-bar" id="screen-switch-container">
                <button type="button" class="button btn-screen-toggle active" data-screen="1">Screen 1</button>
                <button type="button" class="button btn-screen-toggle" data-screen="2">Screen 2</button>
                <button type="button" class="button btn-screen-toggle" data-screen="3">Screen 3</button>
            </div>
            <span class="toolbar-sep">|</span>
            <button type="button" class="button button-small btn-viewport-toggle active" data-viewport="desktop">Desktop</button>
            <button type="button" class="button button-small btn-viewport-toggle" data-viewport="mobile">Mobile</button>
            <label style="margin-left: 15px;">W: <input type="number" id="stage-width" value="640" style="width: 65px;"> px</label>
            <label style="margin-left: 8px;">H: <input type="number" id="stage-height" value="400" style="width: 65px;"> px</label>
        </div>
        <div class="header-right">
            <button class="button button-secondary" id="wppoppop-btn-embed">Embed Code</button>
            <button class="button button-secondary" id="wppoppop-btn-preview">Live Preview</button>
            <button class="button button-primary" id="wppoppop-btn-save">Save Popup</button>
        </div>
    </header>

    <div class="wppoppop-builder-body">
        <!-- Elements & Layers Left Sidebar -->
        <aside class="wppoppop-panel wppoppop-sidebar-left">
            <div class="panel-tabs">
                <button type="button" class="tab-btn active" data-tab="tab-elements">Elements</button>
                <button type="button" class="tab-btn" data-tab="tab-layers">Layers</button>
            </div>

            <!-- Complete 19 Elements Registry -->
            <div id="tab-elements" class="tab-pane active">
                <p class="panel-hint">Add element to active screen:</p>
                <div class="element-item" data-type="text"><span class="dashicons dashicons-editor-textcolor"></span> Text Block</div>
                <div class="element-item" data-type="input"><span class="dashicons dashicons-email-alt"></span> Email Field</div>
                <div class="element-item" data-type="number"><span class="dashicons dashicons-calculator"></span> Number Field</div>
                <div class="element-item" data-type="dropdown"><span class="dashicons dashicons-menu-alt"></span> Select Dropdown</div>
                <div class="element-item" data-type="radio"><span class="dashicons dashicons-marker"></span> Radio Options</div>
                <div class="element-item" data-type="checkbox"><span class="dashicons dashicons-yes"></span> Checkbox List</div>
                <div class="element-item" data-type="rating"><span class="dashicons dashicons-star-filled"></span> Star Rating</div>
                <div class="element-item" data-type="date"><span class="dashicons dashicons-calendar-alt"></span> Date Picker</div>
                <div class="element-item" data-type="slider"><span class="dashicons dashicons-leftright"></span> Range Slider</div>
                <div class="element-item" data-type="signature"><span class="dashicons dashicons-edit"></span> Signature Pad</div>
                <div class="element-item" data-type="wheel"><span class="dashicons dashicons-chart-pie"></span> Lucky Wheel</div>
                <div class="element-item" data-type="scratch"><span class="dashicons dashicons-tickets-alt"></span> Scratch Card</div>
                <div class="element-item" data-type="countdown"><span class="dashicons dashicons-clock"></span> Countdown Timer</div>
                <div class="element-item" data-type="progress"><span class="dashicons dashicons-ellipsis"></span> Progress Bar</div>
                <div class="element-item" data-type="file"><span class="dashicons dashicons-upload"></span> File Upload</div>
                <div class="element-item" data-type="nextstep"><span class="dashicons dashicons-arrow-right-alt"></span> Next Step Button</div>
                <div class="element-item" data-type="button"><span class="dashicons dashicons-button"></span> Submit Button</div>
                <div class="element-item" data-type="pay_btn"><span class="dashicons dashicons-cart"></span> Payment Button</div>
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
                <label style="font-size:12px;cursor:pointer;"><input type="checkbox" id="chk-grid-snap" checked> Snap 10px Grid</label>
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

        <!-- Right Configuration Sidebar: Full 15 Vertical Accordion Sections -->
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
                                <label>Binding Key (e.g. email, prize, coupon):</label>
                                <input type="text" id="prop-field-name" class="widefat">
                            </div>
                            <div class="form-group">
                                <label>Content / Dynamic Tokens ({user_name}, {query:utm_source}):</label>
                                <input type="text" id="prop-content" class="widefat">
                            </div>
                            <div class="form-group" id="group-prop-goto" style="display:none;">
                                <label>Navigate to Screen:</label>
                                <select id="prop-goto-screen" class="widefat">
                                    <option value="1">Screen 1</option>
                                    <option value="2">Screen 2</option>
                                    <option value="3">Screen 3</option>
                                </select>
                            </div>
                            <div class="form-group" id="group-prop-options" style="display:none;">
                                <label>Options / Slices (comma-separated):</label>
                                <input type="text" id="prop-options" class="widefat">
                            </div>
                            <div class="form-group" id="group-prop-validation" style="background:#f8fafc; padding:10px; border-radius:4px; border:1px solid #e2e8f0;">
                                <label style="font-weight:700;"><input type="checkbox" id="prop-required"> Mandatory Field (Required)</label>
                                <div style="margin-top:6px;">
                                    <label>Custom Error Bubble Text:</label>
                                    <input type="text" id="prop-error-msg" value="Please fill out this field." class="widefat">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Font Family:</label>
                                <select id="prop-font-family" class="widefat">
                                    <option value="Inherit">Inherit Theme Font</option>
                                    <option value="Inter">Inter (Sans-serif)</option>
                                    <option value="Roboto">Roboto (Sans-serif)</option>
                                    <option value="Montserrat">Montserrat (Modern)</option>
                                </select>
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

                <!-- 2. Layer Animation -->
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
                <div class="accordion-item" data-accordion="sounds">
                    <div class="accordion-header">
                        <span class="accordion-title"><span class="dashicons dashicons-format-audio"></span> Sound Effects</span>
                        <span class="accordion-icon dashicons dashicons-arrow-down-alt2"></span>
                    </div>
                    <div class="accordion-body">
                        <label class="trigger-option"><input type="checkbox" id="snd-enable" checked> Enable Web Audio Synth Chimes</label>
                    </div>
                </div>

                <!-- 4. Box & Backdrop Styling -->
                <div class="accordion-item" data-accordion="backdrop">
                    <div class="accordion-header">
                        <span class="accordion-title"><span class="dashicons dashicons-art"></span> Box & Backdrop Styling</span>
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
                        <label class="trigger-option"><input type="checkbox" id="trig-adblock"> On AdBlock Detected</label>
                        <label class="trigger-option"><input type="checkbox" id="trig-mobile-back"> On Mobile Back-Button</label>
                    </div>
                </div>

                <!-- 6. Conditional Logic & Math -->
                <div class="accordion-item" data-accordion="logic">
                    <div class="accordion-header">
                        <span class="accordion-title"><span class="dashicons dashicons-calculator"></span> Conditional Logic & Math</span>
                        <span class="accordion-icon dashicons dashicons-arrow-down-alt2"></span>
                    </div>
                    <div class="accordion-body">
                        <div class="form-group">
                            <label>Calculation Formula (e.g. {qty} * 25):</label>
                            <input type="text" id="math-expression" class="widefat">
                        </div>
                        <div class="form-group">
                            <label>Target Layer ID for Calculation Output:</label>
                            <input type="text" id="math-output-target" class="widefat">
                        </div>
                    </div>
                </div>

                <!-- 7. Sticky Side Tabs -->
                <div class="accordion-item" data-accordion="sidetabs">
                    <div class="accordion-header">
                        <span class="accordion-title"><span class="dashicons dashicons-tag"></span> Sticky Side Tabs</span>
                        <span class="accordion-icon dashicons dashicons-arrow-down-alt2"></span>
                    </div>
                    <div class="accordion-body">
                        <label class="trigger-option"><input type="checkbox" id="tab-enable"> Enable Sticky Side Tab</label>
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

                <!-- 8. Payments & Checkout -->
                <div class="accordion-item" data-accordion="payments">
                    <div class="accordion-header">
                        <span class="accordion-title"><span class="dashicons dashicons-money-alt"></span> Payments & Checkout</span>
                        <span class="accordion-icon dashicons dashicons-arrow-down-alt2"></span>
                    </div>
                    <div class="accordion-body">
                        <label class="trigger-option"><input type="checkbox" id="pay-enable"> Enable Checkout</label>
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
                    <div class="accordion-header">
                        <span class="accordion-title"><span class="dashicons dashicons-download"></span> Secure Downloads</span>
                        <span class="accordion-icon dashicons dashicons-arrow-down-alt2"></span>
                    </div>
                    <div class="accordion-body">
                        <label class="trigger-option"><input type="checkbox" id="dl-enable"> Enable File Download on Submit</label>
                        <div class="form-group" style="margin-top:6px;">
                            <label>Media File URL:</label>
                            <input type="url" id="dl-url" placeholder="https://site.com/file.zip" class="widefat">
                        </div>
                    </div>
                </div>

                <!-- 10. Video Listeners -->
                <div class="accordion-item" data-accordion="video">
                    <div class="accordion-header">
                        <span class="accordion-title"><span class="dashicons dashicons-video-alt3"></span> Video Listeners</span>
                        <span class="accordion-icon dashicons dashicons-arrow-down-alt2"></span>
                    </div>
                    <div class="accordion-body">
                        <label class="trigger-option"><input type="checkbox" id="vid-enable"> Trigger on Video Ended</label>
                    </div>
                </div>

                <!-- 11. Subscriber Autoresponder -->
                <div class="accordion-item" data-accordion="autoresponder">
                    <div class="accordion-header">
                        <span class="accordion-title"><span class="dashicons dashicons-email"></span> Autoresponder</span>
                        <span class="accordion-icon dashicons dashicons-arrow-down-alt2"></span>
                    </div>
                    <div class="accordion-body">
                        <label class="trigger-option"><input type="checkbox" id="ar-enable"> Send User Autoresponder Email</label>
                        <div class="form-group" style="margin-top:6px;">
                            <label>Subject:</label>
                            <input type="text" id="ar-subject" value="Thank you!" class="widefat">
                        </div>
                        <div class="form-group">
                            <label>Message Content:</label>
                            <textarea id="ar-message" class="widefat" rows="4">Thank you for subscribing!</textarea>
                        </div>
                    </div>
                </div>

                <!-- 12. Marketing & Webhooks -->
                <div class="accordion-item" data-accordion="marketing">
                    <div class="accordion-header">
                        <span class="accordion-title"><span class="dashicons dashicons-share-alt"></span> Marketing & Webhooks</span>
                        <span class="accordion-icon dashicons dashicons-arrow-down-alt2"></span>
                    </div>
                    <div class="accordion-body">
                        <div class="form-group">
                            <label>Webhook URL:</label>
                            <input type="url" id="mkt-webhook-url" placeholder="https://webhook.site/..." class="widefat">
                        </div>
                        <div class="form-group">
                            <label>HMAC-SHA256 Secret:</label>
                            <input type="password" id="mkt-webhook-secret" class="widefat">
                        </div>
                        <button type="button" class="button" id="btn-test-webhook">Ping Webhook</button>
                    </div>
                </div>

                <!-- 13. Twilio SMS Alerts -->
                <div class="accordion-item" data-accordion="twilio">
                    <div class="accordion-header">
                        <span class="accordion-title"><span class="dashicons dashicons-phone"></span> Twilio SMS Alerts</span>
                        <span class="accordion-icon dashicons dashicons-arrow-down-alt2"></span>
                    </div>
                    <div class="accordion-body">
                        <label class="trigger-option"><input type="checkbox" id="sms-enable"> Enable Real-Time SMS</label>
                        <div class="form-group" style="margin-top:6px;">
                            <label>Account SID:</label>
                            <input type="text" id="sms-sid" class="widefat">
                        </div>
                        <div class="form-group">
                            <label>Auth Token:</label>
                            <input type="password" id="sms-token" class="widefat">
                        </div>
                        <div class="form-group">
                            <label>From Number:</label>
                            <input type="text" id="sms-from" class="widefat">
                        </div>
                        <div class="form-group">
                            <label>To Mobile Number:</label>
                            <input type="text" id="sms-to" class="widefat">
                        </div>
                        <button type="button" class="button" id="btn-test-sms">Test SMS Dispatch</button>
                    </div>
                </div>

                <!-- 14. Targeting & Attribution -->
                <div class="accordion-item" data-accordion="targeting">
                    <div class="accordion-header">
                        <span class="accordion-title"><span class="dashicons dashicons-location-alt"></span> Targeting & Attribution</span>
                        <span class="accordion-icon dashicons dashicons-arrow-down-alt2"></span>
                    </div>
                    <div class="accordion-body">
                        <div class="form-group">
                            <label>Visitor Authentication:</label>
                            <select id="target-auth-mode" class="widefat">
                                <option value="all">All Visitors</option>
                                <option value="guests_only">Guests / Logged-Out Only</option>
                                <option value="logged_in_only">Logged-In Users Only</option>
                            </select>
                        </div>
                        <div class="form-group" id="group-target-roles">
                            <label>Target User Roles (comma-separated):</label>
                            <input type="text" id="target-roles" placeholder="subscriber, customer" class="widefat">
                        </div>
                        <hr>
                        <div class="form-group">
                            <label>Require URL Query / UTM Param:</label>
                            <input type="text" id="target-url-param-key" placeholder="e.g. utm_campaign, ref" class="widefat">
                        </div>
                        <div class="form-group">
                            <label>Expected Parameter Value:</label>
                            <input type="text" id="target-url-param-val" placeholder="e.g. summer_sale" class="widefat">
                        </div>
                        <hr>
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
                            <label>Country Rules:</label>
                            <select id="target-geo-mode" class="widefat">
                                <option value="all">All Countries</option>
                                <option value="whitelist">Show Only In Selected</option>
                                <option value="blacklist">Block Selected</option>
                            </select>
                        </div>
                        <div class="form-group" id="group-geo-countries" style="display:none;">
                            <label>Country Codes (e.g. US, CA, GB):</label>
                            <input type="text" id="target-geo-countries" class="widefat">
                        </div>
                    </div>
                </div>

                <!-- 15. Frequency Capping & Cookies -->
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
