<?php
if (!defined('ABSPATH')) {
    exit;
}
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
        <!-- Elements & Layers Left Sidebar -->
        <aside class="wppoppop-panel wppoppop-sidebar-left">
            <div class="panel-tabs">
                <button type="button" class="tab-btn active" data-tab="tab-elements">Elements</button>
                <button type="button" class="tab-btn" data-tab="tab-layers">Layers</button>
            </div>

            <div id="tab-elements" class="tab-pane active">
                <p class="panel-hint">Add element to active screen:</p>
                <div class="element-item" data-type="wheel"><span class="dashicons dashicons-chart-pie"></span> Lucky Wheel</div>
                <div class="element-item" data-type="text"><span class="dashicons dashicons-editor-textcolor"></span> Text Block</div>
                <div class="element-item" data-type="input"><span class="dashicons dashicons-email-alt"></span> Email Field</div>
                <div class="element-item" data-type="dropdown"><span class="dashicons dashicons-menu-alt"></span> Select Dropdown</div>
                <div class="element-item" data-type="radio"><span class="dashicons dashicons-marker"></span> Radio Options</div>
                <div class="element-item" data-type="checkbox"><span class="dashicons dashicons-yes"></span> Checkbox List</div>
                <div class="element-item" data-type="rating"><span class="dashicons dashicons-star-filled"></span> Star Rating</div>
                <div class="element-item" data-type="date"><span class="dashicons dashicons-calendar-alt"></span> Date Picker</div>
                <div class="element-item" data-type="number"><span class="dashicons dashicons-calculator"></span> Number Field</div>
                <div class="element-item" data-type="nextstep"><span class="dashicons dashicons-arrow-right-alt"></span> Next Step Button</div>
                <div class="element-item" data-type="button"><span class="dashicons dashicons-button"></span> Submit Button</div>
                <div class="element-item" data-type="html"><span class="dashicons dashicons-editor-code"></span> Raw HTML</div>
            </div>

            <div id="tab-layers" class="tab-pane">
                <ul id="wppoppop-layers-list" class="layers-list">
                    <li class="empty-layers">No elements on current screen.</li>
                </ul>
            </div>
        </aside>

        <!-- Canvas Viewport with Undo/Redo & Alignment Bar -->
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

        <!-- Right Configuration Sidebar (Vertical Accordion) -->
        <aside class="wppoppop-panel wppoppop-sidebar-right">
            <div class="wppoppop-accordion">

                <!-- 1. Inspector Accordion Item -->
                <div class="accordion-item active" data-accordion="inspector">
                    <div class="accordion-header">
                        <span class="accordion-title"><span class="dashicons dashicons-admin-generic"></span> Inspector</span>
                        <span class="accordion-icon dashicons dashicons-arrow-down-alt2"></span>
                    </div>
                    <div class="accordion-body">
                        <div id="inspector-empty-state">Select any canvas layer to edit properties.</div>
                        <div id="inspector-controls" style="display: none;">
                            <div class="form-group">
                                <label>Binding Key (e.g. prize, email, qty):</label>
                                <input type="text" id="prop-field-name" placeholder="e.g. prize, coupon, email" class="widefat">
                            </div>
                            <div class="form-group">
                                <label>Content / Label / Text:</label>
                                <input type="text" id="prop-content" class="widefat">
                            </div>
                            <div class="form-group">
                                <label>Font Family:</label>
                                <select id="prop-font-family" class="widefat">
                                    <option value="Inherit">Inherit Theme Font</option>
                                    <option value="Inter">Inter (Sans-serif)</option>
                                    <option value="Roboto">Roboto (Sans-serif)</option>
                                    <option value="Open Sans">Open Sans (Sans-serif)</option>
                                    <option value="Montserrat">Montserrat (Modern)</option>
                                    <option value="Poppins">Poppins (Geometric)</option>
                                    <option value="Playfair Display">Playfair Display (Serif)</option>
                                </select>
                            </div>
                            <div class="form-group" id="group-prop-options" style="display: none;">
                                <label>Options / Slices (comma-separated):</label>
                                <input type="text" id="prop-options" placeholder="10% OFF, FREE SHIPPING, 20% OFF" class="widefat">
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

                <!-- 2. Animation & Transitions -->
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

                <!-- 3. Backdrop & Dismissal -->
                <div class="accordion-item" data-accordion="backdrop">
                    <div class="accordion-header">
                        <span class="accordion-title"><span class="dashicons dashicons-art"></span> Backdrop & Dismissal</span>
                        <span class="accordion-icon dashicons dashicons-arrow-down-alt2"></span>
                    </div>
                    <div class="accordion-body">
                        <div class="form-group">
                            <label>Glassmorphism Blur (px):</label>
                            <input type="number" id="style-backdrop-blur" value="5" min="0" max="25" class="widefat">
                            <small>Set to 0 for dark overlay without blur effect.</small>
                        </div>
                        <hr>
                        <label class="trigger-option"><input type="checkbox" id="style-close-esc" checked> Close on ESC key</label>
                        <label class="trigger-option"><input type="checkbox" id="style-close-backdrop" checked> Close on backdrop click</label>
                    </div>
                </div>

                <!-- 4. Display Triggers -->
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
                    </div>
                </div>

                <!-- 5. Subscriber Autoresponder -->
                <div class="accordion-item" data-accordion="autoresponder">
                    <div class="accordion-header">
                        <span class="accordion-title"><span class="dashicons dashicons-email"></span> Autoresponder</span>
                        <span class="accordion-icon dashicons dashicons-arrow-down-alt2"></span>
                    </div>
                    <div class="accordion-body">
                        <label class="trigger-option"><input type="checkbox" id="ar-enable"> Send User Autoresponder Email</label>
                        <div class="form-group" style="margin-top: 10px;">
                            <label>Email Subject:</label>
                            <input type="text" id="ar-subject" value="Congratulations! Here is your exclusive reward" class="widefat">
                        </div>
                        <div class="form-group">
                            <label>Message Content ({prize}, {email}):</label>
                            <textarea id="ar-message" class="widefat" rows="5">Hi there,

Thank you for subscribing! Your won reward is: {prize}

Use coupon code WINNER at checkout.</textarea>
                        </div>
                    </div>
                </div>

                <!-- 6. Marketing & CRM Integrations -->
                <div class="accordion-item" data-accordion="marketing">
                    <div class="accordion-header">
                        <span class="accordion-title"><span class="dashicons dashicons-share-alt"></span> Marketing & Sync</span>
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
                    </div>
                </div>

                <!-- 7. Targeting & Geolocation -->
                <div class="accordion-item" data-accordion="targeting">
                    <div class="accordion-header">
                        <span class="accordion-title"><span class="dashicons dashicons-location-alt"></span> Targeting & Geo</span>
                        <span class="accordion-icon dashicons dashicons-arrow-down-alt2"></span>
                    </div>
                    <div class="accordion-body">
                        <div class="form-group">
                            <label>Country Rules:</label>
                            <select id="target-geo-mode" class="widefat">
                                <option value="all">Allow All Countries</option>
                                <option value="whitelist">Show Only In Selected Countries</option>
                                <option value="blacklist">Block Selected Countries</option>
                            </select>
                        </div>
                        <div class="form-group" id="group-geo-countries" style="display:none;">
                            <label>Country Codes (e.g. US, CA, GB):</label>
                            <input type="text" id="target-geo-countries" class="widefat">
                        </div>
                        <hr>
                        <div class="form-group">
                            <label>Device Viewports:</label>
                            <select id="target-devices" class="widefat">
                                <option value="all">All Devices</option>
                                <option value="desktop">Desktop Only</option>
                                <option value="mobile">Mobile / Tablet Only</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- 8. Frequency & Cookies -->
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
