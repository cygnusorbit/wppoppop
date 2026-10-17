<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div id="wppoppop-floating-layers-panel" class="wppoppop-floating-layers-panel" style="z-index:100000;">

    
    
    <div id="wppoppop-layers-header">
        <span class="wppoppop-layers-title-wrap">
            <span class="dashicons dashicons-menu"></span> LAYERS PANEL
        </span>
        <div class="wppoppop-layers-ctrls-wrap">
            <span id="wppoppop-layers-count" title="Active Layers Count">0</span>
            <button type="button" id="wppoppop-layers-toggle-collapse" title="Collapse / Expand Layers Panel" aria-label="Toggle Layers Panel">
                <span class="dashicons dashicons-arrow-up-alt2"></span>
            </button>
        </div>
    

    </div>
    
        <!-- Phase 5: Layers Panel Toolbar, Search & Bulk Actions -->
    <div class="wppoppop-layers-panel-toolbar" style="display: flex; align-items: center; justify-content: space-between; padding: 6px 8px; background: #0f172a; border-bottom: 1px solid #1e293b; gap: 6px;">
        <div class="wppoppop-layers-search-wrap" style="display: flex; align-items: center; flex: 1; background: #1e293b; border: 1px solid #334155; border-radius: 4px; padding: 0 6px;">
            <span class="dashicons dashicons-search" style="font-size: 13px; width: 13px; height: 13px; line-height: 1; color: #64748b; margin-right: 4px;"></span>
            <input type="text" id="wppoppop-layers-search" class="wppoppop-layers-search" placeholder="<?php esc_attr_e('Filter layers...', 'wppoppop'); ?>" autocomplete="off" style="width: 100%; background: transparent !important; border: none !important; outline: none !important; box-shadow: none !important; color: #f1f5f9 !important; font-size: 11px !important; padding: 3px 0 !important; height: 22px !important;">
        </div>
        <div class="wppoppop-layers-bulk-actions" style="display: flex; align-items: center; gap: 3px;">
            <button type="button" class="wppoppop-layers-bulk-btn" id="wppoppop-layers-bulk-vis" title="<?php esc_attr_e('Toggle All Visibility', 'wppoppop'); ?>" aria-label="Toggle All Visibility" style="display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px; padding: 0; background: #1e293b; border: 1px solid #334155; border-radius: 3px; color: #94a3b8; cursor: pointer;">
                <span class="dashicons dashicons-visibility" style="font-size: 14px; width: 14px; height: 14px; line-height: 1;"></span>
            </button>
            <button type="button" class="wppoppop-layers-bulk-btn" id="wppoppop-layers-bulk-lock" title="<?php esc_attr_e('Toggle All Lock', 'wppoppop'); ?>" aria-label="Toggle All Lock" style="display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px; padding: 0; background: #1e293b; border: 1px solid #334155; border-radius: 3px; color: #94a3b8; cursor: pointer;">
                <span class="dashicons dashicons-unlock" style="font-size: 14px; width: 14px; height: 14px; line-height: 1;"></span>
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