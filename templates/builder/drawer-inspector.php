<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div id="wppoppop-inspector-drawer">
    <div class="wppoppop-insp-header">
        <button type="button" id="wppoppop-inspector-close" title="Close Properties">&times;</button>
        <button type="button" class="wppoppop-insp-tab active" data-tab="basic">Basic</button>
        <button type="button" class="wppoppop-insp-tab" data-tab="style">Style</button>
        <button type="button" class="wppoppop-insp-tab" data-tab="logic">Logic</button>
    </div>

    <div style="flex:1;overflow-y:auto;padding:16px;">
        <!-- Basic Tab -->
        <div class="wppoppop-insp-content" id="insp-tab-basic">
            <!-- Layer Actions: Duplicate & Delete -->
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;gap:8px;">
                <button type="button" id="wppoppop-insp-btn-duplicate" class="wppoppop-insp-action-btn" title="Duplicate Layer (Cmd/Ctrl+D)" style="flex:1;background:#1e293b;border:1px solid #334155;color:#94a3b8;border-radius:4px;padding:5px 8px;font-size:11px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:4px;">
                    <span class="dashicons dashicons-admin-page" style="font-size:14px;width:14px;height:14px;"></span> Duplicate
                </button>
                <button type="button" id="wppoppop-insp-btn-delete" class="wppoppop-insp-action-btn" title="Delete Layer (Delete/Backspace)" style="flex:1;background:#1e293b;border:1px solid rgba(239,68,68,0.4);color:#fca5a5;border-radius:4px;padding:5px 8px;font-size:11px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:4px;">
                    <span class="dashicons dashicons-trash" style="font-size:14px;width:14px;height:14px;color:#ef4444;"></span> Delete
                </button>
            </div>

            <!-- Alignment Toolbar -->
            <div style="margin-bottom:12px;background:#1e293b;border:1px solid #334155;border-radius:6px;padding:8px;">
                <label style="display:block;font-size:10px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px;">Align to Canvas</label>
                <div class="wppoppop-align-btn-group" style="display:grid;grid-template-columns:repeat(6, 1fr);gap:4px;">
                    <button type="button" class="wppoppop-align-btn" data-align="left" title="Align Left">
                        <span class="dashicons dashicons-align-left" style="font-size:14px;width:14px;height:14px;"></span>
                    </button>
                    <button type="button" class="wppoppop-align-btn" data-align="center_h" title="Center Horizontally">
                        <span class="dashicons dashicons-align-center" style="font-size:14px;width:14px;height:14px;"></span>
                    </button>
                    <button type="button" class="wppoppop-align-btn" data-align="right" title="Align Right">
                        <span class="dashicons dashicons-align-right" style="font-size:14px;width:14px;height:14px;"></span>
                    </button>
                    <button type="button" class="wppoppop-align-btn" data-align="top" title="Align Top">
                        <span class="dashicons dashicons-arrow-up-alt2" style="font-size:14px;width:14px;height:14px;"></span>
                    </button>
                    <button type="button" class="wppoppop-align-btn" data-align="center_v" title="Middle Vertically">
                        <span class="dashicons dashicons-minus" style="font-size:14px;width:14px;height:14px;"></span>
                    </button>
                    <button type="button" class="wppoppop-align-btn" data-align="bottom" title="Align Bottom">
                        <span class="dashicons dashicons-arrow-down-alt2" style="font-size:14px;width:14px;height:14px;"></span>
                    </button>
                </div>
            </div>

            <div style="margin-bottom:12px;">
                <label>Layer Label</label>
                <input type="text" id="prop-layer-name">
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:12px;">
                <div>
                    <label>Top (px)</label>
                    <input type="number" id="prop-pos-top">
                </div>
                <div>
                    <label>Left (px)</label>
                    <input type="number" id="prop-pos-left">
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:12px;">
                <div>
                    <label>Width (px)</label>
                    <input type="number" id="prop-size-width">
                </div>
                <div>
                    <label>Height (px)</label>
                    <input type="number" id="prop-size-height">
                </div>
            </div>

            <div style="margin-bottom:12px;">
                <label>Content / Value</label>
                <textarea id="prop-content" rows="3"></textarea>
            </div>
        </div>

        <!-- Style Tab with 50%/50% Color Pickers & Live Animate.style Previews -->
        <div class="wppoppop-insp-content" id="insp-tab-style" style="display:none;">
            <div style="margin-bottom:12px;">
                <label>Font Family</label>
                <select id="prop-font-family">
                    <option value="inherit">Inherit System</option>
                    <option value="Arial, sans-serif">Arial</option>
                    <option value="'Helvetica Neue', sans-serif">Helvetica</option>
                    <option value="'Roboto', sans-serif">Roboto</option>
                    <option value="'Open Sans', sans-serif">Open Sans</option>
                    <option value="'Inter', sans-serif">Inter</option>
                    <option value="Georgia, serif">Georgia</option>
                </select>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:12px;">
                <div>
                    <label>Font Size (px)</label>
                    <input type="number" id="prop-font-size" value="14">
                </div>
                <div>
                    <label>Radius (px)</label>
                    <input type="number" id="prop-border-radius" value="0">
                </div>
            </div>

            <!-- Text Color: 50% Color Swatch + 50% Hex Input on Same Row -->
            <div style="margin-bottom:12px;">
                <label style="display:block;margin-bottom:4px;">Text Color</label>
                <div class="wppoppop-color-picker-row">
                    <div class="wppoppop-color-picker-wrap">
                        <input type="color" class="wppoppop-color-swatch-input" data-target="#prop-color" value="#1e293b" title="Pick text color">
                    </div>
                    <input type="text" id="prop-color" class="wppoppop-color-hex-input" value="#1e293b" placeholder="#1e293b">
                </div>
            </div>

            <!-- Background Color: 50% Color Swatch + 50% Hex Input on Same Row -->
            <div style="margin-bottom:12px;">
                <label style="display:block;margin-bottom:4px;">Background Color</label>
                <div class="wppoppop-color-picker-row">
                    <div class="wppoppop-color-picker-wrap">
                        <input type="color" class="wppoppop-color-swatch-input" data-target="#prop-bg-color" value="#ffffff" title="Pick background color">
                    </div>
                    <input type="text" id="prop-bg-color" class="wppoppop-color-hex-input" value="#ffffff" placeholder="#ffffff">
                </div>
            </div>

            <div style="margin-bottom:12px;">
                <label>Opacity (0 to 1)</label>
                <input type="number" id="prop-opacity" step="0.1" min="0" max="1" value="1">
            </div>

            <!-- Element Animation Effect with Replay Action -->
            <div style="margin-bottom:12px;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                    <label style="margin:0;">Animation Effect (Animate.style)</label>
                    <button type="button" id="wppoppop-insp-btn-replay-anim" title="Replay Animation on Canvas" style="background:transparent;border:none;color:#38bdf8;font-size:11px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:3px;padding:0;">
                        <span class="dashicons dashicons-controls-play" style="font-size:13px;width:13px;height:13px;"></span> Replay
                    </button>
                </div>
                <select id="prop-anim-effect">
                    <option value="none">None</option>
                    <optgroup label="Attention Seekers">
                        <option value="animate__bounce">Bounce</option>
                        <option value="animate__flash">Flash</option>
                        <option value="animate__pulse">Pulse</option>
                        <option value="animate__rubberBand">Rubber Band</option>
                        <option value="animate__shakeX">Shake X</option>
                        <option value="animate__shakeY">Shake Y</option>
                        <option value="animate__headShake">Head Shake</option>
                        <option value="animate__swing">Swing</option>
                        <option value="animate__tada">Tada</option>
                        <option value="animate__wobble">Wobble</option>
                        <option value="animate__jello">Jello</option>
                        <option value="animate__heartBeat">HeartBeat</option>
                    </optgroup>
                    <optgroup label="Entrances">
                        <option value="animate__fadeIn">Fade In</option>
                        <option value="animate__fadeInDown">Fade In Down</option>
                        <option value="animate__fadeInUp">Fade In Up</option>
                        <option value="animate__fadeInLeft">Fade In Left</option>
                        <option value="animate__fadeInRight">Fade In Right</option>
                        <option value="animate__bounceIn">Bounce In</option>
                        <option value="animate__zoomIn">Zoom In</option>
                        <option value="animate__slideInUp">Slide In Up</option>
                        <option value="animate__slideInDown">Slide In Down</option>
                        <option value="animate__flipInX">Flip In X</option>
                        <option value="animate__flipInY">Flip In Y</option>
                    </optgroup>
                </select>
            </div>
        </div>

        <!-- Logic Tab -->
        <div class="wppoppop-insp-content" id="insp-tab-logic" style="display:none;">
            <div style="margin-bottom:12px;">
                <label>Action on Click / Submit</label>
                <select id="prop-action-close">
                    <option value="none">Do Nothing</option>
                    <option value="next_screen">Proceed to Next Screen</option>
                    <option value="jump_screen">Jump to Specific Screen</option>
                    <option value="close">Close Popup</option>
                    <option value="redirect">Redirect to URL</option>
                </select>
            </div>

            <div id="prop-target-screen-wrap" style="margin-bottom:12px;">
                <label>Default Target Screen</label>
                <select id="prop-target-screen">
                    <!-- Populated dynamically -->
                </select>
            </div>

            <div id="prop-action-url-wrap" style="margin-bottom:12px;">
                <label>Redirect URL</label>
                <input type="url" id="prop-action-url" placeholder="https://example.com/checkout">
            </div>

            <div style="margin-bottom:12px;">
                <label style="display:flex;align-items:center;gap:6px;cursor:pointer;">
                    <input type="checkbox" id="prop-action-blank"> Open link in new window
                </label>
            </div>

            <div class="wppoppop-cond-toggle-wrap" style="margin-top:14px;padding:12px;background:#1e293b;border:1px solid #334155;border-radius:6px;">
                <label style="display:flex;align-items:center;gap:8px;font-weight:700;color:#f8fafc;cursor:pointer;margin-bottom:6px;">
                    <input type="checkbox" id="prop-cond-enable">
                    <span>Enable Conditional Logic</span>
                </label>
                <p style="font-size:11px;color:#94a3b8;margin:0 0 10px 0;line-height:1.4;">
                    Branch navigation dynamically based on visitor inputs in elements on this screen.
                </p>

                <div id="prop-cond-box" style="display:none;border-top:1px solid #334155;padding-top:10px;">
                    <div style="margin-bottom:10px;">
                        <label>Evaluate Element</label>
                        <select id="prop-cond-field">
                            <!-- Populated dynamically -->
                        </select>
                    </div>

                    <div style="margin-bottom:10px;">
                        <label>Condition</label>
                        <select id="prop-cond-operator">
                            <option value="equals">Equals (=)</option>
                            <option value="not_equals">Does not equal (!=)</option>
                            <option value="contains">Contains text</option>
                            <option value="greater_than">Greater than (&gt;)</option>
                            <option value="less_than">Less than (&lt;)</option>
                            <option value="is_empty">Is Empty</option>
                            <option value="is_not_empty">Is Not Empty</option>
                        </select>
                    </div>

                    <div id="prop-cond-val-wrap" style="margin-bottom:10px;">
                        <label>Match Value</label>
                        <input type="text" id="prop-cond-val" placeholder="e.g. VIP, Yes, 5">
                    </div>

                    <div style="margin-bottom:10px;">
                        <label>If True, Jump to Screen</label>
                        <select id="prop-cond-target-screen">
                            <!-- Populated dynamically -->
                        </select>
                    </div>

                    <div style="margin-bottom:4px;">
                        <label>Otherwise (Fallback)</label>
                        <select id="prop-cond-fallback-screen">
                            <!-- Populated dynamically -->
                        </select>
                    </div>
                </div>
            </div>

            <div style="margin-top:14px;">
                <label>Custom JavaScript OnClick</label>
                <textarea id="prop-action-js" rows="3" placeholder="console.log('Action triggered');"></textarea>
            </div>
        </div>
    </div>
</div>
