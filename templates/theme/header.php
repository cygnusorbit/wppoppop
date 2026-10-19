<?php
if (!defined('ABSPATH')) {
    exit;
}
$msgs = [
    'saved'        => ['success', 'Theme saved.'],
    'reset'        => ['success', 'Theme reset to defaults.'],
    'imported'     => ['success', 'Theme imported.'],
    'import_error' => ['error', 'Import failed: invalid JSON.'],
];
?>
<div class="wppoppop-dashboard-header wpp-theme-topbar">
    <div class="wpp-theme-topbar-title">
        <h1 class="wp-heading-inline"><span class="dashicons dashicons-art"></span> <?php esc_html_e('Theme', 'wppoppop'); ?></h1>
        <p class="wpp-theme-subtitle"><?php esc_html_e('Customize fonts, sizes and colors for every WpPopPop admin page.', 'wppoppop'); ?></p>
    </div>
    <div class="wppoppop-dashboard-actions">
        <input type="search" id="wpp-theme-search" placeholder="<?php esc_attr_e('Search settings...', 'wppoppop'); ?>">
        <button type="button" class="button button-secondary" id="wpp-theme-reset-form">
            <span class="dashicons dashicons-undo"></span> <?php esc_html_e('Revert unsaved', 'wppoppop'); ?>
        </button>
        <button type="submit" class="button button-primary" form="wpp-theme-form">
            <span class="dashicons dashicons-yes"></span> <?php esc_html_e('Save Theme', 'wppoppop'); ?>
        </button>
    </div>
</div>
<?php if (isset($msgs[$theme_status])) : ?>
    <div class="notice notice-<?php echo esc_attr($msgs[$theme_status][0]); ?> is-dismissible"><p><?php echo esc_html($msgs[$theme_status][1]); ?></p></div>
<?php endif; ?>
