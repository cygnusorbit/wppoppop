<?php
if (!defined('ABSPATH')) exit;
?>
<!-- DECOMPOSED MODULE 04: LABEL / HEADLINE / TEXT (label / title / paragraph) -->
<div class="element-panel wppoppop-panel-specific" id="panel-elem-label" data-elem-family="media" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;">4. Label / Headline Content</div>
    <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">LABEL CONTENT / INLINE HTML</label>
    <textarea id="prop-label-content" rows="3" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="content" placeholder="Enter headline or body label..." style="margin-bottom:10px;font-size:12px;"></textarea>
    <input type="hidden" id="prop-title-text" class="wppoppop-gp-bind" data-prop="content">
    <input type="hidden" id="prop-paragraph-text" class="wppoppop-gp-bind" data-prop="content">
    <input type="hidden" id="prop-text-content" class="wppoppop-gp-bind" data-prop="content">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">SEMANTIC TAG</label>
            <select id="prop-label-tag" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="tag" style="font-size:12px;">
                <option value="div">DIV Container</option>
                <option value="p">Paragraph (P)</option>
                <option value="span">Inline SPAN</option>
                <option value="h1">Heading H1</option>
                <option value="h2">Heading H2</option>
                <option value="h3">Heading H3</option>
                <option value="h4">Heading H4</option>
                <option value="h5">Heading H5</option>
                <option value="h6">Heading H6</option>
            </select>
            <input type="hidden" id="prop-title-tag" class="wppoppop-gp-bind" data-prop="tag">
            <input type="hidden" id="prop-paragraph-tag" class="wppoppop-gp-bind" data-prop="tag">
            <input type="hidden" id="prop-text-tag" class="wppoppop-gp-bind" data-prop="tag">
        </div>
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;">TEXT TRANSFORM</label>
            <select id="prop-label-transform" class="widefat wppoppop-input wppoppop-gp-bind" data-prop="textTransform" style="font-size:12px;">
                <option value="none">None (As Typed)</option>
                <option value="uppercase">UPPERCASE</option>
                <option value="lowercase">lowercase</option>
                <option value="capitalize">Capitalize Words</option>
            </select>
            <input type="hidden" id="prop-title-transform" class="wppoppop-gp-bind" data-prop="textTransform">
        </div>
    </div>
</div>
