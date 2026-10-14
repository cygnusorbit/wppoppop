<?php
if (!defined('ABSPATH')) exit;
?>
<!-- TAB 4: GREENPOP DATA TAB (FORM FAMILY ELEMENTS 10-25) -->
<div id="insp-tab-data" class="wppoppop-insp-content wppoppop-insp-tab-content wppoppop-tab-pane" data-tab-content="data" style="display:none;padding:14px;">
    <div style="background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:14px;margin-bottom:14px;">
        <div style="font-size:11px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:12px;letter-spacing:0.6px;">Data &amp; Field Binding</div>

        <div style="margin-bottom:12px;">
            <label for="prop-data-default" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">DEFAULT VALUE</label>
            <input type="text" id="prop-data-default" class="widefat wppoppop-input" placeholder="Static initial value..." style="width:100%;height:36px;background:#090d16;border:1px solid #1e293b;color:#f8fafc;border-radius:6px;padding:0 10px;font-size:12px;">
            <span style="display:block;font-size:10px;color:#64748b;margin-top:3px;">Enter default value of the field.</span>
        </div>

        <div style="margin-bottom:12px;padding-top:8px;border-top:1px solid #1e293b;">
            <label style="display:flex;align-items:center;justify-content:space-between;cursor:pointer;font-size:11px;font-weight:700;color:#cbd5e1;text-transform:uppercase;">
                <span>DYNAMIC DEFAULT VALUE</span>
                <input type="checkbox" id="prop-data-dynamic-default" value="1">
            </label>
            <span style="display:block;font-size:10px;color:#64748b;margin-top:3px;">Set default value dynamically from URL query parameters.</span>
        </div>

        <div id="prop-data-param-wrap" style="display:none;margin-bottom:12px;">
            <label for="prop-data-param-name" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">PARAMETER NAME</label>
            <input type="text" id="prop-data-param-name" class="widefat wppoppop-input" placeholder="e.g. email or user_email" style="width:100%;height:36px;background:#090d16;border:1px solid #1e293b;color:#f8fafc;border-radius:6px;padding:0 10px;font-size:12px;">
            <span style="display:block;font-size:10px;color:#64748b;margin-top:3px;">Enter URL parameter name (e.g. <code>?email=user@domain.com</code>).</span>
        </div>

        <div style="margin-bottom:12px;padding-top:8px;border-top:1px solid #1e293b;">
            <label style="display:flex;align-items:center;justify-content:space-between;cursor:pointer;font-size:11px;font-weight:700;color:#cbd5e1;text-transform:uppercase;">
                <span>READ ONLY</span>
                <input type="checkbox" id="prop-data-readonly" value="1">
            </label>
            <span style="display:block;font-size:10px;color:#64748b;margin-top:3px;">Lock input field so visitors cannot edit its value.</span>
        </div>

        <div style="padding-top:8px;border-top:1px solid #1e293b;">
            <label style="display:flex;align-items:center;justify-content:space-between;cursor:pointer;font-size:11px;font-weight:700;color:#cbd5e1;text-transform:uppercase;">
                <span>SAVE TO DATABASE</span>
                <input type="checkbox" id="prop-data-save-db" value="1" checked>
            </label>
            <span style="display:block;font-size:10px;color:#64748b;margin-top:3px;">Store submitted field value in the database record.</span>
        </div>
    </div>
</div>

<!-- TAB 5: GREENPOP ADVANCED TAB (FORM FAMILY ELEMENTS 10-25) -->
<div id="insp-tab-advanced" class="wppoppop-insp-content wppoppop-insp-tab-content wppoppop-tab-pane" data-tab-content="advanced" style="display:none;padding:14px;">
    <div style="background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:14px;margin-bottom:14px;">
        <div style="font-size:11px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:12px;letter-spacing:0.6px;">Advanced, Filters &amp; Validators</div>

        <div style="margin-bottom:12px;">
            <label for="prop-adv-field-id" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">ID (FIELD IDENTIFIER)</label>
            <input type="text" id="prop-adv-field-id" class="widefat wppoppop-input" placeholder="e.g. 11 or email" style="width:100%;height:36px;background:#090d16;border:1px solid #1e293b;color:#f8fafc;border-radius:6px;padding:0 10px;font-size:12px;">
        </div>

        <div style="margin-bottom:12px;">
            <label for="prop-adv-input-mask" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">INPUT MASK PATTERN</label>
            <input type="text" id="prop-adv-input-mask" class="widefat wppoppop-input" placeholder="e.g. (999) 999-9999" style="width:100%;height:36px;background:#090d16;border:1px solid #1e293b;color:#f8fafc;border-radius:6px;padding:0 10px;font-size:12px;">
        </div>

        <div style="margin-bottom:14px;padding-top:10px;border-top:1px solid #1e293b;">
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:6px;text-transform:uppercase;">FILTERS</label>
            <div id="wppoppop-adv-filters-list" class="wppoppop-adv-badge-wrap" style="display:flex;flex-wrap:wrap;gap:6px;min-height:32px;padding:6px;background:#090d16;border:1px solid #1e293b;border-radius:6px;margin-bottom:8px;"></div>
            <div style="display:flex;gap:5px;flex-wrap:wrap;">
                <button type="button" class="wppoppop-adv-add-btn" data-kind="filter" data-val="alpha">+ Alpha</button>
                <button type="button" class="wppoppop-adv-add-btn" data-kind="filter" data-val="alphanumeric">+ Alphanumeric</button>
                <button type="button" class="wppoppop-adv-add-btn" data-kind="filter" data-val="digits">+ Digits</button>
                <button type="button" class="wppoppop-adv-add-btn" data-kind="filter" data-val="regex">+ Regex</button>
                <button type="button" class="wppoppop-adv-add-btn" data-kind="filter" data-val="strip_tags">+ Strip Tags</button>
                <button type="button" class="wppoppop-adv-add-btn" data-kind="filter" data-val="trim">+ Trim</button>
            </div>
        </div>

        <div style="padding-top:10px;border-top:1px solid #1e293b;">
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:6px;text-transform:uppercase;">VALIDATORS</label>
            <div id="wppoppop-adv-validators-list" class="wppoppop-adv-badge-wrap" style="display:flex;flex-wrap:wrap;gap:6px;min-height:32px;padding:6px;background:#090d16;border:1px solid #1e293b;border-radius:6px;margin-bottom:8px;"></div>
            <div style="display:flex;gap:5px;flex-wrap:wrap;">
                <button type="button" class="wppoppop-adv-add-btn" data-kind="validator" data-val="required">+ Required</button>
                <button type="button" class="wppoppop-adv-add-btn" data-kind="validator" data-val="email">+ Email</button>
                <button type="button" class="wppoppop-adv-add-btn" data-kind="validator" data-val="equal">+ Equal</button>
                <button type="button" class="wppoppop-adv-add-btn" data-kind="validator" data-val="equal_field">+ Equal To Field</button>
                <button type="button" class="wppoppop-adv-add-btn" data-kind="validator" data-val="in_array">+ In Array</button>
                <button type="button" class="wppoppop-adv-add-btn" data-kind="validator" data-val="prevent_duplicates">+ Prevent Duplicates</button>
            </div>
        </div>
    </div>
</div>
