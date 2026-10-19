<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wpp-theme-box wpp-theme-preview">
    <h3><?php esc_html_e('Live Preview', 'wppoppop'); ?></h3>
    <p class="description"><?php esc_html_e('Changes apply instantly to this whole page before saving.', 'wppoppop'); ?></p>
    <div class="wppoppop-kpi-card"><span class="kpi-label">Views</span><span class="kpi-val">1,284</span></div>
    <p>
        <a href="#" onclick="return false;">Sample link</a>
        <span class="wppoppop-status-badge status-publish">Publish</span>
        <span class="wppoppop-status-badge status-draft">Draft</span>
        <span class="wppoppop-shortcode-chip">[wppoppop id="abc"]</span>
    </p>
    <p>
        <button type="button" class="button button-primary">Primary</button>
        <button type="button" class="button">Secondary</button>
        <button type="button" class="button wpp-btn-danger">Danger</button>
    </p>
    <p><input type="text" placeholder="Text input"> <select><option>Select</option></select></p>
    <table class="wp-list-table widefat striped">
        <thead><tr><th>Name</th><th>Status</th></tr></thead>
        <tbody><tr><td>Row one</td><td>Active</td></tr><tr><td>Row two</td><td>Draft</td></tr></tbody>
    </table>
</div>
