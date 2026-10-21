<?php
if (!defined('ABSPATH')) {
    exit;
}
$__align_on = !array_key_exists('builder_alignment_tool', (array) $settings) || !empty($settings['builder_alignment_tool']);
?>
<style>
.wppoppop-bld-list{max-width:720px;margin:0;padding:0;list-style:none}
.wppoppop-bld-list li{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:12px 14px;background:#fff;border:1px solid #dcdcde}
.wppoppop-bld-list .bld-name{font-size:13px;font-weight:600;color:#1d2327}
.wppoppop-bld-list .bld-desc{display:block;margin-top:2px;font-size:12px;font-weight:400;color:#646970}
.wppoppop-switch-bld{position:relative;display:inline-block;width:44px;height:24px;flex:none}
.wppoppop-switch-bld input{position:absolute;opacity:0;width:100%;height:100%;margin:0;cursor:pointer;z-index:2}
.wppoppop-switch-bld .trk{position:absolute;inset:0;background:#c3c4c7;border-radius:24px;transition:background .15s}
.wppoppop-switch-bld .trk:before{content:"";position:absolute;left:3px;top:3px;width:18px;height:18px;background:#fff;border-radius:50%;transition:transform .15s;box-shadow:0 1px 2px rgba(0,0,0,.3)}
.wppoppop-switch-bld input:checked + .trk{background:#00a32a}
.wppoppop-switch-bld input:checked + .trk:before{transform:translateX(20px)}
.wppoppop-switch-bld input:focus + .trk{box-shadow:0 0 0 2px #2271b1}
</style>
<div class="wppoppop-settings-section">
    <h3><?php esc_html_e('Builder Tools', 'wppoppop'); ?></h3>
    <p class="description"><?php esc_html_e('Turn visual builder tools on or off. Save settings, then reload the builder.', 'wppoppop'); ?></p>
    <ul class="wppoppop-bld-list">
        <li>
            <span class="bld-name"><?php esc_html_e('Alignment Tool', 'wppoppop'); ?>
                <span class="bld-desc"><?php esc_html_e('Floating toolbar on the stage: align left / center / right, top / middle / bottom, and distribute.', 'wppoppop'); ?></span>
            </span>
            <label class="wppoppop-switch-bld" title="<?php esc_attr_e('Alignment Tool', 'wppoppop'); ?>">
                <input type="hidden" name="settings[builder_alignment_tool]" value="0">
                <input type="checkbox" name="settings[builder_alignment_tool]" value="1" <?php checked($__align_on); ?>>
                <span class="trk"></span>
            </label>
        </li>
    </ul>
</div>
