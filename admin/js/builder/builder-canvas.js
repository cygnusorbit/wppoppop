/**
 * WpPopPop Visual Builder: Canvas Stage Engine
 * Direct-Pointer Resizing, Live Preview & Full 26-Element Stage Rendering
 */
(function($) {
    'use strict';

    window.WpPopPopBuilderCanvas = {
        isPreview: false,
        audioCtx: null,

        init: function() {
            this.bindCanvasCornerResize();
            this.bindRibbonTools();
            this.bindCanvasSelection();
            this.bindPreviewModeControls();
        },

        bindCanvasCornerResize: function() {
            var self = this;
            var $box = $('#wppoppop-canvas-box');
            var handleEl = document.getElementById('wppoppop-canvas-corner-handle');
            if (!handleEl) return;

            var onPointerDown = function(e) {
                if (self.isPreview) return;
                e.preventDefault();
                e.stopPropagation();

                var pointerId = e.pointerId;
                if (handleEl.setPointerCapture && pointerId !== undefined) {
                    try { handleEl.setPointerCapture(pointerId); } catch(err) {}
                }

                var startX = e.clientX;
                var startY = e.clientY;
                var startW = $box.outerWidth();
                var startH = $box.outerHeight();
                var core = window.WpPopPopBuilderCore;
                var cur = core ? (core.state.currentCanvas || 1) : 1;

                $('body').addClass('wppoppop-resizing-canvas');
                self.updateSizeBadge(startW, startH);

                var onPointerMove = function(moveEvent) {
                    moveEvent.preventDefault();
                    var dx = moveEvent.clientX - startX;
                    var dy = moveEvent.clientY - startY;

                    var newW = Math.max(200, Math.min(1600, Math.round(startW + dx)));
                    var newH = Math.max(150, Math.min(1200, Math.round(startH + dy)));

                    $box.css({ width: newW + 'px', height: newH + 'px' });
                    self.updateSizeBadge(newW, newH);

                    if (core && core.state.canvasMeta && core.state.canvasMeta[cur]) {
                        core.state.canvasMeta[cur].width = newW;
                        core.state.canvasMeta[cur].height = newH;
                    }

                    $('#set-canvas-width, #quick-box-width').val(newW);
                    $('#set-canvas-height, #quick-box-height').val(newH);
                };

                var onPointerUp = function(upEvent) {
                    if (handleEl.releasePointerCapture && pointerId !== undefined) {
                        try { handleEl.releasePointerCapture(pointerId); } catch(err) {}
                    }

                    window.removeEventListener('pointermove', onPointerMove);
                    window.removeEventListener('pointerup', onPointerUp);
                    window.removeEventListener('pointercancel', onPointerUp);

                    window.removeEventListener('mousemove', onPointerMove);
                    window.removeEventListener('mouseup', onPointerUp);

                    $('body').removeClass('wppoppop-resizing-canvas');
                    $('#wppoppop-canvas-size-badge').fadeOut(200);

                    if (core) {
                        core.pushHistory();
                    }
                };

                window.addEventListener('pointermove', onPointerMove, { passive: false });
                window.addEventListener('pointerup', onPointerUp);
                window.addEventListener('pointercancel', onPointerUp);

                window.addEventListener('mousemove', onPointerMove, { passive: false });
                window.addEventListener('mouseup', onPointerUp);
            };

            handleEl.removeEventListener('pointerdown', onPointerDown);
            handleEl.addEventListener('pointerdown', onPointerDown);
            handleEl.removeEventListener('mousedown', onPointerDown);
            handleEl.addEventListener('mousedown', onPointerDown);
        },

        updateSizeBadge: function(w, h) {
            $('#wppoppop-canvas-size-badge').text(w + ' × ' + h + ' px').show();
        },

        getActiveElements: function() {
            var core = window.WpPopPopBuilderCore;
            if (!core || !core.state.canvases) return [];
            var cur = core.state.currentCanvas || 1;
            if (!core.state.canvases[cur]) {
                core.state.canvases[cur] = [];
            }
            return core.state.canvases[cur];
        },

        renderCanvas: function() {
            var self = this;
            var $root = $('#wppoppop-canvas-elements-root');
            $root.empty();

            var elements = this.getActiveElements();
            elements.forEach(function(el) {
                var $node = self.buildElementNode(el);
                $root.append($node);
                self.attachInteractions($node, el);
            });

            if (this.isPreview) {
                this.initActivePreviewWidgets();
            }
        },

        buildElementNode: function(el) {
            var $div = $('<div>')
                .addClass('wppoppop-canvas-item')
                .attr('id', 'el-' + el.id)
                .attr('data-id', el.id)
                .css({
                    top: (el.top || 20) + 'px',
                    left: (el.left || 20) + 'px',
                    width: (el.width || 180) + 'px',
                    height: (el.height || 42) + 'px',
                    zIndex: el.zIndex || 10,
                    borderRadius: (el.borderRadius || 4) + 'px',
                    opacity: el.opacity !== undefined ? el.opacity : 1
                });

            if (el.fontFamily && el.fontFamily !== 'inherit') $div.css('fontFamily', el.fontFamily);
            if (el.fontSize) $div.css('fontSize', el.fontSize + 'px');
            if (el.fontWeight) $div.css('fontWeight', el.fontWeight);
            if (el.textAlign) $div.css('textAlign', el.textAlign);
            if (el.padding) $div.css('padding', el.padding + 'px');
            if (el.color) $div.css('color', el.color);
            if (el.bgColor) $div.css('backgroundColor', el.bgColor);

            // Shape element geometry is handled via internal SVG strokes
            if (el.type !== 'shape') {
                var bStyle = el.borderStyle || 'solid';
                if (el.borderWidth !== undefined && el.borderWidth !== null) {
                    var bw = parseInt(el.borderWidth, 10);
                    if (bw > 0 || bStyle !== 'none') {
                        $div.css({
                            borderStyle: bStyle,
                            borderWidth: bw + 'px',
                            borderColor: el.borderColor || '#cbd5e1'
                        });
                    }
                } else if (el.borderColor === 'transparent') {
                    $div.css('borderColor', 'transparent');
                }
            }

            if (el.boxShadow && el.boxShadow !== 'none') $div.css('boxShadow', el.boxShadow);

            var innerHtml = this.getInnerMarkup(el);
            $div.html(innerHtml);

            var activeId = window.WpPopPopBuilderCore ? window.WpPopPopBuilderCore.state.activeId : null;
            if (!this.isPreview && activeId !== null && String(activeId) === String(el.id)) {
                $div.addClass('wppoppop-selected');
            }

            return $div;
        },

        getInnerMarkup: function(el) {
            var label = el.content || el.name || 'Element';
            var bgStyle = el.bgColor === 'transparent' ? 'background:transparent;' : (el.bgColor ? 'background:' + el.bgColor + ';' : '');
            var textStyle = el.color ? 'color:' + el.color + ';' : '';
            var alignStyle = el.textAlign ? 'text-align:' + el.textAlign + ';' : '';
            var weightStyle = el.fontWeight ? 'font-weight:' + el.fontWeight + ';' : '';
            var justifyVal = el.textAlign === 'center' ? 'center' : (el.textAlign === 'right' ? 'flex-end' : 'flex-start');
            var type = (el.type || 'text').toString().toLowerCase().trim();

            switch (type) {
                case 'title':
                    var titleTag = el.htmlTag || 'h2';
                    return '<' + titleTag + ' style="width:100%;height:100%;display:flex;align-items:center;justify-content:' + justifyVal + ';margin:0;padding:0 8px;' + textStyle + alignStyle + weightStyle + '">' + (el.content || 'Catchy Campaign Title') + '</' + titleTag + '>';

                case 'text':
                    var tag = el.htmlTag || 'p';
                    return '<' + tag + ' style="width:100%;height:100%;display:flex;align-items:center;justify-content:' + justifyVal + ';margin:0;padding:0 8px;' + textStyle + alignStyle + weightStyle + '">' + (el.content || 'Headline or Text') + '</' + tag + '>';

                case 'image':
                    var imgUrl = el.imageUrl || el.content || '';
                    var imgAlt = el.imageAlt || '';
                    var imgFit = el.imageFit || 'cover';
                    if (!imgUrl) {
                        return '<div style="width:100%;height:100%;background:#f1f5f9;border:1px dashed #cbd5e1;border-radius:inherit;display:flex;flex-direction:column;align-items:center;justify-content:center;color:#94a3b8;font-size:11px;font-weight:600;pointer-events:none;padding:4px;"><span class="dashicons dashicons-format-image" style="font-size:24px;width:24px;height:24px;margin-bottom:4px;display:inline-block;line-height:1;"></span><span>No Image Selected</span></div>';
                    }
                    return '<img src="' + imgUrl + '" alt="' + imgAlt + '" style="width:100%;height:100%;object-fit:' + imgFit + ';display:block;border-radius:inherit;pointer-events:none;">';

                case 'video':
                    var rawUrl = (el.videoUrl || el.content || '').trim();
                    if (!rawUrl) {
                        return '<div style="width:100%;height:100%;background:#1e293b;border-radius:inherit;display:flex;flex-direction:column;align-items:center;justify-content:center;color:#ffffff;font-size:11px;font-weight:700;"><span class="dashicons dashicons-video-alt3" style="font-size:32px;width:32px;height:32px;margin-bottom:6px;"></span><span>Video Player Placeholder</span></div>';
                    }

                    var embedUrl = rawUrl;
                    var isYt = rawUrl.indexOf('youtube.com') > -1 || rawUrl.indexOf('youtu.be') > -1;
                    var isVim = rawUrl.indexOf('vimeo.com') > -1;

                    if (isYt) {
                        var ytId = '';
                        var m = rawUrl.match(/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([\w-]{11})/);
                        if (m && m[1]) ytId = m[1];
                        embedUrl = 'https://www.youtube.com/embed/' + (ytId || 'dQw4w9WgXcQ') + (el.videoAutoplay ? '?autoplay=1&mute=1' : '');
                        return '<iframe src="' + embedUrl + '" style="width:100%;height:100%;border:none;border-radius:inherit;display:block;' + (this.isPreview ? 'pointer-events:auto;' : 'pointer-events:none;') + '" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe>';
                    } else if (isVim) {
                        var vimId = rawUrl.split('/').pop().split('?')[0];
                        embedUrl = 'https://player.vimeo.com/video/' + vimId + (el.videoAutoplay ? '?autoplay=1&muted=1' : '');
                        return '<iframe src="' + embedUrl + '" style="width:100%;height:100%;border:none;border-radius:inherit;display:block;' + (this.isPreview ? 'pointer-events:auto;' : 'pointer-events:none;') + '" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
                    } else {
                        // Native HTML5 MP4 Video
                        return '<video src="' + rawUrl + '" ' + (el.videoControls !== false ? 'controls' : '') + ' ' + (el.videoAutoplay ? 'autoplay muted' : '') + ' style="width:100%;height:100%;object-fit:cover;border-radius:inherit;' + (this.isPreview ? 'pointer-events:auto;' : 'pointer-events:none;') + '"></video>';
                    }

                case 'shape':
                    var preset = el.shapePreset || 'circle';
                    var fill = el.shapeFill === 'transparent' ? 'none' : (el.shapeFill || '#3b82f6');
                    var stroke = el.shapeStroke === 'transparent' ? 'none' : (el.shapeStroke || '#1d4ed8');
                    var strokeW = el.shapeStrokeWidth !== undefined ? parseInt(el.shapeStrokeWidth, 10) : 0;
                    var rot = el.shapeRotate !== undefined ? parseInt(el.shapeRotate, 10) : 0;

                    var strokeAttr = stroke !== 'none' && strokeW > 0 
                        ? 'stroke="' + stroke + '" stroke-width="' + strokeW + '" stroke-linejoin="round" vector-effect="non-scaling-stroke"' 
                        : 'stroke="none"';
                    var fillAttr = 'fill="' + fill + '"';

                    var svgContent = '';
                    switch (preset) {
                        case 'circle':
                            svgContent = '<ellipse cx="50" cy="50" rx="46" ry="46" ' + fillAttr + ' ' + strokeAttr + ' />';
                            break;
                        case 'square':
                            svgContent = '<rect x="4" y="4" width="92" height="92" ' + fillAttr + ' ' + strokeAttr + ' />';
                            break;
                        case 'rounded_square':
                            svgContent = '<rect x="4" y="4" width="92" height="92" rx="16" ry="16" ' + fillAttr + ' ' + strokeAttr + ' />';
                            break;
                        case 'star':
                            svgContent = '<polygon points="50,4 64,34 97,36 71,58 80,90 50,71 20,90 29,58 3,36 36,34" ' + fillAttr + ' ' + strokeAttr + ' />';
                            break;
                        case 'triangle':
                            svgContent = '<polygon points="50,6 94,92 6,92" ' + fillAttr + ' ' + strokeAttr + ' />';
                            break;
                        case 'diamond':
                            svgContent = '<polygon points="50,5 95,50 50,95 5,50" ' + fillAttr + ' ' + strokeAttr + ' />';
                            break;
                        case 'heart':
                            svgContent = '<path d="M 50,32 C 50,32 44,14 26,14 C 11,14 4,28 4,44 C 4,68 40,88 50,94 C 60,88 96,68 96,44 C 96,28 89,14 74,14 C 56,14 50,32 50,32 Z" ' + fillAttr + ' ' + strokeAttr + ' />';
                            break;
                        case 'hexagon':
                            svgContent = '<polygon points="50,4 92,26 92,74 50,96 8,74 8,26" ' + fillAttr + ' ' + strokeAttr + ' />';
                            break;
                        case 'octagon':
                            svgContent = '<polygon points="30,4 70,4 96,30 96,70 70,96 30,96 4,70 4,30" ' + fillAttr + ' ' + strokeAttr + ' />';
                            break;
                        case 'shield':
                            svgContent = '<path d="M 50,4 L 92,18 L 92,54 C 92,78 50,96 50,96 C 50,96 8,78 8,54 L 8,18 Z" ' + fillAttr + ' ' + strokeAttr + ' />';
                            break;
                        case 'cross':
                            svgContent = '<polygon points="35,4 65,4 65,35 96,35 96,65 65,65 65,96 35,96 35,65 4,65 4,35 35,35" ' + fillAttr + ' ' + strokeAttr + ' />';
                            break;
                        default:
                            svgContent = '<rect x="4" y="4" width="92" height="92" ' + fillAttr + ' ' + strokeAttr + ' />';
                            break;
                    }

                    return '<svg viewBox="0 0 100 100" preserveAspectRatio="none" style="width:100%;height:100%;display:block;transform:rotate(' + rot + 'deg);pointer-events:none;overflow:visible;">' + svgContent + '</svg>';

                case 'textfield':
                    return '<input type="text" placeholder="' + (el.content || 'Enter text here...') + '" ' + (el.required ? 'required' : '') + ' style="width:100%;height:100%;padding:0 10px;border:1px solid #cbd5e1;border-radius:inherit;' + bgStyle + textStyle + alignStyle + weightStyle + '">';

                case 'email':
                    return '<input type="email" placeholder="' + (el.content || 'Enter your email...') + '" ' + (el.required ? 'required' : '') + ' style="width:100%;height:100%;padding:0 10px;border:1px solid #cbd5e1;border-radius:inherit;' + bgStyle + textStyle + alignStyle + weightStyle + '">';

                case 'number':
                    return '<input type="number" placeholder="' + (el.content || 'Enter number...') + '" min="' + (el.min || 0) + '" max="' + (el.max || 100) + '" step="' + (el.step || 1) + '" style="width:100%;height:100%;padding:0 10px;border:1px solid #cbd5e1;border-radius:inherit;' + bgStyle + textStyle + alignStyle + weightStyle + '">';

                case 'select':
                    var opts = (el.content ? el.content.split(',') : ['Option 1', 'Option 2', 'Option 3']);
                    var selectHtml = '<select style="width:100%;height:100%;padding:0 10px;border:1px solid #cbd5e1;border-radius:inherit;' + bgStyle + textStyle + alignStyle + weightStyle + '">';
                    opts.forEach(function(o) { selectHtml += '<option>' + o.trim() + '</option>'; });
                    selectHtml += '</select>';
                    return selectHtml;

                case 'radios':
                    var rOpts = (el.content ? el.content.split(',') : ['Choice A', 'Choice B']);
                    var radiosHtml = '<div style="display:flex;gap:12px;align-items:center;justify-content:' + justifyVal + ';height:100%;padding:0 8px;font-size:12px;' + textStyle + weightStyle + '">';
                    rOpts.forEach(function(r, idx) {
                        radiosHtml += '<label style="cursor:pointer;"><input type="radio" name="preview_radio_' + el.id + '" value="' + r.trim() + '" ' + (idx === 0 ? 'checked' : '') + '> ' + r.trim() + '</label>';
                    });
                    radiosHtml += '</div>';
                    return radiosHtml;

                case 'checkboxes':
                    return '<div style="display:flex;align-items:center;justify-content:' + justifyVal + ';gap:6px;height:100%;padding:0 8px;font-size:12px;' + textStyle + weightStyle + '"><label style="cursor:pointer;"><input type="checkbox" ' + (el.checked !== false ? 'checked' : '') + '> <span>' + (el.content || 'I agree to the terms') + '</span></label></div>';

                case 'rating':
                    var starCount = parseInt(el.content, 10) || 5;
                    var starColor = el.ratingColor || '#f59e0b';
                    var starsHtml = '<div class="wppoppop-field-rating" style="display:flex;gap:4px;align-items:center;justify-content:' + justifyVal + ';height:100%;color:' + starColor + ';font-size:20px;user-select:none;">';
                    for (var s = 1; s <= 5; s++) {
                        starsHtml += '<span class="wppoppop-preview-star" data-val="' + s + '" style="cursor:pointer;transition:transform 0.1s ease;' + (s <= starCount ? '' : 'color:#cbd5e1;') + '">★</span>';
                    }
                    starsHtml += '</div>';
                    return starsHtml;

                case 'date':
                    return '<input type="date" value="' + (el.content || '2026-10-08') + '" style="width:100%;height:100%;padding:0 10px;border:1px solid #cbd5e1;border-radius:inherit;' + bgStyle + textStyle + alignStyle + weightStyle + '">';

                case 'slider':
                    return '<div style="padding:0 10px;height:100%;display:flex;align-items:center;"><input type="range" min="' + (el.min || 0) + '" max="' + (el.max || 100) + '" value="' + (el.content || 50) + '" style="width:100%;"></div>';

                case 'signature':
                    return '<div style="width:100%;height:100%;position:relative;background:#ffffff;border-radius:inherit;"><canvas class="wppoppop-preview-sig-canvas" width="' + (el.width || 200) + '" height="' + (el.height || 80) + '" style="width:100%;height:100%;border:1px dashed #94a3b8;border-radius:inherit;touch-action:none;cursor:crosshair;"></canvas><button type="button" class="wppoppop-preview-sig-clear button" style="position:absolute;bottom:4px;right:4px;font-size:9px;padding:1px 6px;height:20px;background:#e2e8f0;border:none;cursor:pointer;">' + (el.clearLabel || 'Clear') + '</button></div>';

                case 'wheel':
                    return '<div class="wppoppop-preview-wheel-wrap" style="width:100%;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;position:relative;"><canvas class="wppoppop-preview-wheel-canvas" width="160" height="160" style="border-radius:50%;box-shadow:0 4px 12px rgba(0,0,0,0.2);"></canvas><button type="button" class="button wppoppop-preview-wheel-btn" style="margin-top:6px;background:#4338ca;color:#fff;border:none;font-weight:700;font-size:11px;padding:3px 10px;border-radius:4px;cursor:pointer;">' + (el.btnText || 'SPIN TO WIN!') + '</button></div>';

                case 'scratch':
                    return '<div class="wppoppop-preview-scratch-wrap" style="width:100%;height:100%;position:relative;overflow:hidden;border-radius:inherit;user-select:none;"><div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:#fef08a;color:#854d0e;font-weight:700;font-size:13px;padding:8px;text-align:center;box-sizing:border-box;">' + (el.content || 'YOU WON 25% OFF!') + '</div><canvas class="wppoppop-preview-scratch-canvas" width="' + (el.width || 200) + '" height="' + (el.height || 60) + '" style="position:absolute;inset:0;width:100%;height:100%;touch-action:none;cursor:crosshair;"></canvas></div>';

                case 'countdown':
                    var secs = parseInt(el.countdownSeconds, 10) || 900;
                    var mins = Math.floor(secs / 60);
                    var remSecs = secs % 60;
                    var timeStr = (mins < 10 ? '0' : '') + mins + ' : ' + (remSecs < 10 ? '0' : '') + remSecs;
                    return '<div class="wppoppop-preview-countdown" data-secs="' + secs + '" style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-family:monospace;font-weight:700;font-size:16px;background:#1e293b;color:#f8fafc;border-radius:inherit;"><span class="cd-display">' + timeStr + '</span></div>';

                case 'progress':
                    var pct = parseInt(el.content, 10) || 65;
                    var barColor = el.progressColor || '#2563eb';
                    return '<div style="width:100%;height:100%;background:#e2e8f0;border-radius:inherit;overflow:hidden;position:relative;"><div style="width:' + pct + '%;height:100%;background:' + barColor + ';transition:width 0.3s ease;"></div></div>';

                case 'file':
                    return '<div style="width:100%;height:100%;border:1px dashed #cbd5e1;display:flex;align-items:center;justify-content:center;font-size:11px;color:#64748b;border-radius:inherit;padding:4px;"><input type="file" style="width:100%;font-size:11px;"></div>';

                case 'step_btn':
                    var targetCanvas = el.goto_canvas || el.goto_screen || 2;
                    var stepBtnBg = el.bgColor === 'transparent' ? 'background:transparent;border:1px dashed #2563eb;color:#2563eb;' : (el.bgColor ? 'background:' + el.bgColor + ';' : 'background:#2563eb;color:#fff;border:none;');
                    return '<button type="button" class="wppoppop-preview-step-btn" data-goto-canvas="' + targetCanvas + '" style="width:100%;height:100%;' + stepBtnBg + (el.color ? 'color:' + el.color + ';' : '') + weightStyle + 'border-radius:inherit;cursor:pointer;">' + (el.content || ('Canvas ' + targetCanvas + ' &rarr;')) + '</button>';

                case 'submit':
                    var submitBtnBg = el.bgColor === 'transparent' ? 'background:transparent;border:1px dashed #c2185b;color:#c2185b;' : (el.bgColor ? 'background:' + el.bgColor + ';' : 'background:#c2185b;color:#fff;border:none;');
                    return '<button type="button" class="wppoppop-preview-submit-btn" style="width:100%;height:100%;' + submitBtnBg + (el.color ? 'color:' + el.color + ';' : '') + weightStyle + 'border-radius:inherit;cursor:pointer;font-weight:700;">' + (el.content || 'Submit Form') + '</button>';

                case 'link_btn':
                    var linkBtnBg = el.bgColor === 'transparent' ? 'background:transparent;border:1px dashed #2563eb;color:#2563eb;' : (el.bgColor ? 'background:' + el.bgColor + ';' : 'background:#2563eb;color:#fff;border:none;');
                    return '<button type="button" class="wppoppop-preview-link-btn" data-url="' + (el.linkUrl || '#') + '" data-blank="' + (el.linkBlank ? '1' : '0') + '" style="width:100%;height:100%;' + linkBtnBg + (el.color ? 'color:' + el.color + ';' : '') + weightStyle + 'border-radius:inherit;cursor:pointer;font-weight:700;">' + (el.content || 'Learn More &rarr;') + '</button>';

                case 'pay':
                    var cur = el.payCurrency || 'USD';
                    var amt = el.payAmount !== undefined ? el.payAmount : 19.99;
                    var payBtnBg = el.bgColor === 'transparent' ? 'background:transparent;border:1px dashed #059669;color:#059669;' : (el.bgColor ? 'background:' + el.bgColor + ';' : 'background:#059669;color:#fff;border:none;');
                    return '<button type="button" class="wppoppop-preview-pay-btn" style="width:100%;height:100%;' + payBtnBg + (el.color ? 'color:' + el.color + ';' : '') + weightStyle + 'border-radius:inherit;cursor:pointer;font-weight:700;">' + (el.content || ('Checkout Now (' + cur + ' ' + amt + ')')) + '</button>';

                case 'close_icon':
                    var iconGlyph = el.closeIconStyle === 'dashicon' ? '<span class="dashicons dashicons-no-alt" style="font-size:inherit;width:auto;height:auto;line-height:1;"></span>' : '&times;';
                    return '<button type="button" class="wppoppop-preview-close-btn" data-close-action="' + (el.closeAction || 'close') + '" style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:transparent;border:none;color:inherit;font-size:inherit;font-weight:700;line-height:1;cursor:pointer;padding:0;">' + iconGlyph + '</button>';

                case 'html':
                    return '<div style="width:100%;height:100%;overflow:hidden;padding:4px;font-size:11px;border:1px solid #cbd5e1;border-radius:inherit;' + bgStyle + textStyle + alignStyle + weightStyle + '">' + (el.content || '<strong>Custom HTML</strong>') + '</div>';

                default:
                    return '<div style="padding:6px;font-size:12px;' + textStyle + alignStyle + weightStyle + '">' + label + '</div>';
            }
        },

        playAnimation: function(id, effect) {
            if (!effect || effect === 'none') return;
            var $node = $('#el-' + id);
            if (!$node.length) return;

            $node.removeClass(function(index, className) {
                return (className.match(/animate__\S+/g) || []).join(' ') + ' ' + (className.match(/anim-\S+/g) || []).join(' ');
            });

            void $node[0].offsetWidth;
            $node.addClass('animate__animated animate__' + effect + ' anim-' + effect);
        },

        attachInteractions: function($node, el) {
            var self = this;
            if (this.isPreview) return;

            if (el.locked) {
                $node.addClass('wppoppop-locked');
                return;
            }

            $node.draggable({
                containment: '#wppoppop-canvas-box',
                grid: [10, 10],
                stop: function(event, ui) {
                    el.top = ui.position.top;
                    el.left = ui.position.left;
                    if (window.WpPopPopBuilderInspector) {
                        window.WpPopPopBuilderInspector.syncCoordinates(el);
                    }
                    if (window.WpPopPopBuilderCore) {
                        window.WpPopPopBuilderCore.pushHistory();
                    }
                }
            });

            $node.resizable({
                containment: '#wppoppop-canvas-box',
                grid: [10, 10],
                handles: 'se',
                stop: function(event, ui) {
                    el.width = ui.size.width;
                    el.height = ui.size.height;
                    if (window.WpPopPopBuilderInspector) {
                        window.WpPopPopBuilderInspector.syncCoordinates(el);
                    }
                    if (window.WpPopPopBuilderCore) {
                        window.WpPopPopBuilderCore.pushHistory();
                    }
                }
            });
        },

        bindRibbonTools: function() {
            var self = this;
            $('.wppoppop-ribbon-tool').on('click', function(e) {
                e.preventDefault();
                if (self.isPreview) return;
                var type = $(this).data('type');
                self.addElement(type);
            });
        },

        addElement: function(type) {
            var elements = this.getActiveElements();
            var id = 'layer_' + Date.now().toString(36) + '_' + Math.random().toString(36).substr(2, 4);
            var nextZ = elements.length ? Math.max.apply(null, elements.map(function(e) { return e.zIndex || 10; })) + 1 : 10;

            var defaultWidth = 200;
            var defaultHeight = 42;
            var defaultContent = '';
            var defaultBorderRadius = 4;
            var defaultBorderWidth = 1;
            var defaultBg = '#ffffff';
            var defaultColor = '#0f172a';
            var defaultFontSize = 14;
            var defaultFontWeight = '400';
            var defaultTextAlign = 'left';

            switch (type) {
                case 'title':
                    defaultWidth = 280;
                    defaultHeight = 48;
                    defaultFontSize = 24;
                    defaultFontWeight = '700';
                    defaultTextAlign = 'center';
                    defaultContent = 'Catchy Campaign Title';
                    break;
                case 'text':
                    defaultWidth = 240;
                    defaultHeight = 60;
                    defaultContent = 'Add your description or subheadline text here.';
                    break;
                case 'image':
                    defaultWidth = 200;
                    defaultHeight = 140;
                    defaultBorderRadius = 0;
                    defaultBorderWidth = 0;
                    break;
                case 'video':
                    defaultWidth = 320;
                    defaultHeight = 180;
                    defaultBorderRadius = 6;
                    defaultBorderWidth = 0;
                    defaultContent = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';
                    break;
                case 'shape':
                    defaultWidth = 160;
                    defaultHeight = 160;
                    defaultBorderRadius = 0;
                    defaultBorderWidth = 0;
                    break;
                case 'textfield':
                    defaultWidth = 220;
                    defaultHeight = 42;
                    defaultContent = 'Enter text here...';
                    break;
                case 'close_icon':
                    defaultWidth = 34;
                    defaultHeight = 34;
                    defaultBorderRadius = 17;
                    defaultBorderWidth = 0;
                    defaultBg = 'transparent';
                    defaultColor = '#64748b';
                    defaultFontSize = 22;
                    defaultContent = '×';
                    break;
                case 'submit':
                    defaultWidth = 200;
                    defaultHeight = 44;
                    defaultBg = '#c2185b';
                    defaultColor = '#ffffff';
                    defaultBorderRadius = 6;
                    defaultFontWeight = '700';
                    defaultContent = 'Submit Form';
                    break;
                case 'link_btn':
                    defaultWidth = 200;
                    defaultHeight = 44;
                    defaultBg = '#2563eb';
                    defaultColor = '#ffffff';
                    defaultBorderRadius = 6;
                    defaultFontWeight = '700';
                    defaultContent = 'Learn More &rarr;';
                    break;
                case 'html':
                    defaultWidth = 240;
                    defaultHeight = 70;
                    break;
                case 'signature':
                case 'wheel':
                    defaultHeight = 120;
                    break;
            }

            var newEl = {
                id: id,
                type: type,
                name: type.toUpperCase() + ' ' + (elements.length + 1),
                top: 50 + (elements.length * 15) % 150,
                left: 50 + (elements.length * 15) % 200,
                width: defaultWidth,
                height: defaultHeight,
                zIndex: nextZ,
                borderRadius: defaultBorderRadius,
                borderStyle: 'solid',
                borderWidth: defaultBorderWidth,
                bgColor: defaultBg,
                color: defaultColor,
                fontSize: defaultFontSize,
                fontWeight: defaultFontWeight,
                textAlign: defaultTextAlign,
                padding: 0,
                opacity: 1,
                content: defaultContent,
                imageUrl: '',
                imageAlt: '',
                imageFit: 'cover',
                videoUrl: type === 'video' ? 'https://www.youtube.com/watch?v=dQw4w9WgXcQ' : '',
                videoAutoplay: false,
                videoControls: true,
                shapePreset: 'circle',
                shapeFill: '#3b82f6',
                shapeStroke: '#1d4ed8',
                shapeStrokeWidth: 2,
                shapeRotate: 0,
                linkUrl: 'https://example.com',
                linkBlank: true,
                closeIconStyle: 'times',
                closeAction: 'close',
                submitAction: 'default',
                goto_canvas: 2,
                goto_screen: 2,
                locked: false,
                hidden: false
            };

            elements.push(newEl);
            if (window.WpPopPopBuilderCore) {
                window.WpPopPopBuilderCore.state.activeId = id;
            }

            this.renderCanvas();

            if (window.WpPopPopBuilderLayers) {
                window.WpPopPopBuilderLayers.renderLayers();
            }
            if (window.WpPopPopBuilderInspector) {
                window.WpPopPopBuilderInspector.open(id);
            }
            if (window.WpPopPopBuilderCore) {
                window.WpPopPopBuilderCore.pushHistory();
            }
        },

        bindCanvasSelection: function() {
            var self = this;
            $(document).on('click', '.wppoppop-canvas-item', function(e) {
                if (self.isPreview) return;
                e.stopPropagation();
                var id = $(this).data('id');
                self.selectElement(id);
            });

            $('#wppoppop-canvas-box, .wppoppop-builder-workspace').on('click', function(e) {
                if (self.isPreview) return;
                if ($(e.target).closest('.wppoppop-canvas-item, #wppoppop-inspector-drawer, #wppoppop-floating-layers-panel, #wppoppop-canvas-corner-handle, .wppoppop-ribbon-bar, .wppoppop-builder-header').length === 0) {
                    self.deselect();
                }
            });
        },

        selectElement: function(id) {
            if (window.WpPopPopBuilderCore) {
                window.WpPopPopBuilderCore.state.activeId = id;
            }
            $('.wppoppop-canvas-item').removeClass('wppoppop-selected');
            $('#el-' + id).addClass('wppoppop-selected');

            if (window.WpPopPopBuilderLayers) {
                window.WpPopPopBuilderLayers.highlightLayer(id);
            }
            if (window.WpPopPopBuilderInspector) {
                window.WpPopPopBuilderInspector.open(id);
            }
        },

        deselect: function() {
            if (window.WpPopPopBuilderCore) {
                window.WpPopPopBuilderCore.state.activeId = null;
            }
            $('.wppoppop-canvas-item').removeClass('wppoppop-selected');
            if (window.WpPopPopBuilderInspector) {
                window.WpPopPopBuilderInspector.close();
            }
            if (window.WpPopPopBuilderLayers) {
                $('.wppoppop-layer-item').removeClass('active');
            }
        },

        deleteActiveElement: function() {
            var core = window.WpPopPopBuilderCore;
            var activeId = core ? core.state.activeId : null;
            if (!activeId) return;

            var elements = this.getActiveElements();
            var idx = elements.findIndex(function(e) { return e.id === activeId; });
            if (idx > -1) {
                elements.splice(idx, 1);
                this.deselect();
                this.renderCanvas();
                if (window.WpPopPopBuilderLayers) {
                    window.WpPopPopBuilderLayers.renderLayers();
                }
                if (core) {
                    core.pushHistory();
                }
            }
        },

        // =========================================================================
        // LIVE WORKSPACE PREVIEW ENGINE
        // =========================================================================
        bindPreviewModeControls: function() {
            var self = this;

            $('#wppoppop-btn-preview').off('click.previewToggle').on('click.previewToggle', function(e) {
                e.preventDefault();
                if (self.isPreview) {
                    self.exitPreviewMode();
                } else {
                    self.enterPreviewMode();
                }
            });

            $(document).on('click', '#wppoppop-preview-exit-btn', function(e) {
                e.preventDefault();
                self.exitPreviewMode();
            });

            $(document).on('click', '#wppoppop-preview-replay-btn', function(e) {
                e.preventDefault();
                self.playCanvasEntranceAnimation();
            });

            $(document).on('click', '.wppoppop-status-reset-btn', function() {
                $('#wppoppop-stage-status-overlay').removeClass('active');
            });
        },

        enterPreviewMode: function() {
            this.isPreview = true;
            this.deselect();

            $('body').addClass('wppoppop-preview-active');
            $('.wppoppop-builder-workspace').addClass('is-preview-mode');

            var $btn = $('#wppoppop-btn-preview');
            $btn.addClass('preview-active')
                .html('<span class="dashicons dashicons-edit"></span> <span class="wppoppop-btn-label">Exit Preview</span>');

            if (window.WpPopPopBuilderInspector) window.WpPopPopBuilderInspector.close();
            if (window.WpPopPopBuilderSettings) window.WpPopPopBuilderSettings.closeDrawer();

            this.renderCanvas();
            this.playCanvasEntranceAnimation();
            this.playChime('open');
        },

        exitPreviewMode: function() {
            this.isPreview = false;
            $('body').removeClass('wppoppop-preview-active');
            $('.wppoppop-builder-workspace').removeClass('is-preview-mode');

            var $btn = $('#wppoppop-btn-preview');
            $btn.removeClass('preview-active')
                .html('<span class="dashicons dashicons-visibility"></span> <span class="wppoppop-btn-label">Preview</span>');

            $('#wppoppop-stage-status-overlay').removeClass('active');

            var $box = $('#wppoppop-canvas-box');
            $box.removeClass(function(i, c) { return (c.match(/(^|\s)animate__\S+/g) || []).join(' '); });

            this.renderCanvas();
        },

        playCanvasEntranceAnimation: function() {
            var core = window.WpPopPopBuilderCore;
            if (!core) return;
            var cur = core.state.currentCanvas || 1;
            var meta = (core.state.canvasMeta && core.state.canvasMeta[cur]) || {};
            var $box = $('#wppoppop-canvas-box');

            var appearance = (meta.anim_appearance || 'fadeIn').toString().trim();
            var duration = parseInt(meta.anim_duration, 10) || 1000;
            var delay = parseInt(meta.anim_delay, 10) || 0;

            $box.removeClass(function(i, c) { return (c.match(/(^|\s)animate__\S+/g) || []).join(' '); });

            if (appearance === 'none') {
                $box.css({ opacity: 1 });
            } else {
                var animClass = appearance;
                if (animClass.indexOf('animate__') !== 0) {
                    animClass = 'animate__' + animClass;
                }

                $box.css({
                    '--animate-duration': (duration / 1000) + 's',
                    '--animate-delay': (delay / 1000) + 's'
                });
                $box.addClass('animate__animated ' + animClass);
            }

            var elements = this.getActiveElements();
            elements.forEach(function(el) {
                if (el.animEffect && el.animEffect !== 'none') {
                    var $elNode = $('#el-' + el.id);
                    $elNode.removeClass(function(i, c) { return (c.match(/(^|\s)animate__\S+/g) || []).join(' '); });
                    $elNode.addClass('animate__animated animate__' + el.animEffect);
                }
            });
        },

        initActivePreviewWidgets: function() {
            var self = this;

            // 1. Next Canvas Button
            $('.wppoppop-preview-step-btn').off('click.previewStep').on('click.previewStep', function(e) {
                e.preventDefault();
                var target = parseInt($(this).data('goto-canvas'), 10) || 2;
                self.transitionToCanvas(target);
            });

            // 2. Submit Button
            $('.wppoppop-preview-submit-btn').off('click.previewSubmit').on('click.previewSubmit', function(e) {
                e.preventDefault();
                self.handlePreviewSubmit($(this));
            });

            // 3. Link Button
            $('.wppoppop-preview-link-btn').off('click.previewLink').on('click.previewLink', function(e) {
                e.preventDefault();
                var url = $(this).data('url');
                var blank = $(this).data('blank') == '1';
                if (!url || url === '#') {
                    alert('Preview: Link destination triggered (URL: empty)');
                    return;
                }
                if (blank) {
                    window.open(url, '_blank');
                } else {
                    window.location.href = url;
                }
            });

            // 4. Close Icon
            $('.wppoppop-preview-close-btn').off('click.previewClose').on('click.previewClose', function(e) {
                e.preventDefault();
                var act = $(this).data('close-action') || 'close';
                self.playChime('click');
                $('#wppoppop-canvas-box').fadeOut(250, function() {
                    alert('Preview: Popup closed via Close Icon (Action: ' + act + ')');
                    $('#wppoppop-canvas-box').fadeIn(200);
                });
            });

            // 5. Star Rating Interactivity
            $('.wppoppop-preview-star').off('click.previewStar').on('click.previewStar', function(e) {
                e.stopPropagation();
                var ratingVal = parseInt($(this).data('val'), 10) || 5;
                var $parent = $(this).closest('.wppoppop-field-rating');
                $parent.find('.wppoppop-preview-star').each(function() {
                    var v = parseInt($(this).data('val'), 10);
                    $(this).css('color', v <= ratingVal ? '#f59e0b' : '#cbd5e1');
                });
                self.playChime('click');
            });

            // 6. Digital Signature Drawing
            $('.wppoppop-preview-sig-canvas').each(function() {
                var sigCanvas = this;
                var ctx = sigCanvas.getContext('2d');
                var drawing = false;

                $(sigCanvas).off('pointerdown.sig pointermove.sig pointerup.sig')
                    .on('pointerdown.sig', function(e) {
                        drawing = true;
                        var rect = sigCanvas.getBoundingClientRect();
                        ctx.beginPath();
                        ctx.moveTo(e.clientX - rect.left, e.clientY - rect.top);
                    })
                    .on('pointermove.sig', function(e) {
                        if (!drawing) return;
                        var rect = sigCanvas.getBoundingClientRect();
                        ctx.lineTo(e.clientX - rect.left, e.clientY - rect.top);
                        ctx.strokeStyle = '#0f172a';
                        ctx.lineWidth = 2;
                        ctx.stroke();
                    })
                    .on('pointerup.sig pointercancel.sig', function() {
                        drawing = false;
                    });
            });

            $('.wppoppop-preview-sig-clear').off('click.sigClear').on('click.sigClear', function(e) {
                e.stopPropagation();
                var $c = $(this).siblings('.wppoppop-preview-sig-canvas');
                if ($c.length) {
                    var ctx = $c[0].getContext('2d');
                    ctx.clearRect(0, 0, $c[0].width, $c[0].height);
                }
            });

            // 7. Lucky Wheel Interactive Simulation
            $('.wppoppop-preview-wheel-wrap').each(function() {
                var $wrap = $(this);
                var canvas = $wrap.find('.wppoppop-preview-wheel-canvas')[0];
                if (!canvas) return;

                var ctx = canvas.getContext('2d');
                var slices = ['10% OFF', 'FREE SHIPPING', '25% OFF', 'JACKPOT', '5% OFF'];
                var numSlices = slices.length;
                var arc = (2 * Math.PI) / numSlices;
                var colors = ['#f43f5e', '#3b82f6', '#10b981', '#f59e0b', '#8b5cf6'];
                var r = canvas.width / 2;

                ctx.clearRect(0, 0, canvas.width, canvas.height);
                for (var i = 0; i < numSlices; i++) {
                    var angle = i * arc;
                    ctx.beginPath();
                    ctx.fillStyle = colors[i % colors.length];
                    ctx.moveTo(r, r);
                    ctx.arc(r, r, r - 3, angle, angle + arc);
                    ctx.lineTo(r, r);
                    ctx.fill();
                    ctx.save();
                    ctx.translate(r, r);
                    ctx.rotate(angle + arc / 2);
                    ctx.textAlign = 'right';
                    ctx.fillStyle = '#ffffff';
                    ctx.font = 'bold 10px sans-serif';
                    ctx.fillText(slices[i], r - 8, 3);
                    ctx.restore();
                }

                $wrap.find('.wppoppop-preview-wheel-btn').off('click.wheelSpin').on('click.wheelSpin', function(e) {
                    e.stopPropagation();
                    var $btn = $(this);
                    if ($btn.prop('disabled')) return;
                    $btn.prop('disabled', true);

                    var winIdx = Math.floor(Math.random() * numSlices);
                    var deg = 1800 + (360 - (winIdx * (360 / numSlices) + 18));
                    $(canvas).css({
                        transition: 'transform 3.5s cubic-bezier(0.15, 0.9, 0.25, 1)',
                        transform: 'rotate(' + deg + 'deg)'
                    });

                    setTimeout(function() {
                        self.playChime('win');
                        self.launchConfetti();
                        $btn.text('Won: ' + slices[winIdx] + '!').css('background', '#10b981');
                    }, 3500);
                });
            });

            // 8. Scratch Card Interactive Simulation
            $('.wppoppop-preview-scratch-wrap').each(function() {
                var $wrap = $(this);
                var canvas = $wrap.find('.wppoppop-preview-scratch-canvas')[0];
                if (!canvas) return;

                var ctx = canvas.getContext('2d');
                ctx.fillStyle = '#94a3b8';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                ctx.fillStyle = '#ffffff';
                ctx.font = 'bold 11px sans-serif';
                ctx.textAlign = 'center';
                ctx.fillText('SCRATCH TO REVEAL', canvas.width / 2, canvas.height / 2 + 4);

                var scratching = false;
                var cleared = false;

                $(canvas).off('pointerdown.scratch pointermove.scratch pointerup.scratch')
                    .on('pointerdown.scratch', function(e) {
                        scratching = true;
                        scratchAt(e);
                    })
                    .on('pointermove.scratch', function(e) {
                        if (!scratching) return;
                        scratchAt(e);
                    })
                    .on('pointerup.scratch pointercancel.scratch', function() {
                        scratching = false;
                    });

                function scratchAt(e) {
                    var rect = canvas.getBoundingClientRect();
                    var x = e.clientX - rect.left;
                    var y = e.clientY - rect.top;
                    ctx.globalCompositeOperation = 'destination-out';
                    ctx.beginPath();
                    ctx.arc(x, y, 14, 0, Math.PI * 2, false);
                    ctx.fill();

                    if (!cleared) {
                        cleared = true;
                        setTimeout(function() {
                            $(canvas).fadeOut(300);
                            self.playChime('win');
                            self.launchConfetti();
                        }, 1200);
                    }
                }
            });

            // 9. Live Countdown Ticker
            $('.wppoppop-preview-countdown').each(function() {
                var $timer = $(this);
                var secs = parseInt($timer.data('secs'), 10) || 900;
                var $display = $timer.find('.cd-display');

                if ($timer.data('timer-id')) {
                    clearInterval($timer.data('timer-id'));
                }

                var tid = setInterval(function() {
                    secs--;
                    if (secs <= 0) {
                        clearInterval(tid);
                        secs = 0;
                    }
                    var m = Math.floor(secs / 60);
                    var s = secs % 60;
                    $display.text((m < 10 ? '0' : '') + m + ' : ' + (s < 10 ? '0' : '') + s);
                }, 1000);

                $timer.data('timer-id', tid);
            });
        },

        transitionToCanvas: function(targetCanvas) {
            var self = this;
            var core = window.WpPopPopBuilderCore;
            if (!core) return;

            var cur = core.state.currentCanvas || 1;
            var meta = (core.state.canvasMeta && core.state.canvasMeta[cur]) || {};
            var $box = $('#wppoppop-canvas-box');

            var exitAnim = (meta.anim_disappearance || 'fadeOut').toString().trim();
            if (exitAnim.indexOf('animate__') !== 0 && exitAnim !== 'none') {
                exitAnim = 'animate__' + exitAnim;
            }

            var doSwitch = function() {
                core.switchCanvas(targetCanvas);
                self.playCanvasEntranceAnimation();
            };

            if (exitAnim !== 'none') {
                $box.removeClass(function(i, c) { return (c.match(/(^|\s)animate__\S+/g) || []).join(' '); });
                $box.addClass('animate__animated ' + exitAnim);
                setTimeout(doSwitch, 350);
            } else {
                doSwitch();
            }
        },

        handlePreviewSubmit: function($btn) {
            var self = this;
            var $box = $('#wppoppop-canvas-box');
            var valid = true;

            $box.find('input[type="email"]').each(function() {
                var val = $(this).val();
                if ($(this).prop('required') && (!val || val.indexOf('@') === -1)) {
                    valid = false;
                    $(this).css('borderColor', '#ef4444');
                } else {
                    $(this).css('borderColor', '#cbd5e1');
                }
            });

            $box.find('input[type="text"]').each(function() {
                var val = $(this).val();
                if ($(this).prop('required') && !val.trim()) {
                    valid = false;
                    $(this).css('borderColor', '#ef4444');
                } else {
                    $(this).css('borderColor', '#cbd5e1');
                }
            });

            if (!valid) {
                alert('Please fill out all required fields.');
                return;
            }

            this.playChime('win');
            this.launchConfetti();
            $('#wppoppop-stage-status-overlay').addClass('active');
        },

        playChime: function(type) {
            try {
                var AudioContext = window.AudioContext || window.webkitAudioContext;
                if (!AudioContext) return;
                if (!this.audioCtx) this.audioCtx = new AudioContext();
                var ctx = this.audioCtx;
                if (ctx.state === 'suspended') {
                    ctx.resume().catch(function() {});
                }

                var osc = ctx.createOscillator();
                var gain = ctx.createGain();
                osc.type = (type === 'win') ? 'triangle' : 'sine';
                osc.frequency.setValueAtTime(type === 'win' ? 659.25 : 523.25, ctx.currentTime);
                if (type === 'win') {
                    osc.frequency.exponentialRampToValueAtTime(1046.50, ctx.currentTime + 0.25);
                }
                gain.gain.setValueAtTime(0.08, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + 0.35);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start(ctx.currentTime);
                osc.stop(ctx.currentTime + 0.36);
            } catch(e) {}
        },

        launchConfetti: function() {
            var $box = $('#wppoppop-canvas-box');
            var canvas = document.createElement('canvas');
            canvas.style.cssText = 'position:absolute;inset:0;width:100%;height:100%;pointer-events:none;z-index:999999;';
            $box.append(canvas);

            var w = canvas.width = $box.outerWidth() || 640;
            var h = canvas.height = $box.outerHeight() || 400;
            var ctx = canvas.getContext('2d');
            var particles = [];
            var colors = ['#f43f5e', '#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899'];

            for (var i = 0; i < 60; i++) {
                particles.push({
                    x: w / 2,
                    y: h / 2,
                    w: Math.random() * 8 + 4,
                    h: Math.random() * 6 + 4,
                    color: colors[Math.floor(Math.random() * colors.length)],
                    vx: (Math.random() - 0.5) * 12,
                    vy: (Math.random() - 0.7) * 14,
                    rot: Math.random() * 360,
                    vRot: (Math.random() - 0.5) * 10
                });
            }

            var frame = 0;
            function animate() {
                ctx.clearRect(0, 0, w, h);
                particles.forEach(function(p) {
                    p.x += p.vx;
                    p.y += p.vy;
                    p.vy += 0.35;
                    p.rot += p.vRot;
                    ctx.save();
                    ctx.translate(p.x, p.y);
                    ctx.rotate((p.rot * Math.PI) / 180);
                    ctx.fillStyle = p.color;
                    ctx.fillRect(-p.w / 2, -p.h / 2, p.w, p.h);
                    ctx.restore();
                });

                frame++;
                if (frame < 100) {
                    requestAnimationFrame(animate);
                } else {
                    $(canvas).remove();
                }
            }
            animate();
        }
    };

    window.WpPopPopBuilder = window.WpPopPopBuilder || {};
    window.WpPopPopBuilder.Canvas = window.WpPopPopBuilderCanvas;
})(jQuery);
