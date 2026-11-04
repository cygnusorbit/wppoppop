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
            <span class="dashicons dashicons-align-left"></span>
        </button>
        <button type="button" class="wppoppop-align-btn" data-align="center" title="<?php esc_attr_e('Align Center (Horizontal)', 'wppoppop'); ?>">
            <span class="dashicons dashicons-align-center"></span>
        </button>
        <button type="button" class="wppoppop-align-btn" data-align="right" title="<?php esc_attr_e('Align Right', 'wppoppop'); ?>">
            <span class="dashicons dashicons-align-right"></span>
        </button>
        <span class="wppoppop-toolbar-sep wppoppop-align-separator"></span>
        <button type="button" class="wppoppop-align-btn" data-align="top" title="<?php esc_attr_e('Align Top', 'wppoppop'); ?>">
            <span class="dashicons dashicons-arrow-up-alt2"></span>
        </button>
        <button type="button" class="wppoppop-align-btn" data-align="middle" title="<?php esc_attr_e('Align Middle (Vertical)', 'wppoppop'); ?>">
            <span class="dashicons dashicons-menu"></span>
        </button>
        <button type="button" class="wppoppop-align-btn" data-align="bottom" title="<?php esc_attr_e('Align Bottom', 'wppoppop'); ?>">
            <span class="dashicons dashicons-arrow-down-alt2"></span>
        </button>
        <span class="wppoppop-toolbar-sep wppoppop-align-separator"></span>
        <button type="button" class="wppoppop-align-btn wppoppop-nudge-btn" data-nudge="left" title="<?php esc_attr_e('Move Left 1px (Shift = 10px)', 'wppoppop'); ?><?php esc_html_e('" aria-label="', 'wppoppop'); ?><?php esc_attr_e('Move Left', 'wppoppop'); ?>">
            <span class="dashicons dashicons-arrow-left"></span>
        </button>
        <button type="button" class="wppoppop-align-btn wppoppop-nudge-btn" data-nudge="right" title="<?php esc_attr_e('Move Right 1px (Shift = 10px)', 'wppoppop'); ?><?php esc_html_e('" aria-label="', 'wppoppop'); ?><?php esc_attr_e('Move Right', 'wppoppop'); ?>">
            <span class="dashicons dashicons-arrow-right"></span>
        </button>
        <button type="button" class="wppoppop-align-btn wppoppop-nudge-btn" data-nudge="up" title="<?php esc_attr_e('Move Up 1px (Shift = 10px)', 'wppoppop'); ?><?php esc_html_e('" aria-label="', 'wppoppop'); ?><?php esc_attr_e('Move Up', 'wppoppop'); ?>">
            <span class="dashicons dashicons-arrow-up"></span>
        </button>
        <button type="button" class="wppoppop-align-btn wppoppop-nudge-btn" data-nudge="down" title="<?php esc_attr_e('Move Down 1px (Shift = 10px)', 'wppoppop'); ?><?php esc_html_e('" aria-label="', 'wppoppop'); ?><?php esc_attr_e('Move Down', 'wppoppop'); ?>">
            <span class="dashicons dashicons-arrow-down"></span>
        </button>
    </div>

    <div id="wppoppop-canvas-box">
        <!-- Phase 3: Magnetic Smart Snap Crosshair Guides -->
        <div id="wppoppop-guide-v" class="wppoppop-smart-guide wppoppop-guide-v" style="display:none;"></div>
        <div id="wppoppop-guide-h" class="wppoppop-smart-guide wppoppop-guide-h" style="display:none;"></div>
        <div id="wppoppop-canvas-elements-root"></div>
        
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
