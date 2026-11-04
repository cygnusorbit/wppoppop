<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div id="wppoppop-floating-layers-panel" class="wppoppop-floating-layers-panel">

    
    
    <div id="wppoppop-layers-header">
        <span class="wppoppop-layers-title-wrap">
            <span class="dashicons dashicons-menu"></span> <?php esc_html_e('LAYERS PANEL', 'wppoppop'); ?>
        </span>
        <div class="wppoppop-layers-ctrls-wrap">
            <span id="wppoppop-layers-count" title="<?php esc_attr_e('Active Layers Count', 'wppoppop'); ?>">0</span>
            <button type="button" id="wppoppop-layers-toggle-collapse" title="<?php esc_attr_e('Collapse / Expand Layers Panel', 'wppoppop'); ?><?php esc_html_e('" aria-label="', 'wppoppop'); ?><?php esc_attr_e('Toggle Layers Panel', 'wppoppop'); ?>">
                <span class="dashicons dashicons-arrow-up-alt2"></span>
            </button>
        </div>
    
    

    </div>
    
    <div id="wppoppop-layers-list">
        <!-- Dynamically rendered and sortable layer items -->
    </div>
</div>
