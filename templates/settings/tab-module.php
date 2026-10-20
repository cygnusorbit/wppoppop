<?php
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('WpPopPop_Admin_Settings')) {
    require_once WPPOPPOP_PATH . 'includes/admin/class-admin-settings.php';
}
$__mods = (isset($settings['modules']) && is_array($settings['modules'])) ? $settings['modules'] : [];
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
</style>
<div class="wppoppop-settings-section">
    <h3><?php esc_html_e('Campaign Settings Modules', 'wppoppop'); ?></h3>
    <p class="description"><?php esc_html_e('Switch a module off to hide it from Campaign Settings in the builder. Saved values are kept, so switching it back on restores them. Save settings, then reload the builder.', 'wppoppop'); ?></p>
    <ul class="wppoppop-mod-list">
        <?php foreach (WpPopPop_Admin_Settings::get_modules() as $__k => $__label) :
            $__on = !array_key_exists($__k, $__mods) || !empty($__mods[$__k]);
            $__n  = (int) substr($__k, 4, 2);
            ?>
            <li>
                <span class="mod-name"><span class="mod-num"><?php echo (int) $__n; ?>.</span><?php echo esc_html($__label); ?></span>
                <label class="wppoppop-switch-mod" title="<?php echo esc_attr($__label); ?>">
                    <input type="hidden" name="settings[modules][<?php echo esc_attr($__k); ?>]" value="0">
                    <input type="checkbox" name="settings[modules][<?php echo esc_attr($__k); ?>]" value="1" <?php checked($__on); ?>>
                    <span class="trk"></span>
                </label>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
