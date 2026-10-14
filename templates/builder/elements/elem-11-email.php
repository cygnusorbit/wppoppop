<?php
if (!defined('ABSPATH')) exit;
?>
<!-- DECOMPOSED MODULE 11: EMAIL ADDRESS INPUT (email) -->
<div class="element-panel wppoppop-panel-specific" id="panel-elem-email" data-elem-family="form" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;">11. Email Address Settings (Form Family — 5 Tabs)</div>

    <label for="prop-email-placeholder" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">PLACEHOLDER TEXT</label>
    <input type="text" id="prop-email-placeholder" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="placeholder" placeholder="e.g. name@example.com" style="margin-bottom:10px;font-size:12px;">

    <label for="prop-email-fieldname" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">FIELD NAME / TOKEN</label>
    <input type="text" id="prop-email-fieldname" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="fieldName" value="email" placeholder="email" style="margin-bottom:10px;font-size:12px;">

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px;">
        <div>
            <label for="prop-email-autocomplete" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">AUTOCOMPLETE</label>
            <select id="prop-email-autocomplete" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="autocomplete" style="font-size:12px;">
                <option value="email">Email (email)</option>
                <option value="on">Enabled (on)</option>
                <option value="off">Disabled (off)</option>
                <option value="username">Username</option>
            </select>
        </div>
        <div>
            <label for="prop-email-val-mode" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">DOMAIN FILTER</label>
            <select id="prop-email-val-mode" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="emailValidationMode" style="font-size:12px;">
                <option value="standard">Standard RFC Syntax</option>
                <option value="strict">Block Disposable Domains</option>
            </select>
        </div>
    </div>

    <label style="display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:600;color:#cbd5e1;cursor:pointer;">
        <input type="checkbox" id="prop-email-required" class="wppoppop-gp-bind" data-prop="required" value="1" checked> Required Field
    </label>
</div>
