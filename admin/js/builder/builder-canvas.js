(function(window, $) {
    'use strict';
    window.WpPopPopBuilder = window.WpPopPopBuilder || {};

    var Canvas = {
        init: function() {
            this.bindRibbonTools();
            this.bindCanvasClick();
            this.bindBoxDimensions();
        },

        bindRibbonTools: function() {
            var self = this;
            $('.wppoppop-ribbon-tool').on('click', function() {
                var type = $(this).data('type');
                self.createElement(type);
            });
        },

        createElement: function(type) {
            var core = window.WpPopPopBuilder.Core;
            var id = 'el_' + Date.now();
            var newEl = {
                id: id,
                type: type,
                screen: core.state.currentScreen || 1,
                top: 40,
                left: 40,
                width: 240,
                height: (type === 'button' || type === 'submit' || type === 'step_btn' || type === 'pay') ? 45 : (type === 'wheel' ? 260 : 40),
                zIndex: core.state.elements.length + 1,
                content: this.getDefaultContent(type),
                fontSize: 14,
                fontFamily: 'inherit',
                color: '#1e293b',
                bgColor: (type === 'button' || type === 'submit' || type === 'step_btn') ? '#2563eb' : (type === 'pay' ? '#10b981' : '#ffffff'),
                borderRadius: 4,
                opacity: 1,
                locked: false,
                hidden: false,
                actionUrl: '',
                actionBlank: false,
                actionClose: 'none',
                actionJs: '',
                animEffect: 'none'
            };

            core.state.elements.push(newEl);
            core.recordHistory();

            this.renderElements();
            if (window.WpPopPopBuilder.Layers) window.WpPopPopBuilder.Layers.renderList();
            this.selectElement(id);
        },

        getDefaultContent: function(type) {
            switch(type) {
                case 'text': return 'Double click or edit text layer...';
                case 'email': return 'Enter your email address...';
                case 'number': return '1';
                case 'select': return 'Option 1, Option 2, Option 3';
                case 'radios': return 'Choice A, Choice B';
                case 'checkboxes': return 'I accept terms';
                case 'step_btn': return 'Next Step &rarr;';
                case 'submit': return 'Get My Discount';
                case 'pay': return 'Buy Now ($19.99)';
                case 'countdown': return '600';
                case 'progress': return '50';
                case 'scratch': return 'PROMO50';
                case 'wheel': return '10% OFF, FREE SHIP, 25% OFF, JACKPOT';
                default: return '';
            }
        },

        renderElements: function() {
            var self = this;
            var core = window.WpPopPopBuilder.Core;
            var $root = $('#wppoppop-canvas-elements-root');
            $root.empty();

            $.each(core.state.elements, function(idx, el) {
                if (el.screen !== core.state.currentScreen) return;
                if (el.hidden) return;

                var $el = $('<div class="wppoppop-canvas-item"></div>')
                    .attr('id', 'canvas-' + el.id)
                    .data('id', el.id)
                    .css({
                        top: el.top + 'px',
                        left: el.left + 'px',
                        width: el.width + 'px',
                        height: el.height + 'px',
                        zIndex: el.zIndex,
                        fontFamily: el.fontFamily,
                        fontSize: el.fontSize + 'px',
                        color: el.color,
                        background: el.bgColor,
                        borderRadius: el.borderRadius + 'px',
                        opacity: el.opacity,
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: (el.type === 'button' || el.type === 'submit' || el.type === 'step_btn' || el.type === 'pay') ? 'center' : 'flex-start',
                        padding: '6px 10px'
                    });

                $el.html(self.getPreviewMarkup(el));
                $root.append($el);

                self.attachInteractions($el, el);
            });

            if (core.state.activeId) {
                this.highlightElement(core.state.activeId);
            }
        },

        getPreviewMarkup: function(el) {
            switch(el.type) {
                case 'wheel':
                    return '<div style="width:100%;text-align:center;font-weight:700;color:#c2185b;">&#9678; [Fortune Wheel Preview]</div>';
                case 'scratch':
                    return '<div style="width:100%;text-align:center;background:#94a3b8;color:#fff;border-radius:4px;padding:8px 0;">&#9986; Scratch: ' + el.content + '</div>';
                case 'rating':
                    return '<span style="color:#f59e0b;font-size:20px;">&#9733;&#9733;&#9733;&#9733;&#9733;</span>';
                case 'countdown':
                    return '<div style="width:100%;text-align:center;font-weight:800;font-family:monospace;font-size:16px;">10:00</div>';
                case 'progress':
                    return '<div style="width:100%;height:8px;background:#e2e8f0;border-radius:4px;overflow:hidden;"><div style="width:50%;height:100%;background:#2563eb;"></div></div>';
                default:
                    return el.content || ('[' + el.type.toUpperCase() + ']');
            }
        },

        attachInteractions: function($el, el) {
            var self = this;
            var core = window.WpPopPopBuilder.Core;

            if (!el.locked) {
                $el.draggable({
                    containment: '#wppoppop-canvas-box',
                    grid: [10, 10],
                    stop: function(e, ui) {
                        el.top = ui.position.top;
                        el.left = ui.position.left;
                        core.recordHistory();
                        if (window.WpPopPopBuilder.Inspector) window.WpPopPopBuilder.Inspector.syncCoords(el);
                    }
                }).resizable({
                    containment: '#wppoppop-canvas-box',
                    handles: 'se',
                    stop: function(e, ui) {
                        el.width = ui.size.width;
                        el.height = ui.size.height;
                        core.recordHistory();
                        if (window.WpPopPopBuilder.Inspector) window.WpPopPopBuilder.Inspector.syncCoords(el);
                    }
                });
            }

            $el.on('click', function(e) {
                e.stopPropagation();
                self.selectElement(el.id);
            });
        },

        selectElement: function(id) {
            var core = window.WpPopPopBuilder.Core;
            core.state.activeId = id;
            this.highlightElement(id);

            var el = core.getElementById(id);
            if (el && window.WpPopPopBuilder.Inspector) {
                window.WpPopPopBuilder.Inspector.open(el);
            }
            if (window.WpPopPopBuilder.Layers) {
                window.WpPopPopBuilder.Layers.highlightItem(id);
            }
        },

        highlightElement: function(id) {
            $('.wppoppop-canvas-item').removeClass('wppoppop-selected');
            $('#canvas-' + id).addClass('wppoppop-selected');
        },

        filterByScreen: function() {
            this.renderElements();
        },

        bindCanvasClick: function() {
            $('#wppoppop-canvas-box').on('click', function(e) {
                if (e.target === this) {
                    $('.wppoppop-canvas-item').removeClass('wppoppop-selected');
                    window.WpPopPopBuilder.Core.state.activeId = null;
                    if (window.WpPopPopBuilder.Inspector) window.WpPopPopBuilder.Inspector.close();
                }
            });
        },

        bindBoxDimensions: function() {
            $('#set-box-width, #set-box-height').on('input', function() {
                var w = $('#set-box-width').val() || 640;
                var h = $('#set-box-height').val() || 400;
                if (window.WpPopPopBuilder.Core.state.viewport === 'desktop') {
                    $('#wppoppop-canvas-box').css({ width: w + 'px', height: h + 'px' });
                }
            });
        }
    };

    window.WpPopPopBuilder.Canvas = Canvas;
})(window, jQuery);
