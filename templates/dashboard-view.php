<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap wppoppop-dashboard-wrap" style="width: 100%; padding-right: 30px; max-width: none; box-sizing: border-box;">
    <!-- Top Bar (shared with Dashboard) -->
    <?php
    $bar_title   = __('Popups', 'wppoppop');
    $bar_icon    = 'dashicons-megaphone';
    $bar_actions = WPPOPPOP_PATH . 'templates/dashboard/bar-actions.php';
    ?>
    <div class="wppoppop-dash" style="margin:0 0 20px;max-width:none;">
        <?php include WPPOPPOP_PATH . 'templates/partials/dash-bar.php'; ?>
    </div>

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
