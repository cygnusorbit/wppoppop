<?php
if (!defined('ABSPATH')) {
    exit;
}
$bar_title   = __('Extend', 'wppoppop');
$bar_icon    = 'dashicons-plugins-checked';
$bar_actions = WPPOPPOP_PATH . 'templates/extend/bar-actions.php';
?>
<h1 class="screen-reader-text"><?php esc_html_e('Extend: add and manage modules', 'wppoppop'); ?></h1>
<?php include WPPOPPOP_PATH . 'templates/partials/dash-bar.php'; ?>
