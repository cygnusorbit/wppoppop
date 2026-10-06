<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div id="wppoppop-floating-layers-panel" style="position:fixed;bottom:24px;left:24px;width:240px;background:#ffffff;border-radius:8px;box-shadow:0 10px 25px rgba(0,0,0,0.3);z-index:9990;overflow:hidden;border:1px solid #e2e8f0;">
    <div id="wppoppop-layers-header" style="background:#c2185b;color:#ffffff;padding:8px 12px;font-size:12px;font-weight:700;display:flex;align-items:center;justify-content:space-between;cursor:move;user-select:none;">
        <span style="display:inline-flex;align-items:center;gap:6px;">
            <span class="dashicons dashicons-menu" style="font-size:14px;width:14px;height:14px;"></span> LAYERS
        </span>
        <span id="wppoppop-layers-count" style="background:rgba(255,255,255,0.25);padding:1px 6px;border-radius:10px;font-size:10px;">0</span>
    </div>
    
    <div id="wppoppop-layers-list" style="max-height:260px;overflow-y:auto;padding:6px;display:flex;flex-direction:column;gap:4px;">
        <!-- Dynamically rendered layer list items -->
    </div>
</div>
