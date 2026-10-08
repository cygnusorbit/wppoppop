/**
 * WpPopPop Visual Builder: Floating Draggable Magenta LAYERS Panel Engine
 * Responsive Minimization, Draggable Bounds & Sortable Layers
 */
(function($) {
    'use strict';

    window.WpPopPopBuilderLayers = {
        init: function() {
            this.bindPanelDragging();
            this.bindSortable();
            this.bindLayerActions();
            this.bindCollapseToggle();
            this.renderLayers();
        },

        bindCollapseToggle: function() {
            $(document).on('click', '#wppoppop-layers-toggle-collapse', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $('#wppoppop-floating-layers-panel').toggleClass('collapsed');
            });
        },

        bindPanelDragging: function() {
            var $panel = $('#wppoppop-floating-layers-panel');
            if ($panel.data('ui-draggable')) {
                $panel.draggable('destroy');
            }
            $panel.draggable({
                handle: '#wppoppop-layers-header',
                containment: '.wppoppop-builder-workspace',
                start: function(e, ui) {
                    $(this).css({ bottom: 'auto', right: 'auto', height: 'auto' });
                }
            });
        },

        bindSortable: function() {
            var self = this;
            $('#wppoppop-layers-list').sortable({
                handle: '.wppoppop-layer-grip',
                axis: 'y',
                placeholder: 'wppoppop-layer-drop-placeholder',
                update: function() {
                    self.reorderFromList();
                }
            });
        },

        renderLayers: function() {
            var core = window.WpPopPopBuilderCore;
            var $list = $('#wppoppop-layers-list');
            $list.empty();

            if (!core || !core.state.canvases) return;
            var cur = core.state.currentCanvas || 1;
            var elements = (core.state.canvases[cur] || []).slice().reverse();

            $('#wppoppop-layers-count').text(elements.length);

            if (elements.length === 0) {
                $list.html('<div style="text-align:center;padding:14px 8px;font-size:11px;color:#94a3b8;font-style:italic;">No layers on this canvas</div>');
                return;
            }

            elements.forEach(function(el) {
                var elId = el.id;
                var $row = $('<div>')
                    .addClass('wppoppop-layer-item')
                    .attr('data-id', elId)
                    .html(
                        '<span class="wppoppop-layer-grip dashicons dashicons-menu"></span>' +
                        '<span class="wppoppop-layer-title">' + (el.name || (el.type ? el.type.toUpperCase() : 'LAYER')) + '</span>' +
                        '<div class="wppoppop-layer-actions">' +
                            '<button type="button" class="wppoppop-layer-action-btn btn-lock ' + (el.locked ? 'active' : '') + '" title="Lock"><span class="dashicons dashicons-lock"></span></button>' +
                            '<button type="button" class="wppoppop-layer-action-btn btn-vis ' + (el.hidden ? 'active' : '') + '" title="Visibility"><span class="dashicons dashicons-visibility"></span></button>' +
                            '<button type="button" class="wppoppop-layer-action-btn btn-del" title="Delete"><span class="dashicons dashicons-trash"></span></button>' +
                        '</div>'
                    );

                if (core.state.activeId !== null && String(core.state.activeId) === String(elId)) {
                    $row.addClass('active');
                }
                $list.append($row);
            });
        },

        highlightLayer: function(id) {
            $('.wppoppop-layer-item').removeClass('active');
            $('.wppoppop-layer-item').filter(function() {
                return String($(this).attr('data-id')) === String(id);
            }).addClass('active');
        },

        bindLayerActions: function() {
            var self = this;

            $(document).on('click', '.wppoppop-layer-item', function(e) {
                if ($(e.target).closest('.wppoppop-layer-action-btn').length > 0) return;
                e.stopPropagation();
                var id = $(this).attr('data-id');
                if (window.WpPopPopBuilderCanvas) {
                    window.WpPopPopBuilderCanvas.selectElement(id);
                }
            });

            $(document).on('click', '.btn-lock', function(e) {
                e.stopPropagation();
                var id = $(this).closest('.wppoppop-layer-item').attr('data-id');
                self.toggleLock(id);
            });

            $(document).on('click', '.btn-vis', function(e) {
                e.stopPropagation();
                var id = $(this).closest('.wppoppop-layer-item').attr('data-id');
                self.toggleVisibility(id);
            });

            $(document).on('click', '.btn-del', function(e) {
                e.stopPropagation();
                var id = $(this).closest('.wppoppop-layer-item').attr('data-id');
                self.deleteLayer(id);
            });
        },

        toggleLock: function(id) {
            var elements = window.WpPopPopBuilderCanvas.getActiveElements();
            var el = elements.find(function(e) { return String(e.id) === String(id); });
            if (el) {
                el.locked = !el.locked;
                this.renderLayers();
                window.WpPopPopBuilderCanvas.renderCanvas();
            }
        },

        toggleVisibility: function(id) {
            var elements = window.WpPopPopBuilderCanvas.getActiveElements();
            var el = elements.find(function(e) { return String(e.id) === String(id); });
            if (el) {
                el.hidden = !el.hidden;
                $('#el-' + id).toggle(!el.hidden);
                this.renderLayers();
            }
        },

        deleteLayer: function(id) {
            var elements = window.WpPopPopBuilderCanvas.getActiveElements();
            var idx = elements.findIndex(function(e) { return String(e.id) === String(id); });
            if (idx > -1) {
                elements.splice(idx, 1);
                if (window.WpPopPopBuilderCore.state.activeId !== null && String(window.WpPopPopBuilderCore.state.activeId) === String(id)) {
                    window.WpPopPopBuilderCanvas.deselect();
                }
                this.renderLayers();
                window.WpPopPopBuilderCanvas.renderCanvas();
                window.WpPopPopBuilderCore.pushHistory();
            }
        },

        reorderFromList: function() {
            var core = window.WpPopPopBuilderCore;
            var cur = core.state.currentCanvas || 1;
            var elements = core.state.canvases[cur] || [];
            var idOrder = [];

            $('#wppoppop-layers-list .wppoppop-layer-item').each(function() {
                idOrder.push(String($(this).attr('data-id')));
            });

            idOrder.reverse();
            elements.sort(function(a, b) {
                return idOrder.indexOf(String(a.id)) - idOrder.indexOf(String(b.id));
            });

            elements.forEach(function(el, idx) {
                el.zIndex = 10 + idx;
            });

            window.WpPopPopBuilderCanvas.renderCanvas();
            core.pushHistory();
        }
    };
})(jQuery);
