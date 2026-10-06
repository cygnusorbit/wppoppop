<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-builder-wrap" style="position:fixed;inset:0;background:#0f172a;z-index:99990;display:flex;flex-direction:column;overflow:hidden;">
    <!-- 1. Top Application Toolbar -->
    <?php include WPPOPPOP_PATH . 'templates/builder/header.php'; ?>

    <!-- 2. Horizontal Elements Ribbon Toolbar -->
    <?php include WPPOPPOP_PATH . 'templates/builder/ribbon.php'; ?>

    <!-- Main Workspace Frame (Supports Pushing-Frame Inspector Docking) -->
    <div class="wppoppop-main-frame" style="display:flex;flex:1;overflow:hidden;position:relative;">
        <!-- 3. Visual Stage & Canvas Workspace -->
        <?php include WPPOPPOP_PATH . 'templates/builder/canvas.php'; ?>

        <!-- 4. Pushing Frame Layer Inspector Drawer -->
        <?php include WPPOPPOP_PATH . 'templates/builder/drawer-inspector.php'; ?>
    </div>

    <!-- 5. Floating Draggable Layers Panel -->
    <?php include WPPOPPOP_PATH . 'templates/builder/layers-panel.php'; ?>

    <!-- 6. Slide-Out Campaign Settings Drawer -->
    <?php include WPPOPPOP_PATH . 'templates/builder/drawer-settings.php'; ?>

    <!-- 7. Embed Codes & Live Sandbox Preview Modals -->
    <?php include WPPOPPOP_PATH . 'templates/builder/modals.php'; ?>
</div>
