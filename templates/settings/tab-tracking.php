<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- template variables are local to the method that includes this file
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
    </table>

    <?php do_action('wppoppop_tracking_tab_sections', $t, $chk, $num); ?>

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

    <h3><?php esc_html_e('Debug', 'wppoppop'); ?></h3>
    <table class="form-table">
        <?php $chk('debug_ab', __('Admin Debug Badge', 'wppoppop'), __('Show tracking and A/B variant status on the front end (administrators only)', 'wppoppop')); ?>
    </table>
</div>
