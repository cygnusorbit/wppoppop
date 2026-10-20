<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<!-- 17. Popup Overlay -->
            <div class="wppoppop-acc-item">
                <button type="button" class="wppoppop-acc-header">17. Popup Overlay</button>
                <div class="wppoppop-acc-body" style="display:none;">

                    <label for="set-ov-opacity" style="display:block;font-size:11px;font-weight:700;color:#475569;text-transform:uppercase;">Background Overlay Opacity</label>
                    <div style="display:flex;align-items:center;gap:10px;margin:4px 0 12px;">
                        <input type="range" id="set-ov-opacity" min="0" max="100" step="1" value="65" style="flex:1;">
                        <span id="set-ov-opacity-val" style="min-width:42px;text-align:right;font-size:12px;font-weight:600;">65%</span>
                    </div>

                    <label for="set-ov-color" style="display:block;font-size:11px;font-weight:700;color:#475569;text-transform:uppercase;">Overlay Color</label>
                    <div style="display:flex;align-items:center;gap:12px;margin:4px 0 14px;">
                        <input type="color" id="set-ov-color" value="#0f172a" style="width:44px;height:30px;padding:0;border:1px solid #cbd5e1;border-radius:4px;background:#fff;cursor:pointer;">
                        <label style="font-size:12px;"><input type="checkbox" id="set-ov-transparent" value="1"> Transparent</label>
                    </div>

                    <div style="border-top:1px solid #e2e8f0;padding-top:12px;">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                            <strong style="font-size:11px;color:#475569;text-transform:uppercase;">Custom Background</strong>
                            <button type="button" id="set-ov-bg-add" class="button button-secondary" style="font-size:11px;">+ Add</button>
                        </div>

                        <div id="set-ov-bg-list" style="margin-bottom:8px;"></div>

                        <div id="set-ov-bg-editor" style="display:none;background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;padding:10px;">
                            <label for="set-ov-bg-name" style="font-size:11px;font-weight:600;color:#475569;">Name</label>
                            <input type="text" id="set-ov-bg-name" class="widefat" placeholder="e.g. Winter Snow" style="margin-bottom:8px;">

                            <label for="set-ov-bg-type" style="font-size:11px;font-weight:600;color:#475569;">Background Type</label>
                            <select id="set-ov-bg-type" class="widefat" style="margin-bottom:8px;">
                                <option value="image">Picture (JPEG / PNG)</option>
                                <option value="animation">Animation (HTML / JS)</option>
                            </select>

                            <div id="set-ov-bg-img-wrap">
                                <label for="set-ov-bg-url" style="font-size:11px;font-weight:600;color:#475569;">Image URL</label>
                                <div style="display:flex;gap:6px;margin-bottom:6px;">
                                    <input type="url" id="set-ov-bg-url" class="widefat" placeholder="https://.../image.jpg">
                                    <button type="button" id="set-ov-bg-media" class="button button-secondary" style="white-space:nowrap;font-size:11px;">Media</button>
                                </div>
                                <div id="set-ov-bg-preview" style="height:70px;border:1px dashed #cbd5e1;border-radius:4px;background:#fff center/cover no-repeat;margin-bottom:8px;"></div>
                            </div>

                            <div id="set-ov-bg-anim-wrap" style="display:none;">
                                <label for="set-ov-bg-html" style="font-size:11px;font-weight:600;color:#475569;">HTML / CSS / JS</label>
                                <textarea id="set-ov-bg-html" rows="8" class="widefat" spellcheck="false" placeholder="&lt;canvas id=&quot;c&quot;&gt;&lt;/canvas&gt;&lt;script&gt;...&lt;/script&gt;" style="font-family:Menlo,Consolas,monospace;font-size:11px;margin-bottom:4px;"></textarea>
                                <p style="font-size:11px;color:#64748b;margin:0 0 8px;">Runs in a sandboxed frame behind the popup. Scripts require the unfiltered_html capability (administrators on single-site installs).</p>
                            </div>

                            <div style="display:flex;gap:6px;">
                                <button type="button" id="set-ov-bg-apply" class="button button-primary" style="font-size:11px;">Apply</button>
                                <button type="button" id="set-ov-bg-cancel" class="button button-secondary" style="font-size:11px;">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
