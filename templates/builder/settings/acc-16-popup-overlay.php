<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<!-- 16. Popup Overlay -->
            <div class="wppoppop-acc-item">
                <button type="button" class="wppoppop-acc-header"><?php esc_html_e('Popup Overlay', 'wppoppop'); ?></button>
                <div class="wppoppop-acc-body" style="display:none;">

                    <!-- WPPOPPOP_OVERLAY_ENABLE: master switch, default ON -->
                    <div class="wppoppop-ov-enable-row" style="display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:12px;padding-bottom:10px;border-bottom:1px solid #e2e8f0;">
                        <label for="set-ov-enable" style="font-size:11px;font-weight:700;color:#475569;margin:0;text-transform:uppercase;cursor:pointer;"><?php esc_html_e('Enable Overlay', 'wppoppop'); ?></label>
                        <span class="wppoppop-ovf-switch">
                            <input type="checkbox" id="set-ov-enable" value="1" role="switch" aria-label="<?php esc_attr_e('Show the overlay behind the popup', 'wppoppop'); ?>" checked>
                            <span class="wppoppop-ovf-track"></span>
                        </span>
                    </div>

                    <label for="set-ov-opacity" style="display:block;font-size:11px;font-weight:700;color:#475569;text-transform:uppercase;"><?php esc_html_e('Background Overlay Opacity', 'wppoppop'); ?></label>
                    <div style="display:flex;align-items:center;gap:10px;margin:4px 0 12px;">
                        <input type="range" id="set-ov-opacity" min="0" max="100" step="1" value="65" style="flex:1;">
                        <span id="set-ov-opacity-val" style="min-width:42px;text-align:right;font-size:12px;font-weight:600;">65%</span>
                    </div>

                    <label for="set-ov-color" style="display:block;font-size:11px;font-weight:700;color:#475569;text-transform:uppercase;"><?php esc_html_e('Overlay Color', 'wppoppop'); ?></label>
                    <div style="display:flex;align-items:center;gap:12px;margin:4px 0 14px;">
                        <input type="color" id="set-ov-color" value="#0f172a" style="width:44px;height:30px;padding:0;border:1px solid #cbd5e1;border-radius:4px;background:#fff;cursor:pointer;">
                    </div>

                    <div style="border-top:1px solid #e2e8f0;padding-top:12px;">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                            <strong style="font-size:11px;color:#475569;text-transform:uppercase;"><?php esc_html_e('Custom Background', 'wppoppop'); ?></strong>
                            <button type="button" id="set-ov-bg-add" class="button button-secondary" style="font-size:11px;"><?php esc_html_e('+ Add', 'wppoppop'); ?></button>
                        </div>

                        <div id="set-ov-bg-list" style="margin-bottom:8px;"></div>
                        <div id="set-ov-bg-pager" style="display:none;align-items:center;justify-content:space-between;gap:6px;margin:-2px 0 10px;font-size:11px;color:#475569;"></div>

                        <div id="set-ov-bg-editor" style="display:none;background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;padding:10px;">
                            <label for="set-ov-bg-name" style="font-size:11px;font-weight:600;color:#475569;"><?php esc_html_e('Name', 'wppoppop'); ?></label>
                            <input type="text" id="set-ov-bg-name" class="widefat" placeholder="<?php esc_attr_e('e.g. Winter Snow', 'wppoppop'); ?>" style="margin-bottom:8px;">

                            <label for="set-ov-bg-type" style="font-size:11px;font-weight:600;color:#475569;"><?php esc_html_e('Background Type', 'wppoppop'); ?></label>
                            <select id="set-ov-bg-type" class="widefat" style="margin-bottom:8px;">
                                <option value="image"><?php esc_html_e('Picture (JPEG / PNG)', 'wppoppop'); ?></option>
                                <option value="animation"><?php esc_html_e('Animation (HTML / JS)', 'wppoppop'); ?></option>
                            </select>

                            <label for="set-ov-bg-opacity" style="font-size:11px;font-weight:600;color:#475569;"><?php esc_html_e('Background Opacity (picture / animation)', 'wppoppop'); ?></label>
                            <div style="display:flex;align-items:center;gap:10px;margin:2px 0 8px;">
                                <input type="range" id="set-ov-bg-opacity" min="0" max="100" step="1" value="100" style="flex:1;">
                                <span id="set-ov-bg-opacity-val" style="min-width:42px;text-align:right;font-size:12px;font-weight:600;">100%</span>
                            </div>

                            <div id="set-ov-bg-img-wrap">
                                <label for="set-ov-bg-url" style="font-size:11px;font-weight:600;color:#475569;"><?php esc_html_e('Image URL', 'wppoppop'); ?></label>
                                <div style="display:flex;gap:6px;margin-bottom:6px;">
                                    <input type="url" id="set-ov-bg-url" class="widefat" placeholder="https://.../image.jpg">
                                    <button type="button" id="set-ov-bg-media" class="button button-secondary" style="white-space:nowrap;font-size:11px;"><?php esc_html_e('Media', 'wppoppop'); ?></button>
                                </div>
                                <div id="set-ov-bg-preview" style="height:70px;border:1px dashed #cbd5e1;border-radius:4px;background:#fff center/cover no-repeat;margin-bottom:8px;"></div>
                            </div>

                            <div id="set-ov-bg-anim-wrap" style="display:none;">
                                <label for="set-ov-bg-html" style="font-size:11px;font-weight:600;color:#475569;"><?php esc_html_e('HTML / CSS / JS', 'wppoppop'); ?></label>
                                <textarea id="set-ov-bg-html" rows="8" class="widefat" spellcheck="false" placeholder="&lt;canvas id=&quot;c&quot;&gt;&lt;/canvas&gt;&lt;script&gt;...&lt;/script&gt;" style="font-family:Menlo,Consolas,monospace;font-size:11px;margin-bottom:4px;"></textarea>
                                <p style="font-size:11px;color:#64748b;margin:0 0 8px;"><?php esc_html_e('Runs in a sandboxed frame behind the popup. Scripts require the unfiltered_html capability (administrators on single-site installs).', 'wppoppop'); ?></p>
                            </div>

                            <div style="display:flex;gap:6px;">
                                <button type="button" id="set-ov-bg-apply" class="button button-primary" style="font-size:11px;"><?php esc_html_e('Apply', 'wppoppop'); ?></button>
                                <button type="button" id="set-ov-bg-cancel" class="button button-secondary" style="font-size:11px;"><?php esc_html_e('Cancel', 'wppoppop'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
