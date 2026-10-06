(function(window, $) {
    'use strict';
    window.WpPopPopBuilder = window.WpPopPopBuilder || {};

    var Core = {
        elements: [],
        screens: [],
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
            
            // Initialize Screens Array (Default to Screen 1, Screen 2, Screen 3 if not present)
            if (Array.isArray(initialConfig.screens) && initialConfig.screens.length > 0) {
                this.screens = initialConfig.screens;
            } else {
                var defaultW = (initialConfig.settings && initialConfig.settings.width) ? initialConfig.settings.width : 640;
                var defaultH = (initialConfig.settings && initialConfig.settings.height) ? initialConfig.settings.height : 400;
                this.screens = [
                    { id: 1, title: 'Screen 1', width: defaultW, height: defaultH },
                    { id: 2, title: 'Screen 2', width: defaultW, height: defaultH },
                    { id: 3, title: 'Screen 3', width: defaultW, height: defaultH }
                ];
            }

            this.currentScreen = this.screens[0].id;
            this.renderScreenTabs();
            this.pushHistory();
            this.bindHotkeys();
            this.bindTopControls();
            this.bindRevisionButtons();
            this.applyCurrentScreenDimensions();
        },

        bindHotkeys: function() {
            var self = this;
            $(document).on('keydown', function(e) {
                if ((e.metaKey || e.ctrlKey) && e.key === 's') {
                    e.preventDefault();
                    $('#wppoppop-btn-save').trigger('click');
                    return;
                }

                if ($(e.target).is('input, textarea, select') || $(e.target).is('[contenteditable="true"]')) {
                    return;
                }

                if ((e.metaKey || e.ctrlKey) && e.key === 'z' && !e.shiftKey) {
                    e.preventDefault();
                    self.undo();
                    return;
                }

                if ((e.metaKey || e.ctrlKey) && (e.key === 'y' || (e.shiftKey && e.key === 'z'))) {
                    e.preventDefault();
                    self.redo();
                    return;
                }

                if ((e.key === 'Delete' || e.key === 'Backspace') && self.activeId) {
                    e.preventDefault();
                    self.removeElement(self.activeId);
                    return;
                }

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

            window.addEventListener('beforeunload', function(e) {
                if (self.isDirty) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });
        },

        bindTopControls: function() {
            var self = this;

            // Viewport switching
            $('.wppoppop-viewport-btn').on('click', function() {
                var mode = $(this).data('mode');
                self.setViewport(mode);
            });

            // Add Screen Button
            $(document).on('click', '#wppoppop-btn-add-screen', function(e) {
                e.preventDefault();
                self.addScreen();
            });

            // Screen Tab Click Selection
            $(document).on('click', '.wppoppop-screen-tab', function(e) {
                if ($(e.target).hasClass('wppoppop-screen-tab-close') || $(e.target).is('input')) return;
                var sId = parseInt($(this).data('screen'), 10);
                self.setScreen(sId);
            });

            // Screen Tab Delete
            $(document).on('click', '.wppoppop-screen-tab-close', function(e) {
                e.stopPropagation();
                var sId = parseInt($(this).closest('.wppoppop-screen-tab').data('screen'), 10);
                self.removeScreen(sId);
            });

            // Screen Tab Inline Rename on Double Click
            $(document).on('dblclick', '.wppoppop-screen-tab-title', function(e) {
                e.stopPropagation();
                var $titleSpan = $(this);
                var $tab = $titleSpan.closest('.wppoppop-screen-tab');
                var sId = parseInt($tab.data('screen'), 10);
                var currentText = $titleSpan.text().trim();

                var $input = $('<input type="text" class="wppoppop-screen-tab-input">')
                    .val(currentText)
                    .on('blur keydown', function(ev) {
                        if (ev.type === 'blur' || ev.key === 'Enter') {
                            var newName = $(this).val().trim() || currentText;
                            self.renameScreen(sId, newName);
                        } else if (ev.key === 'Escape') {
                            self.renderScreenTabs();
                        }
                    });

                $titleSpan.replaceWith($input);
                $input.focus().select();
            });
        },

        renderScreenTabs: function() {
            var self = this;
            var $list = $('#wppoppop-screen-tabs-list');
            $list.empty();

            this.screens.forEach(function(sc) {
                var isActive = (sc.id === self.currentScreen);
                var $tab = $('<div></div>')
                    .addClass('wppoppop-screen-tab' + (isActive ? ' active' : ''))
                    .attr('data-screen', sc.id)
                    .html(
                        '<span class="wppoppop-screen-tab-title" title="Double click to rename">' + sc.title + '</span>' +
                        (self.screens.length > 1 ? '<span class="dashicons dashicons-no-alt wppoppop-screen-tab-close" title="Delete screen"></span>' : '')
                    );
                $list.append($tab);
            });

            $(document).trigger('builder:screens:rendered', [this.screens]);
        },

        addScreen: function() {
            var maxId = 0;
            this.screens.forEach(function(s) { if (s.id > maxId) maxId = s.id; });
            var newId = maxId + 1;
            var activeSc = this.getCurrentScreenObj();
            var newScreen = {
                id: newId,
                title: 'Screen ' + newId,
                width: activeSc ? activeSc.width : 640,
                height: activeSc ? activeSc.height : 400
            };

            this.screens.push(newScreen);
            this.currentScreen = newId;
            this.isDirty = true;
            this.renderScreenTabs();
            this.applyCurrentScreenDimensions();
            this.pushHistory();
            $(document).trigger('builder:screen:change', [newId]);
        },

        removeScreen: function(sId) {
            if (this.screens.length <= 1) return;
            if (!confirm('Are you sure you want to delete this screen and its layers?')) return;

            this.screens = this.screens.filter(function(s) { return s.id !== sId; });
            this.elements = this.elements.filter(function(e) { return e.screen !== sId; });

            if (this.currentScreen === sId) {
                this.currentScreen = this.screens[0].id;
            }

            this.isDirty = true;
            this.renderScreenTabs();
            this.applyCurrentScreenDimensions();
            this.pushHistory();
            $(document).trigger('builder:screen:change', [this.currentScreen]);
        },

        renameScreen: function(sId, newTitle) {
            var sc = this.screens.find(function(s) { return s.id === sId; });
            if (sc) {
                sc.title = newTitle;
                this.isDirty = true;
                this.renderScreenTabs();
                this.pushHistory();
            }
        },

        setScreen: function(sId) {
            this.currentScreen = sId;
            this.activeId = null;
            this.renderScreenTabs();
            this.applyCurrentScreenDimensions();
            $(document).trigger('builder:screen:change', [sId]);
            $(document).trigger('builder:element:deselected');
        },

        getCurrentScreenObj: function() {
            var self = this;
            return this.screens.find(function(s) { return s.id === self.currentScreen; }) || this.screens[0];
        },

        applyCurrentScreenDimensions: function() {
            var sc = this.getCurrentScreenObj();
            if (!sc) return;

            if (this.viewport === 'mobile') {
                $('#wppoppop-canvas-box').css({ width: '360px', height: (sc.height || 400) + 'px' });
            } else {
                $('#wppoppop-canvas-box').css({ width: (sc.width || 640) + 'px', height: (sc.height || 400) + 'px' });
            }

            // Sync with Settings Drawer Inputs
            $('#set-box-width').val(sc.width || 640);
            $('#set-box-height').val(sc.height || 400);
        },

        updateScreenDimensions: function(newW, newH) {
            var sc = this.getCurrentScreenObj();
            if (sc) {
                sc.width = newW;
                sc.height = newH;
                this.isDirty = true;
                this.applyCurrentScreenDimensions();
            }
        },

        setViewport: function(mode) {
            this.viewport = mode;
            $('.wppoppop-viewport-btn').removeClass('active');
            $('.wppoppop-viewport-btn[data-mode="' + mode + '"]').addClass('active');
            this.applyCurrentScreenDimensions();
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
            this.history.push(JSON.stringify({
                elements: this.elements,
                screens: this.screens
            }));
            if (this.history.length > 30) this.history.shift();
            this.historyIndex = this.history.length - 1;
            this.updateRevisionButtonsState();
        },

        undo: function() {
            if (this.historyIndex > 0) {
                this.historyIndex--;
                var snapshot = JSON.parse(this.history[this.historyIndex]);
                this.elements = snapshot.elements || [];
                if (snapshot.screens) this.screens = snapshot.screens;
                this.activeId = null;
                this.isDirty = true;
                this.renderScreenTabs();
                this.applyCurrentScreenDimensions();
                this.updateRevisionButtonsState();
                $(document).trigger('builder:elements:updated');
                $(document).trigger('builder:element:deselected');
            }
        },

        redo: function() {
            if (this.historyIndex < this.history.length - 1) {
                this.historyIndex++;
                var snapshot = JSON.parse(this.history[this.historyIndex]);
                this.elements = snapshot.elements || [];
                if (snapshot.screens) this.screens = snapshot.screens;
                this.activeId = null;
                this.isDirty = true;
                this.renderScreenTabs();
                this.applyCurrentScreenDimensions();
                this.updateRevisionButtonsState();
                $(document).trigger('builder:elements:updated');
                $(document).trigger('builder:element:deselected');
            }
        },

        bindRevisionButtons: function() {
            var self = this;
            $('#wppoppop-btn-undo').on('click', function() { self.undo(); });
            $('#wppoppop-btn-redo').on('click', function() { self.redo(); });
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
