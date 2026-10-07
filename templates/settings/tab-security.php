<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-settings-section">
    <h3><?php esc_html_e('Geolocation & Lead Verification', 'wppoppop'); ?></h3>
    <table class="form-table">
        <tr>
            <th scope="row"><label for="email_validation"><?php esc_html_e('Email Validation Mode', 'wppoppop'); ?></label></th>
            <td>
                <select name="wppoppop_settings[email_validation]" id="email_validation">
                    <option value="basic" <?php selected($settings['email_validation'] ?? 'basic', 'basic'); ?>><?php esc_html_e('Standard RFC Syntax Check', 'wppoppop'); ?></option>
                    <option value="disposable_filter" <?php selected($settings['email_validation'] ?? 'basic', 'disposable_filter'); ?>><?php esc_html_e('Syntax Check + Block Disposable Domains', 'wppoppop'); ?></option>
                </select>
                <p class="description"><?php esc_html_e('Prevent fraudulent or temporary email addresses from registering.', 'wppoppop'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="geoip_service"><?php esc_html_e('Geolocation Service', 'wppoppop'); ?></label></th>
            <td>
                <select name="wppoppop_settings[geoip_service]" id="geoip_service">
                    <option value="none" <?php selected($settings['geoip_service'] ?? 'none', 'none'); ?>><?php esc_html_e('None (Server Headers Only: Cloudflare / Proxy)', 'wppoppop'); ?></option>
                    <option value="ipapi" <?php selected($settings['geoip_service'] ?? 'none', 'ipapi'); ?>><?php esc_html_e('ip-api.com (IP Lookup Service)', 'wppoppop'); ?></option>
                    <option value="maxmind" <?php selected($settings['geoip_service'] ?? 'none', 'maxmind'); ?>><?php esc_html_e('MaxMind GeoIP2 Integration', 'wppoppop'); ?></option>
                </select>
                <p class="description"><?php esc_html_e('Provider used to detect visitor countries for geo-whitelisting and blacklisting.', 'wppoppop'); ?></p>
            </td>
        </tr>
    </table>
</div>
