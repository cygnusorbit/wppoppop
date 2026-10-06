(function(window, $) {
    'use strict';
    window.WpPopPop = window.WpPopPop || {};

    var Layers = {
        init: function() {
            $('#wppoppop-layers-floating-panel').draggable({
                handle: '.wppoppop-layers-header',
                containment: 'window'
            });

            this.bindEvents();
        },

        renderList: function() {
            var self = this;
            var $list = $('#wppoppop-layers-list');
            $list.empty();

            var currentScreenEls = window.WpPopPop.State.elements.filter(function(el) {
                return el.screen === window.WpPopPop.State.activeScreen;
            });

            currentScreenEls.sort(function(a, b) { return b.z_index - a.z_index; });

            currentScreenEls.forEach(function(el) {
                var lockIcon = el.locked ? 'dashicons-lock' : 'dashicons-unlock';
                var eyeIcon = el.visible ? 'dashicons-visibility' : 'dashicons-hidden';

                var $item = $('<li class="wppoppop-layer-row" data-id="' + el.id + '">' +
                    '<span class="dashicons dashicons-menu wppoppop-layer-grip"></span>' +
                    '<span class="wppoppop-layer-label">' + el.name + '</span>' +
                    '<div class="wppoppop-layer-actions">' +
                        '<span class="dashicons ' + lockIcon + ' wppoppop-btn-lock" title="Lock Layer"></span>' +
                        '<span class="dashicons ' + eyeIcon + ' wppoppop-btn-eye" title="Toggle Visibility"></span>' +
                        '<span class="dashicons dashicons-trash wppoppop-btn-del" title="Delete Layer"></span>' +
                    '</div>' +
                '</li>');

                if (el.id === window.WpPopPop.State.selectedId) {
                    $item.addClass('active');
                }

                $list.append($item);
            });

            if ($list.data('ui-sortable')) $list.sortable('destroy');
            $list.sortable({
                handle: '.wppoppop-layer-grip',
                stop: function() {
                    self.reorderFromDom();
                }
            });
        },

        highlight: function(id) {
            $('.wppoppop-layer-row').removeClass('active');
            $('.wppoppop-layer-row[data-id="' + id + '"]').addClass('active');
        },

        bindEvents: function() {
            var self = this;

            $(document).on('click', '.wppoppop-layer-row', function(e) {
                if ($(e.target).closest('.wppoppop-layer-actions').length) return;
                var id = $(this).data('id');
                window.WpPopPop.Canvas.selectElement(id);
            });

            $(document).on('click', '.wppoppop-btn-lock', function(e) {
                e.stopPropagation();
                var id = $(this).closest('.wppoppop-layer-row').data('id');
                var el = window.WpPopPop.State.elements.find(function(item) { return item.id === id; });
                if (!el) return;
                el.locked = !el.locked;
                window.WpPopPop.Canvas.refresh();
                self.renderList();
            });

            $(document).on('click', '.wppoppop-btn-eye', function(e) {
                e.stopPropagation();
                var id = $(this).closest('.wppoppop-layer-row').data('id');
                var el = window.WpPopPop.State.elements.find(function(item) { return item.id === id; });
                if (!el) return;
                el.visible = !el.visible;
                $('#' + el.id).toggle(el.visible);
                self.renderList();
            });

            $(document).on('click', '.wppoppop-btn-del', function(e) {
                e.stopPropagation();
                var id = $(this).closest('.wppoppop-layer-row').data('id');
                window.WpPopPop.History.pushState();
                window.WpPopPop.State.elements = window.WpPopPop.State.elements.filter(function(item) { return item.id !== id; });
                $('#' + id).remove();
                if (window.WpPopPop.State.selectedId === id) {
                    window.WpPopPop.Inspector.close();
                }
                self.renderList();
            });
        },

        reorderFromDom: function() {
            var self = this;
            var rows = $('#wppoppop-layers-list .wppoppop-layer-row');
            var total = rows.length;
            rows.each(function(idx) {
                var id = $(this).data('id');
                var el = window.WpPopPop.State.elements.find(function(item) { return item.id === id; });
                if (el) {
                    el.z_index = total - idx;
                    $('#' + el.id).css('zIndex', el.z_index);
                }
            });
        }
    };

    window.WpPopPop.Layers = Layers;
})(window, jQuery);
