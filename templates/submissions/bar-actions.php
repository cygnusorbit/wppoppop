<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="cell actions">
    <button type="button" class="wpp-bar-btn is-primary" id="wppoppop-btn-export-csv">
        <span class="dashicons dashicons-download"></span><span class="wpp-bar-label"><?php echo esc_html__('Export to CSV', 'wppoppop'); ?></span>
    </button>
    <button type="button" class="wpp-bar-btn is-icon" id="wppoppop-table-options-btn" title="<?php echo esc_attr__('Table options', 'wppoppop'); ?>" aria-label="<?php echo esc_attr__('Table options', 'wppoppop'); ?>">
        <span class="dashicons dashicons-admin-generic"></span>
    </button>
</div>
