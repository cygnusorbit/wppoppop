<?php
if (!defined('ABSPATH')) exit;
?>
<!-- TAB 4: DATA TAB (FORM FAMILY ELEMENTS 10-25) -->
<div id="insp-tab-data" class="wppoppop-insp-content wppoppop-insp-tab-content wppoppop-tab-pane" data-tab-content="data" style="display:none;padding:14px;">
    <div style="background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:14px;margin-bottom:14px;">
        <div style="font-size:11px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:12px;letter-spacing:0.6px;"><?php esc_html_e('Data & Field Binding', 'wppoppop'); ?></div>

        <div style="margin-bottom:12px;">
            <label for="prop-data-default" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e('DEFAULT VALUE', 'wppoppop'); ?></label>
            <input type="text" id="prop-data-default" class="widefat wppoppop-input" placeholder="<?php esc_attr_e('Static initial value...', 'wppoppop'); ?>" style="width:100%;height:36px;background:#090d16;border:1px solid #1e293b;color:#f8fafc;border-radius:6px;padding:0 10px;font-size:12px;">
            <span style="display:block;font-size:10px;color:#64748b;margin-top:3px;"><?php esc_html_e('Enter default value of the field.', 'wppoppop'); ?></span>
        </div>

        <div style="margin-bottom:12px;padding-top:8px;border-top:1px solid #1e293b;">
            <label style="display:flex;align-items:center;justify-content:space-between;cursor:pointer;font-size:11px;font-weight:700;color:#cbd5e1;text-transform:uppercase;">
                <span><?php esc_html_e('DYNAMIC DEFAULT VALUE', 'wppoppop'); ?></span>
                <input type="checkbox" id="prop-data-dynamic-default" value="1">
            </label>
            <span style="display:block;font-size:10px;color:#64748b;margin-top:3px;"><?php esc_html_e('Set default value dynamically from URL query parameters.', 'wppoppop'); ?></span>
        </div>

        <div id="prop-data-param-wrap" style="display:none;margin-bottom:12px;">
            <label for="prop-data-param-name" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e('PARAMETER NAME', 'wppoppop'); ?></label>
            <input type="text" id="prop-data-param-name" class="widefat wppoppop-input" placeholder="<?php esc_attr_e('e.g. email or user_email', 'wppoppop'); ?>" style="width:100%;height:36px;background:#090d16;border:1px solid #1e293b;color:#f8fafc;border-radius:6px;padding:0 10px;font-size:12px;">
            <span style="display:block;font-size:10px;color:#64748b;margin-top:3px;"><?php esc_html_e('Enter URL parameter name (e.g. <code>?email=user@domain.com</code>).', 'wppoppop'); ?></span>
        </div>

        <div style="margin-bottom:12px;padding-top:8px;border-top:1px solid #1e293b;">
            <label style="display:flex;align-items:center;justify-content:space-between;cursor:pointer;font-size:11px;font-weight:700;color:#cbd5e1;text-transform:uppercase;">
                <span><?php esc_html_e('READ ONLY', 'wppoppop'); ?></span>
                <input type="checkbox" id="prop-data-readonly" value="1">
            </label>
            <span style="display:block;font-size:10px;color:#64748b;margin-top:3px;"><?php esc_html_e('Lock input field so visitors cannot edit its value.', 'wppoppop'); ?></span>
        </div>

        <div style="padding-top:8px;border-top:1px solid #1e293b;">
            <label style="display:flex;align-items:center;justify-content:space-between;cursor:pointer;font-size:11px;font-weight:700;color:#cbd5e1;text-transform:uppercase;">
                <span><?php esc_html_e('SAVE TO DATABASE', 'wppoppop'); ?></span>
                <input type="checkbox" id="prop-data-save-db" value="1" checked>
            </label>
            <span style="display:block;font-size:10px;color:#64748b;margin-top:3px;"><?php esc_html_e('Store submitted field value in the database record.', 'wppoppop'); ?></span>
        </div>
    </div>
</div>

<!-- TAB 5: ADVANCED TAB (FORM FAMILY ELEMENTS 10-25) -->
<div id="insp-tab-advanced" class="wppoppop-insp-content wppoppop-insp-tab-content wppoppop-tab-pane" data-tab-content="advanced" style="display:none;padding:14px;">
    <div style="background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:14px;margin-bottom:14px;">
        <div style="font-size:11px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:12px;letter-spacing:0.6px;"><?php esc_html_e('Advanced, Filters & Validators', 'wppoppop'); ?></div>

        <div style="margin-bottom:12px;">
            <label for="prop-adv-field-id" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e('ID (FIELD IDENTIFIER)', 'wppoppop'); ?></label>
            <input type="text" id="prop-adv-field-id" class="widefat wppoppop-input" placeholder="<?php esc_attr_e('e.g. 11 or email', 'wppoppop'); ?>" style="width:100%;height:36px;background:#090d16;border:1px solid #1e293b;color:#f8fafc;border-radius:6px;padding:0 10px;font-size:12px;">
        </div>

        <div style="margin-bottom:12px;">
            <label for="prop-adv-input-mask" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e('INPUT MASK PATTERN', 'wppoppop'); ?></label>
            <input type="text" id="prop-adv-input-mask" class="widefat wppoppop-input" placeholder="e.g. (999) 999-9999" style="width:100%;height:36px;background:#090d16;border:1px solid #1e293b;color:#f8fafc;border-radius:6px;padding:0 10px;font-size:12px;">
        </div>

        <div style="margin-bottom:14px;padding-top:10px;border-top:1px solid #1e293b;">
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:6px;text-transform:uppercase;"><?php esc_html_e('FILTERS', 'wppoppop'); ?></label>
            <div id="wppoppop-adv-filters-list" class="wppoppop-adv-badge-wrap" style="display:flex;flex-wrap:wrap;gap:6px;min-height:32px;padding:6px;background:#090d16;border:1px solid #1e293b;border-radius:6px;margin-bottom:8px;"></div>
            <div style="display:flex;gap:5px;flex-wrap:wrap;">
                <button type="button" class="wppoppop-adv-add-btn" data-kind="filter" data-val="alpha"><?php esc_html_e('+ Alpha', 'wppoppop'); ?></button>
                <button type="button" class="wppoppop-adv-add-btn" data-kind="filter" data-val="alphanumeric"><?php esc_html_e('+ Alphanumeric', 'wppoppop'); ?></button>
                <button type="button" class="wppoppop-adv-add-btn" data-kind="filter" data-val="digits"><?php esc_html_e('+ Digits', 'wppoppop'); ?></button>
                <button type="button" class="wppoppop-adv-add-btn" data-kind="filter" data-val="regex"><?php esc_html_e('+ Regex', 'wppoppop'); ?></button>
                <button type="button" class="wppoppop-adv-add-btn" data-kind="filter" data-val="strip_tags"><?php esc_html_e('+ Strip Tags', 'wppoppop'); ?></button>
                <button type="button" class="wppoppop-adv-add-btn" data-kind="filter" data-val="trim"><?php esc_html_e('+ Trim', 'wppoppop'); ?></button>
            </div>
        </div>

        <div style="padding-top:10px;border-top:1px solid #1e293b;">
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:6px;text-transform:uppercase;"><?php esc_html_e('VALIDATORS', 'wppoppop'); ?></label>
            <div id="wppoppop-adv-validators-list" class="wppoppop-adv-badge-wrap" style="display:flex;flex-wrap:wrap;gap:6px;min-height:32px;padding:6px;background:#090d16;border:1px solid #1e293b;border-radius:6px;margin-bottom:8px;"></div>
            <div style="display:flex;gap:5px;flex-wrap:wrap;">
                <button type="button" class="wppoppop-adv-add-btn" data-kind="validator" data-val="required"><?php esc_html_e('+ Required', 'wppoppop'); ?></button>
                <button type="button" class="wppoppop-adv-add-btn" data-kind="validator" data-val="email"><?php esc_html_e('+ Email', 'wppoppop'); ?></button>
                <button type="button" class="wppoppop-adv-add-btn" data-kind="validator" data-val="equal"><?php esc_html_e('+ Equal', 'wppoppop'); ?></button>
                <button type="button" class="wppoppop-adv-add-btn" data-kind="validator" data-val="equal_field"><?php esc_html_e('+ Equal To Field', 'wppoppop'); ?></button>
                <button type="button" class="wppoppop-adv-add-btn" data-kind="validator" data-val="in_array"><?php esc_html_e('+ In Array', 'wppoppop'); ?></button>
                <button type="button" class="wppoppop-adv-add-btn" data-kind="validator" data-val="prevent_duplicates"><?php esc_html_e('+ Prevent Duplicates', 'wppoppop'); ?></button>
            </div>
        </div>
    </div>
</div>
