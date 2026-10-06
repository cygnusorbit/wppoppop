<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<!-- Embed & Shortcode Modal -->
<div class="wppoppop-modal-backdrop" id="wppoppop-embed-modal" style="display:none;">
    <div class="wppoppop-modal-dialog">
        <div class="wppoppop-modal-header">
            <h3>Embed Codes & Integrations</h3>
            <button type="button" class="wppoppop-modal-close" id="wppoppop-embed-close">&times;</button>
        </div>
        <div class="wppoppop-modal-body">
            <label>Standard Shortcode</label>
            <input type="text" class="widefat" id="wppoppop-embed-shortcode" readonly>
            <label style="margin-top:10px;display:block;">Content Locker Shortcode</label>
            <input type="text" class="widefat" id="wppoppop-embed-locker" readonly>
            <label style="margin-top:10px;display:block;">Button Click Trigger (HTML Link)</label>
            <input type="text" class="widefat" id="wppoppop-embed-trigger" readonly>
            <label style="margin-top:10px;display:block;">Remote Cross-Domain Embed (&lt;script&gt; tag)</label>
            <textarea class="widefat" id="wppoppop-embed-remote" rows="3" readonly></textarea>
        </div>
    </div>
</div>

<!-- Interactive Live Preview Sandbox Modal -->
<div class="wppoppop-modal-backdrop" id="wppoppop-preview-modal" style="display:none;">
    <div class="wppoppop-modal-dialog wppoppop-preview-dialog">
        <div class="wppoppop-modal-header">
            <h3>Live Interactive Preview Sandbox</h3>
            <button type="button" class="wppoppop-modal-close" id="wppoppop-preview-close">&times;</button>
        </div>
        <div class="wppoppop-modal-body" id="wppoppop-preview-stage" style="min-height:480px;display:flex;align-items:center;justify-content:center;background:#f1f5f9;">
            <!-- Rendered dynamically via builder.js -->
        </div>
    </div>
</div>
