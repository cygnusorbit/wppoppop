<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-card-box" style="background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;padding:20px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;border-bottom:1px solid #f1f5f9;padding-bottom:10px;">
        <span class="dashicons dashicons-download" style="font-size:20px;width:20px;height:20px;color:#8b5cf6;"></span>
        <h3 style="margin:0;font-size:16px;font-weight:700;color:#1e293b;">Bulk Backup & Template Portability</h3>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">
        <!-- Bulk Export Container -->
        <div style="background:#f8fafc;padding:18px;border-radius:8px;border:1px solid #e2e8f0;display:flex;flex-direction:column;">
            <h4 style="margin:0 0 6px 0;font-size:14px;color:#1e293b;">Export All Campaigns (Bulk JSON)</h4>
            <p style="margin:0 0 14px 0;font-size:12px;color:#64748b;line-height:1.4;flex:1;">
                Download a complete, unified JSON archive containing all published popup designs, elements, screen sequences, and settings.
            </p>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" id="wppoppop-bulk-export-form" style="margin:0;">
                <input type="hidden" name="action" value="wppoppop_bulk_export">
                <?php wp_nonce_field('wppoppop_tools_action', 'wppoppop_tools_nonce'); ?>
                <button type="submit" name="wppoppop_bulk_export" id="wppoppop-bulk-export-btn" class="button button-primary" style="background:#8b5cf6;border-color:#7c3aed;display:inline-flex;align-items:center;gap:6px;">
                    <span class="dashicons dashicons-download" style="font-size:16px;width:16px;height:16px;"></span> Export Complete Backup
                </button>
            </form>
        </div>

        <!-- Bulk Import Container -->
        <div style="background:#f8fafc;padding:18px;border-radius:8px;border:1px solid #e2e8f0;display:flex;flex-direction:column;">
            <h4 style="margin:0 0 6px 0;font-size:14px;color:#1e293b;">Restore / Import Bulk Archive</h4>
            <p style="margin:0 0 14px 0;font-size:12px;color:#64748b;line-height:1.4;">
                Select a previously exported WpPopPop bulk JSON archive to restore all popup configurations and A/B campaigns into this site.
            </p>
            <form method="post" enctype="multipart/form-data" id="wppoppop-bulk-import-form" style="margin:0;">
                <?php wp_nonce_field('wppoppop_tools_action', 'wppoppop_tools_nonce'); ?>
                <input type="file" name="wppoppop_bulk_import_file" id="wppoppop-bulk-import-file" accept=".json,application/json" required style="width:100%;margin-bottom:12px;font-size:12px;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <button type="submit" name="wppoppop_bulk_import" id="wppoppop-bulk-import-btn" class="button button-secondary" style="display:inline-flex;align-items:center;gap:6px;">
                        <span class="dashicons dashicons-upload" style="font-size:16px;width:16px;height:16px;"></span> Restore Bulk Backup
                    </button>
                    <span id="wppoppop-import-status-text" style="font-size:12px;color:#64748b;display:none;"></span>
                </div>
            </form>
        </div>
    </div>
</div>
