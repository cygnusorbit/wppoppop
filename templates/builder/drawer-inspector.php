<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div id="wppoppop-inspector-drawer" class="wppoppop-inspector-drawer" style="position:fixed;top:52px;right:0;bottom:0;width:360px;background:#0f172a;color:#f8fafc;z-index:99995;box-shadow:-5px 0 25px rgba(0,0,0,0.5);display:flex;flex-direction:column;transform:translateX(100%);transition:transform 0.25s cubic-bezier(0.16,1,0.3,1);box-sizing:border-box;">
    
    <!-- Drawer Header -->
    <div class="wppoppop-inspector-header" style="padding:12px 16px;background:#1e293b;border-bottom:1px solid #334155;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
        <div style="display:flex;align-items:center;gap:8px;">
            <span id="prop-layer-type-badge" style="background:#2563eb;color:#ffffff;font-size:10px;font-weight:700;padding:2px 6px;border-radius:4px;text-transform:uppercase;">ELEMENT</span>
            <input type="text" id="prop-layer-name" value="Layer Name" style="background:transparent;border:1px solid transparent;color:#f8fafc;font-size:13px;font-weight:700;width:150px;padding:2px 4px;border-radius:4px;" title="Rename layer">
        </div>
        <div style="display:flex;align-items:center;gap:6px;">
            <button type="button" id="wppoppop-inspector-dup-btn" class="button button-small" title="Duplicate Layer" style="background:#334155;color:#e2e8f0;border:none;padding:2px 6px;cursor:pointer;"><span class="dashicons dashicons-admin-page" style="font-size:14px;width:14px;height:14px;"></span></button>
            <button type="button" id="wppoppop-inspector-del-btn" class="button button-small" title="Delete Layer" style="background:#ef4444;color:#ffffff;border:none;padding:2px 6px;cursor:pointer;"><span class="dashicons dashicons-trash" style="font-size:14px;width:14px;height:14px;"></span></button>
            <button type="button" id="wppoppop-inspector-close" class="button button-small" title="Close Inspector" style="background:transparent;color:#94a3b8;border:none;font-size:20px;cursor:pointer;line-height:1;padding:0 4px;">&times;</button>
        </div>
    </div>

    <!-- Inspector Tabs Navigation -->
    <div class="wppoppop-inspector-tabs-nav" style="display:flex;background:#1e293b;border-bottom:1px solid #334155;flex-shrink:0;">
        <button type="button" class="wppoppop-insp-tab active" data-tab="basic" style="flex:1;padding:10px 0;background:#0f172a;color:#38bdf8;border:none;border-bottom:3px solid #38bdf8;font-size:12px;font-weight:700;cursor:pointer;text-align:center;">BASIC</button>
        <button type="button" class="wppoppop-insp-tab" data-tab="style" style="flex:1;padding:10px 0;background:transparent;color:#94a3b8;border:none;border-bottom:3px solid transparent;font-size:12px;font-weight:700;cursor:pointer;text-align:center;">STYLE</button>
        <button type="button" class="wppoppop-insp-tab" data-tab="logic" style="flex:1;padding:10px 0;background:transparent;color:#94a3b8;border:none;border-bottom:3px solid transparent;font-size:12px;font-weight:700;cursor:pointer;text-align:center;">LOGIC</button>
    </div>

    <!-- Scrollable Drawer Body -->
    <div class="wppoppop-inspector-body" style="flex:1;overflow-y:auto;padding:16px;box-sizing:border-box;">

        <!-- ================= TAB 1: BASIC ================= -->
        <div id="wppoppop-insp-tab-basic" class="wppoppop-insp-tab-content" style="display:block;">
            <div class="wppoppop-prop-group" style="margin-bottom:18px;">
                <label style="display:block;font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:8px;letter-spacing:0.5px;">Coordinates & Size</label>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                    <div><span style="font-size:10px;color:#64748b;">X (Left)</span><input type="number" id="prop-x" value="20" style="width:100%;background:#1e293b;border:1px solid #334155;color:#f8fafc;padding:5px 8px;border-radius:4px;"></div>
                    <div><span style="font-size:10px;color:#64748b;">Y (Top)</span><input type="number" id="prop-y" value="20" style="width:100%;background:#1e293b;border:1px solid #334155;color:#f8fafc;padding:5px 8px;border-radius:4px;"></div>
                    <div><span style="font-size:10px;color:#64748b;">Width (px)</span><input type="number" id="prop-w" value="200" style="width:100%;background:#1e293b;border:1px solid #334155;color:#f8fafc;padding:5px 8px;border-radius:4px;"></div>
                    <div><span style="font-size:10px;color:#64748b;">Height (px)</span><input type="number" id="prop-h" value="40" style="width:100%;background:#1e293b;border:1px solid #334155;color:#f8fafc;padding:5px 8px;border-radius:4px;"></div>
                </div>
            </div>

            <div id="panel-content-generic" class="wppoppop-prop-group" style="margin-bottom:18px;">
                <label for="prop-content" style="display:block;font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:8px;letter-spacing:0.5px;">Content / Label</label>
                <textarea id="prop-content" rows="3" style="width:100%;background:#1e293b;border:1px solid #334155;color:#f8fafc;padding:8px;border-radius:4px;box-sizing:border-box;font-size:13px;"></textarea>
            </div>

            <!-- Contextual 26 Element Panels -->
            <div id="panel-type-title" class="wppoppop-panel-specific" style="display:none;margin-bottom:18px;">
                <label for="prop-title-tag" style="display:block;font-size:11px;font-weight:700;color:#94a3b8;margin-bottom:6px;">HTML Heading Tag</label>
                <select id="prop-title-tag" style="width:100%;background:#1e293b;border:1px solid #334155;color:#f8fafc;padding:6px;border-radius:4px;">
                    <option value="h1">H1 — Large Main Heading</option>
                    <option value="h2" selected>H2 — Section Heading</option>
                    <option value="h3">H3 — Subheading</option>
                    <option value="h4">H4 — Small Heading</option>
                    <option value="p">P — Paragraph</option>
                </select>
            </div>

            <div id="panel-type-image" class="wppoppop-panel-specific" style="display:none;margin-bottom:18px;">
                <label for="prop-image-url" style="display:block;font-size:11px;font-weight:700;color:#94a3b8;margin-bottom:6px;">Image URL</label>
                <input type="text" id="prop-image-url" placeholder="https://example.com/image.png" style="width:100%;background:#1e293b;border:1px solid #334155;color:#f8fafc;padding:6px;border-radius:4px;margin-bottom:8px;">
                <label for="prop-image-fit" style="display:block;font-size:11px;font-weight:700;color:#94a3b8;margin-bottom:6px;">Object Fit</label>
                <select id="prop-image-fit" style="width:100%;background:#1e293b;border:1px solid #334155;color:#f8fafc;padding:6px;border-radius:4px;">
                    <option value="cover">Cover (Fill & Crop)</option>
                    <option value="contain">Contain (Scale Proportional)</option>
                    <option value="fill">Fill (Stretch)</option>
                </select>
            </div>

            <div id="panel-type-video" class="wppoppop-panel-specific" style="display:none;margin-bottom:18px;">
                <label for="prop-video-url" style="display:block;font-size:11px;font-weight:700;color:#94a3b8;margin-bottom:6px;">Video URL (YouTube, Vimeo, MP4)</label>
                <input type="text" id="prop-video-url" placeholder="https://www.youtube.com/watch?v=..." style="width:100%;background:#1e293b;border:1px solid #334155;color:#f8fafc;padding:6px;border-radius:4px;margin-bottom:8px;">
                <label style="display:flex;align-items:center;gap:6px;font-size:12px;color:#e2e8f0;cursor:pointer;">
                    <input type="checkbox" id="prop-video-autoplay" value="1"> Autoplay Video (Muted)
                </label>
            </div>

            <div id="panel-type-shape" class="wppoppop-panel-specific" style="display:none;margin-bottom:18px;">
                <label for="prop-shape-preset" style="display:block;font-size:11px;font-weight:700;color:#94a3b8;margin-bottom:6px;">Vector Shape Preset</label>
                <select id="prop-shape-preset" style="width:100%;background:#1e293b;border:1px solid #334155;color:#f8fafc;padding:6px;border-radius:4px;margin-bottom:8px;">
                    <option value="circle">Circle / Ellipse</option>
                    <option value="square">Square</option>
                    <option value="rounded_square">Rounded Square</option>
                    <option value="star">Star</option>
                    <option value="triangle">Triangle</option>
                    <option value="diamond">Diamond</option>
                    <option value="heart">Heart</option>
                    <option value="hexagon">Hexagon</option>
                    <option value="octagon">Octagon</option>
                    <option value="shield">Shield</option>
                    <option value="cross">Cross</option>
                </select>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                    <div><span style="font-size:10px;color:#64748b;">Stroke Width</span><input type="number" id="prop-shape-stroke-width" min="0" max="20" value="0" style="width:100%;background:#1e293b;border:1px solid #334155;color:#f8fafc;padding:5px;border-radius:4px;"></div>
                    <div><span style="font-size:10px;color:#64748b;">Rotation (deg)</span><input type="number" id="prop-shape-rotate" min="0" max="360" value="0" style="width:100%;background:#1e293b;border:1px solid #334155;color:#f8fafc;padding:5px;border-radius:4px;"></div>
                </div>
            </div>

            <div id="panel-type-form-field" class="wppoppop-panel-specific" style="display:none;margin-bottom:18px;">
                <label for="prop-field-name" style="display:block;font-size:11px;font-weight:700;color:#94a3b8;margin-bottom:6px;">Field Name Identifier ({token})</label>
                <input type="text" id="prop-field-name" placeholder="email" style="width:100%;background:#1e293b;border:1px solid #334155;color:#f8fafc;padding:6px;border-radius:4px;margin-bottom:8px;">
                <label style="display:flex;align-items:center;gap:6px;font-size:12px;color:#e2e8f0;cursor:pointer;margin-bottom:8px;">
                    <input type="checkbox" id="prop-field-required" value="1"> Required Field (Mandatory)
                </label>
                <div id="panel-sub-choices" style="display:none;">
                    <label for="prop-field-choices" style="display:block;font-size:11px;font-weight:700;color:#94a3b8;margin-bottom:6px;">Choices List (Comma Separated)</label>
                    <input type="text" id="prop-field-choices" placeholder="Option 1, Option 2, Option 3" style="width:100%;background:#1e293b;border:1px solid #334155;color:#f8fafc;padding:6px;border-radius:4px;">
                </div>
            </div>

            <div id="panel-type-step-btn" class="wppoppop-panel-specific" style="display:none;margin-bottom:18px;">
                <label for="prop-step-target" style="display:block;font-size:11px;font-weight:700;color:#94a3b8;margin-bottom:6px;">Target Canvas Sequence</label>
                <select id="prop-step-target" style="width:100%;background:#1e293b;border:1px solid #334155;color:#f8fafc;padding:6px;border-radius:4px;">
                    <option value="1">Canvas 1</option>
                    <option value="2" selected>Canvas 2</option>
                    <option value="3">Canvas 3</option>
                </select>
            </div>

            <div id="panel-type-link-btn" class="wppoppop-panel-specific" style="display:none;margin-bottom:18px;">
                <label for="prop-link-url" style="display:block;font-size:11px;font-weight:700;color:#94a3b8;margin-bottom:6px;">Link Destination URL</label>
                <input type="text" id="prop-link-url" placeholder="https://example.com" style="width:100%;background:#1e293b;border:1px solid #334155;color:#f8fafc;padding:6px;border-radius:4px;margin-bottom:8px;">
                <label style="display:flex;align-items:center;gap:6px;font-size:12px;color:#e2e8f0;cursor:pointer;">
                    <input type="checkbox" id="prop-link-blank" value="1" checked> Open Link in New Tab
                </label>
            </div>

            <div id="panel-type-wheel" class="wppoppop-panel-specific" style="display:none;margin-bottom:18px;">
                <label for="prop-wheel-slices" style="display:block;font-size:11px;font-weight:700;color:#94a3b8;margin-bottom:6px;">Prize Slices (Comma Separated)</label>
                <input type="text" id="prop-wheel-slices" value="10% OFF, FREE SHIP, 25% OFF, JACKPOT, 5% OFF" style="width:100%;background:#1e293b;border:1px solid #334155;color:#f8fafc;padding:6px;border-radius:4px;margin-bottom:8px;">
                <label for="prop-wheel-btn-text" style="display:block;font-size:11px;font-weight:700;color:#94a3b8;margin-bottom:6px;">Spin Button Label</label>
                <input type="text" id="prop-wheel-btn-text" value="SPIN TO WIN!" style="width:100%;background:#1e293b;border:1px solid #334155;color:#f8fafc;padding:6px;border-radius:4px;">
            </div>

            <div id="panel-type-countdown" class="wppoppop-panel-specific" style="display:none;margin-bottom:18px;">
                <label for="prop-countdown-seconds" style="display:block;font-size:11px;font-weight:700;color:#94a3b8;margin-bottom:6px;">Duration (Seconds)</label>
                <input type="number" id="prop-countdown-seconds" value="900" min="10" step="30" style="width:100%;background:#1e293b;border:1px solid #334155;color:#f8fafc;padding:6px;border-radius:4px;">
            </div>

            <div id="panel-type-pay" class="wppoppop-panel-specific" style="display:none;margin-bottom:18px;">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                    <div><span style="font-size:10px;color:#64748b;">Amount</span><input type="number" id="prop-pay-amount" value="19.99" step="0.01" style="width:100%;background:#1e293b;border:1px solid #334155;color:#f8fafc;padding:5px;border-radius:4px;"></div>
                    <div><span style="font-size:10px;color:#64748b;">Currency</span><input type="text" id="prop-pay-currency" value="USD" style="width:100%;background:#1e293b;border:1px solid #334155;color:#f8fafc;padding:5px;border-radius:4px;"></div>
                </div>
            </div>
        </div>

        <!-- ================= TAB 2: STYLE ================= -->
        <div id="wppoppop-insp-tab-style" class="wppoppop-insp-tab-content" style="display:none;">
            <div class="wppoppop-prop-group" style="margin-bottom:18px;">
                <label style="display:block;font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:8px;letter-spacing:0.5px;">Typography</label>
                <div style="margin-bottom:8px;">
                    <span style="font-size:10px;color:#64748b;">Font Family</span>
                    <select id="prop-font-family" style="width:100%;background:#1e293b;border:1px solid #334155;color:#f8fafc;padding:6px;border-radius:4px;">
                        <option value="inherit">Theme Default</option>
                        <option value="Inter, sans-serif">Inter</option>
                        <option value="Roboto, sans-serif">Roboto</option>
                        <option value="Montserrat, sans-serif">Montserrat</option>
                        <option value="Poppins, sans-serif">Poppins</option>
                        <option value="Open Sans, sans-serif">Open Sans</option>
                        <option value="Lato, sans-serif">Lato</option>
                    </select>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:8px;">
                    <div><span style="font-size:10px;color:#64748b;">Font Size (px)</span><input type="number" id="prop-font-size" value="14" min="8" max="96" style="width:100%;background:#1e293b;border:1px solid #334155;color:#f8fafc;padding:5px;border-radius:4px;"></div>
                    <div><span style="font-size:10px;color:#64748b;">Font Weight</span>
                        <select id="prop-font-weight" style="width:100%;background:#1e293b;border:1px solid #334155;color:#f8fafc;padding:5px;border-radius:4px;">
                            <option value="400">Regular (400)</option>
                            <option value="500">Medium (500)</option>
                            <option value="600">Semi-Bold (600)</option>
                            <option value="700">Bold (700)</option>
                            <option value="800">Extra Bold (800)</option>
                        </select>
                    </div>
                    <div><span style="font-size:10px;color:#64748b;">Line Height</span><input type="number" id="prop-line-height" value="1.4" step="0.1" min="0.8" max="3" style="width:100%;background:#1e293b;border:1px solid #334155;color:#f8fafc;padding:5px;border-radius:4px;"></div>
                    <div><span style="font-size:10px;color:#64748b;">Letter Spacing (px)</span><input type="number" id="prop-letter-spacing" value="0" step="0.5" min="-2" max="10" style="width:100%;background:#1e293b;border:1px solid #334155;color:#f8fafc;padding:5px;border-radius:4px;"></div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:8px;">
                    <div><span style="font-size:10px;color:#64748b;">Alignment</span>
                        <select id="prop-text-align" style="width:100%;background:#1e293b;border:1px solid #334155;color:#f8fafc;padding:5px;border-radius:4px;">
                            <option value="left">Left</option>
                            <option value="center">Center</option>
                            <option value="right">Right</option>
                        </select>
                    </div>
                    <div><span style="font-size:10px;color:#64748b;">Text Color</span>
                        <div style="display:flex;align-items:center;gap:4px;">
                            <input type="color" id="prop-color" value="#0f172a" style="width:28px;height:28px;padding:0;border:none;background:transparent;cursor:pointer;">
                            <input type="text" id="prop-color-hex" value="#0f172a" style="flex:1;background:#1e293b;border:1px solid #334155;color:#f8fafc;padding:4px;border-radius:4px;font-size:11px;">
                        </div>
                    </div>
                </div>
            </div>

            <div class="wppoppop-prop-group" style="margin-bottom:18px;">
                <label style="display:block;font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:8px;letter-spacing:0.5px;">Background & Borders</label>
                <div style="margin-bottom:8px;">
                    <span style="font-size:10px;color:#64748b;">Background Color</span>
                    <div style="display:flex;align-items:center;gap:6px;">
                        <input type="color" id="prop-bg-color" value="#ffffff" style="width:28px;height:28px;padding:0;border:none;background:transparent;cursor:pointer;">
                        <input type="text" id="prop-bg-color-hex" value="#ffffff" style="flex:1;background:#1e293b;border:1px solid #334155;color:#f8fafc;padding:4px;border-radius:4px;font-size:11px;">
                        <button type="button" id="prop-bg-transparent-btn" class="button button-small" style="background:#334155;color:#e2e8f0;border:none;font-size:10px;padding:2px 6px;">Clear</button>
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:8px;">
                    <div><span style="font-size:10px;color:#64748b;">Border Style</span>
                        <select id="prop-border-style" style="width:100%;background:#1e293b;border:1px solid #334155;color:#f8fafc;padding:5px;border-radius:4px;">
                            <option value="none">None</option>
                            <option value="solid" selected>Solid</option>
                            <option value="dashed">Dashed</option>
                            <option value="dotted">Dotted</option>
                        </select>
                    </div>
                    <div><span style="font-size:10px;color:#64748b;">Border Width (px)</span><input type="number" id="prop-border-width" value="1" min="0" max="20" style="width:100%;background:#1e293b;border:1px solid #334155;color:#f8fafc;padding:5px;border-radius:4px;"></div>
                    <div><span style="font-size:10px;color:#64748b;">Border Radius (px)</span><input type="number" id="prop-border-radius" value="4" min="0" max="100" style="width:100%;background:#1e293b;border:1px solid #334155;color:#f8fafc;padding:5px;border-radius:4px;"></div>
                    <div><span style="font-size:10px;color:#64748b;">Border Color</span>
                        <div style="display:flex;align-items:center;gap:4px;">
                            <input type="color" id="prop-border-color" value="#cbd5e1" style="width:28px;height:28px;padding:0;border:none;background:transparent;cursor:pointer;">
                            <input type="text" id="prop-border-color-hex" value="#cbd5e1" style="flex:1;background:#1e293b;border:1px solid #334155;color:#f8fafc;padding:4px;border-radius:4px;font-size:11px;">
                        </div>
                    </div>
                </div>
                <div><span style="font-size:10px;color:#64748b;">Box Shadow</span>
                    <select id="prop-box-shadow" style="width:100%;background:#1e293b;border:1px solid #334155;color:#f8fafc;padding:6px;border-radius:4px;">
                        <option value="none">None</option>
                        <option value="0 1px 3px rgba(0,0,0,0.1)">Subtle Small</option>
                        <option value="0 4px 6px -1px rgba(0,0,0,0.15)">Medium Elevation</option>
                        <option value="0 10px 15px -3px rgba(0,0,0,0.2)">High Depth</option>
                        <option value="0 20px 25px -5px rgba(0,0,0,0.3)">Ultra Shadow</option>
                    </select>
                </div>
            </div>

            <!-- ELEMENT ANIMATION SUITE -->
                <div class="wppoppop-elem-anim-setting-wrap" style="margin-top:16px;padding-top:14px;border-top:1px solid #e2e8f0;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                        <div style="display:flex;align-items:center;gap:6px;">
                            <span style="font-size:12px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:0.5px;">ANIMATION</span>
                            <span class="dashicons dashicons-editor-help" style="font-size:16px;width:16px;height:16px;color:#2563eb;cursor:help;display:inline-flex;align-items:center;justify-content:center;" title="Configure appearance entrance and disappearance exit animations for this element."></span>
                        </div>
                        <a href="https://animate.style/" target="_blank" rel="noopener noreferrer" class="wppoppop-anim-badge-link" style="display:inline-flex;align-items:center;padding:2px 8px;font-size:11px;font-weight:600;color:#2563eb;background:#eff6ff;border:1px solid #bfdbfe;border-radius:4px;text-decoration:none;line-height:1.4;">Animate.css</a>
                    </div>

                    <!-- Appearance Dropdown -->
                    <div style="margin-bottom:10px;">
                        <select id="prop-anim-appearance" class="widefat wppoppop-anim-appearance-input" style="width:100%;font-size:12px;height:34px;border:1px solid #cbd5e1;border-radius:4px;background:#fff;padding:0 8px;box-sizing:border-box;">
                            <option value="none">None</option>
                            <option value="bounceInLeft" selected>bounceInLeft</option>
                            <option value="bounceInRight">bounceInRight</option>
                            <option value="bounceInDown">bounceInDown</option>
                            <option value="bounceInUp">bounceInUp</option>
                            <option value="bounceIn">bounceIn</option>
                            <option value="fadeIn">fadeIn</option>
                            <option value="fadeInDown">fadeInDown</option>
                            <option value="fadeInLeft">fadeInLeft</option>
                            <option value="fadeInRight">fadeInRight</option>
                            <option value="fadeInUp">fadeInUp</option>
                            <option value="zoomIn">zoomIn</option>
                            <option value="zoomInDown">zoomInDown</option>
                            <option value="zoomInUp">zoomInUp</option>
                            <option value="slideDown">slideDown</option>
                            <option value="slideUp">slideUp</option>
                            <option value="slideLeft">slideLeft</option>
                            <option value="slideRight">slideRight</option>
                            <option value="flipInX">flipInX</option>
                            <option value="flipInY">flipInY</option>
                            <option value="pulse">pulse</option>
                            <option value="tada">tada</option>
                            <option value="rubberBand">rubberBand</option>
                            <option value="shake">shake</option>
                        </select>
                        <span class="wppoppop-anim-sublabel" style="display:block;font-size:11px;font-style:italic;color:#94a3b8;margin-top:3px;">Appearance</span>
                    </div>

                    <!-- Duration & Start delay Inputs Row -->
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px;">
                        <div>
                            <div class="wppoppop-input-suffix-wrap" style="position:relative;display:flex;align-items:center;">
                                <input type="number" id="prop-anim-duration" value="1000" min="0" step="50" class="widefat wppoppop-anim-duration-input" style="width:100%;height:34px;padding:0 30px 0 8px;font-size:12px;border:1px solid #cbd5e1;border-radius:4px;box-sizing:border-box;">
                                <span class="wppoppop-input-suffix" style="position:absolute;right:8px;font-size:11px;color:#94a3b8;pointer-events:none;">ms</span>
                            </div>
                            <span class="wppoppop-anim-sublabel" style="display:block;font-size:11px;font-style:italic;color:#94a3b8;margin-top:3px;">Duration</span>
                        </div>
                        <div>
                            <div class="wppoppop-input-suffix-wrap" style="position:relative;display:flex;align-items:center;">
                                <input type="number" id="prop-anim-delay" value="0" min="0" step="50" class="widefat wppoppop-anim-delay-input" style="width:100%;height:34px;padding:0 30px 0 8px;font-size:12px;border:1px solid #cbd5e1;border-radius:4px;box-sizing:border-box;">
                                <span class="wppoppop-input-suffix" style="position:absolute;right:8px;font-size:11px;color:#94a3b8;pointer-events:none;">ms</span>
                            </div>
                            <span class="wppoppop-anim-sublabel" style="display:block;font-size:11px;font-style:italic;color:#94a3b8;margin-top:3px;">Start delay</span>
                        </div>
                    </div>

                    <!-- Disappearance Dropdown -->
                    <div style="margin-bottom:4px;">
                        <select id="prop-anim-disappearance" class="widefat wppoppop-anim-disappearance-input" style="width:100%;font-size:12px;height:34px;border:1px solid #cbd5e1;border-radius:4px;background:#fff;padding:0 8px;box-sizing:border-box;">
                            <option value="none">None</option>
                            <option value="bounceOut">bounceOut</option>
                            <option value="bounceOutLeft">bounceOutLeft</option>
                            <option value="bounceOutRight">bounceOutRight</option>
                            <option value="bounceOutUp">bounceOutUp</option>
                            <option value="bounceOutDown">bounceOutDown</option>
                            <option value="fadeOut">fadeOut</option>
                            <option value="fadeOutDown">fadeOutDown</option>
                            <option value="fadeOutLeft">fadeOutLeft</option>
                            <option value="fadeOutRight">fadeOutRight</option>
                            <option value="fadeOutUp">fadeOutUp</option>
                            <option value="zoomOut">zoomOut</option>
                            <option value="slideOutDown">slideOutDown</option>
                            <option value="slideOutUp">slideOutUp</option>
                            <option value="slideOutLeft">slideOutLeft</option>
                            <option value="slideOutRight">slideOutRight</option>
                            <option value="flipOutX">flipOutX</option>
                            <option value="flipOutY">flipOutY</option>
                        </select>
                        <span class="wppoppop-anim-sublabel" style="display:block;font-size:11px;font-style:italic;color:#94a3b8;margin-top:3px;">Disappearance</span>
                    </div>
                </div>
        </div>

    </div>
</div>
