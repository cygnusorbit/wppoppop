<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div id="wppoppop-inspector-drawer" style="width:340px;background:#ffffff;border-left:1px solid #e2e8f0;display:none;flex-direction:column;box-shadow:-4px 0 16px rgba(0,0,0,0.06);z-index:9995;">
    <!-- Inspector Header Tabs -->
    <div style="display:flex;align-items:center;background:#0f172a;color:#ffffff;height:42px;">
        <button type="button" id="wppoppop-inspector-close" style="width:42px;height:42px;background:#991b1b;border:none;color:#ffffff;font-size:18px;cursor:pointer;display:flex;align-items:center;justify-content:center;line-height:1;" title="Close Inspector">&times;</button>
        <button type="button" class="wppoppop-insp-tab active" data-tab="basic" style="flex:1;background:transparent;border:none;color:#ffffff;font-weight:700;font-size:12px;cursor:pointer;height:100%;">Basic</button>
        <button type="button" class="wppoppop-insp-tab" data-tab="style" style="flex:1;background:transparent;border:none;color:#94a3b8;font-weight:700;font-size:12px;cursor:pointer;height:100%;">Style</button>
        <button type="button" class="wppoppop-insp-tab" data-tab="logic" style="flex:1;background:transparent;border:none;color:#94a3b8;font-weight:700;font-size:12px;cursor:pointer;height:100%;">Logic</button>
    </div>

    <!-- Inspector Body -->
    <div style="flex:1;overflow-y:auto;padding:16px;">
        <!-- TAB: BASIC -->
        <div class="wppoppop-insp-content active" id="insp-tab-basic">
            <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">LAYER NAME</label>
            <input type="text" id="prop-layer-name" class="widefat" style="margin-bottom:12px;font-size:12px;">

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:12px;">
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">TOP (PX)</label>
                    <input type="number" id="prop-pos-top" class="widefat" style="font-size:12px;">
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">LEFT (PX)</label>
                    <input type="number" id="prop-pos-left" class="widefat" style="font-size:12px;">
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:12px;">
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">WIDTH (PX)</label>
                    <input type="number" id="prop-size-width" class="widefat" style="font-size:12px;">
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">HEIGHT (PX)</label>
                    <input type="number" id="prop-size-height" class="widefat" style="font-size:12px;">
                </div>
            </div>

            <div id="prop-content-wrap" style="margin-bottom:12px;">
                <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">CONTENT / LABEL</label>
                <textarea id="prop-content" rows="3" class="widefat" style="font-size:12px;"></textarea>
            </div>
        </div>

        <!-- TAB: STYLE -->
        <div class="wppoppop-insp-content" id="insp-tab-style" style="display:none;">
            <div style="margin-bottom:12px;">
                <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">TYPOGRAPHY & FONT</label>
                <select id="prop-font-family" class="widefat" style="font-size:12px;margin-bottom:6px;">
                    <option value="inherit">Theme Default Font</option>
                    <option value="Inter">Inter</option>
                    <option value="Roboto">Roboto</option>
                    <option value="Poppins">Poppins</option>
                    <option value="Montserrat">Montserrat</option>
                    <option value="Playfair Display">Playfair Display</option>
                </select>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                    <input type="number" id="prop-font-size" placeholder="Size (px)" class="widefat" style="font-size:12px;">
                    <input type="color" id="prop-color" class="widefat" style="height:32px;padding:2px;">
                </div>
            </div>

            <div style="margin-bottom:12px;">
                <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">BACKGROUND & BORDER</label>
                <input type="color" id="prop-bg-color" class="widefat" style="height:32px;padding:2px;margin-bottom:6px;">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                    <input type="number" id="prop-border-radius" placeholder="Radius (px)" class="widefat" style="font-size:12px;">
                    <input type="number" id="prop-opacity" step="0.1" min="0.1" max="1" placeholder="Opacity" class="widefat" style="font-size:12px;">
                </div>
            </div>

            <div style="margin-bottom:12px;">
                <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">ENTRANCE ANIMATION</label>
                <select id="prop-anim-effect" class="widefat" style="font-size:12px;">
                    <option value="none">None</option>
                    <option value="fade">Fade In</option>
                    <option value="slideDown">Slide Down</option>
                    <option value="bounceIn">Bounce In</option>
                    <option value="zoomIn">Zoom In</option>
                </select>
            </div>
        </div>

        <!-- TAB: LOGIC -->
        <div class="wppoppop-insp-content" id="insp-tab-logic" style="display:none;">
            <div style="margin-bottom:12px;">
                <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">ONCLICK URL ACTION</label>
                <input type="url" id="prop-action-url" placeholder="https://..." class="widefat" style="font-size:12px;margin-bottom:6px;">
                <label style="font-size:12px;color:#334155;cursor:pointer;">
                    <input type="checkbox" id="prop-action-blank" value="1"> Open in new tab
                </label>
            </div>

            <div style="margin-bottom:12px;">
                <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">CLOSE ACTION</label>
                <select id="prop-action-close" class="widefat" style="font-size:12px;">
                    <option value="none">None</option>
                    <option value="close">Just close popup</option>
                    <option value="close_period">Close for session/period</option>
                    <option value="close_forever">Close forever</option>
                </select>
            </div>

            <div style="margin-bottom:12px;">
                <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">CUSTOM JAVASCRIPT CALLBACK</label>
                <textarea id="prop-action-js" rows="3" placeholder="console.log('Clicked');" class="widefat code" style="font-size:11px;"></textarea>
            </div>
        </div>
    </div>
</div>
