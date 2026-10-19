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
$bar_title   = __('Theme', 'wppoppop');
$bar_icon    = 'dashicons-art';
$bar_actions = WPPOPPOP_PATH . 'templates/theme/bar-actions.php';
?>
<h1 class="screen-reader-text"><?php esc_html_e('Theme', 'wppoppop'); ?></h1>
<?php include WPPOPPOP_PATH . 'templates/partials/dash-bar.php'; ?>
<?php if (isset($msgs[$theme_status])) : ?>
    <div class="notice notice-<?php echo esc_attr($msgs[$theme_status][0]); ?> is-dismissible"><p><?php echo esc_html($msgs[$theme_status][1]); ?></p></div>
<?php endif; ?>
