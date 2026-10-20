<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-card-box wpp-tools-card">
    <div class="wpp-tools-card-head">
        <span class="dashicons dashicons-admin-generic"></span>
        <h3><?php esc_html_e('Restore Options & Maintenance', 'wppoppop'); ?></h3>
    </div>
    <div id="wppoppop-maint-notice" class="wpp-tools-notice" hidden></div>

    <div class="wpp-tools-grid">
        <div class="wpp-tools-panel">
            <h4><?php esc_html_e('Restore options', 'wppoppop'); ?></h4>
            <p class="wpp-tools-help"><?php esc_html_e('Applied the next time you restore a backup above. Original embed UIDs are kept unless they already exist.', 'wppoppop'); ?></p>
            <label class="wpp-tools-check"><input type="checkbox" id="wppoppop-import-dryrun"> <?php esc_html_e('Preview only (dry run, nothing is written)', 'wppoppop'); ?></label>
            <label class="wpp-tools-check"><input type="checkbox" id="wppoppop-import-settings"> <?php esc_html_e('Also restore plugin settings and theme', 'wppoppop'); ?></label>
        </div>

        <div class="wpp-tools-panel">
            <h4><?php esc_html_e('Purge old logs', 'wppoppop'); ?></h4>
            <p class="wpp-tools-help"><?php esc_html_e('Delete activity-log entries older than the chosen number of days.', 'wppoppop'); ?></p>
            <div class="wpp-tools-row">
                <input type="number" id="wppoppop-purge-days" value="30" min="1" max="3650" class="small-text">
                <button type="button" class="button" data-wpp-maint="wppoppop_purge_logs"><?php esc_html_e('Purge logs', 'wppoppop'); ?></button>
            </div>
        </div>

        <div class="wpp-tools-panel">
            <h4><?php esc_html_e('Optimize tables', 'wppoppop'); ?></h4>
            <p class="wpp-tools-help"><?php esc_html_e('Runs OPTIMIZE TABLE on the plugin tables to reclaim space.', 'wppoppop'); ?></p>
            <button type="button" class="button" data-wpp-maint="wppoppop_optimize_tables"><?php esc_html_e('Optimize now', 'wppoppop'); ?></button>
        </div>

        <div class="wpp-tools-panel">
            <h4><?php esc_html_e('Orphan check', 'wppoppop'); ?></h4>
            <p class="wpp-tools-help"><?php esc_html_e('Counts submissions and transactions whose popup no longer exists. Read-only.', 'wppoppop'); ?></p>
            <button type="button" class="button" data-wpp-maint="wppoppop_orphan_report"><?php esc_html_e('Run report', 'wppoppop'); ?></button>
        </div>
    </div>
</div>
