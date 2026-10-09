<?php
if (!defined('ABSPATH')) {
    exit;
}

$email_validation       = isset($settings['email_validation']) ? $settings['email_validation'] : 'basic';
$enable_recaptcha       = !empty($settings['enable_recaptcha']);
$recaptcha_site_key     = isset($settings['recaptcha_site_key']) ? $settings['recaptcha_site_key'] : '';
$recaptcha_secret_key   = isset($settings['recaptcha_secret_key']) ? $settings['recaptcha_secret_key'] : '';
$max_submissions_per_ip = isset($settings['max_submissions_per_ip']) ? (int) $settings['max_submissions_per_ip'] : 10;
$ip_geotargeting        = !empty($settings['ip_geotargeting']);
$geoip_service          = isset($settings['geoip_service']) ? $settings['geoip_service'] : (isset($settings['geoip_api_service']) ? $settings['geoip_api_service'] : 'none');
?>
<div class="wppoppop-settings-section">
    <h3><?php esc_html_e('Bot Protection & Geo-Targeting Rules', 'wppoppop'); ?></h3>
    <table class="form-table">
        <tr>
            <th scope="row"><label for="setting-email-val"><?php esc_html_e('Email Validation Mode', 'wppoppop'); ?></label></th>
            <td>
                <select id="setting-email-val" name="settings[email_validation]">
                    <option value="basic" <?php selected($email_validation, 'basic'); ?>><?php esc_html_e('Standard RFC Syntax Check', 'wppoppop'); ?></option>
                    <option value="disposable_filter" <?php selected($email_validation, 'disposable_filter'); ?>><?php esc_html_e('Syntax Check + Block Disposable Domains (10MinuteMail, Mailinator)', 'wppoppop'); ?></option>
                </select>
                <p class="description"><?php esc_html_e('Rejects throwaway and temporary email addresses from polluting your CRM.', 'wppoppop'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php esc_html_e('Google reCAPTCHA', 'wppoppop'); ?></th>
            <td>
                <label>
                    <input type="checkbox" name="settings[enable_recaptcha]" value="1" <?php checked($enable_recaptcha); ?>>
                    <?php esc_html_e('Enable Google reCAPTCHA verification on all lead submission forms', 'wppoppop'); ?>
                </label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="setting-recaptcha-site"><?php esc_html_e('reCAPTCHA Site Key', 'wppoppop'); ?></label></th>
            <td>
                <input type="text" id="setting-recaptcha-site" name="settings[recaptcha_site_key]" value="<?php echo esc_attr($recaptcha_site_key); ?>" class="regular-text">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="setting-recaptcha-secret"><?php esc_html_e('reCAPTCHA Secret Key', 'wppoppop'); ?></label></th>
            <td>
                <input type="text" id="setting-recaptcha-secret" name="settings[recaptcha_secret_key]" value="<?php echo esc_attr($recaptcha_secret_key); ?>" class="regular-text">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="setting-ip-limit"><?php esc_html_e('IP Rate Limiting', 'wppoppop'); ?></label></th>
            <td>
                <input type="number" id="setting-ip-limit" name="settings[max_submissions_per_ip]" value="<?php echo esc_attr($max_submissions_per_ip); ?>" min="1" max="100" class="small-text">
                <?php esc_html_e('submissions allowed per IP per hour', 'wppoppop'); ?>
                <p class="description"><?php esc_html_e('Throttles aggressive bot submissions and prevents form spam.', 'wppoppop'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php esc_html_e('IP Geo-Targeting', 'wppoppop'); ?></th>
            <td>
                <label>
                    <input type="checkbox" name="settings[ip_geotargeting]" value="1" <?php checked($ip_geotargeting); ?>>
                    <?php esc_html_e('Enable IP-based country and regional targeting rules', 'wppoppop'); ?>
                </label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="setting-geoip-service"><?php esc_html_e('GeoIP Resolution Service', 'wppoppop'); ?></label></th>
            <td>
                <select id="setting-geoip-service" name="settings[geoip_service]">
                    <option value="none" <?php selected($geoip_service, 'none'); ?>><?php esc_html_e('None (Server Headers: Cloudflare CF-IPCountry only)', 'wppoppop'); ?></option>
                    <option value="ipapi" <?php selected($geoip_service, 'ipapi'); ?>>ipapi.co (JSON API)</option>
                    <option value="ip-api" <?php selected($geoip_service, 'ip-api'); ?>>ip-api.com</option>
                    <option value="maxmind" <?php selected($geoip_service, 'maxmind'); ?>>MaxMind GeoIP2</option>
                </select>
                <p class="description"><?php esc_html_e('Provider used to detect visitor countries for geo-whitelisting and blacklisting.', 'wppoppop'); ?></p>
            </td>
        </tr>
    </table>
</div>
