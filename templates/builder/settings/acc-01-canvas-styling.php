<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<!-- 1. Dedicated Per-Canvas Settings with Bullets, Color, Animate.style & Logic -->
            <div class="wppoppop-acc-item">
                <button type="button" class="wppoppop-acc-header active">
                    <span><?php esc_html_e('Canvas Settings', 'wppoppop'); ?></span>
                </button>
                <div class="wppoppop-acc-body" style="display:block;">
                    
                    <!-- Canvas Selection Bullets Strip -->
                    <div style="margin-bottom:14px;">
                        <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;text-transform:uppercase;"><?php esc_html_e('SELECT CANVAS TO CONFIGURE', 'wppoppop'); ?></label>
                        <div class="wppoppop-canvas-bullets-bar" id="wppoppop-canvas-bullets-container">
                            <!-- Dynamically populated canvas bullets with status dots -->
                        </div>
                    </div>

                    <!-- Canvas Settings Sub-Tabs (General & Color vs Logic) -->
                    <div class="wppoppop-canvas-subtabs">
                        <button type="button" class="wppoppop-csubtab active" data-tab="general"><?php esc_html_e('General & Color', 'wppoppop'); ?></button>
                        <button type="button" class="wppoppop-csubtab" data-tab="logic"><?php esc_html_e('Canvas Logic', 'wppoppop'); ?></button>
                    </div>

                    <!-- SUBTAB 1: GENERAL, SIZE, COLOR & ANIMATION -->
                    <div class="wppoppop-csubcontent active" id="csub-tab-general">
                        <div style="margin-bottom:12px;">
                            <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;"><?php esc_html_e('CANVAS NAME', 'wppoppop'); ?></label>
                            <input type="text" id="set-canvas-name" value="Canvas 1" class="widefat" placeholder="<?php esc_attr_e('e.g. Lead Opt-in, Thank You...', 'wppoppop'); ?>" style="font-size:12px;">
                        </div>

                        <div style="margin-bottom:12px;">
                            <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;"><?php esc_html_e('CANVAS SIZE (WIDTH × HEIGHT PX)', 'wppoppop'); ?></label>
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                                <div>
                                    <label style="font-size:10px;color:#64748b;"><?php esc_html_e('Width (px)', 'wppoppop'); ?></label>
                                    <input type="number" id="set-canvas-width" value="300" min="200" max="1600" class="widefat" style="font-size:12px;">
                                </div>
                                <div>
                                    <label style="font-size:10px;color:#64748b;"><?php esc_html_e('Height (px)', 'wppoppop'); ?></label>
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
                                <label for="set-canvas-allow-overflow" style="font-size:11px;font-weight:700;color:#475569;margin:0;text-transform:uppercase;cursor:pointer;"><?php esc_html_e('ELEMENT OVERFLOW', 'wppoppop'); ?> <span id="set-canvas-overflow-state" style="color:#2563eb;"><?php esc_html_e('OFF', 'wppoppop'); ?></span></label>
                                <span class="wppoppop-ovf-switch">
                                    <input type="checkbox" id="set-canvas-allow-overflow" value="1" role="switch" aria-label="<?php esc_attr_e('Allow canvas element overflow', 'wppoppop'); ?>">
                                    <span class="wppoppop-ovf-track"></span>
                                </span>
                            </div>
                        </div>

                        <!-- WPPOPPOP_DEFAULT_CLOSE: per-canvas default close button toggle -->
                        <div class="wppoppop-field-group" style="margin-bottom:12px;padding-bottom:10px;border-bottom:1px solid #334155;">
                            <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;">
                                <label for="set-canvas-close-btn" style="font-size:11px;font-weight:700;color:#475569;margin:0;text-transform:uppercase;cursor:pointer;"><?php esc_html_e('SHOW DEFAULT CLOSE BUTTON', 'wppoppop'); ?></label>
                                <span class="wppoppop-ovf-switch">
                                    <input type="checkbox" id="set-canvas-close-btn" value="1" role="switch" aria-label="<?php esc_attr_e('Show the default close button on this canvas', 'wppoppop'); ?>" checked>
                                    <span class="wppoppop-ovf-track"></span>
                                </span>
                            </div>
                        </div>

                        <!-- WPPOPPOP_BG_CLOSE: click outside the popup to close (popup-level, default off) -->
                        <div class="wppoppop-field-group" style="margin-bottom:12px;padding-bottom:10px;border-bottom:1px solid #334155;">
                            <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;">
                                <label for="set-canvas-bg-close" style="font-size:11px;font-weight:700;color:#475569;margin:0;text-transform:uppercase;cursor:pointer;"><?php esc_html_e('CLICK BACKGROUND TO CLOSE', 'wppoppop'); ?></label>
                                <span class="wppoppop-ovf-switch">
                                    <input type="checkbox" id="set-canvas-bg-close" value="1" role="switch" aria-label="<?php esc_attr_e('Close the popup when the background outside it is clicked', 'wppoppop'); ?>">
                                    <span class="wppoppop-ovf-track"></span>
                                </span>
                            </div>
                        </div>

                        <!-- Phase 4: Canvas Auto-Close Countdown -->
                        <div class="wppoppop-field-group" style="margin-bottom:12px;padding-bottom:10px;border-bottom:1px solid #334155;">
                            <label for="set-canvas-autoclose" style="display:block;font-size:11px;font-weight:700;color:#94a3b8;margin-bottom:4px;"><?php esc_html_e('AUTO-CLOSE COUNTDOWN (SECONDS)', 'wppoppop'); ?></label>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <input type="number" id="set-canvas-autoclose" class="wppoppop-input" min="0" max="300" step="1" value="0" placeholder="0" style="width:120px;height:30px;padding:4px 8px;font-size:12px;border:1px solid #334155;border-radius:4px;background:#1e293b;color:#f1f5f9;">
                                <span style="font-size:11px;color:#94a3b8;"><?php esc_html_e('sec (0 = disabled)', 'wppoppop'); ?></span>
                            </div>
                            <span style="font-size:10px;color:#64748b;display:block;margin-top:2px;"><?php esc_html_e('Automatically dismisses the popup after specified duration.', 'wppoppop'); ?></span>
                        </div>


                        <div id="set-canvas-color-grid" style="display:grid;grid-template-columns:minmax(0,1fr) 92px;gap:12px;align-items:start;margin-bottom:12px;">
                            <div style="min-width:0;">
                                <div style="display:flex;align-items:center;gap:6px;height:18px;margin-bottom:4px;">
                                    <label style="font-size:11px;font-weight:700;color:#475569;margin:0;white-space:nowrap;"><?php esc_html_e('CANVAS COLOR', 'wppoppop'); ?></label>
                                    <button type="button" id="set-canvas-bg-transparent-btn" class="wppoppop-checker-btn" title="<?php esc_attr_e('Toggle Transparent Background (Click to Enable / Disable)', 'wppoppop'); ?><?php esc_html_e('" aria-label="', 'wppoppop'); ?><?php esc_attr_e('Toggle Transparent Background', 'wppoppop'); ?>" aria-pressed="false" style="width:18px;height:18px;border-radius:3px;border:1px solid #64748b;cursor:pointer;padding:0;background-color:#ffffff;background-image:linear-gradient(45deg, #cbd5e1 25%, transparent 25%), linear-gradient(-45deg, #cbd5e1 25%, transparent 25%), linear-gradient(45deg, transparent 75%, #cbd5e1 75%), linear-gradient(-45deg, transparent 75%, #cbd5e1 75%);background-size:8px 8px;background-position:0 0, 0 4px, 4px -4px, -4px 0px;transition:all 0.15s ease;"></button>
                                </div>
                            <select id="set-canvas-bg-mode" class="widefat" style="margin-bottom:8px;font-size:12px;">
                                <option value="solid"><?php esc_html_e('Solid Background Color', 'wppoppop'); ?></option>
                                <option value="gradient"><?php esc_html_e('Linear Gradient', 'wppoppop'); ?></option>
                            </select>
                            
                            <div id="set-canvas-solid-wrap">
                                <input type="color" id="set-canvas-bg-color" value="#ffffff" class="widefat" style="height:34px;padding:2px;">
                            </div>

                            <div id="set-canvas-gradient-wrap" style="display:none;">
                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:6px;">
                                    <div>
                                        <label style="font-size:10px;color:#64748b;"><?php esc_html_e('Color 1', 'wppoppop'); ?></label>
                                        <input type="color" id="set-canvas-grad-color1" value="#3b82f6" class="widefat" style="height:32px;padding:2px;">
                                    </div>
                                    <div>
                                        <label style="font-size:10px;color:#64748b;"><?php esc_html_e('Color 2', 'wppoppop'); ?></label>
                                        <input type="color" id="set-canvas-grad-color2" value="#1d4ed8" class="widefat" style="height:32px;padding:2px;">
                                    </div>
                                </div>
                                <label style="font-size:10px;color:#64748b;"><?php esc_html_e('Gradient Angle (0-360°)', 'wppoppop'); ?></label>
                                <input type="number" id="set-canvas-grad-angle" value="135" min="0" max="360" class="widefat" style="font-size:12px;">
                            </div>
                            </div>
                            <div>
                                <div style="display:flex;align-items:center;height:18px;margin-bottom:4px;">
                                    <label for="set-canvas-radius" style="font-size:11px;font-weight:700;color:#475569;margin:0;white-space:nowrap;"><?php esc_html_e('RADIUS (PX)', 'wppoppop'); ?></label>
                                </div>
                                <input type="number" id="set-canvas-radius" value="0" min="0" max="500" step="1" aria-label="<?php esc_attr_e('Canvas corner radius in pixels', 'wppoppop'); ?>" style="width:100%;height:34px;min-height:34px;padding:2px 8px;font-size:12px;box-sizing:border-box;">
                            </div>
                        </div>

                    </div>

                    <!-- SUBTAB 2: DEDICATED CANVAS LOGIC (CONDITIONAL LOGIC) -->
                    <div class="wppoppop-csubcontent" id="csub-tab-logic" style="display:none;">
                        <!-- Elements Count Badge -->
                        <div style="display:flex;align-items:center;justify-content:space-between;background:#f8fafc;border:1px solid #e2e8f0;padding:8px 12px;border-radius:6px;margin-bottom:12px;">
                            <span style="font-size:11px;font-weight:700;color:#475569;"><?php esc_html_e('Canvas Elements:', 'wppoppop'); ?></span>
                            <span id="set-canvas-element-count-badge" style="background:#2563eb;color:#ffffff;font-size:10px;font-weight:700;padding:2px 8px;border-radius:10px;"><?php esc_html_e('0 Elements', 'wppoppop'); ?></span>
                        </div>

                        <!-- Empty Canvas Warning Banner -->
                        <div id="set-canvas-logic-disabled-notice" class="wppoppop-logic-notice" style="display:none;">
                            <span class="dashicons dashicons-info" style="color:#f59e0b;font-size:16px;width:16px;height:16px;margin-top:1px;"></span>
                            <span style="font-size:11px;line-height:1.4;color:#92400e;">
                                <strong><?php esc_html_e('Conditional Logic Locked:', 'wppoppop'); ?></strong> <?php esc_html_e('Add at least one element to this canvas to enable conditional triggers or sequential rules.', 'wppoppop'); ?>
                            </span>
                        </div>

                        <!-- Conditional Logic Checkbox -->
                        <div style="margin-bottom:12px;">
                            <label style="font-size:12px;font-weight:700;color:#1e293b;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                                <input type="checkbox" id="set-canvas-logic-enable" value="1"> <?php esc_html_e('Enable Conditional Logic', 'wppoppop'); ?>
                            </label>
                            <p style="margin:4px 0 0 20px;font-size:11px;color:#64748b;line-height:1.4;">
                                <?php esc_html_e('Control whether this canvas is displayed or skipped based on user input from prior steps.', 'wppoppop'); ?>
                            </p>
                        </div>

                        <!-- Conditional Rules Builder Panel -->
                        <div id="set-canvas-logic-rules-panel" style="display:none;background:#f8fafc;border:1px solid #cbd5e1;border-radius:6px;padding:12px;margin-top:10px;">
                            <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;"><?php esc_html_e('CONDITION: TRIGGER FIELD', 'wppoppop'); ?></label>
                            <select id="set-canvas-logic-field" class="widefat" style="margin-bottom:8px;font-size:12px;">
                                <!-- Dynamically populated with elements/tokens -->
                            </select>

                            <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;"><?php esc_html_e('OPERATOR', 'wppoppop'); ?></label>
                            <select id="set-canvas-logic-operator" class="widefat" style="margin-bottom:8px;font-size:12px;">
                                <option value="equals"><?php esc_html_e('Equals (Exact Match)', 'wppoppop'); ?></option>
                                <option value="not_equals"><?php esc_html_e('Does Not Equal', 'wppoppop'); ?></option>
                                <option value="contains"><?php esc_html_e('Contains Value', 'wppoppop'); ?></option>
                                <option value="greater_than"><?php esc_html_e('Greater Than (>)', 'wppoppop'); ?></option>
                                <option value="less_than"><?php esc_html_e('Less Than (<)', 'wppoppop'); ?></option>
                                <option value="is_filled"><?php esc_html_e('Is Filled / Not Empty', 'wppoppop'); ?></option>
                            </select>

                            <div id="set-canvas-logic-val-wrap" style="margin-bottom:8px;">
                                <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;"><?php esc_html_e('EXPECTED VALUE', 'wppoppop'); ?></label>
                                <input type="text" id="set-canvas-logic-val" class="widefat" placeholder="<?php esc_attr_e('e.g. Yes, 100, promo', 'wppoppop'); ?>" style="font-size:12px;">
                            </div>

                            <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;"><?php esc_html_e('ACTION IF TRUE', 'wppoppop'); ?></label>
                            <select id="set-canvas-logic-action" class="widefat" style="font-size:12px;">
                                <option value="show"><?php esc_html_e('Display This Canvas', 'wppoppop'); ?></option>
                                <option value="skip"><?php esc_html_e('Skip to Next Canvas', 'wppoppop'); ?></option>
                                <option value="redirect"><?php esc_html_e('Redirect Visitor', 'wppoppop'); ?></option>
                            </select>
                        </div>
                    </div>

                </div>
            </div>
