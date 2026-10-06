<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-builder-wrapper">
    <!-- Top Workspace Header -->
    <?php include WPPOPPOP_PATH . 'templates/builder/top-bar.php'; ?>

    <!-- Elements Ribbon Toolbar -->
    <?php include WPPOPPOP_PATH . 'templates/builder/ribbon.php'; ?>

    <!-- Main Workspace Frame (contracts left when properties panel opens) -->
    <div class="wppoppop-main-frame">
        <?php include WPPOPPOP_PATH . 'templates/builder/canvas.php'; ?>
        <?php include WPPOPPOP_PATH . 'templates/builder/layers-panel.php'; ?>
    </div>

    <!-- Pushing Layer Properties Inspector Sidebar -->
    <?php include WPPOPPOP_PATH . 'templates/builder/panel-properties.php'; ?>

    <!-- Slide-Out Campaign Settings Drawer -->
    <?php include WPPOPPOP_PATH . 'templates/builder/drawer-settings.php'; ?>

    <!-- Embed Code & Live Preview Modals -->
    <?php include WPPOPPOP_PATH . 'templates/builder/modals.php'; ?>
</div>
