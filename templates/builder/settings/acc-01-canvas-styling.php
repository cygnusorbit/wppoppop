<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<!-- 1. Dedicated Per-Canvas Settings with Bullets, Color, Animate.style & Logic -->
            <div class="wppoppop-acc-item">
                <button type="button" class="wppoppop-acc-header active">
                    <span>1. Canvas Settings (<strong id="set-current-canvas-badge">Canvas 1</strong>)</span>
                </button>
                <div class="wppoppop-acc-body" style="display:block;">
                    
                    <!-- Canvas Selection Bullets Strip -->
                    <div style="margin-bottom:14px;">
                        <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;text-transform:uppercase;">SELECT CANVAS TO CONFIGURE</label>
                        <div class="wppoppop-canvas-bullets-bar" id="wppoppop-canvas-bullets-container">
                            <!-- Dynamically populated canvas bullets with status dots -->
                        </div>
                    </div>

                    <!-- Canvas Settings Sub-Tabs (General & Color vs Logic) -->
                    <div class="wppoppop-canvas-subtabs">
                        <button type="button" class="wppoppop-csubtab active" data-tab="general">General & Color</button>
                        <button type="button" class="wppoppop-csubtab" data-tab="logic">Canvas Logic</button>
                    </div>

                    <!-- SUBTAB 1: GENERAL, SIZE, COLOR & ANIMATION -->
                    <div class="wppoppop-csubcontent active" id="csub-tab-general">
                        <div style="margin-bottom:12px;">
                            <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">CANVAS NAME</label>
                            <input type="text" id="set-canvas-name" value="Canvas 1" class="widefat" placeholder="e.g. Lead Opt-in, Thank You..." style="font-size:12px;">
                        </div>

                        <div style="margin-bottom:12px;">
                            <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">CANVAS SIZE (WIDTH &times; HEIGHT PX)</label>
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                                <div>
                                    <label style="font-size:10px;color:#64748b;">Width (px)</label>
                                    <input type="number" id="set-canvas-width" value="640" min="200" max="1600" class="widefat" style="font-size:12px;">
                                </div>
                                <div>
                                    <label style="font-size:10px;color:#64748b;">Height (px)</label>
                                    <input type="number" id="set-canvas-height" value="400" min="150" max="1200" class="widefat" style="font-size:12px;">
                                </div>
                            </div>
                        </div>
                        <!-- Canvas Overflow Toggle (WPPOPPOP_CANVAS_OVERFLOW) -->
                        <style>
                            .wppoppop-ovf-switch{position:relative;display:inline-block;width:40px;height:22px;flex-shrink:0;}
                            .wppoppop-ovf-switch input{position:absolute;opacity:0;width:100%;height:100%;margin:0;cursor:pointer;z-index:2;}
                            .wppoppop-ovf-track{position:absolute;inset:0;background:#94a3b8;border-radius:22px;transition:background .18s ease;-webkit-transition:background .18s ease;}
                            .wppoppop-ovf-track:before{content:"";position:absolute;width:16px;height:16px;left:3px;top:3px;background:#fff;border-radius:50%;transition:transform .18s ease;-webkit-transition:-webkit-transform .18s ease;}
                            .wppoppop-ovf-switch input:checked + .wppoppop-ovf-track{background:#2563eb;}
                            .wppoppop-ovf-switch input:checked + .wppoppop-ovf-track:before{-webkit-transform:translateX(18px);transform:translateX(18px);}
                            .wppoppop-ovf-switch input:focus-visible + .wppoppop-ovf-track{outline:2px solid #93c5fd;outline-offset:2px;}
                        </style>
                        <div class="wppoppop-field-group" style="margin-bottom:12px;padding-bottom:10px;border-bottom:1px solid #334155;">
                            <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;">
                                <label for="set-canvas-allow-overflow" style="font-size:11px;font-weight:700;color:#475569;margin:0;text-transform:uppercase;cursor:pointer;">ALLOW CANVAS ELEMENT OVERFLOW <span id="set-canvas-overflow-state" style="color:#2563eb;">OFF</span></label>
                                <span class="wppoppop-ovf-switch">
                                    <input type="checkbox" id="set-canvas-allow-overflow" value="1" role="switch" aria-label="Allow canvas element overflow">
                                    <span class="wppoppop-ovf-track"></span>
                                </span>
                            </div>
                            <span style="font-size:10px;color:#64748b;display:block;margin-top:4px;">ON: elements may extend beyond the canvas edges (visible). OFF: anything outside the canvas is clipped.</span>
                        </div>

                        <!-- WPPOPPOP_DEFAULT_CLOSE: per-canvas default close button toggle -->
                        <div class="wppoppop-field-group" style="margin-bottom:12px;padding-bottom:10px;border-bottom:1px solid #334155;">
                            <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;">
                                <label for="set-canvas-close-btn" style="font-size:11px;font-weight:700;color:#475569;margin:0;text-transform:uppercase;cursor:pointer;">SHOW DEFAULT CLOSE BUTTON</label>
                                <span class="wppoppop-ovf-switch">
                                    <input type="checkbox" id="set-canvas-close-btn" value="1" role="switch" aria-label="Show the default close button on this canvas" checked>
                                    <span class="wppoppop-ovf-track"></span>
                                </span>
                            </div>
                            <span style="font-size:10px;color:#64748b;display:block;margin-top:4px;">ON: visitors see the round &times; button in the top-right corner of this canvas. OFF: hidden. Use a Close element or a Close-action button instead.</span>
                        </div>

                        <!-- Phase 4: Canvas Auto-Close Countdown -->
                        <div class="wppoppop-field-group" style="margin-bottom:12px;padding-bottom:10px;border-bottom:1px solid #334155;">
                            <label for="set-canvas-autoclose" style="display:block;font-size:11px;font-weight:700;color:#94a3b8;margin-bottom:4px;">AUTO-CLOSE COUNTDOWN (SECONDS)</label>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <input type="number" id="set-canvas-autoclose" class="wppoppop-input" min="0" max="300" step="1" value="0" placeholder="0" style="width:120px;height:30px;padding:4px 8px;font-size:12px;border:1px solid #334155;border-radius:4px;background:#1e293b;color:#f1f5f9;">
                                <span style="font-size:11px;color:#94a3b8;">sec (0 = disabled)</span>
                            </div>
                            <span style="font-size:10px;color:#64748b;display:block;margin-top:2px;">Automatically dismisses the popup after specified duration.</span>
                        </div>


                        <div style="margin-bottom:12px;">
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                        <label style="font-size:11px;font-weight:700;color:#475569;margin:0;">CANVAS BACKGROUND FILL</label>
                        <button type="button" id="set-canvas-bg-transparent-btn" class="wppoppop-checker-btn" title="Toggle Transparent Background (Click to Enable / Disable)" aria-label="Toggle Transparent Background" aria-pressed="false" style="width:18px;height:18px;border-radius:3px;border:1px solid #64748b;cursor:pointer;padding:0;background-color:#ffffff;background-image:linear-gradient(45deg, #cbd5e1 25%, transparent 25%), linear-gradient(-45deg, #cbd5e1 25%, transparent 25%), linear-gradient(45deg, transparent 75%, #cbd5e1 75%), linear-gradient(-45deg, transparent 75%, #cbd5e1 75%);background-size:8px 8px;background-position:0 0, 0 4px, 4px -4px, -4px 0px;transition:all 0.15s ease;"></button>
                    </div>
                            <select id="set-canvas-bg-mode" class="widefat" style="margin-bottom:8px;font-size:12px;">
                                <option value="solid">Solid Background Color</option>
                                <option value="gradient">Linear Gradient</option>
                            </select>
                            
                            <div id="set-canvas-solid-wrap">
                                <input type="color" id="set-canvas-bg-color" value="#ffffff" class="widefat" style="height:34px;padding:2px;">
                            </div>

                            <div id="set-canvas-gradient-wrap" style="display:none;">
                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:6px;">
                                    <div>
                                        <label style="font-size:10px;color:#64748b;">Color 1</label>
                                        <input type="color" id="set-canvas-grad-color1" value="#3b82f6" class="widefat" style="height:32px;padding:2px;">
                                    </div>
                                    <div>
                                        <label style="font-size:10px;color:#64748b;">Color 2</label>
                                        <input type="color" id="set-canvas-grad-color2" value="#1d4ed8" class="widefat" style="height:32px;padding:2px;">
                                    </div>
                                </div>
                                <label style="font-size:10px;color:#64748b;">Gradient Angle (0-360&deg;)</label>
                                <input type="number" id="set-canvas-grad-angle" value="135" min="0" max="360" class="widefat" style="font-size:12px;">
                            </div>
                        </div>

                        <!-- ANIMATE.STYLE ANIMATION SECTION -->
                        <!-- ANIMATION SECTION -->
                        <div class="wppoppop-anim-setting-wrap" style="margin-top:16px;padding-top:14px;border-top:1px solid #e2e8f0;">
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                                <div style="display:flex;align-items:center;gap:6px;">
                                    <span style="font-size:12px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:0.5px;">ANIMATION</span>
                                    <span class="dashicons dashicons-editor-help" style="font-size:16px;width:16px;height:16px;color:#64748b;cursor:help;" title="Configure appearance entrance and disappearance exit animations for this canvas."></span>
                                </div>
                                <button type="button" id="set-canvas-anim-play" class="button wppoppop-anim-play-btn" title="Play Canvas Animation" style="display:inline-flex;align-items:center;gap:4px;padding:2px 8px;font-size:11px;font-weight:600;color:#334155;background:#f1f5f9;border:1px solid #cbd5e1;border-radius:4px;cursor:pointer;line-height:1.4;height:24px;">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor" style="pointer-events:none;"><polygon points="6,4 20,12 6,20"/></svg>
                                    <span>Play</span>
                                </button>
                            </div>

                            <div style="margin-bottom:10px;">
                                <select id="set-canvas-anim-appearance" class="widefat" style="font-size:12px;height:34px;">
                                    <option value="none">None</option>
                                    <optgroup label="Fading Entrances">
                                        <option value="fadeIn" selected>fadeIn</option>
                                        <option value="fadeInDown">fadeInDown</option>
                                        <option value="fadeInDownBig">fadeInDownBig</option>
                                        <option value="fadeInLeft">fadeInLeft</option>
                                        <option value="fadeInLeftBig">fadeInLeftBig</option>
                                        <option value="fadeInRight">fadeInRight</option>
                                        <option value="fadeInRightBig">fadeInRightBig</option>
                                        <option value="fadeInUp">fadeInUp</option>
                                        <option value="fadeInUpBig">fadeInUpBig</option>
                                    </optgroup>
                                    <optgroup label="Zooming Entrances">
                                        <option value="zoomIn">zoomIn</option>
                                        <option value="zoomInDown">zoomInDown</option>
                                        <option value="zoomInLeft">zoomInLeft</option>
                                        <option value="zoomInRight">zoomInRight</option>
                                        <option value="zoomInUp">zoomInUp</option>
                                    </optgroup>
                                    <optgroup label="Bouncing Entrances">
                                        <option value="bounceIn">bounceIn</option>
                                        <option value="bounceInDown">bounceInDown</option>
                                        <option value="bounceInLeft">bounceInLeft</option>
                                        <option value="bounceInRight">bounceInRight</option>
                                        <option value="bounceInUp">bounceInUp</option>
                                    </optgroup>
                                    <optgroup label="Sliding Entrances">
                                        <option value="slideInDown">slideInDown</option>
                                        <option value="slideInLeft">slideInLeft</option>
                                        <option value="slideInRight">slideInRight</option>
                                        <option value="slideInUp">slideInUp</option>
                                    </optgroup>
                                    <optgroup label="Back Entrances">
                                        <option value="backInDown">backInDown</option>
                                        <option value="backInLeft">backInLeft</option>
                                        <option value="backInRight">backInRight</option>
                                        <option value="backInUp">backInUp</option>
                                    </optgroup>
                                </select>
                                <span class="wppoppop-anim-sublabel">Appearance</span>
                            </div>

                            <!-- Duration & Start delay Inputs Row -->
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px;">
                                <div>
                                    <div class="wppoppop-input-suffix-wrap">
                                        <input type="number" id="set-canvas-anim-duration" value="1000" min="0" step="50" class="widefat">
                                        <span class="wppoppop-input-suffix">ms</span>
                                    </div>
                                    <span class="wppoppop-anim-sublabel">Duration</span>
                                </div>
                                <div>
                                    <div class="wppoppop-input-suffix-wrap">
                                        <input type="number" id="set-canvas-anim-delay" value="0" min="0" step="50" class="widefat">
                                        <span class="wppoppop-input-suffix">ms</span>
                                    </div>
                                    <span class="wppoppop-anim-sublabel">Start delay</span>
                                </div>
                            </div>

                            <!-- Disappearance on/off switch -->
                            <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;margin:4px 0 8px;">
                                <label for="set-canvas-anim-exit-enable" style="font-size:11px;font-weight:700;color:#475569;margin:0;text-transform:uppercase;cursor:pointer;">Play disappearance on close</label>
                                <span class="wppoppop-ovf-switch">
                                    <input type="checkbox" id="set-canvas-anim-exit-enable" value="1" role="switch" aria-label="Play disappearance animation when the canvas closes" checked>
                                    <span class="wppoppop-ovf-track"></span>
                                </span>
                            </div>
                            <!-- Disappearance Dropdown (Animate.css Exits) -->
                            <div style="margin-bottom:4px;">
                                <select id="set-canvas-anim-disappearance" class="widefat" style="font-size:12px;height:34px;">
                                    <option value="none">None</option>
                                    <optgroup label="Fading Exits">
                                        <option value="fadeOut" selected>fadeOut</option>
                                        <option value="fadeOutDown">fadeOutDown</option>
                                        <option value="fadeOutDownBig">fadeOutDownBig</option>
                                        <option value="fadeOutLeft">fadeOutLeft</option>
                                        <option value="fadeOutLeftBig">fadeOutLeftBig</option>
                                        <option value="fadeOutRight">fadeOutRight</option>
                                        <option value="fadeOutRightBig">fadeOutRightBig</option>
                                        <option value="fadeOutUp">fadeOutUp</option>
                                        <option value="fadeOutUpBig">fadeOutUpBig</option>
                                    </optgroup>
                                    <optgroup label="Zooming Exits">
                                        <option value="zoomOut">zoomOut</option>
                                        <option value="zoomOutDown">zoomOutDown</option>
                                        <option value="zoomOutLeft">zoomOutLeft</option>
                                        <option value="zoomOutRight">zoomOutRight</option>
                                        <option value="zoomOutUp">zoomOutUp</option>
                                    </optgroup>
                                    <optgroup label="Bouncing Exits">
                                        <option value="bounceOut">bounceOut</option>
                                        <option value="bounceOutDown">bounceOutDown</option>
                                        <option value="bounceOutLeft">bounceOutLeft</option>
                                        <option value="bounceOutRight">bounceOutRight</option>
                                        <option value="bounceOutUp">bounceOutUp</option>
                                    </optgroup>
                                    <optgroup label="Sliding Exits">
                                        <option value="slideOutDown">slideOutDown</option>
                                        <option value="slideOutLeft">slideOutLeft</option>
                                        <option value="slideOutRight">slideOutRight</option>
                                        <option value="slideOutUp">slideOutUp</option>
                                    </optgroup>
                                    <optgroup label="Back Exits">
                                        <option value="backOutDown">backOutDown</option>
                                        <option value="backOutLeft">backOutLeft</option>
                                        <option value="backOutRight">backOutRight</option>
                                        <option value="backOutUp">backOutUp</option>
                                    </optgroup>
                                </select>
                                <span class="wppoppop-anim-sublabel">Disappearance</span>
                            </div>
                        </div>

                    </div>

                    <!-- SUBTAB 2: DEDICATED CANVAS LOGIC (CONDITIONAL LOGIC) -->
                    <div class="wppoppop-csubcontent" id="csub-tab-logic" style="display:none;">
                        <!-- Elements Count Badge -->
                        <div style="display:flex;align-items:center;justify-content:space-between;background:#f8fafc;border:1px solid #e2e8f0;padding:8px 12px;border-radius:6px;margin-bottom:12px;">
                            <span style="font-size:11px;font-weight:700;color:#475569;">Canvas Elements:</span>
                            <span id="set-canvas-element-count-badge" style="background:#2563eb;color:#ffffff;font-size:10px;font-weight:700;padding:2px 8px;border-radius:10px;">0 Elements</span>
                        </div>

                        <!-- Empty Canvas Warning Banner -->
                        <div id="set-canvas-logic-disabled-notice" class="wppoppop-logic-notice" style="display:none;">
                            <span class="dashicons dashicons-info" style="color:#f59e0b;font-size:16px;width:16px;height:16px;margin-top:1px;"></span>
                            <span style="font-size:11px;line-height:1.4;color:#92400e;">
                                <strong>Conditional Logic Locked:</strong> Add at least one element to this canvas to enable conditional triggers or sequential rules.
                            </span>
                        </div>

                        <!-- Conditional Logic Checkbox -->
                        <div style="margin-bottom:12px;">
                            <label style="font-size:12px;font-weight:700;color:#1e293b;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                                <input type="checkbox" id="set-canvas-logic-enable" value="1"> Enable Conditional Logic
                            </label>
                            <p style="margin:4px 0 0 20px;font-size:11px;color:#64748b;line-height:1.4;">
                                Control whether this canvas is displayed or skipped based on user input from prior steps.
                            </p>
                        </div>

                        <!-- Conditional Rules Builder Panel -->
                        <div id="set-canvas-logic-rules-panel" style="display:none;background:#f8fafc;border:1px solid #cbd5e1;border-radius:6px;padding:12px;margin-top:10px;">
                            <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">CONDITION: TRIGGER FIELD</label>
                            <select id="set-canvas-logic-field" class="widefat" style="margin-bottom:8px;font-size:12px;">
                                <!-- Dynamically populated with elements/tokens -->
                            </select>

                            <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">OPERATOR</label>
                            <select id="set-canvas-logic-operator" class="widefat" style="margin-bottom:8px;font-size:12px;">
                                <option value="equals">Equals (Exact Match)</option>
                                <option value="not_equals">Does Not Equal</option>
                                <option value="contains">Contains Value</option>
                                <option value="greater_than">Greater Than (&gt;)</option>
                                <option value="less_than">Less Than (&lt;)</option>
                                <option value="is_filled">Is Filled / Not Empty</option>
                            </select>

                            <div id="set-canvas-logic-val-wrap" style="margin-bottom:8px;">
                                <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">EXPECTED VALUE</label>
                                <input type="text" id="set-canvas-logic-val" class="widefat" placeholder="e.g. Yes, 100, promo" style="font-size:12px;">
                            </div>

                            <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">ACTION IF TRUE</label>
                            <select id="set-canvas-logic-action" class="widefat" style="font-size:12px;">
                                <option value="show">Display This Canvas</option>
                                <option value="skip">Skip to Next Canvas</option>
                                <option value="redirect">Redirect Visitor</option>
                            </select>
                        </div>
                    </div>

                </div>
            </div>
