/**
 * WpPopPop Visual Builder: Canvas Stage Engine
 * Preserves all 21 Interactive Drag-and-Drop Elements with Typography, Alignment & Live Animation Preview
 */
(function($) {
    'use strict';

    window.WpPopPopBuilderCanvas = {
        init: function() {
            this.bindRibbonTools();
            this.bindCanvasSelection();
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
                    opacity: el.opacity !== undefined ? el.opacity : 1
                });

            if (el.fontFamily && el.fontFamily !== 'inherit') $div.css('fontFamily', el.fontFamily);
            if (el.fontSize) $div.css('fontSize', el.fontSize + 'px');
            if (el.fontWeight) $div.css('fontWeight', el.fontWeight);
            if (el.textAlign) $div.css('textAlign', el.textAlign);
            if (el.padding) $div.css('padding', el.padding + 'px');
            if (el.color) $div.css('color', el.color);
            if (el.bgColor) $div.css('backgroundColor', el.bgColor);

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

            if (el.boxShadow && el.boxShadow !== 'none') $div.css('boxShadow', el.boxShadow);

            var innerHtml = this.getInnerMarkup(el);
            $div.html(innerHtml);

            if (window.WpPopPopBuilderCore.state.activeId === el.id) {
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

            switch (el.type) {
                case 'text':
                    return '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:' + justifyVal + ';padding:0 8px;' + textStyle + alignStyle + weightStyle + '">' + (el.content || 'Headline or Text') + '</div>';
                case 'email':
                    return '<input type="email" placeholder="' + (el.content || 'Enter your email...') + '" style="width:100%;height:100%;pointer-events:none;padding:0 10px;border:1px solid #cbd5e1;border-radius:inherit;' + bgStyle + textStyle + alignStyle + weightStyle + '">';
                case 'number':
                    return '<input type="number" placeholder="' + (el.content || 'Enter number...') + '" style="width:100%;height:100%;pointer-events:none;padding:0 10px;border:1px solid #cbd5e1;border-radius:inherit;' + bgStyle + textStyle + alignStyle + weightStyle + '">';
                case 'select':
                    return '<select style="width:100%;height:100%;pointer-events:none;padding:0 10px;border:1px solid #cbd5e1;border-radius:inherit;' + bgStyle + textStyle + alignStyle + weightStyle + '"><option>' + (el.content || 'Option 1, Option 2') + '</option></select>';
                case 'radios':
                    return '<div style="display:flex;gap:10px;align-items:center;justify-content:' + justifyVal + ';height:100%;padding:0 8px;font-size:12px;' + textStyle + weightStyle + '"><label><input type="radio" checked> Option A</label><label><input type="radio"> Option B</label></div>';
                case 'checkboxes':
                    return '<div style="display:flex;align-items:center;justify-content:' + justifyVal + ';gap:6px;height:100%;padding:0 8px;font-size:12px;' + textStyle + weightStyle + '"><input type="checkbox" checked> <span>' + (el.content || 'I agree to the terms') + '</span></div>';
                case 'rating':
                    return '<div style="display:flex;gap:4px;align-items:center;justify-content:' + justifyVal + ';height:100%;color:#f59e0b;font-size:18px;">★ ★ ★ ★ ★</div>';
                case 'date':
                    return '<input type="text" placeholder="' + (el.content || 'YYYY-MM-DD') + '" style="width:100%;height:100%;pointer-events:none;padding:0 10px;border:1px solid #cbd5e1;border-radius:inherit;' + bgStyle + textStyle + alignStyle + weightStyle + '">';
                case 'slider':
                    return '<div style="padding:0 10px;height:100%;display:flex;align-items:center;"><input type="range" style="width:100%;pointer-events:none;"></div>';
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
                    var stepBtnBg = el.bgColor === 'transparent' ? 'background:transparent;border:1px dashed #2563eb;color:#2563eb;' : (el.bgColor ? 'background:' + el.bgColor + ';color:#fff;border:none;' : 'background:#2563eb;color:#fff;border:none;');
                    return '<button type="button" style="width:100%;height:100%;' + stepBtnBg + (el.color ? 'color:' + el.color + ';' : '') + weightStyle + 'border-radius:inherit;cursor:pointer;">' + (el.content || ('Canvas ' + targetCanvas + ' &rarr;')) + '</button>';
                case 'submit':
                    var submitBtnBg = el.bgColor === 'transparent' ? 'background:transparent;border:1px dashed #c2185b;color:#c2185b;' : (el.bgColor ? 'background:' + el.bgColor + ';color:#fff;border:none;' : 'background:#c2185b;color:#fff;border:none;');
                    return '<button type="button" style="width:100%;height:100%;' + submitBtnBg + (el.color ? 'color:' + el.color + ';' : '') + weightStyle + 'border-radius:inherit;cursor:pointer;">' + (el.content || 'Submit Form') + '</button>';
                case 'pay':
                    var payBtnBg = el.bgColor === 'transparent' ? 'background:transparent;border:1px dashed #059669;color:#059669;' : (el.bgColor ? 'background:' + el.bgColor + ';color:#fff;border:none;' : 'background:#059669;color:#fff;border:none;');
                    return '<button type="button" style="width:100%;height:100%;' + payBtnBg + (el.color ? 'color:' + el.color + ';' : '') + weightStyle + 'border-radius:inherit;cursor:pointer;">' + (el.content || 'Checkout Now') + '</button>';
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

            // Strip existing animation classes
            $node.removeClass(function(index, className) {
                return (className.match(/animate__\S+/g) || []).join(' ') + ' ' + (className.match(/anim-\S+/g) || []).join(' ');
            });

            // Trigger cross-browser DOM reflow
            void $node[0].offsetWidth;

            // Re-apply animation classes
            $node.addClass('animate__animated animate__' + effect + ' anim-' + effect);
        },

        attachInteractions: function($node, el) {
            var self = this;
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
                    window.WpPopPopBuilderCore.pushHistory();
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
                width: (type === 'text' || type === 'html') ? 240 : 200,
                height: (type === 'text' || type === 'signature') ? 70 : 42,
                zIndex: nextZ,
                borderRadius: 4,
                borderStyle: 'solid',
                borderWidth: 1,
                fontWeight: '400',
                textAlign: 'left',
                padding: 0,
                opacity: 1,
                content: '',
                goto_canvas: 2,
                goto_screen: 2,
                locked: false,
                hidden: false
            };

            if (type === 'step_btn') {
                newEl.content = 'Next Canvas &rarr;';
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

        bindCanvasSelection: function() {
            var self = this;
            $(document).on('click', '.wppoppop-canvas-item', function(e) {
                e.stopPropagation();
                var id = $(this).data('id');
                self.selectElement(id);
            });

            $('#wppoppop-canvas-box, .wppoppop-builder-workspace').on('click', function(e) {
                if ($(e.target).closest('.wppoppop-canvas-item, #wppoppop-inspector-drawer, #wppoppop-floating-layers-panel, .wppoppop-ribbon-bar, .wppoppop-builder-header').length === 0) {
                    self.deselect();
                }
            });
        },

        selectElement: function(id) {
            window.WpPopPopBuilderCore.state.activeId = id;
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
            window.WpPopPopBuilderCore.state.activeId = null;
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
            var activeId = core.state.activeId;
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
                core.pushHistory();
            }
        }
    };

    // Backward-compatible alias
    window.WpPopPopBuilder = window.WpPopPopBuilder || {};
    window.WpPopPopBuilder.Canvas = window.WpPopPopBuilderCanvas;
})(jQuery);
