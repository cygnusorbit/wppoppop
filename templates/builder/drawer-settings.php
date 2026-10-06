<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-drawer-backdrop" id="wppoppop-settings-drawer-backdrop"></div>
<div class="wppoppop-drawer" id="wppoppop-settings-drawer">
    <div class="wppoppop-drawer-header">
        <h3>Campaign Settings</h3>
        <button type="button" class="wppoppop-drawer-close" id="wppoppop-settings-drawer-close">&times;</button>
    </div>
    <div class="wppoppop-drawer-body">
        <div class="wppoppop-accordion">
            <!-- 1. Box & Backdrop Styling -->
            <div class="wppoppop-accordion-item">
                <div class="wppoppop-accordion-header"><span>Box & Backdrop Styling</span><span class="dashicons dashicons-arrow-down-alt2"></span></div>
                <div class="wppoppop-accordion-body">
                    <label>Popup Title</label>
                    <input type="text" id="wppoppop-cfg-title" class="widefat" value="My New Popup">
                    <div style="display:flex;gap:10px;margin-top:10px;">
                        <div style="flex:1;"><label>Width (px)</label><input type="number" id="wppoppop-cfg-width" value="640"></div>
                        <div style="flex:1;"><label>Height (px)</label><input type="number" id="wppoppop-cfg-height" value="400"></div>
                    </div>
                    <label style="margin-top:10px;display:block;">Background Mode</label>
                    <select id="wppoppop-cfg-bg-mode" class="widefat">
                        <option value="solid">Solid Color</option>
                        <option value="gradient">Linear Gradient</option>
                    </select>
                    <div id="wppoppop-cfg-solid-wrap" style="margin-top:10px;">
                        <label>Background Color</label>
                        <input type="color" id="wppoppop-cfg-bg-color" value="#ffffff">
                    </div>
                    <div id="wppoppop-cfg-gradient-wrap" style="display:none;margin-top:10px;">
                        <div style="display:flex;gap:10px;">
                            <div><label>Color 1</label><input type="color" id="wppoppop-cfg-grad-1" value="#1e293b"></div>
                            <div><label>Color 2</label><input type="color" id="wppoppop-cfg-grad-2" value="#0f172a"></div>
                        </div>
                        <label style="margin-top:8px;display:block;">Angle (deg)</label>
                        <input type="number" id="wppoppop-cfg-grad-angle" value="135">
                    </div>
                    <label style="margin-top:10px;display:block;">Backdrop Glassmorphism Blur (px)</label>
                    <input type="number" id="wppoppop-cfg-blur" value="0">
                </div>
            </div>

            <!-- 2. Display Triggers -->
            <div class="wppoppop-accordion-item">
                <div class="wppoppop-accordion-header"><span>Display Triggers</span><span class="dashicons dashicons-arrow-down-alt2"></span></div>
                <div class="wppoppop-accordion-body">
                    <label><input type="checkbox" id="wppoppop-trig-onload" checked> On Page Load</label>
                    <div style="margin:5px 0 10px 20px;"><label>Delay (seconds)</label><input type="number" id="wppoppop-trig-delay" value="0"></div>
                    <label><input type="checkbox" id="wppoppop-trig-exit"> On Exit Intent (Cursor Top)</label>
                    <label style="margin-top:8px;display:block;"><input type="checkbox" id="wppoppop-trig-scroll"> On Scroll Depth</label>
                    <div style="margin:5px 0 10px 20px;"><label>Scroll %</label><input type="number" id="wppoppop-trig-scroll-val" value="50"></div>
                    <label><input type="checkbox" id="wppoppop-trig-idle"> On Inactivity (Idle)</label>
                    <div style="margin:5px 0 10px 20px;"><label>Idle Seconds</label><input type="number" id="wppoppop-trig-idle-val" value="30"></div>
                    <label><input type="checkbox" id="wppoppop-trig-adblock"> On AdBlock Detected</label>
                    <label style="margin-top:8px;display:block;"><input type="checkbox" id="wppoppop-trig-backbutton"> On Mobile Back-Button (Exit Intercept)</label>
                </div>
            </div>

            <!-- 3. Targeting & Geolocation -->
            <div class="wppoppop-accordion-item">
                <div class="wppoppop-accordion-header"><span>Targeting & Geolocation</span><span class="dashicons dashicons-arrow-down-alt2"></span></div>
                <div class="wppoppop-accordion-body">
                    <label>Device Visibility</label>
                    <select id="wppoppop-target-devices" class="widefat">
                        <option value="all">All Devices</option>
                        <option value="desktop">Desktop Only</option>
                        <option value="mobile">Mobile & Tablets Only</option>
                    </select>
                    <label style="margin-top:10px;display:block;">Visitor Authentication</label>
                    <select id="wppoppop-target-auth" class="widefat">
                        <option value="all">All Visitors</option>
                        <option value="guests_only">Guests / Logged-Out Only</option>
                        <option value="logged_in_only">Logged-In Users Only</option>
                    </select>
                    <label style="margin-top:10px;display:block;">Geolocation Mode</label>
                    <select id="wppoppop-target-geomode" class="widefat">
                        <option value="all">All Countries</option>
                        <option value="whitelist">Whitelist (Only allowed)</option>
                        <option value="blacklist">Blacklist (Block countries)</option>
                    </select>
                    <label style="margin-top:5px;display:block;">Country Codes (ISO, comma-separated e.g. US,CA,GB)</label>
                    <input type="text" id="wppoppop-target-geocountries" class="widefat" placeholder="US, CA, GB">
                </div>
            </div>

            <!-- 4. Frequency Capping & Cookies -->
            <div class="wppoppop-accordion-item">
                <div class="wppoppop-accordion-header"><span>Frequency & Cookies</span><span class="dashicons dashicons-arrow-down-alt2"></span></div>
                <div class="wppoppop-accordion-body">
                    <label>Display Frequency</label>
                    <select id="wppoppop-freq-mode" class="widefat">
                        <option value="always">Always Show</option>
                        <option value="session">Once Per Session</option>
                        <option value="days">Hide for X Days After Close</option>
                    </select>
                    <div style="margin-top:8px;"><label>Days to Hide</label><input type="number" id="wppoppop-freq-days" value="7"></div>
                    <label style="margin-top:10px;display:block;"><input type="checkbox" id="wppoppop-freq-hide-submit" checked> Hide Forever After Submission</label>
                </div>
            </div>

            <!-- 5. Subscriber Autoresponder -->
            <div class="wppoppop-accordion-item">
                <div class="wppoppop-accordion-header"><span>Subscriber Autoresponder</span><span class="dashicons dashicons-arrow-down-alt2"></span></div>
                <div class="wppoppop-accordion-body">
                    <label><input type="checkbox" id="wppoppop-ar-enable"> Send User Autoresponder Email</label>
                    <label style="margin-top:8px;display:block;">Email Subject</label>
                    <input type="text" id="wppoppop-ar-subject" class="widefat" value="Here is your reward!">
                    <label style="margin-top:8px;display:block;">Email Message Body ({email}, {prize} supported)</label>
                    <textarea id="wppoppop-ar-message" class="widefat" rows="4">Thank you for subscribing! Enjoy your offer.</textarea>
                </div>
            </div>

            <!-- 6. Marketing & Webhooks -->
            <div class="wppoppop-accordion-item">
                <div class="wppoppop-accordion-header"><span>Marketing & Webhooks</span><span class="dashicons dashicons-arrow-down-alt2"></span></div>
                <div class="wppoppop-accordion-body">
                    <label>Webhook URL (Zapier / Make / Webhook)</label>
                    <input type="url" id="wppoppop-wh-url" class="widefat" placeholder="https://api.example.com/webhook">
                    <label style="margin-top:8px;display:block;">HMAC-SHA256 Secret Key</label>
                    <input type="password" id="wppoppop-wh-secret" class="widefat">
                    <button type="button" class="button" id="wppoppop-wh-test" style="margin-top:8px;">Test Webhook Ping</button>
                    <hr style="margin:12px 0;">
                    <label><input type="checkbox" id="wppoppop-mc-enable"> Enable Mailchimp Sync</label>
                    <input type="text" id="wppoppop-mc-key" class="widefat" placeholder="API Key" style="margin-top:5px;">
                    <input type="text" id="wppoppop-mc-list" class="widefat" placeholder="Audience List ID" style="margin-top:5px;">
                </div>
            </div>

            <!-- 7. Twilio SMS Alerts -->
            <div class="wppoppop-accordion-item">
                <div class="wppoppop-accordion-header"><span>Twilio SMS Alerts</span><span class="dashicons dashicons-arrow-down-alt2"></span></div>
                <div class="wppoppop-accordion-body">
                    <label><input type="checkbox" id="wppoppop-sms-enable"> Send SMS Alert on Lead</label>
                    <input type="text" id="wppoppop-sms-sid" class="widefat" placeholder="Account SID" style="margin-top:5px;">
                    <input type="password" id="wppoppop-sms-token" class="widefat" placeholder="Auth Token" style="margin-top:5px;">
                    <input type="text" id="wppoppop-sms-from" class="widefat" placeholder="Twilio Phone Number" style="margin-top:5px;">
                    <input type="text" id="wppoppop-sms-to" class="widefat" placeholder="Recipient Number" style="margin-top:5px;">
                    <button type="button" class="button" id="wppoppop-sms-test" style="margin-top:8px;">Test SMS Dispatch</button>
                </div>
            </div>

            <!-- 8. Sticky Side Tabs -->
            <div class="wppoppop-accordion-item">
                <div class="wppoppop-accordion-header"><span>Sticky Side Tabs</span><span class="dashicons dashicons-arrow-down-alt2"></span></div>
                <div class="wppoppop-accordion-body">
                    <label><input type="checkbox" id="wppoppop-tab-enable"> Enable Side Tab Trigger</label>
                    <label style="margin-top:8px;display:block;">Tab Text</label>
                    <input type="text" id="wppoppop-tab-text" class="widefat" value="Special Offer">
                    <label style="margin-top:8px;display:block;">Position</label>
                    <select id="wppoppop-tab-pos" class="widefat"><option value="left">Left Edge</option><option value="right">Right Edge</option></select>
                </div>
            </div>

            <!-- 9. Sticky Floating Ribbon -->
            <div class="wppoppop-accordion-item">
                <div class="wppoppop-accordion-header"><span>Sticky Floating Ribbon</span><span class="dashicons dashicons-arrow-down-alt2"></span></div>
                <div class="wppoppop-accordion-body">
                    <label><input type="checkbox" id="wppoppop-ribbon-enable"> Display as Floating Ribbon Bar</label>
                    <label style="margin-top:8px;display:block;">Position</label>
                    <select id="wppoppop-ribbon-pos" class="widefat"><option value="top">Top Header Bar</option><option value="bottom">Bottom Footer Bar</option></select>
                </div>
            </div>

            <!-- 10. WooCommerce Suite -->
            <div class="wppoppop-accordion-item">
                <div class="wppoppop-accordion-header"><span>WooCommerce Conversion Suite</span><span class="dashicons dashicons-arrow-down-alt2"></span></div>
                <div class="wppoppop-accordion-body">
                    <label><input type="checkbox" id="wppoppop-wc-coupon-enable"> Auto-Generate Single-Use Coupon</label>
                    <div style="display:flex;gap:10px;margin-top:5px;">
                        <div><label>Discount %</label><input type="number" id="wppoppop-wc-coupon-amt" value="10"></div>
                        <div><label>Prefix</label><input type="text" id="wppoppop-wc-coupon-pfx" value="POP"></div>
                    </div>
                    <label style="margin-top:8px;display:block;"><input type="checkbox" id="wppoppop-wc-auto-apply"> Automatically Apply to Live Cart</label>
                </div>
            </div>

            <!-- 11. Payments & Checkout -->
            <div class="wppoppop-accordion-item">
                <div class="wppoppop-accordion-header"><span>Payments & Checkout</span><span class="dashicons dashicons-arrow-down-alt2"></span></div>
                <div class="wppoppop-accordion-body">
                    <label>Currency</label>
                    <select id="wppoppop-pay-currency" class="widefat"><option value="USD">USD ($)</option><option value="EUR">EUR (€)</option><option value="GBP">GBP (£)</option></select>
                    <label style="margin-top:8px;display:block;">Gateway Mode</label>
                    <select id="wppoppop-pay-gateway" class="widefat"><option value="Stripe">Stripe</option><option value="PayPal">PayPal</option></select>
                </div>
            </div>

            <!-- 12. Secure Downloads -->
            <div class="wppoppop-accordion-item">
                <div class="wppoppop-accordion-header"><span>Secure Downloads</span><span class="dashicons dashicons-arrow-down-alt2"></span></div>
                <div class="wppoppop-accordion-body">
                    <label><input type="checkbox" id="wppoppop-dl-enable"> Enable Tokenized Download</label>
                    <label style="margin-top:8px;display:block;">Protected Media File URL</label>
                    <input type="url" id="wppoppop-dl-url" class="widefat" placeholder="https://site.com/uploads/ebook.pdf">
                </div>
            </div>

            <!-- 13. Quiz & Lead Scoring -->
            <div class="wppoppop-accordion-item">
                <div class="wppoppop-accordion-header"><span>Quiz & Lead Scoring</span><span class="dashicons dashicons-arrow-down-alt2"></span></div>
                <div class="wppoppop-accordion-body">
                    <label><input type="checkbox" id="wppoppop-quiz-enable"> Enable Quiz & Scoring Evaluation</label>
                    <div style="margin-top:8px;"><label>Pass Score Threshold</label><input type="number" id="wppoppop-quiz-pass-score" value="40"></div>
                    <div style="display:flex;gap:10px;margin-top:5px;">
                        <div><label>Pass Screen</label><input type="number" id="wppoppop-quiz-pass-screen" value="2"></div>
                        <div><label>Fail Screen</label><input type="number" id="wppoppop-quiz-fail-screen" value="3"></div>
                    </div>
                    <label style="margin-top:8px;display:block;"><input type="checkbox" id="wppoppop-quiz-confetti" checked> Trigger Confetti Shower on Pass</label>
                </div>
            </div>

            <!-- 14. Sound Effects -->
            <div class="wppoppop-accordion-item">
                <div class="wppoppop-accordion-header"><span>Sound Effects</span><span class="dashicons dashicons-arrow-down-alt2"></span></div>
                <div class="wppoppop-accordion-body">
                    <label><input type="checkbox" id="wppoppop-snd-enable"> Enable Web Audio Synth Chimes</label>
                </div>
            </div>

            <!-- 15. Scoped Custom CSS & JS -->
            <div class="wppoppop-accordion-item">
                <div class="wppoppop-accordion-header"><span>Scoped CSS & JS</span><span class="dashicons dashicons-arrow-down-alt2"></span></div>
                <div class="wppoppop-accordion-body">
                    <label>Custom Scoped CSS</label>
                    <textarea id="wppoppop-custom-css" class="widefat" rows="3" placeholder="#wppoppop-popup-{uid} .my-cls { ... }"></textarea>
                    <label style="margin-top:8px;display:block;">Custom JS Callbacks</label>
                    <textarea id="wppoppop-custom-js" class="widefat" rows="3" placeholder="console.log('Popup active');"></textarea>
                </div>
            </div>
        </div>
    </div>
</div>
