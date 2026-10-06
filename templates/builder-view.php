<?php
if (!defined('ABSPATH')) {
    exit;
}

$title = (isset($popup) && !empty($popup->title)) ? $popup->title : 'Untitled Popup Campaign';
$config_raw = (isset($popup) && !empty($popup->data)) ? $popup->data : '{"elements":[],"meta":{}}';
?>
<div class="wppoppop-builder-wrap">
    <?php include WPPOPPOP_PATH . 'templates/builder/header.php'; ?>
    <?php include WPPOPPOP_PATH . 'templates/builder/ribbon.php'; ?>

    <div class="wppoppop-main-frame">
        <?php include WPPOPPOP_PATH . 'templates/builder/canvas.php'; ?>
        <?php include WPPOPPOP_PATH . 'templates/builder/drawer-inspector.php'; ?>
    </div>

    <?php include WPPOPPOP_PATH . 'templates/builder/layers-panel.php'; ?>
    <?php include WPPOPPOP_PATH . 'templates/builder/drawer-settings.php'; ?>
    <?php include WPPOPPOP_PATH . 'templates/builder/modals.php'; ?>
</div>

<script>
window.wppoppop_initial_config = <?php echo !empty($config_raw) ? $config_raw : '{}'; ?>;
</script>
