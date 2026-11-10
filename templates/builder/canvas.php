<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-builder-workspace">

   
    <!-- Floating Workspace Preview Control Bar (Active during Preview Mode) -->
    <div id="wppoppop-workspace-preview-bar" class="wppoppop-workspace-preview-bar">
        <span class="wppoppop-preview-badge">
            <span class="dashicons dashicons-visibility"></span> <?php esc_html_e('LIVE PREVIEW MODE', 'wppoppop'); ?>
        </span>
        <button type="button" id="wppoppop-preview-replay-btn" class="button wppoppop-preview-bar-btn" title="<?php esc_attr_e('Replay Canvas Entrance Animation', 'wppoppop'); ?>"><?php esc_html_e('Replay Animation', 'wppoppop'); ?></button>
        <button type="button" id="wppoppop-preview-exit-btn" class="button button-primary wppoppop-preview-bar-btn" title="<?php esc_attr_e('Exit Preview and Return to Editor', 'wppoppop'); ?>"><?php esc_html_e('Exit Preview', 'wppoppop'); ?></button>
    </div>

    <!-- Floating Draggable Magenta LAYERS Panel (Top Right of Workspace) -->
    <?php include WPPOPPOP_PATH . 'templates/builder/layers-panel.php'; ?>

    <!-- Visual Canvas Box Stage with Dedicated Corner Drag Grip -->
        <!-- Parity Phase 3: Stage Alignment Toolbar -->
    <div id="wppoppop-alignment-toolbar" class="wppoppop-alignment-toolbar" style="display:none;">
        <button type="button" class="wppoppop-align-btn" data-align="left" title="<?php esc_attr_e('Align Left', 'wppoppop'); ?>">
            <svg class="wppoppop-align-svg" viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true" focusable="false"><rect x="3" y="3" width="2" height="18"/><rect x="7" y="5" width="10" height="5"/><rect x="7" y="13" width="14" height="5"/></svg>
        </button>
        <button type="button" class="wppoppop-align-btn" data-align="center" title="<?php esc_attr_e('Align Center', 'wppoppop'); ?>">
            <svg class="wppoppop-align-svg" viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true" focusable="false"><rect x="11" y="3" width="2" height="18"/><rect x="7" y="5" width="10" height="5"/><rect x="4" y="13" width="16" height="5"/></svg>
        </button>
        <button type="button" class="wppoppop-align-btn" data-align="right" title="<?php esc_attr_e('Align Right', 'wppoppop'); ?>">
            <svg class="wppoppop-align-svg" viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true" focusable="false"><rect x="19" y="3" width="2" height="18"/><rect x="7" y="5" width="10" height="5"/><rect x="3" y="13" width="14" height="5"/></svg>
        </button>
        <span class="wppoppop-toolbar-sep wppoppop-align-separator"></span>
        <button type="button" class="wppoppop-align-btn" data-align="top" title="<?php esc_attr_e('Align Top', 'wppoppop'); ?>">
            <svg class="wppoppop-align-svg" viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true" focusable="false"><rect x="3" y="3" width="18" height="2"/><rect x="6" y="7" width="5" height="14"/><rect x="13" y="7" width="5" height="9"/></svg>
        </button>
        <button type="button" class="wppoppop-align-btn" data-align="middle" title="<?php esc_attr_e('Align Middle', 'wppoppop'); ?>">
            <svg class="wppoppop-align-svg" viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true" focusable="false"><rect x="3" y="11" width="18" height="2"/><rect x="6" y="5" width="5" height="14"/><rect x="13" y="7" width="5" height="10"/></svg>
        </button>
        <button type="button" class="wppoppop-align-btn" data-align="bottom" title="<?php esc_attr_e('Align Bottom', 'wppoppop'); ?>">
            <svg class="wppoppop-align-svg" viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true" focusable="false"><rect x="3" y="19" width="18" height="2"/><rect x="6" y="10" width="5" height="8"/><rect x="13" y="4" width="5" height="14"/></svg>
        </button>
        <span class="wppoppop-toolbar-sep wppoppop-align-separator"></span>
        <button type="button" class="wppoppop-align-btn wppoppop-nudge-btn" data-nudge="left" title="<?php esc_attr_e('Left', 'wppoppop'); ?>">
            <span class="dashicons dashicons-arrow-left"></span>
        </button>
        <button type="button" class="wppoppop-align-btn wppoppop-nudge-btn" data-nudge="right" title="<?php esc_attr_e('Right', 'wppoppop'); ?>">
            <span class="dashicons dashicons-arrow-right"></span>
        </button>
        <button type="button" class="wppoppop-align-btn wppoppop-nudge-btn" data-nudge="up" title="<?php esc_attr_e('Top', 'wppoppop'); ?>">
            <span class="dashicons dashicons-arrow-up"></span>
        </button>
        <button type="button" class="wppoppop-align-btn wppoppop-nudge-btn" data-nudge="down" title="<?php esc_attr_e('Bottom', 'wppoppop'); ?>">
            <span class="dashicons dashicons-arrow-down"></span>
        </button>
    </div>

    <div id="wppoppop-canvas-box">
        <!-- Phase 3: Magnetic Smart Snap Crosshair Guides -->
        <div id="wppoppop-guide-v" class="wppoppop-smart-guide wppoppop-guide-v" style="display:none;"></div>
        <div id="wppoppop-guide-h" class="wppoppop-smart-guide wppoppop-guide-h" style="display:none;"></div>
        <div id="wppoppop-canvas-elements-root"></div>
        <!-- WPPOPPOP_DEFAULT_CLOSE_EDIT: edit-mode preview of the default close button (visual only) -->
        <span id="wppoppop-canvas-default-close" class="wppoppop-canvas-default-close" aria-hidden="true" style="display:none;">&times;</span>
        
        <!-- On-Canvas Submission Feedback Overlay (Active during Preview Simulation) -->
        <div id="wppoppop-stage-status-overlay" class="wppoppop-stage-status-overlay">
            <span class="dashicons dashicons-yes-alt wppoppop-status-icon"></span>
            <h4 class="wppoppop-status-heading"><?php esc_html_e('Submission Successful!', 'wppoppop'); ?></h4>
            <p class="wppoppop-status-subtext"><?php esc_html_e('Thank you! Your information has been recorded.', 'wppoppop'); ?></p>
            <button type="button" class="button button-secondary wppoppop-status-reset-btn" style="margin-top:10px;"><?php esc_html_e('Reset Form', 'wppoppop'); ?></button>
        </div>

        <!-- Live Resize Dimension Tooltip Badge -->
        <div id="wppoppop-canvas-size-guide" aria-hidden="true">600 x 450</div>
        <div id="wppoppop-canvas-size-badge"><?php esc_html_e('640 × 400 px', 'wppoppop'); ?></div>

        <!-- Dedicated Ergonomic Bottom-Right Corner Resize Handle -->
                <!-- Dedicated Bottom-Right Corner Resize Handle (Inset Diagonal Gray Grip) -->
        <div id="wppoppop-canvas-corner-handle" title="<?php esc_attr_e('Drag to Resize Canvas', 'wppoppop'); ?>" style="position:absolute;bottom:2px;right:2px;width:14px;height:14px;background:transparent;cursor:se-resize;z-index:1;display:flex;align-items:flex-end;justify-content:flex-end;user-select:none;">
            <svg width="10" height="10" viewBox="0 0 10 10" fill="none" style="pointer-events:none;">
                <line x1="8.5" y1="2.5" x2="2.5" y2="8.5" stroke="#777777" stroke-width="1.2" stroke-linecap="round"/>
                <line x1="8.5" y1="6" x2="6" y2="8.5" stroke="#777777" stroke-width="1.2" stroke-linecap="round"/>
            </svg>
        </div>
    </div>
</div>
