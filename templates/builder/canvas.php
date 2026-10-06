<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-builder-workspace" id="wppoppop-builder-stage">
    <div id="wppoppop-canvas-box" style="width:640px;height:400px;background:#ffffff;">
        <div id="wppoppop-canvas-elements-root"></div>
        
        <!-- Precision Bottom-Right Corner Canvas Resize Handle -->
        <div class="wppoppop-canvas-resize-handle" id="wppoppop-canvas-resize-handle" title="Click and drag to resize popup canvas">
            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M10 2L2 10M10 6L6 10M10 10H10.01" stroke="#94a3b8" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
        </div>

        <!-- Live Dimensions Badge Tooltip -->
        <div class="wppoppop-canvas-dim-tooltip" id="wppoppop-canvas-dim-tooltip">640 &times; 400 px</div>
    </div>

    <!-- Floating Draggable Magenta Layers Panel Docked in Top-Right of Checker Area -->
    <?php include WPPOPPOP_PATH . 'templates/builder/layers-panel.php'; ?>
</div>
