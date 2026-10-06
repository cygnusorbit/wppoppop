<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div id="wppoppop-inspector-drawer">
    <div class="wppoppop-insp-header">
        <button type="button" id="wppoppop-inspector-close" title="Close Properties">&times;</button>
        <button type="button" class="wppoppop-insp-tab active" data-tab="basic">Basic</button>
        <button type="button" class="wppoppop-insp-tab" data-tab="style">Style</button>
        <button type="button" class="wppoppop-insp-tab" data-tab="logic">Logic</button>
    </div>

    <div style="flex:1;overflow-y:auto;padding:16px;">
        <!-- Basic Tab -->
        <div class="wppoppop-insp-content" id="insp-tab-basic">
            <div style="margin-bottom:12px;">
                <label>Layer Label</label>
                <input type="text" id="prop-layer-name">
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:12px;">
                <div>
                    <label>Top (px)</label>
                    <input type="number" id="prop-pos-top">
                </div>
                <div>
                    <label>Left (px)</label>
                    <input type="number" id="prop-pos-left">
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:12px;">
                <div>
                    <label>Width (px)</label>
                    <input type="number" id="prop-size-width">
                </div>
                <div>
                    <label>Height (px)</label>
                    <input type="number" id="prop-size-height">
                </div>
            </div>

            <div style="margin-bottom:12px;">
                <label>Content / Value</label>
                <textarea id="prop-content" rows="3"></textarea>
            </div>
        </div>

        <!-- Style Tab -->
        <div class="wppoppop-insp-content" id="insp-tab-style" style="display:none;">
            <div style="margin-bottom:12px;">
                <label>Font Family</label>
                <select id="prop-font-family">
                    <option value="inherit">Inherit System</option>
                    <option value="Arial, sans-serif">Arial</option>
                    <option value="'Helvetica Neue', sans-serif">Helvetica</option>
                    <option value="'Roboto', sans-serif">Roboto</option>
                    <option value="'Open Sans', sans-serif">Open Sans</option>
                    <option value="'Inter', sans-serif">Inter</option>
                    <option value="Georgia, serif">Georgia</option>
                </select>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:12px;">
                <div>
                    <label>Font Size (px)</label>
                    <input type="number" id="prop-font-size" value="14">
                </div>
                <div>
                    <label>Radius (px)</label>
                    <input type="number" id="prop-border-radius" value="0">
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:12px;">
                <div>
                    <label>Text Color</label>
                    <input type="text" id="prop-color" value="#1e293b">
                </div>
                <div>
                    <label>Background</label>
                    <input type="text" id="prop-bg-color" value="#ffffff">
                </div>
            </div>

            <div style="margin-bottom:12px;">
                <label>Opacity (0 to 1)</label>
                <input type="number" id="prop-opacity" step="0.1" min="0" max="1" value="1">
            </div>

            <div style="margin-bottom:12px;">
                <label>Animation Effect</label>
                <select id="prop-anim-effect">
                    <option value="none">None</option>
                    <option value="fadeIn">Fade In</option>
                    <option value="bounceIn">Bounce In</option>
                    <option value="zoomIn">Zoom In</option>
                    <option value="slideInUp">Slide In Up</option>
                    <option value="pulse">Pulse</option>
                    <option value="tada">Tada</option>
                </select>
            </div>
        </div>

        <!-- Logic Tab -->
        <div class="wppoppop-insp-content" id="insp-tab-logic" style="display:none;">
            <div style="margin-bottom:12px;">
                <label>Action on Click / Submit</label>
                <select id="prop-action-close">
                    <option value="none">Do Nothing</option>
                    <option value="next_screen">Proceed to Next Screen</option>
                    <option value="jump_screen">Jump to Specific Screen</option>
                    <option value="close">Close Popup</option>
                    <option value="redirect">Redirect to URL</option>
                </select>
            </div>

            <!-- Target Screen Dropdown for Screen Transitions -->
            <div id="prop-target-screen-wrap" style="margin-bottom:12px;">
                <label>Target Screen</label>
                <select id="prop-target-screen">
                    <!-- Populated dynamically with available screens -->
                </select>
            </div>

            <div id="prop-action-url-wrap" style="margin-bottom:12px;">
                <label>Redirect URL</label>
                <input type="url" id="prop-action-url" placeholder="https://example.com/checkout">
            </div>

            <div style="margin-bottom:12px;">
                <label style="display:flex;align-items:center;gap:6px;cursor:pointer;">
                    <input type="checkbox" id="prop-action-blank"> Open link in new window
                </label>
            </div>

            <!-- Screen 1 Conditional Routing Logic -->
            <div style="margin-top:14px;padding-top:12px;border-top:1px solid #1e293b;">
                <label style="font-weight:700;color:#f1f5f9;margin-bottom:6px;display:block;">Conditional Logic Routing</label>
                <p style="font-size:10px;color:#94a3b8;margin:0 0 8px 0;">Evaluate conditions from Screen 1 before progressing:</p>
                
                <div style="margin-bottom:8px;">
                    <label>If Field Value Matches</label>
                    <input type="text" id="prop-cond-val" placeholder="e.g. VIP or Yes">
                </div>
                <div style="margin-bottom:12px;">
                    <label>Then Jump to Screen</label>
                    <select id="prop-cond-target-screen">
                        <!-- Populated dynamically -->
                    </select>
                </div>
            </div>

            <div style="margin-top:12px;">
                <label>Custom JavaScript OnClick</label>
                <textarea id="prop-action-js" rows="3" placeholder="console.log('Action triggered');"></textarea>
            </div>
        </div>
    </div>
</div>
