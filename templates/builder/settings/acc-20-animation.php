<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<!-- DECOMPOSED MODULE 20: ANIMATION (per-canvas appearance / disappearance) -->
            <div class="wppoppop-acc-item">
                <button type="button" class="wppoppop-acc-header">Animation</button>
                <div class="wppoppop-acc-body" style="display:none;">
                    <span style="display:block;font-size:10px;color:#64748b;margin-bottom:10px;">Applies to the canvas currently selected in Canvas Settings.</span>
                    <!-- ANIMATE.STYLE ANIMATION SECTION -->
                        <!-- ANIMATION SECTION -->
                        <div class="wppoppop-anim-setting-wrap" style="margin:0;padding:0;">
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                                <div style="display:flex;align-items:center;gap:6px;">
                                    <span style="font-size:12px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:0.5px;">ANIMATION</span>
                                    <span class="dashicons dashicons-editor-help" style="font-size:16px;width:16px;height:16px;color:#64748b;cursor:help;" title="Configure appearance entrance and disappearance exit animations for this canvas."></span>
                                </div>
                                <button type="button" id="set-canvas-anim-play" class="button wppoppop-anim-play-btn" title="Play Canvas Animation" style="display:inline-flex;align-items:center;gap:4px;padding:2px 8px;font-size:11px;font-weight:600;color:#334155;background:#f1f5f9;border:1px solid #cbd5e1;border-radius:4px;cursor:pointer;line-height:1.4;height:24px;">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor" style="pointer-events:none;"><polygon points="6,4 20,12 6,20"/></svg>
                                    <span>Play</span>
                                </button>
                            </div>

                            <div style="margin-bottom:10px;">
                                <select id="set-canvas-anim-appearance" class="widefat" style="font-size:12px;height:34px;">
                                    <option value="none">None</option>
                                    <optgroup label="Fading Entrances">
                                        <option value="fadeIn" selected>fadeIn</option>
                                        <option value="fadeInDown">fadeInDown</option>
                                        <option value="fadeInDownBig">fadeInDownBig</option>
                                        <option value="fadeInLeft">fadeInLeft</option>
                                        <option value="fadeInLeftBig">fadeInLeftBig</option>
                                        <option value="fadeInRight">fadeInRight</option>
                                        <option value="fadeInRightBig">fadeInRightBig</option>
                                        <option value="fadeInUp">fadeInUp</option>
                                        <option value="fadeInUpBig">fadeInUpBig</option>
                                    </optgroup>
                                    <optgroup label="Zooming Entrances">
                                        <option value="zoomIn">zoomIn</option>
                                        <option value="zoomInDown">zoomInDown</option>
                                        <option value="zoomInLeft">zoomInLeft</option>
                                        <option value="zoomInRight">zoomInRight</option>
                                        <option value="zoomInUp">zoomInUp</option>
                                    </optgroup>
                                    <optgroup label="Bouncing Entrances">
                                        <option value="bounceIn">bounceIn</option>
                                        <option value="bounceInDown">bounceInDown</option>
                                        <option value="bounceInLeft">bounceInLeft</option>
                                        <option value="bounceInRight">bounceInRight</option>
                                        <option value="bounceInUp">bounceInUp</option>
                                    </optgroup>
                                    <optgroup label="Sliding Entrances">
                                        <option value="slideInDown">slideInDown</option>
                                        <option value="slideInLeft">slideInLeft</option>
                                        <option value="slideInRight">slideInRight</option>
                                        <option value="slideInUp">slideInUp</option>
                                    </optgroup>
                                    <optgroup label="Back Entrances">
                                        <option value="backInDown">backInDown</option>
                                        <option value="backInLeft">backInLeft</option>
                                        <option value="backInRight">backInRight</option>
                                        <option value="backInUp">backInUp</option>
                                    </optgroup>
                                </select>
                                <span class="wppoppop-anim-sublabel">Appearance</span>
                            </div>

                            <!-- Duration & Start delay Inputs Row -->
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px;">
                                <div>
                                    <div class="wppoppop-input-suffix-wrap">
                                        <input type="number" id="set-canvas-anim-duration" value="1000" min="0" step="50" class="widefat">
                                        <span class="wppoppop-input-suffix">ms</span>
                                    </div>
                                    <span class="wppoppop-anim-sublabel">Duration</span>
                                </div>
                                <div>
                                    <div class="wppoppop-input-suffix-wrap">
                                        <input type="number" id="set-canvas-anim-delay" value="0" min="0" step="50" class="widefat">
                                        <span class="wppoppop-input-suffix">ms</span>
                                    </div>
                                    <span class="wppoppop-anim-sublabel">Start delay</span>
                                </div>
                            </div>

                            <!-- Disappearance on/off switch -->
                            <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;margin:4px 0 8px;">
                                <label for="set-canvas-anim-exit-enable" style="font-size:11px;font-weight:700;color:#475569;margin:0;text-transform:uppercase;cursor:pointer;">Play disappearance on close</label>
                                <span class="wppoppop-ovf-switch">
                                    <input type="checkbox" id="set-canvas-anim-exit-enable" value="1" role="switch" aria-label="Play disappearance animation when the canvas closes" checked>
                                    <span class="wppoppop-ovf-track"></span>
                                </span>
                            </div>
                            <!-- Disappearance Dropdown (Animate.css Exits) -->
                            <div style="margin-bottom:4px;">
                                <select id="set-canvas-anim-disappearance" class="widefat" style="font-size:12px;height:34px;">
                                    <option value="none">None</option>
                                    <optgroup label="Fading Exits">
                                        <option value="fadeOut" selected>fadeOut</option>
                                        <option value="fadeOutDown">fadeOutDown</option>
                                        <option value="fadeOutDownBig">fadeOutDownBig</option>
                                        <option value="fadeOutLeft">fadeOutLeft</option>
                                        <option value="fadeOutLeftBig">fadeOutLeftBig</option>
                                        <option value="fadeOutRight">fadeOutRight</option>
                                        <option value="fadeOutRightBig">fadeOutRightBig</option>
                                        <option value="fadeOutUp">fadeOutUp</option>
                                        <option value="fadeOutUpBig">fadeOutUpBig</option>
                                    </optgroup>
                                    <optgroup label="Zooming Exits">
                                        <option value="zoomOut">zoomOut</option>
                                        <option value="zoomOutDown">zoomOutDown</option>
                                        <option value="zoomOutLeft">zoomOutLeft</option>
                                        <option value="zoomOutRight">zoomOutRight</option>
                                        <option value="zoomOutUp">zoomOutUp</option>
                                    </optgroup>
                                    <optgroup label="Bouncing Exits">
                                        <option value="bounceOut">bounceOut</option>
                                        <option value="bounceOutDown">bounceOutDown</option>
                                        <option value="bounceOutLeft">bounceOutLeft</option>
                                        <option value="bounceOutRight">bounceOutRight</option>
                                        <option value="bounceOutUp">bounceOutUp</option>
                                    </optgroup>
                                    <optgroup label="Sliding Exits">
                                        <option value="slideOutDown">slideOutDown</option>
                                        <option value="slideOutLeft">slideOutLeft</option>
                                        <option value="slideOutRight">slideOutRight</option>
                                        <option value="slideOutUp">slideOutUp</option>
                                    </optgroup>
                                    <optgroup label="Back Exits">
                                        <option value="backOutDown">backOutDown</option>
                                        <option value="backOutLeft">backOutLeft</option>
                                        <option value="backOutRight">backOutRight</option>
                                        <option value="backOutUp">backOutUp</option>
                                    </optgroup>
                                </select>
                                <span class="wppoppop-anim-sublabel">Disappearance</span>
                            </div>
                        </div>
                </div>
            </div>
