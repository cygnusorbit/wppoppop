<?php
if (!defined('ABSPATH')) exit;
?>
<!-- DECOMPOSED MODULE 24: PASSWORD INPUT (password) -->
<div class="element-panel wppoppop-panel-specific" id="panel-elem-password" data-elem-family="form" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;">24. Password Settings (Form Family — 5 Tabs)</div>

    <label for="prop-password-placeholder" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">PASSWORD PLACEHOLDER</label>
    <input type="text" id="prop-password-placeholder" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="placeholder" value="Enter password..." style="margin-bottom:10px;font-size:12px;">

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px;">
        <div>
            <label for="prop-password-fieldname" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">FIELD TOKEN</label>
            <input type="text" id="prop-password-fieldname" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="fieldName" value="password" style="font-size:12px;">
        </div>
        <div>
            <label for="prop-password-minlen" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">MIN LENGTH</label>
            <input type="number" id="prop-password-minlen" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="minLength" value="8" min="1" max="64" style="font-size:12px;">
        </div>
    </div>

    <div style="display:flex;gap:14px;">
        <label style="display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:600;color:#cbd5e1;cursor:pointer;">
            <input type="checkbox" id="prop-password-toggle" class="wppoppop-gp-bind" data-prop="showEyeToggle" value="1" checked> Show Reveal Eye Icon
        </label>
        <label style="display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:600;color:#cbd5e1;cursor:pointer;">
            <input type="checkbox" id="prop-password-required" class="wppoppop-gp-bind" data-prop="required" value="1"> Required
        </label>
    </div>
</div>
