<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<!-- 1. Dedicated Per-Canvas Settings with Bullets, Color, Animate.style & Logic -->
            <div class="wppoppop-acc-item">
                <button type="button" class="wppoppop-acc-header active">
                    <span>Canvas Settings</span>
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
                                    <input type="number" id="set-canvas-width" value="300" min="200" max="1600" class="widefat" style="font-size:12px;">
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
                                <label for="set-canvas-allow-overflow" style="font-size:11px;font-weight:700;color:#475569;margin:0;text-transform:uppercase;cursor:pointer;">ELEMENT OVERFLOW <span id="set-canvas-overflow-state" style="color:#2563eb;">OFF</span></label>
                                <span class="wppoppop-ovf-switch">
                                    <input type="checkbox" id="set-canvas-allow-overflow" value="1" role="switch" aria-label="Allow canvas element overflow">
                                    <span class="wppoppop-ovf-track"></span>
                                </span>
                            </div>
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


                        <div id="set-canvas-color-grid" style="display:grid;grid-template-columns:minmax(0,1fr) 92px;gap:12px;align-items:start;margin-bottom:12px;">
                            <div style="min-width:0;">
                                <div style="display:flex;align-items:center;gap:6px;height:18px;margin-bottom:4px;">
                                    <label style="font-size:11px;font-weight:700;color:#475569;margin:0;white-space:nowrap;">CANVAS COLOR</label>
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
                            <div>
                                <div style="display:flex;align-items:center;height:18px;margin-bottom:4px;">
                                    <label for="set-canvas-radius" style="font-size:11px;font-weight:700;color:#475569;margin:0;white-space:nowrap;">RADIUS (PX)</label>
                                </div>
                                <input type="number" id="set-canvas-radius" value="0" min="0" max="500" step="1" aria-label="Canvas corner radius in pixels" style="width:100%;height:34px;min-height:34px;padding:2px 8px;font-size:12px;box-sizing:border-box;">
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
