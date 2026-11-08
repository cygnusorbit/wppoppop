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
        <h3 class="wppoppop-settings-drawer-title" style="margin:0;font-size:15px;font-weight:700;color:#ffffff !important;line-height:1.3;"><?php esc_html_e('Popup Settings', 'wppoppop'); ?></h3>
        <button type="button" id="wppoppop-settings-drawer-close" style="background:transparent;border:none;color:#94a3b8;font-size:22px;cursor:pointer;line-height:1;">&times;</button>
    </div>

    <div style="flex:1;overflow-y:auto;padding:16px;">
        <div class="wppoppop-accordion-group">
            
            <?php
            /* WPPOPPOP_MODULE_ENGINE_DRAWER: built-in accordions (acc-*.php) and declarative modules share one order */
            $wppoppop_acc_files = glob(__DIR__ . '/settings/acc-*.php');
            $wppoppop_acc_decl  = class_exists('WpPopPop_Modules_Engine') ? WpPopPop_Modules_Engine::installed_ui() : [];
            if (!empty($wppoppop_acc_files) || !empty($wppoppop_acc_decl)) {
                if (!class_exists('WpPopPop_Admin_Settings')) {
                    require_once WPPOPPOP_PATH . 'includes/admin/class-admin-settings.php';
                }
                $wppoppop_acc_entries = [];
                foreach ((array) $wppoppop_acc_files as $wppoppop_acc_f) {
                    $wppoppop_acc_entries[basename($wppoppop_acc_f, '.php')] = $wppoppop_acc_f;
                }
                foreach ($wppoppop_acc_decl as $wppoppop_acc_slug => $wppoppop_acc_m) {
                    if (!isset($wppoppop_acc_entries[$wppoppop_acc_slug])) {
                        $wppoppop_acc_entries[$wppoppop_acc_slug] = null;
                    }
                }
                ksort($wppoppop_acc_entries);
                /* Order is controlled globally in Settings > Module */
                $wppoppop_acc_order = array_flip(WpPopPop_Admin_Settings::get_module_order());
                uksort($wppoppop_acc_entries, function ($ka, $kb) use ($wppoppop_acc_order) {
                    $pa = isset($wppoppop_acc_order[$ka]) ? $wppoppop_acc_order[$ka] : PHP_INT_MAX;
                    $pb = isset($wppoppop_acc_order[$kb]) ? $wppoppop_acc_order[$kb] : PHP_INT_MAX;
                    return $pa === $pb ? strcmp($ka, $kb) : ($pa < $pb ? -1 : 1);
                });
                foreach ($wppoppop_acc_entries as $wppoppop_acc_key => $wppoppop_acc_file) {
                    $wppoppop_acc_on   = WpPopPop_Admin_Settings::module_enabled($wppoppop_acc_key);
                    $wppoppop_acc_trusted = ($wppoppop_acc_file === null); /* engine markup is escaped at build time */
                    if ($wppoppop_acc_on && !$wppoppop_acc_trusted) {
                        include $wppoppop_acc_file;
                        continue;
                    }
                    ob_start();
                    if ($wppoppop_acc_trusted) {
                        echo WpPopPop_Modules_Engine::render_accordion($wppoppop_acc_decl[$wppoppop_acc_key]); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    } else {
                        include $wppoppop_acc_file;
                    }
                    $wppoppop_acc_html = ob_get_clean();
                    if (!$wppoppop_acc_on) {
                        // Disabled: keep the markup (so the save flush never wipes stored values) but hide it.
                        $wppoppop_acc_html = preg_replace(
                            '/<div class="wppoppop-acc-item"/',
                            '<div class="wppoppop-acc-item wppoppop-module-off" data-module="' . esc_attr($wppoppop_acc_key) . '" style="display:none !important;"',
                            $wppoppop_acc_html, 1, $wppoppop_acc_hit
                        );
                        if (empty($wppoppop_acc_hit)) {
                            $wppoppop_acc_html = '<div class="wppoppop-module-off" style="display:none !important;">' . $wppoppop_acc_html . '</div>';
                        }
                    }
                    echo $wppoppop_acc_trusted ? $wppoppop_acc_html : wp_kses_post( $wppoppop_acc_html ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                }
            }
            ?>

        </div>
        <div class="wppoppop-defaults-box" style="margin-top:16px;padding:12px;border:1px dashed #cbd5e1;border-radius:6px;background:#f8fafc;">
            <p style="margin:0 0 8px;font-size:12px;line-height:1.4;color:#475569;"><?php esc_html_e('Use this popup\'s canvas and popup settings (not its layers) as the starting point for every new popup.', 'wppoppop'); ?></p>
            <button type="button" class="button" data-wpp-defaults-save="popup"><?php esc_html_e('Save as default for new popups', 'wppoppop'); ?></button>
        </div>
    </div>
</div>
<?php /* Decomposed Phase 4 Settings Tokens (templates/builder/settings/acc-*.php): set-exit-intent-sensitivity, set-trigger-scroll-depth, set-canvas-autoclose */ ?>
