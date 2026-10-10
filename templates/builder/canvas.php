<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-builder-workspace">
    <!-- Floating Workspace Preview Control Bar (Active during Preview Mode) -->
    <div id="wppoppop-workspace-preview-bar" class="wppoppop-workspace-preview-bar">
        <span class="wppoppop-preview-badge">
            <span class="dashicons dashicons-visibility"></span> LIVE PREVIEW MODE
        </span>
        <button type="button" id="wppoppop-preview-replay-btn" class="button wppoppop-preview-bar-btn" title="Replay Canvas Entrance Animation">Replay Animation</button>
        <button type="button" id="wppoppop-preview-exit-btn" class="button button-primary wppoppop-preview-bar-btn" title="Exit Preview and Return to Editor">Exit Preview</button>
    </div>

    <!-- Floating Draggable Magenta LAYERS Panel (Top Right of Workspace) -->
    <?php include WPPOPPOP_PATH . 'templates/builder/layers-panel.php'; ?>

    <!-- Visual Canvas Box Stage with Dedicated Corner Drag Grip -->
    <div id="wppoppop-canvas-box">
        <div id="wppoppop-canvas-elements-root"></div>
        
        <!-- On-Canvas Submission Feedback Overlay (Active during Preview Simulation) -->
        <div id="wppoppop-stage-status-overlay" class="wppoppop-stage-status-overlay">
            <span class="dashicons dashicons-yes-alt wppoppop-status-icon"></span>
            <h4 class="wppoppop-status-heading">Submission Successful!</h4>
            <p class="wppoppop-status-subtext">Thank you! Your information has been recorded.</p>
            <button type="button" class="button button-secondary wppoppop-status-reset-btn" style="margin-top:10px;">Reset Form</button>
        </div>

        <!-- Live Resize Dimension Tooltip Badge -->
        <div id="wppoppop-canvas-size-badge">640 × 400 px</div>

        <!-- Dedicated Ergonomic Bottom-Right Corner Resize Handle -->
        <div id="wppoppop-canvas-corner-handle" title="Drag to Resize Canvas">
            <svg width="10" height="10" viewBox="0 0 10 10" fill="none" style="pointer-events:none;">
                <line x1="8" y1="2" x2="2" y2="8" stroke="#ffffff" stroke-width="1.5" stroke-linecap="round"/>
                <line x1="8" y1="5.5" x2="5.5" y2="8" stroke="#ffffff" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
        </div>
    </div>
</div>
