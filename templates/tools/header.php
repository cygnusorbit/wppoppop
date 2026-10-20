<?php
if (!defined('ABSPATH')) {
    exit;
}
$bar_title   = __('Tools & Export', 'wppoppop');
$bar_icon    = 'dashicons-admin-tools';
$bar_actions = WPPOPPOP_PATH . 'templates/tools/bar-actions.php';
?>
<h1 class="screen-reader-text"><?php esc_html_e('Tools, Diagnostics & Portability', 'wppoppop'); ?></h1>
<?php include WPPOPPOP_PATH . 'templates/partials/dash-bar.php'; ?>
