<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-box wpp-theme-box">
    <h3><?php esc_html_e('Export / Import', 'wppoppop'); ?></h3>
    <textarea id="wpp-theme-json" name="wpp_theme_import" rows="6" spellcheck="false"></textarea>
    <p>
        <button type="button" class="button" id="wpp-theme-export"><?php esc_html_e('Export current', 'wppoppop'); ?></button>
        <button type="submit" class="button" id="wpp-theme-import" data-action="import"><?php esc_html_e('Import JSON', 'wppoppop'); ?></button>
    </p>
    <hr>
    <p class="wpp-theme-tools-foot">
        <button type="button" class="button" data-wpp-defaults-save="theme" title="<?php echo esc_attr__('Use the last saved theme as the default', 'wppoppop'); ?>"><?php esc_html_e('Save as Default', 'wppoppop'); ?></button>
        <button type="submit" class="button wpp-btn-danger" id="wpp-theme-reset-all" data-action="reset"><?php esc_html_e('Reset all to defaults', 'wppoppop'); ?></button>
    </p>
</div>
