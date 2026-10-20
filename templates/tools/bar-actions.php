<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="cell actions">
    <button type="button" class="wpp-bar-btn" data-wpp-trigger="#wppoppop-copy-system-info-btn"><span class="dashicons dashicons-clipboard"></span><?php esc_html_e('Copy Report', 'wppoppop'); ?></button>
    <button type="button" class="wpp-bar-btn" data-wpp-trigger="#wppoppop-repair-tables-btn"><span class="dashicons dashicons-admin-tools"></span><?php esc_html_e('Verify Schema', 'wppoppop'); ?></button>
    <button type="button" class="wpp-bar-btn is-primary" data-wpp-trigger="#wppoppop-bulk-export-btn"><span class="dashicons dashicons-download"></span><?php esc_html_e('Export Backup', 'wppoppop'); ?></button>
</div>
