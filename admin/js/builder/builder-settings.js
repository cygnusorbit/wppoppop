/**
 * WpPopPop Visual Builder: Popup Settings Drawer Engine
 * Dedicated Per-Canvas Settings (Name, Size, Color, Animation, Logic Tab) & Bullets Selector
 */
(function($) {
    'use strict';

    window.WpPopPopSettingsRegistry = window.WpPopPopSettingsRegistry || {
        modules: {},
        registerAccordion: function(id, mod) {
            this.modules[id] = mod || {};
        },
        hydrateAll: function(settings) {
            Object.keys(this.modules).forEach(function(k) {
                if (typeof window.WpPopPopSettingsRegistry.modules[k].hydrate === 'function') {
                    window.WpPopPopSettingsRegistry.modules[k].hydrate(settings);
                }
            });
        },
        serializeAll: function(baseSettings) {
            var out = baseSettings || {};
            Object.keys(this.modules).forEach(function(k) {
                if (typeof window.WpPopPopSettingsRegistry.modules[k].serialize === 'function') {
                    out[k] = window.WpPopPopSettingsRegistry.modules[k].serialize();
                }
            });
            return out;
        }
    };

    window.WpPopPopBuilderSettings = {
        init: function() {
            this.bindDrawer();
            this.bindAccordions();
            this.bindCanvasSubtabs();
            this.bindCanvasBullets();
            this.bindCanvasMetaInputs();
            this.bindCanvasLogicInputs();
            this.bindLiveStyle();
        },

        openDrawer: function() {
            $('#wppoppop-settings-drawer').addClass('open');
            $('#wppoppop-settings-backdrop').addClass('open');
            this.renderCanvasBullets();
            this.syncCurrentCanvasSettings();
        },

        closeDrawer: function() {
            $('#wppoppop-settings-drawer').removeClass('open');
            $('#wppoppop-settings-backdrop').removeClass('open');
        },

        bindDrawer: function() {
            var self = this;
            $('#wppoppop-btn-settings').on('click', function(e) {
                e.preventDefault();
                self.openDrawer();
            });

            $('#wppoppop-settings-drawer-close, #wppoppop-settings-backdrop').on('click', function(e) {
                e.preventDefault();
                self.closeDrawer();
            });

            self.bindOutsideClose();
        },

        // Click on any background / checker area outside the drawer slides it out
        bindOutsideClose: function() {
            var self = this;
            var SAFE = '#wppoppop-settings-drawer, #wppoppop-btn-settings, .wppoppop-btn-settings, ' +
                       '#wppoppop-inspector-drawer, #wppoppop-floating-layers-panel, ' +
                       '.media-modal, .media-modal-backdrop, .wp-picker-container, .wp-picker-holder, ' +
                       '.iris-picker, .ui-datepicker, .ui-dialog, .ui-widget-overlay, .ui-autocomplete, ' +
                       '.wppoppop-modal, #wppoppop-embed-modal, .wppoppop-canvas-bullet, ' +
                       '#wppoppop-settings-add-canvas-bullet, ' +
                       '.wppoppop-canvas-tab, .wppoppop-screen-tab, .wppoppop-canvas-tab-wrapper, ' +
                       '#wppoppop-canvas-tabs-container, #wppoppop-add-canvas-btn, ' +
                       '#wppoppop-canvas-duplicate-btn, .wppoppop-tab-delete-canvas, ' +
                       '.wppoppop-hdr-canvas-label, .wppoppop-quick-dimensions-wrap';
            var downInside = false;

            // Remember whether the press started inside the drawer (text-selection drags)
            $(document).off('mousedown.wppoppop_outside_close touchstart.wppoppop_outside_close')
                .on('mousedown.wppoppop_outside_close touchstart.wppoppop_outside_close', function(e) {
                    downInside = $(e.target).closest(SAFE).length > 0;
                });

            $(document).off('click.wppoppop_outside_close').on('click.wppoppop_outside_close', function(e) {
                if (!$('#wppoppop-settings-drawer').hasClass('open')) return;
                if (downInside) { downInside = false; return; }
                var t = e.target;
                // Target re-rendered/removed during the click (e.g. canvas bullets) - ignore
                if (!t || !document.documentElement.contains(t)) return;
                if ($(t).closest(SAFE).length) return;
                self.closeDrawer();
            });

            $(document).off('keydown.wppoppop_outside_close').on('keydown.wppoppop_outside_close', function(e) {
                if ((e.key === 'Escape' || e.keyCode === 27) && $('#wppoppop-settings-drawer').hasClass('open')) {
                    var tag = (e.target.tagName || '').toLowerCase();
                    if (tag === 'input' || tag === 'textarea' || tag === 'select') return;
                    self.closeDrawer();
                }
            });
        },

        bindAccordions: function() {
            $(document).on('click', '.wppoppop-acc-header', function(e) {
                e.preventDefault();
                var $header = $(this);
                var $body = $header.next('.wppoppop-acc-body');

                $('.wppoppop-acc-header').not($header).removeClass('active');
                $('.wppoppop-acc-body').not($body).slideUp(150);

                $header.toggleClass('active');
                $body.slideToggle(150);
            });
        },

        bindCanvasSubtabs: function() {
            $('.wppoppop-csubtab').on('click', function(e) {
                e.preventDefault();
                var tab = $(this).data('tab');
                $('.wppoppop-csubtab').removeClass('active');
                $(this).addClass('active');

                $('.wppoppop-csubcontent').hide();
                $('#csub-tab-' + tab).show();
            });
        },

        renderCanvasBullets: function() {
            var core = window.WpPopPopBuilderCore;
            if (!core) return;

            var $container = $('#wppoppop-canvas-bullets-container');
            $container.empty();

            var canvasKeys = Object.keys(core.state.canvases).map(Number).sort(function(a, b) { return a - b; });
            if (canvasKeys.length === 0) canvasKeys = [1, 2];

            canvasKeys.forEach(function(cNum) {
                var meta = core.state.canvasMeta[cNum] || { name: 'Canvas ' + cNum };
                var name = meta.name || ('Canvas ' + cNum);
                var isActive = (cNum === core.state.currentCanvas);

                var $btn = $('<button type="button" class="wppoppop-canvas-bullet"></button>')
                    .attr('data-canvas', cNum)
                    .toggleClass('active', isActive)
                    .html('<span class="wppoppop-bullet-dot"></span> ' + name);

                $container.append($btn);
            });

            var $addBullet = $('<button type="button" id="wppoppop-settings-add-canvas-bullet" class="wppoppop-bullet-add-btn" title="Add Canvas">+</button>');
            $container.append($addBullet);
        },

        bindCanvasBullets: function() {
            var self = this;

            $(document).on('click', '.wppoppop-canvas-bullet', function(e) {
                e.preventDefault();
                var cNum = parseInt($(this).data('canvas'), 10) || 1;
                var core = window.WpPopPopBuilderCore;
                if (core) {
                    core.switchCanvas(cNum);
                }
                self.syncCurrentCanvasSettings();
            });

            $(document).on('click', '#wppoppop-settings-add-canvas-bullet', function(e) {
                e.preventDefault();
                var core = window.WpPopPopBuilderCore;
                if (core) {
                    core.addNewCanvas();
                }
                self.renderCanvasBullets();
                self.syncCurrentCanvasSettings();
            });
        },

        syncCurrentCanvasSettings: function() {
            var core = window.WpPopPopBuilderCore;
            if (!core) return;
            var cur = core.state.currentCanvas || 1;
            var meta = core.state.canvasMeta[cur] || {};

            $('#set-canvas-name').val(meta.name || ('Canvas ' + cur));
            $('#set-canvas-width').val(meta.width || 640);
            $('#set-canvas-height').val(meta.height || 400);

            var isTransparent = (meta.bg_color === 'transparent');
            $('#set-canvas-bg-transparent-btn')
                .toggleClass('active', isTransparent)
                .attr('aria-pressed', isTransparent ? 'true' : 'false');

            if (isTransparent) {
                $('#set-canvas-bg-mode').val(meta._prev_bg_mode || 'solid');
                $('#set-canvas-bg-color').val(meta._prev_bg_color && meta._prev_bg_color !== 'transparent' ? meta._prev_bg_color : '#ffffff');
            } else {
                $('#set-canvas-bg-mode').val(meta.bg_mode || 'solid');
                $('#set-canvas-bg-color').val(meta.bg_color || '#ffffff');
            }

            $('#set-canvas-grad-color1').val(meta.grad_color1 || '#3b82f6');
            $('#set-canvas-grad-color2').val(meta.grad_color2 || '#1d4ed8');
            $('#set-canvas-grad-angle').val(meta.grad_angle || 135);

            // WPPOPPOP_CANVAS_OVERFLOW: per-canvas overflow, auto-close & logic hydration
            var ovOn = (meta.allow_overflow === true || meta.allow_overflow === 1 || meta.allow_overflow === '1' || meta.allow_overflow === 'true');
            $('#set-canvas-allow-overflow').prop('checked', ovOn);
            this.applyCanvasOverflow(ovOn);

            var acVal = parseInt(meta.autoclose_delay !== undefined ? meta.autoclose_delay : meta.autoclose, 10);
            $('#set-canvas-autoclose').val(isNaN(acVal) ? 0 : acVal);

            try {
                this.populateLogicFieldOptions();
                $('#set-canvas-logic-enable').prop('checked', !!meta.logic_enabled);
                if (meta.logic_field) $('#set-canvas-logic-field').val(meta.logic_field);
                $('#set-canvas-logic-operator').val(meta.logic_operator || 'equals');
                $('#set-canvas-logic-val').val(meta.logic_value || '');
                $('#set-canvas-logic-action').val(meta.logic_action || 'show');
                this.checkConditionalLogicEligibility();
                $('#set-canvas-logic-rules-panel').toggle($('#set-canvas-logic-enable').is(':checked') && !$('#set-canvas-logic-enable').prop('disabled'));
            } catch (e) {}

            // 4. Sync Canvas Animation Settings
            var animApp = (!meta.anim_appearance || meta.anim_appearance === 'fade') ? 'fadeIn' : meta.anim_appearance;
            var animDur = (meta.anim_duration !== undefined && meta.anim_duration !== '') ? parseInt(meta.anim_duration, 10) : 1000;
            var animDel = (meta.anim_delay !== undefined && meta.anim_delay !== '') ? parseInt(meta.anim_delay, 10) : 0;
            var animDis = (!meta.anim_disappearance || meta.anim_disappearance === 'fade') ? 'fadeOut' : meta.anim_disappearance;

            $('#set-canvas-anim-appearance, #set-anim-appearance').val(animApp);
            $('#set-canvas-anim-duration, #set-anim-duration').val(animDur);
            $('#set-canvas-anim-delay, #set-anim-delay').val(animDel);
            $('#set-canvas-anim-disappearance, #set-anim-disappearance').val(animDis);

            if (typeof this.renderCanvasBullets === 'function') {
                this.renderCanvasBullets();
            }
            if (typeof this.syncQuizLogicBadge === 'function') {
                this.syncQuizLogicBadge();
            }
        },

        checkConditionalLogicEligibility: function() {
            var core = window.WpPopPopBuilderCore;
            if (!core) return;

            var cur = core.state.currentCanvas || 1;
            var elements = (core.state.canvases && core.state.canvases[cur]) ? core.state.canvases[cur] : [];
            var count = elements.length;

            $('#set-canvas-element-count-badge').text(count + (count === 1 ? ' Element' : ' Elements'));

            var $toggle = $('#set-canvas-logic-enable');
            var $notice = $('#set-canvas-logic-disabled-notice');
            var $rulesPanel = $('#set-canvas-logic-rules-panel');

            if (count === 0) {
                $toggle.prop('disabled', true).prop('checked', false);
                $notice.show();
                $rulesPanel.hide();
                if (core.state.canvasMeta[cur]) {
                    core.state.canvasMeta[cur].logic_enabled = false;
                }
            } else {
                $toggle.prop('disabled', false);
                $notice.hide();
            }
        },

        populateLogicFieldOptions: function() {
            var core = window.WpPopPopBuilderCore;
            if (!core) return;

            var $select = $('#set-canvas-logic-field');
            $select.empty();

            var addedTokens = [];
            var cur = core.state.currentCanvas || 1;

            Object.keys(core.state.canvases).forEach(function(cNum) {
                var els = core.state.canvases[cNum] || [];
                els.forEach(function(el) {
                    var token = el.field_name || el.type;
                    if (addedTokens.indexOf(token) === -1) {
                        addedTokens.push(token);
                        var optLabel = (el.name || el.type.toUpperCase()) + ' (Canvas ' + cNum + ' &bull; {' + token + '})';
                        $select.append($('<option></option>').val(token).html(optLabel));
                    }
                });
            });

            if (addedTokens.length === 0) {
                $select.append('<option value="">No form fields found</option>');
            }
        },

        bindCanvasMetaInputs: function() {
            var self = this;
            $(document).off('input.wppoppop_cac change.wppoppop_cac', '#set-canvas-autoclose').on('input.wppoppop_cac change.wppoppop_cac', '#set-canvas-autoclose', function() {
                var val = parseInt($(this).val(), 10) || 0;
                var core = window.WpPopPopBuilderCore;
                if (!core) return;
                var cur = core.state.currentCanvas || 1;
                if (!core.state.canvasMeta[cur]) core.state.canvasMeta[cur] = {};
                core.state.canvasMeta[cur].autoclose = val;
                core.state.canvasMeta[cur].autoclose_delay = val;
                if (core.state.settings) core.state.settings.autoclose_delay = val;
            });
            $(document).off('input.wppoppop_exit change.wppoppop_exit', '#set-exit-intent-sensitivity').on('input.wppoppop_exit change.wppoppop_exit', '#set-exit-intent-sensitivity', function() {
                var val = parseInt($(this).val(), 10) || 20;
                if (window.WpPopPopBuilderCore && window.WpPopPopBuilderCore.state && window.WpPopPopBuilderCore.state.settings) {
                    window.WpPopPopBuilderCore.state.settings.exit_intent_sensitivity = val;
                }
            });
            $(document).off('input.wppoppop_scroll change.wppoppop_scroll', '#set-trigger-scroll-depth').on('input.wppoppop_scroll change.wppoppop_scroll', '#set-trigger-scroll-depth', function() {
                var val = parseInt($(this).val(), 10) || 50;
                if (window.WpPopPopBuilderCore && window.WpPopPopBuilderCore.state && window.WpPopPopBuilderCore.state.settings) {
                    window.WpPopPopBuilderCore.state.settings.scroll_depth = val;
                }
            });
            $(document).off('change.wppoppop_ovf', '#set-canvas-allow-overflow').on('change.wppoppop_ovf', '#set-canvas-allow-overflow', function() {
                var core = window.WpPopPopBuilderCore;
                if (!core) return;
                var cur = core.state.currentCanvas || 1;
                if (!core.state.canvasMeta[cur]) core.state.canvasMeta[cur] = {};
                var on = $(this).is(':checked');
                core.state.canvasMeta[cur].allow_overflow = on;
                self.applyCanvasOverflow(on);
                core.pushHistory();
            });
            // Play Canvas Animation on Demand
            $(document).on('click', '#set-canvas-anim-play', function(e) {
                e.preventDefault();
                if (window.WpPopPopBuilderCanvas && typeof window.WpPopPopBuilderCanvas.playCanvasEntranceAnimation === 'function') {
                    window.WpPopPopBuilderCanvas.playCanvasEntranceAnimation();
                }
            });

            var self = this;

            // Two-Way Toggle for Canvas Transparency
            $('#set-canvas-bg-transparent-btn').off('click.canvasTransparent').on('click.canvasTransparent', function(e) {
                e.preventDefault();
                var core = window.WpPopPopBuilderCore;
                if (!core) return;
                var cur = core.state.currentCanvas || 1;
                var meta = core.state.canvasMeta[cur] || {};

                if (meta.bg_color === 'transparent') {
                    // DISABLE TRANSPARENT: Restore previous color or default solid
                    var restoredColor = meta._prev_bg_color || '#ffffff';
                    if (restoredColor === 'transparent') restoredColor = '#ffffff';
                    meta.bg_color = restoredColor;
                    meta.bg_mode = meta._prev_bg_mode || 'solid';

                    $('#set-canvas-bg-color').val(restoredColor);
                    $('#set-canvas-bg-mode').val(meta.bg_mode);
                    $('#set-canvas-bg-transparent-btn').removeClass('active').attr('aria-pressed', 'false');
                } else {
                    // ENABLE TRANSPARENT: Cache previous color & mode
                    meta._prev_bg_color = meta.bg_color || $('#set-canvas-bg-color').val() || '#ffffff';
                    meta._prev_bg_mode = meta.bg_mode || $('#set-canvas-bg-mode').val() || 'solid';
                    meta.bg_color = 'transparent';

                    $('#set-canvas-bg-transparent-btn').addClass('active').attr('aria-pressed', 'true');
                }

                self.applyActiveCanvasBackground();
                core.pushHistory();
            });

            $('#set-canvas-name').off('input.canvasName').on('input.canvasName', function() {
                var core = window.WpPopPopBuilderCore;
                if (!core) return;
                var cur = core.state.currentCanvas || 1;
                var meta = core.state.canvasMeta[cur];
                if (meta) {
                    meta.name = $(this).val();
                    core.renderCanvasTabs();
                    $('#wppoppop-stage-canvas-badge').text(meta.name.toUpperCase());
                }
            });

            $('#set-canvas-width, #set-canvas-height').off('input.canvasDim change.canvasDim').on('input.canvasDim change.canvasDim', function() {
                var core = window.WpPopPopBuilderCore;
                if (!core) return;
                var cur = core.state.currentCanvas || 1;
                var meta = core.state.canvasMeta[cur];
                if (meta && core.state.viewport === 'desktop') {
                    var w = Math.max(200, Math.min(1600, parseInt($('#set-canvas-width').val(), 10) || 640));
                    var h = Math.max(150, Math.min(1200, parseInt($('#set-canvas-height').val(), 10) || 400));
                    meta.width = w;
                    meta.height = h;
                    $('#wppoppop-canvas-box').css({ width: w + 'px', height: h + 'px' });
                    $('#quick-box-width').val(w);
                    $('#quick-box-height').val(h);
                    core.pushHistory();
                }
            });

            $('#set-canvas-bg-mode').off('change.canvasBgMode').on('change.canvasBgMode', function() {
                var core = window.WpPopPopBuilderCore;
                if (!core) return;
                var cur = core.state.currentCanvas || 1;
                var meta = core.state.canvasMeta[cur];
                var mode = $(this).val();
                if (meta) {
                    meta.bg_mode = mode;
                    if (meta.bg_color === 'transparent') {
                        meta.bg_color = meta._prev_bg_color || '#ffffff';
                        if (meta.bg_color === 'transparent') meta.bg_color = '#ffffff';
                        $('#set-canvas-bg-color').val(meta.bg_color);
                        $('#set-canvas-bg-transparent-btn').removeClass('active').attr('aria-pressed', 'false');
                    }
                    self.applyActiveCanvasBackground();
                    core.pushHistory();
                }
            });

            $('#set-canvas-bg-color').off('input.canvasBg change.canvasBg').on('input.canvasBg change.canvasBg', function() {
                var core = window.WpPopPopBuilderCore;
                if (!core) return;
                var cur = core.state.currentCanvas || 1;
                var meta = core.state.canvasMeta[cur];
                if (meta) {
                    meta.bg_color = $(this).val();
                    meta._prev_bg_color = meta.bg_color;
                    meta.bg_mode = 'solid';
                    $('#set-canvas-bg-mode').val('solid');
                    $('#set-canvas-bg-transparent-btn').removeClass('active').attr('aria-pressed', 'false');
                    self.applyActiveCanvasBackground();
                    core.pushHistory();
                }
            });

            $('#set-canvas-grad-color1, #set-canvas-grad-color2, #set-canvas-grad-angle').off('input.canvasGrad change.canvasGrad').on('input.canvasGrad change.canvasGrad', function() {
                var core = window.WpPopPopBuilderCore;
                if (!core) return;
                var cur = core.state.currentCanvas || 1;
                var meta = core.state.canvasMeta[cur];
                if (meta) {
                    meta.grad_color1 = $('#set-canvas-grad-color1').val();
                    meta.grad_color2 = $('#set-canvas-grad-color2').val();
                    meta.grad_angle = parseInt($('#set-canvas-grad-angle').val(), 10) || 135;
                    meta.bg_mode = 'gradient';
                    $('#set-canvas-bg-mode').val('gradient');
                    $('#set-canvas-bg-transparent-btn').removeClass('active').attr('aria-pressed', 'false');
                    self.applyActiveCanvasBackground();
                    core.pushHistory();
                }
            });

            $('#set-anim-appearance, #set-anim-duration, #set-anim-delay').off('change.canvasAnim input.canvasAnim').on('change.canvasAnim input.canvasAnim', function() {
                var core = window.WpPopPopBuilderCore;
                if (!core) return;
                var cur = core.state.currentCanvas || 1;
                var meta = core.state.canvasMeta[cur];
                if (meta) {
                    meta.anim_appearance = $('#set-anim-appearance').val();
                    meta.anim_duration = parseInt($('#set-anim-duration').val(), 10) || 1000;
                    meta.anim_delay = parseInt($('#set-anim-delay').val(), 10) || 0;
                    core.pushHistory();
                }
            });
            // Canvas Animation Settings Sync (Appearance, Duration, Delay, Disappearance)
            $('#set-canvas-anim-appearance, #set-anim-appearance').off('change.canvasAnim').on('change.canvasAnim', function() {
                var core = window.WpPopPopBuilderCore;
                if (!core) return;
                var cur = core.state.currentCanvas || 1;
                if (!core.state.canvasMeta[cur]) core.state.canvasMeta[cur] = {};
                core.state.canvasMeta[cur].anim_appearance = $(this).val();
                core.pushHistory();
            });

            $('#set-canvas-anim-duration, #set-anim-duration').off('input.canvasAnim change.canvasAnim').on('input.canvasAnim change.canvasAnim', function() {
                var core = window.WpPopPopBuilderCore;
                if (!core) return;
                var cur = core.state.currentCanvas || 1;
                if (!core.state.canvasMeta[cur]) core.state.canvasMeta[cur] = {};
                var val = parseInt($(this).val(), 10);
                core.state.canvasMeta[cur].anim_duration = isNaN(val) ? 1000 : val;
                core.pushHistory();
            });

            $('#set-canvas-anim-delay, #set-anim-delay').off('input.canvasAnim change.canvasAnim').on('input.canvasAnim change.canvasAnim', function() {
                var core = window.WpPopPopBuilderCore;
                if (!core) return;
                var cur = core.state.currentCanvas || 1;
                if (!core.state.canvasMeta[cur]) core.state.canvasMeta[cur] = {};
                var val = parseInt($(this).val(), 10);
                core.state.canvasMeta[cur].anim_delay = isNaN(val) ? 0 : val;
                core.pushHistory();
            });

            $('#set-canvas-anim-disappearance, #set-anim-disappearance').off('change.canvasAnim').on('change.canvasAnim', function() {
                var core = window.WpPopPopBuilderCore;
                if (!core) return;
                var cur = core.state.currentCanvas || 1;
                if (!core.state.canvasMeta[cur]) core.state.canvasMeta[cur] = {};
                core.state.canvasMeta[cur].anim_disappearance = $(this).val();
                core.pushHistory();
            });

        },

        applyActiveCanvasBackground: function() {
            var core = window.WpPopPopBuilderCore;
            if (!core) return;
            var cur = core.state.currentCanvas || 1;
            var meta = core.state.canvasMeta[cur] || {};
            var $box = $('#wppoppop-canvas-box');

            if (meta.bg_color === 'transparent') {
                $box.addClass('wppoppop-canvas-transparent');
                $box.css({ background: 'transparent' });
                $('#set-canvas-bg-transparent-btn').addClass('active').attr('aria-pressed', 'true');
                $('#set-canvas-solid-wrap').show();
                $('#set-canvas-gradient-wrap').hide();
            } else {
                $box.removeClass('wppoppop-canvas-transparent');
                $('#set-canvas-bg-transparent-btn').removeClass('active').attr('aria-pressed', 'false');
                if (meta.bg_mode === 'gradient') {
                    $('#set-canvas-solid-wrap').hide();
                    $('#set-canvas-gradient-wrap').show();
                    var col1 = meta.grad_color1 || '#3b82f6';
                    var col2 = meta.grad_color2 || '#1d4ed8';
                    var angle = meta.grad_angle || 135;
                    $box.css({ background: 'linear-gradient(' + angle + 'deg, ' + col1 + ', ' + col2 + ')' });
                } else {
                    $('#set-canvas-solid-wrap').show();
                    $('#set-canvas-gradient-wrap').hide();
                    $box.css({ background: meta.bg_color || '#ffffff' });
                }
            }
        },

        bindCanvasLogicInputs: function() {
            var getMeta = function() {
                var core = window.WpPopPopBuilderCore;
                if (!core) return null;
                var cur = core.state.currentCanvas || 1;
                return core.state.canvasMeta[cur];
            };

            $('#set-canvas-logic-enable').on('change', function() {
                var meta = getMeta();
                if (!meta) return;
                var isEnabled = $(this).is(':checked');
                meta.logic_enabled = isEnabled;
                $('#set-canvas-logic-rules-panel')[isEnabled ? 'slideDown' : 'slideUp'](150);
            });

            $('#set-canvas-logic-field, #set-canvas-logic-operator, #set-canvas-logic-val, #set-canvas-logic-action').on('input change', function() {
                var meta = getMeta();
                if (!meta) return;
                meta.logic_field = $('#set-canvas-logic-field').val();
                meta.logic_operator = $('#set-canvas-logic-operator').val();
                meta.logic_value = $('#set-canvas-logic-val').val();
                meta.logic_action = $('#set-canvas-logic-action').val();
            });
        },

        applyCanvasOverflow: function(on) {
            on = !!on;
            $('#wppoppop-canvas-elements-root').toggleClass('wppoppop-canvas-clip', !on);
            $('#wppoppop-canvas-box').toggleClass('wppoppop-canvas-overflow-on', on);
            $('#set-canvas-overflow-state').text(on ? 'ON' : 'OFF');
            // WPPOPPOP_OVERFLOW_REFRESH: unlock / lock drag + resize containment on live layers
            if (window.WpPopPopBuilderCanvas && typeof window.WpPopPopBuilderCanvas.refreshInteractionContainment === 'function') {
                window.WpPopPopBuilderCanvas.refreshInteractionContainment();
            }
        },

        bindLiveStyle: function() {},

        getSettings: function() {
            return {
                animation: {
                    appearance: $('#set-canvas-anim-appearance, #set-anim-appearance').val() || 'fadeIn',
                    duration: parseInt($('#set-canvas-anim-duration, #set-anim-duration').val(), 10) || 1000,
                    delay: parseInt($('#set-canvas-anim-delay, #set-anim-delay').val(), 10) || 0,
                    disappearance: $('#set-canvas-anim-disappearance, #set-anim-disappearance').val() || 'fadeOut'
                },
                box: {
                    width: parseInt($('#set-canvas-width').val(), 10) || 640,
                    height: parseInt($('#set-canvas-height').val(), 10) || 400
                },
                triggers: {
                    load: $('#trig-load').is(':checked'),
                    load_delay: parseInt($('#trig-load-delay').val(), 10) || 0,
                    exit: $('#trig-exit').is(':checked'),
                    scroll: $('#trig-scroll').is(':checked'),
                    scroll_val: parseInt($('#trig-scroll-val').val(), 10) || 50,
                    adblock: $('#trig-adblock').is(':checked'),
                    backbutton: $('#trig-backbutton').is(':checked')
                },
                logic_math: {
                    formula: $('#set-math-formula').val() || '',
                    target: $('#set-math-target').val() || ''
                },
                sidetab: {
                    enable: $('#set-sidetab-enable').is(':checked'),
                    label: $('#set-sidetab-label').val() || '',
                    pos: $('#set-sidetab-pos').val() || 'left'
                },
                payments: {
                    gateway: $('#set-pay-gateway').val() || 'stripe',
                    amount: parseFloat($('#set-pay-amount').val()) || 0
                },
                downloads: {
                    enable: $('#set-dl-enable').is(':checked'),
                    url: $('#set-dl-url').val() || ''
                },
                video: {
                    enable: $('#set-vid-enable').is(':checked'),
                    time: parseInt($('#set-vid-time').val(), 10) || 0
                },
                autoresponder: {
                    enable: $('#set-auto-enable').is(':checked'),
                    subject: $('#set-auto-subject').val() || '',
                    body: $('#set-auto-body').val() || ''
                },
                webhooks: {
                    url: $('#set-webhook-url').val() || '',
                    secret: $('#set-webhook-secret').val() || ''
                },
                sms: {
                    enable: $('#set-sms-enable').is(':checked'),
                    phone: $('#set-sms-phone').val() || ''
                },
                targeting: {
                    auth: $('#set-target-auth').val() || 'all'
                },
                frequency: {
                    mode: $('#set-freq-mode').val() || 'always'
                },
                woocommerce: {
                    coupon: $('#set-wc-coupon').is(':checked'),
                    amount: $('#set-wc-amount').val() || ''
                },
                custom_code: {
                    css: $('#set-custom-css').val() || '',
                    js: $('#set-custom-js').val() || ''
                },
                quiz: {
                    enable: $('#set-quiz-enable').is(':checked'),
                    pass_threshold: parseInt($('#set-quiz-pass').val(), 10) || 70,
                    pass_canvas: parseInt($('#set-quiz-pass-canvas').val(), 10) || 2,
                    pass_screen: parseInt($('#set-quiz-pass-canvas').val(), 10) || 2,
                    fail_canvas: parseInt($('#set-quiz-fail-canvas').val(), 10) || 3,
                    fail_screen: parseInt($('#set-quiz-fail-canvas').val(), 10) || 3,
                    confetti: $('#set-quiz-confetti').is(':checked')
                }
            };
        },

        setSettings: function(settings) {
            if (!settings) return;
            
            if (settings.animation) {
                if (settings.animation.appearance) {
                    $('#set-canvas-anim-appearance, #set-anim-appearance').val(settings.animation.appearance);
                }
                if (settings.animation.duration !== undefined) {
                    $('#set-canvas-anim-duration, #set-anim-duration').val(settings.animation.duration);
                }
                if (settings.animation.delay !== undefined) {
                    $('#set-canvas-anim-delay, #set-anim-delay').val(settings.animation.delay);
                }
                if (settings.animation.disappearance) {
                    $('#set-canvas-anim-disappearance, #set-anim-disappearance').val(settings.animation.disappearance);
                }
            }
if (settings.triggers) {
                $('#trig-load').prop('checked', !!settings.triggers.load);
                $('#trig-load-delay').val(settings.triggers.load_delay || '');
                $('#trig-exit').prop('checked', !!settings.triggers.exit);
                $('#trig-scroll').prop('checked', !!settings.triggers.scroll);
                $('#trig-scroll-val').val(settings.triggers.scroll_val || 50);
                $('#trig-adblock').prop('checked', !!settings.triggers.adblock);
                $('#trig-backbutton').prop('checked', !!settings.triggers.backbutton);
            }
            if (settings.quiz) {
                $('#set-quiz-enable').prop('checked', !!settings.quiz.enable);
                $('#set-quiz-pass').val(settings.quiz.pass_threshold || 70);
                var p = settings.quiz.pass_canvas || settings.quiz.pass_screen || 2;
                var f = settings.quiz.fail_canvas || settings.quiz.fail_screen || 3;
                $('#set-quiz-pass-canvas, #set-quiz-pass-screen').val(p);
                $('#set-quiz-fail-canvas, #set-quiz-fail-screen').val(f);
                $('#set-quiz-confetti').prop('checked', !!settings.quiz.confetti);
            }
        }
    };
})(jQuery);


/* WPPOPPOP_SETTINGS_EXTRA */
(function($) {
    'use strict';
    var S = window.WpPopPopBuilderSettings;
    if (!S || S._extraWrapped) return;
    S._extraWrapped = true;
    var origGet = S.getSettings, origSet = S.setSettings;
    S.getSettings = function() {
        var out = origGet.apply(this, arguments) || {};
        var core = window.WpPopPopBuilderCore;
        var st = (core && core.state && core.state.settings) || {};
        out.exit_intent_sensitivity = parseInt($('#set-exit-intent-sensitivity').val(), 10) || st.exit_intent_sensitivity || 20;
        out.scroll_depth = parseInt($('#set-trigger-scroll-depth').val(), 10) || st.scroll_depth || 50;
        return out;
    };
    S.setSettings = function(settings) {
        var r = origSet.apply(this, arguments);
        if (settings) {
            if (settings.exit_intent_sensitivity) $('#set-exit-intent-sensitivity').val(settings.exit_intent_sensitivity);
            if (settings.scroll_depth) $('#set-trigger-scroll-depth').val(settings.scroll_depth);
        }
        return r;
    };
})(jQuery);


/* WPPOPPOP_EXIT_TOGGLE */
(function($) {
    'use strict';
    var S = window.WpPopPopBuilderSettings;
    if (!S) return;

    function isOn(meta) {
        var v = meta ? meta.anim_exit_enabled : undefined;
        return !(v === false || v === 0 || v === '0' || v === 'false');
    }

    function applyExitUi(on) {
        var $sel = $('#set-canvas-anim-disappearance, #set-anim-disappearance');
        $('#set-canvas-anim-exit-enable').prop('checked', !!on);
        $sel.prop('disabled', !on).attr('aria-disabled', on ? 'false' : 'true');
        $sel.parent().css({ opacity: on ? '' : '0.45', transition: 'opacity .15s ease' });
    }

    if (!S._exitToggleWrapped) {
        S._exitToggleWrapped = true;
        var origSync = S.syncCurrentCanvasSettings;
        S.syncCurrentCanvasSettings = function() {
            var r = typeof origSync === 'function' ? origSync.apply(this, arguments) : undefined;
            var core = window.WpPopPopBuilderCore;
            if (core && core.state) {
                var cur = core.state.currentCanvas || 1;
                applyExitUi(isOn(core.state.canvasMeta && core.state.canvasMeta[cur]));
            }
            return r;
        };
    }

    $(document).off('change.wppoppopExitToggle', '#set-canvas-anim-exit-enable')
        .on('change.wppoppopExitToggle', '#set-canvas-anim-exit-enable', function() {
            var core = window.WpPopPopBuilderCore;
            if (!core || !core.state) return;
            var cur = core.state.currentCanvas || 1;
            if (!core.state.canvasMeta) core.state.canvasMeta = {};
            if (!core.state.canvasMeta[cur]) core.state.canvasMeta[cur] = {};
            var on = $(this).is(':checked');
            core.state.canvasMeta[cur].anim_exit_enabled = on;
            applyExitUi(on);
            if (typeof core.pushHistory === 'function') core.pushHistory();
        });

    $(function() {
        setTimeout(function() { if (S.syncCurrentCanvasSettings) S.syncCurrentCanvasSettings(); }, 300);
    });
})(jQuery);


/* WPPOPPOP_TRIGGER_SYNC: one scroll value, clamped numbers, sub-fields follow their checkbox */
(function($) {
    'use strict';
    var S = window.WpPopPopBuilderSettings;
    if (!S || S._triggerSyncWrapped) return;
    S._triggerSyncWrapped = true;

    function clamp(v, lo, hi, def) {
        v = parseInt(v, 10);
        if (isNaN(v)) { v = def; }
        return Math.max(lo, Math.min(hi, v));
    }

    function toggleSub() {
        /* kept editable */
        /* kept editable */
        /* kept editable */
    }

    var origGet = S.getSettings, origSet = S.setSettings;

    S.getSettings = function() {
        var out = origGet.apply(this, arguments) || {};
        var pct = clamp($('#set-trigger-scroll-depth').val(), 1, 100, 50);
        out.triggers = out.triggers || {};
        out.triggers.scroll_val = pct;      /* renderer reads this first */
        out.scroll_depth = pct;             /* legacy fallback stays identical */
        out.triggers.load_delay = clamp($('#trig-load-delay').val(), 0, 3600, 0);
        out.exit_intent_sensitivity = clamp($('#set-exit-intent-sensitivity').val(), 1, 200, 20);
        return out;
    };

    S.setSettings = function(settings) {
        var r = origSet.apply(this, arguments);
        if (settings) {
            var t = settings.triggers || {};
            var pct = clamp(t.scroll_val || settings.scroll_depth, 1, 100, 50);
            $('#set-trigger-scroll-depth, #trig-scroll-val').val(pct);
            $('#trig-load-delay').val(clamp(t.load_delay, 0, 3600, 0));
            if (settings.exit_intent_sensitivity) {
                $('#set-exit-intent-sensitivity').val(clamp(settings.exit_intent_sensitivity, 1, 200, 20));
            }
        }
        toggleSub();
        return r;
    };

    $(document)
        .off('input.wppTrigSync change.wppTrigSync', '#set-trigger-scroll-depth')
        .on('input.wppTrigSync change.wppTrigSync', '#set-trigger-scroll-depth', function() {
            $('#trig-scroll-val').val(clamp($(this).val(), 1, 100, 50));
        })
        .off('change.wppTrigToggle', '#trig-load, #trig-exit, #trig-scroll')
        .on('change.wppTrigToggle', '#trig-load, #trig-exit, #trig-scroll', toggleSub);

    $(function() { setTimeout(toggleSub, 350); });
})(jQuery);


/* WPPOPPOP_TRIGGER_TOGGLES: slide-toggles, always-on-load, repeat-every-X-hours, always-editable fields */
(function($) {
    'use strict';
    var S = window.WpPopPopBuilderSettings;
    if (!S || S._trigToggleWrapped) return;
    S._trigToggleWrapped = true;

    function clamp(v, lo, hi, def) {
        v = parseInt(v, 10);
        if (isNaN(v)) { v = def; }
        return Math.max(lo, Math.min(hi, v));
    }

    function dimSubs() {
        $('.wppoppop-trig-sub[data-for]').each(function() {
            var on = $('#' + $(this).attr('data-for')).is(':checked');
            $(this).toggleClass('is-off', !on); /* visual hint only, never disabled */
        });
        $('#trig-load-delay, #set-exit-intent-sensitivity, #set-trigger-scroll-depth, #trig-repeat-hours')
            .prop('disabled', false).prop('readOnly', false);
    }

    var origGet = S.getSettings, origSet = S.setSettings;

    S.getSettings = function() {
        var out = origGet.apply(this, arguments) || {};
        out.triggers = out.triggers || {};
        out.triggers.always = $('#trig-always').is(':checked');
        out.triggers.repeat = $('#trig-repeat').is(':checked');
        out.triggers.repeat_hours = clamp($('#trig-repeat-hours').val(), 1, 720, 24);
        var pct = clamp($('#set-trigger-scroll-depth').val(), 1, 100, 50);
        out.triggers.scroll_val = pct;
        out.scroll_depth = pct;
        out.triggers.load_delay = clamp($('#trig-load-delay').val(), 0, 3600, 0);
        out.exit_intent_sensitivity = clamp($('#set-exit-intent-sensitivity').val(), 1, 200, 20);
        return out;
    };

    S.setSettings = function(settings) {
        var r = origSet.apply(this, arguments);
        if (settings) {
            var t = settings.triggers || {};
            $('#trig-always').prop('checked', !!t.always && t.always !== 'false');
            $('#trig-repeat').prop('checked', !!t.repeat && t.repeat !== 'false');
            $('#trig-repeat-hours').val(clamp(t.repeat_hours, 1, 720, 24));
            var pct = clamp(t.scroll_val || settings.scroll_depth, 1, 100, 50);
            $('#set-trigger-scroll-depth, #trig-scroll-val').val(pct);
            $('#trig-load-delay').val(clamp(t.load_delay, 0, 3600, 0));
            if (settings.exit_intent_sensitivity) {
                $('#set-exit-intent-sensitivity').val(clamp(settings.exit_intent_sensitivity, 1, 200, 20));
            }
        }
        dimSubs();
        return r;
    };

    function syncState() {
        var core = window.WpPopPopBuilderCore;
        if (core && core.state) {
            core.state.settings = core.state.settings || {};
            core.state.settings.exit_intent_sensitivity = clamp($('#set-exit-intent-sensitivity').val(), 1, 200, 20);
            core.state.settings.scroll_depth = clamp($('#set-trigger-scroll-depth').val(), 1, 100, 50);
        }
        $('#trig-scroll-val').val(clamp($('#set-trigger-scroll-depth').val(), 1, 100, 50));
    }

    $(document)
        .off('input.wppTrig change.wppTrig', '#set-exit-intent-sensitivity, #set-trigger-scroll-depth, #trig-load-delay, #trig-repeat-hours')
        .on('input.wppTrig change.wppTrig', '#set-exit-intent-sensitivity, #set-trigger-scroll-depth, #trig-load-delay, #trig-repeat-hours', syncState)
        .off('change.wppTrigDim', '#trig-load, #trig-exit, #trig-scroll, #trig-repeat')
        .on('change.wppTrigDim', '#trig-load, #trig-exit, #trig-scroll, #trig-repeat', dimSubs);

    $(function() { setTimeout(dimSubs, 350); });
})(jQuery);


/* WPPOPPOP_CELEBRATION_UI: per-popup success celebration effect */
(function($) {
    'use strict';
    var S = window.WpPopPopBuilderSettings;
    if (!S || S._celebrationWrapped) return;
    S._celebrationWrapped = true;
    var ALLOWED = ['default', 'none', 'burst', 'cannon', 'fireworks', 'rain', 'stars', 'both'];
    function norm(v) { return ALLOWED.indexOf(v) !== -1 ? v : 'default'; }
    var origGet = S.getSettings, origSet = S.setSettings;
    S.getSettings = function() {
        var out = origGet.apply(this, arguments) || {};
        out.celebration = { effect: norm($('#set-celebration-effect').val()) };
        return out;
    };
    S.setSettings = function(settings) {
        var r = origSet.apply(this, arguments);
        var eff = (settings && settings.celebration && settings.celebration.effect) || 'default';
        $('#set-celebration-effect').val(norm(eff));
        return r;
    };
})(jQuery);


/* WPPOPPOP_OVERLAY_UI: popup overlay opacity / color / custom backgrounds */
(function($) {
    'use strict';
    var S = window.WpPopPopBuilderSettings;
    if (!S || S._overlayWrapped) return;
    S._overlayWrapped = true;

    var model = { backgrounds: [], selected: '' };
    var editingId = null;
    var IMG_RE = /\.(jpe?g|png)(\?.*)?$/i;

    function newId() { return 'bg_' + Math.random().toString(36).slice(2, 8) + (new Date().getTime()).toString(36).slice(-3); }
    function find(id) {
        for (var i = 0; i < model.backgrounds.length; i++) { if (model.backgrounds[i].id === id) return model.backgrounds[i]; }
        return null;
    }
    function clampOpacity(v) {
        v = parseInt(v, 10);
        if (isNaN(v)) v = 65;
        return Math.max(0, Math.min(100, v));
    }

    function syncOpacityLabel() { $('#set-ov-opacity-val').text(clampOpacity($('#set-ov-opacity').val()) + '%'); }
    function syncTransparentUi() {
        var on = $('#set-ov-transparent').is(':checked');
        $('#set-ov-color').prop('disabled', on).css('opacity', on ? 0.4 : 1);
    }

    /* WPPOPPOP_OVERLAY_PAGER: paginated custom background list (default 5 per page) */
    var ovPage = 0, ovPerPage = 5, ovLastCount = 0;

    function pageOfSelected() {
        for (var i = 0; i < model.backgrounds.length; i++) {
            if (model.backgrounds[i].id === model.selected) { return Math.floor(i / ovPerPage); }
        }
        return 0;
    }

    function renderPager(total, pages) {
        var $p = $('#set-ov-bg-pager').empty();
        if (total <= 5) { $p.hide(); return; }
        var $info = $('<span></span>').text(total + ' items');
        var $nav = $('<span></span>').css({ display: 'inline-flex', alignItems: 'center', gap: '6px' });
        $nav.append($('<button type="button" class="button button-small set-ov-pg-prev"></button>').prop('disabled', ovPage <= 0).html('&lsaquo; Prev'));
        $nav.append($('<span></span>').css({ minWidth: '70px', textAlign: 'center', fontWeight: '600' }).text('Page ' + (ovPage + 1) + ' / ' + pages));
        $nav.append($('<button type="button" class="button button-small set-ov-pg-next"></button>').prop('disabled', ovPage >= pages - 1).html('Next &rsaquo;'));
        var $sel = $('<select id="set-ov-pg-size" class="button-small"></select>').css({ fontSize: '11px', height: '26px' });
        $.each([5, 10, 20], function(i, n) {
            $sel.append($('<option></option>').val(n).text(n + ' / page').prop('selected', n === ovPerPage));
        });
        $p.append($info, $nav, $sel).css('display', 'flex');
    }

    function renderList() {
        var $l = $('#set-ov-bg-list').empty();
        var total = model.backgrounds.length;
        var pages = Math.max(1, Math.ceil(total / ovPerPage));
        if (ovPage < 0 || total > ovLastCount) { ovPage = pageOfSelected(); }   /* load or add: show the selected item */
        ovLastCount = total;
        ovPage = Math.max(0, Math.min(pages - 1, ovPage));

        function row(id, label, badge, editable) {
            var $r = $('<div></div>').css({ display: 'flex', alignItems: 'center', gap: '6px', padding: '6px 8px', border: '1px solid #e2e8f0', borderRadius: '4px', marginBottom: '4px', background: '#fff' });
            var $rad = $('<input type="radio" name="set-ov-bg-selected">').val(id).prop('checked', model.selected === id);
            var $lab = $('<span></span>').css({ flex: 1, fontSize: '12px', overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap' }).text(label);
            $r.append($rad, $lab);
            if (badge) { $r.append($('<span></span>').css({ fontSize: '10px', color: '#64748b', textTransform: 'uppercase' }).text(badge)); }
            if (editable) {
                $r.append($('<button type="button" class="button button-small set-ov-bg-edit"></button>').attr('data-id', id).text('Edit'));
                $r.append($('<button type="button" class="button button-small set-ov-bg-del"></button>').attr('data-id', id).css('color', '#b91c1c').text('Delete'));
            }
            $l.append($r);
        }
        row('', 'None (color overlay only)', '', false);
        var start = ovPage * ovPerPage;
        $.each(model.backgrounds.slice(start, start + ovPerPage), function(i, bg) {
            row(bg.id, bg.name || 'Background', bg.type === 'animation' ? 'Animation' : 'Picture', true);
        });
        renderPager(total, pages);
    }

    $(document)
        .off('click.wppOvPrev', '.set-ov-pg-prev').on('click.wppOvPrev', '.set-ov-pg-prev', function(e) { e.preventDefault(); ovPage--; renderList(); })
        .off('click.wppOvNext', '.set-ov-pg-next').on('click.wppOvNext', '.set-ov-pg-next', function(e) { e.preventDefault(); ovPage++; renderList(); })
        .off('change.wppOvSize', '#set-ov-pg-size').on('change.wppOvSize', '#set-ov-pg-size', function() {
            var first = ovPage * ovPerPage;               /* keep the first visible item in view */
            var n = parseInt($(this).val(), 10);
            ovPerPage = (n === 10 || n === 20) ? n : 5;
            ovPage = Math.floor(first / ovPerPage);
            renderList();
        });

    function toggleType() {
        var anim = $('#set-ov-bg-type').val() === 'animation';
        $('#set-ov-bg-img-wrap').toggle(!anim);
        $('#set-ov-bg-anim-wrap').toggle(anim);
    }
    function setPreview(url) {
        $('#set-ov-bg-preview').css('background-image', url ? 'url("' + String(url).replace(/"/g, '%22') + '")' : 'none');
    }
    function openEditor(bg) {
        editingId = bg ? bg.id : null;
        $('#set-ov-bg-name').val(bg ? bg.name : '');
        var wppOp = (bg && bg.opacity !== undefined && bg.opacity !== '' && !isNaN(parseInt(bg.opacity, 10))) ? Math.max(0, Math.min(100, parseInt(bg.opacity, 10))) : 100; /* WPPOPPOP_OVERLAY_BGOP */
        $('#set-ov-bg-opacity').val(wppOp);
        $('#set-ov-bg-opacity-val').text(wppOp + '%');
        $('#set-ov-bg-type').val(bg ? bg.type : 'image');
        $('#set-ov-bg-url').val(bg && bg.type === 'image' ? bg.url : '');
        $('#set-ov-bg-html').val(bg && bg.type === 'animation' ? bg.html : '');
        setPreview(bg && bg.type === 'image' ? bg.url : '');
        toggleType();
        $('#set-ov-bg-editor').slideDown(120);
    }
    function closeEditor() { editingId = null; $('#set-ov-bg-editor').slideUp(120); }

    var origGet = S.getSettings, origSet = S.setSettings;

    S.getSettings = function() {
        var out = origGet.apply(this, arguments) || {};
        out.overlay = {
            opacity: clampOpacity($('#set-ov-opacity').val()),
            color: $('#set-ov-color').val() || '#0f172a',
            transparent: $('#set-ov-transparent').is(':checked'),
            selected: model.selected || '',
            backgrounds: $.map(model.backgrounds, function(b) { return $.extend({}, b); })
        };
        return out;
    };

    S.setSettings = function(settings) {
        var r = origSet.apply(this, arguments);
        var ov = (settings && settings.overlay) || {};
        $('#set-ov-opacity').val(ov.opacity !== undefined && ov.opacity !== '' ? clampOpacity(ov.opacity) : 65);
        $('#set-ov-color').val(/^#[0-9a-f]{6}$/i.test(ov.color || '') ? ov.color : '#0f172a');
        $('#set-ov-transparent').prop('checked', !!ov.transparent && ov.transparent !== 'false' && ov.transparent !== '0');
        var list = ov.backgrounds;
        if (list && !$.isArray(list) && typeof list === 'object') { list = $.map(list, function(v) { return v; }); }
        model.backgrounds = $.isArray(list) ? $.map(list, function(b) { return $.extend({ id: newId(), name: '', type: 'image', url: '', html: '' }, b); }) : [];
        model.selected = (ov.selected && find(ov.selected)) ? ov.selected : '';
        ovPage = -1;
        closeEditor();
        syncOpacityLabel();
        syncTransparentUi();
        renderList();
        return r;
    };

    $(document)
        .off('input.wppOv change.wppOv', '#set-ov-opacity').on('input.wppOv change.wppOv', '#set-ov-opacity', syncOpacityLabel)
        .off('change.wppOvT', '#set-ov-transparent').on('change.wppOvT', '#set-ov-transparent', syncTransparentUi)
        .off('change.wppOvType', '#set-ov-bg-type').on('change.wppOvType', '#set-ov-bg-type', toggleType)
        .off('input.wppOvUrl change.wppOvUrl', '#set-ov-bg-url').on('input.wppOvUrl change.wppOvUrl', '#set-ov-bg-url', function() { setPreview($.trim($(this).val())); })
        .off('click.wppOvAdd', '#set-ov-bg-add').on('click.wppOvAdd', '#set-ov-bg-add', function(e) { e.preventDefault(); if (model.backgrounds.length >= 20) { window.alert('Maximum of 20 custom backgrounds.'); return; } openEditor(null); })
        .off('click.wppOvCancel', '#set-ov-bg-cancel').on('click.wppOvCancel', '#set-ov-bg-cancel', function(e) { e.preventDefault(); closeEditor(); })
        .off('change.wppOvSel', 'input[name="set-ov-bg-selected"]').on('change.wppOvSel', 'input[name="set-ov-bg-selected"]', function() { model.selected = $(this).val() || ''; })
        .off('click.wppOvEdit', '.set-ov-bg-edit').on('click.wppOvEdit', '.set-ov-bg-edit', function(e) { e.preventDefault(); var bg = find($(this).attr('data-id')); if (bg) openEditor(bg); })
        .off('click.wppOvDel', '.set-ov-bg-del').on('click.wppOvDel', '.set-ov-bg-del', function(e) {
            e.preventDefault();
            var id = $(this).attr('data-id');
            if (!window.confirm('Delete this background?')) return;
            model.backgrounds = $.grep(model.backgrounds, function(b) { return b.id !== id; });
            if (model.selected === id) model.selected = '';
            if (editingId === id) closeEditor();
            renderList();
        })
        .off('click.wppOvMedia', '#set-ov-bg-media').on('click.wppOvMedia', '#set-ov-bg-media', function(e) {
            e.preventDefault();
            if (!window.wp || !window.wp.media) {
                var manual = window.prompt('WordPress Media is unavailable. Enter image URL (JPG/PNG):', $('#set-ov-bg-url').val() || '');
                if (manual) { $('#set-ov-bg-url').val($.trim(manual)).trigger('change'); }
                return;
            }
            var frame = window.wp.media({ title: 'Select Overlay Background (JPG / PNG)', button: { text: 'Use Image' }, multiple: false, library: { type: ['image/jpeg', 'image/png'] } });
            frame.on('select', function() {
                var att = frame.state().get('selection').first();
                att = att ? att.toJSON() : null;
                if (att && att.url) { $('#set-ov-bg-url').val(att.url).trigger('change'); }
            });
            frame.open();
        })
        .off('click.wppOvApply', '#set-ov-bg-apply').on('click.wppOvApply', '#set-ov-bg-apply', function(e) {
            e.preventDefault();
            var type = $('#set-ov-bg-type').val() === 'animation' ? 'animation' : 'image';
            var name = $.trim($('#set-ov-bg-name').val()) || (type === 'animation' ? 'Animation' : 'Picture');
            var item = { id: editingId || newId(), name: name.slice(0, 60), type: type, url: '', html: '',
                opacity: (function(v) { v = parseInt(v, 10); return isNaN(v) ? 100 : Math.max(0, Math.min(100, v)); })($('#set-ov-bg-opacity').val()) };
            if (type === 'image') {
                item.url = $.trim($('#set-ov-bg-url').val());
                if (!IMG_RE.test(item.url)) { window.alert('Please choose a JPEG or PNG image (.jpg, .jpeg, .png).'); return; }
            } else {
                item.html = $('#set-ov-bg-html').val() || '';
                if (!$.trim(item.html)) { window.alert('Please enter the animation HTML / JS.'); return; }
            }
            if (editingId) {
                for (var i = 0; i < model.backgrounds.length; i++) { if (model.backgrounds[i].id === editingId) { model.backgrounds[i] = item; break; } }
            } else {
                model.backgrounds.push(item);
                model.selected = item.id;
            }
            closeEditor();
            renderList();
        });

    $(function() { syncOpacityLabel(); syncTransparentUi(); renderList(); });
})(jQuery);

/* WPPOPPOP_OVERLAY_BGOP: live % label for the background opacity slider */
jQuery(function($) {
    $(document).off('input.wppOvBgOp change.wppOvBgOp', '#set-ov-bg-opacity')
        .on('input.wppOvBgOp change.wppOvBgOp', '#set-ov-bg-opacity', function() {
            $('#set-ov-bg-opacity-val').text((parseInt($(this).val(), 10) || 0) + '%');
        });
});


/* WPPOPPOP_DOUBLE_OPTIN_UI: Double Opt-In module (Inspector DOM <-> settings.double_optin) */
(function($) {
    'use strict';
    var S = window.WpPopPopBuilderSettings;
    if (!S || S._doptWrapped) return;
    S._doptWrapped = true;

    var DEF = {
        enable: false,
        recipient: '',
        subject: 'Please confirm your email address',
        message: 'Dear visitor!\n\nPlease confirm your email address by clicking the following link:\n<a href="{{confirmation-url}}">{{confirmation-url}}</a>\n\nThanks.',
        from_email: '{{global-from-email}}',
        from_name: '{{global-from-name}}',
        thank_message: '<h4 style="text-align: center;">Thank you!</h4><p style="text-align: center;">Your email address successfully confirmed.</p>',
        thank_url: ''
    };

    var MAP = {
        recipient: '#set-dopt-recipient',
        subject: '#set-dopt-subject',
        message: '#set-dopt-message',
        from_email: '#set-dopt-from-email',
        from_name: '#set-dopt-from-name',
        thank_message: '#set-dopt-thank-message',
        thank_url: '#set-dopt-thank-url'
    };

    function isTrue(v) { return v === true || v === 1 || v === '1' || v === 'true'; }

    function refreshTags() {
        var tags = [
            ['Form values', null],
            ['{{email}}', '{{email}}'],
            ['General', null],
            ['Confirmation URL', '{{confirmation-url}}'],
            ['Global from email', '{{global-from-email}}'],
            ['Global from name', '{{global-from-name}}'],
            ['Site name', '{{site-name}}']
        ];
        var seen = {};
        var core = window.WpPopPopBuilderCore;
        var fields = [];
        if (core && core.state && core.state.canvases) {
            Object.keys(core.state.canvases).forEach(function(c) {
                (core.state.canvases[c] || []).forEach(function(el) {
                    var tok = el && (el.field_name || el.fieldName || el.fieldId);
                    if (!tok || seen[tok]) return;
                    seen[tok] = true;
                    fields.push([String(tok) + ' | ' + (el.type || 'field'), '{{' + tok + '}}']);
                });
            });
        }
        $('.wppoppop-dopt-tag').each(function() {
            var $s = $(this).empty().append('<option value="">&lt;/&gt; Tags</option>');
            var $g1 = $('<optgroup label="Form values"></optgroup>');
            $.each(fields.concat([tags[1]]), function(i, t) { $g1.append($('<option></option>').val(t[1]).text(t[0])); });
            var $g2 = $('<optgroup label="General"></optgroup>');
            $.each(tags.slice(3), function(i, t) { $g2.append($('<option></option>').val(t[1]).text(t[0])); });
            $s.append($g1, $g2);
        });
    }

    var origGet = S.getSettings, origSet = S.setSettings;

    S.getSettings = function() {
        var out = origGet.apply(this, arguments) || {};
        var d = { enable: $('#set-dopt-enable').is(':checked') };
        $.each(MAP, function(k, sel) {
            var $e = $(sel);
            d[k] = $e.length ? ($e.val() || '') : DEF[k];
        });
        out.double_optin = d;
        return out;
    };

    S.setSettings = function(settings) {
        var r = origSet.apply(this, arguments);
        var d = (settings && settings.double_optin && typeof settings.double_optin === 'object') ? settings.double_optin : {};
        $('#set-dopt-enable').prop('checked', isTrue(d.enable));
        $.each(MAP, function(k, sel) {
            $(sel).val(d[k] !== undefined && d[k] !== null ? d[k] : DEF[k]);
        });
        refreshTags();
        return r;
    };

    $(document)
        .off('change.wppDoptTag', '.wppoppop-dopt-tag')
        .on('change.wppDoptTag', '.wppoppop-dopt-tag', function() {
            var tag = $(this).val();
            if (!tag) return;
            var $t = $($(this).attr('data-target'));
            var el = $t.get(0);
            if (!el) return;
            var v = $t.val() || '';
            var s = (typeof el.selectionStart === 'number') ? el.selectionStart : v.length;
            var e = (typeof el.selectionEnd === 'number') ? el.selectionEnd : s;
            $t.val(v.slice(0, s) + tag + v.slice(e));
            var pos = s + tag.length;
            try { el.focus(); el.setSelectionRange(pos, pos); } catch (err) {}
            $(this).val('');
            $t.trigger('input');
        })
        .off('click.wppDoptHdr', '#wppoppop-dopt-acc .wppoppop-acc-header')
        .on('click.wppDoptHdr', '#wppoppop-dopt-acc .wppoppop-acc-header', refreshTags);

    $(function() { setTimeout(refreshTags, 300); });
})(jQuery);


/* WPPOPPOP_NOTIFICATIONS_UI: Notifications module (Inspector DOM <-> settings.notifications) */
(function($) {
    'use strict';
    var S = window.WpPopPopBuilderSettings;
    if (!S || S._ntfWrapped) return;
    S._ntfWrapped = true;

    var MAX = 20;
    var list = [];
    var OPS = [['is', 'is'], ['is_not', 'is not'], ['contains', 'contains'], ['not_contains', 'does not contain'], ['empty', 'is empty'], ['not_empty', 'is not empty']];

    function uid() { return 'n_' + Math.random().toString(36).slice(2, 8); }
    function esc(v) { return $('<div></div>').text(v === undefined || v === null ? '' : String(v)).html(); }
    function isTrue(v) { return v === true || v === 1 || v === '1' || v === 'true'; }

    function blank() {
        return {
            id: uid(), name: 'Notification', enable: true, send: 'after_submission',
            recipient: '', subject: 'New submission from {{form-name}}', message: '{{form-data}}',
            attachments: [], reply_to: '', from_email: '{{global-from-email}}', from_name: '{{global-from-name}}',
            logic_enable: false, logic_match: 'all', rules: []
        };
    }

    function fieldTokens() {
        var out = [], seen = {};
        var core = window.WpPopPopBuilderCore;
        if (core && core.state && core.state.canvases) {
            Object.keys(core.state.canvases).forEach(function(c) {
                (core.state.canvases[c] || []).forEach(function(el) {
                    var tok = el && (el.field_name || el.fieldName || el.fieldId);
                    if (!tok || seen[tok]) return;
                    seen[tok] = true;
                    out.push({ tok: String(tok), type: el.type || 'field' });
                });
            });
        }
        return out;
    }

    function tagOptions() {
        var h = '<option value="">&lt;/&gt; Tags</option><optgroup label="Form values">';
        $.each(fieldTokens(), function(i, f) { h += '<option value="{{' + esc(f.tok) + '}}">' + esc(f.tok) + ' | ' + esc(f.type) + '</option>'; });
        h += '<option value="{{email}}">email</option><option value="{{form-data}}">All form data</option></optgroup><optgroup label="General">' +
            '<option value="{{form-name}}">Popup name</option><option value="{{submission-id}}">Submission ID</option>' +
            '<option value="{{country}}">Visitor country</option><option value="{{site-name}}">Site name</option>' +
            '<option value="{{global-from-email}}">Global from email</option><option value="{{global-from-name}}">Global from name</option></optgroup>';
        return h;
    }

    function tag(k) { return '<button type="button" class="button button-small wppoppop-ntf-tag" data-for="' + k + '" title="Insert merge tag" aria-label="Insert merge tag" aria-haspopup="true" aria-expanded="false" style="display:inline-flex;align-items:center;justify-content:center;min-height:22px;height:22px;line-height:1;padding:0 5px;margin:0 0 0 auto;"><span class="dashicons" aria-hidden="true" style="display:inline-flex;align-items:center;justify-content:center;width:16px;height:16px;font-size:16px;line-height:1;margin:0;padding:0;">&#xf150;</span></button>'; }

    function inputRow(label, k, n, big) {
        var ctl = big
            ? '<textarea class="widefat" rows="5" data-k="' + k + '">' + esc(n[k]) + '</textarea>'
            : '<input type="text" class="widefat" data-k="' + k + '" value="' + esc(n[k]) + '">';
        return '<div style="margin:0 0 10px;"><div class="wppoppop-ntf-lblrow" style="position:relative;display:flex;align-items:center;gap:6px;margin:0 0 4px;"><label style="font-weight:600;">' + label + '</label>' + tag(k) + '</div>' + ctl + '</div>';
    }

    function ruleHtml(r) {
        var f = '<select data-r="field"><option value="">Select field</option>';
        var found = false;
        $.each(fieldTokens(), function(i, t) { var sel = (r.field === t.tok); if (sel) found = true; f += '<option value="' + esc(t.tok) + '"' + (sel ? ' selected' : '') + '>' + esc(t.tok) + '</option>'; });
        if (r.field && !found) { f += '<option value="' + esc(r.field) + '" selected>' + esc(r.field) + '</option>'; }
        f += '</select>';
        var o = '<select data-r="operator">';
        $.each(OPS, function(i, p) { o += '<option value="' + p[0] + '"' + (r.operator === p[0] ? ' selected' : '') + '>' + p[1] + '</option>'; });
        o += '</select>';
        return '<div class="wppoppop-ntf-rule" style="display:flex;gap:4px;margin:4px 0;flex-wrap:wrap;">' + f + o +
            '<input type="text" data-r="value" value="' + esc(r.value) + '" placeholder="Value" style="flex:1;min-width:70px;">' +
            '<button type="button" class="button button-small wppoppop-ntf-rule-del" aria-label="Remove rule">&times;</button></div>';
    }

    function cardHtml(n, idx) {
        var att = '';
        $.each(n.attachments || [], function(i, a) {
            att += '<div style="display:flex;align-items:center;gap:6px;margin:3px 0;font-size:12px;"><span class="dashicons dashicons-media-default"></span><span style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' + esc(a.name || a.url || ('#' + a.id)) + '</span><button type="button" class="button button-small wppoppop-ntf-att-del" data-i="' + i + '">&times;</button></div>';
        });
        var rules = '';
        $.each(n.rules || [], function(i, r) { rules += ruleHtml(r); });
        return '<div class="wppoppop-ntf-card" data-id="' + esc(n.id) + '" style="border:1px solid #cbd5e1;border-radius:6px;margin-bottom:10px;background:#f8fafc;">' +
            '<div class="wppoppop-ntf-head" style="display:flex;align-items:center;justify-content:space-between;padding:8px 10px;cursor:pointer;">' +
                '<strong class="wppoppop-ntf-title" style="font-style:italic;">' + esc(n.name || 'Notification') + '</strong>' +
                '<button type="button" class="button-link wppoppop-ntf-del" title="Delete" aria-label="Delete notification" style="color:#b91c1c;"><span class="dashicons dashicons-trash"></span></button>' +
            '</div>' +
            '<div class="wppoppop-ntf-body" style="padding:6px 10px 10px;' + (idx === 0 && list.length === 1 ? '' : 'display:none;') + '">' +
                '<div style="margin:0 0 10px;"><label style="font-weight:600;display:block;">Name</label><input type="text" class="widefat" data-k="name" value="' + esc(n.name) + '"></div>' +
                '<label style="display:flex;align-items:center;gap:8px;margin:0 0 10px;font-weight:600;"><input type="checkbox" data-k="enable"' + (isTrue(n.enable) ? ' checked' : '') + '> Enabled</label>' +
                '<div style="margin:0 0 10px;"><label style="font-weight:600;display:block;">Send</label><select class="widefat" data-k="send">' +
                    '<option value="after_submission"' + (n.send === 'after_submission' ? ' selected' : '') + '>After successful form submission</option>' +
                    '<option value="after_confirmation"' + (n.send === 'after_confirmation' ? ' selected' : '') + '>After double opt-in confirmation</option></select></div>' +
                inputRow('Recipient', 'recipient', n, false) +
                inputRow('Subject', 'subject', n, false) +
                inputRow('Message', 'message', n, true) +
                '<div style="margin:0 0 10px;"><label style="font-weight:600;display:block;">Attachments</label>' + att + '<button type="button" class="button button-small wppoppop-ntf-att-add" style="display:inline-flex;align-items:center;justify-content:center;gap:4px;line-height:1;"><span class="dashicons dashicons-plus" style="display:inline-flex;align-items:center;justify-content:center;font-size:14px;width:14px;height:14px;line-height:1;margin:0;padding:0;"></span>Add file</button></div>' +
                inputRow('Reply-To', 'reply_to', n, false) +
                '<div style="margin:0 0 10px;"><label style="font-weight:600;display:block;">From</label><div style="display:flex;gap:6px;"><input type="text" class="widefat" data-k="from_email" value="' + esc(n.from_email) + '"><input type="text" class="widefat" data-k="from_name" value="' + esc(n.from_name) + '"></div></div>' +
                '<label style="display:flex;align-items:center;gap:8px;margin:0 0 6px;font-weight:600;"><input type="checkbox" data-k="logic_enable"' + (isTrue(n.logic_enable) ? ' checked' : '') + '> Enable conditional logic</label>' +
                '<div class="wppoppop-ntf-logic" style="' + (isTrue(n.logic_enable) ? '' : 'display:none;') + 'padding:6px;border:1px dashed #cbd5e1;border-radius:4px;">' +
                    '<div style="margin-bottom:4px;">Send if <select data-k="logic_match"><option value="all"' + (n.logic_match !== 'any' ? ' selected' : '') + '>all</option><option value="any"' + (n.logic_match === 'any' ? ' selected' : '') + '>any</option></select> of the rules match:</div>' +
                    rules + '<button type="button" class="button button-small wppoppop-ntf-rule-add">+ Add rule</button>' +
                '</div>' +
            '</div></div>';
    }

    function render() {
        var $l = $('#wppoppop-ntf-list');
        if (!$l.length) return;
        var openIds = {};
        $l.find('.wppoppop-ntf-card').each(function() { if ($(this).find('.wppoppop-ntf-body').is(':visible')) openIds[$(this).attr('data-id')] = true; });
        var h = '';
        $.each(list, function(i, n) { h += cardHtml(n, i); });
        if (!list.length) { h = '<p class="description" style="margin:0;">No notifications yet. Click "Add notification".</p>'; }
        $l.html(h);
        $l.find('.wppoppop-ntf-card').each(function() { if (openIds[$(this).attr('data-id')]) $(this).find('.wppoppop-ntf-body').show(); });
        $('#wppoppop-ntf-add').prop('disabled', list.length >= MAX);
    }

    function byId(id) { for (var i = 0; i < list.length; i++) { if (list[i].id === id) return list[i]; } return null; }
    function cur($el) { return byId($el.closest('.wppoppop-ntf-card').attr('data-id')); }

    var origGet = S.getSettings, origSet = S.setSettings;

    S.getSettings = function() {
        var out = origGet.apply(this, arguments) || {};
        out.notifications = $.map(list, function(n) { return $.extend(true, {}, n); });
        return out;
    };

    S.setSettings = function(settings) {
        var r = origSet.apply(this, arguments);
        var src = settings && settings.notifications;
        if (src && !$.isArray(src) && typeof src === 'object') { src = $.map(src, function(v) { return v; }); }
        list = [];
        $.each($.isArray(src) ? src : [], function(i, n) {
            if (!n || typeof n !== 'object') return;
            var m = $.extend(true, blank(), n);
            m.id = n.id ? String(n.id) : uid();
            m.enable = isTrue(n.enable) || n.enable === undefined;
            m.logic_enable = isTrue(n.logic_enable);
            m.attachments = $.isArray(n.attachments) ? n.attachments : [];
            m.rules = $.isArray(n.rules) ? n.rules : [];
            list.push(m);
        });
        render();
        return r;
    };

    $(document)
        .off('.wppNtf')
        .on('click.wppNtf', '#wppoppop-ntf-add', function(e) {
            e.preventDefault();
            if (list.length >= MAX) return;
            var n = blank();
            list.push(n);
            render();
            $('#wppoppop-ntf-list .wppoppop-ntf-card[data-id="' + n.id + '"] .wppoppop-ntf-body').show();
        })
        .on('click.wppNtf', '.wppoppop-ntf-head', function(e) {
            if ($(e.target).closest('.wppoppop-ntf-del').length) return;
            $(this).next('.wppoppop-ntf-body').slideToggle(120);
        })
        .on('click.wppNtf', '.wppoppop-ntf-del', function(e) {
            e.preventDefault(); e.stopPropagation();
            var n = cur($(this));
            if (!n || !window.confirm('Delete this notification?')) return;
            list = $.grep(list, function(x) { return x.id !== n.id; });
            render();
        })
        .on('input.wppNtf change.wppNtf', '#wppoppop-ntf-list [data-k]', function() {
            var n = cur($(this)); if (!n) return;
            var k = $(this).attr('data-k');
            n[k] = ($(this).attr('type') === 'checkbox') ? $(this).is(':checked') : $(this).val();
            if (k === 'name') { $(this).closest('.wppoppop-ntf-card').find('.wppoppop-ntf-title').text(n.name || 'Notification'); }
            if (k === 'logic_enable') { $(this).closest('.wppoppop-ntf-card').find('.wppoppop-ntf-logic').toggle(!!n.logic_enable); }
        })
        .on('change.wppNtf', '.wppoppop-ntf-tag-legacy', function() {
            var t = $(this).val(); if (!t) return;
            var $c = $(this).closest('.wppoppop-ntf-card');
            var $t = $c.find('[data-k="' + $(this).attr('data-for') + '"]');
            var el = $t.get(0); if (!el) return;
            var v = $t.val() || '';
            var s = (typeof el.selectionStart === 'number') ? el.selectionStart : v.length;
            var e2 = (typeof el.selectionEnd === 'number') ? el.selectionEnd : s;
            $t.val(v.slice(0, s) + t + v.slice(e2));
            try { el.focus(); el.setSelectionRange(s + t.length, s + t.length); } catch (err) {}
            $(this).val('');
            $t.trigger('input');
        })
        .on('click.wppNtf', '.wppoppop-ntf-rule-add', function(e) {
            e.preventDefault();
            var n = cur($(this)); if (!n) return;
            if (n.rules.length >= 20) return;
            n.rules.push({ field: '', operator: 'is', value: '' });
            render();
            $('#wppoppop-ntf-list .wppoppop-ntf-card[data-id="' + n.id + '"] .wppoppop-ntf-body').show();
        })
        .on('click.wppNtf', '.wppoppop-ntf-rule-del', function(e) {
            e.preventDefault();
            var n = cur($(this)); if (!n) return;
            n.rules.splice($(this).closest('.wppoppop-ntf-rule').index('.wppoppop-ntf-rule') - $(this).closest('.wppoppop-ntf-card').find('.wppoppop-ntf-rule').first().index('.wppoppop-ntf-rule'), 1);
            render();
        })
        .on('input.wppNtf change.wppNtf', '#wppoppop-ntf-list [data-r]', function() {
            var n = cur($(this)); if (!n) return;
            var $rows = $(this).closest('.wppoppop-ntf-card').find('.wppoppop-ntf-rule');
            var i = $rows.index($(this).closest('.wppoppop-ntf-rule'));
            if (i < 0 || !n.rules[i]) return;
            n.rules[i][$(this).attr('data-r')] = $(this).val();
        })
        .on('click.wppNtf', '.wppoppop-ntf-att-add', function(e) {
            e.preventDefault();
            var n = cur($(this)); if (!n) return;
            if (!window.wp || !window.wp.media) { window.alert('WordPress Media is unavailable on this screen.'); return; }
            var frame = window.wp.media({ title: 'Select attachment', button: { text: 'Attach' }, multiple: false });
            frame.on('select', function() {
                var a = frame.state().get('selection').first();
                a = a ? a.toJSON() : null;
                if (a && a.id && n.attachments.length < 10) {
                    n.attachments.push({ id: a.id, name: a.filename || a.title || '', url: a.url || '' });
                    render();
                    $('#wppoppop-ntf-list .wppoppop-ntf-card[data-id="' + n.id + '"] .wppoppop-ntf-body').show();
                }
            });
            frame.open();
        })
        .on('click.wppNtf', '.wppoppop-ntf-att-del', function(e) {
            e.preventDefault();
            var n = cur($(this)); if (!n) return;
            n.attachments.splice(parseInt($(this).attr('data-i'), 10), 1);
            render();
            $('#wppoppop-ntf-list .wppoppop-ntf-card[data-id="' + n.id + '"] .wppoppop-ntf-body').show();
        })
        .on('click.wppNtf', '#wppoppop-ntf-acc .wppoppop-acc-header', function() { setTimeout(render, 0); });

    $(function() { setTimeout(render, 300); });
})(jQuery);


/* WPPOPPOP_NTF_TAGMENU: icon button + merge-tag menu for Notifications */
(function($) {
    'use strict';
    if (window._wppNtfTagMenu) return;
    window._wppNtfTagMenu = true;

    var $menu = null, $btn = null;

    function esc(v) { return $('<div></div>').text(v === undefined || v === null ? '' : String(v)).html(); }

    function tokens() {
        var out = [], seen = {};
        var core = window.WpPopPopBuilderCore;
        if (core && core.state && core.state.canvases) {
            Object.keys(core.state.canvases).forEach(function(c) {
                (core.state.canvases[c] || []).forEach(function(el) {
                    var t = el && (el.field_name || el.fieldName || el.fieldId);
                    if (!t || seen[t]) return;
                    seen[t] = true;
                    out.push([String(t) + ' | ' + (el.type || 'field'), '{{' + t + '}}']);
                });
            });
        }
        return out;
    }

    function close() {
        if ($btn) { $btn.attr('aria-expanded', 'false'); }
        if ($menu) { $menu.remove(); }
        $menu = null; $btn = null;
    }

    function group(title, items) {
        var h = '<div style="padding:6px 10px 2px;font-size:11px;font-weight:700;color:#475569;">' + esc(title) + '</div>';
        $.each(items, function(i, it) {
            h += '<button type="button" class="wppoppop-ntf-tag-item" data-tag="' + esc(it[1]) + '" style="display:block;width:100%;text-align:left;border:0;background:transparent;padding:5px 10px;font-size:12px;cursor:pointer;color:#1e293b;">' + esc(it[0]) + '</button>';
        });
        return h;
    }

    function open($b) {
        close();
        $btn = $b;
        $b.attr('aria-expanded', 'true');
        var forms = tokens().concat([['email', '{{email}}'], ['All form data', '{{form-data}}']]);
        var gen = [['Popup name', '{{form-name}}'], ['Submission ID', '{{submission-id}}'], ['Visitor country', '{{country}}'], ['Site name', '{{site-name}}'], ['Global from email', '{{global-from-email}}'], ['Global from name', '{{global-from-name}}']];
        $menu = $('<div class="wppoppop-ntf-tag-menu" role="menu"></div>').css({
            position: 'absolute', top: '100%', right: 0, left: 'auto', zIndex: 50, minWidth: '190px', maxHeight: '220px',
            overflowY: 'auto', background: '#fff', border: '1px solid #cbd5e1', borderRadius: '4px',
            boxShadow: '0 4px 14px rgba(0,0,0,.15)', marginTop: '2px'
        }).html(group('Form values', forms) + group('General', gen));
        $b.closest('.wppoppop-ntf-lblrow').append($menu);
    }

    $(document)
        .off('.wppNtfMenu')
        .on('click.wppNtfMenu', '.wppoppop-ntf-tag', function(e) {
            e.preventDefault(); e.stopPropagation();
            if ($btn && $btn[0] === this && $menu) { close(); } else { open($(this)); }
        })
        .on('mouseenter.wppNtfMenu', '.wppoppop-ntf-tag-item', function() { $(this).css('background', '#f1f5f9'); })
        .on('mouseleave.wppNtfMenu', '.wppoppop-ntf-tag-item', function() { $(this).css('background', 'transparent'); })
        .on('click.wppNtfMenu', '.wppoppop-ntf-tag-item', function(e) {
            e.preventDefault(); e.stopPropagation();
            var tag = $(this).attr('data-tag');
            var $t = $btn ? $btn.closest('.wppoppop-ntf-card').find('[data-k="' + $btn.attr('data-for') + '"]') : $();
            var el = $t.get(0);
            if (el && tag) {
                var v = $t.val() || '';
                var a = (typeof el.selectionStart === 'number') ? el.selectionStart : v.length;
                var b = (typeof el.selectionEnd === 'number') ? el.selectionEnd : a;
                $t.val(v.slice(0, a) + tag + v.slice(b));
                try { el.focus(); el.setSelectionRange(a + tag.length, a + tag.length); } catch (err) {}
                $t.trigger('input');
            }
            close();
        })
        .on('click.wppNtfMenu', function(e) {
            if ($menu && !$(e.target).closest('.wppoppop-ntf-tag-menu, .wppoppop-ntf-tag').length) { close(); }
        })
        .on('keydown.wppNtfMenu', function(e) {
            if ($menu && (e.key === 'Escape' || e.keyCode === 27)) { e.stopPropagation(); close(); }
        });
})(jQuery);


/* WPPOPPOP_DOPT_TAGMENU: icon button + merge-tag menu for Double Opt-In */
(function($) {
    'use strict';
    if (window._wppDoptTagMenu) return;
    window._wppDoptTagMenu = true;

    var $menu = null, $btn = null;

    function esc(v) { return $('<div></div>').text(v === undefined || v === null ? '' : String(v)).html(); }

    function tokens() {
        var out = [], seen = {};
        var core = window.WpPopPopBuilderCore;
        if (core && core.state && core.state.canvases) {
            Object.keys(core.state.canvases).forEach(function(c) {
                (core.state.canvases[c] || []).forEach(function(el) {
                    var t = el && (el.field_name || el.fieldName || el.fieldId);
                    if (!t || seen[t]) return;
                    seen[t] = true;
                    out.push([String(t) + ' | ' + (el.type || 'field'), '{{' + t + '}}']);
                });
            });
        }
        return out;
    }

    function close() {
        if ($btn) { $btn.attr('aria-expanded', 'false'); }
        if ($menu) { $menu.remove(); }
        $menu = null; $btn = null;
    }

    function group(title, items) {
        var h = '<div style="padding:6px 10px 2px;font-size:11px;font-weight:700;color:#475569;">' + esc(title) + '</div>';
        $.each(items, function(i, it) {
            h += '<button type="button" class="wppoppop-dopt-tag-item" data-tag="' + esc(it[1]) + '" style="display:block;width:100%;text-align:left;border:0;background:transparent;padding:5px 10px;font-size:12px;cursor:pointer;color:#1e293b;">' + esc(it[0]) + '</button>';
        });
        return h;
    }

    function open($b) {
        close();
        $btn = $b;
        $b.attr('aria-expanded', 'true');
        var forms = tokens().concat([['email', '{{email}}']]);
        var gen = [['Confirmation URL', '{{confirmation-url}}'], ['Global from email', '{{global-from-email}}'], ['Global from name', '{{global-from-name}}'], ['Site name', '{{site-name}}']];
        $menu = $('<div class="wppoppop-dopt-tag-menu" role="menu"></div>').css({
            position: 'absolute', top: '100%', right: 0, left: 'auto', zIndex: 50, minWidth: '190px', maxHeight: '220px',
            overflowY: 'auto', background: '#fff', border: '1px solid #cbd5e1', borderRadius: '4px',
            boxShadow: '0 4px 14px rgba(0,0,0,.15)', marginTop: '2px'
        }).html(group('Form values', forms) + group('General', gen));
        $b.closest('.wppoppop-dopt-lblrow').append($menu);
    }

    $(document)
        .off('.wppDoptMenu')
        .on('click.wppDoptMenu', '.wppoppop-dopt-tagbtn', function(e) {
            e.preventDefault(); e.stopPropagation();
            if ($btn && $btn[0] === this && $menu) { close(); } else { open($(this)); }
        })
        .on('mouseenter.wppDoptMenu', '.wppoppop-dopt-tag-item', function() { $(this).css('background', '#f1f5f9'); })
        .on('mouseleave.wppDoptMenu', '.wppoppop-dopt-tag-item', function() { $(this).css('background', 'transparent'); })
        .on('click.wppDoptMenu', '.wppoppop-dopt-tag-item', function(e) {
            e.preventDefault(); e.stopPropagation();
            var tag = $(this).attr('data-tag');
            var $t = $btn ? $($btn.attr('data-target')) : $();
            var el = $t.get(0);
            if (el && tag) {
                var v = $t.val() || '';
                var a = (typeof el.selectionStart === 'number') ? el.selectionStart : v.length;
                var b = (typeof el.selectionEnd === 'number') ? el.selectionEnd : a;
                $t.val(v.slice(0, a) + tag + v.slice(b));
                try { el.focus(); el.setSelectionRange(a + tag.length, a + tag.length); } catch (err) {}
                $t.trigger('input');
            }
            close();
        })
        .on('click.wppDoptMenu', function(e) {
            if ($menu && !$(e.target).closest('.wppoppop-dopt-tag-menu, .wppoppop-dopt-tagbtn').length) { close(); }
        })
        .on('keydown.wppDoptMenu', function(e) {
            if ($menu && (e.key === 'Escape' || e.keyCode === 27)) { e.stopPropagation(); close(); }
        });
})(jQuery);
