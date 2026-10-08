<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-builder-workspace">
    <!-- Floating Workspace Preview Control Bar -->
    <div id="wppoppop-workspace-preview-bar" class="wppoppop-workspace-preview-bar">
        <span class="wppoppop-preview-badge">
            <span class="dashicons dashicons-visibility"></span> LIVE PREVIEW MODE
        </span>
        <button type="button" id="wppoppop-preview-replay-btn" class="button wppoppop-preview-bar-btn" title="Replay Canvas Entrance Animation">
            <span class="dashicons dashicons-controls-play"></span> Replay Animation
        </button>
        <button type="button" id="wppoppop-preview-exit-btn" class="button button-primary wppoppop-preview-bar-btn" title="Exit Preview and Return to Editor">
            <span class="dashicons dashicons-edit"></span> Exit Preview
        </button>
    </div>

    <!-- Floating Draggable Magenta LAYERS Panel (Movable across workspace) -->
    <?php include WPPOPPOP_PATH . 'templates/builder/layers-panel.php'; ?>

    <!-- Visual Canvas Box Stage (overflow: visible allows image & shape overflow) -->
    <div id="wppoppop-canvas-box" style="position:relative;width:640px;height:400px;background:#ffffff;border-radius:8px;box-shadow:0 25px 50px -12px rgba(0,0,0,0.5);transition:none;cursor:default;overflow:visible !important;">
        <div id="wppoppop-canvas-elements-root" style="width:100%;height:100%;position:relative;border-radius:inherit;overflow:visible !important;"></div>
        
        <!-- On-Canvas Submission Feedback Overlay -->
        <div id="wppoppop-stage-status-overlay" class="wppoppop-stage-status-overlay">
            <span class="dashicons dashicons-yes-alt wppoppop-status-icon"></span>
            <h4 class="wppoppop-status-heading">Submission Successful!</h4>
            <p class="wppoppop-status-subtext">Thank you! Your information has been recorded.</p>
            <button type="button" class="button button-secondary wppoppop-status-reset-btn" style="margin-top:10px;">Reset Form</button>
        </div>

        <!-- Live Resize Dimension Tooltip Badge -->
        <div id="wppoppop-canvas-size-badge">640 × 400 px</div>

        <!-- Dedicated Bottom-Right Corner Resize Handle -->
        <div id="wppoppop-canvas-corner-handle" title="Drag to Resize Canvas">
            <svg width="10" height="10" viewBox="0 0 10 10" fill="none" style="pointer-events:none;">
                <line x1="8" y1="2" x2="2" y2="8" stroke="#ffffff" stroke-width="1.5" stroke-linecap="round"/>
                <line x1="8" y1="5.5" x2="5.5" y2="8" stroke="#ffffff" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
        </div>
    </div>
</div>
