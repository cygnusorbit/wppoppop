/**
 * WpPopPop Visual Builder: Core State Machine & Sequence Controller
 * Responsive Viewport Synchronization, Multi-Canvas Tabs & History Stack
 */
(function($) {
    'use strict';

    window.WpPopPopBuilderCore = {
        state: {
            currentCanvas: 1,
            get currentScreen() { return this.currentCanvas; },
            set currentScreen(v) { this.currentCanvas = parseInt(v, 10) || 1; },
            canvases: { 1: [], 2: [] },
            canvasMeta: {
                1: { name: 'Canvas 1', width: 640, height: 400, bg_mode: 'solid', bg_color: '#ffffff', grad_color1: '#3b82f6', grad_color2: '#1d4ed8', grad_angle: 135, logic_enabled: false },
                2: { name: 'Canvas 2', width: 640, height: 400, bg_mode: 'solid', bg_color: '#ffffff', grad_color1: '#3b82f6', grad_color2: '#1d4ed8', grad_angle: 135, logic_enabled: false }
            },
            get screens() { return this.canvases; },
            set screens(v) { this.canvases = v || { 1: [], 2: [] }; },
            activeId: null,
            viewport: 'desktop',
            history: [],
            historyIndex: -1
        },

        init: function() {
            this.bindCanvasTabs();
            this.bindViewportToggles();
            this.bindKeyboardShortcuts();
            this.bindWindowResize();
            this.renderCanvasTabs();
            this.pushHistory();
        },

        bindWindowResize: function() {
            var resizeTimer = null;
            $(window).on('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    if (window.WpPopPopBuilderLayers && $('#wppoppop-floating-layers-panel').length) {
                        window.WpPopPopBuilderLayers.bindPanelDragging();
                    }
                }, 150);
            });
        },

        renderCanvasTabs: function() {
            var self = this;
            var $container = $('#wppoppop-canvas-tabs-container');
            if ($container.length === 0) return;
            $container.empty();

            var canvasKeys = Object.keys(this.state.canvases).map(Number).sort(function(a, b) { return a - b; });
            if (canvasKeys.length === 0) canvasKeys = [1, 2];
            var canDelete = canvasKeys.length > 1;

            canvasKeys.forEach(function(cNum) {
                var meta = self.state.canvasMeta[cNum] || { name: 'Canvas ' + cNum };
                var name = meta.name || ('Canvas ' + cNum);
                var isActive = (cNum === self.state.currentCanvas);

                var $wrapper = $('<div></div>')
                    .addClass('wppoppop-canvas-tab-wrapper')
                    .toggleClass('active', isActive);

                var $btn = $('<button type="button" class="wppoppop-canvas-tab wppoppop-screen-tab"></button>')
                    .attr('data-canvas', cNum)
                    .attr('data-screen', cNum)
                    .text(name);

                $wrapper.append($btn);

                if (canDelete) {
                    var $delBtn = $('<span class="wppoppop-tab-delete-canvas" title="Delete Canvas">&times;</span>')
                        .attr('data-canvas', cNum);
                    $wrapper.append($delBtn);
                }

                $container.append($wrapper);
            });

            var $addBtn = $('<button type="button" id="wppoppop-add-canvas-btn" class="wppoppop-canvas-add-btn" title="Add Canvas">+</button>');
            $container.append($addBtn);
        },

        bindCanvasTabs: function() {
            var self = this;

            // Clicking top Canvas button switches canvas AND opens Campaign Settings drawer
            $(document).on('click', '.wppoppop-canvas-tab, .wppoppop-screen-tab', function(e) {
                e.preventDefault();
                var targetCanvas = $(this).data('canvas') || $(this).data('screen') || 1;
                self.switchCanvas(targetCanvas);
                if (window.WpPopPopBuilderSettings) {
                    window.WpPopPopBuilderSettings.openDrawer();
                }
            });

            $(document).on('click', '.wppoppop-tab-delete-canvas', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var cNum = $(this).data('canvas');
                self.deleteCanvas(cNum);
            });

            $(document).on('click', '#wppoppop-add-canvas-btn', function(e) {
                e.preventDefault();
                self.addNewCanvas();
                if (window.WpPopPopBuilderSettings) {
                    window.WpPopPopBuilderSettings.openDrawer();
                }
            });
        },

        deleteCanvas: function(cNum) {
            cNum = parseInt(cNum, 10);
            var keys = Object.keys(this.state.canvases).map(Number).sort(function(a, b) { return a - b; });

            if (keys.length <= 1) {
                alert('Your popup campaign must have at least one canvas.');
                return;
            }

            var meta = this.state.canvasMeta[cNum] || { name: 'Canvas ' + cNum };
            var canvasName = meta.name || ('Canvas ' + cNum);

            if (!window.confirm('Are you sure you want to delete "' + canvasName + '"? All elements on this canvas will be permanently removed.')) {
                return;
            }

            delete this.state.canvases[cNum];
            delete this.state.canvasMeta[cNum];

            var remainingKeys = Object.keys(this.state.canvases).map(Number).sort(function(a, b) { return a - b; });
            var nextActive = remainingKeys.indexOf(cNum) !== -1 ? cNum : remainingKeys[0];

            this.renderCanvasTabs();
            this.switchCanvas(nextActive);
            if (window.WpPopPopBuilderSettings) {
                window.WpPopPopBuilderSettings.renderCanvasBullets();
            }
            this.pushHistory();
        },

        addNewCanvas: function() {
            var canvasKeys = Object.keys(this.state.canvases).map(Number);
            var nextNum = canvasKeys.length ? Math.max.apply(null, canvasKeys) + 1 : 3;

            this.state.canvases[nextNum] = [];
            this.state.canvasMeta[nextNum] = {
                name: 'Canvas ' + nextNum,
                width: 640,
                height: 400,
                bg_mode: 'solid',
                bg_color: '#ffffff',
                grad_color1: '#3b82f6',
                grad_color2: '#1d4ed8',
                grad_angle: 135,
                logic_enabled: false
            };

            this.renderCanvasTabs();
            this.switchCanvas(nextNum);
            if (window.WpPopPopBuilderSettings) {
                window.WpPopPopBuilderSettings.renderCanvasBullets();
            }
            this.pushHistory();
        },

        switchCanvas: function(canvasNum) {
            canvasNum = parseInt(canvasNum, 10) || 1;
            this.state.currentCanvas = canvasNum;
            this.state.activeId = null;

            if (!this.state.canvases[canvasNum]) {
                this.state.canvases[canvasNum] = [];
            }
            if (!this.state.canvasMeta[canvasNum]) {
                this.state.canvasMeta[canvasNum] = {
                    name: 'Canvas ' + canvasNum,
                    width: 640,
                    height: 400,
                    bg_mode: 'solid',
                    bg_color: '#ffffff',
                    grad_color1: '#3b82f6',
                    grad_color2: '#1d4ed8',
                    grad_angle: 135,
                    logic_enabled: false
                };
            }

            var meta = this.state.canvasMeta[canvasNum];

            // 1. Update Tab Highlights
            $('.wppoppop-canvas-tab-wrapper').removeClass('active');
            $('.wppoppop-canvas-tab-wrapper:has([data-canvas="' + canvasNum + '"])').addClass('active');

            // 2. Restore Dedicated Dimensions for this Canvas
            if (this.state.viewport === 'desktop') {
                var w = meta.width || 640;
                var h = meta.height || 400;
                $('#wppoppop-canvas-box').css({ width: w + 'px', height: h + 'px' });
                $('#quick-box-width, #set-canvas-width').val(w);
                $('#quick-box-height, #set-canvas-height').val(h);
            }

            // 3. Restore Dedicated Background Fill for this Canvas
            if (window.WpPopPopBuilderSettings) {
                window.WpPopPopBuilderSettings.applyActiveCanvasBackground();
                window.WpPopPopBuilderSettings.syncCurrentCanvasSettings();
            }

            if (window.WpPopPopBuilderCanvas) {
                window.WpPopPopBuilderCanvas.renderCanvas();
            }
            if (window.WpPopPopBuilderLayers) {
                window.WpPopPopBuilderLayers.renderLayers();
            }
            if (window.WpPopPopBuilderInspector) {
                window.WpPopPopBuilderInspector.close();
            }
        },

        switchScreen: function(screenNum) {
            this.switchCanvas(screenNum);
        },

        bindViewportToggles: function() {
            var self = this;
            $(document).on('click', '.wppoppop-viewport-btn', function(e) {
                e.preventDefault();
                var mode = $(this).data('mode');
                self.setViewport(mode);
            });
        },

        setViewport: function(mode) {
            this.state.viewport = mode;
            $('.wppoppop-viewport-btn').removeClass('active');
            $('.wppoppop-viewport-btn[data-mode="' + mode + '"]').addClass('active');

            var $box = $('#wppoppop-canvas-box');
            if (mode === 'mobile') {
                $box.css({ width: '360px', height: '540px' });
            } else {
                var cur = this.state.currentCanvas || 1;
                var meta = this.state.canvasMeta[cur] || { width: 640, height: 400 };
                var w = meta.width || 640;
                var h = meta.height || 400;
                $box.css({ width: w + 'px', height: h + 'px' });
            }
        },

        pushHistory: function() {
            var snapshot = JSON.stringify({
                canvases: this.state.canvases,
                canvasMeta: this.state.canvasMeta
            });
            if (this.state.history.length > 0 && this.state.history[this.state.historyIndex] === snapshot) {
                return;
            }

            if (this.state.historyIndex < this.state.history.length - 1) {
                this.state.history = this.state.history.slice(0, this.state.historyIndex + 1);
            }

            this.state.history.push(snapshot);
            if (this.state.history.length > 25) {
                this.state.history.shift();
            }
            this.state.historyIndex = this.state.history.length - 1;
        },

        undo: function() {
            if (this.state.historyIndex > 0) {
                this.state.historyIndex--;
                this.restoreSnapshot(this.state.history[this.state.historyIndex]);
            }
        },

        redo: function() {
            if (this.state.historyIndex < this.state.history.length - 1) {
                this.state.historyIndex++;
                this.restoreSnapshot(this.state.history[this.state.historyIndex]);
            }
        },

        restoreSnapshot: function(snapshotStr) {
            try {
                var parsed = JSON.parse(snapshotStr);
                this.state.canvases = parsed.canvases || {};
                this.state.canvasMeta = parsed.canvasMeta || {};
                this.renderCanvasTabs();
                this.switchCanvas(this.state.currentCanvas);
            } catch(e) {}
        },

        bindKeyboardShortcuts: function() {
            var self = this;
            $(document).on('keydown', function(e) {
                if ($(e.target).is('input, textarea, select')) {
                    return;
                }
                if ((e.metaKey || e.ctrlKey) && e.which === 90 && !e.shiftKey) {
                    e.preventDefault();
                    self.undo();
                }
                if (((e.metaKey || e.ctrlKey) && e.which === 89) || ((e.metaKey || e.ctrlKey) && e.shiftKey && e.which === 90)) {
                    e.preventDefault();
                    self.redo();
                }
                if ((e.which === 46 || e.which === 8) && self.state.activeId) {
                    e.preventDefault();
                    if (window.WpPopPopBuilderLayers) {
                        window.WpPopPopBuilderLayers.deleteLayer(self.state.activeId);
                    }
                }
            });
        }
    };
})(jQuery);
