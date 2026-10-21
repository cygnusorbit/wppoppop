<?php
if (!defined('ABSPATH')) {
    exit;
}
/** Label row: label on the left, small merge-tag icon button (dashicons-shortcode f150) on the right. */
$wppoppop_dopt_label = function ($for, $text, $target = '') {
    $btn = '';
    if ($target !== '') {
        $btn = '<button type="button" class="button button-small wppoppop-dopt-tagbtn" data-target="' . esc_attr($target) . '" title="' . esc_attr__('Insert merge tag', 'wppoppop') . '" aria-label="' . esc_attr__('Insert merge tag', 'wppoppop') . '" aria-haspopup="true" aria-expanded="false" style="display:inline-flex;align-items:center;justify-content:center;min-height:22px;height:22px;line-height:1;padding:0 5px;margin:0 0 0 auto;"><span class="dashicons" aria-hidden="true" style="display:inline-flex;align-items:center;justify-content:center;width:16px;height:16px;font-size:16px;line-height:1;margin:0;padding:0;">&#xf150;</span></button>';
    }
    return '<div class="wppoppop-dopt-lblrow" style="position:relative;display:flex;align-items:center;gap:6px;margin:0 0 4px;"><label for="' . esc_attr($for) . '" style="font-weight:600;">' . esc_html($text) . '</label>' . $btn . '</div>';
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

        <div style="margin:0 0 10px;">
            <?php echo $wppoppop_dopt_label('set-dopt-recipient', __('Recipient', 'wppoppop'), '#set-dopt-recipient'); ?>
            <input type="text" id="set-dopt-recipient" class="widefat" placeholder="<?php esc_attr_e('Empty = the visitor email', 'wppoppop'); ?>">
        </div>

        <div style="margin:0 0 10px;">
            <?php echo $wppoppop_dopt_label('set-dopt-subject', __('Subject', 'wppoppop'), '#set-dopt-subject'); ?>
            <input type="text" id="set-dopt-subject" class="widefat" value="Please confirm your email address">
        </div>

        <div style="margin:0 0 10px;">
            <?php echo $wppoppop_dopt_label('set-dopt-message', __('Message', 'wppoppop'), '#set-dopt-message'); ?>
            <textarea id="set-dopt-message" rows="6" class="widefat"><?php echo esc_textarea("Dear visitor!\n\nPlease confirm your email address by clicking the following link:\n<a href=\"{{confirmation-url}}\">{{confirmation-url}}</a>\n\nThanks."); ?></textarea>
        </div>

        <div style="margin:0 0 10px;">
            <?php echo $wppoppop_dopt_label('set-dopt-from-email', __('From', 'wppoppop')); ?>
            <div style="display:flex;gap:6px;">
                <input type="text" id="set-dopt-from-email" class="widefat" value="{{global-from-email}}" placeholder="email">
                <input type="text" id="set-dopt-from-name" class="widefat" value="{{global-from-name}}" placeholder="name">
            </div>
        </div>

        <div style="margin:0 0 10px;">
            <?php echo $wppoppop_dopt_label('set-dopt-thank-message', __('Thank-you message', 'wppoppop'), '#set-dopt-thank-message'); ?>
            <textarea id="set-dopt-thank-message" rows="4" class="widefat"><?php echo esc_textarea('<h4 style="text-align: center;">Thank you!</h4><p style="text-align: center;">Your email address successfully confirmed.</p>'); ?></textarea>
        </div>

        <div style="margin:0;">
            <?php echo $wppoppop_dopt_label('set-dopt-thank-url', __('Thank-you URL', 'wppoppop')); ?>
            <input type="url" id="set-dopt-thank-url" class="widefat" placeholder="https://">
            <p class="description" style="margin:4px 0 0;"><?php esc_html_e('If set, the visitor is redirected here after confirming (instead of the message).', 'wppoppop'); ?></p>
        </div>
    </div>
</div>
