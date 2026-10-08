<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-builder-workspace" style="position:relative;flex:1;overflow:auto;display:flex;align-items:center;justify-content:center;background:#0f172a;background-image:linear-gradient(45deg, #1e293b 25%, transparent 25%), linear-gradient(-45deg, #1e293b 25%, transparent 25%), linear-gradient(45deg, transparent 75%, #1e293b 75%), linear-gradient(-45deg, transparent 75%, #1e293b 75%);background-size:20px 20px;background-position:0 0, 0 10px, 10px -10px, -10px 0px;padding:60px 40px;min-height:calc(100vh - 100px);">
    
    <!-- Floating Draggable Magenta LAYERS Panel (Movable anywhere across workspace) -->
    <?php include WPPOPPOP_PATH . 'templates/builder/layers-panel.php'; ?>

    <!-- Dynamically Resizable Visual Canvas Box with Dedicated Corner Drag Grip -->
    <div id="wppoppop-canvas-box" style="position:relative;width:640px;height:400px;background:#ffffff;border-radius:8px;box-shadow:0 25px 50px -12px rgba(0,0,0,0.5);transition:none;cursor:default;">
        <div id="wppoppop-canvas-elements-root" style="width:100%;height:100%;position:relative;overflow:hidden;border-radius:inherit;"></div>
        
        <!-- Live Resize Dimension Tooltip Badge -->
        <div id="wppoppop-canvas-size-badge" style="display:none;position:absolute;bottom:-28px;right:0;background:#1e293b;color:#38bdf8;font-size:11px;font-weight:700;padding:2px 8px;border-radius:4px;border:1px solid #334155;pointer-events:none;z-index:999999;">640 × 400 px</div>

        <!-- Dedicated Bottom-Right Corner Resize Handle (Stacked beneath floating layer panel) -->
        <div id="wppoppop-canvas-corner-handle" title="Drag to Resize Canvas" style="position:absolute;bottom:0;right:0;width:22px;height:22px;background:#c2185b;border-top-left-radius:6px;border-bottom-right-radius:8px;border-left:2px solid #ffffff;border-top:2px solid #ffffff;cursor:se-resize;z-index:9995;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 6px rgba(0,0,0,0.3);user-select:none;">
            <svg width="10" height="10" viewBox="0 0 10 10" fill="none" style="pointer-events:none;">
                <line x1="8" y1="2" x2="2" y2="8" stroke="#ffffff" stroke-width="1.5" stroke-linecap="round"/>
                <line x1="8" y1="5.5" x2="5.5" y2="8" stroke="#ffffff" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
        </div>
    </div>
</div>
