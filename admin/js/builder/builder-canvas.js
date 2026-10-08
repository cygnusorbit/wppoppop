/**
 * WpPopPop Visual Builder: Canvas Stage Engine
 * Full 19-Element Visual Renderer, Drag Bounds & Conditional Logic Triggers
 */
(function($) {
    'use strict';

    window.WpPopPopBuilderCanvas = {
        init: function() {
            this.bindCanvasCornerResize();
            this.bindRibbonTools();
            this.bindCanvasSelection();
        },

        bindCanvasCornerResize: function() {
            var self = this;
            var $box = $('#wppoppop-canvas-box');
            var handleEl = document.getElementById('wppoppop-canvas-corner-handle');
            if (!handleEl) return;

            var onPointerDown = function(e) {
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
                    borderWidth: (el.borderWidth || 0) + 'px',
                    borderStyle: (el.borderWidth > 0 ? 'solid' : 'none'),
                    borderColor: el.borderColor || 'transparent',
                    boxShadow: (el.boxShadow && el.boxShadow !== 'none') ? el.boxShadow : 'none',
                    opacity: el.opacity !== undefined ? el.opacity : 1,
                    display: el.hidden ? 'none' : 'block'
                });

            if (el.fontFamily && el.fontFamily !== 'inherit') $div.css('fontFamily', el.fontFamily);
            if (el.fontSize) $div.css('fontSize', el.fontSize + 'px');
            if (el.fontWeight) $div.css('fontWeight', el.fontWeight);
            if (el.textAlign) $div.css('textAlign', el.textAlign);
            if (el.color) $div.css('color', el.color);
            if (el.bgColor) $div.css('backgroundColor', el.bgColor);

            $div.html(this.getInnerMarkup(el));

            var activeId = window.WpPopPopBuilderCore.state.activeId;
            if (activeId !== null && String(activeId) === String(el.id)) {
                $div.addClass('wppoppop-selected');
            }

            return $div;
        },

        getInnerMarkup: function(el) {
            var label = el.content || el.name || 'Element';
            var align = el.textAlign || 'left';
            var type = (el.type || 'text').toString().toLowerCase().trim();

            switch (type) {
                case 'text':
                    var tag = el.htmlTag || 'p';
                    return '<' + tag + ' style="width:100%;height:100%;display:flex;align-items:center;justify-content:' + (align === 'center' ? 'center' : (align === 'right' ? 'flex-end' : 'flex-start')) + ';margin:0;padding:0 8px;line-height:1.3;">' + (el.content || 'Click to edit text layer...') + '</' + tag + '>';

                case 'email':
                    return '<input type="email" placeholder="' + (el.content || 'Enter your email...') + '" style="width:100%;height:100%;padding:0 10px;border:1px solid #cbd5e1;border-radius:inherit;text-align:' + align + ';box-sizing:border-box;">';

                case 'number':
                    return '<input type="number" value="' + (el.content || '1') + '" min="' + (el.min || 0) + '" max="' + (el.max || 100) + '" style="width:100%;height:100%;padding:0 10px;border:1px solid #cbd5e1;border-radius:inherit;text-align:' + align + ';box-sizing:border-box;">';

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
                        radiosHtml += '<label><input type="radio" ' + (idx === 0 ? 'checked' : '') + '> ' + r.trim() + '</label>';
                    });
                    radiosHtml += '</div>';
                    return radiosHtml;

                case 'checkboxes':
                    return '<div style="display:flex;align-items:center;gap:6px;height:100%;padding:0 8px;font-size:12px;"><input type="checkbox" ' + (el.checked !== false ? 'checked' : '') + '> <span>' + (el.content || 'I agree to the terms') + '</span></div>';

                case 'rating':
                    var starCount = parseInt(el.content, 10) || 5;
                    var starColor = el.ratingColor || '#f59e0b';
                    var stars = '';
                    for (var s = 0; s < starCount; s++) stars += '★ ';
                    return '<div style="display:flex;gap:4px;align-items:center;justify-content:' + (align === 'center' ? 'center' : (align === 'right' ? 'flex-end' : 'flex-start')) + ';height:100%;color:' + starColor + ';font-size:18px;">' + stars.trim() + '</div>';

                case 'date':
                    return '<input type="text" placeholder="' + (el.content || 'YYYY-MM-DD') + '" style="width:100%;height:100%;padding:0 10px;border:1px solid #cbd5e1;border-radius:inherit;text-align:' + align + ';box-sizing:border-box;">';

                case 'slider':
                    return '<div style="padding:0 10px;height:100%;display:flex;align-items:center;"><input type="range" min="' + (el.min || 0) + '" max="' + (el.max || 100) + '" value="' + (el.content || 50) + '" style="width:100%;"></div>';

                case 'signature':
                    return '<div style="width:100%;height:100%;border:1px dashed #94a3b8;display:flex;align-items:center;justify-content:center;color:#94a3b8;font-size:11px;">✍ ' + (el.clearLabel ? 'Digital Signature' : 'Digital Signature Pad') + '</div>';

                case 'wheel':
                    return '<div style="width:100%;height:100%;background:#e0e7ff;color:#4338ca;display:flex;flex-direction:column;align-items:center;justify-content:center;font-weight:700;font-size:12px;border-radius:inherit;padding:4px;"><span style="font-size:24px;">🎡</span><span>' + (el.btnText || 'SPIN TO WIN!') + '</span></div>';

                case 'scratch':
                    var foil = el.foilColor || '#94a3b8';
                    return '<div style="width:100%;height:100%;background:' + foil + ';color:#ffffff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12px;border-radius:inherit;padding:4px;">🎟 ' + (el.content || 'Scratch to Reveal') + '</div>';

                case 'countdown':
                    var secs = parseInt(el.countdownSeconds, 10) || 900;
                    var mins = Math.floor(secs / 60);
                    var remSecs = secs % 60;
                    var timeStr = (mins < 10 ? '0' : '') + mins + ' : ' + (remSecs < 10 ? '0' : '') + remSecs;
                    return '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-family:monospace;font-weight:700;font-size:16px;background:#1e293b;color:#f8fafc;border-radius:inherit;">' + timeStr + '</div>';

                case 'progress':
                    var pct = parseInt(el.content, 10) || 65;
                    var barColor = el.progressColor || '#2563eb';
                    return '<div style="width:100%;height:100%;background:#e2e8f0;border-radius:inherit;overflow:hidden;position:relative;"><div style="width:' + pct + '%;height:100%;background:' + barColor + ';"></div></div>';

                case 'file':
                    return '<div style="width:100%;height:100%;border:1px dashed #cbd5e1;display:flex;align-items:center;justify-content:center;font-size:11px;color:#64748b;border-radius:inherit;">📁 Choose File (' + (el.fileExts || '.pdf, .jpg') + ')</div>';

                case 'step_btn':
                    var targetCanvas = el.goto_canvas || el.goto_screen || 2;
                    return '<button type="button" style="width:100%;height:100%;background:' + (el.bgColor || '#2563eb') + ';color:' + (el.color || '#fff') + ';border:none;border-radius:inherit;font-weight:700;">' + (el.content || ('Canvas ' + targetCanvas + ' &rarr;')) + '</button>';

                case 'submit':
                    return '<button type="button" style="width:100%;height:100%;background:' + (el.bgColor || '#c2185b') + ';color:' + (el.color || '#fff') + ';border:none;border-radius:inherit;font-weight:700;">' + (el.content || 'Submit Form') + '</button>';

                case 'pay':
                    var cur = el.payCurrency || 'USD';
                    var amt = el.payAmount !== undefined ? el.payAmount : 19.99;
                    return '<button type="button" style="width:100%;height:100%;background:' + (el.bgColor || '#059669') + ';color:' + (el.color || '#fff') + ';border:none;border-radius:inherit;font-weight:700;">' + (el.content || ('Pay ' + cur + ' ' + amt)) + '</button>';

                case 'html':
                    return '<div style="width:100%;height:100%;overflow:hidden;padding:4px;font-size:11px;border:1px dashed #cbd5e1;border-radius:inherit;">' + (el.content || '<strong>Custom HTML Block</strong>') + '</div>';

                default:
                    return '<div style="padding:6px;font-size:12px;">' + label + '</div>';
            }
        },

        attachInteractions: function($node, el) {
            var self = this;
            if (el.locked) {
                $node.addClass('wppoppop-locked');
                return;
            }

            $node.draggable({
                containment: '#wppoppop-canvas-elements-root',
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
                containment: '#wppoppop-canvas-elements-root',
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
                width: (type === 'text' || type === 'html') ? 260 : (type === 'wheel' ? 180 : 200),
                height: (type === 'text') ? 50 : (type === 'signature' || type === 'wheel' ? 120 : 42),
                zIndex: nextZ,
                borderRadius: 4,
                borderWidth: (type === 'email' || type === 'number' || type === 'select' || type === 'date') ? 1 : 0,
                borderColor: '#cbd5e1',
                opacity: 1,
                fontSize: 14,
                fontWeight: '400',
                textAlign: 'left',
                color: (type === 'step_btn' || type === 'submit' || type === 'pay') ? '#ffffff' : '#0f172a',
                bgColor: (type === 'step_btn') ? '#2563eb' : (type === 'submit' ? '#c2185b' : (type === 'pay' ? '#059669' : '#ffffff')),
                content: this.getDefaultContent(type),
                field_name: type + '_' + (elements.length + 1),
                goto_canvas: 2,
                goto_screen: 2,
                locked: false,
                hidden: false
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

            // Update conditional logic availability immediately
            if (window.WpPopPopBuilderSettings) {
                window.WpPopPopBuilderSettings.checkConditionalLogicEligibility();
            }

            window.WpPopPopBuilderCore.pushHistory();
        },

        getDefaultContent: function(type) {
            switch(type) {
                case 'text': return 'Click to edit your text headline...';
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
                e.preventDefault();
                e.stopPropagation();
                var id = $(this).attr('data-id') || $(this).data('id');
                self.selectElement(id);
            });

            $(document).on('click', '.wppoppop-canvas-item', function(e) {
                e.stopPropagation();
                var id = $(this).attr('data-id') || $(this).data('id');
                self.selectElement(id);
            });

            // Clicking blank canvas stage ONLY deselects active layer (Does NOT open Settings drawer)
            $('#wppoppop-canvas-box').on('click', function(e) {
                if ($(e.target).closest('.wppoppop-canvas-item, #wppoppop-canvas-corner-handle').length === 0) {
                    self.deselect();
                }
            });

            // Clicking empty workspace area
            $('.wppoppop-builder-workspace').on('click', function(e) {
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
        }
    };
})(jQuery);
