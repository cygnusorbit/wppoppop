<?php
if (!defined('ABSPATH')) exit;
?>
<!-- DECOMPOSED MODULE 13: PHONE / TEL INPUT (phone / tel) -->
<div class="element-panel wppoppop-panel-specific" id="panel-elem-phone" data-elem-family="form" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;">13. Phone / Tel Settings (Form Family — 5 Tabs)</div>

    <label for="prop-phone-placeholder" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">PHONE PLACEHOLDER</label>
    <input type="text" id="prop-phone-placeholder" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="placeholder" value="+1 (555) 000-0000" style="margin-bottom:10px;font-size:12px;">

    <label for="prop-phone-fieldname" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">FIELD NAME / TOKEN</label>
    <input type="text" id="prop-phone-fieldname" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="fieldName" value="phone" placeholder="phone" style="margin-bottom:10px;font-size:12px;">

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px;">
        <div>
            <label for="prop-phone-prefix" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">DIAL CODE PREFIX</label>
            <input type="text" id="prop-phone-prefix" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="phonePrefix" value="+1" placeholder="+1" style="font-size:12px;">
        </div>
        <div>
            <label for="prop-phone-mask" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">FORMAT MASK</label>
            <input type="text" id="prop-phone-mask" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="inputMask" value="(999) 999-9999" style="font-size:12px;">
        </div>
    </div>

    <label style="display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:600;color:#cbd5e1;cursor:pointer;">
        <input type="checkbox" id="prop-phone-required" class="wppoppop-gp-bind" data-prop="required" value="1"> Required Field
    </label>
</div>
