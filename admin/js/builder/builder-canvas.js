/**
 * WpPopPop Visual Builder: Canvas Stage Engine
 * Live Workspace Preview Mode, Animate.css Execution, Shape SVG Engine & Overflow Physics
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
            var isShape = (el.type === 'shape');
            var isOverflowable = (el.type === 'image' || el.type === 'shape');

            var $div = $('<div>')
                .addClass('wppoppop-canvas-item')
                .attr('id', 'el-' + el.id)
                .attr('data-id', el.id)
                .css({
                    top: (el.top !== undefined ? el.top : 20) + 'px',
                    left: (el.left !== undefined ? el.left : 20) + 'px',
                    width: (el.width || 180) + 'px',
                    height: (el.height || 42) + 'px',
                    zIndex: el.zIndex || 10,
                    borderRadius: (isShape ? 0 : (el.borderRadius || 4)) + 'px',
                    borderWidth: (isShape ? 0 : (el.borderWidth || 0)) + 'px',
                    borderStyle: (!isShape && el.borderWidth > 0 ? 'solid' : 'none'),
                    borderColor: (isShape ? 'transparent' : (el.borderColor || 'transparent')),
                    boxShadow: (el.boxShadow && el.boxShadow !== 'none') ? el.boxShadow : 'none',
                    opacity: el.opacity !== undefined ? el.opacity : 1,
                    display: el.hidden ? 'none' : 'block',
                    overflow: isOverflowable ? 'visible' : 'hidden'
                });

            if (!isShape) {
                if (el.fontFamily && el.fontFamily !== 'inherit') $div.css('fontFamily', el.fontFamily);
                if (el.fontSize) $div.css('fontSize', el.fontSize + 'px');
                if (el.fontWeight) $div.css('fontWeight', el.fontWeight);
                if (el.textAlign) $div.css('textAlign', el.textAlign);
                if (el.color) $div.css('color', el.color);
                if (el.bgColor) $div.css('backgroundColor', el.bgColor);
            } else {
                $div.css('backgroundColor', 'transparent');
            }

            $div.html(this.getInnerMarkup(el));

            var activeId = window.WpPopPopBuilderCore.state.activeId;
            if (!this.isPreview && activeId !== null && String(activeId) === String(el.id)) {
                $div.addClass('wppoppop-selected');
            }

            return $div;
        },

        renderShapeSvg: function(preset, fill, stroke, strokeWidth, rotate) {
            preset = preset || 'circle';
            fill = fill || '#3b82f6';
            stroke = stroke || 'transparent';
            strokeWidth = strokeWidth !== undefined ? strokeWidth : 0;
            rotate = rotate || 0;

            var transformCss = rotate ? 'transform:rotate(' + rotate + 'deg);-webkit-transform:rotate(' + rotate + 'deg);' : '';
            var svgStyle = 'width:100%;height:100%;display:block;overflow:visible;' + transformCss;
            var sw = parseInt(strokeWidth, 10) || 0;
            var strokeAttr = (sw > 0 && stroke !== 'transparent') ? 'stroke="' + stroke + '" stroke-width="' + sw + '" vector-effect="non-scaling-stroke"' : '';
            var path = '';

            switch(preset) {
                case 'square':
                    path = '<rect x="4" y="4" width="92" height="92" fill="' + fill + '" ' + strokeAttr + '/>';
                    break;
                case 'rounded_square':
                    path = '<rect x="4" y="4" width="92" height="92" rx="16" ry="16" fill="' + fill + '" ' + strokeAttr + '/>';
                    break;
                case 'star':
                    path = '<polygon points="50,4 64,34 97,38 73,61 80,94 50,78 20,94 27,61 3,38 36,34" fill="' + fill + '" ' + strokeAttr + ' stroke-linejoin="round"/>';
                    break;
                case 'triangle':
                    path = '<polygon points="50,6 96,92 4,92" fill="' + fill + '" ' + strokeAttr + ' stroke-linejoin="round"/>';
                    break;
                case 'diamond':
                    path = '<polygon points="50,4 96,50 50,96 4,50" fill="' + fill + '" ' + strokeAttr + ' stroke-linejoin="round"/>';
                    break;
                case 'heart':
                    path = '<path d="M50 88 C20 70 4 50 4 30 C4 14 16 4 30 4 C40 4 47 11 50 17 C53 11 60 4 70 4 C84 4 96 14 96 30 C96 50 80 70 50 88 Z" fill="' + fill + '" ' + strokeAttr + ' stroke-linejoin="round"/>';
                    break;
                case 'hexagon':
                    path = '<polygon points="25,6 75,6 96,50 75,94 25,94 4,50" fill="' + fill + '" ' + strokeAttr + ' stroke-linejoin="round"/>';
                    break;
                case 'octagon':
                    path = '<polygon points="30,4 70,4 96,30 96,70 70,96 30,96 4,70 4,30" fill="' + fill + '" ' + strokeAttr + ' stroke-linejoin="round"/>';
                    break;
                case 'shield':
                    path = '<path d="M50 4 L92 18 L92 54 C92 76 50 96 50 96 C50 96 8 76 8 54 L8 18 Z" fill="' + fill + '" ' + strokeAttr + ' stroke-linejoin="round"/>';
                    break;
                case 'cross':
                    path = '<polygon points="36,4 64,4 64,36 96,36 96,64 64,64 64,96 36,96 36,64 4,64 4,36 36,36" fill="' + fill + '" ' + strokeAttr + ' stroke-linejoin="round"/>';
                    break;
                case 'circle':
                default:
                    path = '<ellipse cx="50" cy="50" rx="46" ry="46" fill="' + fill + '" ' + strokeAttr + '/>';
                    break;
            }

            return '<svg viewBox="0 0 100 100" preserveAspectRatio="none" style="' + svgStyle + '">' + path + '</svg>';
        },

        getInnerMarkup: function(el) {
            var label = el.content || el.name || 'Element';
            var align = el.textAlign || 'left';
            var type = (el.type || 'text').toString().toLowerCase().trim();

            switch (type) {
                case 'text':
                    var tag = el.htmlTag || 'p';
                    return '<' + tag + ' style="width:100%;height:100%;display:flex;align-items:center;justify-content:' + (align === 'center' ? 'center' : (align === 'right' ? 'flex-end' : 'flex-start')) + ';margin:0;padding:0 8px;line-height:1.3;">' + (el.content || 'Click to edit text layer...') + '</' + tag + '>';

                case 'image':
                    var imgUrl = el.content || el.imgUrl || '';
                    var fit = el.objectFit || 'cover';
                    if (!imgUrl) {
                        return '<div style="width:100%;height:100%;background:#f1f5f9;border:1px dashed #cbd5e1;border-radius:inherit;display:flex;flex-direction:column;align-items:center;justify-content:center;color:#94a3b8;font-size:11px;font-weight:600;"><span class="dashicons dashicons-format-image" style="font-size:24px;width:24px;height:24px;margin-bottom:2px;"></span><span>Select Image in Settings</span></div>';
                    }
                    return '<img src="' + imgUrl + '" alt="' + (el.altText || '') + '" style="width:100%;height:100%;object-fit:' + fit + ';border-radius:inherit;display:block;pointer-events:none;">';

                case 'shape':
                    var preset = el.shapePreset || el.content || 'circle';
                    var fill = el.bgColor || '#3b82f6';
                    var stroke = el.borderColor || 'transparent';
                    var strokeWidth = (el.borderWidth !== undefined) ? el.borderWidth : 0;
                    var rotate = el.rotation || 0;
                    return this.renderShapeSvg(preset, fill, stroke, strokeWidth, rotate);

                case 'email':
                    var phEmail = el.content || 'Enter your email...';
                    return '<input type="email" placeholder="' + phEmail + '" ' + (el.required ? 'required' : '') + ' style="width:100%;height:100%;padding:0 10px;border:1px solid #cbd5e1;border-radius:inherit;text-align:' + align + ';box-sizing:border-box;">';

                case 'number':
                    return '<input type="number" value="' + (el.content || '1') + '" min="' + (el.min || 0) + '" max="' + (el.max || 100) + '" step="' + (el.step || 1) + '" style="width:100%;height:100%;padding:0 10px;border:1px solid #cbd5e1;border-radius:inherit;text-align:' + align + ';box-sizing:border-box;">';

                case 'select':
                    var opts = (el.content ? el.content.split(',') : ['Option 1', 'Option 2', 'Option 3']);
                    var selectHtml = '<select style="width:100%;height:100%;padding:0 10px;border:1px solid #cbd5e1;border-radius:inherit;box-sizing:border-box;">';
                    opts.forEach(function(o) { selectHtml += '<option>' + o.trim() + '</option>'; });
                    selectHtml += '</select>';
                    return selectHtml;

                case 'radios':
                    var rOpts = (el.content ? el.content.split(',') : ['Choice A', 'Choice B']);
                    var radiosHtml = '<div style="display:flex;gap:12px;align-items:center;height:100%;padding:0 8px;font-size:12px;">';
                    rOpts.forEach(function(r, idx) {
                        radiosHtml += '<label style="cursor:pointer;"><input type="radio" name="preview_radio_' + el.id + '" value="' + r.trim() + '" ' + (idx === 0 ? 'checked' : '') + '> ' + r.trim() + '</label>';
                    });
                    radiosHtml += '</div>';
                    return radiosHtml;

                case 'checkboxes':
                    return '<div style="display:flex;align-items:center;gap:6px;height:100%;padding:0 8px;font-size:12px;"><label style="cursor:pointer;"><input type="checkbox" ' + (el.checked !== false ? 'checked' : '') + '> <span>' + (el.content || 'I agree to the terms') + '</span></label></div>';

                case 'rating':
                    var starCount = parseInt(el.content, 10) || 5;
                    var starColor = el.ratingColor || '#f59e0b';
                    var starsHtml = '<div class="wppoppop-field-rating" style="display:flex;gap:4px;align-items:center;justify-content:' + (align === 'center' ? 'center' : (align === 'right' ? 'flex-end' : 'flex-start')) + ';height:100%;color:' + starColor + ';font-size:20px;user-select:none;">';
                    for (var s = 1; s <= 5; s++) {
                        starsHtml += '<span class="wppoppop-preview-star" data-val="' + s + '" style="cursor:pointer;transition:transform 0.1s ease;' + (s <= starCount ? '' : 'color:#cbd5e1;') + '">★</span>';
                    }
                    starsHtml += '</div>';
                    return starsHtml;

                case 'date':
                    return '<input type="date" value="' + (el.content || '2026-10-08') + '" style="width:100%;height:100%;padding:0 10px;border:1px solid #cbd5e1;border-radius:inherit;text-align:' + align + ';box-sizing:border-box;">';

                case 'slider':
                    return '<div style="padding:0 10px;height:100%;display:flex;align-items:center;box-sizing:border-box;"><input type="range" min="' + (el.min || 0) + '" max="' + (el.max || 100) + '" value="' + (el.content || 50) + '" style="width:100%;"></div>';

                case 'signature':
                    return '<div style="width:100%;height:100%;position:relative;background:#ffffff;border-radius:inherit;"><canvas class="wppoppop-preview-sig-canvas" width="' + (el.width || 200) + '" height="' + (el.height || 80) + '" style="width:100%;height:100%;border:1px dashed #94a3b8;border-radius:inherit;touch-action:none;cursor:crosshair;"></canvas><button type="button" class="wppoppop-preview-sig-clear button" style="position:absolute;bottom:4px;right:4px;font-size:9px;padding:1px 6px;height:20px;background:#e2e8f0;border:none;cursor:pointer;">' + (el.clearLabel || 'Clear') + '</button></div>';

                case 'wheel':
                    return '<div class="wppoppop-preview-wheel-wrap" style="width:100%;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;position:relative;"><canvas class="wppoppop-preview-wheel-canvas" width="160" height="160" style="border-radius:50%;box-shadow:0 4px 12px rgba(0,0,0,0.2);"></canvas><button type="button" class="button wppoppop-preview-wheel-btn" style="margin-top:6px;background:#4338ca;color:#fff;border:none;font-weight:700;font-size:11px;padding:3px 10px;border-radius:4px;cursor:pointer;">' + (el.btnText || 'SPIN TO WIN!') + '</button></div>';

                case 'scratch':
                    var foil = el.foilColor || '#94a3b8';
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
                    return '<button type="button" class="wppoppop-preview-step-btn" data-goto-canvas="' + targetCanvas + '" style="width:100%;height:100%;background:' + (el.bgColor || '#2563eb') + ';color:' + (el.color || '#fff') + ';border:none;border-radius:inherit;font-weight:700;cursor:pointer;">' + (el.content || ('Canvas ' + targetCanvas + ' &rarr;')) + '</button>';

                case 'submit':
                    return '<button type="button" class="wppoppop-preview-submit-btn" style="width:100%;height:100%;background:' + (el.bgColor || '#c2185b') + ';color:' + (el.color || '#fff') + ';border:none;border-radius:inherit;font-weight:700;cursor:pointer;">' + (el.content || 'Submit Form') + '</button>';

                case 'pay':
                    var cur = el.payCurrency || 'USD';
                    var amt = el.payAmount !== undefined ? el.payAmount : 19.99;
                    return '<button type="button" class="wppoppop-preview-pay-btn" style="width:100%;height:100%;background:' + (el.bgColor || '#059669') + ';color:' + (el.color || '#fff') + ';border:none;border-radius:inherit;font-weight:700;cursor:pointer;">' + (el.content || ('Pay ' + cur + ' ' + amt)) + '</button>';

                case 'html':
                    return '<div style="width:100%;height:100%;overflow:hidden;padding:4px;box-sizing:border-box;">' + (el.content || '<strong>Custom HTML Block</strong>') + '</div>';

                default:
                    return '<div style="padding:6px;font-size:12px;">' + label + '</div>';
            }
        },

        attachInteractions: function($node, el) {
            var self = this;
            if (this.isPreview) return;

            if (el.locked) {
                $node.addClass('wppoppop-locked');
                return;
            }

            // Allow image & shape elements to overflow on workspace
            var isOverflowable = (el.type === 'image' || el.type === 'shape');
            var containmentTarget = isOverflowable ? '.wppoppop-builder-workspace' : '#wppoppop-canvas-elements-root';

            $node.draggable({
                containment: containmentTarget,
                grid: [10, 10],
                drag: function(event, ui) {
                    el.top = ui.position.top;
                    el.left = ui.position.left;
                    if (window.WpPopPopBuilderInspector) {
                        window.WpPopPopBuilderInspector.syncCoordinates(el);
                    }
                },
                stop: function(event, ui) {
                    el.top = ui.position.top;
                    el.left = ui.position.left;
                    if (window.WpPopPopBuilderInspector) {
                        window.WpPopPopBuilderInspector.syncCoordinates(el);
                    }
                    window.WpPopPopBuilderCore.pushHistory();
                }
            });

            $node.resizable({
                containment: containmentTarget,
                handles: 'e, s, se',
                resize: function(event, ui) {
                    el.width = ui.size.width;
                    el.height = ui.size.height;
                    if (window.WpPopPopBuilderInspector) {
                        window.WpPopPopBuilderInspector.syncCoordinates(el);
                    }
                },
                stop: function(event, ui) {
                    el.width = ui.size.width;
                    el.height = ui.size.height;
                    if (window.WpPopPopBuilderInspector) {
                        window.WpPopPopBuilderInspector.syncCoordinates(el);
                    }
                    window.WpPopPopBuilderCore.pushHistory();
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

            var newEl = {
                id: id,
                type: type,
                name: type.toUpperCase() + ' ' + (elements.length + 1),
                top: 50 + (elements.length * 15) % 150,
                left: 50 + (elements.length * 15) % 200,
                width: (type === 'image') ? 220 : ((type === 'shape') ? 120 : ((type === 'text' || type === 'html') ? 260 : (type === 'wheel' ? 180 : 200))),
                height: (type === 'image') ? 140 : ((type === 'shape') ? 120 : ((type === 'text') ? 50 : (type === 'signature' || type === 'wheel' ? 120 : 42))),
                zIndex: nextZ,
                borderRadius: 4,
                borderWidth: (type === 'shape' ? 0 : ((type === 'email' || type === 'number' || type === 'select' || type === 'date') ? 1 : 0)),
                borderColor: (type === 'shape' ? '#1d4ed8' : '#cbd5e1'),
                opacity: 1,
                fontSize: 14,
                fontWeight: '400',
                textAlign: 'left',
                color: (type === 'step_btn' || type === 'submit' || type === 'pay') ? '#ffffff' : '#0f172a',
                bgColor: (type === 'shape') ? '#3b82f6' : ((type === 'step_btn') ? '#2563eb' : (type === 'submit' ? '#c2185b' : (type === 'pay' ? '#059669' : '#ffffff'))),
                content: this.getDefaultContent(type),
                field_name: type + '_' + (elements.length + 1),
                goto_canvas: 2,
                goto_screen: 2,
                locked: false,
                hidden: false,
                objectFit: 'cover',
                altText: 'Popup Image',
                shapePreset: 'circle',
                rotation: 0
            };

            elements.push(newEl);
            window.WpPopPopBuilderCore.state.activeId = id;
            this.renderCanvas();

            if (window.WpPopPopBuilderLayers) {
                window.WpPopPopBuilderLayers.renderLayers();
            }
            if (window.WpPopPopBuilderInspector) {
                window.WpPopPopBuilderInspector.open(id);
            }
            if (window.WpPopPopBuilderSettings) {
                window.WpPopPopBuilderSettings.checkConditionalLogicEligibility();
            }

            window.WpPopPopBuilderCore.pushHistory();
        },

        getDefaultContent: function(type) {
            switch(type) {
                case 'text': return 'Click to edit your text headline...';
                case 'image': return '';
                case 'shape': return 'circle';
                case 'email': return 'Enter your email...';
                case 'number': return '1';
                case 'select': return 'First Option, Second Option, Third Option';
                case 'radios': return 'Choice A, Choice B';
                case 'checkboxes': return 'I agree to the terms';
                case 'date': return '2026-10-08';
                case 'slider': return '50';
                case 'wheel': return '10% OFF, FREE SHIPPING, 25% OFF, JACKPOT';
                case 'scratch': return 'YOU WON 25% OFF! USE CODE: WIN25';
                case 'countdown': return '900';
                case 'progress': return '65';
                case 'step_btn': return 'Next Canvas &rarr;';
                case 'submit': return 'Submit Form';
                case 'pay': return 'Checkout Now';
                case 'html': return '<p>Custom <strong>HTML block</strong></p>';
                default: return '';
            }
        },

        bindCanvasSelection: function() {
            var self = this;

            $('#wppoppop-canvas-elements-root').on('click', '.wppoppop-canvas-item', function(e) {
                if (self.isPreview) return;
                e.preventDefault();
                e.stopPropagation();
                var id = $(this).attr('data-id') || $(this).data('id');
                self.selectElement(id);
            });

            $(document).on('click', '.wppoppop-canvas-item', function(e) {
                if (self.isPreview) return;
                e.stopPropagation();
                var id = $(this).attr('data-id') || $(this).data('id');
                self.selectElement(id);
            });

            $('#wppoppop-canvas-box').on('click', function(e) {
                if (self.isPreview) return;
                if ($(e.target).closest('.wppoppop-canvas-item, #wppoppop-canvas-corner-handle').length === 0) {
                    self.deselect();
                }
            });

            $('.wppoppop-builder-workspace').on('click', function(e) {
                if (self.isPreview) return;
                if ($(e.target).closest('.wppoppop-canvas-item, #wppoppop-inspector-drawer, #wppoppop-floating-layers-panel, #wppoppop-canvas-corner-handle, .wppoppop-ribbon-bar, .wppoppop-builder-header, #wppoppop-settings-drawer, #wppoppop-canvas-box').length === 0) {
                    self.deselect();
                }
            });
        },

        selectElement: function(id) {
            window.WpPopPopBuilderCore.state.activeId = id;
            $('.wppoppop-canvas-item').removeClass('wppoppop-selected');
            $('#el-' + id).addClass('wppoppop-selected');

            if (window.WpPopPopBuilderSettings) {
                window.WpPopPopBuilderSettings.closeDrawer();
            }
            if (window.WpPopPopBuilderLayers) {
                window.WpPopPopBuilderLayers.highlightLayer(id);
            }
            if (window.WpPopPopBuilderInspector) {
                window.WpPopPopBuilderInspector.open(id);
            }
        },

        deselect: function() {
            window.WpPopPopBuilderCore.state.activeId = null;
            $('.wppoppop-canvas-item').removeClass('wppoppop-selected');
            if (window.WpPopPopBuilderInspector) {
                window.WpPopPopBuilderInspector.close();
            }
            if (window.WpPopPopBuilderLayers) {
                $('.wppoppop-layer-item').removeClass('active');
            }
        },

        bindPreviewModeControls: function() {
            var self = this;

            $('#wppoppop-btn-preview').on('click', function(e) {
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

            $('#wppoppop-btn-preview').addClass('preview-active').text('Exit Preview');

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

            $('#wppoppop-btn-preview').removeClass('preview-active').text('Preview');

            $('#wppoppop-stage-status-overlay').removeClass('active');

            var $box = $('#wppoppop-canvas-box');
            $box.removeClass(function(i, c) { return (c.match(/(^|\s)animate__\S+/g) || []).join(' '); });

            this.renderCanvas();
        },

        playCanvasEntranceAnimation: function() {
            var core = window.WpPopPopBuilderCore;
            if (!core) return;
            var cur = core.state.currentCanvas || 1;
            var meta = core.state.canvasMeta[cur] || {};
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

            $('.wppoppop-preview-step-btn').off('click.previewStep').on('click.previewStep', function(e) {
                e.preventDefault();
                var target = parseInt($(this).data('goto-canvas'), 10) || 2;
                self.transitionToCanvas(target);
            });

            $('.wppoppop-preview-submit-btn').off('click.previewSubmit').on('click.previewSubmit', function(e) {
                e.preventDefault();
                self.handlePreviewSubmit($(this));
            });

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
            var meta = core.state.canvasMeta[cur] || {};
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

            if (!valid) {
                alert('Please enter a valid email address.');
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
})(jQuery);
