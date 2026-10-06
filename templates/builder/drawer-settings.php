<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<!-- Settings Drawer Backdrop Scrim -->
<div id="wppoppop-settings-backdrop" class="wppoppop-drawer-backdrop"></div>

<!-- Settings Slide-Out Drawer -->
<div id="wppoppop-settings-drawer" class="wppoppop-settings-drawer">
    <div class="wppoppop-settings-header">
        <span class="wppoppop-settings-heading">
            <span class="dashicons dashicons-admin-generic"></span> Campaign Settings
        </span>
        <button type="button" id="wppoppop-settings-drawer-close" class="wppoppop-drawer-close-btn" title="Close Settings (Esc)">&times;</button>
    </div>

    <div class="wppoppop-settings-scroll-body wppoppop-accordion-group">
        <!-- 1. Box & Canvas Dimensions per Screen with Screen Rename, 50%/50% Color Pickers & Animations -->
        <div class="wppoppop-acc-item">
            <button type="button" class="wppoppop-acc-header active" id="wppoppop-acc-canvas-dimensions">
                <span>1. Canvas Dimensions & Background</span>
                <span class="dashicons dashicons-arrow-down-alt2 wppoppop-acc-caret"></span>
            </button>
            <div class="wppoppop-acc-body">
                <!-- Screen Bullet Selector Bar -->
                <div class="wppoppop-screen-bullets-bar" style="margin-bottom:12px;padding-bottom:10px;border-bottom:1px solid #334155;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#94a3b8;margin-bottom:6px;text-transform:uppercase;">Select Screen to Configure:</label>
                    <div id="wppoppop-screen-settings-bullets" class="wppoppop-screen-bullets-nav" style="display:flex;flex-wrap:wrap;gap:6px;">
                        <!-- Rendered dynamically -->
                    </div>
                </div>

                <!-- Sub-Tabs: Dimensions & Background VS Screen Logic -->
                <div class="wppoppop-screen-subtabs" style="display:flex;background:#0f172a;border-radius:4px;padding:2px;gap:4px;margin-bottom:12px;border:1px solid #334155;">
                    <button type="button" class="wppoppop-screen-subtab active" data-subtab="canvas" style="flex:1;background:#2563eb;color:#ffffff;border:none;padding:6px 10px;font-size:11px;font-weight:700;cursor:pointer;border-radius:3px;text-align:center;">Dimensions & Background</button>
                    <button type="button" class="wppoppop-screen-subtab" data-subtab="logic" style="flex:1;background:transparent;color:#94a3b8;border:none;padding:6px 10px;font-size:11px;font-weight:700;cursor:pointer;border-radius:3px;text-align:center;">Logic</button>
                </div>

                <!-- SUBTAB PANE 1: Dimensions, Rename, 50%/50% Colors & Animations -->
                <div id="wppoppop-subtab-pane-canvas">
                    <div style="margin-bottom:12px;background:#1e293b;padding:10px;border-radius:6px;border:1px solid #334155;">
                        <label style="display:block;font-size:11px;font-weight:700;color:#60a5fa;margin-bottom:4px;text-transform:uppercase;">Screen Name (Rename)</label>
                        <input type="text" id="set-screen-title" placeholder="e.g. Screen 1, Offer Step, Thank You" style="width:100%;font-weight:600;">
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px;">
                        <div>
                            <label>Width (px)</label>
                            <input type="number" id="set-box-width" value="640">
                        </div>
                        <div>
                            <label>Height (px)</label>
                            <input type="number" id="set-box-height" value="400">
                        </div>
                    </div>

                    <div style="margin-bottom:10px;">
                        <label>Background Style</label>
                        <select id="set-bg-mode">
                            <option value="solid">Solid Color</option>
                            <option value="gradient">Linear Gradient</option>
                        </select>
                    </div>

                    <!-- Solid Background with 50%/50% Row -->
                    <div id="set-solid-wrap" style="margin-bottom:10px;">
                        <label style="display:block;margin-bottom:4px;">Background Color</label>
                        <div class="wppoppop-color-picker-row">
                            <div class="wppoppop-color-picker-wrap">
                                <input type="color" class="wppoppop-color-swatch-input" data-target="#set-bg-color" value="#ffffff" title="Choose color">
                            </div>
                            <input type="text" id="set-bg-color" class="wppoppop-color-hex-input" value="#ffffff" placeholder="#ffffff">
                        </div>
                    </div>

                    <!-- Linear Gradient with 50%/50% Rows -->
                    <div id="set-gradient-wrap" style="display:none;margin-bottom:10px;">
                        <div style="margin-bottom:8px;">
                            <label style="display:block;margin-bottom:4px;">Gradient Start</label>
                            <div class="wppoppop-color-picker-row">
                                <div class="wppoppop-color-picker-wrap">
                                    <input type="color" class="wppoppop-color-swatch-input" data-target="#set-grad-color1" value="#3b82f6" title="Choose start color">
                                </div>
                                <input type="text" id="set-grad-color1" class="wppoppop-color-hex-input" value="#3b82f6" placeholder="#3b82f6">
                            </div>
                        </div>

                        <div style="margin-bottom:8px;">
                            <label style="display:block;margin-bottom:4px;">Gradient End</label>
                            <div class="wppoppop-color-picker-row">
                                <div class="wppoppop-color-picker-wrap">
                                    <input type="color" class="wppoppop-color-swatch-input" data-target="#set-grad-color2" value="#1d4ed8" title="Choose end color">
                                </div>
                                <input type="text" id="set-grad-color2" class="wppoppop-color-hex-input" value="#1d4ed8" placeholder="#1d4ed8">
                            </div>
                        </div>

                        <label>Angle (deg)</label>
                        <input type="number" id="set-grad-angle" value="135">
                    </div>

                    <!-- Screen Animation Settings Section (Appearance, Duration, Delay, Disappearance) -->
                    <div class="wppoppop-screen-anim-section" style="margin-top:14px;padding-top:12px;border-top:1px solid #334155;">
                        <div style="display:flex;align-items:center;gap:6px;margin-bottom:8px;">
                            <label style="font-size:12px;font-weight:800;color:#cbd5e1;text-transform:uppercase;letter-spacing:0.5px;margin:0;">ANIMATION</label>
                            <span class="dashicons dashicons-editor-help" style="font-size:16px;width:16px;height:16px;color:#94a3b8;cursor:help;" title="Configure entrance and exit transitions for this screen/canvas."></span>
                        </div>

                        <!-- Appearance (Entrance Dropdown) -->
                        <div style="margin-bottom:10px;">
                            <select id="set-screen-anim-in">
                                <option value="fade">Fade</option>
                                <option value="slideDown">Slide Down</option>
                                <option value="bounceIn">Bounce In</option>
                                <option value="zoomIn">Zoom In</option>
                                <option value="flipIn">Flip In</option>
                                <option value="none">None</option>
                            </select>
                            <span class="wppoppop-field-subcaption">Appearance</span>
                        </div>

                        <!-- Duration & Start Delay (Two Inputs with Unit Badges) -->
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px;">
                            <div>
                                <div class="wppoppop-unit-input-wrap">
                                    <input type="number" id="set-screen-anim-duration" value="1000" min="0" step="50">
                                    <span class="wppoppop-unit-badge">ms</span>
                                </div>
                                <span class="wppoppop-field-subcaption">Duration</span>
                            </div>
                            <div>
                                <div class="wppoppop-unit-input-wrap">
                                    <input type="number" id="set-screen-anim-delay" value="0" min="0" step="50">
                                    <span class="wppoppop-unit-badge">ms</span>
                                </div>
                                <span class="wppoppop-field-subcaption">Start delay</span>
                            </div>
                        </div>

                        <!-- Disappearance (Exit Dropdown) -->
                        <div style="margin-bottom:6px;">
                            <select id="set-screen-anim-out">
                                <option value="fade">Fade</option>
                                <option value="slideUp">Slide Up</option>
                                <option value="zoomOut">Zoom Out</option>
                                <option value="flipOut">Flip Out</option>
                                <option value="none">None</option>
                            </select>
                            <span class="wppoppop-field-subcaption">Disappearance</span>
                        </div>
                    </div>
                </div>

                <!-- SUBTAB PANE 2: Screen Conditional Logic with Empty Canvas Guard -->
                <div id="wppoppop-subtab-pane-logic" style="display:none;">
                    <div style="background:#1e293b;border:1px solid #334155;border-radius:6px;padding:12px;margin-bottom:10px;">
                        <label class="wppoppop-slide-toggle" id="wppoppop-logic-toggle-label">
                            <span class="wppoppop-switch">
                                <input type="checkbox" id="set-screen-cond-enable">
                                <span class="wppoppop-slider"></span>
                            </span>
                            <span class="wppoppop-switch-label">Enable Conditional Logic for this Screen</span>
                        </label>

                        <div id="set-screen-empty-notice" style="display:none;align-items:center;gap:6px;background:rgba(239,68,68,0.12);border:1px solid rgba(239,68,68,0.3);color:#fca5a5;padding:8px 10px;border-radius:4px;font-size:11px;line-height:1.4;margin-top:6px;">
                            <span class="dashicons dashicons-warning" style="font-size:16px;width:16px;height:16px;color:#ef4444;flex-shrink:0;"></span>
                            <span>Cannot enable logic: This screen has no elements. Add form elements to the canvas first.</span>
                        </div>

                        <p style="font-size:11px;color:#94a3b8;margin:6px 0 10px 0;line-height:1.4;">
                            Evaluate inputs from elements on this screen to branch visitors to specific destination screens.
                        </p>

                        <div id="set-screen-cond-box" style="display:none;border-top:1px solid #334155;padding-top:10px;">
                            <div style="margin-bottom:10px;">
                                <label>Evaluate Form Element</label>
                                <select id="set-screen-cond-field">
                                    <!-- Populated dynamically -->
                                </select>
                            </div>

                            <div style="margin-bottom:10px;">
                                <label>Condition Operator</label>
                                <select id="set-screen-cond-operator">
                                    <option value="equals">Equals (=)</option>
                                    <option value="not_equals">Does not equal (!=)</option>
                                    <option value="contains">Contains text</option>
                                    <option value="greater_than">Greater than (&gt;)</option>
                                    <option value="less_than">Less than (&lt;)</option>
                                    <option value="is_empty">Is Empty</option>
                                    <option value="is_not_empty">Is Not Empty</option>
                                </select>
                            </div>

                            <div id="set-screen-cond-val-wrap" style="margin-bottom:10px;">
                                <label>Match Value</label>
                                <input type="text" id="set-screen-cond-val" placeholder="e.g. VIP, Yes, 5">
                            </div>

                            <div style="margin-bottom:10px;">
                                <label>If Condition Matches &rarr; Jump to Screen</label>
                                <select id="set-screen-cond-target">
                                    <!-- Populated dynamically -->
                                </select>
                            </div>

                            <div>
                                <label>Otherwise (Default Fallback)</label>
                                <select id="set-screen-cond-fallback">
                                    <!-- Populated dynamically -->
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Display Triggers -->
        <div class="wppoppop-acc-item">
            <button type="button" class="wppoppop-acc-header">
                <span>2. Display Triggers</span>
                <span class="dashicons dashicons-arrow-down-alt2 wppoppop-acc-caret"></span>
            </button>
            <div class="wppoppop-acc-body" style="display:none;">
                <label class="wppoppop-slide-toggle">
                    <span class="wppoppop-switch">
                        <input type="checkbox" id="trig-load" checked>
                        <span class="wppoppop-slider"></span>
                    </span>
                    <span class="wppoppop-switch-label">On Page Load</span>
                </label>
                <div style="margin-bottom:10px;margin-left:46px;">
                    <label>Delay (seconds)</label>
                    <input type="number" id="trig-load-delay" value="0" min="0">
                </div>

                <label class="wppoppop-slide-toggle">
                    <span class="wppoppop-switch">
                        <input type="checkbox" id="trig-exit">
                        <span class="wppoppop-slider"></span>
                    </span>
                    <span class="wppoppop-switch-label">Exit Intent (Mouse leaves window)</span>
                </label>

                <label class="wppoppop-slide-toggle">
                    <span class="wppoppop-switch">
                        <input type="checkbox" id="trig-scroll">
                        <span class="wppoppop-slider"></span>
                    </span>
                    <span class="wppoppop-switch-label">On Scroll Percentage</span>
                </label>
                <div style="margin-bottom:10px;margin-left:46px;">
                    <label>Scroll %</label>
                    <input type="number" id="trig-scroll-val" value="50" min="1" max="100">
                </div>

                <label class="wppoppop-slide-toggle">
                    <span class="wppoppop-switch">
                        <input type="checkbox" id="trig-adblock">
                        <span class="wppoppop-slider"></span>
                    </span>
                    <span class="wppoppop-switch-label">AdBlock Detector Trigger</span>
                </label>

                <label class="wppoppop-slide-toggle">
                    <span class="wppoppop-switch">
                        <input type="checkbox" id="trig-backbutton">
                        <span class="wppoppop-slider"></span>
                    </span>
                    <span class="wppoppop-switch-label">Browser Back-Button Trap</span>
                </label>
            </div>
        </div>

        <!-- 3. Conditional Logic & Math -->
        <div class="wppoppop-acc-item">
            <button type="button" class="wppoppop-acc-header">
                <span>3. Math & Expression Logic</span>
                <span class="dashicons dashicons-arrow-down-alt2 wppoppop-acc-caret"></span>
            </button>
            <div class="wppoppop-acc-body" style="display:none;">
                <label>Math Calculation Formula</label>
                <input type="text" id="set-math-formula" placeholder="{field_1} * 2.5 + 10">
                <label style="margin-top:8px;">Target Layer for Output</label>
                <input type="text" id="set-math-target" placeholder="el_123456">
            </div>
        </div>

        <!-- 4. Sticky Side Tabs -->
        <div class="wppoppop-acc-item">
            <button type="button" class="wppoppop-acc-header">
                <span>4. Sticky Side Tab Launcher</span>
                <span class="dashicons dashicons-arrow-down-alt2 wppoppop-acc-caret"></span>
            </button>
            <div class="wppoppop-acc-body" style="display:none;">
                <label class="wppoppop-slide-toggle">
                    <span class="wppoppop-switch">
                        <input type="checkbox" id="set-sidetab-enable">
                        <span class="wppoppop-slider"></span>
                    </span>
                    <span class="wppoppop-switch-label">Enable Sticky Tab</span>
                </label>
                <div style="margin-top:8px;">
                    <label>Tab Label</label>
                    <input type="text" id="set-sidetab-label" placeholder="Special Offer">
                    <label style="margin-top:8px;">Position</label>
                    <select id="set-sidetab-pos">
                        <option value="left">Left Edge</option>
                        <option value="right">Right Edge</option>
                        <option value="bottom">Bottom Bar</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- 5. Payment Gateways -->
        <div class="wppoppop-acc-item">
            <button type="button" class="wppoppop-acc-header">
                <span>5. Instant Payments</span>
                <span class="dashicons dashicons-arrow-down-alt2 wppoppop-acc-caret"></span>
            </button>
            <div class="wppoppop-acc-body" style="display:none;">
                <label>Gateway</label>
                <select id="set-pay-gateway">
                    <option value="stripe">Stripe</option>
                    <option value="paypal">PayPal</option>
                </select>
                <label style="margin-top:8px;">Default Charge Amount</label>
                <input type="number" id="set-pay-amount" step="0.01" value="19.99">
            </div>
        </div>

        <!-- 6. Secure Downloads -->
        <div class="wppoppop-acc-item">
            <button type="button" class="wppoppop-acc-header">
                <span>6. Lead Magnet Downloads</span>
                <span class="dashicons dashicons-arrow-down-alt2 wppoppop-acc-caret"></span>
            </button>
            <div class="wppoppop-acc-body" style="display:none;">
                <label class="wppoppop-slide-toggle">
                    <span class="wppoppop-switch">
                        <input type="checkbox" id="set-dl-enable">
                        <span class="wppoppop-slider"></span>
                    </span>
                    <span class="wppoppop-switch-label">Deliver Download on Submit</span>
                </label>
                <div style="margin-top:8px;">
                    <label>Direct File URL</label>
                    <input type="url" id="set-dl-url" placeholder="https://example.com/ebook.pdf">
                </div>
            </div>
        </div>

        <!-- 7. Video Listeners -->
        <div class="wppoppop-acc-item">
            <button type="button" class="wppoppop-acc-header">
                <span>7. Embedded Video Listeners</span>
                <span class="dashicons dashicons-arrow-down-alt2 wppoppop-acc-caret"></span>
            </button>
            <div class="wppoppop-acc-body" style="display:none;">
                <label class="wppoppop-slide-toggle">
                    <span class="wppoppop-switch">
                        <input type="checkbox" id="set-vid-enable">
                        <span class="wppoppop-slider"></span>
                    </span>
                    <span class="wppoppop-switch-label">Trigger on Video Playback</span>
                </label>
                <div style="margin-top:8px;">
                    <label>Trigger after (seconds)</label>
                    <input type="number" id="set-vid-time" value="30">
                </div>
            </div>
        </div>

        <!-- 8. Subscriber Autoresponder -->
        <div class="wppoppop-acc-item">
            <button type="button" class="wppoppop-acc-header">
                <span>8. Subscriber Autoresponder</span>
                <span class="dashicons dashicons-arrow-down-alt2 wppoppop-acc-caret"></span>
            </button>
            <div class="wppoppop-acc-body" style="display:none;">
                <label class="wppoppop-slide-toggle">
                    <span class="wppoppop-switch">
                        <input type="checkbox" id="set-auto-enable">
                        <span class="wppoppop-slider"></span>
                    </span>
                    <span class="wppoppop-switch-label">Send User Welcome Email</span>
                </label>
                <div style="margin-top:8px;">
                    <label>Email Subject</label>
                    <input type="text" id="set-auto-subject" placeholder="Welcome! Here is your download">
                    <label style="margin-top:8px;">Email Body</label>
                    <textarea id="set-auto-body" rows="4" placeholder="Hello {email}, thanks for subscribing!"></textarea>
                </div>
            </div>
        </div>

        <!-- 9. Webhooks & Integrations -->
        <div class="wppoppop-acc-item">
            <button type="button" class="wppoppop-acc-header">
                <span>9. Marketing & Outgoing Webhooks</span>
                <span class="dashicons dashicons-arrow-down-alt2 wppoppop-acc-caret"></span>
            </button>
            <div class="wppoppop-acc-body" style="display:none;">
                <label>Outgoing Webhook URL</label>
                <input type="url" id="set-webhook-url" placeholder="https://api.zapier.com/hooks/catch/...">
                <label style="margin-top:8px;">Webhook Secret (HMAC-SHA256)</label>
                <input type="text" id="set-webhook-secret">
            </div>
        </div>

        <!-- 10. Twilio SMS Alerts -->
        <div class="wppoppop-acc-item">
            <button type="button" class="wppoppop-acc-header">
                <span>10. Twilio SMS Alerts</span>
                <span class="dashicons dashicons-arrow-down-alt2 wppoppop-acc-caret"></span>
            </button>
            <div class="wppoppop-acc-body" style="display:none;">
                <label class="wppoppop-slide-toggle">
                    <span class="wppoppop-switch">
                        <input type="checkbox" id="set-sms-enable">
                        <span class="wppoppop-slider"></span>
                    </span>
                    <span class="wppoppop-switch-label">Send Admin SMS on Lead</span>
                </label>
                <div style="margin-top:8px;">
                    <label>Destination Phone</label>
                    <input type="tel" id="set-sms-phone" placeholder="+1234567890">
                </div>
            </div>
        </div>

        <!-- 11. Targeting & Attribution -->
        <div class="wppoppop-acc-item">
            <button type="button" class="wppoppop-acc-header">
                <span>11. Audience Targeting & Geo</span>
                <span class="dashicons dashicons-arrow-down-alt2 wppoppop-acc-caret"></span>
            </button>
            <div class="wppoppop-acc-body" style="display:none;">
                <label>User Authentication Status</label>
                <select id="set-target-auth">
                    <option value="all">All Visitors</option>
                    <option value="logged_in">Logged In Users Only</option>
                    <option value="logged_out">Logged Out Visitors Only</option>
                </select>
            </div>
        </div>

        <!-- 12. Frequency & Cookies -->
        <div class="wppoppop-acc-item">
            <button type="button" class="wppoppop-acc-header">
                <span>12. Frequency & Cookie Limits</span>
                <span class="dashicons dashicons-arrow-down-alt2 wppoppop-acc-caret"></span>
            </button>
            <div class="wppoppop-acc-body" style="display:none;">
                <label>Display Frequency</label>
                <select id="set-freq-mode">
                    <option value="always">Always Show</option>
                    <option value="once_per_session">Once Per Browser Session</option>
                    <option value="once_per_week">Once Every 7 Days</option>
                </select>
            </div>
        </div>

        <!-- 13. WooCommerce Conversion Suite -->
        <div class="wppoppop-acc-item">
            <button type="button" class="wppoppop-acc-header">
                <span>13. WooCommerce Conversion Suite</span>
                <span class="dashicons dashicons-arrow-down-alt2 wppoppop-acc-caret"></span>
            </button>
            <div class="wppoppop-acc-body" style="display:none;">
                <label class="wppoppop-slide-toggle">
                    <span class="wppoppop-switch">
                        <input type="checkbox" id="set-wc-coupon">
                        <span class="wppoppop-slider"></span>
                    </span>
                    <span class="wppoppop-switch-label">Auto-Generate Personal Coupon</span>
                </label>
                <div style="margin-top:8px;">
                    <label>Coupon Value (%)</label>
                    <input type="number" id="set-wc-amount" value="15">
                </div>
            </div>
        </div>

        <!-- 14. Scoped CSS & JS -->
        <div class="wppoppop-acc-item">
            <button type="button" class="wppoppop-acc-header">
                <span>14. Scoped CSS & JavaScript</span>
                <span class="dashicons dashicons-arrow-down-alt2 wppoppop-acc-caret"></span>
            </button>
            <div class="wppoppop-acc-body" style="display:none;">
                <label>Custom Scoped CSS</label>
                <textarea id="set-custom-css" rows="3" placeholder="#wppoppop-canvas-box { border-radius: 12px; }"></textarea>
                <label style="margin-top:8px;">Custom Scoped JS</label>
                <textarea id="set-custom-js" rows="3" placeholder="console.log('Popup initialized');"></textarea>
            </div>
        </div>

        <!-- 15. Quiz & Lead Scoring -->
        <div class="wppoppop-acc-item">
            <button type="button" class="wppoppop-acc-header">
                <span>15. Quiz & Lead Scoring</span>
                <span class="dashicons dashicons-arrow-down-alt2 wppoppop-acc-caret"></span>
            </button>
            <div class="wppoppop-acc-body" style="display:none;">
                <label class="wppoppop-slide-toggle">
                    <span class="wppoppop-switch">
                        <input type="checkbox" id="set-quiz-enable">
                        <span class="wppoppop-slider"></span>
                    </span>
                    <span class="wppoppop-switch-label">Enable Quiz Scoring</span>
                </label>
                <div style="margin-top:8px;">
                    <label>Pass Score Threshold</label>
                    <input type="number" id="set-quiz-pass" value="70">
                </div>
                <div style="margin-top:8px;">
                    <label class="wppoppop-slide-toggle">
                        <span class="wppoppop-switch">
                            <input type="checkbox" id="set-quiz-confetti" checked>
                            <span class="wppoppop-slider"></span>
                        </span>
                        <span class="wppoppop-switch-label">Trigger Confetti on Pass</span>
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>
