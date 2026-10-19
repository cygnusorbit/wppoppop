<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wpp-theme-box">
    <h3><?php esc_html_e('Export / Import', 'wppoppop'); ?></h3>
    <textarea id="wpp-theme-json" name="wpp_theme_import" rows="6" spellcheck="false"></textarea>
    <p>
        <button type="button" class="button" id="wpp-theme-export"><?php esc_html_e('Export current', 'wppoppop'); ?></button>
        <button type="submit" class="button" id="wpp-theme-import" data-action="import"><?php esc_html_e('Import JSON', 'wppoppop'); ?></button>
    </p>
    <hr>
    <button type="submit" class="button wpp-btn-danger" id="wpp-theme-reset-all" data-action="reset"><?php esc_html_e('Reset all to defaults', 'wppoppop'); ?></button>
</div>
