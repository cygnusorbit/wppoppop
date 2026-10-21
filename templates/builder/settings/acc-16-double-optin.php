<?php
if (!defined('ABSPATH')) {
    exit;
}
$wppoppop_dopt_tags = function ($target) {
    return '<select class="wppoppop-dopt-tag" data-target="' . esc_attr($target) . '" title="' . esc_attr__('Insert merge tag', 'wppoppop') . '" aria-label="' . esc_attr__('Insert merge tag', 'wppoppop') . '" style="max-width:110px;margin-left:6px;"><option value="">&lt;/&gt; Tags</option></select>';
};
?>
<!-- 16. Double Opt-In -->
<div class="wppoppop-acc-item" id="wppoppop-dopt-acc">
    <button type="button" class="wppoppop-acc-header">16. Double Opt-In</button>
    <div class="wppoppop-acc-body" style="display:none;">
        <label style="display:flex;align-items:center;gap:8px;font-weight:600;margin-bottom:10px;">
            <input type="checkbox" id="set-dopt-enable" value="1"> <?php esc_html_e('Enable Double Opt-In', 'wppoppop'); ?>
        </label>
        <p class="description" style="margin:0 0 10px;"><?php esc_html_e('Leads stay "pending" until the visitor clicks the confirmation link.', 'wppoppop'); ?></p>

        <label for="set-dopt-recipient" style="font-weight:600;"><?php esc_html_e('Recipient', 'wppoppop'); ?></label>
        <div style="display:flex;align-items:center;margin:4px 0 10px;">
            <input type="text" id="set-dopt-recipient" class="widefat" placeholder="<?php esc_attr_e('Empty = the visitor email', 'wppoppop'); ?>">
            <?php echo $wppoppop_dopt_tags('#set-dopt-recipient'); ?>
        </div>

        <label for="set-dopt-subject" style="font-weight:600;"><?php esc_html_e('Subject', 'wppoppop'); ?></label>
        <div style="display:flex;align-items:center;margin:4px 0 10px;">
            <input type="text" id="set-dopt-subject" class="widefat" value="Please confirm your email address">
            <?php echo $wppoppop_dopt_tags('#set-dopt-subject'); ?>
        </div>

        <label for="set-dopt-message" style="font-weight:600;"><?php esc_html_e('Message', 'wppoppop'); ?></label>
        <div style="margin:4px 0 10px;">
            <textarea id="set-dopt-message" rows="6" class="widefat"><?php echo esc_textarea("Dear visitor!\n\nPlease confirm your email address by clicking the following link:\n<a href=\"{{confirmation-url}}\">{{confirmation-url}}</a>\n\nThanks."); ?></textarea>
            <?php echo $wppoppop_dopt_tags('#set-dopt-message'); ?>
        </div>

        <label style="font-weight:600;"><?php esc_html_e('From', 'wppoppop'); ?></label>
        <div style="display:flex;gap:6px;margin:4px 0 10px;">
            <input type="text" id="set-dopt-from-email" class="widefat" value="{{global-from-email}}" placeholder="email">
            <input type="text" id="set-dopt-from-name" class="widefat" value="{{global-from-name}}" placeholder="name">
        </div>

        <label for="set-dopt-thank-message" style="font-weight:600;"><?php esc_html_e('Thank-you message', 'wppoppop'); ?></label>
        <div style="margin:4px 0 10px;">
            <textarea id="set-dopt-thank-message" rows="4" class="widefat"><?php echo esc_textarea('<h4 style="text-align: center;">Thank you!</h4><p style="text-align: center;">Your email address successfully confirmed.</p>'); ?></textarea>
            <?php echo $wppoppop_dopt_tags('#set-dopt-thank-message'); ?>
        </div>

        <label for="set-dopt-thank-url" style="font-weight:600;"><?php esc_html_e('Thank-you URL', 'wppoppop'); ?></label>
        <input type="url" id="set-dopt-thank-url" class="widefat" style="margin:4px 0 4px;" placeholder="https://">
        <p class="description" style="margin:0;"><?php esc_html_e('If set, the visitor is redirected here after confirming (instead of the message).', 'wppoppop'); ?></p>
    </div>
</div>
