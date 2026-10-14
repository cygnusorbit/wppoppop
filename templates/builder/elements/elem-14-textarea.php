<?php
if (!defined('ABSPATH')) exit;
?>
<!-- DECOMPOSED MODULE 14: TEXTAREA MULTILINE INPUT (textarea) -->
<div class="element-panel wppoppop-panel-specific" id="panel-elem-textarea" data-elem-family="form" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;">14. Textarea Settings (Form Family — 5 Tabs)</div>

    <label for="prop-textarea-placeholder" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">PLACEHOLDER TEXT</label>
    <input type="text" id="prop-textarea-placeholder" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="placeholder" value="Write your message..." style="margin-bottom:10px;font-size:12px;">

    <label for="prop-textarea-fieldname" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">FIELD NAME / TOKEN</label>
    <input type="text" id="prop-textarea-fieldname" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="fieldName" value="message" placeholder="message" style="margin-bottom:10px;font-size:12px;">

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px;">
        <div>
            <label for="prop-textarea-rows" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">VISIBLE ROWS</label>
            <input type="number" id="prop-textarea-rows" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="rows" value="4" min="2" max="15" style="font-size:12px;">
        </div>
        <div>
            <label for="prop-textarea-maxlen" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">MAX CHARACTERS</label>
            <input type="number" id="prop-textarea-maxlen" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="maxLength" value="500" min="10" max="5000" style="font-size:12px;">
        </div>
    </div>

    <label style="display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:600;color:#cbd5e1;cursor:pointer;">
        <input type="checkbox" id="prop-textarea-required" class="wppoppop-gp-bind" data-prop="required" value="1"> Required Field
    </label>
</div>
