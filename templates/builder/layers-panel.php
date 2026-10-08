<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div id="wppoppop-floating-layers-panel" class="wppoppop-floating-layers-panel" style="z-index:100000;">
    <div id="wppoppop-layers-header">
        <span class="wppoppop-layers-title-wrap">
            <span class="dashicons dashicons-menu"></span> LAYERS
        </span>
        <div class="wppoppop-layers-ctrls-wrap">
            <span id="wppoppop-layers-count" title="Active Layers Count">0</span>
            <button type="button" id="wppoppop-layers-toggle-collapse" title="Collapse / Expand Layers Panel" aria-label="Toggle Layers Panel">
                <span class="dashicons dashicons-arrow-up-alt2"></span>
            </button>
        </div>
    </div>
    
    <div id="wppoppop-layers-list">
        <!-- Dynamically rendered and sortable layer items -->
    </div>
</div>
