<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="cell actions">
    <button type="button" class="wpp-bar-btn" id="wppoppop-btn-open-import">
        <span class="dashicons dashicons-upload"></span><?php esc_html_e('Import JSON', 'wppoppop'); ?>
    </button>
    <a href="<?php echo esc_url(admin_url('admin.php?page=wppoppop-builder')); ?>" class="wpp-bar-btn is-primary" id="wppoppop-btn-create-popup">
        <span class="dashicons dashicons-plus-alt2"></span><?php esc_html_e('Create Popup', 'wppoppop'); ?>
    </a>
</div>
