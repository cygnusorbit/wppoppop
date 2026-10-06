<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap wppoppop-dashboard-wrap" style="max-width:1200px;">
    <!-- Dashboard Header & Top Actions -->
    <?php include WPPOPPOP_PATH . 'templates/dashboard/header.php'; ?>

    <!-- Performance Metric Cards -->
    <?php include WPPOPPOP_PATH . 'templates/dashboard/kpi-cards.php'; ?>

    <!-- Campaigns List Table -->
    <?php include WPPOPPOP_PATH . 'templates/dashboard/table.php'; ?>

    <!-- JSON Import Modal Dialog -->
    <?php include WPPOPPOP_PATH . 'templates/dashboard/modal-import.php'; ?>

    <!-- Embed Code Snippets Modal Dialog -->
    <?php include WPPOPPOP_PATH . 'templates/dashboard/modal-embed.php'; ?>
</div>
