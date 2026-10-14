<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div id="wppoppop-inspector-drawer" class="wppoppop-inspector-drawer" style="position:fixed;top:52px;right:0;bottom:0;width:340px;background:#1e293b;border-left:1px solid #334155;z-index:99995;transform:translateX(100%);transition:transform 0.25s cubic-bezier(0.16,1,0.3,1);display:flex;flex-direction:column;box-shadow:-5px 0 25px rgba(0,0,0,0.5);font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;color:#f8fafc;box-sizing:border-box;">
    
    <!-- Inspector Header -->
    <div style="height:48px;background:#0f172a;border-bottom:1px solid #334155;display:flex;align-items:center;justify-content:space-between;padding:0 16px;flex-shrink:0;">
        <span style="font-size:12px;font-weight:700;letter-spacing:0.5px;color:#38bdf8;display:flex;align-items:center;gap:6px;">
            <span class="dashicons dashicons-admin-generic" style="font-size:16px;width:16px;height:16px;"></span> LAYER SETTINGS
        </span>
        <button type="button" id="wppoppop-inspector-close" style="background:transparent;border:none;color:#94a3b8;cursor:pointer;font-size:18px;padding:4px;display:flex;align-items:center;justify-content:center;line-height:1;" title="Close Inspector">&times;</button>
    </div>

    <!-- Inspector Tabs -->
    <div style="display:flex;background:#0f172a;border-bottom:1px solid #334155;padding:0 8px;flex-shrink:0;">
        <button type="button" class="wppoppop-insp-tab active" data-tab="basic" style="flex:1;background:transparent;border:none;border-bottom:2px solid #38bdf8;color:#38bdf8;padding:8px;font-size:11px;font-weight:700;cursor:pointer;">CONTENT</button>
        <button type="button" class="wppoppop-insp-tab" data-tab="style" style="flex:1;background:transparent;border:none;border-bottom:2px solid transparent;color:#94a3b8;padding:8px;font-size:11px;font-weight:700;cursor:pointer;">STYLE</button>
        <button type="button" class="wppoppop-insp-tab" data-tab="logic" style="flex:1;background:transparent;border:none;border-bottom:2px solid transparent;color:#94a3b8;padding:8px;font-size:11px;font-weight:700;cursor:pointer;">LOGIC</button>
        <button type="button" class="wppoppop-insp-tab wppoppop-form-family-tab" id="insp-tab-nav-data" data-tab="data" style="display:none;flex:1;background:transparent;border:none;border-bottom:2px solid transparent;color:#94a3b8;padding:8px;font-size:11px;font-weight:700;cursor:pointer;">DATA</button>
        <button type="button" class="wppoppop-insp-tab wppoppop-form-family-tab" id="insp-tab-nav-advanced" data-tab="advanced" style="display:none;flex:1;background:transparent;border:none;border-bottom:2px solid transparent;color:#94a3b8;padding:8px;font-size:11px;font-weight:700;cursor:pointer;">ADVANCED</button>
        </div>

    <!-- Inspector Body -->
    <div style="flex:1;overflow-y:auto;padding:16px;display:flex;flex-direction:column;gap:16px;">
        
        <!-- =================== TAB 1: BASIC CONTENT =================== -->
        <div id="insp-tab-basic" class="wppoppop-tab-pane">

            <div class="wppoppop-inspector-section" style="margin-bottom:12px;">
                <label style="display:block;font-size:11px;font-weight:700;color:#cbd5e1;margin-bottom:4px;">LAYER NAME</label>
                <input type="text" id="prop-layer-name" class="wppoppop-input" style="width:100%;height:30px;padding:4px 8px;font-size:12px;border:1px solid #334155;border-radius:4px;background:#1e293b;color:#f1f5f9;">
            </div>
            <div style="display:flex;flex-direction:column;gap:12px;">
                

                <!-- DYNAMIC ELEMENT-SPECIFIC SETTINGS -->
                <div id="wppoppop-element-specific-settings">
                <?php
                foreach (array('elem-01-shape.php', 'elem-02-image.php', 'elem-03-video.php', 'elem-04-label.php', 'elem-05-html.php', 'elem-06-close.php', 'elem-07-icon.php', 'elem-08-progress.php', 'elem-09-button.php', 'elem-10-text.php', 'elem-11-email.php', 'elem-12-number.php', 'elem-13-phone.php', 'elem-14-textarea.php', 'elem-15-select.php', 'elem-16-checkbox.php', 'elem-17-radio.php', 'elem-18-multiselect.php', 'elem-19-imageselect.php', 'elem-20-tile.php', 'elem-21-date.php', 'elem-22-time.php', 'elem-23-upload.php', 'elem-24-password.php', 'elem-25-rating.php', 'elem-26-submit.php') as $wppoppop_mod) {
                    $wppoppop_mod_file = __DIR__ . '/elements/' . $wppoppop_mod;
                    if (file_exists($wppoppop_mod_file)) {
                        include $wppoppop_mod_file;
                    }
                }
                ?>

                <!-- TEXT / RICH CONTENT -->
                <div class="element-panel" id="panel-elem-paragraph-legacy" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">TEXT CONTENT</label>
                    <textarea id="prop-text-content" rows="4" class="widefat" placeholder="Enter text content..." style="font-size:12px;"></textarea>
                </div>

                <!-- IMAGE -->
                <div class="element-panel" id="panel-elem-image-legacy" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">IMAGE SOURCE URL</label>
                    <input type="text" id="prop-image-url" class="widefat" placeholder="https://..." style="font-size:12px;margin-bottom:8px;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">IMAGE ALT TEXT</label>
                    <input type="text" id="prop-image-alt" class="widefat" placeholder="Image description for accessibility" style="font-size:12px;margin-bottom:8px;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">OBJECT FIT</label>
                    <select id="prop-image-fit" class="widefat" style="font-size:12px;">
                        <option value="cover" selected>Cover (Clip to container)</option>
                        <option value="contain">Contain (Full aspect)</option>
                        <option value="fill">Fill (Stretch)</option>
                        <option value="none">Original Scale</option>
                    </select>
                </div>

                <!-- EMAIL FIELD -->
                <div class="element-panel" id="panel-elem-email-legacy" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">PLACEHOLDER TEXT</label>
                    <input type="text" id="prop-email-placeholder" class="widefat" placeholder="e.g. name@example.com" style="font-size:12px;margin-bottom:8px;">
                    <label style="display:block;font-size:10px;font-weight:700;color:#64748b;margin:8px 0 4px;text-transform:uppercase;">AUTOCOMPLETE ATTRIBUTE</label>
                    <select id="prop-email-autocomplete" class="widefat wppoppop-input" style="font-size:12px;margin-bottom:8px;">
                        <option value="email">Email (email)</option>
                        <option value="off">Disabled (off)</option>
                        <option value="on">Enabled (on)</option>
                        <option value="name">Full name (name)</option>
                        <option value="given-name">First name (given-name)</option>
                        <option value="family-name">Last name (family-name)</option>
                        <option value="tel">Telephone (tel)</option>
                        <option value="username">Username (username)</option>
                    </select>
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">FIELD NAME / TOKEN</label>
                    <input type="text" id="prop-email-name" class="widefat" value="email" style="font-size:12px;margin-bottom:8px;">
                    <label style="font-size:12px;color:#334155;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                        <input type="checkbox" id="prop-email-required" value="1" checked> Required Field
                    </label>
                </div>

                <!-- NUMBER FIELD -->
                <div class="element-panel" id="panel-elem-number-legacy" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">PLACEHOLDER TEXT</label>
                    <input type="text" id="prop-num-placeholder" class="widefat" placeholder="e.g. 10" style="font-size:12px;margin-bottom:8px;">
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:6px;margin-bottom:8px;">
                        <div>
                            <label style="font-size:10px;color:#64748b;">MIN</label>
                            <input type="number" id="prop-num-min" value="0" class="widefat" style="font-size:11px;">
                        </div>
                        <div>
                            <label style="font-size:10px;color:#64748b;">MAX</label>
                            <input type="number" id="prop-num-max" value="100" class="widefat" style="font-size:11px;">
                        </div>
                        <div>
                            <label style="font-size:10px;color:#64748b;">STEP</label>
                            <input type="number" id="prop-num-step" value="1" class="widefat" style="font-size:11px;">
                        </div>
                    </div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">FIELD NAME / TOKEN</label>
                    <input type="text" id="prop-num-name" class="widefat" value="number_field" style="font-size:12px;margin-bottom:8px;">
                    <label style="font-size:12px;color:#334155;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                        <input type="checkbox" id="prop-num-required" value="1"> Required Field
                    </label>
                </div>

                <!-- SELECT / DROPDOWN -->
                <div class="element-panel" id="panel-elem-select-legacy" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">OPTIONS (ONE PER LINE)</label>
                    <textarea id="prop-select-options" rows="4" class="widefat" placeholder="Option 1&#10;Option 2&#10;Option 3" style="font-size:12px;margin-bottom:8px;"></textarea>
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">FIELD NAME / TOKEN</label>
                    <input type="text" id="prop-select-name" class="widefat" value="select_field" style="font-size:12px;margin-bottom:8px;">
                    <label style="font-size:12px;color:#334155;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                        <input type="checkbox" id="prop-select-required" value="1"> Required Field
                    </label>
                </div>

                <!-- DROPDOWN ALIAS PANEL -->
                <div class="element-panel" id="panel-elem-dropdown" style="display:none;"></div>

                <!-- RADIOS -->
                <div class="element-panel" id="panel-elem-radios-legacy" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">RADIO OPTIONS (ONE PER LINE)</label>
                    <textarea id="prop-radio-options" rows="4" class="widefat" placeholder="Option A&#10;Option B&#10;Option C" style="font-size:12px;margin-bottom:8px;"></textarea>
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">FIELD NAME / TOKEN</label>
                    <input type="text" id="prop-radio-name" class="widefat" value="radio_group" style="font-size:12px;">
                </div>

                <!-- RADIO ALIAS PANEL -->
                <div class="element-panel" id="panel-elem-radio-legacy" style="display:none;"></div>

                <!-- CHECKBOXES -->
                <div class="element-panel" id="panel-elem-checkboxes-legacy" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">CHECKBOX LABEL</label>
                    <input type="text" id="prop-cb-label" class="widefat" value="I agree to terms" style="font-size:12px;margin-bottom:8px;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">FIELD NAME / TOKEN</label>
                    <input type="text" id="prop-cb-name" class="widefat" value="terms_agreement" style="font-size:12px;margin-bottom:8px;">
                    <div style="display:flex;gap:12px;align-items:center;">
                        <label style="font-size:12px;color:#334155;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                            <input type="checkbox" id="prop-cb-checked" value="1"> Default Checked
                        </label>
                        <label style="font-size:12px;color:#334155;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                            <input type="checkbox" id="prop-cb-required" value="1"> Required
                        </label>
                    </div>
                </div>

                <!-- CHECKBOX ALIAS PANEL -->
                <div class="element-panel" id="panel-elem-checkbox-legacy" style="display:none;"></div>

                <!-- DATE PICKER -->
                <div class="element-panel" id="panel-elem-date-legacy" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">PLACEHOLDER TEXT</label>
                    <input type="text" id="prop-date-placeholder" class="widefat" placeholder="Select date..." style="font-size:12px;margin-bottom:8px;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">FIELD NAME / TOKEN</label>
                    <input type="text" id="prop-date-name" class="widefat" value="date_field" style="font-size:12px;margin-bottom:8px;">
                    <label style="font-size:12px;color:#334155;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                        <input type="checkbox" id="prop-date-required" value="1"> Required Field
                    </label>
                </div>

                <!-- PROGRESS BAR -->
                <div class="element-panel" id="panel-elem-progress-legacy" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">PROGRESS VALUE: <span id="prop-progress-val-num">50</span>%</label>
                    <input type="range" id="prop-progress-val" min="0" max="100" value="50" style="width:100%;margin-bottom:8px;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">BAR FILL COLOR</label>
                    <input type="color" id="prop-progress-bar-color" value="#3b82f6" class="widefat" style="height:32px;">
                </div>

                <!-- FILE UPLOAD -->
                <div class="element-panel" id="panel-elem-file-legacy" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">ALLOWED EXTENSIONS</label>
                    <input type="text" id="prop-file-extensions" class="widefat" placeholder="jpg, png, pdf, docx" value="jpg, png, pdf" style="font-size:12px;margin-bottom:8px;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">MAX FILE SIZE (MB)</label>
                    <input type="number" id="prop-file-max-size" value="5" min="1" max="50" class="widefat" style="font-size:12px;margin-bottom:8px;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">FIELD NAME / TOKEN</label>
                    <input type="text" id="prop-file-name" class="widefat" value="attachment" style="font-size:12px;margin-bottom:8px;">
                    <label style="font-size:12px;color:#334155;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                        <input type="checkbox" id="prop-file-required" value="1"> Required Field
                    </label>
                </div>

                <!-- STEP BUTTON -->
                <div class="element-panel" id="panel-elem-step_btn-legacy" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">BUTTON LABEL</label>
                    <input type="text" id="prop-step-label" value="Next Step &rarr;" class="widefat" style="font-size:12px;margin-bottom:8px;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">TARGET CANVAS NUMBER</label>
                    <input type="number" id="prop-step-canvas" value="2" min="1" max="10" class="widefat" style="font-size:12px;">
                </div>

                <!-- STEP ALIAS PANEL -->
                <div class="element-panel" id="panel-elem-step" style="display:none;"></div>

                <!-- PAY BUTTON -->
                <div class="element-panel" id="panel-elem-pay" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">BUTTON LABEL</label>
                    <input type="text" id="prop-pay-label" value="Pay Now" class="widefat" style="font-size:12px;margin-bottom:8px;">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;margin-bottom:8px;">
                        <div>
                            <label style="font-size:10px;color:#64748b;">AMOUNT</label>
                            <input type="number" id="prop-pay-amount" value="19.99" step="0.01" class="widefat" style="font-size:11px;">
                        </div>
                        <div>
                            <label style="font-size:10px;color:#64748b;">CURRENCY</label>
                            <select id="prop-pay-currency" class="widefat" style="font-size:11px;">
                                <option value="USD" selected>USD ($)</option>
                                <option value="EUR">EUR (€)</option>
                                <option value="GBP">GBP (£)</option>
                            </select>
                        </div>
                    </div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">PAYMENT GATEWAY</label>
                    <select id="prop-pay-gateway" class="widefat" style="font-size:12px;">
                        <option value="stripe" selected>Stripe</option>
                        <option value="paypal">PayPal</option>
                    </select>
                </div>

                <!-- CUSTOM HTML -->
                                <!-- CUSTOM HTML -->
                <div class="element-panel" id="panel-elem-html-legacy" style="display:none;">
                    <div style="font-size:11px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:8px;border-bottom:1px solid #334155;padding-bottom:4px;">CUSTOM HTML / EMBED CODE</div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#94a3b8;margin-bottom:4px;">EMBED CODE / MARKUP</label>
                    <textarea id="prop-html-code" rows="7" class="widefat wppoppop-input" placeholder="<div>Custom HTML / Embed here</div>" style="width:100%;background:#0f172a !important;border:1px solid #334155 !important;color:#f8fafc !important;font-family:monospace;font-size:11px;padding:8px;border-radius:4px;box-sizing:border-box;resize:vertical;"></textarea>
                    <span style="display:block;font-size:10px;color:#64748b;margin-top:4px;">Live renders HTML, iframes, SVG badges, and custom styled embeds on the canvas.</span>
                </div></textarea>
                </div>

                <!-- VIDEO -->
                                <!-- DIVIDER ELEMENT -->
                <div class="element-panel" id="panel-elem-divider" style="display:none;">
                    <div style="font-size:11px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:8px;border-bottom:1px solid #334155;padding-bottom:4px;">DIVIDER SETTINGS</div>
                    
                    <div style="margin-bottom:10px;">
                        <label for="prop-divider-style" style="display:block;font-size:11px;font-weight:700;color:#94a3b8;margin-bottom:4px;">DIVIDER TYPE</label>
                        <select id="prop-divider-style" class="widefat wppoppop-input" style="width:100%;height:32px;background:#0f172a !important;border:1px solid #334155 !important;color:#f8fafc !important;font-size:12px;border-radius:4px;padding:4px 8px;">
                            <option value="solid" selected>Solid Line</option>
                            <option value="dashed">Dashed Line</option>
                            <option value="dotted">Dotted Line</option>
                            <option value="double">Double Line</option>
                            <option value="groove">Groove Line</option>
                            <option value="ridge">Ridge Line</option>
                        </select>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px;">
                        <div>
                            <label for="prop-divider-weight" style="display:block;font-size:11px;font-weight:700;color:#94a3b8;margin-bottom:4px;">THICKNESS (PX)</label>
                            <input type="number" id="prop-divider-weight" class="widefat wppoppop-input" min="1" max="30" value="2" style="width:100%;height:32px;background:#0f172a !important;border:1px solid #334155 !important;color:#f8fafc !important;font-size:12px;border-radius:4px;padding:4px 8px;">
                        </div>
                        <div>
                            <label for="prop-divider-color" style="display:block;font-size:11px;font-weight:700;color:#94a3b8;margin-bottom:4px;">LINE COLOR</label>
                            <input type="color" id="prop-divider-color" class="widefat wppoppop-input" value="#94a3b8" style="width:100%;height:32px;background:#0f172a !important;border:1px solid #334155 !important;border-radius:4px;padding:2px;cursor:pointer;">
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px;">
                        <div>
                            <label for="prop-divider-width-pct" style="display:block;font-size:11px;font-weight:700;color:#94a3b8;margin-bottom:4px;">WIDTH (%)</label>
                            <input type="number" id="prop-divider-width-pct" class="widefat wppoppop-input" min="10" max="100" step="5" value="100" style="width:100%;height:32px;background:#0f172a !important;border:1px solid #334155 !important;color:#f8fafc !important;font-size:12px;border-radius:4px;padding:4px 8px;">
                        </div>
                        <div>
                            <label for="prop-divider-align" style="display:block;font-size:11px;font-weight:700;color:#94a3b8;margin-bottom:4px;">ALIGNMENT</label>
                            <select id="prop-divider-align" class="widefat wppoppop-input" style="width:100%;height:32px;background:#0f172a !important;border:1px solid #334155 !important;color:#f8fafc !important;font-size:12px;border-radius:4px;padding:4px 8px;">
                                <option value="center" selected>Center</option>
                                <option value="left">Left</option>
                                <option value="right">Right</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- ICON ELEMENT -->
                <div class="element-panel" id="panel-elem-icon-legacy" style="display:none;">
                    <div style="font-size:11px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:8px;border-bottom:1px solid #334155;padding-bottom:4px;">FONT AWESOME ICON</div>
                    
                    <!-- Live Icon Preview Card -->
                    <div style="display:flex;align-items:center;justify-content:center;background:#0f172a;border:1px solid #334155;border-radius:6px;padding:16px;margin-bottom:12px;">
                        <i id="prop-icon-live-preview" class="fas fa-star" style="font-size:36px;color:#38bdf8;"></i>
                    </div>

                    <div style="margin-bottom:10px;">
                        <label for="prop-icon-preset" style="display:block;font-size:11px;font-weight:700;color:#94a3b8;margin-bottom:4px;">CHOOSE ICON</label>
                        <select id="prop-icon-preset" class="widefat wppoppop-input" style="width:100%;height:32px;background:#0f172a !important;border:1px solid #334155 !important;color:#f8fafc !important;font-size:12px;border-radius:4px;padding:4px 8px;">
                            <optgroup label="Popular">
                                <option value="fas fa-star" selected>Star (★)</option>
                                <option value="fas fa-heart">Heart (♥)</option>
                                <option value="fas fa-check">Checkmark (✔)</option>
                                <option value="fas fa-check-circle">Check Circle</option>
                                <option value="fas fa-times">Close / Times (✖)</option>
                                <option value="fas fa-fire">Fire / Hot</option>
                                <option value="fas fa-bolt">Lightning Bolt</option>
                                <option value="fas fa-gift">Gift Box</option>
                                <option value="fas fa-bell">Notification Bell</option>
                                <option value="fas fa-thumbs-up">Thumbs Up</option>
                            </optgroup>
                            <optgroup label="Commerce & Badges">
                                <option value="fas fa-shopping-cart">Shopping Cart</option>
                                <option value="fas fa-tag">Discount Tag</option>
                                <option value="fas fa-percent">Percent (%)</option>
                                <option value="fas fa-dollar-sign">Dollar Sign ($)</option>
                                <option value="fas fa-crown">VIP Crown</option>
                                <option value="fas fa-award">Award Badge</option>
                                <option value="fas fa-gem">Diamond Gem</option>
                                <option value="fas fa-shield-alt">Security Shield</option>
                                <option value="fas fa-lock">Padlock</option>
                            </optgroup>
                            <optgroup label="Communication & Media">
                                <option value="fas fa-envelope">Email Envelope</option>
                                <option value="fas fa-phone">Phone Call</option>
                                <option value="fas fa-comment">Chat Comment</option>
                                <option value="fas fa-paper-plane">Paper Plane</option>
                                <option value="fas fa-play">Play Video</option>
                                <option value="fas fa-bullhorn">Announcement Bullhorn</option>
                            </optgroup>
                            <optgroup label="Arrows & Navigation">
                                <option value="fas fa-arrow-right">Arrow Right (&rarr;)</option>
                                <option value="fas fa-arrow-down">Arrow Down (&darr;)</option>
                                <option value="fas fa-chevron-right">Chevron Right</option>
                                <option value="fas fa-rocket">Rocket Launch</option>
                            </optgroup>
                            <optgroup label="Custom">
                                <option value="custom">-- Custom Icon Class --</option>
                            </optgroup>
                        </select>
                    </div>

                    <div style="margin-bottom:10px;">
                        <label for="prop-icon-class" style="display:block;font-size:11px;font-weight:700;color:#94a3b8;margin-bottom:4px;">FONT AWESOME CLASS</label>
                        <input type="text" id="prop-icon-class" class="widefat wppoppop-input" placeholder="e.g. fas fa-star" value="fas fa-star" style="width:100%;height:32px;background:#0f172a !important;border:1px solid #334155 !important;color:#f8fafc !important;font-size:12px;font-family:monospace;border-radius:4px;padding:4px 8px;">
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px;">
                        <div>
                            <label for="prop-icon-size" style="display:block;font-size:11px;font-weight:700;color:#94a3b8;margin-bottom:4px;">ICON SIZE (PX)</label>
                            <input type="number" id="prop-icon-size" class="widefat wppoppop-input" min="12" max="180" value="36" style="width:100%;height:32px;background:#0f172a !important;border:1px solid #334155 !important;color:#f8fafc !important;font-size:12px;border-radius:4px;padding:4px 8px;">
                        </div>
                        <div>
                            <label for="prop-icon-color" style="display:block;font-size:11px;font-weight:700;color:#94a3b8;margin-bottom:4px;">ICON COLOR</label>
                            <input type="color" id="prop-icon-color" class="widefat wppoppop-input" value="#38bdf8" style="width:100%;height:32px;background:#0f172a !important;border:1px solid #334155 !important;border-radius:4px;padding:2px;cursor:pointer;">
                        </div>
                    </div>
                </div>

<div class="element-panel" id="panel-elem-video-legacy" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">VIDEO URL (YOUTUBE / VIMEO / MP4)</label>
                    <input type="url" id="prop-video-url" class="widefat" placeholder="https://www.youtube.com/watch?v=..." style="font-size:12px;margin-bottom:8px;">
                    <div style="display:flex;gap:12px;align-items:center;margin-top:6px;">
                        <label style="font-size:12px;color:#334155;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                            <input type="checkbox" id="prop-video-autoplay" value="1"> Autoplay
                        </label>
                        <label style="font-size:12px;color:#334155;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                            <input type="checkbox" id="prop-video-controls" value="1" checked> Controls
                        </label>
                        <label style="font-size:12px;color:#334155;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                            <input type="checkbox" id="prop-video-loop" value="1"> Loop
                        </label>
                    </div>
                </div>
                <!-- LINK / ACTION BUTTON -->
                <div class="element-panel" id="panel-elem-link-legacy" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">BUTTON / LINK TEXT</label>
                    <input type="text" id="prop-link-label" value="Learn More &rarr;" class="widefat" style="font-size:12px;margin-bottom:8px;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">LINK DESTINATION URL</label>
                    <input type="url" id="prop-link-url" placeholder="https://..." class="widefat" style="font-size:12px;margin-bottom:8px;">
                    <label style="font-size:12px;color:#334155;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                        <input type="checkbox" id="prop-link-target-blank" value="1" checked> Open in New Tab
                    </label>
                </div>
                <!-- CLOSE BUTTON -->
                <div class="element-panel" id="panel-elem-close-legacy" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">CLOSE BUTTON ICON / LABEL</label>
                    <input type="text" id="prop-close-label" value="&times;" class="widefat" style="font-size:12px;margin-bottom:8px;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">CLOSE ACTION BEHAVIOR</label>
                    <select id="prop-close-action" class="widefat" style="font-size:12px;">
                        <option value="close">Close popup immediately</option>
                        <option value="close_period">Close and hide for current session</option>
                        <option value="close_forever">Close and hide permanently</option>
                    </select>
                </div>
                <!-- WHEEL -->
                <div class="element-panel" id="panel-elem-wheel" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">PRIZE SLICES (COMMA SEPARATED)</label>
                    <textarea id="prop-wheel-slices" rows="4" class="widefat" placeholder="10% OFF, FREE SHIPPING, 25% OFF, JACKPOT" style="font-size:12px;margin-bottom:8px;"></textarea>
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">SPIN BUTTON LABEL</label>
                    <input type="text" id="prop-wheel-btn-text" value="SPIN TO WIN!" class="widefat" style="font-size:12px;margin-bottom:8px;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">WIN MESSAGE</label>
                    <input type="text" id="prop-wheel-win-msg" value="Congratulations! You won {prize}!" class="widefat" style="font-size:12px;">
                </div>
                <!-- SCRATCH -->
                <div class="element-panel" id="panel-elem-scratch" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">SECRET WINNING / REVEAL TEXT</label>
                    <input type="text" id="prop-scratch-prize" value="YOU WON 25% OFF! USE CODE: WIN25" class="widefat" style="font-size:12px;margin-bottom:8px;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">FOIL COVER COLOR</label>
                    <input type="color" id="prop-scratch-foil" value="#94a3b8" class="widefat" style="height:32px;margin-bottom:8px;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">PERCENT UNCOVERED TO REVEAL (10-90%)</label>
                    <input type="number" id="prop-scratch-pct" min="10" max="90" value="45" class="widefat" style="font-size:12px;">
                </div>
                <!-- COUNTDOWN -->
                <div class="element-panel" id="panel-elem-countdown" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">DURATION (SECONDS)</label>
                    <input type="number" id="prop-countdown-seconds" value="900" class="widefat" placeholder="e.g. 900 for 15 minutes" style="font-size:12px;margin-bottom:8px;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">ACTION ON EXPIRE</label>
                    <select id="prop-countdown-expire" class="widefat" style="font-size:12px;">
                        <option value="none">Stay at 00:00</option>
                        <option value="close">Close Popup</option>
                        <option value="redirect">Redirect URL</option>
                    </select>
                </div>
                <!-- RATING -->
                <div class="element-panel" id="panel-elem-rating-legacy" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">DEFAULT STAR RATING (1-5)</label>
                    <input type="number" id="prop-rating-val" min="1" max="5" value="5" class="widefat" style="font-size:12px;margin-bottom:8px;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">STAR COLOR</label>
                    <input type="color" id="prop-rating-color" value="#f59e0b" class="widefat" style="height:32px;margin-bottom:8px;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">FIELD NAME / TOKEN</label>
                    <input type="text" id="prop-rating-fieldname" value="rating" class="widefat" style="font-size:12px;">
                </div>
                <!-- SIGNATURE -->
                <div class="element-panel" id="panel-elem-signature" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">PEN INK COLOR</label>
                    <input type="color" id="prop-sig-color" value="#0f172a" class="widefat" style="height:32px;margin-bottom:8px;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">CLEAR BUTTON LABEL</label>
                    <input type="text" id="prop-sig-clear-label" value="Clear Signature" class="widefat" style="font-size:12px;margin-bottom:8px;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">FIELD NAME</label>
                    <input type="text" id="prop-sig-fieldname" value="digital_signature" class="widefat" style="font-size:12px;">
                </div>
                <!-- SLIDER -->
                <div class="element-panel" id="panel-elem-slider" style="display:none;">
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:6px;margin-bottom:8px;">
                        <div>
                            <label style="font-size:10px;color:#64748b;">MIN</label>
                            <input type="number" id="prop-slider-min" value="0" class="widefat" style="font-size:11px;">
                        </div>
                        <div>
                            <label style="font-size:10px;color:#64748b;">MAX</label>
                            <input type="number" id="prop-slider-max" value="100" class="widefat" style="font-size:11px;">
                        </div>
                        <div>
                            <label style="font-size:10px;color:#64748b;">DEFAULT</label>
                            <input type="number" id="prop-slider-val" value="50" class="widefat" style="font-size:11px;">
                        </div>
                    </div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">FIELD NAME / TOKEN</label>
                    <input type="text" id="prop-slider-fieldname" value="range_val" class="widefat" style="font-size:12px;">
                </div>
                <!-- SUBMIT BUTTON -->
                <div class="element-panel" id="panel-elem-submit-legacy" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">BUTTON LABEL</label>
                    <input type="text" id="prop-submit-label" value="Submit Form" class="widefat" style="font-size:12px;margin-bottom:8px;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">ACTION ON SUBMIT</label>
                    <select id="prop-submit-action" class="widefat" style="font-size:12px;">
                        <option value="default">Save Lead & Close</option>
                        <option value="next_canvas">Save Lead & Advance to Next Canvas</option>
                        <option value="redirect">Save Lead & Redirect URL</option>
                    </select>
                </div>
                    
                    <!-- Element #1: Title -->
                            <!-- GreenPop Parity: Title Element Dedicated Panel -->
        <div class="element-panel" id="panel-elem-title-legacy" style="display:none;">
            <div class="insp-group" style="margin-bottom:12px;">
                <label class="insp-label" for="prop-title-text" style="font-weight:600;display:block;margin-bottom:4px;">Headline Text</label>
                <textarea id="prop-title-text" class="wppoppop-input" rows="3" placeholder="Enter headline text..." style="width:100%;resize:vertical;font-size:13px;line-height:1.4;box-sizing:border-box;"></textarea>
            </div>
            <div class="insp-group" style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:12px;">
                <div>
                    <label class="insp-label" for="prop-title-tag" style="font-weight:600;display:block;margin-bottom:4px;">Heading Tag</label>
                    <select id="prop-title-tag" class="wppoppop-select" style="width:100%;height:30px;font-size:12px;">
                        <option value="h1">H1 - Page Main</option>
                        <option value="h2" selected>H2 - Section</option>
                        <option value="h3">H3 - Subheading</option>
                        <option value="h4">H4 - Small Heading</option>
                        <option value="h5">H5 - Minor Heading</option>
                        <option value="h6">H6 - Caption Heading</option>
                        <option value="div">DIV - Generic</option>
                    </select>
                </div>
                <div>
                    <label class="insp-label" for="prop-title-transform" style="font-weight:600;display:block;margin-bottom:4px;">Text Transform</label>
                    <select id="prop-title-transform" class="wppoppop-select" style="width:100%;height:30px;font-size:12px;">
                        <option value="none">None (Default)</option>
                        <option value="uppercase">UPPERCASE</option>
                        <option value="lowercase">lowercase</option>
                        <option value="capitalize">Capitalize</option>
                    </select>
                </div>
            </div>
        </div>

                    <!-- Element #2: Paragraph -->
                    <div id="panel-elem-paragraph-legacy" class="element-panel wppoppop-elem-panel" style="display:none;">
                        <div class="wppoppop-prop-group">
                            <label for="prop-paragraph-text" style="font-size:11px;font-weight:700;color:#94a3b8;display:block;margin-bottom:4px;">PARAGRAPH BODY</label>
                            <textarea id="prop-paragraph-text" class="wppoppop-input" rows="4" style="width:100%;background:#0f172a;border:1px solid #334155;color:#f8fafc;padding:6px 10px;border-radius:4px;font-size:12px;box-sizing:border-box;resize:vertical;"></textarea>
                        </div>
                    </div>

                    <!-- Element #3: Text Field -->
                    <div id="panel-elem-textfield-legacy" class="element-panel wppoppop-elem-panel" style="display:none;">
                        <div class="wppoppop-prop-group">
                            <label for="prop-textfield-placeholder" style="font-size:11px;font-weight:700;color:#94a3b8;display:block;margin-bottom:4px;">PLACEHOLDER TEXT</label>
                            <input type="text" id="prop-textfield-placeholder" class="wppoppop-input" style="width:100%;background:#0f172a;border:1px solid #334155;color:#f8fafc;padding:6px 10px;border-radius:4px;font-size:12px;box-sizing:border-box;">
                        </div>
                    </div>

                    <!-- Element #4: Vector Shape Settings -->
                    <div id="panel-elem-shape-legacy" class="element-panel wppoppop-elem-panel" style="display:none;">
                        <div style="font-size:11px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin:8px 0 6px 0;border-bottom:1px solid #334155;padding-bottom:4px;">SHAPE GEOMETRY &amp; OUTLINE</div>
                        
                        <div class="wppoppop-prop-group" style="margin-bottom:10px;">
                            <label for="prop-shape-preset" style="font-size:11px;font-weight:700;color:#94a3b8;display:block;margin-bottom:4px;">PRESET SHAPE</label>
                            <select id="prop-shape-preset" class="wppoppop-input" style="width:100%;background:#0f172a;border:1px solid #334155;color:#f8fafc;padding:6px 10px;border-radius:4px;font-size:12px;box-sizing:border-box;">
                                <option value="circle">Circle</option>
                                <option value="square">Square</option>
                                <option value="rounded_rect">Rounded Rectangle</option>
                                <option value="star">Star (5-Point)</option>
                                <option value="triangle">Triangle</option>
                                <option value="diamond">Diamond</option>
                                <option value="heart">Heart</option>
                                <option value="hexagon">Hexagon</option>
                                <option value="octagon">Octagon</option>
                                <option value="shield">Shield</option>
                                <option value="cross">Cross</option>
                            </select>
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px;">
                            <div class="wppoppop-prop-group">
                                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                                    <label for="prop-shape-fill" style="font-size:11px;font-weight:700;color:#94a3b8;margin:0;">FILL COLOR</label>
                                    <button type="button" id="prop-shape-fill-transparent-btn" class="wppoppop-checker-btn" title="Set Transparent Fill" aria-label="Transparent Fill"></button>
                                </div>
                                <input type="color" id="prop-shape-fill" class="wppoppop-input" value="#3b82f6" style="width:100%;height:32px;padding:2px;background:#0f172a;border:1px solid #334155;border-radius:4px;cursor:pointer;box-sizing:border-box;">
                            </div>
                            <div class="wppoppop-prop-group">
                                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                                    <label for="prop-shape-stroke" style="font-size:11px;font-weight:700;color:#94a3b8;margin:0;">STROKE COLOR</label>
                                    <button type="button" id="prop-shape-stroke-transparent-btn" class="wppoppop-checker-btn" title="Set Transparent Stroke" aria-label="Transparent Stroke"></button>
                                </div>
                                <input type="color" id="prop-shape-stroke" class="wppoppop-input" value="#1d4ed8" style="width:100%;height:32px;padding:2px;background:#0f172a;border:1px solid #334155;border-radius:4px;cursor:pointer;box-sizing:border-box;">
                            </div>
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px;">
                            <div class="wppoppop-prop-group">
                                <label for="prop-shape-stroke-width" style="font-size:11px;font-weight:700;color:#94a3b8;display:block;margin-bottom:4px;">STROKE (PX)</label>
                                <input type="number" id="prop-shape-stroke-width" class="wppoppop-input" min="0" max="30" value="2" style="width:100%;background:#0f172a;border:1px solid #334155;color:#f8fafc;padding:6px 10px;border-radius:4px;font-size:12px;box-sizing:border-box;">
                            </div>
                            <div class="wppoppop-prop-group">
                                <label for="prop-shape-rotate" style="font-size:11px;font-weight:700;color:#94a3b8;display:block;margin-bottom:4px;">ROTATION (°)</label>
                                <input type="number" id="prop-shape-rotate" class="wppoppop-input" min="0" max="360" value="0" style="width:100%;background:#0f172a;border:1px solid #334155;color:#f8fafc;padding:6px 10px;border-radius:4px;font-size:12px;box-sizing:border-box;">
                            </div>
                        </div>
                    </div>

                    <!-- General Content Fallback -->
                    <div id="panel-elem-general" class="element-panel wppoppop-elem-panel" style="display:none;">
                        <div class="wppoppop-prop-group">
                            <label for="prop-content" style="font-size:11px;font-weight:700;color:#94a3b8;display:block;margin-bottom:4px;">ELEMENT CONTENT / LABEL</label>
                            <textarea id="prop-content" class="wppoppop-input" rows="3" style="width:100%;background:#0f172a;border:1px solid #334155;color:#f8fafc;padding:6px 10px;border-radius:4px;font-size:12px;box-sizing:border-box;resize:vertical;"></textarea>
                        </div>
                    </div>
                </div>
                </div>

                <!-- Position & Dimensions -->
                <div style="font-size:11px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-top:6px;border-bottom:1px solid #334155;padding-bottom:4px;">COORDINATES &amp; BOUNDS</div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                    <div>
                        <label for="prop-left" style="font-size:10px;font-weight:700;color:#94a3b8;">X POS (PX)</label>
                        <input type="number" id="prop-left" class="wppoppop-input" style="width:100%;background:#0f172a;border:1px solid #334155;color:#f8fafc;padding:5px 8px;border-radius:4px;font-size:11px;box-sizing:border-box;">
                    </div>
                    <div>
                        <label for="prop-top" style="font-size:10px;font-weight:700;color:#94a3b8;">Y POS (PX)</label>
                        <input type="number" id="prop-top" class="wppoppop-input" style="width:100%;background:#0f172a;border:1px solid #334155;color:#f8fafc;padding:5px 8px;border-radius:4px;font-size:11px;box-sizing:border-box;">
                    </div>
                    <div>
                        <label for="prop-width" style="font-size:10px;font-weight:700;color:#94a3b8;">WIDTH (PX)</label>
                        <input type="number" id="prop-width" class="wppoppop-input" style="width:100%;background:#0f172a;border:1px solid #334155;color:#f8fafc;padding:5px 8px;border-radius:4px;font-size:11px;box-sizing:border-box;">
                    </div>
                    <div>
                        <label for="prop-height" style="font-size:10px;font-weight:700;color:#94a3b8;">HEIGHT (PX)</label>
                        <input type="number" id="prop-height" class="wppoppop-input" style="width:100%;background:#0f172a;border:1px solid #334155;color:#f8fafc;padding:5px 8px;border-radius:4px;font-size:11px;box-sizing:border-box;">
                    </div>
                </div>
                            <!-- =================== ELEMENT ANIMATION (CONTENT TAB) =================== -->
                <div id="wppoppop-element-anim-wrap" style="margin-top:14px;border-top:1px solid #334155;padding-top:10px;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                        <span style="font-size:11px;font-weight:800;color:#38bdf8;text-transform:uppercase;letter-spacing:0.5px;">ANIMATION</span>
                        <span style="font-size:9px;color:#94a3b8;background:#1e293b;border:1px solid #334155;padding:1px 6px;border-radius:4px;">Animate.css</span>
                    </div>
                    <div>
                        <label for="prop-animation" style="font-size:10px;font-weight:700;color:#94a3b8;display:block;margin-bottom:4px;">APPEARANCE EFFECT</label>
                        <div style="display:flex;gap:8px;align-items:center;">
                            <select id="prop-animation" class="wppoppop-input" style="flex:1;background:#0f172a !important;border:1px solid #334155 !important;color:#f8fafc !important;padding:6px 10px;border-radius:4px;font-size:12px;box-sizing:border-box;">
                                <option value="none">None</option>
                                <option value="fade">Fade In</option>
                                <option value="bounceIn">Bounce In</option>
                                <option value="bounceInLeft">Bounce In Left</option>
                                <option value="bounceInRight">Bounce In Right</option>
                                <option value="bounce">Bounce</option>
                                <option value="tada">Tada</option>
                                <option value="rubberBand">RubberBand</option>
                                <option value="slideDown">Slide Down</option>
                                <option value="slideUp">Slide Up</option>
                                <option value="slideLeft">Slide Left</option>
                                <option value="slideRight">Slide Right</option>
                                <option value="zoomIn">Zoom In</option>
                                <option value="flipIn">Flip In</option>
                                <option value="pulse">Pulse</option>
                                <option value="shake">Shake</option>
                            </select>
                            <button type="button" id="prop-anim-play-btn" style="background:#2563eb;border:none;color:#ffffff;padding:6px 12px;border-radius:4px;font-size:11px;font-weight:700;cursor:pointer;white-space:nowrap;display:flex;align-items:center;gap:4px;" title="Test Element Animation">&#9654; Play</button>
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:8px;">
                        <div>
                            <label for="prop-anim-duration" style="font-size:10px;font-weight:700;color:#94a3b8;display:block;margin-bottom:4px;">DURATION (MS)</label>
                            <input type="number" id="prop-anim-duration" class="wppoppop-input" value="1000" step="50" min="0" style="width:100%;background:#0f172a !important;border:1px solid #334155 !important;color:#f8fafc !important;padding:5px 8px;border-radius:4px;font-size:11px;box-sizing:border-box;">
                        </div>
                        <div>
                            <label for="prop-anim-delay" style="font-size:10px;font-weight:700;color:#94a3b8;display:block;margin-bottom:4px;">START DELAY (MS)</label>
                            <input type="number" id="prop-anim-delay" class="wppoppop-input" value="0" step="50" min="0" style="width:100%;background:#0f172a !important;border:1px solid #334155 !important;color:#f8fafc !important;padding:5px 8px;border-radius:4px;font-size:11px;box-sizing:border-box;">
                        </div>
                    </div>

                    <div style="margin-top:8px;">
                        <label for="prop-anim-disappearance" style="font-size:10px;font-weight:700;color:#94a3b8;display:block;margin-bottom:4px;">DISAPPEARANCE / EXIT</label>
                        <select id="prop-anim-disappearance" class="wppoppop-input" style="width:100%;background:#0f172a !important;border:1px solid #334155 !important;color:#f8fafc !important;padding:6px 10px;border-radius:4px;font-size:12px;box-sizing:border-box;">
                            <option value="none">None</option>
                            <option value="fade">Fade</option>
                            <option value="slideDown">Slide Down</option>
                            <option value="slideUp">Slide Up</option>
                            <option value="slideLeft">Slide Left</option>
                            <option value="slideRight">Slide Right</option>
                            <option value="zoomOut">Zoom Out</option>
                        </select>
                    </div>
                </div>
        </div>

        <!-- =================== TAB 2: STYLE =================== -->
        <div id="insp-tab-style" class="wppoppop-tab-pane wppoppop-insp-content" style="display:none;background:transparent !important;padding:0;margin:0;">
            <!-- Element Opacity & Visibility -->
            <div class="wppoppop-inspector-section" style="background:transparent !important;margin-bottom:14px;">
                <div style="font-size:11px;font-weight:800;color:#38bdf8;text-transform:uppercase;border-bottom:1px solid #334155;padding-bottom:4px;margin-bottom:8px;">OPACITY &amp; VISIBILITY</div>
                <div class="wppoppop-field-group">
                    <label for="prop-opacity" style="font-size:10px;font-weight:700;color:#94a3b8;display:block;margin-bottom:4px;">OPACITY</label>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <input type="range" id="prop-opacity" min="0" max="100" value="100" style="flex:1;cursor:pointer;">
                        <span id="prop-opacity-val" style="min-width:38px;font-size:11px;font-weight:700;color:#38bdf8;background:#0f172a;border:1px solid #334155;padding:2px 6px;border-radius:4px;text-align:center;">100%</span>
                    </div>
                </div>
            </div>

            <!-- Button Hover States (Contextual for Buttons) -->
            <div class="wppoppop-inspector-section" id="prop-btn-hover-section" style="display:none;background:transparent !important;margin-bottom:14px;">
                <div style="font-size:11px;font-weight:800;color:#38bdf8;text-transform:uppercase;border-bottom:1px solid #334155;padding-bottom:4px;margin-bottom:8px;">BUTTON HOVER STATES</div>
                <div style="display:grid;grid-template-columns:repeat(3, 1fr);gap:8px;">
                    <div>
                        <label for="prop-btn-hover-bg" style="font-size:10px;font-weight:700;color:#94a3b8;display:block;margin-bottom:4px;">HOVER BG</label>
                        <input type="color" id="prop-btn-hover-bg" value="#2563eb" style="width:100%;height:32px;padding:2px;background:#0f172a;border:1px solid #334155;border-radius:4px;cursor:pointer;box-sizing:border-box;">
                    </div>
                    <div>
                        <label for="prop-btn-hover-color" style="font-size:10px;font-weight:700;color:#94a3b8;display:block;margin-bottom:4px;">HOVER TEXT</label>
                        <input type="color" id="prop-btn-hover-color" value="#ffffff" style="width:100%;height:32px;padding:2px;background:#0f172a;border:1px solid #334155;border-radius:4px;cursor:pointer;box-sizing:border-box;">
                    </div>
                    <div>
                        <label for="prop-btn-hover-border" style="font-size:10px;font-weight:700;color:#94a3b8;display:block;margin-bottom:4px;">HOVER BORDER</label>
                        <input type="color" id="prop-btn-hover-border" value="#1d4ed8" style="width:100%;height:32px;padding:2px;background:#0f172a;border:1px solid #334155;border-radius:4px;cursor:pointer;box-sizing:border-box;">
                    </div>
                </div>
            </div>

            <!-- Typography & Spacing -->
            <div class="wppoppop-inspector-section" id="insp-section-typography" style="background:transparent !important;margin-bottom:14px;">
                <div style="font-size:11px;font-weight:800;color:#38bdf8;text-transform:uppercase;border-bottom:1px solid #334155;padding-bottom:4px;margin-bottom:8px;">TYPOGRAPHY &amp; SPACING</div>
                
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:8px;">
                    <div>
                        <label for="prop-font-size" style="font-size:10px;font-weight:700;color:#94a3b8;display:block;margin-bottom:4px;">SIZE (PX)</label>
                        <input type="number" id="prop-font-size" value="14" style="width:100%;background:#0f172a !important;border:1px solid #334155 !important;color:#f8fafc !important;padding:5px 8px;border-radius:4px;font-size:11px;box-sizing:border-box;">
                    </div>
                    <div>
                        <label for="prop-font-weight" style="font-size:10px;font-weight:700;color:#94a3b8;display:block;margin-bottom:4px;">WEIGHT</label>
                        <select id="prop-font-weight" style="width:100%;background:#0f172a !important;border:1px solid #334155 !important;color:#f8fafc !important;padding:5px 8px;border-radius:4px;font-size:11px;box-sizing:border-box;">
                            <option value="400">Regular (400)</option>
                            <option value="600">Semi-Bold (600)</option>
                            <option value="700">Bold (700)</option>
                            <option value="800">Extra-Bold (800)</option>
                        </select>
                    </div>
                    <div>
                        <label for="prop-line-height" style="font-size:10px;font-weight:700;color:#94a3b8;display:block;margin-bottom:4px;">LINE HEIGHT</label>
                        <input type="number" id="prop-line-height" step="0.1" value="1.4" style="width:100%;background:#0f172a !important;border:1px solid #334155 !important;color:#f8fafc !important;padding:5px 8px;border-radius:4px;font-size:11px;box-sizing:border-box;">
                    </div>
                    <div>
                        <label for="prop-letter-spacing" style="font-size:10px;font-weight:700;color:#94a3b8;display:block;margin-bottom:4px;">SPACING (PX)</label>
                        <input type="number" id="prop-letter-spacing" step="0.5" value="0" style="width:100%;background:#0f172a !important;border:1px solid #334155 !important;color:#f8fafc !important;padding:5px 8px;border-radius:4px;font-size:11px;box-sizing:border-box;">
                    </div>
                </div>

                <!-- Text Alignment -->
                <div style="margin-bottom:8px;">
                    <label for="prop-text-align" style="font-size:10px;font-weight:700;color:#94a3b8;display:block;margin-bottom:4px;">TEXT ALIGN</label>
                    <select id="prop-text-align" style="width:100%;background:#0f172a !important;border:1px solid #334155 !important;color:#f8fafc !important;padding:6px 8px;border-radius:4px;font-size:11px;box-sizing:border-box;">
                        <option value="left">Left</option>
                        <option value="center">Center</option>
                        <option value="right">Right</option>
                    </select>
                </div>

                <!-- 4-Way Directional Padding -->
                <div id="wppoppop-padding-control-wrap">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                        <label style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin:0;">PADDING (PX)</label>
                        <span style="font-size:9px;color:#64748b;font-weight:600;">TOP · RIGHT · BOTTOM · LEFT</span>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:6px;">
                        <div>
                            <input type="number" id="prop-padding-top" class="wppoppop-pad-field" min="0" max="250" value="0" data-dir="top" style="width:100%;background:#0f172a !important;border:1px solid #334155 !important;color:#f8fafc !important;padding:5px 2px;text-align:center;border-radius:4px;font-size:11px;box-sizing:border-box;" placeholder="0">
                        </div>
                        <div>
                            <input type="number" id="prop-padding-right" class="wppoppop-pad-field" min="0" max="250" value="0" data-dir="right" style="width:100%;background:#0f172a !important;border:1px solid #334155 !important;color:#f8fafc !important;padding:5px 2px;text-align:center;border-radius:4px;font-size:11px;box-sizing:border-box;" placeholder="0">
                        </div>
                        <div>
                            <input type="number" id="prop-padding-bottom" class="wppoppop-pad-field" min="0" max="250" value="0" data-dir="bottom" style="width:100%;background:#0f172a !important;border:1px solid #334155 !important;color:#f8fafc !important;padding:5px 2px;text-align:center;border-radius:4px;font-size:11px;box-sizing:border-box;" placeholder="0">
                        </div>
                        <div>
                            <input type="number" id="prop-padding-left" class="wppoppop-pad-field" min="0" max="250" value="0" data-dir="left" style="width:100%;background:#0f172a !important;border:1px solid #334155 !important;color:#f8fafc !important;padding:5px 2px;text-align:center;border-radius:4px;font-size:11px;box-sizing:border-box;" placeholder="0">
                        </div>
                    </div>
                    <input type="hidden" id="prop-padding" value="0">
                </div>
            </div>

            <!-- Colors Section -->
            <div class="wppoppop-inspector-section" style="background:transparent !important;margin-bottom:14px;">
                <div style="font-size:11px;font-weight:800;color:#38bdf8;text-transform:uppercase;border-bottom:1px solid #334155;padding-bottom:4px;margin-bottom:8px;">COLORS</div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                    <div>
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                            <label for="prop-color" style="font-size:10px;font-weight:700;color:#94a3b8;margin:0;">TEXT COLOR</label>
                            <button type="button" id="prop-text-transparent-btn" class="wppoppop-checker-btn" title="Set Transparent Text" aria-label="Transparent Text" style="width:16px;height:16px;border-radius:2px;border:1px solid #475569;background:repeating-conic-gradient(#cbd5e1 0% 25%, #fff 0% 50%) 50%/8px 8px;cursor:pointer;"></button>
                        </div>
                        <input type="color" id="prop-color" value="#ffffff" style="width:100%;height:32px;padding:2px;background:#0f172a !important;border:1px solid #334155 !important;border-radius:4px;cursor:pointer;box-sizing:border-box;">
                    </div>
                    <div>
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                            <label for="prop-bg-color" style="font-size:10px;font-weight:700;color:#94a3b8;margin:0;">BG COLOR</label>
                            <button type="button" id="prop-bg-transparent-btn" class="wppoppop-checker-btn" title="Set Transparent Background" aria-label="Transparent Background" style="width:16px;height:16px;border-radius:2px;border:1px solid #475569;background:repeating-conic-gradient(#cbd5e1 0% 25%, #fff 0% 50%) 50%/8px 8px;cursor:pointer;"></button>
                        </div>
                        <input type="color" id="prop-bg-color" value="#2563eb" style="width:100%;height:32px;padding:2px;background:#0f172a !important;border:1px solid #334155 !important;border-radius:4px;cursor:pointer;box-sizing:border-box;">
                    </div>
                </div>
            </div>

            <!-- Box Shadow Builder -->
            <div class="wppoppop-inspector-section" id="insp-section-box-shadow" style="background:transparent !important;margin-bottom:14px;">
                <div style="font-size:11px;font-weight:800;color:#38bdf8;text-transform:uppercase;border-bottom:1px solid #334155;padding-bottom:4px;margin-bottom:8px;">BOX SHADOW</div>
                <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:6px;margin-bottom:8px;">
                    <div>
                        <label style="font-size:9px;color:#64748b;font-weight:600;display:block;">X (PX)</label>
                        <input type="number" id="prop-shadow-x" value="0" style="width:100%;background:#0f172a !important;border:1px solid #334155 !important;color:#f8fafc !important;padding:4px 6px;border-radius:4px;font-size:11px;box-sizing:border-box;">
                    </div>
                    <div>
                        <label style="font-size:9px;color:#64748b;font-weight:600;display:block;">Y (PX)</label>
                        <input type="number" id="prop-shadow-y" value="4" style="width:100%;background:#0f172a !important;border:1px solid #334155 !important;color:#f8fafc !important;padding:4px 6px;border-radius:4px;font-size:11px;box-sizing:border-box;">
                    </div>
                    <div>
                        <label style="font-size:9px;color:#64748b;font-weight:600;display:block;">BLUR</label>
                        <input type="number" id="prop-shadow-blur" value="10" min="0" style="width:100%;background:#0f172a !important;border:1px solid #334155 !important;color:#f8fafc !important;padding:4px 6px;border-radius:4px;font-size:11px;box-sizing:border-box;">
                    </div>
                    <div>
                        <label style="font-size:9px;color:#64748b;font-weight:600;display:block;">SPREAD</label>
                        <input type="number" id="prop-shadow-spread" value="0" style="width:100%;background:#0f172a !important;border:1px solid #334155 !important;color:#f8fafc !important;padding:4px 6px;border-radius:4px;font-size:11px;box-sizing:border-box;">
                    </div>
                </div>
                <div>
                    <label for="prop-shadow-color" style="font-size:10px;font-weight:700;color:#94a3b8;display:block;margin-bottom:4px;">SHADOW COLOR</label>
                    <input type="text" id="prop-shadow-color" placeholder="rgba(0,0,0,0.15)" style="width:100%;background:#0f172a !important;border:1px solid #334155 !important;color:#f8fafc !important;padding:5px 8px;border-radius:4px;font-size:11px;box-sizing:border-box;">
                </div>
            </div>
        </div>

        <!-- =================== TAB 3: LOGIC =================== -->
        <div id="insp-tab-logic" class="wppoppop-tab-pane wppoppop-insp-content" style="display:none;background:transparent !important;padding:0;margin:0;">
            <!-- Merge Tag / Token -->
            <div class="wppoppop-inspector-section" style="background:transparent !important;margin-bottom:14px;">
                <div style="font-size:11px;font-weight:800;color:#38bdf8;text-transform:uppercase;border-bottom:1px solid #334155;padding-bottom:4px;margin-bottom:8px;">MERGE TAG / TOKEN</div>
                <div style="background:#0f172a;border:1px solid #334155;border-radius:6px;padding:8px;">
                    <label style="font-size:10px;color:#64748b;display:block;margin-bottom:4px;">Click to copy token placeholder:</label>
                    <input type="text" id="prop-display-token" readonly style="width:100%;background:#1e293b !important;border:1px solid #475569 !important;color:#38bdf8 !important;font-family:monospace;font-size:12px;font-weight:600;padding:6px 10px;border-radius:4px;cursor:pointer;box-sizing:border-box;" title="Click to copy">
                </div>
            </div>

            <!-- Step Navigation & Actions -->
            <div class="wppoppop-inspector-section" style="background:transparent !important;margin-bottom:14px;">
                <div style="font-size:11px;font-weight:800;color:#38bdf8;text-transform:uppercase;border-bottom:1px solid #334155;padding-bottom:4px;margin-bottom:8px;">STEP NAVIGATION &amp; ACTIONS</div>
                <div class="wppoppop-field-group">
                    <label for="prop-goto-canvas" style="font-size:10px;font-weight:700;color:#94a3b8;display:block;margin-bottom:4px;">TARGET CANVAS ON CLICK</label>
                    <select id="prop-goto-canvas" style="width:100%;background:#0f172a !important;border:1px solid #334155 !important;color:#f8fafc !important;padding:6px 10px;border-radius:4px;font-size:12px;box-sizing:border-box;">
                        <option value="1">Canvas 1</option>
                        <option value="2">Canvas 2</option>
                        <option value="3">Canvas 3</option>
                    </select>
                </div>
            </div>

            <!-- On-Click URL Redirect -->
            <div class="wppoppop-inspector-section" style="background:transparent !important;margin-bottom:14px;">
                <div style="font-size:11px;font-weight:800;color:#38bdf8;text-transform:uppercase;border-bottom:1px solid #334155;padding-bottom:4px;margin-bottom:8px;">ON-CLICK URL REDIRECT</div>
                <input type="url" id="prop-action-url" placeholder="https://..." style="width:100%;background:#0f172a !important;border:1px solid #334155 !important;color:#f8fafc !important;padding:6px 10px;border-radius:4px;font-size:12px;box-sizing:border-box;margin-bottom:6px;">
                <label style="font-size:11px;color:#94a3b8;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                    <input type="checkbox" id="prop-action-blank" value="1"> Open redirect in new tab
                </label>
            </div>

            <!-- Popup Close Behavior -->
            <div class="wppoppop-inspector-section" style="background:transparent !important;margin-bottom:14px;">
                <div style="font-size:11px;font-weight:800;color:#38bdf8;text-transform:uppercase;border-bottom:1px solid #334155;padding-bottom:4px;margin-bottom:8px;">POPUP CLOSE BEHAVIOR</div>
                <select id="prop-action-close" style="width:100%;background:#0f172a !important;border:1px solid #334155 !important;color:#f8fafc !important;padding:6px 10px;border-radius:4px;font-size:12px;box-sizing:border-box;">
                    <option value="none">Do not close popup</option>
                    <option value="close">Close popup immediately</option>
                    <option value="close_period">Close and hide for current session</option>
                    <option value="close_forever">Close and hide permanently</option>
                </select>
            </div>

            <!-- Custom CSS Identifiers -->
            <div class="wppoppop-inspector-section" style="background:transparent !important;margin-bottom:14px;">
                <div style="font-size:11px;font-weight:800;color:#38bdf8;text-transform:uppercase;border-bottom:1px solid #334155;padding-bottom:4px;margin-bottom:8px;">CUSTOM CSS IDENTIFIERS</div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                    <div>
                        <label for="prop-custom-class" style="font-size:10px;font-weight:700;color:#94a3b8;display:block;margin-bottom:4px;">CSS CLASS</label>
                        <input type="text" id="prop-custom-class" placeholder="custom-btn-class" style="width:100%;height:30px;padding:4px 8px;font-size:11px;border:1px solid #334155;border-radius:4px;background:#0f172a;color:#f1f5f9;box-sizing:border-box;">
                    </div>
                    <div>
                        <label for="prop-custom-id" style="font-size:10px;font-weight:700;color:#94a3b8;display:block;margin-bottom:4px;">CSS ID</label>
                        <input type="text" id="prop-custom-id" placeholder="custom-btn-id" style="width:100%;height:30px;padding:4px 8px;font-size:11px;border:1px solid #334155;border-radius:4px;background:#0f172a;color:#f1f5f9;box-sizing:border-box;">
                    </div>
                </div>
            </div>

            <!-- Custom JS Callback -->
            <div class="wppoppop-inspector-section" style="background:transparent !important;margin-bottom:8px;">
                <div style="font-size:11px;font-weight:800;color:#38bdf8;text-transform:uppercase;border-bottom:1px solid #334155;padding-bottom:4px;margin-bottom:8px;">CUSTOM JS CALLBACK</div>
                <textarea id="prop-action-js" rows="3" placeholder="console.log('Layer clicked');" style="width:100%;background:#0f172a !important;border:1px solid #334155 !important;color:#f8fafc !important;font-family:monospace;font-size:11px;padding:6px 10px;border-radius:4px;box-sizing:border-box;resize:vertical;"></textarea>
            </div>
        </div>

    </div>
</div>


