<?php
if (!defined('ABSPATH')) {
    exit;
}
$bar_title   = __('Settings', 'wppoppop');
$bar_icon    = 'dashicons-admin-generic';
$bar_actions = WPPOPPOP_PATH . 'templates/settings/bar-actions.php';
?>
<h1 class="screen-reader-text"><?php esc_html_e('WpPopPop Settings', 'wppoppop'); ?></h1>
<?php include WPPOPPOP_PATH . 'templates/partials/dash-bar.php'; ?>
