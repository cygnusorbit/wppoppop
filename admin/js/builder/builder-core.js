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
                1: { name: 'Canvas 1', width: 300, height: 400, bg_mode: 'solid', bg_transparent: true, bg_color: 'transparent' },
                2: { name: 'Canvas 2', width: 300, height: 400, bg_mode: 'solid', bg_transparent: true, bg_color: 'transparent' }
            },
            get screens() { return this.canvases; },
            set screens(v) { this.canvases = v || { 1: [], 2: [] }; },
            activeId: null,
            viewport: 'desktop',
            history: [],
            historyIndex: -1
        },

        updateHistoryButtons: function() {
        var self = this;
        var hasUndo = self.historyIndex > 0;
        var hasRedo = self.history && self.historyIndex < (self.history.length - 1);
        $('#wppoppop-btn-undo').prop('disabled', !hasUndo);
        $('#wppoppop-btn-redo').prop('disabled', !hasRedo);
    },

    duplicateActiveCanvas: function() {
        var self = this;
        var srcNum = self.state.currentCanvas || 1;
        var existingCanvases = Object.keys(self.state.canvases || {}).map(Number);
        var maxNum = existingCanvases.length ? Math.max.apply(null, existingCanvases) : 1;
        var newNum = maxNum + 1;

        // Deep clone elements with fresh IDs
        var srcElements = (self.state.canvases && self.state.canvases[srcNum]) ? self.state.canvases[srcNum] : [];
        var clonedElements = srcElements.map(function(el) {
            var copy = JSON.parse(JSON.stringify(el));
            copy.id = 'el_' + Date.now().toString(36) + '_' + Math.random().toString(36).substr(2, 5);
            return copy;
        });

        self.state.canvases = self.state.canvases || {};
        self.state.canvases[newNum] = clonedElements;

        // Clone canvas meta
        self.state.canvasMeta = self.state.canvasMeta || {};
        if (self.state.canvasMeta[srcNum]) {
            self.state.canvasMeta[newNum] = JSON.parse(JSON.stringify(self.state.canvasMeta[srcNum]));
        } else {
            self.state.canvasMeta[newNum] = { bg_color: '#ffffff' };
        }

        if (typeof self.renderCanvasTabs === 'function') {
            self.renderCanvasTabs();
        }
        self.switchCanvas(newNum);
        if (typeof self.pushHistory === 'function') {
            self.pushHistory();
        }
    },

    openEmbedModal: function() {
        if (window.WpPopPopBuilderModals && typeof window.WpPopPopBuilderModals.openEmbed === 'function') {
            window.WpPopPopBuilderModals.openEmbed();
        }
    },

    copyEmbedSnippet: function($btn, targetId) {
        var $input = $('#' + targetId);
        if (!$input.length) return;
        $input.select();
        
        var text = $input.val();
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text);
        } else {
            document.execCommand('copy');
        }

        var origText = $btn.html();
        $btn.html('<span class="dashicons dashicons-yes"></span> Copied!').addClass('copied');
        setTimeout(function() {
            $btn.html(origText).removeClass('copied');
        }, 1800);
    },

    openSandboxPreview: function(device) {
        var self = this;
        var dev = device || 'desktop';
        var $modal = $('#wppoppop-modal-preview');
        var $viewport = $('#wppoppop-preview-viewport');
        var $root = $('#wppoppop-preview-render-root');
        
        $viewport.removeClass('device-desktop device-tablet device-mobile').addClass('device-' + dev);
        $('.wppoppop-device-btn').removeClass('active');
        $('.wppoppop-device-btn[data-device="' + dev + '"]').addClass('active');

        // Render clone of current canvas box into preview container
        var $sourceBox = $('#wppoppop-canvas-box');
        if ($sourceBox.length) {
            var $clone = $sourceBox.clone();
            $clone.find('.ui-resizable-handle, .wppoppop-selection-overlay').remove();
            $clone.css({
                'transform': 'none',
                'position': 'relative',
                'margin': '20px auto',
                'pointer-events': 'auto'
            });
            $root.empty().append($clone);
        }

        $modal.fadeIn(150);
    },

    bindModalEvents: function() {
        var self = this;

        // Settings Button Delegated Trigger
        $(document).off('click.wppoppop_core_settings', '#wppoppop-btn-settings, .wppoppop-btn-settings')
            .on('click.wppoppop_core_settings', '#wppoppop-btn-settings, .wppoppop-btn-settings', function(e) {
                e.preventDefault();
                if (window.WpPopPopBuilderSettings && typeof window.WpPopPopBuilderSettings.toggleDrawer === 'function') {
                    window.WpPopPopBuilderSettings.toggleDrawer();
                } else if (window.WpPopPopBuilderSettings && typeof window.WpPopPopBuilderSettings.openDrawer === 'function') {
                    window.WpPopPopBuilderSettings.openDrawer();
                }
            });

        var self = this;

        // Header Embed Button
        /* Embed button is bound once, in builder-modals.js (WpPopPopBuilderModals.bindEmbedModal) */

        // Header Live Sandbox Preview Button
        $('#wppoppop-btn-preview').off('click.wppoppop_preview_modal').on('click.wppoppop_preview_modal', function(e) {
            e.preventDefault();
            self.openSandboxPreview('desktop');
        });

        // Device Switcher
        $('.wppoppop-device-btn').off('click.wppoppop_device').on('click.wppoppop_device', function(e) {
            e.preventDefault();
            var dev = $(this).data('device');
            self.openSandboxPreview(dev);
        });

        // Universal Modal Close
        $('.wppoppop-modal-close, .wppoppop-modal-backdrop').off('click.wppoppop_close_modal').on('click.wppoppop_close_modal', function(e) {
            if ($(e.target).hasClass('wppoppop-modal-backdrop') || $(e.target).hasClass('wppoppop-modal-close')) {
                $('.wppoppop-modal-backdrop').fadeOut(120);
            }
        });
    },

    bindKeyboardShortcuts: function() {
        var self = this;
        $(document).off('keydown.wppoppop_shortcuts').on('keydown.wppoppop_shortcuts', function(e) {
            var tag = (e.target.tagName || '').toLowerCase();
            var isInput = tag === 'input' || tag === 'textarea' || tag === 'select' || e.target.isContentEditable;
            var isMac = navigator.platform.toUpperCase().indexOf('MAC') >= 0;
            var cmdOrCtrl = isMac ? e.metaKey : e.ctrlKey;

            // Cmd/Ctrl + Z (Undo)
            if (cmdOrCtrl && !e.shiftKey && (e.key === 'z' || e.key === 'Z')) {
                if (!isInput) {
                    e.preventDefault();
                    if (typeof self.undo === 'function') self.undo();
                    return;
                }
            }

            // Cmd/Ctrl + Y or Cmd/Ctrl + Shift + Z (Redo)
            if ((cmdOrCtrl && (e.key === 'y' || e.key === 'Y')) || (cmdOrCtrl && e.shiftKey && (e.key === 'z' || e.key === 'Z'))) {
                if (!isInput) {
                    e.preventDefault();
                    if (typeof self.redo === 'function') self.redo();
                    return;
                }
            }

            // Cmd/Ctrl + S (Save)
            if (cmdOrCtrl && (e.key === 's' || e.key === 'S')) {
                e.preventDefault();
                $('#wppoppop-btn-save').trigger('click');
                return;
            }

            // Escape (Deselect & Close Inspector)
            if (e.key === 'Escape' || e.keyCode === 27) {
                if (window.WpPopPopBuilderCanvas && typeof window.WpPopPopBuilderCanvas.deselect === 'function') {
                    window.WpPopPopBuilderCanvas.deselect();
                }
                if (window.WpPopPopBuilderInspector && typeof window.WpPopPopBuilderInspector.close === 'function') {
                    window.WpPopPopBuilderInspector.close();
                }
            }

            // Delete / Backspace (Active element)
            if ((e.key === 'Delete' || e.key === 'Backspace') && !isInput) {
                if (window.WpPopPopBuilderCanvas && typeof window.WpPopPopBuilderCanvas.deleteActiveElement === 'function') {
                    if (window.WpPopPopBuilderCanvas.activeId) {
                        e.preventDefault();
                        window.WpPopPopBuilderCanvas.deleteActiveElement();
                    }
                }
            }
        });
    },

    initThemeMode: function() {
            var saved = 'light';
            try { saved = localStorage.getItem('wppoppop_builder_theme') || 'light'; } catch (e) {}
            this.setThemeMode(saved === 'dark' ? 'dark' : 'light');
            this.bindThemeToggle();
        },

        setThemeMode: function(theme) {
            theme = (theme === 'dark') ? 'dark' : 'light';
            var isLight = (theme === 'light');
            $('.wppoppop-builder-wrap, .wppoppop-main-frame, body').toggleClass('theme-light', isLight);
            $('.wppoppop-builder-wrap').attr('data-wppoppop-theme', theme);
            $('#wppoppop-btn-theme-toggle').attr('aria-pressed', isLight ? 'false' : 'true');
            try { localStorage.setItem('wppoppop_builder_theme', theme); } catch (e) {}
        },

        toggleThemeMode: function() {
            var isLight = $('body').hasClass('theme-light');
            this.setThemeMode(isLight ? 'dark' : 'light');
        },

        bindThemeToggle: function() {
            var self = this;
            $(document).off('click.wppoppop_theme', '#wppoppop-btn-theme-toggle, .wppoppop-btn-theme')
                .on('click.wppoppop_theme', '#wppoppop-btn-theme-toggle, .wppoppop-btn-theme', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    self.toggleThemeMode();
                });
        },

        bindWorkspacePan: function() {
            var self = this;
            var isSpaceDown = false;
            var isPanning = false;
            var startX = 0, startY = 0;
            var startPanX = 0, startPanY = 0;
            var hasPanned = false;

            if (!self.state.pan) {
                self.state.pan = { x: 0, y: 0 };
            }

            // Keyboard spacebar detection (safely ignores focused inputs/textareas)
            $(document).off('keydown.wppoppop_pan').on('keydown.wppoppop_pan', function(e) {
                if (e.which === 32 || e.code === 'Space') {
                    var $target = $(e.target);
                    if ($target.is('input, textarea, select, [contenteditable="true"]') || $target.closest('.note-editor, .element-panel').length) {
                        return; // Let user type normal space in text fields
                    }
                    e.preventDefault();
                    if (isSpaceDown) return;
                    isSpaceDown = true;
                    $('body').addClass('wppoppop-space-held');
                }
            });

            $(document).off('keyup.wppoppop_pan').on('keyup.wppoppop_pan', function(e) {
                if (e.which === 32 || e.code === 'Space') {
                    var $target = $(e.target);
                    if ($target.is('input, textarea, select, [contenteditable="true"]') || $target.closest('.note-editor, .element-panel').length) {
                        return;
                    }
                    isSpaceDown = false;
                    isPanning = false;
                    $('body').removeClass('wppoppop-space-held wppoppop-is-panning');
                }
            });

            $(window).off('blur.wppoppop_pan').on('blur.wppoppop_pan', function() {
                isSpaceDown = false;
                isPanning = false;
                $('body').removeClass('wppoppop-space-held wppoppop-is-panning');
            });

            // Mouse pan gesture
            $(document).off('mousedown.wppoppop_pan').on('mousedown.wppoppop_pan', function(e) {
                if (!isSpaceDown) return;
                var $ws = $(e.target).closest('.wppoppop-builder-workspace');
                if (!$ws.length) return;

                e.preventDefault();
                e.stopPropagation();

                isPanning = true;
                hasPanned = false;
                startX = e.clientX;
                startY = e.clientY;
                startPanX = self.state.pan ? self.state.pan.x : 0;
                startPanY = self.state.pan ? self.state.pan.y : 0;

                $('body').addClass('wppoppop-is-panning');
            });

            $(document).off('mousemove.wppoppop_pan').on('mousemove.wppoppop_pan', function(e) {
                if (!isPanning || !isSpaceDown) return;
                e.preventDefault();

                var dx = e.clientX - startX;
                var dy = e.clientY - startY;

                if (Math.abs(dx) > 2 || Math.abs(dy) > 2) {
                    hasPanned = true;
                }

                self.state.pan.x = Math.round(startPanX + dx);
                self.state.pan.y = Math.round(startPanY + dy);
                self.updateCanvasTransform();
            });

            $(document).off('mouseup.wppoppop_pan').on('mouseup.wppoppop_pan', function(e) {
                if (isPanning) {
                    isPanning = false;
                    $('body').removeClass('wppoppop-is-panning');

                    if (hasPanned) {
                        var blockClick = function(ev) {
                            ev.stopPropagation();
                            ev.preventDefault();
                            $(document).off('click.wppoppop_pan_block', blockClick);
                        };
                        $(document).on('click.wppoppop_pan_block', blockClick);
                        setTimeout(function() {
                            $(document).off('click.wppoppop_pan_block', blockClick);
                        }, 60);
                    }
                }
            });
        },

        /**
         * Unified Canvas Group Scaling: canvas + all child elements scale as one group.
         * Max scale keeps the scaled group inside the workspace with STAGE_PAD px on each side.
         */
        STAGE_PAD: 10,

        getStageBounds: function($box, $ws) {
            var pad = this.STAGE_PAD;
            var bw = $box[0].offsetWidth || 640, bh = $box[0].offsetHeight || 400;
            var ww = $ws.length ? ($ws.innerWidth() || 800) : 800;
            var wh = $ws.length ? ($ws.innerHeight() || 600) : 600;
            var max = Math.min((ww - pad * 2) / bw, (wh - pad * 2) / bh);
            max = Math.max(0.05, max);
            return { max: max, fit: Math.min(max, 1), ww: ww, wh: wh, gw: bw, gh: bh };
        },

        setZoom: function(level) {
            var st = this.state;
            var $box = $('#wppoppop-canvas-box');
            var $ws = $('.wppoppop-builder-workspace').first();
            if (!$box.length) return;
            if (level === 'fit') {
                level = this.getStageBounds($box, $ws).fit;
                st.pan = { x: 0, y: 0 };
                st.zoomMode = 'fit';
            } else {
                level = parseFloat(level);
                if (isNaN(level) || level <= 0) level = 1;
                if (level > 3) level = level / 100;
                st.zoomMode = 'manual';
            }
            st.zoom = level;
            this.updateCanvasTransform();
        },

        clampStageAxis: function(pan, wsSize, groupSize) {
            var slack = (wsSize - groupSize) / 2 - this.STAGE_PAD;
            if (slack <= 0) return 0;
            return Math.max(-slack, Math.min(slack, pan));
        },

        bindZoom: function() {
            var self = this;
            $(document).off('change.wppoppop_zoom input.wppoppop_zoom', '#wppoppop-zoom-select')
                .on('change.wppoppop_zoom', '#wppoppop-zoom-select', function() {
                    self.setZoom($(this).val());
                });
            var t = null;
            $(window).off('resize.wppoppop_zoom').on('resize.wppoppop_zoom', function() {
                clearTimeout(t);
                t = setTimeout(function() {
                    if (self.state.zoomMode === 'fit') self.setZoom('fit');
                    else self.updateCanvasTransform();
                }, 120);
            });
            var v = $('#wppoppop-zoom-select').val();
            if (v) this.setZoom(v);
            if (window.ResizeObserver) {
                var ro = new ResizeObserver(function() { self.updateCanvasTransform(); });
                var boxEl = $('#wppoppop-canvas-box')[0];
                var wsEl = $('.wppoppop-builder-workspace').first()[0];
                if (boxEl) ro.observe(boxEl);
                if (wsEl) ro.observe(wsEl);
            }
        },

        updateCanvasTransform: function() {
            var zoom = (this.state && this.state.zoom) ? this.state.zoom : 1;
            if (!this.state.zoom && $('#wppoppop-zoom-select').length) {
                var zVal = parseFloat($('#wppoppop-zoom-select').val());
                if (!isNaN(zVal) && zVal > 0) {
                    zoom = zVal <= 3 ? zVal : (zVal / 100);
                }
            }
            var pan = (this.state && this.state.pan) ? this.state.pan : { x: 0, y: 0 };

            var $box = $('#wppoppop-canvas-box');
            var $ws = $('.wppoppop-builder-workspace').first();
            if ($box.length && $ws.length) {
                var b = this.getStageBounds($box, $ws);
                zoom = Math.min(zoom, b.max);
                pan = {
                    x: this.clampStageAxis(pan.x, b.ww, b.gw * zoom),
                    y: this.clampStageAxis(pan.y, b.wh, b.gh * zoom)
                };
                if (this.state) this.state.pan = pan;
            }
            if (this.state) this.state.effectiveZoom = zoom;
            if (window.WpPopPopBuilderCanvas) window.WpPopPopBuilderCanvas.currentZoom = zoom;

            var transformVal = 'translate(' + pan.x + 'px, ' + pan.y + 'px) scale(' + zoom + ')';

            if ($box.length) {
                $box.css({
                    'transform': transformVal,
                    '-webkit-transform': transformVal,
                    'transform-origin': 'center center'
                });
            }

            // Shift checkerboard pattern synchronously with mouse drag
            var $ws = $('.wppoppop-builder-workspace');
            if ($ws.length) {
                var ox = ((pan.x % 16) + 16) % 16;
                var oy = ((pan.y % 16) + 16) % 16;
                $ws.css('background-position',
                    ox + 'px ' + oy + 'px, ' +
                    ox + 'px ' + (oy + 8) + 'px, ' +
                    (ox + 8) + 'px ' + (oy - 8) + 'px, ' +
                    (ox - 8) + 'px ' + oy + 'px'
                );
            }
        },

        init: function() {
            this.bindWorkspacePan();
            this.updateCanvasTransform();
            this.initThemeMode();
            this.bindZoom();
            this.bindModalEvents();
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

            // Clicking top Canvas button switches canvas AND opens Popup Settings drawer
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
                alert('Your popup must have at least one canvas.');
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
                width: 300,
                height: 400,
                bg_mode: 'solid',
                bg_transparent: true,
                bg_color: '#ffffff',
                grad_color1: '#3b82f6',
                grad_color2: '#1d4ed8',
                grad_angle: 135,
                canvas_radius: 0,
                anim_appearance: 'fade',
                anim_duration: 1000,
                anim_delay: 0,
                anim_disappearance: 'none',
                anim_exit_enabled: false,
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
                    width: 300,
                    height: 400,
                    bg_mode: 'solid',
                    bg_color: '#ffffff',
                    grad_color1: '#3b82f6',
                    grad_color2: '#1d4ed8',
                    grad_angle: 135,
                    canvas_radius: 0,
                    anim_appearance: 'fade',
                    anim_duration: 1000,
                    anim_delay: 0,
                    anim_disappearance: 'none',
                    anim_exit_enabled: false,
                    logic_enabled: false
                };
            }

            var meta = this.state.canvasMeta[canvasNum];

            // 1. Update Tab Highlights
            $('.wppoppop-canvas-tab-wrapper').removeClass('active');
            $('.wppoppop-canvas-tab-wrapper:has([data-canvas="' + canvasNum + '"])').addClass('active');

            // 2. Restore Dedicated Dimensions for this Canvas
            if (this.state.viewport === 'desktop') {
                var w = meta.width || 300;
                var h = meta.height || 400;
                var $box = $('#wppoppop-canvas-box');
            if ($box.length) {
                $box.css({ width: w + 'px', height: h + 'px' });
                $box[0].style.setProperty('width', w + 'px', 'important');
                $box[0].style.setProperty('height', h + 'px', 'important');
            }
            if (window.WpPopPopBuilderSettings && typeof window.WpPopPopBuilderSettings.applyActiveCanvasBackground === 'function') {
                window.WpPopPopBuilderSettings.applyActiveCanvasBackground();
            }
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
                $box.css({ width: '300px', height: '540px' });
            } else {
                var cur = this.state.currentCanvas || 1;
                var meta = this.state.canvasMeta[cur] || { width: 300, height: 400 };
                var w = meta.width || 300;
                var h = meta.height || 400;
                $box.css({ width: w + 'px', height: h + 'px' });
            }
        },

        pushHistory: function() {
        var self = this; setTimeout(function(){ self.updateHistoryButtons(); }, 10);
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
        var self = this; setTimeout(function(){ self.updateHistoryButtons(); }, 10);
            if (this.state.historyIndex > 0) {
                this.state.historyIndex--;
                this.restoreSnapshot(this.state.history[this.state.historyIndex]);
            }
        },

        redo: function() {
        var self = this; setTimeout(function(){ self.updateHistoryButtons(); }, 10);
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
        }
    };
})(jQuery);

/* === WPPOPPOP_SHAPE_INTERACTION_ENGINE_START === */
(function() {
    'use strict';

    // 1. Toggle-Slide Click Handler & Sync
    document.addEventListener('click', function(e) {
        var toggle = e.target.closest('.wppoppop-toggle-switch, .wppoppop-switch, .wppoppop-toggle-slide');
        if (!toggle) return;

        var checkbox = toggle.querySelector('input[type="checkbox"]');
        if (!checkbox) return;

        // If click was directly on the input, allow natural browser state change
        if (e.target !== checkbox) {
            checkbox.checked = !checkbox.checked;
            var changeEvt = new Event('change', { bubbles: true });
            checkbox.dispatchEvent(changeEvt);
        }

        if (checkbox.checked) {
            toggle.classList.add('is-active', 'active');
        } else {
            toggle.classList.remove('is-active', 'active');
        }
    }, true);

    // 2. Shape Background Color Field Suppression on Layer Selection
    function updateShapeInspectorView() {
        var inspector = document.querySelector('.wppoppop-inspector, #wppoppop-inspector, .wppoppop-layer-settings');
        if (!inspector) return;

        var activeLayer = document.querySelector('.wppoppop-layer.is-selected, .wppoppop-layer.active, [data-layer-id].active');
        var layerType = activeLayer ? (activeLayer.getAttribute('data-type') || activeLayer.getAttribute('data-element-type') || '') : '';

        if (!layerType) {
            var typeInp = inspector.querySelector('input[name="type"], input[data-prop="type"]');
            if (typeInp) layerType = typeInp.value;
        }

        if (layerType === 'shape') {
            inspector.setAttribute('data-element', 'shape');
            inspector.setAttribute('data-element-type', 'shape');

            // Hide background color controls dynamically
            var bgControls = inspector.querySelectorAll(
                '.wppoppop-control-background, .wppoppop-control-bg-color, [data-prop="background"], [data-prop="background_color"], [data-prop="backgroundColor"]'
            );
            bgControls.forEach(function(el) {
                el.style.setProperty('display', 'none', 'important');
            });

            // Ensure Stroke default shows 0 if empty
            var strokeInp = inspector.querySelector('input[name="stroke_width"], input[data-prop="stroke_width"], input[data-prop="stroke"]');
            if (strokeInp && (strokeInp.value === '' || strokeInp.value === undefined)) {
                strokeInp.value = '0';
            }

            // Ensure Border & Corner Radius default show 0 if empty
            var borderInp = inspector.querySelector('input[name="border_width"], input[data-prop="border_width"], input[data-prop="borderRadius"]');
            if (borderInp && (borderInp.value === '' || borderInp.value === undefined)) {
                borderInp.value = '0';
            }
        } else {
            if (inspector.getAttribute('data-element') === 'shape') {
                inspector.removeAttribute('data-element');
                inspector.removeAttribute('data-element-type');
            }
        }
    }

    document.addEventListener('click', function() {
        setTimeout(updateShapeInspectorView, 50);
    });
    window.addEventListener('load', function() {
        setTimeout(updateShapeInspectorView, 100);
    });
})();
/* === WPPOPPOP_SHAPE_INTERACTION_ENGINE_END === */

/* === WPPOPPOP_SHAPE_DEFAULTS_ENFORCER_START === */
(function() {
    'use strict';

    var ZERO_PROPS = [
        'stroke', 'stroke_width', 'strokeWidth', 'stroke-width',
        'border_width', 'borderWidth', 'border-width',
        'border_radius', 'borderRadius', 'border-radius', 'corner_radius', 'cornerRadius', 'radius',
        'shadow_x', 'shadow_y', 'shadow_blur', 'shadow_spread',
        'shadowX', 'shadowY', 'shadowBlur', 'shadowSpread',
        'box_shadow_x', 'box_shadow_y', 'box_shadow_blur', 'box_shadow_spread'
    ];

    function sanitizeShapeModel(element) {
        if (!element) return;
        var type = element.type || element.element_type || element.elementType || '';
        if (type !== 'shape') return;

        // Force stroke to 0 if undefined, null, empty string, or non-explicit
        if (element.stroke_width === undefined || element.stroke_width === null || element.stroke_width === '' || element.stroke_width === 1 || element.stroke_width === '1' || element.stroke_width === '1px') {
            element.stroke_width = 0;
        }
        if (element.stroke === undefined || element.stroke === null || element.stroke === '' || element.stroke === 1 || element.stroke === '1') {
            element.stroke = 0;
        }

        // Force border and corner radius to 0
        if (element.border_width === undefined || element.border_width === null || element.border_width === '') {
            element.border_width = 0;
        }
        if (element.border_radius === undefined || element.border_radius === null || element.border_radius === '') {
            element.border_radius = 0;
        }
        if (element.borderRadius === undefined || element.borderRadius === null || element.borderRadius === '') {
            element.borderRadius = 0;
        }

        // Force all box shadow fields to 0
        ['shadow_x', 'shadow_y', 'shadow_blur', 'shadow_spread'].forEach(function(sp) {
            if (element[sp] === undefined || element[sp] === null || element[sp] === '') {
                element[sp] = 0;
            }
        });
    }

    function syncInspectorInputs() {
        var inspector = document.querySelector('.wppoppop-inspector, #wppoppop-inspector, .wppoppop-sidebar-settings, .wppoppop-layer-settings');
        if (!inspector) return;

        var activeLayer = document.querySelector('.wppoppop-layer.is-selected, .wppoppop-layer.active, [data-layer-id].active, .wppoppop-layer-item.active');
        var layerType = activeLayer ? (activeLayer.getAttribute('data-type') || activeLayer.getAttribute('data-element-type') || activeLayer.getAttribute('data-element') || '') : '';

        if (!layerType) {
            var typeInp = inspector.querySelector('input[name="type"], input[data-prop="type"], [data-setting="type"]');
            if (typeInp) layerType = typeInp.value;
        }

        if (layerType === 'shape') {
            inspector.setAttribute('data-element', 'shape');
            inspector.setAttribute('data-element-type', 'shape');

            // Hide background color controls
            var bgControls = inspector.querySelectorAll(
                '.wppoppop-control-background, .wppoppop-control-bg-color, [data-prop="background"], [data-prop="background_color"], [data-prop="backgroundColor"], .control-background-color'
            );
            bgControls.forEach(function(el) {
                el.style.setProperty('display', 'none', 'important');
            });

            // Enforce "0" on empty or uninitialized target fields in inspector DOM
            var inputs = inspector.querySelectorAll('input, select');
            inputs.forEach(function(inp) {
                var prop = (inp.getAttribute('data-prop') || inp.getAttribute('data-setting') || inp.getAttribute('data-key') || inp.name || inp.id || '').toLowerCase();
                for (var i = 0; i < ZERO_PROPS.length; i++) {
                    var zp = ZERO_PROPS[i].toLowerCase();
                    if (prop === zp || prop.endsWith('_' + zp) || prop.endsWith('-' + zp)) {
                        if (inp.value === '' || inp.value === undefined || inp.value === null || inp.value === '1' && (prop.indexOf('stroke') !== -1 || prop.indexOf('border') !== -1)) {
                            // If it defaulted to 1 or empty on a brand new shape, reset to 0
                            if (!activeLayer.getAttribute('data-has-custom-style')) {
                                inp.value = '0';
                                var evt = new Event('input', { bubbles: true });
                                inp.dispatchEvent(evt);
                            }
                        }
                        break;
                    }
                }
            });
        }
    }

    // Intercept builder element addition
    if (typeof window.WpPopPop !== 'undefined' && window.WpPopPop.registerElement) {
        var origReg = window.WpPopPop.registerElement;
        window.WpPopPop.registerElement = function(type, config) {
            if (type === 'shape' && config && config.defaults) {
                config.defaults.stroke = 0;
                config.defaults.stroke_width = 0;
                config.defaults.strokeWidth = 0;
                config.defaults.border_width = 0;
                config.defaults.border_radius = 0;
                config.defaults.borderRadius = 0;
                config.defaults.shadow_x = 0;
                config.defaults.shadow_y = 0;
                config.defaults.shadow_blur = 0;
                config.defaults.shadow_spread = 0;
            }
            return origReg.apply(this, arguments);
        };
    }

    // Bind DOM events
    document.addEventListener('click', function() {
        setTimeout(syncInspectorInputs, 40);
    }, true);

    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(syncInspectorInputs, 80);
    });

    window.addEventListener('load', function() {
        setTimeout(syncInspectorInputs, 150);
    });
})();
/* === WPPOPPOP_SHAPE_DEFAULTS_ENFORCER_END === */

/* === WPPOPPOP_SHAPE_STROKE_ZERO_ENGINE_START === */
(function() {
    'use strict';

    function enforceShapeStrokeZero() {
        var inspector = document.querySelector('.wppoppop-inspector, #wppoppop-inspector, .wppoppop-layer-settings');
        if (!inspector) return;

        var activeLayer = document.querySelector('.wppoppop-layer.is-selected, .wppoppop-layer.active, [data-layer-id].active, .wppoppop-layer-item.active');
        var layerType = activeLayer ? (activeLayer.getAttribute('data-type') || activeLayer.getAttribute('data-element-type') || activeLayer.getAttribute('data-element') || '') : '';

        if (!layerType) {
            var typeInp = inspector.querySelector('input[name="type"], input[data-prop="type"], [data-setting="type"]');
            if (typeInp) layerType = typeInp.value;
        }

        if (layerType === 'shape') {
            // Find STROKE WIDTH (PX) inputs in inspector
            var strokeInputs = inspector.querySelectorAll(
                'input[name="stroke_width"], input[name="strokeWidth"], input[name="stroke"], ' +
                'input[data-prop="stroke_width"], input[data-prop="strokeWidth"], input[data-prop="stroke"], ' +
                'input[data-setting="stroke_width"], input[data-key="stroke_width"], #stroke_width'
            );

            strokeInputs.forEach(function(inp) {
                // If input is empty, undefined, or defaulted to 1 on initial load, lock it to 0
                if (inp.value === '' || inp.value === undefined || inp.value === null || (inp.value === '1' && !activeLayer.hasAttribute('data-stroke-user-set'))) {
                    inp.value = '0';
                    inp.setAttribute('value', '0');
                    var evt = new Event('input', { bubbles: true });
                    inp.dispatchEvent(evt);
                }
            });

            // Mark when user explicitly changes stroke
            strokeInputs.forEach(function(inp) {
                if (!inp._strokeTrackerAttached) {
                    inp._strokeTrackerAttached = true;
                    inp.addEventListener('change', function() {
                        if (activeLayer) activeLayer.setAttribute('data-stroke-user-set', 'true');
                    });
                }
            });
        }
    }

    // Intercept builder element registration to force default 0
    if (typeof window.WpPopPop !== 'undefined' && window.WpPopPop.registerElement) {
        var origRegister = window.WpPopPop.registerElement;
        window.WpPopPop.registerElement = function(type, config) {
            if (type === 'shape' && config && config.defaults) {
                config.defaults.stroke_width = 0;
                config.defaults.strokeWidth = 0;
                config.defaults.stroke = 0;
            }
            return origRegister.apply(this, arguments);
        };
    }

    document.addEventListener('click', function() {
        setTimeout(enforceShapeStrokeZero, 40);
    }, true);

    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(enforceShapeStrokeZero, 80);
    });

    window.addEventListener('load', function() {
        setTimeout(enforceShapeStrokeZero, 120);
    });
})();
/* === WPPOPPOP_SHAPE_STROKE_ZERO_ENGINE_END === */
