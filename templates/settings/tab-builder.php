<?php
if (!defined('ABSPATH')) {
    exit;
}
$__bld = array_merge(WpPopPop_Admin_Settings::get_builder_defaults(), (array) $settings);
$__align_on = !empty($__bld['builder_alignment_tool']);
$__vp_on = !empty($__bld['builder_viewport_toggle']);
$__zm_on = !empty($__bld['builder_zoom_select']);
$__st_on = !empty($__bld['builder_save_template']);
$__ex_on = !empty($__bld['builder_extra_elements']);
$__cc_on = !empty($__bld['builder_click_canvas_settings']);
$__ce_on = !empty($__bld['builder_click_element_settings']);
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
        <li>
            <span class="bld-name"><?php esc_html_e('Viewport', 'wppoppop'); ?>
                <span class="bld-desc"><?php esc_html_e('Show the Desktop / Mobile viewport switch in the builder header.', 'wppoppop'); ?></span>
            </span>
            <label class="wppoppop-switch-bld" title="<?php esc_attr_e('Viewport', 'wppoppop'); ?>">
                <input type="hidden" name="settings[builder_viewport_toggle]" value="0">
                <input type="checkbox" name="settings[builder_viewport_toggle]" value="1" <?php checked($__vp_on); ?>>
                <span class="trk"></span>
            </label>
        </li>
        <li>
            <span class="bld-name"><?php esc_html_e('Zoom Select', 'wppoppop'); ?>
                <span class="bld-desc"><?php esc_html_e('Show the stage zoom dropdown (50% - Fit) in the builder header.', 'wppoppop'); ?></span>
            </span>
            <label class="wppoppop-switch-bld" title="<?php esc_attr_e('Zoom Select', 'wppoppop'); ?>">
                <input type="hidden" name="settings[builder_zoom_select]" value="0">
                <input type="checkbox" name="settings[builder_zoom_select]" value="1" <?php checked($__zm_on); ?>>
                <span class="trk"></span>
            </label>
        </li>
        <li>
            <span class="bld-name"><?php esc_html_e('Save As Template', 'wppoppop'); ?>
                <span class="bld-desc"><?php esc_html_e('Show the green Save As Template button in the builder header so popups can be saved to the Popups Library.', 'wppoppop'); ?></span>
            </span>
            <label class="wppoppop-switch-bld" title="<?php esc_attr_e('Save As Template', 'wppoppop'); ?>">
                <input type="hidden" name="settings[builder_save_template]" value="0">
                <input type="checkbox" name="settings[builder_save_template]" value="1" <?php checked($__st_on); ?>>
                <span class="trk"></span>
            </label>
        </li>
        <li>
            <span class="bld-name"><?php esc_html_e('Additional Ribbon Bar Elements', 'wppoppop'); ?>
                <span class="bld-desc"><?php esc_html_e('Show the extra elements (Image Select, Tile, Date, Rating) in the builder ribbon bar. Off by default.', 'wppoppop'); ?></span>
            </span>
            <label class="wppoppop-switch-bld" title="<?php esc_attr_e('Additional Ribbon Bar Elements', 'wppoppop'); ?>">
                <input type="hidden" name="settings[builder_extra_elements]" value="0">
                <input type="checkbox" name="settings[builder_extra_elements]" value="1" <?php checked($__ex_on); ?>>
                <span class="trk"></span>
            </label>
        </li>
        <li>
            <span class="bld-name"><?php esc_html_e('Click Canvas - Open Popup Settings', 'wppoppop'); ?>
                <span class="bld-desc"><?php esc_html_e('Clicking an empty area of the canvas slides in Popup Settings. Off by default.', 'wppoppop'); ?></span>
            </span>
            <label class="wppoppop-switch-bld" title="<?php esc_attr_e('Click Canvas - Open Popup Settings', 'wppoppop'); ?>">
                <input type="hidden" name="settings[builder_click_canvas_settings]" value="0">
                <input type="checkbox" name="settings[builder_click_canvas_settings]" value="1" <?php checked($__cc_on); ?>>
                <span class="trk"></span>
            </label>
        </li>
        <li>
            <span class="bld-name"><?php esc_html_e('Click Element - Open Layer Settings', 'wppoppop'); ?>
                <span class="bld-desc"><?php esc_html_e('Clicking an element on the canvas slides in Layer Settings. Off by default.', 'wppoppop'); ?></span>
            </span>
            <label class="wppoppop-switch-bld" title="<?php esc_attr_e('Click Element - Open Layer Settings', 'wppoppop'); ?>">
                <input type="hidden" name="settings[builder_click_element_settings]" value="0">
                <input type="checkbox" name="settings[builder_click_element_settings]" value="1" <?php checked($__ce_on); ?>>
                <span class="trk"></span>
            </label>
        </li>
    </ul>
</div>
