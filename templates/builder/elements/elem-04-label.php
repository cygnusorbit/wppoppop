<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!-- DECOMPOSED MODULE 04: LABEL / HEADLINE / PARAGRAPH (label / title / paragraph) -->
<div class="wppoppop-elem-panel element-panel wppoppop-panel-specific" id="panel-elem-label" data-elem-family="media" style="display:none;background:#0f172a;border:1px solid #1e293b;border-radius:8px;padding:12px;margin-bottom:14px;">
    <div style="font-size:10px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-bottom:10px;letter-spacing:0.5px;"><?php esc_html_e( '4. Label / Headline Content', 'wppoppop' ); ?></div>
    <div style="margin-bottom:10px;">
        <label for="prop-label-text" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'TEXT / INLINE HTML CONTENT', 'wppoppop' ); ?></label>
        <textarea class="widefat wppoppop-input wppoppop-gp-bind" id="prop-label-text" data-prop="content" rows="3" placeholder="<?php esc_attr_e( 'Enter headline or paragraph text...', 'wppoppop' ); ?>" style="font-size:12px;"></textarea>
    </div>
    <div class="wppoppop-gp-2col-grid">
        <div class="wppoppop-gp-col">
            <label for="prop-label-tag" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'SEMANTIC HTML TAG', 'wppoppop' ); ?></label>
            <select class="widefat wppoppop-input wppoppop-gp-bind" id="prop-label-tag" data-prop="tag" style="font-size:12px;">
                <option value="h1">H1 Heading</option>
                <option value="h2">H2 Heading</option>
                <option value="h3">H3 Heading</option>
                <option value="h4">H4 Heading</option>
                <option value="h5">H5 Heading</option>
                <option value="h6">H6 Heading</option>
                <option value="p">Paragraph (P)</option>
                <option value="div">DIV Container</option>
                <option value="span">SPAN Inline</option>
            </select>
        </div>
        <div class="wppoppop-gp-col">
            <label for="prop-label-transform" style="display:block;font-size:10px;font-weight:700;color:#94a3b8;margin-bottom:4px;text-transform:uppercase;"><?php esc_html_e( 'TEXT TRANSFORM', 'wppoppop' ); ?></label>
            <select class="widefat wppoppop-input wppoppop-gp-bind" id="prop-label-transform" data-prop="textTransform" style="font-size:12px;">
                <option value="none"><?php esc_html_e( 'None (Normal)', 'wppoppop' ); ?></option>
                <option value="uppercase">UPPERCASE</option>
                <option value="lowercase">lowercase</option>
                <option value="capitalize">Capitalize Each Word</option>
            </select>
        </div>
    </div>
</div>
