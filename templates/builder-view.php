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
                <button type="button" class="button btn-screen-toggle" data-screen="2">Screen 2 (Step 2)</button>
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
        <!-- Elements Sidebar -->
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
                <div class="element-item" data-type="nextstep"><span class="dashicons dashicons-arrow-right-alt"></span> Next Step Button</div>
                <div class="element-item" data-type="button"><span class="dashicons dashicons-button"></span> Submit Button</div>
                <div class="element-item" data-type="html"><span class="dashicons dashicons-editor-code"></span> Raw HTML / Embed</div>
            </div>

            <div id="tab-layers" class="tab-pane">
                <ul id="wppoppop-layers-list" class="layers-list">
                    <li class="empty-layers">No elements on current screen.</li>
                </ul>
            </div>
        </aside>

        <!-- Canvas Stage -->
        <main class="wppoppop-canvas-viewport">
            <div class="wppoppop-stage" id="wppoppop-stage" style="width: 640px; height: 400px; background-color: #ffffff;">
                <div class="canvas-grid-guide"></div>
            </div>
        </main>

        <!-- Configuration Sidebar -->
        <aside class="wppoppop-panel wppoppop-sidebar-right">
            <div class="panel-tabs">
                <button type="button" class="tab-btn active" data-tab="tab-props">Inspector</button>
                <button type="button" class="tab-btn" data-tab="tab-animation">Animation</button>
                <button type="button" class="tab-btn" data-tab="tab-triggers">Triggers</button>
                <button type="button" class="tab-btn" data-tab="tab-frequency">Cookies</button>
                <button type="button" class="tab-btn" data-tab="tab-targeting">Targeting</button>
            </div>

            <!-- Inspector Tab -->
            <div id="tab-props" class="tab-pane active">
                <div id="inspector-empty-state">Select any canvas layer to edit properties.</div>
                <div id="inspector-controls" style="display: none;">
                    <div class="form-group">
                        <label>Binding Key / Name (for Live Token {name}):</label>
                        <input type="text" id="prop-field-name" placeholder="e.g. email, user_name, total" class="widefat">
                    </div>
                    <div class="form-group">
                        <label>Content / Label / Text:</label>
                        <input type="text" id="prop-content" class="widefat">
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

            <!-- Layer Animation Tab -->
            <div id="tab-animation" class="tab-pane">
                <h4>Layer-by-Layer Transitions</h4>
                <div id="anim-empty-state">Select a layer to customize entrance animation.</div>
                <div id="anim-controls" style="display: none;">
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

            <!-- Triggers Tab -->
            <div id="tab-triggers" class="tab-pane">
                <h4>Displaying Modes</h4>
                <label class="trigger-option"><input type="checkbox" id="trig-load" checked> On Page Load</label>
                <div class="sub-option">Delay: <input type="number" id="trig-load-delay" value="0" min="0" style="width: 60px;"> sec</div>
                <label class="trigger-option"><input type="checkbox" id="trig-exit"> On Exit Intent</label>
                <label class="trigger-option"><input type="checkbox" id="trig-scroll"> On Scroll Depth (> 50%)</label>
                <label class="trigger-option"><input type="checkbox" id="trig-idle"> On User Inactivity (15s)</label>
            </div>

            <!-- Frequency & Cookies Tab -->
            <div id="tab-frequency" class="tab-pane">
                <h4>Frequency Capping</h4>
                <div class="form-group">
                    <label>Popup Display Frequency:</label>
                    <select id="freq-mode" class="widefat">
                        <option value="everytime">Every Page View</option>
                        <option value="once_session">Once Per Browser Session</option>
                        <option value="days">Once Every X Days</option>
                    </select>
                </div>
                <div class="form-group" id="group-freq-days" style="display:none;">
                    <label>Days before showing again:</label>
                    <input type="number" id="freq-days-count" value="7" min="1" class="widefat">
                </div>
                <hr>
                <label class="trigger-option"><input type="checkbox" id="freq-hide-submitted" checked> Do not show again after form submission</label>
            </div>

            <!-- Device & Location Targeting Tab -->
            <div id="tab-targeting" class="tab-pane">
                <h4>Device Targeting</h4>
                <div class="form-group">
                    <label>Target Viewports:</label>
                    <select id="target-devices" class="widefat">
                        <option value="all">All Devices (Desktop & Mobile)</option>
                        <option value="desktop">Desktop Only (> 768px)</option>
                        <option value="mobile">Mobile / Tablet Only (<= 768px)</option>
                    </select>
                </div>

                <hr>
                <h4>Location Targeting</h4>
                <div class="form-group">
                    <label>Display Location:</label>
                    <select id="target-scope" class="widefat">
                        <option value="everywhere">Everywhere</option>
                        <option value="posts">Single Posts</option>
                        <option value="pages">Pages Only</option>
                        <option value="specific">Specific Post IDs</option>
                    </select>
                </div>
                <div class="form-group" id="group-specific-ids" style="display:none;">
                    <label>Post IDs (comma-separated):</label>
                    <input type="text" id="target-specific-ids" placeholder="e.g. 1, 14, 25" class="widefat">
                </div>
            </div>
        </aside>
    </div>
</div>
