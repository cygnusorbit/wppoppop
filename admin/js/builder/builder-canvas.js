(function(window, $) {
    'use strict';
    window.WpPopPop = window.WpPopPop || {};

    var Canvas = {
        init: function() {
            this.bindRibbonTools();
            this.bindAlignment();
        },

        bindRibbonTools: function() {
            var self = this;
            $('.wppoppop-tool-item').on('click', function() {
                var type = $(this).data('type');
                self.addElement(type);
            });
        },

        addElement: function(type) {
            window.WpPopPop.History.pushState();
            var State = window.WpPopPop.State;
            var id = 'elem_' + Date.now();
            var newEl = {
                id: id,
                type: type,
                name: type.toUpperCase() + ' ' + (State.elements.length + 1),
                screen: State.activeScreen,
                left: 40,
                top: 40 + (State.elements.length % 5) * 20,
                width: (type === 'button' || type === 'nextstep' || type === 'pay_btn') ? 140 : 200,
                height: (type === 'text') ? 36 : 42,
                z_index: State.elements.length + 1,
                content: (type === 'text') ? 'Click to edit text' : (type === 'button' ? 'Submit' : ''),
                color: '#1e293b',
                bg_color: (type === 'button') ? '#00a32a' : (type === 'pay_btn' ? '#0284c7' : '#2271b1'),
                font_family: 'inherit',
                font_size: 16,
                border_radius: 4,
                anim: 'none',
                required: false,
                field_name: '',
                options: (type === 'wheel') ? ['10% OFF', 'FREE SHIP', '5% OFF', 'JACKPOT'] : [],
                goto_screen: 2,
                click_action: 'none',
                locked: false,
                visible: true
            };

            State.elements.push(newEl);
            this.renderSingle(newEl);
            window.WpPopPop.Layers.renderList();
            this.selectElement(id);
        },

        renderSingle: function(el) {
            if (el.screen !== window.WpPopPop.State.activeScreen) return;
            var self = this;
            var $box = $('#wppoppop-canvas');
            var innerHtml = self.getElementHtml(el);

            var $node = $('<div class="wppoppop-canvas-layer" id="' + el.id + '"></div>')
                .css({
                    position: 'absolute',
                    left: el.left + 'px',
                    top: el.top + 'px',
                    width: el.width + 'px',
                    height: el.height + 'px',
                    zIndex: el.z_index,
                    display: el.visible ? 'block' : 'none'
                })
                .html(innerHtml);

            $box.append($node);

            if (!el.locked) {
                $node.draggable({
                    containment: '#wppoppop-canvas',
                    grid: [10, 10],
                    stop: function(e, ui) {
                        el.left = ui.position.left;
                        el.top = ui.position.top;
                        window.WpPopPop.Inspector.syncCoords(el);
                    }
                }).resizable({
                    containment: '#wppoppop-canvas',
                    stop: function(e, ui) {
                        el.width = ui.size.width;
                        el.height = ui.size.height;
                        window.WpPopPop.Inspector.syncSize(el);
                    }
                });
            }

            $node.on('click', function(e) {
                e.stopPropagation();
                self.selectElement(el.id);
            });
        },

        getElementHtml: function(el) {
            switch(el.type) {
                case 'text':
                    return '<div style="font-family:' + el.font_family + ';font-size:' + el.font_size + 'px;color:' + el.color + ';width:100%;height:100%;display:flex;align-items:center;">' + (el.content || 'Text') + '</div>';
                case 'input':
                    return '<input type="email" placeholder="' + (el.content || 'Enter email...') + '" style="width:100%;height:100%;pointer-events:none;box-sizing:border-box;padding:0 8px;">';
                case 'number':
                    return '<input type="number" value="' + (el.content || '1') + '" style="width:100%;height:100%;pointer-events:none;box-sizing:border-box;padding:0 8px;">';
                case 'dropdown':
                    return '<select style="width:100%;height:100%;pointer-events:none;"><option>Select Choice...</option></select>';
                case 'radio':
                    return '<div style="display:flex;align-items:center;gap:6px;height:100%;"><input type="radio" checked disabled><label>Choice 1</label></div>';
                case 'checkbox':
                    return '<div style="display:flex;align-items:center;gap:6px;height:100%;"><input type="checkbox" checked disabled><label>' + (el.content || 'I Agree') + '</label></div>';
                case 'rating':
                    return '<div style="font-size:20px;color:#f59e0b;height:100%;display:flex;align-items:center;">&#9733;&#9733;&#9733;&#9733;&#9733;</div>';
                case 'date':
                    return '<input type="date" style="width:100%;height:100%;pointer-events:none;">';
                case 'slider':
                    return '<input type="range" style="width:100%;height:100%;pointer-events:none;">';
                case 'signature':
                    return '<div style="width:100%;height:100%;border:1px dashed #94a3b8;display:flex;align-items:center;justify-content:center;font-size:11px;color:#64748b;">Signature Pad</div>';
                case 'wheel':
                    return '<div style="width:100%;height:100%;background:#f59e0b;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;border-radius:50%;">SPIN!</div>';
                case 'scratch':
                    return '<div style="width:100%;height:100%;background:#cbd5e1;display:flex;align-items:center;justify-content:center;font-weight:700;">Scratch Foil</div>';
                case 'countdown':
                    return '<div style="width:100%;height:100%;background:#1e293b;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;">14:59</div>';
                case 'progress':
                    return '<div style="width:100%;height:100%;background:#e2e8f0;border-radius:4px;"><div style="width:50%;height:100%;background:#2271b1;"></div></div>';
                case 'file':
                    return '<input type="file" style="width:100%;height:100%;pointer-events:none;font-size:11px;">';
                case 'nextstep':
                    return '<button type="button" style="width:100%;height:100%;background:' + el.bg_color + ';color:#fff;border:none;border-radius:' + el.border_radius + 'px;font-weight:600;">Next Step &rarr;</button>';
                case 'button':
                    return '<button type="button" style="width:100%;height:100%;background:' + el.bg_color + ';color:#fff;border:none;border-radius:' + el.border_radius + 'px;font-weight:600;">' + (el.content || 'Submit') + '</button>';
                case 'pay_btn':
                    return '<button type="button" style="width:100%;height:100%;background:#0284c7;color:#fff;border:none;border-radius:' + el.border_radius + 'px;font-weight:700;">Pay $10.00</button>';
                case 'html':
                    return '<div style="width:100%;height:100%;overflow:hidden;">' + (el.content || '&lt;HTML&gt;') + '</div>';
                default:
                    return '<div>' + el.type + '</div>';
            }
        },

        selectElement: function(id) {
            window.WpPopPop.State.selectedId = id;
            $('.wppoppop-canvas-layer').removeClass('wppoppop-layer-selected');
            $('#' + id).addClass('wppoppop-layer-selected');

            var el = window.WpPopPop.State.elements.find(function(item) { return item.id === id; });
            if (el) {
                window.WpPopPop.Inspector.open(el);
                window.WpPopPop.Layers.highlight(id);
            }
        },

        refresh: function() {
            var self = this;
            $('#wppoppop-canvas').empty();
            window.WpPopPop.State.elements.forEach(function(el) {
                self.renderSingle(el);
            });
        },

        bindAlignment: function() {
            var self = this;
            $('.wppoppop-align-btn').on('click', function() {
                var align = $(this).data('align');
                var id = window.WpPopPop.State.selectedId;
                if (!id) return;
                var el = window.WpPopPop.State.elements.find(function(item) { return item.id === id; });
                if (!el) return;

                var cWidth = window.WpPopPop.State.activeViewport === 'mobile' ? 360 : window.WpPopPop.State.config.meta.width;
                var cHeight = window.WpPopPop.State.config.meta.height;

                window.WpPopPop.History.pushState();

                if (align === 'left') el.left = 0;
                else if (align === 'center-h') el.left = Math.round((cWidth - el.width) / 2);
                else if (align === 'right') el.left = cWidth - el.width;
                else if (align === 'top') el.top = 0;
                else if (align === 'center-v') el.top = Math.round((cHeight - el.height) / 2);
                else if (align === 'bottom') el.top = cHeight - el.height;

                $('#' + el.id).css({ left: el.left + 'px', top: el.top + 'px' });
                window.WpPopPop.Inspector.syncCoords(el);
            });
        }
    };

    window.WpPopPop.Canvas = Canvas;
})(window, jQuery);
