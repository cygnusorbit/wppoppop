<?php
if (!defined('ABSPATH')) exit;
?>
<!-- DECOMPOSED MODULE 15: SELECT DROPDOWN (select / dropdown) -->
<div class="element-panel wppoppop-panel-specific" id="panel-elem-select" data-elem-family="form" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;"><?php esc_html_e('15. Select Dropdown Options', 'wppoppop'); ?></div>
    <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e('FIRST OPTION PLACEHOLDER', 'wppoppop'); ?></label>
    <input type="text" id="prop-select-first" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="firstOption" placeholder="<?php esc_attr_e('Select an option...', 'wppoppop'); ?>" style="margin-bottom:10px;font-size:12px;">
    <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e('DROPDOWN CHOICES (ONE PER LINE)', 'wppoppop'); ?></label>
    <textarea id="prop-select-options" rows="4" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="optionsList" placeholder="Option 1&#10;Option 2&#10;Option 3" style="font-size:12px;"></textarea>
</div>
