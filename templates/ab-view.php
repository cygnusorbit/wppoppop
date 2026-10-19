<?php
if (!defined('ABSPATH')) {
    exit;
}
$bar_title   = __('A/B Campaigns', 'wppoppop');
$bar_icon    = 'dashicons-randomize';
$bar_actions = WPPOPPOP_PATH . 'templates/ab/bar-actions.php';
?>
<div class="wrap wppoppop-dash wppoppop-ab-wrap">
    <h1 class="screen-reader-text"><?php esc_html_e('A/B Campaigns', 'wppoppop'); ?></h1>

    <!-- Top Bar (shared) -->
    <?php include WPPOPPOP_PATH . 'templates/partials/dash-bar.php'; ?>

    <!-- Notice container -->
    <?php include WPPOPPOP_PATH . 'templates/ab/header.php'; ?>

    <!-- Experiments List Table -->
    <?php include WPPOPPOP_PATH . 'templates/ab/table.php'; ?>

    <!-- Create Experiment Modal Dialog -->
    <?php include WPPOPPOP_PATH . 'templates/ab/modal-create.php'; ?>
</div>
