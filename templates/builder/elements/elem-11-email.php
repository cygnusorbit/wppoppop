<?php
if (!defined('ABSPATH')) exit;
?>
<!-- DECOMPOSED MODULE 11: EMAIL ADDRESS (email) -->
<div class="element-panel wppoppop-panel-specific" id="panel-elem-email" data-elem-family="form" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;">11. Email Address Settings</div>
    <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">EMAIL PLACEHOLDER</label>
    <input type="text" id="prop-email-placeholder" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="placeholder" placeholder="Enter your email address..." style="margin-bottom:10px;font-size:12px;">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;align-items:center;">
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">AUTOCOMPLETE</label>
            <select id="prop-email-autocomplete" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="autocomplete" style="font-size:12px;">
                <option value="email">Email (email)</option>
                <option value="on">On</option>
                <option value="off">Off</option>
            </select>
        </div>
        <div style="padding-top:14px;">
            <label style="display:inline-flex;align-items:center;gap:6px;font-size:11px;color:#cbd5e1;cursor:pointer;">
                <input type="checkbox" id="prop-email-disposable" class="wppoppop-gp-bind" data-prop="blockDisposable" value="1"> Block Disposable
            </label>
        </div>
    </div>
</div>
