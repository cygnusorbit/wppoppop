<?php
if (!defined('ABSPATH')) {
    exit;
}
$wpp_zip_ok = class_exists('WpPopPop_Template_Bundle') && WpPopPop_Template_Bundle::zip_available();
?>
<div class="wpp-imp-card" id="wppoppop-lib-import-card">
    <div class="wpp-imp-head">
        <span class="dashicons dashicons-upload"></span>
        <h3><?php esc_html_e('Import Template', 'wppoppop'); ?></h3>
    </div>
    <p class="wpp-imp-help">
        <?php
        echo $wpp_zip_ok
            ? esc_html__('Import a template .zip (template.json plus its images) or a .json file. Images are added to your Media Library so the links never break. Imported templates appear under My Templates.', 'wppoppop')
            : esc_html__('Import a template .json file. The PHP Zip extension is not available on this server, so .zip files cannot be imported. Imported templates appear under My Templates.', 'wppoppop');
        ?>
    </p>
    <div class="wpp-imp-row">
        <div class="wpp-imp-drop" id="wppoppop-lib-drop">
            <span class="dashicons dashicons-media-archive"></span>
            <span class="wpp-imp-drop-text">
                <?php echo $wpp_zip_ok ? esc_html__('Drag a template .zip here, or', 'wppoppop') : esc_html__('Drag a template .json here, or', 'wppoppop'); ?>
                <button type="button" class="wpp-imp-browse" id="wppoppop-lib-drop-pick"><?php esc_html_e('browse', 'wppoppop'); ?></button>
            </span>
            <input type="file" id="wppoppop-lib-file" class="wpp-imp-file" accept=".zip,.json,application/zip,application/json" multiple tabindex="-1" aria-hidden="true">
        </div>
        <button type="button" class="button button-primary wppoppop-lib-import-btn" id="wpp-imp-btn"><?php esc_html_e('Import Template', 'wppoppop'); ?></button>
    </div>
    <div id="wppoppop-lib-drop-log" class="wpp-drop-log" aria-live="polite" style="display:none;"></div>
</div>
