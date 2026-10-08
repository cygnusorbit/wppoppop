<?php
if (!defined('ABSPATH')) {
    exit;
}
if (function_exists('wp_enqueue_media')) {
    wp_enqueue_media();
}
?>
<!-- Hardware-Accelerated Slide-In Layer Settings Inspector Panel -->
<div id="wppoppop-inspector-drawer" class="wppoppop-inspector-drawer">
    <!-- Inspector Header Tabs -->
    <div style="display:flex;align-items:center;background:#0f172a;color:#ffffff;height:42px;flex-shrink:0;">
        <button type="button" id="wppoppop-inspector-close" style="width:42px;height:42px;background:#991b1b;border:none;color:#ffffff;font-size:18px;cursor:pointer;display:flex;align-items:center;justify-content:center;line-height:1;" title="Close Inspector">&times;</button>
        <button type="button" class="wppoppop-insp-tab active" data-tab="basic" style="flex:1;background:transparent;border:none;color:#ffffff;font-weight:700;font-size:12px;cursor:pointer;height:100%;">Basic</button>
        <button type="button" class="wppoppop-insp-tab" data-tab="style" style="flex:1;background:transparent;border:none;color:#94a3b8;font-weight:700;font-size:12px;cursor:pointer;height:100%;">Style</button>
        <button type="button" class="wppoppop-insp-tab" data-tab="logic" style="flex:1;background:transparent;border:none;color:#94a3b8;font-weight:700;font-size:12px;cursor:pointer;height:100%;">Logic</button>
    </div>

    <!-- Active Element Type Indicator -->
    <div style="background:#1e293b;padding:8px 16px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #334155;flex-shrink:0;">
        <span style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;">Selected Element:</span>
        <span id="insp-element-type-badge" style="background:#2563eb;color:#ffffff;font-size:10px;font-weight:700;padding:2px 8px;border-radius:4px;text-transform:uppercase;">PARAGRAPH</span>
    </div>

    <!-- Inspector Body -->
    <div style="flex:1;overflow-y:auto;padding:16px;">
        <!-- TAB: BASIC -->
        <div class="wppoppop-insp-content active" id="insp-tab-basic" style="display:block;">
            <div style="margin-bottom:12px;">
                <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">LAYER NAME</label>
                <input type="text" id="prop-layer-name" class="widefat" style="font-size:12px;">
            </div>

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

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:16px;">
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">WIDTH (PX)</label>
                    <input type="number" id="prop-size-width" class="widefat" style="font-size:12px;">
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">HEIGHT (PX)</label>
                    <input type="number" id="prop-size-height" class="widefat" style="font-size:12px;">
                </div>
            </div>

            <hr style="border:none;border-top:1px solid #e2e8f0;margin:16px 0;">

            <!-- Contextual Element Setting Panels -->
            <div id="wppoppop-element-specific-settings">
                <!-- 1. TITLE -->
                <div class="element-panel" id="panel-elem-title" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">TITLE HEADING TEXT</label>
                    <input type="text" id="prop-title-content" class="widefat" value="Catchy Campaign Title" style="font-size:12px;margin-bottom:8px;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">HEADING TAG</label>
                    <select id="prop-title-tag" class="widefat" style="font-size:12px;">
                        <option value="h1">Heading 1 (&lt;h1&gt;)</option>
                        <option value="h2" selected>Heading 2 (&lt;h2&gt;)</option>
                        <option value="h3">Heading 3 (&lt;h3&gt;)</option>
                        <option value="h4">Heading 4 (&lt;h4&gt;)</option>
                    </select>
                </div>

                <!-- 2. PARAGRAPH / TEXT -->
                <div class="element-panel" id="panel-elem-text" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">PARAGRAPH CONTENT / HTML</label>
                    <textarea id="prop-text-content" rows="3" class="widefat" style="font-size:12px;margin-bottom:8px;"></textarea>
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">HTML TAG</label>
                    <select id="prop-text-tag" class="widefat" style="font-size:12px;">
                        <option value="p">Paragraph (&lt;p&gt;)</option>
                        <option value="h1">Heading 1 (&lt;h1&gt;)</option>
                        <option value="h2">Heading 2 (&lt;h2&gt;)</option>
                        <option value="h3">Heading 3 (&lt;h3&gt;)</option>
                        <option value="span">Inline Span (&lt;span&gt;)</option>
                    </select>
                </div>

                <!-- 3. IMAGE (WordPress Media Uploader) -->
                <div class="element-panel" id="panel-elem-image" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">IMAGE PREVIEW & SELECTION</label>
                    <div id="prop-image-preview-wrap" style="width:100%;height:120px;background:#f1f5f9;border:1px dashed #cbd5e1;border-radius:6px;display:flex;align-items:center;justify-content:center;margin-bottom:8px;overflow:hidden;position:relative;">
                        <img id="prop-image-preview" src="" alt="Preview" style="max-width:100%;max-height:100%;object-fit:contain;display:none;">
                        <span id="prop-image-placeholder" style="font-size:11px;color:#94a3b8;font-weight:600;">No image selected</span>
                    </div>
                    <div style="display:flex;gap:6px;margin-bottom:10px;">
                        <button type="button" id="prop-image-upload-btn" class="button button-primary" style="flex:1;height:32px;font-size:11px;display:inline-flex;align-items:center;justify-content:center;gap:4px;background:#2563eb;border-color:#1d4ed8;cursor:pointer;">
                            <span class="dashicons dashicons-upload" style="font-size:14px;width:14px;height:14px;line-height:1;"></span> Select from Media Library
                        </button>
                        <button type="button" id="prop-image-remove-btn" class="button" style="height:32px;font-size:11px;color:#ef4444;border-color:#fca5a5;cursor:pointer;">
                            Remove
                        </button>
                    </div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">IMAGE URL</label>
                    <input type="url" id="prop-image-url" placeholder="https://..." class="widefat" style="font-size:12px;margin-bottom:8px;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">ALT TEXT</label>
                    <input type="text" id="prop-image-alt" placeholder="Image description..." class="widefat" style="font-size:12px;margin-bottom:8px;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">OBJECT FIT</label>
                    <select id="prop-image-fit" class="widefat" style="font-size:12px;">
                        <option value="cover">Cover (Fill & Crop)</option>
                        <option value="contain">Contain (Scale to Fit)</option>
                        <option value="fill">Fill (Stretch)</option>
                        <option value="none">Original Scale</option>
                    </select>
                </div>

                <!-- 4. VIDEO (YouTube / Vimeo / MP4) -->
                <div class="element-panel" id="panel-elem-video" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">VIDEO URL (YOUTUBE / VIMEO / MP4)</label>
                    <input type="url" id="prop-video-url" class="widefat" placeholder="https://www.youtube.com/watch?v=..." style="font-size:12px;margin-bottom:8px;">
                    <div style="display:flex;flex-direction:column;gap:6px;margin-bottom:8px;">
                        <label style="font-size:12px;color:#334155;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                            <input type="checkbox" id="prop-video-autoplay" value="1"> Autoplay Video (Muted)
                        </label>
                        <label style="font-size:12px;color:#334155;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                            <input type="checkbox" id="prop-video-controls" value="1" checked> Show Video Player Controls
                        </label>
                    </div>
                </div>

                <!-- 5. SHAPE (11 SVG Presets) -->
                <div class="element-panel" id="panel-elem-shape" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">PRESET SHAPE</label>
                    <select id="prop-shape-preset" class="widefat" style="font-size:12px;margin-bottom:10px;">
                        <option value="circle">Circle / Oval</option>
                        <option value="square">Square / Rectangle</option>
                        <option value="rounded_square">Rounded Rectangle</option>
                        <option value="star">Star (5-Point)</option>
                        <option value="triangle">Triangle</option>
                        <option value="diamond">Diamond / Rhombus</option>
                        <option value="heart">Heart</option>
                        <option value="hexagon">Hexagon</option>
                        <option value="octagon">Octagon</option>
                        <option value="shield">Shield</option>
                        <option value="cross">Cross / Plus</option>
                    </select>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px;">
                        <div>
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                                <label style="font-size:10px;color:#64748b;font-weight:700;margin:0;">FILL COLOR</label>
                                <button type="button" id="prop-shape-fill-transparent-btn" class="wppoppop-checker-btn" title="Set transparent (fill: transparent;)" aria-label="Set transparent shape fill" style="width:18px;height:18px;border-radius:3px;border:1px solid #cbd5e1;cursor:pointer;padding:0;background-color:#ffffff;background-image:linear-gradient(45deg,#94a3b8 25%,transparent 25%),linear-gradient(-45deg,#94a3b8 25%,transparent 25%),linear-gradient(45deg,transparent 75%,#94a3b8 75%),linear-gradient(-45deg,transparent 75%,#94a3b8 75%);background-size:6px 6px;background-position:0 0,0 3px,3px -3px,-3px 0px;box-shadow:inset 0 0 0 1px rgba(0,0,0,0.05);"></button>
                            </div>
                            <input type="color" id="prop-shape-fill" value="#3b82f6" class="widefat" style="height:32px;padding:2px;">
                        </div>
                        <div>
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                                <label style="font-size:10px;color:#64748b;font-weight:700;margin:0;">STROKE COLOR</label>
                                <button type="button" id="prop-shape-stroke-transparent-btn" class="wppoppop-checker-btn" title="Set transparent (stroke: transparent;)" aria-label="Set transparent shape stroke" style="width:18px;height:18px;border-radius:3px;border:1px solid #cbd5e1;cursor:pointer;padding:0;background-color:#ffffff;background-image:linear-gradient(45deg,#94a3b8 25%,transparent 25%),linear-gradient(-45deg,#94a3b8 25%,transparent 25%),linear-gradient(45deg,transparent 75%,#94a3b8 75%),linear-gradient(-45deg,transparent 75%,#94a3b8 75%);background-size:6px 6px;background-position:0 0,0 3px,3px -3px,-3px 0px;box-shadow:inset 0 0 0 1px rgba(0,0,0,0.05);"></button>
                            </div>
                            <input type="color" id="prop-shape-stroke" value="#1d4ed8" class="widefat" style="height:32px;padding:2px;">
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:6px;">
                        <div>
                            <label style="font-size:10px;color:#64748b;font-weight:700;">STROKE (PX)</label>
                            <input type="number" id="prop-shape-stroke-width" value="0" min="0" max="20" class="widefat" style="font-size:12px;">
                        </div>
                        <div>
                            <label style="font-size:10px;color:#64748b;font-weight:700;">ROTATION (&deg;)</label>
                            <input type="number" id="prop-shape-rotate" value="0" min="0" max="360" class="widefat" style="font-size:12px;">
                        </div>
                    </div>
                </div>

                <!-- 6. TEXT FIELD -->
                <div class="element-panel" id="panel-elem-textfield" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">PLACEHOLDER TEXT</label>
                    <input type="text" id="prop-textfield-placeholder" class="widefat" placeholder="Enter text here..." style="font-size:12px;margin-bottom:8px;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">FIELD NAME / TOKEN</label>
                    <input type="text" id="prop-textfield-fieldname" value="text_field" class="widefat" style="font-size:12px;margin-bottom:8px;">
                    <label style="font-size:12px;color:#334155;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                        <input type="checkbox" id="prop-textfield-required" value="1"> Required Field
                    </label>
                </div>

                <!-- 7. EMAIL -->
                <div class="element-panel" id="panel-elem-email" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">PLACEHOLDER TEXT</label>
                    <input type="text" id="prop-email-placeholder" class="widefat" placeholder="Enter your email..." style="font-size:12px;margin-bottom:8px;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">FIELD NAME / TOKEN</label>
                    <input type="text" id="prop-email-fieldname" value="email" class="widefat" style="font-size:12px;margin-bottom:8px;">
                    <label style="font-size:12px;color:#334155;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                        <input type="checkbox" id="prop-email-required" value="1" checked> Required Field
                    </label>
                </div>

                <!-- 8. NUMBER -->
                <div class="element-panel" id="panel-elem-number" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">DEFAULT VALUE / PLACEHOLDER</label>
                    <input type="number" id="prop-number-val" class="widefat" style="font-size:12px;margin-bottom:8px;">
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:6px;margin-bottom:8px;">
                        <div>
                            <label style="font-size:10px;color:#64748b;">MIN</label>
                            <input type="number" id="prop-number-min" value="0" class="widefat" style="font-size:11px;">
                        </div>
                        <div>
                            <label style="font-size:10px;color:#64748b;">MAX</label>
                            <input type="number" id="prop-number-max" value="100" class="widefat" style="font-size:11px;">
                        </div>
                        <div>
                            <label style="font-size:10px;color:#64748b;">STEP</label>
                            <input type="number" id="prop-number-step" value="1" class="widefat" style="font-size:11px;">
                        </div>
                    </div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">FIELD NAME / TOKEN</label>
                    <input type="text" id="prop-number-fieldname" value="quantity" class="widefat" style="font-size:12px;">
                </div>

                <!-- 9. SELECT / DROPDOWN -->
                <div class="element-panel" id="panel-elem-select" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">DROPDOWN OPTIONS (COMMA SEPARATED)</label>
                    <textarea id="prop-select-options" rows="3" class="widefat" placeholder="Option 1, Option 2, Option 3" style="font-size:12px;margin-bottom:8px;"></textarea>
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">FIELD NAME / TOKEN</label>
                    <input type="text" id="prop-select-fieldname" value="dropdown_field" class="widefat" style="font-size:12px;">
                </div>

                <!-- 10. RADIOS -->
                <div class="element-panel" id="panel-elem-radios" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">RADIO CHOICES (COMMA SEPARATED)</label>
                    <textarea id="prop-radios-options" rows="3" class="widefat" placeholder="Choice A, Choice B, Choice C" style="font-size:12px;margin-bottom:8px;"></textarea>
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">FIELD NAME / TOKEN</label>
                    <input type="text" id="prop-radios-fieldname" value="radio_choice" class="widefat" style="font-size:12px;">
                </div>

                <!-- 11. CHECKBOXES -->
                <div class="element-panel" id="panel-elem-checkboxes" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">CHECKBOX LABEL TEXT</label>
                    <input type="text" id="prop-checkbox-label" class="widefat" value="I agree to the terms and conditions" style="font-size:12px;margin-bottom:8px;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">FIELD NAME / TOKEN</label>
                    <input type="text" id="prop-checkbox-fieldname" value="terms_agreement" class="widefat" style="font-size:12px;margin-bottom:8px;">
                    <label style="font-size:12px;color:#334155;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                        <input type="checkbox" id="prop-checkbox-checked" value="1" checked> Checked by default
                    </label>
                </div>

                <!-- 12. RATING -->
                <div class="element-panel" id="panel-elem-rating" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">DEFAULT STAR RATING (1-5)</label>
                    <input type="number" id="prop-rating-val" min="1" max="5" value="5" class="widefat" style="font-size:12px;margin-bottom:8px;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">STAR COLOR</label>
                    <input type="color" id="prop-rating-color" value="#f59e0b" class="widefat" style="height:32px;margin-bottom:8px;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">FIELD NAME / TOKEN</label>
                    <input type="text" id="prop-rating-fieldname" value="rating" class="widefat" style="font-size:12px;">
                </div>

                <!-- 13. DATE -->
                <div class="element-panel" id="panel-elem-date" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">FIELD NAME / TOKEN</label>
                    <input type="text" id="prop-date-fieldname" value="appointment_date" class="widefat" style="font-size:12px;margin-bottom:8px;">
                    <label style="font-size:12px;color:#334155;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                        <input type="checkbox" id="prop-date-required" value="1"> Required Date
                    </label>
                </div>

                <!-- 14. SLIDER -->
                <div class="element-panel" id="panel-elem-slider" style="display:none;">
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:6px;margin-bottom:8px;">
                        <div>
                            <label style="font-size:10px;color:#64748b;">MIN</label>
                            <input type="number" id="prop-slider-min" value="0" class="widefat" style="font-size:11px;">
                        </div>
                        <div>
                            <label style="font-size:10px;color:#64748b;">MAX</label>
                            <input type="number" id="prop-slider-max" value="100" class="widefat" style="font-size:11px;">
                        </div>
                        <div>
                            <label style="font-size:10px;color:#64748b;">DEFAULT</label>
                            <input type="number" id="prop-slider-val" value="50" class="widefat" style="font-size:11px;">
                        </div>
                    </div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">FIELD NAME / TOKEN</label>
                    <input type="text" id="prop-slider-fieldname" value="range_val" class="widefat" style="font-size:12px;">
                </div>

                <!-- 15. SIGNATURE -->
                <div class="element-panel" id="panel-elem-signature" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">PEN INK COLOR</label>
                    <input type="color" id="prop-sig-color" value="#0f172a" class="widefat" style="height:32px;margin-bottom:8px;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">CLEAR BUTTON LABEL</label>
                    <input type="text" id="prop-sig-clear-label" value="Clear Signature" class="widefat" style="font-size:12px;margin-bottom:8px;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">FIELD NAME</label>
                    <input type="text" id="prop-sig-fieldname" value="digital_signature" class="widefat" style="font-size:12px;">
                </div>

                <!-- 16. WHEEL -->
                <div class="element-panel" id="panel-elem-wheel" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">PRIZE SLICES (COMMA SEPARATED)</label>
                    <textarea id="prop-wheel-slices" rows="4" class="widefat" placeholder="10% OFF, FREE SHIPPING, 25% OFF, JACKPOT" style="font-size:12px;margin-bottom:8px;"></textarea>
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">SPIN BUTTON LABEL</label>
                    <input type="text" id="prop-wheel-btn-text" value="SPIN TO WIN!" class="widefat" style="font-size:12px;margin-bottom:8px;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">WIN MESSAGE</label>
                    <input type="text" id="prop-wheel-win-msg" value="Congratulations! You won {prize}!" class="widefat" style="font-size:12px;">
                </div>

                <!-- 17. SCRATCH -->
                <div class="element-panel" id="panel-elem-scratch" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">SECRET WINNING / REVEAL TEXT</label>
                    <input type="text" id="prop-scratch-prize" value="YOU WON 25% OFF! USE CODE: WIN25" class="widefat" style="font-size:12px;margin-bottom:8px;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">FOIL COVER COLOR</label>
                    <input type="color" id="prop-scratch-foil" value="#94a3b8" class="widefat" style="height:32px;margin-bottom:8px;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">PERCENT UNCOVERED TO REVEAL (10-90%)</label>
                    <input type="number" id="prop-scratch-pct" min="10" max="90" value="45" class="widefat" style="font-size:12px;">
                </div>

                <!-- 18. COUNTDOWN -->
                <div class="element-panel" id="panel-elem-countdown" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">DURATION (SECONDS)</label>
                    <input type="number" id="prop-countdown-seconds" value="900" class="widefat" placeholder="e.g. 900 for 15 minutes" style="font-size:12px;margin-bottom:8px;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">ACTION ON EXPIRE</label>
                    <select id="prop-countdown-expire" class="widefat" style="font-size:12px;">
                        <option value="none">Stay at 00:00</option>
                        <option value="close">Close Popup</option>
                        <option value="redirect">Redirect URL</option>
                    </select>
                </div>

                <!-- 19. PROGRESS -->
                <div class="element-panel" id="panel-elem-progress" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">PROGRESS PERCENTAGE (0-100%)</label>
                    <input type="number" id="prop-progress-val" min="0" max="100" value="65" class="widefat" style="font-size:12px;margin-bottom:8px;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">BAR FILL COLOR</label>
                    <input type="color" id="prop-progress-color" value="#2563eb" class="widefat" style="height:32px;">
                </div>

                <!-- 20. FILE UPLOAD -->
                <div class="element-panel" id="panel-elem-file" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">ALLOWED FILE EXTENSIONS</label>
                    <input type="text" id="prop-file-exts" value=".jpg, .jpeg, .png, .pdf" class="widefat" style="font-size:12px;margin-bottom:8px;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">MAX FILE SIZE (MB)</label>
                    <input type="number" id="prop-file-max-mb" value="5" class="widefat" style="font-size:12px;margin-bottom:8px;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">FIELD NAME</label>
                    <input type="text" id="prop-file-fieldname" value="attachment" class="widefat" style="font-size:12px;">
                </div>

                <!-- 21. SUBMIT BUTTON -->
                <div class="element-panel" id="panel-elem-submit" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">BUTTON LABEL</label>
                    <input type="text" id="prop-submit-label" value="Submit Form" class="widefat" style="font-size:12px;margin-bottom:8px;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">ACTION ON SUBMIT</label>
                    <select id="prop-submit-action" class="widefat" style="font-size:12px;">
                        <option value="default">Save Lead & Close</option>
                        <option value="next_canvas">Save Lead & Advance to Next Canvas</option>
                        <option value="redirect">Save Lead & Redirect URL</option>
                    </select>
                </div>

                <!-- 22. LINK BUTTON -->
                <div class="element-panel" id="panel-elem-link_btn" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">BUTTON LABEL</label>
                    <input type="text" id="prop-link-label" value="Learn More &rarr;" class="widefat" style="font-size:12px;margin-bottom:8px;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">TARGET LINK URL</label>
                    <input type="url" id="prop-link-url" placeholder="https://..." value="https://example.com" class="widefat" style="font-size:12px;margin-bottom:8px;">
                    <label style="font-size:12px;color:#334155;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                        <input type="checkbox" id="prop-link-blank" value="1" checked> Open link in new browser tab
                    </label>
                </div>

                <!-- 23. NEXT CANVAS BUTTON (step_btn) -->
                <div class="element-panel" id="panel-elem-step_btn" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">BUTTON LABEL</label>
                    <input type="text" id="prop-step-label" value="Next Canvas &rarr;" class="widefat" style="font-size:12px;margin-bottom:8px;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">TARGET CANVAS NUMBER</label>
                    <input type="number" id="prop-step-canvas" min="1" max="10" value="2" class="widefat" style="font-size:12px;">
                </div>

                <!-- 24. PAY BUTTON -->
                <div class="element-panel" id="panel-elem-pay" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">BUTTON LABEL</label>
                    <input type="text" id="prop-pay-label" value="Checkout Now" class="widefat" style="font-size:12px;margin-bottom:8px;">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:8px;">
                        <div>
                            <label style="font-size:10px;color:#64748b;">AMOUNT</label>
                            <input type="number" step="0.01" id="prop-pay-amount" value="19.99" class="widefat" style="font-size:12px;">
                        </div>
                        <div>
                            <label style="font-size:10px;color:#64748b;">CURRENCY</label>
                            <select id="prop-pay-currency" class="widefat" style="font-size:12px;">
                                <option value="USD">USD ($)</option>
                                <option value="EUR">EUR (€)</option>
                                <option value="GBP">GBP (£)</option>
                                <option value="AUD">AUD ($)</option>
                                <option value="CAD">CAD ($)</option>
                            </select>
                        </div>
                    </div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">PAYMENT GATEWAY</label>
                    <select id="prop-pay-gateway" class="widefat" style="font-size:12px;">
                        <option value="stripe">Stripe</option>
                        <option value="paypal">PayPal</option>
                    </select>
                </div>

                <!-- 25. CLOSE ICON -->
                <div class="element-panel" id="panel-elem-close_icon" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">ICON GLYPH / STYLE</label>
                    <select id="prop-close-icon-style" class="widefat" style="font-size:12px;margin-bottom:8px;">
                        <option value="times">Classic &times; (Times Cross)</option>
                        <option value="dashicon">Dashicon Dismiss (Square / Circle)</option>
                    </select>
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">CLOSE ACTION BEHAVIOR</label>
                    <select id="prop-close-icon-action" class="widefat" style="font-size:12px;">
                        <option value="close">Close popup immediately</option>
                        <option value="close_period">Close and hide for current session</option>
                        <option value="close_forever">Close and hide permanently</option>
                    </select>
                </div>

                <!-- 26. HTML -->
                <div class="element-panel" id="panel-elem-html" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">RAW HTML / EMBED CODE</label>
                    <textarea id="prop-html-code" rows="5" class="widefat code" placeholder="<div>Custom HTML or &lt;iframe&gt;...</div>" style="font-size:11px;font-family:monospace;"></textarea>
                </div>
            </div>
        </div>

        <!-- TAB: STYLE -->
        <div class="wppoppop-insp-content" id="insp-tab-style" style="display:none;">
            <div style="margin-bottom:14px;">
                <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">TYPOGRAPHY & FONT</label>
                <select id="prop-font-family" class="widefat" style="font-size:12px;margin-bottom:6px;">
                    <option value="inherit">Theme Default Font</option>
                    <option value="Inter">Inter</option>
                    <option value="Roboto">Roboto</option>
                    <option value="Poppins">Poppins</option>
                    <option value="Montserrat">Montserrat</option>
                    <option value="Open Sans">Open Sans</option>
                    <option value="Playfair Display">Playfair Display</option>
                </select>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:6px;">
                    <div>
                        <label style="font-size:10px;color:#64748b;">FONT SIZE (PX)</label>
                        <input type="number" id="prop-font-size" value="14" class="widefat" style="font-size:12px;">
                    </div>
                    <div>
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                            <label style="font-size:10px;color:#64748b;margin:0;">TEXT COLOR</label>
                            <button type="button" id="prop-text-transparent-btn" class="wppoppop-checker-btn" title="Set transparent (color: transparent;)" aria-label="Set transparent text color" style="width:18px;height:18px;border-radius:3px;border:1px solid #cbd5e1;cursor:pointer;padding:0;background-color:#ffffff;background-image:linear-gradient(45deg,#94a3b8 25%,transparent 25%),linear-gradient(-45deg,#94a3b8 25%,transparent 25%),linear-gradient(45deg,transparent 75%,#94a3b8 75%),linear-gradient(-45deg,transparent 75%,#94a3b8 75%);background-size:6px 6px;background-position:0 0,0 3px,3px -3px,-3px 0px;box-shadow:inset 0 0 0 1px rgba(0,0,0,0.05);"></button>
                        </div>
                        <input type="color" id="prop-color" value="#0f172a" class="widefat" style="height:32px;padding:2px;">
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:6px;">
                    <div>
                        <label style="font-size:10px;color:#64748b;">FONT WEIGHT</label>
                        <select id="prop-font-weight" class="widefat" style="font-size:12px;">
                            <option value="400">Normal (400)</option>
                            <option value="600">Semi-Bold (600)</option>
                            <option value="700">Bold (700)</option>
                            <option value="800">Extra Bold (800)</option>
                        </select>
                    </div>
                    <div>
                        <label style="font-size:10px;color:#64748b;">TEXT ALIGN</label>
                        <select id="prop-text-align" class="widefat" style="font-size:12px;">
                            <option value="left">Left</option>
                            <option value="center">Center</option>
                            <option value="right">Right</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label style="font-size:10px;color:#64748b;">PADDING (PX)</label>
                    <input type="number" id="prop-padding" value="0" min="0" max="60" class="widefat" style="font-size:12px;">
                </div>
            </div>

            <div style="margin-bottom:14px;">
                <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">BACKGROUND & BORDERS</label>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:6px;">
                    <div>
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                            <label style="font-size:10px;color:#64748b;margin:0;">BG COLOR</label>
                            <button type="button" id="prop-bg-transparent-btn" class="wppoppop-checker-btn" title="Set transparent (background-color: transparent;)" aria-label="Set transparent background" style="width:18px;height:18px;border-radius:3px;border:1px solid #cbd5e1;cursor:pointer;padding:0;background-color:#ffffff;background-image:linear-gradient(45deg,#94a3b8 25%,transparent 25%),linear-gradient(-45deg,#94a3b8 25%,transparent 25%),linear-gradient(45deg,transparent 75%,#94a3b8 75%),linear-gradient(-45deg,transparent 75%,#94a3b8 75%);background-size:6px 6px;background-position:0 0,0 3px,3px -3px,-3px 0px;box-shadow:inset 0 0 0 1px rgba(0,0,0,0.05);"></button>
                        </div>
                        <input type="color" id="prop-bg-color" value="#ffffff" class="widefat" style="height:32px;padding:2px;">
                    </div>
                    <div>
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                            <label style="font-size:10px;color:#64748b;margin:0;">BORDER COLOR</label>
                            <button type="button" id="prop-border-transparent-btn" class="wppoppop-checker-btn" title="Set transparent (border-color: transparent;)" aria-label="Set transparent border" style="width:18px;height:18px;border-radius:3px;border:1px solid #cbd5e1;cursor:pointer;padding:0;background-color:#ffffff;background-image:linear-gradient(45deg,#94a3b8 25%,transparent 25%),linear-gradient(-45deg,#94a3b8 25%,transparent 25%),linear-gradient(45deg,transparent 75%,#94a3b8 75%),linear-gradient(-45deg,transparent 75%,#94a3b8 75%);background-size:6px 6px;background-position:0 0,0 3px,3px -3px,-3px 0px;box-shadow:inset 0 0 0 1px rgba(0,0,0,0.05);"></button>
                        </div>
                        <input type="color" id="prop-border-color" value="#cbd5e1" class="widefat" style="height:32px;padding:2px;">
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:6px;">
                    <div>
                        <label style="font-size:10px;color:#64748b;">BORDER STYLE</label>
                        <select id="prop-border-style" class="widefat" style="font-size:12px;">
                            <option value="solid">Solid</option>
                            <option value="dashed">Dashed</option>
                            <option value="dotted">Dotted</option>
                            <option value="double">Double</option>
                            <option value="none">None</option>
                        </select>
                    </div>
                    <div>
                        <label style="font-size:10px;color:#64748b;">RADIUS (PX)</label>
                        <input type="number" id="prop-border-radius" value="4" class="widefat" style="font-size:12px;">
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:6px;">
                    <div>
                        <label style="font-size:10px;color:#64748b;">BORDER WIDTH (PX)</label>
                        <input type="number" id="prop-border-width" value="1" min="0" max="10" class="widefat" style="font-size:12px;">
                    </div>
                    <div>
                        <label style="font-size:10px;color:#64748b;">OPACITY</label>
                        <input type="number" id="prop-opacity" step="0.1" min="0.1" max="1" value="1" class="widefat" style="font-size:12px;">
                    </div>
                </div>
            </div>

            <div style="margin-bottom:12px;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                    <div style="display:flex;align-items:center;gap:6px;">
                        <label style="font-size:11px;font-weight:700;color:#475569;margin:0;">ENTRANCE ANIMATION</label>
                        <button type="button" id="prop-anim-replay-btn" title="Replay Animation on Canvas" style="background:#2563eb;color:#fff;border:none;border-radius:3px;font-size:9px;font-weight:700;padding:1px 5px;cursor:pointer;line-height:1.4;">&#9654; Play</button>
                    </div>
                    <a href="https://animate.style/" target="_blank" rel="noopener" style="font-size:10px;font-weight:700;color:#2563eb;text-decoration:none;">Animate.css &rarr;</a>
                </div>
                <select id="prop-anim-effect" class="widefat" style="font-size:12px;margin-bottom:8px;">
                    <option value="none">None</option>
                    <optgroup label="Attention Seekers">
                        <option value="bounce">bounce</option>
                        <option value="flash">flash</option>
                        <option value="pulse">pulse</option>
                        <option value="rubberBand">rubberBand</option>
                        <option value="shakeX">shakeX</option>
                        <option value="shakeY">shakeY</option>
                        <option value="tada">tada</option>
                        <option value="wobble">wobble</option>
                        <option value="heartBeat">heartBeat</option>
                    </optgroup>
                    <optgroup label="Fading Entrances">
                        <option value="fadeIn">fadeIn</option>
                        <option value="fadeInDown">fadeInDown</option>
                        <option value="fadeInLeft">fadeInLeft</option>
                        <option value="fadeInRight">fadeInRight</option>
                        <option value="fadeInUp">fadeInUp</option>
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
                </select>

                <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">BOX SHADOW</label>
                <select id="prop-box-shadow" class="widefat" style="font-size:12px;">
                    <option value="none">None</option>
                    <option value="0 1px 3px rgba(0,0,0,0.1)">Subtle Shadow</option>
                    <option value="0 4px 6px -1px rgba(0,0,0,0.15)">Medium Shadow</option>
                    <option value="0 10px 15px -3px rgba(0,0,0,0.2)">Prominent Shadow</option>
                </select>
            </div>
        </div>

        <!-- TAB: LOGIC -->
        <div class="wppoppop-insp-content" id="insp-tab-logic" style="display:none;">
            <div style="margin-bottom:14px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;padding:10px;">
                <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:2px;">MERGE TAG / TOKEN</label>
                <p style="font-size:11px;color:#64748b;margin:0 0 6px 0;">Use in other text layers to display this value in real-time:</p>
                <input type="text" id="prop-display-token" readonly class="widefat code" style="font-family:monospace;font-size:12px;background:#ffffff;">
            </div>

            <div style="margin-bottom:14px;">
                <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">ONCLICK URL REDIRECT ACTION</label>
                <input type="url" id="prop-action-url" placeholder="https://..." class="widefat" style="font-size:12px;margin-bottom:6px;">
                <label style="font-size:12px;color:#334155;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                    <input type="checkbox" id="prop-action-blank" value="1"> Open redirect in new browser tab
                </label>
            </div>

            <div style="margin-bottom:14px;">
                <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">POPUP CLOSE BEHAVIOR</label>
                <select id="prop-action-close" class="widefat" style="font-size:12px;">
                    <option value="none">Do not close popup</option>
                    <option value="close">Close popup immediately</option>
                    <option value="close_period">Close and hide for current session</option>
                    <option value="close_forever">Close and hide permanently</option>
                </select>
            </div>

            <div style="margin-bottom:12px;">
                <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">CUSTOM JS CALLBACK</label>
                <textarea id="prop-action-js" rows="3" placeholder="console.log('Layer clicked');" class="widefat code" style="font-size:11px;font-family:monospace;"></textarea>
            </div>
        </div>
    </div>
</div>
