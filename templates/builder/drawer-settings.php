<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div id="wppoppop-settings-drawer" style="position:fixed;top:0;left:0;bottom:0;width:380px;background:#ffffff;box-shadow:4px 0 24px rgba(0,0,0,0.25);z-index:99999;display:none;flex-direction:column;overflow:hidden;">
    <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 18px;background:#0f172a;color:#ffffff;">
        <h3 style="margin:0;font-size:15px;font-weight:700;">Campaign Settings</h3>
        <button type="button" id="wppoppop-settings-drawer-close" style="background:transparent;border:none;color:#94a3b8;font-size:22px;cursor:pointer;line-height:1;">&times;</button>
    </div>

    <div style="flex:1;overflow-y:auto;padding:16px;">
        <div class="wppoppop-accordion-group">
            <!-- 1. Box & Backdrop Styling -->
            <div class="wppoppop-acc-item">
                <button type="button" class="wppoppop-acc-header">1. Box & Backdrop Styling</button>
                <div class="wppoppop-acc-body">
                    <label>Popup Dimensions (W x H)</label>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:8px;">
                        <input type="number" id="set-box-width" value="640" class="widefat">
                        <input type="number" id="set-box-height" value="400" class="widefat">
                    </div>
                    <label>Background Fill Mode</label>
                    <select id="set-bg-mode" class="widefat" style="margin-bottom:8px;">
                        <option value="solid">Solid Background Color</option>
                        <option value="gradient">Linear Gradient</option>
                    </select>
                    <div id="set-solid-wrap">
                        <input type="color" id="set-bg-color" value="#ffffff" class="widefat" style="height:32px;">
                    </div>
                    <div id="set-gradient-wrap" style="display:none;">
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:6px;">
                            <input type="color" id="set-grad-color1" value="#3b82f6" class="widefat" style="height:32px;">
                            <input type="color" id="set-grad-color2" value="#1d4ed8" class="widefat" style="height:32px;">
                        </div>
                        <input type="number" id="set-grad-angle" value="135" min="0" max="360" placeholder="Angle (0-360°)" class="widefat">
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
                <button type="button" class="wppoppop-acc-header">3. Conditional Logic & Math</button>
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
                <button type="button" class="wppoppop-acc-header">5. Payments & Checkout</button>
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
                <button type="button" class="wppoppop-acc-header">9. Marketing & Webhooks</button>
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
                <button type="button" class="wppoppop-acc-header">11. Targeting & Attribution</button>
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
                <button type="button" class="wppoppop-acc-header">12. Frequency Capping & Cookies</button>
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
                <button type="button" class="wppoppop-acc-header">14. Custom Scoped CSS & JS</button>
                <div class="wppoppop-acc-body" style="display:none;">
                    <textarea id="set-custom-css" rows="3" placeholder=".wppoppop-box { ... }" class="widefat code" style="margin-bottom:6px;"></textarea>
                    <textarea id="set-custom-js" rows="3" placeholder="console.log('Ready');" class="widefat code"></textarea>
                </div>
            </div>

            <!-- 15. Quiz & Lead Scoring -->
            <div class="wppoppop-acc-item">
                <button type="button" class="wppoppop-acc-header">15. Quiz & Lead Scoring</button>
                <div class="wppoppop-acc-body" style="display:none;">
                    <label><input type="checkbox" id="set-quiz-enable" value="1"> Enable Lead Scoring</label>
                    <input type="number" id="set-quiz-pass" placeholder="Pass Threshold Score" class="widefat" style="margin:6px 0;">
                    <label><input type="checkbox" id="set-quiz-confetti" value="1"> Trigger Particle Confetti on Pass</label>
                </div>
            </div>
        </div>
    </div>
</div>
