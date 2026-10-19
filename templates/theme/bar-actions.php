<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="cell actions">
    <input type="search" id="wpp-theme-search" placeholder="<?php esc_attr_e('Search settings...', 'wppoppop'); ?>">
    <button type="button" class="wpp-bar-btn" id="wpp-theme-reset-form"><span class="dashicons dashicons-undo"></span><?php esc_html_e('Revert', 'wppoppop'); ?></button>
    <button type="submit" class="wpp-bar-btn is-primary" form="wpp-theme-form"><span class="dashicons dashicons-yes"></span><?php esc_html_e('Save Theme', 'wppoppop'); ?></button>
</div>
