<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div id="wppoppop-inspector-drawer" class="wppoppop-inspector-drawer" style="position:fixed;top:52px;right:0;bottom:0;width:340px;background:#1e293b;border-left:1px solid #334155;z-index:99995;transform:translateX(100%);transition:transform 0.25s cubic-bezier(0.16,1,0.3,1);display:flex;flex-direction:column;box-shadow:-5px 0 25px rgba(0,0,0,0.5);font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;color:#f8fafc;box-sizing:border-box;">
    
    <!-- Inspector Header -->
    <div style="height:48px;background:#0f172a;border-bottom:1px solid #334155;display:flex;align-items:center;justify-content:space-between;padding:0 16px;flex-shrink:0;">
        <span style="font-size:12px;font-weight:700;letter-spacing:0.5px;color:#38bdf8;display:flex;align-items:center;gap:6px;">
            <span class="dashicons dashicons-admin-generic" style="font-size:16px;width:16px;height:16px;"></span> LAYER SETTINGS
        </span>
        <button type="button" id="wppoppop-inspector-close" style="background:transparent;border:none;color:#94a3b8;cursor:pointer;font-size:18px;padding:4px;display:flex;align-items:center;justify-content:center;line-height:1;" title="Close Inspector">&times;</button>
    </div>

    <!-- Inspector Tabs -->
    <div style="display:flex;background:#0f172a;border-bottom:1px solid #334155;padding:0 8px;flex-shrink:0;">
        <button type="button" class="wppoppop-insp-tab active" data-tab="basic" style="flex:1;background:transparent;border:none;border-bottom:2px solid #38bdf8;color:#38bdf8;padding:8px;font-size:11px;font-weight:700;cursor:pointer;">CONTENT</button>
        <button type="button" class="wppoppop-insp-tab" data-tab="style" style="flex:1;background:transparent;border:none;border-bottom:2px solid transparent;color:#94a3b8;padding:8px;font-size:11px;font-weight:700;cursor:pointer;">STYLE</button>
        <button type="button" class="wppoppop-insp-tab" data-tab="logic" style="flex:1;background:transparent;border:none;border-bottom:2px solid transparent;color:#94a3b8;padding:8px;font-size:11px;font-weight:700;cursor:pointer;">LOGIC</button>
    </div>

    <!-- Inspector Body -->
    <div style="flex:1;overflow-y:auto;padding:16px;display:flex;flex-direction:column;gap:16px;">
        
        <!-- =================== TAB 1: BASIC CONTENT =================== -->
        <div id="insp-tab-basic" class="wppoppop-tab-pane">
            <div style="display:flex;flex-direction:column;gap:12px;">
                <div class="wppoppop-prop-group">
                    <label for="prop-name" style="font-size:11px;font-weight:700;color:#94a3b8;display:block;margin-bottom:4px;">LAYER NAME</label>
                    <input type="text" id="prop-name" class="wppoppop-input" style="width:100%;background:#0f172a;border:1px solid #334155;color:#f8fafc;padding:6px 10px;border-radius:4px;font-size:12px;box-sizing:border-box;">
                </div>

                <!-- DYNAMIC ELEMENT-SPECIFIC SETTINGS -->
                <div id="wppoppop-element-specific-settings">
                    
                    <!-- Element #1: Title -->
                    <div id="panel-elem-title" class="wppoppop-elem-panel" style="display:none;">
                        <div class="wppoppop-prop-group">
                            <label for="prop-title-text" style="font-size:11px;font-weight:700;color:#94a3b8;display:block;margin-bottom:4px;">HEADING TEXT</label>
                            <textarea id="prop-title-text" class="wppoppop-input" rows="3" style="width:100%;background:#0f172a;border:1px solid #334155;color:#f8fafc;padding:6px 10px;border-radius:4px;font-size:12px;box-sizing:border-box;resize:vertical;"></textarea>
                        </div>
                    </div>

                    <!-- Element #2: Paragraph -->
                    <div id="panel-elem-paragraph" class="wppoppop-elem-panel" style="display:none;">
                        <div class="wppoppop-prop-group">
                            <label for="prop-paragraph-text" style="font-size:11px;font-weight:700;color:#94a3b8;display:block;margin-bottom:4px;">PARAGRAPH BODY</label>
                            <textarea id="prop-paragraph-text" class="wppoppop-input" rows="4" style="width:100%;background:#0f172a;border:1px solid #334155;color:#f8fafc;padding:6px 10px;border-radius:4px;font-size:12px;box-sizing:border-box;resize:vertical;"></textarea>
                        </div>
                    </div>

                    <!-- Element #3: Text Field -->
                    <div id="panel-elem-textfield" class="wppoppop-elem-panel" style="display:none;">
                        <div class="wppoppop-prop-group">
                            <label for="prop-textfield-placeholder" style="font-size:11px;font-weight:700;color:#94a3b8;display:block;margin-bottom:4px;">PLACEHOLDER TEXT</label>
                            <input type="text" id="prop-textfield-placeholder" class="wppoppop-input" style="width:100%;background:#0f172a;border:1px solid #334155;color:#f8fafc;padding:6px 10px;border-radius:4px;font-size:12px;box-sizing:border-box;">
                        </div>
                    </div>

                    <!-- Element #4: Vector Shape Settings -->
                    <div id="panel-elem-shape" class="wppoppop-elem-panel" style="display:none;">
                        <div style="font-size:11px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin:8px 0 6px 0;border-bottom:1px solid #334155;padding-bottom:4px;">SHAPE GEOMETRY &amp; OUTLINE</div>
                        
                        <div class="wppoppop-prop-group" style="margin-bottom:10px;">
                            <label for="prop-shape-preset" style="font-size:11px;font-weight:700;color:#94a3b8;display:block;margin-bottom:4px;">PRESET SHAPE</label>
                            <select id="prop-shape-preset" class="wppoppop-input" style="width:100%;background:#0f172a;border:1px solid #334155;color:#f8fafc;padding:6px 10px;border-radius:4px;font-size:12px;box-sizing:border-box;">
                                <option value="circle">Circle</option>
                                <option value="square">Square</option>
                                <option value="rounded_rect">Rounded Rectangle</option>
                                <option value="star">Star (5-Point)</option>
                                <option value="triangle">Triangle</option>
                                <option value="diamond">Diamond</option>
                                <option value="heart">Heart</option>
                                <option value="hexagon">Hexagon</option>
                                <option value="octagon">Octagon</option>
                                <option value="shield">Shield</option>
                                <option value="cross">Cross</option>
                            </select>
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px;">
                            <div class="wppoppop-prop-group">
                                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                                    <label for="prop-shape-fill" style="font-size:11px;font-weight:700;color:#94a3b8;margin:0;">FILL COLOR</label>
                                    <button type="button" id="prop-shape-fill-transparent-btn" class="wppoppop-checker-btn" title="Set Transparent Fill" aria-label="Transparent Fill"></button>
                                </div>
                                <input type="color" id="prop-shape-fill" class="wppoppop-input" value="#3b82f6" style="width:100%;height:32px;padding:2px;background:#0f172a;border:1px solid #334155;border-radius:4px;cursor:pointer;box-sizing:border-box;">
                            </div>
                            <div class="wppoppop-prop-group">
                                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                                    <label for="prop-shape-stroke" style="font-size:11px;font-weight:700;color:#94a3b8;margin:0;">STROKE COLOR</label>
                                    <button type="button" id="prop-shape-stroke-transparent-btn" class="wppoppop-checker-btn" title="Set Transparent Stroke" aria-label="Transparent Stroke"></button>
                                </div>
                                <input type="color" id="prop-shape-stroke" class="wppoppop-input" value="#1d4ed8" style="width:100%;height:32px;padding:2px;background:#0f172a;border:1px solid #334155;border-radius:4px;cursor:pointer;box-sizing:border-box;">
                            </div>
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px;">
                            <div class="wppoppop-prop-group">
                                <label for="prop-shape-stroke-width" style="font-size:11px;font-weight:700;color:#94a3b8;display:block;margin-bottom:4px;">STROKE (PX)</label>
                                <input type="number" id="prop-shape-stroke-width" class="wppoppop-input" min="0" max="30" value="2" style="width:100%;background:#0f172a;border:1px solid #334155;color:#f8fafc;padding:6px 10px;border-radius:4px;font-size:12px;box-sizing:border-box;">
                            </div>
                            <div class="wppoppop-prop-group">
                                <label for="prop-shape-rotate" style="font-size:11px;font-weight:700;color:#94a3b8;display:block;margin-bottom:4px;">ROTATION (°)</label>
                                <input type="number" id="prop-shape-rotate" class="wppoppop-input" min="0" max="360" value="0" style="width:100%;background:#0f172a;border:1px solid #334155;color:#f8fafc;padding:6px 10px;border-radius:4px;font-size:12px;box-sizing:border-box;">
                            </div>
                        </div>
                    </div>

                    <!-- General Content Fallback -->
                    <div id="panel-elem-general" class="wppoppop-elem-panel">
                        <div class="wppoppop-prop-group">
                            <label for="prop-content" style="font-size:11px;font-weight:700;color:#94a3b8;display:block;margin-bottom:4px;">ELEMENT CONTENT / LABEL</label>
                            <textarea id="prop-content" class="wppoppop-input" rows="3" style="width:100%;background:#0f172a;border:1px solid #334155;color:#f8fafc;padding:6px 10px;border-radius:4px;font-size:12px;box-sizing:border-box;resize:vertical;"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Position & Dimensions -->
                <div style="font-size:11px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-top:6px;border-bottom:1px solid #334155;padding-bottom:4px;">COORDINATES &amp; BOUNDS</div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                    <div>
                        <label for="prop-left" style="font-size:10px;font-weight:700;color:#94a3b8;">X POS (PX)</label>
                        <input type="number" id="prop-left" class="wppoppop-input" style="width:100%;background:#0f172a;border:1px solid #334155;color:#f8fafc;padding:5px 8px;border-radius:4px;font-size:11px;box-sizing:border-box;">
                    </div>
                    <div>
                        <label for="prop-top" style="font-size:10px;font-weight:700;color:#94a3b8;">Y POS (PX)</label>
                        <input type="number" id="prop-top" class="wppoppop-input" style="width:100%;background:#0f172a;border:1px solid #334155;color:#f8fafc;padding:5px 8px;border-radius:4px;font-size:11px;box-sizing:border-box;">
                    </div>
                    <div>
                        <label for="prop-width" style="font-size:10px;font-weight:700;color:#94a3b8;">WIDTH (PX)</label>
                        <input type="number" id="prop-width" class="wppoppop-input" style="width:100%;background:#0f172a;border:1px solid #334155;color:#f8fafc;padding:5px 8px;border-radius:4px;font-size:11px;box-sizing:border-box;">
                    </div>
                    <div>
                        <label for="prop-height" style="font-size:10px;font-weight:700;color:#94a3b8;">HEIGHT (PX)</label>
                        <input type="number" id="prop-height" class="wppoppop-input" style="width:100%;background:#0f172a;border:1px solid #334155;color:#f8fafc;padding:5px 8px;border-radius:4px;font-size:11px;box-sizing:border-box;">
                    </div>
                </div>
                            <!-- =================== ELEMENT ANIMATION (CONTENT TAB) =================== -->
                <div id="wppoppop-element-anim-wrap" style="margin-top:14px;border-top:1px solid #334155;padding-top:10px;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                        <span style="font-size:11px;font-weight:800;color:#38bdf8;text-transform:uppercase;letter-spacing:0.5px;">ANIMATION</span>
                        <span style="font-size:9px;color:#94a3b8;background:#1e293b;border:1px solid #334155;padding:1px 6px;border-radius:4px;">Animate.css</span>
                    </div>
                    <div>
                        <label for="prop-animation" style="font-size:10px;font-weight:700;color:#94a3b8;display:block;margin-bottom:4px;">APPEARANCE EFFECT</label>
                        <div style="display:flex;gap:8px;align-items:center;">
                            <select id="prop-animation" class="wppoppop-input" style="flex:1;background:#0f172a !important;border:1px solid #334155 !important;color:#f8fafc !important;padding:6px 10px;border-radius:4px;font-size:12px;box-sizing:border-box;">
                                <option value="none">None</option>
                                <option value="fade">Fade In</option>
                                <option value="bounceIn">Bounce In</option>
                                <option value="bounceInLeft">Bounce In Left</option>
                                <option value="bounceInRight">Bounce In Right</option>
                                <option value="bounce">Bounce</option>
                                <option value="tada">Tada</option>
                                <option value="rubberBand">RubberBand</option>
                                <option value="slideDown">Slide Down</option>
                                <option value="slideUp">Slide Up</option>
                                <option value="slideLeft">Slide Left</option>
                                <option value="slideRight">Slide Right</option>
                                <option value="zoomIn">Zoom In</option>
                                <option value="flipIn">Flip In</option>
                                <option value="pulse">Pulse</option>
                                <option value="shake">Shake</option>
                            </select>
                            <button type="button" id="prop-anim-play-btn" style="background:#2563eb;border:none;color:#ffffff;padding:6px 12px;border-radius:4px;font-size:11px;font-weight:700;cursor:pointer;white-space:nowrap;display:flex;align-items:center;gap:4px;" title="Test Element Animation">&#9654; Play</button>
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:8px;">
                        <div>
                            <label for="prop-anim-duration" style="font-size:10px;font-weight:700;color:#94a3b8;display:block;margin-bottom:4px;">DURATION (MS)</label>
                            <input type="number" id="prop-anim-duration" class="wppoppop-input" value="1000" step="50" min="0" style="width:100%;background:#0f172a !important;border:1px solid #334155 !important;color:#f8fafc !important;padding:5px 8px;border-radius:4px;font-size:11px;box-sizing:border-box;">
                        </div>
                        <div>
                            <label for="prop-anim-delay" style="font-size:10px;font-weight:700;color:#94a3b8;display:block;margin-bottom:4px;">START DELAY (MS)</label>
                            <input type="number" id="prop-anim-delay" class="wppoppop-input" value="0" step="50" min="0" style="width:100%;background:#0f172a !important;border:1px solid #334155 !important;color:#f8fafc !important;padding:5px 8px;border-radius:4px;font-size:11px;box-sizing:border-box;">
                        </div>
                    </div>

                    <div style="margin-top:8px;">
                        <label for="prop-anim-disappearance" style="font-size:10px;font-weight:700;color:#94a3b8;display:block;margin-bottom:4px;">DISAPPEARANCE / EXIT</label>
                        <select id="prop-anim-disappearance" class="wppoppop-input" style="width:100%;background:#0f172a !important;border:1px solid #334155 !important;color:#f8fafc !important;padding:6px 10px;border-radius:4px;font-size:12px;box-sizing:border-box;">
                            <option value="none">None</option>
                            <option value="fade">Fade</option>
                            <option value="slideDown">Slide Down</option>
                            <option value="slideUp">Slide Up</option>
                            <option value="slideLeft">Slide Left</option>
                            <option value="slideRight">Slide Right</option>
                            <option value="zoomOut">Zoom Out</option>
                        </select>
                    </div>
                </div>
        </div>
        </div>

                        <!-- =================== TAB 2: STYLE =================== -->
        <div id="insp-tab-style" class="wppoppop-tab-pane" style="display:none;">
            <div style="display:flex;flex-direction:column;gap:12px;">
                
                <!-- Section 1: Typography & Spacing -->
                <div style="font-size:11px;font-weight:800;color:#38bdf8;text-transform:uppercase;border-bottom:1px solid #334155;padding-bottom:4px;">TYPOGRAPHY &amp; SPACING</div>
                
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                    <div>
                        <label for="prop-font-size" style="font-size:10px;font-weight:700;color:#94a3b8;display:block;margin-bottom:4px;">SIZE (PX)</label>
                        <input type="number" id="prop-font-size" class="wppoppop-input" value="14" style="width:100%;background:#0f172a !important;border:1px solid #334155 !important;color:#f8fafc !important;padding:5px 8px;border-radius:4px;font-size:11px;box-sizing:border-box;">
                    </div>
                    <div>
                        <label for="prop-font-weight" style="font-size:10px;font-weight:700;color:#94a3b8;display:block;margin-bottom:4px;">WEIGHT</label>
                        <select id="prop-font-weight" class="wppoppop-input" style="width:100%;background:#0f172a !important;border:1px solid #334155 !important;color:#f8fafc !important;padding:5px 8px;border-radius:4px;font-size:11px;box-sizing:border-box;">
                            <option value="400">Regular (400)</option>
                            <option value="600">Semi-Bold (600)</option>
                            <option value="700">Bold (700)</option>
                            <option value="800">Extra-Bold (800)</option>
                        </select>
                    </div>
                    <div>
                        <label for="prop-line-height" style="font-size:10px;font-weight:700;color:#94a3b8;display:block;margin-bottom:4px;">LINE HEIGHT</label>
                        <input type="number" id="prop-line-height" class="wppoppop-input" step="0.1" value="1.4" style="width:100%;background:#0f172a !important;border:1px solid #334155 !important;color:#f8fafc !important;padding:5px 8px;border-radius:4px;font-size:11px;box-sizing:border-box;">
                    </div>
                    <div>
                        <label for="prop-letter-spacing" style="font-size:10px;font-weight:700;color:#94a3b8;display:block;margin-bottom:4px;">SPACING (PX)</label>
                        <input type="number" id="prop-letter-spacing" class="wppoppop-input" step="0.5" value="0" style="width:100%;background:#0f172a !important;border:1px solid #334155 !important;color:#f8fafc !important;padding:5px 8px;border-radius:4px;font-size:11px;box-sizing:border-box;">
                    </div>
                </div>

                <!-- Text Alignment (Dedicated Full-Width Row) -->
                <div>
                    <label for="prop-text-align" style="font-size:10px;font-weight:700;color:#94a3b8;display:block;margin-bottom:4px;">TEXT ALIGN</label>
                    <select id="prop-text-align" class="wppoppop-input" style="width:100%;background:#0f172a !important;border:1px solid #334155 !important;color:#f8fafc !important;padding:6px 8px;border-radius:4px;font-size:11px;box-sizing:border-box;">
                        <option value="left">Left</option>
                        <option value="center">Center</option>
                        <option value="right">Right</option>
                    </select>
                </div>

                <!-- 4-Way Directional Padding (Dedicated Full-Width Row & Universal Text/Control Sync) -->
                <div id="wppoppop-padding-control-wrap">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                        <label style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin:0;">PADDING (PX)</label>
                        <span style="font-size:9px;color:#64748b;font-weight:600;">TOP · RIGHT · BOTTOM · LEFT</span>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:6px;">
                        <div>
                            <label for="prop-padding-top" style="font-size:9px;color:#94a3b8;font-weight:600;display:block;margin-bottom:2px;text-align:center;">TOP</label>
                            <input type="number" id="prop-padding-top" class="wppoppop-input wppoppop-pad-field" min="0" max="250" value="0" data-dir="top" style="width:100%;background:#0f172a !important;border:1px solid #334155 !important;color:#f8fafc !important;padding:5px 2px;text-align:center;border-radius:4px;font-size:11px;box-sizing:border-box;" placeholder="0">
                        </div>
                        <div>
                            <label for="prop-padding-right" style="font-size:9px;color:#94a3b8;font-weight:600;display:block;margin-bottom:2px;text-align:center;">RIGHT</label>
                            <input type="number" id="prop-padding-right" class="wppoppop-input wppoppop-pad-field" min="0" max="250" value="0" data-dir="right" style="width:100%;background:#0f172a !important;border:1px solid #334155 !important;color:#f8fafc !important;padding:5px 2px;text-align:center;border-radius:4px;font-size:11px;box-sizing:border-box;" placeholder="0">
                        </div>
                        <div>
                            <label for="prop-padding-bottom" style="font-size:9px;color:#94a3b8;font-weight:600;display:block;margin-bottom:2px;text-align:center;">BOTTOM</label>
                            <input type="number" id="prop-padding-bottom" class="wppoppop-input wppoppop-pad-field" min="0" max="250" value="0" data-dir="bottom" style="width:100%;background:#0f172a !important;border:1px solid #334155 !important;color:#f8fafc !important;padding:5px 2px;text-align:center;border-radius:4px;font-size:11px;box-sizing:border-box;" placeholder="0">
                        </div>
                        <div>
                            <label for="prop-padding-left" style="font-size:9px;color:#94a3b8;font-weight:600;display:block;margin-bottom:2px;text-align:center;">LEFT</label>
                            <input type="number" id="prop-padding-left" class="wppoppop-input" min="0" max="250" value="0" data-dir="left" style="width:100%;background:#0f172a !important;border:1px solid #334155 !important;color:#f8fafc !important;padding:5px 2px;text-align:center;border-radius:4px;font-size:11px;box-sizing:border-box;" placeholder="0">
                        </div>
                    </div>
                    <input type="hidden" id="prop-padding" value="0">
                </div>

                <!-- Section 2: Colors (Full-Width with 2-Column Inputs) -->
                <div style="font-size:11px;font-weight:800;color:#38bdf8;text-transform:uppercase;margin-top:6px;border-bottom:1px solid #334155;padding-bottom:4px;">COLORS</div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                    <div class="wppoppop-prop-group">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                            <label for="prop-color" style="font-size:10px;font-weight:700;color:#94a3b8;margin:0;">TEXT COLOR</label>
                            <button type="button" id="prop-text-transparent-btn" class="wppoppop-checker-btn" title="Set Transparent Text" aria-label="Transparent Text" style="width:16px;height:16px;border-radius:2px;border:1px solid #475569;background:repeating-conic-gradient(#cbd5e1 0% 25%, #fff 0% 50%) 50%/8px 8px;cursor:pointer;"></button>
                        </div>
                        <input type="color" id="prop-color" class="wppoppop-input" value="#ffffff" style="width:100%;height:32px;padding:2px;background:#0f172a !important;border:1px solid #334155 !important;border-radius:4px;cursor:pointer;box-sizing:border-box;">
                    </div>
                    <div class="wppoppop-prop-group">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                            <label for="prop-bg-color" style="font-size:10px;font-weight:700;color:#94a3b8;margin:0;">BG COLOR</label>
                            <button type="button" id="prop-bg-transparent-btn" class="wppoppop-checker-btn" title="Set Transparent Background" aria-label="Transparent Background" style="width:16px;height:16px;border-radius:2px;border:1px solid #475569;background:repeating-conic-gradient(#cbd5e1 0% 25%, #fff 0% 50%) 50%/8px 8px;cursor:pointer;"></button>
                        </div>
                        <input type="color" id="prop-bg-color" class="wppoppop-input" value="#2563eb" style="width:100%;height:32px;padding:2px;background:#0f172a !important;border:1px solid #334155 !important;border-radius:4px;cursor:pointer;box-sizing:border-box;">
                    </div>
                </div>

            </div>
        </div>

        <!-- =================== TAB 3: LOGIC =================== -->
        <div id="insp-tab-logic" class="wppoppop-tab-pane" style="display:none;">
            <div style="font-size:11px;font-weight:800;color:#38bdf8;text-transform:uppercase;border-bottom:1px solid #334155;padding-bottom:4px;">STEP NAVIGATION &amp; ACTIONS</div>
            <div class="wppoppop-prop-group" style="margin-top:8px;">
                <label for="prop-goto-canvas" style="font-size:11px;font-weight:700;color:#94a3b8;display:block;margin-bottom:4px;">TARGET CANVAS ON CLICK</label>
                <select id="prop-goto-canvas" class="wppoppop-input" style="width:100%;background:#0f172a;border:1px solid #334155;color:#f8fafc;padding:6px 10px;border-radius:4px;font-size:12px;box-sizing:border-box;">
                    <option value="2">Canvas 2</option>
                    <option value="1">Canvas 1</option>
                    <option value="3">Canvas 3</option>
                </select>
            </div>
        </div>

    </div>
</div>
