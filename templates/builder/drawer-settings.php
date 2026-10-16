<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<!-- Slide-Out Backdrop -->
<div id="wppoppop-settings-backdrop" class="wppoppop-settings-backdrop"></div>

<!-- Hardware-Accelerated Slide-In Campaign Settings Drawer -->
<div id="wppoppop-settings-drawer" class="wppoppop-settings-drawer">
    <div class="wppoppop-settings-drawer-header" style="display:flex;align-items:center;justify-content:space-between;padding:14px 18px;background:#0f172a;color:#ffffff;flex-shrink:0;">
        <h3 class="wppoppop-settings-drawer-title" style="margin:0;font-size:15px;font-weight:700;color:#ffffff !important;line-height:1.3;">Campaign Settings</h3>
        <button type="button" id="wppoppop-settings-drawer-close" style="background:transparent;border:none;color:#94a3b8;font-size:22px;cursor:pointer;line-height:1;">&times;</button>
    </div>

    <div style="flex:1;overflow-y:auto;padding:16px;">
        <div class="wppoppop-accordion-group">
            
            <?php
            $wppoppop_acc_files = glob(__DIR__ . '/settings/acc-*.php');
            if (!empty($wppoppop_acc_files)) {
                sort($wppoppop_acc_files);
                foreach ($wppoppop_acc_files as $wppoppop_acc_file) {
                    include $wppoppop_acc_file;
                }
            }
            ?>

        </div>
    </div>
</div>
<?php /* Decomposed Phase 4 Settings Tokens (templates/builder/settings/acc-*.php): set-exit-intent-sensitivity, set-trigger-scroll-depth, set-canvas-autoclose */ ?>
