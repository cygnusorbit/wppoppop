<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div id="wppoppop-floating-layers-panel" class="wppoppop-floating-layers-panel" style="z-index:100000;">

    <!-- Phase 5: Layers Search Bar & Bulk Actions -->
    <div class="wppoppop-layers-panel-toolbar">
        <div class="wppoppop-layers-search-wrap">
            <span class="dashicons dashicons-search"></span>
            <input type="text" id="wppoppop-layers-search" class="wppoppop-layers-search" placeholder="Filter layers..." autocomplete="off">
        </div>
        <div class="wppoppop-layers-bulk-actions">
            <button type="button" class="wppoppop-layers-bulk-btn" id="wppoppop-layers-bulk-vis" title="Toggle All Visibility" aria-label="Toggle All Visibility">
                <span class="dashicons dashicons-visibility"></span>
            </button>
            <button type="button" class="wppoppop-layers-bulk-btn" id="wppoppop-layers-bulk-lock" title="Toggle All Lock" aria-label="Toggle All Lock">
                <span class="dashicons dashicons-unlock"></span>
            </button>
        </div>
    </div>
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


    <div class="wppoppop-layer-template-stub" style="display:none;" aria-hidden="true">
        <span class="wppoppop-layer-title wppoppop-layer-title-editable">Layer Title</span>
    </div>