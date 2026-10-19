<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-modal-backdrop" id="wppoppop-dash-embed-modal" role="dialog" aria-modal="true" aria-labelledby="wppoppop-dash-embed-title" style="display:none;position:fixed;top:0;right:0;bottom:0;left:0;background:rgba(15,23,42,0.7);-webkit-backdrop-filter:blur(3px);backdrop-filter:blur(3px);z-index:999999;align-items:center;justify-content:center;padding:16px;box-sizing:border-box;">
    <div style="background:#ffffff;border-radius:8px;width:560px;max-width:92vw;box-shadow:0 20px 25px -5px rgba(0,0,0,0.25);overflow:hidden;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 20px;background:#0f172a;color:#ffffff;">
            <h3 id="wppoppop-dash-embed-title" style="margin:0;font-size:14px;font-weight:700;color:#ffffff;"><?php esc_html_e('Embed Snippets', 'wppoppop'); ?> <span id="wppoppop-dash-embed-uid" style="font-family:monospace;font-weight:500;color:#38bdf8;font-size:12px;"></span></h3>
            <button type="button" id="wppoppop-dash-embed-close" aria-label="<?php esc_attr_e('Close', 'wppoppop'); ?>" style="background:transparent;border:none;color:#94a3b8;font-size:22px;cursor:pointer;line-height:1;">&times;</button>
        </div>
        <div style="padding:20px;">
            <div id="wppoppop-dash-embed-draft" style="display:none;margin:0 0 14px 0;padding:8px 12px;border-left:4px solid #f59e0b;background:#fffbeb;color:#92400e;font-size:12px;">
                <?php esc_html_e('This campaign is a Draft. Publish it first, otherwise the snippets below will output nothing on your site.', 'wppoppop'); ?>
            </div>

            <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;"><?php esc_html_e('Inline Shortcode', 'wppoppop'); ?></label>
            <div style="display:flex;gap:6px;">
                <input type="text" class="widefat" id="wppoppop-dash-sc-field" readonly style="font-family:monospace;flex:1;">
                <button type="button" class="button wppoppop-btn-copy-input" data-target="wppoppop-dash-sc-field"><?php esc_html_e('Copy', 'wppoppop'); ?></button>
            </div>

            <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin:14px 0 4px 0;"><?php esc_html_e('Button Shortcode (opens as popup)', 'wppoppop'); ?></label>
            <div style="display:flex;gap:6px;">
                <input type="text" class="widefat" id="wppoppop-dash-btnsc-field" readonly style="font-family:monospace;flex:1;">
                <button type="button" class="button wppoppop-btn-copy-input" data-target="wppoppop-dash-btnsc-field"><?php esc_html_e('Copy', 'wppoppop'); ?></button>
            </div>

            <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin:14px 0 4px 0;"><?php esc_html_e('On-Click Link (HTML)', 'wppoppop'); ?></label>
            <div style="display:flex;gap:6px;">
                <input type="text" class="widefat" id="wppoppop-dash-btn-field" readonly style="font-family:monospace;flex:1;">
                <button type="button" class="button wppoppop-btn-copy-input" data-target="wppoppop-dash-btn-field"><?php esc_html_e('Copy', 'wppoppop'); ?></button>
            </div>

            <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin:14px 0 4px 0;"><?php esc_html_e('JavaScript API', 'wppoppop'); ?></label>
            <div style="display:flex;gap:6px;">
                <input type="text" class="widefat" id="wppoppop-dash-js-field" readonly style="font-family:monospace;flex:1;">
                <button type="button" class="button wppoppop-btn-copy-input" data-target="wppoppop-dash-js-field"><?php esc_html_e('Copy', 'wppoppop'); ?></button>
            </div>
        </div>
    </div>
</div>
