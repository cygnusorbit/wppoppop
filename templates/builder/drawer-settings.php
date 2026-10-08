<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<!-- Slide-Out Backdrop -->
<div id="wppoppop-settings-backdrop" class="wppoppop-settings-backdrop"></div>

<!-- Hardware-Accelerated Slide-In Campaign Settings Drawer -->
<div id="wppoppop-settings-drawer" class="wppoppop-settings-drawer">
    <div class="wppoppop-settings-drawer-header" style="display:flex;align-items:center;justify-content:space-between;padding:14px 18px;background:#0f172a;color:#ffffff;flex-shrink:0;">
        <h3 class="wppoppop-settings-drawer-title" style="margin:0;font-size:15px;font-weight:700;color:#ffffff !important;line-height:1.3;">Campaign Settings</h3>
        <button type="button" id="wppoppop-settings-drawer-close" style="background:transparent;border:none;color:#94a3b8;font-size:22px;cursor:pointer;line-height:1;">&times;</button>
    </div>

    <div style="flex:1;overflow-y:auto;padding:16px;">
        <div class="wppoppop-accordion-group">
            
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

                        <div style="margin-bottom:12px;">
                            <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">CANVAS BACKGROUND FILL</label>
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
                        <div class="wppoppop-anim-setting-wrap" style="margin-top:16px;padding-top:14px;border-top:1px solid #e2e8f0;">
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                                <div style="display:flex;align-items:center;gap:6px;">
                                    <span style="font-size:12px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:0.5px;">ANIMATION</span>
                                    <a href="https://animate.style/" target="_blank" rel="noopener" class="dashicons dashicons-editor-help" style="font-size:16px;width:16px;height:16px;color:#2563eb;text-decoration:none;" title="Powered by Animate.css (https://animate.style/)"></a>
                                </div>
                                <span style="font-size:10px;font-weight:700;background:#eff6ff;color:#2563eb;padding:1px 6px;border-radius:4px;border:1px solid #bfdbfe;">Animate.css</span>
                            </div>

                            <!-- Appearance Dropdown (Animate.css Entrances) -->
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

            <!-- 2. Display Triggers -->
            <div class="wppoppop-acc-item">
                <button type="button" class="wppoppop-acc-header">2. Display Triggers</button>
                <div class="wppoppop-acc-body" style="display:none;">
                    <label><input type="checkbox" id="trig-load" value="1"> On Page Load</label>
                    <input type="number" id="trig-load-delay" placeholder="Delay (seconds)" class="widefat" style="margin:4px 0 8px 0;">
                    <label><input type="checkbox" id="trig-exit" value="1"> On Exit Intent</label><br>
                    <label><input type="checkbox" id="trig-scroll" value="1"> On Scroll Depth (%)</label>
                    <input type="number" id="trig-scroll-val" placeholder="Scroll % (e.g. 50)" class="widefat" style="margin:4px 0 8px 0;">
                    <label><input type="checkbox" id="trig-adblock" value="1"> On AdBlock Detected</label><br>
                    <label><input type="checkbox" id="trig-backbutton" value="1"> On Mobile Back-Button</label>
                </div>
            </div>

            <!-- 3. Conditional Logic & Math -->
            <div class="wppoppop-acc-item">
                <button type="button" class="wppoppop-acc-header">3. Conditional Logic &amp; Math</button>
                <div class="wppoppop-acc-body" style="display:none;">
                    <label>Real-Time Math Calculation Formula</label>
                    <input type="text" id="set-math-formula" placeholder="{qty} * 25" class="widefat" style="margin-bottom:6px;">
                    <input type="text" id="set-math-target" placeholder="Target Element ID" class="widefat">
                </div>
            </div>

            <!-- 4. Sticky Side Tabs -->
            <div class="wppoppop-acc-item">
                <button type="button" class="wppoppop-acc-header">4. Sticky Side Tabs</button>
                <div class="wppoppop-acc-body" style="display:none;">
                    <label><input type="checkbox" id="set-sidetab-enable" value="1"> Enable Sticky Side Tab</label>
                    <input type="text" id="set-sidetab-label" placeholder="Feedback / Offer" class="widefat" style="margin:6px 0;">
                    <select id="set-sidetab-pos" class="widefat">
                        <option value="left">Left Edge</option>
                        <option value="right">Right Edge</option>
                    </select>
                </div>
            </div>

            <!-- 5. Payments & Checkout -->
            <div class="wppoppop-acc-item">
                <button type="button" class="wppoppop-acc-header">5. Payments &amp; Checkout</button>
                <div class="wppoppop-acc-body" style="display:none;">
                    <label>Gateway</label>
                    <select id="set-pay-gateway" class="widefat" style="margin-bottom:6px;">
                        <option value="stripe">Stripe</option>
                        <option value="paypal">PayPal</option>
                    </select>
                    <input type="number" step="0.01" id="set-pay-amount" placeholder="Amount (e.g. 19.99)" class="widefat">
                </div>
            </div>

            <!-- 6. Secure Downloads -->
            <div class="wppoppop-acc-item">
                <button type="button" class="wppoppop-acc-header">6. Secure Downloads</button>
                <div class="wppoppop-acc-body" style="display:none;">
                    <label><input type="checkbox" id="set-dl-enable" value="1"> Enable Tokenized Download</label>
                    <input type="url" id="set-dl-url" placeholder="Protected File URL" class="widefat" style="margin-top:6px;">
                </div>
            </div>

            <!-- 7. Video Playback Listeners -->
            <div class="wppoppop-acc-item">
                <button type="button" class="wppoppop-acc-header">7. Video Playback Listeners</button>
                <div class="wppoppop-acc-body" style="display:none;">
                    <label><input type="checkbox" id="set-vid-enable" value="1"> Listen for Video Playback</label>
                    <input type="number" id="set-vid-time" placeholder="Trigger at timestamp (seconds)" class="widefat" style="margin-top:6px;">
                </div>
            </div>

            <!-- 8. Subscriber Autoresponder -->
            <div class="wppoppop-acc-item">
                <button type="button" class="wppoppop-acc-header">8. Subscriber Autoresponder</button>
                <div class="wppoppop-acc-body" style="display:none;">
                    <label><input type="checkbox" id="set-auto-enable" value="1"> Send Welcome Email</label>
                    <input type="text" id="set-auto-subject" placeholder="Email Subject" class="widefat" style="margin:6px 0;">
                    <textarea id="set-auto-body" rows="3" placeholder="Thank you for subscribing!" class="widefat"></textarea>
                </div>
            </div>

            <!-- 9. Marketing & Webhooks -->
            <div class="wppoppop-acc-item">
                <button type="button" class="wppoppop-acc-header">9. Marketing &amp; Webhooks</button>
                <div class="wppoppop-acc-body" style="display:none;">
                    <label>Webhook URL (POST)</label>
                    <input type="url" id="set-webhook-url" placeholder="https://..." class="widefat" style="margin-bottom:6px;">
                    <input type="text" id="set-webhook-secret" placeholder="HMAC Secret Key" class="widefat">
                </div>
            </div>

            <!-- 10. Twilio SMS Alerts -->
            <div class="wppoppop-acc-item">
                <button type="button" class="wppoppop-acc-header">10. Twilio SMS Alerts</button>
                <div class="wppoppop-acc-body" style="display:none;">
                    <label><input type="checkbox" id="set-sms-enable" value="1"> Enable Lead SMS Notification</label>
                    <input type="text" id="set-sms-phone" placeholder="+1234567890" class="widefat" style="margin-top:6px;">
                </div>
            </div>

            <!-- 11. Targeting & Attribution -->
            <div class="wppoppop-acc-item">
                <button type="button" class="wppoppop-acc-header">11. Targeting &amp; Attribution</button>
                <div class="wppoppop-acc-body" style="display:none;">
                    <label>Visitor Authentication</label>
                    <select id="set-target-auth" class="widefat" style="margin-bottom:6px;">
                        <option value="all">All Visitors</option>
                        <option value="guest">Guests Only</option>
                        <option value="user">Logged-In Users Only</option>
                    </select>
                </div>
            </div>

            <!-- 12. Frequency Capping & Cookies -->
            <div class="wppoppop-acc-item">
                <button type="button" class="wppoppop-acc-header">12. Frequency Capping &amp; Cookies</button>
                <div class="wppoppop-acc-body" style="display:none;">
                    <label>Show Frequency</label>
                    <select id="set-freq-mode" class="widefat">
                        <option value="always">Always Display</option>
                        <option value="session">Once Per Browser Session</option>
                        <option value="days">Once Every X Days</option>
                    </select>
                </div>
            </div>

            <!-- 13. WooCommerce Conversion Suite -->
            <div class="wppoppop-acc-item">
                <button type="button" class="wppoppop-acc-header">13. WooCommerce Conversion Suite</button>
                <div class="wppoppop-acc-body" style="display:none;">
                    <label><input type="checkbox" id="set-wc-coupon" value="1"> Auto-Generate Dynamic Coupon</label>
                    <input type="text" id="set-wc-amount" placeholder="Discount Amount (%)" class="widefat" style="margin-top:6px;">
                </div>
            </div>

            <!-- 14. Custom Scoped CSS & JavaScript -->
            <div class="wppoppop-acc-item">
                <button type="button" class="wppoppop-acc-header">14. Custom Scoped CSS &amp; JS</button>
                <div class="wppoppop-acc-body" style="display:none;">
                    <textarea id="set-custom-css" rows="3" placeholder=".wppoppop-box { ... }" class="widefat code" style="margin-bottom:6px;"></textarea>
                    <textarea id="set-custom-js" rows="3" placeholder="console.log('Ready');" class="widefat code"></textarea>
                </div>
            </div>

            <!-- 15. Quiz & Lead Scoring -->
            <div class="wppoppop-acc-item">
                <button type="button" class="wppoppop-acc-header">15. Quiz &amp; Lead Scoring</button>
                <div class="wppoppop-acc-body" style="display:none;">
                    <label><input type="checkbox" id="set-quiz-enable" value="1"> Enable Lead Scoring</label>
                    <input type="number" id="set-quiz-pass" placeholder="Pass Threshold Score" class="widefat" style="margin:6px 0;">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:6px;">
                        <div>
                            <label style="font-size:11px;font-weight:600;color:#475569;">Pass Canvas</label>
                            <input type="number" id="set-quiz-pass-canvas" name="set-quiz-pass-screen" value="2" class="widefat" style="font-size:12px;">
                        </div>
                        <div>
                            <label style="font-size:11px;font-weight:600;color:#475569;">Fail Canvas</label>
                            <input type="number" id="set-quiz-fail-canvas" name="set-quiz-fail-screen" value="3" class="widefat" style="font-size:12px;">
                        </div>
                    </div>
                    <label><input type="checkbox" id="set-quiz-confetti" value="1"> Trigger Particle Confetti on Pass</label>
                </div>
            </div>

        </div>
    </div>
</div>
