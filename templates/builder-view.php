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
            <input type="text" id="wppoppop-popup-title" value="New High-Converting Popup" placeholder="Popup Name...">
            <input type="hidden" id="wppoppop-popup-uid" value="">
        </div>
        <div class="header-center">
            <button type="button" class="button button-secondary" id="btn-open-templates"><span class="dashicons dashicons-layout" style="vertical-align: middle;"></span> Presets</button>
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
                <p class="panel-hint">Click an element to add to canvas:</p>
                <div class="element-item" data-type="text"><span class="dashicons dashicons-editor-textcolor"></span> Text Block</div>
                <div class="element-item" data-type="input"><span class="dashicons dashicons-email-alt"></span> Email Field</div>
                <div class="element-item" data-type="number"><span class="dashicons dashicons-calculator"></span> Number Field</div>
                <div class="element-item" data-type="button"><span class="dashicons dashicons-button"></span> Action Button</div>
                <div class="element-item" data-type="paybutton"><span class="dashicons dashicons-cart"></span> Payment Button</div>
                <div class="element-item" data-type="html"><span class="dashicons dashicons-editor-code"></span> Raw HTML</div>
            </div>

            <div id="tab-layers" class="tab-pane">
                <ul id="wppoppop-layers-list" class="layers-list">
                    <li class="empty-layers">No elements on canvas.</li>
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
                <button type="button" class="tab-btn active" data-tab="tab-props">Props</button>
                <button type="button" class="tab-btn" data-tab="tab-triggers">Triggers</button>
                <button type="button" class="tab-btn" data-tab="tab-sidetab">Side Tab</button>
                <button type="button" class="tab-btn" data-tab="tab-payment">Pay</button>
                <button type="button" class="tab-btn" data-tab="tab-downloads">Downloads</button>
                <button type="button" class="tab-btn" data-tab="tab-video">Video</button>
            </div>

            <!-- Inspector Tab -->
            <div id="tab-props" class="tab-pane active">
                <div id="inspector-empty-state">Select any canvas layer to edit properties.</div>
                <div id="inspector-controls" style="display: none;">
                    <div class="form-group">
                        <label>Binding Key:</label>
                        <input type="text" id="prop-field-name" placeholder="e.g. qty, email, item" class="widefat">
                    </div>
                    <div class="form-group">
                        <label>Content / Label:</label>
                        <input type="text" id="prop-content" class="widefat">
                    </div>
                    <div class="form-group">
                        <label>Font Size (px):</label>
                        <input type="number" id="prop-font-size" value="16" min="10" max="72">
                    </div>
                    <div class="form-group">
                        <label>Color:</label>
                        <input type="color" id="prop-color" value="#222222">
                    </div>
                    <div class="form-group">
                        <label>Background:</label>
                        <input type="color" id="prop-bg-color" value="#00a32a">
                    </div>
                    <div class="form-row-actions">
                        <button type="button" class="button" id="prop-duplicate-element">Duplicate</button>
                        <button type="button" class="button button-link-delete" id="prop-delete-element">Delete</button>
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

            <!-- Side Tabs Add-On Tab -->
            <div id="tab-sidetab" class="tab-pane">
                <h4>Sticky Side Tab Add-On</h4>
                <label class="trigger-option"><input type="checkbox" id="sidetab-enable"> Enable Sticky Side Tab</label>
                <div class="form-group" style="margin-top: 10px;">
                    <label>Tab Text Label:</label>
                    <input type="text" id="sidetab-label" value="Special Offer" class="widefat">
                </div>
                <div class="form-group">
                    <label>Position:</label>
                    <select id="sidetab-position" class="widefat">
                        <option value="right">Right Screen Edge</option>
                        <option value="left">Left Screen Edge</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Tab Background Color:</label>
                    <input type="color" id="sidetab-bg" value="#2271b1">
                </div>
            </div>

            <!-- Payments Tab -->
            <div id="tab-payment" class="tab-pane">
                <h4>Payment Popups</h4>
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
                    <label>Payment Gateway Provider:</label>
                    <select id="pay-gateway" class="widefat">
                        <option value="Stripe">Stripe Checkout</option>
                        <option value="PayPal">PayPal Commerce</option>
                    </select>
                </div>
            </div>

            <!-- Secure Downloads Add-On Tab -->
            <div id="tab-downloads" class="tab-pane">
                <h4>Secure Downloads Add-On</h4>
                <label class="trigger-option"><input type="checkbox" id="dl-enable"> Enable Secure Download</label>
                <div class="form-group" style="margin-top: 10px;">
                    <label>Protected Media URL:</label>
                    <input type="url" id="dl-file-url" placeholder="https://site.com/wp-content/uploads/file.pdf" class="widefat">
                </div>
                <div class="form-group">
                    <label>Token Expiry (Hours):</label>
                    <input type="number" id="dl-expiry-hours" value="24" min="1" class="widefat">
                </div>
            </div>

            <!-- Video Events Listener Add-On Tab -->
            <div id="tab-video" class="tab-pane">
                <h4>Video Events Listener</h4>
                <label class="trigger-option"><input type="checkbox" id="video-enable"> Enable Video Triggers</label>
                <div class="form-group" style="margin-top: 10px;">
                    <label>Trigger on Video Event:</label>
                    <select id="video-trigger-mode" class="widefat">
                        <option value="ended">When Video Finishes Playing</option>
                        <option value="play">When Video Starts Playing</option>
                    </select>
                </div>
            </div>
        </aside>
    </div>

    <!-- Presets Modal -->
    <div id="wppoppop-presets-modal" class="presets-modal-backdrop" style="display:none;">
        <div class="presets-modal-box">
            <h2>Select a Library Template Preset</h2>
            <div class="preset-cards">
                <div class="preset-card" data-preset="newsletter">
                    <h3>1. Minimal Newsletter</h3>
                    <p>Clean design optimized for email capturing.</p>
                    <button type="button" class="button button-primary btn-apply-preset">Load Template</button>
                </div>
                <div class="preset-card" data-preset="payment">
                    <h3>2. Instant Checkout</h3>
                    <p>Monetize content directly with payment button element.</p>
                    <button type="button" class="button button-primary btn-apply-preset">Load Template</button>
                </div>
                <div class="preset-card" data-preset="download">
                    <h3>3. Lead Magnet PDF</h3>
                    <p>Delivers secure, encrypted download link upon signup.</p>
                    <button type="button" class="button button-primary btn-apply-preset">Load Template</button>
                </div>
            </div>
            <p style="text-align: right; margin-top: 20px;">
                <button type="button" class="button button-secondary" id="btn-close-presets">Close</button>
            </p>
        </div>
    </div>
</div>
