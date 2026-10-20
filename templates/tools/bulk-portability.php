<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-card-box wpp-tools-card">
    <div class="wpp-tools-card-head">
        <span class="dashicons dashicons-download"></span>
        <h3><?php esc_html_e('Bulk Backup & Template Portability', 'wppoppop'); ?></h3>
    </div>

    <div class="wpp-tools-grid">
        <div class="wpp-tools-panel">
            <h4><?php esc_html_e('Export All Campaigns (Bulk JSON)', 'wppoppop'); ?></h4>
            <p class="wpp-tools-help"><?php esc_html_e('Download a complete JSON archive containing all popup designs, A/B campaigns, plugin settings and the global theme.', 'wppoppop'); ?></p>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" id="wppoppop-bulk-export-form">
                <input type="hidden" name="action" value="wppoppop_bulk_export">
                <input type="hidden" name="wppoppop_tools_nonce" value="<?php echo esc_attr(wp_create_nonce('wppoppop_tools_action')); ?>"><?php wp_referer_field(); ?>
                <button type="submit" name="wppoppop_bulk_export" id="wppoppop-bulk-export-btn" class="button button-primary wpp-tools-btn"><span class="dashicons dashicons-download"></span><?php esc_html_e('Export Complete Backup', 'wppoppop'); ?></button>
            </form>
        </div>

        <div class="wpp-tools-panel">
            <h4><?php esc_html_e('Restore / Import Bulk Archive', 'wppoppop'); ?></h4>
            <p class="wpp-tools-help"><?php esc_html_e('Select a previously exported WpPopPop bulk JSON archive (10 MB maximum). Restore is all-or-nothing: if the database rejects any entry, nothing is kept.', 'wppoppop'); ?></p>
            <form method="post" enctype="multipart/form-data" id="wppoppop-bulk-import-form">
                <input type="hidden" name="wppoppop_tools_nonce" value="<?php echo esc_attr(wp_create_nonce('wppoppop_tools_action')); ?>"><?php wp_referer_field(); ?>
                <input type="file" name="wppoppop_bulk_import_file" id="wppoppop-bulk-import-file" class="wpp-tools-file" accept=".json,application/json" required>
                <label class="wpp-tools-check"><input type="checkbox" name="import_settings" value="1"> <?php esc_html_e('Also restore plugin settings and theme', 'wppoppop'); ?></label>
                <div class="wpp-tools-row">
                    <button type="submit" name="wppoppop_bulk_import" id="wppoppop-bulk-import-btn" class="button wpp-tools-btn"><span class="dashicons dashicons-upload"></span><?php esc_html_e('Restore Bulk Backup', 'wppoppop'); ?></button>
                    <span id="wppoppop-import-status-text" class="wpp-tools-status" hidden></span>
                </div>
            </form>
        </div>
    </div>
</div>
