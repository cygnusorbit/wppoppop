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
        <!-- 1. Box & Backdrop -->
        <div class="wppoppop-acc-item">
            <button type="button" class="wppoppop-acc-header active">
                <span>1. Canvas Dimensions & Background</span>
                <span class="dashicons dashicons-arrow-down-alt2 wppoppop-acc-caret"></span>
            </button>
            <div class="wppoppop-acc-body">
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
                <div id="set-solid-wrap" style="margin-bottom:10px;">
                    <label>Background Color</label>
                    <input type="text" id="set-bg-color" value="#ffffff">
                </div>
                <div id="set-gradient-wrap" style="display:none;margin-bottom:10px;">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:6px;">
                        <div>
                            <label>Gradient Start</label>
                            <input type="text" id="set-grad-color1" value="#3b82f6">
                        </div>
                        <div>
                            <label>Gradient End</label>
                            <input type="text" id="set-grad-color2" value="#1d4ed8">
                        </div>
                    </div>
                    <label>Angle (deg)</label>
                    <input type="number" id="set-grad-angle" value="135">
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
                <label style="display:flex;align-items:center;gap:6px;margin-bottom:8px;">
                    <input type="checkbox" id="trig-load" checked> On Page Load
                </label>
                <div style="margin-bottom:10px;margin-left:20px;">
                    <label>Delay (seconds)</label>
                    <input type="number" id="trig-load-delay" value="0" min="0">
                </div>
                <label style="display:flex;align-items:center;gap:6px;margin-bottom:8px;">
                    <input type="checkbox" id="trig-exit"> Exit Intent (Mouse leaves window)
                </label>
                <label style="display:flex;align-items:center;gap:6px;margin-bottom:8px;">
                    <input type="checkbox" id="trig-scroll"> On Scroll Percentage
                </label>
                <div style="margin-bottom:10px;margin-left:20px;">
                    <label>Scroll %</label>
                    <input type="number" id="trig-scroll-val" value="50" min="1" max="100">
                </div>
                <label style="display:flex;align-items:center;gap:6px;margin-bottom:8px;">
                    <input type="checkbox" id="trig-adblock"> AdBlock Detector Trigger
                </label>
                <label style="display:flex;align-items:center;gap:6px;">
                    <input type="checkbox" id="trig-backbutton"> Browser Back-Button Trap
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
                <label style="display:flex;align-items:center;gap:6px;margin-bottom:8px;">
                    <input type="checkbox" id="set-sidetab-enable"> Enable Sticky Tab
                </label>
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
                <label style="display:flex;align-items:center;gap:6px;margin-bottom:8px;">
                    <input type="checkbox" id="set-dl-enable"> Deliver Download on Submit
                </label>
                <label>Direct File URL</label>
                <input type="url" id="set-dl-url" placeholder="https://example.com/ebook.pdf">
            </div>
        </div>

        <!-- 7. Video Listeners -->
        <div class="wppoppop-acc-item">
            <button type="button" class="wppoppop-acc-header">
                <span>7. Embedded Video Listeners</span>
                <span class="dashicons dashicons-arrow-down-alt2 wppoppop-acc-caret"></span>
            </button>
            <div class="wppoppop-acc-body" style="display:none;">
                <label style="display:flex;align-items:center;gap:6px;margin-bottom:8px;">
                    <input type="checkbox" id="set-vid-enable"> Trigger on Video Playback
                </label>
                <label>Trigger after (seconds)</label>
                <input type="number" id="set-vid-time" value="30">
            </div>
        </div>

        <!-- 8. Subscriber Autoresponder -->
        <div class="wppoppop-acc-item">
            <button type="button" class="wppoppop-acc-header">
                <span>8. Subscriber Autoresponder</span>
                <span class="dashicons dashicons-arrow-down-alt2 wppoppop-acc-caret"></span>
            </button>
            <div class="wppoppop-acc-body" style="display:none;">
                <label style="display:flex;align-items:center;gap:6px;margin-bottom:8px;">
                    <input type="checkbox" id="set-auto-enable"> Send User Welcome Email
                </label>
                <label>Email Subject</label>
                <input type="text" id="set-auto-subject" placeholder="Welcome! Here is your download">
                <label style="margin-top:8px;">Email Body</label>
                <textarea id="set-auto-body" rows="4" placeholder="Hello {email}, thanks for subscribing!"></textarea>
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
                <label style="display:flex;align-items:center;gap:6px;margin-bottom:8px;">
                    <input type="checkbox" id="set-sms-enable"> Send Admin SMS on Lead
                </label>
                <label>Destination Phone</label>
                <input type="tel" id="set-sms-phone" placeholder="+1234567890">
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
                <label style="display:flex;align-items:center;gap:6px;margin-bottom:8px;">
                    <input type="checkbox" id="set-wc-coupon"> Auto-Generate Personal Coupon
                </label>
                <label>Coupon Value (%)</label>
                <input type="number" id="set-wc-amount" value="15">
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
                <label style="display:flex;align-items:center;gap:6px;margin-bottom:8px;">
                    <input type="checkbox" id="set-quiz-enable"> Enable Quiz Scoring
                </label>
                <label>Pass Score Threshold</label>
                <input type="number" id="set-quiz-pass" value="70">
                <label style="display:flex;align-items:center;gap:6px;margin-top:8px;">
                    <input type="checkbox" id="set-quiz-confetti" checked> Trigger Confetti on Pass
                </label>
            </div>
        </div>
    </div>
</div>
