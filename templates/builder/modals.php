<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<!-- Live Sandbox Preview Modal -->
<div class="wppoppop-modal-backdrop" id="wppoppop-builder-preview-modal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,0.8);backdrop-filter:blur(4px);z-index:999999;align-items:center;justify-content:center;">
    <div style="background:#ffffff;border-radius:8px;width:720px;max-width:95vw;max-height:90vh;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 25px 50px -12px rgba(0,0,0,0.5);">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 18px;background:#0f172a;color:#ffffff;">
            <h3 style="margin:0;font-size:14px;font-weight:700;">Interactive Sandbox Preview</h3>
            <button type="button" id="wppoppop-builder-preview-close" style="background:transparent;border:none;color:#94a3b8;font-size:22px;cursor:pointer;line-height:1;">&times;</button>
        </div>
        <div id="wppoppop-preview-sandbox-root" style="flex:1;overflow:auto;padding:30px;display:flex;align-items:center;justify-content:center;background:#f1f5f9;"></div>
    </div>
</div>

<!-- How to Embed & Display Popup -->
<div class="wppoppop-modal-backdrop" id="wppoppop-modal-embed" style="display:none;">
    <div class="wppoppop-modal-dialog wppoppop-modal-embed-dialog" role="dialog" aria-modal="true" aria-labelledby="wppoppop-embed-title">
        <div class="wppoppop-modal-header">
            <h3 id="wppoppop-embed-title"><span class="dashicons dashicons-code-standards"></span> <?php esc_html_e('How to Embed & Display Popup', 'wppoppop'); ?></h3>
            <button type="button" class="wppoppop-modal-close" data-modal="wppoppop-modal-embed" aria-label="<?php esc_attr_e('Close', 'wppoppop'); ?>">&times;</button>
        </div>
        <div class="wppoppop-modal-body">
            <div class="wppoppop-embed-tabs">
                <button type="button" class="wppoppop-embed-tab active" data-tab="shortcode"><?php esc_html_e('Shortcode', 'wppoppop'); ?></button>
                <button type="button" class="wppoppop-embed-tab" data-tab="onclick"><?php esc_html_e('On-Click Link', 'wppoppop'); ?></button>
                <button type="button" class="wppoppop-embed-tab" data-tab="js-api"><?php esc_html_e('JavaScript API', 'wppoppop'); ?></button>
            </div>

            <div class="wppoppop-embed-notice" id="wppoppop-embed-unsaved" style="display:none;">
                <?php esc_html_e('This popup has not been saved yet. Click "Save Popup" first so the snippets below contain its real ID.', 'wppoppop'); ?>
            </div>

            <div class="wppoppop-embed-notice" id="wppoppop-embed-draft" style="display:none;">
                <?php esc_html_e('This popup is a Draft, so the snippets below output nothing on your site yet. Set its status to Published from the Popups list (Bulk Actions > Set Status: Published).', 'wppoppop'); ?>
            </div>

            <div class="wppoppop-embed-tab-content active" id="embed-tab-shortcode">
                <p class="wppoppop-embed-desc"><?php esc_html_e('Paste this shortcode anywhere inside your WordPress page, post, or template:', 'wppoppop'); ?></p>
                <div class="wppoppop-code-box">
                    <input type="text" id="wppoppop-embed-shortcode-val" readonly value="">
                    <button type="button" class="wppoppop-btn-copy" data-target="wppoppop-embed-shortcode-val">
                        <span class="dashicons dashicons-clipboard"></span> <?php esc_html_e('Copy', 'wppoppop'); ?>
                    </button>
                </div>
                <div class="wppoppop-embed-guide">
                    <h4><?php esc_html_e('How to use', 'wppoppop'); ?></h4>
                    <ol>
                        <li><?php esc_html_e('Click Save Popup and make sure its status is Published.', 'wppoppop'); ?></li>
                        <li><?php esc_html_e('Edit the page or post and add a Shortcode block (or use the Classic editor).', 'wppoppop'); ?></li>
                        <li><?php esc_html_e('Paste the shortcode, update the page, then view it on your site.', 'wppoppop'); ?></li>
                    </ol>
                    <h4><?php esc_html_e('Recommended', 'wppoppop'); ?></h4>
                    <ul>
                        <li><?php esc_html_e('Use the shortcode to embed the popup inline inside content, such as landing pages and sidebars.', 'wppoppop'); ?></li>
                        <li><?php esc_html_e('Use only one inline copy of the same popup per page.', 'wppoppop'); ?></li>
                        <li><?php esc_html_e('Want a button that opens it as a popup instead? Use this variant:', 'wppoppop'); ?>
                            <code class="wppoppop-embed-ex">[wppoppop uid="<span class="wppoppop-embed-uid"></span>" mode="button" text="Get My Offer"]</code>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="wppoppop-embed-tab-content" id="embed-tab-onclick" style="display:none;">
                <p class="wppoppop-embed-desc"><?php esc_html_e('Attach this HTML snippet to any anchor link, button, or menu item to open the popup upon click:', 'wppoppop'); ?></p>
                <div class="wppoppop-code-box">
                    <input type="text" id="wppoppop-embed-onclick-val" readonly value="">
                    <button type="button" class="wppoppop-btn-copy" data-target="wppoppop-embed-onclick-val">
                        <span class="dashicons dashicons-clipboard"></span> <?php esc_html_e('Copy', 'wppoppop'); ?>
                    </button>
                </div>
                <div class="wppoppop-embed-guide">
                    <h4><?php esc_html_e('How to use', 'wppoppop'); ?></h4>
                    <ol>
                        <li><?php esc_html_e('Add a Custom HTML block (or an HTML widget in your page builder) where the link should appear.', 'wppoppop'); ?></li>
                        <li><?php esc_html_e('Paste the snippet and change the link text if you like.', 'wppoppop'); ?></li>
                        <li><?php esc_html_e('Menu item: Appearance > Menus > Custom Links, set the URL to:', 'wppoppop'); ?>
                            <code class="wppoppop-embed-ex">#wppoppop-<span class="wppoppop-embed-uid"></span></code>
                        </li>
                    </ol>
                    <h4><?php esc_html_e('Recommended', 'wppoppop'); ?></h4>
                    <ul>
                        <li><?php esc_html_e('Works on any element. Add the data attribute to an existing button:', 'wppoppop'); ?>
                            <code class="wppoppop-embed-ex">&lt;button type="button" data-wppoppop-id="<span class="wppoppop-embed-uid"></span>"&gt;Open&lt;/button&gt;</code>
                        </li>
                        <li><?php esc_html_e('A click always reopens the popup, even if the visitor closed it earlier.', 'wppoppop'); ?></li>
                        <li><?php esc_html_e('Keep the class "wppoppop-trigger" and add your own classes for styling.', 'wppoppop'); ?></li>
                    </ul>
                </div>
            </div>

            <div class="wppoppop-embed-tab-content" id="embed-tab-js-api" style="display:none;">
                <p class="wppoppop-embed-desc"><?php esc_html_e('Trigger this popup programmatically via custom JavaScript or third-party webhooks:', 'wppoppop'); ?></p>
                <div class="wppoppop-code-box">
                    <input type="text" id="wppoppop-embed-js-val" readonly value="">
                    <button type="button" class="wppoppop-btn-copy" data-target="wppoppop-embed-js-val">
                        <span class="dashicons dashicons-clipboard"></span> <?php esc_html_e('Copy', 'wppoppop'); ?>
                    </button>
                </div>
                <div class="wppoppop-embed-guide">
                    <h4><?php esc_html_e('How to use', 'wppoppop'); ?></h4>
                    <ol>
                        <li><?php esc_html_e('Place the code in your theme script, a footer-scripts plugin, or an event callback.', 'wppoppop'); ?></li>
                        <li><?php esc_html_e('Run it after the page has loaded, for example inside a load event or a button click.', 'wppoppop'); ?></li>
                    </ol>
                    <h4><?php esc_html_e('Available methods', 'wppoppop'); ?></h4>
                    <ul>
                        <li><code>WpPopPop.show(uid)</code> &ndash; <?php esc_html_e('open the popup', 'wppoppop'); ?></li>
                        <li><code>WpPopPop.hide(uid)</code> &ndash; <?php esc_html_e('close the popup', 'wppoppop'); ?></li>
                        <li><code>WpPopPop.toggle(uid)</code> / <code>WpPopPop.isOpen(uid)</code></li>
                    </ul>
                    <h4><?php esc_html_e('Recommended', 'wppoppop'); ?></h4>
                    <ul>
                        <li><?php esc_html_e('Keep the "window.WpPopPop &&" guard so your script never errors if the plugin is inactive.', 'wppoppop'); ?></li>
                        <li><?php esc_html_e('Example, open after 5 seconds:', 'wppoppop'); ?>
                            <code class="wppoppop-embed-ex">setTimeout(function(){ window.WpPopPop &amp;&amp; window.WpPopPop.show('<span class="wppoppop-embed-uid"></span>'); }, 5000);</code>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Save As Template -->
<style>
#wppoppop-modal-save-template{position:fixed;inset:0;background:rgba(15,23,42,.7);z-index:999999;align-items:center;justify-content:center}
#wppoppop-modal-save-template .wpt-dlg{background:#fff;color:#1e293b;border-radius:8px;width:520px;max-width:95vw;max-height:92vh;overflow:auto;box-shadow:0 25px 50px -12px rgba(0,0,0,.5)}
#wppoppop-modal-save-template .wpt-hd{display:flex;align-items:center;justify-content:space-between;padding:12px 18px;background:#0f172a;color:#fff}
#wppoppop-modal-save-template .wpt-hd h3{margin:0;font-size:14px;color:#fff}
#wppoppop-modal-save-template .wpt-x{background:transparent;border:0;color:#94a3b8;font-size:22px;cursor:pointer;line-height:1}
#wppoppop-modal-save-template .wpt-bd{padding:16px 18px}
#wppoppop-modal-save-template label.wpt-l{display:block;font-weight:600;font-size:12px;margin:12px 0 4px}
#wppoppop-modal-save-template input[type=text],#wppoppop-modal-save-template textarea{width:100%;box-sizing:border-box}
#wppoppop-modal-save-template .wpt-img{display:flex;gap:12px;align-items:center}
#wppoppop-modal-save-template .wpt-prev{width:120px;height:80px;border:1px dashed #94a3b8;border-radius:6px;background:#f1f5f9 center/cover no-repeat;flex:none}
#wppoppop-modal-save-template .wpt-box{background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;padding:8px 10px;font-size:12px;line-height:1.7}
#wppoppop-modal-save-template .wpt-ft{display:flex;justify-content:flex-end;gap:8px;padding:12px 18px;border-top:1px solid #e2e8f0}
#wppoppop-modal-save-template .wpt-msg{margin-top:10px;font-size:12px;display:none}
</style>
<div class="wppoppop-modal-backdrop" id="wppoppop-modal-save-template" style="display:none;">
    <div class="wpt-dlg" role="dialog" aria-modal="true" aria-labelledby="wppoppop-tpl-title">
        <div class="wpt-hd">
            <h3 id="wppoppop-tpl-title"><?php esc_html_e('Save As Template', 'wppoppop'); ?></h3>
            <button type="button" class="wpt-x" id="wppoppop-tpl-close" aria-label="<?php esc_attr_e('Close', 'wppoppop'); ?>">&times;</button>
        </div>
        <div class="wpt-bd">
            <label class="wpt-l"><?php esc_html_e('Popup Image', 'wppoppop'); ?></label>
            <div class="wpt-img">
                <div class="wpt-prev" id="wppoppop-tpl-prev"></div>
                <div>
                    <button type="button" class="button" id="wppoppop-tpl-upload"><?php esc_html_e('Upload Image', 'wppoppop'); ?></button>
                    <button type="button" class="button" id="wppoppop-tpl-remove-img"><?php esc_html_e('Remove', 'wppoppop'); ?></button>
                    <input type="hidden" id="wppoppop-tpl-image" value="">
                </div>
            </div>
            <label class="wpt-l" for="wppoppop-tpl-name"><?php esc_html_e('Popup Name', 'wppoppop'); ?></label>
            <input type="text" id="wppoppop-tpl-name" maxlength="120">
            <label class="wpt-l" for="wppoppop-tpl-desc"><?php esc_html_e('Description', 'wppoppop'); ?></label>
            <textarea id="wppoppop-tpl-desc" rows="3" maxlength="400"></textarea>
            <label class="wpt-l"><?php esc_html_e('Used Elements', 'wppoppop'); ?> <span id="wppoppop-tpl-total"></span></label>
            <div class="wpt-box" id="wppoppop-tpl-elements"></div>
            <label class="wpt-l"><?php esc_html_e('Canvas Size', 'wppoppop'); ?></label>
            <div class="wpt-box" id="wppoppop-tpl-size"></div>
            <div class="wpt-msg" id="wppoppop-tpl-msg"></div>
        </div>
        <div class="wpt-ft">
            <button type="button" class="button" id="wppoppop-tpl-cancel"><?php esc_html_e('Cancel', 'wppoppop'); ?></button>
            <button type="button" class="button button-primary" id="wppoppop-tpl-submit" style="background:#00a32a;border-color:#008a20;"><?php esc_html_e('Save Template', 'wppoppop'); ?></button>
        </div>
    </div>
</div>
