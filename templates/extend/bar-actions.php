<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="cell actions">
    <button type="button" class="wpp-bar-btn" id="wpp-mod-cats-btn" aria-haspopup="dialog"><span class="dashicons dashicons-category"></span><?php esc_html_e('Module Category', 'wppoppop'); ?></button> <!-- WPPOPPOP_CATS_ON_EXTEND -->
    <a class="wpp-bar-btn" href="<?php echo esc_url(admin_url('admin.php?page=wppoppop-settings#module')); ?>"><span class="dashicons dashicons-admin-generic"></span><?php esc_html_e('Manage Modules', 'wppoppop'); ?></a>
    <?php if (!empty($ext_can_upload)) : ?>
        <button type="button" class="wpp-bar-btn is-primary" id="wpp-ext-bar-upload"><span class="dashicons dashicons-upload"></span><?php esc_html_e('Upload Module', 'wppoppop'); ?></button>
    <?php endif; ?>
</div>
