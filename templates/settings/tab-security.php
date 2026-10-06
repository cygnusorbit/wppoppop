<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-settings-section">
    <h3>Geolocation & Lead Verification</h3>
    <table class="form-table">
        <tr>
            <th scope="row"><label for="email_validation">Email Validation Mode</label></th>
            <td>
                <select name="email_validation" id="email_validation">
                    <option value="basic" <?php selected($settings['email_validation'] ?? 'basic', 'basic'); ?>>Standard RFC Syntax Check</option>
                    <option value="disposable_filter" <?php selected($settings['email_validation'] ?? 'basic', 'disposable_filter'); ?>>Syntax Check + Block Disposable Domains</option>
                </select>
                <p class="description">Prevent fraudulent or temporary email addresses (e.g., Mailinator, 10MinuteMail) from registering.</p>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="geoip_service">Geolocation Service</label></th>
            <td>
                <select name="geoip_service" id="geoip_service">
                    <option value="none" <?php selected($settings['geoip_service'] ?? 'none', 'none'); ?>>None (Server Headers Only: Cloudflare / Proxy)</option>
                    <option value="ipapi" <?php selected($settings['geoip_service'] ?? 'none', 'ipapi'); ?>>ip-api.com (IP Lookup Service)</option>
                    <option value="maxmind" <?php selected($settings['geoip_service'] ?? 'none', 'maxmind'); ?>>MaxMind GeoIP2 Integration</option>
                </select>
                <p class="description">Provider used to detect visitor countries for geo-whitelisting and blacklisting.</p>
            </td>
        </tr>
    </table>
</div>
