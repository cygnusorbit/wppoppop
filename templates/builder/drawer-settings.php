<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<!-- Slide-Out Backdrop -->
<div id="wppoppop-settings-backdrop" class="wppoppop-settings-backdrop"></div>

<!-- Hardware-Accelerated Slide-In Popup Settings Drawer -->
<div id="wppoppop-settings-drawer" class="wppoppop-settings-drawer">
    <div class="wppoppop-settings-drawer-header" style="display:flex;align-items:center;justify-content:space-between;padding:14px 18px;background:#0f172a;color:#ffffff;flex-shrink:0;">
        <h3 class="wppoppop-settings-drawer-title" style="margin:0;font-size:15px;font-weight:700;color:#ffffff !important;line-height:1.3;">Popup Settings</h3>
        <button type="button" id="wppoppop-settings-drawer-close" style="background:transparent;border:none;color:#94a3b8;font-size:22px;cursor:pointer;line-height:1;">&times;</button>
    </div>

    <div style="flex:1;overflow-y:auto;padding:16px;">
        <div class="wppoppop-accordion-group">
            
            <?php
            $wppoppop_acc_files = glob(__DIR__ . '/settings/acc-*.php');
            if (!empty($wppoppop_acc_files)) {
                sort($wppoppop_acc_files);
                if (!class_exists('WpPopPop_Admin_Settings')) {
                    require_once WPPOPPOP_PATH . 'includes/admin/class-admin-settings.php';
                }
                foreach ($wppoppop_acc_files as $wppoppop_acc_file) {
                    $wppoppop_acc_key = basename($wppoppop_acc_file, '.php');
                    if (WpPopPop_Admin_Settings::module_enabled($wppoppop_acc_key)) {
                        include $wppoppop_acc_file;
                        continue;
                    }
                    // Disabled: keep the markup (so the save flush never wipes stored values) but hide it.
                    ob_start();
                    include $wppoppop_acc_file;
                    $wppoppop_acc_html = ob_get_clean();
                    $wppoppop_acc_html = preg_replace(
                        '/<div class="wppoppop-acc-item"/',
                        '<div class="wppoppop-acc-item wppoppop-module-off" data-module="' . esc_attr($wppoppop_acc_key) . '" style="display:none !important;"',
                        $wppoppop_acc_html, 1, $wppoppop_acc_hit
                    );
                    if (empty($wppoppop_acc_hit)) {
                        $wppoppop_acc_html = '<div class="wppoppop-module-off" style="display:none !important;">' . $wppoppop_acc_html . '</div>';
                    }
                    echo $wppoppop_acc_html;
                }
            }
            ?>

        </div>
    </div>
</div>
<?php /* Decomposed Phase 4 Settings Tokens (templates/builder/settings/acc-*.php): set-exit-intent-sensitivity, set-trigger-scroll-depth, set-canvas-autoclose */ ?>
