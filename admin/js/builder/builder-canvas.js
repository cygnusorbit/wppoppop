(function(window, $) {
    'use strict';
    window.WpPopPopBuilder = window.WpPopPopBuilder || {};

    var Canvas = {
        init: function() {
            this.bindRibbonTools();
            this.bindStageClick();
            this.bindCustomEvents();
            this.render();
        },

        bindRibbonTools: function() {
            var self = this;
            $('.wppoppop-ribbon-tool').on('click', function() {
                var type = $(this).data('type');
                self.createDefaultElement(type);
            });
        },

        bindStageClick: function() {
            $('#wppoppop-builder-stage').on('click', function(e) {
                if ($(e.target).is('#wppoppop-builder-stage') || $(e.target).is('#wppoppop-canvas-box') || $(e.target).is('#wppoppop-canvas-elements-root')) {
                    window.WpPopPopBuilder.Core.activeId = null;
                    $(document).trigger('builder:element:deselected');
                }
            });
        },

        bindCustomEvents: function() {
            var self = this;
            $(document).on('builder:elements:updated builder:screen:change', function() {
                self.render();
            });

            $(document).on('builder:element:selected', function(e, id) {
                $('.wppoppop-canvas-el').removeClass('active');
                $('#canvas-el-' + id).addClass('active');
            });

            $(document).on('builder:element:deselected', function() {
                $('.wppoppop-canvas-el').removeClass('active');
            });

            $(document).on('builder:element:modified', function(e, el) {
                self.renderElementDom(el);
            });
        },

        createDefaultElement: function(type) {
            var Core = window.WpPopPopBuilder.Core;
            var newId = 'el_' + Date.now().toString(36) + Math.random().toString(36).substr(2, 4);

            var defaultWidth = 220;
            var defaultHeight = 44;
            var defaultBg = '#ffffff';
            var defaultColor = '#1e293b';
            var defaultContent = '';

            switch (type) {
                case 'text':
                    defaultContent = 'Headline or marketing message goes here.';
                    defaultHeight = 48;
                    defaultBg = 'transparent';
                    break;
                case 'email':
                    defaultContent = 'Enter your email...';
                    break;
                case 'number':
                    defaultContent = '0';
                    defaultWidth = 160;
                    break;
                case 'rating':
                    defaultWidth = 180;
                    defaultHeight = 40;
                    defaultBg = 'transparent';
                    break;
                case 'slider':
                    defaultWidth = 240;
                    defaultHeight = 54;
                    break;
                case 'signature':
                    defaultWidth = 260;
                    defaultHeight = 110;
                    break;
                case 'wheel':
                    defaultWidth = 160;
                    defaultHeight = 160;
                    defaultBg = 'transparent';
                    break;
                case 'scratch':
                    defaultWidth = 220;
                    defaultHeight = 90;
                    break;
                case 'countdown':
                    defaultWidth = 240;
                    defaultHeight = 56;
                    defaultBg = 'transparent';
                    break;
                case 'progress':
                    defaultWidth = 240;
                    defaultHeight = 40;
                    defaultBg = 'transparent';
                    break;
                case 'file':
                    defaultWidth = 240;
                    defaultHeight = 70;
                    break;
                case 'step_btn':
                    defaultContent = 'Next Step →';
                    defaultBg = '#3b82f6';
                    defaultColor = '#ffffff';
                    break;
                case 'submit':
                    defaultContent = 'Claim Your Discount';
                    defaultBg = '#10b981';
                    defaultColor = '#ffffff';
                    break;
                case 'pay':
                    defaultContent = 'Pay $19.99 Now';
                    defaultBg = '#6366f1';
                    defaultColor = '#ffffff';
                    break;
                case 'html':
                    defaultContent = '<div style="padding:10px;text-align:center;">Custom HTML Box</div>';
                    defaultWidth = 260;
                    defaultHeight = 70;
                    defaultBg = '#f8fafc';
                    break;
            }

            var defaultEl = {
                id: newId,
                type: type,
                screen: Core.currentScreen,
                top: 60,
                left: 60,
                width: defaultWidth,
                height: defaultHeight,
                label: type.charAt(0).toUpperCase() + type.slice(1).replace('_', ' '),
                content: defaultContent,
                fontFamily: 'inherit',
                fontSize: 14,
                borderRadius: 4,
                color: defaultColor,
                bgColor: defaultBg,
                opacity: 1,
                animEffect: 'none',
                actionUrl: '',
                actionBlank: false,
                actionClose: 'none',
                actionJs: '',
                locked: false,
                hidden: false
            };

            Core.addElement(defaultEl);
        },

        render: function() {
            var self = this;
            var Core = window.WpPopPopBuilder.Core;
            var $root = $('#wppoppop-canvas-elements-root');
            $root.empty();

            var currentEls = Core.elements.filter(function(e) {
                return e.screen === Core.currentScreen && !e.hidden;
            });

            // Set stacking z-index based on element index in array
            currentEls.forEach(function(el, idx) {
                var $el = self.buildElementNode(el, idx + 10);
                $root.append($el);
                self.attachInteractions($el, el);
            });

            if (Core.activeId) {
                $('#canvas-el-' + Core.activeId).addClass('active');
            }
        },

        buildElementNode: function(el, zIndex) {
            var $el = $('<div></div>')
                .attr('id', 'canvas-el-' + el.id)
                .addClass('wppoppop-canvas-el')
                .css({
                    top: el.top + 'px',
                    left: el.left + 'px',
                    width: el.width + 'px',
                    height: el.height + 'px',
                    fontFamily: el.fontFamily || 'inherit',
                    fontSize: (el.fontSize || 14) + 'px',
                    borderRadius: (el.borderRadius || 0) + 'px',
                    color: el.color || '#1e293b',
                    background: el.bgColor || 'transparent',
                    opacity: el.opacity !== undefined ? el.opacity : 1,
                    zIndex: zIndex || 10
                });

            if (el.locked) $el.addClass('locked');

            $el.html(this.generateMarkupForType(el));
            return $el;
        },

        generateMarkupForType: function(el) {
            var text = el.content || el.label || '';
            switch (el.type) {
                case 'text':
                    return '<div style="width:100%;height:100%;display:flex;align-items:center;padding:0 8px;line-height:1.4;box-sizing:border-box;">' + text + '</div>';

                case 'email':
                    return '<div style="display:flex;align-items:center;width:100%;height:100%;padding:0 12px;background:#ffffff;border:1px solid #cbd5e1;border-radius:inherit;box-sizing:border-box;"><span class="dashicons dashicons-email" style="color:#94a3b8;margin-right:8px;font-size:16px;"></span><span style="color:#94a3b8;font-size:inherit;">' + (text || 'user@example.com') + '</span></div>';

                case 'number':
                    return '<div style="display:flex;align-items:center;width:100%;height:100%;padding:0 12px;background:#ffffff;border:1px solid #cbd5e1;border-radius:inherit;box-sizing:border-box;"><span class="dashicons dashicons-calculator" style="color:#94a3b8;margin-right:8px;font-size:16px;"></span><span style="color:#94a3b8;font-size:inherit;">' + (text || '0') + '</span></div>';

                case 'select':
                    return '<div style="display:flex;align-items:center;justify-content:space-between;width:100%;height:100%;padding:0 12px;background:#ffffff;border:1px solid #cbd5e1;border-radius:inherit;box-sizing:border-box;"><span style="color:#475569;font-size:inherit;">Select an option...</span><span class="dashicons dashicons-arrow-down-alt2" style="color:#94a3b8;font-size:14px;"></span></div>';

                case 'radios':
                    return '<div style="display:flex;align-items:center;gap:12px;width:100%;height:100%;padding:0 8px;box-sizing:border-box;"><label style="display:flex;align-items:center;gap:4px;font-size:inherit;color:inherit;"><input type="radio" checked disabled> Choice A</label><label style="display:flex;align-items:center;gap:4px;font-size:inherit;color:inherit;"><input type="radio" disabled> Choice B</label></div>';

                case 'checkboxes':
                    return '<div style="display:flex;align-items:center;gap:8px;width:100%;height:100%;padding:0 8px;box-sizing:border-box;"><input type="checkbox" checked disabled><span style="font-size:inherit;color:inherit;">' + (text || 'I accept terms & conditions') + '</span></div>';

                case 'rating':
                    return '<div style="display:flex;align-items:center;justify-content:center;gap:6px;width:100%;height:100%;color:#f59e0b;font-size:22px;"><span>★</span><span>★</span><span>★</span><span>★</span><span>★</span></div>';

                case 'date':
                    return '<div style="display:flex;align-items:center;width:100%;height:100%;padding:0 12px;background:#ffffff;border:1px solid #cbd5e1;border-radius:inherit;box-sizing:border-box;"><span class="dashicons dashicons-calendar-alt" style="color:#94a3b8;margin-right:8px;font-size:16px;"></span><span style="color:#94a3b8;font-size:inherit;">YYYY-MM-DD</span></div>';

                case 'slider':
                    return '<div style="display:flex;flex-direction:column;justify-content:center;width:100%;height:100%;padding:0 10px;box-sizing:border-box;"><div style="display:flex;justify-content:space-between;font-size:11px;color:#64748b;margin-bottom:4px;"><span>0</span><span>Value: 50</span><span>100</span></div><input type="range" min="0" max="100" value="50" style="width:100%;pointer-events:none;"></div>';

                case 'signature':
                    return '<div style="display:flex;flex-direction:column;justify-content:flex-end;width:100%;height:100%;border:1px dashed #94a3b8;background:rgba(255,255,255,0.95);border-radius:inherit;padding:8px;box-sizing:border-box;"><div style="border-bottom:1px solid #64748b;display:flex;justify-content:space-between;font-size:11px;color:#94a3b8;font-style:italic;padding-bottom:2px;"><span>Sign here ✕</span><span class="dashicons dashicons-edit"></span></div></div>';

                case 'wheel':
                    return '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:radial-gradient(circle, #f59e0b 20%, #ef4444 80%);border-radius:50%;border:3px solid #fbbf24;box-shadow:0 4px 6px rgba(0,0,0,0.2);color:#ffffff;font-weight:800;font-size:12px;letter-spacing:1px;text-align:center;">🎡 SPIN</div>';

                case 'scratch':
                    return '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:repeating-linear-gradient(45deg, #94a3b8, #94a3b8 10px, #64748b 10px, #64748b 20px);border-radius:inherit;color:#ffffff;font-weight:700;font-size:12px;text-shadow:0 1px 2px rgba(0,0,0,0.5);">✨ SCRATCH HERE ✨</div>';

                case 'countdown':
                    return '<div style="display:flex;align-items:center;justify-content:center;gap:6px;width:100%;height:100%;"><div style="background:#1e293b;color:#fff;padding:4px 8px;border-radius:4px;font-weight:700;font-size:13px;text-align:center;">00<span style="display:block;font-size:8px;color:#94a3b8;">HRS</span></div><span>:</span><div style="background:#1e293b;color:#fff;padding:4px 8px;border-radius:4px;font-weight:700;font-size:13px;text-align:center;">15<span style="display:block;font-size:8px;color:#94a3b8;">MIN</span></div><span>:</span><div style="background:#1e293b;color:#fff;padding:4px 8px;border-radius:4px;font-weight:700;font-size:13px;text-align:center;">30<span style="display:block;font-size:8px;color:#94a3b8;">SEC</span></div></div>';

                case 'progress':
                    return '<div style="width:100%;height:100%;display:flex;flex-direction:column;justify-content:center;padding:0 8px;box-sizing:border-box;"><div style="width:100%;height:12px;background:#e2e8f0;border-radius:6px;overflow:hidden;"><div style="width:65%;height:100%;background:#3b82f6;border-radius:6px;"></div></div><span style="font-size:10px;color:#64748b;margin-top:2px;text-align:right;">Step 1 of 2 (65%)</span></div>';

                case 'file':
                    return '<div style="display:flex;align-items:center;justify-content:center;gap:6px;width:100%;height:100%;border:2px dashed #94a3b8;border-radius:inherit;background:rgba(248,250,252,0.9);color:#64748b;font-size:12px;font-weight:600;"><span class="dashicons dashicons-upload" style="font-size:16px;"></span> Choose file or drag here</div>';

                case 'step_btn':
                    return '<button type="button" style="width:100%;height:100%;background:inherit;color:inherit;border:none;border-radius:inherit;font-weight:700;font-size:inherit;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:6px;">' + (text || 'Next Step') + ' <span class="dashicons dashicons-controls-forward" style="font-size:14px;width:14px;height:14px;"></span></button>';

                case 'submit':
                    return '<button type="button" style="width:100%;height:100%;background:inherit;color:inherit;border:none;border-radius:inherit;font-weight:700;font-size:inherit;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:6px;"><span class="dashicons dashicons-yes" style="font-size:14px;width:14px;height:14px;"></span> ' + (text || 'Submit') + '</button>';

                case 'pay':
                    return '<button type="button" style="width:100%;height:100%;background:inherit;color:inherit;border:none;border-radius:inherit;font-weight:700;font-size:inherit;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:6px;"><span class="dashicons dashicons-cart" style="font-size:14px;width:14px;height:14px;"></span> ' + (text || 'Pay Now') + '</button>';

                case 'html':
                    return '<div style="width:100%;height:100%;overflow:hidden;box-sizing:border-box;">' + (text || '<div>Custom HTML</div>') + '</div>';

                default:
                    return '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:600;">[' + el.label + ']</div>';
            }
        },

        renderElementDom: function(el) {
            var $el = $('#canvas-el-' + el.id);
            if ($el.length) {
                $el.css({
                    top: el.top + 'px',
                    left: el.left + 'px',
                    width: el.width + 'px',
                    height: el.height + 'px',
                    fontFamily: el.fontFamily,
                    fontSize: el.fontSize + 'px',
                    borderRadius: el.borderRadius + 'px',
                    color: el.color,
                    background: el.bgColor,
                    opacity: el.opacity
                });

                if (el.locked) {
                    $el.addClass('locked');
                    if ($el.data('ui-draggable')) $el.draggable('disable');
                    if ($el.data('ui-resizable')) $el.resizable('disable');
                } else {
                    $el.removeClass('locked');
                    if ($el.data('ui-draggable')) $el.draggable('enable');
                    if ($el.data('ui-resizable')) $el.resizable('enable');
                }

                if (el.hidden) {
                    $el.hide();
                } else {
                    $el.show();
                }

                $el.html(this.generateMarkupForType(el));
            }
        },

        attachInteractions: function($el, el) {
            var Core = window.WpPopPopBuilder.Core;

            $el.off('click').on('click', function(e) {
                e.stopPropagation();
                Core.selectElement(el.id);
            });

            // Initialize Draggable with 10px grid and real-time live drag updates
            $el.draggable({
                containment: '#wppoppop-canvas-box',
                grid: [10, 10],
                disabled: !!el.locked,
                drag: function(evt, ui) {
                    var top = Math.round(ui.position.top);
                    var left = Math.round(ui.position.left);
                    el.top = top;
                    el.left = left;
                    $(document).trigger('builder:element:moving', [{ id: el.id, top: top, left: left }]);
                },
                stop: function(evt, ui) {
                    var top = Math.round(ui.position.top);
                    var left = Math.round(ui.position.left);
                    Core.updateElement(el.id, { top: top, left: left });
                    Core.pushHistory();
                }
            });

            // Initialize Resizable with 10px grid and real-time live resize updates
            $el.resizable({
                containment: '#wppoppop-canvas-box',
                grid: [10, 10],
                handles: 'e, s, se',
                disabled: !!el.locked,
                resize: function(evt, ui) {
                    var width = Math.round(ui.size.width);
                    var height = Math.round(ui.size.height);
                    el.width = width;
                    el.height = height;
                    $(document).trigger('builder:element:resizing', [{ id: el.id, width: width, height: height }]);
                },
                stop: function(evt, ui) {
                    var width = Math.round(ui.size.width);
                    var height = Math.round(ui.size.height);
                    Core.updateElement(el.id, { width: width, height: height });
                    Core.pushHistory();
                }
            });
        }
    };

    window.WpPopPopBuilder.Canvas = Canvas;
})(window, jQuery);
