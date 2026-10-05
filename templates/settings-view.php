<?php
if (!defined('ABSPATH')) {
    exit;
}

$settings = get_option('wppoppop_settings', []);

// Defaults
$sender_name       = isset($settings['sender_name']) ? $settings['sender_name'] : 'wppoppop';
$sender_email      = isset($settings['sender_email']) ? $settings['sender_email'] : 'noreply@localhost';
$preload_popups    = !empty($settings['preload_popups']);
$preload_events    = !empty($settings['preload_events']);
$ga_tracking       = !empty($settings['ga_tracking']);
$google_fonts      = isset($settings['google_fonts']) ? !empty($settings['google_fonts']) : true;
$font_awesome      = !empty($settings['font_awesome']);
$air_datepicker    = isset($settings['air_datepicker']) ? !empty($settings['air_datepicker']) : true;
$no_air_datepicker = !empty($settings['no_air_datepicker']);
$jquery_mask       = !empty($settings['jquery_mask']);
$js_parser         = !empty($settings['js_parser']);
$signature_pad     = !empty($settings['signature_pad']);
$range_slider      = !empty($settings['range_slider']);
$adblock_detector  = !empty($settings['adblock_detector']);
$csv_separator     = isset($settings['csv_separator']) ? $settings['csv_separator'] : ',';
$custom_fonts      = isset($settings['custom_fonts']) ? $settings['custom_fonts'] : '';
$email_validation  = isset($settings['email_validation']) ? $settings['email_validation'] : 'basic';
$geoip_service     = isset($settings['geoip_service']) ? $settings['geoip_service'] : 'none';
$user_uploads      = isset($settings['user_uploads']) ? $settings['user_uploads'] : 'keep';
$custom_css        = isset($settings['custom_css']) ? $settings['custom_css'] : '';
$custom_js         = isset($settings['custom_js']) ? $settings['custom_js'] : '';
?>
<div class="wrap wppoppop-settings-wrap">
    <div class="settings-header-bar">
        <h1>WpPopPop - General Settings</h1>
        <a href="https://github.com/cygnusorbit/wppoppop" target="_blank" class="button button-secondary btn-docs">Online Documentation</a>
    </div>

    <div class="settings-nav-tabs">
        <button type="button" class="nav-tab active" data-tab="tab-general">General</button>
        <button type="button" class="nav-tab" data-tab="tab-advanced">Advanced</button>
    </div>

    <form id="wppoppop-settings-form">
        <!-- Tab 1: General Settings -->
        <div id="tab-general" class="settings-tab-pane active">
            
            <!-- Mailing Settings Section -->
            <div class="settings-section-badge">Mailing Settings</div>
            <table class="settings-form-table">
                <tr>
                    <th scope="row"><label for="sender_name">Sender name:</label></th>
                    <td>
                        <input type="text" name="sender_name" id="sender_name" value="<?php echo esc_attr($sender_name); ?>" class="regular-text widefat-setting">
                        <p class="description">Please enter sender name. All messages from plugin are sent using this name as "FROM:" header value..</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="sender_email">Sender email:</label></th>
                    <td>
                        <input type="text" name="sender_email" id="sender_email" value="<?php echo esc_attr($sender_email); ?>" class="regular-text widefat-setting">
                        <p class="description">Please enter sender e-mail. All messages from plugin are sent using this e-mail as "FROM:" header value..</p>
                    </td>
                </tr>
            </table>

            <!-- Miscellaneous Section -->
            <div class="settings-section-badge" style="margin-top: 25px;">Miscellaneous</div>
            <table class="settings-form-table">
                <!-- Pre-load Popups -->
                <tr>
                    <th scope="row">Pre-load popups:</th>
                    <td>
                        <div class="toggle-row">
                            <label class="wppoppop-switch">
                                <input type="checkbox" name="preload_popups" id="preload_popups" <?php checked($preload_popups); ?>>
                                <span class="wppoppop-slider"></span>
                            </label>
                            <label for="preload_popups" class="toggle-label-text">Pre-load popups</label>
                        </div>
                        <p class="description">Tick checkbox to pre-load popups (not recommended). If disabled, popups are pulled on demand using AJAX.</p>

                        <div class="toggle-row" style="margin-top: 14px;">
                            <label class="wppoppop-switch">
                                <input type="checkbox" name="preload_events" id="preload_events" <?php checked($preload_events); ?>>
                                <span class="wppoppop-slider"></span>
                            </label>
                            <label for="preload_events" class="toggle-label-text">Pre-load event popups</label>
                        </div>
                        <p class="description">If enabled, only event popups (OnLoad, OnExit, etc.) are loaded together with website. All other popups are pulled on demand using AJAX.</p>
                    </td>
                </tr>

                <!-- GA Tracking -->
                <tr>
                    <th scope="row">GA tracking:</th>
                    <td>
                        <div class="toggle-row">
                            <label class="wppoppop-switch">
                                <input type="checkbox" name="ga_tracking" id="ga_tracking" <?php checked($ga_tracking); ?>>
                                <span class="wppoppop-slider"></span>
                            </label>
                            <label for="ga_tracking" class="toggle-label-text">Enable Google Analytics tracking</label>
                        </div>
                        <p class="description">Send form submission event to Google Analytics. GA must be installed on your website. If you use GTM, please configure it properly to accept events.</p>
                    </td>
                </tr>

                <!-- Google Fonts -->
                <tr>
                    <th scope="row">Google Fonts:</th>
                    <td>
                        <div class="toggle-row">
                            <label class="wppoppop-switch">
                                <input type="checkbox" name="google_fonts" id="google_fonts" <?php checked($google_fonts); ?>>
                                <span class="wppoppop-slider"></span>
                            </label>
                            <label for="google_fonts" class="toggle-label-text">Enable Google Fonts</label>
                        </div>
                        <p class="description">Turn this feature on if you want to use full set of Google Fonts loaded from Google servers.</p>
                    </td>
                </tr>

                <!-- Font Awesome -->
                <tr>
                    <th scope="row">Font Awesome:</th>
                    <td>
                        <div class="toggle-row">
                            <label class="wppoppop-switch">
                                <input type="checkbox" name="font_awesome" id="font_awesome" <?php checked($font_awesome); ?>>
                                <span class="wppoppop-slider"></span>
                            </label>
                            <label for="font_awesome" class="toggle-label-text">Enable Font Awesome icons</label>
                        </div>
                        <p class="description">Turn this feature on if you want to use full set of Font Awesome icons.</p>
                    </td>
                </tr>

                <!-- Air Datepicker plugin -->
                <tr>
                    <th scope="row">Air Datepicker plugin:</th>
                    <td>
                        <div class="toggle-row">
                            <label class="wppoppop-switch">
                                <input type="checkbox" name="air_datepicker" id="air_datepicker" <?php checked($air_datepicker); ?>>
                                <span class="wppoppop-slider"></span>
                            </label>
                            <label for="air_datepicker" class="toggle-label-text">Enable Air Datepicker plugin</label>
                        </div>
                        <p class="description">Turn this feature on if you want to use nice datepicker with forms.</p>

                        <div class="toggle-row" style="margin-top: 14px;">
                            <label class="wppoppop-switch">
                                <input type="checkbox" name="no_air_datepicker" id="no_air_datepicker" <?php checked($no_air_datepicker); ?>>
                                <span class="wppoppop-slider"></span>
                            </label>
                            <label for="no_air_datepicker" class="toggle-label-text">Do not load Air Datepicker plugin</label>
                        </div>
                        <p class="description">Turn this feature on if your theme or another plugin already loads Air Datepicker plugin.</p>
                    </td>
                </tr>

                <!-- jQuery Mask plugin -->
                <tr>
                    <th scope="row">jQuery Mask plugin:</th>
                    <td>
                        <div class="toggle-row">
                            <label class="wppoppop-switch">
                                <input type="checkbox" name="jquery_mask" id="jquery_mask" <?php checked($jquery_mask); ?>>
                                <span class="wppoppop-slider"></span>
                            </label>
                            <label for="jquery_mask" class="toggle-label-text">Enable jQuery Mask plugin</label>
                        </div>
                        <p class="description">Turn this feature on if you want to specify input masks for text fields.</p>
                    </td>
                </tr>

                <!-- JavaScript Expression Parser -->
                <tr>
                    <th scope="row">JavaScript Expression Parser:</th>
                    <td>
                        <div class="toggle-row">
                            <label class="wppoppop-switch">
                                <input type="checkbox" name="js_parser" id="js_parser" <?php checked($js_parser); ?>>
                                <span class="wppoppop-slider"></span>
                            </label>
                            <label for="js_parser" class="toggle-label-text">Enable JavaScript Expression Parser plugin</label>
                        </div>
                        <p class="description">Turn this feature on if you want to use math expressions and show results on front-end side.</p>
                    </td>
                </tr>

                <!-- Signature Pad plugin -->
                <tr>
                    <th scope="row">Signature Pad plugin:</th>
                    <td>
                        <div class="toggle-row">
                            <label class="wppoppop-switch">
                                <input type="checkbox" name="signature_pad" id="signature_pad" <?php checked($signature_pad); ?>>
                                <span class="wppoppop-slider"></span>
                            </label>
                            <label for="signature_pad" class="toggle-label-text">Enable Signature Pad plugin</label>
                        </div>
                        <p class="description">Turn this feature on if you want to use signature pad with forms.</p>
                    </td>
                </tr>

                <!-- Ion.RangeSlider plugin -->
                <tr>
                    <th scope="row">Ion.RangeSlider plugin:</th>
                    <td>
                        <div class="toggle-row">
                            <label class="wppoppop-switch">
                                <input type="checkbox" name="range_slider" id="range_slider" <?php checked($range_slider); ?>>
                                <span class="wppoppop-slider"></span>
                            </label>
                            <label for="range_slider" class="toggle-label-text">Enable Ion.RangeSlider plugin</label>
                        </div>
                        <p class="description">Turn this feature on if you want to use range slider with forms.</p>
                    </td>
                </tr>

                <!-- AdBlock detector -->
                <tr>
                    <th scope="row">AdBlock detector:</th>
                    <td>
                        <div class="toggle-row">
                            <label class="wppoppop-switch">
                                <input type="checkbox" name="adblock_detector" id="adblock_detector" <?php checked($adblock_detector); ?>>
                                <span class="wppoppop-slider"></span>
                            </label>
                            <label for="adblock_detector" class="toggle-label-text">Enable AdBlock detector</label>
                        </div>
                        <p class="description">Turn this feature on if you want to use OnAdBlockDetected popups.</p>
                    </td>
                </tr>

                <!-- CSV Column Separator -->
                <tr>
                    <th scope="row"><label for="csv_separator">CSV column separator:</label></th>
                    <td>
                        <select name="csv_separator" id="csv_separator" class="settings-select">
                            <option value="," <?php selected($csv_separator, ','); ?>>Comma - ","</option>
                            <option value=";" <?php selected($csv_separator, ';'); ?>>Semicolon - ";"</option>
                            <option value="tab" <?php selected($csv_separator, 'tab'); ?>>Tab</option>
                        </select>
                        <p class="description">Please select CSV column separator.</p>
                    </td>
                </tr>

                <!-- Custom Local Fonts -->
                <tr>
                    <th scope="row"><label for="custom_fonts">Custom local fonts:</label></th>
                    <td>
                        <textarea name="custom_fonts" id="custom_fonts" rows="4" class="widefat-setting" placeholder="---"><?php echo esc_textarea($custom_fonts); ?></textarea>
                        <p class="description">Set custom local font names (one name per line). Font name must be exactly the same as it goes in your CSS-files, without quotes.</p>
                    </td>
                </tr>

                <!-- Email Validation -->
                <tr>
                    <th scope="row"><label for="email_validation">Email validation:</label></th>
                    <td>
                        <select name="email_validation" id="email_validation" class="settings-select">
                            <option value="basic" <?php selected($email_validation, 'basic'); ?>>Basic (check syntax)</option>
                            <option value="deep" <?php selected($email_validation, 'deep'); ?>>Deep (MX records check)</option>
                            <option value="none" <?php selected($email_validation, 'none'); ?>>Disabled</option>
                        </select>
                        <p class="description">Please select the type of email validation.</p>
                    </td>
                </tr>

                <!-- GeoIP Service -->
                <tr>
                    <th scope="row"><label for="geoip_service">GeoIP service:</label></th>
                    <td>
                        <select name="geoip_service" id="geoip_service" class="settings-select">
                            <option value="none" <?php selected($geoip_service, 'none'); ?>>None</option>
                            <option value="cloudflare" <?php selected($geoip_service, 'cloudflare'); ?>>Cloudflare (CF-IPCountry Header)</option>
                            <option value="ip_api" <?php selected($geoip_service, 'ip_api'); ?>>IP-API Web Service</option>
                        </select>
                        <p class="description">Please select the GeoIP service.</p>
                    </td>
                </tr>

                <!-- User Uploads -->
                <tr>
                    <th scope="row"><label for="user_uploads">User uploads:</label></th>
                    <td>
                        <select name="user_uploads" id="user_uploads" class="settings-select">
                            <option value="keep" <?php selected($user_uploads, 'keep'); ?>>Do not delete files</option>
                            <option value="7_days" <?php selected($user_uploads, '7_days'); ?>>Delete after 7 days</option>
                            <option value="30_days" <?php selected($user_uploads, '30_days'); ?>>Delete after 30 days</option>
                        </select>
                        <p class="description">Please select how long to keep user uploads on server.</p>
                    </td>
                </tr>

                <!-- Reset Cookie -->
                <tr>
                    <th scope="row">Reset cookie:</th>
                    <td>
                        <button type="button" id="btn-reset-cookie" class="button btn-reset-cookies">
                            <span class="dashicons dashicons-dismiss"></span> Reset Cookies
                        </button>
                        <p class="description">Click the button to reset cookie. Popup will appear for all users. Do this operation if you changed content in popup and want to display it for returning visitors.</p>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Tab 2: Advanced Settings -->
        <div id="tab-advanced" class="settings-tab-pane">
            <div class="settings-section-badge">Advanced Scripts & Styles</div>
            <table class="settings-form-table">
                <tr>
                    <th scope="row"><label for="custom_css">Global Custom CSS:</label></th>
                    <td>
                        <textarea name="custom_css" id="custom_css" rows="6" class="widefat-setting" placeholder="/* Custom CSS loaded with all popups */"><?php echo esc_textarea($custom_css); ?></textarea>
                        <p class="description">Custom stylesheet rules inserted globally across all popup instances.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="custom_js">Global Custom JavaScript:</label></th>
                    <td>
                        <textarea name="custom_js" id="custom_js" rows="6" class="widefat-setting" placeholder="// Custom JavaScript execution on popup initialization"><?php echo esc_textarea($custom_js); ?></textarea>
                        <p class="description">Global JavaScript snippet executed whenever any popup is triggered on the frontend.</p>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Fixed / Bottom Actions Bar -->
        <div class="settings-actions-footer">
            <button type="submit" id="btn-save-settings" class="button btn-save-primary">
                <span class="dashicons dashicons-yes"></span> Save Settings
            </button>
        </div>
    </form>
</div>
