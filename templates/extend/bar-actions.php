<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="cell actions">
    <a class="wpp-bar-btn" href="<?php echo esc_url(admin_url('admin.php?page=wppoppop-settings#module')); ?>"><span class="dashicons dashicons-admin-generic"></span><?php esc_html_e('Manage Modules', 'wppoppop'); ?></a>
    <?php if (!empty($ext_can_upload)) : ?>
        <button type="button" class="wpp-bar-btn is-primary" id="wpp-ext-bar-upload"><span class="dashicons dashicons-upload"></span><?php esc_html_e('Upload Module', 'wppoppop'); ?></button>
    <?php endif; ?>
</div>
