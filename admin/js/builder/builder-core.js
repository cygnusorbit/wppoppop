(function(window, $) {
    'use strict';
    window.WpPopPopBuilder = window.WpPopPopBuilder || {};

    var Core = {
        state: {
            uid: '',
            title: 'Untitled Popup Campaign',
            currentScreen: 1,
            viewport: 'desktop',
            activeId: null,
            elements: [],
            settings: {}
        },

        history: [],
        historyIndex: -1,
        maxHistory: 25,

        init: function() {
            var vars = window.wppoppop_vars || {};
            this.state.uid = vars.current_uid || '';
            this.bindScreenTabs();
            this.bindViewportSwitcher();
            this.bindKeyboardShortcuts();
        },

        recordHistory: function() {
            if (this.historyIndex < this.history.length - 1) {
                this.history = this.history.slice(0, this.historyIndex + 1);
            }
            this.history.push(JSON.stringify(this.state.elements));
            if (this.history.length > this.maxHistory) {
                this.history.shift();
            } else {
                this.historyIndex++;
            }
        },

        undo: function() {
            if (this.historyIndex > 0) {
                this.historyIndex--;
                this.state.elements = JSON.parse(this.history[this.historyIndex]);
                if (window.WpPopPopBuilder.Canvas) window.WpPopPopBuilder.Canvas.renderElements();
                if (window.WpPopPopBuilder.Layers) window.WpPopPopBuilder.Layers.renderList();
            }
        },

        redo: function() {
            if (this.historyIndex < this.history.length - 1) {
                this.historyIndex++;
                this.state.elements = JSON.parse(this.history[this.historyIndex]);
                if (window.WpPopPopBuilder.Canvas) window.WpPopPopBuilder.Canvas.renderElements();
                if (window.WpPopPopBuilder.Layers) window.WpPopPopBuilder.Layers.renderList();
            }
        },

        bindKeyboardShortcuts: function() {
            var self = this;
            $(document).on('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.key === 'z') {
                    e.preventDefault();
                    if (e.shiftKey) {
                        self.redo();
                    } else {
                        self.undo();
                    }
                } else if ((e.ctrlKey || e.metaKey) && e.key === 'y') {
                    e.preventDefault();
                    self.redo();
                }
            });
        },

        bindScreenTabs: function() {
            var self = this;
            $('.wppoppop-screen-tab').on('click', function() {
                var screenNum = parseInt($(this).data('screen'), 10) || 1;
                self.state.currentScreen = screenNum;

                $('.wppoppop-screen-tab').removeClass('active').css({ background: 'transparent', color: '#9ca3af' });
                $(this).addClass('active').css({ background: '#2563eb', color: '#ffffff' });

                if (window.WpPopPopBuilder.Canvas) window.WpPopPopBuilder.Canvas.filterByScreen();
                if (window.WpPopPopBuilder.Layers) window.WpPopPopBuilder.Layers.renderList();
            });
        },

        bindViewportSwitcher: function() {
            var self = this;
            $('.wppoppop-viewport-btn').on('click', function() {
                var mode = $(this).data('mode');
                self.state.viewport = mode;

                $('.wppoppop-viewport-btn').removeClass('active').css({ background: 'transparent', color: '#9ca3af' });
                $(this).addClass('active').css({ background: '#374151', color: '#ffffff' });

                var $box = $('#wppoppop-canvas-box');
                if (mode === 'mobile') {
                    $box.css({ width: '360px', height: '540px' });
                } else {
                    var w = $('#set-box-width').val() || 640;
                    var h = $('#set-box-height').val() || 400;
                    $box.css({ width: w + 'px', height: h + 'px' });
                }
            });
        },

        getElementById: function(id) {
            for (var i = 0; i < this.state.elements.length; i++) {
                if (this.state.elements[i].id === id) return this.state.elements[i];
            }
            return null;
        }
    };

    window.WpPopPopBuilder.Core = Core;
})(window, jQuery);
