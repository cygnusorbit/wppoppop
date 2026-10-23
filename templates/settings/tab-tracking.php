<?php
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('WpPopPop_Tracking')) {
    require_once WPPOPPOP_PATH . 'includes/class-wppoppop-tracking.php';
}
$t = function ($k) { return WpPopPop_Tracking::get($k); };

/* Checkbox row: hidden 0 + checkbox 1 so an unchecked box really saves as off */
$chk = function ($key, $title, $label, $desc = '') use ($t) {
    ?>
    <tr>
        <th scope="row"><?php echo esc_html($title); ?></th>
        <td>
            <input type="hidden" name="settings[<?php echo esc_attr($key); ?>]" value="0">
            <label><input type="checkbox" name="settings[<?php echo esc_attr($key); ?>]" value="1" <?php checked(!empty($t($key))); ?>> <?php echo esc_html($label); ?></label>
            <?php if ($desc !== '') : ?><p class="description"><?php echo esc_html($desc); ?></p><?php endif; ?>
        </td>
    </tr>
    <?php
};
$num = function ($key, $title, $min, $max, $unit = '', $desc = '') use ($t) {
    ?>
    <tr>
        <th scope="row"><label for="wpp-<?php echo esc_attr($key); ?>"><?php echo esc_html($title); ?></label></th>
        <td>
            <input type="number" id="wpp-<?php echo esc_attr($key); ?>" name="settings[<?php echo esc_attr($key); ?>]" value="<?php echo esc_attr($t($key)); ?>" min="<?php echo (int) $min; ?>" max="<?php echo (int) $max; ?>" class="small-text"> <?php echo esc_html($unit); ?>
            <?php if ($desc !== '') : ?><p class="description"><?php echo esc_html($desc); ?></p><?php endif; ?>
        </td>
    </tr>
    <?php
};
?>
<div class="wppoppop-settings-section">
    <h3><?php esc_html_e('Impression Tracking', 'wppoppop'); ?></h3>
    <table class="form-table">
        <?php $chk('track_impressions', __('Count Impressions', 'wppoppop'), __('Record an impression each time a popup is shown', 'wppoppop')); ?>
        <?php $chk('track_once_session', __('Once Per Session', 'wppoppop'), __('Count each popup once per browser session', 'wppoppop')); ?>
        <?php $chk('track_exclude_admins', __('Exclude Administrators', 'wppoppop'), __('Do not count views from logged-in administrators', 'wppoppop')); ?>
        <?php $chk('track_exclude_bots', __('Exclude Bots', 'wppoppop'), __('Ignore crawlers and headless browsers (server-side)', 'wppoppop')); ?>
        <?php $num('track_retention_days', __('Daily Stats Retention', 'wppoppop'), 0, 3650, __('days (0 = keep forever)', 'wppoppop')); ?>
    </table>

    <h3><?php esc_html_e('A/B Testing', 'wppoppop'); ?></h3>
    <table class="form-table">
        <tr>
            <th scope="row"><label for="wpp-ab_default_mode"><?php esc_html_e('Default Embed Type', 'wppoppop'); ?></label></th>
            <td>
                <select id="wpp-ab_default_mode" name="settings[ab_default_mode]">
                    <option value="inline" <?php selected($t('ab_default_mode'), 'inline'); ?>><?php esc_html_e('Inline Popup', 'wppoppop'); ?></option>
                    <option value="onload" <?php selected($t('ab_default_mode'), 'onload'); ?>><?php esc_html_e('On-Load Popup', 'wppoppop'); ?></option>
                </select>
                <p class="description"><?php esc_html_e('Used by [wppoppop_ab] when the shortcode has no mode attribute.', 'wppoppop'); ?></p>
            </td>
        </tr>
        <?php $num('ab_cookie_days', __('Variant Cookie Lifetime', 'wppoppop'), 1, 365, __('days', 'wppoppop')); ?>
        <?php $chk('ab_sticky_user', __('Sticky Variant by User', 'wppoppop'), __('Logged-in visitors always see the same variant on every device', 'wppoppop')); ?>
        <?php $chk('ab_auto_promote', __('Auto-Promote Winner', 'wppoppop'), __('Keep only the best variant once the result is statistically significant', 'wppoppop'), __('Uses each popup\'s lifetime impressions and leads. Checked daily.', 'wppoppop')); ?>
        <?php $num('ab_promote_min_impr', __('Minimum Impressions per Variant', 'wppoppop'), 50, 100000); ?>
        <tr>
            <th scope="row"><label for="wpp-ab_promote_confidence"><?php esc_html_e('Confidence Level', 'wppoppop'); ?></label></th>
            <td>
                <select id="wpp-ab_promote_confidence" name="settings[ab_promote_confidence]">
                    <?php foreach ([90, 95, 99] as $c) : ?>
                        <option value="<?php echo (int) $c; ?>" <?php selected((int) $t('ab_promote_confidence'), $c); ?>><?php echo (int) $c; ?>%</option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
    </table>

    <h3><?php esc_html_e('Cache Compatibility', 'wppoppop'); ?></h3>
    <table class="form-table">
        <?php $chk('cache_nocache_ab', __('Exclude A/B Pages from Page Cache', 'wppoppop'), __('Send the DONOTCACHEPAGE flag on pages that contain [wppoppop_ab]', 'wppoppop'), __('Honoured by WP Rocket, W3 Total Cache, LiteSpeed and WP Super Cache.', 'wppoppop')); ?>
    </table>

    <h3><?php esc_html_e('Privacy', 'wppoppop'); ?></h3>
    <table class="form-table">
        <?php $chk('privacy_anonymize_ip', __('Anonymise IP Addresses', 'wppoppop'), __('Store lead IPs with the last part removed', 'wppoppop')); ?>
        <?php $chk('privacy_respect_dnt', __('Respect Do-Not-Track', 'wppoppop'), __('Do not count impressions when the browser sends DNT', 'wppoppop')); ?>
        <tr>
            <th scope="row"><label for="wpp-privacy_consent_cookie"><?php esc_html_e('Consent Cookie Name', 'wppoppop'); ?></label></th>
            <td>
                <input type="text" id="wpp-privacy_consent_cookie" name="settings[privacy_consent_cookie]" value="<?php echo esc_attr($t('privacy_consent_cookie')); ?>" class="regular-text" autocomplete="off">
                <p class="description"><?php esc_html_e('If set, impressions are only counted while this cookie exists (e.g. cookie_notice_accepted).', 'wppoppop'); ?></p>
            </td>
        </tr>
    </table>

    <h3><?php esc_html_e('Frequency Capping (global)', 'wppoppop'); ?></h3>
    <table class="form-table">
        <?php $num('freq_max_per_page', __('Max Popups per Page', 'wppoppop'), 0, 20, __('(0 = unlimited)', 'wppoppop')); ?>
        <?php $num('freq_min_gap', __('Minimum Gap Between Popups', 'wppoppop'), 0, 3600, __('seconds (0 = off)', 'wppoppop')); ?>
    </table>

    <h3><?php esc_html_e('Rate Limiting & CORS', 'wppoppop'); ?></h3>
    <table class="form-table">
        <?php $num('rate_limit_impr', __('Impression Requests per IP', 'wppoppop'), 1, 600, __('per minute', 'wppoppop')); ?>
        <?php $num('rate_limit_submit', __('Lead Submissions per IP', 'wppoppop'), 1, 120, __('per minute', 'wppoppop')); ?>
        <tr>
            <th scope="row"><label for="wpp-cors_origins"><?php esc_html_e('Allowed Origins', 'wppoppop'); ?></label></th>
            <td>
                <textarea id="wpp-cors_origins" name="settings[cors_origins]" rows="3" class="large-text code" placeholder="https://partner.example.com"><?php echo esc_textarea($t('cors_origins')); ?></textarea>
                <p class="description"><?php esc_html_e('One origin per line for cross-site embeds. Empty = allow any origin (current behaviour).', 'wppoppop'); ?></p>
            </td>
        </tr>
    </table>

    <h3><?php esc_html_e('Debug & Reporting', 'wppoppop'); ?></h3>
    <table class="form-table">
        <?php $chk('debug_ab', __('Admin Debug Badge', 'wppoppop'), __('Show tracking and A/B variant status on the front end (administrators only)', 'wppoppop')); ?>
        <?php $chk('report_enabled', __('Weekly Email Report', 'wppoppop'), __('Email impressions, leads and CR per campaign every week', 'wppoppop')); ?>
        <tr>
            <th scope="row"><label for="wpp-report_email"><?php esc_html_e('Report Recipient', 'wppoppop'); ?></label></th>
            <td><input type="email" id="wpp-report_email" name="settings[report_email]" value="<?php echo esc_attr($t('report_email')); ?>" class="regular-text" placeholder="<?php echo esc_attr(get_option('admin_email')); ?>"></td>
        </tr>
    </table>
</div>
