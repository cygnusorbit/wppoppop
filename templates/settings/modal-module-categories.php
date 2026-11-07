<?php
/* WPP_MODULE_CATS: Module Category manager (opened from Settings > Module). Rows are built by module-categories.js. */
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wpp-cat-backdrop" id="wpp-cat-modal" role="dialog" aria-modal="true" aria-labelledby="wpp-cat-title" hidden>
    <div class="wpp-cat-dlg" tabindex="-1">
        <div class="wpp-cat-hd">
            <h3 id="wpp-cat-title"><?php esc_html_e('Module Categories', 'wppoppop'); ?></h3>
            <button type="button" class="wpp-cat-x" id="wpp-cat-close" aria-label="<?php echo esc_attr__('Close', 'wppoppop'); ?>">&times;</button>
        </div>
        <div class="wpp-cat-bd">
            <p class="wpp-cat-note"><?php esc_html_e('Add, rename, delete and reorder categories. They appear as filter pills above the module boxes on the Extend page. Deleting a category moves its modules back to All Modules.', 'wppoppop'); ?></p>
            <ul class="wpp-cat-list wpp-cat-fixed" id="wpp-cat-fixed"></ul>
            <ul class="wpp-cat-list" id="wpp-cat-list"></ul>
            <div class="wpp-cat-add">
                <label class="screen-reader-text" for="wpp-cat-new"><?php esc_html_e('New category name', 'wppoppop'); ?></label>
                <input type="text" id="wpp-cat-new" maxlength="40" autocomplete="off" placeholder="<?php echo esc_attr__('New category name…', 'wppoppop'); ?>">
                <button type="button" class="button" id="wpp-cat-add"><?php esc_html_e('Add', 'wppoppop'); ?></button>
            </div>
            <div class="wpp-cat-msg" id="wpp-cat-msg" role="status" aria-live="polite" hidden></div>
        </div>
        <div class="wpp-cat-ft">
            <button type="button" class="button" id="wpp-cat-cancel"><?php esc_html_e('Cancel', 'wppoppop'); ?></button>
            <button type="button" class="button button-primary" id="wpp-cat-save"><?php esc_html_e('Save Categories', 'wppoppop'); ?></button>
        </div>
    </div>
</div>
