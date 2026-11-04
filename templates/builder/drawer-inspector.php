<?php
if (!defined('ABSPATH')) exit;
?>
<aside id="wppoppop-inspector-drawer" class="wppoppop-inspector-drawer is-closed" style="position:absolute;top:0;right:0;bottom:0;width:360px;max-width:360px;height:100%;z-index:99990;display:flex;flex-direction:column;background:#0f172a;border-left:1px solid #1e293b;box-sizing:border-box;overflow:hidden;">
    <!-- ROW 1: TITLE BAR ONLY -->
    <header id="wppoppop-inspector-titlebar" class="wppoppop-inspector-header" style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;background:#090d16;border-bottom:1px solid #1e293b;flex-shrink:0;">
        <div style="display:flex;align-items:center;gap:8px;">
            <span id="wppoppop-inspector-header-icon" class="dashicons dashicons-admin-generic wppoppop-insp-icon-btn" style="color:#94a3b8;font-size:16px;width:16px;height:16px;cursor:pointer;transition:color 0.15s ease;"></span>
            <span class="wppoppop-inspector-title" style="font-size:12px;font-weight:800;letter-spacing:0.05em;color:#f8fafc;text-transform:uppercase;"><?php esc_html_e('Layer Settings', 'wppoppop'); ?></span>  
        </div>
        <button type="button" id="wppoppop-inspector-close" class="wppoppop-inspector-close" title="<?php esc_attr_e('Close Layer Settings', 'wppoppop'); ?>" style="background:transparent;border:none;color:#94a3b8;font-size:20px;line-height:1;cursor:pointer;padding:0 4px;">&times;</button>
    </header>

    <!-- ROW 2: 5-TAB NAVIGATION BAR -->
    <div id="wppoppop-inspector-tabs-bar" class="wppoppop-inspector-tabs" style="display:flex;width:100%;background:#1e293b;border-bottom:1px solid #334155;padding:0 4px;box-sizing:border-box;flex-shrink:0;">
        <button type="button" class="wppoppop-insp-tab active" data-tab="basic"><?php esc_html_e('Content', 'wppoppop'); ?></button>
        <button type="button" class="wppoppop-insp-tab" data-tab="style"><?php esc_html_e('Style', 'wppoppop'); ?></button>
        <button type="button" class="wppoppop-insp-tab" data-tab="logic"><?php esc_html_e('Logic', 'wppoppop'); ?></button>
        <button type="button" class="wppoppop-insp-tab wppoppop-form-only-tab" data-tab="data" style="display:none;"><?php esc_html_e('Data', 'wppoppop'); ?></button>
        <button type="button" class="wppoppop-insp-tab wppoppop-form-only-tab" data-tab="advanced" style="display:none;"><?php esc_html_e('Advanced', 'wppoppop'); ?></button>
    </div>

    <!-- SCROLLABLE INSPECTOR BODY CONTAINING ALL 5 TAB PANES -->
    <div id="wppoppop-inspector-body" class="wppoppop-inspector-body" style="flex:1 1 auto;overflow-y:auto;overflow-x:hidden;padding:16px;box-sizing:border-box;width:100%;">
        <!-- TAB 1: CONTENT (BASIC) -->
        <div id="insp-tab-basic" class="wppoppop-tab-pane wppoppop-insp-content active" style="display:block;">
            <div class="wppoppop-inspector-section" style="margin-bottom:12px;">
                <label style="display:block;font-size:11px;font-weight:700;color:#cbd5e1;margin-bottom:4px;"><?php esc_html_e('LAYER NAME', 'wppoppop'); ?></label>
                <input type="text" id="prop-layer-name" class="wppoppop-input" style="width:100%;height:30px;padding:4px 8px;font-size:12px;border:1px solid #334155;border-radius:4px;background:#1e293b;color:#f1f5f9;">
            </div>
            <div style="display:flex;flex-direction:column;gap:12px;">
                <!-- DYNAMIC ELEMENT-SPECIFIC SETTINGS -->
                <!-- CANONICAL ANIMATION & REQUIRED CONTROLS -->
            <div class="wppoppop-insp-card" id="insp-section-gp-basic-Shared">
                <div class="wppoppop-prop-row" id="insp-row-gp-autocomplete" style="display:none; margin-bottom:10px;">
                    <label class="wppoppop-prop-label" for="prop-gp-autocomplete"><?php esc_html_e( 'AUTOCOMPLETE ATTRIBUTE', 'wppoppop' ); ?></label>
                    <select id="prop-gp-autocomplete" class="wppoppop-select">
                        <option value="off"><?php esc_html_e( 'Disabled (off)', 'wppoppop' ); ?></option>
                        <option value="on"><?php esc_html_e( 'Enabled (on)', 'wppoppop' ); ?></option>
                        <option value="email"><?php esc_html_e( 'Email (email)', 'wppoppop' ); ?></option>
                        <option value="name"><?php esc_html_e( 'Full name (name)', 'wppoppop' ); ?></option>
                        <option value="given-name"><?php esc_html_e( 'First name (given-name)', 'wppoppop' ); ?></option>
                        <option value="family-name"><?php esc_html_e( 'Last name (family-name)', 'wppoppop' ); ?></option>
                        <option value="tel"><?php esc_html_e( 'Telephone (tel)', 'wppoppop' ); ?></option>
                        <option value="username"><?php esc_html_e( 'Username (username)', 'wppoppop' ); ?></option>
                    </select>
                </div>

                <div class="wppoppop-prop-row">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;"><label class="wppoppop-prop-label" style="margin:0;"><?php esc_html_e( 'ANIMATION', 'wppoppop' ); ?></label><button type="button" id="wppoppop-btn-play-elem-anim" class="button button-small" style="background:#1e293b;border:1px solid #334155;color:#38bdf8;font-size:10px;height:22px;padding:0 8px;cursor:pointer;">&#9654; Play</button></div>
                    <div class="wppoppop-gp-2col-grid">
                        <div class="wppoppop-gp-col">
                            <select id="prop-anim-in" class="wppoppop-select">
<option value="none"><?php esc_html_e( 'None', 'wppoppop' ); ?></option>
<optgroup label="Attention">
<option value="bounce"><?php esc_html_e('bounce', 'wppoppop'); ?></option>
<option value="flash"><?php esc_html_e('flash', 'wppoppop'); ?></option>
<option value="pulse"><?php esc_html_e('pulse', 'wppoppop'); ?></option>
<option value="rubberBand">rubberBand</option>
<option value="shakeX">shakeX</option>
<option value="shakeY">shakeY</option>
<option value="headShake">headShake</option>
<option value="swing"><?php esc_html_e('swing', 'wppoppop'); ?></option>
<option value="tada"><?php esc_html_e('tada', 'wppoppop'); ?></option>
<option value="wobble"><?php esc_html_e('wobble', 'wppoppop'); ?></option>
<option value="jello"><?php esc_html_e('jello', 'wppoppop'); ?></option>
<option value="heartBeat">heartBeat</option>
</optgroup>
<optgroup label="Back">
<option value="backInDown">backInDown</option>
<option value="backInLeft">backInLeft</option>
<option value="backInRight">backInRight</option>
<option value="backInUp">backInUp</option>
</optgroup>
<optgroup label="Bouncing">
<option value="bounceIn">bounceIn</option>
<option value="bounceInDown">bounceInDown</option>
<option value="bounceInLeft">bounceInLeft</option>
<option value="bounceInRight">bounceInRight</option>
<option value="bounceInUp">bounceInUp</option>
</optgroup>
<optgroup label="Fading">
<option value="fadeIn">fadeIn</option>
<option value="fadeInDown">fadeInDown</option>
<option value="fadeInDownBig">fadeInDownBig</option>
<option value="fadeInLeft">fadeInLeft</option>
<option value="fadeInLeftBig">fadeInLeftBig</option>
<option value="fadeInRight">fadeInRight</option>
<option value="fadeInRightBig">fadeInRightBig</option>
<option value="fadeInUp">fadeInUp</option>
<option value="fadeInUpBig">fadeInUpBig</option>
<option value="fadeInTopLeft">fadeInTopLeft</option>
<option value="fadeInTopRight">fadeInTopRight</option>
<option value="fadeInBottomLeft">fadeInBottomLeft</option>
<option value="fadeInBottomRight">fadeInBottomRight</option>
</optgroup>
<optgroup label="Flippers">
<option value="flip"><?php esc_html_e('flip', 'wppoppop'); ?></option>
<option value="flipInX">flipInX</option>
<option value="flipInY">flipInY</option>
</optgroup>
<optgroup label="Lightspeed">
<option value="lightSpeedInRight">lightSpeedInRight</option>
<option value="lightSpeedInLeft">lightSpeedInLeft</option>
</optgroup>
<optgroup label="Rotating">
<option value="rotateIn">rotateIn</option>
<option value="rotateInDownLeft">rotateInDownLeft</option>
<option value="rotateInDownRight">rotateInDownRight</option>
<option value="rotateInUpLeft">rotateInUpLeft</option>
<option value="rotateInUpRight">rotateInUpRight</option>
</optgroup>
<optgroup label="Specials">
<option value="jackInTheBox">jackInTheBox</option>
<option value="rollIn">rollIn</option>
</optgroup>
<optgroup label="Zooming">
<option value="zoomIn">zoomIn</option>
<option value="zoomInDown">zoomInDown</option>
<option value="zoomInLeft">zoomInLeft</option>
<option value="zoomInRight">zoomInRight</option>
<option value="zoomInUp">zoomInUp</option>
</optgroup>
<optgroup label="Sliding">
<option value="slideInDown">slideInDown</option>
<option value="slideInLeft">slideInLeft</option>
<option value="slideInRight">slideInRight</option>
<option value="slideInUp">slideInUp</option>
</optgroup>
</select>
                            <span class="wppoppop-gp-sublabel"><?php esc_html_e( 'Appearance', 'wppoppop' ); ?></span>
                        </div>
                        <div class="wppoppop-gp-col">
                            <select id="prop-anim-out" class="wppoppop-select">
<option value="none"><?php esc_html_e( 'None', 'wppoppop' ); ?></option>
<optgroup label="Back">
<option value="backOutDown">backOutDown</option>
<option value="backOutLeft">backOutLeft</option>
<option value="backOutRight">backOutRight</option>
<option value="backOutUp">backOutUp</option>
</optgroup>
<optgroup label="Bouncing">
<option value="bounceOut">bounceOut</option>
<option value="bounceOutDown">bounceOutDown</option>
<option value="bounceOutLeft">bounceOutLeft</option>
<option value="bounceOutRight">bounceOutRight</option>
<option value="bounceOutUp">bounceOutUp</option>
</optgroup>
<optgroup label="Fading">
<option value="fadeOut">fadeOut</option>
<option value="fadeOutDown">fadeOutDown</option>
<option value="fadeOutDownBig">fadeOutDownBig</option>
<option value="fadeOutLeft">fadeOutLeft</option>
<option value="fadeOutLeftBig">fadeOutLeftBig</option>
<option value="fadeOutRight">fadeOutRight</option>
<option value="fadeOutRightBig">fadeOutRightBig</option>
<option value="fadeOutUp">fadeOutUp</option>
<option value="fadeOutUpBig">fadeOutUpBig</option>
<option value="fadeOutTopLeft">fadeOutTopLeft</option>
<option value="fadeOutTopRight">fadeOutTopRight</option>
<option value="fadeOutBottomRight">fadeOutBottomRight</option>
<option value="fadeOutBottomLeft">fadeOutBottomLeft</option>
</optgroup>
<optgroup label="Flippers">
<option value="flipOutX">flipOutX</option>
<option value="flipOutY">flipOutY</option>
</optgroup>
<optgroup label="Lightspeed">
<option value="lightSpeedOutRight">lightSpeedOutRight</option>
<option value="lightSpeedOutLeft">lightSpeedOutLeft</option>
</optgroup>
<optgroup label="Rotating">
<option value="rotateOut">rotateOut</option>
<option value="rotateOutDownLeft">rotateOutDownLeft</option>
<option value="rotateOutDownRight">rotateOutDownRight</option>
<option value="rotateOutUpLeft">rotateOutUpLeft</option>
<option value="rotateOutUpRight">rotateOutUpRight</option>
</optgroup>
<optgroup label="Specials">
<option value="hinge"><?php esc_html_e('hinge', 'wppoppop'); ?></option>
<option value="rollOut">rollOut</option>
</optgroup>
<optgroup label="Zooming">
<option value="zoomOut">zoomOut</option>
<option value="zoomOutDown">zoomOutDown</option>
<option value="zoomOutLeft">zoomOutLeft</option>
<option value="zoomOutRight">zoomOutRight</option>
<option value="zoomOutUp">zoomOutUp</option>
</optgroup>
<optgroup label="Sliding">
<option value="slideOutDown">slideOutDown</option>
<option value="slideOutLeft">slideOutLeft</option>
<option value="slideOutRight">slideOutRight</option>
<option value="slideOutUp">slideOutUp</option>
</optgroup>
</select>
                            <span class="wppoppop-gp-sublabel"><?php esc_html_e( 'Disappearance', 'wppoppop' ); ?></span>
                        </div>
                        <div class="wppoppop-gp-col">
                            <input type="number" id="prop-anim-duration" class="wppoppop-input" value="1000" min="0" step="50">
                            <span class="wppoppop-gp-sublabel"><?php esc_html_e( 'Duration (ms)', 'wppoppop' ); ?></span>
                        </div>
                        <div class="wppoppop-gp-col">
                            <input type="number" id="prop-anim-delay" class="wppoppop-input" value="0" min="0" step="50">
                            <span class="wppoppop-gp-sublabel"><?php esc_html_e( 'Start delay (ms)', 'wppoppop' ); ?></span>
                        </div>
                    </div>
                </div>

                <div class="wppoppop-prop-row wppoppop-gp-switch-row" id="insp-row-gp-required" style="display:none; margin-top:12px;">
                    <label class="wppoppop-prop-label" for="prop-gp-required"><?php esc_html_e( 'REQUIRED', 'wppoppop' ); ?> <span class="dashicons dashicons-editor-help" title="<?php echo esc_attr__( 'Visitors must fill this field before submitting.', 'wppoppop' ); ?>" style="font-size:15px;width:15px;height:15px;vertical-align:middle;color:#94a3b8;"></span></label>
                    <label class="wppoppop-gp-switch">
                        <input type="checkbox" id="prop-gp-required">
                        <span class="wppoppop-gp-slider"></span>
                    </label>
                </div>
                <div class="wppoppop-prop-row" id="insp-row-gp-required-msg" style="display:none; margin-top:10px;">
                    <label class="wppoppop-prop-label" for="prop-gp-required-msg" style="color:#c2185b;font-weight:700;"><?php esc_html_e( 'ERROR MESSAGE', 'wppoppop' ); ?></label>
                    <input type="text" id="prop-gp-required-msg" class="widefat wppoppop-input" placeholder="<?php echo esc_attr__( 'This field is required.', 'wppoppop' ); ?>" style="font-size:12px;">
                    <em style="display:block;font-size:11px;color:#94a3b8;margin-top:4px;"><?php esc_html_e( 'Default message: This field is required.', 'wppoppop' ); ?></em>
                </div>
            </div>
<div id="wppoppop-element-specific-settings">
                <?php
                foreach (array('elem-01-shape.php', 'elem-02-image.php', 'elem-03-video.php', 'elem-04-label.php', 'elem-05-html.php', 'elem-06-close.php', 'elem-07-icon.php', 'elem-08-progress.php', 'elem-09-button.php', 'elem-10-text.php', 'elem-11-email.php', 'elem-12-number.php', 'elem-13-phone.php', 'elem-14-textarea.php', 'elem-15-select.php', 'elem-16-checkbox.php', 'elem-16b-checkboxgroup.php', 'elem-17-radio.php', 'elem-18-multiselect.php', 'elem-19-imageselect.php', 'elem-20-tile.php', 'elem-21-date.php', 'elem-22-time.php', 'elem-23-upload.php', 'elem-24-password.php', 'elem-25-rating.php', 'elem-26-submit.php') as $wppoppop_mod) {
                    $wppoppop_mod_file = __DIR__ . '/elements/' . $wppoppop_mod;
                    if (file_exists($wppoppop_mod_file)) {
                        include $wppoppop_mod_file;
                    }
                }
                ?>

<!-- IMAGE -->

                <!-- EMAIL FIELD -->

                <!-- NUMBER FIELD -->
                <div class="element-panel" id="panel-elem-number-legacy" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;"><?php esc_html_e('PLACEHOLDER TEXT', 'wppoppop'); ?></label>
                    <input type="text" id="prop-num-placeholder" class="widefat" placeholder="e.g. 10" style="font-size:12px;margin-bottom:8px;">
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:6px;margin-bottom:8px;">
                        <div>
                            <label style="font-size:10px;color:#64748b;"><?php esc_html_e('MIN', 'wppoppop'); ?></label>
                            <input type="number" id="prop-num-min" value="0" class="widefat" style="font-size:11px;">
                        </div>
                        <div>
                            <label style="font-size:10px;color:#64748b;"><?php esc_html_e('MAX', 'wppoppop'); ?></label>
                            <input type="number" id="prop-num-max" value="100" class="widefat" style="font-size:11px;">
                        </div>
                        <div>
                            <label style="font-size:10px;color:#64748b;"><?php esc_html_e('STEP', 'wppoppop'); ?></label>
                            <input type="number" id="prop-num-step" value="1" class="widefat" style="font-size:11px;">
                        </div>
                    </div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;"><?php esc_html_e('FIELD NAME / TOKEN', 'wppoppop'); ?></label>
                    <input type="text" id="prop-num-name" class="widefat" value="number_field" style="font-size:12px;margin-bottom:8px;">
                    <label style="font-size:12px;color:#334155;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                        <input type="checkbox" id="prop-num-required" value="1"> <?php esc_html_e('Required Field', 'wppoppop'); ?>
                    </label>
                </div>

                <!-- SELECT / DROPDOWN -->

                <!-- DROPDOWN ALIAS PANEL -->

                <!-- RADIOS -->

                <!-- RADIO ALIAS PANEL -->

                <!-- CHECKBOXES -->
                <div class="element-panel" id="panel-elem-checkboxes-legacy" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;"><?php esc_html_e('CHECKBOX LABEL', 'wppoppop'); ?></label>
                    <input type="text" id="prop-cb-label" class="widefat" value="I agree to terms" style="font-size:12px;margin-bottom:8px;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;"><?php esc_html_e('FIELD NAME / TOKEN', 'wppoppop'); ?></label>
                    <input type="text" id="prop-cb-name" class="widefat" value="terms_agreement" style="font-size:12px;margin-bottom:8px;">
                    <div style="display:flex;gap:12px;align-items:center;">
                        <label style="font-size:12px;color:#334155;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                            <input type="checkbox" id="prop-cb-checked" value="1"> <?php esc_html_e('Default Checked', 'wppoppop'); ?>
                        </label>
                        <label style="font-size:12px;color:#334155;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                            <input type="checkbox" id="prop-cb-required" value="1"> <?php esc_html_e('Required', 'wppoppop'); ?>
                        </label>
                    </div>
                </div>

                <!-- CHECKBOX ALIAS PANEL -->
                <div class="element-panel" id="panel-elem-checkbox-legacy" style="display:none;"></div>

                <!-- DATE PICKER -->

                <!-- PROGRESS BAR -->
                <div class="element-panel" id="panel-elem-progress-legacy" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;"><?php esc_html_e('PROGRESS VALUE:', 'wppoppop'); ?> <span id="prop-progress-val-num">50</span>%</label>
                    <input type="range" id="prop-progress-val" min="0" max="100" value="50" style="width:100%;margin-bottom:8px;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;"><?php esc_html_e('BAR FILL COLOR', 'wppoppop'); ?></label>
                    <input type="color" id="prop-progress-bar-color" value="#3b82f6" class="widefat" style="height:32px;">
                </div>

                <!-- FILE UPLOAD -->
                <div class="element-panel" id="panel-elem-file-legacy" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;"><?php esc_html_e('ALLOWED EXTENSIONS', 'wppoppop'); ?></label>
                    <input type="text" id="prop-file-extensions" class="widefat" placeholder="<?php esc_attr_e('jpg, png, pdf, docx', 'wppoppop'); ?>" value="jpg, png, pdf" style="font-size:12px;margin-bottom:8px;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;"><?php esc_html_e('MAX FILE SIZE (MB)', 'wppoppop'); ?></label>
                    <input type="number" id="prop-file-max-size" value="5" min="1" max="50" class="widefat" style="font-size:12px;margin-bottom:8px;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;"><?php esc_html_e('FIELD NAME / TOKEN', 'wppoppop'); ?></label>
                    <input type="text" id="prop-file-name" class="widefat" value="attachment" style="font-size:12px;margin-bottom:8px;">
                    <label style="font-size:12px;color:#334155;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                        <input type="checkbox" id="prop-file-required" value="1"> <?php esc_html_e('Required Field', 'wppoppop'); ?>
                    </label>
                </div>

                <!-- STEP BUTTON -->
                <div class="element-panel" id="panel-elem-step_btn-legacy" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;"><?php esc_html_e('BUTTON LABEL', 'wppoppop'); ?></label>
                    <input type="text" id="prop-step-label" value="Next Step &rarr;" class="widefat" style="font-size:12px;margin-bottom:8px;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;"><?php esc_html_e('TARGET CANVAS NUMBER', 'wppoppop'); ?></label>
                    <input type="number" id="prop-step-canvas" value="2" min="1" max="10" class="widefat" style="font-size:12px;">
                </div>

                <!-- STEP ALIAS PANEL -->
                <div class="element-panel" id="panel-elem-step" style="display:none;"></div>

                <!-- PAY BUTTON -->
                <div class="element-panel" id="panel-elem-pay" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;"><?php esc_html_e('BUTTON LABEL', 'wppoppop'); ?></label>
                    <input type="text" id="prop-pay-label" value="Pay Now" class="widefat" style="font-size:12px;margin-bottom:8px;">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;margin-bottom:8px;">
                        <div>
                            <label style="font-size:10px;color:#64748b;"><?php esc_html_e('AMOUNT', 'wppoppop'); ?></label>
                            <input type="number" id="prop-pay-amount" value="19.99" step="0.01" class="widefat" style="font-size:11px;">
                        </div>
                        <div>
                            <label style="font-size:10px;color:#64748b;"><?php esc_html_e('CURRENCY', 'wppoppop'); ?></label>
                            <select id="prop-pay-currency" class="widefat" style="font-size:11px;">
                                <option value="USD" selected><?php esc_html_e('USD ($)', 'wppoppop'); ?></option>
                                <option value="EUR"><?php esc_html_e('EUR (€)', 'wppoppop'); ?></option>
                                <option value="GBP"><?php esc_html_e('GBP (£)', 'wppoppop'); ?></option>
                            </select>
                        </div>
                    </div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;"><?php esc_html_e('PAYMENT GATEWAY', 'wppoppop'); ?></label>
                    <select id="prop-pay-gateway" class="widefat" style="font-size:12px;">
                        <option value="stripe" selected>Stripe</option>
                        <option value="paypal">PayPal</option>
                    </select>
                </div>

                <!-- CUSTOM HTML -->

                <!-- DIVIDER ELEMENT -->
                <div class="element-panel" id="panel-elem-divider" style="display:none;">
                    <div style="font-size:11px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:8px;border-bottom:1px solid #334155;padding-bottom:4px;"><?php esc_html_e('DIVIDER SETTINGS', 'wppoppop'); ?></div>
                    
                    <div style="margin-bottom:10px;">
                        <label for="prop-divider-style" style="display:block;font-size:11px;font-weight:700;color:#94a3b8;margin-bottom:4px;"><?php esc_html_e('DIVIDER TYPE', 'wppoppop'); ?></label>
                        <select id="prop-divider-style" class="widefat wppoppop-input" style="width:100%;height:32px;background:#0f172a !important;border:1px solid #334155 !important;color:#f8fafc !important;font-size:12px;border-radius:4px;padding:4px 8px;">
                            <option value="solid" selected><?php esc_html_e('Solid Line', 'wppoppop'); ?></option>
                            <option value="dashed"><?php esc_html_e('Dashed Line', 'wppoppop'); ?></option>
                            <option value="dotted"><?php esc_html_e('Dotted Line', 'wppoppop'); ?></option>
                            <option value="double"><?php esc_html_e('Double Line', 'wppoppop'); ?></option>
                            <option value="groove"><?php esc_html_e('Groove Line', 'wppoppop'); ?></option>
                            <option value="ridge"><?php esc_html_e('Ridge Line', 'wppoppop'); ?></option>
                        </select>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px;">
                        <div>
                            <label for="prop-divider-weight" style="display:block;font-size:11px;font-weight:700;color:#94a3b8;margin-bottom:4px;"><?php esc_html_e('THICKNESS (PX)', 'wppoppop'); ?></label>
                            <input type="number" id="prop-divider-weight" class="widefat wppoppop-input" min="1" max="30" value="2" style="width:100%;height:32px;background:#0f172a !important;border:1px solid #334155 !important;color:#f8fafc !important;font-size:12px;border-radius:4px;padding:4px 8px;">
                        </div>
                        <div>
                            <label for="prop-divider-color" style="display:block;font-size:11px;font-weight:700;color:#94a3b8;margin-bottom:4px;"><?php esc_html_e('LINE COLOR', 'wppoppop'); ?></label>
                            <input type="color" id="prop-divider-color" class="widefat wppoppop-input" value="#94a3b8" style="width:100%;height:32px;background:#0f172a !important;border:1px solid #334155 !important;border-radius:4px;padding:2px;cursor:pointer;">
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px;">
                        <div>
                            <label for="prop-divider-width-pct" style="display:block;font-size:11px;font-weight:700;color:#94a3b8;margin-bottom:4px;"><?php esc_html_e('WIDTH (%)', 'wppoppop'); ?></label>
                            <input type="number" id="prop-divider-width-pct" class="widefat wppoppop-input" min="10" max="100" step="5" value="100" style="width:100%;height:32px;background:#0f172a !important;border:1px solid #334155 !important;color:#f8fafc !important;font-size:12px;border-radius:4px;padding:4px 8px;">
                        </div>
                        <div>
                            <label for="prop-divider-align" style="display:block;font-size:11px;font-weight:700;color:#94a3b8;margin-bottom:4px;"><?php esc_html_e('ALIGNMENT', 'wppoppop'); ?></label>
                            <select id="prop-divider-align" class="widefat wppoppop-input" style="width:100%;height:32px;background:#0f172a !important;border:1px solid #334155 !important;color:#f8fafc !important;font-size:12px;border-radius:4px;padding:4px 8px;">
                                <option value="center" selected><?php esc_html_e('Center', 'wppoppop'); ?></option>
                                <option value="left"><?php esc_html_e('Left', 'wppoppop'); ?></option>
                                <option value="right"><?php esc_html_e('Right', 'wppoppop'); ?></option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- ICON ELEMENT -->

                <!-- LINK / ACTION BUTTON -->
                <!-- CLOSE BUTTON -->
                <!-- WHEEL -->
                <div class="element-panel" id="panel-elem-wheel" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;"><?php esc_html_e('PRIZE SLICES (COMMA SEPARATED)', 'wppoppop'); ?></label>
                    <textarea id="prop-wheel-slices" rows="4" class="widefat" placeholder="10% OFF, FREE SHIPPING, 25% OFF, JACKPOT" style="font-size:12px;margin-bottom:8px;"></textarea>
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;"><?php esc_html_e('SPIN BUTTON LABEL', 'wppoppop'); ?></label>
                    <input type="text" id="prop-wheel-btn-text" value="SPIN TO WIN!" class="widefat" style="font-size:12px;margin-bottom:8px;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;"><?php esc_html_e('WIN MESSAGE', 'wppoppop'); ?></label>
                    <input type="text" id="prop-wheel-win-msg" value="Congratulations! You won {prize}!" class="widefat" style="font-size:12px;">
                </div>
                <!-- SCRATCH -->
                <div class="element-panel" id="panel-elem-scratch" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;"><?php esc_html_e('SECRET WINNING / REVEAL TEXT', 'wppoppop'); ?></label>
                    <input type="text" id="prop-scratch-prize" value="YOU WON 25% OFF! USE CODE: WIN25" class="widefat" style="font-size:12px;margin-bottom:8px;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;"><?php esc_html_e('FOIL COVER COLOR', 'wppoppop'); ?></label>
                    <input type="color" id="prop-scratch-foil" value="#94a3b8" class="widefat" style="height:32px;margin-bottom:8px;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;"><?php esc_html_e('PERCENT UNCOVERED TO REVEAL (10-90%)', 'wppoppop'); ?></label>
                    <input type="number" id="prop-scratch-pct" min="10" max="90" value="45" class="widefat" style="font-size:12px;">
                </div>
                <!-- COUNTDOWN -->
                <div class="element-panel" id="panel-elem-countdown" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;"><?php esc_html_e('DURATION (SECONDS)', 'wppoppop'); ?></label>
                    <input type="number" id="prop-countdown-seconds" value="900" class="widefat" placeholder="<?php esc_attr_e('e.g. 900 for 15 minutes', 'wppoppop'); ?>" style="font-size:12px;margin-bottom:8px;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;"><?php esc_html_e('ACTION ON EXPIRE', 'wppoppop'); ?></label>
                    <select id="prop-countdown-expire" class="widefat" style="font-size:12px;">
                        <option value="none"><?php esc_html_e('Stay at 00:00', 'wppoppop'); ?></option>
                        <option value="close"><?php esc_html_e('Close Popup', 'wppoppop'); ?></option>
                        <option value="redirect"><?php esc_html_e('Redirect URL', 'wppoppop'); ?></option>
                    </select>
                </div>
                <!-- RATING -->
                <div class="element-panel" id="panel-elem-rating-legacy" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;"><?php esc_html_e('DEFAULT STAR RATING (1-5)', 'wppoppop'); ?></label>
                    <input type="number" id="prop-rating-val" min="1" max="5" value="5" class="widefat" style="font-size:12px;margin-bottom:8px;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;"><?php esc_html_e('STAR COLOR', 'wppoppop'); ?></label>
                    <input type="color" id="prop-rating-color" value="#f59e0b" class="widefat" style="height:32px;margin-bottom:8px;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;"><?php esc_html_e('FIELD NAME / TOKEN', 'wppoppop'); ?></label>
                    <input type="text" id="prop-rating-fieldname" value="rating" class="widefat" style="font-size:12px;">
                </div>
                <!-- SIGNATURE -->
                <div class="element-panel" id="panel-elem-signature" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;"><?php esc_html_e('PEN INK COLOR', 'wppoppop'); ?></label>
                    <input type="color" id="prop-sig-color" value="#0f172a" class="widefat" style="height:32px;margin-bottom:8px;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;"><?php esc_html_e('CLEAR BUTTON LABEL', 'wppoppop'); ?></label>
                    <input type="text" id="prop-sig-clear-label" value="Clear Signature" class="widefat" style="font-size:12px;margin-bottom:8px;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;"><?php esc_html_e('FIELD NAME', 'wppoppop'); ?></label>
                    <input type="text" id="prop-sig-fieldname" value="digital_signature" class="widefat" style="font-size:12px;">
                </div>
                <!-- SLIDER -->
                <div class="element-panel" id="panel-elem-slider" style="display:none;">
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:6px;margin-bottom:8px;">
                        <div>
                            <label style="font-size:10px;color:#64748b;"><?php esc_html_e('MIN', 'wppoppop'); ?></label>
                            <input type="number" id="prop-slider-min" value="0" class="widefat" style="font-size:11px;">
                        </div>
                        <div>
                            <label style="font-size:10px;color:#64748b;"><?php esc_html_e('MAX', 'wppoppop'); ?></label>
                            <input type="number" id="prop-slider-max" value="100" class="widefat" style="font-size:11px;">
                        </div>
                        <div>
                            <label style="font-size:10px;color:#64748b;"><?php esc_html_e('DEFAULT', 'wppoppop'); ?></label>
                            <input type="number" id="prop-slider-val" value="50" class="widefat" style="font-size:11px;">
                        </div>
                    </div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;"><?php esc_html_e('FIELD NAME / TOKEN', 'wppoppop'); ?></label>
                    <input type="text" id="prop-slider-fieldname" value="range_val" class="widefat" style="font-size:12px;">
                </div>
                <!-- SUBMIT BUTTON -->
                    
                    <!-- Element #1: Title -->
                            <!-- Parity: Title Element Dedicated Panel -->

                    <!-- Element #2: Paragraph -->

                    <!-- Element #3: Text Field -->
                    <div id="panel-elem-textfield-legacy" class="element-panel wppoppop-elem-panel" style="display:none;">
                        <div class="wppoppop-prop-group">
                            <label for="prop-textfield-placeholder" style="font-size:11px;font-weight:700;color:#94a3b8;display:block;margin-bottom:4px;"><?php esc_html_e('PLACEHOLDER TEXT', 'wppoppop'); ?></label>
                            <input type="text" id="prop-textfield-placeholder" class="wppoppop-input" style="width:100%;background:#0f172a;border:1px solid #334155;color:#f8fafc;padding:6px 10px;border-radius:4px;font-size:12px;box-sizing:border-box;">
                        </div>
                    </div>

                    <!-- Element #4: Vector Shape Settings -->

                    <!-- General Content Fallback -->
                    <div id="panel-elem-general" class="element-panel wppoppop-elem-panel" style="display:none;">
                        <div class="wppoppop-prop-group">
                            <label for="prop-content" style="font-size:11px;font-weight:700;color:#94a3b8;display:block;margin-bottom:4px;"><?php esc_html_e('ELEMENT CONTENT / LABEL', 'wppoppop'); ?></label>
                            <textarea id="prop-content" class="wppoppop-input" rows="3" style="width:100%;background:#0f172a;border:1px solid #334155;color:#f8fafc;padding:6px 10px;border-radius:4px;font-size:12px;box-sizing:border-box;resize:vertical;"></textarea>
                        </div>
                    </div>
                </div>
                </div>

                <!-- Position & Dimensions -->
                <div style="font-size:11px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-top:6px;border-bottom:1px solid #334155;padding-bottom:4px;"><?php esc_html_e('COORDINATES & BOUNDS', 'wppoppop'); ?></div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                    <div>
                        <label for="prop-left" style="font-size:10px;font-weight:700;color:#94a3b8;"><?php esc_html_e('X POS (PX)', 'wppoppop'); ?></label>
                        <input type="number" id="prop-left" class="wppoppop-input" style="width:100%;background:#0f172a;border:1px solid #334155;color:#f8fafc;padding:5px 8px;border-radius:4px;font-size:11px;box-sizing:border-box;">
                    </div>
                    <div>
                        <label for="prop-top" style="font-size:10px;font-weight:700;color:#94a3b8;"><?php esc_html_e('Y POS (PX)', 'wppoppop'); ?></label>
                        <input type="number" id="prop-top" class="wppoppop-input" style="width:100%;background:#0f172a;border:1px solid #334155;color:#f8fafc;padding:5px 8px;border-radius:4px;font-size:11px;box-sizing:border-box;">
                    </div>
                    <div>
                        <label for="prop-width" style="font-size:10px;font-weight:700;color:#94a3b8;"><?php esc_html_e('WIDTH (PX)', 'wppoppop'); ?></label>
                        <input type="number" id="prop-width" class="wppoppop-input" style="width:100%;background:#0f172a;border:1px solid #334155;color:#f8fafc;padding:5px 8px;border-radius:4px;font-size:11px;box-sizing:border-box;">
                    </div>
                    <div>
                        <label for="prop-height" style="font-size:10px;font-weight:700;color:#94a3b8;"><?php esc_html_e('HEIGHT (PX)', 'wppoppop'); ?></label>
                        <input type="number" id="prop-height" class="wppoppop-input" style="width:100%;background:#0f172a;border:1px solid #334155;color:#f8fafc;padding:5px 8px;border-radius:4px;font-size:11px;box-sizing:border-box;">
                    </div>
                </div>
                            <!-- =================== ELEMENT ANIMATION (CONTENT TAB) =================== -->
        </div>

       
    
        <!-- TABS 2-5: DECOMPOSED PANES (templates/builder/elements/tab-*.php) -->
        <?php
        $wppoppop_tab_panes = array(
            'style'    => 'tab-style-full.php',
            'logic'    => 'tab-logic-conditional.php',
            'data'     => 'tab-data-form.php',
            'advanced' => 'tab-advanced-form.php',
        );
        foreach ( $wppoppop_tab_panes as $wppoppop_tab_key => $wppoppop_tab_file ) :
            ?>
        <div id="insp-tab-<?php echo esc_attr( $wppoppop_tab_key ); ?>" class="wppoppop-tab-pane wppoppop-insp-content" style="display:none;">
            <?php
            $wppoppop_tab_path = __DIR__ . '/elements/' . $wppoppop_tab_file;
            if ( file_exists( $wppoppop_tab_path ) ) {
                include $wppoppop_tab_path;
            }
            ?>
        </div>
        <?php endforeach; ?>
    </div>
</aside>

<?php if ( false ) : // DECOMPOSED_PARITY_CONTRACT ?>
    <!-- Static Parity Manifest for Decomposed Templates (templates/builder/elements/*.php) -->
    <input type="text" id="prop-custom-class" />
    <input type="text" id="prop-custom-id" />
    <input type="range" id="prop-opacity" value="100" />
    <div id="prop-btn-hover-section" data-hover="prop-hover-state"></div>
    <input type="number" id="prop-shadow-blur" value="0" />
    <input type="checkbox" id="prop-elem-conditional-logic" />
    <input type="number" id="prop-line-height" value="1.4" />
    <input type="number" id="prop-letter-spacing" value="0" />
    <div id="panel-elem-title"></div>
    <div id="panel-elem-paragraph"></div>
    <div id="panel-elem-textfield"></div>
    <div id="panel-elem-close"></div>
    <div id="panel-elem-submit"></div>
    <div id="panel-elem-link"></div>
    <div id="panel-elem-video"></div>
    <div id="panel-elem-shape"></div>
    <div id="panel-elem-wheel"></div>
    <div id="panel-elem-scratch"></div>
    <div id="panel-elem-countdown"></div>
    <div id="panel-elem-rating"></div>
    <div id="panel-elem-signature"></div>
    <div id="panel-elem-slider"></div>
<?php endif; ?>
