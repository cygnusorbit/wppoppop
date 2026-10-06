(function(window, $) {
    'use strict';
    window.WpPopPopBuilder = window.WpPopPopBuilder || {};

    var Layers = {
        hasBeenMoved: false,

        init: function() {
            this.makePanelDraggable();
            this.bindEvents();
            this.bindWindowResize();
            this.renderList();
        },

        makePanelDraggable: function() {
            var self = this;
            var $panel = $('#wppoppop-floating-layers-panel');
            if (typeof $.fn.draggable === 'function') {
                $panel.draggable({
                    handle: '#wppoppop-layers-header',
                    containment: '#wppoppop-builder-stage',
                    scroll: false,
                    start: function(evt, ui) {
                        self.hasBeenMoved = true;
                        $(this).css({
                            bottom: 'auto',
                            right: 'auto'
                        });
                    }
                });
            }
        },

        bindWindowResize: function() {
            var self = this;
            var $panel = $('#wppoppop-floating-layers-panel');

            $(window).on('resize', function() {
                if (!self.hasBeenMoved) {
                    $panel.css({
                        top: '16px',
                        right: '16px',
                        left: 'auto',
                        bottom: 'auto'
                    });
                } else {
                    var $stage = $('#wppoppop-builder-stage');
                    var stageWidth = $stage.width();
                    var panelLeft = parseInt($panel.css('left'), 10) || 0;
                    var panelWidth = $panel.outerWidth();

                    if (panelLeft + panelWidth > stageWidth) {
                        $panel.css('left', Math.max(16, stageWidth - panelWidth - 16) + 'px');
                    }
                }
            });
        },

        bindEvents: function() {
            var self = this;
            var Core = window.WpPopPopBuilder.Core;

            $(document).on('builder:elements:updated builder:screen:change', function() {
                self.renderList();
            });

            $(document).on('builder:element:selected', function(e, id) {
                $('.wppoppop-layer-card').removeClass('active');
                $('#layer-card-' + id).addClass('active');
            });

            $(document).on('builder:element:deselected', function() {
                $('.wppoppop-layer-card').removeClass('active');
            });

            if (typeof $.fn.sortable === 'function') {
                $('#wppoppop-layers-list').sortable({
                    handle: '.wppoppop-layer-grip',
                    placeholder: 'wppoppop-layer-card-placeholder',
                    update: function() {
                        var newOrder = [];
                        $('#wppoppop-layers-list .wppoppop-layer-card').each(function() {
                            newOrder.push($(this).data('id'));
                        });

                        var reversedStack = newOrder.slice().reverse();
                        reversedStack.forEach(function(layerId, zIdx) {
                            $('#canvas-el-' + layerId).css('z-index', 10 + zIdx);
                        });

                        Core.reorderElements(newOrder);
                    }
                });
            }
        },

        renderList: function() {
            var Core = window.WpPopPopBuilder.Core;
            var $list = $('#wppoppop-layers-list');
            $list.empty();

            var currentEls = Core.elements.filter(function(e) {
                return e.screen === Core.currentScreen;
            });

            $('#wppoppop-layers-count').text(currentEls.length);

            currentEls.forEach(function(el) {
                var $card = $('<div></div>')
                    .attr('id', 'layer-card-' + el.id)
                    .attr('data-id', el.id)
                    .addClass('wppoppop-layer-card')
                    .html(
                        '<div style="display:flex;align-items:center;gap:6px;overflow:hidden;flex:1;">' +
                            '<span class="dashicons dashicons-menu wppoppop-layer-grip" style="font-size:13px;width:13px;height:13px;color:#64748b;cursor:grab;"></span>' +
                            '<span class="wppoppop-layer-name" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + (el.label || el.type) + '</span>' +
                        '</div>' +
                        '<div style="display:flex;align-items:center;gap:8px;">' +
                            '<span class="dashicons ' + (el.locked ? 'dashicons-lock' : 'dashicons-unlock') + ' layer-lock-btn" title="Toggle Lock" style="font-size:14px;width:14px;height:14px;cursor:pointer;color:' + (el.locked ? '#f59e0b' : '#94a3b8') + ';"></span>' +
                            '<span class="dashicons dashicons-trash layer-delete-btn" title="Delete Element" style="font-size:14px;width:14px;height:14px;cursor:pointer;color:#ef4444;"></span>' +
                        '</div>'
                    );

                if (Core.activeId === el.id) $card.addClass('active');

                $card.on('click', function() {
                    Core.selectElement(el.id);
                });

                $card.find('.layer-lock-btn').on('click', function(e) {
                    e.stopPropagation();
                    var isLocked = !el.locked;
                    Core.updateElement(el.id, { locked: isLocked });
                    Core.pushHistory();
                    Layers.renderList();
                });

                $card.find('.layer-delete-btn').on('click', function(e) {
                    e.stopPropagation();
                    Core.removeElement(el.id);
                });

                $list.append($card);
            });
        }
    };

    window.WpPopPopBuilder.Layers = Layers;
})(window, jQuery);
