<?php
/**
 * WpPopPop Builder Startup Guide Modal Template
 *
 * @package WpPopPop
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<!-- === WPPOPPOP_STARTUP_GUIDE_START === -->
<div id="wppoppop-startup-guide-backdrop" class="wppoppop-guide-backdrop" style="display: none;">
    <div id="wppoppop-startup-guide" class="wppoppop-guide-modal" role="dialog" aria-modal="true" aria-labelledby="wppoppop-guide-title">
        <header class="wppoppop-guide-header">
            <span class="wppoppop-guide-step-badge" id="wppoppop-guide-step-indicator">Step 1 of 4</span>
            <button type="button" class="wppoppop-guide-close" id="wppoppop-guide-close-btn" aria-label="Close Guide">&times;</button>
        </header>

        <div class="wppoppop-guide-body">
            <!-- Step 1 -->
            <section class="wppoppop-guide-step active" data-step="1">
                <div class="wppoppop-guide-icon-box">&#9635;</div>
                <h3 class="wppoppop-guide-title">Welcome to the Popup Builder</h3>
                <p>Build custom modals with real-time visual precision. Your canvas initializes at the standard <strong>300px &times; 400px</strong> baseline, and automatically scales proportionally on mobile devices with zero coordinate distortion.</p>
            </section>

            <!-- Step 2 -->
            <section class="wppoppop-guide-step" data-step="2">
                <div class="wppoppop-guide-icon-box">&#10010;</div>
                <h3 class="wppoppop-guide-title">Adding & Positioning Elements</h3>
                <p>Click any item from the left <strong>Elements</strong> sidebar (Image, Shape, Text, or Button) to place it on the canvas. Drag layers freely with 1:1 cursor alignment, or use arrow keys to adjust bounds.</p>
            </section>

            <!-- Step 3 -->
            <section class="wppoppop-guide-step" data-step="3">
                <div class="wppoppop-guide-icon-box">&#9881;</div>
                <h3 class="wppoppop-guide-title">Inspector & Layer Settings</h3>
                <p>Select any canvas element to configure its properties in the right <strong>Inspector</strong>. Refine Coordinates & Bounds, SVG strokes, border radius, drop shadows, or background fills with instant live sync.</p>
            </section>

            <!-- Step 4 -->
            <section class="wppoppop-guide-step" data-step="4">
                <div class="wppoppop-guide-icon-box">&#10003;</div>
                <h3 class="wppoppop-guide-title">Test & Save Your Popup</h3>
                <p>Use the top toolbar to switch between <strong>Desktop</strong> and <strong>Mobile</strong> preview modes. Once satisfied, click <strong>Save Popup</strong> to persist your configuration to the live frontend renderer.</p>
            </section>
        </div>

        <footer class="wppoppop-guide-footer">
            <label class="wppoppop-guide-checkbox">
                <input type="checkbox" id="wppoppop-guide-dont-show"> Do not show automatically
            </label>
            <div class="wppoppop-guide-actions">
                <button type="button" class="button button-secondary" id="wppoppop-guide-prev" disabled>Back</button>
                <button type="button" class="button button-primary" id="wppoppop-guide-next">Next &rarr;</button>
                <button type="button" class="button button-primary" id="wppoppop-guide-finish" style="display: none;">Get Started</button>
            </div>
        </footer>
    </div>
</div>
<!-- === WPPOPPOP_STARTUP_GUIDE_END === -->
<?php if (file_exists(__DIR__ . '/admin/builder-startup-guide.php')) { include __DIR__ . '/admin/builder-startup-guide.php'; } elseif (file_exists(__DIR__ . '/builder-startup-guide.php')) { include __DIR__ . '/builder-startup-guide.php'; } ?>
