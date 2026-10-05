<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-builder-wrap">
    <!-- Header -->
    <header class="wppoppop-builder-header">
        <div class="header-left">
            <span class="dashicons dashicons-external" style="margin-right: 8px; font-size: 24px;"></span>
            <input type="text" id="wppoppop-popup-title" value="My Converting Popup" placeholder="Popup Name...">
            <input type="hidden" id="wppoppop-popup-uid" value="">
        </div>
        <div class="header-center">
            <label>Width: <input type="number" id="stage-width" value="640" style="width: 70px;"> px</label>
            <label style="margin-left: 10px;">Height: <input type="number" id="stage-height" value="400" style="width: 70px;"> px</label>
        </div>
        <div class="header-right">
            <button class="button button-secondary" id="wppoppop-btn-reset">Clear Canvas</button>
            <button class="button button-primary" id="wppoppop-btn-save">Save Popup</button>
        </div>
    </header>

    <div class="wppoppop-builder-body">
        <!-- Left Sidebar: Elements & Layers -->
        <aside class="wppoppop-panel wppoppop-sidebar-left">
            <div class="panel-tabs">
                <button type="button" class="tab-btn active" data-tab="tab-elements">Elements</button>
                <button type="button" class="tab-btn" data-tab="tab-layers">Layers</button>
            </div>

            <div id="tab-elements" class="tab-pane active">
                <p class="panel-hint">Click an element to add it to the canvas:</p>
                <div class="element-item" data-type="text"><span class="dashicons dashicons-editor-textcolor"></span> Text Block</div>
                <div class="element-item" data-type="input"><span class="dashicons dashicons-email-alt"></span> Email Field</div>
                <div class="element-item" data-type="button"><span class="dashicons dashicons-button"></span> Submit Button</div>
                <div class="element-item" data-type="html"><span class="dashicons dashicons-editor-code"></span> Raw HTML / Embed</div>
            </div>

            <div id="tab-layers" class="tab-pane">
                <p class="panel-hint">Layer stack (top to bottom):</p>
                <ul id="wppoppop-layers-list" class="layers-list">
                    <li class="empty-layers">No elements added yet.</li>
                </ul>
            </div>
        </aside>

        <!-- Canvas Stage -->
        <main class="wppoppop-canvas-viewport">
            <div class="wppoppop-stage" id="wppoppop-stage" style="width: 640px; height: 400px; background-color: #ffffff;">
                <div class="canvas-grid-guide"></div>
            </div>
        </main>

        <!-- Right Sidebar: Inspector & Triggers -->
        <aside class="wppoppop-panel wppoppop-sidebar-right">
            <div class="panel-tabs">
                <button type="button" class="tab-btn active" data-tab="tab-props">Inspector</button>
                <button type="button" class="tab-btn" data-tab="tab-triggers">Triggers</button>
                <button type="button" class="tab-btn" data-tab="tab-actions">Submission</button>
            </div>

            <!-- Inspector Tab -->
            <div id="tab-props" class="tab-pane active">
                <div id="inspector-empty-state">Select any canvas layer to edit properties.</div>
                <div id="inspector-controls" style="display: none;">
                    <div class="form-group">
                        <label>Content / Label:</label>
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
                        <label>Element Background:</label>
                        <input type="color" id="prop-bg-color" value="#00a32a">
                    </div>
                    <div class="form-group">
                        <label>Custom CSS Class:</label>
                        <input type="text" id="prop-custom-class" placeholder="e.g. my-accent-btn" class="widefat">
                    </div>
                    <div class="form-row-actions">
                        <button type="button" class="button" id="prop-duplicate-element">Duplicate</button>
                        <button type="button" class="button button-link-delete" id="prop-delete-element">Delete</button>
                    </div>
                </div>
            </div>

            <!-- Display Modes & Triggers Tab -->
            <div id="tab-triggers" class="tab-pane">
                <h4>Display Modes</h4>
                <label class="trigger-option">
                    <input type="checkbox" id="trig-load" checked> On Page Load
                </label>
                <div class="sub-option">
                    Delay: <input type="number" id="trig-load-delay" value="0" min="0" style="width: 60px;"> sec
                </div>

                <label class="trigger-option">
                    <input type="checkbox" id="trig-exit"> On Exit Intent
                </label>

                <label class="trigger-option">
                    <input type="checkbox" id="trig-scroll"> On Scroll Depth
                </label>
                <div class="sub-option">
                    Scroll %: <input type="number" id="trig-scroll-percent" value="50" min="1" max="100" style="width: 60px;"> %
                </div>

                <label class="trigger-option">
                    <input type="checkbox" id="trig-idle"> On User Inactivity
                </label>
                <div class="sub-option">
                    Idle: <input type="number" id="trig-idle-seconds" value="15" min="1" style="width: 60px;"> sec
                </div>

                <div class="form-group" style="margin-top: 15px;">
                    <label>Click Trigger Selector:</label>
                    <input type="text" id="trig-click-selector" placeholder=".open-popup-btn" class="widefat">
                </div>
            </div>

            <!-- Submission Actions Tab -->
            <div id="tab-actions" class="tab-pane">
                <h4>Post-Submission</h4>
                <div class="form-group">
                    <label>Success Message:</label>
                    <textarea id="act-success-msg" class="widefat" rows="3">Thank you! Your information has been registered.</textarea>
                </div>
                <div class="form-group">
                    <label>Redirect URL (Optional):</label>
                    <input type="url" id="act-redirect-url" placeholder="https://example.com/thanks" class="widefat">
                </div>
            </div>
        </aside>
    </div>
</div>
