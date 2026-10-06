(function(window, $) {
    'use strict';
    window.WpPopPopBuilder = window.WpPopPopBuilder || {};

    var Core = {
        elements: [],
        activeId: null,
        currentScreen: 1,
        viewport: 'desktop',
        history: [],
        historyIndex: -1,
        config: {},
        isDirty: false,

        init: function(initialConfig) {
            this.config = initialConfig || {};
            this.elements = Array.isArray(initialConfig.elements) ? initialConfig.elements : [];
            this.pushHistory();
            this.bindHotkeys();
            this.bindTopControls();
            this.bindRevisionButtons();
        },

        bindHotkeys: function() {
            var self = this;
            $(document).on('keydown', function(e) {
                // Intercept Cmd/Ctrl+S for Save
                if ((e.metaKey || e.ctrlKey) && e.key === 's') {
                    e.preventDefault();
                    $('#wppoppop-btn-save').trigger('click');
                    return;
                }

                // Ignore hotkeys when typing in form controls
                if ($(e.target).is('input, textarea, select') || $(e.target).is('[contenteditable="true"]')) {
                    return;
                }

                // Undo: Cmd/Ctrl+Z
                if ((e.metaKey || e.ctrlKey) && e.key === 'z' && !e.shiftKey) {
                    e.preventDefault();
                    self.undo();
                    return;
                }

                // Redo: Cmd/Ctrl+Y or Cmd/Ctrl+Shift+Z
                if ((e.metaKey || e.ctrlKey) && (e.key === 'y' || (e.shiftKey && e.key === 'z'))) {
                    e.preventDefault();
                    self.redo();
                    return;
                }

                // Delete active element
                if ((e.key === 'Delete' || e.key === 'Backspace') && self.activeId) {
                    e.preventDefault();
                    self.removeElement(self.activeId);
                    return;
                }

                // Keyboard Arrow Nudging (1px, 10px with Shift)
                if (self.activeId && ['ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight'].indexOf(e.key) !== -1) {
                    e.preventDefault();
                    var el = self.getElementById(self.activeId);
                    if (!el || el.locked) return;

                    var step = e.shiftKey ? 10 : 1;
                    var newTop = el.top || 0;
                    var newLeft = el.left || 0;

                    if (e.key === 'ArrowUp') newTop = Math.max(0, newTop - step);
                    if (e.key === 'ArrowDown') newTop = newTop + step;
                    if (e.key === 'ArrowLeft') newLeft = Math.max(0, newLeft - step);
                    if (e.key === 'ArrowRight') newLeft = newLeft + step;

                    self.updateElement(self.activeId, { top: newTop, left: newLeft });
                }
            });

            // Prevent accidental tab closure if unsaved
            window.addEventListener('beforeunload', function(e) {
                if (self.isDirty) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });
        },

        bindTopControls: function() {
            var self = this;
            $('.wppoppop-screen-tab').on('click', function() {
                var screen = parseInt($(this).data('screen'), 10) || 1;
                self.setScreen(screen);
            });

            $('.wppoppop-viewport-btn').on('click', function() {
                var mode = $(this).data('mode');
                self.setViewport(mode);
            });
        },

        bindRevisionButtons: function() {
            var self = this;
            $('#wppoppop-btn-undo').on('click', function() {
                self.undo();
            });
            $('#wppoppop-btn-redo').on('click', function() {
                self.redo();
            });
        },

        setScreen: function(screenNum) {
            this.currentScreen = screenNum;
            $('.wppoppop-screen-tab').removeClass('active');
            $('.wppoppop-screen-tab[data-screen="' + screenNum + '"]').addClass('active');
            this.activeId = null;
            $(document).trigger('builder:screen:change', [screenNum]);
        },

        setViewport: function(mode) {
            this.viewport = mode;
            $('.wppoppop-viewport-btn').removeClass('active');
            $('.wppoppop-viewport-btn[data-mode="' + mode + '"]').addClass('active');
            var width = (mode === 'mobile') ? '360px' : ($('#set-box-width').val() ? $('#set-box-width').val() + 'px' : '640px');
            $('#wppoppop-canvas-box').css('width', width);
        },

        addElement: function(elementData) {
            this.elements.push(elementData);
            this.activeId = elementData.id;
            this.isDirty = true;
            this.pushHistory();
            $(document).trigger('builder:elements:updated');
            $(document).trigger('builder:element:selected', [elementData.id]);
        },

        updateElement: function(id, props) {
            var el = this.getElementById(id);
            if (el) {
                Object.assign(el, props);
                this.isDirty = true;
                $(document).trigger('builder:element:modified', [el]);
            }
        },

        removeElement: function(id) {
            this.elements = this.elements.filter(function(e) { return e.id !== id; });
            if (this.activeId === id) this.activeId = null;
            this.isDirty = true;
            this.pushHistory();
            $(document).trigger('builder:elements:updated');
            $(document).trigger('builder:element:deselected');
        },

        selectElement: function(id) {
            this.activeId = id;
            $(document).trigger('builder:element:selected', [id]);
        },

        getElementById: function(id) {
            return this.elements.find(function(e) { return e.id === id; });
        },

        reorderElements: function(newIdOrder) {
            this.elements.sort(function(a, b) {
                var indexA = newIdOrder.indexOf(a.id);
                var indexB = newIdOrder.indexOf(b.id);
                if (indexA === -1) return 1;
                if (indexB === -1) return -1;
                return indexA - indexB;
            });
            this.isDirty = true;
            this.pushHistory();
            $(document).trigger('builder:elements:updated');
        },

        pushHistory: function() {
            if (this.historyIndex < this.history.length - 1) {
                this.history = this.history.slice(0, this.historyIndex + 1);
            }
            this.history.push(JSON.stringify(this.elements));
            if (this.history.length > 30) this.history.shift();
            this.historyIndex = this.history.length - 1;
            this.updateRevisionButtonsState();
        },

        undo: function() {
            if (this.historyIndex > 0) {
                this.historyIndex--;
                this.elements = JSON.parse(this.history[this.historyIndex]);
                this.activeId = null;
                this.isDirty = true;
                this.updateRevisionButtonsState();
                $(document).trigger('builder:elements:updated');
                $(document).trigger('builder:element:deselected');
            }
        },

        redo: function() {
            if (this.historyIndex < this.history.length - 1) {
                this.historyIndex++;
                this.elements = JSON.parse(this.history[this.historyIndex]);
                this.activeId = null;
                this.isDirty = true;
                this.updateRevisionButtonsState();
                $(document).trigger('builder:elements:updated');
                $(document).trigger('builder:element:deselected');
            }
        },

        updateRevisionButtonsState: function() {
            var canUndo = this.historyIndex > 0;
            var canRedo = this.historyIndex < this.history.length - 1;

            $('#wppoppop-btn-undo').prop('disabled', !canUndo).css('opacity', canUndo ? '1' : '0.4');
            $('#wppoppop-btn-redo').prop('disabled', !canRedo).css('opacity', canRedo ? '1' : '0.4');
        }
    };

    window.WpPopPopBuilder.Core = Core;
})(window, jQuery);
