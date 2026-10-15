<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<!-- Embed Codes Dialog Modal -->
<div class="wppoppop-modal-backdrop" id="wppoppop-builder-embed-modal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,0.7);backdrop-filter:blur(3px);z-index:999999;align-items:center;justify-content:center;">
    <div style="background:#ffffff;border-radius:8px;width:520px;max-width:92vw;box-shadow:0 20px 25px -5px rgba(0,0,0,0.25);overflow:hidden;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 20px;background:#0f172a;color:#ffffff;">
            <h3 style="margin:0;font-size:14px;font-weight:700;">Embed Codes & Shortcodes</h3>
            <button type="button" id="wppoppop-builder-embed-close" style="background:transparent;border:none;color:#94a3b8;font-size:22px;cursor:pointer;line-height:1;">&times;</button>
        </div>
        <div style="padding:20px;">
            <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">Standard Shortcode</label>
            <input type="text" class="widefat" id="wppoppop-embed-sc" readonly style="font-family:monospace;margin-bottom:12px;">

            <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">Content Locker Shortcode</label>
            <input type="text" class="widefat" id="wppoppop-embed-locker" readonly style="font-family:monospace;margin-bottom:12px;">

            <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">Click Trigger Link</label>
            <input type="text" class="widefat" id="wppoppop-embed-click" readonly style="font-family:monospace;">
        </div>
    </div>
</div>

<!-- Live Sandbox Preview Modal -->
<div class="wppoppop-modal-backdrop" id="wppoppop-builder-preview-modal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,0.8);backdrop-filter:blur(4px);z-index:999999;align-items:center;justify-content:center;">
    <div style="background:#ffffff;border-radius:8px;width:720px;max-width:95vw;max-height:90vh;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 25px 50px -12px rgba(0,0,0,0.5);">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 18px;background:#0f172a;color:#ffffff;">
            <h3 style="margin:0;font-size:14px;font-weight:700;">Interactive Sandbox Preview</h3>
            <button type="button" id="wppoppop-builder-preview-close" style="background:transparent;border:none;color:#94a3b8;font-size:22px;cursor:pointer;line-height:1;">&times;</button>
        </div>
        <div id="wppoppop-preview-sandbox-root" style="flex:1;overflow:auto;padding:30px;display:flex;align-items:center;justify-content:center;background:#f1f5f9;"></div>
    </div>
</div>

<!-- Parity: Embed Code & Sharing Modal -->
<div class="wppoppop-modal-backdrop" id="wppoppop-modal-embed" style="display:none;">
    <div class="wppoppop-modal-dialog wppoppop-modal-embed-dialog">
        <div class="wppoppop-modal-header">
            <h3><span class="dashicons dashicons-code-standards"></span> <?php esc_html_e('How to Embed & Display Popup', 'wppoppop'); ?></h3>
            <button type="button" class="wppoppop-modal-close" data-modal="wppoppop-modal-embed">&times;</button>
        </div>
        <div class="wppoppop-modal-body">
            <div class="wppoppop-embed-tabs">
                <button type="button" class="wppoppop-embed-tab active" data-tab="shortcode"><?php esc_html_e('Shortcode', 'wppoppop'); ?></button>
                <button type="button" class="wppoppop-embed-tab" data-tab="onclick"><?php esc_html_e('On-Click Link', 'wppoppop'); ?></button>
                <button type="button" class="wppoppop-embed-tab" data-tab="js-api"><?php esc_html_e('JavaScript API', 'wppoppop'); ?></button>
            </div>
            
            <div class="wppoppop-embed-tab-content active" id="embed-tab-shortcode">
                <p class="wppoppop-embed-desc"><?php esc_html_e('Paste this shortcode anywhere inside your WordPress page, post, or template:', 'wppoppop'); ?></p>
                <div class="wppoppop-code-box">
                    <input type="text" id="wppoppop-embed-shortcode-val" readonly value="[wppoppop id=&quot;0&quot;]">
                    <button type="button" class="wppoppop-btn-copy" data-target="wppoppop-embed-shortcode-val">
                        <span class="dashicons dashicons-clipboard"></span> <?php esc_html_e('Copy', 'wppoppop'); ?>
                    </button>
                </div>
            </div>

            <div class="wppoppop-embed-tab-content" id="embed-tab-onclick" style="display:none;">
                <p class="wppoppop-embed-desc"><?php esc_html_e('Attach this HTML snippet to any anchor link, button, or menu item to open the popup upon click:', 'wppoppop'); ?></p>
                <div class="wppoppop-code-box">
                    <input type="text" id="wppoppop-embed-onclick-val" readonly value="&lt;a href=&quot;#&quot; class=&quot;wppoppop-trigger&quot; data-wppoppop-id=&quot;0&quot;&gt;Open Popup&lt;/a&gt;">
                    <button type="button" class="wppoppop-btn-copy" data-target="wppoppop-embed-onclick-val">
                        <span class="dashicons dashicons-clipboard"></span> <?php esc_html_e('Copy', 'wppoppop'); ?>
                    </button>
                </div>
            </div>

            <div class="wppoppop-embed-tab-content" id="embed-tab-js-api" style="display:none;">
                <p class="wppoppop-embed-desc"><?php esc_html_e('Trigger this popup programmatically via custom JavaScript or third-party webhooks:', 'wppoppop'); ?></p>
                <div class="wppoppop-code-box">
                    <input type="text" id="wppoppop-embed-js-val" readonly value="window.WpPopPop &amp;&amp; window.WpPopPop.show(0);">
                    <button type="button" class="wppoppop-btn-copy" data-target="wppoppop-embed-js-val">
                        <span class="dashicons dashicons-clipboard"></span> <?php esc_html_e('Copy', 'wppoppop'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Parity: Sandbox Multi-Device Live Preview Modal -->
<div class="wppoppop-modal-backdrop" id="wppoppop-modal-preview" style="display:none;">
    <div class="wppoppop-modal-dialog wppoppop-modal-preview-dialog">
        <div class="wppoppop-modal-header" style="justify-content:space-between;">
            <div class="wppoppop-preview-device-switcher">
                <button type="button" class="wppoppop-device-btn active" data-device="desktop" title="Desktop View (100%)">
                    <span class="dashicons dashicons-desktop"></span> <?php esc_html_e('Desktop', 'wppoppop'); ?>
                </button>
                <button type="button" class="wppoppop-device-btn" data-device="tablet" title="Tablet View (768px)">
                    <span class="dashicons dashicons-tablet"></span> <?php esc_html_e('Tablet', 'wppoppop'); ?>
                </button>
                <button type="button" class="wppoppop-device-btn" data-device="mobile" title="Mobile View (375px)">
                    <span class="dashicons dashicons-smartphone"></span> <?php esc_html_e('Mobile', 'wppoppop'); ?>
                </button>
            </div>
            <button type="button" class="wppoppop-modal-close" data-modal="wppoppop-modal-preview">&times;</button>
        </div>
        <div class="wppoppop-preview-stage-container">
            <div class="wppoppop-preview-viewport-wrapper device-desktop" id="wppoppop-preview-viewport">
                <div class="wppoppop-preview-render-root" id="wppoppop-preview-render-root">
                    <!-- Cloned stage renders live here -->
                </div>
            </div>
        </div>
    </div>
</div>
