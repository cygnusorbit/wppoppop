(function(window, $) {
    'use strict';
    window.WpPopPopBuilder = window.WpPopPopBuilder || {};

    var Core = {
        state: {
            uid: (window.wppoppop_vars && window.wppoppop_vars.current_uid) || '',
            currentScreen: 1,
            viewport: 'desktop',
            activeId: null,
            elements: [],
            config: {
                meta: { width: 640, height: 400, bgMode: 'solid', bgColor: '#ffffff', gradColor1: '#3b82f6', gradColor2: '#1d4ed8', gradAngle: 135 },
                triggers: { onLoad: false, onLoadDelay: 0, onExit: false, onScroll: false, scrollVal: 50, onAdblock: false, onBackButton: false },
                logic: { formula: '', targetId: '' },
                sideTab: { enable: false, label: '', pos: 'left' },
                payment: { gateway: 'stripe', amount: 0 },
                downloads: { enable: false, url: '' },
                video: { enable: false, time: 0 },
                autoresponder: { enable: false, subject: '', body: '' },
                marketing: { webhookUrl: '', webhookSecret: '' },
                twilio: { enable: false, phone: '' },
                targeting: { auth: 'all' },
                frequency: { mode: 'always' },
                woocommerce: { enableCoupon: false, couponAmount: '' },
                customCode: { css: '', js: '' },
                quiz: { enable: false, passScore: 0, confetti: false }
            }
        },

        undoStack: [],
        redoStack: [],

        init: function() {
            this.bindScreenTabs();
            this.bindViewportToggles();
            this.bindKeyboardShortcuts();
        },

        getElementById: function(id) {
            return this.state.elements.find(function(el) { return el.id === id; });
        },

        recordHistory: function() {
            var snapshot = JSON.stringify({ elements: this.state.elements, config: this.state.config });
            if (this.undoStack.length >= 25) {
                this.undoStack.shift();
            }
            this.undoStack.push(snapshot);
            this.redoStack = [];
        },

        undo: function() {
            if (!this.undoStack.length) return;
            var current = JSON.stringify({ elements: this.state.elements, config: this.state.config });
            this.redoStack.push(current);
            var prev = JSON.parse(this.undoStack.pop());
            this.state.elements = prev.elements || [];
            this.state.config = prev.config || this.state.config;
            this.refreshWorkspace();
        },

        redo: function() {
            if (!this.redoStack.length) return;
            var current = JSON.stringify({ elements: this.state.elements, config: this.state.config });
            this.undoStack.push(current);
            var next = JSON.parse(this.redoStack.pop());
            this.state.elements = next.elements || [];
            this.state.config = next.config || this.state.config;
            this.refreshWorkspace();
        },

        refreshWorkspace: function() {
            if (window.WpPopPopBuilder.Canvas) {
                window.WpPopPopBuilder.Canvas.renderElements();
            }
            if (window.WpPopPopBuilder.Layers) {
                window.WpPopPopBuilder.Layers.renderList();
            }
            if (window.WpPopPopBuilder.Inspector) {
                if (this.state.activeId) {
                    var el = this.getElementById(this.state.activeId);
                    if (el) window.WpPopPopBuilder.Inspector.open(el);
                    else window.WpPopPopBuilder.Inspector.close();
                } else {
                    window.WpPopPopBuilder.Inspector.close();
                }
            }
        },

        bindScreenTabs: function() {
            var self = this;
            $(document).on('click', '.wppoppop-screen-tab', function(e) {
                e.preventDefault();
                $('.wppoppop-screen-tab').removeClass('active').css({ background: 'transparent', color: '#9ca3af' });
                $(this).addClass('active').css({ background: '#2563eb', color: '#ffffff' });
                self.state.currentScreen = parseInt($(this).data('screen'), 10) || 1;
                self.state.activeId = null;
                if (window.WpPopPopBuilder.Inspector) {
                    window.WpPopPopBuilder.Inspector.close();
                }
                if (window.WpPopPopBuilder.Canvas) {
                    window.WpPopPopBuilder.Canvas.renderElements();
                }
                if (window.WpPopPopBuilder.Layers) {
                    window.WpPopPopBuilder.Layers.renderList();
                }
            });
        },

        bindViewportToggles: function() {
            var self = this;
            $(document).on('click', '.wppoppop-viewport-btn', function(e) {
                e.preventDefault();
                $('.wppoppop-viewport-btn').removeClass('active').css({ background: 'transparent', color: '#9ca3af' });
                $(this).addClass('active').css({ background: '#374151', color: '#ffffff' });
                var mode = $(this).data('mode');
                self.state.viewport = mode;
                var $box = $('#wppoppop-canvas-box');
                if (mode === 'mobile') {
                    $box.css({ width: '360px', height: '560px' });
                } else {
                    var w = (self.state.config.meta && self.state.config.meta.width) || 640;
                    var h = (self.state.config.meta && self.state.config.meta.height) || 400;
                    $box.css({ width: w + 'px', height: h + 'px' });
                }
            });
        },

        bindKeyboardShortcuts: function() {
            var self = this;
            $(document).on('keydown', function(e) {
                if ($(e.target).is('input, textarea, select')) return;

                if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'z') {
                    e.preventDefault();
                    if (e.shiftKey) self.redo();
                    else self.undo();
                } else if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'y') {
                    e.preventDefault();
                    self.redo();
                } else if (e.key === 'Delete' || e.key === 'Backspace') {
                    if (self.state.activeId) {
                        e.preventDefault();
                        self.deleteElement(self.state.activeId);
                    }
                }
            });
        },

        deleteElement: function(id) {
            this.recordHistory();
            this.state.elements = this.state.elements.filter(function(el) { return el.id !== id; });
            if (this.state.activeId === id) {
                this.state.activeId = null;
                if (window.WpPopPopBuilder.Inspector) window.WpPopPopBuilder.Inspector.close();
            }
            if (window.WpPopPopBuilder.Canvas) window.WpPopPopBuilder.Canvas.renderElements();
            if (window.WpPopPopBuilder.Layers) window.WpPopPopBuilder.Layers.renderList();
        }
    };

    window.WpPopPopBuilder.Core = Core;
})(window, jQuery);
