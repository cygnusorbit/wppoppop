<?php
if (!defined('ABSPATH')) {
    exit;
}

$current_uid = isset($_GET['uid']) ? sanitize_text_field(wp_unslash($_GET['uid'])) : ($popup ? $popup->uid : '');
?>
<div class="wppoppop-builder-wrap">
    <!-- Hidden Security Nonce & Campaign Identifier State -->
    <?php wp_nonce_field('wppoppop_builder_nonce', 'wppoppop-builder-nonce'); ?>
    <input type="hidden" id="wppoppop-builder-uid" value="<?php echo esc_attr($current_uid); ?>">

    <!-- 1. Top Application Toolbar (Canvas Sequence & Navigation) -->
    <?php include WPPOPPOP_PATH . 'templates/builder/header.php'; ?>

    <!-- 2. Horizontal Elements Ribbon Toolbar (19 Elements) -->
    <?php include WPPOPPOP_PATH . 'templates/builder/ribbon.php'; ?>

    <!-- Main Workspace Frame (Supports Pushing-Frame Inspector Docking) -->
    <div class="wppoppop-main-frame" style="display:flex;flex:1;overflow:hidden;position:relative;">
        <!-- 3. Visual Stage, Layers Panel & Canvas Workspace -->
        <?php include WPPOPPOP_PATH . 'templates/builder/canvas.php'; ?>

        <!-- 4. Pushing Frame Layer Inspector Drawer -->
        <?php include WPPOPPOP_PATH . 'templates/builder/drawer-inspector.php'; ?>
    </div>

    <!-- 5. Slide-Out Campaign Settings Drawer -->
    <?php include WPPOPPOP_PATH . 'templates/builder/drawer-settings.php'; ?>

    <!-- 6. Embed Codes & Live Sandbox Preview Modals -->
    <?php include WPPOPPOP_PATH . 'templates/builder/modals.php'; ?>
</div>
