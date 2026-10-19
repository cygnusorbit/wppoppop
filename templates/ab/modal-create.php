<?php
if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$available_popups = $wpdb->get_results("SELECT uid, title FROM {$wpdb->prefix}wppoppop_items WHERE status = 'publish' ORDER BY id DESC");
?>
<div class="wpp-modal-backdrop" id="wppoppop-create-ab-modal">
    <div class="wpp-modal-card">
        <div class="wpp-modal-head">
            <h3><?php esc_html_e('Create A/B Split Testing Experiment', 'wppoppop'); ?></h3>
            <button type="button" class="wpp-modal-close" id="wppoppop-create-ab-close" aria-label="<?php esc_attr_e('Close', 'wppoppop'); ?>">&times;</button>
        </div>
        <div class="wpp-modal-body">
            <label for="wppoppop-ab-title-input"><?php esc_html_e('Campaign Title', 'wppoppop'); ?></label>
            <input type="text" id="wppoppop-ab-title-input" class="widefat" placeholder="e.g. Summer Promo Headline Test">

            <label class="wpp-modal-sep"><?php esc_html_e('Select Popups for Testing (Minimum 2)', 'wppoppop'); ?></label>
            <p class="description"><?php esc_html_e('Traffic will be distributed randomly across your selected variations.', 'wppoppop'); ?></p>

            <div class="wpp-modal-list">
                <?php if (empty($available_popups)) : ?>
                    <p class="wpp-ab-none"><?php esc_html_e('No published popups available. Please create and publish popups in the builder first.', 'wppoppop'); ?></p>
                <?php else : ?>
                    <?php foreach ($available_popups as $ap) : ?>
                        <label class="wpp-modal-item">
                            <input type="checkbox" class="wppoppop-ab-popup-checkbox" value="<?php echo esc_attr($ap->uid); ?>">
                            <span><strong><?php echo esc_html($ap->title); ?></strong> <code>(<?php echo esc_html($ap->uid); ?>)</code></span>
                        </label>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="wpp-modal-foot">
            <button type="button" class="button" id="wppoppop-create-ab-cancel"><?php esc_html_e('Cancel', 'wppoppop'); ?></button>
            <button type="button" class="button button-primary" id="wppoppop-create-ab-submit"><?php esc_html_e('Launch Campaign', 'wppoppop'); ?></button>
        </div>
    </div>
</div>
