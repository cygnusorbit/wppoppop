<?php
if (!defined('ABSPATH')) {
    exit;
}
$msgs = [
    'saved'        => ['updated', 'Theme saved.'],
    'reset'        => ['updated', 'Theme reset to defaults.'],
    'imported'     => ['updated', 'Theme imported.'],
    'import_error' => ['error', 'Import failed: invalid JSON.'],
];
?>
<div class="wppoppop-dashboard-header">
    <h1><span class="dashicons dashicons-art"></span> <?php esc_html_e('Theme', 'wppoppop'); ?></h1>
    <div class="wppoppop-dashboard-actions">
        <input type="search" id="wpp-theme-search" placeholder="<?php esc_attr_e('Search settings...', 'wppoppop'); ?>">
        <button type="button" class="button" id="wpp-theme-reset-form"><?php esc_html_e('Revert unsaved', 'wppoppop'); ?></button>
        <button type="submit" class="button button-primary" form="wpp-theme-form"><?php esc_html_e('Save Theme', 'wppoppop'); ?></button>
    </div>
</div>
<?php if (isset($msgs[$theme_status])) : ?>
    <div class="notice notice-<?php echo $msgs[$theme_status][0] === 'error' ? 'error' : 'success'; ?> is-dismissible"><p><?php echo esc_html($msgs[$theme_status][1]); ?></p></div>
<?php endif; ?>
