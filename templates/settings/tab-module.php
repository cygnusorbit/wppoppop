<?php
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('WpPopPop_Admin_Settings')) {
    require_once WPPOPPOP_PATH . 'includes/admin/class-admin-settings.php';
}
$__mods = array_merge(
    WpPopPop_Admin_Settings::get_module_defaults(),
    (isset($settings['modules']) && is_array($settings['modules'])) ? $settings['modules'] : []
);
$__locked = WpPopPop_Admin_Settings::get_locked_modules();
$__all    = WpPopPop_Admin_Settings::get_modules();
$__order  = WpPopPop_Admin_Settings::normalize_module_order(isset($settings['module_order']) ? $settings['module_order'] : WpPopPop_Admin_Settings::get_module_order());
?>
<style>
.wppoppop-mod-list{max-width:720px;margin:0;padding:0;list-style:none}
.wppoppop-mod-list li{display:flex;align-items:center;justify-content:space-between;padding:12px 14px;background:#fff;border:1px solid #dcdcde;margin:0 0 -1px}
.wppoppop-mod-list .mod-name{font-size:13px;font-weight:600;color:#1d2327}
.wppoppop-mod-list .mod-num{display:inline-block;min-width:22px;color:#787c82;font-weight:400}
.wppoppop-switch-mod{position:relative;display:inline-block;width:44px;height:24px;flex:none}
.wppoppop-switch-mod input{position:absolute;opacity:0;width:100%;height:100%;margin:0;cursor:pointer;z-index:2}
.wppoppop-switch-mod .trk{position:absolute;inset:0;background:#c3c4c7;border-radius:24px;transition:background .15s}
.wppoppop-switch-mod .trk:before{content:"";position:absolute;left:3px;top:3px;width:18px;height:18px;background:#fff;border-radius:50%;transition:transform .15s;box-shadow:0 1px 2px rgba(0,0,0,.3)}
.wppoppop-switch-mod input:checked + .trk{background:#00a32a}
.wppoppop-switch-mod input:checked + .trk:before{transform:translateX(20px)}
.wppoppop-switch-mod input:focus + .trk{box-shadow:0 0 0 2px #2271b1}
.wppoppop-switch-mod.is-locked{cursor:not-allowed}
.wppoppop-switch-mod.is-locked input[type=checkbox]{display:none !important}
.wppoppop-switch-mod.is-locked .trk{background:#00a32a;opacity:.55}
.wppoppop-switch-mod.is-locked .trk:before{transform:translateX(20px)}
.wppoppop-mod-list .mod-lock{margin-left:8px;font-size:11px;font-weight:400;color:#787c82}
.wppoppop-mod-list .wppoppop-mod-handle{cursor:grab;color:#787c82;margin-right:8px;vertical-align:middle}
.wppoppop-mod-list .wppoppop-mod-handle:active{cursor:grabbing}
.wppoppop-mod-list .mod-name{display:inline-flex;align-items:center}
.wppoppop-mod-list li.ui-sortable-helper{box-shadow:0 4px 14px rgba(0,0,0,.18)}
.wppoppop-mod-list li.wppoppop-mod-ph{background:#f0f6fc;border:1px dashed #2271b1;visibility:visible !important}
</style>
<div class="wppoppop-settings-section">
    <h3><?php esc_html_e('Popup Settings Modules', 'wppoppop'); ?></h3>
    <p class="description"><?php esc_html_e('Drag a module to change its position; the number is its position. Switch a module off to hide it from Popup Settings in the builder. Saved values are kept, so switching it back on restores them. Save settings, then reload the builder.', 'wppoppop'); ?></p>
    <ul class="wppoppop-mod-list" id="wppoppop-mod-list">
        <?php foreach ($__order as $__i => $__k) :
            $__label = $__all[$__k];
            $__lk = in_array($__k, $__locked, true);
            $__on = $__lk || !empty($__mods[$__k]);
            $__n  = $__i + 1;
            ?>
            <li data-module="<?php echo esc_attr($__k); ?>">
                <span class="mod-name"><span class="dashicons dashicons-menu wppoppop-mod-handle" title="<?php esc_attr_e('Drag to reorder', 'wppoppop'); ?>"></span><input type="hidden" name="settings[module_order][]" value="<?php echo esc_attr($__k); ?>"><span class="mod-num"><?php echo (int) $__n; ?>.</span><?php echo esc_html($__label); ?><?php if ($__lk) : ?><span class="mod-lock"><?php esc_html_e('(always enabled)', 'wppoppop'); ?></span><?php endif; ?></span>
                <label class="wppoppop-switch-mod<?php echo $__lk ? ' is-locked' : ''; ?>" title="<?php echo esc_attr($__label); ?>">
                    <input type="hidden" name="settings[modules][<?php echo esc_attr($__k); ?>]" value="<?php echo $__lk ? '1' : '0'; ?>">
                    <input type="checkbox" name="settings[modules][<?php echo esc_attr($__k); ?>]" value="1" <?php checked($__on); ?><?php echo $__lk ? ' disabled="disabled" onclick="return false;"' : ''; ?>>
                    <span class="trk"></span>
                </label>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
