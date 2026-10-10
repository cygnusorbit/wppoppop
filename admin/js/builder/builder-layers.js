/**
 * WpPopPop Visual Builder: Floating Magenta Layers Panel Controller
 * Full 26-Element Stacking, Deep Duplication, Compact 30% Action Buttons & Sorting
 */
(function($) {
    'use strict';

    window.WpPopPopBuilderLayers = {
        init: function() {
            this.bindPanelInteractions();
            this.bindEvents();
        },

        bindPanelInteractions: function() {
            var $panel = $('#wppoppop-floating-layers-panel');
            if (!$panel.length) return;

            // Make panel draggable within workspace
            $panel.draggable({
                handle: '#wppoppop-layers-header, .wppoppop-layers-panel-header',
                containment: '.wppoppop-builder-workspace',
                scroll: false,
                cursor: 'grab'
            });

            // Toggle collapse body
            $panel.off('click.toggleCollapse', '#wppoppop-layers-toggle-collapse, .wppoppop-layers-collapse-btn').on('click.toggleCollapse', '#wppoppop-layers-toggle-collapse, .wppoppop-layers-collapse-btn', function(e) {
                e.stopPropagation();
                $panel.toggleClass('collapsed');
            });
        },

        renderLayers: function() {
            var self = this;
            var $list = $('#wppoppop-layers-list');
            if (!$list.length) return;

            $list.empty();

            if (!window.WpPopPopBuilderCanvas) return;
            var elements = window.WpPopPopBuilderCanvas.getActiveElements();

            $('#wppoppop-layers-count').text(elements ? elements.length : 0);

            if (!elements || elements.length === 0) {
                $list.html('<div style="padding:14px 10px;text-align:center;color:#94a3b8;font-size:11px;font-style:italic;">No layers on this canvas. Click an element in the Ribbon Bar to add one.</div>');
                return;
            }

            var activeId = window.WpPopPopBuilderCore ? window.WpPopPopBuilderCore.state.activeId : null;

            // Sort elements by zIndex descending so top-most layer appears at top of list
            var sortedElements = elements.slice().sort(function(a, b) {
                return (b.zIndex || 10) - (a.zIndex || 10);
            });

            sortedElements.forEach(function(el) {
                var isSelected = activeId !== null && String(activeId) === String(el.id);
                var isLocked = !!el.locked;
                var isHidden = !!el.hidden;

                var iconClass = self.getLayerIcon(el.type || 'text');
                var defaultLabel = (el.type === 'text' || el.type === 'paragraph') ? 'PARAGRAPH' : (el.type || 'ELEMENT').toUpperCase();
                var layerTitle = el.name || defaultLabel;

                var $item = $('<div>')
                    .addClass('wppoppop-layer-item')
                    .attr('id', 'layer-item-' + el.id)
                    .attr('data-id', el.id)
                    .toggleClass('active', isSelected)
                    .toggleClass('is-locked', isLocked)
                    .toggleClass('is-hidden', isHidden);

                // 30% Smaller Action Buttons
                var itemHtml = 
                    '<div class="wppoppop-layer-drag-grip" title="Drag to reorder layer depth"><span class="dashicons dashicons-menu"></span></div>' +
                    '<div class="wppoppop-layer-icon"><span class="dashicons ' + iconClass + '"></span></div>' +
                    '<div class="wppoppop-layer-title" title="' + self.escapeHtml(layerTitle) + '">' + self.escapeHtml(layerTitle) + '</div>' +
                    '<div class="wppoppop-layer-actions">' +
                        '<button type="button" class="wppoppop-layer-action-btn wppoppop-layer-btn-duplicate" title="Duplicate Layer" data-id="' + el.id + '"><span class="dashicons dashicons-admin-page"></span></button>' +
                        '<button type="button" class="wppoppop-layer-action-btn wppoppop-layer-btn-lock" title="' + (isLocked ? 'Unlock Layer' : 'Lock Layer') + '" data-id="' + el.id + '"><span class="dashicons ' + (isLocked ? 'dashicons-lock' : 'dashicons-unlock') + '"></span></button>' +
                        '<button type="button" class="wppoppop-layer-action-btn wppoppop-layer-btn-vis" title="' + (isHidden ? 'Show Layer' : 'Hide Layer') + '" data-id="' + el.id + '"><span class="dashicons ' + (isHidden ? 'dashicons-hidden' : 'dashicons-visibility') + '"></span></button>' +
                        '<button type="button" class="wppoppop-layer-action-btn wppoppop-layer-btn-del" title="Delete Layer" data-id="' + el.id + '"><span class="dashicons dashicons-trash"></span></button>' +
                    '</div>';

                $item.html(itemHtml);
                $list.append($item);
            });

            this.bindSortable();
        },

        getLayerIcon: function(type) {
            var t = (type || 'text').toString().toLowerCase().trim();
            switch (t) {
                // Core Typography
                case 'title':       return 'dashicons-heading';
                case 'text':
                case 'paragraph':   return 'dashicons-editor-paragraph';

                // Media & Shapes
                case 'image':       return 'dashicons-format-image';
                case 'video':       return 'dashicons-video-alt3';
                case 'shape':       return 'dashicons-marker';

                // Form Inputs
                case 'textfield':   return 'dashicons-forms';
                case 'email':       return 'dashicons-email';
                case 'number':      return 'dashicons-calculator';
                case 'select':      return 'dashicons-menu-alt';
                case 'radios':      return 'dashicons-marker';
                case 'checkboxes':  return 'dashicons-yes';
                case 'rating':      return 'dashicons-star-filled';
                case 'date':        return 'dashicons-calendar-alt';
                case 'slider':      return 'dashicons-leftright';
                case 'signature':   return 'dashicons-edit';

                // Gamification & Urgency
                case 'wheel':       return 'dashicons-update';
                case 'scratch':     return 'dashicons-tickets-alt';
                case 'countdown':   return 'dashicons-clock';
                case 'progress':    return 'dashicons-performance';
                case 'file':        return 'dashicons-upload';

                // Action Buttons & Triggers
                case 'submit':      return 'dashicons-yes-alt';
                case 'link_btn':    return 'dashicons-admin-links';
                case 'step_btn':    return 'dashicons-arrow-right-alt';
                case 'pay':         return 'dashicons-cart';
                case 'close_icon':  return 'dashicons-no-alt';

                // Custom HTML
                case 'html':        return 'dashicons-html';

                default:            return 'dashicons-admin-generic';
            }
        },

        bindSortable: function() {
            var self = this;
            var $list = $('#wppoppop-layers-list');
            if (!$list.length) return;

            $list.sortable({
                handle: '.wppoppop-layer-drag-grip',
                axis: 'y',
                containment: 'parent',
                tolerance: 'pointer',
                opacity: 0.85,
                stop: function() {
                    self.syncZIndexesFromList();
                }
            });
        },

        syncZIndexesFromList: function() {
            var $items = $('#wppoppop-layers-list').find('.wppoppop-layer-item');
            var total = $items.length;
            if (!total) return;

            var elements = window.WpPopPopBuilderCanvas.getActiveElements();

            $items.each(function(index) {
                var id = $(this).attr('data-id');
                var newZ = (total - index) * 5 + 10;

                var el = elements.find(function(e) { return String(e.id) === String(id); });
                if (el) {
                    el.zIndex = newZ;
                    $('#el-' + id).css('z-index', newZ);
                }
            });

            if (window.WpPopPopBuilderCore) {
                window.WpPopPopBuilderCore.pushHistory();
            }
        },

        pushForInspector: function(isOpen) {
            var $panel = $('#wppoppop-floating-layers-panel');
            if (!$panel.length) return;

            var $workspace = $('#wppoppop-builder-workspace, .wppoppop-builder-workspace').first();
            var wsWidth = $workspace.width() || $(window).width();
            var drawerWidth = 360;
            var gutter = 16;
            var panelWidth = $panel.outerWidth() || 240;

            if (isOpen) {
                var hasInlineLeft = $panel[0].style.left !== '' && $panel[0].style.left !== 'auto';
                if (hasInlineLeft) {
                    var currentLeft = parseFloat($panel.css('left')) || 0;
                    var collisionThreshold = wsWidth - drawerWidth - gutter;

                    if (currentLeft + panelWidth > collisionThreshold) {
                        if ($panel.data('orig-left') === undefined) {
                            $panel.data('orig-left', currentLeft);
                        }
                        var pushedLeft = Math.max(gutter, collisionThreshold - panelWidth);
                        $panel.css({
                            left: pushedLeft + 'px',
                            right: 'auto',
                            transition: 'left 0.28s cubic-bezier(0.16, 1, 0.3, 1)'
                        });
                    }
                } else {
                    $panel.removeClass('is-dragged').addClass('docked-default');
                    $panel.css({
                        right: (drawerWidth + gutter) + 'px',
                        left: 'auto',
                        transition: 'right 0.28s cubic-bezier(0.16, 1, 0.3, 1)'
                    });
                }
            } else {
                if ($panel.data('orig-left') !== undefined) {
                    var origLeft = $panel.data('orig-left');
                    $panel.removeData('orig-left');
                    $panel.css({
                        left: origLeft + 'px',
                        right: 'auto',
                        transition: 'left 0.28s cubic-bezier(0.16, 1, 0.3, 1)'
                    });
                } else if (!$panel.hasClass('is-dragged') || ($panel[0].style.left === '' || $panel[0].style.left === 'auto')) {
                    $panel.addClass('docked-default');
                    $panel.css({
                        right: gutter + 'px',
                        left: 'auto',
                        transition: 'right 0.28s cubic-bezier(0.16, 1, 0.3, 1)'
                    });
                }
            }
        },

        pushLayerPanel: function(isOpen) {
            this.pushForInspector(isOpen);
        },

        bindEvents: function() {
            var self = this;

            // 1. Layer item selection
            $(document).off('click.layerSelect', '.wppoppop-layer-item').on('click.layerSelect', '.wppoppop-layer-item', function(e) {
                if ($(e.target).closest('button, .wppoppop-layer-drag-grip').length) return;
                var id = $(this).attr('data-id');
                if (window.WpPopPopBuilderCanvas) {
                    window.WpPopPopBuilderCanvas.selectElement(id);
                }
            });

            // 2. Lock / Unlock button
            $(document).off('click.layerLock', '.wppoppop-layer-btn-lock').on('click.layerLock', '.wppoppop-layer-btn-lock', function(e) {
                e.stopPropagation();
                var id = $(this).attr('data-id');
                self.toggleLock(id);
            });

            // 3. Visibility button
            $(document).off('click.layerVis', '.wppoppop-layer-btn-vis').on('click.layerVis', '.wppoppop-layer-btn-vis', function(e) {
                e.stopPropagation();
                var id = $(this).attr('data-id');
                self.toggleVisibility(id);
            });

            // 4. Duplicate button
            $(document).off('click.layerDup', '.wppoppop-layer-btn-duplicate').on('click.layerDup', '.wppoppop-layer-btn-duplicate', function(e) {
                e.stopPropagation();
                var id = $(this).attr('data-id');
                self.duplicateLayer(id);
            });

            // 5. Delete button
            $(document).off('click.layerDel', '.wppoppop-layer-btn-del').on('click.layerDel', '.wppoppop-layer-btn-del', function(e) {
                e.stopPropagation();
                var id = $(this).attr('data-id');
                self.deleteLayer(id);
            });
        },

        highlightLayer: function(id) {
            $('.wppoppop-layer-item').removeClass('active');
            var $item = $('#layer-item-' + id);
            if ($item.length) {
                $item.addClass('active');
                var $container = $('#wppoppop-layers-list');
                if ($container.length) {
                    var itemTop = $item.position().top;
                    if (itemTop < 0 || itemTop > $container.height() - 30) {
                        $container.scrollTop($container.scrollTop() + itemTop - 10);
                    }
                }
            }
        },

        toggleLock: function(id) {
            var elements = window.WpPopPopBuilderCanvas.getActiveElements();
            var el = elements.find(function(e) { return String(e.id) === String(id); });
            if (!el) return;

            el.locked = !el.locked;

            var $node = $('#el-' + id);
            var $item = $('#layer-item-' + id);

            if (el.locked) {
                $node.addClass('wppoppop-locked');
                $item.addClass('is-locked');
                $item.find('.wppoppop-layer-btn-lock').attr('title', 'Unlock Layer')
                    .find('.dashicons').removeClass('dashicons-unlock').addClass('dashicons-lock');

                try {
                    $node.draggable('disable');
                    $node.resizable('disable');
                } catch(err) {}
            } else {
                $node.removeClass('wppoppop-locked');
                $item.removeClass('is-locked');
                $item.find('.wppoppop-layer-btn-lock').attr('title', 'Lock Layer')
                    .find('.dashicons').removeClass('dashicons-lock').addClass('dashicons-unlock');

                try {
                    $node.draggable('enable');
                    $node.resizable('enable');
                } catch(err) {}
            }

            if (window.WpPopPopBuilderCore) {
                window.WpPopPopBuilderCore.pushHistory();
            }
        },

        toggleVisibility: function(id) {
            var elements = window.WpPopPopBuilderCanvas.getActiveElements();
            var el = elements.find(function(e) { return String(e.id) === String(id); });
            if (!el) return;

            el.hidden = !el.hidden;

            var $node = $('#el-' + id);
            var $item = $('#layer-item-' + id);

            if (el.hidden) {
                $node.css('display', 'none');
                $item.addClass('is-hidden');
                $item.find('.wppoppop-layer-btn-vis').attr('title', 'Show Layer')
                    .find('.dashicons').removeClass('dashicons-visibility').addClass('dashicons-hidden');
            } else {
                $node.css('display', 'block');
                $item.removeClass('is-hidden');
                $item.find('.wppoppop-layer-btn-vis').attr('title', 'Hide Layer')
                    .find('.dashicons').removeClass('dashicons-hidden').addClass('dashicons-visibility');
            }

            if (window.WpPopPopBuilderCore) {
                window.WpPopPopBuilderCore.pushHistory();
            }
        },

        duplicateLayer: function(id) {
            var elements = window.WpPopPopBuilderCanvas.getActiveElements();
            var el = elements.find(function(e) { return String(e.id) === String(id); });
            if (!el) return;

            var clone = JSON.parse(JSON.stringify(el));
            var newId = 'layer_' + Date.now().toString(36) + '_' + Math.random().toString(36).substr(2, 4);
            clone.id = newId;

            var baseLabel = (el.type === 'text' || el.type === 'paragraph') ? 'PARAGRAPH' : (el.name || el.type.toUpperCase());
            clone.name = baseLabel + ' (Copy)';

            clone.top = (parseInt(el.top, 10) || 0) + 15;
            clone.left = (parseInt(el.left, 10) || 0) + 15;

            var maxZ = elements.reduce(function(max, item) {
                return Math.max(max, parseInt(item.zIndex, 10) || 10);
            }, 10);
            clone.zIndex = maxZ + 5;

            elements.push(clone);

            window.WpPopPopBuilderCanvas.renderCanvas();
            this.renderLayers();

            window.WpPopPopBuilderCanvas.selectElement(newId);

            if (window.WpPopPopBuilderCore) {
                window.WpPopPopBuilderCore.pushHistory();
            }
        },

        deleteLayer: function(id) {
            var elements = window.WpPopPopBuilderCanvas.getActiveElements();
            var idx = elements.findIndex(function(e) { return String(e.id) === String(id); });
            if (idx === -1) return;

            elements.splice(idx, 1);

            if (window.WpPopPopBuilderCore && window.WpPopPopBuilderCore.state.activeId === id) {
                window.WpPopPopBuilderCanvas.deselect();
            }

            window.WpPopPopBuilderCanvas.renderCanvas();
            this.renderLayers();

            if (window.WpPopPopBuilderCore) {
                window.WpPopPopBuilderCore.pushHistory();
            }
        },

        escapeHtml: function(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }
    };

    window.WpPopPopBuilder = window.WpPopPopBuilder || {};
    window.WpPopPopBuilder.Layers = window.WpPopPopBuilderLayers;
})(jQuery);
