(function(window, $) {
    'use strict';
    window.WpPopPopBuilder = window.WpPopPopBuilder || {};

    var Layers = {
        init: function() {
            this.makePanelDraggable();
            this.initSortable();
        },

        makePanelDraggable: function() {
            var $panel = $('#wppoppop-floating-layers-panel');
            if ($panel.length && $.fn.draggable) {
                $panel.draggable({
                    handle: '#wppoppop-layers-header',
                    containment: 'window'
                });
            }
        },

        initSortable: function() {
            var self = this;
            var $list = $('#wppoppop-layers-list');
            if ($list.length && $.fn.sortable) {
                $list.sortable({
                    handle: '.wppoppop-layer-grip',
                    placeholder: 'wppoppop-layer-drop-placeholder',
                    update: function() {
                        self.reindexStack();
                    }
                });
            }
        },

        renderList: function() {
            var self = this;
            var core = window.WpPopPopBuilder.Core;
            if (!core) return;

            var $list = $('#wppoppop-layers-list');
            $list.empty();

            var currentLayers = [];
            $.each(core.state.elements, function(idx, el) {
                if (el.screen === core.state.currentScreen) {
                    currentLayers.push(el);
                }
            });

            $('#wppoppop-layers-count').text(currentLayers.length);

            // Stacking order: highest zIndex on top
            currentLayers.sort(function(a, b) {
                return (b.zIndex || 0) - (a.zIndex || 0);
            });

            $.each(currentLayers, function(idx, el) {
                var $item = $('<div class="wppoppop-layer-item"></div>')
                    .attr('data-id', el.id)
                    .toggleClass('active', el.id === core.state.activeId);

                var grip = '<span class="dashicons dashicons-menu wppoppop-layer-grip"></span>';
                var titleText = el.name || ((el.type ? el.type.toUpperCase() : 'LAYER') + ' (' + el.id.substring(3, 8) + ')');
                var title = '<span class="wppoppop-layer-title">' + titleText + '</span>';
                
                var lockIcon = el.locked ? 'dashicons-lock' : 'dashicons-unlock';
                var lockActive = el.locked ? 'active' : '';
                var eyeIcon = el.hidden ? 'dashicons-hidden' : 'dashicons-visibility';
                var eyeActive = el.hidden ? 'active' : '';

                var actions = '<div class="wppoppop-layer-actions">' +
                    '<button type="button" class="wppoppop-layer-action-btn btn-lock ' + lockActive + '" data-id="' + el.id + '" title="Lock Element"><span class="dashicons ' + lockIcon + '"></span></button>' +
                    '<button type="button" class="wppoppop-layer-action-btn btn-eye ' + eyeActive + '" data-id="' + el.id + '" title="Toggle Visibility"><span class="dashicons ' + eyeIcon + '"></span></button>' +
                    '</div>';

                $item.html(grip + title + actions);
                $list.append($item);
            });

            if ($.fn.sortable && $list.data('ui-sortable')) {
                $list.sortable('refresh');
            }

            this.bindLayerEvents();
        },

        bindLayerEvents: function() {
            var self = this;
            var core = window.WpPopPopBuilder.Core;

            $('.wppoppop-layer-item').off('click').on('click', function(e) {
                if ($(e.target).closest('.wppoppop-layer-action-btn').length) return;
                var id = $(this).data('id');
                if (window.WpPopPopBuilder.Canvas) {
                    window.WpPopPopBuilder.Canvas.selectElement(id);
                }
            });

            $('.btn-lock').off('click').on('click', function(e) {
                e.stopPropagation();
                var id = $(this).data('id');
                var el = core.getElementById(id);
                if (el) {
                    el.locked = !el.locked;
                    self.renderList();
                    if (window.WpPopPopBuilder.Canvas) window.WpPopPopBuilder.Canvas.renderElements();
                }
            });

            $('.btn-eye').off('click').on('click', function(e) {
                e.stopPropagation();
                var id = $(this).data('id');
                var el = core.getElementById(id);
                if (el) {
                    el.hidden = !el.hidden;
                    self.renderList();
                    if (window.WpPopPopBuilder.Canvas) window.WpPopPopBuilder.Canvas.renderElements();
                }
            });
        },

        highlightItem: function(id) {
            $('.wppoppop-layer-item').removeClass('active');
            $('.wppoppop-layer-item[data-id="' + id + '"]').addClass('active');
        },

        reindexStack: function() {
            var core = window.WpPopPopBuilder.Core;
            if (!core) return;

            var total = $('#wppoppop-layers-list .wppoppop-layer-item').length;

            $('#wppoppop-layers-list .wppoppop-layer-item').each(function(index) {
                var id = $(this).data('id');
                var el = core.getElementById(id);
                if (el) {
                    el.zIndex = total - index;
                }
            });

            core.recordHistory();
            if (window.WpPopPopBuilder.Canvas) window.WpPopPopBuilder.Canvas.renderElements();
        }
    };

    window.WpPopPopBuilder.Layers = Layers;
})(window, jQuery);
