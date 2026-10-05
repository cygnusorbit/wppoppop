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
            <input type="text" id="wppoppop-popup-title" value="My Converting Popup" placeholder="Popup Name...">
            <input type="hidden" id="wppoppop-popup-uid" value="">
        </div>
        <div class="header-center">
            <label>Width: <input type="number" id="stage-width" value="640" style="width: 70px;"> px</label>
            <label style="margin-left: 10px;">Height: <input type="number" id="stage-height" value="400" style="width: 70px;"> px</label>
        </div>
        <div class="header-right">
            <button class="button button-secondary" id="wppoppop-btn-reset">Reset</button>
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
                <p class="panel-hint">Click an element to add to canvas:</p>
                <div class="element-item" data-type="text"><span class="dashicons dashicons-editor-textcolor"></span> Text Block</div>
                <div class="element-item" data-type="input"><span class="dashicons dashicons-email-alt"></span> Email Field</div>
                <div class="element-item" data-type="number"><span class="dashicons dashicons-calculator"></span> Number Field</div>
                <div class="element-item" data-type="button"><span class="dashicons dashicons-button"></span> Submit Button</div>
                <div class="element-item" data-type="html"><span class="dashicons dashicons-editor-code"></span> Raw HTML / Embed</div>
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

        <!-- Right Sidebar: Inspector, Triggers, Targeting, Logic, JS/CSS, Notifications -->
        <aside class="wppoppop-panel wppoppop-sidebar-right">
            <div class="panel-tabs">
                <button type="button" class="tab-btn active" data-tab="tab-props">Inspector</button>
                <button type="button" class="tab-btn" data-tab="tab-triggers">Triggers</button>
                <button type="button" class="tab-btn" data-tab="tab-targeting">Targeting</button>
                <button type="button" class="tab-btn" data-tab="tab-logic">Logic & Math</button>
                <button type="button" class="tab-btn" data-tab="tab-code">JS & CSS</button>
                <button type="button" class="tab-btn" data-tab="tab-notifications">Notif</button>
            </div>

            <!-- 1. Inspector Tab -->
            <div id="tab-props" class="tab-pane active">
                <div id="inspector-empty-state">Select any canvas layer to edit properties.</div>
                <div id="inspector-controls" style="display: none;">
                    <div class="form-group">
                        <label>Element ID / Binding Name:</label>
                        <input type="text" id="prop-field-name" placeholder="e.g. qty, total, discount" class="widefat">
                    </div>
                    <div class="form-group">
                        <label>Content / Label / Placeholder:</label>
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
                    <div class="form-group">
                        <label>Custom CSS Class:</label>
                        <input type="text" id="prop-custom-class" placeholder="e.g. accent-btn" class="widefat">
                    </div>
                    <div class="form-row-actions">
                        <button type="button" class="button" id="prop-duplicate-element">Duplicate</button>
                        <button type="button" class="button button-link-delete" id="prop-delete-element">Delete</button>
                    </div>
                </div>
            </div>

            <!-- 2. Triggers Tab -->
            <div id="tab-triggers" class="tab-pane">
                <h4>Displaying Modes</h4>
                <label class="trigger-option"><input type="checkbox" id="trig-load" checked> On Page Load</label>
                <div class="sub-option">Delay: <input type="number" id="trig-load-delay" value="0" min="0" style="width: 60px;"> sec</div>

                <label class="trigger-option"><input type="checkbox" id="trig-exit"> On Exit Intent</label>

                <label class="trigger-option"><input type="checkbox" id="trig-scroll"> On Scroll Depth</label>
                <div class="sub-option">Scroll %: <input type="number" id="trig-scroll-percent" value="50" min="1" max="100" style="width: 60px;"> %</div>

                <label class="trigger-option"><input type="checkbox" id="trig-idle"> On User Inactivity</label>
                <div class="sub-option">Idle: <input type="number" id="trig-idle-seconds" value="15" min="1" style="width: 60px;"> sec</div>

                <div class="form-group" style="margin-top: 15px;">
                    <label>Manual Click Selector:</label>
                    <input type="text" id="trig-click-selector" placeholder=".open-wppoppop" class="widefat">
                </div>
            </div>

            <!-- 3. Targeting Tab -->
            <div id="tab-targeting" class="tab-pane">
                <h4>Targeting System</h4>
                <div class="form-group">
                    <label>Display Location:</label>
                    <select id="target-scope" class="widefat">
                        <option value="everywhere">Everywhere (All Pages & Posts)</option>
                        <option value="posts">Single Posts Only</option>
                        <option value="pages">Pages Only</option>
                        <option value="specific">Specific Post/Page IDs</option>
                    </select>
                </div>
                <div class="form-group" id="group-specific-ids" style="display:none;">
                    <label>Post IDs (comma-separated):</label>
                    <input type="text" id="target-specific-ids" placeholder="e.g. 1, 14, 25" class="widefat">
                </div>
            </div>

            <!-- 4. Logic & Math Tab -->
            <div id="tab-logic" class="tab-pane">
                <h4>Conditional Logic</h4>
                <div class="form-group">
                    <label>If Field Name:</label>
                    <input type="text" id="logic-if-field" placeholder="e.g. qty" class="widefat">
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

                <hr>
                <h4>Math Expression</h4>
                <div class="form-group">
                    <label>Expression Formula:</label>
                    <input type="text" id="math-expression" placeholder="e.g. {qty} * 20" class="widefat">
                    <small>Wrap field names in curly braces.</small>
                </div>
                <div class="form-group">
                    <label>Output into Element ID:</label>
                    <input type="text" id="math-output-target" placeholder="e.g. elem_12345" class="widefat">
                </div>
            </div>

            <!-- 5. Custom JS & Scoped CSS Tab -->
            <div id="tab-code" class="tab-pane">
                <h4>Custom CSS</h4>
                <textarea id="custom-css-area" rows="4" class="widefat" placeholder="/* Custom CSS rules */"></textarea>

                <h4 style="margin-top: 15px;">On Init JS Handler</h4>
                <textarea id="custom-js-init" rows="3" class="widefat" placeholder="console.log('Popup initialized');"></textarea>

                <h4 style="margin-top: 15px;">On Submit JS Handler</h4>
                <textarea id="custom-js-submit" rows="3" class="widefat" placeholder="console.log('Form submitted');"></textarea>
            </div>

            <!-- 6. Notifications & Submissions Tab -->
            <div id="tab-notifications" class="tab-pane">
                <h4>Email Notifications</h4>
                <label class="trigger-option"><input type="checkbox" id="notif-enable" checked> Send Notification Email</label>
                <div class="form-group" style="margin-top: 10px;">
                    <label>Recipient Email:</label>
                    <input type="email" id="notif-recipient" placeholder="<?php echo esc_attr(get_option('admin_email')); ?>" class="widefat">
                </div>
                <div class="form-group">
                    <label>Email Subject:</label>
                    <input type="text" id="notif-subject" value="New Lead Submission" class="widefat">
                </div>

                <hr>
                <h4>Confirmation Action</h4>
                <div class="form-group">
                    <label>Success Message:</label>
                    <textarea id="act-success-msg" class="widefat" rows="2">Thank you! Your information has been registered.</textarea>
                </div>
                <div class="form-group">
                    <label>Redirect URL (Optional):</label>
                    <input type="url" id="act-redirect-url" placeholder="https://example.com/thanks" class="widefat">
                </div>
            </div>
        </aside>
    </div>
</div>
