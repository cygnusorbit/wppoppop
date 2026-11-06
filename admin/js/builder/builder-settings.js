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

            $('#wppoppop-settings-drawer-close').on('click', function(e) {
                e.preventDefault();
                self.closeDrawer();
            });

            self.bindOutsideClose();
        },

        // WPPOPPOP_DRAWER_CLOSE_BUTTON_ONLY: Popup Settings closes ONLY via its top-corner close button
        bindOutsideClose: function() {
            $(document).off('mousedown.wppoppop_outside_close touchstart.wppoppop_outside_close click.wppoppop_outside_close keydown.wppoppop_outside_close');
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
                var meta = core.state.canvasMeta[cNum] || { name: (cNum === 2 ? 'Confirmation' : 'Canvas ' + cNum) };
                var name = meta.name || (cNum === 2 ? 'Confirmation' : 'Canvas ' + cNum);
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

            $('#set-canvas-name').val(meta.name || (cur === 2 ? 'Confirmation' : 'Canvas ' + cur));
            $('#set-canvas-width').val(meta.width || 300);
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
            $('#set-canvas-radius').val(Math.max(0, parseInt(meta.canvas_radius, 10) || 0));

            // WPPOPPOP_CANVAS_OVERFLOW: per-canvas overflow, auto-close & logic hydration
            var ovOn = (meta.allow_overflow === true || meta.allow_overflow === 1 || meta.allow_overflow === '1' || meta.allow_overflow === 'true');
            $('#set-canvas-allow-overflow').prop('checked', ovOn);
            this.applyCanvasOverflow(ovOn);
            /* WPPOPPOP_DEFAULT_CLOSE hydrate: missing = enabled */
            var dcOn = !(meta.close_btn_enabled === false || meta.close_btn_enabled === 0 || meta.close_btn_enabled === '0' || meta.close_btn_enabled === 'false');
            $('#set-canvas-close-btn').prop('checked', dcOn);

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
            $(document).off('input.wppoppop_cac change.wppoppop_cac', '#set-canvas-autoclose').on('input.wppoppop_cac change.wppoppop_cac', '#set-canvas-autoclose', function(e) {
                var val = Math.max(0, Math.min(300, parseInt($(this).val(), 10) || 0)); /* WPPOPPOP_AUTOCLOSE_CLAMP */
                var core = window.WpPopPopBuilderCore;
                if (!core) return;
                var cur = core.state.currentCanvas || 1;
                if (!core.state.canvasMeta[cur]) core.state.canvasMeta[cur] = {};
                core.state.canvasMeta[cur].autoclose = val;
                core.state.canvasMeta[cur].autoclose_delay = val;
                if (core.state.settings) core.state.settings.autoclose_delay = val;
                if (e && e.type === 'change' && typeof core.pushHistory === 'function') core.pushHistory();
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
            /* WPPOPPOP_DEFAULT_CLOSE: per-canvas default close button */
            $(document).off('change.wppoppop_dclose', '#set-canvas-close-btn').on('change.wppoppop_dclose', '#set-canvas-close-btn', function() {
                var core = window.WpPopPopBuilderCore;
                if (!core) return;
                var cur = core.state.currentCanvas || 1;
                if (!core.state.canvasMeta[cur]) core.state.canvasMeta[cur] = {};
                core.state.canvasMeta[cur].close_btn_enabled = $(this).is(':checked');
                if (typeof core.pushHistory === 'function') core.pushHistory();
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

            /* WPPOPPOP_CANVAS_RADIUS: per-canvas corner radius (px) */
            $('#set-canvas-radius').off('input.canvasRadius change.canvasRadius').on('input.canvasRadius change.canvasRadius', function(e) {
                var core = window.WpPopPopBuilderCore;
                if (!core) return;
                var cur = core.state.currentCanvas || 1;
                if (!core.state.canvasMeta[cur]) core.state.canvasMeta[cur] = {};
                core.state.canvasMeta[cur].canvas_radius = Math.max(0, Math.min(500, parseInt($(this).val(), 10) || 0));
                self.applyActiveCanvasBackground();
                if (e.type === 'change') core.pushHistory();
            });


        },

        applyActiveCanvasBackground: function() {
            var core = window.WpPopPopBuilderCore;
            if (!core) return;
            var cur = core.state.currentCanvas || 1;
            var meta = core.state.canvasMeta[cur] || {};
            var $box = $('#wppoppop-canvas-box');
            var radPx = (Math.max(0, parseInt(meta.canvas_radius, 10) || 0)) + 'px';
            $box.css('border-radius', radPx);
            if ($box.length) { $box[0].style.setProperty('--wppoppop-canvas-radius', radPx); }

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
                box: {
                    width: parseInt($('#set-canvas-width').val(), 10) || 640,
                    height: parseInt($('#set-canvas-height').val(), 10) || 400
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


/* Display Triggers (settings.triggers, exit intent, scroll depth) live in the module package: admin.js */

/* Canvas Animation (controls, canvasMeta sync, exit toggle, Play) live in the module package: admin.js */


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


/* Popup Overlay (enable, opacity, colour, custom backgrounds) lives in the module package: admin.js */


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
        return '<div class="wppoppop-ntf-card" data-id="' + esc(n.id) + '" style="border:1px solid #cbd5e1;border-radius:0px;margin-bottom:10px;background:#f8fafc;">' +
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
                '<div class="wppoppop-ntf-logic" style="' + (isTrue(n.logic_enable) ? '' : 'display:none;') + 'padding:6px;border:1px dashed #cbd5e1;border-radius:0px;">' +
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
            if (!n || !window.confirm(wp.i18n.__('Delete this notification?', 'wppoppop'))) return;
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
            if (!window.wp || !window.wp.media) { window.alert(wp.i18n.__('WordPress Media is unavailable on this screen.', 'wppoppop')); return; }
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
            overflowY: 'auto', background: '#fff', border: '1px solid #cbd5e1', borderRadius: '0px',
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
            overflowY: 'auto', background: '#fff', border: '1px solid #cbd5e1', borderRadius: '0px',
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


/* WPPOPPOP_BG_CLOSE_UI: popup-level "Click Background to Close" (default false) */
(function($) {
    'use strict';
    var S = window.WpPopPopBuilderSettings;
    if (!S || S._bgCloseWrapped) return;
    S._bgCloseWrapped = true;
    function truthy(v) { return v === true || v === 1 || v === '1' || v === 'true'; }
    var origGet = S.getSettings, origSet = S.setSettings;
    S.getSettings = function() {
        var out = origGet.apply(this, arguments) || {};
        out.close_on_backdrop = $('#set-canvas-bg-close').is(':checked');
        return out;
    };
    S.setSettings = function(settings) {
        var r = origSet.apply(this, arguments);
        $('#set-canvas-bg-close').prop('checked', !!settings && truthy(settings.close_on_backdrop));
        return r;
    };
    $(document).off('change.wppoppop_bgclose', '#set-canvas-bg-close').on('change.wppoppop_bgclose', '#set-canvas-bg-close', function() {
        var core = window.WpPopPopBuilderCore;
        if (core && typeof core.pushHistory === 'function') core.pushHistory();
    });
})(jQuery);
