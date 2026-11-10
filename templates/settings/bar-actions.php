<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="cell actions">
    <div class="wppoppop-settings-search" role="search">
        <label class="screen-reader-text" for="wppoppop-settings-search"><?php esc_html_e('Search settings', 'wppoppop'); ?></label>
        <span class="dashicons dashicons-search" aria-hidden="true"></span>
        <input type="text" id="wppoppop-settings-search" class="wppoppop-settings-search-input" placeholder="<?php echo esc_attr__('Search settings...  ( / )', 'wppoppop'); ?>" autocomplete="off" autocapitalize="off" spellcheck="false" aria-controls="wppoppop-settings-form">
        <span id="wppoppop-settings-search-count" class="wpp-s-count" aria-live="polite"></span>
        <button type="button" id="wppoppop-settings-search-clear" class="wpp-s-clear" aria-label="<?php echo esc_attr__('Clear search', 'wppoppop'); ?>" hidden>&times;</button>
    </div>
    <span id="wppoppop-bar-save-msg" class="wpp-bar-msg" aria-live="polite"></span>
    <button type="submit" class="wpp-bar-btn is-primary" form="wppoppop-settings-form" id="wppoppop-bar-save-btn"><span class="dashicons dashicons-yes"></span><?php esc_html_e('Save Settings', 'wppoppop'); ?></button>
</div>
