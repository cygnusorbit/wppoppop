/**
 * WpPopPop Visual Builder: Canvas Stage Engine
 * Direct-Pointer Corner Drag Resizing, 26 Elements & Scalable SVG Vector Shape Renderer
 */
(function($) {
    'use strict';

    window.WpPopPopBuilderCanvas = {
        isPreview: false,

        init: function() {
            this.bindCanvasCornerResize();
            this.bindRibbonTools();
            this.bindCanvasSelection();
            this.bindPreviewModeControls();
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
        },

        enterPreviewMode: function() {
            this.isPreview = true;
            $('.wppoppop-builder-workspace').addClass('is-preview-mode');
            $('#wppoppop-btn-preview').addClass('active').html('<span class="dashicons dashicons-no" style="font-size:16px;width:16px;height:16px;margin-top:2px;"></span> Exit Preview');

            if (window.WpPopPopBuilderInspector) window.WpPopPopBuilderInspector.close();
            if (window.WpPopPopBuilderSettings) window.WpPopPopBuilderSettings.closeDrawer();

            if ($('#wppoppop-workspace-preview-bar').length === 0) {
                var $bar = $('<div id="wppoppop-workspace-preview-bar" style="position:fixed;top:60px;left:50%;transform:translateX(-50%);background:#0f172a;border:1px solid #334155;border-radius:24px;padding:6px 14px;display:flex;align-items:center;gap:10px;z-index:999999;box-shadow:0 10px 25px rgba(0,0,0,0.5);color:#f8fafc;font-size:12px;font-weight:700;">' +
                    '<span style="color:#10b981;display:flex;align-items:center;gap:4px;">● LIVE PREVIEW</span>' +
                    '<button type="button" id="wppoppop-preview-replay-btn" style="background:#1e293b;border:1px solid #475569;color:#e2e8f0;padding:3px 10px;border-radius:12px;font-size:11px;cursor:pointer;">Replay Animation</button>' +
                    '<button type="button" id="wppoppop-preview-exit-btn" style="background:#dc2626;border:none;color:#ffffff;padding:3px 10px;border-radius:12px;font-size:11px;cursor:pointer;">Exit Preview</button>' +
                    '</div>');
                $('body').append($bar);
            } else {
                $('#wppoppop-workspace-preview-bar').show();
            }

            this.playCanvasEntranceAnimation();
            this.initActivePreviewWidgets();
        },

        exitPreviewMode: function() {
            this.isPreview = false;
            $('.wppoppop-builder-workspace').removeClass('is-preview-mode');
            $('#wppoppop-btn-preview').removeClass('active').html('<span class="dashicons dashicons-visibility" style="font-size:16px;width:16px;height:16px;margin-top:2px;"></span> Preview');
            $('#wppoppop-workspace-preview-bar').hide();

            var $box = $('#wppoppop-canvas-box');
            $box.removeClass('anim-fade anim-slideDown anim-slideUp anim-slideLeft anim-slideRight anim-zoomIn anim-bounceIn');

            if (window.WpPopPopBuilderSettings) {
                window.WpPopPopBuilderSettings.applyActiveCanvasBackground();
            }
            this.renderCanvas();
        },

        playCanvasEntranceAnimation: function() {
            var core = window.WpPopPopBuilderCore;
            if (!core) return;
            var cur = core.state.currentCanvas || 1;
            var meta = core.state.canvasMeta[cur] || {};
            var $box = $('#wppoppop-canvas-box');

            var anim = meta.anim_appearance || 'fade';
            var dur = (meta.anim_duration !== undefined ? meta.anim_duration : 1000) / 1000;
            var del = (meta.anim_delay !== undefined ? meta.anim_delay : 0) / 1000;

            $box.removeClass('anim-fade anim-slideDown anim-slideUp anim-slideLeft anim-slideRight anim-zoomIn anim-bounceIn');

            if (anim !== 'none') {
                $box.css({
                    animationDuration: dur + 's',
                    animationDelay: del + 's',
                    animationFillMode: 'both'
                }).addClass('anim-' + anim);
            }
        },

        initActivePreviewWidgets: function() {
            var self = this;
            var core = window.WpPopPopBuilderCore;

            $('#wppoppop-canvas-elements-root').off('click.previewStep').on('click.previewStep', '.wppoppop-next-canvas-btn, .wppoppop-next-screen-btn, .wppoppop-next-step, [data-type="step_btn"]', function(e) {
                if (!self.isPreview) return;
                e.preventDefault();
                e.stopPropagation();

                var target = parseInt($(this).data('goto-canvas') || $(this).data('goto-screen') || $(this).data('goto'), 10) || 2;
                if (core) {
                    core.switchCanvas(target);
                    self.playCanvasEntranceAnimation();
                }
            });
        },

        bindCanvasCornerResize: function() {
            var self = this;
            var $box = $('#wppoppop-canvas-box');
            var $handle = $('#wppoppop-canvas-corner-handle');

            $handle.off('mousedown.canvasCorner pointerdown.canvasCorner').on('mousedown.canvasCorner pointerdown.canvasCorner', function(e) {
                e.preventDefault();
                e.stopPropagation();

                var startX = e.clientX;
                var startY = e.clientY;
                var startW = $box.outerWidth();
                var startH = $box.outerHeight();
                var core = window.WpPopPopBuilderCore;
                var cur = core ? (core.state.currentCanvas || 1) : 1;

                $('body').addClass('wppoppop-resizing-canvas');
                self.updateSizeBadge(startW, startH);

                $(document).on('mousemove.canvasCorner pointermove.canvasCorner', function(moveEvent) {
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
                });

                $(document).one('mouseup.canvasCorner pointerup.canvasCorner', function() {
                    $(document).off('mousemove.canvasCorner pointermove.canvasCorner');
                    $('body').removeClass('wppoppop-resizing-canvas');
                    $('#wppoppop-canvas-size-badge').fadeOut(200);

                    if (core) {
                        core.pushHistory();
                    }
                });
            });
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
                if (!self.isPreview) {
                    self.attachInteractions($node, el);
                }
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
                    opacity: el.opacity || 1,
                    display: el.hidden ? 'none' : 'block'
                });

            if (el.fontFamily && el.fontFamily !== 'inherit') $div.css('fontFamily', el.fontFamily);
            if (el.fontSize) $div.css('fontSize', el.fontSize + 'px');
            if (el.fontWeight) $div.css('fontWeight', el.fontWeight);
            if (el.lineHeight) $div.css('lineHeight', el.lineHeight);
            if (el.letterSpacing) $div.css('letterSpacing', el.letterSpacing + 'px');
            if (el.color) $div.css('color', el.color);
            if (el.bgColor) $div.css('backgroundColor', el.bgColor);

            $div.html(this.getInnerMarkup(el));

            if (!this.isPreview && window.WpPopPopBuilderCore.state.activeId === el.id) {
                $div.addClass('wppoppop-selected');
            }

            return $div;
        },

        renderShapeSvg: function(preset, fill, stroke, strokeWidth, rotate) {
            preset = preset || 'circle';
            fill = fill || '#3b82f6';
            stroke = stroke || '#1d4ed8';
            var sw = (strokeWidth !== undefined) ? parseInt(strokeWidth, 10) : 2;
            var rot = parseInt(rotate, 10) || 0;

            var sAttr = (sw > 0 && stroke !== 'transparent') ? 'stroke="' + stroke + '" stroke-width="' + sw + '" vector-effect="non-scaling-stroke"' : 'stroke="none"';
            var fAttr = (fill !== 'transparent') ? 'fill="' + fill + '"' : 'fill="transparent"';
            var transformAttr = rot ? ' transform="rotate(' + rot + ' 50 50)"' : '';

            var geom = '';
            switch(preset) {
                case 'square':
                    geom = '<rect x="5" y="5" width="90" height="90" ' + fAttr + ' ' + sAttr + transformAttr + ' />';
                    break;
                case 'rounded_rect':
                case 'rounded_rectangle':
                    geom = '<rect x="5" y="5" width="90" height="90" rx="15" ry="15" ' + fAttr + ' ' + sAttr + transformAttr + ' />';
                    break;
                case 'star':
                    geom = '<polygon points="50,5 64,36 98,36 70,57 81,91 50,70 19,91 30,57 2,36 36,36" ' + fAttr + ' ' + sAttr + transformAttr + ' stroke-linejoin="round" />';
                    break;
                case 'triangle':
                    geom = '<polygon points="50,8 92,90 8,90" ' + fAttr + ' ' + sAttr + transformAttr + ' stroke-linejoin="round" />';
                    break;
                case 'diamond':
                    geom = '<polygon points="50,5 92,50 50,95 8,50" ' + fAttr + ' ' + sAttr + transformAttr + ' stroke-linejoin="round" />';
                    break;
                case 'heart':
                    geom = '<path d="M50 82 C50 82 12 58 12 33 C12 18 24 10 36 10 C44 10 48 15 50 18 C52 15 56 10 64 10 C76 10 88 18 88 33 C88 58 50 82 50 82 Z" ' + fAttr + ' ' + sAttr + transformAttr + ' stroke-linejoin="round" />';
                    break;
                case 'hexagon':
                    geom = '<polygon points="50,5 89,27 89,73 50,95 11,73 11,27" ' + fAttr + ' ' + sAttr + transformAttr + ' stroke-linejoin="round" />';
                    break;
                case 'octagon':
                    geom = '<polygon points="30,5 70,5 95,30 95,70 70,95 30,95 5,70 5,30" ' + fAttr + ' ' + sAttr + transformAttr + ' stroke-linejoin="round" />';
                    break;
                case 'shield':
                    geom = '<path d="M50 5 L88 18 V50 C88 72 70 88 50 95 C30 88 12 72 12 50 V18 Z" ' + fAttr + ' ' + sAttr + transformAttr + ' stroke-linejoin="round" />';
                    break;
                case 'cross':
                    geom = '<polygon points="35,5 65,5 65,35 95,35 95,65 65,65 65,95 35,95 35,65 5,65 5,35 35,35" ' + fAttr + ' ' + sAttr + transformAttr + ' stroke-linejoin="round" />';
                    break;
                case 'circle':
                default:
                    geom = '<circle cx="50" cy="50" r="45" ' + fAttr + ' ' + sAttr + transformAttr + ' />';
                    break;
            }

            return '<svg viewBox="0 0 100 100" preserveAspectRatio="none" style="width:100%;height:100%;display:block;overflow:visible;">' + geom + '</svg>';
        },

        getInnerMarkup: function(el) {
            var label = el.content || el.name || 'Element';
            switch (el.type) {
                case 'shape':
                    return this.renderShapeSvg(el.shapePreset, el.shapeFill, el.shapeStroke, el.shapeStrokeWidth, el.shapeRotate);
                case 'title':
                    return '<h2 style="margin:0;width:100%;height:100%;display:flex;align-items:center;font-size:inherit;font-weight:inherit;color:inherit;line-height:inherit;">' + (el.content || 'Headline Title') + '</h2>';
                case 'paragraph':
                case 'text':
                    return '<div style="width:100%;height:100%;display:flex;align-items:center;padding:0 4px;font-size:inherit;color:inherit;line-height:inherit;">' + (el.content || 'Headline or Text') + '</div>';
                case 'textfield':
                    return '<input type="text" placeholder="' + (el.content || 'Enter your details...') + '" style="width:100%;height:100%;padding:0 10px;border:1px solid #cbd5e1;border-radius:inherit;box-sizing:border-box;">';
                case 'image':
                    return '<div style="width:100%;height:100%;background:#e2e8f0;display:flex;align-items:center;justify-content:center;color:#64748b;font-size:12px;border-radius:inherit;">🖼 Image Layer</div>';
                case 'video':
                    return '<div style="width:100%;height:100%;background:#1e293b;color:#f8fafc;display:flex;align-items:center;justify-content:center;font-size:12px;border-radius:inherit;">▶ Video Stream</div>';
                case 'close':
                    return '<button type="button" style="width:100%;height:100%;background:transparent;border:none;cursor:pointer;font-size:20px;line-height:1;display:flex;align-items:center;justify-content:center;">&times;</button>';
                case 'link_btn':
                    return '<a href="#" style="width:100%;height:100%;background:#3b82f6;color:#ffffff;display:flex;align-items:center;justify-content:center;text-decoration:none;font-weight:700;border-radius:inherit;">' + (el.content || 'Visit Link &rarr;') + '</a>';
                case 'email':
                    return '<input type="email" placeholder="' + (el.content || 'Enter your email...') + '" style="width:100%;height:100%;padding:0 10px;border:1px solid #cbd5e1;border-radius:inherit;box-sizing:border-box;">';
                case 'number':
                    return '<input type="number" placeholder="' + (el.content || '1') + '" style="width:100%;height:100%;padding:0 10px;border:1px solid #cbd5e1;border-radius:inherit;box-sizing:border-box;">';
                case 'select':
                    return '<select style="width:100%;height:100%;padding:0 10px;border:1px solid #cbd5e1;border-radius:inherit;box-sizing:border-box;"><option>' + (el.content || 'Option 1, Option 2') + '</option></select>';
                case 'radios':
                    return '<div style="display:flex;gap:10px;align-items:center;height:100%;padding:0 8px;font-size:12px;"><label><input type="radio" checked> Option A</label><label><input type="radio"> Option B</label></div>';
                case 'checkboxes':
                    return '<div style="display:flex;align-items:center;gap:6px;height:100%;padding:0 8px;font-size:12px;"><input type="checkbox" checked> <span>' + (el.content || 'I agree to terms') + '</span></div>';
                case 'rating':
                    return '<div style="display:flex;gap:4px;align-items:center;justify-content:center;height:100%;color:#f59e0b;font-size:18px;">★ ★ ★ ★ ★</div>';
                case 'date':
                    return '<input type="text" placeholder="' + (el.content || 'YYYY-MM-DD') + '" style="width:100%;height:100%;padding:0 10px;border:1px solid #cbd5e1;border-radius:inherit;box-sizing:border-box;">';
                case 'slider':
                    return '<div style="padding:0 10px;height:100%;display:flex;align-items:center;"><input type="range" style="width:100%;"></div>';
                case 'signature':
                    return '<div style="width:100%;height:100%;border:1px dashed #94a3b8;display:flex;align-items:center;justify-content:center;color:#94a3b8;font-size:11px;">✍ Digital Signature Pad</div>';
                case 'wheel':
                    return '<div style="width:100%;height:100%;background:#e0e7ff;color:#4338ca;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12px;border-radius:inherit;">🎡 Fortune Prize Wheel</div>';
                case 'scratch':
                    return '<div style="width:100%;height:100%;background:#94a3b8;color:#ffffff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12px;border-radius:inherit;">🎟 Scratch-Off Card</div>';
                case 'countdown':
                    return '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-family:monospace;font-weight:700;font-size:16px;background:#1e293b;color:#f8fafc;border-radius:inherit;">00 : 15 : 00</div>';
                case 'progress':
                    return '<div style="width:100%;height:100%;background:#e2e8f0;border-radius:inherit;overflow:hidden;position:relative;"><div style="width:65%;height:100%;background:#2563eb;"></div></div>';
                case 'file':
                    return '<div style="width:100%;height:100%;border:1px dashed #cbd5e1;display:flex;align-items:center;justify-content:center;font-size:11px;color:#64748b;border-radius:inherit;">📁 Choose File to Upload</div>';
                case 'step_btn':
                    var targetCanvas = el.goto_canvas || el.goto_screen || 2;
                    return '<button type="button" class="wppoppop-next-canvas-btn" data-goto-canvas="' + targetCanvas + '" style="width:100%;height:100%;background:#2563eb;color:#fff;border:none;border-radius:inherit;font-weight:700;cursor:pointer;">' + (el.content || ('Canvas ' + targetCanvas + ' &rarr;')) + '</button>';
                case 'submit':
                    return '<button type="button" style="width:100%;height:100%;background:#c2185b;color:#fff;border:none;border-radius:inherit;font-weight:700;cursor:pointer;">' + (el.content || 'Submit Form') + '</button>';
                case 'pay':
                    return '<button type="button" style="width:100%;height:100%;background:#059669;color:#fff;border:none;border-radius:inherit;font-weight:700;cursor:pointer;">' + (el.content || 'Checkout Now') + '</button>';
                case 'html':
                    return '<div style="width:100%;height:100%;overflow:hidden;padding:4px;font-size:11px;border:1px solid #cbd5e1;border-radius:inherit;">' + (el.content || '<strong>Custom HTML Block</strong>') + '</div>';
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
                width: (type === 'shape') ? 120 : ((type === 'text' || type === 'html') ? 240 : (type === 'rating' ? 140 : 200)),
                height: (type === 'shape') ? 120 : ((type === 'text' || type === 'signature') ? 60 : 42),
                zIndex: nextZ,
                borderRadius: 4,
                opacity: 1,
                content: this.getDefaultContent(type),
                goto_canvas: 2,
                goto_screen: 2,
                locked: false,
                hidden: false
            };

            if (type === 'shape') {
                newEl.shapePreset = 'circle';
                newEl.shapeFill = '#3b82f6';
                newEl.shapeStroke = '#1d4ed8';
                newEl.shapeStrokeWidth = 2;
                newEl.shapeRotate = 0;
            }

            elements.push(newEl);
            window.WpPopPopBuilderCore.state.activeId = id;
            this.renderCanvas();

            if (window.WpPopPopBuilderLayers) {
                window.WpPopPopBuilderLayers.renderLayers();
            }
            if (window.WpPopPopBuilderInspector) {
                window.WpPopPopBuilderInspector.open(id);
            }

            window.WpPopPopBuilderCore.pushHistory();
        },

        getDefaultContent: function(type) {
            switch(type) {
                case 'title': return 'Headline Title';
                case 'paragraph':
                case 'text': return 'Click to edit your text headline...';
                case 'textfield': return 'Enter your text...';
                case 'email': return 'Enter your best email...';
                case 'number': return '1';
                case 'select': return 'First Option, Second Option';
                case 'radios': return 'Option A, Option B';
                case 'checkboxes': return 'I agree to the terms';
                case 'date': return '2026-10-10';
                case 'step_btn': return 'Next Canvas &rarr;';
                case 'submit': return 'Claim Offer Now';
                case 'pay': return 'Complete Checkout ($19.99)';
                case 'html': return '<p>Custom <strong>HTML block</strong></p>';
                default: return '';
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

            $('#wppoppop-canvas-box').on('click', function(e) {
                if (self.isPreview) return;
                if ($(e.target).closest('.wppoppop-canvas-item, #wppoppop-canvas-corner-handle, #wppoppop-canvas-stage-bar').length === 0) {
                    self.deselect();
                    if (window.WpPopPopBuilderSettings) {
                        window.WpPopPopBuilderSettings.openDrawer();
                    }
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
        }
    };
})(jQuery);
