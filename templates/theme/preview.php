<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-box wpp-theme-box wpp-theme-preview">
    <h3><?php esc_html_e('Live Preview', 'wppoppop'); ?></h3>
    <p class="description"><?php esc_html_e('Changes apply instantly to this whole page before saving.', 'wppoppop'); ?></p>
    <div class="wppoppop-kpi-card"><span class="kpi-label"><?php esc_html_e('Views', 'wppoppop'); ?></span><span class="kpi-val">1,284</span></div>
    <p>
        <a href="#" onclick="return false;"><?php esc_html_e('Sample link', 'wppoppop'); ?></a>
        <span class="wppoppop-status-badge status-publish"><?php esc_html_e('Publish', 'wppoppop'); ?></span>
        <span class="wppoppop-status-badge status-draft"><?php esc_html_e('Draft', 'wppoppop'); ?></span>
        <span class="wppoppop-shortcode-chip"><?php esc_html_e('[wppoppop id="abc"]', 'wppoppop'); ?></span>
    </p>
    <p>
        <button type="button" class="button button-primary"><?php esc_html_e('Primary', 'wppoppop'); ?></button>
        <button type="button" class="button"><?php esc_html_e('Secondary', 'wppoppop'); ?></button>
        <button type="button" class="button wpp-btn-danger"><?php esc_html_e('Danger', 'wppoppop'); ?></button>
    </p>
    <p><input type="text" placeholder="<?php esc_attr_e('Text input', 'wppoppop'); ?>"> <select><option><?php esc_html_e('Select', 'wppoppop'); ?></option></select></p>
    <table class="wp-list-table widefat striped">
        <thead><tr><th><?php esc_html_e('Name', 'wppoppop'); ?></th><th><?php esc_html_e('Status', 'wppoppop'); ?></th></tr></thead>
        <tbody><tr><td><?php esc_html_e('Row one', 'wppoppop'); ?></td><td><?php esc_html_e('Active', 'wppoppop'); ?></td></tr><tr><td><?php esc_html_e('Row two', 'wppoppop'); ?></td><td><?php esc_html_e('Draft', 'wppoppop'); ?></td></tr></tbody>
    </table>
</div>
