(function(window, $) {
    'use strict';
    window.WpPopPopBuilder = window.WpPopPopBuilder || {};

    var Layers = {
        init: function() {
            this.makeDraggable();
            this.bindEvents();
            this.render();
        },

        makeDraggable: function() {
            $('#wppoppop-floating-layers-panel').draggable({
                handle: '#wppoppop-layers-header',
                containment: 'window'
            });
        },

        bindEvents: function() {
            var self = this;
            var Core = window.WpPopPopBuilder.Core;

            $(document).on('builder:elements:updated builder:screen:change', function() {
                self.render();
            });

            $(document).on('builder:element:selected', function(e, id) {
                $('.wppoppop-layer-card').removeClass('active');
                $('#layer-card-' + id).addClass('active');
            });

            $(document).on('builder:element:deselected', function() {
                $('.wppoppop-layer-card').removeClass('active');
            });

            // Sortable layer list for live Z-Index reordering
            $('#wppoppop-layers-list').sortable({
                handle: '.wppoppop-layer-grip',
                placeholder: 'wppoppop-layer-card-placeholder',
                update: function() {
                    // Top of list = Highest visual layer (topmost Z-Index)
                    var newOrder = [];
                    $('#wppoppop-layers-list .wppoppop-layer-card').each(function() {
                        newOrder.push($(this).data('id'));
                    });

                    // Reverse for stack order (index 0 is frontmost layer)
                    var reversedStack = newOrder.slice().reverse();
                    reversedStack.forEach(function(layerId, zIdx) {
                        $('#canvas-el-' + layerId).css('z-index', 10 + zIdx);
                    });

                    Core.reorderElements(newOrder);
                }
            });
        },

        render: function() {
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
                        '<div style="display:flex;align-items:center;gap:6px;">' +
                            '<span class="dashicons ' + (el.locked ? 'dashicons-lock' : 'dashicons-unlock') + ' layer-lock-btn" title="Toggle Lock" style="font-size:14px;width:14px;height:14px;cursor:pointer;color:' + (el.locked ? '#f59e0b' : '#94a3b8') + ';"></span>' +
                            '<span class="dashicons ' + (el.hidden ? 'dashicons-hidden' : 'dashicons-visibility') + ' layer-hide-btn" title="Toggle Visibility" style="font-size:14px;width:14px;height:14px;cursor:pointer;color:' + (el.hidden ? '#ef4444' : '#94a3b8') + ';"></span>' +
                        '</div>'
                    );

                if (Core.activeId === el.id) $card.addClass('active');

                $card.on('click', function() {
                    Core.selectElement(el.id);
                });

                // Lock toggle listener
                $card.find('.layer-lock-btn').on('click', function(e) {
                    e.stopPropagation();
                    var isLocked = !el.locked;
                    Core.updateElement(el.id, { locked: isLocked });
                    Core.pushHistory();
                    Layers.render();
                });

                // Visibility toggle listener
                $card.find('.layer-hide-btn').on('click', function(e) {
                    e.stopPropagation();
                    var isHidden = !el.hidden;
                    Core.updateElement(el.id, { hidden: isHidden });
                    if (isHidden && Core.activeId === el.id) {
                        Core.activeId = null;
                        $(document).trigger('builder:element:deselected');
                    }
                    Core.pushHistory();
                    Layers.render();
                });

                $list.append($card);
            });
        }
    };

    window.WpPopPopBuilder.Layers = Layers;
})(window, jQuery);
