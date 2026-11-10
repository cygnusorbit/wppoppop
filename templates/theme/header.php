<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- template variables are local to the method that includes this file
if (!defined('ABSPATH')) {
    exit;
}
$msgs = [
    'saved'        => ['success', __('Theme saved.', 'wppoppop')],
    'reset'        => ['success', __('Theme reset to defaults.', 'wppoppop')],
    'imported'     => ['success', __('Theme imported.', 'wppoppop')],
    'import_error' => ['error', __('Import failed: invalid JSON.', 'wppoppop')],
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
